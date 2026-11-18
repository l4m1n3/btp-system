<div>
    <x-page-header title="Congés & absences" subtitle="Demandes, validations et absences constatées">
        <x-slot:actions>@can('rh.manage')<x-btn wire:click="creer">+ Saisir</x-btn>@endcan</x-slot:actions>
    </x-page-header>

    @if ($absentsAujourdhui->isNotEmpty())
        <div class="mb-4 rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm">
            <span class="font-semibold">Absents aujourd'hui :</span>
            {{ $absentsAujourdhui->map(fn ($c) => $c->employe->nom_complet.' ('.$c->type->label().')')->join(', ') }}
        </div>
    @endif

    <x-card :padding="false">
        <div class="flex flex-wrap gap-2 border-b border-slate-100 p-3">
            <select wire:model.live="statut" class="form-input w-auto"><option value="">Tous statuts</option>@foreach (\App\Enums\StatutConge::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
            <select wire:model.live="type" class="form-input w-auto"><option value="">Tous types</option>@foreach (\App\Enums\TypeConge::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Employé</th><th>Type</th><th>Période</th><th class="text-right">Jours</th><th>Motif</th><th>Statut</th><th></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($conges as $c)
                        <tr wire:key="cg{{ $c->id }}">
                            <td><a href="{{ route('employes.show', $c->employe) }}" wire:navigate class="link">{{ $c->employe->nom_complet }}</a></td>
                            <td>{{ $c->type->label() }}</td>
                            <td class="whitespace-nowrap">{{ date_fr($c->date_debut) }} → {{ date_fr($c->date_fin) }}</td>
                            <td class="text-right">{{ $c->nombre_jours }}</td>
                            <td class="max-w-xs truncate">{{ $c->motif }}</td>
                            <td><x-badge :value="$c->statut" /></td>
                            <td class="text-right whitespace-nowrap">
                                @can('rh.manage')
                                    @if ($c->statut === \App\Enums\StatutConge::Demande)
                                        <x-btn size="sm" variant="success" wire:click="traiter({{ $c->id }}, 'approuve')">Approuver</x-btn>
                                        <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="traiter({{ $c->id }}, 'refuse')">Refuser</x-btn>
                                    @endif
                                    <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="supprimer({{ $c->id }})" wire:confirm="Supprimer ?">×</x-btn>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $conges->links() }}</div>
    </x-card>

    <x-modal wire:model="modal" title="Congé ou absence">
        <form wire:submit="enregistrer" id="form-conge" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Employé" name="form.employe_id" required class="sm:col-span-2"><select wire:model="form.employe_id" class="form-input"><option value="">—</option>@foreach ($employes as $e)<option value="{{ $e->id }}">{{ $e->nom }} {{ $e->prenom }} ({{ $e->matricule }})</option>@endforeach</select></x-field>
            <x-field label="Type" name="form.type"><select wire:model.live="form.type" class="form-input">@foreach (\App\Enums\TypeConge::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <div></div>
            <x-field label="Du" name="form.date_debut" required><input type="date" wire:model="form.date_debut" class="form-input"></x-field>
            <x-field label="Au" name="form.date_fin" required><input type="date" wire:model="form.date_fin" class="form-input"></x-field>
            <x-field label="Motif" name="form.motif" class="sm:col-span-2"><input wire:model="form.motif" class="form-input"></x-field>
            @if (($form['type'] ?? '') !== 'absence_injustifiee')
                <label class="flex items-center gap-2 text-sm sm:col-span-2"><input type="checkbox" wire:model="form.approuver" class="rounded border-slate-300 text-brand-600"> Approuver directement</label>
            @else
                <p class="text-xs text-slate-500 sm:col-span-2">Une absence injustifiée est retenue sur le salaire (prorata 1/30 par jour pour les mensuels).</p>
            @endif
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-conge">Enregistrer</x-btn></x-slot:footer>
    </x-modal>
</div>
