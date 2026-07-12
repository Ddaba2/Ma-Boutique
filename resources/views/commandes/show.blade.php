@extends('layouts.premium')
@section('title', 'Commande ' . $commande->reference)
@section('subtitle', 'Détail du bon de commande')

@section('content')
<div class="row mt-4">
    <div class="col-xl-4 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Informations</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Référence</span><strong>{{ $commande->reference }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Fournisseur</span><strong>{{ $commande->fournisseur->nom }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Créée le</span><strong>{{ $commande->created_at->format('d/m/Y') }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Livraison prévue</span><strong>{{ $commande->date_livraison_prevue?->format('d/m/Y') ?? '—' }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Total</span><strong class="text-primary fs-5">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Statut</span>
                        <span class="badge bg-{{ $commande->statutBadge() }}">{{ $commande->statutLabel() }}</span>
                    </li>
                </ul>

                @if(!in_array($commande->statut, ['recue', 'annulee']))
                <div class="mt-4">
                    <form method="POST" action="{{ route('commandes.statut', $commande) }}">
                        @csrf @method('PATCH')
                        <label class="form-label fw-semibold small">Changer le statut</label>
                        <select name="statut" class="form-select form-select-sm mb-2">
                            @foreach(['en_attente'=>'En attente','envoyee'=>'Envoyée','recue_partielle'=>'Reçue partiellement','recue'=>'Reçue (met à jour le stock)','annulee'=>'Annulée'] as $val => $label)
                                <option value="{{ $val }}" {{ $commande->statut === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-primary w-100">Mettre à jour</button>
                    </form>
                </div>
                @endif

                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('commandes.pdf', $commande) }}" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf me-2"></i>Télécharger le PDF
                    </a>
                    <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-success"></i>Produits commandés</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>Produit</th><th>Référence</th><th>Quantité</th><th>Prix unit.</th><th>Total ligne</th></tr></thead>
                        <tbody>
                            @foreach($commande->details as $detail)
                            <tr>
                                <td class="fw-semibold">{{ $detail->produit->nom }}</td>
                                <td><span class="badge bg-light text-dark">{{ $detail->produit->reference }}</span></td>
                                <td><span class="badge bg-primary">{{ $detail->quantite }}</span></td>
                                <td>{{ number_format($detail->prix_unitaire, 0, ',', ' ') }} F</td>
                                <td class="fw-bold">{{ number_format($detail->total_ligne, 0, ',', ' ') }} F</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4" class="text-end">TOTAL</td>
                                <td class="text-primary">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @if($commande->notes)
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="fw-bold text-muted mb-2"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                <p class="mb-0">{{ $commande->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
