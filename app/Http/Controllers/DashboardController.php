<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Vente;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStock        = Produit::sum('stock_actuel');
        $ventesAujourdHui  = Vente::whereDate('created_at', today())->count();
        $stockFaible       = Produit::stockFaible()->count();
        $produitsEnRupture = Produit::enRupture()->count();

        $ventesDuJour = Vente::whereDate('created_at', today())
            ->with('detailVentes.produit')
            ->orderBy('created_at', 'desc')
            ->get();

        $entreesStockDuJour = MouvementStock::whereDate('created_at', today())
            ->where('type', 'entree')
            ->with('produit')
            ->orderBy('created_at', 'desc')
            ->get();

        // CA du jour
        $caJour = Vente::whereDate('created_at', today())->where('statut', 'terminee')->sum('total');

        // Graphique 1 : Ventes des 30 derniers jours
        $ventesParJour = Vente::where('statut', 'terminee')
            ->where('created_at', '>=', now()->subDays(29))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels30j  = [];
        $data30j    = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $labels30j[] = now()->subDays($i)->format('d/m');
            $data30j[]   = isset($ventesParJour[$d]) ? (float) $ventesParJour[$d]->total : 0;
        }

        // Graphique 2 : Répartition par mode de paiement (30 jours)
        $parModePaiement = Vente::where('statut', 'terminee')
            ->where('created_at', '>=', now()->subDays(29))
            ->select('mode_paiement', DB::raw('COUNT(*) as total'))
            ->groupBy('mode_paiement')
            ->get();

        $libellesModePaiement = Vente::libellesModePaiement();
        $labelsPaiement = $parModePaiement->pluck('mode_paiement')
            ->map(fn($m) => $libellesModePaiement[$m] ?? ucfirst($m))
            ->toArray();
        $dataPaiement = $parModePaiement->pluck('total')->toArray();

        // Graphique 3 : Top 10 produits vendus (30 jours)
        $topProduits = DB::table('detail_ventes')
            ->join('produits', 'detail_ventes.produit_id', '=', 'produits.id')
            ->join('ventes', 'detail_ventes.vente_id', '=', 'ventes.id')
            ->where('ventes.statut', 'terminee')
            ->where('ventes.created_at', '>=', now()->subDays(29))
            ->select('produits.nom', DB::raw('SUM(detail_ventes.quantite) as total_qty'))
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $labelsTop = $topProduits->pluck('nom')->toArray();
        $dataTop   = $topProduits->pluck('total_qty')->toArray();

        return view('dashboard', compact(
            'totalStock', 'ventesAujourdHui', 'stockFaible', 'produitsEnRupture',
            'ventesDuJour', 'entreesStockDuJour', 'caJour',
            'labels30j', 'data30j',
            'labelsPaiement', 'dataPaiement',
            'labelsTop', 'dataTop'
        ));
    }
}
