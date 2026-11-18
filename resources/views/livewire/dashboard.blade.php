<div>
    <x-page-header title="Tableau de bord" :subtitle="'Situation au '.now()->translatedFormat('l j F Y')" />

    {{-- Alertes --}}
    @if (count($data['alertes']))
        <div class="mb-6 grid gap-2 sm:grid-cols-2">
            @foreach ($data['alertes'] as $a)
                @php $c = ['red' => 'border-red-200 bg-red-50 text-red-800', 'amber' => 'border-amber-200 bg-amber-50 text-amber-900', 'slate' => 'border-slate-200 bg-white text-slate-700'][$a['niveau']]; @endphp
                <a href="{{ $a['lien'] }}" wire:navigate class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm {{ $c }} hover:shadow-sm">
                    <span class="size-2 shrink-0 rounded-full {{ ['red' => 'bg-red-500', 'amber' => 'bg-amber-500', 'slate' => 'bg-slate-400'][$a['niveau']] }}"></span>
                    {{ $a['texte'] }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Marchés --}}
    @isset($data['marches'])
        @php $m = $data['marches']; @endphp
        <h2 class="mb-3 text-sm font-semibold tracking-wide text-slate-500 uppercase">Marchés</h2>
        <div class="mb-8 grid grid-cols-2 gap-3 lg:grid-cols-5">
            <x-stat label="En cours" :value="$m['par_statut']['en_cours']['nb']" :sub="fcfa($m['par_statut']['en_cours']['montant']).' HT'" accent="blue" :href="route('marches.index', ['statut' => 'en_cours'])" />
            <x-stat label="En attente" :value="$m['par_statut']['en_attente']['nb']" :sub="fcfa($m['par_statut']['en_attente']['montant']).' HT'" accent="amber" :href="route('marches.index', ['statut' => 'en_attente'])" />
            <x-stat label="Terminés" :value="$m['par_statut']['termine']['nb']" :sub="fcfa($m['par_statut']['termine']['montant']).' HT'" accent="green" :href="route('marches.index', ['statut' => 'termine'])" />
            <x-stat label="Reste à réaliser" :value="fcfa($m['carnet_commandes'])" sub="Carnet de commandes HT" accent="navy" />
            <x-stat label="Encaissé {{ now()->year }}" :value="fcfa($m['encaisse_annee'])" :sub="'À encaisser : '.fcfa($m['a_encaisser'])" accent="brand" />
        </div>
    @endisset

    {{-- Dépenses --}}
    @isset($data['depenses'])
        @php $d = $data['depenses']; @endphp
        <h2 class="mb-3 text-sm font-semibold tracking-wide text-slate-500 uppercase">Dépenses validées</h2>
        <div class="mb-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <x-stat label="Ce mois" :value="fcfa($d['mois_total'])" accent="brand" />
            <x-stat label="Dont chantiers" :value="fcfa($d['mois_chantier'])" accent="blue" />
            <x-stat label="Dont interne" :value="fcfa($d['mois_interne'])" sub="Frais généraux" accent="violet" />
            <x-stat label="Depuis le 1er janvier" :value="fcfa($d['annee_total'])" :sub="$d['en_attente'].' en attente de validation'" accent="navy" :href="route('depenses.index', ['statut' => 'en_attente'])" />
        </div>
        <div class="mb-8 grid gap-4 lg:grid-cols-3">
            <x-card title="Évolution sur 6 mois" class="lg:col-span-2">
                @php $max = max(1, $d['serie']->max(fn ($s) => $s['interne'] + $s['chantier'])); @endphp
                <div class="flex h-48 items-end gap-3">
                    @foreach ($d['serie'] as $s)
                        <div class="flex flex-1 flex-col items-center gap-1" title="Chantiers : {{ fcfa($s['chantier']) }} — Interne : {{ fcfa($s['interne']) }}">
                            <span class="text-[10px] text-slate-500 tabular-nums">{{ $s['interne'] + $s['chantier'] > 0 ? number_format(($s['interne'] + $s['chantier']) / 1e6, 1, ',', ' ').' M' : '' }}</span>
                            <div class="flex w-full max-w-12 flex-col justify-end overflow-hidden rounded-t" style="height: {{ round(($s['interne'] + $s['chantier']) / $max * 150) }}px">
                                <div class="bg-violet-400" style="height: {{ $s['interne'] + $s['chantier'] > 0 ? round($s['interne'] / ($s['interne'] + $s['chantier']) * 100) : 0 }}%"></div>
                                <div class="flex-1 bg-navy-700"></div>
                            </div>
                            <span class="text-xs text-slate-600">{{ $s['mois'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 flex gap-4 text-xs text-slate-600">
                    <span class="flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-navy-700"></span>Chantiers</span>
                    <span class="flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-violet-400"></span>Interne</span>
                </div>
            </x-card>
            <x-card title="Principales catégories (année)">
                @php $maxCat = max(1, (float) $d['par_categorie']->max('total')); @endphp
                <ul class="space-y-3">
                    @forelse ($d['par_categorie'] as $cat)
                        <li>
                            <div class="mb-1 flex justify-between gap-2 text-xs"><span class="truncate text-slate-700">{{ $cat->categorie }}</span><span class="font-medium whitespace-nowrap tabular-nums">{{ fcfa($cat->total) }}</span></div>
                            <div class="h-1.5 rounded-full bg-slate-100"><div class="h-full rounded-full bg-brand-500" style="width: {{ round($cat->total / $maxCat * 100) }}%"></div></div>
                        </li>
                    @empty
                        <x-empty message="Aucune dépense cette année." />
                    @endforelse
                </ul>
            </x-card>
        </div>
    @endisset

    {{-- Chantiers --}}
    @isset($data['chantiers'])
        <x-card :title="'Chantiers actifs ('.$data['chantiers']['actifs'].')'" :padding="false">
            <x-slot:actions><a href="{{ route('chantiers.index') }}" wire:navigate class="link text-xs">Tout voir</a></x-slot:actions>
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr><th>Chantier</th><th>Statut</th><th class="w-48">Avancement</th><th class="text-right">Budget</th><th class="text-right">Coût engagé</th><th class="w-40">Budget consommé</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($data['chantiers']['liste'] as $l)
                            <tr>
                                <td>
                                    <a href="{{ route('chantiers.show', $l['model']) }}" wire:navigate class="link">{{ $l['model']->nom }}</a>
                                    <div class="text-xs text-slate-500">{{ $l['model']->marche?->reference ?? 'Hors marché' }}@if($l['retard']) · <span class="font-medium text-red-600">délai dépassé</span>@endif</div>
                                </td>
                                <td><x-badge :value="$l['model']->statut" /></td>
                                <td><x-progress :value="$l['avancement']" /></td>
                                <td class="text-right tabular-nums">{{ fcfa($l['model']->budget, false) }}</td>
                                <td class="text-right tabular-nums">{{ fcfa($l['cout'], false) }}</td>
                                <td><x-progress :value="$l['conso']" :color="$l['conso'] >= 100 ? 'bg-red-500' : ($l['conso'] >= config('btp.alertes.seuil_budget') ? 'bg-amber-500' : 'bg-emerald-500')" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-empty message="Aucun chantier actif." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    @endisset
</div>
