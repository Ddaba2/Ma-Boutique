@extends('layouts.premium')
@section('title', 'Clients')
@section('subtitle', 'Base clients consolidée')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body py-3">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un client..." value="{{ request('search') }}">
                    <button class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
                    @if(request('search'))
                        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Effacer</a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2 text-primary"></i>Clients ({{ $clients->total() }})</h5>
                <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Nouveau client
                </a>
            </div>
            <div class="card-body p-0">
                @if($clients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Nom</th><th>Téléphone</th><th>Email</th>
                                <th>Nb ventes</th><th>Statut</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $client->nom_complet }}</div>
                                </td>
                                <td>{{ $client->telephone ?? '—' }}</td>
                                <td>{{ $client->email ?? '—' }}</td>
                                <td><span class="badge bg-info">{{ $client->ventes_count }}</span></td>
                                <td>
                                    @if($client->active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $clients->withQueryString()->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                    <p>Aucun client trouvé.</p>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary">Ajouter le premier client</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
