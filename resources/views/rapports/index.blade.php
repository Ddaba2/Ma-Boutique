@extends('layouts.premium')

@section('title', 'Rapports')
@section('subtitle', 'Historique des ventes et entrées de stock')

@section('content')
<div class="row mt-4">
    <div class="col-12">
        <!-- Rapports détaillés -->
        <div class="d-flex gap-2 flex-wrap mb-4">
            <a href="{{ route('rapports.ventes') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-2"></i>Rapport détaillé des ventes
            </a>
            <a href="{{ route('rapports.stocks') }}" class="btn btn-outline-primary">
                <i class="fas fa-boxes me-2"></i>Rapport détaillé des stocks
            </a>
            <a href="{{ route('rapports.performances') }}" class="btn btn-outline-primary">
                <i class="fas fa-trophy me-2"></i>Performances & rentabilité
            </a>
        </div>

        <!-- Filtres -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('rapports.index') }}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label for="date_debut" class="form-label fw-semibold">Date de début</label>
                            <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut', now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_fin" class="form-label fw-semibold">Date de fin</label>
                            <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Filtrer
                                </button>
                                <a href="{{ route('rapports.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Historique des ventes -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-shopping-cart me-2 text-success"></i>
                        Historique des ventes
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dateDebut = request('date_debut', now()->subDays(30)->format('Y-m-d'));
                                $dateFin = request('date_fin', now()->format('Y-m-d'));
                                $ventes = \App\Models\Vente::whereDate('created_at', '>=', $dateDebut)
                                    ->whereDate('created_at', '<=', $dateFin)
                                    ->with('detailVentes')
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                            @endphp
                            
                            @if($ventes->count() > 0)
                                @foreach($ventes as $vente)
                                    <tr>
                                        <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                                        <td><span class="badge bg-light text-dark">{{ $vente->reference }}</span></td>
                                        <td>{{ $vente->client_nom ?? 'Non spécifié' }}</td>
                                        <td class="fw-bold">{{ $vente->detailVentes->sum('quantite') }}</td>
                                        <td class="fw-bold text-primary">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                                        <td><span class="badge bg-success">Payée</span></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>Aucune vente trouvée pour cette période</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- Total des ventes -->
                @if($ventes->count() > 0)
                    <div class="alert alert-success mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Total des ventes:</strong> {{ $ventes->count() }} transaction(s)
                            </div>
                            <div>
                                <strong>Montant total:</strong> {{ number_format($ventes->sum('total'), 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Historique des entrées de stock -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-plus-circle me-2 text-warning"></i>
                        Historique des entrées de stock
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                                <th>Fournisseur</th>
                                <th>Motif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dateDebut = request('date_debut', now()->subDays(30)->format('Y-m-d'));
                                $dateFin = request('date_fin', now()->format('Y-m-d'));
                                
                                $entreesStock = \App\Models\Produit::where('created_at', '>=', $dateDebut . ' 00:00:00')
                                    ->where('created_at', '<=', $dateFin . ' 23:59:59')
                                    ->where('stock_actuel', '>', 0)
                                    ->get()
                                    ->map(function($produit) {
                                        return (object) [
                                            'created_at' => $produit->created_at,
                                            'produit' => $produit,
                                            'quantite' => $produit->stock_actuel,
                                            'montant' => $produit->stock_actuel * $produit->prix_achat,
                                            'fournisseur' => 'Stock initial',
                                            'motif' => 'Entrée de stock initiale'
                                        ];
                                    });
                            @endphp
                            
                            @if($entreesStock->count() > 0)
                                @foreach($entreesStock as $entree)
                                    <tr>
                                        <td>{{ $entree->created_at->format('d/m/Y') }}</td>
                                        <td>{{ $entree->produit->nom ?? 'Produit supprimé' }}</td>
                                        <td class="fw-bold text-success">+{{ $entree->quantite }}</td>
                                        <td class="fw-bold text-primary">{{ number_format($entree->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $entree->fournisseur ?? 'Non spécifié' }}</td>
                                        <td>{{ $entree->motif ?? 'Entrée de stock' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-boxes fa-2x mb-2"></i>
                                        <p>Aucune entrée de stock trouvée pour cette période</p>
                                        <small class="text-muted">Les entrées de stock apparaîtront ici une fois enregistrées</small>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- Total des entrées -->
                @if($entreesStock->count() > 0)
                    <div class="alert alert-warning mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Total des entrées:</strong> {{ $entreesStock->count() }} entrée(s)
                            </div>
                            <div>
                                <strong>Quantité totale:</strong> {{ $entreesStock->sum('quantite') }} unités
                            </div>
                            <div>
                                <strong>Montant total:</strong> {{ number_format($entreesStock->sum('montant'), 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bouton unique pour télécharger tous les rapports -->
        <div class="card animate-fadeInUp mt-4">
            <div class="card-body text-center">
                <h5 class="mb-3">
                    <i class="fas fa-download me-2"></i>
                    Télécharger tous les rapports
                </h5>
                <div class="d-flex justify-content-center">
                    <a href="{{ route('rapports.export.pdf.complete') }}?date_debut={{ request('date_debut', now()->subDays(30)->format('Y-m-d')) }}&date_fin={{ request('date_fin', now()->format('Y-m-d')) }}" class="btn btn-danger btn-lg">
                        <i class="fas fa-file-pdf me-2"></i>Télécharger PDF complet
                    </a>
                </div>
                <p class="text-muted mt-3 mb-0">
                    <small>Inclut les ventes et les entrées de stock pour la période sélectionnée</small>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
