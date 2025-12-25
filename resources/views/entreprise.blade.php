@extends('layouts.app')

@section('title', 'Gestion des Entreprises')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Gestion des Entreprises</h1>
            <p class="text-muted">Gérez vos partenaires, clients et fournisseurs</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                <i class="fas fa-plus me-2"></i>Nouvelle entreprise
            </button>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total entreprises</h6>
                            <h3 class="mb-0">156</h3>
                        </div>
                        <div class="bg-primary bg-opacity-25 p-3 rounded">
                            <i class="fas fa-building text-primary fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card border-0 bg-success bg-opacity-10">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total abonnements</h6>
                            <h3 class="mb-0">89</h3>
                        </div>
                        <div class="bg-success bg-opacity-25 p-3 rounded">
                            <i class="fas fa-users text-success fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tableau des entreprises -->
    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-hover" id="companiesTable">
                <thead>
                    <tr>
                        <th width="50">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Entreprise</th>
                        <th>Email</th>
                        <th>Contact</th>
                        <th>Abonnement</th>
                        <th>Date début<br> Abonnement</th>
                        <th>Date fin<br> Abonnement</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companies as $patron)
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>{{ $patron->nom }}</td>
                            <td>{{ $patron->email }}</td>
                            <td>{{ $patron->telephone }}</td>
                            <td>{{ $patron->abonnement }}</td>
                            <td>{{ $patron->date_debut }}</td>
                            <td>{{ $patron->date_fin }}</td>
                            <td>{{ $patron->actif ? 'Actif' : 'Inactif' }}</td>
                            <td class="text-end">
                                 <a href="{{ route('entreprise.details', $patron->id) }}" class="btn btn-sm btn-outline-dark me-1">
                                    <i class="fas fa-eye"></i>
                                 </a>
                                {{-- <a href="{{ route('entreprise.edit', $patron->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </a> --}}
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
                <span class="text-muted">Page {{ $companies->currentPage() }} sur {{ $companies->lastPage() }}</span>
            </div>
            <div>
                {{ $companies->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout d'entreprise -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle entreprise</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('entreprise.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom de l'entreprise *</label>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone *</label>
                            <input type="tel" class="form-control" name="telephone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                         <div class="col-md-6">
                            <label class="form-label">Abonnement</label>
                            <select class="form-select" name="abonnement">
                                <option value="">Sélectionner</option>
                                <option value="gratuit">Gratuit</option>
                                <option value="mensuel">Mensuel</option>
                                <option value="annuel">Annuel</option>
                            </select>
                        </div>
                         <div class="col-md-6">
                            <label class="form-label">Date debut</label>
                            <input type="date" class="form-control" name="date_debut" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date fin</label>
                            <input type="date" class="form-control" name="date_fin" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });

    // Graphique de répartition par type
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: ['Clients', 'Fournisseurs', 'Sous-traitants', 'Partenaires'],
            datasets: [{
                data: [89, 42, 25, 18],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#6c757d'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Graphique mensuel
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Nouvelles entreprises',
                data: [8, 12, 15, 10, 14, 18, 16, 12, 20, 22, 18, 15],
                backgroundColor: '#4361ee',
                borderColor: '#4361ee',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
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

    // Gestion de la sélection multiple
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.select-item');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    const exportSelectedBtn = document.getElementById('exportSelected');

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedActions();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedActions);
    });

    function updateSelectedActions() {
        const selected = document.querySelectorAll('.select-item:checked');
        deleteSelectedBtn.disabled = selected.length === 0;
        exportSelectedBtn.disabled = selected.length === 0;
    }

    // Recherche et filtres
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const resetFiltersBtn = document.getElementById('resetFilters');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeValue = typeFilter.value;
        const statusValue = statusFilter.value;

        document.querySelectorAll('#companiesTable tbody tr').forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const type = row.dataset.type;
            const status = row.dataset.status;

            const matchSearch = name.includes(searchTerm);
            const matchType = !typeValue || type === typeValue;
            const matchStatus = !statusValue || status === statusValue;

            row.style.display = (matchSearch && matchType && matchStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    typeFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);

    resetFiltersBtn.addEventListener('click', function() {
        searchInput.value = '';
        typeFilter.value = '';
        statusFilter.value = '';
        filterTable();
    });

    // Filtres avancés
    const applyFiltersBtn = document.getElementById('applyFilters');
    applyFiltersBtn.addEventListener('click', function() {
        // Implémenter la logique de filtrage avancé ici
        const modal = bootstrap.Modal.getInstance(document.getElementById('filterModal'));
        modal.hide();
        showToast('Filtres appliqués avec succès');
    });

    // Gestion du formulaire d'ajout
    const addCompanyForm = document.getElementById('addCompanyForm');
    addCompanyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Implémenter l'ajout AJAX ici
        const modal = bootstrap.Modal.getInstance(document.getElementById('addCompanyModal'));
        modal.hide();
        showToast('Entreprise ajoutée avec succès');
        this.reset();
    });

    // Fonctions de gestion des entreprises
    window.viewCompany = function(id) {
        window.location.href = `/entreprises/${id}`;
    };

    window.editCompany = function(id) {
        // Implémenter l'édition ici
        showToast('Modification de l\'entreprise #' + id);
    };

    window.deleteCompany = function(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ?')) {
            // Implémenter la suppression AJAX ici
            showToast('Entreprise supprimée avec succès', 'success');
        }
    };

    // Fonction utilitaire pour afficher les toasts
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 p-3`;
        toast.style.zIndex = '1050';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', function() {
            this.remove();
        });
    }

    // Exporter les données
    exportSelectedBtn.addEventListener('click', function() {
        const selected = [];
        document.querySelectorAll('.select-item:checked').forEach(checkbox => {
            selected.push(checkbox.value);
        });
        
        if (selected.length > 0) {
            // Implémenter l'export ici
            showToast(`Export de ${selected.length} entreprises démarré`, 'success');
        }
    });

    // Suppression multiple
    deleteSelectedBtn.addEventListener('click', function() {
        const selected = [];
        document.querySelectorAll('.select-item:checked').forEach(checkbox => {
            selected.push(checkbox.value);
        });
        
        if (selected.length > 0 && confirm(`Êtes-vous sûr de vouloir supprimer ${selected.length} entreprise(s) ?`)) {
            // Implémenter la suppression multiple ici
            showToast(`${selected.length} entreprises supprimées`, 'success');
            selectAll.checked = false;
            checkboxes.forEach(checkbox => checkbox.checked = false);
            updateSelectedActions();
        }
    });
});
</script>

<style>
.avatar {
    flex-shrink: 0;
}

.table th {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    border-bottom-width: 2px;
}

.table tbody tr {
    cursor: pointer;
    transition: background-color 0.2s;
}

.table tbody tr:hover {
    background-color: rgba(52, 152, 219, 0.05);
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}

.bg-client {
    background-color: #28a745 !important;
}

.bg-fournisseur {
    background-color: #ffc107 !important;
}

.bg-sous_traitant {
    background-color: #17a2b8 !important;
}

.bg-partenaire {
    background-color: #6c757d !important;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
}

.form-control:focus, .form-select:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

#searchInput {
    border-radius: 0.375rem 0 0 0.375rem;
}

.chart-container {
    position: relative;
}
</style>
@endsection
@endsection