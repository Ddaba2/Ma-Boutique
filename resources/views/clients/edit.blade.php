@extends('layouts.premium')
@section('title', 'Modifier client')
@section('subtitle', $client->nom_complet)

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-7 col-lg-9">
        <div class="card animate-fadeInUp">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-warning"></i>Modifier : {{ $client->nom_complet }}</h5></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('clients.update', $client) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom *</label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $client->nom) }}" required>
                            @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="{{ old('prenom', $client->prenom) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $client->telephone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $client->adresse) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Mettre à jour</button>
                        <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
