<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $produits = Produit::where('nom', 'like', '%' . $query . '%')
            ->where('active', true)
            ->where('stock_actuel', '>', 0)
            ->get(['id', 'nom', 'prix_vente', 'stock_actuel', 'stock_min']);

        return response()->json($produits);
    }
}
