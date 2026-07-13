@extends('layouts.premium')
@section('title', 'Choisir une boutique')
@section('subtitle', 'Sélectionnez la boutique sur laquelle vous souhaitez travailler')

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-7 col-lg-9">
        <div class="card animate-fadeInUp">
            <div class="card-header"><h5 class="mb-0 fw-bold"><i class="fas fa-store me-2 text-primary"></i>Choisir une boutique</h5></div>
            <div class="card-body p-4">
                <div class="list-group">
                    @foreach($boutiques as $boutique)
                        <form method="POST" action="{{ route('boutiques.switch') }}" class="mb-2">
                            @csrf
                            <input type="hidden" name="boutique_id" value="{{ $boutique->id }}">
                            <button type="submit" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-3">
                                <span><i class="fas fa-store me-2 text-primary"></i>{{ $boutique->nom }}</span>
                                <i class="fas fa-arrow-right text-muted"></i>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
