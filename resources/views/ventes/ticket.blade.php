<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ticket {{ $vente->reference }}</title>
    <style>
        @page {
            size: {{ $largeur }}mm auto;
            margin: 2mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Courier New', Consolas, monospace;
            font-size: 11px;
            width: {{ $largeur - 4 }}mm;
            margin: 0 auto;
            color: #000;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }
        .large { font-size: 14px; }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .ligne {
            display: flex;
            justify-content: space-between;
            gap: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }

        td {
            padding: 1px 0;
            vertical-align: top;
        }

        .qte { text-align: center; width: 10%; }
        .pu { text-align: right; width: 25%; }
        .lt { text-align: right; width: 25%; }

        .footer {
            margin-top: 8px;
            text-align: center;
        }

        .no-print { text-align: center; margin: 10px 0; }

        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="center bold large">{{ $entreprise->nom ?? 'GesBoutique' }}</div>
    @if($entreprise?->adresse)
        <div class="center">{{ $entreprise->adresse }}</div>
    @endif
    @if($entreprise?->telephone)
        <div class="center">Tél: {{ $entreprise->telephone }}</div>
    @endif

    <hr>

    <div class="ligne"><span>Reçu:</span><span class="bold">{{ $vente->reference }}</span></div>
    <div class="ligne"><span>Date:</span><span>{{ $vente->created_at->format('d/m/Y H:i') }}</span></div>
    <div class="ligne"><span>Client:</span><span>{{ $vente->client_nom ?? 'Client comptant' }}</span></div>

    <hr>

    <table>
        <tbody>
            @foreach($vente->detailVentes as $detail)
                <tr>
                    <td colspan="4">{{ $detail->produit->nom }}</td>
                </tr>
                <tr>
                    <td class="qte">{{ $detail->quantite }}</td>
                    <td class="pu">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }}</td>
                    <td class="lt">{{ number_format($detail->total_ligne, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <div class="ligne bold large"><span>TOTAL</span><span>{{ number_format($vente->total, 0, ',', ' ') }} F</span></div>
    <div class="ligne"><span>Mode de paiement</span><span>{{ $vente->modePaiementLabel() }}</span></div>
    @if($vente->montant_recu)
        <div class="ligne"><span>Reçu</span><span>{{ number_format($vente->montant_recu, 0, ',', ' ') }} F</span></div>
        <div class="ligne"><span>Monnaie</span><span>{{ number_format($vente->monnaie, 0, ',', ' ') }} F</span></div>
    @endif

    <hr>

    <div class="footer">
        Merci pour votre confiance !<br>
        {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="no-print">
        <button onclick="window.print()">Imprimer</button>
        <button onclick="window.close()">Fermer</button>
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>
