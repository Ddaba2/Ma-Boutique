<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\BoutiqueProduit;
use App\Models\Categorie;
use App\Models\MouvementStock;
use App\Support\BoutiqueContext;
use App\Support\PdfBranding;
use Carbon\Carbon;
use PDF;

class RapportController extends Controller
{
    public function index()
    {
        return view('rapports.index');
    }

    public function ventes(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::today()->subDays(30)->format('Y-m-d'));
        $dateFin = $request->input('date_fin', Carbon::today()->format('Y-m-d'));
        
        $ventes = Vente::with('detailVentes.produit')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalVentes = $ventes->count();
        $chiffreAffaires = $ventes->sum('total');
        $produitsVendus = $ventes->sum(function($vente) {
            return $vente->detailVentes->sum('quantite');
        });

        // Ventes par jour
        $ventesParJour = $ventes->groupBy(function($vente) {
            return $vente->created_at->format('Y-m-d');
        })->map(function($dayVentes) {
            return [
                'nombre' => $dayVentes->count(),
                'ca' => $dayVentes->sum('total'),
                'produits' => $dayVentes->sum(function($v) {
                    return $v->detailVentes->sum('quantite');
                })
            ];
        });

        // Top produits
        $topProduits = DetailVente::with('produit')
            ->whereHas('vente', function($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59']);
            })
            ->selectRaw('produit_id, SUM(quantite) as total_vendu, SUM(total_ligne) as total_ca')
            ->groupBy('produit_id')
            ->orderBy('total_vendu', 'desc')
            ->limit(10)
            ->get();

        // Ventes par mode de paiement
        $ventesParMode = $ventes->groupBy('mode_paiement')->map(function($modeVentes) {
            return [
                'nombre' => $modeVentes->count(),
                'ca' => $modeVentes->sum('total')
            ];
        });

