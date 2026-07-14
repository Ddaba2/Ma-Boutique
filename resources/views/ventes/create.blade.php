@extends('layouts.premium')

@section('title', 'Nouvelle Vente')
@section('subtitle', 'Enregistrer une nouvelle vente multi-produits')

@section('content')
<div class="row mt-4">
    <!-- Formulaire de vente (Gauche) -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card animate-fadeInUp h-100">
            <div class="card-header bg-success text-white py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Panier de Vente
                </h5>
            </div>
            <div class="card-body p-4">
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

                <form method="POST" action="{{ route('ventes.store') }}" id="venteForm">
                    @csrf
                    
                    <!-- Informations client -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom py-2">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-user me-2"></i>Informations client
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="client_nom" class="form-label fw-semibold mb-1">
                                            Nom du client *
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="client_nom" name="client_nom" 
                                               placeholder="Entrez le nom du client" 
                                               value="{{ old('client_nom', 'Client Comptant') }}"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="client_telephone" class="form-label fw-semibold mb-1">
                                            Téléphone
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="client_telephone" name="client_telephone"
                                               value="{{ old('client_telephone') }}"
                                               placeholder="Téléphone du client">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sélection de produit -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom py-2">
                            <h6 class="mb-0 fw-bold text-success">
                                <i class="fas fa-search me-2"></i>Rechercher & Ajouter des produits
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-6 position-relative mb-3 mb-md-0">
                                    <label for="produit_search" class="form-label fw-semibold mb-1">Rechercher une marchandise</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control form-control-lg" id="produit_search" 
                                               placeholder="Tapez le nom ou la référence..." autocomplete="off">
                                    </div>
                                    <!-- Liste d'autocomplétion personnalisée -->
                                    <div id="autocomplete-results" class="autocomplete-suggestions shadow-lg rounded-3 d-none"></div>
                                </div>
                                <div class="col-md-2 mb-3 mb-md-0">
                                    <label for="input_quantite" class="form-label fw-semibold mb-1">Qté</label>
                                    <input type="number" class="form-control form-control-lg" id="input_quantite" value="1" min="1">
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-success btn-lg w-100 py-3" id="btn-ajouter-produit">
                                        <i class="fas fa-plus me-2"></i>Ajouter au panier
                                    </button>
                                </div>
                            </div>

                            <!-- Informations rapides produit sélectionné -->
                            <div id="selected-product-info" class="mt-3 p-3 bg-white rounded-3 border d-none">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2" id="info-ref">-</span>
                                        <span class="fw-bold" id="info-nom">-</span>
                                    </div>
                                    <div>
                                        Prix: <span class="fw-bold text-success" id="info-prix">0</span> FCFA |
                                        Stock: <span class="badge bg-secondary" id="info-stock">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des articles (Panier) -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-dark">
                                <i class="fas fa-shopping-basket me-2 text-info"></i>Articles dans cette vente
                            </h6>
                            <span class="badge bg-info" id="cart-item-count">0 article</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="cart-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4 py-3">Marchandise</th>
                                            <th class="text-end py-3">Prix (FCFA)</th>
                                            <th class="text-center py-3" style="width: 150px;">Quantité</th>
                                            <th class="text-end py-3">Total (FCFA)</th>
                                            <th class="text-center py-3" style="width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cart-tbody">
                                        <tr id="empty-cart-row">
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fas fa-shopping-basket fa-3x mb-3 text-secondary d-block"></i>
                                                Aucun produit n'a encore été ajouté au panier.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Paiement & Règlement -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom py-2">
                            <h6 class="mb-0 fw-bold text-warning">
                                <i class="fas fa-money-bill-alt me-2"></i>Règlement & Mode de paiement
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3 mb-md-0">
                                        <label for="mode_paiement" class="form-label fw-semibold mb-1">Mode de paiement *</label>
                                        <select class="form-select form-select-lg" id="mode_paiement" name="mode_paiement" required>
                                            <option value="espece" selected>Espèce</option>
                                            <option value="mobile">Mobile Money</option>
                                            <option value="carte">Carte bancaire</option>
                                            <option value="autre">Autre</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3 mb-md-0">
                                        <label for="montant_recu" class="form-label fw-semibold mb-1">Montant reçu (FCFA) *</label>
                                        <input type="number" class="form-control form-control-lg text-end fw-bold" id="montant_recu" name="montant_recu" placeholder="0" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <span class="text-muted d-block small mb-1">Monnaie à rendre :</span>
                                        <div class="form-control form-control-lg text-end fw-bold bg-success text-white" id="reste">
                                            0 FCFA
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-success btn-lg px-5 py-3" id="btn-valider-vente" disabled>
                            <i class="fas fa-save me-2"></i>Enregistrer la vente & Facturer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Prévisualisation de la Facture (Droite) -->
    <div class="col-xl-4 col-lg-5 mb-4 no-print">
        <div class="card animate-fadeInUp h-100">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="fas fa-receipt me-2"></i>
                    Aperçu de la Facture
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="facture-container p-4 bg-white shadow-sm">
                    <!-- En-tête -->
                    <div class="facture-header p-3 text-white rounded-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0 fw-bold">GesBoutique</h4>
                                <small>Matériel Informatique & Mobilier</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-light text-dark fw-bold">FACTURE</span>
                            </div>
                        </div>
                    </div>

                    <!-- Infos Facture -->
                    <div class="row mb-3 small">
                        <div class="col-6">
                            <span class="text-muted d-block">Facturé à :</span>
                            <strong id="facture_client" class="text-dark">Client Comptant</strong>
                            <span id="facture_telephone" class="d-block text-muted">-</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted d-block">N° Facture :</span>
                            <strong id="facture_numero" class="text-primary font-monospace">-</strong>
                            <span class="d-block text-muted">Date: {{ date('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Tableau des articles -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered small">
                            <thead class="table-light">
                                <tr>
                                    <th>Article</th>
                                    <th class="text-center" style="width: 40px;">Qté</th>
                                    <th class="text-end" style="width: 70px;">P.U.</th>
                                    <th class="text-end" style="width: 80px;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="facture-tbody">
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Panier vide</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totaux -->
                    <div class="border-top pt-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total HT :</span>
                            <span class="fw-bold"><span id="facture_total_ht">0</span> FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">TVA (0%) :</span>
                            <span class="fw-bold">0 FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between border-top border-dark pt-2 mb-2">
                            <span class="fw-bold text-dark fs-6">Net à payer :</span>
                            <strong class="text-primary fs-6"><span id="facture_grand_total">0</span> FCFA</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1 text-success">
                            <span class="small">Montant Reçu :</span>
                            <span class="small"><span id="facture_recu">0</span> FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between text-info fw-bold">
                            <span class="small">Rendu :</span>
                            <span class="small"><span id="facture_reste">0</span> FCFA</span>
                        </div>
                    </div>

                    <!-- Pied de page -->
                    <div class="text-center text-muted small mt-4 pt-3 border-top">
                        Merci pour votre confiance !
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles de recherche autocomplete */
.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 12px;
    right: 12px;
    z-index: 1000;
    max-height: 250px;
    overflow-y: auto;
    background: white;
    border: 1px solid #e2e8f0;
    border-top: none;
}

.autocomplete-suggestion {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s ease;
}

.autocomplete-suggestion:last-child {
    border-bottom: none;
}

.autocomplete-suggestion:hover {
    background: #f8fafc;
}

/* Styles pour la facture professionnelle */
.facture-container {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
}

.facture-header {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
}
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const searchInput = document.getElementById('produit_search');
    const resultsDiv = document.getElementById('autocomplete-results');
    const selectedInfoDiv = document.getElementById('selected-product-info');
    const inputQuantite = document.getElementById('input_quantite');
    const btnAjouter = document.getElementById('btn-ajouter-produit');
    
    const cartTbody = document.getElementById('cart-tbody');
    const cartItemCount = document.getElementById('cart-item-count');
    const montantRecuInput = document.getElementById('montant_recu');
    const resteDiv = document.getElementById('reste');
    const btnValider = document.getElementById('btn-valider-vente');
    
    // Éléments de l'aperçu facture
    const factureClient = document.getElementById('facture_client');
    const factureTelephone = document.getElementById('facture_telephone');
    const factureTbody = document.getElementById('facture-tbody');
    const factureTotalHt = document.getElementById('facture_total_ht');
    const factureGrandTotal = document.getElementById('facture_grand_total');
    const factureRecu = document.getElementById('facture_recu');
    const factureReste = document.getElementById('facture_reste');
    const factureNumero = document.getElementById('facture_numero');
    
    // Variables d'état
    let selectedProduct = null;
    let cart = [];

    // Générer numéro de facture automatique
    const factNum = 'V' + new Date().getFullYear() + 
                    String(new Date().getMonth() + 1).padStart(2, '0') + 
                    String(new Date().getDate()).padStart(2, '0') + 
                    String(Math.floor(100 + Math.random() * 900));
    factureNumero.textContent = factNum;

    // Événements d'informations client
    document.getElementById('client_nom').addEventListener('input', function() {
        factureClient.textContent = this.value || 'Client Comptant';
    });
    
    document.getElementById('client_telephone').addEventListener('input', function() {
        factureTelephone.textContent = this.value || '-';
    });

    // Recherche de produits AJAX
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            resultsDiv.classList.add('d-none');
            return;
        }

        fetch(`/api/produits/search?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(produits => {
                resultsDiv.innerHTML = '';
                if (produits.length === 0) {
                    resultsDiv.innerHTML = '<div class="p-3 text-muted">Aucun produit trouvé</div>';
                    resultsDiv.classList.remove('d-none');
                    return;
                }

                produits.forEach(prod => {
                    const div = document.createElement('div');
                    div.className = 'autocomplete-suggestion';
                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-light text-dark me-2 font-monospace">${prod.stock_actuel > 0 ? 'Disponible' : 'Rupture'}</span>
                                <strong>${prod.nom}</strong>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-success">${parseInt(prod.prix_vente)} FCFA</span><br>
                                <small class="text-muted">Stock: ${prod.stock_actuel}</small>
                            </div>
                        </div>
                    `;
                    div.addEventListener('click', () => {
                        selectProduct(prod);
                    });
                    resultsDiv.appendChild(div);
                });
                resultsDiv.classList.remove('d-none');
            })
            .catch(err => {
                console.error('Erreur recherche produits:', err);
            });
    });

    // Masquer l'autocomplétion lors d'un clic en dehors
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('d-none');
        }
    });

    // Sélectionner un produit
    function selectProduct(prod) {
        selectedProduct = prod;
        searchInput.value = prod.nom;
        resultsDiv.classList.add('d-none');
        
        // Mettre à jour la carte d'info produit
        document.getElementById('info-ref').textContent = 'ID: ' + prod.id;
        document.getElementById('info-nom').textContent = prod.nom;
        document.getElementById('info-prix').textContent = parseInt(prod.prix_vente).toLocaleString();
        document.getElementById('info-stock').textContent = prod.stock_actuel;
        
        selectedInfoDiv.classList.remove('d-none');
        inputQuantite.focus();
    }

    // Ajouter un produit au panier
    btnAjouter.addEventListener('click', function() {
        if (!selectedProduct) {
            alert('Veuillez d\'abord rechercher et sélectionner un produit.');
            return;
        }

        const qty = parseInt(inputQuantite.value) || 1;
        if (qty < 1) {
            alert('La quantité doit être supérieure ou égale à 1.');
            return;
        }

        if (qty > selectedProduct.stock_actuel) {
            alert(`Stock insuffisant. Quantité maximale disponible : ${selectedProduct.stock_actuel}`);
            return;
        }

        // Vérifier si le produit est déjà dans le panier
        const existingItem = cart.find(item => item.product.id === selectedProduct.id);
        if (existingItem) {
            const newQty = existingItem.quantity + qty;
            if (newQty > selectedProduct.stock_actuel) {
                alert(`Impossible d'ajouter cette quantité. Le stock total dépasserait la limite disponible (${selectedProduct.stock_actuel}).`);
                return;
            }
            existingItem.quantity = newQty;
        } else {
            cart.push({
                product: selectedProduct,
                quantity: qty,
                price: parseFloat(selectedProduct.prix_vente)
            });
        }

        // Réinitialiser la sélection
        selectedProduct = null;
        searchInput.value = '';
        selectedInfoDiv.classList.add('d-none');
        inputQuantite.value = 1;

        // Mettre à jour l'interface
        updateCartUI();
    });

    // Mettre à jour l'interface utilisateur du panier et de la facture
    function updateCartUI() {
        // Vider la table HTML
        cartTbody.innerHTML = '';
        factureTbody.innerHTML = '';

        if (cart.length === 0) {
            cartTbody.innerHTML = `
                <tr id="empty-cart-row">
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="fas fa-shopping-basket fa-3x mb-3 text-secondary d-block"></i>
                        Aucun produit n'a encore été ajouté au panier.
                    </td>
                </tr>
            `;
            factureTbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted small">Panier vide</td>
                </tr>
            `;
            cartItemCount.textContent = '0 article';
            btnValider.disabled = true;
            
            updateTotals(0);
            return;
        }

        let grandTotal = 0;
        let totalItems = 0;

        cart.forEach((item, index) => {
            const lineTotal = item.quantity * item.price;
            grandTotal += lineTotal;
            totalItems += item.quantity;

            // Ligne pour la table panier (Formulaire)
            const trCart = document.createElement('tr');
            trCart.innerHTML = `
                <td class="px-4">
                    <strong class="d-block text-dark">${item.product.nom}</strong>
                    <small class="text-muted font-monospace">Ref: PROD-${String(item.product.id).padStart(4, '0')}</small>
                    <!-- Champs cachés pour l'envoi du formulaire -->
                    <input type="hidden" name="produit_ids[]" value="${item.product.id}">
                    <input type="hidden" name="prix_unitaires[]" value="${item.price}">
                </td>
                <td class="text-end fw-semibold text-success">${parseInt(item.price).toLocaleString()}</td>
                <td class="text-center">
                    <div class="input-group input-group-sm justify-content-center">
                        <button type="button" class="btn btn-outline-secondary px-2 btn-qty-minus" data-index="${index}">-</button>
                        <input type="number" name="quantites[]" class="form-control text-center input-qty-val" value="${item.quantity}" min="1" max="${item.product.stock_actuel}" style="max-width: 60px;" data-index="${index}">
                        <button type="button" class="btn btn-outline-secondary px-2 btn-qty-plus" data-index="${index}">+</button>
                    </div>
                </td>
                <td class="text-end fw-bold text-dark">${parseInt(lineTotal).toLocaleString()}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger btn-delete-item" data-index="${index}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            cartTbody.appendChild(trCart);

            // Ligne pour l'aperçu facture (Droite)
            const trFacture = document.createElement('tr');
            trFacture.innerHTML = `
                <td>${item.product.nom}</td>
                <td class="text-center">${item.quantity}</td>
                <td class="text-end">${parseInt(item.price).toLocaleString()}</td>
                <td class="text-end">${parseInt(lineTotal).toLocaleString()}</td>
            `;
            factureTbody.appendChild(trFacture);
        });

        // Configurer les écouteurs de quantité dans le panier
        document.querySelectorAll('.btn-qty-minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                if (cart[idx].quantity > 1) {
                    cart[idx].quantity--;
                    updateCartUI();
                }
            });
        });

        document.querySelectorAll('.btn-qty-plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                if (cart[idx].quantity < cart[idx].product.stock_actuel) {
                    cart[idx].quantity++;
                    updateCartUI();
                } else {
                    alert('Stock limite atteint pour ce produit.');
                }
            });
        });

        document.querySelectorAll('.input-qty-val').forEach(input => {
            input.addEventListener('change', function() {
                const idx = parseInt(this.dataset.index);
                let val = parseInt(this.value) || 1;
                const max = parseInt(this.max);

                if (val < 1) val = 1;
                if (val > max) {
                    alert(`Stock insuffisant. Limité à ${max}.`);
                    val = max;
                }

                cart[idx].quantity = val;
                updateCartUI();
            });
        });

        // Supprimer un élément du panier
        document.querySelectorAll('.btn-delete-item').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.dataset.index);
                cart.splice(idx, 1);
                updateCartUI();
            });
        });

        // Mettre à jour les totaux globaux
        cartItemCount.textContent = `${totalItems} article${totalItems > 1 ? 's' : ''}`;
        btnValider.disabled = false;
        updateTotals(grandTotal);
    }

    // Mettre à jour les calculs de prix et de monnaie
    function updateTotals(grandTotal) {
        // Afficher totaux dans le panier et l'aperçu facture
        const totalEl = document.getElementById('total');
        if (totalEl) totalEl.innerHTML = `<span class="fw-bold">${parseInt(grandTotal).toLocaleString()}</span> FCFA`;
        factureTotalHt.textContent = parseInt(grandTotal).toLocaleString();
        factureGrandTotal.textContent = parseInt(grandTotal).toLocaleString();

        // Calculer la monnaie à rendre
        const montantRecu = parseFloat(montantRecuInput.value) || 0;
        const reste = montantRecu - grandTotal;

        factureRecu.textContent = parseInt(montantRecu).toLocaleString();
        factureReste.textContent = parseInt(reste).toLocaleString();
        resteDiv.textContent = parseInt(reste).toLocaleString() + ' FCFA';

        // Changer style de couleur du reste
        if (reste < 0) {
            resteDiv.className = 'form-control form-control-lg text-end fw-bold bg-danger text-white';
            btnValider.disabled = true; // Empêcher validation si paiement insuffisant
        } else {
            resteDiv.className = 'form-control form-control-lg text-end fw-bold bg-success text-white';
            if (cart.length > 0) btnValider.disabled = false;
        }
    }

    // Écouteur de saisie sur le montant reçu
    montantRecuInput.addEventListener('input', function() {
        // Récupérer le total à payer actuel
        let total = 0;
        cart.forEach(item => {
            total += item.quantity * item.price;
        });
        updateTotals(total);
    });

    // Soumission du formulaire : tentative en ligne normale, bascule vers la
    // file d'attente hors-ligne uniquement en cas d'échec réseau réel (pas de
    // changement de comportement pour une soumission qui aboutit normalement).
    const venteForm = document.getElementById('venteForm');
    venteForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        if (cart.length === 0) {
            alert('Votre panier de vente est vide. Veuillez ajouter au moins un produit.');
            return;
        }

        const formData = new FormData(venteForm);

        try {
            const response = await fetch(venteForm.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
            });
            window.location.href = response.url;
        } catch (networkError) {
            if (!window.OfflineVentes) {
                alert('Connexion indisponible et la file d\'attente hors-ligne n\'a pas pu être chargée. Réessayez.');
                return;
            }

            const uuid = await window.OfflineVentes.enqueue({
                client_nom: document.getElementById('client_nom').value,
                client_telephone: document.getElementById('client_telephone').value,
                mode_paiement: document.getElementById('mode_paiement').value,
                montant_recu: parseFloat(montantRecuInput.value) || 0,
                lignes: cart.map(function (item) {
                    return {
                        produit_id: item.product.id,
                        quantite: item.quantity,
                        prix_unitaire: item.price,
                    };
                }),
                total: grandTotalCourant(),
            });

            afficherConfirmationHorsLigne(uuid);

            cart = [];
            updateCartUI();
            venteForm.reset();

            if (window.OfflineVentesUI) {
                window.OfflineVentesUI.rafraichirBadge();
            }
        }
    });

    function grandTotalCourant() {
        return cart.reduce((somme, item) => somme + item.quantity * item.price, 0);
    }

    function afficherConfirmationHorsLigne(uuid) {
        const conteneur = document.querySelector('.card-body.p-4');
        const alerte = document.createElement('div');
        alerte.className = 'alert alert-warning alert-dismissible fade show';
        alerte.innerHTML =
            '<i class="fas fa-cloud-arrow-up me-2"></i>' +
            '<strong>Pas de connexion.</strong> La vente a été enregistrée localement (réf. provisoire ' + uuid.slice(0, 8).toUpperCase() + ') ' +
            'et sera synchronisée automatiquement dès le retour du réseau.' +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        conteneur.prepend(alerte);
    }
});
</script>
@endsection
