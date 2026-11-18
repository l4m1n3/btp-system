<div>
    <x-page-header title="Employés" subtitle="Personnel, contrats et affectations">
        <x-slot:actions>@can('rh.manage')<x-btn wire:click="editer">+ Nouvel employé</x-btn>@endcan</x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <x-stat label="Effectif actif" :value="$effectif" accent="navy" />
        <x-stat label="Masse salariale mensuelle (base)" :value="fcfa($masseMensuelle)" sub="Hors journaliers, primes et heures sup" accent="brand" />
        <x-stat label="Journaliers actifs" :value="$journaliers" accent="amber" />
    </div>

    <x-card :padding="false">
        <div class="flex flex-wrap gap-2 border-b border-slate-100 p-3">
            <input type="search" wire:model.live.debounce.300ms="recherche" placeholder="Nom, matricule, fonction…" class="form-input max-w-xs">
            <select wire:model.live="statut" class="form-input w-auto"><option value="">Tous statuts</option>@foreach (\App\Enums\StatutEmploye::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
            <select wire:model.live="contrat" class="form-input w-auto"><option value="">Tous contrats</option>@foreach (\App\Enums\TypeContrat::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
            <select wire:model.live="chantier" class="form-input w-auto"><option value="">Toutes affectations</option><option value="siege">Siège / non affecté</option>@foreach ($chantiers as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Employé</th><th>Fonction</th><th>Contrat</th><th>Affectation</th><th class="text-right">Rémunération</th><th>Statut</th><th></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employes as $e)
                        <tr wire:key="e{{ $e->id }}">
                            <td><a href="{{ route('employes.show', $e) }}" wire:navigate class="link">{{ $e->nom_complet }}</a><div class="font-mono text-xs text-slate-500">{{ $e->matricule }}</div></td>
                            <td>{{ $e->fonction }}<div class="text-xs text-slate-500">{{ $e->categorie }}</div></td>
                            <td><x-badge :value="$e->type_contrat" />@if($e->date_fin_contrat)<div class="mt-0.5 text-xs {{ $e->contratExpireBientot() ? 'font-medium text-red-600' : 'text-slate-500' }}">fin {{ date_fr($e->date_fin_contrat) }}</div>@endif</td>
                            <td>{{ $e->chantier?->nom ?? 'Siège' }}</td>
                            <td class="text-right tabular-nums whitespace-nowrap">{{ $e->mode_remuneration === \App\Enums\ModeRemuneration::Mensuel ? fcfa($e->salaire_base).' /mois' : fcfa($e->taux_journalier).' /jour' }}</td>
                            <td><x-badge :value="$e->statut" /></td>
                            <td class="text-right">@can('rh.manage')<x-btn size="sm" variant="ghost" wire:click="editer({{ $e->id }})">Modifier</x-btn>@endcan</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty message="Aucun employé." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $employes->links() }}</div>
    </x-card>

    <x-modal wire:model="modal" :title="$editId ? 'Modifier l\'employé' : 'Nouvel employé'" max-width="4xl">
        <form wire:submit="enregistrer" id="form-emp" class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-4">
                <x-field label="Matricule" name="form.matricule" required><input wire:model="form.matricule" class="form-input"></x-field>
                <x-field label="Nom" name="form.nom" required><input wire:model="form.nom" class="form-input"></x-field>
                <x-field label="Prénom" name="form.prenom" required><input wire:model="form.prenom" class="form-input"></x-field>
                <x-field label="Sexe" name="form.sexe"><select wire:model="form.sexe" class="form-input"><option value="">—</option><option value="M">Masculin</option><option value="F">Féminin</option></select></x-field>
                <x-field label="Date de naissance" name="form.date_naissance"><input type="date" wire:model="form.date_naissance" class="form-input"></x-field>
                <x-field label="Téléphone" name="form.telephone"><input wire:model="form.telephone" class="form-input"></x-field>
                <x-field label="N° CNSS" name="form.numero_cnss"><input wire:model="form.numero_cnss" class="form-input"></x-field>
                <x-field label="Adresse" name="form.adresse"><input wire:model="form.adresse" class="form-input"></x-field>
            </div>
            <div class="grid gap-4 border-t border-slate-100 pt-4 sm:grid-cols-4">
                <x-field label="Fonction" name="form.fonction" required><input wire:model="form.fonction" class="form-input" placeholder="ex. Maçon, conducteur d'engins"></x-field>
                <x-field label="Catégorie" name="form.categorie"><input wire:model="form.categorie" class="form-input" placeholder="ex. Ouvrier qualifié"></x-field>
                <x-field label="Affectation" name="form.chantier_id"><select wire:model="form.chantier_id" class="form-input"><option value="">Siège</option>@foreach ($chantiers as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
                <x-field label="Statut" name="form.statut"><select wire:model="form.statut" class="form-input">@foreach (\App\Enums\StatutEmploye::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
                <x-field label="Contrat" name="form.type_contrat"><select wire:model.live="form.type_contrat" class="form-input">@foreach (\App\Enums\TypeContrat::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
                <x-field label="Date d'embauche" name="form.date_embauche" required><input type="date" wire:model="form.date_embauche" class="form-input"></x-field>
                <x-field label="Fin de contrat" name="form.date_fin_contrat" :required="($form['type_contrat'] ?? '') === 'cdd'"><input type="date" wire:model="form.date_fin_contrat" class="form-input"></x-field>
            </div>
            <div class="grid gap-4 border-t border-slate-100 pt-4 sm:grid-cols-4">
                <x-field label="Rémunération" name="form.mode_remuneration"><select wire:model.live="form.mode_remuneration" class="form-input">@foreach (\App\Enums\ModeRemuneration::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
                @if (($form['mode_remuneration'] ?? '') === 'mensuel')
                    <x-field label="Salaire de base mensuel" name="form.salaire_base" required><input type="number" wire:model="form.salaire_base" class="form-input"></x-field>
                @else
                    <x-field label="Taux journalier" name="form.taux_journalier" required><input type="number" wire:model="form.taux_journalier" class="form-input"></x-field>
                @endif
                <x-field label="Mode de paiement" name="form.mode_paiement"><select wire:model="form.mode_paiement" class="form-input">@foreach (\App\Enums\ModePaiement::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
                <x-field label="N° de compte / mobile money" name="form.numero_compte"><input wire:model="form.numero_compte" class="form-input"></x-field>
            </div>
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-emp">Enregistrer</x-btn></x-slot:footer>
    </x-modal>
</div>