        return view('rapports.ventes', compact(
            'ventes', 'dateDebut', 'dateFin', 'totalVentes', 
            'chiffreAffaires', 'produitsVendus', 'ventesParJour',
            'topProduits', 'ventesParMode'
        ));
    }

    public function stocks(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::today()->subDays(30)->format('Y-m-d'));
        $dateFin = $request->input('date_fin', Carbon::today()->format('Y-m-d'));

        $stocks = BoutiqueProduit::dansBoutique(BoutiqueContext::id())
            ->with('produit.categorie')
            ->parStatutStock($request->statut_stock)
            ->get();

        $produits = BoutiqueProduit::fusionnerAvecCatalogue($stocks);

        $totalProduits = $produits->count();
        $produitsEnRupture = $stocks->filter(fn($s) => $s->statutStock() === 'rupture')->count();
        $produitsStockFaible = $stocks->filter(fn($s) => $s->statutStock() === 'faible')->count();
        $produitsStockNormal = $stocks->filter(fn($s) => $s->statutStock() === 'normal')->count();

        // Valeur du stock
        $valeurStock = $produits->sum(function($produit) {
            return $produit->stock_actuel * $produit->prix_achat;
        });

        // Stock par catégorie
        $stockParCategorie = $produits->groupBy('categorie_id')->map(function($catProduits) {
            return [
                'nombre' => $catProduits->count(),
                'quantite' => $catProduits->sum('stock_actuel'),
                'valeur' => $catProduits->sum(function($p) {
                    return $p->stock_actuel * $p->prix_achat;
                })
            ];
        });

        // Entrées de stock réelles sur la période (mouvements tracés, pas une
        // approximation basée sur la date de création des produits).
        $entreesStock = MouvementStock::with('produit')
            ->where('type', 'entree')
            ->whereBetween('date_mouvement', [$dateDebut, $dateFin])
            ->orderBy('date_mouvement', 'desc')
            ->get();

        $totalEntrees = $entreesStock->count();
        $quantiteTotale = $entreesStock->sum('quantite');

        $categories = Categorie::where('active', true)->get();

        return view('rapports.stocks', compact(
            'produits', 'categories', 'totalProduits',
            'produitsEnRupture', 'produitsStockFaible', 'produitsStockNormal',
            'valeurStock', 'stockParCategorie',
            'entreesStock', 'totalEntrees', 'quantiteTotale', 'dateDebut', 'dateFin'
        ));
    }

    public function performances(Request $request)
    {
        $periode = $request->input('periode', 'mois'); // jour, semaine, mois, annee
        
        $dateDebut = match($periode) {
            'jour' => Carbon::today(),
            'semaine' => Carbon::today()->subDays(7),
            'mois' => Carbon::today()->subMonth(),
            'annee' => Carbon::today()->subYear(),
            default => Carbon::today()->subMonth()
        };

        $ventes = Vente::where('created_at', '>=', $dateDebut)->get();
        
        // CA par jour
        $caParJour = Vente::where('created_at', '>=', $dateDebut)
            ->selectRaw('DATE(created_at) as date, SUM(total) as ca, COUNT(*) as nombre')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Produits les plus rentables
        $produitsRentables = DetailVente::with('produit')
            ->whereHas('vente', function($query) use ($dateDebut) {
                $query->where('created_at', '>=', $dateDebut);
            })
            ->selectRaw('produit_id, SUM(total_ligne) as ca_total, SUM(quantite * (SELECT prix_achat FROM produits WHERE id = produit_id)) as cout_total')
            ->groupBy('produit_id')
            ->get()
            ->map(function($item) {
                $benefice = $item->ca_total - $item->cout_total;
                $marge = $item->ca_total > 0 ? ($benefice / $item->ca_total) * 100 : 0;
                return [
                    'produit' => $item->produit,
                    'ca' => $item->ca_total,
                    'benefice' => $benefice,
                    'marge' => $marge
                ];
            })
            ->sortByDesc('benefice')
            ->take(10);

        // Moyennes
        $panierMoyen = $ventes->count() > 0 ? $ventes->sum('total') / $ventes->count() : 0;
        $produitsParVente = $ventes->count() > 0 ? 
            $ventes->sum(function($v) { return $v->detailVentes->sum('quantite'); }) / $ventes->count() : 0;

        return view('rapports.performances', compact(
            'periode', 'dateDebut', 'caParJour', 'produitsRentables',
            'panierMoyen', 'produitsParVente', 'ventes'
        ));
    }

    public function exportVentes(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::today()->subDays(30)->format('Y-m-d'));
        $dateFin = $request->input('date_fin', Carbon::today()->format('Y-m-d'));
        
        $ventes = Vente::with('detailVentes.produit')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "rapport_ventes_{$dateDebut}_au_{$dateFin}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($ventes) {
            $file = fopen('php://output', 'w');
            
            // En-tête CSV
            fputcsv($file, [
                'Référence', 'Date', 'Heure', 'Client', 'Total', 'Mode paiement', 'Statut'
            ]);
            
            foreach ($ventes as $vente) {
                fputcsv($file, [
                    $vente->reference,
                    $vente->created_at->format('d/m/Y'),
                    $vente->created_at->format('H:i'),
                    $vente->client_nom ?? 'Client anonyme',
                    $vente->total,
                    $vente->mode_paiement,
                    $vente->statut
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdfVentes(Request $request)
    {
        $dateDebut = $request->input('date_debut', Carbon::today()->subDays(30)->format('Y-m-d'));
        $dateFin = $request->input('date_fin', Carbon::today()->format('Y-m-d'));
        
        $ventes = Vente::with('detailVentes.produit')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalVentes = $ventes->count();
        $chiffreAffaires = $ventes->sum('total');

        $pdf = \PDF::loadView('rapports.pdf.ventes', array_merge(
            compact('ventes', 'dateDebut', 'dateFin', 'totalVentes', 'chiffreAffaires'),
            PdfBranding::forView()
        ));

        return $pdf->download("rapport_ventes_{$dateDebut}_au_{$dateFin}.pdf");
    }

    
    public function exportPdfStocks(Request $request)
    {
        $stocks = BoutiqueProduit::dansBoutique(BoutiqueContext::id())
            ->with('produit')
            ->when($request->search, function($query, $search) {
                return $query->whereHas('produit', fn($q) => $q->where('nom', 'like', '%' . $search . '%'));
            })
            ->get();

        $produits = BoutiqueProduit::fusionnerAvecCatalogue($stocks);

        $totalProduits = $produits->count();
        $produitsEnRupture = $produits->where('stock_actuel', 0)->count();
        $valeurStock = $produits->sum(function($produit) {
            return $produit->stock_actuel * $produit->prix_achat;
        });

        $pdf = \PDF::loadView('rapports.pdf.stocks', array_merge(
            compact('produits', 'totalProduits', 'produitsEnRupture', 'valeurStock'),
            PdfBranding::forView()
        ));

        return $pdf->download("rapport_stocks_" . Carbon::now()->format('Y-m-d') . ".pdf");
    }

    public function exportPdfComplete(Request $request)
    {
        $dateDebut = $request->date_debut ?? now()->subDays(30)->format('Y-m-d');
        $dateFin = $request->date_fin ?? now()->format('Y-m-d');

        // Récupérer les données des ventes
        $ventes = Vente::with('detailVentes.produit')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer les données des stocks (de la boutique courante)
        $stocks = BoutiqueProduit::dansBoutique(BoutiqueContext::id())->with('produit')->get();
        $produits = BoutiqueProduit::fusionnerAvecCatalogue($stocks);

        $data = [
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'ventes' => $ventes,
            'produits' => $produits,
            'total_ventes' => $ventes->sum('total'),
            'total_produits_vendus' => $ventes->sum(function($v) { return $v->detailVentes->sum('quantite'); }),
            'valeur_stock' => $produits->sum(function($p) { return $p->stock_actuel * ($p->prix_vente ?? 0); }),
            'total_produits' => $produits->count(),
            'produits_en_rupture' => $produits->where('stock_actuel', 0)->count(),
            'produits_stock_faible' => $produits->filter(function($p) {
                return $p->stock_actuel > 0 && $p->stock_actuel <= ($p->stock_min ?? 5);
            })->count()
        ];

        $pdf = PDF::loadView('rapports.pdf_complete', array_merge($data, PdfBranding::forView()));
        return $pdf->download("rapport_complet_{$dateDebut}_au_{$dateFin}.pdf");
    }
}
