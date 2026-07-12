@extends('layouts.premium')
@section('title', 'Nouveau bon de commande')
@section('subtitle', 'Commander auprès d\'un fournisseur')

@section('content')
<div class="row mt-4">
    <div class="col-xl-8 col-lg-9">
        <div class="card animate-fadeInUp">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2 text-primary"></i>Nouveau bon de commande</h5></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('commandes.store') }}" id="commandeForm">
                    @csrf
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fournisseur *</label>
                            <select name="fournisseur_id" class="form-select @error('fournisseur_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($fournisseurs as $f)
                                    <option value="{{ $f->id }}">{{ $f->nom }}</option>
                                @endforeach
                            </select>
                            @error('fournisseur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date de livraison prévue</label>
                            <input type="date" name="date_livraison_prevue" class="form-control" value="{{ old('date_livraison_prevue') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-list me-2"></i>Produits commandés</h6>
                    <div id="lignes">
                        <div class="row g-2 mb-2 ligne-produit">
                            <div class="col-5">
                                <select name="produit_ids[]" class="form-select form-select-sm" required>
                                    <option value="">-- Produit --</option>
                                    @foreach($produits as $p)
                                        <option value="{{ $p->id }}" data-prix="{{ $p->prix_achat }}">{{ $p->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <input type="number" name="quantites[]" class="form-control form-control-sm quantite" placeholder="Qté" min="1" required>
                            </div>
                            <div class="col-3">
                                <input type="number" name="prix_unitaires[]" class="form-control form-control-sm prix" placeholder="Prix unit." step="0.01" min="0" required>
                            </div>
                            <div class="col-2">
                                <span class="badge bg-light text-dark p-2 total-ligne w-100 text-center">0 F</span>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="ajouterLigne()">
                        <i class="fas fa-plus me-1"></i>Ajouter un produit
                    </button>

                    <div class="card bg-light mt-4 border-0">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total commande :</span>
                                <span id="totalGlobal" class="text-primary">0 FCFA</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Créer le bon</button>
                        <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
const produitsData = @json($produits->pluck('prix_achat', 'id'));

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.ligne-produit').forEach(ligne => {
        const qty   = parseFloat(ligne.querySelector('.quantite').value) || 0;
        const prix  = parseFloat(ligne.querySelector('.prix').value) || 0;
        const sousTotal = qty * prix;
        ligne.querySelector('.total-ligne').textContent = sousTotal.toLocaleString('fr-FR') + ' F';
        total += sousTotal;
    });
    document.getElementById('totalGlobal').textContent = total.toLocaleString('fr-FR') + ' FCFA';
}

function ajouterLigne() {
    const template = document.querySelector('.ligne-produit').cloneNode(true);
    template.querySelector('select').value = '';
    template.querySelector('.quantite').value = '';
    template.querySelector('.prix').value = '';
    template.querySelector('.total-ligne').textContent = '0 F';
    template.querySelector('select').insertAdjacentHTML('afterend',
        '<button type="button" class="btn btn-sm btn-outline-danger ms-1" onclick="this.closest(\'.ligne-produit\').remove(); calcTotal()"><i class="fas fa-trash"></i></button>');
    document.getElementById('lignes').appendChild(template);
    bindLigne(template);
}

function bindLigne(ligne) {
    ligne.querySelectorAll('input, select').forEach(el => el.addEventListener('input', () => {
        const select = ligne.querySelector('select[name="produit_ids[]"]');
        if (select.value && ligne.querySelector('.prix').value === '') {
            ligne.querySelector('.prix').value = produitsData[select.value] || '';
        }
        calcTotal();
    }));
}

document.querySelectorAll('.ligne-produit').forEach(bindLigne);
</script>
@endsection
@endsection
