<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Stocks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-item h3 {
            margin: 0;
            color: #2563eb;
            font-size: 20px;
        }
        .stat-item p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) {
            background: #f8fafc;
        }
        .stock-normal { color: #22c55e; font-weight: bold; }
        .stock-faible { color: #f59e0b; font-weight: bold; }
        .stock-rupture { color: #ef4444; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT DES STOCKS</h1>
        <p>GesBoutique - Système de Gestion de Boutique</p>
        <p>État des stocks au {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
        <p>Généré le: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-item">
            <h3>{{ $totalProduits }}</h3>
            <p>Total produits</p>
        </div>
        <div class="stat-item">
            <h3>{{ $produitsEnRupture }}</h3>
            <p>Produits en rupture</p>
        </div>
        <div class="stat-item">
            <h3>{{ number_format($valeurStock, 0, ',', ' ') }} FCFA</h3>
            <p>Valeur totale du stock</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Stock actuel</th>
                <th>Stock min</th>
                <th>Stock max</th>
                <th>Prix achat</th>
                <th>Prix vente</th>
                <th>Valeur stock</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $produit)
                <tr>
                    <td><strong>{{ $produit->reference }}</strong></td>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ $produit->categorie->nom ?? 'Non catégorisé' }}</td>
                    <td><strong>{{ $produit->stock_actuel }}</strong></td>
                    <td>{{ $produit->stock_min }}</td>
                    <td>{{ $produit->stock_max }}</td>
                    <td>{{ number_format($produit->prix_achat, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</td>
                    <td><strong>{{ number_format($produit->stock_actuel * $produit->prix_achat, 0, ',', ' ') }} FCFA</strong></td>
                    <td>
                        @if($produit->stock_actuel == 0)
                            <span class="stock-rupture">Rupture</span>
                        @elseif($produit->stock_actuel <= $produit->stock_min)
                            <span class="stock-faible">Faible</span>
                        @else
                            <span class="stock-normal">Normal</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Rapport généré par GesBoutique - © {{ date('Y') }} Tous droits réservés</p>
        <p>Ce document est confidentiel et destiné uniquement à un usage interne</p>
    </div>
</body>
</html>
