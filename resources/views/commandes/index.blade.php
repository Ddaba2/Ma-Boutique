@extends('layouts.premium')
@section('title', 'Bons de commande')
@section('subtitle', 'Gestion des achats fournisseurs')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body py-3">
                <form method="GET" class="d-flex gap-2">
                    <select name="statut" class="form-select" style="max-width:200px">
                        <option value="">Tous les statuts</option>
                        @foreach(['en_attente'=>'En attente','envoyee'=>'Envoyée','recue_partielle'=>'Reçue partiellement','recue'=>'Reçue','annulee'=>'Annulée'] as $val => $label)
                            <option value="{{ $val }}" {{ request('statut') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary px-4"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    @if(request('statut'))
                        <a href="{{ route('commandes.index') }}" class="btn btn-outline-secondary">Effacer</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2 text-primary"></i>Bons de commande</h5>
                <a href="{{ route('commandes.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Nouveau</a>
            </div>
            <div class="card-body p-0">
                @if($commandes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr><th>Référence</th><th>Fournisseur</th><th>Date</th><th>Livraison prévue</th><th>Total</th><th>Statut</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($commandes as $cmd)
                            <tr>
                                <td class="fw-bold">{{ $cmd->reference }}</td>
                                <td>{{ $cmd->fournisseur->nom }}</td>
                                <td class="small">{{ $cmd->created_at->format('d/m/Y') }}</td>
                                <td class="small">{{ $cmd->date_livraison_prevue?->format('d/m/Y') ?? '—' }}</td>
                                <td class="fw-bold">{{ number_format($cmd->total, 0, ',', ' ') }} F</td>
                                <td><span class="badge bg-{{ $cmd->statutBadge() }}">{{ $cmd->statutLabel() }}</span></td>
                                <td class="d-flex gap-1">
                                    <a href="{{ route('commandes.show', $cmd) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('commandes.pdf', $cmd) }}" class="btn btn-sm btn-outline-danger" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $commandes->withQueryString()->links() }}</div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-file-invoice fa-3x mb-3 d-block"></i>
                    <p>Aucun bon de commande.</p>
                    <a href="{{ route('commandes.create') }}" class="btn btn-primary">Créer le premier bon</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
