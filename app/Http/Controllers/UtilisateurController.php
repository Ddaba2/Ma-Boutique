<?php

namespace App\Http\Controllers;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UtilisateurController extends Controller
{
    public function index()
    {
        $utilisateurs = User::with('boutique')->orderBy('name')->get();
        return view('utilisateurs.index', compact('utilisateurs'));
    }

    public function create()
    {
        $boutiques = Boutique::where('active', true)->orderBy('nom')->get();
        return view('utilisateurs.create', compact('boutiques'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6|confirmed',
            'role'        => 'required|in:gerant,gestionnaire,caissier',
            'boutique_id' => 'nullable|exists:boutiques,id|required_unless:role,gerant',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'boutique_id' => $request->role === 'gerant' ? null : $request->boutique_id,
            'active'      => true,
        ]);

        return redirect()->route('utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $utilisateur)
    {
        $boutiques = Boutique::where('active', true)->orderBy('nom')->get();
        return view('utilisateurs.edit', compact('utilisateur', 'boutiques'));
    }

    public function update(Request $request, User $utilisateur)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $utilisateur->id,
            'role'        => 'required|in:gerant,gestionnaire,caissier',
            'boutique_id' => 'nullable|exists:boutiques,id|required_unless:role,gerant',
            'active'      => 'boolean',
            'password'    => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'role'        => $request->role,
            'boutique_id' => $request->role === 'gerant' ? null : $request->boutique_id,
            'active'      => $request->boolean('active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $utilisateur->update($data);

        return redirect()->route('utilisateurs.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $utilisateur)
    {
        if ($utilisateur->id === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $utilisateur->update(['active' => false]);
        return redirect()->route('utilisateurs.index')
            ->with('success', 'Utilisateur désactivé.');
    }
}
