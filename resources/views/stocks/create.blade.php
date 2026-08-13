@extends('layouts.premium')

@section('title', 'Ajouter du Stock')
@section('subtitle', 'Enregistrez une nouvelle entrée de marchandise')

@section('content')
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card animate-fadeInUp">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-plus-circle me-2 text-success"></i>
                    Informations de l'entrée de stock
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('stocks.store') }}">
                    @csrf
                    
                    <!-- Section Produit -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-box me-2"></i>Détails du produit
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="produit_nom" class="form-label fw-semibold">
                                            <i class="fas fa-tag me-1"></i>Nom du produit *
                                        </label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-lg" id="produit_nom" name="produit_nom"
                                                   placeholder="Entrez le nom du produit"
                                                   value="{{ old('produit_nom') }}"
                                                   autocomplete="off"
                                                   required>
                                            <div id="produit-suggestions" class="autocomplete-suggestions shadow-lg rounded-3 d-none"></div>
                                        </div>
                                        @error('produit_nom')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Commencez à taper pour rechercher un produit existant (y compris en rupture) ou entrez un nouveau nom</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="quantite" class="form-label fw-semibold">
                                            <i class="fas fa-sort-numeric-up me-1"></i>Quantité *
                                        </label>
                                        <input type="number" class="form-control form-control-lg" id="quantite" name="quantite"
                                               value="{{ old('quantite') }}"
                                               placeholder="0" min="1" required>
                                        @error('quantite')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="prix_achat" class="form-label fw-semibold">
                                            <i class="fas fa-money-bill-wave me-1"></i>Prix d'achat (FCFA)
                                        </label>
                                        <input type="number" class="form-control" id="prix_achat" name="prix_achat"
                                               value="{{ old('prix_achat') }}"
                                               placeholder="0" min="0" step="0.01">
                                        @error('prix_achat')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="prix_vente" class="form-label fw-semibold">
                                            <i class="fas fa-tag me-1"></i>Prix de vente (FCFA)
                                        </label>
                                        <input type="number" class="form-control" id="prix_vente" name="prix_vente"
                                               value="{{ old('prix_vente') }}"
                                               placeholder="0" min="0" step="0.01">
                                        @error('prix_vente')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Fournisseur -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-warning">
                                <i class="fas fa-truck me-2"></i>Informations fournisseur
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fournisseur" class="form-label fw-semibold">
                                            <i class="fas fa-building me-1"></i>Nom du fournisseur
                                        </label>
                                        <input type="text" class="form-control" id="fournisseur" name="fournisseur"
                                               value="{{ old('fournisseur') }}"
                                               placeholder="Nom du fournisseur">
                                        @error('fournisseur')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="contact_fournisseur" class="form-label fw-semibold">
                                            <i class="fas fa-phone me-1"></i>Contact fournisseur
                                        </label>
                                        <input type="text" class="form-control" id="contact_fournisseur" name="contact_fournisseur"
                                               value="{{ old('contact_fournisseur') }}"
                                               placeholder="Téléphone ou email">
                                        @error('contact_fournisseur')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Détails -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-info">
                                <i class="fas fa-info-circle me-2"></i>Détails de l'entrée
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="motif" class="form-label fw-semibold">
                                            <i class="fas fa-comment-dots me-1"></i>Motif de l'entrée
                                        </label>
                                        <select class="form-select" id="motif" name="motif">
                                            <option value="">Sélectionnez un motif</option>
                                            <option value="Nouvelle livraison" {{ old('motif') == 'Nouvelle livraison' ? 'selected' : '' }}>Nouvelle livraison</option>
                                            <option value="Retour client" {{ old('motif') == 'Retour client' ? 'selected' : '' }}>Retour client</option>
                                            <option value="Correction d'inventaire" {{ old('motif') == 'Correction d\'inventaire' ? 'selected' : '' }}>Correction d'inventaire</option>
                                            <option value="Autre" {{ old('motif') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                        @error('motif')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="reference_facture" class="form-label fw-semibold">
                                            <i class="fas fa-receipt me-1"></i>Référence facture
                                        </label>
                                        <input type="text" class="form-control" id="reference_facture" name="reference_facture"
                                               value="{{ old('reference_facture') }}"
                                               placeholder="N° de facture ou bon de livraison">
                                        @error('reference_facture')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="notes" class="form-label fw-semibold">
                                            <i class="fas fa-sticky-note me-1"></i>Notes supplémentaires
                                        </label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                                  placeholder="Informations complémentaires sur cette entrée de stock">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between gap-3">
                        <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Retour au stock
                        </a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="fas fa-redo me-2"></i>Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>Enregistrer l'entrée
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Colonne latérale -->
    <div class="col-lg-4">
        <!-- Résumé -->
        <div class="card animate-fadeInUp mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Conseils
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-light border-0">
                    <h6 class="alert-heading mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Pour une bonne gestion
                    </h6>
                    <ul class="small mb-0">
                        <li>Vérifiez toujours la quantité reçue</li>
                        <li>Notez le fournisseur pour faciliter les suivis</li>
                        <li>Conservez les factures pour la comptabilité</li>
                        <li>Indiquez un motif clair pour les rapports</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="card animate-fadeInUp">
            <div class="card-header bg-success text-white">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Stock actuel
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-boxes fa-3x text-success"></i>
                    </div>
                    <h5 class="fw-bold">{{ $totalProduits ?? 0 }}</h5>
                    <p class="text-muted mb-0">Produits en stock</p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-success fw-bold">{{ $produitsStockNormal ?? 0 }}</div>
                            <small class="text-muted">Normal</small>
                        </div>
                        <div class="col-4">
                            <div class="text-warning fw-bold">{{ $produitsStockFaible ?? 0 }}</div>
                            <small class="text-muted">Faible</small>
                        </div>
                        <div class="col-4">
                            <div class="text-danger fw-bold">{{ $produitsEnRupture ?? 0 }}</div>
                            <small class="text-muted">Rupture</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1000;
    max-height: 250px;
    overflow-y: auto;
    background: white;
    border: 1px solid #e2e8f0;
    border-top: none;
}
.autocomplete-suggestion {
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
}
.autocomplete-suggestion:last-child { border-bottom: none; }
.autocomplete-suggestion:hover { background: #f8fafc; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-complétion pour le nom du produit : cherche parmi les produits déjà
    // au catalogue (y compris en rupture, voir inclure_ruptures=1) pour éviter
    // de recréer un doublon par simple faute de frappe, et préremplit les prix
    // quand un produit existant est choisi.
    const produitNomInput = document.getElementById('produit_nom');
    const suggestionsDiv = document.getElementById('produit-suggestions');
    const prixAchatInput = document.getElementById('prix_achat');
    const prixVenteInput = document.getElementById('prix_vente');
    let debounceTimer = null;
    let requeteEnCours = null;
    let produitChoisiId = null;

    produitNomInput.addEventListener('input', function() {
        produitChoisiId = null;
        const query = this.value.trim();
        clearTimeout(debounceTimer);

        if (query.length < 2) {
            suggestionsDiv.classList.add('d-none');
            return;
        }

        debounceTimer = setTimeout(() => {
            if (requeteEnCours) requeteEnCours.abort();
            requeteEnCours = new AbortController();

            fetch(`/api/produits/search?q=${encodeURIComponent(query)}&inclure_ruptures=1`, { signal: requeteEnCours.signal })
                .then(res => res.json())
                .then(produits => {
                    suggestionsDiv.innerHTML = '';
                    if (produits.length === 0) {
                        suggestionsDiv.classList.add('d-none');
                        return;
                    }

                    produits.forEach(prod => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-suggestion';
                        div.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>${escapeHtml(prod.nom)}</strong>
                                <small class="text-muted">Stock actuel : ${prod.stock_actuel}</small>
                            </div>
                        `;
                        div.addEventListener('click', () => {
                            produitNomInput.value = prod.nom;
                            produitChoisiId = prod.id;
                            if (prixAchatInput && !prixAchatInput.value) prixAchatInput.value = prod.prix_achat ?? '';
                            if (prixVenteInput && !prixVenteInput.value) prixVenteInput.value = prod.prix_vente ?? '';
                            suggestionsDiv.classList.add('d-none');
                        });
                        suggestionsDiv.appendChild(div);
                    });
                    suggestionsDiv.classList.remove('d-none');
                })
                .catch(err => {
                    if (err.name !== 'AbortError') console.error('Erreur recherche produits:', err);
                });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!produitNomInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('d-none');
        }
    });
});
</script>
@endsection
