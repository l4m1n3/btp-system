@extends('layouts.apps')

@section('title', 'Fiches journalières - ' . $chantier->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- EN-TÊTE --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}" class="text-decoration-none">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Fiches journalières</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="bi bi-journal-text text-warning me-2"></i>
                        Fiches journalières
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-building me-1"></i>
                        Chantier : <strong>{{ $chantier->nom }}</strong>
                        @if($chantier->adresse)
                            &mdash; <i class="bi bi-geo-alt me-1"></i>{{ $chantier->adresse }}
                        @endif
                    </p>
                </div>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#createFicheModal">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle fiche
                </button>
            </div>
        </div>
    </div>

    {{-- ALERTES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
 
    {{-- STATS RAPIDES --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-journal-text fs-2"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-semibold">TOTAL FICHES</p>
                        <h3 class="mb-0 fw-bold">{{ $fiches->total() }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-calendar-check fs-2"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-semibold">CE MOIS</p>
                        <h3 class="mb-0 fw-bold">
                            {{ $fiches->getCollection()->filter(fn($f) => $f->date >= now()->startOfMonth())->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-calendar-day fs-2"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0 small fw-semibold">AUJOURD'HUI</p>
                        <h3 class="mb-0 fw-bold">
                            {{ $fiches->getCollection()->filter(fn($f) => $f->date->isToday())->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLEAU --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-list-ul text-warning me-2"></i>
                Liste des fiches journalières
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3 text-muted small fw-semibold"><i class="bi bi-calendar me-1"></i>DATE</th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center"><i class="bi bi-people me-1"></i>PERSONNEL</th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center"><i class="bi bi-truck me-1"></i>MATÉRIEL</th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center"><i class="bi bi-box-seam me-1"></i>APPROS</th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center"><i class="bi bi-tools me-1"></i>TRAVAUX</th>
                            <th class="px-4 py-3 text-muted small fw-semibold"><i class="bi bi-chat-text me-1"></i>OBSERVATIONS</th>
                            <th class="px-4 py-3 text-muted small fw-semibold text-center"><i class="bi bi-gear me-1"></i>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fiches as $fiche)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="date-badge bg-warning bg-opacity-10 text-warning rounded p-2 text-center" style="min-width:50px">
                                        <div class="fw-bold lh-1">{{ $fiche->date->format('d') }}</div>
                                        <small class="text-uppercase" style="font-size:10px">{{ $fiche->date->translatedFormat('M') }}</small>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $fiche->date->translatedFormat('l') }}</div>
                                        <small class="text-muted">{{ $fiche->date->format('Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">{{ $fiche->personnel->count() }} ligne(s)</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ $fiche->materiel->count() }} engin(s)</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">{{ $fiche->appros->count() }} article(s)</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ $fiche->travaux->count() }} tâche(s)</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($fiche->observations)
                                    <div class="text-truncate" style="max-width:200px" title="{{ $fiche->observations }}">
                                        {{ $fiche->observations }}
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('fiches.show', [$chantier->id, $fiche->id]) }}"
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    {{-- Bouton modifier ouvre le modal edit --}}
                                    <button type="button"
                                            class="btn btn-outline-warning btn-edit"
                                            data-bs-toggle="tooltip"
                                            title="Modifier"
                                            data-fiche="{{ json_encode([
                                                'id'           => $fiche->id,
                                                'date'         => $fiche->date->format('Y-m-d'),
                                                'observations' => $fiche->observations,
                                                'evenements'   => $fiche->evenements,
                                                'personnel'    => $fiche->personnel,
                                                'materiel'     => $fiche->materiel,
                                                'appros'       => $fiche->appros,
                                                'travaux'      => $fiche->travaux,
                                            ]) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('fiches.destroy', [$chantier->id, $fiche->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Supprimer cette fiche ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-journal-x fs-1 text-muted d-block mb-3"></i>
                                <h5 class="text-muted mb-2">Aucune fiche journalière</h5>
                                <p class="text-muted mb-3">Commencez par créer la première fiche pour ce chantier</p>
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#createFicheModal">
                                    <i class="bi bi-plus-circle me-2"></i>Créer une fiche
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($fiches->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted small">
                    Affichage de <strong>{{ $fiches->firstItem() }}</strong>
                    à <strong>{{ $fiches->lastItem() }}</strong>
                    sur <strong>{{ $fiches->total() }}</strong> fiches
                </div>
                {{ $fiches->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>


{{-- ========================================================
     MODAL CRÉATION
======================================================== --}}
<div class="modal fade" id="createFicheModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <form action="{{ route('fiches.store', $chantier->id) }}" method="POST" id="createFicheForm">
            @csrf
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header text-white border-0 py-3" style="background: linear-gradient(135deg,#ffc107,#e0a800)">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-journal-plus me-2"></i>Nouvelle fiche journalière
                        <small class="fw-normal ms-2 opacity-75">— {{ $chantier->nom }}</small>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    {{-- Date + signatures --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar text-warning me-1"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-building me-1"></i>Site / Commune
                            </label>
                            <input type="text" name="site" class="form-control" placeholder="Ex: Commune de Niamey">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nom contrôleur</label>
                            <input type="text" name="controleur_nom" class="form-control" placeholder="Nom, prénom">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nom entreprise</label>
                            <input type="text" name="entreprise_nom" class="form-control" value="{{ $chantier->entreprises->nom ?? '' }}">
                        </div>
                    </div>

                    {{-- Tabs sections --}}
                    <ul class="nav nav-tabs mb-3" id="createTabs">
                        <li class="nav-item">
                            <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#c-personnel" type="button">
                                <i class="bi bi-people me-1"></i>I. Personnel
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#c-materiel" type="button">
                                <i class="bi bi-truck me-1"></i>II. Matériel
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#c-appros" type="button">
                                <i class="bi bi-box-seam me-1"></i>III. Approvisionnements
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#c-travaux" type="button">
                                <i class="bi bi-tools me-1"></i>IV. Travaux réalisés
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#c-observations" type="button">
                                <i class="bi bi-chat-text me-1"></i>V. Observations
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="createTabsContent">

                        {{-- I. PERSONNEL --}}
                        <div class="tab-pane fade show active" id="c-personnel">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Ajoutez les lignes de personnel présent sur le chantier</small>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="addRow('c-personnel-body', 'personnel')">
                                    <i class="bi bi-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-warning">
                                        <tr>
                                            <th style="width:50%">Désignation</th>
                                            <th class="text-center">Hommes</th>
                                            <th class="text-center">Femmes</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="c-personnel-body">
                                        @foreach(['Conducteur des travaux','Chef de chantier','Chef d\'équipe','Maçon','Ferrailleur','Menuisier-coffreur','Conducteur d\'engin','Chauffeur de camion','Chauffeur de véhicule de liaison','Magasinier','Manœuvre','Main d\'œuvre locale','Gardien'] as $poste)
                                        <tr>
                                            <td>
                                                <input type="text" name="personnel[][designation]" class="form-control form-control-sm" value="{{ $poste }}">
                                            </td>
                                            <td>
                                                <input type="number" name="personnel[][hommes]" class="form-control form-control-sm text-center" value="0" min="0">
                                            </td>
                                            <td>
                                                <input type="number" name="personnel[][femmes]" class="form-control form-control-sm text-center" value="0" min="0">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- II. MATÉRIEL --}}
                        <div class="tab-pane fade" id="c-materiel">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Listez le matériel utilisé sur le chantier</small>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="addRow('c-materiel-body', 'materiel')">
                                    <i class="bi bi-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-warning">
                                        <tr>
                                            <th style="width:50%">Désignation</th>
                                            <th class="text-center">Nombre</th>
                                            <th class="text-center">État</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="c-materiel-body">
                                        @foreach(['Camion-benne','Camion-citerne','Citerne / cuve','Véhicule de liaison','Matériel topo','Compacteur','Vibreur','Brouette','Pelle','Pioche','Barre à mine'] as $engin)
                                        <tr>
                                            <td>
                                                <input type="text" name="materiel[][designation]" class="form-control form-control-sm" value="{{ $engin }}">
                                            </td>
                                            <td>
                                                <input type="number" name="materiel[][nombre]" class="form-control form-control-sm text-center" value="0" min="0">
                                            </td>
                                            <td>
                                                <select name="materiel[][etat]" class="form-select form-select-sm">
                                                    <option value="">—</option>
                                                    <option value="bon">Bon</option>
                                                    <option value="moyen">Moyen</option>
                                                    <option value="mauvais">Mauvais</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- III. APPROVISIONNEMENTS --}}
                        <div class="tab-pane fade" id="c-appros">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Saisissez les matériaux réceptionnés aujourd'hui</small>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="addRow('c-appros-body', 'appros')">
                                    <i class="bi bi-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-warning">
                                        <tr>
                                            <th style="width:40%">Désignation</th>
                                            <th class="text-center">Unité</th>
                                            <th class="text-center">Quantité</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="c-appros-body">
                                        @foreach([
                                            ['Sable','m3'],['Gravier','m3'],['Ciment','Tonne'],
                                            ['Fer à béton','paquets'],['Planches','unité'],['Autres','']
                                        ] as [$mat, $unit])
                                        <tr>
                                            <td>
                                                <input type="text" name="appros[][designation]" class="form-control form-control-sm" value="{{ $mat }}">
                                            </td>
                                            <td>
                                                <input type="text" name="appros[][unite]" class="form-control form-control-sm text-center" value="{{ $unit }}">
                                            </td>
                                            <td>
                                                <input type="number" name="appros[][quantite]" class="form-control form-control-sm text-center" value="0" min="0" step="0.01">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- IV. TRAVAUX RÉALISÉS --}}
                        <div class="tab-pane fade" id="c-travaux">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Décrivez les travaux effectués et leur niveau d'avancement</small>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="addRow('c-travaux-body', 'travaux')">
                                    <i class="bi bi-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-warning">
                                        <tr>
                                            <th style="width:50%">Désignation des travaux</th>
                                            <th class="text-center">Niveau d'avancement</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="c-travaux-body">
                                        <tr>
                                            <td><input type="text" name="travaux[][designation]" class="form-control form-control-sm" placeholder="Ex: Coulage fondations bloc A"></td>
                                            <td><input type="text" name="travaux[][niveau_avancement]" class="form-control form-control-sm text-center" placeholder="Ex: 75%"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- V. OBSERVATIONS --}}
                        <div class="tab-pane fade" id="c-observations">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                                        Événements particuliers survenus au cours de la journée
                                    </label>
                                    <textarea name="evenements" class="form-control" rows="5"
                                              placeholder="Incidents, accidents, intempéries, visites..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-chat-text text-warning me-1"></i>
                                        Observations / Recommandations
                                    </label>
                                    <textarea name="observations" class="form-control" rows="5"
                                              placeholder="Remarques générales, points d'attention..."></textarea>
                                </div>
                            </div>
                        </div>

                    </div>{{-- /tab-content --}}
                </div>{{-- /modal-body --}}

                <div class="modal-footer border-0 bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="bi bi-check-circle me-2"></i>Enregistrer la fiche
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- ========================================================
     MODAL MODIFICATION
======================================================== --}}
<div class="modal fade" id="editFicheModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <form id="editFicheForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header text-white border-0 py-3" style="background: linear-gradient(135deg,#0d6efd,#0a58ca)">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2"></i>Modifier la fiche journalière
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-calendar text-primary me-1"></i>Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="date" id="edit-date" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Site / Commune</label>
                            <input type="text" name="site" id="edit-site" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nom contrôleur</label>
                            <input type="text" name="controleur_nom" id="edit-controleur" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nom entreprise</label>
                            <input type="text" name="entreprise_nom" id="edit-entreprise" class="form-control">
                        </div>
                    </div>

                    <ul class="nav nav-tabs mb-3" id="editTabs">
                        <li class="nav-item">
                            <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#e-personnel" type="button">
                                <i class="bi bi-people me-1"></i>I. Personnel
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#e-materiel" type="button">
                                <i class="bi bi-truck me-1"></i>II. Matériel
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#e-appros" type="button">
                                <i class="bi bi-box-seam me-1"></i>III. Approvisionnements
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#e-travaux" type="button">
                                <i class="bi bi-tools me-1"></i>IV. Travaux réalisés
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#e-observations" type="button">
                                <i class="bi bi-chat-text me-1"></i>V. Observations
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- I. PERSONNEL --}}
                        <div class="tab-pane fade show active" id="e-personnel">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('e-personnel-body', 'personnel')">
                                    <i class="bi bi-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width:50%">Désignation</th>
                                            <th class="text-center">Hommes</th>
                                            <th class="text-center">Femmes</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="e-personnel-body"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- II. MATÉRIEL --}}
                        <div class="tab-pane fade" id="e-materiel">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('e-materiel-body', 'materiel')">
                                    <i class="bi bi-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width:50%">Désignation</th>
                                            <th class="text-center">Nombre</th>
                                            <th class="text-center">État</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="e-materiel-body"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- III. APPROVISIONNEMENTS --}}
                        <div class="tab-pane fade" id="e-appros">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('e-appros-body', 'appros')">
                                    <i class="bi bi-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width:40%">Désignation</th>
                                            <th class="text-center">Unité</th>
                                            <th class="text-center">Quantité</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="e-appros-body"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- IV. TRAVAUX --}}
                        <div class="tab-pane fade" id="e-travaux">
                            <div class="d-flex justify-content-end mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow('e-travaux-body', 'travaux')">
                                    <i class="bi bi-plus me-1"></i>Ajouter
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width:50%">Désignation des travaux</th>
                                            <th class="text-center">Niveau d'avancement</th>
                                            <th class="text-center" style="width:50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="e-travaux-body"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- V. OBSERVATIONS --}}
                        <div class="tab-pane fade" id="e-observations">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-exclamation-triangle text-primary me-1"></i>
                                        Événements particuliers
                                    </label>
                                    <textarea name="evenements" id="edit-evenements" class="form-control" rows="5"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="bi bi-chat-text text-primary me-1"></i>
                                        Observations / Recommandations
                                    </label>
                                    <textarea name="observations" id="edit-observations" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0 bg-light py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-2"></i>Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<style>
