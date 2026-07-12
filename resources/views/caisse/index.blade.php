@extends('layouts.premium')
@section('title', 'Clôtures de caisse')
@section('subtitle', 'Historique des clôtures journalières')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cash-register me-2 text-primary"></i>Clôtures de caisse</h5>
                <a href="{{ route('caisse.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Clôturer aujourd'hui
                </a>
            </div>
            <div class="card-body p-0">
                @if($clotures->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr><th>Date</th><th>Nb ventes</th><th>Total ventes</th><th>Fond ouverture</th><th>Écart</th><th>Statut</th><th>Clôturé par</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($clotures as $c)
                            <tr>
                                <td class="fw-semibold">{{ $c->date->format('d/m/Y') }}</td>
                                <td><span class="badge bg-info">{{ $c->nombre_ventes }}</span></td>
                                <td class="fw-bold">{{ number_format($c->total_ventes, 0, ',', ' ') }} F</td>
                                <td>{{ number_format($c->fond_ouverture, 0, ',', ' ') }} F</td>
                                <td>
                                    <span class="fw-bold {{ $c->ecart >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $c->ecart >= 0 ? '+' : '' }}{{ number_format($c->ecart, 0, ',', ' ') }} F
                                    </span>
                                </td>
                                <td><span class="badge bg-{{ $c->statut === 'clos' ? 'success' : 'warning' }}">{{ ucfirst($c->statut) }}</span></td>
                                <td class="small">{{ $c->user?->name ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('caisse.show', $c) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $clotures->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-cash-register fa-3x mb-3 d-block"></i>
                    <p>Aucune clôture de caisse enregistrée.</p>
                    <a href="{{ route('caisse.create') }}" class="btn btn-primary">Effectuer la première clôture</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
