<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ChantierPro')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
    /* ─────────────────────────────────────────
       TOKENS
    ───────────────────────────────────────── */
    :root {
        --orange:       #E8520A;
        --orange-dim:   rgba(232,82,10,.1);
        --navy:         #0B2545;
        --navy-mid:     #163A6B;
        --sand:         #F6F4F0;
        --white:        #FFFFFF;
        --ink:          #1A1A2E;
        --ink-2:        #374151;
        --muted:        #6B7280;
        --border:       #E5E7EB;
        --border-dark:  rgba(255,255,255,.10);
        --sidebar-w:    240px;
        --sidebar-w-sm: 64px;
        --topbar-h:     56px;
        --radius:       10px;
        --radius-sm:    6px;
        --shadow-sm:    0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md:    0 4px 16px rgba(0,0,0,.08);
        --transition:   0.18s ease;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Inter', system-ui, sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: var(--ink);
        background: var(--sand);
        min-height: 100dvh;
        -webkit-font-smoothing: antialiased;
    }

    /* ─────────────────────────────────────────
       TOPBAR
    ───────────────────────────────────────── */
    .topbar {
        position: fixed;
        top: 0; left: 0; right: 0;
        height: var(--topbar-h);
        background: var(--navy);
        display: flex;
        align-items: center;
        padding: 0 16px;
        gap: 12px;
        z-index: 200;
        border-bottom: 1px solid var(--border-dark);
    }

    /* brand */
    .topbar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        flex-shrink: 0;
        width: var(--sidebar-w);
        padding-right: 16px;
    }
    .brand-icon {
        width: 30px; height: 30px;
        background: var(--orange);
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; color: #fff;
        flex-shrink: 0;
    }
    .brand-name {
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        letter-spacing: -.02em;
        white-space: nowrap;
        overflow: hidden;
        transition: opacity var(--transition), max-width var(--transition);
        max-width: 160px;
    }

    /* mobile toggle */
    .topbar-hamburger {
        display: none;
        align-items: center; justify-content: center;
        width: 36px; height: 36px;
        background: rgba(255,255,255,.08);
        border: 1px solid var(--border-dark);
        border-radius: var(--radius-sm);
        color: #fff; font-size: 18px;
        cursor: pointer;
        flex-shrink: 0;
    }

    /* page title in topbar */
    .topbar-title {
        flex: 1;
        font-size: 15px;
        font-weight: 600;
        color: rgba(255,255,255,.75);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: none; /* shown only on mobile */
    }

    /* right section */
    .topbar-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* notification */
    .notif-btn {
        position: relative;
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.08);
        border: 1px solid var(--border-dark);
        border-radius: 50%;
        color: rgba(255,255,255,.8);
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        transition: background var(--transition);
    }
    .notif-btn:hover { background: rgba(255,255,255,.15); color: #fff; }
    .notif-dot {
        position: absolute; top: 5px; right: 5px;
        width: 8px; height: 8px;
        background: var(--orange);
        border-radius: 50%;
        border: 1.5px solid var(--navy);
    }

    /* user pill */
    .user-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 5px 10px 5px 5px;
        background: rgba(255,255,255,.08);
        border: 1px solid var(--border-dark);
        border-radius: 20px;
        text-decoration: none;
        cursor: pointer;
        transition: background var(--transition);
    }
    .user-pill:hover { background: rgba(255,255,255,.14); }
    .user-avatar {
        width: 26px; height: 26px;
        border-radius: 50%;
        background: var(--orange);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        letter-spacing: 0;
    }
    .user-meta { display: flex; flex-direction: column; }
    .user-name  { font-size: 12px; font-weight: 600; color: #fff; line-height: 1.2; }
    .user-role  { font-size: 10px; color: rgba(255,255,255,.5); line-height: 1.2; }

    /* ─────────────────────────────────────────
       SIDEBAR
    ───────────────────────────────────────── */
    .sidebar {
        position: fixed;
        top: var(--topbar-h); left: 0;
        width: var(--sidebar-w);
        height: calc(100dvh - var(--topbar-h));
        background: var(--white);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        z-index: 190;
        transition: width var(--transition), transform var(--transition);
    }

    /* collapsed desktop */
    .sidebar.is-slim {
        width: var(--sidebar-w-sm);
    }
    .sidebar.is-slim .nav-label,
    .sidebar.is-slim .menu-section,
    .sidebar.is-slim .sidebar-footer-text {
        opacity: 0;
        pointer-events: none;
        width: 0;
        overflow: hidden;
    }
    .sidebar.is-slim .nav-link {
        justify-content: center;
        padding: 10px;
    }
    .sidebar.is-slim .nav-link .nav-badge { display: none; }
    .sidebar.is-slim .nav-link i { margin: 0; }

    /* slim toggle button */
    .slim-toggle {
        position: absolute;
        right: -11px; top: 20px;
        width: 22px; height: 22px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; color: var(--muted);
        cursor: pointer;
        z-index: 10;
        box-shadow: var(--shadow-sm);
        transition: color var(--transition);
    }
    .slim-toggle:hover { color: var(--navy); }

    /* nav items */
    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 12px 0;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }

    .menu-section {
        padding: 14px 16px 4px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        white-space: nowrap;
        transition: opacity var(--transition), width var(--transition);
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 12px;
        margin: 1px 8px;
        border-radius: var(--radius-sm);
        color: var(--ink-2);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        white-space: nowrap;
        transition: background var(--transition), color var(--transition);
        position: relative;
    }
    .nav-link:hover {
        background: var(--sand);
        color: var(--navy);
    }
    .nav-link.active {
        background: var(--orange-dim);
        color: var(--orange);
    }
    .nav-link.active i { color: var(--orange); }
    .nav-link i {
        font-size: 16px;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
        color: var(--muted);
        transition: color var(--transition);
    }
    .nav-link:hover i { color: var(--navy); }

    .nav-label {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: opacity var(--transition), width var(--transition);
    }

    .nav-badge {
        margin-left: auto;
        background: var(--orange);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 10px;
        flex-shrink: 0;
    }

    /* sidebar footer */
    .sidebar-footer {
        padding: 12px;
        border-top: 1px solid var(--border);
        flex-shrink: 0;
    }
    .sidebar-footer-inner {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px;
        border-radius: var(--radius-sm);
        color: var(--muted);
        font-size: 12px;
        cursor: pointer;
        text-decoration: none;
        transition: background var(--transition);
    }
    .sidebar-footer-inner:hover {
        background: var(--sand);
        color: var(--orange);
    }
    .sidebar-footer-inner i { font-size: 16px; flex-shrink: 0; }
    .sidebar-footer-text { white-space: nowrap; overflow: hidden; transition: opacity var(--transition), width var(--transition); }

    /* overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        top: var(--topbar-h);
        background: rgba(0,0,0,.35);
        z-index: 185;
        backdrop-filter: blur(2px);
    }
    .sidebar-overlay.active { display: block; }

    /* ─────────────────────────────────────────
       MAIN CONTENT
    ───────────────────────────────────────── */
    .app-body {
        padding-top: var(--topbar-h);
        margin-left: var(--sidebar-w);
        min-height: 100dvh;
        display: flex;
        flex-direction: column;
        transition: margin-left var(--transition);
    }
    .app-body.is-slim { margin-left: var(--sidebar-w-sm); }

    .content-area {
        flex: 1;
        padding: 24px;
    }

    /* page header (inside content, shown on desktop) */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .page-header-left h1 {
        font-size: 20px;
        font-weight: 700;
        color: var(--navy);
        letter-spacing: -.02em;
        line-height: 1.2;
        margin-bottom: 4px;
    }
    .page-header-left p {
        font-size: 13px;
        color: var(--muted);
    }
    .page-header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* breadcrumb */
    .breadcrumb-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .breadcrumb-wrap a { color: var(--muted); text-decoration: none; }
    .breadcrumb-wrap a:hover { color: var(--orange); }
    .breadcrumb-wrap i { font-size: 10px; }

    /* alerts */
    .cp-alert {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 14px;
        border-radius: var(--radius-sm);
        margin-bottom: 16px;
        font-size: 13px;
        border: 1px solid transparent;
    }
    .cp-alert i { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
    .cp-alert-success { background: #ECFDF5; border-color: #A7F3D0; color: #065F46; }
    .cp-alert-danger  { background: #FEF2F2; border-color: #FECACA; color: #991B1B; }
    .cp-alert-warning { background: #FFFBEB; border-color: #FDE68A; color: #92400E; }
    .cp-alert-close {
        margin-left: auto; background: none; border: none;
        font-size: 16px; color: inherit; opacity: .5;
        cursor: pointer; padding: 0; line-height: 1;
        flex-shrink: 0;
    }
    .cp-alert-close:hover { opacity: 1; }

    /* buttons */
    .btn-cp {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        font-size: 13px; font-weight: 600;
        cursor: pointer; border: none;
        text-decoration: none;
        transition: background var(--transition), box-shadow var(--transition);
        white-space: nowrap;
    }
    .btn-cp-primary { background: var(--orange); color: #fff; }
    .btn-cp-primary:hover { background: #d0480a; color: #fff; }
    .btn-cp-outline {
        background: transparent; color: var(--ink-2);
        border: 1.5px solid var(--border);
    }
    .btn-cp-outline:hover { border-color: var(--navy); color: var(--navy); }

    /* cards */
    .cp-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .cp-card-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .cp-card-title {
        font-size: 14px; font-weight: 600; color: var(--navy);
        display: flex; align-items: center; gap: 8px;
    }
    .cp-card-title i { color: var(--orange); }
    .cp-card-body { padding: 18px; }

    /* stat cards */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 18px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        box-shadow: var(--shadow-sm);
    }
    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stat-icon-orange { background: var(--orange-dim); color: var(--orange); }
    .stat-icon-navy   { background: rgba(11,37,69,.08); color: var(--navy); }
    .stat-icon-green  { background: #ECFDF5; color: #059669; }
    .stat-icon-red    { background: #FEF2F2; color: #DC2626; }
    .stat-body { flex: 1; min-width: 0; }
    .stat-value {
        font-size: 22px; font-weight: 700; color: var(--navy);
        line-height: 1.1; letter-spacing: -.03em;
    }
    .stat-label { font-size: 12px; color: var(--muted); margin-top: 2px; }
    .stat-trend {
        font-size: 11px; font-weight: 600; margin-top: 4px;
        display: flex; align-items: center; gap: 3px;
    }
    .stat-trend-up   { color: #059669; }
    .stat-trend-down { color: #DC2626; }

    /* footer */
    .app-footer {
        background: var(--white);
        border-top: 1px solid var(--border);
        padding: 14px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        font-size: 12px;
        color: var(--muted);
    }
    .app-footer a {
        color: var(--muted); text-decoration: none;
        transition: color var(--transition);
    }
    .app-footer a:hover { color: var(--orange); }
    .footer-links { display: flex; gap: 16px; }

    /* ─────────────────────────────────────────
       RESPONSIVE — TABLET ≤992px
    ───────────────────────────────────────── */
    @media (max-width: 992px) {
        .topbar-brand { width: auto; padding-right: 0; }
        .brand-name { display: none; }
        .topbar-hamburger { display: flex; }

        .sidebar {
            transform: translateX(-100%);
            width: var(--sidebar-w);
            box-shadow: var(--shadow-md);
        }
        .sidebar.mobile-open {
            transform: translateX(0);
        }
        .sidebar.is-slim { width: var(--sidebar-w); } /* no slim on mobile */
        .slim-toggle { display: none; }

        .app-body,
        .app-body.is-slim {
            margin-left: 0;
        }

        .topbar-title { display: block; }

        .stat-grid { grid-template-columns: 1fr 1fr; }

        .page-header-actions .btn-cp span { display: none; } /* icon-only on tablet */
        .page-header-actions .btn-cp { padding: 8px 10px; }
    }

    /* ─────────────────────────────────────────
       RESPONSIVE — MOBILE ≤576px
    ───────────────────────────────────────── */
    @media (max-width: 576px) {
        .content-area { padding: 14px; }

        .stat-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
        .stat-card { padding: 12px; }
        .stat-icon { width: 34px; height: 34px; font-size: 16px; }
        .stat-value { font-size: 18px; }

        .page-header { flex-direction: column; gap: 10px; }
        .page-header-actions { width: 100%; }
        .page-header-actions .btn-cp { flex: 1; justify-content: center; }
        .page-header-actions .btn-cp span { display: inline; } /* back to text */

        .user-meta { display: none; }
        .user-pill { padding: 5px; }

        .app-footer { flex-direction: column; text-align: center; }
        .footer-links { flex-wrap: wrap; justify-content: center; }

        .cp-card-body { padding: 14px; }
    }

    /* ─────────────────────────────────────────
       DEMO CONTENT (à supprimer en production)
    ───────────────────────────────────────── */
    .demo-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .demo-table th {
        padding: 10px 14px;
        font-size: 11px; font-weight: 700; letter-spacing: .05em;
        text-transform: uppercase; color: var(--muted);
        border-bottom: 1px solid var(--border);
        text-align: left; white-space: nowrap;
    }
    .demo-table td {
        padding: 11px 14px;
        border-bottom: 1px solid var(--border);
        color: var(--ink-2);
        vertical-align: middle;
    }
    .demo-table tr:last-child td { border-bottom: none; }
    .demo-table tr:hover td { background: var(--sand); }

    .status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px; font-weight: 600;
    }
    .status-pill::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .s-green  { background: #ECFDF5; color: #059669; }
    .s-orange { background: #FFF7ED; color: #C2410C; }
    .s-blue   { background: #EFF6FF; color: #1D4ED8; }
    .s-gray   { background: #F3F4F6; color: #4B5563; }

    .table-wrap { overflow-x: auto; }
    </style>

    @stack('styles')
</head>
<body>

<!-- ═══════════════════════════════════════
     TOPBAR
════════════════════════════════════════ -->
<header class="topbar">
    {{-- Hamburger (mobile/tablet) --}}
    <button class="topbar-hamburger" id="mobileToggle" aria-label="Menu">
        <i class="bi bi-list"></i>
    </button>

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="topbar-brand">
        <span class="brand-icon"><i class="bi bi-building-gear"></i></span>
        <span class="brand-name">ChantierPro</span>
    </a>

    {{-- Page title (mobile only) --}}
    <span class="topbar-title">@yield('page-title', 'Tableau de bord')</span>

    {{-- Right actions --}}
    <div class="topbar-right">
        {{-- Notifications --}}
        <a href="#" class="notif-btn" data-bs-toggle="dropdown" aria-label="Notifications">
            <i class="bi bi-bell"></i>
            <span class="notif-dot"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" style="min-width:260px;font-size:13px">
            <li><h6 class="dropdown-header">Notifications</h6></li>
            <li>
                <a class="dropdown-item d-flex gap-2 align-items-start py-2" href="#">
                    <i class="bi bi-exclamation-triangle-fill text-warning mt-1" style="flex-shrink:0"></i>
                    <div>
                        <div style="font-weight:600;font-size:12px">Retard détecté</div>
                        <div style="color:#6B7280;font-size:11px">Route RN1 — Terrassement km 14</div>
                    </div>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex gap-2 align-items-start py-2" href="#">
                    <i class="bi bi-cash-coin text-danger mt-1" style="flex-shrink:0"></i>
                    <div>
                        <div style="font-weight:600;font-size:12px">Budget dépassé</div>
                        <div style="color:#6B7280;font-size:11px">Résidence Palmiers — Béton +12%</div>
                    </div>
                </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item text-center" style="font-size:12px;color:#E8520A" href="#">Voir toutes les alertes</a></li>
        </ul>

        {{-- User --}}
        <div class="dropdown">
            <a href="#" class="user-pill dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="user-meta">
                    <span class="user-name">{{ Auth::user()->name ?? 'Utilisateur' }}</span>
                    <span class="user-role">{{ Auth::user()->role ?? 'Admin' }}</span>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="font-size:13px;min-width:180px">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle me-2 text-muted"></i>Mon profil</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2 text-muted"></i>Paramètres</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle me-2 text-muted"></i>Aide</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logoute') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- ═══════════════════════════════════════
     OVERLAY (mobile)
════════════════════════════════════════ -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ═══════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ -->
<aside class="sidebar" id="sidebar">

    {{-- slim toggle (desktop) --}}
    <button class="slim-toggle" id="slimToggle" aria-label="Réduire la barre">
        <i class="bi bi-chevron-left" id="slimIcon"></i>
    </button>

    <nav class="sidebar-nav">

        <div class="menu-section">Navigation</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span class="nav-label">Tableau de bord</span>
        </a>

        <a href="#"
           class="nav-link {{ request()->routeIs('chantiers.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            <span class="nav-label">Chantiers</span>
        </a>

        <a href="#"
           class="nav-link {{ request()->routeIs('planning.*') ? 'active' : '' }}">
            <i class="bi bi-calendar3-range"></i>
            <span class="nav-label">Planning</span>
        </a>

        <a href="#"
           class="nav-link {{ request()->routeIs('fiches.*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i>
            <span class="nav-label">Fiches journalières</span>
        </a>

        <div class="menu-section">Finances</div>

        <a href="{{ route('rapport.depenses.liste') }}"
           class="nav-link {{ request()->routeIs('rapport.depenses.*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i>
            <span class="nav-label">Dépenses</span>
            <span class="nav-badge">3</span>
        </a>

        <a href="#"
           class="nav-link {{ request()->routeIs('rapports.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-pdf"></i>
            <span class="nav-label">Rapports PDF</span>
        </a>

        <div class="menu-section">Système</div>

        <a href="#" class="nav-link">
            <i class="bi bi-people"></i>
            <span class="nav-label">Équipe</span>
        </a>

        <a href="#" class="nav-link">
            <i class="bi bi-gear"></i>
            <span class="nav-label">Paramètres</span>
        </a>

    </nav>

    {{-- Footer sidebar --}}
    <div class="sidebar-footer">
        <a href="#" class="sidebar-footer-inner">
            <i class="bi bi-question-circle"></i>
            <span class="sidebar-footer-text">Aide &amp; support</span>
        </a>
    </div>

</aside>

<!-- ═══════════════════════════════════════
     MAIN
════════════════════════════════════════ -->
<div class="app-body" id="appBody">
    <main class="content-area">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="cp-alert cp-alert-success" id="alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
            <button class="cp-alert-close" onclick="this.closest('.cp-alert').remove()">&times;</button>
        </div>
        @endif

        @if(session('error'))
        <div class="cp-alert cp-alert-danger" id="alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ session('error') }}</div>
            <button class="cp-alert-close" onclick="this.closest('.cp-alert').remove()">&times;</button>
        </div>
        @endif

        {{-- ══ PAGE HEADER (desktop) ══ --}}
        <div class="page-header">
            <div class="page-header-left">
                <div class="breadcrumb-wrap">
                    <a href="{{ route('dashboard') }}">Accueil</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>@yield('page-title', 'Tableau de bord')</span>
                </div>
                <h1>@yield('page-title', 'Tableau de bord')</h1>
                <p>@yield('page-subtitle', 'Vue d\'ensemble de vos chantiers')</p>
            </div>
            <div class="page-header-actions">
                @yield('page-actions')
            </div>
        </div>

        {{-- ══ CONTENU DE LA PAGE ══ --}}
        @yield('content')

        {{-- ══ DEMO PLACEHOLDER (à retirer) ══ --}}
        @if(request()->routeIs('dashboard'))
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-navy"><i class="bi bi-building"></i></div>
                <div class="stat-body">
                    <div class="stat-value">8</div>
                    <div class="stat-label">Chantiers actifs</div>
                    <div class="stat-trend stat-trend-up"><i class="bi bi-arrow-up-short"></i> +2 ce mois</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-orange"><i class="bi bi-journal-text"></i></div>
                <div class="stat-body">
                    <div class="stat-value">24</div>
                    <div class="stat-label">Fiches ce mois</div>
                    <div class="stat-trend stat-trend-up"><i class="bi bi-arrow-up-short"></i> +6 vs mois passé</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-red"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-body">
                    <div class="stat-value">3</div>
                    <div class="stat-label">Alertes actives</div>
                    <div class="stat-trend stat-trend-down"><i class="bi bi-arrow-down-short"></i> retard ou budget</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><i class="bi bi-check-circle"></i></div>
                <div class="stat-body">
                    <div class="stat-value">2</div>
                    <div class="stat-label">Terminés ce mois</div>
                    <div class="stat-trend stat-trend-up"><i class="bi bi-arrow-up-short"></i> dans les délais</div>
                </div>
            </div>
        </div>

        <div class="cp-card">
            <div class="cp-card-header">
                <div class="cp-card-title">
                    <i class="bi bi-building"></i>
                    Chantiers en cours
                </div>
                <a href="#" class="btn-cp btn-cp-primary">
                    <i class="bi bi-plus-lg"></i>
                    <span>Nouveau chantier</span>
                </a>
            </div>
            <div class="table-wrap">
                <table class="demo-table">
                    <thead>
                        <tr>
                            <th>Chantier</th>
                            <th>Statut</th>
                            <th>Budget</th>
                            <th>Avancement</th>
                            <th>Dernière fiche</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="font-weight:600;color:var(--navy)">Résidence Les Palmiers</div>
                                <div style="font-size:11px;color:var(--muted)">Niamey Nord</div>
                            </td>
                            <td><span class="status-pill s-green">En cours</span></td>
                            <td>
                                <div style="font-weight:600">68%</div>
                                <div style="width:80px;height:4px;background:var(--border);border-radius:2px;margin-top:4px">
                                    <div style="width:68%;height:100%;background:var(--orange);border-radius:2px"></div>
                                </div>
                            </td>
                            <td>45%</td>
                            <td style="color:var(--muted)">Aujourd'hui</td>
                            <td>
                                <a href="#" class="btn-cp btn-cp-outline" style="padding:5px 10px;font-size:12px">
                                    Voir <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight:600;color:var(--navy)">Route RN1 — km 12-18</div>
                                <div style="font-size:11px;color:var(--muted)">Tillabéri</div>
                            </td>
                            <td><span class="status-pill s-orange">Retard</span></td>
                            <td>
                                <div style="font-weight:600;color:#DC2626">92%</div>
                                <div style="width:80px;height:4px;background:var(--border);border-radius:2px;margin-top:4px">
                                    <div style="width:92%;height:100%;background:#DC2626;border-radius:2px"></div>
                                </div>
                            </td>
                            <td>61%</td>
                            <td style="color:var(--muted)">Hier</td>
                            <td>
                                <a href="#" class="btn-cp btn-cp-outline" style="padding:5px 10px;font-size:12px">
                                    Voir <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight:600;color:var(--navy)">École primaire Yantala</div>
                                <div style="font-size:11px;color:var(--muted)">Niamey</div>
                            </td>
                            <td><span class="status-pill s-blue">Planifié</span></td>
                            <td>
                                <div style="font-weight:600">0%</div>
                                <div style="width:80px;height:4px;background:var(--border);border-radius:2px;margin-top:4px">
                                    <div style="width:0%;height:100%;background:var(--orange);border-radius:2px"></div>
                                </div>
                            </td>
                            <td>0%</td>
                            <td style="color:var(--muted)">—</td>
                            <td>
                                <a href="#" class="btn-cp btn-cp-outline" style="padding:5px 10px;font-size:12px">
                                    Voir <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </main>

    <footer class="app-footer">
        <span>&copy; {{ date('Y') }} ChantierPro — CFSIS Globale Services &nbsp;·&nbsp; v1.0.0</span>
        <div class="footer-links">
            <a href="#">CGU</a>
            <a href="#">Confidentialité</a>
            <a href="#">Support</a>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar        = document.getElementById('sidebar');
    const overlay        = document.getElementById('sidebarOverlay');
    const mobileToggle   = document.getElementById('mobileToggle');
    const slimToggle     = document.getElementById('slimToggle');
    const slimIcon       = document.getElementById('slimIcon');
    const appBody        = document.getElementById('appBody');

    /* ── Mobile open/close ── */
    function openMobile() {
        sidebar.classList.add('mobile-open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeMobile() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    mobileToggle?.addEventListener('click', function () {
        sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile();
    });
    overlay?.addEventListener('click', closeMobile);

    /* ── Desktop slim toggle ── */
    const SLIM_KEY = 'cp_sidebar_slim';
    function applySlim(slim) {
        sidebar.classList.toggle('is-slim', slim);
        appBody.classList.toggle('is-slim', slim);
        slimIcon.className = slim ? 'bi bi-chevron-right' : 'bi bi-chevron-left';
        localStorage.setItem(SLIM_KEY, slim ? '1' : '0');
    }

    slimToggle?.addEventListener('click', function () {
        applySlim(!sidebar.classList.contains('is-slim'));
    });

    if (localStorage.getItem(SLIM_KEY) === '1') {
        applySlim(true);
    }

    /* ── Close sidebar on nav click (mobile) ── */
    sidebar.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 992) closeMobile();
        });
    });

    /* ── Auto-dismiss flash alerts ── */
    document.querySelectorAll('.cp-alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 400);
        }, 5000);
    });

    /* ── Close mobile sidebar on resize ── */
    window.addEventListener('resize', function () {
        if (window.innerWidth > 992) closeMobile();
    });
});
</script>

@stack('scripts')
@yield('scripts')
</body>
</html>