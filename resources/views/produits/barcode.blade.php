@extends('layouts.premium')
@section('title', 'Code-barres — ' . $produit->nom)
@section('subtitle', 'Générer et imprimer le code-barres')

@section('content')
<div class="row mt-4 justify-content-center">
    <div class="col-xl-6 col-lg-8">
        <div class="card animate-fadeInUp">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fas fa-barcode me-2 text-primary"></i>Code-barres produit</h5>
                <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
            <div class="card-body p-4 text-center">
                <div class="bg-white border rounded-3 p-4 mb-4 d-inline-block" id="labelZone">
                    <div class="fw-bold fs-5 mb-2">{{ $produit->nom }}</div>
                    <svg id="barcode"></svg>
                    <div class="text-muted small mt-1">{{ $produit->reference }}</div>
                    <div class="fw-bold text-primary">{{ number_format($produit->prix_vente, 0, ',', ' ') }} FCFA</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre d'étiquettes à imprimer</label>
                    <input type="number" id="nbEtiquettes" class="form-control text-center" value="1" min="1" max="50" style="max-width:120px; margin:auto">
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-primary" onclick="imprimer()">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                    <a href="{{ route('produits.barcode.all') }}" class="btn btn-outline-primary">
                        <i class="fas fa-th me-2"></i>Tous les produits
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    JsBarcode("#barcode", "{{ $produit->reference }}", {
        format: "CODE128",
        width: 2,
        height: 60,
        displayValue: true,
        fontSize: 12,
        margin: 10
    });

    function imprimer() {
        const nb   = parseInt(document.getElementById('nbEtiquettes').value) || 1;
        const zone = document.getElementById('labelZone').outerHTML;
        let html   = '';
        for (let i = 0; i < nb; i++) {
            html += `<div style="display:inline-block;margin:5px;padding:10px;border:1px solid #ccc;text-align:center;font-family:Arial;page-break-inside:avoid">${zone}</div>`;
        }

        const win = window.open('', '_blank');
        win.document.write(`<!DOCTYPE html><html><head><title>Étiquettes</title>
            <style>
                body { margin: 10px; }
                @media print { button { display: none; } }
            </style></head>
            <body>
                <button onclick="window.print()" style="margin-bottom:10px;padding:8px 16px">Imprimer</button>
                <div>${html}</div>
            </body></html>`);
        win.document.close();
        win.focus();
    }
</script>
@endsection
