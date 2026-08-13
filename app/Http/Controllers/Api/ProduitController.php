<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Support\BoutiqueContext;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    /**
     * @param inclure_ruptures Si true, ne filtre pas sur stock_actuel > 0.
     *   Utilisé par l'écran de vente (on ne peut vendre que ce qui reste en
     *   stock) vs. l'écran d'entrée de stock, où chercher un produit déjà en
     *   rupture est justement le cas d'usage principal — sans ce paramètre,
     *   l'autocomplete masquerait les produits qu'on cherche à réapprovisionner
     *   et pousserait à recréer des doublons par erreur de frappe.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $boutiqueId = BoutiqueContext::id();
        $inclureRuptures = $request->boolean('inclure_ruptures');

        $produits = Produit::where('nom', 'like', '%' . $query . '%')
            ->where('active', true)
            ->whereHas('boutiqueProduits', function ($q) use ($boutiqueId, $inclureRuptures) {
                $q->dansBoutique($boutiqueId);
                if (!$inclureRuptures) {
                    $q->where('stock_actuel', '>', 0);
                }
            })
            ->with(['boutiqueProduits' => function ($q) use ($boutiqueId) {
                $q->dansBoutique($boutiqueId);
            }])
            ->get(['id', 'nom', 'prix_vente', 'prix_achat']);

        return response()->json($produits->map(function ($produit) {
            $stock = $produit->boutiqueProduits->first();

            return [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'prix_vente' => $produit->prix_vente,
                'prix_achat' => $produit->prix_achat,
                'stock_actuel' => $stock->stock_actuel ?? 0,
                'stock_min' => $stock->stock_min ?? 5,
            ];
        }));
    }
}
