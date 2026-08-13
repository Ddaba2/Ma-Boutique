@extends('layouts.premium')
@section('title', $produit->nom)
@section('subtitle', 'Fiche produit')

@section('content')
<div class="row mt-4">
    <div class="col-xl-4 mb-4">
        <div class="card animate-slideInLeft h-100">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                        <i class="fas fa-box fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $produit->nom }}</h4>
                    <span class="badge bg-light text-dark">{{ $produit->reference }}</span>
                    @if(!$produit->active)
                        <span class="badge bg-secondary ms-1">Inactif</span>
                    @endif
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="fas fa-tag me-2"></i>Catégorie</span>
                        <strong>{{ $produit->categorie->nom ?? '—' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="fas fa-coins me-2"></i>Prix d'achat</span>
                        <strong>{{ number_format($produit->prix_achat, 0, ',', ' ') }} FCFA</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="fas fa-tags me-2"></i>Prix de vente</span>
                        <strong>{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="fas fa-chart-line me-2"></i>Bénéfice / unité</span>
                        <strong class="{{ $produit->benefice() >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($produit->benefice(), 0, ',', ' ') }} FCFA</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="fas fa-boxes me-2"></i>Stock (cette boutique)</span>
                        <strong>{{ $stockBoutique->stock_actuel ?? 0 }}</strong>
                    </li>
                </ul>
                @if($produit->description)
                    <p class="text-muted small mt-3 mb-0">{{ $produit->description }}</p>
                @endif
                <div class="mt-4 d-grid gap-2">
                    @if(in_array(auth()->user()->role, ['gerant', 'gestionnaire']))
                    <a href="{{ route('produits.edit', $produit) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    @endif
                    <a href="{{ route('produits.barcode', $produit) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-barcode me-2"></i>Code-barres
                    </a>
                    <a href="{{ route('ventes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-shopping-cart me-2"></i>Vendre ce produit
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 mb-4">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-info"></i>Derniers mouvements de stock</h5>
            </div>
            <div class="card-body p-0">
                @if($derniersMouvements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr><th>Date</th><th>Type</th><th>Quantité</th><th>Motif</th></tr>
                        </thead>
                        <tbody>
                            @foreach($derniersMouvements as $mouvement)
                            <tr>
                                <td class="small">{{ $mouvement->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $mouvement->type === 'entree' ? 'success' : 'danger' }}">
                                        {{ $mouvement->type === 'entree' ? 'Entrée' : 'Sortie' }}
                                    </span>
                                </td>
                                <td class="fw-bold">{{ $mouvement->type === 'entree' ? '+' : '-' }}{{ $mouvement->quantite }}</td>
                                <td class="small text-muted">{{ $mouvement->motif ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucun mouvement de stock enregistré
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
