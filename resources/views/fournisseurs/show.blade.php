@extends('layouts.premium')
@section('title', $fournisseur->nom)
@section('subtitle', 'Fiche fournisseur')

@section('content')
<div class="row mt-4">
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                        <i class="fas fa-truck fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold">{{ $fournisseur->nom }}</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Contact</span><strong>{{ $fournisseur->contact ?? '—' }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Téléphone</span><strong>{{ $fournisseur->telephone ?? '—' }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Email</span><strong>{{ $fournisseur->email ?? '—' }}</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Délai livraison</span><strong>{{ $fournisseur->delai_livraison }} jours</strong></li>
                    <li class="list-group-item d-flex justify-content-between px-0"><span class="text-muted">Total commandes</span><strong>{{ number_format($totalAchats, 0, ',', ' ') }} FCFA</strong></li>
                </ul>
                <div class="d-grid gap-2 mt-4">
                    <a href="{{ route('commandes.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-2"></i>Nouvelle commande</a>
                    <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit me-2"></i>Modifier</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2 text-info"></i>Commandes ({{ $nbCommandes }})</h5>
            </div>
            <div class="card-body p-0">
                @if($commandes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead><tr><th>Référence</th><th>Date</th><th>Total</th><th>Statut</th><th>Actions</th></tr></thead>
                        <tbody>
                            @foreach($commandes as $cmd)
                            <tr>
                                <td class="fw-semibold">{{ $cmd->reference }}</td>
                                <td class="small">{{ $cmd->created_at->format('d/m/Y') }}</td>
                                <td class="fw-bold">{{ number_format($cmd->total, 0, ',', ' ') }} F</td>
                                <td><span class="badge bg-{{ $cmd->statutBadge() }}">{{ $cmd->statutLabel() }}</span></td>
                                <td><a href="{{ route('commandes.show', $cmd) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $commandes->links() }}</div>
                @else
                <div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucune commande</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
