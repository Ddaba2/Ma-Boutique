@extends('layouts.premium')

@section('title', 'Modifier la Catégorie')
@section('subtitle', 'Mettez à jour les informations de cette catégorie')

@section('content')
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-edit me-2 text-warning"></i>
                    Modifier la catégorie : {{ $categorie->nom }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('categories.update', $categorie) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label fw-semibold">Nom de la catégorie *</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="{{ old('nom', $categorie->nom) }}" 
                               placeholder="Ex: Boissons, Électronique, Vêtements" required>
                        @error('nom')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Décrivez cette catégorie de produits">{{ old('description', $categorie->description) }}</textarea>
                        @error('description')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="couleur" class="form-label fw-semibold">Couleur</label>
                                <div class="d-flex gap-2">
                                    <input type="color" class="form-control form-control-color" id="couleur" name="couleur" 
                                           value="{{ old('couleur', $categorie->couleur) }}" 
                                           style="width: 60px;">
                                    <input type="text" class="form-control" id="couleur_text" name="couleur_text" 
                                           value="{{ old('couleur', $categorie->couleur) }}" 
                                           placeholder="#3B82F6" pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                                <small class="text-muted">Choisissez une couleur pour identifier cette catégorie</small>
                                @error('couleur')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="active" class="form-label fw-semibold">Statut</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="active" name="active" 
                                           {{ old('active', $categorie->active) ? 'checked' : '' }} value="1">
                                    <label class="form-check-label" for="active">
                                        Catégorie active
                                    </label>
                                    <div class="text-muted small mt-1">
                                        Les catégories inactives n'apparaîtront pas dans les listes
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Attention
                        </h6>
                        <p class="mb-0">Si vous désactivez cette catégorie, les produits associés ne seront plus visibles dans le système de vente.</p>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                        <a href="{{ route('categories.show', $categorie) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Informations actuelles -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Informations actuelles
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Nom actuel</small>
                    <p class="mb-2 fw-bold">{{ $categorie->nom }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Couleur actuelle</small>
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle me-2" style="width: 20px; height: 20px; background-color: {{ $categorie->couleur }};"></div>
                        <span>{{ $categorie->couleur }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Statut actuel</small>
                    <p class="mb-2">
                        @if($categorie->active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Nombre de produits</small>
                    <p class="mb-2 fw-bold">{{ $categorie->produits->count() }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Créée le</small>
                    <p class="mb-2">{{ $categorie->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Dernière modification</small>
                    <p class="mb-2">{{ $categorie->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Produits associés -->
        <div class="card animate-fadeInUp mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-boxes me-2 text-success"></i>
                    Produits associés ({{ $categorie->produits->count() }})
                </h5>
            </div>
            <div class="card-body">
                @forelse($categorie->produits->take(5) as $produit)
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div>
                            <strong>{{ $produit->nom }}</strong>
                            <br><small class="text-muted">{{ $produit->reference }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">{{ $produit->stock_actuel }} en stock</span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center mb-0">Aucun produit dans cette catégorie</p>
                @endforelse
                
                @if($categorie->produits->count() > 5)
                    <div class="text-center mt-3">
                        <small class="text-muted">Et {{ $categorie->produits->count() - 5 }} autres produits...</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Aperçu en temps réel -->
        <div class="card animate-fadeInUp mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-eye me-2 text-primary"></i>
                    Aperçu en temps réel
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center p-3 rounded-2" id="preview" style="background-color: {{ $categorie->couleur }}20;">
                    <div class="rounded-circle me-3" id="preview_color" style="width: 24px; height: 24px; background-color: {{ $categorie->couleur }};"></div>
                    <div>
                        <strong id="preview_name">{{ $categorie->nom }}</strong>
                        <br><small class="text-muted" id="preview_desc">{{ $categorie->description ?: 'Description de la catégorie...' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nomInput = document.getElementById('nom');
    const descInput = document.getElementById('description');
    const colorInput = document.getElementById('couleur');
    const colorTextInput = document.getElementById('couleur_text');
    const preview = document.getElementById('preview');
    const previewColor = document.getElementById('preview_color');
    const previewName = document.getElementById('preview_name');
    const previewDesc = document.getElementById('preview_desc');

    function updatePreview() {
        const nom = nomInput.value || '{{ $categorie->nom }}';
        const desc = descInput.value || '{{ $categorie->description ?: "Description de la catégorie..." }}';
        const couleur = colorInput.value || '{{ $categorie->couleur }}';
        
        previewName.textContent = nom;
        previewDesc.textContent = desc;
        previewColor.style.backgroundColor = couleur;
        preview.style.backgroundColor = couleur + '20';
    }

    function syncColorInputs() {
        colorTextInput.value = colorInput.value;
        updatePreview();
    }

    function syncColorText() {
        if (/^#[0-9A-Fa-f]{6}$/.test(colorTextInput.value)) {
            colorInput.value = colorTextInput.value;
            updatePreview();
        }
    }

    nomInput.addEventListener('input', updatePreview);
    descInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', syncColorInputs);
    colorTextInput.addEventListener('input', syncColorText);
    
    // Initialisation
    updatePreview();
});
</script>
@endsection
