<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $vente->reference }}</title>
    @include('pdf.partials.styles')
</head>
<body>
@php
    $b = $branding ?? \App\Support\PdfBranding::resolve();
    $nomClient = $vente->client_nom ?? $vente->client?->nom_complet ?? 'Client comptant';
    $telephoneClient = $vente->client_telephone ?? $vente->client?->telephone;
    $emailClient = $vente->client_email ?? $vente->client?->email;
    $adresseClient = $vente->client?->adresse;
    $nbArticles = $vente->detailVentes->sum('quantite');
@endphp

<div class="page">
    @include('pdf.partials.header', [
        'docTitle' => 'FACTURE',
        'docSubtitle' => 'N° ' . $vente->reference,
    ])

    <table class="info-row" width="100%">
        <tr>
            <td width="48%" class="info-box">
                <h4>Facturé à</h4>
                <div class="name">{{ $nomClient }}</div>
                @if($telephoneClient)
                    <div class="info-line"><span class="label">Téléphone</span> {{ $telephoneClient }}</div>
                @endif
                @if($emailClient)
                    <div class="info-line"><span class="label">Email</span> {{ $emailClient }}</div>
                @endif
                <div class="address-line">
                    <strong>Adresse :</strong>
                    @if($adresseClient)
                        {{ $adresseClient }}
                    @else
                        <span style="color:#9ca3af;">—</span>
                    @endif
                </div>
            </td>
            <td width="4%"></td>
            <td width="48%" class="info-box">
                <h4>Détails de la facture</h4>
                <div class="info-line"><span class="label">Date</span> {{ $vente->created_at->format('d/m/Y') }}</div>
                <div class="info-line"><span class="label">Heure</span> {{ $vente->created_at->format('H:i') }}</div>
                <div class="info-line"><span class="label">Statut</span> {{ ucfirst($vente->statut ?? 'terminée') }}</div>
                @if($vente->mode_paiement)
                    <div class="info-line"><span class="label">Paiement</span> {{ $vente->modePaiementLabel() }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="4%" class="center">#</th>
                <th width="14%">Référence</th>
                <th width="38%">Désignation</th>
                <th width="10%" class="center">Qté</th>
                <th width="17%" class="right">Prix unitaire</th>
                <th width="17%" class="right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->detailVentes as $index => $detail)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $detail->produit->reference ?? '—' }}</td>
                    <td>{{ $detail->produit->nom }}</td>
                    <td class="center">{{ $detail->quantite }}</td>
                    <td class="right">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td class="right">{{ number_format($detail->total_ligne, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="bottom-row" width="100%">
        <tr>
            <td width="52%" class="payment-box">
                <h4>Informations de règlement</h4>
                @if($vente->mode_paiement)
                    <div class="info-line"><span class="label">Mode</span> {{ $vente->modePaiementLabel() }}</div>
                @endif
                @if($vente->montant_recu)
                    <div class="info-line"><span class="label">Reçu</span> {{ number_format($vente->montant_recu, 0, ',', ' ') }} FCFA</div>
                @endif
                @if($vente->monnaie && $vente->monnaie > 0)
                    <div class="info-line"><span class="label">Monnaie</span> {{ number_format($vente->monnaie, 0, ',', ' ') }} FCFA</div>
                @endif
                <div class="info-line" style="margin-top:8px; color:#6b7280;">
                    Arrêtée la présente facture à la somme de
                    <strong style="color:#111827;">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</strong>.
                </div>
            </td>
            <td width="4%"></td>
            <td width="44%" class="totals-box">
                <table>
                    <tr>
                        <td class="label">Nombre d'articles</td>
                        <td class="value">{{ $nbArticles }}</td>
                    </tr>
                    <tr>
                        <td class="label">Sous-total HT</td>
                        <td class="value">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td class="label">TVA</td>
                        <td class="value">0 FCFA</td>
                    </tr>
                    <tr class="grand">
                        <td class="label" style="color:#dbeafe;">NET À PAYER</td>
                        <td class="value">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($vente->notes)
        <div class="notes-box">
            <strong>Notes :</strong> {{ $vente->notes }}
        </div>
    @endif

    <table class="signature-row" width="100%">
        <tr>
            <td></td>
            <td width="220">
                <div class="signature-box">Signature &amp; cachet</div>
            </td>
        </tr>
    </table>

    @include('pdf.partials.footer')
</div>
</body>
</html>
