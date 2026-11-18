<div>
    <x-page-header title="Marchés" subtitle="Marchés en attente, en cours et terminés">
        <x-slot:actions>
            @can('marches.manage')<x-btn wire:click="creer">+ Nouveau marché</x-btn>@endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex flex-wrap gap-2">
        <button wire:click="$set('statut', '')" @class(['rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset', 'bg-navy-900 text-white ring-navy-900' => $statut === '', 'bg-white text-slate-600 ring-slate-300 hover:bg-slate-50' => $statut !== ''])>Tous ({{ $compteurs->sum() }})</button>
        @foreach (\App\Enums\StatutMarche::cases() as $s)
            <button wire:click="$set('statut', '{{ $s->value }}')" @class(['rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset', 'bg-navy-900 text-white ring-navy-900' => $statut === $s->value, 'bg-white text-slate-600 ring-slate-300 hover:bg-slate-50' => $statut !== $s->value])>{{ $s->label() }} ({{ $compteurs[$s->value] ?? 0 }})</button>
        @endforeach
    </div>

    <x-card :padding="false">
        <div class="flex flex-wrap gap-2 border-b border-slate-100 p-3">
            <input type="search" wire:model.live.debounce.300ms="recherche" placeholder="Référence ou intitulé…" class="form-input max-w-xs">
            <select wire:model.live="domaine" class="form-input w-auto"><option value="">Tous domaines</option>@foreach (\App\Enums\Domaine::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
            <select wire:model.live="client" class="form-input w-auto"><option value="">Tous clients</option>@foreach ($clients as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Référence</th><th>Intitulé</th><th>Client</th><th class="text-right">Montant HT</th><th class="w-40">Facturé</th><th>Fin prévue</th><th>Statut</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($marches as $m)
                        <tr wire:key="m{{ $m->id }}">
                            <td class="whitespace-nowrap"><a href="{{ route('marches.show', $m) }}" wire:navigate class="link">{{ $m->reference }}</a></td>
                            <td class="max-w-xs"><div class="truncate">{{ $m->intitule }}</div><div class="text-xs text-slate-500">{{ $m->domaine->label() }}{{ $m->localisation ? ' · '.$m->localisation : '' }}</div></td>
                            <td>{{ $m->client->nom }}</td>
                            <td class="text-right tabular-nums whitespace-nowrap">{{ fcfa($m->montant_ht) }}</td>
                            <td><x-progress :value="$m->taux_facturation" /></td>
                            <td class="whitespace-nowrap">{{ date_fr($m->date_fin_prevue) }}</td>
                            <td><x-badge :value="$m->statut" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty message="Aucun marché ne correspond aux filtres." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $marches->links() }}</div>
    </x-card>

    @include('livewire.marches.form-modal')
</div>
