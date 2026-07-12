@extends('layouts.premium')
@section('title', 'Modifier fournisseur')
@section('subtitle', $fournisseur->nom)

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-7 col-lg-9">
        <div class="card animate-fadeInUp">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-warning"></i>Modifier : {{ $fournisseur->nom }}</h5></div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('fournisseurs.update', $fournisseur) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom *</label>
                            <input type="text" name="nom" class="form-control" value="{{ old('nom', $fournisseur->nom) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact</label>
                            <input type="text" name="contact" class="form-control" value="{{ old('contact', $fournisseur->contact) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="{{ old('telephone', $fournisseur->telephone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $fournisseur->email) }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Adresse</label>
                            <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $fournisseur->adresse) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Délai (jours)</label>
                            <input type="number" name="delai_livraison" class="form-control" value="{{ old('delai_livraison', $fournisseur->delai_livraison) }}" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $fournisseur->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Mettre à jour</button>
                        <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
