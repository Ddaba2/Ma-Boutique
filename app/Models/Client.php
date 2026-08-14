<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'telephone', 'email', 'adresse', 'notes', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function ventes()
    {
        return $this->hasMany(Vente::class);
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->nom.' '.$this->prenom);
    }

    public function totalAchats(): float
    {
        return $this->ventes()->where('statut', 'terminee')->sum('total');
    }
}
