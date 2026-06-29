@extends('layouts.apps')

@section('title', 'Dépenses — Gestionnaire')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- En-tête de page avec statistiques -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">💰 Gestion des Dépenses</h1>
                    <p class="text-muted mb-0">Suivez et gérez toutes vos dépenses en temps réel</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepenseModal">
                        <i class="bi bi-plus-circle"></i> Nouvelle Dépense
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    @if($depenses->count() > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Dépenses</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($depenses->sum('montant'), 0, '.', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                                <i class="bi bi-receipt fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Nombre de Dépenses</p>
                            <h4 class="mb-0 fw-bold">{{ $depenses->total() }}</h4>
                            <small class="text-muted">transactions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                                <i class="bi bi-calculator fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Dépense Moyenne</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($depenses->avg('montant'), 0, '.', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                                <i class="bi bi-file-earmark-check fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Avec Justificatif</p>
                            <h4 class="mb-0 fw-bold">{{ $depenses->filter(fn($d) => $d->justificatif)->count() }}</h4>
                            <small class="text-muted">sur {{ $depenses->count() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('depenses.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Date de début</label>
                    <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Date de fin</label>
                    <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Catégorie</label>
                    <select name="categorie" class="form-select">
                        <option value="">Toutes les catégories</option>
                        <option value="Matériaux" {{ request('categorie') == 'Matériaux' ? 'selected' : '' }}>Matériaux</option>
                        <option value="Main d'œuvre" {{ request('categorie') == "Main d'œuvre" ? 'selected' : '' }}>Main d'œuvre</option>
                        <option value="Transport" {{ request('categorie') == 'Transport' ? 'selected' : '' }}>Transport</option>
                        <option value="Équipement" {{ request('categorie') == 'Équipement' ? 'selected' : '' }}>Équipement</option>
                        <option value="Autre" {{ request('categorie') == 'Autre' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel"></i> Filtrer
                    </button>
                    <a href="{{ route('depenses.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des dépenses -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-list-ul text-primary me-2"></i>
                    Liste des Dépenses
                </h5>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                    {{ $depenses->total() }} résultat(s)
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 text-muted small fw-semibold">#</th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-calendar-event me-1"></i>Date
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-tag me-1"></i>Catégorie
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-file-text me-1"></i>Description
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-end">
                                <i class="bi bi-cash me-1"></i>Montant
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-person me-1"></i>Responsable
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center">
                                <i class="bi bi-paperclip me-1"></i>Justificatif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depenses as $index => $depense)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <span class="badge bg-light text-dark">
                                        {{ $index + 1 + ($depenses->currentPage() - 1) * $depenses->perPage() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge 
                                        @if($depense->categorie == 'Matériaux') bg-primary
                                        @elseif($depense->categorie == "Main d'œuvre") bg-success
                                        @elseif($depense->categorie == 'Transport') bg-info
                                        @elseif($depense->categorie == 'Équipement') bg-warning
                                        @else bg-secondary
                                        @endif
                                        bg-opacity-10 
                                        @if($depense->categorie == 'Matériaux') text-primary
                                        @elseif($depense->categorie == "Main d'œuvre") text-success
                                        @elseif($depense->categorie == 'Transport') text-info
                                        @elseif($depense->categorie == 'Équipement') text-warning
                                        @else text-secondary
                                        @endif
                                        px-3 py-2">
                                        {{ $depense->categorie }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $depense->description }}">
                                        {{ $depense->description }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <strong class="text-dark">{{ number_format($depense->montant, 0, '.', ' ') }}</strong>
                                    <small class="text-muted ms-1">FCFA</small>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <span>{{ $depense->responsable }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($depense->justificatif)
                                        <a href="{{ asset('storage/' . $depense->justificatif) }}" 
                                           target="_blank" 
                                           class="d-inline-block position-relative"
                                           data-bs-toggle="tooltip"
                                           title="Voir le justificatif">
                                            <img src="{{ asset('storage/' . $depense->justificatif) }}" 
                                                alt="Justificatif" 
                                                class="rounded shadow-sm"
                                                style="width: 60px; height: 60px; object-fit: cover; transition: transform 0.2s;"
                                                onmouseover="this.style.transform='scale(1.1)'"
                                                onmouseout="this.style.transform='scale(1)'">
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                <i class="bi bi-check"></i>
                                            </span>
                                        </a>    
                                    @else
                                        <span class="badge bg-light text-muted px-3 py-2">
                                            <i class="bi bi-x-circle me-1"></i>Non fourni
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        <p class="mb-0">Aucune dépense trouvée</p>
                                        <small>Commencez par ajouter votre première dépense</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($depenses->count() > 0)
                        <tfoot class="table-light border-top">
                            <tr>
                                <th colspan="4" class="px-4 py-3 text-end fw-semibold">
                                    <i class="bi bi-calculator me-2"></i>TOTAL GÉNÉRAL :
                                </th>
                                <th class="px-4 py-3 text-end">
                                    <strong class="text-primary fs-5">{{ number_format($depenses->sum('montant'), 0, '.', ' ') }}</strong>
                                    <small class="text-muted ms-1">FCFA</small>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($depenses->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Affichage de {{ $depenses->firstItem() }} à {{ $depenses->lastItem() }} sur {{ $depenses->total() }} résultats
                </div>
                <div>
                    {{ $depenses->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="modal fade" id="addDepenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-cash-coin text-primary me-2"></i>Nouvelle Dépense
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('depenses.stores') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="chantier_id" value="{{ $depense->chantier_id }}">

                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Planning --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Planning <span class="text-danger">*</span></label>
                                <select name="planning_id" class="form-select" required>
                                    <option value="">Sélectionnez un planning</option>
                                    @foreach ($planning as $plan)
                                        <option value="{{ $plan->id }}">
                                            {{ $plan->activite }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Catégorie --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Catégorie</label>
                                <select name="categorie" class="form-select" required>
                                    <option value="">Sélectionnez</option>
                                    <option value="Matériaux">Matériaux</option>
                                    <option value="Main d'œuvre">Main d'œuvre</option>
                                    <option value="Transport">Transport</option>
                                    <option value="Équipement">Équipement</option>
                                    <option value="Autre">Autre</option>
                                </select>
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>

                            {{-- Montant --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Montant</label>
                                <input type="number" name="montant" class="form-control" required>
                            </div>

                            {{-- Responsable --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Responsable</label>
                                <input type="text" name="responsable" class="form-control">
                            </div>

                            {{-- Date --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date</label>
                                <input type="date" name="date_depense" class="form-control">
                            </div>

                            {{-- Justificatif --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Justificatif</label>
                                <input type="file" name="justificatif" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animations et effets */
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .table tbody tr {
        transition: background-color 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .px-4 {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }
</style>

<!-- Bootstrap Icons (si pas déjà inclus) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<!-- Activer les tooltips Bootstrap -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection