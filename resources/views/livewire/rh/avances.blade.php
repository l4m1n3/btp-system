<div>
    <x-page-header title="Avances sur salaire" subtitle="Retenues automatiquement sur la paie du mois indiqué">
        <x-slot:actions>@can('rh.manage')<x-btn wire:click="creer">+ Nouvelle avance</x-btn>@endcan</x-slot:actions>
    </x-page-header>
    <x-card :padding="false">
        <div class="flex flex-wrap items-center gap-3 border-b border-slate-100 p-3">
            <input type="month" wire:model.live="periode" class="form-input w-auto" title="Mois de retenue">
            <span class="text-sm text-slate-600">Total : <b>{{ fcfa($total) }}</b></span>
        </div>
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Date</th><th>Employé</th><th>Motif</th><th>Retenue sur</th><th class="text-right">Montant</th><th>État</th><th></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($avances as $a)
                        <tr wire:key="av{{ $a->id }}">
                            <td>{{ date_fr($a->date_avance) }}</td>
                            <td>{{ $a->employe->nom_complet }}</td>
                            <td>{{ $a->motif }}</td>
                            <td>{{ $a->periode_retenue }}</td>
                            <td class="text-right tabular-nums">{{ fcfa($a->montant) }}</td>
                            <td>@if($a->bulletin_id)<x-badge color="green">Retenue</x-badge>@else<x-badge color="amber">À retenir</x-badge>@endif</td>
                            <td class="text-right">@can('rh.manage')@unless($a->bulletin_id)<x-btn size="sm" variant="ghost" class="text-red-600" wire:click="supprimer({{ $a->id }})" wire:confirm="Supprimer cette avance ?">×</x-btn>@endunless @endcan</td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-empty /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $avances->links() }}</div>
    </x-card>

    <x-modal wire:model="modal" title="Nouvelle avance" max-width="lg">
        <form wire:submit="enregistrer" id="form-av" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Employé" name="form.employe_id" required class="sm:col-span-2"><select wire:model="form.employe_id" class="form-input"><option value="">—</option>@foreach ($employes as $e)<option value="{{ $e->id }}">{{ $e->nom }} {{ $e->prenom }} ({{ $e->matricule }})</option>@endforeach</select></x-field>
            <x-field label="Date" name="form.date_avance" required><input type="date" wire:model="form.date_avance" class="form-input"></x-field>
            <x-field label="Montant" name="form.montant" required><input type="number" wire:model="form.montant" class="form-input"></x-field>
            <x-field label="Retenir sur la paie de" name="form.periode_retenue" required><input type="month" wire:model="form.periode_retenue" class="form-input"></x-field>
            <x-field label="Motif" name="form.motif"><input wire:model="form.motif" class="form-input"></x-field>
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-av">Enregistrer</x-btn></x-slot:footer>
    </x-modal>
</div>
