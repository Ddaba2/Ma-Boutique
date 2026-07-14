@extends('layouts.premium')
@section('title', 'Ventes en conflit')
@section('subtitle', 'Ventes synchronisées hors-ligne avec un stock insuffisant à vérifier')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-triangle-exclamation me-2 text-danger"></i>Ventes en conflit ({{ $ventes->count() }})</h5>
            </div>
            <div class="card-body p-0">
                @if($ventes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Détail du conflit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventes as $vente)
                                    <tr>
                                        <td><strong class="text-primary">{{ $vente->reference }}</strong></td>
                                        <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $vente->client_nom ?? 'Client anonyme' }}</td>
                                        <td class="fw-bold">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                                        <td class="small text-danger">{{ $vente->notes_conflit }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('ventes.show', $vente) }}" class="btn btn-outline-primary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form method="POST" action="{{ route('ventes.conflits.resoudre', $vente) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success" title="Marquer comme résolu" onclick="return confirm('Confirmez-vous que le stock a été vérifié pour cette vente ?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-circle-check fa-3x mb-3 text-success"></i>
                        <p>Aucune vente en conflit pour l'instant.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
