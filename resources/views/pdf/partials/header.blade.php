@php
    $b = $branding ?? \App\Support\PdfBranding::resolve();
@endphp
<table class="top-band" width="100%">
    <tr>
        <td width="100" style="padding-right: 14px;">
            @if(!empty($logoPath))
                <div class="logo-box">
                    <img src="{{ $logoPath }}" alt="Logo">
                </div>
            @else
                <div class="logo-placeholder">LOGO</div>
            @endif
        </td>
        <td>
            <div class="company-name">{{ $b['nom'] }}</div>
            @if($b['nif'])
                <div class="company-line">NIF : {{ $b['nif'] }}</div>
            @endif
            @if($b['adresse'])
                <div class="company-line">{{ $b['adresse'] }}</div>
            @endif
            @if($b['telephone'])
                <div class="company-line">Tél : {{ $b['telephone'] }}</div>
            @endif
            @if($b['email'])
                <div class="company-line">Email : {{ $b['email'] }}</div>
            @endif
            @if($b['site_web'])
                <div class="company-line">{{ $b['site_web'] }}</div>
            @endif
        </td>
        <td width="200" class="doc-badge">
            <div class="title">{{ $docTitle }}</div>
            @if(!empty($docSubtitle))
                <div class="ref">{{ $docSubtitle }}</div>
            @endif
        </td>
    </tr>
</table>
