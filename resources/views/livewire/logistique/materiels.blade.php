<div>
    <x-page-header title="Matériel & engins" subtitle="Parc, affectations aux chantiers, pannes et maintenance">
        <x-slot:actions>@can('logistique.manage')<x-btn wire:click="editer">+ Nouveau matériel</x-btn>@endcan</x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-5">
        @foreach (\App\Enums\EtatMateriel::cases() as $e)
            <button wire:click="$set('etat', '{{ $etat === $e->value ? '' : $e->value }}')" @class(['rounded-xl border bg-white p-3 text-left shadow-xs', 'border-brand-500 ring-2 ring-brand-100' => $etat === $e->value, 'border-slate-200 hover:border-slate-300' => $etat !== $e->value])>
                <p class="text-xs text-slate-500">{{ $e->label() }}</p><p class="text-xl font-bold">{{ $compteurs[$e->value] ?? 0 }}</p>
            </button>
        @endforeach
    </div>

    <x-card :padding="false">
        <div class="flex flex-wrap gap-2 border-b border-slate-100 p-3">
            <input type="search" wire:model.live.debounce.300ms="recherche" placeholder="Code, désignation, immatriculation…" class="form-input max-w-xs">
            <select wire:model.live="type" class="form-input w-auto"><option value="">Tous types</option>@foreach (\App\Enums\TypeMateriel::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Code</th><th>Désignation</th><th>Type</th><th>Chantier</th><th>État</th><th></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($materiels as $m)
                        <tr wire:key="mat{{ $m->id }}">
                            <td class="font-mono text-xs">{{ $m->code }}</td>
                            <td><span class="font-medium">{{ $m->designation }}</span><div class="text-xs text-slate-500">{{ $m->marque }} {{ $m->immatriculation }}</div></td>
                            <td>{{ $m->type->label() }}</td>
                            <td>@if($m->chantier)<a href="{{ route('chantiers.show', $m->chantier) }}" wire:navigate class="link">{{ $m->chantier->nom }}</a>@else<span class="text-slate-400">Au parc</span>@endif</td>
                            <td><x-badge :value="$m->etat" /></td>
                            <td class="text-right whitespace-nowrap">
                                <x-btn size="sm" variant="ghost" wire:click="voirHistorique({{ $m->id }})">Historique</x-btn>
                                @can('logistique.manage')
                                    <div x-data="{ o: false }" class="relative inline-block text-left">
                                        <x-btn size="sm" variant="secondary" x-on:click="o = !o">Actions ▾</x-btn>
                                        <div x-show="o" x-cloak x-on:click.outside="o = false" class="absolute right-0 z-10 mt-1 w-48 rounded-lg border border-slate-200 bg-white py-1 text-left text-sm shadow-lg">
                                            <button class="block w-full px-3 py-1.5 text-left hover:bg-slate-50" wire:click="ouvrirAffectation({{ $m->id }})" x-on:click="o = false">Affecter à un chantier</button>
                                            @if ($m->chantier_id)<button class="block w-full px-3 py-1.5 text-left hover:bg-slate-50" wire:click="liberer({{ $m->id }})" x-on:click="o = false">Retour au parc</button>@endif
                                            <button class="block w-full px-3 py-1.5 text-left hover:bg-slate-50" wire:click="ouvrirMaintenance({{ $m->id }})" x-on:click="o = false">Saisir une maintenance</button>
                                            <button class="block w-full px-3 py-1.5 text-left text-red-600 hover:bg-slate-50" wire:click="changerEtat({{ $m->id }}, 'en_panne')" x-on:click="o = false">Déclarer en panne</button>
                                            <button class="block w-full px-3 py-1.5 text-left hover:bg-slate-50" wire:click="changerEtat({{ $m->id }}, 'disponible')" x-on:click="o = false">Remettre en service</button>
                                            <button class="block w-full px-3 py-1.5 text-left hover:bg-slate-50" wire:click="editer({{ $m->id }})" x-on:click="o = false">Modifier la fiche</button>
                                        </div>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty message="Aucun matériel." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $materiels->links() }}</div>
    </x-card>

    <x-modal wire:model="modal" :title="$editId ? 'Modifier le matériel' : 'Nouveau matériel'">
        <form wire:submit="enregistrer" id="form-mat" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Code" name="form.code" required><input wire:model="form.code" class="form-input"></x-field>
            <x-field label="Type" name="form.type"><select wire:model="form.type" class="form-input">@foreach (\App\Enums\TypeMateriel::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <x-field label="Désignation" name="form.designation" required class="sm:col-span-2"><input wire:model="form.designation" class="form-input" placeholder="ex. Pelle hydraulique 20 t"></x-field>
            <x-field label="Marque / modèle" name="form.marque"><input wire:model="form.marque" class="form-input"></x-field>
            <x-field label="Immatriculation / n° série" name="form.immatriculation"><input wire:model="form.immatriculation" class="form-input"></x-field>
            <x-field label="Date d'acquisition" name="form.date_acquisition"><input type="date" wire:model="form.date_acquisition" class="form-input"></x-field>
            <x-field label="Valeur d'acquisition" name="form.valeur_acquisition"><input type="number" wire:model="form.valeur_acquisition" class="form-input"></x-field>
            <x-field label="Notes" name="form.notes" class="sm:col-span-2"><textarea wire:model="form.notes" rows="2" class="form-input"></textarea></x-field>
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-mat">Enregistrer</x-btn></x-slot:footer>
    </x-modal>

    <x-modal wire:model="modalAffectation" title="Affecter à un chantier" max-width="md">
        <form wire:submit="affecter" id="form-aff" class="space-y-4">
            <x-field label="Chantier" name="affectation.chantier_id" required><select wire:model="affectation.chantier_id" class="form-input"><option value="">—</option>@foreach ($chantiers as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
            <x-field label="À partir du" name="affectation.date_debut" required><input type="date" wire:model="affectation.date_debut" class="form-input"></x-field>
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-aff">Affecter</x-btn></x-slot:footer>
    </x-modal>

    <x-modal wire:model="modalMaintenance" title="Saisir une maintenance">
        <form wire:submit="enregistrerMaintenance" id="form-maint" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Date" name="maintenance.date_maintenance" required><input type="date" wire:model="maintenance.date_maintenance" class="form-input"></x-field>
            <x-field label="Type" name="maintenance.type"><select wire:model="maintenance.type" class="form-input">@foreach (\App\Models\Maintenance::TYPES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <x-field label="Description" name="maintenance.description" required class="sm:col-span-2"><input wire:model="maintenance.description" class="form-input" placeholder="ex. Remplacement flexible hydraulique"></x-field>
            <x-field label="Coût (FCFA)" name="maintenance.cout"><input type="number" wire:model="maintenance.cout" class="form-input"></x-field>
            <x-field label="État après intervention" name="maintenance.etat_apres"><select wire:model="maintenance.etat_apres" class="form-input"><option value="">Inchangé</option>@foreach (\App\Enums\EtatMateriel::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <label class="flex items-center gap-2 text-sm sm:col-span-2"><input type="checkbox" wire:model="maintenance.creer_depense" class="rounded border-slate-300 text-brand-600"> Enregistrer le coût en dépense (chantier si affecté, sinon interne)</label>
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-maint">Enregistrer</x-btn></x-slot:footer>
    </x-modal>

    <x-modal wire:model="modalHistorique" :title="'Historique — '.($historique?->designation ?? '')" max-width="3xl">
        @if ($historique)
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <p class="mb-2 text-xs font-semibold text-slate-500 uppercase">Affectations</p>
                    <ul class="space-y-1 text-sm">
                        @forelse ($historique->affectations as $a)
                            <li>{{ $a->chantier->nom }} <span class="text-slate-500">— du {{ date_fr($a->date_debut) }} {{ $a->date_fin ? 'au '.date_fr($a->date_fin) : '(en cours)' }}</span></li>
                        @empty
                            <li class="text-slate-400">Aucune affectation.</li>
                        @endforelse
                    </ul>
                </div>
                <div>
                    <p class="mb-2 text-xs font-semibold text-slate-500 uppercase">Maintenances ({{ fcfa($historique->maintenances->sum('cout')) }})</p>
                    <ul class="space-y-1 text-sm">
                        @forelse ($historique->maintenances as $mt)
                            <li>{{ date_fr($mt->date_maintenance) }} — {{ $mt->description }} <span class="text-slate-500">({{ fcfa($mt->cout) }})</span></li>
                        @empty
                            <li class="text-slate-400">Aucune maintenance.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    </x-modal>
</div>
