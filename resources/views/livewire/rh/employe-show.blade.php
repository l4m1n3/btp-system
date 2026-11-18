<div>
    <x-page-header :title="$e->nom_complet" :subtitle="$e->fonction.' · '.$e->matricule">
        <x-slot:breadcrumb><a href="{{ route('employes.index') }}" wire:navigate class="link">Employés</a></x-slot:breadcrumb>
        <x-slot:actions><x-badge :value="$e->statut" class="text-sm" /></x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card title="Dossier">
            <dl class="space-y-2.5 text-sm">
                @foreach ([
                    'Contrat' => $e->type_contrat->label(),
                    'Embauche' => date_fr($e->date_embauche),
                    'Fin de contrat' => date_fr($e->date_fin_contrat),
                    'Affectation' => $e->chantier?->nom ?? 'Siège',
                    'Rémunération' => $e->mode_remuneration === \App\Enums\ModeRemuneration::Mensuel ? fcfa($e->salaire_base).' / mois' : fcfa($e->taux_journalier).' / jour',
                    'Paiement' => $e->mode_paiement->label().($e->numero_compte ? ' — '.$e->numero_compte : ''),
                    'Téléphone' => $e->telephone ?: '—',
                    'N° CNSS' => $e->numero_cnss ?: '—',
                    'Naissance' => date_fr($e->date_naissance),
                    'Compte utilisateur' => $e->user?->email ?? '—',
                ] as $k => $v)
                    <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ $k }}</dt><dd class="text-right font-medium">{{ $v }}</dd></div>
                @endforeach
            </dl>
        </x-card>

        <div class="space-y-6 lg:col-span-2">
            <x-card :title="'Pointage — '.ucfirst($debut->translatedFormat('F Y'))">
                <x-slot:actions><input type="month" wire:model.live="mois" class="form-input py-1 text-xs"></x-slot:actions>
                <div class="mb-3 flex gap-4 text-sm">
                    <span>Présent : <b>{{ $pointages->where('present', true)->count() }}</b> j</span>
                    <span>Absent : <b>{{ $pointages->where('present', false)->count() }}</b> j</span>
                    <span>Heures sup : <b>{{ rtrim(rtrim(number_format($pointages->sum('heures_sup'), 2, ',', ''), '0'), ',') }}</b> h</span>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                    @for ($d = $debut->copy(); $d->lte($debut->copy()->endOfMonth()); $d->addDay())
                        @php $p = $pointages->first(fn ($x) => $x->date_pointage->isSameDay($d)); @endphp
                        <div @class(['rounded p-1.5', 'bg-emerald-100 text-emerald-800' => $p?->present, 'bg-red-100 text-red-800' => $p && ! $p->present, 'bg-slate-50 text-slate-400' => ! $p]) title="{{ $p?->chantier?->nom }}">
                            {{ $d->day }}@if($p && $p->heures_sup > 0)<sup>+{{ (float) $p->heures_sup }}</sup>@endif
                        </div>
                    @endfor
                </div>
            </x-card>

            <div class="grid gap-6 sm:grid-cols-2">
                <x-card title="Congés & absences" :padding="false">
                    <ul class="divide-y divide-slate-100 text-sm">
                        @forelse ($conges as $c)
                            <li class="flex items-center justify-between gap-2 px-4 py-2"><span>{{ $c->type->label() }} <span class="text-xs text-slate-500">{{ date_fr($c->date_debut) }} → {{ date_fr($c->date_fin) }} ({{ $c->nombre_jours }} j)</span></span><x-badge :value="$c->statut" /></li>
                        @empty
                            <li><x-empty message="Aucun congé." /></li>
                        @endforelse
                    </ul>
                </x-card>
                <x-card title="Avances" :padding="false">
                    <ul class="divide-y divide-slate-100 text-sm">
                        @forelse ($avances as $a)
                            <li class="flex items-center justify-between gap-2 px-4 py-2"><span>{{ date_fr($a->date_avance) }} <span class="text-xs text-slate-500">retenue {{ $a->periode_retenue }}</span></span><span class="tabular-nums">{{ fcfa($a->montant) }}</span></li>
                        @empty
                            <li><x-empty message="Aucune avance." /></li>
                        @endforelse
                    </ul>
                </x-card>
            </div>

            @can('paie.view')
                <x-card title="Bulletins de paie" :padding="false">
                    <table class="table-base">
                        <thead><tr><th>Période</th><th class="text-right">Brut</th><th class="text-right">Net à payer</th><th></th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($bulletins as $b)
                                <tr><td>{{ $b->periode->libelle }}</td><td class="text-right tabular-nums">{{ fcfa($b->salaire_brut) }}</td><td class="text-right font-medium tabular-nums">{{ fcfa($b->net_a_payer) }}</td><td class="text-right"><a href="{{ route('pdf.bulletin', $b) }}" target="_blank" class="link text-xs">PDF</a></td></tr>
                            @empty
                                <tr><td colspan="4"><x-empty message="Aucun bulletin." /></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-card>
            @endcan
        </div>
    </div>
</div>
