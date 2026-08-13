@extends('layouts.premium')

@section('title', 'Tableau de bord')
@section('subtitle', 'Vue d\'ensemble de votre boutique')

@section('content')
<div class="row mt-4">
    <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay:0.1s">
        <div class="stat-card p-4 h-100">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-1 fw-semibold small text-uppercase">Total en stock</h6>
                    <h2 class="mb-0 fw-bold">{{ number_format($totalStock) }}</h2>
                    <small class="text-primary">articles</small>
                </div>
                <div class="bg-primary bg-gradient p-3 rounded-3 ms-3">
                    <i class="fas fa-boxes fa-2x text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay:0.2s">
        <div class="stat-card success p-4 h-100">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-1 fw-semibold small text-uppercase">Ventes du jour</h6>
                    <h2 class="mb-0 fw-bold">{{ $ventesAujourdHui }}</h2>
                    <small class="text-success">{{ number_format($caJour, 0, ',', ' ') }} FCFA</small>
                </div>
                <div class="bg-success bg-gradient p-3 rounded-3 ms-3">
                    <i class="fas fa-shopping-cart fa-2x text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay:0.3s">
        <div class="stat-card warning p-4 h-100">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-1 fw-semibold small text-uppercase">Stock faible</h6>
                    <h2 class="mb-0 fw-bold">{{ $stockFaible }}</h2>
                    <small class="text-warning">produits à réapprovisionner</small>
                </div>
                <div class="bg-warning bg-gradient p-3 rounded-3 ms-3">
                    <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay:0.4s">
        <div class="stat-card danger p-4 h-100">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-1 fw-semibold small text-uppercase">Rupture de stock</h6>
                    <h2 class="mb-0 fw-bold">{{ $produitsEnRupture }}</h2>
                    <small class="text-danger">produits épuisés</small>
                </div>
                <div class="bg-danger bg-gradient p-3 rounded-3 ms-3">
                    <i class="fas fa-times-circle fa-2x text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('ventes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Nouvelle vente
                    </a>
                    <a href="{{ route('stocks.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Entrée stock
                    </a>
                    <a href="{{ route('caisse.create') }}" class="btn btn-warning btn-sm text-dark">
                        <i class="fas fa-cash-register me-1"></i>Clôturer caisse
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row mb-4">
    <!-- Courbe ventes 30 jours -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card h-100 animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-chart-line me-2 text-primary"></i>Chiffre d'affaires — 30 derniers jours
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartVentes" height="100"></canvas>
            </div>
        </div>
    </div>
    <!-- Camembert paiement -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card h-100 animate-fadeInUp">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-chart-pie me-2 text-success"></i>Modes de paiement
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="chartPaiement" style="max-height:220px"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Top produits -->
    <div class="col-xl-7 mb-4">
        <div class="card h-100 animate-fadeInUp">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-trophy me-2 text-warning"></i>Top 10 produits vendus (30 jours)
                </h6>
            </div>
            <div class="card-body">
                <canvas id="chartTop" height="180"></canvas>
            </div>
        </div>
    </div>

    <!-- Opérations du jour -->
    <div class="col-xl-5 mb-4">
        <div class="card h-100 animate-fadeInUp">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-info"></i>Ventes du jour ({{ $ventesDuJour->count() }})
                </h6>
            </div>
            <div class="card-body p-0" style="max-height:320px; overflow-y:auto;">
                @if($ventesDuJour->count() > 0)
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Heure</th>
                                <th>Client</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventesDuJour as $vente)
                            <tr>
                                <td class="small text-muted">{{ $vente->created_at->format('H:i') }}</td>
                                <td class="small">{{ $vente->client_nom ?? '—' }}</td>
                                <td class="small fw-bold">{{ number_format($vente->total, 0, ',', ' ') }} F</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                        Aucune vente aujourd'hui
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/charts.js'])
<script>
// resources/js/charts.js est chargé en tant que module (déféré par nature) :
// on attend DOMContentLoaded pour être sûr que window.Chart est bien défini
// avant de l'utiliser ici, l'ordre d'exécution entre un <script type=module>
// et un <script> classique n'étant pas celui qu'on aurait avec un simple
// <script src="..."> synchrone comme auparavant.
document.addEventListener('DOMContentLoaded', function () {
    const palette = {
        blue:   'rgba(37,99,235,',
        green:  'rgba(34,197,94,',
        amber:  'rgba(245,158,11,',
        red:    'rgba(239,68,68,',
        purple: 'rgba(139,92,246,',
    };

    // Graphique 1 — Ventes 30 jours
    new Chart(document.getElementById('chartVentes'), {
        type: 'line',
        data: {
            labels: @json($labels30j),
            datasets: [{
                label: 'CA (FCFA)',
                data: @json($data30j),
                borderColor: 'rgba(37,99,235,1)',
                backgroundColor: 'rgba(37,99,235,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: 'rgba(37,99,235,1)',
                pointRadius: 3,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.parsed.y.toLocaleString('fr-FR') + ' FCFA'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: v => v.toLocaleString('fr-FR') + ' F' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Graphique 2 — Mode de paiement
    new Chart(document.getElementById('chartPaiement'), {
        type: 'doughnut',
        data: {
            labels: @json($labelsPaiement),
            datasets: [{
                data: @json($dataPaiement),
                backgroundColor: [
                    'rgba(37,99,235,0.85)',
                    'rgba(34,197,94,0.85)',
                    'rgba(245,158,11,0.85)',
                    'rgba(139,92,246,0.85)',
                ],
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 12 } } }
            }
        }
    });

    // Graphique 3 — Top produits
    new Chart(document.getElementById('chartTop'), {
        type: 'bar',
        data: {
            labels: @json($labelsTop),
            datasets: [{
                label: 'Quantité vendue',
                data: @json($dataTop),
                backgroundColor: 'rgba(245,158,11,0.8)',
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } },
                y: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
});
</script>
@endsection
