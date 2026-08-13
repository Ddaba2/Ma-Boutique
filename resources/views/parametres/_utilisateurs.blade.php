<div class="card animate-fadeInUp">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-user-cog me-2 text-primary"></i>Utilisateurs ({{ $utilisateurs->count() }})</h5>
        <a href="{{ route('utilisateurs.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Nouvel utilisateur
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Boutique</th><th>Statut</th><th>Créé le</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($utilisateurs as $u)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $u->name }}</div>
                                    @if($u->id === auth()->id())
                                        <span class="badge bg-success" style="font-size:0.65rem">Vous</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">{{ $u->email }}</td>
                        <td>
                            <span class="badge role-badge-{{ $u->role }}">
                                {{ match($u->role) { 'gerant' => 'Gérant', 'gestionnaire' => 'Gestionnaire', default => 'Caissier' } }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $u->boutique->nom ?? '— (toutes)' }}</td>
                        <td>
                            <span class="badge bg-{{ $u->active ? 'success' : 'secondary' }}">
                                {{ $u->active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('utilisateurs.edit', $u) }}" class="btn btn-outline-warning" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('utilisateurs.destroy', $u) }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" title="Désactiver" onclick="return confirm('Désactiver cet utilisateur ? Il ne pourra plus se connecter.')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
