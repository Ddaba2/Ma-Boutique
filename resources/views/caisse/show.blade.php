@extends('layouts.premium')
@section('title', 'Clôture du ' . $caisse->date->format('d/m/Y'))
@section('subtitle', 'Détail de la clôture de caisse')

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-8">
        <div class="card animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-cash-register me-2 text-primary"></i>
                    Clôture du {{ $caisse->date->format('d/m/Y') }}
                </h5>
                <span class="badge bg-{{ $caisse->statut === 'clos' ? 'success' : 'warning' }} fs-6">
                    {{ ucfirst($caisse->statut) }}
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success bg-opacity-10 border-0 text-center p-3">
                            <div class="small text-muted fw-bold text-uppercase">Espèces</div>
                            <div class="fw-bold fs-5 text-success">{{ number_format($caisse->total_especes, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary bg-opacity-10 border-0 text-center p-3">
                            <div class="small text-muted fw-bold text-uppercase">Carte</div>
                            <div class="fw-bold fs-5 text-primary">{{ number_format($caisse->total_carte, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info bg-opacity-10 border-0 text-center p-3">
                            <div class="small text-muted fw-bold text-uppercase">Mobile</div>
                            <div class="fw-bold fs-5 text-info">{{ number_format($caisse->total_mobile, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning bg-opacity-10 border-0 text-center p-3">
                            <div class="small text-muted fw-bold text-uppercase">Autre</div>
                            <div class="fw-bold fs-5 text-warning">{{ number_format($caisse->total_autre, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                </div>

                <table class="table table-modern">
                    <tbody>
                        <tr><td class="text-muted">Nombre de ventes</td><td class="fw-bold text-end"><span class="badge bg-info fs-6">{{ $caisse->nombre_ventes }}</span></td></tr>
                        <tr><td class="text-muted">Total des ventes</td><td class="fw-bold text-end fs-5">{{ number_format($caisse->total_ventes, 0, ',', ' ') }} FCFA</td></tr>
                        <tr><td class="text-muted">Fond d'ouverture</td><td class="fw-bold text-end">{{ number_format($caisse->fond_ouverture, 0, ',', ' ') }} FCFA</td></tr>
                        <tr><td class="text-muted">Total encaissé</td><td class="fw-bold text-end">{{ number_format($caisse->totalEncaisse(), 0, ',', ' ') }} FCFA</td></tr>
                        <tr class="table-{{ $caisse->ecart >= 0 ? 'success' : 'danger' }}">
                            <td class="fw-bold">Écart de caisse</td>
                            <td class="fw-bold text-end fs-5 {{ $caisse->ecart >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $caisse->ecart >= 0 ? '+' : '' }}{{ number_format($caisse->ecart, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tbody>
                </table>

                @if($caisse->notes)
                <div class="alert alert-info mt-3">
                    <i class="fas fa-sticky-note me-2"></i><strong>Notes :</strong> {{ $caisse->notes }}
                </div>
                @endif

                <div class="text-muted small mt-3">
                    <i class="fas fa-user me-1"></i>Clôturé par {{ $caisse->user?->name ?? '—' }}
                    le {{ $caisse->created_at->format('d/m/Y à H:i') }}
                </div>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('caisse.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
