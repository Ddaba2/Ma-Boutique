@extends('layouts.premium')
@section('title', 'Mouvements de stock')
@section('subtitle', 'Toutes les entrées, sorties et ajustements de cette boutique')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-exchange-alt me-2 text-info"></i>Mouvements de stock</h5>
                <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Retour au stock
                </a>
            </div>
            <div class="card-body p-0">
                @if($mouvements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Produit</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Motif</th>
                                <th>Référence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mouvements as $mouvement)
                            <tr>
                                <td class="small">{{ $mouvement->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('stocks.historique', $mouvement->produit_id) }}" class="text-decoration-none">
                                        {{ $mouvement->produit->nom ?? 'Produit supprimé' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $mouvement->type === 'entree' ? 'success' : ($mouvement->type === 'ajout_manuel' ? 'info' : 'danger') }}">
                                        {{ match($mouvement->type) {
                                            'entree' => 'Entrée',
                                            'sortie' => 'Sortie',
                                            'ajout_manuel' => 'Ajustement',
                                            'retour_client' => 'Retour client',
                                            default => $mouvement->type,
                                        } }}
                                    </span>
                                </td>
                                <td class="fw-bold">{{ in_array($mouvement->type, ['entree', 'ajout_manuel', 'retour_client']) ? '+' : '-' }}{{ $mouvement->quantite }}</td>
                                <td class="small text-muted">{{ $mouvement->motif ?? '—' }}</td>
                                <td><span class="badge bg-light text-dark">{{ $mouvement->reference }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $mouvements->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucun mouvement de stock enregistré
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
