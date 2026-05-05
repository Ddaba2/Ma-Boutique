@extends('layouts.premium')

@section('title', 'Nouvelle Catégorie')
@section('subtitle', 'Ajoutez une nouvelle catégorie de produits')

@section('content')
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-plus me-2 text-success"></i>
                    Nouvelle catégorie
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nom" class="form-label fw-semibold">Nom de la catégorie *</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="{{ old('nom') }}" 
                               placeholder="Ex: Boissons, Électronique, Vêtements" required>
                        @error('nom')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Décrivez cette catégorie de produits">{{ old('description') }}</textarea>
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
                                           value="{{ old('couleur', '#3B82F6') }}" 
                                           style="width: 60px;">
                                    <input type="text" class="form-control" id="couleur_text" name="couleur_text" 
                                           value="{{ old('couleur', '#3B82F6') }}" 
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
                                           {{ old('active', true) ? 'checked' : '' }} value="1">
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

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Créer la catégorie
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Informations -->
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Informations
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-semibold text-muted mb-2">Pourquoi créer des catégories ?</h6>
                    <ul class="small text-muted">
                        <li>Organiser vos produits de manière logique</li>
                        <li>Faciliter la recherche dans le POS</li>
                        <li>Générer des rapports par catégorie</li>
                        <li>Améliorer l'expérience utilisateur</li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-semibold text-muted mb-2">Conseils</h6>
                    <ul class="small text-muted">
                        <li>Utilisez des noms clairs et concis</li>
                        <li>Choisissez une couleur distinctive</li>
                        <li>Ajoutez une description utile</li>
                        <li>Activez uniquement les catégories utilisées</li>
                    </ul>
                </div>

                <div class="alert alert-light">
                    <h6 class="alert-heading mb-2">
                        <i class="fas fa-lightbulb me-2 text-warning"></i>
                        Exemples de catégories
                    </h6>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary">Boissons</span>
                        <span class="badge bg-success">Alimentation</span>
                        <span class="badge bg-warning">Électronique</span>
                        <span class="badge bg-info">Vêtements</span>
                        <span class="badge bg-danger">Maison</span>
                        <span class="badge bg-secondary">Hygiène</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aperçu -->
        <div class="card animate-fadeInUp mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-eye me-2 text-primary"></i>
                    Aperçu en temps réel
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center p-3 rounded-2" id="preview" style="background-color: rgba(59, 130, 246, 0.1);">
                    <div class="rounded-circle me-3" id="preview_color" style="width: 24px; height: 24px; background-color: #3B82F6;"></div>
                    <div>
                        <strong id="preview_name">Nom de la catégorie</strong>
                        <br><small class="text-muted" id="preview_desc">Description de la catégorie...</small>
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
        const nom = nomInput.value || 'Nom de la catégorie';
        const desc = descInput.value || 'Description de la catégorie...';
        const couleur = colorInput.value || '#3B82F6';
        
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
