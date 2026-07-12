<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture - {{ $vente->reference }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .company-info {
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
        }
        
        .company-details h2 {
            margin: 0 0 5px 0;
            color: #007bff;
            font-size: 20px;
        }
        
        .company-details p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
            background: #f8f9fa;
        }
        
        .invoice-info {
            background: #f8f9fa;
            padding: 15px;
            position: right;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .invoice-info h3 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 16px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-item strong {
            color: #495057;
            display: inline-block;
            width: 80px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .products-table th {
            background: #007bff;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: 600;
        }
        
        .products-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .products-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 5px;
        }
        
        .total-label {
            width: 150px;
            text-align: right;
            padding-right: 15px;
            font-weight: 600;
        }
        
        .total-value {
            width: 120px;
            text-align: right;
            font-weight: bold;
        }
        
        .grand-total {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        
        .payment-info {
            background: #e7f3ff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-details">
                @if($entreprise)
                    <h2>{{ $entreprise->nom }}</h2>
                    <p><strong>NIF:</strong> {{ $entreprise->nif }}</p>
                    <p><strong>Adresse:</strong> {{ $entreprise->adresse }}</p>
                    <p><strong>Téléphone:</strong> {{ $entreprise->telephone }}</p>
                    <p><strong>Email:</strong> {{ $entreprise->email }}</p>
                    @if($entreprise->site_web)
                        <p><strong>Site web:</strong> {{ $entreprise->site_web }}</p>
                    @endif
                @else
                    <h2>GesBoutique</h2>
                    <p>NIF: NIF123456789</p>
                    <p>Adresse: Bamako, Mali</p>
                    <p>Téléphone: +223 XX XX XX XX</p>
                    <p>Email: contact@gesboutique.com</p>
                @endif
            </div>
            @if($entreprise && $entreprise->logo)
                <img src="{{ asset('storage/' . $entreprise->logo) }}" alt="Logo" class="logo">
            @else
                <div class="logo" style="display: flex; align-items: center; justify-content: center; font-size: 10px; text-align: center;">
                    LOGO
                </div>
            @endif
        </div>
    </div>

    <div class="invoice-info">
        <h3>FACTURE</h3>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <strong>Numéro:</strong> {{ $vente->reference }}
                </div>
                <div class="info-item">
                    <strong>Date:</strong> {{ $vente->created_at->format('d/m/Y') }}
                </div>
                <div class="info-item">
                    <strong>Heure:</strong> {{ $vente->created_at->format('H:i') }}
                </div>
            </div>
            <div>
                <div class="info-item">
                    <strong>Client:</strong> {{ $vente->client_nom ?? 'Client anonyme' }} - Téléphone: {{ $vente->client_telephone ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    @if($vente->mode_paiement)
        <div class="payment-info">
            <strong>Mode de paiement:</strong> {{ ucfirst($vente->mode_paiement) }}
            @if($vente->montant_recu)
                <br><strong>Montant reçu:</strong> {{ number_format($vente->montant_recu, 0, ',', ' ') }} FCFA
            @endif
            @if($vente->monnaie)
                <br><strong>Monnaie:</strong> {{ number_format($vente->monnaie, 0, ',', ' ') }} FCFA
            @endif
        </div>
    @endif

    <table class="products-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="40%">Produit</th>
                <th width="15%">Quantité</th>
                <th width="20%">Prix unitaire</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->detailVentes as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->produit->nom }}</td>
                    <td>{{ $detail->quantite }}</td>
                    <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($detail->total, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <div class="total-label">Total produits:</div>
            <div class="total-value">{{ $vente->detailVentes->sum('quantite') }}</div>
        </div>
        <div class="total-row">
            <div class="total-label">Montant total:</div>
            <div class="total-value">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</div>
        </div>
        
        <div class="total-row grand-total">
            <div class="total-label">NET À PAYER:</div>
            <div class="total-value">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    @if($vente->notes)
        <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
            <strong>Notes:</strong> {{ $vente->notes }}
        </div>
    @endif

    <div style="margin-top: 50px; text-align: right;">
        <p style="font-size: 11px;">Signature et cachet</p>
    </div>

    <div class="footer">
        <p>Merci pour votre confiance !</p>
        <p>Cette facture est générée automatiquement par GesBoutique</p>
        <p>Générée le: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
