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

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS personnalisé --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #e2e8f0;
            --text: #334155;
            --text-light: #94a3b8;
            --border: #e2e8f0;

            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            --topbar-height: 64px;
            --radius: 8px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Layout */
        .main-container {
            display: flex;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* Top Bar */
        .top-bar {
            background-color: white;
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            height: var(--topbar-height);
        }

        .page-title-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .notification-badge {
            position: relative;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light);
            border-radius: 50%;
            color: var(--secondary);
            transition: background 0.2s;
        }

        .notification-badge:hover {
            background: var(--gray);
        }

        .notification-badge .badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            background: var(--danger);
            color: white;
            border: 2px solid white;
        }

        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.25rem 0.75rem 0.25rem 0.25rem;
            border-radius: 40px;
            transition: background 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            border: 1px solid var(--border);
        }

        .user-dropdown:hover {
            background: var(--light);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
            line-height: 1.3;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--dark);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background-color: white;
            border-right: 1px solid var(--border);
            transition: width 0.2s;
            position: fixed;
            height: calc(100vh - var(--topbar-height));
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 999;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid var(--border);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--dark);
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            white-space: nowrap;
        }

        .brand-logo i {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .sidebar.collapsed .brand-logo span {
            display: none;
        }

        .menu-toggle {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--secondary);
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .menu-toggle:hover {
            background: var(--light);
            border-color: var(--primary);
            color: var(--primary);
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-section {
            padding: 0.5rem 1rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--text-light);
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .sidebar.collapsed .menu-section {
            display: none;
        }

        .nav-item {
            margin: 0.25rem 0.5rem;
        }

        .nav-link {
            color: var(--text);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            transition: background 0.2s;
            border-radius: var(--radius);
        }

        .nav-link:hover {
            background: var(--light);
        }

        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1.2rem;
            color: var(--secondary);
        }

        .nav-link.active i {
            color: white;
        }

        .nav-text {
            white-space: nowrap;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .sidebar.collapsed .nav-text {
            display: none;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            border-radius: 12px;
        }

        .sidebar.collapsed .nav-badge {
            display: none;
        }

        /* Contenu principal */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: margin-left 0.2s;
            background-color: var(--light);
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed);
        }

        /* Alertes */
        .alert {
            border: 1px solid transparent;
            border-radius: var(--radius);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            background-color: white;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        /* Footer */
        footer {
            background-color: white;
            border-top: 1px solid var(--border);
            padding: 1rem 2rem;
            margin-top: auto;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .footer-links {
            display: flex;
            gap: 2rem;
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        /* Dropdown */
        .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 0.5rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: var(--light);
        }

        .dropdown-divider {
            border-top-color: var(--border);
        }

        /* Mobile Toggle */
        .mobile-toggle {
            width: 40px;
            height: 40px;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--secondary);
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .mobile-toggle:hover {
            background: var(--light);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .mobile-toggle {
                display: flex;
            }

            .sidebar {
                margin-left: -100%;
                box-shadow: none;
            }
            
            .sidebar.show {
                margin-left: 0;
                box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }

            .main-content.expanded {
                margin-left: 0;
            }
            
            .top-bar {
                padding: 0 1rem;
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

        /* Overlay mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            width: 100%;
            height: calc(100vh - var(--topbar-height));
            background: rgba(0, 0, 0, 0.3);
            z-index: 998;
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="page-title-section">
            <button class="mobile-toggle" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h1 class="page-title">
                @yield('page-title', 'Tableau de bord')
            </h1>
        </div>
        
        <div class="user-info">
            <div class="notification-badge" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span class="badge">3</span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-envelope me-2"></i> 2 nouveaux messages</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-exclamation-triangle me-2"></i> 1 alerte budget</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center" href="#">Voir tout</a></li>
            </ul>

            <div class="dropdown">
                <a href="#" class="user-dropdown dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <span class="user-name">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                        <span class="user-role">{{ Auth::user()->role ?? 'Admin' }}</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle me-2"></i> Profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Paramètres</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle me-2"></i> Aide</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logoute') }}">
                            @csrf
                            <button type="submit" class="dropdown-item w-100">
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
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('entreprise') ? 'active' : '' }}" 
                           href="{{ route('entreprise') }}">
                            <i class="bi bi-wallet2"></i>
                            <span class="nav-text">Entreprises</span>
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
        <div class="footer-content">
            <div>
                © {{ date('Y') }} CFSIS Globale Services - Tous droits réservés
            </div>
            <div class="footer-links">
                <a href="#">Conditions</a>
                <a href="#">Confidentialité</a>
                <a href="#">Support</a>
            </div>
        </div>
    </footer>

    {{-- Scripts Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
                    
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
                
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    const icon = sidebarCollapse.querySelector('i');
                    if (icon) {
                        icon.classList.remove('bi-chevron-left');
                        icon.classList.add('bi-chevron-right');
                    }
                }
            }

            // Auto-dismiss alerts après 5 secondes
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = bootstrap.Alert.getInstance(alert);
                    if (bsAlert) bsAlert.close();
                }, 5000);
            });
        });
    </script>

    @stack('scripts')
    @yield('scripts')
</body>
</html>