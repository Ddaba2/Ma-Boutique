@extends('layouts.premium')
@section('title', 'Codes-barres — Tous les produits')
@section('subtitle', 'Générer les étiquettes de tous les produits')

@section('content')
<div class="row mt-4">
    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fas fa-barcode me-2 text-primary"></i>{{ $produits->count() }} produits</span>
                <button class="btn btn-primary" onclick="imprimerTout()">
                    <i class="fas fa-print me-2"></i>Imprimer toutes les étiquettes
                </button>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card animate-fadeInUp">
            <div class="card-body p-4">
                <div class="row g-3" id="etiquettesGrid">
                    @foreach($produits as $p)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="border rounded-3 p-3 text-center bg-white etiquette" data-ref="{{ $p->reference }}" data-nom="{{ $p->nom }}" data-prix="{{ $p->prix_vente }}">
                            <div class="fw-semibold small mb-1">{{ $p->nom }}</div>
                            <svg class="barcode-svg" data-ref="{{ $p->reference }}"></svg>
                            <div class="text-muted" style="font-size:0.7rem">{{ $p->reference }}</div>
                            <div class="fw-bold text-primary small">{{ number_format($p->prix_vente, 0, ',', ' ') }} FCFA</div>
                            <div class="mt-2">
                                <a href="{{ route('produits.barcode', $p) }}" class="btn btn-xs btn-outline-primary btn-sm">
                                    <i class="fas fa-print me-1"></i>Imprimer
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@vite(['resources/js/barcode.js'])
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.barcode-svg').forEach(svg => {
            JsBarcode(svg, svg.dataset.ref, {
                format: "CODE128",
                width: 1.5,
                height: 40,
                displayValue: true,
                fontSize: 10,
                margin: 5
            });
        });
    });

    // La fenêtre de "tout imprimer" ci-dessous ouvre un document totalement
    // séparé (window.open) qui n'hérite pas des scripts déjà chargés dans
    // cette page : elle a donc besoin de sa propre copie de JsBarcode. Reste
    // en CDN (n'affecte que ce popup d'impression groupée, pas l'affichage
    // normal de cette page qui fonctionne désormais hors-ligne).
    function imprimerTout() {
        const etiquettes = document.querySelectorAll('.etiquette');
        let html = '';
        etiquettes.forEach(el => {
            html += `<div style="display:inline-block;width:200px;margin:5px;padding:10px;border:1px solid #ccc;text-align:center;font-family:Arial;page-break-inside:avoid">
                ${el.innerHTML.replace(/<div class="mt-2">[\s\S]*?<\/div>/, '')}
            </div>`;
        });

        const win = window.open('', '_blank');
        win.document.write(`<!DOCTYPE html><html><head><title>Toutes les étiquettes</title>
            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"><\/script>
            <style>body{margin:10px}@media print{button{display:none}}</style></head>
            <body>
                <button onclick="window.print()" style="margin-bottom:10px;padding:8px 16px">Imprimer</button>
                <div>${html}</div>
                <script>document.querySelectorAll('[data-ref]').forEach(svg=>{JsBarcode(svg,svg.dataset.ref,{format:"CODE128",width:1.5,height:40,displayValue:true,fontSize:10,margin:5})})<\/script>
            </body></html>`);
        win.document.close();
    }
</script>
@endsection