.stat-icon {
    width: 56px; height: 56px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 12px; flex-shrink: 0;
}
.table tbody tr:hover { background-color: rgba(255,193,7,0.04); }
</style>

@push('scripts')
<script>
// ── Ajouter une ligne vide selon la section ──────────────────────────────────
function addRow(tbodyId, type) {
    const tbody = document.getElementById(tbodyId);
    let html = '';

    if (type === 'personnel') {
        html = `<tr>
            <td><input type="text"   name="personnel[][designation]" class="form-control form-control-sm" placeholder="Désignation"></td>
            <td><input type="number" name="personnel[][hommes]"      class="form-control form-control-sm text-center" value="0" min="0"></td>
            <td><input type="number" name="personnel[][femmes]"      class="form-control form-control-sm text-center" value="0" min="0"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    } else if (type === 'materiel') {
        html = `<tr>
            <td><input type="text"   name="materiel[][designation]" class="form-control form-control-sm" placeholder="Désignation"></td>
            <td><input type="number" name="materiel[][nombre]"      class="form-control form-control-sm text-center" value="0" min="0"></td>
            <td><select name="materiel[][etat]" class="form-select form-select-sm">
                    <option value="">—</option>
                    <option value="bon">Bon</option>
                    <option value="moyen">Moyen</option>
                    <option value="mauvais">Mauvais</option>
                </select></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    } else if (type === 'appros') {
        html = `<tr>
            <td><input type="text"   name="appros[][designation]" class="form-control form-control-sm" placeholder="Matériau"></td>
            <td><input type="text"   name="appros[][unite]"       class="form-control form-control-sm text-center" placeholder="m3"></td>
            <td><input type="number" name="appros[][quantite]"    class="form-control form-control-sm text-center" value="0" min="0" step="0.01"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    } else if (type === 'travaux') {
        html = `<tr>
            <td><input type="text" name="travaux[][designation]"       class="form-control form-control-sm" placeholder="Description des travaux"></td>
            <td><input type="text" name="travaux[][niveau_avancement]" class="form-control form-control-sm text-center" placeholder="Ex: 50%"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    }

    tbody.insertAdjacentHTML('beforeend', html);
}

