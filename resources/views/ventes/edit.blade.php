@extends('layouts.premium')

@section('title', 'Modifier la Vente')
@section('subtitle', 'Mettez à jour les informations de cette vente')

@section('content')
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-edit me-2 text-warning"></i>
                    Modifier la vente #{{ $vente->reference }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('ventes.update', $vente) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_nom" class="form-label fw-semibold">Nom du client</label>
                                <input type="text" class="form-control" id="client_nom" name="client_nom" 
                                       value="{{ old('client_nom', $vente->client_nom) }}" 
                                       placeholder="Nom du client">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_telephone" class="form-label fw-semibold">Téléphone</label>
                                <input type="tel" class="form-control" id="client_telephone" name="client_telephone" 
                                       value="{{ old('client_telephone', $vente->client_telephone) }}" 
                                       placeholder="Numéro de téléphone">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" id="client_email" name="client_email" 
                                       value="{{ old('client_email', $vente->client_email) }}" 
                                       placeholder="Email du client">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mode_paiement" class="form-label fw-semibold">Mode de paiement</label>
                                <select class="form-select" id="mode_paiement" name="mode_paiement" required>
                                    <option value="espece" {{ $vente->mode_paiement == 'espece' ? 'selected' : '' }}>Espèce</option>
                                    <option value="carte" {{ $vente->mode_paiement == 'carte' ? 'selected' : '' }}>Carte bancaire</option>
                                    <option value="mobile" {{ $vente->mode_paiement == 'mobile' ? 'selected' : '' }}>Mobile money</option>
                                    <option value="autre" {{ $vente->mode_paiement == 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="montant_recu" class="form-label fw-semibold">Montant reçu</label>
                                <input type="number" class="form-control" id="montant_recu" name="montant_recu" 
                                       value="{{ old('montant_recu', $vente->montant_recu) }}" 
                                       placeholder="Montant reçu" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="statut" class="form-label fw-semibold">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="terminee" {{ $vente->statut == 'terminee' ? 'selected' : '' }}>Terminée</option>
                                    <option value="en_cours" {{ $vente->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="annulee" {{ $vente->statut == 'annulee' ? 'selected' : '' }}>Annulée</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Notes supplémentaires">{{ old('notes', $vente->notes) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                        <a href="{{ route('ventes.show', $vente) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Détails de la vente -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Détails actuels
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Référence</small>
                    <p class="mb-2 fw-bold">{{ $vente->reference }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Date et heure</small>
                    <p class="mb-2">{{ $vente->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Total actuel</small>
                    <p class="mb-2 fw-bold text-primary fs-5">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Produits vendus</small>
                    <p class="mb-2">{{ $vente->detailVentes->count() }} produits</p>
                </div>
            </div>
        </div>

        <!-- Produits de la vente -->
        <div class="card animate-fadeInUp mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-shopping-basket me-2 text-success"></i>
                    Produits de cette vente
                </h5>
            </div>
            <div class="card-body">
                @foreach($vente->detailVentes as $detail)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <strong>{{ $detail->produit->nom }}</strong>
                            <br><small class="text-muted">{{ $detail->quantite }} × {{ number_format($detail->prix_unitaire, 0, ',', ' ') }} FCFA</small>
                        </div>
                        <div class="text-end">
                            <strong class="text-primary">{{ number_format($detail->total_ligne, 0, ',', ' ') }} FCFA</strong>
                        </div>
                    </div>
                @endforeach
                
                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <strong class="fs-5">Total</strong>
                    <strong class="text-primary fs-5">{{ number_format($vente->total, 0, ',', ' ') }} FCFA</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
