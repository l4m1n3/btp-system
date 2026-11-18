<div>
    <x-page-header :title="$marche->intitule">
        <x-slot:breadcrumb><a href="{{ route('marches.index') }}" wire:navigate class="link">Marchés</a> / {{ $marche->reference }}</x-slot:breadcrumb>
        <x-slot:actions>
            <x-badge :value="$marche->statut" class="text-sm" />
            @can('marches.manage')<x-btn variant="secondary" wire:click="modifier({{ $marche->id }})">Modifier</x-btn>@endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-5">
        <x-stat label="Montant HT" :value="fcfa($marche->montant_ht)" :sub="'TTC : '.fcfa($marche->montant_ttc)" />
        <x-stat label="Facturé (HT)" :value="fcfa($marche->montant_facture)" :sub="$marche->taux_facturation.' % du marché'" accent="blue" />
        <x-stat label="Encaissé" :value="fcfa($marche->montant_encaisse)" accent="green" />
        <x-stat label="Reste à réaliser" :value="fcfa($marche->reste_a_realiser)" accent="amber" />
        <x-stat label="Coût engagé chantiers" :value="fcfa($marche->chantiers->sum(fn ($c) => $c->coutTotal()))" sub="Dépenses + matériaux" accent="brand" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card title="Informations" class="lg:col-span-1">
            <dl class="space-y-2.5 text-sm">
                @foreach ([
                    'Référence' => $marche->reference,
                    'Client' => $marche->client->nom,
                    'Domaine' => $marche->domaine->label(),
                    'Localisation' => $marche->localisation ?: '—',
                    'TVA' => $marche->taux_tva.' %',
                    'Retenue de garantie' => $marche->taux_retenue_garantie.' %',
                    'Avance de démarrage' => fcfa($marche->avance_demarrage),
                    'Soumission' => date_fr($marche->date_soumission),
                    'Signature' => date_fr($marche->date_signature),
                    'Ordre de service' => date_fr($marche->date_ordre_service),
                    'Délai' => $marche->delai_jours ? $marche->delai_jours.' jours' : '—',
                    'Fin prévue' => date_fr($marche->date_fin_prevue),
                    'Réception' => date_fr($marche->date_reception),
                ] as $k => $v)
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ $k }}</dt><dd class="text-right font-medium text-slate-800">{{ $v }}</dd></div>
                @endforeach
            </dl>
            @if ($marche->description)<p class="mt-4 border-t border-slate-100 pt-3 text-sm text-slate-600">{{ $marche->description }}</p>@endif
        </x-card>

        <div class="space-y-6 lg:col-span-2">
            <x-card title="Décomptes et encaissements" :padding="false">
                <x-slot:actions>@can('marches.manage')<x-btn size="sm" wire:click="nouveauDecompte">+ Décompte</x-btn>@endcan</x-slot:actions>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead><tr><th>N°</th><th>Date</th><th class="text-right">Montant HT</th><th class="text-right">Payé</th><th>Paiement</th><th>Statut</th><th></th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($marche->decomptes as $d)
                                <tr wire:key="d{{ $d->id }}">
                                    <td>Décompte n° {{ $d->numero }}</td>
                                    <td>{{ date_fr($d->date_decompte) }}</td>
                                    <td class="text-right tabular-nums">{{ fcfa($d->montant_ht, false) }}</td>
                                    <td class="text-right tabular-nums">{{ fcfa($d->montant_paye, false) }}</td>
                                    <td>{{ date_fr($d->date_paiement) }}</td>
                                    <td><x-badge :value="$d->statut" /></td>
                                    <td class="text-right whitespace-nowrap">
                                        @can('marches.manage')
                                            <x-btn size="sm" variant="ghost" wire:click="modifierDecompte({{ $d->id }})">Modifier</x-btn>
                                            <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="supprimerDecompte({{ $d->id }})" wire:confirm="Supprimer ce décompte ?">×</x-btn>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7"><x-empty message="Aucun décompte." /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card title="Chantiers du marché" :padding="false">
                <x-slot:actions>@can('chantiers.manage')<x-btn size="sm" variant="secondary" :href="route('chantiers.index', ['nouveau' => $marche->id])" wire:navigate>+ Chantier</x-btn>@endcan</x-slot:actions>
                <div class="overflow-x-auto">
                    <table class="table-base">
                        <thead><tr><th>Chantier</th><th>Chef</th><th class="w-40">Avancement</th><th class="text-right">Budget</th><th class="text-right">Coût engagé</th><th>Statut</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($marche->chantiers as $c)
                                <tr>
                                    <td><a href="{{ route('chantiers.show', $c) }}" wire:navigate class="link">{{ $c->nom }}</a><div class="text-xs text-slate-500">{{ $c->code }}</div></td>
                                    <td>{{ $c->chef?->name ?? '—' }}</td>
                                    <td><x-progress :value="$c->avancement" /></td>
                                    <td class="text-right tabular-nums">{{ fcfa($c->budget, false) }}</td>
                                    <td class="text-right tabular-nums">{{ fcfa($c->coutTotal(), false) }}</td>
                                    <td><x-badge :value="$c->statut" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="6"><x-empty message="Aucun chantier rattaché." /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    @include('livewire.marches.form-modal')

    <x-modal wire:model="modalDecompte" :title="$decompteId ? 'Modifier le décompte' : 'Nouveau décompte'">
        <form wire:submit="enregistrerDecompte" id="form-decompte" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Numéro" name="decompte.numero" required><input type="number" wire:model="decompte.numero" class="form-input"></x-field>
            <x-field label="Date du décompte" name="decompte.date_decompte" required><input type="date" wire:model="decompte.date_decompte" class="form-input"></x-field>
            <x-field label="Montant HT" name="decompte.montant_ht" required><input type="number" wire:model="decompte.montant_ht" class="form-input"></x-field>
            <x-field label="Statut" name="decompte.statut"><select wire:model="decompte.statut" class="form-input">@foreach (\App\Enums\StatutDecompte::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <x-field label="Montant encaissé" name="decompte.montant_paye" hint="Le statut passe à « Payé » si un montant est encaissé."><input type="number" wire:model="decompte.montant_paye" class="form-input"></x-field>
            <x-field label="Date de paiement" name="decompte.date_paiement"><input type="date" wire:model="decompte.date_paiement" class="form-input"></x-field>
            <x-field label="Observations" name="decompte.observations" class="sm:col-span-2"><input wire:model="decompte.observations" class="form-input"></x-field>
        </form>
        <x-slot:footer>
            <x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn>
            <x-btn type="submit" form="form-decompte">Enregistrer</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
