@extends('layouts.premium')
@section('title', $client->nom_complet)
@section('subtitle', 'Fiche client & historique d\'achat')

@section('content')
<div class="row mt-4">
    <div class="col-xl-4 mb-4">
        <div class="card animate-slideInLeft h-100">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $client->nom_complet }}</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="fas fa-phone me-2"></i>Téléphone</span>
                        <strong>{{ $client->telephone ?? '—' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="fas fa-envelope me-2"></i>Email</span>
                        <strong>{{ $client->email ?? '—' }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Adresse</span>
                        <strong>{{ $client->adresse ?? '—' }}</strong>
                    </li>
                </ul>
                <div class="row g-3 mt-3">
                    <div class="col-4 text-center">
                        <div class="fw-bold text-primary fs-5">{{ $nbVentes }}</div>
                        <div class="small text-muted">Achats</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-bold text-success" style="font-size:0.9rem">{{ number_format($totalAchats, 0, ',', ' ') }}</div>
                        <div class="small text-muted">Total FCFA</div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-bold text-warning" style="font-size:0.9rem">{{ number_format($panierMoyen, 0, ',', ' ') }}</div>
                        <div class="small text-muted">Moy./achat</div>
                    </div>
                </div>
                <div class="mt-4 d-grid gap-2">
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    <a href="{{ route('ventes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-shopping-cart me-2"></i>Nouvelle vente
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 mb-4">
        <div class="card animate-fadeInUp">
            <div class="card-header">
                <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2 text-info"></i>Historique des achats</h5>
            </div>
            <div class="card-body p-0">
                @if($ventes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr><th>Date</th><th>Référence</th><th>Total</th><th>Paiement</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            @foreach($ventes as $vente)
                            <tr>
                                <td class="small">{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-light text-dark">{{ $vente->reference }}</span></td>
                                <td class="fw-bold">{{ number_format($vente->total, 0, ',', ' ') }} F</td>
                                <td>
                                    <span class="badge bg-{{ match($vente->mode_paiement) { 'espece' => 'success', 'carte' => 'primary', 'mobile' => 'info', default => 'secondary' } }}">
                                        {{ $vente->modePaiementLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('ventes.show', $vente) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $ventes->links() }}</div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucun achat enregistré
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
