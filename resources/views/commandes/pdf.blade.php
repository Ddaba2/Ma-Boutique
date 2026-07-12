<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de commande {{ $commande->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2937; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 3px solid #2563eb; padding-bottom: 20px; }
        .company h2 { color: #2563eb; margin: 0 0 5px 0; }
        .doc-info { text-align: right; }
        .doc-info h1 { color: #2563eb; font-size: 22px; margin: 0; }
        .doc-info .ref { font-size: 14px; font-weight: bold; color: #374151; }
        .fournisseur-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .fournisseur-box h4 { margin: 0 0 8px 0; color: #6b7280; font-size: 11px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        thead th { background: #2563eb; color: white; padding: 10px 12px; text-align: left; font-size: 11px; text-transform: uppercase; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tfoot td { background: #1f2937; color: white; padding: 12px; font-weight: bold; }
        .statut { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; background: #fef3c7; color: #92400e; }
        .footer { margin-top: 40px; padding-top: 15px; border-top: 1px solid #e2e8f0; text-align: center; color: #9ca3af; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">
            <h2>{{ $entreprise?->nom ?? 'GesBoutique' }}</h2>
            <div>{{ $entreprise?->adresse ?? '' }}</div>
            <div>{{ $entreprise?->telephone ?? '' }}</div>
        </div>
        <div class="doc-info">
            <h1>BON DE COMMANDE</h1>
            <div class="ref">{{ $commande->reference }}</div>
            <div>Date : {{ $commande->created_at->format('d/m/Y') }}</div>
            @if($commande->date_livraison_prevue)
            <div>Livraison prévue : {{ $commande->date_livraison_prevue->format('d/m/Y') }}</div>
            @endif
            <div style="margin-top:8px"><span class="statut">{{ $commande->statutLabel() }}</span></div>
        </div>
    </div>

    <div class="fournisseur-box">
        <h4>Fournisseur</h4>
        <strong>{{ $commande->fournisseur->nom }}</strong>
        @if($commande->fournisseur->contact)<div>Contact : {{ $commande->fournisseur->contact }}</div>@endif
        @if($commande->fournisseur->telephone)<div>Tél : {{ $commande->fournisseur->telephone }}</div>@endif
        @if($commande->fournisseur->email)<div>Email : {{ $commande->fournisseur->email }}</div>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Référence</th>
                <th>Désignation</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->details as $i => $detail)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $detail->produit->reference }}</td>
                <td>{{ $detail->produit->nom }}</td>
                <td>{{ $detail->quantite }}</td>
                <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($detail->total_ligne, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right">MONTANT TOTAL</td>
                <td>{{ number_format($commande->total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>

    @if($commande->notes)
    <div style="margin-top:20px; padding:12px; background:#fffbeb; border-left:4px solid #f59e0b; border-radius:4px">
        <strong>Notes :</strong> {{ $commande->notes }}
    </div>
    @endif

    <div class="footer">
        Bon de commande généré le {{ now()->format('d/m/Y à H:i') }} — {{ $entreprise?->nom ?? 'GesBoutique' }}
    </div>
</body>
</html>
