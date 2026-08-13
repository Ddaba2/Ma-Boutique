<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fournisseur extends Model
{
    protected $fillable = [
        'nom', 'contact', 'telephone', 'email', 'adresse', 'delai_livraison', 'notes', 'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'delai_livraison' => 'integer',
    ];

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    public function totalCommandes(): float
    {
        return $this->commandes()->sum('total');
    }
}
