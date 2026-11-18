@php
    $cout = $chantier->coutTotal();
    $conso = $chantier->tauxConsommationBudget();
    $peutSuivre = Gate::allows('chantier.suivre', $chantier);
@endphp
<div>
    <x-page-header :title="$chantier->nom">
        <x-slot:breadcrumb>
            <a href="{{ route('chantiers.index') }}" wire:navigate class="link">Chantiers</a> / {{ $chantier->code }}
            @if ($chantier->marche) · Marché <a href="{{ route('marches.show', $chantier->marche) }}" wire:navigate class="link">{{ $chantier->marche->reference }}</a> ({{ $chantier->marche->client->nom }}) @endif
        </x-slot:breadcrumb>
        <x-slot:actions>
            <x-badge :value="$chantier->statut" class="text-sm" />
            @can('rapports.view')<x-btn variant="secondary" :href="route('pdf.chantier', $chantier)" target="_blank">Situation PDF</x-btn>@endcan
            @can('chantiers.manage')<x-btn variant="secondary" wire:click="modifier({{ $chantier->id }})">Modifier</x-btn>@endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-5">
        <div class="col-span-2 rounded-xl border border-slate-200 bg-white p-4 shadow-xs lg:col-span-1">
            <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Avancement physique</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($chantier->avancement, 1, ',', ' ') }} %</p>
            <x-progress :value="$chantier->avancement" :label="false" class="mt-2" />
        </div>
        <x-stat label="Budget" :value="fcfa($chantier->budget)" />
        <x-stat label="Coût engagé" :value="fcfa($cout)" sub="Dépenses validées + matériaux" accent="brand" />
        <x-stat label="Budget consommé" :value="number_format($conso, 1, ',', ' ').' %'" :sub="'Reste : '.fcfa((float) $chantier->budget - $cout)" :accent="$conso >= 100 ? 'red' : ($conso >= 90 ? 'amber' : 'green')" />
        <x-stat label="Délais" :value="date_fr($chantier->date_fin_prevue)" :sub="$chantier->estEnRetard() ? 'Délai dépassé' : ($chantier->date_fin_prevue ? 'Fin prévue' : 'Non planifié')" :accent="$chantier->estEnRetard() ? 'red' : 'navy'" />
    </div>

    <x-tabs :active="$onglet" :tabs="['apercu' => 'Aperçu', 'planning' => 'Planning & avancement ('.$chantier->taches->count().')', 'depenses' => 'Dépenses', 'journal' => 'Journal de chantier', 'ressources' => 'Personnel & matériel', 'finances' => 'Situation financière']" />

    @if ($onglet === 'apercu')
        <div class="grid gap-6 lg:grid-cols-3">
            <x-card title="Informations">
                <dl class="space-y-2.5 text-sm">
                    @foreach (['Code' => $chantier->code, 'Chef de chantier' => $chantier->chef?->name ?? '—', 'Localisation' => $chantier->localisation ?: '—', 'Début' => date_fr($chantier->date_debut), 'Fin prévue' => date_fr($chantier->date_fin_prevue), 'Fin réelle' => date_fr($chantier->date_fin_reelle)] as $k => $v)
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ $k }}</dt><dd class="text-right font-medium">{{ $v }}</dd></div>
                    @endforeach
                </dl>
                @if ($chantier->description)<p class="mt-4 border-t border-slate-100 pt-3 text-sm text-slate-600">{{ $chantier->description }}</p>@endif
            </x-card>
            <x-card title="Tâches à surveiller" class="lg:col-span-2" :padding="false">
                @php $aSurveiller = $chantier->taches->filter(fn ($t) => $t->estEnRetard() || ($parTache[$t->id] ?? 0) > (float) $t->cout_prevu && (float) $t->cout_prevu > 0); @endphp
                <ul class="divide-y divide-slate-100">
                    @forelse ($aSurveiller as $t)
                        <li class="flex items-center justify-between gap-3 px-5 py-3 text-sm">
                            <span>{{ $t->libelle }}</span>
                            <span class="flex gap-2">
                                @if ($t->estEnRetard())<x-badge color="red">Retard ({{ date_fr($t->date_fin_prevue) }})</x-badge>@endif
                                @if (($parTache[$t->id] ?? 0) > (float) $t->cout_prevu && (float) $t->cout_prevu > 0)<x-badge color="amber">Dépassement : {{ fcfa($parTache[$t->id] - $t->cout_prevu) }}</x-badge>@endif
                            </span>
                        </li>
                    @empty
                        <li><x-empty message="Aucune tâche en retard ni en dépassement de coût." /></li>
                    @endforelse
                </ul>
            </x-card>
        </div>
    @endif

    @if ($onglet === 'planning')
        <x-card :padding="false">
            <x-slot:actions>
                @if ($peutSuivre)
                    <x-btn size="sm" variant="secondary" wire:click="enregistrerAvancements">Enregistrer l'avancement</x-btn>
                    <x-btn size="sm" wire:click="nouvelleTache">+ Tâche</x-btn>
                @endif
            </x-slot:actions>
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr><th>#</th><th>Tâche</th><th>Période prévue</th><th class="text-right">Coût prévu</th><th class="text-right">Dépensé</th><th class="w-56">Avancement</th><th>Statut</th><th></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $parTache = $chantier->depenses()->validees()->whereNotNull('tache_id')->selectRaw('tache_id, SUM(montant) as total')->groupBy('tache_id')->pluck('total', 'tache_id'); @endphp
                        @forelse ($chantier->taches as $t)
                            <tr wire:key="t{{ $t->id }}">
                                <td class="text-slate-400">{{ $t->ordre }}</td>
                                <td><span class="font-medium text-slate-900">{{ $t->libelle }}</span>@if($t->responsable)<div class="text-xs text-slate-500">{{ $t->responsable }}</div>@endif</td>
                                <td class="whitespace-nowrap @if($t->estEnRetard()) text-red-600 @endif">{{ date_fr($t->date_debut_prevue, 'd/m') }} → {{ date_fr($t->date_fin_prevue) }}</td>
                                <td class="text-right tabular-nums">{{ fcfa($t->cout_prevu, false) }}</td>
                                <td class="text-right tabular-nums @if(($parTache[$t->id] ?? 0) > (float) $t->cout_prevu && (float) $t->cout_prevu > 0) font-semibold text-red-600 @endif">{{ fcfa($parTache[$t->id] ?? 0, false) }}</td>
                                <td>
                                    @if ($peutSuivre)
                                        <div class="flex items-center gap-2">
                                            <input type="range" min="0" max="100" step="5" wire:model.live="avancements.{{ $t->id }}" class="flex-1 accent-brand-600">
                                            <span class="w-10 text-right text-xs font-medium tabular-nums">{{ $avancements[$t->id] ?? $t->avancement }} %</span>
                                        </div>
                                    @else
                                        <x-progress :value="$t->avancement" />
                                    @endif
                                </td>
                                <td><x-badge :value="$t->statut" /></td>
                                <td class="text-right whitespace-nowrap">
                                    @if ($peutSuivre)
                                        <x-btn size="sm" variant="ghost" wire:click="modifierTache({{ $t->id }})">Modifier</x-btn>
                                        <x-btn size="sm" variant="ghost" class="text-red-600" wire:click="supprimerTache({{ $t->id }})" wire:confirm="Supprimer cette tâche ?">×</x-btn>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8"><x-empty message="Aucune tâche planifiée. Ajoutez les tâches du planning pour suivre l'avancement." /></td></tr>
                        @endforelse
                    </tbody>
                    @if ($chantier->taches->isNotEmpty())
                        <tfoot class="bg-slate-50 text-sm font-semibold"><tr><td></td><td class="px-4 py-2">Total</td><td></td><td class="px-4 py-2 text-right tabular-nums">{{ fcfa($chantier->taches->sum('cout_prevu'), false) }}</td><td class="px-4 py-2 text-right tabular-nums">{{ fcfa($parTache->sum(), false) }}</td><td class="px-4 py-2">{{ number_format($chantier->avancement, 1, ',', ' ') }} % (pondéré)</td><td colspan="2"></td></tr></tfoot>
                    @endif
                </table>
            </div>
        </x-card>
    @endif

    @if ($onglet === 'depenses')
        <x-card :padding="false">
            <x-slot:actions>
                <a href="{{ route('depenses.index', ['chantier' => $chantier->id]) }}" wire:navigate class="link text-xs">Ouvrir dans Dépenses</a>
                @can('depenses.create')<x-btn size="sm" :href="route('depenses.index', ['chantier' => $chantier->id, 'nouveau' => 1])" wire:navigate>+ Dépense</x-btn>@endcan
            </x-slot:actions>
            @include('livewire.depenses.table', ['depenses' => $depenses, 'avecChantier' => false])
        </x-card>
    @endif

    @if ($onglet === 'journal')
        <div class="mb-3 flex justify-end">
            @if ($peutSuivre)<x-btn :href="route('chantiers.journal', $chantier)" wire:navigate>+ Rapport du jour</x-btn>@endif
        </div>
        <div class="space-y-3">
            @forelse ($rapports as $r)
                <x-card wire:key="r{{ $r->id }}">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-slate-900">{{ ucfirst($r->date_rapport->translatedFormat('l j F Y')) }}</p>
                            <p class="text-xs text-slate-500">Par {{ $r->auteur?->name ?? '—' }}{{ $r->meteo ? ' · Météo : '.$r->meteo : '' }}</p>
                        </div>
                        <a href="{{ route('chantiers.journal', [$chantier, $r]) }}" wire:navigate class="link text-xs">{{ $peutSuivre ? 'Ouvrir / modifier' : 'Ouvrir' }}</a>
                    </div>
                    <div class="mt-3 grid gap-3 text-sm sm:grid-cols-4">
                        @foreach (\App\Enums\TypeLigneJournal::cases() as $type)
                            @php $lignes = $r->lignes->where('type', $type); @endphp
                            <div>
                                <p class="mb-1 text-xs font-semibold text-slate-500 uppercase">{{ $type->label() }}</p>
                                @forelse ($lignes as $l)
                                    <p class="text-slate-700">{{ $l->designation }}@if($l->quantite) — {{ rtrim(rtrim(number_format($l->quantite, 2, ',', ' '), '0'), ',') }} {{ $l->unite }}@endif @if($l->avancement !== null) ({{ $l->avancement }} %)@endif</p>
                                @empty
                                    <p class="text-slate-400">—</p>
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                    @if ($r->observations || $r->incidents)
                        <div class="mt-3 space-y-1 border-t border-slate-100 pt-3 text-sm">
                            @if ($r->observations)<p><span class="font-medium">Observations :</span> {{ $r->observations }}</p>@endif
                            @if ($r->incidents)<p class="text-red-700"><span class="font-medium">Incidents :</span> {{ $r->incidents }}</p>@endif
                        </div>
                    @endif
                </x-card>
            @empty
                <x-card><x-empty message="Aucun rapport journalier." /></x-card>
            @endforelse
        </div>
    @endif

    @if ($onglet === 'ressources')
        <div class="grid gap-6 lg:grid-cols-2">
            <x-card :title="'Personnel affecté ('.$employes->count().')'" :padding="false">
                <table class="table-base">
                    <thead><tr><th>Employé</th><th>Fonction</th><th>Contrat</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($employes as $e)
                            <tr><td>@can('rh.view')<a href="{{ route('employes.show', $e) }}" wire:navigate class="link">{{ $e->nom_complet }}</a>@else{{ $e->nom_complet }}@endcan<div class="text-xs text-slate-500">{{ $e->matricule }}</div></td><td>{{ $e->fonction }}</td><td><x-badge :value="$e->type_contrat" /></td></tr>
                        @empty
                            <tr><td colspan="3"><x-empty message="Aucun employé affecté." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
            <x-card :title="'Matériel sur le chantier ('.$materiels->count().')'" :padding="false">
                <table class="table-base">
                    <thead><tr><th>Matériel</th><th>Type</th><th>État</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($materiels as $m)
                            <tr><td>{{ $m->designation }}<div class="text-xs text-slate-500">{{ $m->code }} {{ $m->immatriculation }}</div></td><td>{{ $m->type->label() }}</td><td><x-badge :value="$m->etat" /></td></tr>
                        @empty
                            <tr><td colspan="3"><x-empty message="Aucun matériel affecté." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    @endif

    @if ($onglet === 'finances')
        <div class="grid gap-6 lg:grid-cols-2">
            <x-card title="Répartition des coûts">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($parCategorie as $cat)
                            <tr><td class="py-2">{{ $cat->categorie }} <span class="text-xs text-slate-400">({{ $cat->nb }})</span></td><td class="py-2 text-right tabular-nums">{{ fcfa($cat->total) }}</td></tr>
                        @endforeach
                        <tr><td class="py-2">Matériaux sortis du stock</td><td class="py-2 text-right tabular-nums">{{ fcfa($totalMateriaux) }}</td></tr>
                        <tr class="font-semibold"><td class="py-2">Coût total engagé</td><td class="py-2 text-right tabular-nums">{{ fcfa($totalDepenses + $totalMateriaux) }}</td></tr>
                        <tr><td class="py-2 text-slate-500">Budget</td><td class="py-2 text-right tabular-nums text-slate-500">{{ fcfa($chantier->budget) }}</td></tr>
                        <tr class="{{ (float) $chantier->budget - $totalDepenses - $totalMateriaux < 0 ? 'text-red-600' : 'text-emerald-700' }} font-semibold"><td class="py-2">Écart (budget − coût)</td><td class="py-2 text-right tabular-nums">{{ fcfa((float) $chantier->budget - $totalDepenses - $totalMateriaux) }}</td></tr>
                    </tbody>
                </table>
                @if ($enAttente > 0)<p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800">{{ fcfa($enAttente) }} de dépenses en attente de validation ne sont pas encore comptées.</p>@endif
            </x-card>
            <x-card title="Matériaux consommés" :padding="false">
                <table class="table-base">
                    <thead><tr><th>Date</th><th>Article</th><th class="text-right">Qté</th><th class="text-right">Valeur</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($materiaux as $m)
                            <tr><td>{{ date_fr($m->date_mouvement) }}</td><td>{{ $m->article->designation }}</td><td class="text-right tabular-nums">{{ rtrim(rtrim(number_format($m->quantite, 2, ',', ' '), '0'), ',') }} {{ $m->article->unite }}</td><td class="text-right tabular-nums">{{ fcfa($m->valeur, false) }}</td></tr>
                        @empty
                            <tr><td colspan="4"><x-empty message="Aucune sortie de stock vers ce chantier." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    @endif

    @include('livewire.chantiers.form-modal', $listes)

    <x-modal wire:model="modalTache" :title="$tacheId ? 'Modifier la tâche' : 'Nouvelle tâche'">
        <form wire:submit="enregistrerTache" id="form-tache" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Libellé" name="tache.libelle" required class="sm:col-span-2"><input wire:model="tache.libelle" class="form-input" placeholder="ex. Fouilles et fondations"></x-field>
            <x-field label="Responsable" name="tache.responsable"><input wire:model="tache.responsable" class="form-input"></x-field>
            <x-field label="Ordre" name="tache.ordre"><input type="number" wire:model="tache.ordre" class="form-input"></x-field>
            <x-field label="Début prévu" name="tache.date_debut_prevue" required><input type="date" wire:model="tache.date_debut_prevue" class="form-input"></x-field>
            <x-field label="Fin prévue" name="tache.date_fin_prevue" required><input type="date" wire:model="tache.date_fin_prevue" class="form-input"></x-field>
            <x-field label="Coût prévu (FCFA)" name="tache.cout_prevu" hint="Sert de pondération pour l'avancement global"><input type="number" wire:model="tache.cout_prevu" class="form-input"></x-field>
            <x-field label="Avancement (%)" name="tache.avancement"><input type="number" min="0" max="100" wire:model="tache.avancement" class="form-input"></x-field>
            <x-field label="Statut" name="tache.statut"><select wire:model="tache.statut" class="form-input">@foreach (\App\Enums\StatutTache::options() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></x-field>
            <x-field label="Note" name="tache.note" class="sm:col-span-2"><textarea wire:model="tache.note" rows="2" class="form-input"></textarea></x-field>
        </form>
        <x-slot:footer>
            <x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn>
            <x-btn type="submit" form="form-tache">Enregistrer</x-btn>
        </x-slot:footer>
    </x-modal>
</div>
