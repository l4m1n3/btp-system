@extends('layouts.app')

@section('title', 'Gestion des Entreprises')

@section('content')
<div class="container-xl py-4">

    {{-- EN-TÊTE --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1 class="h4 mb-1">
                        <i class="bi bi-building text-secondary me-2"></i>
                        Gestion des Entreprises
                    </h1>
                    <p class="text-secondary mb-0 small">Gérez vos partenaires, clients et fournisseurs</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Imprimer
                    </button>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                        <i class="bi bi-plus-circle me-2"></i>Nouvelle entreprise
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIQUES --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="border rounded p-3 bg-white">
                <p class="text-secondary small mb-1">TOTAL ENTREPRISES</p>
                <p class="h4 mb-0">{{ $companies->total() }}</p>
                <small class="text-success">+8% ce mois</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="border rounded p-3 bg-white">
                <p class="text-secondary small mb-1">ACTIFS</p>
                <p class="h4 mb-0">{{ $totalActifs ?? 0 }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="border rounded p-3 bg-white">
                <p class="text-secondary small mb-1">ABONNEMENTS</p>
                <p class="h4 mb-0">{{ $totalAbonnements ?? 0 }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="border rounded p-3 bg-white">
                <p class="text-secondary small mb-1">INACTIFS</p>
                <p class="h4 mb-0">{{ $totalInactifs ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- FILTRES ET RECHERCHE --}}
    <div class="border rounded bg-white p-3 mb-4">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <label class="small text-secondary fw-normal">Rechercher</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0"
                           placeholder="Nom, email, téléphone..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="small text-secondary fw-normal">Abonnement</label>
                <select name="abonnement" class="form-select form-select-sm">
                    <option value="">Tous les types</option>
                    <option value="gratuit" {{ request('abonnement') == 'gratuit' ? 'selected' : '' }}>Gratuit</option>
                    <option value="mensuel" {{ request('abonnement') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                    <option value="annuel" {{ request('abonnement') == 'annuel' ? 'selected' : '' }}>Annuel</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small text-secondary fw-normal">Statut</label>
                <select name="statut" class="form-select form-select-sm">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i>Filtrer
                </button>
                <a href="{{ route('entreprise') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- TABLEAU DES ENTREPRISES --}}
    <div class="border rounded bg-white">
        <div class="border-bottom p-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fs-6 fw-normal">
                    <i class="bi bi-list-ul text-secondary me-2"></i>
                    Liste des Entreprises
                    <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2">{{ $companies->total() }}</span>
                </h5>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-3 py-2" width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th class="px-3 py-2 small fw-normal">ENTREPRISE</th>
                        <th class="px-3 py-2 small fw-normal">EMAIL</th>
                        <th class="px-3 py-2 small fw-normal">CONTACT</th>
                        <th class="px-3 py-2 small fw-normal">ABONNEMENT</th>
                        <th class="px-3 py-2 small fw-normal">PÉRIODE</th>
                        <th class="px-3 py-2 small fw-normal text-center">STATUT</th>
                        <th class="px-3 py-2 small fw-normal text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $patron)
                    <tr>
                        <td class="px-3 py-2">
                            <input type="checkbox" class="form-check-input select-item">
                        </td>
                        <td class="px-3 py-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded p-1 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <span class="small fw-bold text-secondary">{{ strtoupper(substr($patron->nom, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <span class="fw-semibold">{{ $patron->nom }}</span>
                                    <small class="text-secondary d-block">#{{ $patron->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-2">
                            <small>{{ $patron->email ?? 'N/A' }}</small>
                        </td>
                        <td class="px-3 py-2">
                            <small>{{ $patron->telephone }}</small>
                        </td>
                        <td class="px-3 py-2">
                            @if($patron->abonnement)
                                <span class="badge bg-light text-secondary border">
                                    {{ ucfirst($patron->abonnement) }}
                                </span>
                            @else
                                <small class="text-secondary">-</small>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            @if($patron->date_debut && $patron->date_fin)
                                <small class="text-secondary">
                                    {{ \Carbon\Carbon::parse($patron->date_debut)->format('d/m/Y') }}
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    {{ \Carbon\Carbon::parse($patron->date_fin)->format('d/m/Y') }}
                                </small>
                            @else
                                <small class="text-secondary">-</small>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if($patron->actif)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                    Actif
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                    Inactif
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center">
                            <a href="{{ route('entreprise.details', $patron->id) }}"
                               class="btn btn-sm btn-outline-secondary py-0 px-1 border-0"
                               title="Voir">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-secondary py-0 px-1 border-0"
                                    title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary py-0 px-1 border-0"
                                    title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-building text-secondary d-block" style="font-size: 2.5rem;"></i>
                            <p class="text-secondary mt-2 mb-0">Aucune entreprise trouvée</p>
                            <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                                <i class="bi bi-plus-circle me-2"></i>Ajouter une entreprise
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($companies->hasPages())
        <div class="border-top p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="small text-secondary">
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border">
            <div class="modal-header border-bottom py-2">
                <h5 class="modal-title fs-6">
                    <i class="bi bi-plus-circle me-2"></i>Nouvelle entreprise
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('entreprise.store') }}" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="small text-secondary">Nom de l'entreprise <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" name="nom" required>
                        </div>
                        <div class="col-12">
                            <label class="small text-secondary">Téléphone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control form-control-sm" name="telephone" required>
                        </div>
                        <div class="col-12">
                            <label class="small text-secondary">Email</label>
                            <input type="email" class="form-control form-control-sm" name="email">
                        </div>
                        <div class="col-12">
                            <label class="small text-secondary">Type d'abonnement</label>
                            <select class="form-select form-select-sm" name="abonnement">
                                <option value="">Sélectionner...</option>
                                <option value="gratuit">Gratuit</option>
                                <option value="mensuel">Mensuel</option>
                                <option value="annuel">Annuel</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small text-secondary">Date début</label>
                            <input type="date" class="form-control form-control-sm" name="date_debut"
                                   value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="small text-secondary">Date fin</label>
                            <input type="date" class="form-control form-control-sm" name="date_fin"
                                   value="{{ now()->addYear()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-sm btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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