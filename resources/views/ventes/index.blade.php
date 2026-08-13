@extends('layouts.premium')

@section('title', 'Historique des Ventes')
@section('subtitle', 'Consultez toutes vos transactions')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <!-- Statistiques principales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-primary animate-fadeInUp" style="animation-delay: 0.1s">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-muted mb-2">Total des ventes</h6>
                                <h3 class="mb-0 fw-bold">{{ $ventes->total() }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-primary bg-gradient p-2 rounded-2">
                                    <i class="fas fa-shopping-cart text-white"></i>
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
                                <h6 class="text-muted mb-2">Chiffre d'affaires</h6>
                                <h3 class="mb-0 fw-bold">{{ number_format($ventes->sum('total'), 0, ',', ' ') }} FCFA</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-success bg-gradient p-2 rounded-2">
                                    <i class="fas fa-chart-line text-white"></i>
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
                                <h6 class="text-muted mb-2">Panier moyen</h6>
                                <h3 class="mb-0 fw-bold">{{ $ventes->count() > 0 ? number_format($ventes->sum('total') / $ventes->count(), 0, ',', ' ') : 0 }} FCFA</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-warning bg-gradient p-2 rounded-2">
                                    <i class="fas fa-calculator text-white"></i>
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
                                <h6 class="text-muted mb-2">Aujourd'hui</h6>
                                <h3 class="mb-0 fw-bold">{{ $ventes->where('created_at', '>=', now()->today())->count() }}</h3>
                            </div>
                            <div class="ms-3">
                                <div class="bg-info bg-gradient p-2 rounded-2">
                                    <i class="fas fa-calendar-day text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des ventes -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Historique des ventes
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('ventes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nouvelle vente
                        </a>
                        <a href="{{ route('rapports.export.pdf.ventes') }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Télécharger PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Date</th>
                                <th>Heure</th>
                                <th>Client</th>
                                <th>Produits</th>
                                <th>Total</th>
                                <th>Paiement</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventes as $vente)
                                <tr>
                                    <td><strong class="text-primary">{{ $vente->reference }}</strong></td>
                                    <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                                    <td><strong>{{ $vente->created_at->format('H:i') }}</strong></td>
                                    <td>{{ $vente->client_nom ?? 'Client anonyme' }}</td>
                                    <td>{{ $vente->detailVentes->sum('quantite') }}</td>
                                    <td class="fw-bold text-primary">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <span class="badge bg-info">{{ $vente->modePaiementLabel() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ match($vente->statut) { 'terminee' => 'success', 'annulee' => 'danger', default => 'secondary' } }}">
                                            {{ match($vente->statut) { 'terminee' => 'Terminée', 'annulee' => 'Annulée', default => ucfirst($vente->statut) } }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('ventes.facture', $vente) }}" class="btn btn-sm btn-outline-danger" title="Facture PDF" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('ventes.ticket', $vente) }}" class="btn btn-sm btn-outline-secondary" title="Ticket de caisse" target="_blank">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            <a href="{{ route('ventes.show', $vente) }}" class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array(auth()->user()->role, ['gerant', 'gestionnaire']) && $vente->statut === 'terminee')
                                            <form method="POST" action="{{ route('ventes.destroy', $vente) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Annuler la vente" onclick="return confirm('Annuler cette vente ? Le stock sera restitué et l\'opération sera tracée.')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="text-center py-5">
                                            <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                                <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                                            </div>
                                            <h5>Aucune vente enregistrée</h5>
                                            <p>Les ventes apparaîtront ici une fois enregistrées</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($ventes->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de {{ $ventes->firstItem() }} à {{ $ventes->lastItem() }} sur {{ $ventes->total() }} ventes
                        </div>
                        <div>
                            {{ $ventes->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
