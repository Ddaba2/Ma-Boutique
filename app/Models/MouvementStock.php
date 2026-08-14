<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBoutique;
use App\Support\ReferenceSequence;
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
        'notes',
    ];

    protected $casts = [
        'date_mouvement' => 'date',
        'prix_unitaire' => 'decimal:2',
        'total' => 'decimal:2',
        'quantite' => 'integer',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public static function generateReference()
    {
        return ReferenceSequence::next('mouvements_stock_'.now()->format('Ymd'), 'STK'.now()->format('Ymd'), 4);
    }
}
