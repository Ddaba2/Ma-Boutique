@extends('layouts.premium')
@section('title', 'Nouvel utilisateur')
@section('subtitle', 'Créer un compte utilisateur')

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-6 col-lg-8">
        <div class="card animate-fadeInUp">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-user-plus me-2 text-primary"></i>Nouvel utilisateur</h5></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('utilisateurs.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nom complet *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email *</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Rôle *</label>
                            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="caissier" {{ old('role') === 'caissier' ? 'selected' : '' }}>Caissier (ventes uniquement)</option>
                                <option value="gestionnaire" {{ old('role') === 'gestionnaire' ? 'selected' : '' }}>Gestionnaire (stocks + fournisseurs)</option>
                                <option value="gerant" {{ old('role') === 'gerant' ? 'selected' : '' }}>Gérant (accès total)</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mot de passe *</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="6">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmer *</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="card bg-light mt-4 border-0">
                        <div class="card-body py-3">
                            <h6 class="fw-bold mb-2">Droits par rôle</h6>
                            <ul class="list-unstyled small mb-0">
                                <li><span class="badge role-badge-caissier me-2">Caissier</span>Ventes, clients, consultation stock</li>
                                <li class="mt-1"><span class="badge role-badge-gestionnaire me-2">Gestionnaire</span>+ Stocks, fournisseurs, commandes</li>
                                <li class="mt-1"><span class="badge role-badge-gerant me-2">Gérant</span>Accès complet + utilisateurs + sauvegardes</li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Créer</button>
                        <a href="{{ route('utilisateurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
