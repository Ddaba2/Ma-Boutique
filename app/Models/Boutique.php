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

    /**
     * Catalogue partagé entre boutiques, stock séparé par boutique : dès
     * qu'une boutique est créée (quelle que soit la façon — il n'existe plus
     * d'écran dédié, seulement `php artisan tinker`/seeder), elle doit
     * démarrer avec une ligne de stock à 0 pour chaque produit déjà existant.
     * Vivait auparavant dans BoutiqueController::store() ; déplacé ici pour
     * rester correct quel que soit le point d'entrée.
     */
    protected static function booted(): void
    {
        static::created(function (self $boutique) {
            $maintenant = now();

            $lignes = Produit::pluck('id')->map(fn ($produitId) => [
                'boutique_id' => $boutique->id,
                'produit_id' => $produitId,
                'stock_actuel' => 0,
                'stock_min' => 5,
                'stock_max' => 100,
                'created_at' => $maintenant,
                'updated_at' => $maintenant,
            ])->all();

            if (! empty($lignes)) {
                BoutiqueProduit::insert($lignes);
            }
        });
    }
}
