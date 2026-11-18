<x-modal wire:model="modal" :title="$editId ? 'Modifier le marché' : 'Nouveau marché'" max-width="3xl">
    <form wire:submit="enregistrer" id="form-marche" class="grid gap-4 sm:grid-cols-3">
        <x-field label="Référence" name="form.reference" required><input wire:model="form.reference" class="form-input" placeholder="ex. AO-2026-014"></x-field>
        <x-field label="Intitulé" name="form.intitule" required class="sm:col-span-2"><input wire:model="form.intitule" class="form-input"></x-field>
        <x-field label="Client" name="form.client_id" required>
            <select wire:model="form.client_id" class="form-input"><option value="">—</option>@foreach (\App\Models\Client::orderBy('nom')->pluck('nom', 'id') as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select>
        </x-field>
        <x-field label="Domaine" name="form.domaine"><select wire:model="form.domaine" class="form-input">@foreach (\App\Enums\Domaine::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
        <x-field label="Statut" name="form.statut"><select wire:model="form.statut" class="form-input">@foreach (\App\Enums\StatutMarche::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
        <x-field label="Localisation" name="form.localisation"><input wire:model="form.localisation" class="form-input"></x-field>
        <x-field label="Montant HT (FCFA)" name="form.montant_ht" required><input type="number" step="1" wire:model="form.montant_ht" class="form-input"></x-field>
        <x-field label="TVA (%)" name="form.taux_tva"><input type="number" step="0.01" wire:model="form.taux_tva" class="form-input"></x-field>
        <x-field label="Retenue de garantie (%)" name="form.taux_retenue_garantie"><input type="number" step="0.01" wire:model="form.taux_retenue_garantie" class="form-input"></x-field>
        <x-field label="Avance de démarrage" name="form.avance_demarrage"><input type="number" step="1" wire:model="form.avance_demarrage" class="form-input"></x-field>
        <x-field label="Date de soumission" name="form.date_soumission"><input type="date" wire:model="form.date_soumission" class="form-input"></x-field>
        <x-field label="Date de signature" name="form.date_signature"><input type="date" wire:model="form.date_signature" class="form-input"></x-field>
        <x-field label="Ordre de service" name="form.date_ordre_service"><input type="date" wire:model="form.date_ordre_service" class="form-input"></x-field>
        <x-field label="Délai (jours)" name="form.delai_jours"><input type="number" wire:model="form.delai_jours" class="form-input"></x-field>
        <x-field label="Fin prévue" name="form.date_fin_prevue" hint="Calculée si vide (OS + délai)"><input type="date" wire:model="form.date_fin_prevue" class="form-input"></x-field>
        <x-field label="Réception" name="form.date_reception"><input type="date" wire:model="form.date_reception" class="form-input"></x-field>
        <x-field label="Description" name="form.description" class="sm:col-span-3"><textarea wire:model="form.description" rows="2" class="form-input"></textarea></x-field>
    </form>
    <x-slot:footer>
        <x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn>
        <x-btn type="submit" form="form-marche">Enregistrer</x-btn>
    </x-slot:footer>
</x-modal>
