@extends('pdf._layout', ['titre' => 'Rapport hebdomadaire'])
@section('contenu')
<h1>Rapport hebdomadaire</h1>
<p class="muted">Semaine du {{ $debut->format('d/m/Y') }} au {{ $fin->format('d/m/Y') }}</p>
<table class="kpi">
    <tr>
        <td><div class="l">Dépenses validées</div><div class="v">{{ fcfa($depenses->sum(fn ($d) => (float) $d->montant)) }}</div></td>
        <td><div class="l">Dont chantiers</div><div class="v">{{ fcfa($depenses->where('portee', \App\Enums\PorteeDepense::Chantier)->sum(fn ($d) => (float) $d->montant)) }}</div></td>
        <td><div class="l">Dont interne</div><div class="v">{{ fcfa($depenses->where('portee', \App\Enums\PorteeDepense::Interne)->sum(fn ($d) => (float) $d->montant)) }}</div></td>
        <td><div class="l">Chantiers actifs</div><div class="v">{{ $chantiers->count() }}</div></td>
    </tr>
</table>
<h2>Chantiers</h2>
<table>
    <thead><tr><th>Chantier</th><th class="r">Avancement</th><th class="r">Budget</th><th class="r">Coût engagé</th><th class="r">Consommé</th></tr></thead>
    <tbody>
        @foreach ($chantiers as $c)
            <tr><td>{{ $c['model']->nom }}</td><td class="r">{{ number_format($c['avancement'], 1, ',', ' ') }} %</td><td class="r">{{ fcfa($c['model']->budget, false) }}</td><td class="r">{{ fcfa($c['cout'], false) }}</td><td class="r {{ $c['conso'] >= 100 ? 'rouge' : '' }}">{{ number_format($c['conso'], 1, ',', ' ') }} %</td></tr>
        @endforeach
    </tbody>
</table>
<h2>Dépenses de la semaine</h2>
<table>
    <thead><tr><th>Date</th><th>Imputation</th><th>Catégorie</th><th>Libellé</th><th class="r">Montant</th></tr></thead>
    <tbody>
        @forelse ($depenses as $d)
            <tr><td>{{ date_fr($d->date_depense) }}</td><td>{{ $d->chantier?->nom ?? 'Interne' }}</td><td>{{ $d->categorie->nom }}</td><td>{{ $d->libelle }}</td><td class="r">{{ fcfa($d->montant, false) }}</td></tr>
        @empty
            <tr><td colspan="5" class="muted">Aucune dépense validée cette semaine.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
