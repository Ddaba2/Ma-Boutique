<?php

namespace App\Models;

use App\Support\ReferenceSequence;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    // stock_actuel/stock_min/stock_max ne servent qu'à amorcer la première
    // ligne BoutiqueProduit à la création (voir ProduitController::store()) :
    // ils ne sont plus mis à jour ensuite. Le stock réel, par boutique, vit
    // uniquement dans BoutiqueProduit — ne jamais lire ces colonnes pour
    // afficher ou calculer un stock courant.
    protected $fillable = [
        'reference',
        'nom',
        'description',
        'prix_achat',
        'prix_vente',
        'stock_actuel',
        'stock_min',
        'stock_max',
        'image',
        'active',
        'categorie_id'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'active' => 'boolean'
    ];

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function detailVentes()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function boutiqueProduits()
    {
        return $this->hasMany(BoutiqueProduit::class);
    }

    /**
     * Ligne de stock de ce produit dans une boutique précise (catalogue
     * partagé, stock séparé par boutique — voir App\Models\BoutiqueProduit).
     */
    public function stockDans(int $boutiqueId): ?BoutiqueProduit
    {
        return $this->boutiqueProduits()->dansBoutique($boutiqueId)->first();
    }

    public function benefice()
    {
        return $this->prix_vente - $this->prix_achat;
    }

    public static function generateReference(): string
    {
        return ReferenceSequence::next('produits', 'PROD', 6);
    }
}
