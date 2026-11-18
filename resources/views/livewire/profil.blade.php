<div>
    <x-page-header title="Mon profil" :subtitle="auth()->user()->email.' · '.auth()->user()->role->label()" />
    <div class="grid max-w-4xl gap-6 md:grid-cols-2">
        <x-card title="Informations">
            <form wire:submit="enregistrerProfil" class="space-y-4">
                <x-field label="Nom" name="name"><input wire:model="name" class="form-input"></x-field>
                <x-field label="Téléphone" name="telephone"><input wire:model="telephone" class="form-input"></x-field>
                <x-btn type="submit">Enregistrer</x-btn>
            </form>
        </x-card>
        <x-card title="Mot de passe">
            <form wire:submit="changerMotDePasse" class="space-y-4">
                <x-field label="Mot de passe actuel" name="actuel"><input type="password" wire:model="actuel" class="form-input" autocomplete="current-password"></x-field>
                <x-field label="Nouveau mot de passe (8 caractères min.)" name="nouveau"><input type="password" wire:model="nouveau" class="form-input" autocomplete="new-password"></x-field>
                <x-field label="Confirmation" name="nouveau_confirmation"><input type="password" wire:model="nouveau_confirmation" class="form-input" autocomplete="new-password"></x-field>
                <x-btn type="submit">Changer le mot de passe</x-btn>
            </form>
        </x-card>
    </div>
</div>
