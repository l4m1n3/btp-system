@extends('pdf._layout', ['titre' => 'Bulletin de paie'])
@section('contenu')
@php $e = $b->employe; @endphp
<h1>Bulletin de paie — {{ $b->periode->libelle }}</h1>
<table style="margin-top:8px">
    <tr>
        <td style="width:50%; border:1px solid #e2e8f0">
            <b>{{ $e->nom_complet }}</b><br>
            Matricule : {{ $e->matricule }}<br>
            Fonction : {{ $e->fonction }}{{ $e->categorie ? ' ('.$e->categorie.')' : '' }}<br>
            N° CNSS : {{ $e->numero_cnss ?: '—' }}
        </td>
        <td style="border:1px solid #e2e8f0">
            Contrat : {{ $e->type_contrat->label() }} — embauché le {{ date_fr($e->date_embauche) }}<br>
            Rémunération : {{ $b->mode_remuneration->label() }} ({{ fcfa($b->salaire_base) }}{{ $b->mode_remuneration === \App\Enums\ModeRemuneration::Journalier ? ' / jour' : ' / mois' }})<br>
            Affectation : {{ $b->chantier?->nom ?? 'Siège' }}<br>
            Paiement : {{ $e->mode_paiement->label() }}{{ $e->numero_compte ? ' — '.$e->numero_compte : '' }}
        </td>
    </tr>
</table>

<table style="margin-top:14px">
    <thead><tr><th>Rubrique</th><th class="r">Base / quantité</th><th class="r">Gains</th><th class="r">Retenues</th></tr></thead>
    <tbody>
        <tr><td>Salaire {{ $b->mode_remuneration === \App\Enums\ModeRemuneration::Journalier ? '(jours travaillés)' : 'de base' }}</td><td class="r">{{ (float) $b->jours_travailles }} j{{ $b->jours_absence > 0 ? ' (dont '.(float) $b->jours_absence.' j d\'absence déduits)' : '' }}</td><td class="r">{{ fcfa($b->salaire_du, false) }}</td><td></td></tr>
        @if ($b->heures_sup > 0)<tr><td>Heures supplémentaires (majorées)</td><td class="r">{{ (float) $b->heures_sup }} h</td><td class="r">{{ fcfa($b->montant_heures_sup, false) }}</td><td></td></tr>@endif
        @foreach ($b->lignes->where('type', '!=', \App\Enums\TypeLignePaie::Retenue) as $l)
            <tr><td>{{ $l->type->label() }} : {{ $l->libelle }}</td><td></td><td class="r">{{ fcfa($l->montant, false) }}</td><td></td></tr>
        @endforeach
        <tr class="total"><td colspan="2">SALAIRE BRUT</td><td class="r">{{ fcfa($b->salaire_brut, false) }}</td><td></td></tr>
        @foreach ($b->avances as $a)
            <tr><td>Avance du {{ date_fr($a->date_avance) }}{{ $a->motif ? ' ('.$a->motif.')' : '' }}</td><td></td><td></td><td class="r">{{ fcfa($a->montant, false) }}</td></tr>
        @endforeach
        @foreach ($b->lignes->where('type', \App\Enums\TypeLignePaie::Retenue) as $l)
            <tr><td>Retenue : {{ $l->libelle }}</td><td></td><td></td><td class="r">{{ fcfa($l->montant, false) }}</td></tr>
        @endforeach
        <tr class="total"><td colspan="2">NET À PAYER</td><td class="r" colspan="2" style="font-size:12px">{{ fcfa($b->net_a_payer) }}</td></tr>
    </tbody>
</table>

<table style="margin-top:40px">
    <tr><td style="border:none; width:50%">Signature de l'employé</td><td style="border:none" class="r">Pour l'entreprise</td></tr>
</table>
@endsection
