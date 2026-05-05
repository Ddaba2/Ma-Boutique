@extends('layouts.premium')

@section('title', 'Tableau de bord')
@section('subtitle', 'Vue d\'ensemble de votre boutique')

@section('content')
<div class="row mt-4">
    <!-- Statistiques principales -->
    <div class="col-xl-4 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.1s">
        <div class="stat-card p-4 h-100">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-2 fw-semibold">Total Marchandise</h6>
                    <h2 class="mb-0 fw-bold">{{ $totalStock }}</h2>
                    <small class="text-success">
                        <i class="fas fa-boxes me-1"></i>
                        En stock
                    </small>
                </div>
                <div class="ms-3">
                    <div class="bg-primary bg-gradient p-3 rounded-3">
                        <i class="fas fa-boxes fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.2s">
        <div class="stat-card success p-4 h-100">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-2 fw-semibold">Ventes du jour</h6>
                    <h2 class="mb-0 fw-bold">{{ $ventesAujourdHui }}</h2>
                    <small class="text-success">
                        <i class="fas fa-shopping-cart me-1"></i>
                        Aujourd'hui
                    </small>
                </div>
                <div class="ms-3">
                    <div class="bg-success bg-gradient p-3 rounded-3">
                        <i class="fas fa-shopping-cart fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.3s">
        <div class="stat-card warning p-4 h-100">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="text-muted mb-2 fw-semibold">Stock faible</h6>
                    <h2 class="mb-0 fw-bold">{{ $stockFaible }}</h2>
                    <small class="text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Alert
                    </small>
                </div>
                <div class="ms-3">
                    <div class="bg-warning bg-gradient p-3 rounded-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-3">
                    <a href="{{ route('ventes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouvelle vente
                    </a>
                    <a href="{{ route('stocks.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Ajouter marchandise en stock
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historique des opérations du jour -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-info"></i>
                    Opérations du jour
                </h5>
            </div>
            <div class="card-body">
                <!-- Ventes du jour -->
                <div class="mb-4">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Ventes du jour ({{ $ventesDuJour->count() }})
                    </h6>
                    @if($ventesDuJour->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Heure</th>
                                        <th>Référence</th>
                                        <th>Client</th>
                                        <th>Total</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventesDuJour as $vente)
                                        <tr>
                                            <td>{{ $vente->created_at->format('H:i') }}</td>
                                            <td><span class="badge bg-light text-dark">{{ $vente->reference }}</span></td>
                                            <td>{{ $vente->client_nom ?? 'Non spécifié' }}</td>
                                            <td class="fw-bold">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                                            <td><span class="badge bg-success">Payée</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Aucune vente aujourd'hui</p>
                        </div>
                    @endif
                </div>

                <!-- Entrées de stock du jour -->
                <div>
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-plus-circle me-2"></i>
                        Entrées de stock du jour ({{ $entreesStockDuJour->count() }})
                    </h6>
                    @if($entreesStockDuJour->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Heure</th>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th>Fournisseur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entreesStockDuJour as $entree)
                                        <tr>
                                            <td>{{ $entree->created_at->format('H:i') }}</td>
                                            <td>{{ $entree->produit->nom }}</td>
                                            <td class="fw-bold text-success">+{{ $entree->quantite }}</td>
                                            <td>{{ $entree->fournisseur ?? 'Non spécifié' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-boxes fa-2x mb-2"></i>
                            <p>Aucune entrée de stock aujourd'hui</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
