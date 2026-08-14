<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailVente extends Model
{
    // 'remise' existe en base (défaut 0) mais aucune interface ne permet de la
    // saisir : VenteService ne la renseigne jamais. Ce n'est pas un bug (elle
    // reste à 0, sans impact sur les totaux) mais une remise par ligne n'est
    // pas une fonctionnalité disponible tant qu'un champ de saisie n'est pas
    // ajouté au panier de vente et pris en compte dans le calcul du total.
    protected $fillable = [
        'vente_id',
        'produit_id',
        'quantite',
        'prix_unitaire',
        'total_ligne',
        'remise',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'total_ligne' => 'decimal:2',
        'remise' => 'decimal:2',
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
