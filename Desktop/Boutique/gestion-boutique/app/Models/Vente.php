<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $fillable = [
        'reference',
        'total',
        'montant_recu',
        'monnaie',
        'client_nom',
        'client_telephone',
        'client_email',
        'notes',
        'statut',
        'mode_paiement'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'montant_recu' => 'decimal:2',
        'monnaie' => 'decimal:2'
    ];

    public function detailVentes()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'detail_ventes')
                    ->withPivot(['quantite', 'prix_unitaire', 'total_ligne', 'remise']);
    }
}
