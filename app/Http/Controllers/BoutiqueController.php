<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\BoutiqueProduit;
use App\Models\Produit;
use App\Support\BoutiqueContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoutiqueController extends Controller
{
    public function index()
    {
        $boutiques = Boutique::withCount('utilisateurs')->orderBy('nom')->get();
        return view('boutiques.index', compact('boutiques'));
    }

    public function create()
    {
        return view('boutiques.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:50',
        ]);
        $data['active'] = true;

        DB::beginTransaction();
        try {
            $boutique = Boutique::create($data);

            // Catalogue partagé : chaque boutique démarre avec une ligne de
            // stock (à 0) pour tous les produits déjà existants.
            $produitIds = Produit::pluck('id');
            $maintenant = now();
            $lignes = $produitIds->map(fn ($produitId) => [
                'boutique_id' => $boutique->id,
                'produit_id' => $produitId,
                'stock_actuel' => 0,
                'stock_min' => 5,
                'stock_max' => 100,
                'created_at' => $maintenant,
                'updated_at' => $maintenant,
            ])->all();

            if (!empty($lignes)) {
                BoutiqueProduit::insert($lignes);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return back()->with('error', 'Une erreur est survenue lors de la création de la boutique.')->withInput();
        }

        if (!BoutiqueContext::estDefinie()) {
            BoutiqueContext::definir($boutique->id);
        }

        return redirect()->route('boutiques.index')->with('success', 'Boutique créée avec succès.');
    }

    public function edit(Boutique $boutique)
    {
        return view('boutiques.edit', compact('boutique'));
    }

    public function update(Request $request, Boutique $boutique)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:50',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active');

        $boutique->update($data);

        return redirect()->route('boutiques.index')->with('success', 'Boutique mise à jour avec succès.');
    }

    /**
     * Écran de sélection présenté à un gérant qui n'a pas encore de boutique
     * courante en session (EnsureBoutiqueSelected).
     */
    public function selectionner()
    {
        $boutiques = Boutique::where('active', true)->orderBy('nom')->get();
        return view('boutiques.selectionner', compact('boutiques'));
    }

    public function switch(Request $request)
    {
        $data = $request->validate([
            'boutique_id' => 'required|exists:boutiques,id',
        ]);

        BoutiqueContext::definir((int) $data['boutique_id']);

        return redirect()->route('dashboard')->with('success', 'Boutique active changée.');
    }
}
