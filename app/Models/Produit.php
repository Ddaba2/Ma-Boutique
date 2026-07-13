<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
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

    public function estEnStockFaible()
    {
        return $this->stock_actuel <= $this->stock_min;
    }

    public function benefice()
    {
        return $this->prix_vente - $this->prix_achat;
    }

    /**
     * 'rupture' | 'faible' | 'normal' — logique unique utilisée partout
     * au lieu d'être réécrite dans chaque contrôleur/vue.
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

    public function scopeParStatutStock($query, ?string $statut)
    {
        return match ($statut) {
            'rupture' => $query->enRupture(),
            'faible' => $query->stockFaible(),
            'normal' => $query->stockNormal(),
            default => $query,
        };
    }

    public static function generateReference(): string
    {
        $count = self::lockForUpdate()->count() + 1;
        return 'PROD' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
}
