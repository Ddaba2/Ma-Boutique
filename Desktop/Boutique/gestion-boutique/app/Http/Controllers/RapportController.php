<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vente;
use App\Models\DetailVente;
use App\Models\Produit;
use App\Models\Categorie;
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
        $produits = Produit::query()
            ->when($request->statut_stock, function($query, $statut) {
                if ($statut == 'rupture') {
                    return $query->where('stock_actuel', 0);
                } elseif ($statut == 'faible') {
                    return $query->whereColumn('stock_actuel', '<=', 'stock_min')
                              ->where('stock_actuel', '>', 0);
                } elseif ($statut == 'normal') {
                    return $query->whereColumn('stock_actuel', '>', 'stock_min');
                }
            })
            ->get();

        $totalProduits = $produits->count();
        $produitsEnRupture = $produits->where('stock_actuel', 0)->count();
        $produitsStockFaible = $produits->where('stock_actuel', '<=', 'stock_min')
                                     ->where('stock_actuel', '>', 0)->count();
        $produitsStockNormal = $produits->where('stock_actuel', '>', 'stock_min')->count();

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

        return view('rapports.stocks', compact(
            'produits', 'categories', 'totalProduits', 
            'produitsEnRupture', 'produitsStockFaible', 'produitsStockNormal',
            'valeurStock', 'stockParCategorie'
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

        $pdf = \PDF::loadView('rapports.pdf.ventes', compact(
            'ventes', 'dateDebut', 'dateFin', 'totalVentes', 'chiffreAffaires'
        ));

        return $pdf->download("rapport_ventes_{$dateDebut}_au_{$dateFin}.pdf");
    }

    
    public function exportPdfStocks(Request $request)
    {
        $produits = Produit::query()
            ->when($request->search, function($query, $search) {
                return $query->where('nom', 'like', '%' . $search . '%');
            })
            ->get();

        $totalProduits = $produits->count();
        $produitsEnRupture = $produits->where('stock_actuel', 0)->count();
        $valeurStock = $produits->sum(function($produit) {
            return $produit->stock_actuel * $produit->prix_achat;
        });

        $pdf = \PDF::loadView('rapports.pdf.stocks', compact(
            'produits', 'totalProduits', 
            'produitsEnRupture', 'valeurStock'
        ));

        return $pdf->download("rapport_stocks_" . Carbon::now()->format('Y-m-d') . ".pdf");
    }

    public function exportExcelStocks(Request $request)
    {
        $categories = Categorie::where('active', true)->get();
        
        $produits = Produit::with('categorie')
            ->when($request->categorie_id, function($query, $categorieId) {
                return $query->where('categorie_id', $categorieId);
            })
            ->get();

        $data = [];
        foreach ($produits as $produit) {
            $data[] = [
                'Référence' => $produit->reference,
                'Nom' => $produit->nom,
                'Catégorie' => $produit->categorie->nom ?? 'Non catégorisé',
                'Stock actuel' => $produit->stock_actuel,
                'Stock minimum' => $produit->stock_min,
                'Stock maximum' => $produit->stock_max,
                'Prix d\'achat' => $produit->prix_achat,
                'Prix de vente' => $produit->prix_vente,
                'Valeur stock' => $produit->stock_actuel * $produit->prix_achat,
                'Statut' => $produit->stock_actuel == 0 ? 'Rupture' : 
                          ($produit->stock_actuel <= $produit->stock_min ? 'Faible' : 'Normal'),
                'Actif' => $produit->active ? 'Oui' : 'Non'
            ];
        }

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

        // Récupérer les données des stocks
        $produits = Produit::query()->get();

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

        $pdf = PDF::loadView('rapports.pdf_complete', $data);
        return $pdf->download("rapport_complet_{$dateDebut}_au_{$dateFin}.pdf");
    }

    public function exportExcelComplete(Request $request)
    {
        $dateDebut = $request->date_debut ?? now()->subDays(30)->format('Y-m-d');
        $dateFin = $request->date_fin ?? now()->format('Y-m-d');

        // Récupérer les données des ventes
        $ventes = Vente::with('detailVentes.produit')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Récupérer les données des stocks
        $produits = Produit::query()->get();

        // Préparer les données pour Excel
        $data = [];
        
        // En-tête
        $data[] = ['RAPPORT COMPLET'];
        $data[] = ["Du {$dateDebut} au {$dateFin}"];
        $data[] = ['GesBoutique - Système de Gestion'];
        $data[] = []; // Ligne vide
        
        // Section Ventes
        $data[] = ['VENTES'];
        $data[] = ['Référence', 'Date', 'Heure', 'Client', 'Total', 'Mode paiement', 'Statut', 'Produits', 'Notes'];
        
        foreach ($ventes as $vente) {
            $data[] = [
                $vente->reference,
                $vente->created_at->format('d/m/Y'),
                $vente->created_at->format('H:i'),
                $vente->client_nom ?? 'Client anonyme',
                $vente->total,
                ucfirst($vente->mode_paiement),
                ucfirst($vente->statut),
                $vente->detailVentes->sum('quantite'),
                $vente->notes ?? ''
            ];
        }
        
        // Ligne vide
        $data[] = [];
        
        // Section Stocks
        $data[] = ['STOCKS'];
        $data[] = ['Produit', 'Référence', 'Catégorie', 'Stock actuel', 'Stock minimum', 'Prix vente', 'Valeur stock', 'État'];
        
        foreach ($produits as $produit) {
            $etat = $produit->stock_actuel == 0 ? 'Rupture' : 
                   ($produit->stock_actuel <= ($produit->stock_min ?? 5) ? 'Faible' : 'Normal');
            
            $data[] = [
                $produit->nom,
                $produit->reference ?? 'PROD-' . str_pad($produit->id, 4, '0', STR_PAD_LEFT),
                $produit->categorie->nom ?? 'Non classé',
                $produit->stock_actuel,
                $produit->stock_min ?? 5,
                $produit->prix_vente ?? 0,
                $produit->stock_actuel * ($produit->prix_vente ?? 0),
                $etat
            ];
        }
        
        // Ligne vide
        $data[] = [];
        
        // Résumé
        $data[] = ['RÉSUMÉ'];
        $data[] = ['Total des ventes', $ventes->sum('total') . ' FCFA'];
        $data[] = ['Total produits vendus', $ventes->sum(function($v) { return $v->detailVentes->sum('quantite'); })];
        $data[] = ['Valeur totale du stock', $produits->sum(function($p) { return $p->stock_actuel * ($p->prix_vente ?? 0); }) . ' FCFA'];
        $data[] = ['Total produits', $produits->count()];
        $data[] = ['Produits en rupture', $produits->where('stock_actuel', 0)->count()];
        $data[] = ['Produits stock faible', $produits->filter(function($p) {
            return $p->stock_actuel > 0 && $p->stock_actuel <= ($p->stock_min ?? 5);
        })->count()];

            }
}
