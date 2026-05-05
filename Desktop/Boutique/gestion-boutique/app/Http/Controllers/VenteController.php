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
            'produit_nom' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1',
            'prix_unitaire' => 'required|numeric|min:0',
            'montant_recu' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Rechercher ou créer le produit
            $produit = Produit::where('nom', 'like', '%' . $request->produit_nom . '%')->first();
            
            if (!$produit) {
                return back()->with('error', 'Produit non trouvé: ' . $request->produit_nom)->withInput();
            }

            // Vérifier le stock
            if ($produit->stock_actuel < $request->quantite) {
                return back()->with('error', 'Stock insuffisant. Stock disponible: ' . $produit->stock_actuel)->withInput();
            }

            // Calculer le total et la monnaie
            $total = $request->quantite * $request->prix_unitaire;
            $montantRecu = $request->montant_recu;
            $monnaie = $montantRecu - $total;

            // Créer la vente
            $reference = 'VENTE' . date('Y') . str_pad(Vente::count() + 1, 5, '0', STR_PAD_LEFT);
            
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

            // Créer le détail de vente
            DetailVente::create([
                'vente_id' => $vente->id,
                'produit_id' => $produit->id,
                'quantite' => $request->quantite,
                'prix_unitaire' => $request->prix_unitaire,
                'total_ligne' => $total
            ]);

            // Mettre à jour le stock
            $produit->stock_actuel -= $request->quantite;
            $produit->save();

            DB::commit();

            return redirect()->route('ventes.index')
                ->with('success', 'Vente enregistrée avec succès! Référence: ' . $reference . ', Total: ' . number_format($total, 0, ',', ' ') . ' FCFA');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement de la vente: ' . $e->getMessage())
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

        $request->validate([
            'client_nom' => 'nullable|string|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'mode_paiement' => 'required|in:espece,carte,mobile,autre'
        ]);

        $vente->update($request->all());

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
