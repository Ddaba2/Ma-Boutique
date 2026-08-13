<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport complet</title>
    @include('pdf.partials.styles')
</head>
<body>
@php
    $periode = \Carbon\Carbon::parse($date_debut)->format('d/m/Y') . ' — ' . \Carbon\Carbon::parse($date_fin)->format('d/m/Y');
@endphp
<div class="page">
    @include('pdf.partials.header', [
        'docTitle' => 'RAPPORT COMPLET',
        'docSubtitle' => 'Période : ' . $periode,
    ])

    @include('pdf.partials.stats', ['stats' => [
        ['value' => number_format($total_ventes, 0, ',', ' ') . ' FCFA', 'label' => "Chiffre d'affaires"],
        ['value' => $total_produits_vendus, 'label' => 'Unités vendues'],
        ['value' => number_format($valeur_stock, 0, ',', ' ') . ' FCFA', 'label' => 'Valeur stock'],
    ]])

    <div class="section-title">Ventes</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Date</th>
                <th class="center">Heure</th>
                <th>Client</th>
                <th class="right">Total</th>
                <th>Paiement</th>
                <th>Statut</th>
                <th class="center">Qté</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventes as $vente)
                <tr>
                    <td><strong>{{ $vente->reference }}</strong></td>
                    <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                    <td class="center">{{ $vente->created_at->format('H:i') }}</td>
                    <td>{{ $vente->client_nom ?? 'Client comptant' }}</td>
                    <td class="right">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $vente->modePaiementLabel() }}</td>
                    <td>{{ ucfirst($vente->statut) }}</td>
                    <td class="center">{{ $vente->detailVentes->sum('quantite') }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="8">Aucune vente sur cette période.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Stocks</div>
    <table class="items-table">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Désignation</th>
                <th>Catégorie</th>
                <th class="center">Stock</th>
                <th class="center">Min</th>
                <th class="right">Prix vente</th>
                <th class="right">Valeur</th>
                <th class="center">État</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produits as $produit)
                <tr>
                    <td><strong>{{ $produit->reference ?? 'PROD-' . str_pad($produit->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $produit->nom }}</td>
                    <td>{{ $produit->categorie->nom ?? '—' }}</td>
                    <td class="center">{{ $produit->stock_actuel }}</td>
                    <td class="center">{{ $produit->stock_min ?? 5 }}</td>
                    <td class="right">{{ number_format($produit->prix_vente ?? 0, 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($produit->stock_actuel * ($produit->prix_vente ?? 0), 0, ',', ' ') }} FCFA</td>
                    <td class="center">
                        @if($produit->stock_actuel == 0)
                            <span class="stock-rupture">Rupture</span>
                        @elseif($produit->stock_actuel <= ($produit->stock_min ?? 5))
                            <span class="stock-faible">Faible</span>
                        @else
                            <span class="stock-normal">Normal</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="8">Aucun produit en stock.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Synthèse</div>
    <div class="summary-box">
        <table>
            <tr>
                <td class="label">Total des ventes (période)</td>
                <td class="value">{{ number_format($total_ventes, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td class="label">Unités vendues</td>
                <td class="value">{{ $total_produits_vendus }}</td>
            </tr>
            <tr>
                <td class="label">Valeur totale du stock</td>
                <td class="value">{{ number_format($valeur_stock, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td class="label">Produits référencés</td>
                <td class="value">{{ $total_produits }}</td>
            </tr>
            <tr>
                <td class="label">Produits en rupture</td>
                <td class="value">{{ $produits_en_rupture }}</td>
            </tr>
            <tr>
                <td class="label">Produits stock faible</td>
                <td class="value">{{ $produits_stock_faible }}</td>
            </tr>
            <tr class="grand">
                <td class="label" style="color:#dbeafe;">Période analysée</td>
                <td class="value">{{ $periode }}</td>
            </tr>
        </table>
    </div>

    @include('pdf.partials.footer')
</div>
</body>
</html>
