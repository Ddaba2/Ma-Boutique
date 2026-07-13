@extends('layouts.premium')
@section('title', 'Modifier utilisateur')
@section('subtitle', $utilisateur->name)

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-6 col-lg-8">
        <div class="card animate-fadeInUp">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-user-edit me-2 text-warning"></i>Modifier : {{ $utilisateur->name }}</h5></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('utilisateurs.update', $utilisateur) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nom complet *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $utilisateur->name) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $utilisateur->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rôle *</label>
                            <select name="role" class="form-select" required>
                                <option value="caissier" {{ old('role', $utilisateur->role) === 'caissier' ? 'selected' : '' }}>Caissier</option>
                                <option value="gestionnaire" {{ old('role', $utilisateur->role) === 'gestionnaire' ? 'selected' : '' }}>Gestionnaire</option>
                                <option value="gerant" {{ old('role', $utilisateur->role) === 'gerant' ? 'selected' : '' }}>Gérant</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="active" class="form-select">
                                <option value="1" {{ old('active', $utilisateur->active) ? 'selected' : '' }}>Actif</option>
                                <option value="0" {{ !old('active', $utilisateur->active) ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                        <div class="col-12" id="champ-boutique">
                            <label class="form-label fw-semibold">Boutique *</label>
                            <select name="boutique_id" class="form-select">
                                @foreach($boutiques as $boutique)
                                    <option value="{{ $boutique->id }}" {{ (int) old('boutique_id', $utilisateur->boutique_id) === $boutique->id ? 'selected' : '' }}>{{ $boutique->nom }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Non applicable pour un gérant, qui supervise toutes les boutiques.</small>
                        </div>
                        <div class="col-12"><hr><p class="text-muted small">Laissez vide pour ne pas changer le mot de passe</p></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nouveau mot de passe</label>
                            <input type="password" name="password" class="form-control" minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmer</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Mettre à jour</button>
                        <a href="{{ route('utilisateurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.querySelector('select[name="role"]');
    const champBoutique = document.getElementById('champ-boutique');

    function toggleBoutique() {
        champBoutique.style.display = roleSelect.value === 'gerant' ? 'none' : '';
    }

    roleSelect.addEventListener('change', toggleBoutique);
    toggleBoutique();
});
</script>
@endsection
