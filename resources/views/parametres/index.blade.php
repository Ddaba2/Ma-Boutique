@extends('layouts.premium')
@section('title', 'Paramètres')
@section('subtitle', 'Utilisateurs, sauvegarde et restauration')

@section('content')
<div class="row mt-4">
    <div class="col-12">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $ongletActif === 'utilisateurs' ? 'active' : '' }}" id="onglet-utilisateurs-btn"
                        data-bs-toggle="tab" data-bs-target="#onglet-utilisateurs" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Utilisateurs
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $ongletActif === 'sauvegardes' ? 'active' : '' }}" id="onglet-sauvegardes-btn"
                        data-bs-toggle="tab" data-bs-target="#onglet-sauvegardes" type="button" role="tab">
                    <i class="fas fa-database me-2"></i>Sauvegarde et restauration
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $ongletActif === 'journal' ? 'active' : '' }}" id="onglet-journal-btn"
                        data-bs-toggle="tab" data-bs-target="#onglet-journal" type="button" role="tab">
                    <i class="fas fa-clipboard-list me-2"></i>Journal d'activité
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade {{ $ongletActif === 'utilisateurs' ? 'show active' : '' }}" id="onglet-utilisateurs" role="tabpanel">
                @include('parametres._utilisateurs')
            </div>
            <div class="tab-pane fade {{ $ongletActif === 'sauvegardes' ? 'show active' : '' }}" id="onglet-sauvegardes" role="tabpanel">
                @include('parametres._sauvegardes')
            </div>
            <div class="tab-pane fade {{ $ongletActif === 'journal' ? 'show active' : '' }}" id="onglet-journal" role="tabpanel">
                @include('parametres._journal')
            </div>
        </div>

    </div>
</div>
@endsection
