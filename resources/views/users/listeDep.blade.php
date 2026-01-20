@extends('layouts.apps')

@section('title', 'Dépenses — Gestionnaire')

@section('content')
<div class="container-fluid px-4 py-4">
    
    {{-- EN-TÊTE --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="bi bi-search text-primary me-2"></i>
                        Rechercher des Dépenses
                    </h1>
                    <p class="text-muted mb-0">Filtrez et analysez vos dépenses par chantier et période</p>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIQUES RAPIDES (si résultats) --}}
    @if($depenses->count() > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Trouvé</p>
                            <h4 class="mb-0 fw-bold">{{ number_format($depenses->sum('montant'), 0, '.', ' ') }}</h4>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                                <i class="bi bi-receipt-cutoff fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Nombre de Résultats</p>
                            <h4 class="mb-0 fw-bold">{{ $depenses->total() }}</h4>
                            <small class="text-muted">dépenses</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                                <i class="bi bi-graph-up-arrow fs-4"></i>
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
    </div>
    @endif

    {{-- FORMULAIRE DE RECHERCHE --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-gradient bg-primary text-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-funnel me-2"></i>Critères de Recherche
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('depenses.search') }}" method="POST" class="row g-4">
                @csrf   
                
                {{-- Sélection du chantier --}}
                <div class="col-md-6">
                    <label for="chantier_id" class="form-label fw-semibold">
                        <i class="bi bi-building text-primary me-1"></i>Chantier
                    </label>
                    <select name="chantier_id" id="chantier_id" class="form-select form-select-lg">
                        <option value="">🏗️ Tous les chantiers</option>
                        @foreach($chantiers as $chantier)
                            <option value="{{ $chantier->id }}" 
                                {{ request('chantier_id') == $chantier->id ? 'selected' : '' }}>
                                {{ $chantier->nom }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Sélectionnez un chantier spécifique ou laissez vide pour tous</small>
                </div>

                {{-- Période --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-calendar-range text-primary me-1"></i>Période
                    </label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" name="date_debut" id="date_debut" 
                                   class="form-control form-control-lg" 
                                   value="{{ request('date_debut') }}"
                                   placeholder="Date début">
                            <small class="text-muted">Du</small>
                        </div>
                        <div class="col-6">
                            <input type="date" name="date_fin" id="date_fin" 
                                   class="form-control form-control-lg" 
                                   value="{{ request('date_fin') }}"
                                   placeholder="Date fin">
                            <small class="text-muted">Au</small>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <a href="{{ route('depenses.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-arrow-clockwise me-2"></i>Réinitialiser
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-search me-2"></i>Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLEAU DES RÉSULTATS --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-table text-primary me-2"></i>Résultats de la Recherche
                </h5>
                @if($depenses->count() > 0)
                <div class="d-flex gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                        {{ $depenses->total() }} résultat(s)
                    </span>
                    <button class="btn btn-sm btn-outline-success" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Imprimer
                    </button>
                    <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                        <i class="bi bi-file-earmark-excel me-1"></i>Exporter
                    </button>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="depensesTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-building me-1"></i>Chantier
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-calendar-event me-1"></i>Date
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold">
                                <i class="bi bi-file-text me-1"></i>Description
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-end">
                                <i class="bi bi-cash me-1"></i>Montant
                            </th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center">
                                <i class="bi bi-paperclip me-1"></i>Justificatif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depenses as $depense)
                            <tr class="border-bottom">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $depense->chantier->nom }}</div>
                                            <small class="text-muted">{{ $depense->chantier->adresse ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</span>
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($depense->date_depense)->diffForHumans() }}</small>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $depense->description }}">
                                        {{ $depense->description }}
                                    </div>
                                    @if($depense->categorie)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary mt-1">
                                            {{ $depense->categorie }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="fw-bold text-dark fs-5">
                                        {{ number_format($depense->montant, 0, '.', ' ') }}
                                    </div>
                                    <small class="text-muted">FCFA</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($depense->justificatif)
                                        <a href="{{ asset('storage/' . $depense->justificatif) }}" 
                                           target="_blank" 
                                           class="d-inline-block position-relative"
                                           data-bs-toggle="tooltip"
                                           title="Cliquez pour agrandir">
                                            <img src="{{ asset('storage/' . $depense->justificatif) }}" 
                                                alt="Justificatif" 
                                                class="rounded shadow-sm hover-zoom"
                                                style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;">
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                                <i class="bi bi-check2"></i>
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
                                <td colspan="5" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                                        <h5 class="text-muted mb-2">Aucune dépense trouvée</h5>
                                        <p class="text-muted mb-3">Essayez de modifier vos critères de recherche</p>
                                        <a href="{{ route('depenses.index') }}" class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-clockwise me-2"></i>Réinitialiser les filtres
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                    @if($depenses->count() > 0)
                        <tfoot class="table-light border-top">
                            <tr>
                                <th colspan="3" class="px-4 py-3 text-end fw-semibold fs-5">
                                    <i class="bi bi-calculator me-2"></i>TOTAL GÉNÉRAL :
                                </th>
                                <th class="px-4 py-3 text-end">
                                    <div class="fw-bold text-primary fs-4">
                                        {{ number_format($depenses->sum('montant'), 0, '.', ' ') }}
                                    </div>
                                    <small class="text-muted">FCFA</small>
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
        
        {{-- PAGINATION --}}
        @if($depenses->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted small">
                    Affichage de <strong>{{ $depenses->firstItem() }}</strong> à <strong>{{ $depenses->lastItem() }}</strong> 
                    sur <strong>{{ $depenses->total() }}</strong> résultats
                </div>
                <div>
                    {{ $depenses->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

</div>

{{-- STYLES PERSONNALISÉS --}}
<style>
    .hover-zoom {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .hover-zoom:hover {
        transform: scale(1.15);
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        z-index: 10;
    }
    
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .table tbody tr {
        transition: background-color 0.2s;
    }
    
    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .bg-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }
    
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

{{-- SCRIPTS --}}
<script>
    // Initialiser les tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Export vers Excel (simple)
    function exportToExcel() {
        var table = document.getElementById('depensesTable');
        var html = table.outerHTML;
        var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        var downloadLink = document.createElement("a");
        downloadLink.href = url;
        downloadLink.download = 'depenses_' + new Date().toISOString().split('T')[0] + '.xls';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>

{{-- Bootstrap Icons (si pas déjà inclus) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

@endsection