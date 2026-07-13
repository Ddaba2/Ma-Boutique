@extends('layouts.premium')
@section('title', 'Modifier la boutique')
@section('subtitle', $boutique->nom)

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-7 col-lg-9">
        <div class="card animate-fadeInUp">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-store me-2 text-primary"></i>Modifier : {{ $boutique->nom }}</h5></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('boutiques.update', $boutique) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nom *</label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $boutique->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $boutique->adresse) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $boutique->telephone) }}">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" {{ old('active', $boutique->active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="active">Boutique active</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
                        <a href="{{ route('boutiques.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
