@extends('layouts.app')

@section('title', 'Rapports de l\'entreprise - ' . $company->nom)

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Gestion des Entreprises</h1>
            <p class="text-muted">Gérez les rapports de l'entreprise</p>
        </div>

        
    </div>
     <!-- Autres informations (à compléter selon tes besoins) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0"> Rapports de l'entreprise {{ $company->nom }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Catégorie</th>
                <th>Description</th>
                <th>Montant</th>
                <th>Responsable</th>
                <th>Justificatif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($depenses as $index => $depense)
                <tr>
                    <td>{{ $index + 1 + ($depenses->currentPage() - 1) * $depenses->perPage() }}</td>
                    <td>{{ \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') }}</td>
                    <td>{{ $depense->categorie }}</td>
                    <td>{{ $depense->description }}</td>
                    <td>{{ number_format($depense->montant, 0, '.', ' ') }} FCFA</td>
                    <td>{{ $depense->responsable }}</td>
                    <td>
                        @if($depense->justificatif)
                           <a href="{{ asset('storage/' . $depense->justificatif) }}" target="_blank">
                                <img src="{{ asset('storage/' . $depense->justificatif) }}" 
                                    alt="Justificatif" 
                                    style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                            </a>

                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Aucune dépense trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

                    <!-- Pagination et actions groupées -->
                <div class="d-flex justify-content-between align-items-center mt-4">    
                        <div>
                            <span class="text-muted">Page {{ $depenses->currentPage() }} sur {{ $depenses->lastPage() }}</span>
                        </div>
                        <div>
                            {{ $depenses->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
</div>
@endsection