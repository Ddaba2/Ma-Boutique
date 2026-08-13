<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\MouvementStock;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Support\BoutiqueContext;
use App\Support\ReglesChamps;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        $boutiqueId = BoutiqueContext::id();
        $categories = Categorie::where('active', true)->get();

        $stocks = BoutiqueProduit::dansBoutique($boutiqueId)
            ->with('produit.categorie')
            ->when(request('categorie_id'), function ($query, $categorieId) {
                return $query->whereHas('produit', fn($q) => $q->where('categorie_id', $categorieId));
            })
            ->parStatutStock(request('statut_stock'))
            ->get();

        // Vue historique inchangée : on fusionne les infos catalogue (Produit)
        // et le stock de la boutique courante (BoutiqueProduit) en un seul objet.
        $produits = BoutiqueProduit::fusionnerAvecCatalogue($stocks);

        $totalProduits = $produits->count();
        $produitsEnRupture = $stocks->filter(fn($s) => $s->statutStock() === 'rupture')->count();
        $produitsStockFaible = $stocks->filter(fn($s) => $s->statutStock() === 'faible')->count();
        $produitsStockNormal = $stocks->filter(fn($s) => $s->statutStock() === 'normal')->count();

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

        $stats = $this->statistiquesStock(BoutiqueContext::id());

        return view('stocks.create', compact('categories', 'produits') + $stats);
    }

    /**
     * Compteurs par état de stock pour la boutique courante, utilisés à la
     * fois par l'écran de liste et par le résumé affiché lors d'une saisie
     * d'entrée de stock.
     */
    private function statistiquesStock(?int $boutiqueId): array
    {
        $stocks = BoutiqueProduit::dansBoutique($boutiqueId)->get();

        return [
            'totalProduits' => $stocks->count(),
            'produitsEnRupture' => $stocks->filter(fn($s) => $s->statutStock() === 'rupture')->count(),
            'produitsStockFaible' => $stocks->filter(fn($s) => $s->statutStock() === 'faible')->count(),
            'produitsStockNormal' => $stocks->filter(fn($s) => $s->statutStock() === 'normal')->count(),
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'produit_nom' => ['required', 'string', 'max:255', ReglesChamps::NOM_SANS_HTML],
            'quantite' => 'required|integer|min:1',
            'prix_achat' => 'nullable|numeric|min:0',
            'prix_vente' => 'nullable|numeric|min:0',
            'fournisseur' => 'nullable|string|max:255',
            'contact_fournisseur' => 'nullable|string|max:255',
            'motif' => 'nullable|string|max:255',
            'reference_facture' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $boutiqueId = BoutiqueContext::id();

        DB::beginTransaction();
        try {
            // Rechercher le produit exact ou approchant (catalogue partagé entre boutiques)
            $produit = Produit::where('nom', $request->produit_nom)->first();

            if (!$produit) {
                $produit = Produit::where('nom', 'like', '%' . $request->produit_nom . '%')->first();
            }

            $isNew = false;
            if (!$produit) {
                // Créer le produit s'il n'existe pas
                $isNew = true;
                $produit = new Produit();
                $produit->nom = $request->produit_nom;
                $produit->reference = Produit::generateReference();
                $produit->prix_achat = $request->prix_achat ?? 0;
                $produit->prix_vente = $request->prix_vente ?? $request->prix_achat ?? 0;
                $produit->stock_actuel = 0;
                $produit->stock_min = 5;
                $produit->active = true;
                $produit->save();

                // Chaque boutique démarre avec une ligne de stock à 0 pour ce nouveau produit.
                BoutiqueProduit::initialiserPourNouveauProduit($produit);
            } else {
                // Mettre à jour les prix si fournis pour un produit existant
                if ($request->prix_achat) {
                    $produit->prix_achat = $request->prix_achat;
                }
                if ($request->prix_vente) {
                    $produit->prix_vente = $request->prix_vente;
                }
            }

            $stockBoutique = BoutiqueProduit::dansBoutique($boutiqueId)
                ->where('produit_id', $produit->id)
                ->lockForUpdate()
                ->first();

            if (!$stockBoutique) {
                $stockBoutique = BoutiqueProduit::create([
                    'boutique_id' => $boutiqueId,
                    'produit_id' => $produit->id,
                    'stock_actuel' => 0,
                    'stock_min' => 5,
                    'stock_max' => 100,
                ]);
            }

            // Déterminer le prix unitaire d'achat pour le mouvement
            $prixUnitaire = $request->prix_achat ?? $produit->prix_achat ?? 0;
            $total = $request->quantite * $prixUnitaire;

            // Construire les notes de mouvement de stock
            $notesMvt = "";
            if ($request->reference_facture) {
                $notesMvt .= "Facture N°: " . $request->reference_facture . "\n";
            }
            if ($request->contact_fournisseur) {
                $notesMvt .= "Contact fournisseur: " . $request->contact_fournisseur . "\n";
            }
            if ($request->notes) {
                $notesMvt .= "Notes: " . $request->notes;
            }

            // Créer le mouvement de stock pour la traçabilité
            $mouvement = MouvementStock::create([
                'produit_id' => $produit->id,
                'type' => 'entree',
                'quantite' => $request->quantite,
                'prix_unitaire' => $prixUnitaire,
                'total' => $total,
                'reference' => MouvementStock::generateReference(),
                'fournisseur' => $request->fournisseur,
                'motif' => $request->motif ?? ($isNew ? 'Création de produit et entrée initiale' : 'Réapprovisionnement de stock'),
                'date_mouvement' => now(),
                'notes' => trim($notesMvt) ?: null
            ]);

            // Mettre à jour le stock actuel de la boutique courante
            $stockBoutique->increment('stock_actuel', $request->quantite);

            DB::commit();

            $successMsg = 'Stock ajouté avec succès! ';
            $successMsg .= 'Produit: ' . $produit->nom . ', ';
            $successMsg .= 'Quantité: +' . $request->quantite . ', ';
            $successMsg .= 'Mouvement: ' . $mouvement->reference;
            if ($isNew) {
                $successMsg .= ' (Nouveau produit enregistré)';
            }

            return redirect()->route('stocks.index')->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout du stock. Veuillez réessayer.')
                ->withInput();
        }
    }

    public function mouvements()
    {
        // MouvementStock utilise BelongsToBoutique : le scope global filtre déjà
        // sur la boutique courante, pas besoin de filtrer explicitement ici.
        $mouvements = MouvementStock::with('produit')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('stocks.mouvements', compact('mouvements'));
    }

    public function historique(Produit $produit)
    {
        $mouvements = MouvementStock::where('produit_id', $produit->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('stocks.historique', compact('produit', 'mouvements'));
    }

    public function ajusterForm(Produit $produit)
    {
        $stockBoutique = $produit->stockDans(BoutiqueContext::id());
        return view('stocks.ajuster', compact('produit', 'stockBoutique'));
    }

    public function ajuster(Request $request, Produit $produit)
    {
        $request->validate([
            'nouveau_stock' => 'required|integer|min:0',
            'motif' => 'required|string|max:255',
        ]);

        $boutiqueId = BoutiqueContext::id();

        DB::beginTransaction();
        try {
            $stockBoutique = BoutiqueProduit::dansBoutique($boutiqueId)
                ->where('produit_id', $produit->id)
                ->lockForUpdate()
                ->first();

            if (!$stockBoutique) {
                $stockBoutique = BoutiqueProduit::create([
                    'boutique_id' => $boutiqueId,
                    'produit_id' => $produit->id,
                    'stock_actuel' => 0,
                    'stock_min' => 5,
                    'stock_max' => 100,
                ]);
            }

            $ancienStock = $stockBoutique->stock_actuel;
            $nouveauStock = (int) $request->nouveau_stock;
            $ecart = $nouveauStock - $ancienStock;

            if ($ecart !== 0) {
                MouvementStock::create([
                    'boutique_id' => $boutiqueId,
                    'produit_id' => $produit->id,
                    'type' => 'ajout_manuel',
                    'quantite' => abs($ecart),
                    'reference' => MouvementStock::generateReference(),
                    'motif' => $request->motif,
                    'date_mouvement' => now(),
                    'notes' => "Ajustement d'inventaire : {$ancienStock} → {$nouveauStock} (" . ($ecart > 0 ? '+' : '') . "{$ecart})",
                ]);

                $stockBoutique->update(['stock_actuel' => $nouveauStock]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', "Erreur lors de l'ajustement du stock.")->withInput();
        }

        return redirect()->route('stocks.index')->with('success', "Stock de « {$produit->nom} » ajusté avec succès.");
    }
}
