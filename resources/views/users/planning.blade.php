@extends('layouts.apps')

@section('title', 'Planning — ' . $chantier->nom)

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- EN-TÊTE --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('chantiers.index') }}">Chantiers</a>
                            </li>
                            <li class="breadcrumb-item active">Planning</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="bi bi-calendar3 text-primary me-2"></i>
                        Planning du chantier
                    </h1>
                    <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            <i class="bi bi-building me-1"></i>{{ $chantier->nom }}
                        </span>
                        @if($chantier->adresse)
                            <span class="text-muted small">
                                <i class="bi bi-geo-alt me-1"></i>{{ $chantier->adresse }}
                            </span>
                        @endif
                        <span class="badge
                            {{ $chantier->statut === 'en_cours' ? 'bg-success' : '' }}
                            {{ $chantier->statut === 'termine'  ? 'bg-secondary' : '' }}
                            {{ $chantier->statut === 'annule'   ? 'bg-danger' : '' }}
                        ">
                            {{ ucfirst(str_replace('_', ' ', $chantier->statut)) }}
                        </span>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg"
                        data-bs-toggle="modal"
                        data-bs-target="#createPlanningModal">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle activité
                </button>
            </div>
        </div>
    </div>

    {{-- CARDS STATISTIQUES --}}
    <div class="row g-3 mb-4">

        {{-- Total activités --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Total activités</p>
                            <h4 class="fw-bold mb-0">{{ $plannings->count() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-list-task text-primary fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- En retard --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">En retard</p>
                            <h4 class="fw-bold mb-0 text-danger">
                                {{ $plannings->filter->est_en_retard->count() }}
                            </h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-clock-history text-danger fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dépassement budgétaire --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Dépassement budget</p>
                            <h4 class="fw-bold mb-0 text-warning">
                                {{ $plannings->filter->est_en_depassement->count() }}
                            </h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-exclamation-triangle text-warning fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Budget consommé --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Coût réel total</p>
                            <h4 class="fw-bold mb-0">
                                {{ number_format($plannings->sum('cout_reel'), 0, ',', ' ') }}
                                <small class="fs-6 fw-normal text-muted">FCFA</small>
                            </h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-currency-exchange text-success fs-5"></i>
                        </div>
                    </div>
                    @if($chantier->budget_total)
                        @php
                            $pct = min(100, ($plannings->sum('cout_reel') / $chantier->budget_total) * 100);
                        @endphp
                        <div class="mt-2">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar {{ $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success') }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-muted small mt-1 mb-0">
                                {{ number_format($pct, 1) }}% du budget total
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- TABLEAU DES ACTIVITÉS --}}
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


@push('scripts')
<script>
    function formatDate(dateString) {
    if (!dateString) return '';
    return dateString.split('T')[0]; // ✅ extrait YYYY-MM-DD
}

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
</script>
@endpush
@endsection