// ── Supprimer une ligne ──────────────────────────────────────────────────────
function removeRow(btn) {
    btn.closest('tr').remove();
}

// ── Peupler le modal d'édition ───────────────────────────────────────────────
function buildRow(type, data) {
    if (type === 'personnel') {
        return `<tr>
            <td><input type="text"   name="personnel[][designation]" class="form-control form-control-sm" value="${data.designation ?? ''}"></td>
            <td><input type="number" name="personnel[][hommes]"      class="form-control form-control-sm text-center" value="${data.hommes ?? 0}" min="0"></td>
            <td><input type="number" name="personnel[][femmes]"      class="form-control form-control-sm text-center" value="${data.femmes ?? 0}" min="0"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    } else if (type === 'materiel') {
        const etatOptions = ['', 'bon', 'moyen', 'mauvais'].map(v =>
            `<option value="${v}" ${data.etat === v ? 'selected' : ''}>${v === '' ? '—' : v.charAt(0).toUpperCase() + v.slice(1)}</option>`
        ).join('');
        return `<tr>
            <td><input type="text"   name="materiel[][designation]" class="form-control form-control-sm" value="${data.designation ?? ''}"></td>
            <td><input type="number" name="materiel[][nombre]"      class="form-control form-control-sm text-center" value="${data.nombre ?? 0}" min="0"></td>
            <td><select name="materiel[][etat]" class="form-select form-select-sm">${etatOptions}</select></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    } else if (type === 'appros') {
        return `<tr>
            <td><input type="text"   name="appros[][designation]" class="form-control form-control-sm" value="${data.designation ?? ''}"></td>
            <td><input type="text"   name="appros[][unite]"       class="form-control form-control-sm text-center" value="${data.unite ?? ''}"></td>
            <td><input type="number" name="appros[][quantite]"    class="form-control form-control-sm text-center" value="${data.quantite ?? 0}" min="0" step="0.01"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    } else if (type === 'travaux') {
        return `<tr>
            <td><input type="text" name="travaux[][designation]"       class="form-control form-control-sm" value="${data.designation ?? ''}"></td>
            <td><input type="text" name="travaux[][niveau_avancement]" class="form-control form-control-sm text-center" value="${data.niveau_avancement ?? ''}"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    }
    return '';
}

