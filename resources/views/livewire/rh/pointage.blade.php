<div>
    <x-page-header title="Pointage" subtitle="Présences et heures supplémentaires du jour, par chantier" />

    <x-card :padding="false">
        <div class="flex flex-wrap items-end gap-3 border-b border-slate-100 p-3">
            <x-field label="Chantier">
                <select wire:model.live="chantier" class="form-input w-64">
                    @if (auth()->user()->voitTousLesChantiers())<option value="siege">Siège / non affectés</option>@endif
                    @foreach ($chantiers as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach
                </select>
            </x-field>
            <x-field label="Date" name="date"><input type="date" wire:model.live="date" max="{{ now()->toDateString() }}" class="form-input"></x-field>
            <div class="flex gap-2 pb-0.5">
                <x-btn size="sm" variant="secondary" wire:click="toutPresent(true)">Tous présents</x-btn>
                <x-btn size="sm" variant="secondary" wire:click="toutPresent(false)">Tous absents</x-btn>
            </div>
            @if ($dejaSaisi)<x-badge color="green" class="mb-2">Déjà saisi — modification</x-badge>@endif
        </div>
        <form wire:submit="enregistrer">
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr><th>Employé</th><th>Fonction</th><th class="w-28 text-center">Présent</th><th class="w-32">Heures sup</th><th>Observation</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($lignes as $id => $l)
                            <tr wire:key="p{{ $id }}" @class(['bg-red-50/50' => ! $l['present']])>
                                <td class="font-medium">{{ $employes[$id]->nom_complet }}<div class="text-xs text-slate-500">{{ $employes[$id]->matricule }}</div></td>
                                <td>{{ $employes[$id]->fonction }}</td>
                                <td class="text-center"><input type="checkbox" wire:model.live="lignes.{{ $id }}.present" class="size-5 rounded border-slate-300 text-emerald-600"></td>
                                <td><input type="number" step="0.5" min="0" max="12" wire:model="lignes.{{ $id }}.heures_sup" @disabled(! $l['present']) class="form-input py-1"></td>
                                <td><input wire:model="lignes.{{ $id }}.observation" class="form-input py-1" placeholder="{{ $l['present'] ? '' : 'Motif de l\'absence' }}"></td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><x-empty message="Aucun employé actif affecté ici. Affectez le personnel depuis la fiche Employés." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (count($lignes))
                <div class="flex items-center justify-between border-t border-slate-100 p-3 text-sm">
                    <span class="text-slate-600">{{ collect($lignes)->where('present', true)->count() }} présent(s) / {{ count($lignes) }}</span>
                    <x-btn type="submit">Enregistrer le pointage</x-btn>
                </div>
            @endif
        </form>
    </x-card>
</div>
