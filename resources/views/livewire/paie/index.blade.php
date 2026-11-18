<div>
    <x-page-header title="Paie" subtitle="Une période par mois : génération des bulletins, ajustements, clôture">
        <x-slot:actions>
            @can('paie.manage')
                <form wire:submit="ouvrir" class="flex items-center gap-2">
                    <input type="month" wire:model="mois" class="form-input w-auto">
                    <x-btn type="submit">Ouvrir / préparer la paie</x-btn>
                </form>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false">
        <table class="table-base">
            <thead><tr><th>Période</th><th class="text-right">Bulletins</th><th class="text-right">Masse salariale brute</th><th class="text-right">Net à payer</th><th>Statut</th><th></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($periodes as $p)
                    <tr wire:key="pp{{ $p->id }}">
                        <td><a href="{{ route('paie.show', $p) }}" wire:navigate class="link">{{ $p->libelle }}</a></td>
                        <td class="text-right">{{ $p->bulletins_count }}</td>
                        <td class="text-right tabular-nums">{{ fcfa($p->bulletins_sum_salaire_brut ?? 0) }}</td>
                        <td class="text-right font-medium tabular-nums">{{ fcfa($p->bulletins_sum_net_a_payer ?? 0) }}</td>
                        <td><x-badge :value="$p->statut" /></td>
                        <td class="text-right">@if($p->bulletins_count)<a href="{{ route('pdf.journal-paie', $p) }}" target="_blank" class="link text-xs">Journal de paie (PDF)</a>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="6"><x-empty message="Aucune période de paie. Choisissez un mois et cliquez sur « Ouvrir / préparer la paie »." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</div>
