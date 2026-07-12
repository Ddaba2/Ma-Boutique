<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $fillable = [
        'reference',
        'client_id',
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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function detailVentes()
    {
        return $this->hasMany(DetailVente::class);
    }

    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'detail_ventes')
                    ->withPivot(['quantite', 'prix_unitaire', 'total_ligne', 'remise']);
    }

    public static function generateReference(): string
    {
        $count = self::lockForUpdate()->count() + 1;
        return 'VENTE' . date('Y') . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public static function libellesModePaiement(): array
    {
        return [
            'espece' => 'Espèce',
            'carte' => 'Carte bancaire',
            'mobile' => 'Mobile Money',
            'autre' => 'Autre',
        ];
    }

    public function modePaiementLabel(): string
    {
        return self::libellesModePaiement()[$this->mode_paiement] ?? ucfirst($this->mode_paiement);
    }
}
