<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'couleur',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
