<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques principales demandées
        $totalStock = \App\Models\Produit::sum('stock_actuel');
        $ventesAujourdHui = \App\Models\Vente::whereDate('created_at', now()->today())->count();
        $stockFaible = \App\Models\Produit::whereColumn('stock_actuel', '<=', 'stock_min')->where('stock_actuel', '>', 0)->count();
        $produitsEnRupture = \App\Models\Produit::where('stock_actuel', 0)->count();
        
        // Historique des opérations du jour
        $aujourdHui = now()->today();
        $ventesDuJour = \App\Models\Vente::whereDate('created_at', $aujourdHui)
            ->with('detailVentes.produit')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Récupérer les entrées de stock du jour (produits créés aujourd'hui)
        $entreesStockDuJour = \App\Models\Produit::whereDate('created_at', $aujourdHui)
            ->where('stock_actuel', '>', 0)
            ->get()
            ->map(function($produit) {
                return (object) [
                    'created_at' => $produit->created_at,
                    'produit' => $produit,
                    'quantite' => $produit->stock_actuel,
                    'montant' => $produit->stock_actuel * $produit->prix_achat,
                    'type' => 'entree',
                    'motif' => 'Entrée de stock'
                ];
            });
        
        return view('dashboard', compact(
            'totalStock',
            'ventesAujourdHui',
            'stockFaible',
            'produitsEnRupture',
            'ventesDuJour',
            'entreesStockDuJour'
        ));
    }
}
