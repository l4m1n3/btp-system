@extends('layouts.apps')

@section('title', 'Détails de l\'entreprise - ' . $company->nom)

@section('content')
<div class="container-fluid"> 
    <!-- En-tête avec actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-3">
                <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; font-size: 1.5rem;">
                    {{ strtoupper(substr($company->nom, 0, 1)) }}
                </div>
                <div>
                    <h1 class="h3 mb-1">{{ $company->nom }}</h1>
                    <div class="text-muted d-flex align-items-center gap-3 flex-wrap">
                        <span>
                            <i class="fas fa-envelope me-1"></i>
                            {{ $company->email ?? 'Non renseigné' }}
                        </span>
                        <span>
                            <i class="fas fa-phone me-1"></i>
                            {{ $company->telephone ?? 'Non renseigné' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Colonne principale -->
        <div class="col-lg-12">
            <!-- Informations principales -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Informations générales</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Nom de l'entreprise</h6>
                            <p class="mb-0 fw-bold">{{ $company->nom }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Statut</h6>
                            <span class="badge bg-{{ $company->actif ? 'success' : 'danger' }} fs-6 px-3 py-2">
                                {{ $company->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Type d'abonnement</h6>
                            <p class="mb-0 text-capitalize">{{ $company->abonnement ?? 'Aucun' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Période d'abonnement</h6>
                            <p class="mb-0">
                                @if($company->date_debut && $company->date_fin)
                                    Du {{ \Carbon\Carbon::parse($company->date_debut)->format('d/m/Y') }}
                                    au {{ \Carbon\Carbon::parse($company->date_fin)->format('d/m/Y') }}
                                @else
                                    Non défini
                                @endif
                            </p>
                        </div>
                        <div class="col-12">
                            <h6 class="text-muted mb-1">Date de création</h6>
                            <p class="mb-0">{{ $company->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Autres informations (à compléter selon tes besoins) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Chantiers associés</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addChantierModal">
                            <i class="fas fa-plus me-1"></i> Ajouter un chantier
                        </button>
                    </div>
                    {{-- <h5 class="mb-0">Informations complémentaires</h5> --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Adresse</th>
                                    <th>Budget Total</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                             @foreach ($chantiers as $chantier)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input selectItem">
                                    </td>
                                    <td>{{ $chantier->nom }}</td>
                                    <td>{{ $chantier->description ?? 'N/A' }}</td>
                                    <td>{{ $chantier->adresse ?? 'N/A' }}</td>
                                    <td>{{ $chantier->budget_total ? number_format($chantier->budget_total, 0, ',', ' ') . ' FCFA' : 'N/A' }}</td>
                                    <td>
                                        @if($chantier->statut === 'en_cours')
                                            <span class="badge bg-info text-white">En cours</span>
                                        @elseif($chantier->statut === 'termine')
                                            <span class="badge bg-success">Terminé</span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('rapport.entreprise.details', $chantier->id) }}" class="btn btn-sm btn-outline-dark me-1">
                                            <i class="fas fa-file"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                             @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination et actions groupées -->
                <div class="d-flex justify-content-between align-items-center mt-4">    
                        <div>
                            <span class="text-muted">Page {{ $chantiers->currentPage() }} sur {{ $chantiers->lastPage() }}</span>
                        </div>
                        <div>
                            {{ $chantiers->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="col-lg-4">
            <!-- Statut abonnement -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-3">Statut abonnement actuel</h6>
                    
                    @if($company->date_fin && \Carbon\Carbon::parse($company->date_fin)->isPast())
                        <div class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                            <h5 class="mt-3 mb-1">Abonnement expiré</h5>
                            <p class="text-muted">Depuis le {{ \Carbon\Carbon::parse($company->date_fin)->format('d/m/Y') }}</p>
                        </div>
                    @elseif($company->actif)
                        <div class="text-success mb-3">
                            <i class="fas fa-check-circle fa-3x"></i>
                            <h5 class="mt-3 mb-1">Abonnement actif</h5>
                            @if($company->date_fin)
                                <p class="mb-0">Expire le <strong>{{ \Carbon\Carbon::parse($company->date_fin)->format('d/m/Y') }}</strong></p>
                                <p class="text-muted small">
                                    Plus que {{ \Carbon\Carbon::parse($company->date_fin)->diffForHumans(null, true) }}
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="text-warning mb-3">
                            <i class="fas fa-pause-circle fa-3x"></i>
                            <h5 class="mt-3 mb-1">Inactif</h5>
                        </div>
                    @endif

                    <button class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fas fa-sync-alt me-2"></i>Renouveler / Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous vraiment sûr de vouloir supprimer l'entreprise :</p>
                <h5 class="text-danger">{{ $company->nom }}</h5>
                <p class="text-muted small mt-3 mb-0">
                    Cette action est irréversible et supprimera également les données associées (contacts, documents, etc.).
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="#" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ajouter Chantier -->
<div class="modal fade" id="addChantierModal" tabindex="-1" aria-labelledby="addChantierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
          {{-- 'nom',
        'description',
        'statut',
        'adresse',
        'budget_total',
        'entreprise_id' --}}
        <form action="{{ route('chantiers.store') }}" method="POST">
            @csrf
            <input type="hidden" name="patron_id" value="{{ $company->id }}">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addChantierModalLabel">Ajouter un chantier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom du chantier</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                     <div class="mb-3">
                        <label for="budget_total" class="form-label">Budget</label>
                        <input type="text" class="form-control" id="budget_total" name="budget_total" required>
                    </div>
                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input type="text" class="form-control" id="description" name="description">
                    </div>
                    
                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select class="form-select" id="statut" name="statut">
                            <option value="" selected>Choisir le statut</option>
                            {{-- 'en_cours', 'termine', 'annule'/ --}}
                            <option value="en_cours">En cours</option>
                            <option value="termine">Terminé</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                </div>
                <input type="hidden" class="form-control" id="entreprise_id" name="entreprise_id" value="{{ $company->id }}">
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Ajouter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection