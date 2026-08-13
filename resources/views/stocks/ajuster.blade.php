@extends('layouts.premium')
@section('title', 'Ajuster le stock — ' . $produit->nom)
@section('subtitle', "Correction après comptage physique")

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-6">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-balance-scale me-2 text-warning"></i>{{ $produit->nom }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Utilisez cet écran uniquement pour corriger un écart constaté lors d'un comptage physique
                    (vol, casse, erreur de saisie...). Toute correction est tracée dans l'historique du produit.
                </div>

                <form method="POST" action="{{ route('stocks.ajuster', $produit) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Stock actuel enregistré</label>
                        <input type="text" class="form-control bg-light" value="{{ $stockBoutique->stock_actuel ?? 0 }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nouveau_stock" class="form-label fw-semibold">Stock réel constaté *</label>
                        <input type="number" class="form-control @error('nouveau_stock') is-invalid @enderror"
                               id="nouveau_stock" name="nouveau_stock" min="0" required
                               value="{{ old('nouveau_stock', $stockBoutique->stock_actuel ?? 0) }}">
                        @error('nouveau_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="motif" class="form-label fw-semibold">Motif de l'ajustement *</label>
                        <input type="text" class="form-control @error('motif') is-invalid @enderror"
                               id="motif" name="motif" required placeholder="Ex : inventaire mensuel, casse, erreur de saisie..."
                               value="{{ old('motif') }}">
                        @error('motif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-check me-2"></i>Valider l'ajustement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
