@extends('layouts.app')

@section('title', 'Gestion des produits')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4>Liste des produits</h4>
        <p class="text-muted">Gérez votre catalogue de produits</p>
    </div>
    <div>
        <form class="d-flex" method="GET" action="{{ route('produits.index') }}">
            <input class="form-control me-2" type="search" name="search" placeholder="Rechercher un produit..." value="{{ request('search') }}">
            <button class="btn btn-outline-primary" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($produits->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Prix vente</th>
                            <th>Stock</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produits as $produit)
                            <tr>
                                <td><strong>{{ $produit->reference }}</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($produit->image)
                                            <img src="{{ $produit->image }}" alt="{{ $produit->nom }}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px;">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px; border-radius: 8px;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $produit->nom }}</strong>
                                            @if($produit->description)
                                                <br><small class="text-muted">{{ Str::limit($produit->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $produit->categorie->nom ?? 'Non catégorisé' }}</span>
                                </td>
                                <td class="fw-bold text-primary">{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold">{{ $produit->stock_actuel }}</span>
                                        <small class="text-muted ms-2">min: {{ $produit->stock_min }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($produit->stock_actuel == 0)
                                        <span class="badge bg-danger">Rupture</span>
                                    @elseif($produit->stock_actuel <= $produit->stock_min)
                                        <span class="badge bg-warning">Stock faible</span>
                                    @else
                                        <span class="badge bg-success">Disponible</span>
                                    @endif
                                    @if(!$produit->active)
                                        <span class="badge bg-secondary ms-1">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('produits.show', $produit) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('produits.edit', $produit) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('produits.destroy', $produit) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $produits->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-4x text-muted mb-3"></i>
                <h5>Aucun produit trouvé</h5>
                <p class="text-muted">Commencez par ajouter votre premier produit</p>
                <a href="{{ route('produits.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Ajouter un produit
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
