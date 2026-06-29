@extends('layouts.apps')

@section('title', 'Détails Chantier - '.$chantier->nom)

@section('content')

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="chantier-header-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-building fs-3"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">{{ $chantier->nom }}</h2>
                        <p class="text-muted mb-0">
                            <i class="bi bi-geo-alt-fill me-1 text-muted"></i>
                            {{ $chantier->adresse ?? 'Adresse non définie' }}
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    @if($chantier->statut == 'en_cours')
                        <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2 fs-6">
                            <i class="bi bi-clock-history me-1"></i>En cours
                        </span>
                    @elseif($chantier->statut == 'termine')
                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fs-6">
                            <i class="bi bi-check-circle me-1"></i>Terminé
                        </span>
                    @elseif($chantier->statut == 'annule')
                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 fs-6">
                            <i class="bi bi-x-circle me-1"></i>Annulé
                        </span>
                    @else
                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2 fs-6">
                            <i class="bi bi-question-circle me-1"></i>{{ ucfirst($chantier->statut) }}
                        </span>
                    @endif
                    <form action="{{ route('rapport.generate') }}" method="POST" enctype="multipart/form-data">
                    @csrf 

                    <input type="hidden" name="chantier_id" value="{{ $chantier->id }}">
                    <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-file me-1"></i>Generer un rapport</button>
                    </form>
                    {{-- <a class="btn btn-outline-secondary" href='{{ route('rapport.generateHebdomadaire') }}'>
                        <i class="bi bi-file me-1"></i>Generer un rapport
                    </a> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- CARDS RAPIDES --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small fw-semibold mb-1">BUDGET TOTAL</p>
                    <h4 class="fw-bold text-primary mb-0">
                        {{ number_format($chantier->budget_total, 0, ',', ' ') }}
                    </h4>
                    <small class="text-muted">FCFA</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small fw-semibold mb-1">ACTIVITÉS PLANNING</p>
                    <h4 class="fw-bold text-success mb-0">
                        {{ $chantier->plannings->count() }}
                    </h4>
                    <small class="text-muted">Tâches planifiées</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small fw-semibold mb-1">CRÉÉ LE</p>
                    <h4 class="fw-bold text-dark mb-0">
                        {{ $chantier->created_at->format('d/m/Y') }}
                    </h4>
                    <small class="text-muted">Date de création</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small fw-semibold mb-1">IDENTIFIANT</p>
                    <h4 class="fw-bold text-warning mb-0">#{{ $chantier->id }}</h4>
                    <small class="text-muted">Référence chantier</small>
                </div>
            </div>
        </div>
    </div>

    {{-- MENU TOGGLE / NAVIGATION --}}
    <div class="card border-0 shadow-sm">

        {{-- BARRE DE NAVIGATION TOGGLE --}}
        <div class="card-header bg-white border-bottom px-4 py-0">
            <ul class="nav nav-tabs border-0 gap-1" id="chantierTabs" role="tablist">

                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-btn active"
                            id="infos-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#infos"
                            type="button" role="tab">
                        <i class="bi bi-info-circle me-2"></i>
                        <span>Informations</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-btn"
                            id="fiches-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#fiches"
                            type="button" role="tab">
                        <i class="bi bi-journal-text me-2"></i>
                        <span>Fiches journalières</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-btn"
                            id="planning-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#planning"
                            type="button" role="tab">
                        <i class="bi bi-calendar-event me-2"></i>
                        <span>Planning</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-btn"
                            id="depenses-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#depenses"
                            type="button" role="tab">
                        <i class="bi bi-cash-stack me-2"></i>
                        <span>Dépenses</span>
                    </button>
                </li>

                {{-- <li class="nav-item" role="presentation">
                    <button class="nav-link tab-btn"
                            id="devis-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#devis"
                            type="button" role="tab">
                        <i class="bi bi-file-earmark-text me-2"></i>
                        <span>Devis</span>
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-btn"
                            id="documents-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#documents"
                            type="button" role="tab">
                        <i class="bi bi-folder me-2"></i>
                        <span>Documents</span>
                    </button>
                </li> --}}

            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="chantierTabsContent">

                {{-- ONGLET : INFORMATIONS --}}
                <div class="tab-pane fade show active" id="infos" role="tabpanel">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-4 h-100">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-file-text text-primary me-2"></i>Description
                                </h5>
                                <p class="text-muted mb-0 lh-lg">
                                    {{ $chantier->description ?? 'Aucune description disponible.' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-4 h-100">
                                <h5 class="fw-bold mb-3">
                                    <i class="bi bi-card-list text-primary me-2"></i>Informations générales
                                </h5>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Adresse</small>
                                        <span class="fw-semibold">{{ $chantier->adresse ?? 'Non définie' }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Statut</small>
                                        <span class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $chantier->statut)) }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Création</small>
                                        <span class="fw-semibold">{{ $chantier->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Budget</small>
                                        <span class="fw-semibold text-primary">{{ number_format($chantier->budget_total, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ONGLET : FICHES JOURNALIÈRES --}}
                <div class="tab-pane fade" id="fiches" role="tabpanel">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-journal-text text-warning me-2"></i>Fiches journalières
                        </h5>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#createFicheModal">
                            <i class="bi bi-plus-circle me-2"></i>Nouvelle fiche
                        </button>
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

                {{-- ONGLET : PLANNING --}}
                <div class="tab-pane fade" id="planning" role="tabpanel">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-calendar-event text-secondary me-2"></i>Planning du chantier
                        </h5>
                        <button class="btn btn-primary btn-lg"
                        data-bs-toggle="modal"
                        data-bs-target="#createPlanningModal">
                            <i class="bi bi-plus-circle me-2"></i>Nouvelle activité
                        </button>
                    </div>
                {{-- TABLEAU DES PLANNINGS --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="bi bi-table me-2 text-primary"></i>Activités planifiées
                                </h5>
                                {{-- Filtres rapides --}}
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ request()->fullUrlWithQuery(['filtre' => null]) }}"
                                    class="btn btn-sm {{ !request('filtre') ? 'btn-primary' : 'btn-outline-secondary' }}">
                                        Toutes
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['filtre' => 'retard']) }}"
                                    class="btn btn-sm {{ request('filtre') === 'retard' ? 'btn-danger' : 'btn-outline-danger' }}">
                                        <i class="bi bi-clock-history me-1"></i>Retards
                                    </a>
                                    <a href="{{ request()->fullUrlWithQuery(['filtre' => 'depassement']) }}"
                                    class="btn btn-sm {{ request('filtre') === 'depassement' ? 'btn-warning' : 'btn-outline-warning' }}">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Dépassements
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            @if($plannings->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3 mb-0">Aucune activité planifiée pour ce chantier.</p>
                                    <button class="btn btn-primary mt-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#createPlanningModal">
                                        <i class="bi bi-plus-circle me-2"></i>Créer la première activité
                                    </button>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Activité</th>
                                                <th>Responsable</th>
                                                <th>Dates prévues</th>
                                                <th>Dates réelles</th>
                                                <th>Retard</th>
                                                <th>Budget prévu</th>
                                                <th>Coût réel</th>
                                                <th>Écart</th>
                                                <th>Statut</th>
                                                <th class="text-end pe-4">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($plannings as $planning)
                                                @php
                                                    $retard      = $planning->est_en_retard;
                                                    $depassement = $planning->est_en_depassement;
                                                @endphp
                                                <tr class="{{ $retard && $depassement ? 'table-danger' : ($retard ? 'table-warning' : '') }}">

                                                    {{-- Activité --}}
                                                    <td class="ps-4">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <div>
                                                                <p class="mb-0 fw-semibold">{{ $planning->activite }}</p>
                                                                @if($planning->note)
                                                                    <p class="mb-0 text-muted small text-truncate" style="max-width: 180px;">
                                                                        {{ $planning->note }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>

                                                    {{-- Responsable --}}
                                                    <td>
                                                        <span class="text-muted small">
                                                            {{ $planning->responsable ?? '—' }}
                                                        </span>
                                                    </td>

                                                    {{-- Dates prévues --}}
                                                    <td>
                                                        <div class="small">
                                                            <span class="text-muted">Début :</span>
                                                            {{ $planning->date_debut_prevue->format('d/m/Y') }}<br>
                                                            <span class="text-muted">Fin :</span>
                                                            {{ $planning->date_fin_prevue->format('d/m/Y') }}
                                                        </div>
                                                    </td>

                                                    {{-- Dates réelles --}}
                                                    <td>
                                                        <div class="small">
                                                            <span class="text-muted">Début :</span>
                                                            {{ $planning->date_debut_reelle?->format('d/m/Y') ?? '—' }}<br>
                                                            <span class="text-muted">Fin :</span>
                                                            {{ $planning->date_fin_reelle?->format('d/m/Y') ?? '—' }}
                                                        </div>
                                                    </td>

                                                    {{-- Retard --}}
                                                    <td>
                                                        @if($retard)
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-clock-history me-1"></i>
                                                                +{{ abs($planning->jours_retard) }}j
                                                            </span>
                                                        @elseif($planning->statut === 'termine')
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle me-1"></i>À temps
                                                            </span>
                                                        @else
                                                            <span class="badge bg-light text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Budget prévu --}}
                                                    <td class="small">
                                                        {{ number_format($planning->cout_prevu, 0, ',', ' ') }}
                                                        <span class="text-muted">FCFA</span>
                                                    </td>

                                                    {{-- Coût réel --}}
                                                    <td class="small {{ $depassement ? 'text-danger fw-semibold' : '' }}">
                                                        {{ number_format($planning->cout_reel, 0, ',', ' ') }}
                                                        <span class="text-muted">FCFA</span>
                                                    </td>

                                                    {{-- Écart --}}
                                                    <td>
                                                        @if($depassement)
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-arrow-up me-1"></i>
                                                                +{{ number_format($planning->montant_depassement, 0, ',', ' ') }}
                                                            </span>
                                                        @elseif($planning->montant_depassement < 0)
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-arrow-down me-1"></i>
                                                                {{ number_format(abs($planning->montant_depassement), 0, ',', ' ') }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-light text-muted">—</span>
                                                        @endif
                                                    </td>

                                                    {{-- Statut --}}
                                                    <td>
                                                        @php
                                                            $statutColors = [
                                                                'planifie'  => 'secondary',
                                                                'en_cours'  => 'primary',
                                                                'termine'   => 'success',
                                                                'retarde'   => 'danger',
                                                                'annule'    => 'dark',
                                                            ];
                                                        @endphp
                                                        <span class="badge bg-{{ $statutColors[$planning->statut] ?? 'secondary' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $planning->statut)) }}
                                                        </span>
                                                    </td>

                                                    {{-- Actions --}}
                                                    <td class="text-end pe-4">
                                                        <div class="d-flex justify-content-end gap-2">

                                                            <!-- Bouton Modifier -->
                                                            <button class="btn btn-sm btn-outline-primary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editPlanningModal"
                                                                    data-planning="{{ $planning->toJson() }}">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>

                                                            <!-- Bouton Supprimer -->
                                                            <form action="{{ route('planning.destroy', $planning) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Supprimer cette activité ?')">
                                                                @csrf
                                                                @method('DELETE')

                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>

                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- ONGLET : DÉPENSES --}}
                <div class="tab-pane fade" id="depenses" role="tabpanel">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-cash-stack text-success me-2"></i>Dépenses du chantier
                        </h5>
                         <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepenseModal">
                                <i class="bi bi-plus-circle"></i> Nouvelle Dépense
                            </button>
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

                </div>

                {{-- ONGLET : DEVIS --}}
                {{-- <div class="tab-pane fade" id="devis" role="tabpanel">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-file-earmark-text text-dark me-2"></i>Devis
                        </h5>
                        <a href="{{ route('devis.form', $chantier->id) }}" class="btn btn-dark">
                            <i class="bi bi-plus-circle me-2"></i>Nouveau devis
                        </a>
                    </div>

                    <div class="alert alert-secondary border-0 bg-secondary bg-opacity-10">
                        <i class="bi bi-info-circle me-2"></i>
                        Créez et gérez les devis associés à ce chantier.
                    </div>

                </div> --}}

                {{-- ONGLET : DOCUMENTS --}}
                {{-- <div class="tab-pane fade" id="documents" role="tabpanel">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-folder text-warning me-2"></i>Documents
                        </h5>
                        <button class="btn btn-outline-warning">
                            <i class="bi bi-upload me-2"></i>Importer un document
                        </button>
                    </div>

                    <div class="text-center py-5">
                        <i class="bi bi-folder-x fs-1 text-muted d-block mb-3"></i>
                        <h6 class="text-muted">Aucun document</h6>
                        <p class="text-muted small">PDF, images, factures, devis signés...</p>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-cloud-upload me-2"></i>Importer
                        </button>
                    </div>

                </div> --}}

            </div>
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
        {{-- MODAL CRÉATION --}}
    <div class="modal fade" id="createPlanningModal" tabindex="-1" aria-labelledby="createPlanningLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-semibold" id="createPlanningLabel">
                        <i class="bi bi-plus-circle text-primary me-2"></i>Nouvelle activité
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('planning.store', $chantier) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <input type="hidden" name="chantier_id" value="{{ $chantier->id }}">
                            {{-- Activité --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Activité <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="activite"
                                    class="form-control @error('activite') is-invalid @enderror"
                                    value="{{ old('activite') }}"
                                    placeholder="Ex : Terrassement, Fondations…"
                                    required>
                                @error('activite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Responsable --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Responsable</label>
                                <input type="text"
                                    name="responsable"
                                    class="form-control"
                                    value="{{ old('responsable') }}"
                                    placeholder="Nom du responsable">
                            </div>

                            {{-- Statut --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Statut</label>
                                <select name="statut" class="form-select">
                                    @foreach(['planifie' => 'Planifié', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'retarde' => 'Retardé', 'annule' => 'Annulé'] as $val => $label)
                                        <option value="{{ $val }}" {{ old('statut') === $val ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            {{-- Dates prévues --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-primary me-1"></i>
                                    Début prévu <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    name="date_debut_prevue"
                                    class="form-control @error('date_debut_prevue') is-invalid @enderror"
                                    value="{{ old('date_debut_prevue') }}"
                                    required>
                                @error('date_debut_prevue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-check text-primary me-1"></i>
                                    Fin prévue <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    name="date_fin_prevue"
                                    class="form-control @error('date_fin_prevue') is-invalid @enderror"
                                    value="{{ old('date_fin_prevue') }}"
                                    required>
                                @error('date_fin_prevue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Dates réelles --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Début réel</label>
                                <input type="date"
                                    name="date_debut_reelle"
                                    class="form-control"
                                    value="{{ old('date_debut_reelle') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Fin réelle</label>
                                <input type="date"
                                    name="date_fin_reelle"
                                    class="form-control"
                                    value="{{ old('date_fin_reelle') }}">
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            {{-- Coûts --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-wallet2 text-success me-1"></i>
                                    Coût prévu (FCFA) <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    name="cout_prevu"
                                    class="form-control @error('cout_prevu') is-invalid @enderror"
                                    value="{{ old('cout_prevu', 0) }}"
                                    min="0"
                                    step="0.01"
                                    required>
                                @error('cout_prevu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="bi bi-receipt me-1"></i>Coût réel (FCFA)
                                </label>
                                <input type="number"
                                    name="cout_reel"
                                    class="form-control"
                                    value="{{ old('cout_reel', 0) }}"
                                    min="0"
                                    step="0.01">
                            </div>

                            {{-- Note --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted">Note</label>
                                <textarea name="note"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Observations, remarques…">{{ old('note') }}</textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- MODAL ÉDITION --}}
    <div class="modal fade" id="editPlanningModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-pencil text-primary me-2"></i>Modifier l'activité
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editPlanningForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label fw-semibold">Activité <span class="text-danger">*</span></label>
                                <input type="text" name="activite" id="edit_activite" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Responsable</label>
                                <input type="text" name="responsable" id="edit_responsable" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Statut</label>
                                <select name="statut" id="edit_statut" class="form-select">
                                    @foreach(['planifie' => 'Planifié', 'en_cours' => 'En cours', 'termine' => 'Terminé', 'retarde' => 'Retardé', 'annule' => 'Annulé'] as $val => $label)
                                        <option value="{{ $val }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Début prévu <span class="text-danger">*</span></label>
                                <input type="date" name="date_debut_prevue" id="edit_date_debut_prevue" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Fin prévue <span class="text-danger">*</span></label>
                                <input type="date" name="date_fin_prevue" id="edit_date_fin_prevue" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Début réel</label>
                                <input type="date" name="date_debut_reelle" id="edit_date_debut_reelle" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Fin réelle</label>
                                <input type="date" name="date_fin_reelle" id="edit_date_fin_reelle" class="form-control">
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Coût prévu (FCFA) <span class="text-danger">*</span></label>
                                <input type="number" name="cout_prevu" id="edit_cout_prevu" class="form-control" min="0" step="0.01" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted">Coût réel (FCFA)</label>
                                <input type="number" name="cout_reel" id="edit_cout_reel" class="form-control" min="0" step="0.01">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted">Note</label>
                                <textarea name="note" id="edit_note" class="form-control" rows="2"></textarea>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
 {{-- MODAL CRÉATION DEPENSE--}}
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
                    <input type="hidden" name="chantier_id" value="{{ $chantier->id }}">
                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Planning --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Planning <span class="text-danger">*</span></label>
                                <select name="planning_id" class="form-select" required>
                                    <option value="">Sélectionnez un planning</option>
                                    @foreach ($planing as $plan)
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

{{-- STYLES --}}
<style>
    .chantier-header-icon {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        flex-shrink: 0;
    }

    .nav-tabs .tab-btn {
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        padding: 1rem 1.25rem;
        color: #6c757d;
        font-weight: 500;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .nav-tabs .tab-btn:hover {
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.05);
        border-bottom-color: rgba(13, 110, 253, 0.3);
    }

    .nav-tabs .tab-btn.active {
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.06);
        border-bottom-color: #0d6efd;
        font-weight: 600;
    }

    .nav-tabs {
        overflow-x: auto;
        flex-wrap: nowrap;
        scrollbar-width: none;
    }

    .nav-tabs::-webkit-scrollbar {
        display: none;
    }

    @media (max-width: 768px) {
        .nav-tabs .tab-btn span {
            display: none;
        }
        .nav-tabs .tab-btn {
            padding: 0.75rem;
        }
    }
</style>

@push('scripts')
<script>

document.getElementById('editPlanningModal').addEventListener('show.bs.modal', function (e) {

    const btn = e.relatedTarget;
    const planning = JSON.parse(btn.dataset.planning);

    const form = document.getElementById('editPlanningForm');

    form.action = `/planning/update/${planning.id}`;

    const fields = [
        'activite',
        'responsable',
        'statut',
        'note',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
        'cout_prevu',
        'cout_reel'
    ];

    fields.forEach(field => {
        const el = document.getElementById(`edit_${field}`);

        if (el) {
            let value = planning[field] ?? '';

            // ✅ conversion spécifique pour les dates ISO
            if (el.type === 'date') {
                value = formatDate(value);
            }

            el.value = value;
        }
    });

});
    function formatDate(dateString) {
    if (!dateString) return '';
    return dateString.split('T')[0]; // ✅ extrait YYYY-MM-DD
}
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