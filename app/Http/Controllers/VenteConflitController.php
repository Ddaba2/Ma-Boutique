<?php

namespace App\Http\Controllers;

use App\Models\Vente;

class VenteConflitController extends Controller
{
    public function index()
    {
        $ventes = Vente::where('conflit_stock', true)
            ->with('detailVentes.produit')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ventes.conflits', compact('ventes'));
    }

    /**
     * Le gérant/gestionnaire a vérifié physiquement le stock et confirme que
     * la vente peut rester telle quelle (ex. réapprovisionnement entre-temps).
     * Aucune correction automatique du stock n'est tentée ici.
     */
    public function resoudre(Vente $vente)
    {
        $vente->update(['conflit_stock' => false]);

        return redirect()->route('ventes.conflits')
            ->with('success', 'Conflit marqué comme résolu pour la vente ' . $vente->reference . '.');
    }
}
