@extends('layouts.premium')

@section('title', 'Gestion des Catégories')
@section('subtitle', 'Organisez vos produits par catégories')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary animate-fadeInUp" style="animation-delay: 0.1s">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Total catégories</h6>
                                <h3 class="mb-0 fw-bold">{{ $categories->count() }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-primary bg-gradient p-2 rounded-2">
                                    <i class="fas fa-layer-group text-white"></i>
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
                                <h6 class="text-muted mb-2">Catégories actives</h6>
                                <h3 class="mb-0 fw-bold">{{ $categories->where('active', true)->count() }}</h3>
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
                                <h6 class="text-muted mb-2">Catégories inactives</h6>
                                <h3 class="mb-0 fw-bold">{{ $categories->where('active', false)->count() }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-warning bg-gradient p-2 rounded-2">
                                    <i class="fas fa-pause-circle text-white"></i>
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
                                <h6 class="text-muted mb-2">Produits totaux</h6>
                                <h3 class="mb-0 fw-bold">{{ $categories->sum(function($cat) { return $cat->produits->count(); }) }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-info bg-gradient p-2 rounded-2">
                                    <i class="fas fa-boxes text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des catégories -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-layer-group me-2 text-primary"></i>
                        Liste des catégories
                    </h5>
                    <a href="{{ route('categories.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Nouvelle catégorie
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Couleur</th>
                                <th>Produits</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $categorie)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2" style="width: 20px; height: 20px; background-color: {{ $categorie->couleur }};"></div>
                                            <strong>{{ $categorie->nom }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ Str::limit($categorie->description, 50) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-2" style="width: 16px; height: 16px; background-color: {{ $categorie->couleur }};"></div>
                                            <small>{{ $categorie->couleur }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $categorie->produits->count() }}</span>
                                    </td>
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
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('categories.show', $categorie) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('categories.edit', $categorie) }}" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('categories.destroy', $categorie) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-layer-group fa-3x mb-3"></i>
                                            <h5>Aucune catégorie trouvée</h5>
                                            <p>Commencez par créer votre première catégorie</p>
                                            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Nouvelle catégorie
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
