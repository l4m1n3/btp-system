<?php

namespace App\Livewire\Logistique;

use App\Enums\TypeMouvement;
use App\Livewire\Concerns\Notifie;
use App\Models\Article;
use App\Models\Chantier;
use App\Models\Fournisseur;
use App\Models\MouvementStock;
use App\Services\StockService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Stock matériaux')]
class Stock extends Component
{
    use Notifie, WithPagination;

    #[Url]
    public string $recherche = '';

    #[Url]
    public bool $alerte = false;

    public bool $modalArticle = false;

    public ?int $articleId = null;

    public array $article = [];

    public bool $modalMouvement = false;

    public array $mouvement = [];

    public function mount(): void
    {
        $this->authorize('logistique.view');
    }

    public function updating($name): void
    {
        if (in_array($name, ['recherche', 'alerte'])) {
            $this->resetPage();
        }
    }

    public function editerArticle(?int $id = null): void
    {
        $this->authorize('logistique.manage');
        $this->resetValidation();
        $a = $id ? Article::findOrFail($id) : null;
        $this->articleId = $id;
        $this->article = [
            'code' => $a?->code ?? 'ART-'.str_pad((string) (Article::max('id') + 1), 3, '0', STR_PAD_LEFT),
            'designation' => $a?->designation ?? '', 'unite' => $a?->unite ?? '', 'stock_minimum' => $a?->stock_minimum ?? 0,
        ];
        $this->modalArticle = true;
    }

    public function enregistrerArticle(): void
    {
        $this->authorize('logistique.manage');
        $data = $this->validate([
            'article.code' => ['required', 'string', 'max:50', Rule::unique('articles', 'code')->ignore($this->articleId)],
            'article.designation' => 'required|string|max:255',
            'article.unite' => 'required|string|max:30',
            'article.stock_minimum' => 'required|numeric|min:0',
        ], [], ['article.designation' => 'désignation', 'article.unite' => 'unité'])['article'];
        Article::updateOrCreate(['id' => $this->articleId], $data);
        $this->modalArticle = false;
        $this->succes('Article enregistré.');
    }

    public function nouveauMouvement(string $type = 'entree', ?int $articleId = null): void
    {
        abort_unless(Gate::any(['logistique.manage', 'stock.sortie']), 403);
        if ($type !== 'sortie') {
            $this->authorize('logistique.manage');
        }
        $this->resetValidation();
        $this->mouvement = [
            'type' => $type, 'article_id' => $articleId ?? '', 'quantite' => '', 'prix_unitaire' => '',
            'chantier_id' => '', 'fournisseur_id' => '', 'date_mouvement' => now()->toDateString(), 'reference' => '', 'observation' => '',
        ];
        $this->modalMouvement = true;
    }

    public function enregistrerMouvement(StockService $stock): void
    {
        $type = TypeMouvement::tryFrom($this->mouvement['type'] ?? '');
        abort_unless($type, 422);
        $type === TypeMouvement::Sortie ? abort_unless(Gate::any(['logistique.manage', 'stock.sortie']), 403) : $this->authorize('logistique.manage');

        $chantiersAutorises = Chantier::visiblePour(auth()->user())->actifs()->pluck('id')->all();
        $this->validate([
            'mouvement.article_id' => 'required|exists:articles,id',
            'mouvement.quantite' => $type === TypeMouvement::Ajustement ? 'required|numeric|not_in:0' : 'required|numeric|gt:0',
            'mouvement.prix_unitaire' => $type === TypeMouvement::Entree ? 'required|numeric|min:0' : 'nullable',
            'mouvement.chantier_id' => $type === TypeMouvement::Sortie ? ['required', Rule::in($chantiersAutorises)] : 'nullable',
            'mouvement.fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'mouvement.date_mouvement' => 'required|date|before_or_equal:today',
            'mouvement.reference' => 'nullable|string|max:100',
            'mouvement.observation' => 'nullable|string|max:255',
        ], ['mouvement.chantier_id.in' => 'Chantier non autorisé ou inactif.'], [
            'mouvement.article_id' => 'article', 'mouvement.quantite' => 'quantité', 'mouvement.prix_unitaire' => 'prix unitaire', 'mouvement.chantier_id' => 'chantier',
        ]);

        $m = $this->mouvement;
        $article = Article::findOrFail($m['article_id']);
        $attributs = [
            'date_mouvement' => $m['date_mouvement'], 'reference' => $m['reference'] ?: null,
            'observation' => $m['observation'] ?: null, 'saisi_par' => auth()->id(),
            'fournisseur_id' => $m['fournisseur_id'] ?: null,
        ];
        try {
            match ($type) {
                TypeMouvement::Entree => $stock->entree($article, (float) $m['quantite'], (float) $m['prix_unitaire'], $attributs),
                TypeMouvement::Sortie => $stock->sortie($article, (float) $m['quantite'], (int) $m['chantier_id'], $attributs),
                TypeMouvement::Ajustement => $stock->ajustement($article, (float) $m['quantite'], $attributs),
            };
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('mouvement.quantite', $e->validator->errors()->first());

            return;
        }
        $this->modalMouvement = false;
        $this->succes('Mouvement de stock enregistré.');
    }

    public function render()
    {
        $articles = Article::query()
            ->when($this->recherche, fn ($q) => $q->where(fn ($q) => $q->whereLike('designation', "%{$this->recherche}%")->orWhereLike('code', "%{$this->recherche}%")))
            ->when($this->alerte, fn ($q) => $q->sousMinimum())
            ->orderBy('designation')->paginate(20);

        return view('livewire.logistique.stock', [
            'articles' => $articles,
            'valeurTotale' => (float) Article::selectRaw('COALESCE(SUM(stock_actuel * cout_moyen), 0) as v')->value('v'),
            'nbAlertes' => Article::sousMinimum()->count(),
            'derniers' => MouvementStock::with(['article', 'chantier'])->latest('date_mouvement')->latest('id')->limit(15)->get(),
            'listeArticles' => Article::orderBy('designation')->get(['id', 'designation', 'unite', 'stock_actuel']),
            'chantiers' => Chantier::visiblePour(auth()->user())->actifs()->orderBy('nom')->pluck('nom', 'id'),
            'fournisseurs' => Fournisseur::orderBy('nom')->pluck('nom', 'id'),
        ]);
    }
}
