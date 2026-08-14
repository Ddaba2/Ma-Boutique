<div class="card animate-fadeInUp mb-4">
    <div class="card-body">
        <h5 class="mb-1 fw-bold"><i class="fas fa-clipboard-list me-2 text-primary"></i>Journal d'activité</h5>
        <p class="text-muted mb-0 small">
            Trace des actions sensibles : changement de prix, annulation de vente, ajustement de stock manuel.
            Les mouvements de stock détaillés (entrées, sorties) restent visibles dans l'historique de stock.
        </p>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-body p-0">
        @if($journal->count() > 0)
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Détail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journal as $entree)
                    <tr>
                        <td class="text-nowrap">{{ $entree->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $entree->user->name ?? 'Utilisateur supprimé' }}</td>
                        <td><span class="badge bg-{{ $entree->badgeAction() }}">{{ $entree->libelleAction() }}</span></td>
                        <td class="small">{{ $entree->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $journal->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-clipboard-list fa-3x mb-3 d-block"></i>
            <p>Aucune activité enregistrée pour le moment.</p>
        </div>
        @endif
    </div>
</div>
