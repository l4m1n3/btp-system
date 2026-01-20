@extends('layouts.apps')

@section('title', 'Dashboard BTP')

@section('content')
<div class="container-fluid px-4 py-4">
    
    {{-- EN-TÊTE DU DASHBOARD --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>
                        Tableau de bord
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-person-circle me-1"></i>
                        Bienvenue, <strong>{{ Auth::user()->name ?? 'Administrateur' }}</strong>. 
                        Voici vos statistiques en temps réel.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise me-2"></i>Actualiser
                    </button>
                    <button class="btn btn-primary" onclick="exportDashboard()">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Exporter rapport
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIQUES RAPIDES --}}
    <div class="row g-3 mb-4">
        {{-- Total Chantiers --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold">TOTAL CHANTIERS</p>
                            <h2 class="mb-0 fw-bold">{{ $Nbrchantiers }}</h2>
                            <small class="text-success">
                                <i class="bi bi-graph-up-arrow me-1"></i>+12% ce mois
                            </small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-building fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chantiers en cours --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold">EN COURS</p>
                            <h2 class="mb-0 fw-bold">{{ $chantiersEnCours ?? $Nbrchantiers }}</h2>
                            <small class="text-info">
                                <i class="bi bi-clock-history me-1"></i>Actifs
                            </small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-hammer fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chantiers terminés --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold">TERMINÉS</p>
                            <h2 class="mb-0 fw-bold">{{ $chantiersTermines ?? 0 }}</h2>
                            <small class="text-success">
                                <i class="bi bi-check-circle me-1"></i>Complétés
                            </small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check2-circle fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Abonnement --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold">ABONNEMENT</p>
                            <h2 class="mb-0 fw-bold">Premium</h2>
                            <small class="text-warning">
                                <i class="bi bi-star-fill me-1"></i>Plan actif
                            </small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-gem fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLEAU DES CHANTIERS --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        <i class="bi bi-list-ul text-primary me-2"></i>
                        Chantiers associés
                    </h5>
                    <p class="text-muted small mb-0">Gérez tous vos chantiers en un seul endroit</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addChantierModal">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau chantier
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-building me-1"></i>NOM
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-file-text me-1"></i>DESCRIPTION
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-geo-alt me-1"></i>ADRESSE
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-end">
                                <i class="bi bi-cash-stack me-1"></i>BUDGET
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center">
                                <i class="bi bi-flag me-1"></i>STATUT
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center">
                                <i class="bi bi-gear me-1"></i>ACTIONS
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($chantiers as $chantier)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="form-check-input selectItem">
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="chantier-avatar bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $chantier->nom }}</h6>
                                        <small class="text-muted">ID: #{{ $chantier->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-truncate" style="max-width: 250px;" title="{{ $chantier->description }}">
                                    {{ $chantier->description ?? 'Aucune description' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill text-muted me-2"></i>
                                    <span>{{ $chantier->adresse ?? 'Non spécifiée' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end">
                                @if($chantier->budget_total)
                                    <strong class="text-dark">{{ number_format($chantier->budget_total, 0, ',', ' ') }}</strong>
                                    <div><small class="text-muted">FCFA</small></div>
                                @else
                                    <span class="text-muted">Non défini</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($chantier->statut === 'en_cours')
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2">
                                        <i class="bi bi-clock-history me-1"></i>En cours
                                    </span>
                                @elseif($chantier->statut === 'termine')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i>Terminé
                                    </span>
                                @elseif($chantier->statut === 'annule')
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i>Annulé
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                        <i class="bi bi-question-circle me-1"></i>Non défini
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('devis.form', $chantier->id) }}" 
                                       class="btn btn-outline-dark"
                                       data-bs-toggle="tooltip"
                                       title="Devis">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    <a href="{{ route('rapport.depenses.entreprise', $chantier->id) }}" 
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="Voir détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-danger"
                                            data-bs-toggle="tooltip"
                                            title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                    <h5 class="text-muted mb-2">Aucun chantier</h5>
                                    <p class="text-muted mb-3">Commencez par créer votre premier chantier</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addChantierModal">
                                        <i class="bi bi-plus-circle me-2"></i>Créer un chantier
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($chantiers->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted small">
                    Affichage de <strong>{{ $chantiers->firstItem() }}</strong> à <strong>{{ $chantiers->lastItem() }}</strong> 
                    sur <strong>{{ $chantiers->total() }}</strong> chantiers
                </div>
                <div>
                    {{ $chantiers->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>

</div>

{{-- MODAL AJOUTER CHANTIER --}}
<div class="modal fade" id="addChantierModal" tabindex="-1" aria-labelledby="addChantierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('chantiers.store') }}" method="POST" id="addChantierForm">
            @csrf
            <input type="hidden" name="entreprise_id" value="{{ $patron->id ?? '' }}">

            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" id="addChantierModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Nouveau chantier
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        {{-- Colonne gauche --}}
                        <div class="col-md-8">
                            <div class="mb-4">
                                <label for="nom" class="form-label fw-semibold">
                                    <i class="bi bi-building text-primary me-1"></i>
                                    Nom du chantier <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="nom" name="nom" 
                                       placeholder="Ex: Construction Résidence Azur" required autofocus>
                                @error('nom') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold">
                                    <i class="bi bi-file-text text-primary me-1"></i>
                                    Description
                                </label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="5" placeholder="Détails du projet, spécifications, particularités..."></textarea>
                                @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Colonne droite --}}
                        <div class="col-md-4">
                            <div class="mb-4">
                                <label for="budget_total" class="form-label fw-semibold">
                                    <i class="bi bi-cash-stack text-primary me-1"></i>
                                    Budget total <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control" id="budget_total" name="budget_total" 
                                           min="0" step="1000" placeholder="25000000" required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('budget_total') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="adresse" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-primary me-1"></i>
                                    Adresse
                                </label>
                                <input type="text" class="form-control" id="adresse" name="adresse" 
                                       placeholder="Quartier, ville...">
                                @error('adresse') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="statut" class="form-label fw-semibold">
                                    <i class="bi bi-flag text-primary me-1"></i>
                                    Statut <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="statut" name="statut" required>
                                    <option value="" disabled selected>Choisir...</option>
                                    <option value="en_cours">🔨 En cours</option>
                                    <option value="termine">✅ Terminé</option>
                                    <option value="annule">❌ Annulé</option>
                                    <option value="en_attente">⏳ En attente</option>
                                </select>
                                @error('statut') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-check-circle me-2"></i>Créer le chantier
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- STYLES PERSONNALISÉS --}}
<style>
    .stat-icon {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }

    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }

    .chantier-avatar {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.25rem;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.04);
    }

    .bg-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }

    .chart-container {
        position: relative;
    }

    @media (max-width: 768px) {
        .stat-icon {
            width: 50px;
            height: 50px;
        }
    }
</style>

{{-- SCRIPTS --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique d'activité
    const ctx = document.getElementById('activityChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Heures travaillées',
                    data: [420, 450, 480, 520, 490, 210, 180],
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2
                }, {
                    label: 'Chantiers actifs',
                    data: [8, 9, 10, 12, 11, 5, 3],
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false,
                            color: 'rgba(0,0,0,0.05)'
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
    }

    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Select all checkboxes
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.selectItem').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
});

// Export dashboard
function exportDashboard() {
    const toastHTML = `
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
            <div class="toast show" role="alert" id="exportToast">
                <div class="toast-header bg-success text-white border-0">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong class="me-auto">Export réussi</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    Le rapport du dashboard a été exporté avec succès.
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHTML);
    
    setTimeout(() => {
        const toastEl = document.getElementById('exportToast');
        if (toastEl) {
            toastEl.parentElement.remove();
        }
    }, 3000);
    
    // Simuler l'export (vous pouvez remplacer par une vraie fonction d'export)
    window.print();
}
</script>

{{-- Bootstrap Icons (si pas déjà inclus) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@endsection
@endsection