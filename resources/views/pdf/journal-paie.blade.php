@extends('pdf._layout', ['titre' => 'Journal de paie'])
@section('contenu')
<h1>Journal de paie — {{ $periode->libelle }}</h1>
<p class="muted">{{ $bulletins->count() }} bulletin(s) · statut : {{ $periode->statut->label() }}</p>
<table style="margin-top:8px">
    <thead><tr><th>Matricule</th><th>Employé</th><th>Imputation</th><th class="r">Jours</th><th class="r">Salaire dû</th><th class="r">H. sup</th><th class="r">Primes</th><th class="r">Brut</th><th class="r">Avances</th><th class="r">Retenues</th><th class="r">Net à payer</th><th>Payé</th></tr></thead>
    <tbody>
        @foreach ($bulletins as $b)
            <tr>
                <td>{{ $b->employe->matricule }}</td><td>{{ $b->employe->nom_complet }}</td><td>{{ $b->chantier?->nom ?? 'Siège' }}</td>
                <td class="r">{{ (float) $b->jours_travailles }}</td><td class="r">{{ fcfa($b->salaire_du, false) }}</td><td class="r">{{ fcfa($b->montant_heures_sup, false) }}</td>
                <td class="r">{{ fcfa($b->total_primes, false) }}</td><td class="r b">{{ fcfa($b->salaire_brut, false) }}</td><td class="r">{{ fcfa($b->total_avances, false) }}</td>
                <td class="r">{{ fcfa($b->total_retenues, false) }}</td><td class="r b">{{ fcfa($b->net_a_payer, false) }}</td><td>{{ $b->paye ? 'Oui' : '' }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td colspan="4">TOTAUX</td>
            @foreach (['salaire_du', 'montant_heures_sup', 'total_primes', 'salaire_brut', 'total_avances', 'total_retenues', 'net_a_payer'] as $col)
                <td class="r">{{ fcfa($bulletins->sum($col), false) }}</td>
            @endforeach
            <td></td>
        </tr>
    </tbody>
</table>
@endsection
