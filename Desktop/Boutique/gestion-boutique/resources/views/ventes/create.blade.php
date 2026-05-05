@extends('layouts.premium')

@section('title', 'Nouvelle Vente')
@section('subtitle', 'Enregistrer une nouvelle vente')

@section('content')
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card animate-fadeInUp">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Nouvelle Vente
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

                <form method="POST" action="{{ route('ventes.store') }}" id="venteForm">
                    @csrf
                    
                    <!-- Informations client -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-user me-2"></i>Informations client
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_nom" class="form-label fw-semibold">
                                            <i class="fas fa-user me-1"></i>Nom du client
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="client_nom" name="client_nom" 
                                               placeholder="Entrez le nom du client" 
                                               value="{{ old('client_nom') }}"
                                               required>
                                        @error('client_nom')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_telephone" class="form-label fw-semibold">
                                            <i class="fas fa-phone me-1"></i>Téléphone
                                        </label>
                                        <input type="text" class="form-control" id="client_telephone" name="client_telephone"
                                               value="{{ old('client_telephone') }}"
                                               placeholder="Téléphone du client">
                                        @error('client_telephone')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Produit vendu -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-success">
                                <i class="fas fa-box me-2"></i>Détails du produit
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="produit_nom" class="form-label fw-semibold">
                                            <i class="fas fa-tag me-1"></i>Nom du marchandise *
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="produit_nom" name="produit_nom" 
                                               placeholder="Entrez le nom du produit" 
                                               value="{{ old('produit_nom') }}"
                                               required>
                                        @error('produit_nom')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Commencez à taper pour rechercher un produit</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="quantite" class="form-label fw-semibold">
                                            <i class="fas fa-sort-numeric-up me-1"></i>Quantité *
                                        </label>
                                        <input type="number" class="form-control form-control-lg" id="quantite" name="quantite"
                                               value="{{ old('quantite', 1) }}"
                                               placeholder="1" min="1" required>
                                        @error('quantite')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="prix_unitaire" class="form-label fw-semibold">
                                            <i class="fas fa-money-bill-wave me-1"></i>Prix unitaire (FCFA)
                                        </label>
                                        <input type="number" class="form-control form-control-lg" id="prix_unitaire" name="prix_unitaire"
                                               value="{{ old('prix_unitaire') }}"
                                               placeholder="0" min="0" step="0.01" readonly>
                                        @error('prix_unitaire')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="stock_restant" class="form-label fw-semibold">
                                            <i class="fas fa-boxes me-1"></i>Stock restant
                                        </label>
                                        <div class="form-control form-control-lg bg-light" id="stock_restant">
                                            <span class="text-muted">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Paiement -->
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-warning">
                                <i class="fas fa-credit-card me-2"></i>Informations de paiement
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="mode_paiement" class="form-label fw-semibold">
                                            <i class="fas fa-credit-card me-1"></i>Mode de paiement *
                                        </label>
                                        <select class="form-control form-control-lg" id="mode_paiement" name="mode_paiement" required>
                                            <option value="">Sélectionner...</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="espece">Espèce</option>
                                            <option value="virement_bancaire">Virement Bancaire</option>
                                            <option value="cheque">Chèque</option>
                                            <option value="autres">Autres</option>
                                        </select>
                                        @error('mode_paiement')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="total" class="form-label fw-semibold">
                                            <i class="fas fa-calculator me-1"></i>Total à payer (FCFA)
                                        </label>
                                        <div class="form-control form-control-lg bg-primary text-white" id="total">
                                            <span class="fw-bold">0</span> FCFA
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="montant_recu" class="form-label fw-semibold">
                                            <i class="fas fa-hand-holding-usd me-1"></i>Montant reçu (FCFA) *
                                        </label>
                                        <input type="number" class="form-control form-control-lg" id="montant_recu" name="montant_recu"
                                               value="{{ old('montant_recu') }}"
                                               placeholder="0" min="0" step="0.01" required>
                                        @error('montant_recu')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="reste" class="form-label fw-semibold">
                                            <i class="fas fa-coins me-1"></i>Reste à rendre (FCFA)
                                        </label>
                                        <div class="form-control form-control-lg bg-success text-white" id="reste">
                                            <span class="fw-bold">0</span> FCFA
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="fas fa-redo me-2"></i>Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>Enregistrer la vente
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>

<style>
/* Styles pour la facture professionnelle */
.facture-container {
    font-family: 'Inter', system-ui, sans-serif;
    border: 1px solid #e5e7eb;
    border-radius: 0;
}

.facture-header {
    background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%) !important;
    position: relative;
}

.facture-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.facture-number {
    font-family: 'Courier New', monospace;
    font-size: 1.1rem;
    color: #1e40af;
}

.table thead th {
    border-bottom: 2px solid #1e40af;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
}

.signature-line {
    position: relative;
}

.signature-line::after {
    content: 'Signature';
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 0.7rem;
    color: #6b7280;
}

