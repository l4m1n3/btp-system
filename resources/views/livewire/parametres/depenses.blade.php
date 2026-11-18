<div>
    <x-page-header title="Catégories & fournisseurs" subtitle="Référentiels utilisés pour la saisie des dépenses" />

    <div class="grid gap-6 lg:grid-cols-2">
        <x-card title="Catégories de dépenses" :padding="false">
            <x-slot:actions><x-btn size="sm" wire:click="editerCategorie">+ Catégorie</x-btn></x-slot:actions>
            @foreach (\App\Enums\PorteeDepense::cases() as $p)
                <p class="bg-slate-50 px-4 py-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ $p->label() }}</p>
                <ul class="divide-y divide-slate-100">
                    @forelse ($categories[$p->value] ?? [] as $c)
                        <li class="flex items-center justify-between gap-3 px-4 py-2.5 text-sm" wire:key="cat{{ $c->id }}">
                            <span>{{ $c->nom }} @if($c->systeme)<x-badge color="slate">auto</x-badge>@endif <span class="text-xs text-slate-400">· {{ $c->depenses_count }} dépense(s)</span></span>
                            @unless ($c->systeme)
                                <span class="whitespace-nowrap">
                                    <x-btn size="sm" variant="ghost" wire:click="editerCategorie({{ $c->id }})">Modifier</x-btn>
                                    <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="supprimerCategorie({{ $c->id }})" wire:confirm="Supprimer cette catégorie ?">×</x-btn>
                                </span>
                            @endunless
                        </li>
                    @empty
                        <li><x-empty message="Aucune catégorie." /></li>
                    @endforelse
                </ul>
            @endforeach
        </x-card>

        <x-card title="Fournisseurs" :padding="false">
            <x-slot:actions><x-btn size="sm" wire:click="editerFournisseur">+ Fournisseur</x-btn></x-slot:actions>
            <div class="border-b border-slate-100 p-3"><input type="search" wire:model.live.debounce.300ms="rechercheFournisseur" placeholder="Rechercher…" class="form-input"></div>
            <table class="table-base">
                <thead><tr><th>Fournisseur</th><th class="text-right">Achats</th><th></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($fournisseurs as $f)
                        <tr wire:key="f{{ $f->id }}">
                            <td><span class="font-medium">{{ $f->nom }}</span><div class="text-xs text-slate-500">{{ $f->telephone }} {{ $f->nif ? '· NIF '.$f->nif : '' }}</div></td>
                            <td class="text-right tabular-nums">{{ fcfa($f->depenses_sum_montant ?? 0) }}<div class="text-xs text-slate-400">{{ $f->depenses_count }} dépense(s)</div></td>
                            <td class="text-right whitespace-nowrap">
                                <x-btn size="sm" variant="ghost" wire:click="editerFournisseur({{ $f->id }})">Modifier</x-btn>
                                <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="supprimerFournisseur({{ $f->id }})" wire:confirm="Supprimer ce fournisseur ?">×</x-btn>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><x-empty message="Aucun fournisseur." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </div>

    <x-modal wire:model="modalCategorie" :title="$categorieId ? 'Modifier la catégorie' : 'Nouvelle catégorie'" max-width="md">
        <form wire:submit="enregistrerCategorie" id="form-cat" class="space-y-4">
            <x-field label="Nom" name="categorie.nom" required><input wire:model="categorie.nom" class="form-input"></x-field>
            <x-field label="Type" name="categorie.portee"><select wire:model="categorie.portee" class="form-input">@foreach (\App\Enums\PorteeDepense::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <x-field label="Description" name="categorie.description"><input wire:model="categorie.description" class="form-input"></x-field>
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-cat">Enregistrer</x-btn></x-slot:footer>
    </x-modal>

    <x-modal wire:model="modalFournisseur" :title="$fournisseurId ? 'Modifier le fournisseur' : 'Nouveau fournisseur'">
        <form wire:submit="enregistrerFournisseur" id="form-four" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Nom" name="fournisseur.nom" required class="sm:col-span-2"><input wire:model="fournisseur.nom" class="form-input"></x-field>
            <x-field label="NIF" name="fournisseur.nif"><input wire:model="fournisseur.nif" class="form-input"></x-field>
            <x-field label="Téléphone" name="fournisseur.telephone"><input wire:model="fournisseur.telephone" class="form-input"></x-field>
            <x-field label="E-mail" name="fournisseur.email"><input wire:model="fournisseur.email" class="form-input"></x-field>
            <x-field label="Adresse" name="fournisseur.adresse"><input wire:model="fournisseur.adresse" class="form-input"></x-field>
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-four">Enregistrer</x-btn></x-slot:footer>
    </x-modal>
</div>