// ── Ouvrir le modal d'édition au clic ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    // Tooltips
    [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      .forEach(el => new bootstrap.Tooltip(el));

    // Boutons modifier
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            const fiche = JSON.parse(this.dataset.fiche);
            const chantierId = {{ $chantier->id }};

            // Action du formulaire
            document.getElementById('editFicheForm').action =
                `/chantiers/${chantierId}/fiches/${fiche.id}`;

            // Champs principaux
            document.getElementById('edit-date').value         = fiche.date         ?? '';
            document.getElementById('edit-site').value         = fiche.site         ?? '';
            document.getElementById('edit-controleur').value   = fiche.controleur_nom ?? '';
            document.getElementById('edit-entreprise').value   = fiche.entreprise_nom ?? '';
            document.getElementById('edit-evenements').value   = fiche.evenements   ?? '';
            document.getElementById('edit-observations').value = fiche.observations ?? '';

            // Remplir les tableaux
            ['personnel', 'materiel', 'appros', 'travaux'].forEach(type => {
                const tbody = document.getElementById(`e-${type}-body`);
                tbody.innerHTML = '';
                (fiche[type] || []).forEach(row => {
                    tbody.insertAdjacentHTML('beforeend', buildRow(type, row));
                });
            });

            new bootstrap.Modal(document.getElementById('editFicheModal')).show();
        });
    });
});
</script>
@endpush

@endsection