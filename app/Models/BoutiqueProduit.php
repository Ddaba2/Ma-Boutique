<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBoutique;
use App\Models\Scopes\BoutiqueScope;
use Illuminate\Database\Eloquent\Model;

class BoutiqueProduit extends Model
{
    use BelongsToBoutique;

    protected $table = 'boutique_produit';

    protected $fillable = [
        'boutique_id',
        'produit_id',
        'stock_actuel',
        'stock_min',
        'stock_max',
    ];

    /**
     * Interroge explicitement le stock d'une boutique précise, indépendamment
     * de la boutique courante en session (ex. consultation par un gérant).
     */
    public function scopeDansBoutique($query, int $boutiqueId)
    {
        return $query->withoutGlobalScope(BoutiqueScope::class)->where('boutique_id', $boutiqueId);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    /**
     * Le catalogue est partagé entre boutiques : quand un produit est créé,
     * chaque boutique doit avoir sa propre ligne de stock (à 0, sauf la
     * boutique où l'entrée initiale a réellement lieu).
     */
    public static function initialiserPourNouveauProduit(
        Produit $produit,
        ?int $boutiqueAvecStockId = null,
        int $quantiteInitiale = 0,
        int $stockMin = 5,
        int $stockMax = 100
    ): void {
        $maintenant = now();

        $lignes = Boutique::pluck('id')->map(fn ($boutiqueId) => [
            'boutique_id' => $boutiqueId,
            'produit_id' => $produit->id,
            'stock_actuel' => $boutiqueId === $boutiqueAvecStockId ? $quantiteInitiale : 0,
            'stock_min' => $stockMin,
            'stock_max' => $stockMax,
            'created_at' => $maintenant,
            'updated_at' => $maintenant,
        ])->all();

        if (!empty($lignes)) {
            static::insert($lignes);
        }
    }

    /**
     * Fusionne une collection de BoutiqueProduit (stock d'une boutique) avec
     * les informations catalogue du produit associé, pour les vues/exports
     * qui affichent encore un objet "produit" plat avec ses champs de stock.
     */
    public static function fusionnerAvecCatalogue($stocks)
    {
        return $stocks->map(function (self $stock) {
            $produit = $stock->produit;
            return (object) [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'reference' => $produit->reference,
                'categorie_id' => $produit->categorie_id,
                'categorie' => $produit->categorie,
                'prix_achat' => $produit->prix_achat,
                'prix_vente' => $produit->prix_vente,
                'active' => $produit->active,
                'stock_actuel' => $stock->stock_actuel,
                'stock_min' => $stock->stock_min,
                'stock_max' => $stock->stock_max,
            ];
        });
    }

    /**
     * 'rupture' | 'faible' | 'normal' — même logique que Produit::statutStock(),
     * appliquée au stock d'une boutique donnée plutôt qu'à une colonne globale.
     */
    public function statutStock(): string
    {
        if ($this->stock_actuel == 0) {
            return 'rupture';
        }

        return $this->stock_actuel <= $this->stock_min ? 'faible' : 'normal';
    }

    public function scopeEnRupture($query)
    {
        return $query->where('stock_actuel', 0);
    }

    public function scopeStockFaible($query)
    {
        return $query->whereColumn('stock_actuel', '<=', 'stock_min')->where('stock_actuel', '>', 0);
    }

    public function scopeStockNormal($query)
    {
        return $query->whereColumn('stock_actuel', '>', 'stock_min');
    }

    /**
     * Rupture ou stock faible confondus (utilisé pour les alertes).
     */
    public function scopeEnAlerte($query)
    {
        return $query->whereColumn('stock_actuel', '<=', 'stock_min');
    }

    public function scopeParStatutStock($query, ?string $statut)
    {
        return match ($statut) {
            'rupture' => $query->enRupture(),
            'faible' => $query->stockFaible(),
            'normal' => $query->stockNormal(),
            default => $query,
        };
    }
}
