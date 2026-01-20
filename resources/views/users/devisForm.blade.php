@extends('layouts.apps')

@section('title', 'Devis — '.$chantier->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- EN-TÊTE --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Devis</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                        Devis du chantier
                    </h1>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            <i class="bi bi-building me-1"></i>{{ $chantier->nom }}
                        </span>
                        @if($chantier->adresse)
                        <span class="text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>{{ $chantier->adresse }}
                        </span>
                        @endif
                    </div>
                </div>
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createDevisModal">
                    <i class="bi bi-plus-circle me-2"></i>Créer un devis
                </button>
            </div>
        </div>
    </div>

    {{-- STATISTIQUES RAPIDES --}}
    @if($devisList->isNotEmpty())
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                                <i class="bi bi-file-earmark-text fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Total Devis</p>
                            <h3 class="mb-0 fw-bold">{{ $devisList->count() }}</h3>
                            <small class="text-muted">documents</small>
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
                                <i class="bi bi-cash-stack fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Montant Total</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($devisList->sum('montant'), 0, ',', ' ') }}</h3>
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
                            <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                                <i class="bi bi-calculator fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1 small">Montant Moyen</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($devisList->avg('montant'), 0, ',', ' ') }}</h3>
                            <small class="text-muted">FCFA</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TABLE DES DEVIS --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        <i class="bi bi-list-ul text-primary me-2"></i>
                        Liste des devis
                    </h5>
                    <p class="text-muted small mb-0">Gérez tous les devis de ce chantier</p>
                </div>
                @if($devisList->isNotEmpty())
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-success" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>Imprimer
                    </button>
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-file-earmark-excel me-1"></i>Exporter
                    </button>
                </div>
                @endif
            </div>
        </div>

        <div class="card-body p-0">
            @if($devisList->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 text-muted small fw-semibold">
                                    <i class="bi bi-hash me-1"></i>RÉFÉRENCE
                                </th>
                                <th class="px-4 py-3 text-muted small fw-semibold">
                                    <i class="bi bi-file-text me-1"></i>INTITULÉ
                                </th>
                                <th class="px-4 py-3 text-muted small fw-semibold text-end">
                                    <i class="bi bi-cash me-1"></i>MONTANT
                                </th>
                                <th class="px-4 py-3 text-muted small fw-semibold">
                                    <i class="bi bi-calendar-event me-1"></i>DATE ÉMISSION
                                </th>
                                <th class="px-4 py-3 text-muted small fw-semibold text-center">
                                    <i class="bi bi-gear me-1"></i>ACTIONS
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devisList as $index => $devi)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 35px; height: 35px; font-size: 0.8rem; font-weight: 600;">
                                                {{ $index + 1 }}
                                            </div>
                                            <span class="fw-bold text-primary">{{ $devi->reference }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-truncate" style="max-width: 350px;" title="{{ $devi->intitule }}">
                                            {{ $devi->intitule }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <strong class="text-dark fs-5">{{ number_format($devi->montant, 0, ',', ' ') }}</strong>
                                        <div><small class="text-muted">FCFA</small></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 text-muted me-2"></i>
                                            <div>
                                                <div>{{ \Carbon\Carbon::parse($devi->date_emission)->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($devi->date_emission)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#importDevisModal"
                                                    data-devis-id="{{ $devi->id }}"
                                                    title="Importer Excel">
                                                <i class="bi bi-file-earmark-excel"></i>
                                            </button>
                                            <button class="btn btn-outline-info" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success" title="Télécharger PDF">
                                                <i class="bi bi-download"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light border-top">
                            <tr>
                                <th colspan="2" class="px-4 py-3 text-end fw-semibold">
                                    <i class="bi bi-calculator me-2"></i>TOTAL GÉNÉRAL :
                                </th>
                                <th class="px-4 py-3 text-end">
                                    <strong class="text-primary fs-4">{{ number_format($devisList->sum('montant'), 0, ',', ' ') }}</strong>
                                    <div><small class="text-muted">FCFA</small></div>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="py-5">
                        <i class="bi bi-file-earmark-text text-muted" style="font-size: 5rem;"></i>
                        <h5 class="text-muted mt-4 mb-2">Aucun devis disponible</h5>
                        <p class="text-muted mb-4">Commencez par créer votre premier devis pour ce chantier</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDevisModal">
                            <i class="bi bi-plus-circle me-2"></i>Créer mon premier devis
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ===================== MODAL CREATION DEVIS ===================== --}}
<div class="modal fade" id="createDevisModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-gradient bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Créer un nouveau devis
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('devis.store', $chantier->id) }}">
                @csrf
                <input type="hidden" name="chantier_id" value="{{ $chantier->id }}">

                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Chantier :</strong> {{ $chantier->nom }}
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-hash text-primary me-1"></i>
                                Référence <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="reference" class="form-control form-control-lg" 
                                   placeholder="Ex: DEV-2026-001" required value="{{ old('reference') }}">
                            @error('reference')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                Date d'émission <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date_emission" class="form-control form-control-lg"
                                   value="{{ old('date_emission', now()->format('Y-m-d')) }}" required>
                            @error('date_emission')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-file-text text-primary me-1"></i>
                                Intitulé du devis <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="intitule" class="form-control form-control-lg"
                                   value="{{ old('intitule', 'Devis travaux - ' . $chantier->nom) }}" 
                                   placeholder="Décrivez le devis..." required>
                            @error('intitule')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-cash-stack text-primary me-1"></i>
                                Montant total <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" name="montant" step="0.01" min="0"
                                       class="form-control" placeholder="0" required value="{{ old('montant') }}">
                                <span class="input-group-text">FCFA</span>
                            </div>
                            @error('montant')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-4">
                        <i class="bi bi-check-circle me-2"></i>Créer le devis
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ===================== MODAL IMPORT EXCEL ===================== --}}
<div class="modal fade" id="importDevisModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg" method="POST"
              action="{{ route('devis.import') }}"
              enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="devis_id" id="importDevisId">

            <div class="modal-header bg-gradient bg-success text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-earmark-excel me-2"></i>Importer un fichier Excel
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="alert alert-info border-0 mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Format accepté :</strong> .xls, .xlsx
                </div>

                <label class="form-label fw-semibold">
                    <i class="bi bi-upload text-success me-1"></i>
                    Sélectionnez votre fichier
                </label>
                <input type="file" name="fichier" class="form-control form-control-lg" 
                       accept=".xls,.xlsx" required>
                
                <div class="mt-3 p-3 bg-light rounded">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Astuce :</strong> Assurez-vous que votre fichier Excel contient les colonnes nécessaires avant l'import.
                    </small>
                </div>
            </div>

            <div class="modal-footer border-0 bg-light py-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Annuler
                </button>
                <button type="submit" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-upload me-2"></i>Importer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- STYLES PERSONNALISÉS --}}
<style>
    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.04);
    }

    .bg-gradient {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    }

    .bg-gradient.bg-success {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
    }

    @media print {
        .btn, .modal, .no-print {
            display: none !important;
        }
    }
</style>
@endsection

@push('scripts')
<script>
// Passer l'ID du devis au modal d'import
document.getElementById('importDevisModal')
    .addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('importDevisId').value = button.dataset.devisId;
    });

// Réouvrir le modal de création en cas d'erreur
@if ($errors->any())
    new bootstrap.Modal(document.getElementById('createDevisModal')).show();
@endif

// Initialiser les tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush