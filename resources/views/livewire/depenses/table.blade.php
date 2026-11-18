{{-- Tableau de dépenses réutilisé (liste générale et fiche chantier). --}}
@php $avecChantier ??= true; $avecActions ??= false; @endphp
<div class="overflow-x-auto">
    <table class="table-base">
        <thead>
            <tr>
                <th>Date</th><th>Libellé</th>
                @if ($avecChantier)<th>Imputation</th>@endif
                <th>Catégorie</th><th class="text-right">Montant</th><th>Statut</th><th></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($depenses as $d)
                <tr wire:key="dep{{ $d->id }}">
                    <td class="whitespace-nowrap">{{ date_fr($d->date_depense) }}</td>
                    <td class="max-w-sm">
                        <div class="font-medium text-slate-900">{{ $d->libelle }}</div>
                        <div class="text-xs text-slate-500">
                            {{ $d->fournisseur?->nom }}{{ $d->reference_piece ? ' · Pièce '.$d->reference_piece : '' }}{{ $d->tache ? ' · '.$d->tache->libelle : '' }}
                            @if ($d->statut === \App\Enums\StatutDepense::Rejetee && $d->motif_rejet)<span class="text-red-600"> · Rejet : {{ $d->motif_rejet }}</span>@endif
                        </div>
                    </td>
                    @if ($avecChantier)
                        <td>@if ($d->chantier)<a href="{{ route('chantiers.show', $d->chantier) }}" wire:navigate class="link">{{ $d->chantier->nom }}</a>@else<x-badge color="violet">Interne</x-badge>@endif</td>
                    @endif
                    <td>{{ $d->categorie->nom }}</td>
                    <td class="text-right font-medium whitespace-nowrap tabular-nums">{{ fcfa($d->montant) }}</td>
                    <td><x-badge :value="$d->statut" /></td>
                    <td class="text-right whitespace-nowrap">
                        @if ($d->justificatif)<a href="{{ $d->justificatifUrl() }}" target="_blank" class="link mr-1 text-xs">Pièce</a>@endif
                        @if ($avecActions)
                            @if ($d->statut === \App\Enums\StatutDepense::EnAttente)
                                @can('depenses.valider')
                                    <x-btn size="sm" variant="success" wire:click="valider({{ $d->id }})">Valider</x-btn>
                                    <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="demanderRejet({{ $d->id }})">Rejeter</x-btn>
                                @endcan
                                @if ($d->estModifiable() && ($d->saisi_par === auth()->id() || Gate::allows('depenses.valider')))
                                    <x-btn size="sm" variant="ghost" wire:click="modifier({{ $d->id }})">Modifier</x-btn>
                                    <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="supprimer({{ $d->id }})" wire:confirm="Supprimer cette dépense ?">×</x-btn>
                                @endif
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><x-empty message="Aucune dépense." /></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
