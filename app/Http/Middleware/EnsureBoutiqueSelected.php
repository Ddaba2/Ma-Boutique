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
 * fixe (users.boutique_id) ; un gérant sans boutique choisie doit en choisir
 * une (ou n'en a tout simplement aucune encore créée).
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

        if ($request->routeIs('boutiques.*')) {
            return $next($request);
        }

        if ($user->role !== 'gerant') {
            abort(403, 'Aucune boutique ne vous est assignée. Contactez le gérant.');
        }

        if (Boutique::where('active', true)->doesntExist()) {
            return redirect()->route('boutiques.create')
                ->with('info', 'Créez votre première boutique pour commencer.');
        }

        return redirect()->route('boutiques.selectionner');
    }
}
