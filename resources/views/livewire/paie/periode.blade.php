@php $cloturee = $periode->estCloturee(); @endphp
<div>
    <x-page-header :title="'Paie — '.$periode->libelle">
        <x-slot:breadcrumb><a href="{{ route('paie.index') }}" wire:navigate class="link">Paie</a></x-slot:breadcrumb>
        <x-slot:actions>
            <x-badge :value="$periode->statut" class="text-sm" />
            @if ($bulletins->isNotEmpty())<x-btn variant="secondary" :href="route('pdf.journal-paie', $periode)" target="_blank">Journal de paie</x-btn>@endif
            @unless ($cloturee)
                @can('paie.manage')<x-btn variant="navy" wire:click="generer" wire:loading.attr="disabled">{{ $bulletins->isEmpty() ? 'Générer les bulletins' : 'Recalculer' }}</x-btn>@endcan
                @can('paie.cloturer')@if($bulletins->isNotEmpty())<x-btn variant="success" wire:click="cloturer" wire:confirm="Clôturer la paie ? Les bulletins ne seront plus modifiables et la masse salariale sera enregistrée en dépenses.">Clôturer</x-btn>@endif @endcan
            @else
                @if (auth()->user()->hasRole('admin'))<x-btn variant="secondary" wire:click="rouvrir" wire:confirm="Rouvrir cette période ? Les dépenses de salaires générées seront supprimées.">Rouvrir</x-btn>@endif
            @endunless
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <x-stat label="Bulletins" :value="$bulletins->count()" />
        <x-stat label="Masse salariale brute" :value="fcfa($bulletins->sum('salaire_brut'))" accent="brand" />
        <x-stat label="Avances + retenues" :value="fcfa($bulletins->sum('total_avances') + $bulletins->sum('total_retenues'))" accent="amber" />
        <x-stat label="Net à payer" :value="fcfa($bulletins->sum('net_a_payer'))" :sub="$bulletins->where('paye', true)->count().' / '.$bulletins->count().' payé(s)'" accent="green" />
    </div>

    @if ($cloturee)
        <p class="mb-4 rounded-lg bg-emerald-50 px-4 py-2 text-sm text-emerald-800">Clôturée le {{ $periode->cloturee_le?->format('d/m/Y à H:i') }}. Masse salariale imputée : {{ $parImputation->map(fn ($v, $k) => $k.' '.fcfa($v))->join(' · ') }}.</p>
    @else
        <p class="mb-4 rounded-lg bg-slate-100 px-4 py-2 text-xs text-slate-600">Calcul : mensuels = base − 1/30 par jour d'absence non payée ; journaliers = jours pointés présents × taux. Heures sup majorées de {{ (config('btp.paie.majoration_heures_sup') - 1) * 100 }} %. Les primes et retenues ajoutées à la main sont conservées au recalcul.</p>
    @endif

    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Employé</th><th>Imputation</th><th class="text-right">Jours</th><th class="text-right">Salaire dû</th><th class="text-right">H. sup</th><th class="text-right">Primes</th><th class="text-right">Brut</th><th class="text-right">Avances</th><th class="text-right">Retenues</th><th class="text-right">Net</th><th></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bulletins as $b)
                        <tr wire:key="b{{ $b->id }}" @class(['bg-red-50' => $b->net_a_payer < 0])>
                            <td><span class="font-medium">{{ $b->employe->nom_complet }}</span><div class="text-xs text-slate-500">{{ $b->mode_remuneration->label() }}</div></td>
                            <td class="text-xs">{{ $b->chantier?->nom ?? 'Siège' }}</td>
                            <td class="text-right tabular-nums">{{ (float) $b->jours_travailles }}@if($b->jours_absence > 0)<div class="text-xs text-red-600">−{{ (float) $b->jours_absence }} abs.</div>@endif</td>
                            <td class="text-right tabular-nums">{{ fcfa($b->salaire_du, false) }}</td>
                            <td class="text-right tabular-nums">{{ fcfa($b->montant_heures_sup, false) }}@if($b->heures_sup > 0)<div class="text-xs text-slate-500">{{ (float) $b->heures_sup }} h</div>@endif</td>
                            <td class="text-right tabular-nums">{{ fcfa($b->total_primes, false) }}</td>
                            <td class="text-right font-medium tabular-nums">{{ fcfa($b->salaire_brut, false) }}</td>
                            <td class="text-right tabular-nums">{{ fcfa($b->total_avances, false) }}</td>
                            <td class="text-right tabular-nums">{{ fcfa($b->total_retenues, false) }}</td>
                            <td class="text-right font-bold tabular-nums">{{ fcfa($b->net_a_payer, false) }}</td>
                            <td class="text-right whitespace-nowrap">
                                <x-btn size="sm" variant="ghost" wire:click="ouvrirBulletin({{ $b->id }})">{{ $cloturee ? 'Détail' : 'Ajuster' }}</x-btn>
                                <a href="{{ route('pdf.bulletin', $b) }}" target="_blank" class="link text-xs">PDF</a>
                                @can('paie.manage')<button wire:click="basculerPaye({{ $b->id }})" class="ml-1 text-xs {{ $b->paye ? 'text-emerald-600' : 'text-slate-400 hover:text-slate-600' }}" title="Marquer comme payé">{{ $b->paye ? '✓ payé' : 'payé ?' }}</button>@endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11"><x-empty message="Aucun bulletin. Cliquez sur « Générer les bulletins »." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-modal wire:model="modal" :title="$bulletin ? 'Bulletin — '.$bulletin->employe->nom_complet : ''" max-width="2xl">
        @if ($bulletin)
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-100">
                    <tr><td class="py-1.5">Salaire dû ({{ (float) $bulletin->jours_travailles }} j)</td><td class="py-1.5 text-right tabular-nums">{{ fcfa($bulletin->salaire_du) }}</td><td></td></tr>
                    <tr><td class="py-1.5">Heures supplémentaires ({{ (float) $bulletin->heures_sup }} h)</td><td class="py-1.5 text-right tabular-nums">{{ fcfa($bulletin->montant_heures_sup) }}</td><td></td></tr>
                    @foreach ($bulletin->lignes as $l)
                        <tr>
                            <td class="py-1.5">{{ $l->type->label() }} : {{ $l->libelle }}</td>
                            <td class="py-1.5 text-right tabular-nums {{ $l->type === \App\Enums\TypeLignePaie::Retenue ? 'text-red-600' : '' }}">{{ $l->type === \App\Enums\TypeLignePaie::Retenue ? '−' : '' }}{{ fcfa($l->montant) }}</td>
                            <td class="w-8 text-right">@if(! $cloturee && Gate::allows('paie.manage'))<button wire:click="supprimerLigne({{ $l->id }})" class="text-slate-400 hover:text-red-600">×</button>@endif</td>
                        </tr>
                    @endforeach
                    @foreach ($bulletin->avances as $a)
                        <tr><td class="py-1.5">Avance du {{ date_fr($a->date_avance) }}</td><td class="py-1.5 text-right text-red-600 tabular-nums">−{{ fcfa($a->montant) }}</td><td></td></tr>
                    @endforeach
                    <tr class="font-semibold"><td class="py-2">Salaire brut</td><td class="py-2 text-right tabular-nums">{{ fcfa($bulletin->salaire_brut) }}</td><td></td></tr>
                    <tr class="text-base font-bold"><td class="py-2">Net à payer</td><td class="py-2 text-right tabular-nums">{{ fcfa($bulletin->net_a_payer) }}</td><td></td></tr>
                </tbody>
            </table>
            @if (! $cloturee && Gate::allows('paie.manage'))
                <form wire:submit="ajouterLigne" class="mt-4 grid gap-2 border-t border-slate-100 pt-4 sm:grid-cols-12">
                    <select wire:model="ligne.type" class="form-input sm:col-span-3">@foreach (\App\Enums\TypeLignePaie::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
                    <input wire:model="ligne.libelle" placeholder="ex. Prime de rendement, indemnité de transport…" class="form-input sm:col-span-5">
                    <input type="number" wire:model="ligne.montant" placeholder="Montant" class="form-input sm:col-span-2">
                    <x-btn type="submit" class="sm:col-span-2">Ajouter</x-btn>
                    @error('ligne.libelle')<p class="text-xs text-red-600 sm:col-span-12">{{ $message }}</p>@enderror
                    @error('ligne.montant')<p class="text-xs text-red-600 sm:col-span-12">{{ $message }}</p>@enderror
                </form>
                <form wire:submit="changerImputation" class="mt-3 flex items-end gap-2">
                    <x-field label="Imputation du coût" class="flex-1"><select wire:model="imputation.chantier_id" class="form-input"><option value="">Siège (dépense interne)</option>@foreach ($chantiers as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
                    <x-btn type="submit" variant="secondary">Changer</x-btn>
                </form>
            @endif
        @endif
    </x-modal>
</div>
