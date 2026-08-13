<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport des ventes</title>
    @include('pdf.partials.styles')
</head>
<body>
@php
    $panierMoyen = $totalVentes > 0 ? number_format($chiffreAffaires / $totalVentes, 0, ',', ' ') . ' FCFA' : '0 FCFA';
    $periode = \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') . ' — ' . \Carbon\Carbon::parse($dateFin)->format('d/m/Y');
@endphp
<div class="page">
    @include('pdf.partials.header', [
        'docTitle' => 'RAPPORT VENTES',
        'docSubtitle' => 'Période : ' . $periode,
    ])

    @include('pdf.partials.stats', ['stats' => [
        ['value' => $totalVentes, 'label' => 'Nombre de ventes'],
        ['value' => number_format($chiffreAffaires, 0, ',', ' ') . ' FCFA', 'label' => "Chiffre d'affaires"],
        ['value' => $panierMoyen, 'label' => 'Panier moyen'],
    ]])

    <table class="items-table">
        <thead>
            <tr>
                <th>Référence</th>
                <th>Date</th>
                <th class="center">Heure</th>
                <th>Client</th>
                <th class="center">Qté</th>
                <th class="right">Total</th>
                <th>Paiement</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventes as $vente)
                <tr>
                    <td><strong>{{ $vente->reference }}</strong></td>
                    <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                    <td class="center">{{ $vente->created_at->format('H:i') }}</td>
                    <td>{{ $vente->client_nom ?? 'Client comptant' }}</td>
                    <td class="center">{{ $vente->detailVentes->sum('quantite') }}</td>
                    <td class="right"><strong>{{ number_format($vente->total, 0, ',', ' ') }} FCFA</strong></td>
                    <td>{{ $vente->modePaiementLabel() }}</td>
                    <td>{{ ucfirst($vente->statut) }}</td>
                </tr>
            @empty
                <tr class="empty-row">
                    <td colspan="8">Aucune vente sur cette période.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @include('pdf.partials.footer')
</div>
</body>
</html>
