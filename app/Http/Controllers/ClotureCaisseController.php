<?php

namespace App\Http\Controllers;

use App\Models\ClotureCaisse;
use App\Models\Vente;
use App\Support\BoutiqueContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClotureCaisseController extends Controller
{
    public function index()
    {
        $clotures = ClotureCaisse::with('user')->orderBy('date', 'desc')->paginate(20);

        return view('caisse.index', compact('clotures'));
    }

    public function create()
    {
        $today = now()->toDateString();

        // Vérifier si une clôture existe déjà pour aujourd'hui
        $existante = ClotureCaisse::where('date', $today)->first();
        if ($existante) {
            return redirect()->route('caisse.show', $existante)
                ->with('info', 'Une clôture existe déjà pour aujourd\'hui.');
        }

        // Calculer les totaux des ventes du jour
        $ventesJour = Vente::whereDate('created_at', $today)
            ->where('statut', 'terminee')
            ->get();

        $totalEspeces = $ventesJour->where('mode_paiement', 'espece')->sum('total');
        $totalCarte = $ventesJour->where('mode_paiement', 'carte')->sum('total');
        $totalMobile = $ventesJour->where('mode_paiement', 'mobile')->sum('total');
        $totalAutre = $ventesJour->where('mode_paiement', 'autre')->sum('total');
        $totalVentes = $ventesJour->sum('total');
        $nbVentes = $ventesJour->count();

        return view('caisse.create', compact(
            'totalEspeces', 'totalCarte', 'totalMobile', 'totalAutre',
            'totalVentes', 'nbVentes', 'today'
        ));
    }

    public function store(Request $request)
    {
        $boutiqueId = BoutiqueContext::id();

        $request->validate([
            'date' => [
                'required',
                'date',
                'before_or_equal:today',
                Rule::unique('clotures_caisse', 'date')->where('boutique_id', $boutiqueId),
            ],
            'fond_ouverture' => 'required|numeric|min:0',
            'montant_compte' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $today = $request->date;
        $ventesJour = Vente::whereDate('created_at', $today)->where('statut', 'terminee')->get();

        $totalEspeces = $ventesJour->where('mode_paiement', 'espece')->sum('total');
        $totalCarte = $ventesJour->where('mode_paiement', 'carte')->sum('total');
        $totalMobile = $ventesJour->where('mode_paiement', 'mobile')->sum('total');
        $totalAutre = $ventesJour->where('mode_paiement', 'autre')->sum('total');
        $totalVentes = $ventesJour->sum('total');
        $nbVentes = $ventesJour->count();

        $fondOuverture = $request->fond_ouverture;
        $montantCompte = $request->montant_compte;

        // L'écart de caisse ne concerne que les espèces réellement présentes
        // dans le tiroir : carte et mobile money ne transitent jamais par la
        // caisse physique, donc seul le comptage espèces face au théorique
        // (fond d'ouverture + ventes en espèces) a un sens ici.
        $especesTheoriques = $fondOuverture + $totalEspeces;
        $ecart = $montantCompte - $especesTheoriques;

        $cloture = ClotureCaisse::create([
            'date' => $today,
            'fond_ouverture' => $fondOuverture,
            'montant_compte' => $montantCompte,
            'total_especes' => $totalEspeces,
            'total_carte' => $totalCarte,
            'total_mobile' => $totalMobile,
            'total_autre' => $totalAutre,
            'total_ventes' => $totalVentes,
            'nombre_ventes' => $nbVentes,
            'ecart' => $ecart,
            'notes' => $request->notes,
            'statut' => 'clos',
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('caisse.show', $cloture)
            ->with('success', 'Clôture de caisse enregistrée avec succès.');
    }

    public function show(ClotureCaisse $caisse)
    {
        return view('caisse.show', compact('caisse'));
    }
}
