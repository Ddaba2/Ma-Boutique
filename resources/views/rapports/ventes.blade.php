@extends('layouts.premium')

@section('title', 'Rapports de Ventes')
@section('subtitle', 'Analyse détaillée de vos ventes avec dates et heures')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <!-- Filtres -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('rapports.ventes') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="date_debut" class="form-label fw-semibold">Date de début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ $dateDebut ?? now()->subDays(30)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label for="date_fin" class="form-label fw-semibold">Date de fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ $dateFin ?? now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Filtrer
                                </button>
                                <a href="{{ route('rapports.export.ventes') }}?date_debut={{ $dateDebut ?? now()->subDays(30)->format('Y-m-d') }}&date_fin={{ $dateFin ?? now()->format('Y-m-d') }}" class="btn btn-success">
                                    <i class="fas fa-file-csv me-2"></i>CSV
                                </a>
                                <a href="{{ route('rapports.export.pdf.ventes') }}?date_debut={{ $dateDebut ?? now()->subDays(30)->format('Y-m-d') }}&date_fin={{ $dateFin ?? now()->format('Y-m-d') }}" class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
                                </a>
                                <a href="{{ route('rapports.export.excel.ventes') }}?date_debut={{ $dateDebut ?? now()->subDays(30)->format('Y-m-d') }}&date_fin={{ $dateFin ?? now()->format('Y-m-d') }}" class="btn btn-success">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistiques principales -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.1s">
                <div class="stat-card p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2 fw-semibold">Total ventes</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalVentes ?? 0 }}</h2>
                            <small class="text-success">
                                <i class="fas fa-shopping-cart me-1"></i>
                                Transactions
                            </small>
                        </div>
                        <div class="ms-3">
                            <div class="bg-primary bg-gradient p-3 rounded-3">
                                <i class="fas fa-shopping-cart fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.2s">
                <div class="stat-card success p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2 fw-semibold">Chiffre d'affaires</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($chiffreAffaires ?? 0, 0, ',', ' ') }}</h2>
                            <small class="text-success">
                                <i class="fas fa-chart-line me-1"></i>
                                FCFA
                            </small>
                        </div>
                        <div class="ms-3">
                            <div class="bg-success bg-gradient p-3 rounded-3">
                                <i class="fas fa-chart-line fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.3s">
                <div class="stat-card warning p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2 fw-semibold">Panier moyen</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($panierMoyen ?? 0, 0, ',', ' ') }}</h2>
                            <small class="text-warning">
                                <i class="fas fa-calculator me-1"></i>
                                FCFA
                            </small>
                        </div>
                        <div class="ms-3">
                            <div class="bg-warning bg-gradient p-3 rounded-3">
                                <i class="fas fa-calculator fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.4s">
                <div class="stat-card info p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2 fw-semibold">Produits vendus</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalProduitsVendus ?? 0 }}</h2>
                            <small class="text-info">
                                <i class="fas fa-box me-1"></i>
                                Unités
                            </small>
                        </div>
                        <div class="ms-3">
                            <div class="bg-info bg-gradient p-3 rounded-3">
                                <i class="fas fa-box fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des ventes -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-list me-2 text-info"></i>
                    Détail des ventes
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Produits</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($ventes) && $ventes->count() > 0)
                                @foreach($ventes as $vente)
                                    <tr>
                                        <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $vente->created_at->format('H:i') }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $vente->reference }}</span></td>
                                        <td>{{ $vente->client_nom ?? 'Non spécifié' }}</td>
                                        <td>
                                            @if($vente->detailVentes->count() > 0)
                                                <div class="small">
                                                    @foreach($vente->detailVentes->take(2) as $detail)
                                                        <div>{{ $detail->produit->nom ?? 'Produit supprimé' }}</div>
                                                    @endforeach
                                                    @if($vente->detailVentes->count() > 2)
                                                        <div class="text-muted">+{{ $vente->detailVentes->count() - 2 }} autre(s)</div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Aucun produit</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ $vente->detailVentes->sum('quantite') }}</td>
                                        <td class="fw-bold text-primary">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                                        <td><span class="badge bg-success">Payée</span></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>Aucune vente trouvée pour cette période</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
