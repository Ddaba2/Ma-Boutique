<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Services\VenteService;
use App\Support\BoutiqueContext;
use App\Support\ValidePrixCatalogue;
use Illuminate\Http\Request;

/**
 * Point d'entrée pour la synchronisation des ventes créées hors-ligne
 * (file d'attente IndexedDB côté navigateur, voir public/js/offline-ventes.js).
 * Distinct de VenteController::store() car le comportement en cas de stock
 * insuffisant diffère : ici on ne rejette jamais une vente déjà réalisée
 * physiquement par le caissier, on la marque simplement en conflit
 * (Vente::conflit_stock) pour signaler l'anomalie sans la bloquer.
 */
class VenteSyncController extends Controller
{
    public function sync(Request $request, VenteService $venteService)
    {
        $data = $request->validate([
            'uuid_client' => 'required|string|max:64',
            'client_nom' => 'required|string|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'mode_paiement' => 'required|in:espece,carte,mobile,autre',
            'montant_recu' => 'required|numeric|min:0',
            'lignes' => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite' => 'required|integer|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        if (! ValidePrixCatalogue::prixConformes($data['lignes'])) {
            return response()->json([
                'synced' => false,
                'message' => 'Le prix d\'un produit ne correspond pas au catalogue.',
            ], 422);
        }

        $existante = Vente::withoutGlobalScopes()->where('uuid_client', $data['uuid_client'])->first();

        if ($existante) {
            return response()->json([
                'synced' => true,
                'deja_synchronisee' => true,
                'reference' => $existante->reference,
                'conflit' => $existante->conflit_stock,
            ]);
        }

        try {
            $vente = $venteService->creerVente($data, BoutiqueContext::id(), tolererConflitStock: true);

            return response()->json([
                'synced' => true,
                'deja_synchronisee' => false,
                'reference' => $vente->reference,
                'conflit' => $vente->conflit_stock,
            ], 201);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'synced' => false,
                'message' => 'Erreur serveur lors de la synchronisation.',
            ], 500);
        }
    }

    /**
     * Rafraîchit le jeton CSRF avant de rejouer la file d'attente : la
     * session (et donc le token) peut avoir expiré pendant une longue
     * coupure réseau/électricité.
     */
    public function rafraichirCsrf()
    {
        return response()->json(['token' => csrf_token()]);
    }
}
