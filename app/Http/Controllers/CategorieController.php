<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Support\ReglesChamps;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::withCount('produits')->orderBy('nom')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:categories,nom', ReglesChamps::NOM_SANS_HTML],
            'description' => 'nullable|string',
            'couleur' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'active' => 'boolean',
        ]);

        $data['active'] = $request->boolean('active');
        $data['couleur'] = $data['couleur'] ?? '#3B82F6';

        Categorie::create($data);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function show(Categorie $categorie)
    {
        $categorie->load('produits');
        return view('categories.show', compact('categorie'));
    }

    public function edit(Categorie $categorie)
    {
        return view('categories.edit', compact('categorie'));
    }

    public function update(Request $request, Categorie $categorie)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255', 'unique:categories,nom,' . $categorie->id, ReglesChamps::NOM_SANS_HTML],
            'description' => 'nullable|string',
            'couleur' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'active' => 'boolean',
        ]);

        $data['active'] = $request->boolean('active');
        $data['couleur'] = $data['couleur'] ?? $categorie->couleur;

        $categorie->update($data);

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Categorie $categorie)
    {
        // La contrainte est en "set null" en base, mais on prévient l'utilisateur
        // pour éviter de dé-catégoriser des produits par erreur.
        if ($categorie->produits()->exists()) {
            return back()->with('error', 'Impossible de supprimer une catégorie encore associée à des produits.');
        }

        $categorie->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
