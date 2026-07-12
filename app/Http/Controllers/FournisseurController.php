<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    public function index(Request $request)
    {
        $query = Fournisseur::withCount('commandes');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                  ->orWhere('telephone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $fournisseurs = $query->orderBy('nom')->paginate(15);
        return view('fournisseurs.index', compact('fournisseurs'));
    }

    public function create()
    {
        return view('fournisseurs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'             => 'required|string|max:255',
            'contact'         => 'nullable|string|max:255',
            'telephone'       => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'adresse'         => 'nullable|string',
            'delai_livraison' => 'nullable|integer|min:0',
            'notes'           => 'nullable|string',
        ]);

        Fournisseur::create($request->all());

        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(Fournisseur $fournisseur)
    {
        $commandes    = $fournisseur->commandes()->with('details.produit')->orderBy('created_at', 'desc')->paginate(10);
        $totalAchats  = $fournisseur->commandes()->sum('total');
        $nbCommandes  = $fournisseur->commandes()->count();

        return view('fournisseurs.show', compact('fournisseur', 'commandes', 'totalAchats', 'nbCommandes'));
    }

    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'nom'             => 'required|string|max:255',
            'contact'         => 'nullable|string|max:255',
            'telephone'       => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'adresse'         => 'nullable|string',
            'delai_livraison' => 'nullable|integer|min:0',
            'notes'           => 'nullable|string',
        ]);

        $fournisseur->update($request->all());

        return redirect()->route('fournisseurs.show', $fournisseur)
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $fournisseur->update(['active' => false]);
        return redirect()->route('fournisseurs.index')
            ->with('success', 'Fournisseur désactivé.');
    }
}
