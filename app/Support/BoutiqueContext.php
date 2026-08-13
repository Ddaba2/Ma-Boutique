<?php

namespace App\Support;

use App\Models\Boutique;
use Illuminate\Support\Facades\Session;

/**
 * Point d'accès unique à la boutique actuellement sélectionnée en session,
 * plutôt que d'éparpiller des Session::get('boutique_id') partout.
 */
class BoutiqueContext
{
    public static function id(): ?int
    {
        return Session::get('boutique_id');
    }

    public static function boutique(): ?Boutique
    {
        $id = static::id();

        return $id ? Boutique::find($id) : null;
    }

    public static function definir(int $boutiqueId): void
    {
        Session::put('boutique_id', $boutiqueId);
    }

    public static function estDefinie(): bool
    {
        return static::id() !== null;
    }
}
