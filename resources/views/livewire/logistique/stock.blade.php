<div>
    <x-page-header title="Stock matériaux" subtitle="Entrées, sorties vers les chantiers et inventaire (valorisation au coût moyen pondéré)">
        <x-slot:actions>
            @if (Gate::any(['logistique.manage', 'stock.sortie']))<x-btn variant="secondary" wire:click="nouveauMouvement('sortie')">Sortie vers chantier</x-btn>@endif
            @can('logistique.manage')
                <x-btn variant="secondary" wire:click="nouveauMouvement('entree')">Entrée en stock</x-btn>
                <x-btn wire:click="editerArticle">+ Article</x-btn>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <x-stat label="Valeur du stock" :value="fcfa($valeurTotale)" accent="navy" />
        <x-stat label="Articles suivis" :value="$articles->total()" accent="blue" />
        <button wire:click="$toggle('alerte')" class="text-left"><x-stat label="Sous le seuil minimum" :value="$nbAlertes" :sub="$alerte ? 'Filtre actif — cliquer pour tout afficher' : 'Cliquer pour filtrer'" :accent="$nbAlertes ? 'red' : 'green'" /></button>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <x-card :padding="false" class="xl:col-span-2">
            <div class="border-b border-slate-100 p-3"><input type="search" wire:model.live.debounce.300ms="recherche" placeholder="Rechercher un article…" class="form-input max-w-xs"></div>
            <div class="overflow-x-auto">
                <table class="table-base">
                    <thead><tr><th>Article</th><th class="text-right">Stock</th><th class="text-right">Minimum</th><th class="text-right">Coût moyen</th><th class="text-right">Valeur</th><th></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($articles as $a)
                            @php $bas = (float) $a->stock_minimum > 0 && (float) $a->stock_actuel <= (float) $a->stock_minimum; @endphp
                            <tr wire:key="a{{ $a->id }}">
                                <td><span class="font-medium">{{ $a->designation }}</span><div class="font-mono text-xs text-slate-500">{{ $a->code }}</div></td>
                                <td class="text-right font-semibold tabular-nums {{ $bas ? 'text-red-600' : '' }}">{{ rtrim(rtrim(number_format($a->stock_actuel, 2, ',', ' '), '0'), ',') }} {{ $a->unite }}</td>
                                <td class="text-right text-slate-500 tabular-nums">{{ rtrim(rtrim(number_format($a->stock_minimum, 2, ',', ' '), '0'), ',') }}</td>
                                <td class="text-right tabular-nums">{{ fcfa($a->cout_moyen, false) }}</td>
                                <td class="text-right tabular-nums">{{ fcfa($a->valeurStock(), false) }}</td>
                                <td class="text-right whitespace-nowrap">
                                    @if (Gate::any(['logistique.manage', 'stock.sortie']))<x-btn size="sm" variant="ghost" wire:click="nouveauMouvement('sortie', {{ $a->id }})">Sortie</x-btn>@endif
                                    @can('logistique.manage')
                                        <x-btn size="sm" variant="ghost" wire:click="nouveauMouvement('entree', {{ $a->id }})">Entrée</x-btn>
                                        <x-btn size="sm" variant="ghost" wire:click="nouveauMouvement('ajustement', {{ $a->id }})">Inventaire</x-btn>
                                        <x-btn size="sm" variant="ghost" wire:click="editerArticle({{ $a->id }})">Modifier</x-btn>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><x-empty message="Aucun article." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $articles->links() }}</div>
        </x-card>

        <x-card title="Derniers mouvements" :padding="false">
            <ul class="divide-y divide-slate-100 text-sm">
                @forelse ($derniers as $m)
                    <li class="px-4 py-2.5">
                        <div class="flex items-center justify-between gap-2"><span class="font-medium">{{ $m->article->designation }}</span><x-badge :value="$m->type" /></div>
                        <div class="text-xs text-slate-500">{{ date_fr($m->date_mouvement) }} · {{ $m->quantite > 0 && $m->type !== \App\Enums\TypeMouvement::Ajustement ? '' : '' }}{{ rtrim(rtrim(number_format($m->quantite, 2, ',', ' '), '0'), ',') }} {{ $m->article->unite }}{{ $m->chantier ? ' → '.$m->chantier->nom : '' }} · {{ fcfa($m->valeur) }}</div>
                    </li>
                @empty
                    <li><x-empty message="Aucun mouvement." /></li>
                @endforelse
            </ul>
        </x-card>
    </div>

    <x-modal wire:model="modalArticle" :title="$articleId ? 'Modifier l\'article' : 'Nouvel article'" max-width="md">
        <form wire:submit="enregistrerArticle" id="form-art" class="space-y-4">
            <x-field label="Code" name="article.code" required><input wire:model="article.code" class="form-input"></x-field>
            <x-field label="Désignation" name="article.designation" required><input wire:model="article.designation" class="form-input" placeholder="ex. Ciment CPJ 45"></x-field>
            <x-field label="Unité" name="article.unite" required><input wire:model="article.unite" class="form-input" placeholder="sac, tonne, m³, barre…"></x-field>
            <x-field label="Stock minimum (alerte)" name="article.stock_minimum"><input type="number" step="0.01" wire:model="article.stock_minimum" class="form-input"></x-field>
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-art">Enregistrer</x-btn></x-slot:footer>
    </x-modal>

    @php $titres = ['entree' => 'Entrée en stock (achat / réception)', 'sortie' => 'Sortie vers un chantier', 'ajustement' => 'Ajustement d\'inventaire']; @endphp
    <x-modal wire:model="modalMouvement" :title="$titres[$mouvement['type'] ?? 'entree'] ?? 'Mouvement'">
        <form wire:submit="enregistrerMouvement" id="form-mvt" class="grid gap-4 sm:grid-cols-2">
            <x-field label="Article" name="mouvement.article_id" required class="sm:col-span-2">
                <select wire:model="mouvement.article_id" class="form-input"><option value="">—</option>@foreach ($listeArticles as $a)<option value="{{ $a->id }}">{{ $a->designation }} (stock : {{ rtrim(rtrim(number_format($a->stock_actuel, 2, ',', ' '), '0'), ',') }} {{ $a->unite }})</option>@endforeach</select>
            </x-field>
            <x-field :label="($mouvement['type'] ?? '') === 'ajustement' ? 'Écart constaté (+ surplus / − manquant)' : 'Quantité'" name="mouvement.quantite" required><input type="number" step="0.01" wire:model="mouvement.quantite" class="form-input"></x-field>
            @if (($mouvement['type'] ?? '') === 'entree')
                <x-field label="Prix unitaire d'achat" name="mouvement.prix_unitaire" required><input type="number" step="1" wire:model="mouvement.prix_unitaire" class="form-input"></x-field>
                <x-field label="Fournisseur" name="mouvement.fournisseur_id"><select wire:model="mouvement.fournisseur_id" class="form-input"><option value="">—</option>@foreach ($fournisseurs as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
            @elseif (($mouvement['type'] ?? '') === 'sortie')
                <x-field label="Chantier destinataire" name="mouvement.chantier_id" required><select wire:model="mouvement.chantier_id" class="form-input"><option value="">—</option>@foreach ($chantiers as $id => $nom)<option value="{{ $id }}">{{ $nom }}</option>@endforeach</select></x-field>
            @endif
            <x-field label="Date" name="mouvement.date_mouvement" required><input type="date" wire:model="mouvement.date_mouvement" class="form-input"></x-field>
            <x-field label="Référence (BL, bon de sortie…)" name="mouvement.reference"><input wire:model="mouvement.reference" class="form-input"></x-field>
            <x-field label="Observation" name="mouvement.observation" class="sm:col-span-2"><input wire:model="mouvement.observation" class="form-input"></x-field>
            @if (($mouvement['type'] ?? '') === 'entree')<p class="text-xs text-slate-500 sm:col-span-2">L'achat lui-même se saisit dans Dépenses ; l'entrée en stock sert au suivi des quantités et au coût des sorties vers les chantiers.</p>@endif
            @if (($mouvement['type'] ?? '') === 'sortie')<p class="text-xs text-slate-500 sm:col-span-2">La sortie est valorisée au coût moyen et ajoutée au coût du chantier.</p>@endif
        </form>
        <x-slot:footer><x-btn variant="secondary" x-on:click="show = false">Annuler</x-btn><x-btn type="submit" form="form-mvt">Enregistrer</x-btn></x-slot:footer>
    </x-modal>
</div>
