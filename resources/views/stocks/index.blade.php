@extends('layouts.premium')

@section('title', 'Stock')
@section('subtitle', 'Gestion complète des stocks')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <!-- Statistiques principales - Identique à la page vente -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary animate-fadeInUp" style="animation-delay: 0.1s">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Total produits</h6>
                                <h3 class="mb-0 fw-bold">{{ $totalProduits }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-primary bg-gradient p-2 rounded-2">
                                    <i class="fas fa-boxes text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success animate-fadeInUp" style="animation-delay: 0.2s">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Stock normal</h6>
                                <h3 class="mb-0 fw-bold">{{ $produitsStockNormal }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-success bg-gradient p-2 rounded-2">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-warning animate-fadeInUp" style="animation-delay: 0.3s">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Stock faible</h6>
                                <h3 class="mb-0 fw-bold">{{ $produitsStockFaible }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-warning bg-gradient p-2 rounded-2">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info animate-fadeInUp" style="animation-delay: 0.4s">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">En rupture</h6>
                                <h3 class="mb-0 fw-bold">{{ $produitsEnRupture }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-info bg-gradient p-2 rounded-2">
                                    <i class="fas fa-times-circle text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des stocks - Structure identique à la page vente -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-warehouse me-2 text-success"></i>
                        Gestion des stocks
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('stocks.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Ajouter du stock
                        </a>
                        <a href="{{ route('rapports.export.pdf.stocks') }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Télécharger PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Référence</th>
                                <th>Stock actuel</th>
                                <th>Stock minimum</th>
                                <th>Prix de vente</th>
                                <th>Valeur totale</th>
                                <th>État</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($produits->count() > 0)
                                @foreach($produits as $produit)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-gradient rounded-circle p-2 me-3">
                                                    <i class="fas fa-box text-white fa-sm"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $produit->nom }}</h6>
                                                    <small class="text-muted">ID: {{ $produit->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $produit->reference ?? 'PROD-' . str_pad($produit->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="fw-bold">
                                            @if($produit->stock_actuel == 0)
                                                <span class="text-danger">{{ $produit->stock_actuel }}</span>
                                            @elseif($produit->stock_actuel <= $produit->stock_min)
                                                <span class="text-warning">{{ $produit->stock_actuel }}</span>
                                            @else
                                                <span class="text-success">{{ $produit->stock_actuel }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $produit->stock_min ?? 5 }}</td>
                                        <td class="fw-bold text-primary">{{ number_format($produit->prix_vente ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td class="fw-bold text-info">{{ number_format(($produit->stock_actuel * ($produit->prix_vente ?? 0)), 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            @if($produit->stock_actuel == 0)
                                                <span class="badge bg-danger">Rupture</span>
                                            @elseif($produit->stock_actuel <= $produit->stock_min)
                                                <span class="badge bg-warning">Faible</span>
                                            @else
                                                <span class="badge bg-success">Normal</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('stocks.create', ['produit_id' => $produit->id]) }}" class="btn btn-outline-success" title="Ajouter au stock">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <button onclick="showStockHistory({{ $produit->id }})" class="btn btn-outline-info" title="Historique">
                                                    <i class="fas fa-history"></i>
                                                </button>
                                                <button onclick="adjustStock({{ $produit->id }})" class="btn btn-outline-warning" title="Ajuster">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                            <i class="fas fa-warehouse fa-3x text-muted"></i>
                                        </div>
                                        <h5 class="text-muted">Aucun produit en stock</h5>
                                        <p class="text-muted">Commencez par ajouter des produits à votre stock</p>
                                        <a href="{{ route('stocks.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Ajouter du stock
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Informations sur les produits -->
                @if($produits->count() > 0)
                    <div class="text-center mt-4">
                        <div class="text-muted">
                            Affichage de {{ $produits->count() }} produits au total
                        </div>
                    </div>
                @endif
            </div>
        </div>

            </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonctions pour la gestion des stocks
    window.showStockHistory = function(produitId) {
        console.log('Afficher l\'historique du stock pour le produit:', produitId);
        // Implémenter la modal d'historique
    };
    
    window.adjustStock = function(produitId) {
        console.log('Ajuster le stock du produit:', produitId);
        // Implémenter la modal d'ajustement
    };
});
</script>

<style>
/* Styles identiques à la page vente */
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-success {
    border-left: 4px solid #1cc88a !important;
}

.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.table-modern {
    border-collapse: separate;
    border-spacing: 0;
}

.table-modern thead th {
    border-bottom: 2px solid #e3e6f0;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    color: #5a5c69;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fc;
}

.table-modern td {
    vertical-align: middle;
}

/* Animations */
.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endsection
