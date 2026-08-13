<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\BoutiqueProduit;
use App\Models\MouvementStock;
use App\Support\BoutiqueContext;
use App\Support\PdfBranding;
use App\Services\VenteService;
use App\Exceptions\StockInsuffisantException;
use App\Exceptions\MontantInsuffisantException;
use App\Models\Produit;
use App\Support\ValidePrixCatalogue;
use Illuminate\Support\Facades\Auth;
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

        // Le prix unitaire vient du formulaire (nécessaire pour l'afficher
        // dans le panier avant envoi), mais ne doit jamais être source de
        // vérité : un caissier pourrait le modifier depuis les outils
        // développeur du navigateur. On vérifie qu'il correspond au prix de
        // vente réel du catalogue avant d'accepter la vente.
        if (!ValidePrixCatalogue::prixConformes($lignes)) {
            return back()->with('error', 'Le prix d\'un produit a changé, veuillez réessayer la vente.')->withInput();
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

        } catch (StockInsuffisantException|MontantInsuffisantException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement de la vente. Veuillez réessayer.')
                ->withInput();
        }
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

    /**
     * Annule une vente déjà encaissée : ni la vente ni ses lignes ne sont
     * supprimées (l'historique/les rapports doivent rester exacts), le stock
     * est restitué avec un mouvement de stock traçable, et le statut passe à
     * 'annulee'. Une vente déjà annulée ne peut pas l'être une seconde fois
     * (le stock serait restitué deux fois).
     */
    public function destroy(Vente $vente)
    {
        if ($vente->statut === 'annulee') {
            return redirect()->route('ventes.index')
                ->with('error', 'Cette vente est déjà annulée.');
        }

        try {
            DB::transaction(function () use ($vente) {
                foreach ($vente->detailVentes as $detail) {
                    BoutiqueProduit::dansBoutique($vente->boutique_id)
                        ->where('produit_id', $detail->produit_id)
                        ->first()
                        ?->increment('stock_actuel', $detail->quantite);

                    MouvementStock::create([
                        'boutique_id' => $vente->boutique_id,
                        'produit_id' => $detail->produit_id,
                        'type' => 'retour_client',
                        'quantite' => $detail->quantite,
                        'prix_unitaire' => $detail->prix_unitaire,
                        'total' => $detail->total_ligne,
                        'reference' => MouvementStock::generateReference(),
                        'motif' => 'Annulation vente ' . $vente->reference,
                        'date_mouvement' => now(),
                        'notes' => 'Annulée par ' . (Auth::user()->name ?? 'système'),
                    ]);
                }

                $vente->update(['statut' => 'annulee']);
            });

            return redirect()->route('ventes.index')
                ->with('success', 'Vente annulée avec succès, le stock a été restitué.');

        } catch (\Exception $e) {
            report($e);
            return redirect()->route('ventes.index')
                ->with('error', "Erreur lors de l'annulation de la vente.");
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
        $vente->load(['detailVentes.produit', 'client']);

        $pdf = \PDF::loadView('ventes.facture', array_merge(
            compact('vente'),
            PdfBranding::forView()
        ));
        
        // Nom du fichier
        $filename = 'facture_' . $vente->reference . '_' . $vente->created_at->format('Y-m-d') . '.pdf';
        
        // Télécharger le PDF
        return $pdf->download($filename);
    }
}
