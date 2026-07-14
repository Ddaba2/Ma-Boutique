<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBoutique;
use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    use BelongsToBoutique;

    protected $fillable = [
        'reference',
        'uuid_client',
        'boutique_id',
        'client_id',
        'total',
        'montant_recu',
        'monnaie',
        'client_nom',
        'client_telephone',
        'client_email',
        'notes',
        'statut',
        'conflit_stock',
        'notes_conflit',
        'mode_paiement'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'montant_recu' => 'decimal:2',
        'monnaie' => 'decimal:2',
        'conflit_stock' => 'boolean',
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
