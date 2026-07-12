<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture - GesBoutique</title>
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
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-info h2 {
            color: #2563eb;
            margin: 0;
            font-size: 20px;
        }
        .company-info p {
            margin: 3px 0;
            color: #333;
            font-size: 11px;
        }
        .logo {
            max-width: 120px;
            max-height: 60px;
            margin-bottom: 10px;
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
    <div class="company-info">
        @php
            $entreprise = \App\Models\Entreprise::first();
        @endphp
        @if($entreprise && $entreprise->logo)
            <img src="{{ asset($entreprise->logo) }}" alt="Logo" class="logo">
        @endif
        <h2>{{ $entreprise->nom ?? 'GesBoutique SARL' }}</h2>
        <p><strong>NIF:</strong> {{ $entreprise->nif ?? 'NIF123456789' }}</p>
        <p><strong>Adresse:</strong> {{ $entreprise->adresse ?? '123 Rue du Commerce, Quartier Affaires, 75001 Paris, France' }}</p>
        <p><strong>Téléphone:</strong> {{ $entreprise->telephone ?? '+221 77 123 45 67' }}</p>
        <p><strong>Email:</strong> {{ $entreprise->email ?? 'contact@gesboutique.com' }}</p>
        @if($entreprise && $entreprise->site_web)
            <p><strong>Site web:</strong> {{ $entreprise->site_web }}</p>
        @endif
    </div>

    <div class="header">
        <h1>FACTURE - RAPPORT DES VENTES</h1>
        <p>Période: du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</p>
        <p>Généré le: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats">
        <div class="stat-item">
            <h3>{{ $totalVentes }}</h3>
            <p>Total des ventes</p>
        </div>
        <div class="stat-item">
            <h3>{{ number_format($chiffreAffaires, 0, ',', ' ') }} FCFA</h3>
            <p>Chiffre d'affaires</p>
        </div>
        <div class="stat-item">
            <h3>{{ $totalVentes > 0 ? number_format($chiffreAffaires / $totalVentes, 0, ',', ' ') : 0 }} FCFA</h3>
            <p>Panier moyen</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Client</th>
                <th>Produits</th>
                <th>Total</th>
                <th>Paiement</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventes as $vente)
                <tr>
                    <td><strong>{{ $vente->reference }}</strong></td>
                    <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                    <td>{{ $vente->created_at->format('H:i') }}</td>
                    <td>{{ $vente->client_nom ?? 'Client anonyme' }}</td>
                    <td>{{ $vente->detailVentes->sum('quantite') }}</td>
                    <td><strong>{{ number_format($vente->total, 0, ',', ' ') }} FCFA</strong></td>
                    <td>{{ ucfirst($vente->mode_paiement) }}</td>
                    <td>{{ ucfirst($vente->statut) }}</td>
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
