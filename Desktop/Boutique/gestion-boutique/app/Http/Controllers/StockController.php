<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\MouvementStock;
use App\Models\Categorie;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $categories = Categorie::where('active', true)->get();
        $produits = Produit::with('categorie')
            ->when(request('categorie_id'), function($query, $categorieId) {
                return $query->where('categorie_id', $categorieId);
            })
            ->when(request('statut_stock'), function($query, $statut) {
                if ($statut == 'rupture') {
                    return $query->where('stock_actuel', 0);
                } elseif ($statut == 'faible') {
                    return $query->whereColumn('stock_actuel', '<=', 'stock_min')
                              ->where('stock_actuel', '>', 0);
                } elseif ($statut == 'normal') {
                    return $query->whereColumn('stock_actuel', '>', 'stock_min');
                }
            })
            ->get();

        $totalProduits = $produits->count();
        $produitsEnRupture = $produits->where('stock_actuel', 0)->count();
        $produitsStockFaible = $produits->where('stock_actuel', '<=', 'stock_min')
                                     ->where('stock_actuel', '>', 0)->count();
        $produitsStockNormal = $produits->where('stock_actuel', '>', 'stock_min')->count();

        $valeurStock = $produits->sum(function($produit) {
            return $produit->stock_actuel * $produit->prix_achat;
        });

        $stockParCategorie = $produits->groupBy('categorie_id')->map(function($catProduits) {
            return [
                'nombre' => $catProduits->count(),
                'quantite' => $catProduits->sum('stock_actuel'),
                'valeur' => $catProduits->sum(function($p) {
                    return $p->stock_actuel * $p->prix_achat;
                })
            ];
        });

        return view('stocks.index', compact(
            'produits', 'categories', 'totalProduits', 
            'produitsEnRupture', 'produitsStockFaible', 'produitsStockNormal',
            'valeurStock', 'stockParCategorie'
        ));
    }

    public function create()
    {
        $categories = Categorie::where('active', true)->get();
        $produits = Produit::where('active', true)->get();
        return view('stocks.create', compact('categories', 'produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'fournisseur' => 'nullable|string|max:255',
            'motif' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'date_mouvement' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $produit = Produit::findOrFail($request->produit_id);
            
            // Calculer le total
            $total = $request->quantite * $request->prix_unitaire;

            // Créer le mouvement de stock
            $mouvement = MouvementStock::create([
                'produit_id' => $request->produit_id,
                'type' => 'entree',
                'quantite' => $request->quantite,
                'prix_unitaire' => $request->prix_unitaire,
                'total' => $total,
                'reference' => MouvementStock::generateReference(),
                'fournisseur' => $request->fournisseur,
                'motif' => $request->motif ?? 'Entrée de stock',
                'date_mouvement' => $request->date_mouvement,
                'notes' => $request->notes
            ]);

            // Mettre à jour le stock du produit
            $produit->stock_actuel += $request->quantite;
            $produit->save();

            DB::commit();

            return redirect()->route('stocks.index')
                ->with('success', 'Stock ajouté avec succès! Référence: ' . $mouvement->reference);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function mouvements()
    {
        $mouvements = MouvementStock::with('produit')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('stocks.mouvements', compact('mouvements'));
    }

    public function historique($produit_id)
    {
        $produit = Produit::findOrFail($produit_id);
        $mouvements = MouvementStock::where('produit_id', $produit_id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('stocks.historique', compact('produit', 'mouvements'));
    }
}
