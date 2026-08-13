@extends('layouts.premium')

@section('title', 'Rapports de Stocks')
@section('subtitle', 'Analyse détaillée des mouvements de stock avec dates et fournisseurs')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <!-- Filtres -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('rapports.stocks') }}">
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
                                <a href="{{ route('rapports.export.pdf.stocks') }}?date_debut={{ $dateDebut ?? now()->subDays(30)->format('Y-m-d') }}&date_fin={{ $dateFin ?? now()->format('Y-m-d') }}" class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
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
                            <h6 class="text-muted mb-2 fw-semibold">Total entrées</h6>
                            <h2 class="mb-0 fw-bold">{{ $totalEntrees ?? 0 }}</h2>
                            <small class="text-success">
                                <i class="fas fa-plus-circle me-1"></i>
                                Mouvements
                            </small>
                        </div>
                        <div class="ms-3">
                            <div class="bg-success bg-gradient p-3 rounded-3">
                                <i class="fas fa-plus-circle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.2s">
                <div class="stat-card warning p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2 fw-semibold">Quantité totale</h6>
                            <h2 class="mb-0 fw-bold">{{ $quantiteTotale ?? 0 }}</h2>
                            <small class="text-warning">
                                <i class="fas fa-boxes me-1"></i>
                                Unités
                            </small>
                        </div>
                        <div class="ms-3">
                            <div class="bg-warning bg-gradient p-3 rounded-3">
                                <i class="fas fa-boxes fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.3s">
                <div class="stat-card info p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2 fw-semibold">Valeur stock</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($valeurStock ?? 0, 0, ',', ' ') }}</h2>
                            <small class="text-info">
                                <i class="fas fa-chart-line me-1"></i>
                                FCFA
                            </small>
                        </div>
                        <div class="ms-3">
                            <div class="bg-info bg-gradient p-3 rounded-3">
                                <i class="fas fa-chart-line fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4 animate-slideInLeft" style="animation-delay: 0.4s">
                <div class="stat-card danger p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-2 fw-semibold">Produits en rupture</h6>
                            <h2 class="mb-0 fw-bold">{{ $produitsEnRupture ?? 0 }}</h2>
                            <small class="text-danger">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Alert
                            </small>
                        </div>
                        <div class="ms-3">
                            <div class="bg-danger bg-gradient p-3 rounded-3">
                                <i class="fas fa-exclamation-triangle fa-2x text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des entrées de stock -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-plus-circle me-2 text-success"></i>
                    Entrées de stock
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Fournisseur</th>
                                <th>Motif</th>
                                <th>Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($entreesStock) && $entreesStock->count() > 0)
                                @foreach($entreesStock as $entree)
                                    <tr>
                                        <td>{{ $entree->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $entree->created_at->format('H:i') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                                                    <i class="fas fa-box text-white fa-sm"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $entree->produit->nom ?? 'Produit supprimé' }}</h6>
                                                    <small class="text-muted">Réf: {{ $entree->produit->reference ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-bold text-success">+{{ $entree->quantite }}</td>
                                        <td>{{ $entree->fournisseur ?? 'Non spécifié' }}</td>
                                        <td>{{ $entree->motif ?? 'Entrée de stock' }}</td>
                                        <td>{{ $entree->user->name ?? 'Système' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-boxes fa-2x mb-2"></i>
                                        <p>Aucune entrée de stock trouvée pour cette période</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tableau des produits avec état actuel -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-list me-2 text-info"></i>
                    État actuel des stocks
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Catégorie</th>
                                <th>Stock actuel</th>
                                <th>Stock minimum</th>
                                <th>Valeur totale</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($produits) && $produits->count() > 0)
                                @foreach($produits as $produit)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                                                    <i class="fas fa-box text-white fa-sm"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-semibold">{{ $produit->nom }}</h6>
                                                    <small class="text-muted">Réf: {{ $produit->reference ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $produit->categorie->nom ?? 'Non catégorisé' }}
                                            </span>
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
                                        <td>{{ $produit->stock_min }}</td>
                                        <td class="fw-bold text-primary">{{ number_format($produit->stock_actuel * $produit->prix_achat, 0, ',', ' ') }} FCFA</td>
                                        <td>
                                            @if($produit->stock_actuel == 0)
                                                <span class="badge bg-danger">Rupture</span>
                                            @elseif($produit->stock_actuel <= $produit->stock_min)
                                                <span class="badge bg-warning">Faible</span>
                                            @else
                                                <span class="badge bg-success">Normal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>Aucun produit trouvé</p>
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
