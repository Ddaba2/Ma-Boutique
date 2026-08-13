<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport des stocks</title>
    @include('pdf.partials.styles')
</head>
<body>
<div class="page">
    @include('pdf.partials.header', [
        'docTitle' => 'RAPPORT STOCKS',
        'docSubtitle' => 'État au ' . now()->format('d/m/Y à H:i'),
    ])

    @include('pdf.partials.stats', ['stats' => [
        ['value' => $totalProduits, 'label' => 'Total produits'],
        ['value' => $produitsEnRupture, 'label' => 'En rupture'],
        ['value' => number_format($valeurStock, 0, ',', ' ') . ' FCFA', 'label' => 'Valeur du stock'],
    ]])

    <table class="items-table">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Désignation</th>
                <th>Catégorie</th>
                <th class="center">Stock</th>
                <th class="center">Min</th>
                <th class="center">Max</th>
                <th class="right">Prix achat</th>
                <th class="right">Prix vente</th>
                <th class="right">Valeur</th>
                <th class="center">État</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produits as $produit)
                <tr>
                    <td><strong>{{ $produit->reference }}</strong></td>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ $produit->categorie->nom ?? '—' }}</td>
                    <td class="center"><strong>{{ $produit->stock_actuel }}</strong></td>
                    <td class="center">{{ $produit->stock_min }}</td>
                    <td class="center">{{ $produit->stock_max }}</td>
                    <td class="right">{{ number_format($produit->prix_achat, 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</td>
                    <td class="right"><strong>{{ number_format($produit->stock_actuel * $produit->prix_achat, 0, ',', ' ') }} FCFA</strong></td>
                    <td class="center">
                        @if($produit->stock_actuel == 0)
                            <span class="stock-rupture">Rupture</span>
                        @elseif($produit->stock_actuel <= $produit->stock_min)
                            <span class="stock-faible">Faible</span>
                        @else
                            <span class="stock-normal">Normal</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="10">Aucun produit en stock.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials.footer')
</div>
</body>
</html>
