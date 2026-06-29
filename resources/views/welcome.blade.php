<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChantierPro — Gestion de chantiers BTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --orange:   #E8520A;
            --orange-lt:#FFF0E8;
            --navy:     #0B2545;
            --navy-mid: #163A6B;
            --sand:     #F5F0E8;
            --ink:      #1A1A2E;
            --muted:    #6B7280;
            --border:   #E2E4E9;
            --radius:   12px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: var(--ink);
            background: #fff;
            margin: 0;
        }

        /* ─── NAV ─────────────────────────────────────── */
        .nav-wrap {
            background: var(--navy);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            white-space: nowrap;
        }
        .nav-logo-icon {
            width: 32px; height: 32px;
            background: var(--orange);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
            margin: 0; padding: 0;
        }
        .nav-links a {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 0.9rem;
            padding: 8px 14px;
            border-radius: 8px;
            transition: background 0.15s, color 0.15s;
        }
        .nav-links a:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .nav-cta {
            background: var(--orange);
            color: #fff !important;
            font-weight: 600;
        }
        .nav-cta:hover { background: #d0480a !important; }

        @media (max-width: 768px) {
            .nav-links { display: none; }
        }

        /* ─── HERO ─────────────────────────────────────── */
        .hero {
            background: var(--navy);
            color: #fff;
            padding: 80px 24px 72px;
            overflow: hidden;
            position: relative;
        }
        .hero::after {
            content: '';
            position: absolute;
            right: -120px; bottom: -120px;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(232,82,10,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center;
        }
        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; gap: 40px; }
            .hero-visual { display: none; }
        }
        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(232,82,10,0.15);
            border: 1px solid rgba(232,82,10,0.3);
            color: #FFA070;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 20px;
            margin-bottom: 24px;
        }
        .hero h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -0.03em;
            margin: 0 0 20px;
        }
        .hero h1 em {
            font-style: normal;
            color: var(--orange);
        }
        .hero-sub {
            font-size: 1.05rem;
            color: rgba(255,255,255,0.65);
            line-height: 1.7;
            max-width: 480px;
            margin: 0 0 36px;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        .btn-orange {
            background: var(--orange);
            color: #fff;
            border: none;
            padding: 14px 28px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-orange:hover { background: #d0480a; color: #fff; transform: translateY(-1px); }
        .btn-ghost {
            background: transparent;
            color: rgba(255,255,255,0.8);
            border: 1.5px solid rgba(255,255,255,0.25);
            padding: 13px 24px;
            border-radius: var(--radius);
            font-size: 0.95rem;
            text-decoration: none;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-ghost:hover { border-color: rgba(255,255,255,0.6); color: #fff; }
        .hero-note {
            margin-top: 20px;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.4);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .hero-note i { color: #4CAF80; }

        /* Hero visual — dashboard mock */
        .hero-visual {
            position: relative;
        }
        .dash-mock {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            padding: 20px;
            backdrop-filter: blur(4px);
        }
        .dash-topbar {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        .dash-dot { width: 10px; height: 10px; border-radius: 50%; }
        .dash-row {
            background: rgba(255,255,255,0.07);
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dash-row-label { font-size: 0.82rem; color: rgba(255,255,255,0.75); }
        .dash-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .badge-green  { background: rgba(74,200,130,0.2); color: #4AC882; }
        .badge-orange { background: rgba(232,82,10,0.2);  color: #FF8A5B; }
        .badge-blue   { background: rgba(80,130,230,0.2); color: #7AABFF; }
        .dash-progress-wrap {
            background: rgba(255,255,255,0.07);
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 10px;
        }
        .dash-progress-label { font-size: 0.8rem; color: rgba(255,255,255,0.6); margin-bottom: 8px; }
        .dash-bar-bg { background: rgba(255,255,255,0.1); border-radius: 4px; height: 6px; }
        .dash-bar-fill { height: 6px; border-radius: 4px; background: var(--orange); }
        .dash-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        .dash-stat-box {
            background: rgba(255,255,255,0.07);
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }
        .dash-stat-n { font-size: 1.4rem; font-weight: 700; color: #fff; }
        .dash-stat-l { font-size: 0.72rem; color: rgba(255,255,255,0.5); margin-top: 2px; }

        /* ─── SECTION WRAPPER ──────────────────────────── */
        .section {
            padding: 80px 24px;
        }
        .section-inner {
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--orange);
            margin-bottom: 12px;
        }
        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.4rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--navy);
            margin: 0 0 16px;
        }
        .section-sub {
            font-size: 1rem;
            color: var(--muted);
            max-width: 560px;
            line-height: 1.7;
            margin: 0 0 56px;
        }

        /* ─── WORKFLOW ─────────────────────────────────── */
        .workflow { background: var(--sand); }
        .workflow-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0;
            position: relative;
        }
        .workflow-step {
            padding: 32px 24px 32px;
            position: relative;
        }
        .workflow-step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0; top: 50%;
            transform: translateY(-50%);
            width: 1px; height: 60%;
            background: var(--border);
        }
        @media (max-width: 768px) {
            .workflow-step:not(:last-child)::after { display: none; }
            .workflow-step { border-bottom: 1px solid var(--border); }
        }
        .step-num {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--orange);
            line-height: 1;
            opacity: 0.25;
            margin-bottom: 12px;
        }
        .step-title { font-size: 0.95rem; font-weight: 700; color: var(--navy); margin-bottom: 8px; }
        .step-desc { font-size: 0.85rem; color: var(--muted); line-height: 1.6; }

        /* ─── FEATURES ─────────────────────────────────── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        @media (max-width: 900px) { .features-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 600px) { .features-grid { grid-template-columns: 1fr; } }

        .feat-card {
            background: var(--sand);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px 24px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .feat-card:hover {
            border-color: var(--orange);
            box-shadow: 0 4px 24px rgba(232,82,10,0.08);
        }
        .feat-icon {
            width: 44px; height: 44px;
            background: var(--orange-lt);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            color: var(--orange);
            margin-bottom: 16px;
        }
        .feat-title { font-size: 0.95rem; font-weight: 700; color: var(--navy); margin-bottom: 8px; }
        .feat-desc  { font-size: 0.85rem; color: var(--muted); line-height: 1.6; }

        /* highlight card */
        .feat-card.highlight {
            background: var(--navy);
            border-color: transparent;
            grid-column: span 1;
        }
        .feat-card.highlight .feat-icon { background: rgba(232,82,10,0.2); }
        .feat-card.highlight .feat-title { color: #fff; }
        .feat-card.highlight .feat-desc  { color: rgba(255,255,255,0.55); }

        /* ─── PRICING ──────────────────────────────────── */
        .pricing { background: #fff; }
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 900px) { .pricing-grid { grid-template-columns: 1fr; max-width: 440px; } }

        .plan-card {
            border: 1.5px solid var(--border);
            border-radius: 16px;
            padding: 36px 28px;
            position: relative;
        }
        .plan-card.popular {
            border-color: var(--orange);
            box-shadow: 0 8px 40px rgba(232,82,10,0.1);
        }
        .popular-badge {
            position: absolute;
            top: -13px; left: 50%;
            transform: translateX(-50%);
            background: var(--orange);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 4px 16px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .plan-name { font-size: 1rem; font-weight: 700; color: var(--navy); margin-bottom: 6px; }
        .plan-desc { font-size: 0.82rem; color: var(--muted); margin-bottom: 24px; }
        .plan-price {
            font-size: 2.6rem;
            font-weight: 900;
            color: var(--navy);
            letter-spacing: -0.04em;
            line-height: 1;
            margin-bottom: 4px;
        }
        .plan-price sup { font-size: 1.1rem; vertical-align: super; font-weight: 700; }
        .plan-price span { font-size: 0.9rem; font-weight: 400; color: var(--muted); }
        .plan-divider { border: none; border-top: 1px solid var(--border); margin: 24px 0; }
        .plan-features { list-style: none; margin: 0 0 28px; padding: 0; }
        .plan-features li {
            font-size: 0.85rem;
            color: var(--ink);
            padding: 8px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-bottom: 1px solid var(--border);
        }
        .plan-features li:last-child { border-bottom: none; }
        .plan-features .check { color: var(--orange); flex-shrink: 0; margin-top: 2px; }
        .plan-features .miss  { color: #CBD0D8; flex-shrink: 0; margin-top: 2px; }
        .plan-features .miss-text { color: var(--muted); }

        .btn-plan-primary {
            display: block;
            background: var(--orange);
            color: #fff;
            border: none;
            padding: 13px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.92rem;
            text-align: center;
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
        }
        .btn-plan-primary:hover { background: #d0480a; color: #fff; }
        .btn-plan-outline {
            display: block;
            background: transparent;
            color: var(--navy);
            border: 1.5px solid var(--border);
            padding: 12px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 0.92rem;
            text-align: center;
            text-decoration: none;
            transition: border-color 0.2s;
            cursor: pointer;
        }
        .btn-plan-outline:hover { border-color: var(--navy); }

        /* ─── CTA ──────────────────────────────────────── */
        .cta-section {
            background: var(--navy);
            color: #fff;
            padding: 80px 24px;
            text-align: center;
        }
        .cta-section h2 {
            font-size: clamp(1.6rem, 3vw, 2.4rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
        }
        .cta-section p { color: rgba(255,255,255,0.6); margin-bottom: 36px; }

        /* ─── FOOTER ───────────────────────────────────── */
        footer {
            background: var(--ink);
            color: rgba(255,255,255,0.6);
            padding: 56px 24px 32px;
            font-size: 0.85rem;
        }
        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px;
            margin-bottom: 48px;
        }
        @media (max-width: 768px) {
            .footer-grid { grid-template-columns: 1fr 1fr; gap: 28px; }
        }
        @media (max-width: 480px) {
            .footer-grid { grid-template-columns: 1fr; }
        }
        .footer-brand { color: #fff; font-size: 1rem; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        .footer-col-title { color: rgba(255,255,255,0.9); font-weight: 600; margin-bottom: 14px; font-size: 0.82rem; letter-spacing: 0.04em; text-transform: uppercase; }
        footer a { color: rgba(255,255,255,0.55); text-decoration: none; display: block; margin-bottom: 8px; }
        footer a:hover { color: #fff; }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.08);
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .footer-bottom a { display: inline; margin: 0 0 0 20px; }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="nav-wrap">
    <div class="nav-inner">
        <a href="#" class="nav-logo">
            <span class="nav-logo-icon"><i class="bi bi-building-gear"></i></span>
            ChantierPro
        </a>
        <ul class="nav-links">
            <li><a href="#workflow">Comment ça marche</a></li>
            <li><a href="#fonctionnalites">Fonctionnalités</a></li>
            <li><a href="#tarifs">Tarifs</a></li>
            <li><a href="{{ route('login') }}" class="nav-cta">Connexion</a></li>
        </ul>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-copy">
            <div class="hero-eyebrow">
                <i class="bi bi-lightning-charge-fill"></i>
                Plateforme BTP
            </div>
            <h1>Vos chantiers,<br><em>sous contrôle</em><br>chaque jour</h1>
            <p class="hero-sub">
                Fiches journalières, planning, dépenses et alertes retard — tout ce qu'un conducteur de travaux et son directeur ont besoin de voir, au même endroit.
            </p>
            <div class="hero-actions">
                <a href="#tarifs" class="btn-orange">
                    <i class="bi bi-play-fill"></i> Démarrer gratuitement
                </a>
                <a href="#fonctionnalites" class="btn-ghost">Voir les fonctionnalités</a>
            </div>
            <div class="hero-note">
                <i class="bi bi-check-circle-fill"></i>
                14 jours d'essai gratuit · Aucune carte bancaire requise
            </div>
        </div>

        <div class="hero-visual">
            <div class="dash-mock">
                <div class="dash-topbar">
                    <div class="dash-dot" style="background:#FF5F57"></div>
                    <div class="dash-dot" style="background:#FEBC2E"></div>
                    <div class="dash-dot" style="background:#28C840"></div>
                </div>
                <div class="dash-row">
                    <span class="dash-row-label">Résidence Les Palmiers</span>
                    <span class="dash-badge badge-green">En cours</span>
                </div>
                <div class="dash-row">
                    <span class="dash-row-label">Route nationale RN1 — km 14</span>
                    <span class="dash-badge badge-orange">Retard détecté</span>
                </div>
                <div class="dash-row">
                    <span class="dash-row-label">École primaire de Yantala</span>
                    <span class="dash-badge badge-blue">Planifié</span>
                </div>
                <div class="dash-progress-wrap">
                    <div class="dash-progress-label">Budget consommé — Résidence Les Palmiers</div>
                    <div class="dash-bar-bg"><div class="dash-bar-fill" style="width:68%"></div></div>
                    <div style="display:flex;justify-content:space-between;margin-top:6px">
                        <span style="font-size:0.72rem;color:rgba(255,255,255,0.4)">68% utilisé</span>
                        <span style="font-size:0.72rem;color:rgba(255,255,255,0.4)">32% restant</span>
                    </div>
                </div>
                <div class="dash-stats">
                    <div class="dash-stat-box">
                        <div class="dash-stat-n">12</div>
                        <div class="dash-stat-l">Fiches aujourd'hui</div>
                    </div>
                    <div class="dash-stat-box">
                        <div class="dash-stat-n">3</div>
                        <div class="dash-stat-l">Alertes actives</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WORKFLOW -->
<section class="section workflow" id="workflow">
    <div class="section-inner">
        <div class="section-label">Comment ça marche</div>
        <h2 class="section-title">Du chantier au rapport en 4 étapes</h2>
        <p class="section-sub">Un flux pensé pour le terrain. Le conducteur saisit, le directeur consulte.</p>

        <div class="workflow-steps">
            <div class="workflow-step">
                <div class="step-num">01</div>
                <div class="step-title">Créez votre chantier</div>
                <div class="step-desc">Nom, budget, adresse, planning d'activités avec dates et coûts prévus.</div>
            </div>
            <div class="workflow-step">
                <div class="step-num">02</div>
                <div class="step-title">Fiche journalière</div>
                <div class="step-desc">Personnel, matériel, approvisionnements, avancement — remplie en 5 min depuis le téléphone.</div>
            </div>
            <div class="workflow-step">
                <div class="step-num">03</div>
                <div class="step-title">Alertes automatiques</div>
                <div class="step-desc">Retard détecté ? Budget dépassé ? L'application vous avertit avant que ça devienne un problème.</div>
            </div>
            <div class="workflow-step">
                <div class="step-num">04</div>
                <div class="step-title">Rapport hebdomadaire</div>
                <div class="step-desc">Générez le PDF du chantier en un clic, prêt à envoyer au maître d'ouvrage.</div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="section" id="fonctionnalites">
    <div class="section-inner">
        <div class="section-label">Fonctionnalités</div>
        <h2 class="section-title">Tout ce dont votre chantier a besoin</h2>
        <p class="section-sub">Des outils pensés pour les conducteurs de travaux, pas pour les informaticiens.</p>

        <div class="features-grid">
            <div class="feat-card highlight">
                <div class="feat-icon"><i class="bi bi-journal-text"></i></div>
                <div class="feat-title">Fiche journalière complète</div>
                <div class="feat-desc">Personnel par catégorie, matériel et état, approvisionnements, avancement des travaux — et signature numérique du contrôleur.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><i class="bi bi-calendar3-range"></i></div>
                <div class="feat-title">Planning avec détection des retards</div>
                <div class="feat-desc">Comparez automatiquement les dates réelles aux dates prévues. L'activité passe en rouge dès qu'elle dérape.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="feat-title">Suivi des dépenses par poste</div>
                <div class="feat-desc">Chaque dépense est rattachée à une activité du planning. Vous savez exactement où part votre budget.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="feat-title">Alertes dépassement budgétaire</div>
                <div class="feat-desc">Dès que le coût réel dépasse le coût prévu sur une activité, vous recevez une alerte.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><i class="bi bi-file-earmark-pdf"></i></div>
                <div class="feat-title">Rapports PDF hebdomadaires</div>
                <div class="feat-desc">Générez un rapport structuré par chantier chaque semaine, archivé automatiquement.</div>
            </div>
            <div class="feat-card">
                <div class="feat-icon"><i class="bi bi-phone"></i></div>
                <div class="feat-title">Pensé pour le mobile</div>
                <div class="feat-desc">Interface optimisée pour être utilisée sous le soleil, les mains chargées, depuis n'importe quel smartphone.</div>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="section pricing" id="tarifs">
    <div class="section-inner">
        <div class="section-label">Tarifs</div>
        <h2 class="section-title">Un plan pour chaque taille d'entreprise</h2>
        <p class="section-sub">Commencez gratuitement. Évoluez quand vous en avez besoin.</p>

        <div class="pricing-grid">

            <!-- Gratuit -->
            <div class="plan-card">
                <div class="plan-name">Gratuit</div>
                <div class="plan-desc">Pour tester la plateforme</div>
                <div class="plan-price">0<span> / mois</span></div>
                <hr class="plan-divider">
                <ul class="plan-features">
                    <li><i class="bi bi-check check"></i> 2 chantiers actifs</li>
                    <li><i class="bi bi-check check"></i> 1 utilisateur</li>
                    <li><i class="bi bi-check check"></i> Fiches journalières</li>
                    <li><i class="bi bi-check check"></i> Planning de base</li>
                    <li><i class="bi bi-x miss"></i><span class="miss-text">Rapports PDF</span></li>
                    <li><i class="bi bi-x miss"></i><span class="miss-text">Alertes automatiques</span></li>
                </ul>
                <a href="{{ route('login') }}" class="btn-plan-outline">Créer un compte</a>
            </div>

            <!-- Mensuel -->
            <div class="plan-card popular">
                <span class="popular-badge">Le plus choisi</span>
                <div class="plan-name">Mensuel</div>
                <div class="plan-desc">Pour les entreprises actives</div>
                <div class="plan-price"><sup>FCFA</sup> Sur&nbsp;devis<span></span></div>
                <hr class="plan-divider">
                <ul class="plan-features">
                    <li><i class="bi bi-check check"></i> Chantiers illimités</li>
                    <li><i class="bi bi-check check"></i> 5 utilisateurs inclus</li>
                    <li><i class="bi bi-check check"></i> Fiches journalières</li>
                    <li><i class="bi bi-check check"></i> Planning complet</li>
                    <li><i class="bi bi-check check"></i> Rapports PDF</li>
                    <li><i class="bi bi-check check"></i> Alertes retard & budget</li>
                </ul>
                <a href="#contact" class="btn-plan-primary">Nous contacter</a>
            </div>

            <!-- Annuel -->
            <div class="plan-card">
                <div class="plan-name">Annuel</div>
                <div class="plan-desc">Économisez 2 mois par an</div>
                <div class="plan-price"><sup>FCFA</sup> Sur&nbsp;devis<span></span></div>
                <hr class="plan-divider">
                <ul class="plan-features">
                    <li><i class="bi bi-check check"></i> Tout du plan Mensuel</li>
                    <li><i class="bi bi-check check"></i> Utilisateurs illimités</li>
                    <li><i class="bi bi-check check"></i> Stockage étendu</li>
                    <li><i class="bi bi-check check"></i> Support prioritaire</li>
                    <li><i class="bi bi-check check"></i> Formation incluse</li>
                    <li><i class="bi bi-check check"></i> 2 mois offerts</li>
                </ul>
                <a href="#contact" class="btn-plan-outline">Demander un devis</a>
            </div>

        </div>
        <p style="text-align:center;margin-top:32px;font-size:0.82rem;color:var(--muted)">
            14 jours d'essai gratuit sur tous les plans payants. Sans engagement.
        </p>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <h2>Prêt à prendre le contrôle de vos chantiers ?</h2>
    <p>Rejoignez les entreprises BTP qui gèrent leurs chantiers avec ChantierPro.</p>
    <a href="{{ route('login') }}" class="btn-orange" style="font-size:1rem;padding:16px 36px">
        <i class="bi bi-rocket-takeoff"></i> Démarrer gratuitement
    </a>
</section>

<!-- FOOTER -->
<footer id="contact">
    <div class="footer-inner">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <span style="background:var(--orange);width:24px;height:24px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;font-size:0.8rem;flex-shrink:0"><i class="bi bi-building-gear"></i></span>
                    ChantierPro
                </div>
                <p>La solution de gestion de chantiers BTP conçue pour le terrain.</p>
                <div style="margin-top:16px;display:flex;gap:14px">
                    <a href="#" style="display:inline"><i class="bi bi-facebook fs-6"></i></a>
                    <a href="#" style="display:inline"><i class="bi bi-twitter-x fs-6"></i></a>
                    <a href="#" style="display:inline"><i class="bi bi-linkedin fs-6"></i></a>
                </div>
            </div>
            <div>
                <div class="footer-col-title">Produit</div>
                <a href="#fonctionnalites">Fonctionnalités</a>
                <a href="#tarifs">Tarifs</a>
                <a href="#">Sécurité</a>
                <a href="#">Mises à jour</a>
            </div>
            <div>
                <div class="footer-col-title">Ressources</div>
                <a href="#">Documentation</a>
                <a href="#">Guide terrain</a>
                <a href="#">Blog</a>
                <a href="#">À propos</a>
            </div>
            <div>
                <div class="footer-col-title">Contact</div>
                <a href="mailto:contact@chantierpro.com"><i class="bi bi-envelope me-2"></i>contact@chantierpro.com</a>
                <a href="tel:+22700000000"><i class="bi bi-telephone me-2"></i>+227 00 00 00 00</a>
                <a href="#"><i class="bi bi-geo-alt me-2"></i>Niamey, Niger</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; 2025 ChantierPro. Tous droits réservés.</span>
            <div>
                <a href="#">Mentions légales</a>
                <a href="#">Confidentialité</a>
                <a href="#">CGU</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>