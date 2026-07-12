<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    public function index()
    {
        $ventes = Vente::with('detailVentes.produit')->orderBy('created_at', 'desc')->paginate(10);
        return view('ventes.index', compact('ventes'));
    }

    public function create()
    {
        $produits = Produit::where('active', true)->where('stock_actuel', '>', 0)->get();
        return view('ventes.create', compact('produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_nom' => 'required|string|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'mode_paiement' => 'required|string',
            'montant_recu' => 'required|numeric|min:0',
            'produit_ids' => 'required|array|min:1',
            'produit_ids.*' => 'required|exists:produits,id',
            'quantites' => 'required|array|min:1',
            'quantites.*' => 'required|integer|min:1',
            'prix_unitaires' => 'required|array|min:1',
            'prix_unitaires.*' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            $items = [];

            // Valider les stocks et calculer le total
            foreach ($request->produit_ids as $index => $produitId) {
                $produit = Produit::findOrFail($produitId);
                $qty = $request->quantites[$index];
                $prixUnitaire = $request->prix_unitaires[$index];

                if ($produit->stock_actuel < $qty) {
                    return back()->with('error', 'Stock insuffisant pour le produit : ' . $produit->nom . '. Stock disponible: ' . $produit->stock_actuel)->withInput();
                }

                $ligneTotal = $qty * $prixUnitaire;
                $total += $ligneTotal;

                $items[] = [
                    'produit' => $produit,
                    'quantite' => $qty,
                    'prix_unitaire' => $prixUnitaire,
                    'total_ligne' => $ligneTotal
                ];
            }

            $montantRecu = $request->montant_recu;
            $monnaie = $montantRecu - $total;

            // Créer la vente
            $reference = Vente::generateReference();

            $vente = Vente::create([
                'reference' => $reference,
                'total' => $total,
                'montant_recu' => $montantRecu,
                'monnaie' => $monnaie,
                'client_nom' => $request->client_nom,
                'client_telephone' => $request->client_telephone,
                'mode_paiement' => $request->mode_paiement,
                'statut' => 'terminee'
            ]);

            // Créer les détails de la vente et enregistrer les mouvements de stock
            foreach ($items as $item) {
                $produit = $item['produit'];
                $qty = $item['quantite'];

                DetailVente::create([
                    'vente_id' => $vente->id,
                    'produit_id' => $produit->id,
                    'quantite' => $qty,
                    'prix_unitaire' => $item['prix_unitaire'],
                    'total_ligne' => $item['total_ligne']
                ]);

                // Mettre à jour le stock
                $produit->stock_actuel -= $qty;
                $produit->save();

                // Créer le mouvement de stock pour la traçabilité des sorties (ventes)
                \App\Models\MouvementStock::create([
                    'produit_id' => $produit->id,
                    'type' => 'sortie',
                    'quantite' => $qty,
                    'prix_unitaire' => $item['prix_unitaire'],
                    'total' => $item['total_ligne'],
                    'reference' => \App\Models\MouvementStock::generateReference(),
                    'motif' => 'Vente facture ' . $reference,
                    'date_mouvement' => now(),
                    'notes' => 'Client: ' . $request->client_nom
                ]);
            }

            DB::commit();

            return redirect()->route('ventes.index')
                ->with('success', 'Vente enregistrée avec succès! Référence: ' . $reference . ', Total: ' . number_format($total, 0, ',', ' ') . ' FCFA');

        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement de la vente. Veuillez réessayer.')
                ->withInput();
        }
    }

    public function storePos(Request $request)
    {
        return $this->store($request);
    }

    public function show(Vente $vente)
    {
        $vente->load('detailVentes.produit');
        return view('ventes.show', compact('vente'));
    }

    public function edit(Vente $vente)
    {
        // Les ventes terminées ne peuvent pas être modifiées
        if ($vente->statut === 'terminee') {
            return redirect()->route('ventes.index')
                ->with('error', 'Une vente terminée ne peut pas être modifiée.');
        }

        return view('ventes.edit', compact('vente'));
    }

    public function update(Request $request, Vente $vente)
    {
        // Les ventes terminées ne peuvent pas être modifiées
        if ($vente->statut === 'terminee') {
            return redirect()->route('ventes.index')
                ->with('error', 'Une vente terminée ne peut pas être modifiée.');
        }

        $data = $request->validate([
            'client_nom' => 'nullable|string|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'mode_paiement' => 'required|in:espece,carte,mobile,autre'
        ]);

        $vente->update($data);

        return redirect()->route('ventes.index')
            ->with('success', 'Vente mise à jour avec succès.');
    }

    public function destroy(Vente $vente)
    {
        try {
            DB::beginTransaction();

            // Remettre les produits en stock
            foreach ($vente->detailVentes as $detail) {
                $detail->produit->increment('stock_actuel', $detail->quantite);
            }

            // Supprimer la vente
            $vente->delete();

            DB::commit();

            return redirect()->route('ventes.index')
                ->with('success', 'Vente supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            return redirect()->route('ventes.index')
                ->with('error', 'Erreur lors de la suppression de la vente.');
        }
    }

    public function facture(Vente $vente)
    {
        // Charger les détails de la vente avec les produits
        $vente->load('detailVentes.produit');
        
        // Récupérer les informations de l'entreprise
        $entreprise = \App\Models\Entreprise::first();
        
        // Générer le PDF
        $pdf = \PDF::loadView('ventes.facture', compact('vente', 'entreprise'));
        
        // Nom du fichier
        $filename = 'facture_' . $vente->reference . '_' . $vente->created_at->format('Y-m-d') . '.pdf';
        
        // Télécharger le PDF
        return $pdf->download($filename);
    }
}
