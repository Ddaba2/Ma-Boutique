@extends('layouts.premium')

@section('title', 'Performances')
@section('subtitle', 'Rentabilité et tendances des ventes')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <!-- Filtre de période -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('rapports.performances') }}" class="d-flex gap-2 flex-wrap">
                    @foreach(['jour' => 'Aujourd\'hui', 'semaine' => '7 derniers jours', 'mois' => '30 derniers jours', 'annee' => '12 derniers mois'] as $valeur => $libelle)
                        <button type="submit" name="periode" value="{{ $valeur }}"
                                class="btn btn-sm {{ $periode === $valeur ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ $libelle }}
                        </button>
                    @endforeach
                </form>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.1s">
                <div class="stat-card p-4 h-100">
                    <h6 class="text-muted mb-2 fw-semibold">Chiffre d'affaires</h6>
                    <h2 class="mb-0 fw-bold">{{ number_format($ventes->sum('total'), 0, ',', ' ') }}</h2>
                    <small class="text-muted">FCFA sur la période</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.2s">
                <div class="stat-card success p-4 h-100">
                    <h6 class="text-muted mb-2 fw-semibold">Nombre de ventes</h6>
                    <h2 class="mb-0 fw-bold">{{ $ventes->count() }}</h2>
                    <small class="text-muted">transaction(s)</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.3s">
                <div class="stat-card warning p-4 h-100">
                    <h6 class="text-muted mb-2 fw-semibold">Panier moyen</h6>
                    <h2 class="mb-0 fw-bold">{{ number_format($panierMoyen, 0, ',', ' ') }}</h2>
                    <small class="text-muted">FCFA / vente</small>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.4s">
                <div class="stat-card info p-4 h-100">
                    <h6 class="text-muted mb-2 fw-semibold">Produits / vente</h6>
                    <h2 class="mb-0 fw-bold">{{ number_format($produitsParVente, 1) }}</h2>
                    <small class="text-muted">unités en moyenne</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7 mb-4">
                <div class="card animate-fadeInUp h-100">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-primary"></i>Chiffre d'affaires par jour</h5>
                    </div>
                    <div class="card-body">
                        @if($caParJour->count() > 0)
                            <canvas id="chartCaParJour" height="110"></canvas>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>Aucune vente sur cette période
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-5 mb-4">
                <div class="card animate-fadeInUp h-100">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold"><i class="fas fa-trophy me-2 text-warning"></i>Produits les plus rentables</h5>
                    </div>
                    <div class="card-body p-0">
                        @if($produitsRentables->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern mb-0">
                                <thead>
                                    <tr><th>Produit</th><th>CA</th><th>Marge</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($produitsRentables as $item)
                                    <tr>
                                        <td class="small">{{ $item['produit']->nom ?? 'Produit supprimé' }}</td>
                                        <td class="fw-bold">{{ number_format($item['ca'], 0, ',', ' ') }} F</td>
                                        <td>
                                            <span class="badge bg-{{ $item['marge'] >= 20 ? 'success' : ($item['marge'] >= 0 ? 'warning' : 'danger') }}">
                                                {{ number_format($item['marge'], 0) }}%
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune donnée pour cette période
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            La marge est calculée à partir du prix d'achat <strong>actuel</strong> des produits : si un prix d'achat a changé depuis la vente, la marge affichée ici peut différer du bénéfice réellement réalisé à l'époque.
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/charts.js'])
<script>
@if($caParJour->count() > 0)
document.addEventListener('DOMContentLoaded', function () {
    new Chart(document.getElementById('chartCaParJour'), {
        type: 'line',
        data: {
            labels: {!! json_encode($caParJour->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
            datasets: [{
                label: 'CA (FCFA)',
                data: {!! json_encode($caParJour->pluck('ca')) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
@endif
</script>
@endsection
