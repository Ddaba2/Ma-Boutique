<div class="card animate-fadeInUp mb-4">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div>
            <h5 class="mb-1 fw-bold"><i class="fas fa-database me-2 text-primary"></i>Sauvegarde et restauration</h5>
            <p class="text-muted mb-0 small">
                Chaque sauvegarde est un fichier .sql complet, importable directement depuis phpMyAdmin en cas de besoin.
            </p>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('sauvegardes.store') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Sauvegarder maintenant
                </button>
            </form>

            @if(count($sauvegardes) > 0)
            <form method="POST" action="{{ route('sauvegardes.restaurer') }}" class="d-flex gap-2"
                  onsubmit="return confirm('Ceci va REMPLACER toutes les données actuelles par celles de la sauvegarde du {{ $sauvegardes[0]['date']->format('d/m/Y à H:i') }}.\n\nL\'état actuel sera sauvegardé automatiquement avant, mais confirmez-vous vouloir continuer ?');">
                @csrf
                <input type="password" name="password" class="form-control form-control-sm" placeholder="Votre mot de passe" required style="width: 180px;">
                <button type="submit" class="btn btn-outline-danger text-nowrap">
                    <i class="fas fa-undo me-2"></i>Restaurer la dernière sauvegarde
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-body p-0">
        @if(count($sauvegardes) > 0)
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Fichier</th>
                        <th>Date</th>
                        <th>Taille</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sauvegardes as $s)
                    <tr>
                        <td class="small text-muted">{{ $s['nom'] }}</td>
                        <td class="fw-semibold">{{ $s['date']->format('d/m/Y H:i:s') }}</td>
                        <td>{{ number_format($s['taille_ko'], 1, ',', ' ') }} Ko</td>
                        <td>
                            @if(str_starts_with($s['nom'], 'avant_restauration_'))
                                <span class="badge bg-secondary">Sécurité (avant restauration)</span>
                            @else
                                <span class="badge bg-primary">Manuelle</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('sauvegardes.telecharger', $s['nom']) }}" class="btn btn-outline-primary" title="Télécharger">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form method="POST" action="{{ route('sauvegardes.restaurer') }}" class="d-flex gap-1"
                                      onsubmit="return confirm('Ceci va REMPLACER toutes les données actuelles par celles de la sauvegarde {{ $s['nom'] }} ({{ $s['date']->format('d/m/Y à H:i') }}).\n\nL\'état actuel sera sauvegardé automatiquement avant, mais confirmez-vous vouloir continuer ?');">
                                    @csrf
                                    <input type="hidden" name="fichier" value="{{ $s['nom'] }}">
                                    <input type="password" name="password" class="form-control form-control-sm" placeholder="Mot de passe" required style="width: 130px;">
                                    <button type="submit" class="btn btn-outline-danger" title="Restaurer cette sauvegarde">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-database fa-3x mb-3 d-block"></i>
            <p>Aucune sauvegarde pour le moment.</p>
            <p class="small">Cliquez sur « Sauvegarder maintenant » pour créer la première.</p>
        </div>
        @endif
    </div>
</div>
