<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

/**
 * Filtre automatiquement les requêtes sur la boutique actuellement sélectionnée
 * en session (App\Support\BoutiqueContext). Ne s'applique que si une boutique
 * est effectivement sélectionnée — un gérant sans boutique active voit tout.
 */
class BoutiqueScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($boutiqueId = Session::get('boutique_id')) {
            $builder->where($model->getTable().'.boutique_id', $boutiqueId);
        }
    }
}
