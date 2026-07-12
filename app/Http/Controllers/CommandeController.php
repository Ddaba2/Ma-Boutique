<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\DetailCommande;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        $query = Commande::with('fournisseur')->orderBy('created_at', 'desc');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $commandes = $query->paginate(15);
        return view('commandes.index', compact('commandes'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::where('active', true)->orderBy('nom')->get();
        $produits     = Produit::where('active', true)->orderBy('nom')->get();
        return view('commandes.create', compact('fournisseurs', 'produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fournisseur_id'       => 'required|exists:fournisseurs,id',
            'date_livraison_prevue'=> 'nullable|date|after_or_equal:today',
            'notes'                => 'nullable|string',
            'produit_ids'          => 'required|array|min:1',
            'produit_ids.*'        => 'required|exists:produits,id',
            'quantites'            => 'required|array|min:1',
            'quantites.*'          => 'required|integer|min:1',
            'prix_unitaires'       => 'required|array|min:1',
            'prix_unitaires.*'     => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            $reference = Commande::generateReference();

            $commande = Commande::create([
                'reference'            => $reference,
                'fournisseur_id'       => $request->fournisseur_id,
                'statut'               => 'en_attente',
                'date_livraison_prevue'=> $request->date_livraison_prevue,
                'total'                => 0,
                'notes'                => $request->notes,
                'user_id'              => Auth::id(),
            ]);

            foreach ($request->produit_ids as $i => $produitId) {
                $qty   = $request->quantites[$i];
                $prix  = $request->prix_unitaires[$i];
                $ligne = $qty * $prix;
                $total += $ligne;

                DetailCommande::create([
                    'commande_id'  => $commande->id,
                    'produit_id'   => $produitId,
                    'quantite'     => $qty,
                    'prix_unitaire'=> $prix,
                    'total_ligne'  => $ligne,
                ]);
            }

            $commande->update(['total' => $total]);
            DB::commit();

            return redirect()->route('commandes.show', $commande)
                ->with('success', 'Bon de commande ' . $reference . ' créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Une erreur est survenue lors de la création du bon de commande.')->withInput();
        }
    }

    public function show(Commande $commande)
    {
        $commande->load('fournisseur', 'details.produit', 'user');
        $entreprise = \App\Models\Entreprise::first();
        return view('commandes.show', compact('commande', 'entreprise'));
    }

    public function update(Request $request, Commande $commande)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,envoyee,recue_partielle,recue,annulee',
        ]);

        $ancienStatut = $commande->statut;
        $commande->update(['statut' => $request->statut]);

        // Quand la commande est marquée "reçue", on met à jour le stock
        if ($request->statut === 'recue' && $ancienStatut !== 'recue') {
            DB::beginTransaction();
            try {
                foreach ($commande->details as $detail) {
                    $produit = $detail->produit;
                    $produit->increment('stock_actuel', $detail->quantite);

                    MouvementStock::create([
                        'produit_id'    => $produit->id,
                        'type'          => 'entree',
                        'quantite'      => $detail->quantite,
                        'prix_unitaire' => $detail->prix_unitaire,
                        'total'         => $detail->total_ligne,
                        'reference'     => MouvementStock::generateReference(),
                        'motif'         => 'Réception commande ' . $commande->reference,
                        'fournisseur'   => $commande->fournisseur->nom,
                        'date_mouvement'=> now(),
                        'notes'         => 'Commande reçue automatiquement',
                    ]);
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                report($e);
                return back()->with('error', 'Erreur lors de la mise à jour du stock.');
            }
        }

        return back()->with('success', 'Statut de la commande mis à jour.');
    }

    public function destroy(Commande $commande)
    {
        if (in_array($commande->statut, ['recue', 'recue_partielle'])) {
            return back()->with('error', 'Impossible de supprimer une commande déjà reçue.');
        }

        $commande->delete();
        return redirect()->route('commandes.index')
            ->with('success', 'Commande supprimée avec succès.');
    }

    public function pdf(Commande $commande)
    {
        $commande->load('fournisseur', 'details.produit', 'user');
        $entreprise = \App\Models\Entreprise::first();

        $pdf = \PDF::loadView('commandes.pdf', compact('commande', 'entreprise'));
        return $pdf->download('bon_commande_' . $commande->reference . '.pdf');
    }
}
