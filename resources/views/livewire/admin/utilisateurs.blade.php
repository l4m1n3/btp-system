<div>
    <x-page-header title="Utilisateurs" subtitle="Comptes d'accès et rôles">
        <x-slot:actions><x-btn wire:click="editer">+ Nouvel utilisateur</x-btn></x-slot:actions>
    </x-page-header>

    <div class="mb-4 rounded-lg border border-slate-200 bg-white p-4 text-xs text-slate-600">
        <p class="mb-1 font-semibold text-slate-800">Rôles</p>
        <b>Administrateur</b> : tout. <b>Direction</b> : vue complète, marchés, chantiers, validation des dépenses, clôture de paie. <b>Comptable</b> : dépenses (saisie et validation), rapports, clôture de paie. <b>Chef de chantier</b> : ses chantiers uniquement (planning, journal, dépenses, pointage, sorties de stock). <b>RH</b> : employés, pointage, congés, avances, préparation de la paie. <b>Logistique</b> : matériel et stock.
    </div>

    <x-card :padding="false">
        <table class="table-base">
            <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th>Employé lié</th><th>Dernière connexion</th><th>État</th><th></th></tr></thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($utilisateurs as $u)
                    <tr wire:key="u{{ $u->id }}">
                        <td class="font-medium">{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td><x-badge :value="$u->role" /></td>
                        <td>{{ $u->employe?->nom_complet ?? '—' }}</td>
                        <td>{{ $u->derniere_connexion?->diffForHumans() ?? 'Jamais' }}</td>
                        <td>@if($u->actif)<x-badge color="green">Actif</x-badge>@else<x-badge color="slate">Désactivé</x-badge>@endif</td>
                        <td class="text-right"><x-btn size="sm" variant="ghost" wire:click="editer({{ $u->id }})">Modifier</x-btn></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <x-modal wire:model="modal" :title="$editId ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur'">
        @if ($motDePasseGenere)
            <div class="rounded-lg bg-emerald-50 p-4 text-sm text-emerald-900">
                Compte créé. Mot de passe provisoire à transmettre : <code class="rounded bg-white px-2 py-1 font-mono text-base">{{ $motDePasseGenere }}</code>
                <p class="mt-2 text-xs">Il ne sera plus affiché. L'utilisateur pourra le changer dans « Mon profil ».</p>
            </div>
        @else
            <form wire:submit="enregistrer" id="form-user" class="grid gap-4 sm:grid-cols-2">
                <x-field label="Nom" name="form.name" required><input wire:model="form.name" class="form-input"></x-field>
                <x-field label="E-mail" name="form.email" required><input type="email" wire:model="form.email" class="form-input"></x-field>
                <x-field label="Téléphone" name="form.telephone"><input wire:model="form.telephone" class="form-input"></x-field>
                <x-field label="Rôle" name="form.role"><select wire:model="form.role" class="form-input">@foreach (\App\Enums\Role::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
                <x-field label="Employé lié (facultatif)" name="form.employe_id"><select wire:model="form.employe_id" class="form-input"><option value="">—</option>@foreach ($employes as $e)<option value="{{ $e->id }}">{{ $e->nom }} {{ $e->prenom }} ({{ $e->matricule }})</option>@endforeach</select></x-field>
                <x-field label="{{ $editId ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe (vide = généré)' }}" name="form.password"><input type="password" wire:model="form.password" class="form-input" autocomplete="new-password"></x-field>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.actif" class="rounded border-slate-300 text-brand-600"> Compte actif</label>
            </form>
        @endif
        <x-slot:footer>
            <x-btn variant="secondary" x-on:click="show = false">Fermer</x-btn>
            @unless ($motDePasseGenere)<x-btn type="submit" form="form-user">Enregistrer</x-btn>@endunless
        </x-slot:footer>
    </x-modal>
</div>
