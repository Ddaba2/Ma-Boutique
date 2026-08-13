@extends('layouts.premium')
@section('title', 'Clôture de caisse')
@section('subtitle', 'Clôturer la caisse du ' . \Carbon\Carbon::parse($today)->format('d/m/Y'))

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-8">
        <!-- Résumé du jour -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card success p-3 text-center">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Espèces</div>
                    <div class="fw-bold fs-5">{{ number_format($totalEspeces, 0, ',', ' ') }} F</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-3 text-center">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Carte</div>
                    <div class="fw-bold fs-5">{{ number_format($totalCarte, 0, ',', ' ') }} F</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info p-3 text-center">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Mobile</div>
                    <div class="fw-bold fs-5">{{ number_format($totalMobile, 0, ',', ' ') }} F</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning p-3 text-center">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Autre</div>
                    <div class="fw-bold fs-5">{{ number_format($totalAutre, 0, ',', ' ') }} F</div>
                </div>
            </div>
        </div>

        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-cash-register me-2 text-primary"></i>
                    Clôture du {{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}
                    — {{ $nbVentes }} vente(s) — Total: {{ number_format($totalVentes, 0, ',', ' ') }} FCFA
                </h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('caisse.store') }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $today }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fond de caisse à l'ouverture (FCFA) *</label>
                            <input type="number" name="fond_ouverture" class="form-control @error('fond_ouverture') is-invalid @enderror"
                                   value="{{ old('fond_ouverture', 0) }}" min="0" step="50" required id="fondOuverture">
                            @error('fond_ouverture')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Espèces réellement comptées en caisse (FCFA) *</label>
                            <input type="number" name="montant_compte" class="form-control @error('montant_compte') is-invalid @enderror"
                                   value="{{ old('montant_compte') }}" min="0" step="50" required id="montantCompte"
                                   placeholder="Comptez les billets/pièces du tiroir">
                            @error('montant_compte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Ne comptez que les espèces physiques — pas la carte ni le mobile money.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Écart calculé</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-balance-scale"></i></span>
                                <input type="text" class="form-control fw-bold" id="ecartDisplay" readonly placeholder="Renseignez le fond et le montant compté">
                            </div>
                            <small class="text-muted">= espèces comptées − (fond d'ouverture + ventes en espèces)</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes / observations</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Ex: billetterie vérifiée, manque 500F sur les espèces...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="card bg-dark text-white mt-4">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col">
                                    <div class="small opacity-75">Total ventes</div>
                                    <div class="fw-bold">{{ number_format($totalVentes, 0, ',', ' ') }} F</div>
                                </div>
                                <div class="col">
                                    <div class="small opacity-75">Nb ventes</div>
                                    <div class="fw-bold">{{ $nbVentes }}</div>
                                </div>
                                <div class="col">
                                    <div class="small opacity-75">Encaissé total (tous modes)</div>
                                    <div class="fw-bold">{{ number_format($totalEspeces + $totalCarte + $totalMobile + $totalAutre, 0, ',', ' ') }} F</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-lock me-2"></i>Clôturer la caisse
                        </button>
                        <a href="{{ route('caisse.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const totalEspeces = {{ $totalEspeces }};

function recalculerEcart() {
    const fond    = parseFloat(document.getElementById('fondOuverture').value) || 0;
    const compte  = document.getElementById('montantCompte').value;
    const el      = document.getElementById('ecartDisplay');

    if (compte === '') {
        el.value = '';
        el.className = 'form-control fw-bold';
        return;
    }

    const especesTheoriques = fond + totalEspeces;
    const ecart = parseFloat(compte) - especesTheoriques;
    el.value    = (ecart >= 0 ? '+' : '') + ecart.toLocaleString('fr-FR') + ' FCFA';
    el.className = 'form-control fw-bold ' + (ecart >= 0 ? 'text-success' : 'text-danger');
}

document.getElementById('fondOuverture').addEventListener('input', recalculerEcart);
document.getElementById('montantCompte').addEventListener('input', recalculerEcart);
</script>
@endsection
