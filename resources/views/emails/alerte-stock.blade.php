<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Alerte stock</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1f2937; background: #f8fafc; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .header { background: #2563eb; color: white; padding: 20px 24px; }
        .header h1 { margin: 0; font-size: 18px; }
        .content { padding: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        th { background: #f8fafc; font-size: 12px; text-transform: uppercase; color: #6b7280; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: bold; }
        .badge-rupture { background: #fee2e2; color: #991b1b; }
        .badge-faible { background: #fef3c7; color: #92400e; }
        .footer { padding: 16px 24px; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Alerte stock — {{ $boutique->nom }}</h1>
        </div>
        <div class="content">
            <p>Voici l'état du stock nécessitant votre attention à la date du {{ now()->format('d/m/Y') }} :</p>

            @if($enRupture->isNotEmpty())
                <h3>Produits en rupture ({{ $enRupture->count() }})</h3>
                <table>
                    <thead><tr><th>Produit</th><th>Référence</th><th>Statut</th></tr></thead>
                    <tbody>
                        @foreach($enRupture as $stock)
                            <tr>
                                <td>{{ $stock->produit->nom }}</td>
                                <td>{{ $stock->produit->reference }}</td>
                                <td><span class="badge badge-rupture">Rupture</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if($stockFaible->isNotEmpty())
                <h3>Produits en stock faible ({{ $stockFaible->count() }})</h3>
                <table>
                    <thead><tr><th>Produit</th><th>Référence</th><th>Stock actuel</th><th>Seuil min.</th></tr></thead>
                    <tbody>
                        @foreach($stockFaible as $stock)
                            <tr>
                                <td>{{ $stock->produit->nom }}</td>
                                <td>{{ $stock->produit->reference }}</td>
                                <td>{{ $stock->stock_actuel }}</td>
                                <td>{{ $stock->stock_min }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div class="footer">
            Cet email est généré automatiquement par GesBoutique.
        </div>
    </div>
</body>
</html>
