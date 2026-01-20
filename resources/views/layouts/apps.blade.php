<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard BTP')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- CSS personnalisé --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --accent-color: #0dcaf0;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --dark-color: #1a1d29;
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
            --topbar-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Layout Principal */
        .main-container {
            display: flex;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1001;
            height: var(--topbar-height);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .page-title-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .page-title i {
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .notification-badge {
            position: relative;
            cursor: pointer;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .notification-badge:hover {
            background: var(--primary-color);
            color: white;
        }

        .notification-badge .badge {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            border: 2px solid white;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .user-dropdown:hover {
            background: #f8f9fa;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            border: 3px solid white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--dark-color);
        }

        .user-role {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark-color) 0%, #13151f 100%);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            height: calc(100vh - var(--topbar-height));
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }

        .brand-logo i {
            font-size: 1.8rem;
            color: var(--warning-color);
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }

        .sidebar.collapsed .brand-logo span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .menu-toggle {
            position: absolute;
            right: -15px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            border: 2px solid white;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .menu-toggle:hover {
            background: var(--accent-color);
            transform: translateY(-50%) scale(1.1);
        }

        .sidebar-menu {
            padding: 1.5rem 0;
        }

        .menu-section {
            padding: 0.5rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: 1rem;
        }

        .sidebar.collapsed .menu-section {
            opacity: 0;
            height: 0;
            padding: 0;
        }

        .nav-item {
            position: relative;
            margin: 0.25rem 0.75rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.9rem 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary-color);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link:hover::before {
            transform: scaleY(1);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }

        .nav-link.active::before {
            transform: scaleY(1);
        }

        .nav-link i {
            width: 24px;
            text-align: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .nav-text {
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger-color);
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .sidebar.collapsed .nav-badge {
            position: absolute;
            top: 5px;
            right: 5px;
        }

        /* Contenu principal */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8f9fa;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed);
        }

        /* Alerts personnalisées */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .alert i {
            font-size: 1.5rem;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        /* Footer */
        footer {
            margin-top: auto;
            background: linear-gradient(135deg, var(--dark-color) 0%, #13151f 100%);
            color: rgba(255,255,255,0.8);
            padding: 2rem;
            border-top: 3px solid var(--primary-color);
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        /* Dropdown personnalisé */
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            border-radius: 10px;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dropdown-item:hover {
            background: var(--primary-color);
            color: white;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }

        /* Mobile Toggle */
        .mobile-toggle {
            width: 45px;
            height: 45px;
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            color: white;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mobile-toggle:hover {
            background: var(--accent-color);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .mobile-toggle {
                display: flex;
            }

            .sidebar {
                margin-left: -100%;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }

            .main-content.expanded {
                margin-left: 0;
            }
            
            .top-bar {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.2rem;
            }

            .user-details {
                display: none;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-links {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        /* Overlay pour mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            width: 100%;
            height: calc(100vh - var(--topbar-height));
            background: rgba(0,0,0,0.5);
            z-index: 999;
            backdrop-filter: blur(4px);
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            animation: slideIn 0.3s ease;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="page-title-section">
            <button class="mobile-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h1 class="page-title">
                @yield('page-title', 'Tableau de bord')
            </h1>
        </div>
        
        <div class="user-info">
            <div class="notification-badge" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-5"></i>
                <span class="badge bg-danger">3</span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-envelope me-2"></i> 2 nouveaux messages</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-exclamation-triangle me-2"></i> 1 alerte budget</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center" href="#"><strong>Voir tout</strong></a></li>
            </ul>

            <div class="dropdown">
                <a href="#" class="user-dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ Auth::user()->name ?? 'Utilisateur' }}</div>
                        <div class="user-role">{{ Auth::user()->role ?? 'Admin' }}</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle me-2"></i> Mon profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Paramètres</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle me-2"></i> Aide</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logoute') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main-container">
        <!-- Overlay pour mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="brand-logo">
                    <i class="bi bi-building"></i>
                    <span>BTP Manager</span>
                </a>
                <button class="menu-toggle d-none d-lg-flex" id="sidebarCollapse">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>

            <nav class="sidebar-menu">
                <div class="menu-section">Navigation</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                           href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('rapport.depenses.liste') ? 'active' : '' }}" 
                           href="{{ route('rapport.depenses.liste') }}">
                            <i class="bi bi-wallet2"></i>
                            <span class="nav-text">Dépenses</span>
                            
                        </a>
                    </li>

                    <div class="menu-section">Gestion</div>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span class="nav-text">Rapports</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-people"></i>
                            <span class="nav-text">Équipe</span>
                        </a>
                    </li>

                    <div class="menu-section">Système</div>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-gear"></i>
                            <span class="nav-text">Paramètres</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-question-circle"></i>
                            <span class="nav-text">Support</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content" id="mainContent">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div>
                    <strong>© {{ date('Y') }} CFSIS Globale Services</strong> - Tous droits réservés
                    <br>
                    <small class="text-muted">Version 1.0.0</small>
                </div>
                <div class="footer-links">
                    <a href="#">Conditions d'utilisation</a>
                    <a href="#">Confidentialité</a>
                    <a href="#">Support</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts personnalisés --}}
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const mainContent = document.getElementById('mainContent');
            
            // Toggle sidebar sur mobile
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
            }
            
            // Fermer sidebar en cliquant sur l'overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }
            
            // Toggle sidebar collapsed sur desktop
            if (sidebarCollapse) {
                sidebarCollapse.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    const icon = this.querySelector('i');
                    if (sidebar.classList.contains('collapsed')) {
                        icon.classList.remove('bi-chevron-left');
                        icon.classList.add('bi-chevron-right');
                    } else {
                        icon.classList.remove('bi-chevron-right');
                        icon.classList.add('bi-chevron-left');
                    }
                    
                    // Sauvegarder l'état
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
                
                // Restaurer l'état au chargement
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    sidebarCollapse.querySelector('i').classList.replace('bi-chevron-left', 'bi-chevron-right');
                }
            }

            // Auto-dismiss alerts après 5 secondes
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>