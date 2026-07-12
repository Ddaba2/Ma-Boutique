@extends('layouts.premium')

@section('title', 'Détail de la Catégorie')
@section('subtitle', 'Consultez les informations de cette catégorie')

@section('content')
<div class="row mt-4">
    <div class="col-md-8">
        <!-- Informations de la catégorie -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-layer-group me-2 text-primary"></i>
                    {{ $categorie->nom }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold">Nom</td>
                                <td><strong>{{ $categorie->nom }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Couleur</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle me-2" style="width: 20px; height: 20px; background-color: {{ $categorie->couleur }};"></div>
                                        <span>{{ $categorie->couleur }}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Statut</td>
                                <td>
                                    @if($categorie->active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-pause me-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold">Nombre de produits</td>
                                <td><strong>{{ $categorie->produits->count() }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Produits actifs</td>
                                <td><strong>{{ $categorie->produits->where('active', true)->count() }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Créée le</td>
                                <td>{{ $categorie->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($categorie->description)
                    <div class="mt-4">
                        <h6 class="text-muted fw-semibold mb-2">Description</h6>
                        <p class="bg-light p-3 rounded-2">{{ $categorie->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Produits de la catégorie -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-boxes me-2 text-success"></i>
                        Produits de cette catégorie ({{ $categorie->produits->count() }})
                    </h5>
                    <a href="{{ route('produits.create', ['categorie_id' => $categorie->id]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-2"></i>Ajouter un produit
                    </a>
                </div>
            </div>
            <div class="card-body">
                @forelse($categorie->produits as $produit)
                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-2 border">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-gradient rounded-circle p-2 me-3">
                                <i class="fas fa-box text-white fa-sm"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $produit->nom }}</h6>
                                <small class="text-muted">Réf: {{ $produit->reference }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="mb-1">
                                <strong class="text-primary">{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</strong>
                            </div>
                            <div>
                                @if($produit->stock_actuel == 0)
                                    <span class="badge bg-danger">Rupture</span>
                                @elseif($produit->stock_actuel <= $produit->stock_min)
                                    <span class="badge bg-warning">Stock faible</span>
                                @else
                                    <span class="badge bg-success">{{ $produit->stock_actuel }} en stock</span>
                                @endif
                                @if(!$produit->active)
                                    <span class="badge bg-secondary ms-1">Inactif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-box fa-3x mb-3"></i>
                            <h5>Aucun produit dans cette catégorie</h5>
                            <p>Ajoutez des produits pour commencer à vendre</p>
                            <a href="{{ route('produits.create', ['categorie_id' => $categorie->id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Ajouter le premier produit
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Statistiques de la catégorie -->
        <div class="card animate-fadeInUp mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-chart-pie me-2 text-info"></i>
                    Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Valeur totale du stock</span>
                        <strong>{{ number_format($categorie->produits->sum(function($p) { return $p->stock_actuel * $p->prix_achat; }), 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Valeur potentielle</span>
                        <strong class="text-primary">{{ number_format($categorie->produits->sum(function($p) { return $p->stock_actuel * $p->prix_vente; }), 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Produits en rupture</span>
                        <strong class="text-danger">{{ $categorie->produits->where('stock_actuel', 0)->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Stock faible</span>
                        <strong class="text-warning">{{ $categorie->produits->where('stock_actuel', '<=', 'stock_min')->where('stock_actuel', '>', 0)->count() }}</strong>
                    </div>
                </div>

                @if($categorie->produits->count() > 0)
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ ($categorie->produits->where('stock_actuel', '>', 'stock_min')->count() / $categorie->produits->count()) * 100 }}%"></div>
                        <div class="progress-bar bg-warning" style="width: {{ ($categorie->produits->where('stock_actuel', '<=', 'stock_min')->where('stock_actuel', '>', 0)->count() / $categorie->produits->count()) * 100 }}%"></div>
                        <div class="progress-bar bg-danger" style="width: {{ ($categorie->produits->where('stock_actuel', 0)->count() / $categorie->produits->count()) * 100 }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span><i class="fas fa-circle text-success me-1"></i>Normal</span>
                        <span><i class="fas fa-circle text-warning me-1"></i>Faible</span>
                        <span><i class="fas fa-circle text-danger me-1"></i>Rupture</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
