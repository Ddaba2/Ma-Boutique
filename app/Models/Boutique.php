<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boutique extends Model
{
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function utilisateurs()
    {
        return $this->hasMany(User::class);
    }

    public function stocks()
    {
        return $this->hasMany(BoutiqueProduit::class);
    }
}
