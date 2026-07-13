<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Support\BoutiqueContext;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $boutiqueId = BoutiqueContext::id();

        $produits = Produit::where('nom', 'like', '%' . $query . '%')
            ->where('active', true)
            ->whereHas('boutiqueProduits', function ($q) use ($boutiqueId) {
                $q->dansBoutique($boutiqueId)->where('stock_actuel', '>', 0);
            })
            ->with(['boutiqueProduits' => function ($q) use ($boutiqueId) {
                $q->dansBoutique($boutiqueId);
            }])
            ->get(['id', 'nom', 'prix_vente']);

        return response()->json($produits->map(function ($produit) {
            $stock = $produit->boutiqueProduits->first();

            return [
                'id' => $produit->id,
                'nom' => $produit->nom,
                'prix_vente' => $produit->prix_vente,
                'stock_actuel' => $stock->stock_actuel ?? 0,
                'stock_min' => $stock->stock_min ?? 5,
            ];
        }));
    }
}
