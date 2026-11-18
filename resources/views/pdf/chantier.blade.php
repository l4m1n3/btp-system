@extends('pdf._layout', ['titre' => 'Situation de chantier'])
@section('contenu')
@php
    $totalDep = $depenses->sum(fn ($d) => (float) $d->montant);
    $totalMat = $materiaux->sum(fn ($m) => $m->valeur);
    $cout = $totalDep + $totalMat;
    $budget = (float) $chantier->budget;
@endphp
<h1>{{ $chantier->nom }}</h1>
<p class="muted">{{ $chantier->code }}{{ $chantier->marche ? ' · Marché '.$chantier->marche->reference.' ('.$chantier->marche->client->nom.')' : '' }} · Chef de chantier : {{ $chantier->chef?->name ?? '—' }} · Statut : {{ $chantier->statut->label() }}</p>

<table class="kpi">
    <tr>
        <td><div class="l">Avancement physique</div><div class="v">{{ number_format($chantier->avancement, 1, ',', ' ') }} %</div></td>
        <td><div class="l">Budget</div><div class="v">{{ fcfa($budget) }}</div></td>
        <td><div class="l">Coût engagé</div><div class="v">{{ fcfa($cout) }}</div></td>
        <td><div class="l">Écart budget − coût</div><div class="v {{ $budget - $cout < 0 ? 'rouge' : 'vert' }}">{{ fcfa($budget - $cout) }}</div></td>
    </tr>
</table>
<p class="muted">Début : {{ date_fr($chantier->date_debut) }} · Fin prévue : {{ date_fr($chantier->date_fin_prevue) }}{{ $chantier->estEnRetard() ? ' (délai dépassé)' : '' }} · Fin réelle : {{ date_fr($chantier->date_fin_reelle) }}</p>

<h2>Planning et avancement</h2>
<table>
    <thead><tr><th>Tâche</th><th>Début prévu</th><th>Fin prévue</th><th class="r">Coût prévu</th><th class="r">Dépensé</th><th class="r">Avancement</th><th>Statut</th></tr></thead>
    <tbody>
        @forelse ($chantier->taches as $t)
            @php $dep = $depenses->where('tache_id', $t->id)->sum(fn ($d) => (float) $d->montant); @endphp
            <tr>
                <td>{{ $t->libelle }}</td><td>{{ date_fr($t->date_debut_prevue) }}</td><td class="{{ $t->estEnRetard() ? 'rouge' : '' }}">{{ date_fr($t->date_fin_prevue) }}</td>
                <td class="r">{{ fcfa($t->cout_prevu, false) }}</td><td class="r {{ $dep > (float) $t->cout_prevu && (float) $t->cout_prevu > 0 ? 'rouge' : '' }}">{{ fcfa($dep, false) }}</td>
                <td class="r">{{ $t->avancement }} %</td><td>{{ $t->statut->label() }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="muted">Aucune tâche planifiée.</td></tr>
        @endforelse
    </tbody>
</table>

<h2>Dépenses validées par catégorie</h2>
<table>
    <thead><tr><th>Catégorie</th><th class="r">Nombre</th><th class="r">Montant</th></tr></thead>
    <tbody>
        @foreach ($depenses->groupBy(fn ($d) => $d->categorie->nom)->sortByDesc(fn ($g) => $g->sum('montant')) as $cat => $g)
            <tr><td>{{ $cat }}</td><td class="r">{{ $g->count() }}</td><td class="r">{{ fcfa($g->sum(fn ($d) => (float) $d->montant), false) }}</td></tr>
        @endforeach
        <tr><td>Matériaux sortis du stock</td><td class="r">{{ $materiaux->count() }}</td><td class="r">{{ fcfa($totalMat, false) }}</td></tr>
        <tr class="total"><td colspan="2">COÛT TOTAL ENGAGÉ</td><td class="r">{{ fcfa($cout, false) }}</td></tr>
    </tbody>
</table>

<h2>Détail des dépenses</h2>
<table>
    <thead><tr><th>Date</th><th>Libellé</th><th>Catégorie</th><th>Pièce</th><th class="r">Montant</th></tr></thead>
    <tbody>
        @forelse ($depenses as $d)
            <tr><td>{{ date_fr($d->date_depense) }}</td><td>{{ $d->libelle }}</td><td>{{ $d->categorie->nom }}</td><td>{{ $d->reference_piece }}</td><td class="r">{{ fcfa($d->montant, false) }}</td></tr>
        @empty
            <tr><td colspan="5" class="muted">Aucune dépense validée.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
