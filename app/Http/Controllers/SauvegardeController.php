<?php

namespace App\Http\Controllers;

use App\Services\SauvegardeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SauvegardeController extends Controller
{
    public function store(SauvegardeService $sauvegardes)
    {
        try {
            $fichier = $sauvegardes->creer();

            return $this->versOngletSauvegardes()->with('success', "Sauvegarde créée avec succès : {$fichier}");
        } catch (\Throwable $e) {
            report($e);

            return $this->versOngletSauvegardes()->with('error', $e->getMessage());
        }
    }

    public function restaurer(Request $request, SauvegardeService $sauvegardes)
    {
        $request->validate([
            'fichier' => 'nullable|string',
            'password' => 'required|string',
        ]);

        // Action destructrice (remplace toutes les données actuelles) : on
        // exige une reconfirmation par mot de passe, une simple confirmation
        // JS n'étant qu'un garde-fou côté UX, pas une vérification réelle.
        if (! Hash::check($request->string('password')->toString(), Auth::user()->password)) {
            return $this->versOngletSauvegardes()->with('error', 'Mot de passe incorrect. Restauration annulée.');
        }

        try {
            if ($request->filled('fichier')) {
                $fichier = $request->string('fichier')->toString();
                $sauvegardes->restaurer($fichier);
            } else {
                $fichier = $sauvegardes->restaurerDerniere();
            }

            return $this->versOngletSauvegardes()->with('success', "Base de données restaurée depuis : {$fichier}. Une sauvegarde de sécurité de l'état précédent a été créée automatiquement.");
        } catch (\Throwable $e) {
            report($e);

            return $this->versOngletSauvegardes()->with('error', $e->getMessage());
        }
    }

    public function telecharger(string $fichier, SauvegardeService $sauvegardes)
    {
        $chemin = $sauvegardes->cheminSecurise($fichier);
        abort_unless($chemin, 404);

        return response()->download($chemin);
    }

    // Les actions de sauvegarde vivent désormais dans un onglet de la page
    // Paramètres plutôt que sur leur propre page : on y revient explicitement
    // (plutôt que via back()) pour que l'onglet actif reste correct après
    // l'action, y compris si le formulaire a été soumis autrement.
    private function versOngletSauvegardes()
    {
        return redirect()->route('parametres.index', ['onglet' => 'sauvegardes']);
    }
}
