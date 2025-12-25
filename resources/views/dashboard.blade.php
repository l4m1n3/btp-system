@extends('layouts.app')

@section('title', 'Dashboard BTP')

@section('content')
<div class="container-fluid">
    <!-- En-tête du dashboard -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Tableau de bord</h1>
            <p class="text-muted">Bienvenue, {{ Auth::user()->name ?? 'Administrateur' }}. Voici vos statistiques du jour.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-calendar-alt me-2"></i>Ce mois-ci
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Aujourd'hui</a></li>
                    <li><a class="dropdown-item" href="#">Cette semaine</a></li>
                    <li><a class="dropdown-item" href="#">Ce mois-ci</a></li>
                    <li><a class="dropdown-item" href="#">Cette année</a></li>
                </ul>
            </div>
            <button class="btn btn-primary">
                <i class="fas fa-download me-2"></i>Exporter rapport
            </button>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Chantiers actifs</h6>
                        <h2 class="mb-0">12</h2>
                        <span class="text-success small"><i class="fas fa-arrow-up me-1"></i> 15% ce mois</span>
                    </div>
                    <div class="card-icon bg-primary">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none small">Voir tous les chantiers <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Ouvriers présents</h6>
                        <h2 class="mb-0">156</h2>
                        <span class="text-warning small"><i class="fas fa-users me-1"></i> Sur 180</span>
                    </div>
                    <div class="card-icon bg-success">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none small">Gérer les équipes <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">CA ce mois</h6>
                        <h2 class="mb-0">245K€</h2>
                        <span class="text-success small"><i class="fas fa-arrow-up me-1"></i> 8.5% vs mois dernier</span>
                    </div>
                    <div class="card-icon bg-info">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none small">Voir la facturation <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Matériels utilisés</h6>
                        <h2 class="mb-0">89%</h2>
                        <span class="text-danger small"><i class="fas fa-exclamation-triangle me-1"></i> 3 en panne</span>
                    </div>
                    <div class="card-icon bg-warning">
                        <i class="fas fa-tools"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="#" class="text-decoration-none small">Inventaire matériel <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="row g-4">
        <!-- Graphique des activités -->
        <div class="col-xl-8">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Activité des chantiers</h5>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary active">Hebdomadaire</button>
                        <button class="btn btn-sm btn-outline-secondary">Mensuel</button>
                        <button class="btn btn-sm btn-outline-secondary">Annuel</button>
                    </div>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Progression des chantiers -->
        <div class="col-xl-4">
            <div class="dashboard-card">
                <h5 class="mb-4">Progression des chantiers</h5>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tour A - Résidence Les Cèdres</span>
                        <span class="fw-bold">78%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 78%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Centre Commercial "Le Carré"</span>
                        <span class="fw-bold">45%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: 45%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Parking souterrain - Mairie</span>
                        <span class="fw-bold">92%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: 92%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Rénovation École Primaire</span>
                        <span class="fw-bold">32%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: 32%"></div>
                    </div>
                </div>
                <div class="text-end">
                    <a href="#" class="text-decoration-none small">Voir tous les chantiers <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des alertes et activités récentes -->
    <div class="row g-4 mt-4">
        <!-- Alertes urgentes -->
        <div class="col-lg-6">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Alertes urgentes</h5>
                    <span class="badge bg-danger">3 non traitées</span>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                <i class="fas fa-tools text-danger"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Panne de bétonnière</h6>
                                <p class="mb-0 text-muted small">Chantier "Résidence Les Cèdres" - En attente depuis 2 jours</p>
                            </div>
                            <button class="btn btn-sm btn-outline-danger">Traiter</button>
                        </div>
                    </div>
                    <div class="list-group-item border-0 px-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                <i class="fas fa-clipboard-list text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Manque de matériel</h6>
                                <p class="mb-0 text-muted small">Planches de coffrage insuffisantes - Urgent</p>
                            </div>
                            <button class="btn btn-sm btn-outline-warning">Commander</button>
                        </div>
                    </div>
                    <div class="list-group-item border-0 px-0 py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                <i class="fas fa-user-clock text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Absence d'équipe</h6>
                                <p class="mb-0 text-muted small">Équipe charpente absente aujourd'hui - À remplacer</p>
                            </div>
                            <button class="btn btn-sm btn-outline-info">Réaffecter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activités récentes -->
        <div class="col-lg-6">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Activités récentes</h5>
                    <a href="#" class="text-decoration-none small">Voir tout</a>
                </div>
                <div class="timeline">
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-icon bg-success bg-opacity-10 text-success p-2 rounded me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Facture #F2024-0452 validée</h6>
                                <p class="mb-0 text-muted small">Client: Promotelec • Montant: 12.450€ • Il y a 2 heures</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-icon bg-primary bg-opacity-10 text-primary p-2 rounded me-3">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Nouvel ouvrier ajouté</h6>
                                <p class="mb-0 text-muted small">Pierre Martin - Maçon • Affecté au chantier "Centre Commercial"</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-icon bg-info bg-opacity-10 text-info p-2 rounded me-3">
                                <i class="fas fa-truck-loading"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Livraison de matériel</h6>
                                <p class="mb-0 text-muted small">50 tonnes de ciment • Chantier "Parking Mairie" • Livré à 10:30</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="timeline-icon bg-warning bg-opacity-10 text-warning p-2 rounded me-3">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Contrôle sécurité effectué</h6>
                                <p class="mb-0 text-muted small">Chantier "École Primaire" • Aucune anomalie détectée</p>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="d-flex">
                            <div class="timeline-icon bg-danger bg-opacity-10 text-danger p-2 rounded me-3">
                                <i class="fas fa-cloud-rain"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Alerte météo</h6>
                                <p class="mb-0 text-muted small">Pluies annoncées demain • Chantiers extérieurs à sécuriser</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des chantiers -->
    <div class="dashboard-card mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Chantiers en cours</h5>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Rechercher un chantier..." style="width: 200px;">
                <button class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Nouveau chantier
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nom du chantier</th>
                        <th>Client</th>
                        <th>Chef de chantier</th>
                        <th>Progression</th>
                        <th>Date de fin</th>
                        <th>Budget</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <div>
                                    <strong>Tour A - Résidence Les Cèdres</strong>
                                    <div class="text-muted small">Réf: CH-2024-001</div>
                                </div>
                            </div>
                        </td>
                        <td>Promotelec SA</td>
                        <td>Jean Dupont</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                                    <div class="progress-bar bg-success" style="width: 78%"></div>
                                </div>
                                <span class="small">78%</span>
                            </div>
                        </td>
                        <td>15/06/2024</td>
                        <td>850K€</td>
                        <td><span class="status-badge status-active">En cours</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                    <i class="fas fa-shopping-cart text-info"></i>
                                </div>
                                <div>
                                    <strong>Centre Commercial "Le Carré"</strong>
                                    <div class="text-muted small">Réf: CH-2024-008</div>
                                </div>
                            </div>
                        </td>
                        <td>ImmoDev</td>
                        <td>Marie Lambert</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                                    <div class="progress-bar bg-info" style="width: 45%"></div>
                                </div>
                                <span class="small">45%</span>
                            </div>
                        </td>
                        <td>30/09/2024</td>
                        <td>1.2M€</td>
                        <td><span class="status-badge status-active">En cours</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                    <i class="fas fa-parking text-warning"></i>
                                </div>
                                <div>
                                    <strong>Parking souterrain - Mairie</strong>
                                    <div class="text-muted small">Réf: CH-2023-045</div>
                                </div>
                            </div>
                        </td>
                        <td>Ville de Paris</td>
                        <td>Thomas Moreau</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                                    <div class="progress-bar bg-warning" style="width: 92%"></div>
                                </div>
                                <span class="small">92%</span>
                            </div>
                        </td>
                        <td>31/03/2024</td>
                        <td>650K€</td>
                        <td><span class="status-badge status-active">Finalisation</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                    <i class="fas fa-school text-success"></i>
                                </div>
                                <div>
                                    <strong>Rénovation École Primaire</strong>
                                    <div class="text-muted small">Réf: CH-2024-012</div>
                                </div>
                            </div>
                        </td>
                        <td>Éducation Nationale</td>
                        <td>Sophie Bernard</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 6px; width: 80px;">
                                    <div class="progress-bar bg-primary" style="width: 32%"></div>
                                </div>
                                <span class="small">32%</span>
                            </div>
                        </td>
                        <td>15/12/2024</td>
                        <td>420K€</td>
                        <td><span class="status-badge status-active">Démarrage</span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"><i class="fas fa-eye"></i></button>
                                <button class="btn btn-outline-secondary"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Affichage de 4 chantiers sur 12
            </div>
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Précédent</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Suivant</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'activité
    const ctx = document.getElementById('activityChart').getContext('2d');
    const activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [{
                label: 'Heures travaillées',
                data: [420, 450, 480, 520, 490, 210, 180],
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Chantiers actifs',
                data: [8, 9, 10, 12, 11, 5, 3],
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Animation des cartes au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
            }
        });
    }, observerOptions);

    // Observer les cartes
    document.querySelectorAll('.dashboard-card').forEach(card => {
        observer.observe(card);
    });

    // Gestion des boutons d'export
    document.querySelectorAll('.btn-primary').forEach(btn => {
        if (btn.textContent.includes('Exporter')) {
            btn.addEventListener('click', function() {
                const toast = document.createElement('div');
                toast.className = 'position-fixed bottom-0 end-0 p-3';
                toast.style.zIndex = '11';
                toast.innerHTML = `
                    <div class="toast show" role="alert">
                        <div class="toast-header">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <strong class="me-auto">Export réussi</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            Le rapport a été exporté avec succès.
                        </div>
                    </div>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            });
        }
    });
});
</script>

<style>
.animate__animated {
    animation-duration: 0.6s;
}

.timeline-item {
    position: relative;
    padding-left: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item:last-child:before {
    height: 50%;
}

.timeline-icon {
    position: relative;
    z-index: 1;
}

.progress-bar {
    border-radius: 4px;
}

.table th {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom-width: 2px;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.chart-container {
    position: relative;
}
</style>
@endsection
@endsection