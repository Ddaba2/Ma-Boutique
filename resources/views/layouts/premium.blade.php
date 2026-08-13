<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GesBoutique') - Gestion de Boutique Professionnelle</title>

    <!-- PWA -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#2563eb">
    <link rel="icon" href="{{ asset('icons/icon.svg') }}" type="image/svg+xml">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GesBoutique">

    @vite(['resources/css/vendor.css', 'resources/js/vendor.js'])

    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #64748b;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-dark: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            --gradient-success: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
            --shadow-2xl: 0 25px 50px rgba(0,0,0,0.25);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            line-height: 1.6;
            overflow: hidden;
        }

        .app-wrapper {
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background: #0f172a;
            box-shadow: 4px 0 24px rgba(15, 23, 42, 0.35);
            overflow-x: hidden;
            overflow-y: auto;
            z-index: 1030;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.25rem 1rem 1rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            text-align: center;
        }
        .sidebar-logo-frame {
            width: 88px;
            height: 88px;
            margin: 0 auto 0.85rem;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }
        .sidebar-logo-frame img {
            max-width: 78px;
            max-height: 78px;
            object-fit: contain;
        }
        .sidebar-logo-placeholder {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #94a3b8;
            text-transform: uppercase;
        }
        .sidebar-brand-name {
            color: #f8fafc;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -0.01em;
            margin: 0;
            line-height: 1.2;
        }
        .sidebar-brand-meta {
            color: #94a3b8;
            font-size: 0.75rem;
            margin-top: 0.35rem;
        }
        .sidebar-nav {
            flex: 1;
            padding: 0.75rem 0 1rem;
        }
        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: 10px;
            margin: 3px 10px;
            transition: background 0.2s ease, color 0.2s ease;
            position: relative;
            font-weight: 500;
            padding: 10px 14px;
            font-size: 0.875rem;
        }
        .sidebar .nav-link:hover {
            background: rgba(148, 163, 184, 0.12);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }
        .sidebar .nav-link i {
            width: 1.25rem;
            text-align: center;
        }
        .sidebar-section-title {
            color: #64748b;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            padding: 12px 24px 4px;
        }
        .sidebar-footer {
            padding: 0.75rem 1rem 1.25rem;
            border-top: 1px solid rgba(148, 163, 184, 0.15);
        }

        /* Stat Cards */
        .stat-card {
            background: white; border-radius: 16px; box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255,255,255,0.8); position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: var(--gradient-primary);
        }
        .stat-card:hover { transform: translateY(-8px) scale(1.02); box-shadow: var(--shadow-2xl); }
        .stat-card.success::before { background: var(--gradient-success); }
        .stat-card.warning::before { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.info::before    { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .stat-card.danger::before  { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

        /* Header */
        .main-header {
            background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 1rem 0; border-bottom: 1px solid rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 100;
        }
        .main-header h2 { color: var(--dark-color); }
        .main-header .btn-link { color: var(--dark-color); text-decoration: none; }
        .main-header .btn-link:hover { color: var(--primary-color); }

        /* Zone principale scrollable */
        .main-content {
            margin-left: 240px;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Buttons */
        .btn-primary {
            background: var(--gradient-primary); border: none; border-radius: 12px;
            padding: 10px 20px; font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); color: white; }
        .btn-success {
            background: var(--gradient-success); border: none; border-radius: 12px;
            padding: 10px 20px; font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); color: white;
        }
        .btn-success:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); color: white; }

        /* Tables */
        .table-modern { background: white; border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-md); }
        .table-modern thead { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
        .table-modern th {
            border: none; padding: 14px 16px; font-weight: 600;
            color: var(--secondary-color); text-transform: uppercase;
            font-size: 0.72rem; letter-spacing: 0.05em;
        }
        .table-modern td { padding: 14px 16px; border: none; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .table-modern tbody tr:hover { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }

        /* Forms */
        .form-control, .form-select {
            border: 2px solid #e2e8f0; border-radius: 12px; padding: 10px 14px;
            font-weight: 500; transition: all 0.3s ease; background: white;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Badges */
        .badge { padding: 5px 10px; border-radius: 20px; font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.05em; }

        /* Cards */
        .card { border: none; border-radius: 16px; box-shadow: var(--shadow-md); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; }
        .card:hover { transform: translateY(-4px); box-shadow: var(--shadow-xl); }
        .card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            color: #1f2937;
        }
        .card-header.bg-primary,
        .card-header.bg-success,
        .card-header.bg-info,
        .card-header.bg-warning,
        .card-header.bg-danger,
        .card-header.bg-dark {
            background: var(--primary-color) !important;
            color: #fff !important;
            border-bottom: none;
        }
        .card-header.bg-primary { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important; }
        .card-header.bg-success { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%) !important; }
        .card-header.bg-info { background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%) !important; }
        .card-header.bg-warning { background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important; color: #fff !important; }
        .card-header.bg-danger { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important; }
        .card-header.bg-dark { background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important; }
        .card-header.bg-primary .card-title,
        .card-header.bg-success .card-title,
        .card-header.bg-info .card-title,
        .card-header.bg-warning .card-title,
        .card-header.bg-danger .card-title,
        .card-header.bg-dark .card-title,
        .card-header.bg-primary h5,
        .card-header.bg-success h5,
        .card-header.text-white {
            color: #fff !important;
        }

        /* Alerts */
        .alert { border: none; border-radius: 12px; padding: 14px 18px; font-weight: 500; box-shadow: var(--shadow-sm); }
        .alert-success { background: linear-gradient(135deg, #f0fdf4, #dcfce7); color: #166534; border-left: 4px solid #22c55e; }
        .alert-danger  { background: linear-gradient(135deg, #fef2f2, #fee2e2); color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-warning { background: linear-gradient(135deg, #fffbeb, #fef3c7); color: #92400e; border-left: 4px solid #f59e0b; }
        .alert-info    { background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #1e40af; border-left: 4px solid #3b82f6; }

        /* Brand (legacy text mark) */
        .brand-logo {
            font-weight: 700; font-size: 1.3rem;
            color: #f8fafc;
        }

        /* Notification Bell */
        .notif-bell { position: relative; cursor: pointer; }
        .notif-badge {
            position: absolute; top: -6px; right: -6px;
            background: #ef4444; color: white; border-radius: 50%;
            width: 18px; height: 18px; font-size: 0.65rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            animation: pulse-ring 2s infinite;
        }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(239,68,68,0.7); }
            70%  { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
            100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
        }
        .notif-dropdown {
            width: 340px; max-height: 400px; overflow-y: auto;
            border: none; border-radius: 16px; box-shadow: var(--shadow-2xl);
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .animate-fadeInUp  { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
        .animate-slideInLeft { animation: slideInLeft 0.6s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Role badges */
        .role-badge-gerant      { background: linear-gradient(135deg,#7c3aed,#5b21b6); color:white; }
        .role-badge-gestionnaire{ background: linear-gradient(135deg,#2563eb,#1d4ed8); color:white; }
        .role-badge-caissier    { background: linear-gradient(135deg,#0891b2,#0e7490); color:white; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 280px;
                left: -280px;
                transition: left 0.3s ease;
            }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: var(--primary-color); border-radius: 4px; }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar collapse d-md-block" id="sidebarMenu">
            @php
                $branding = \App\Support\PdfBranding::resolve();
                $logoUrl = \App\Support\PdfBranding::logoUrl();
                $role = auth()->user()->role ?? 'caissier';
                $boutiqueActuelle = \App\Support\BoutiqueContext::boutique();
            @endphp
            <div class="sidebar-brand">
                <div class="sidebar-logo-frame" title="Remplacez public/logo-client.png par le logo client">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo {{ $branding['nom'] }}">
                    @else
                        <span class="sidebar-logo-placeholder">Logo</span>
                    @endif
                </div>
                <p class="sidebar-brand-name">{{ $branding['nom'] }}</p>
                <div class="sidebar-brand-meta">{{ auth()->user()->name ?? '' }}</div>
                <span class="badge role-badge-{{ $role }} mt-2">
                    {{ match($role) { 'gerant' => 'Gérant', 'gestionnaire' => 'Gestionnaire', default => 'Caissier' } }}
                </span>
                @if($boutiqueActuelle)
                    <div class="sidebar-brand-meta mt-2">
                        <i class="fas fa-store me-1"></i>{{ $boutiqueActuelle->nom }}
                    </div>
                @endif
            </div>

            <div class="sidebar-nav">
                    <ul class="nav flex-column">
                        {{-- PRINCIPAL --}}
                        <div class="sidebar-section-title">Principal</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i><span>Tableau de bord</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ventes.*') ? 'active' : '' }}" href="{{ route('ventes.index') }}">
                                <i class="fas fa-shopping-cart me-2"></i><span>Ventes</span>
                            </a>
                        </li>

                        {{-- INVENTAIRE --}}
                        <div class="sidebar-section-title mt-2">Inventaire</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}" href="{{ route('stocks.index') }}">
                                <i class="fas fa-boxes me-2"></i><span>Stock</span>
                            </a>
                        </li>

                        {{-- ANALYSES --}}
                        <div class="sidebar-section-title mt-2">Analyses</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('rapports.*') ? 'active' : '' }}" href="{{ route('rapports.index') }}">
                                <i class="fas fa-chart-bar me-2"></i><span>Rapports</span>
                            </a>
                        </li>
                        @if(auth()->user()->role === 'gerant')
                        {{-- SYSTÈME --}}
                        <div class="sidebar-section-title mt-2">Système</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('caisse.*') ? 'active' : '' }}" href="{{ route('caisse.index') }}">
                                <i class="fas fa-cash-register me-2"></i><span>Clôture de caisse</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('parametres.*', 'utilisateurs.*') ? 'active' : '' }}" href="{{ route('parametres.index') }}">
                                <i class="fas fa-cog me-2"></i><span>Paramètres</span>
                            </a>
                        </li>
                        @endif
                    </ul>
            </div>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main content -->
        <main class="px-md-4 main-content">
                <!-- Header -->
                <div class="main-header">
                    <div class="d-flex justify-content-between align-items-center px-2">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-link d-md-none me-3" type="button" onclick="toggleSidebar()">
                                <i class="fas fa-bars fa-lg"></i>
                            </button>
                            <div>
                                <h2 class="mb-0 fw-bold fs-4 text-dark">@yield('title', 'Tableau de bord')</h2>
                                <p class="text-muted mb-0 small">@yield('subtitle', 'Gestion de boutique professionnelle')</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            {{-- Ventes hors-ligne en attente de synchronisation --}}
                            <button type="button" class="btn btn-light btn-sm rounded-circle p-2 position-relative notif-bell" title="Ventes en attente de synchronisation" onclick="window.OfflineVentesUI && window.OfflineVentesUI.synchroniser()">
                                <i class="fas fa-cloud-arrow-up"></i>
                                <span class="notif-badge d-none" id="offline-ventes-badge">0</span>
                            </button>

                            {{-- Notification cloche --}}
                            <div class="dropdown notif-bell" id="notifBell">
                                <button class="btn btn-light btn-sm rounded-circle p-2" data-bs-toggle="dropdown" aria-expanded="false" onclick="chargerAlertes()">
                                    <i class="fas fa-bell"></i>
                                    <span class="notif-badge d-none" id="notifCount">0</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end notif-dropdown p-0" id="notifDropdown">
                                    <div class="p-3 border-bottom bg-warning bg-opacity-10">
                                        <h6 class="mb-0 fw-bold text-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Alertes Stock
                                        </h6>
                                    </div>
                                    <div id="notifContent" class="p-3 text-center text-muted">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                        <span class="ms-2">Chargement...</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Bouton contextuel --}}
                            @if(request()->routeIs('produits.*'))
                                <a href="{{ route('produits.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Nouveau produit
                                </a>
                            @elseif(request()->routeIs('categories.*'))
                                <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Nouvelle catégorie
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Flash Messages -->
                @foreach(['success' => 'check-circle', 'error' => 'exclamation-circle', 'warning' => 'exclamation-triangle', 'info' => 'info-circle'] as $type => $icon)
                    @if(session($type))
                        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show mt-3 animate-fadeInUp" role="alert">
                            <i class="fas fa-{{ $icon }} me-2"></i>{{ session($type) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endforeach

                <!-- Page Content -->
                @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/escape-html.js') }}"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebarMenu').classList.toggle('show');
        }

        // Notification bell : charger les alertes stock bas
        async function chargerAlertes() {
            const content = document.getElementById('notifContent');
            content.innerHTML = '<div class="text-center text-muted"><div class="spinner-border spinner-border-sm"></div> Chargement...</div>';

            try {
                const resp = await fetch('{{ route('api.stock.alertes') }}');
                const produits = await resp.json();
                const badge = document.getElementById('notifCount');

                if (produits.length === 0) {
                    badge.classList.add('d-none');
                    content.innerHTML = '<div class="text-center text-success py-2"><i class="fas fa-check-circle fa-2x mb-2"></i><p class="mb-0">Tous les stocks sont OK</p></div>';
                    return;
                }

                badge.textContent = produits.length;
                badge.classList.remove('d-none');

                let html = '';
                produits.forEach(p => {
                    const isRupture = p.stock_actuel === 0;
                    html += `
                        <div class="d-flex align-items-center p-2 border-bottom">
                            <div class="me-2">
                                <span class="badge bg-${isRupture ? 'danger' : 'warning'}">
                                    <i class="fas fa-${isRupture ? 'times' : 'exclamation'}-triangle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">${escapeHtml(p.nom)}</div>
                                <div class="text-muted" style="font-size:0.75rem">${escapeHtml(p.reference)} · Stock: ${p.stock_actuel} / min: ${p.stock_min}</div>
                            </div>
                        </div>`;
                });
                content.innerHTML = html;
            } catch(e) {
                content.innerHTML = '<div class="text-danger text-center small p-2">Erreur de chargement</div>';
            }
        }

        // Vérifier les alertes au chargement
        document.addEventListener('DOMContentLoaded', () => {
            fetch('{{ route('api.stock.alertes') }}')
                .then(r => r.json())
                .then(data => {
                    if (data.length > 0) {
                        const badge = document.getElementById('notifCount');
                        badge.textContent = data.length;
                        badge.classList.remove('d-none');
                    }
                }).catch(() => {});
        });
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(() => {});
            });
        }
    </script>
    <script src="{{ asset('js/offline-ventes.js') }}"></script>

    @yield('scripts')
</body>
</html>