/* Styles d'impression professionnels */
@media print {
    .no-print {
        display: none !important;
    }
    
    .col-lg-8 {
        display: none !important;
    }
    
    .col-lg-4 {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
    
    .facture-container {
        border: 2px solid #000 !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
    }
    
    .facture-header {
        background: #000 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .facture-header::before {
        display: none !important;
    }
    
    .card-footer {
        display: none !important;
    }
    
    .btn {
        display: none !important;
    }
    
    .text-primary {
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .text-success {
        color: #000 !important;
        font-weight: bold !important;
    }
    
    .text-info {
        color: #000 !important;
        font-weight: bold !important;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .border-bottom {
        border-bottom: 1px solid #000 !important;
    }
    
    .table {
        font-size: 0.9rem;
    }
    
    body {
        background: white !important;
    }
    
    /* S'assurer que tout est en noir et blanc pour l'impression */
    * {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Animations et transitions */
.facture-container {
    transition: all 0.3s ease;
}

.facture-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .facture-header h3 {
        font-size: 1.5rem;
    }
    
    .table {
        font-size: 0.8rem;
    }
    
    .facture-number {
        font-size: 0.9rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const produitNomInput = document.getElementById('produit_nom');
    const quantiteInput = document.getElementById('quantite');
    const prixUnitaireInput = document.getElementById('prix_unitaire');
    const montantRecuInput = document.getElementById('montant_recu');
    const totalDiv = document.getElementById('total');
    const resteDiv = document.getElementById('reste');
    const stockRestantDiv = document.getElementById('stock_restant');
    
    // Éléments de la facture
    const factureClient = document.getElementById('facture_client');
    const factureTelephone = document.getElementById('facture_telephone');
    const factureProduit = document.getElementById('facture_produit');
    const factureQuantite = document.getElementById('facture_quantite');
    const facturePrix = document.getElementById('facture_prix');
    const factureTotal = document.getElementById('facture_total');
    const factureRecu = document.getElementById('facture_recu');
    const factureReste = document.getElementById('facture_reste');
    const factureNumero = document.getElementById('facture_numero');
    
    // Générer numéro de facture
    factureNumero.textContent = 'V' + new Date().getFullYear() + String(new Date().getMonth() + 1).padStart(2, '0') + String(new Date().getDate()).padStart(2, '0') + String(Math.floor(Math.random() * 1000)).padStart(3, '0');
    
    // Recherche de produit
    produitNomInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        if (searchTerm.length < 2) {
            resetProduitInfo();
            return;
        }
        
        // Recherche AJAX (simulée pour l'instant)
        fetch(`/api/produits/search?q=${searchTerm}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const produit = data[0];
                    prixUnitaireInput.value = produit.prix_vente || 0;
                    stockRestantDiv.innerHTML = `<span class="fw-bold ${produit.stock_actuel <= produit.stock_min ? 'text-warning' : 'text-success'}">${produit.stock_actuel}</span>`;
                    updateCalculs();
                    updateFacture();
                }
            })
            .catch(error => {
                console.log('Recherche locale (fallback)');
                // Fallback: recherche locale si l'API n'existe pas
                const produits = @json($produits ?? []);
                const produit = produits.find(p => p.nom.toLowerCase().includes(searchTerm));
                if (produit) {
                    prixUnitaireInput.value = produit.prix_vente || 0;
                    stockRestantDiv.innerHTML = `<span class="fw-bold ${produit.stock_actuel <= produit.stock_min ? 'text-warning' : 'text-success'}">${produit.stock_actuel}</span>`;
                    updateCalculs();
                    updateFacture();
                }
            });
    });
    
    // Mise à jour des calculs
    quantiteInput.addEventListener('input', updateCalculs);
    montantRecuInput.addEventListener('input', updateCalculs);
    
    // Mise à jour des informations client
    document.getElementById('client_nom').addEventListener('input', updateFacture);
    document.getElementById('client_telephone').addEventListener('input', updateFacture);
    
    function updateCalculs() {
        const quantite = parseFloat(quantiteInput.value) || 0;
        const prixUnitaire = parseFloat(prixUnitaireInput.value) || 0;
        const montantRecu = parseFloat(montantRecuInput.value) || 0;
        
        const total = quantite * prixUnitaire;
        const reste = montantRecu - total;
        
        totalDiv.innerHTML = `<span class="fw-bold">${total.toFixed(0)}</span> FCFA`;
        resteDiv.innerHTML = `<span class="fw-bold">${reste.toFixed(0)}</span> FCFA`;
        
        // Changer la couleur du reste
        if (reste < 0) {
            resteDiv.className = 'form-control form-control-lg bg-danger text-white';
        } else {
            resteDiv.className = 'form-control form-control-lg bg-success text-white';
        }
        
        updateFacture();
    }
    
    function updateFacture() {
        const clientNom = document.getElementById('client_nom').value;
        const clientTelephone = document.getElementById('client_telephone').value;
        const produitNom = produitNomInput.value;
        const quantite = quantiteInput.value;
        const prixUnitaire = prixUnitaireInput.value;
        const montantRecu = montantRecuInput.value;
        
        // Mettre à jour les informations client
        factureClient.textContent = clientNom || '-';
        factureTelephone.textContent = clientTelephone || '-';
        
        // Mettre à jour les informations produit
        factureProduit.textContent = produitNom || '-';
        factureQuantite.textContent = quantite || '-';
        facturePrix.textContent = prixUnitaire ? parseFloat(prixUnitaire).toFixed(0) : '-';
        
        // Calculer les totaux
        const total = (parseFloat(quantite) || 0) * (parseFloat(prixUnitaire) || 0);
        const reste = (parseFloat(montantRecu) || 0) - total;
        
        // Mettre à jour tous les éléments de total
        const totalFormatted = total.toFixed(0);
        const resteFormatted = reste.toFixed(0);
        
        factureTotal.textContent = totalFormatted;
        document.getElementById('facture_total_ht').textContent = totalFormatted;
        document.getElementById('facture_total_ttc').textContent = totalFormatted;
        document.getElementById('facture_grand_total').textContent = totalFormatted;
        
        // Mettre à jour les informations de paiement
        factureRecu.textContent = montantRecu || '0';
        document.getElementById('facture_reste').textContent = resteFormatted;
    }
    
    function resetProduitInfo() {
        prixUnitaireInput.value = '';
        stockRestantDiv.innerHTML = '<span class="text-muted">-</span>';
        updateCalculs();
        updateFacture();
    }
});
</script>
@endsection
