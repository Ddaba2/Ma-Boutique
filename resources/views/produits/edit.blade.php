@extends('layouts.premium')
@section('title', 'Modifier ' . $produit->nom)
@section('subtitle', 'Fiche produit — catalogue')

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-8">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-warning"></i>Modifier le produit</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('produits.update', $produit) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nom" class="form-label fw-semibold">Nom du produit *</label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $produit->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="categorie_id" class="form-label fw-semibold">Catégorie *</label>
                            <select class="form-select @error('categorie_id') is-invalid @enderror" id="categorie_id" name="categorie_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                @foreach($categories as $categorie)
                                    <option value="{{ $categorie->id }}" {{ old('categorie_id', $produit->categorie_id) == $categorie->id ? 'selected' : '' }}>
                                        {{ $categorie->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categorie_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $produit->description) }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="prix_achat" class="form-label fw-semibold">Prix d'achat (FCFA) *</label>
                            <input type="number" class="form-control @error('prix_achat') is-invalid @enderror" id="prix_achat" name="prix_achat" value="{{ old('prix_achat', $produit->prix_achat) }}" step="0.01" min="0" required>
                            @error('prix_achat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="prix_vente" class="form-label fw-semibold">Prix de vente (FCFA) *</label>
                            <input type="number" class="form-control @error('prix_vente') is-invalid @enderror" id="prix_vente" name="prix_vente" value="{{ old('prix_vente', $produit->prix_vente) }}" step="0.01" min="0" required>
                            @error('prix_vente')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bénéfice</label>
                            <input type="text" class="form-control bg-light" id="benefice" readonly>
                            <small class="text-muted">Calculé automatiquement</small>
                        </div>

                        <div class="col-md-6">
                            <label for="image" class="form-label fw-semibold">URL de l'image</label>
                            <input type="url" class="form-control" id="image" name="image" value="{{ old('image', $produit->image) }}" placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="active" name="active" value="1" {{ old('active', $produit->active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="active">Produit actif (proposé à la vente)</label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <i class="fas fa-info-circle me-2"></i>
                            Stock actuel dans cette boutique : <strong>{{ $stockBoutique->stock_actuel ?? 0 }}</strong>
                            (seuil d'alerte : {{ $stockBoutique->stock_min ?? 5 }})
                            <div class="small text-muted mt-1">La quantité en stock ne se modifie plus ici : utilisez une entrée de stock pour la faire évoluer, afin de garder une trace de chaque mouvement.</div>
                        </div>
                        <a href="{{ route('stocks.create') }}" class="btn btn-sm btn-outline-primary text-nowrap ms-3">
                            <i class="fas fa-boxes me-1"></i>Entrée de stock
                        </a>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
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
document.addEventListener('DOMContentLoaded', function () {
    const prixVente = document.getElementById('prix_vente');
    const prixAchat = document.getElementById('prix_achat');
    const benefice = document.getElementById('benefice');

    function calculerBenefice() {
        const vente = parseFloat(prixVente.value) || 0;
        const achat = parseFloat(prixAchat.value) || 0;
        benefice.value = (vente - achat).toFixed(2) + ' FCFA';
    }

    prixVente.addEventListener('input', calculerBenefice);
    prixAchat.addEventListener('input', calculerBenefice);
    calculerBenefice();
});
</script>
@endsection
