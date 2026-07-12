<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Complet - GesBoutique</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .summary {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        .summary-item {
            margin-bottom: 10px;
        }
        .summary-label {
            font-weight: bold;
            color: #333;
        }
        .summary-value {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT COMPLET</h1>
        <p>Période du {{ $date_debut }} au {{ $date_fin }}</p>
        <p>GesBoutique - Système de Gestion</p>
        <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Section Ventes -->
    <div class="section">
        <h2>VENTES</h2>
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Mode paiement</th>
                    <th>Statut</th>
                    <th>Produits</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @if($ventes->count() > 0)
                    @foreach($ventes as $vente)
                        <tr>
                            <td>{{ $vente->reference }}</td>
                            <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                            <td>{{ $vente->created_at->format('H:i') }}</td>
                            <td>{{ $vente->client_nom ?? 'Client anonyme' }}</td>
                            <td>{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $vente->modePaiementLabel() }}</td>
                            <td>{{ ucfirst($vente->statut) }}</td>
                            <td>{{ $vente->detailVentes->sum('quantite') }}</td>
                            <td>{{ $vente->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="text-align: center;">Aucune vente trouvée pour cette période</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Section Stocks -->
    <div class="section">
        <h2>STOCKS</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Référence</th>
                    <th>Catégorie</th>
                    <th>Stock actuel</th>
                    <th>Stock minimum</th>
                    <th>Prix vente</th>
                    <th>Valeur stock</th>
                    <th>État</th>
                </tr>
            </thead>
            <tbody>
                @if($produits->count() > 0)
                    @foreach($produits as $produit)
                        <tr>
                            <td>{{ $produit->nom }}</td>
                            <td>{{ $produit->reference ?? 'PROD-' . str_pad($produit->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $produit->categorie->nom ?? 'Non classé' }}</td>
                            <td>{{ $produit->stock_actuel }}</td>
                            <td>{{ $produit->stock_min ?? 5 }}</td>
                            <td>{{ number_format($produit->prix_vente ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($produit->stock_actuel * ($produit->prix_vente ?? 0), 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if($produit->stock_actuel == 0)
                                    <span style="color: red;">Rupture</span>
                                @elseif($produit->stock_actuel <= ($produit->stock_min ?? 5))
                                    <span style="color: orange;">Faible</span>
                                @else
                                    <span style="color: green;">Normal</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" style="text-align: center;">Aucun produit trouvé</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Résumé -->
    <div class="section">
        <h2>RÉSUMÉ</h2>
        <div class="summary">
            <div class="summary-item">
                <span class="summary-label">Total des ventes :</span>
                <span class="summary-value">{{ number_format($total_ventes, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total produits vendus :</span>
                <span class="summary-value">{{ $total_produits_vendus }} unités</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Valeur totale du stock :</span>
                <span class="summary-value">{{ number_format($valeur_stock, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total produits :</span>
                <span class="summary-value">{{ $total_produits }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Produits en rupture :</span>
                <span class="summary-value">{{ $produits_en_rupture }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Produits stock faible :</span>
                <span class="summary-value">{{ $produits_stock_faible }}</span>
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 40px; color: #666; font-size: 10px;">
        <p>Rapport généré par GesBoutique - {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
