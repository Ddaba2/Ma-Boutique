<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\BoutiqueProduit;
use App\Support\BoutiqueContext;
use App\Services\VenteService;
use App\Exceptions\StockInsuffisantException;
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
        return view('ventes.create');
    }

    public function store(Request $request, VenteService $venteService)
    {
        $request->validate([
            'client_nom' => 'required|string|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'mode_paiement' => 'required|in:espece,carte,mobile,autre',
            'montant_recu' => 'required|numeric|min:0',
            'produit_ids' => 'required|array|min:1',
            'produit_ids.*' => 'required|exists:produits,id',
            'quantites' => 'required|array|min:1',
            'quantites.*' => 'required|integer|min:1',
            'prix_unitaires' => 'required|array|min:1',
            'prix_unitaires.*' => 'required|numeric|min:0',
        ]);

        $lignes = [];
        foreach ($request->produit_ids as $index => $produitId) {
            $lignes[] = [
                'produit_id' => $produitId,
                'quantite' => $request->quantites[$index],
                'prix_unitaire' => $request->prix_unitaires[$index],
            ];
        }

        try {
            $vente = $venteService->creerVente([
                'client_nom' => $request->client_nom,
                'client_telephone' => $request->client_telephone,
                'mode_paiement' => $request->mode_paiement,
                'montant_recu' => $request->montant_recu,
                'lignes' => $lignes,
            ], BoutiqueContext::id());

            return redirect()->route('ventes.index')
                ->with('success', 'Vente enregistrée avec succès! Référence: ' . $vente->reference . ', Total: ' . number_format($vente->total, 0, ',', ' ') . ' FCFA');

        } catch (StockInsuffisantException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
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

            // Remettre les produits en stock, dans la boutique où la vente a eu lieu
            foreach ($vente->detailVentes as $detail) {
                BoutiqueProduit::dansBoutique($vente->boutique_id)
                    ->where('produit_id', $detail->produit_id)
                    ->first()
                    ?->increment('stock_actuel', $detail->quantite);
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

    public function ticket(Vente $vente, Request $request)
    {
        $vente->load('detailVentes.produit');
        $entreprise = \App\Models\Entreprise::first();
        $largeur = $request->integer('largeur', 80) === 58 ? 58 : 80;

        return view('ventes.ticket', compact('vente', 'entreprise', 'largeur'));
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
