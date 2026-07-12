@extends('layouts.premium')

@section('title', 'Détail de la Vente')
@section('subtitle', 'Consultez les informations de cette transaction')

@section('content')
<div class="row mt-4">
    <div class="col-md-8">
        <!-- Informations de la vente -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-receipt me-2 text-primary"></i>
                    Vente #{{ $vente->reference }}
                </h5>
                <div>
                    <a href="{{ route('ventes.ticket', $vente) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                        <i class="fas fa-receipt me-1"></i>Ticket de caisse
                    </a>
                    <a href="{{ route('ventes.facture', $vente) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i>Facture PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold">Date et heure</td>
                                <td><strong>{{ $vente->created_at->format('d/m/Y H:i') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Client</td>
                                <td>{{ $vente->client_nom ?? 'Client anonyme' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Téléphone</td>
                                <td>{{ $vente->client_telephone ?? 'Non renseigné' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted fw-semibold">Mode de paiement</td>
                                <td><span class="badge bg-info">{{ $vente->modePaiementLabel() }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Statut</td>
                                <td><span class="badge bg-success">{{ ucfirst($vente->statut) }}</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-semibold">Total</td>
                                <td class="fw-bold text-primary fs-5">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($vente->notes)
                    <div class="mt-3">
                        <h6 class="text-muted fw-semibold mb-2">Notes</h6>
                        <p class="bg-light p-3 rounded-2">{{ $vente->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Produits vendus -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-shopping-basket me-2 text-success"></i>
                    Produits vendus ({{ $vente->detailVentes->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Référence</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Remise</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vente->detailVentes as $detail)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-gradient rounded-circle p-2 me-2">
                                                <i class="fas fa-box text-white fa-sm"></i>
                                            </div>
                                            <strong>{{ $detail->produit->nom }}</strong>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">{{ $detail->produit->reference }}</span></td>
                                    <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</td>
                                    <td><strong>{{ $detail->quantite }}</strong></td>
                                    <td>{{ $detail->remise }}%</td>
                                    <td class="fw-bold text-primary">{{ number_format($detail->total_ligne, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="5" class="text-end">Total</th>
                                <th class="fw-bold">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Résumé financier -->
        <div class="card animate-fadeInUp mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-calculator me-2 text-info"></i>
                    Résumé financier
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sous-total</span>
                        <strong>{{ number_format($vente->detailVentes->sum(function($d) { return $d->prix_unitaire * $d->quantite; }), 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Remise totale</span>
                        <strong class="text-danger">-{{ number_format($vente->detailVentes->sum(function($d) { return ($d->prix_unitaire * $d->quantite) * $d->remise / 100; }), 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total à payer</span>
                        <strong class="text-primary fs-5">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</strong>
                    </div>
                </div>

                @if($vente->montant_recu)
                    <div class="alert alert-success">
                        <h6 class="alert-heading mb-2">Paiement reçu</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Montant reçu:</span>
                            <strong>{{ number_format($vente->montant_recu, 0, ',', ' ') }} FCFA</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Monnaie rendue:</span>
                            <strong class="text-success">{{ number_format($vente->monnaie, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Version imprimable -->
<div class="d-none d-print-block">
    <div class="text-center mb-4">
        <h2>GesBoutique</h2>
        <h4>Reçu de vente #{{ $vente->reference }}</h4>
        <p>{{ $vente->created_at->format('d/m/Y H:i') }}</p>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vente->detailVentes as $detail)
                <tr>
                    <td>{{ $detail->produit->nom }}</td>
                    <td>{{ $detail->quantite }}</td>
                    <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }}</td>
                    <td>{{ number_format($detail->total_ligne, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total</th>
                <th>{{ number_format($vente->total, 0, ',', ' ') }} FCFA</th>
            </tr>
        </tfoot>
    </table>
    
    <div class="text-center mt-4">
        <p>Merci de votre visite !</p>
    </div>
</div>
@endsection
