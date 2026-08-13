@extends('layouts.premium')
@section('title', 'Import produits')
@section('subtitle', 'Importer des produits en masse via CSV')

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-7 col-lg-9">
        <div class="card animate-fadeInUp mb-4">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-csv me-2 text-success"></i>Import CSV de produits</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info mb-4">
                    <h6 class="fw-bold"><i class="fas fa-info-circle me-2"></i>Format attendu</h6>
                    <p class="mb-1">Fichier CSV avec <strong>point-virgule (;)</strong> comme séparateur.</p>
                    <p class="mb-0">Colonnes : <code>nom ; description ; prix_achat ; prix_vente ; stock_actuel ; stock_min ; stock_max ; categorie</code></p>
                </div>

                <form method="POST" action="{{ route('produits.import.csv') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Fichier CSV *</label>
                        <input type="file" name="fichier_csv" class="form-control @error('fichier_csv') is-invalid @enderror" accept=".csv,.txt" required>
                        @error('fichier_csv')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Taille maximum : 2 Mo</div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>Importer
                        </button>
                        <a href="{{ route('produits.import.modele') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-2"></i>Télécharger le modèle CSV
                        </a>
                        <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="fas fa-question-circle me-2 text-warning"></i>Instructions</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Téléchargez le modèle CSV ci-dessus</li>
                    <li class="mb-2">Ouvrez-le avec Excel ou LibreOffice Calc</li>
                    <li class="mb-2">Remplissez vos produits (une ligne par produit)</li>
                    <li class="mb-2">Sauvegardez en format <strong>CSV délimité par point-virgule</strong></li>
                    <li class="mb-2">Uploadez le fichier ici</li>
                </ol>
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Si la catégorie n'est pas trouvée, la première catégorie disponible sera utilisée.
                    Les références sont générées automatiquement.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
