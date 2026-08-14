<?php

namespace App\Http\Controllers;

use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\JournalActivite;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Support\BoutiqueContext;
use App\Support\ReglesChamps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $boutiqueId = BoutiqueContext::id();
        $search = trim((string) $request->query('search', ''));

        $produits = Produit::with(['categorie', 'boutiqueProduits' => function ($q) use ($boutiqueId) {
            $q->dansBoutique($boutiqueId);
        }])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        // Le stock affiché est celui de la boutique courante, pas les colonnes
        // héritées sur produits (conservées uniquement pour compatibilité).
        $produits->getCollection()->transform(function (Produit $produit) {
            $stock = $produit->boutiqueProduits->first();
            $produit->stock_actuel = $stock->stock_actuel ?? 0;
            $produit->stock_min = $stock->stock_min ?? 5;

            return $produit;
        });

        return view('produits.index', compact('produits'));
    }

    public function create()
    {
        $categories = Categorie::where('active', true)->get();

        return view('produits.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255', ReglesChamps::NOM_SANS_HTML],
            'description' => 'nullable|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock_actuel' => 'required|integer|min:0',
            'stock_min' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'image' => ['nullable', 'string', ReglesChamps::NOM_SANS_HTML],
        ]);

        DB::beginTransaction();
        try {
            $produit = Produit::create([
                'reference' => Produit::generateReference(),
                'nom' => $request->nom,
                'description' => $request->description,
                'prix_achat' => $request->prix_achat,
                'prix_vente' => $request->prix_vente,
                'stock_actuel' => $request->stock_actuel,
                'stock_min' => $request->stock_min,
                'stock_max' => $request->stock_max,
                'categorie_id' => $request->categorie_id,
                'image' => $request->image,
                'active' => true,
            ]);

            // Catalogue partagé, stock séparé : la boutique courante reçoit le
            // stock initial saisi, les autres boutiques démarrent à 0.
            BoutiqueProduit::initialiserPourNouveauProduit(
                $produit,
                BoutiqueContext::id(),
                (int) $request->stock_actuel,
                (int) $request->stock_min,
                (int) $request->stock_max
            );

            DB::commit();

            return redirect()->route('produits.index')
                ->with('success', 'Produit créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Une erreur est survenue lors de la création du produit.')->withInput();
        }
    }

    public function show(Produit $produit)
    {
        $stockBoutique = $produit->stockDans(BoutiqueContext::id());
        $derniersMouvements = MouvementStock::where('produit_id', $produit->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('produits.show', compact('produit', 'stockBoutique', 'derniersMouvements'));
    }

    public function edit(Produit $produit)
    {
        $categories = Categorie::where('active', true)->get();
        $stockBoutique = $produit->stockDans(BoutiqueContext::id());

        return view('produits.edit', compact('produit', 'categories', 'stockBoutique'));
    }

    public function update(Request $request, Produit $produit)
    {
        // Le stock ne se modifie plus depuis la fiche produit : il n'est fiable
        // que via le module Stock (entrée/sortie tracée), pas en écrasant une
        // quantité à la main ici — voir StockController et BoutiqueProduit.
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', ReglesChamps::NOM_SANS_HTML],
            'description' => 'nullable|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'image' => ['nullable', 'string', ReglesChamps::NOM_SANS_HTML],
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active');

        $ancienPrixAchat = (float) $produit->prix_achat;
        $ancienPrixVente = (float) $produit->prix_vente;
        $nouveauPrixAchat = (float) $data['prix_achat'];
        $nouveauPrixVente = (float) $data['prix_vente'];

        $produit->update($data);

        if ($ancienPrixAchat !== $nouveauPrixAchat || $ancienPrixVente !== $nouveauPrixVente) {
            JournalActivite::enregistrer(
                'produit.prix_modifie',
                "Prix de « {$produit->nom} » modifié : achat {$ancienPrixAchat} → {$nouveauPrixAchat}, vente {$ancienPrixVente} → {$nouveauPrixVente}.",
                $produit
            );
        }

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit)
    {
        // Désactivation plutôt que suppression physique : un produit déjà vendu
        // est référencé par des lignes de vente/commande et des mouvements de
        // stock historiques (contrainte cascade en base) — le supprimer pour de
        // vrai effacerait silencieusement cet historique. Comme pour les
        // clients/fournisseurs, on désactive et on le masque du catalogue actif.
        $produit->update(['active' => false]);

        return redirect()->route('produits.index')
            ->with('success', 'Produit désactivé et retiré du catalogue actif.');
    }

    public function barcode(Produit $produit)
    {
        return view('produits.barcode', compact('produit'));
    }

    public function barcodeAll()
    {
        $produits = Produit::where('active', true)->orderBy('nom')->get();

        return view('produits.barcode_all', compact('produits'));
    }

    public function importForm()
    {
        return view('produits.import');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'fichier_csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('fichier_csv');
        $handle = fopen($file->getRealPath(), 'r');

        $header = null;
        $ligne = 1;
        $created = 0;
        $errors = [];
        $categories = Categorie::pluck('id', 'nom')->toArray();
        $defaultCategorie = Categorie::first()?->id;
        $boutiqueId = BoutiqueContext::id();

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ';')) !== false) {
                $ligne++;

                if (! $header) {
                    $header = array_map('trim', $row);

                    continue;
                }

                if (count($row) < 4) {
                    $errors[] = "Ligne {$ligne} : nombre de colonnes insuffisant.";

                    continue;
                }

                $data = array_combine($header, array_map('trim', $row));

                $nom = $data['nom'] ?? null;
                if (! $nom) {
                    $errors[] = "Ligne {$ligne} : nom manquant.";

                    continue;
                }

                // Même règle que le formulaire de création (voir ProduitController::store)
                // pour ne pas avoir deux définitions de ce qu'est un nom valide.
                $validationNom = Validator::make(['nom' => $nom], ['nom' => ['string', 'max:255', ReglesChamps::NOM_SANS_HTML]]);
                if ($validationNom->fails()) {
                    $errors[] = "Ligne {$ligne} : nom « {$nom} » invalide (caractères < ou > interdits).";

                    continue;
                }

                $categorieId = $defaultCategorie;
                if (! empty($data['categorie'])) {
                    $categorieId = $categories[$data['categorie']] ?? $defaultCategorie;
                }

                $stockActuel = (int) ($data['stock_actuel'] ?? 0);
                $stockMin = (int) ($data['stock_min'] ?? 5);
                $stockMax = (int) ($data['stock_max'] ?? 100);

                $produitImporte = Produit::create([
                    'reference' => Produit::generateReference(),
                    'nom' => $nom,
                    'description' => $data['description'] ?? null,
                    'prix_achat' => (float) str_replace(',', '.', $data['prix_achat'] ?? 0),
                    'prix_vente' => (float) str_replace(',', '.', $data['prix_vente'] ?? 0),
                    'stock_actuel' => $stockActuel,
                    'stock_min' => $stockMin,
                    'stock_max' => $stockMax,
                    'categorie_id' => $categorieId,
                    'active' => true,
                ]);

                BoutiqueProduit::initialiserPourNouveauProduit(
                    $produitImporte,
                    $boutiqueId,
                    $stockActuel,
                    $stockMin,
                    $stockMax
                );

                $created++;
            }
            fclose($handle);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            report($e);

            return back()->with('error', 'Erreur lors de l\'import du fichier. Vérifiez son format et réessayez.');
        }

        $redirect = redirect()->route('produits.index')
            ->with('success', "$created produit(s) importé(s) avec succès.");

        if (! empty($errors)) {
            $resume = count($errors) > 5
                ? implode(' | ', array_slice($errors, 0, 5)).' | … et '.(count($errors) - 5).' autre(s) ligne(s) ignorée(s).'
                : implode(' | ', $errors);

            $redirect->with('warning', count($errors).' ligne(s) ignorée(s) : '.$resume);
        }

        return $redirect;
    }

    public function exportCsvTemplate()
    {
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8'];
        $filename = 'modele_import_produits.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['nom', 'description', 'prix_achat', 'prix_vente', 'stock_actuel', 'stock_min', 'stock_max', 'categorie'], ';');
            fputcsv($handle, ['Exemple Produit', 'Description exemple', '1000', '1500', '20', '5', '100', 'Catégorie 1'], ';');
            fclose($handle);
        }, $filename, $headers);
    }
}
