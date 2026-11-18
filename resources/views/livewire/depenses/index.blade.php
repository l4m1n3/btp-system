<div>
    <x-page-header title="Dépenses" subtitle="Dépenses internes (frais généraux) et dépenses de chantier">
        <x-slot:actions>
            @can('rapports.view')
                <x-btn variant="secondary" :href="route('export.depenses').'?'.$parametresUrl">Excel</x-btn>
                <x-btn variant="secondary" :href="route('pdf.depenses').'?'.$parametresUrl" target="_blank">PDF</x-btn>
            @endcan
            @can('depenses.create')<x-btn wire:click="creer">+ Nouvelle dépense</x-btn>@endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <x-stat label="Validées (filtre actuel)" :value="fcfa($totaux['validee']->total ?? 0)" :sub="($totaux['validee']->nb ?? 0).' dépense(s)'" accent="green" />
        <x-stat label="En attente de validation" :value="fcfa($totaux['en_attente']->total ?? 0)" :sub="($totaux['en_attente']->nb ?? 0).' dépense(s)'" accent="amber" />
        <x-stat label="Rejetées" :value="fcfa($totaux['rejetee']->total ?? 0)" :sub="($totaux['rejetee']->nb ?? 0).' dépense(s)'" accent="red" />
    </div>

    <x-card :padding="false">
        <div class="grid gap-2 border-b border-slate-100 p-3 sm:grid-cols-4 lg:grid-cols-8">
            <input type="search" wire:model.live.debounce.300ms="recherche" placeholder="Libellé, n° de pièce…" class="form-input sm:col-span-2">
            <select wire:model.live="portee" class="form-input"><option value="">Interne + chantiers</option>@foreach (\App\Enums\PorteeDepense::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
            <select wire:model.live="chantier" class="form-input"><option value="">Tous chantiers</option>@foreach ($chantiers as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select>
            <select wire:model.live="categorie" class="form-input"><option value="">Toutes catégories</option>@foreach ($categories as $c)<option value="{{ $c->id }}">{{ $c->nom }} ({{ $c->portee === \App\Enums\PorteeDepense::Interne ? 'int.' : 'ch.' }})</option>@endforeach</select>
            <select wire:model.live="statut" class="form-input"><option value="">Tous statuts</option>@foreach (\App\Enums\StatutDepense::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
            <input type="date" wire:model.live="du" class="form-input" title="Du">
            <input type="date" wire:model.live="au" class="form-input" title="Au">
        </div>
        <div class="flex items-center justify-between gap-2 border-b border-slate-100 px-3 py-2 text-xs">
            <button wire:click="reinitialiserFiltres" class="link">Réinitialiser les filtres</button>
            @can('depenses.valider')
                @if (($totaux['en_attente']->nb ?? 0) > 0)
                    <x-btn size="sm" variant="success" wire:click="validerSelection" wire:confirm="Valider les {{ $totaux['en_attente']->nb }} dépense(s) en attente correspondant aux filtres ?">Valider les {{ $totaux['en_attente']->nb }} en attente</x-btn>
                @endif
            @endcan
        </div>
        @include('livewire.depenses.table', ['depenses' => $depenses, 'avecActions' => true])
        <div class="p-3">{{ $depenses->links() }}</div>
    </x-card>

    <x-modal wire:model="modal" :title="$editId ? 'Modifier la dépense' : 'Nouvelle dépense'" max-width="3xl">
        <form wire:submit="enregistrer" id="form-depense" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Type de dépense" name="form.portee" class="sm:col-span-2">
                <div class="flex gap-2">
                    @foreach (\App\Enums\PorteeDepense::cases() as $p)
                        @if ($p === \App\Enums\PorteeDepense::Chantier || Gate::allows('depenses.interne'))
                            <label @class(['flex flex-1 cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm', 'border-brand-500 bg-brand-50 text-brand-700' => ($form['portee'] ?? '') === $p->value, 'border-slate-300' => ($form['portee'] ?? '') !== $p->value])>
                                <input type="radio" wire:model.live="form.portee" value="{{ $p->value }}" class="text-brand-600"> {{ $p->label() }}
                            </label>
                        @endif
                    @endforeach
                </div>
            </x-field>
            @if (($form['portee'] ?? '') === 'chantier')
                <x-field label="Chantier" name="form.chantier_id" required><select wire:model.live="form.chantier_id" class="form-input"><option value="">—</option>@foreach ($chantiers as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
                <x-field label="Tâche du planning" name="form.tache_id" hint="Pour suivre le coût réel par tâche"><select wire:model="form.tache_id" class="form-input"><option value="">—</option>@foreach ($tachesForm as $id => $lib)<option value="{{ $id }}">{{ $lib }}</option>@endforeach</select></x-field>
            @endif
            <x-field label="Catégorie" name="form.categorie_id" required><select wire:model="form.categorie_id" class="form-input"><option value="">—</option>@foreach ($categoriesForm as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
            <x-field label="Fournisseur / bénéficiaire" name="form.fournisseur_id"><select wire:model="form.fournisseur_id" class="form-input"><option value="">—</option>@foreach ($fournisseurs as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
            <x-field label="Libellé" name="form.libelle" required class="sm:col-span-2"><input wire:model="form.libelle" class="form-input" placeholder="ex. Achat de 50 sacs de ciment"></x-field>
            <x-field label="Montant (FCFA)" name="form.montant" required><input type="number" step="1" wire:model="form.montant" class="form-input"></x-field>
            <x-field label="Date" name="form.date_depense" required><input type="date" wire:model="form.date_depense" max="{{ now()->toDateString() }}" class="form-input"></x-field>
            <x-field label="Mode de paiement" name="form.mode_paiement"><select wire:model="form.mode_paiement" class="form-input">@foreach (\App\Enums\ModePaiement::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <x-field label="N° de pièce (facture, reçu…)" name="form.reference_piece"><input wire:model="form.reference_piece" class="form-input"></x-field>
            <x-field label="Justificatif (PDF ou photo, 5 Mo max.)" name="justificatif" class="sm:col-span-2">
                <input type="file" wire:model="justificatif" accept=".pdf,.jpg,.jpeg,.png" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium hover:file:bg-slate-200">
                <div wire:loading wire:target="justificatif" class="mt-1 text-xs text-slate-500">Envoi en cours…</div>
            </x-field>
            @cannot('depenses.valider')<p class="text-xs text-slate-500 sm:col-span-2">La dépense sera soumise à validation (comptable ou direction) avant d'entrer dans les totaux.</p>@endcannot
        </form>
        <x-slot:footer>
            <x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn>
            <x-btn type="submit" form="form-depense" wire:loading.attr="disabled">Enregistrer</x-btn>
        </x-slot:footer>
    </x-modal>

    <x-modal wire:model="modalRejet" title="Rejeter la dépense" max-width="md">
        <form wire:submit="rejeter" id="form-rejet">
            <x-field label="Motif du rejet" name="motif" required><textarea wire:model="motif" rows="3" class="form-input" placeholder="ex. Justificatif illisible, montant incohérent…"></textarea></x-field>
        </form>
        <x-slot:footer>
            <x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn>
            <x-btn type="submit" form="form-rejet" variant="danger">Rejeter</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
