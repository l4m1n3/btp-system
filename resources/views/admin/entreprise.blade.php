@extends('layouts.app')

@section('title', 'Gestion des Entreprises')

@section('content')
<div class="container-fluid px-4 py-4">
    
    {{-- EN-TÊTE --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="bi bi-building text-primary me-2"></i>
                        Gestion des Entreprises
                    </h1>
                    <p class="text-muted mb-0">Gérez vos partenaires, clients et fournisseurs</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Imprimer
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                        <i class="bi bi-plus-circle me-2"></i>Nouvelle entreprise
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIQUES --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-building fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small fw-semibold">TOTAL ENTREPRISES</p>
                            <h3 class="mb-0 fw-bold">{{ $companies->total() }}</h3>
                            <small class="text-success">
                                <i class="bi bi-graph-up-arrow me-1"></i>+8% ce mois
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small fw-semibold">ACTIFS</p>
                            <h3 class="mb-0 fw-bold">{{ $companies->where('actif', true)->count() }}</h3>
                            <small class="text-muted">entreprises</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-gem fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small fw-semibold">ABONNEMENTS</p>
                            <h3 class="mb-0 fw-bold">{{ $companies->whereNotNull('abonnement')->count() }}</h3>
                            <small class="text-muted">actifs</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-exclamation-triangle fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small fw-semibold">INACTIFS</p>
                            <h3 class="mb-0 fw-bold">{{ $companies->where('actif', false)->count() }}</h3>
                            <small class="text-muted">entreprises</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTRES ET RECHERCHE --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-semibold">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Nom, email, téléphone..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-semibold">Abonnement</label>
                    <select name="abonnement" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="gratuit" {{ request('abonnement') == 'gratuit' ? 'selected' : '' }}>Gratuit</option>
                        <option value="mensuel" {{ request('abonnement') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                        <option value="annuel" {{ request('abonnement') == 'annuel' ? 'selected' : '' }}>Annuel</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-semibold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel me-1"></i>Filtrer
                    </button>
                    <a href="" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLEAU DES ENTREPRISES --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        <i class="bi bi-list-ul text-primary me-2"></i>
                        Liste des Entreprises
                    </h5>
                    <p class="text-muted small mb-0">
                        {{ $companies->total() }} entreprise(s) enregistrée(s)
                    </p>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                    {{ $companies->count() }} affichée(s)
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="companiesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" width="50">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-building me-1"></i>ENTREPRISE
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-envelope me-1"></i>EMAIL
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-telephone me-1"></i>CONTACT
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-gem me-1"></i>ABONNEMENT
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-calendar-check me-1"></i>PÉRIODE
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
                        @forelse($companies as $patron)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="form-check-input select-item">
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="company-avatar bg-primary bg-opacity-10 text-primary me-3">
                                        {{ strtoupper(substr($patron->nom, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $patron->nom }}</h6>
                                        <small class="text-muted">ID: #{{ $patron->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-envelope text-muted me-2"></i>
                                    <span>{{ $patron->email ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-phone text-muted me-2"></i>
                                    <span>{{ $patron->telephone }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($patron->abonnement)
                                <span class="badge 
                                    @if($patron->abonnement == 'gratuit') bg-secondary
                                    @elseif($patron->abonnement == 'mensuel') bg-info
                                    @else bg-warning
                                    @endif
                                    bg-opacity-10 
                                    @if($patron->abonnement == 'gratuit') text-secondary
                                    @elseif($patron->abonnement == 'mensuel') text-info
                                    @else text-warning
                                    @endif
                                    px-3 py-2">
                                    <i class="bi bi-gem me-1"></i>{{ ucfirst($patron->abonnement) }}
                                </span>
                                @else
                                <span class="text-muted">Aucun</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($patron->date_debut && $patron->date_fin)
                                <div>
                                    <div class="small">
                                        <i class="bi bi-calendar-check text-success me-1"></i>
                                        {{ \Carbon\Carbon::parse($patron->date_debut)->format('d/m/Y') }}
                                    </div>
                                    <div class="small">
                                        <i class="bi bi-calendar-x text-danger me-1"></i>
                                        {{ \Carbon\Carbon::parse($patron->date_fin)->format('d/m/Y') }}
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($patron->actif)
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>Actif
                                </span>
                                @else
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i>Inactif
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('entreprise.details', $patron->id) }}" 
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="Voir détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-outline-secondary"
                                            data-bs-toggle="tooltip"
                                            title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
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
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
                                    <h5 class="text-muted mt-3 mb-2">Aucune entreprise trouvée</h5>
                                    <p class="text-muted mb-3">Commencez par ajouter votre première entreprise</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                                        <i class="bi bi-plus-circle me-2"></i>Ajouter une entreprise
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
        @if($companies->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted small">
                    Affichage de <strong>{{ $companies->firstItem() }}</strong> à <strong>{{ $companies->lastItem() }}</strong> 
                    sur <strong>{{ $companies->total() }}</strong> entreprises
                </div>
                <div>
                    {{ $companies->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endif
    </div>

</div>

{{-- MODAL AJOUT ENTREPRISE --}}
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle entreprise
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('entreprise.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-building text-primary me-1"></i>
                                Nom de l'entreprise <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-lg" name="nom" 
                                   placeholder="Ex: BTP Solutions" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-telephone text-primary me-1"></i>
                                Téléphone <span class="text-danger">*</span>
                            </label>
                            <input type="tel" class="form-control form-control-lg" name="telephone" 
                                   placeholder="+227 XX XX XX XX" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-envelope text-primary me-1"></i>
                                Email
                            </label>
                            <input type="email" class="form-control form-control-lg" name="email"
                                   placeholder="contact@entreprise.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-gem text-primary me-1"></i>
                                Type d'abonnement
                            </label>
                            <select class="form-select form-select-lg" name="abonnement">
                                <option value="">Sélectionner...</option>
                                <option value="gratuit">💼 Gratuit</option>
                                <option value="mensuel">📅 Mensuel</option>
                                <option value="annuel">⭐ Annuel</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-check text-primary me-1"></i>
                                Date de début
                            </label>
                            <input type="date" class="form-control form-control-lg" name="date_debut" 
                                   value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-x text-primary me-1"></i>
                                Date de fin
                            </label>
                            <input type="date" class="form-control form-control-lg" name="date_fin"
                                   value="{{ now()->addYear()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-check-circle me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- STYLES PERSONNALISÉS --}}
<style>
    .stat-icon {
        width: 60px;
        height: 60px;
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
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }

    .company-avatar {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
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

    @media print {
        .btn, .modal, .card-header .badge {
            display: none !important;
        }
    }
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Select all checkboxes
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.select-item').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
});
</script>
@endsection
@endsection