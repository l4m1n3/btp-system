<x-modal wire:model="modal" :title="$editId ? 'Modifier le chantier' : 'Nouveau chantier'" max-width="3xl">
    <form wire:submit="enregistrer" id="form-chantier" class="grid gap-4 sm:grid-cols-3">
        <x-field label="Code" name="form.code" required><input wire:model="form.code" class="form-input"></x-field>
        <x-field label="Nom du chantier" name="form.nom" required class="sm:col-span-2"><input wire:model="form.nom" class="form-input"></x-field>
        <x-field label="Marché" name="form.marche_id"><select wire:model="form.marche_id" class="form-input"><option value="">Hors marché</option>@foreach ($marches as $id => $ref)<option value="{{ $id }}">{{ $ref }}</option>@endforeach</select></x-field>
        <x-field label="Chef de chantier" name="form.chef_chantier_id"><select wire:model="form.chef_chantier_id" class="form-input"><option value="">—</option>@foreach ($chefs as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
        <x-field label="Statut" name="form.statut"><select wire:model="form.statut" class="form-input">@foreach (\App\Enums\StatutChantier::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
        <x-field label="Localisation" name="form.localisation"><input wire:model="form.localisation" class="form-input"></x-field>
        <x-field label="Budget (FCFA)" name="form.budget" required hint="Budget d'exécution (déboursé sec prévu)"><input type="number" wire:model="form.budget" class="form-input"></x-field>
        <x-field label="Début" name="form.date_debut"><input type="date" wire:model="form.date_debut" class="form-input"></x-field>
        <x-field label="Fin prévue" name="form.date_fin_prevue"><input type="date" wire:model="form.date_fin_prevue" class="form-input"></x-field>
        <x-field label="Fin réelle" name="form.date_fin_reelle"><input type="date" wire:model="form.date_fin_reelle" class="form-input"></x-field>
        <x-field label="Description" name="form.description" class="sm:col-span-3"><textarea wire:model="form.description" rows="2" class="form-input"></textarea></x-field>
    </form>
    <x-slot:footer>
        <x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn>
        <x-btn type="submit" form="form-chantier">Enregistrer</x-btn>
    </x-slot:footer>
</x-modal>
