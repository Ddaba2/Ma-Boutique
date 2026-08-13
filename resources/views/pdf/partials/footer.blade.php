@php
    $nomEntreprise = ($branding ?? \App\Support\PdfBranding::resolve())['nom'];
@endphp
<div class="footer">
    <div>Document confidentiel — usage interne.</div>
    <div>Généré le {{ now()->format('d/m/Y à H:i') }} — {{ $nomEntreprise }}</div>
</div>
