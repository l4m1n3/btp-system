<div>
    <x-page-header :title="$rapport ? 'Rapport du '.date_fr($rapport->date_rapport) : 'Nouveau rapport journalier'">
        <x-slot:breadcrumb><a href="{{ route('chantiers.show', ['chantier' => $chantier, 'onglet' => 'journal']) }}" wire:navigate class="link">{{ $chantier->nom }}</a> / Journal de chantier</x-slot:breadcrumb>
    </x-page-header>

    <form wire:submit="enregistrer" class="space-y-6">
        <fieldset @disabled($lectureSeule) class="space-y-6">
            <x-card>
                <div class="grid gap-4 sm:grid-cols-3">
                    <x-field label="Date" name="date_rapport" required><input type="date" wire:model="date_rapport" class="form-input" max="{{ now()->toDateString() }}"></x-field>
                    <x-field label="Météo" name="meteo" class="sm:col-span-2"><input wire:model="meteo" class="form-input" placeholder="ex. Ensoleillé, vent de sable l'après-midi"></x-field>
                </div>
            </x-card>

            @foreach (\App\Enums\TypeLigneJournal::cases() as $type)
                <x-card :title="$type->label()">
                    <x-slot:actions>@unless($lectureSeule)<x-btn size="sm" variant="secondary" wire:click="ajouterLigne('{{ $type->value }}')">+ Ligne</x-btn>@endunless</x-slot:actions>
                    <div class="space-y-2">
                        @foreach ($lignes as $i => $l)
                            @continue($l['type'] !== $type->value)
                            <div class="grid items-start gap-2 sm:grid-cols-12" wire:key="l{{ $i }}">
                                @if ($type === \App\Enums\TypeLigneJournal::Travaux)
                                    <select wire:model.live="lignes.{{ $i }}.tache_id" class="form-input sm:col-span-4"><option value="">Tâche du planning (facultatif)</option>@foreach ($taches as $t)<option value="{{ $t->id }}">{{ $t->libelle }} ({{ $t->avancement }} %)</option>@endforeach</select>
                                    <input wire:model="lignes.{{ $i }}.designation" class="form-input sm:col-span-5" placeholder="Travaux réalisés">
                                    <div class="flex items-center gap-1 sm:col-span-2"><input type="number" min="0" max="100" wire:model="lignes.{{ $i }}.avancement" class="form-input" placeholder="%"><span class="text-xs text-slate-500">%</span></div>
                                @else
                                    <input wire:model="lignes.{{ $i }}.designation" class="form-input sm:col-span-6" placeholder="{{ ['personnel' => 'ex. Maçons, manœuvres…', 'materiel' => 'ex. Bétonnière, camion-benne…', 'approvisionnement' => 'ex. Ciment CPA 45, sable…'][$type->value] ?? '' }}">
                                    <input type="number" step="0.01" wire:model="lignes.{{ $i }}.quantite" class="form-input sm:col-span-2" placeholder="{{ $type->value === 'personnel' ? 'Effectif' : 'Quantité' }}">
                                    <input wire:model="lignes.{{ $i }}.unite" class="form-input sm:col-span-3" placeholder="{{ $type->value === 'personnel' ? 'personnes' : 'Unité' }}">
                                @endif
                                @unless($lectureSeule)<button type="button" wire:click="retirerLigne({{ $i }})" class="rounded px-2 py-2 text-slate-400 hover:bg-red-50 hover:text-red-600 sm:col-span-1" title="Retirer">×</button>@endunless
                                @error("lignes.$i.designation")<p class="text-xs text-red-600 sm:col-span-12">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </div>
                    @if ($type === \App\Enums\TypeLigneJournal::Travaux)<p class="mt-2 text-xs text-slate-500">L'avancement indiqué sur une tâche met à jour le planning (il ne peut pas reculer depuis le journal).</p>@endif
                </x-card>
            @endforeach

            <x-card>
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-field label="Observations" name="observations"><textarea wire:model="observations" rows="3" class="form-input"></textarea></x-field>
                    <x-field label="Incidents / événements" name="incidents"><textarea wire:model="incidents" rows="3" class="form-input" placeholder="Accident, arrêt de chantier, visite du maître d'ouvrage…"></textarea></x-field>
                </div>
            </x-card>
        </fieldset>

        <div class="flex justify-end gap-2">
            <x-btn variant="secondary" :href="route('chantiers.show', ['chantier' => $chantier, 'onglet' => 'journal'])" wire:navigate>Retour</x-btn>
            @unless($lectureSeule)<x-btn type="submit">Enregistrer le rapport</x-btn>@endunless
        </div>
    </form>
</div>
