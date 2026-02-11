<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChantierPro - Gestion de Chantiers BTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #FF6B35;
            --secondary-color: #004E89;
            --accent-color: #F7B801;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #003666 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #003666 100%);
            color: white;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="2" height="2" fill="white" opacity="0.1"/></svg>');
            background-size: 50px 50px;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-custom:hover {
            background-color: #e55a2b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }

        .btn-outline-custom {
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-custom:hover {
            background-color: white;
            color: var(--secondary-color);
        }

        .feature-card {
            padding: 30px;
            border-radius: 15px;
            transition: all 0.3s;
            height: 100%;
            border: 1px solid #e0e0e0;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 20px;
        }

        .pricing-card {
            border-radius: 20px;
            padding: 40px 30px;
            transition: all 0.3s;
            border: 2px solid #e0e0e0;
            height: 100%;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .pricing-card.featured {
            border: 3px solid var(--primary-color);
            position: relative;
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        }

        .featured-badge {
            position: absolute;
            top: -15px;
            right: 30px;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .price {
            font-size: 3rem;
            font-weight: 700;
            color: var(--secondary-color);
        }

        .price-period {
            font-size: 1rem;
            color: #6c757d;
        }

        .pricing-feature {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .pricing-feature:last-child {
            border-bottom: none;
        }

        .stats-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 60px 0;
        }

        .stat-card {
            text-align: center;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            color: #6c757d;
            font-size: 1.1rem;
        }

        footer {
            background-color: #1a1a1a;
            color: white;
            padding: 40px 0 20px;
        }

        .footer-links a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="#">
                <i class="bi bi-building-gear"></i> BTP-Pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#fonctionnalites">Fonctionnalités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tarifs">Tarifs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-light btn-sm" href="{{ route('login') }}">Connexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Gérez vos chantiers BTP en toute simplicité</h1>
                    <p class="lead mb-4">La solution complète pour planifier, suivre et optimiser tous vos projets de construction. Gagnez du temps et augmentez votre rentabilité.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="#tarifs" class="btn btn-primary-custom btn-lg">
                            <i class="bi bi-rocket-takeoff"></i> Démarrer gratuitement
                        </a>
                        <a href="#fonctionnalites" class="btn btn-outline-custom btn-lg">
                            En savoir plus
                        </a>
                    </div>
                    <div class="mt-4">
                        <small class="text-white-50"><i class="bi bi-check-circle-fill text-success"></i> Essai gratuit 14 jours • Sans carte bancaire</small>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='600' height='500' viewBox='0 0 600 500'%3E%3Crect fill='%23f8f9fa' width='600' height='500' rx='20'/%3E%3Cg fill='%23004E89' opacity='0.2'%3E%3Crect x='50' y='50' width='500' height='30' rx='5'/%3E%3Crect x='50' y='100' width='400' height='30' rx='5'/%3E%3Crect x='50' y='150' width='450' height='30' rx='5'/%3E%3Crect x='50' y='220' width='200' height='150' rx='10'/%3E%3Crect x='270' y='220' width='200' height='150' rx='10'/%3E%3Crect x='50' y='390' width='500' height='60' rx='10'/%3E%3C/g%3E%3Ctext x='300' y='250' text-anchor='middle' fill='%23004E89' font-size='24' font-family='Arial' font-weight='bold'%3ETableau de bord%3C/text%3E%3C/svg%3E" alt="Dashboard Preview" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Entreprises clientes</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">2500+</div>
                        <div class="stat-label">Chantiers gérés</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Satisfaction client</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4">
                    <div class="stat-card">
                        <div class="stat-number">35%</div>
                        <div class="stat-label">Gain de productivité</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fonctionnalites" class="py-5" style="padding: 100px 0;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Fonctionnalités puissantes</h2>
                <p class="section-subtitle">Tout ce dont vous avez besoin pour gérer vos chantiers efficacement</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Planning intelligent</h4>
                        <p class="text-muted">Planifiez vos chantiers avec un calendrier interactif et évitez les conflits de ressources.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Gestion d'équipe</h4>
                        <p class="text-muted">Assignez des tâches, suivez la présence et optimisez la répartition de vos équipes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Suivi en temps réel</h4>
                        <p class="text-muted">Tableaux de bord détaillés pour suivre l'avancement et les coûts de vos projets.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Documents centralisés</h4>
                        <p class="text-muted">Stockez et partagez plans, devis et factures en toute sécurité au même endroit.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Application mobile</h4>
                        <p class="text-muted">Accédez à vos chantiers depuis votre smartphone, même hors connexion.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Sécurité des données</h4>
                        <p class="text-muted">Vos données sont cryptées et sauvegardées quotidiennement sur des serveurs sécurisés.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="tarifs" class="py-5" style="padding: 100px 0; background-color: #f8f9fa;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Choisissez votre abonnement</h2>
                <p class="section-subtitle">Des tarifs adaptés à la taille de votre entreprise</p>
            </div>
            <div class="row g-4">
                <!-- Starter Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card bg-white">
                        <h3 class="fw-bold mb-3">Starter</h3>
                        <p class="text-muted mb-4">Pour les petites entreprises</p>
                        <div class="mb-4">
                            <span class="price">29€</span>
                            <span class="price-period">/mois</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Jusqu'à 5 chantiers actifs
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                3 utilisateurs inclus
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                10 Go de stockage
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Application mobile
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Support par email
                            </li>
                            <li class="pricing-feature text-muted">
                                <i class="bi bi-x-circle me-2"></i>
                                Rapports avancés
                            </li>
                            <li class="pricing-feature text-muted">
                                <i class="bi bi-x-circle me-2"></i>
                                API et intégrations
                            </li>
                        </ul>
                        <button class="btn btn-outline-secondary w-100 btn-lg">Commencer</button>
                    </div>
                </div>

                <!-- Professional Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card featured">
                        <span class="featured-badge">Le plus populaire</span>
                        <h3 class="fw-bold mb-3">Professional</h3>
                        <p class="text-muted mb-4">Pour les entreprises en croissance</p>
                        <div class="mb-4">
                            <span class="price">79€</span>
                            <span class="price-period">/mois</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Chantiers illimités
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                10 utilisateurs inclus
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                100 Go de stockage
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Application mobile
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Support prioritaire
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Rapports avancés
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Intégrations tierces
                            </li>
                        </ul>
                        <button class="btn btn-primary-custom w-100 btn-lg">Essayer gratuitement</button>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="col-lg-4">
                    <div class="pricing-card bg-white">
                        <h3 class="fw-bold mb-3">Enterprise</h3>
                        <p class="text-muted mb-4">Pour les grandes entreprises</p>
                        <div class="mb-4">
                            <span class="price">Sur mesure</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Chantiers illimités
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Utilisateurs illimités
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Stockage illimité
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Application mobile
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Support dédié 24/7
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Rapports personnalisés
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                API complète
                            </li>
                            <li class="pricing-feature">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Formation sur site
                            </li>
                        </ul>
                        <button class="btn btn-outline-secondary w-100 btn-lg">Nous contacter</button>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <p class="text-muted">Tous les plans incluent 14 jours d'essai gratuit. Aucune carte bancaire requise.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="padding: 80px 0; background: linear-gradient(135deg, var(--secondary-color) 0%, #003666 100%); color: white;">
        <div class="container text-center">
            <h2 class="display-5 fw-bold mb-4">Prêt à transformer votre gestion de chantiers ?</h2>
            <p class="lead mb-4">Rejoignez les centaines d'entreprises BTP qui nous font confiance</p>
            <a href="#tarifs" class="btn btn-primary-custom btn-lg">
                <i class="bi bi-rocket-takeoff"></i> Démarrer mon essai gratuit
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-building-gear"></i> ChantierPro
                    </h5>
                    <p class="text-muted">La solution de gestion de chantiers BTP qui simplifie votre quotidien.</p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-linkedin fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-instagram fs-5"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4 footer-links">
                    <h6 class="fw-bold mb-3">Produit</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#fonctionnalites">Fonctionnalités</a></li>
                        <li class="mb-2"><a href="#tarifs">Tarifs</a></li>
                        <li class="mb-2"><a href="#">Sécurité</a></li>
                        <li class="mb-2"><a href="#">Mises à jour</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4 footer-links">
                    <h6 class="fw-bold mb-3">Entreprise</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">À propos</a></li>
                        <li class="mb-2"><a href="#">Blog</a></li>
                        <li class="mb-2"><a href="#">Carrières</a></li>
                        <li class="mb-2"><a href="#">Presse</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Contact</h6>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> contact@chantierpro.fr</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> +33 1 23 45 67 89</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> 123 Avenue de la Construction, 75001 Paris</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-muted mb-0">&copy; 2024 ChantierPro. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-center text-md-end footer-links">
                    <a href="#" class="me-3">Mentions légales</a>
                    <a href="#" class="me-3">Confidentialité</a>
                    <a href="#">CGU</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>