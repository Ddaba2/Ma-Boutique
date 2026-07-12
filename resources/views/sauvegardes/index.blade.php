@extends('layouts.premium')
@section('title', 'Sauvegardes')
@section('subtitle', 'Gestion des sauvegardes de la base de données')

@section('content')
<div class="row mt-4">
    <div class="col-xl-4 mb-4">
        <div class="card animate-slideInLeft">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-database me-2 text-primary"></i>Créer une sauvegarde</h5></div>
            <div class="card-body p-4">
                <p class="text-muted small">
                    Lance une sauvegarde complète de la base de données MySQL via mysqldump (XAMPP).
                    Les 7 dernières sauvegardes sont conservées automatiquement.
                </p>
                <form method="POST" action="{{ route('sauvegardes.store') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-download me-2"></i>Sauvegarder maintenant
                    </button>
                </form>

                <hr>
                <div class="small text-muted">
                    <i class="fas fa-clock me-1"></i>
                    <strong>Sauvegarde automatique :</strong> chaque nuit à 02h00<br>
                    <span class="text-warning">⚠ Nécessite que le planificateur Laravel soit actif (Task Scheduler Windows).</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 mb-4">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-history me-2 text-info"></i>
                    Sauvegardes disponibles ({{ count($backups) }})
                </h5>
            </div>
            <div class="card-body p-0">
                @if(count($backups) > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr><th>Fichier</th><th>Date</th><th>Taille</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $b)
                            <tr>
                                <td>
                                    <i class="fas fa-file-alt text-success me-2"></i>
                                    <span class="fw-semibold small">{{ $b['nom'] }}</span>
                                </td>
                                <td class="small">{{ $b['date'] }}</td>
                                <td><span class="badge bg-light text-dark">{{ $b['taille'] }}</span></td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('sauvegardes.download', ['fichier' => $b['nom']]) }}" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form method="POST" action="{{ route('sauvegardes.destroy') }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="fichier" value="{{ $b['nom'] }}">
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette sauvegarde ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-database fa-3x mb-3 d-block"></i>
                    <p>Aucune sauvegarde trouvée.</p>
                    <p class="small">Créez votre première sauvegarde avec le bouton à gauche.</p>
                </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body py-3">
                <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2 text-primary"></i>Activer la sauvegarde automatique</h6>
                <p class="small text-muted mb-2">Pour que les sauvegardes se lancent automatiquement chaque nuit, configurez Windows Task Scheduler :</p>
                <div class="bg-dark text-white rounded p-3 small font-monospace">
                    php {{ base_path() }}\artisan schedule:run
                </div>
                <p class="small text-muted mt-2 mb-0">À exécuter toutes les minutes via le Planificateur de tâches Windows.</p>
            </div>
        </div>
    </div>
</div>
@endsection
