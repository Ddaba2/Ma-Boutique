<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailVente extends Model
{
    protected $fillable = [
        'vente_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'total_ligne',
        'remise'
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'total_ligne' => 'decimal:2',
        'remise' => 'decimal:2'
    ];

    public function vente()
    {
        return $this->belongsTo(Vente::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
