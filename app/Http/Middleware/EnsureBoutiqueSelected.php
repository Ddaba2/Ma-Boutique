<?php

namespace App\Http\Middleware;

use App\Models\Boutique;
use App\Support\BoutiqueContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garantit qu'une boutique est sélectionnée en session avant d'accéder aux
 * pages métier. Un caissier/gestionnaire est toujours rattaché à une boutique
 * fixe (users.boutique_id). Il n'existe plus d'écran de gestion/sélection de
 * boutiques (application mono-boutique) : un gérant sans boutique assignée
 * bascule automatiquement sur l'unique boutique active existante.
 */
class EnsureBoutiqueSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        if (BoutiqueContext::estDefinie()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->boutique_id) {
            BoutiqueContext::definir($user->boutique_id);
            return $next($request);
        }

        $boutique = Boutique::where('active', true)->orderBy('id')->first();

        if (!$boutique) {
            abort(500, "Aucune boutique n'est configurée. Contactez votre prestataire.");
        }

        BoutiqueContext::definir($boutique->id);

        return $next($request);
    }
}
