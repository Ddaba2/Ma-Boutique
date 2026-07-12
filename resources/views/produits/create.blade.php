@extends('layouts.app')

@section('title', 'Ajouter un produit')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus text-primary me-2"></i>
                    Ajouter un nouveau produit
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('produits.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom du produit *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorie_id" class="form-label">Catégorie *</label>
                                <select class="form-select" id="categorie_id" name="categorie_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $categorie)
                                        <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                            {{ $categorie->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categorie_id')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prix_achat" class="form-label">Prix d'achat (FCFA) *</label>
                                <input type="number" class="form-control" id="prix_achat" name="prix_achat" value="{{ old('prix_achat') }}" step="0.01" min="0" required>
                                @error('prix_achat')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prix_vente" class="form-label">Prix de vente (FCFA) *</label>
                                <input type="number" class="form-control" id="prix_vente" name="prix_vente" value="{{ old('prix_vente') }}" step="0.01" min="0" required>
                                @error('prix_vente')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="benefice" class="form-label">Bénéfice</label>
                                <input type="text" class="form-control" id="benefice" readonly class="bg-light">
                                <small class="text-muted">Calculé automatiquement</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_actuel" class="form-label">Stock actuel *</label>
                                <input type="number" class="form-control" id="stock_actuel" name="stock_actuel" value="{{ old('stock_actuel', 0) }}" min="0" required>
                                @error('stock_actuel')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_min" class="form-label">Stock minimum *</label>
                                <input type="number" class="form-control" id="stock_min" name="stock_min" value="{{ old('stock_min', 5) }}" min="0" required>
                                @error('stock_min')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_max" class="form-label">Stock maximum *</label>
                                <input type="number" class="form-control" id="stock_max" name="stock_max" value="{{ old('stock_max', 100) }}" min="0" required>
                                @error('stock_max')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">URL de l'image</label>
                        <input type="url" class="form-control" id="image" name="image" value="{{ old('image') }}" placeholder="https://example.com/image.jpg">
                        @error('image')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Optionnel : URL d'une image pour le produit</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('produits.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer le produit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const prixVente = document.getElementById('prix_vente');
    const prixAchat = document.getElementById('prix_achat');
    const benefice = document.getElementById('benefice');

    function calculerBenefice() {
        const vente = parseFloat(prixVente.value) || 0;
        const achat = parseFloat(prixAchat.value) || 0;
        const benef = vente - achat;
        benefice.value = benef > 0 ? benef.toFixed(2) + ' FCFA' : '0 FCFA';
    }

    prixVente.addEventListener('input', calculerBenefice);
    prixAchat.addEventListener('input', calculerBenefice);
});
</script>
@endsection
