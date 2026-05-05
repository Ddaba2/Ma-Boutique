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
        'active'
    ];

    protected $casts = [
        'prix_achat' => 'decimal:2',
        'prix_vente' => 'decimal:2',
        'active' => 'boolean'
    ];

    
    public function detailVentes()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function estEnStockFaible()
    {
        return $this->stock_actuel <= $this->stock_min;
    }

    public function benefice()
    {
        return $this->prix_vente - $this->prix_achat;
    }
}
