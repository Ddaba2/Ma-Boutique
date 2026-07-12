<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vente;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('ventes');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%$search%")
                  ->orWhere('prenom', 'like', "%$search%")
                  ->orWhere('telephone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        $clients = $query->orderBy('nom')->paginate(15);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'adresse'   => 'nullable|string',
            'notes'     => 'nullable|string',
        ]);

        Client::create($request->all());

        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    public function show(Client $client)
    {
        $ventes = $client->ventes()
            ->with('detailVentes.produit')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalAchats  = $client->ventes()->where('statut', 'terminee')->sum('total');
        $nbVentes     = $client->ventes()->where('statut', 'terminee')->count();
        $panierMoyen  = $nbVentes > 0 ? $totalAchats / $nbVentes : 0;

        return view('clients.show', compact('client', 'ventes', 'totalAchats', 'nbVentes', 'panierMoyen'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'adresse'   => 'nullable|string',
            'notes'     => 'nullable|string',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        $client->update(['active' => false]);
        return redirect()->route('clients.index')
            ->with('success', 'Client désactivé avec succès.');
    }
}
