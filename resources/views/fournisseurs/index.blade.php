@extends('layouts.premium')
@section('title', 'Fournisseurs')
@section('subtitle', 'Gestion des fournisseurs')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body py-3">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un fournisseur..." value="{{ request('search') }}">
                    <button class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">Effacer</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-truck me-2 text-primary"></i>Fournisseurs ({{ $fournisseurs->total() }})</h5>
                <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Nouveau</a>
            </div>
            <div class="card-body p-0">
                @if($fournisseurs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr><th>Nom</th><th>Contact</th><th>Téléphone</th><th>Délai (j)</th><th>Commandes</th><th>Statut</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($fournisseurs as $f)
                            <tr>
                                <td class="fw-semibold">{{ $f->nom }}</td>
                                <td>{{ $f->contact ?? '—' }}</td>
                                <td>{{ $f->telephone ?? '—' }}</td>
                                <td><span class="badge bg-info">{{ $f->delai_livraison }}j</span></td>
                                <td><span class="badge bg-secondary">{{ $f->commandes_count }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $f->active ? 'success' : 'secondary' }}">
                                        {{ $f->active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('fournisseurs.show', $f) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('fournisseurs.edit', $f) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $fournisseurs->withQueryString()->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-truck fa-3x mb-3 d-block"></i>
                    <p>Aucun fournisseur.</p>
                    <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary">Ajouter</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
