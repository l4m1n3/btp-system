<div>
    <x-page-header title="Chantiers" :subtitle="auth()->user()->voitTousLesChantiers() ? 'Suivi de tous les chantiers' : 'Les chantiers dont vous êtes responsable'">
        <x-slot:actions>@can('chantiers.manage')<x-btn wire:click="creer">+ Nouveau chantier</x-btn>@endcan</x-slot:actions>
    </x-page-header>

    <x-card :padding="false">
        <div class="flex flex-wrap gap-2 border-b border-slate-100 p-3">
            <input type="search" wire:model.live.debounce.300ms="recherche" placeholder="Nom ou code…" class="form-input max-w-xs">
            <select wire:model.live="statut" class="form-input w-auto"><option value="">Tous statuts</option>@foreach (\App\Enums\StatutChantier::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Chantier</th><th>Marché</th><th>Chef de chantier</th><th class="w-44">Avancement</th><th class="text-right">Budget</th><th class="w-40">Budget consommé</th><th>Fin prévue</th><th>Statut</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($chantiers as $c)
                        @php $conso = $c->tauxConsommationBudget(); @endphp
                        <tr wire:key="ch{{ $c->id }}">
                            <td><a href="{{ route('chantiers.show', $c) }}" wire:navigate class="link">{{ $c->nom }}</a><div class="text-xs text-slate-500">{{ $c->code }}{{ $c->localisation ? ' · '.$c->localisation : '' }}</div></td>
                            <td class="whitespace-nowrap">{{ $c->marche?->reference ?? '—' }}</td>
                            <td>{{ $c->chef?->name ?? '—' }}</td>
                            <td><x-progress :value="$c->avancement" /></td>
                            <td class="text-right tabular-nums">{{ fcfa($c->budget, false) }}</td>
                            <td><x-progress :value="$conso" :color="$conso >= 100 ? 'bg-red-500' : ($conso >= 90 ? 'bg-amber-500' : 'bg-emerald-500')" /></td>
                            <td class="whitespace-nowrap @if($c->estEnRetard()) font-medium text-red-600 @endif">{{ date_fr($c->date_fin_prevue) }}</td>
                            <td><x-badge :value="$c->statut" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty message="Aucun chantier." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $chantiers->links() }}</div>
    </x-card>

    @include('livewire.chantiers.form-modal')
</div>
