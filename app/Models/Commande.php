<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBoutique;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use BelongsToBoutique;

    protected $fillable = [
        'reference', 'boutique_id', 'fournisseur_id', 'statut', 'date_livraison_prevue', 'total', 'notes', 'user_id'
    ];

    protected $casts = [
        'date_livraison_prevue' => 'date',
        'total' => 'decimal:2',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function details()
    {
        return $this->hasMany(DetailCommande::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateReference(): string
    {
        $prefix = 'CMD' . date('Y');
        $count = self::whereYear('created_at', now()->year)->lockForUpdate()->count() + 1;
        return $prefix . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function statutLabel(): string
    {
        return match ($this->statut) {
            'en_attente'      => 'En attente',
            'envoyee'         => 'Envoyée',
            'recue_partielle' => 'Reçue partiellement',
            'recue'           => 'Reçue',
            'annulee'         => 'Annulée',
            default           => $this->statut,
        };
    }

    public function statutBadge(): string
    {
        return match ($this->statut) {
            'en_attente'      => 'warning',
            'envoyee'         => 'info',
            'recue_partielle' => 'primary',
            'recue'           => 'success',
            'annulee'         => 'danger',
            default           => 'secondary',
        };
    }
}
