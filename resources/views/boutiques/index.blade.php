@extends('layouts.premium')
@section('title', 'Boutiques')
@section('subtitle', 'Gérez vos points de vente')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-store me-2 text-primary"></i>Boutiques ({{ $boutiques->count() }})</h5>
                <a href="{{ route('boutiques.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Nouvelle boutique
                </a>
            </div>
            <div class="card-body p-0">
                @if($boutiques->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th><th>Adresse</th><th>Téléphone</th>
                                <th>Utilisateurs</th><th>Statut</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($boutiques as $boutique)
                            <tr>
                                <td class="fw-semibold">{{ $boutique->nom }}</td>
                                <td>{{ $boutique->adresse ?? '—' }}</td>
                                <td>{{ $boutique->telephone ?? '—' }}</td>
                                <td><span class="badge bg-info">{{ $boutique->utilisateurs_count }}</span></td>
                                <td>
                                    @if($boutique->active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('boutiques.edit', $boutique) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-store fa-3x mb-3"></i>
                    <p>Aucune boutique pour l'instant.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
