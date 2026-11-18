<div>
    <x-page-header title="Clients" subtitle="Maîtres d'ouvrage et donneurs d'ordre">
        <x-slot:actions>
            @can('marches.manage')<x-btn wire:click="creer">+ Nouveau client</x-btn>@endcan
        </x-slot:actions>
    </x-page-header>

    <x-card :padding="false">
        <div class="border-b border-slate-100 p-3">
            <input type="search" wire:model.live.debounce.300ms="recherche" placeholder="Rechercher un client…" class="form-input max-w-xs">
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Nom</th><th>Type</th><th>Contact</th><th class="text-right">Marchés</th><th class="text-right">Montant HT</th><th></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $c)
                        <tr wire:key="c{{ $c->id }}">
                            <td class="font-medium text-slate-900">{{ $c->nom }}</td>
                            <td>{{ \App\Models\Client::TYPES[$c->type] ?? $c->type }}</td>
                            <td>{{ $c->contact }}<div class="text-xs text-slate-500">{{ $c->telephone }} {{ $c->email }}</div></td>
                            <td class="text-right"><a class="link" href="{{ route('marches.index', ['client' => $c->id]) }}" wire:navigate>{{ $c->marches_count }}</a></td>
                            <td class="text-right tabular-nums">{{ fcfa($c->marches_sum_montant_ht ?? 0) }}</td>
                            <td class="text-right whitespace-nowrap">
                                @can('marches.manage')
                                    <x-btn size="sm" variant="ghost" wire:click="modifier({{ $c->id }})">Modifier</x-btn>
                                    <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="supprimer({{ $c->id }})" wire:confirm="Supprimer ce client ?">Supprimer</x-btn>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $clients->links() }}</div>
    </x-card>

    <x-modal wire:model="modal" :title="$editId ? 'Modifier le client' : 'Nouveau client'">
        <form wire:submit="enregistrer" id="form-client" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Nom" name="form.nom" required class="sm:col-span-2"><input wire:model="form.nom" class="form-input"></x-field>
            <x-field label="Type" name="form.type"><select wire:model="form.type" class="form-input">@foreach (\App\Models\Client::TYPES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <x-field label="Personne de contact" name="form.contact"><input wire:model="form.contact" class="form-input"></x-field>
            <x-field label="Téléphone" name="form.telephone"><input wire:model="form.telephone" class="form-input"></x-field>
            <x-field label="E-mail" name="form.email"><input type="email" wire:model="form.email" class="form-input"></x-field>
            <x-field label="Adresse" name="form.adresse" class="sm:col-span-2"><input wire:model="form.adresse" class="form-input"></x-field>
            <x-field label="Notes" name="form.notes" class="sm:col-span-2"><textarea wire:model="form.notes" rows="2" class="form-input"></textarea></x-field>
        </form>
        <x-slot:footer>
            <x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn>
            <x-btn type="submit" form="form-client">Enregistrer</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
