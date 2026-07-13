<?php

namespace App\Models\Concerns;

use App\Models\Boutique;
use App\Models\Scopes\BoutiqueScope;
use App\Support\BoutiqueContext;

/**
 * Filtre automatiquement les requêtes sur la boutique courante (session) et
 * renseigne boutique_id à la création si absent. Utilisé par les modèles qui
 * appartiennent à une boutique précise (Vente, MouvementStock, ClotureCaisse,
 * Commande, BoutiqueProduit) — pas par Produit/Categorie, dont le catalogue
 * est partagé entre boutiques.
 */
trait BelongsToBoutique
{
    public static function bootBelongsToBoutique(): void
    {
        static::addGlobalScope(new BoutiqueScope);

        static::creating(function ($model) {
            if (empty($model->boutique_id) && BoutiqueContext::estDefinie()) {
                $model->boutique_id = BoutiqueContext::id();
            }
        });
    }

    /**
     * Échappe le scope automatique pour les rapports consolidés "toutes boutiques".
     */
    public function scopeToutesBoutiques($query)
    {
        return $query->withoutGlobalScope(BoutiqueScope::class);
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class);
    }
}
