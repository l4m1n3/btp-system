@extends('pdf._layout', ['titre' => 'État des dépenses'])
@section('contenu')
<h1>État des dépenses</h1>
<p class="muted">{{ $filtre }} · {{ $depenses->count() }} ligne(s)</p>
<table class="kpi">
    <tr>
        <td><div class="l">Total</div><div class="v">{{ fcfa($depenses->sum(fn ($d) => (float) $d->montant)) }}</div></td>
        <td><div class="l">Dont chantiers</div><div class="v">{{ fcfa($depenses->where('portee', \App\Enums\PorteeDepense::Chantier)->sum(fn ($d) => (float) $d->montant)) }}</div></td>
        <td><div class="l">Dont interne</div><div class="v">{{ fcfa($depenses->where('portee', \App\Enums\PorteeDepense::Interne)->sum(fn ($d) => (float) $d->montant)) }}</div></td>
        <td><div class="l">En attente de validation</div><div class="v">{{ fcfa($depenses->where('statut', \App\Enums\StatutDepense::EnAttente)->sum(fn ($d) => (float) $d->montant)) }}</div></td>
    </tr>
</table>
<table>
    <thead><tr><th>Date</th><th>Imputation</th><th>Catégorie</th><th>Libellé</th><th>Fournisseur</th><th>Pièce</th><th>Statut</th><th class="r">Montant</th></tr></thead>
    <tbody>
        @foreach ($depenses as $d)
            <tr>
                <td>{{ date_fr($d->date_depense) }}</td><td>{{ $d->chantier?->nom ?? 'Interne' }}</td><td>{{ $d->categorie->nom }}</td><td>{{ $d->libelle }}</td>
                <td>{{ $d->fournisseur?->nom }}</td><td>{{ $d->reference_piece }}</td><td>{{ $d->statut->label() }}</td><td class="r">{{ fcfa($d->montant, false) }}</td>
            </tr>
        @endforeach
        <tr class="total"><td colspan="7">TOTAL</td><td class="r">{{ fcfa($depenses->sum(fn ($d) => (float) $d->montant), false) }}</td></tr>
    </tbody>
</table>
@endsection
