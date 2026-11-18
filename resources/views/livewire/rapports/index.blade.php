<div>
    <x-page-header title="Rapports" subtitle="Situation des dépenses, des chantiers et de la trésorerie sur une période">
        <x-slot:actions>
            <x-btn variant="secondary" :href="route('export.depenses').'?'.$parametres">Dépenses (Excel)</x-btn>
            <x-btn variant="secondary" :href="route('pdf.depenses').'?'.$parametres" target="_blank">État des dépenses (PDF)</x-btn>
        </x-slot:actions>
    </x-page-header>

    <x-card class="mb-6">
        <div class="flex flex-wrap items-end gap-3">
            <x-field label="Du" name="du"><input type="date" wire:model.live="du" class="form-input"></x-field>
            <x-field label="Au" name="au"><input type="date" wire:model.live="au" class="form-input"></x-field>
            <div class="flex flex-wrap gap-1 pb-0.5">
                @foreach (['mois' => 'Ce mois', 'mois_prec' => 'Mois précédent', 'trimestre' => 'Ce trimestre', 'annee' => 'Cette année'] as $k => $v)
                    <x-btn size="sm" variant="ghost" wire:click="periode('{{ $k }}')">{{ $v }}</x-btn>
                @endforeach
            </div>
        </div>
    </x-card>

    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-5">
        <x-stat label="Facturé (décomptes HT)" :value="fcfa($facture)" accent="blue" />
        <x-stat label="Encaissé" :value="fcfa($encaisse)" accent="green" />
        <x-stat label="Dépenses chantiers" :value="fcfa($totalChantiers)" accent="navy" />
        <x-stat label="Dépenses internes" :value="fcfa($totalInterne)" sub="Frais généraux" accent="violet" />
        <x-stat label="Encaissé − dépensé" :value="fcfa($encaisse - $totalChantiers - $totalInterne)" sub="Indicateur de trésorerie" :accent="$encaisse - $totalChantiers - $totalInterne >= 0 ? 'green' : 'red'" />
    </div>

    <div class="mb-6 grid gap-6 lg:grid-cols-2">
        @foreach (\App\Enums\PorteeDepense::cases() as $p)
            <x-card :title="'Dépenses par catégorie — '.$p->label()" :padding="false">
                <table class="table-base">
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($parCategorie[$p->value] ?? [] as $r)
                            <tr><td>{{ $r->categorie }}</td><td class="text-right tabular-nums">{{ fcfa($r->total) }}</td></tr>
                        @empty
                            <tr><td colspan="2"><x-empty message="Aucune dépense validée sur la période." /></td></tr>
                        @endforelse
                    </tbody>
                    @if (! empty($parCategorie[$p->value]))
                        <tfoot class="bg-slate-50 font-semibold"><tr><td class="px-4 py-2">Total</td><td class="px-4 py-2 text-right tabular-nums">{{ fcfa(collect($parCategorie[$p->value])->sum('total')) }}</td></tr></tfoot>
                    @endif
                </table>
            </x-card>
        @endforeach
    </div>

    <x-card title="Situation des chantiers" :padding="false">
        <div class="overflow-x-auto">
            <table class="table-base">
                <thead><tr><th>Chantier</th><th class="text-right">Dépenses (période)</th><th class="text-right">Matériaux (période)</th><th class="text-right">Coût cumulé</th><th class="text-right">Budget</th><th class="w-36">Consommé</th><th class="w-36">Avancement</th><th></th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($chantiers as $l)
                        <tr>
                            <td><a href="{{ route('chantiers.show', $l['model']) }}" wire:navigate class="link">{{ $l['model']->nom }}</a><div class="text-xs text-slate-500">{{ $l['model']->marche?->reference }}</div></td>
                            <td class="text-right tabular-nums">{{ fcfa($l['depenses'], false) }}</td>
                            <td class="text-right tabular-nums">{{ fcfa($l['materiaux'], false) }}</td>
                            <td class="text-right font-medium tabular-nums">{{ fcfa($l['cumul'], false) }}</td>
                            <td class="text-right tabular-nums">{{ fcfa($l['model']->budget, false) }}</td>
                            <td><x-progress :value="$l['conso']" :color="$l['conso'] >= 100 ? 'bg-red-500' : ($l['conso'] >= 90 ? 'bg-amber-500' : 'bg-emerald-500')" /></td>
                            <td><x-progress :value="$l['avancement']" /></td>
                            <td><a href="{{ route('pdf.chantier', $l['model']) }}" target="_blank" class="link text-xs">PDF</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="border-t border-slate-100 px-4 py-2 text-xs text-slate-500">Les matériaux sortis du stock sont comptés dans le coût des chantiers ; leur achat figure dans les dépenses (interne « achats pour stock » ou chantier). Pour la trésorerie, seules les dépenses comptent.</p>
    </x-card>
</div>
