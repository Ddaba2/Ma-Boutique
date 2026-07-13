<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBoutique;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    use BelongsToBoutique;

    protected $table = 'mouvements_stock';

    protected $fillable = [
        'boutique_id',
        'produit_id',
        'type',
        'quantite',
        'prix_unitaire',
        'total',
        'reference',
        'motif',
        'fournisseur',
        'date_mouvement',
        'notes'
    ];

    protected $casts = [
        'date_mouvement' => 'date',
        'prix_unitaire' => 'decimal:2',
        'total' => 'decimal:2',
        'quantite' => 'integer'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public static function generateReference()
    {
        $prefix = 'STK';
        $date = now()->format('Ymd');
        $last = self::whereDate('created_at', now()->format('Y-m-d'))->lockForUpdate()->count();
        return $prefix . $date . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
