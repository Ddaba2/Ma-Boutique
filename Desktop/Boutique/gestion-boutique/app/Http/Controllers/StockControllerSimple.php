<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;

class StockControllerSimple extends Controller
{
    public function index()
    {
        // Récupérer tous les produits
        $produits = Produit::all();
        
        // Calculs précis basés sur la base de données
        $totalProduits = $produits->count();
        $produitsEnRupture = $produits->filter(function($produit) {
            return $produit->stock_actuel == 0;
        })->count();
        
        $produitsStockFaible = $produits->filter(function($produit) {
            return $produit->stock_actuel > 0 && $produit->stock_actuel <= ($produit->stock_min ?? 5);
        })->count();
        
        $produitsStockNormal = $produits->filter(function($produit) {
            return $produit->stock_actuel > ($produit->stock_min ?? 5);
        })->count();
        
        // Calculer la valeur totale du stock (au prix de vente pour plus de pertinence)
        $valeurTotaleStock = $produits->sum(function($produit) {
            return $produit->stock_actuel * ($produit->prix_vente ?? 0);
        });
        
        // Calculer le stock total en unités
        $stockTotalUnites = $produits->sum('stock_actuel');
        
        // Produits avec alertes (faible + rupture)
        $produitsEnAlerte = $produitsStockFaible + $produitsEnRupture;
        
        // Taux de disponibilité
        $tauxDisponibilite = $totalProduits > 0 ? round(($produitsStockNormal / $totalProduits) * 100, 1) : 0;

        return view('stocks.index', compact(
            'produits', 'totalProduits', 
            'produitsEnRupture', 'produitsStockFaible', 'produitsStockNormal',
            'valeurTotaleStock', 'stockTotalUnites', 'produitsEnAlerte', 'tauxDisponibilite'
        ));
    }

    public function create()
    {
        $produits = Produit::where('active', true)->get();
        
        // Statistiques pour la sidebar
        $totalProduits = Produit::count();
        $produitsStockNormal = Produit::where('stock_actuel', '>', 'stock_min')->count();
        $produitsStockFaible = Produit::whereColumn('stock_actuel', '<=', 'stock_min')->where('stock_actuel', '>', 0)->count();
        $produitsEnRupture = Produit::where('stock_actuel', 0)->count();
        
        return view('stocks.create', compact('produits', 'totalProduits', 'produitsStockNormal', 'produitsStockFaible', 'produitsEnRupture'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_nom' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1',
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
            'fournisseur' => 'nullable|string|max:255',
            'contact_fournisseur' => 'nullable|string|max:255',
            'motif' => 'nullable|string|max:255',
            'reference_facture' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            // Rechercher le produit par nom ou le créer s'il n'existe pas
            $produit = Produit::where('nom', 'like', '%' . $request->produit_nom . '%')->first();
            
            if (!$produit) {
                // Créer un nouveau produit
                $produit = new Produit();
                $produit->nom = $request->produit_nom;
                $produit->reference = 'PROD-' . date('Y') . '-' . str_pad(Produit::count() + 1, 4, '0', STR_PAD_LEFT);
                $produit->prix_achat = $request->prix_achat ?? 0;
                $produit->prix_vente = $request->prix_vente ?? $request->prix_achat ?? 0;
                $produit->stock_actuel = 0;
                $produit->stock_min = 5; // Valeur par défaut
                $produit->active = true;
                $produit->save();
            }
            
            // Mettre à jour les prix si fournis
            if ($request->prix_achat) {
                $produit->prix_achat = $request->prix_achat;
            }
            if ($request->prix_vente) {
                $produit->prix_vente = $request->prix_vente;
            }
            
            // Mettre à jour le stock du produit
            $produit->stock_actuel += $request->quantite;
            $produit->save();

            // Créer un mouvement de stock (si vous avez une table pour ça)
            // Pour l'instant, on redirige avec un message de succès

            return redirect()->route('stocks.index')
                ->with('success', 'Stock ajouté avec succès! Produit: ' . $produit->nom . ', Quantité: ' . $request->quantite . 
                    ($produit->wasRecentlyCreated ? ' (Nouveau produit créé)' : ''));

        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du stock: ' . $e->getMessage())
                ->withInput();
        }
    }
}
