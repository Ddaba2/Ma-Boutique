<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBoutique;
use Illuminate\Database\Eloquent\Model;

class ClotureCaisse extends Model
{
    use BelongsToBoutique;

    protected $table = 'clotures_caisse';

    protected $fillable = [
        'date', 'boutique_id', 'fond_ouverture', 'total_especes', 'total_carte', 'total_mobile',
        'total_autre', 'total_ventes', 'nombre_ventes', 'ecart', 'notes', 'statut', 'user_id'
    ];

    protected $casts = [
        'date'           => 'date',
        'fond_ouverture' => 'decimal:2',
        'total_especes'  => 'decimal:2',
        'total_carte'    => 'decimal:2',
        'total_mobile'   => 'decimal:2',
        'total_autre'    => 'decimal:2',
        'total_ventes'   => 'decimal:2',
        'ecart'          => 'decimal:2',
        'nombre_ventes'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function totalEncaisse(): float
    {
        return (float) ($this->total_especes + $this->total_carte + $this->total_mobile + $this->total_autre);
    }
}
