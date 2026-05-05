<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Categorie;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::with('categorie')->paginate(10);
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
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock_actuel' => 'required|integer|min:0',
            'stock_min' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'image' => 'nullable|string'
        ]);

        $reference = 'PROD' . str_pad(Produit::count() + 1, 6, '0', STR_PAD_LEFT);
        
        Produit::create([
            'reference' => $reference,
            'nom' => $request->nom,
            'description' => $request->description,
            'prix_achat' => $request->prix_achat,
            'prix_vente' => $request->prix_vente,
            'stock_actuel' => $request->stock_actuel,
            'stock_min' => $request->stock_min,
            'stock_max' => $request->stock_max,
            'categorie_id' => $request->categorie_id,
            'image' => $request->image,
            'active' => true
        ]);

        return redirect()->route('produits.index')
            ->with('success', 'Produit créé avec succès.');
    }

    public function show(Produit $produit)
    {
        return view('produits.show', compact('produit'));
    }

    public function edit(Produit $produit)
    {
        $categories = Categorie::where('active', true)->get();
        return view('produits.edit', compact('produit', 'categories'));
    }

    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'stock_actuel' => 'required|integer|min:0',
            'stock_min' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
            'categorie_id' => 'required|exists:categories,id',
            'image' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $produit->update($request->all());

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();
        return redirect()->route('produits.index')
            ->with('success', 'Produit supprimé avec succès.');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $produits = Produit::where('nom', 'LIKE', "%{$query}%")
            ->orWhere('reference', 'LIKE', "%{$query}%")
            ->where('active', true)
            ->where('stock_actuel', '>', 0)
            ->with('categorie')
            ->get();

        return response()->json($produits);
    }
}
