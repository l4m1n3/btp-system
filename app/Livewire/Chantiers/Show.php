<?php

namespace App\Livewire\Chantiers;

use App\Enums\StatutDepense;
use App\Enums\StatutTache;
use App\Livewire\Concerns\Notifie;
use App\Models\Chantier;
use App\Models\Tache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;

class Show extends Component
{
    use FormulaireChantier, Notifie;

    public Chantier $chantier;

    #[Url]
    public string $onglet = 'apercu';

    // Tâches du planning
    public bool $modalTache = false;

    public ?int $tacheId = null;

    public array $tache = [];

    /** @var array<int,int> avancements saisis en ligne : tache_id => % */
    public array $avancements = [];

    public function mount(Chantier $chantier): void
    {
        $this->authorize('chantier.voir', $chantier);
        $this->chantier = $chantier;
        $this->chargerAvancements();
    }

    private function chargerAvancements(): void
    {
        $this->avancements = $this->chantier->taches()->pluck('avancement', 'id')->all();
    }

    public function nouvelleTache(): void
    {
        $this->authorize('chantier.suivre', $this->chantier);
        $this->resetValidation();
        $this->tacheId = null;
        $this->tache = [
            'libelle' => '', 'responsable' => '', 'date_debut_prevue' => $this->chantier->date_debut?->toDateString() ?? now()->toDateString(),
            'date_fin_prevue' => '', 'cout_prevu' => 0, 'avancement' => 0, 'statut' => StatutTache::Planifie->value,
            'ordre' => ($this->chantier->taches()->max('ordre') ?? 0) + 1, 'note' => '',
        ];
        $this->modalTache = true;
    }

    public function modifierTache(Tache $tache): void
    {
        $this->authorize('chantier.suivre', $this->chantier);
        abort_unless($tache->chantier_id === $this->chantier->id, 404);
        $this->resetValidation();
        $this->tacheId = $tache->id;
        $this->tache = collect($tache->only(['libelle', 'responsable', 'date_debut_prevue', 'date_fin_prevue', 'cout_prevu', 'avancement', 'statut', 'ordre', 'note']))
            ->map(fn ($v) => $v instanceof \BackedEnum ? $v->value : ($v instanceof \DateTimeInterface ? $v->format('Y-m-d') : ($v ?? '')))->all();
        $this->modalTache = true;
    }

    public function enregistrerTache(): void
    {
        $this->authorize('chantier.suivre', $this->chantier);
        $data = $this->validate([
            'tache.libelle' => 'required|string|max:255',
            'tache.responsable' => 'nullable|string|max:255',
            'tache.date_debut_prevue' => 'required|date',
            'tache.date_fin_prevue' => 'required|date|after_or_equal:tache.date_debut_prevue',
            'tache.cout_prevu' => 'required|numeric|min:0',
            'tache.avancement' => 'required|integer|between:0,100',
            'tache.statut' => ['required', Rule::enum(StatutTache::class)],
            'tache.ordre' => 'nullable|integer|min:0',
            'tache.note' => 'nullable|string',
        ], [], ['tache.date_fin_prevue' => 'fin prévue', 'tache.cout_prevu' => 'coût prévu'])['tache'];

        $this->chantier->taches()->updateOrCreate(['id' => $this->tacheId], array_map(fn ($v) => $v === '' ? null : $v, $data));
        $this->modalTache = false;
        $this->chargerAvancements();
        $this->succes('Tâche enregistrée.');
    }

    public function supprimerTache(Tache $tache): void
    {
        $this->authorize('chantier.suivre', $this->chantier);
        abort_unless($tache->chantier_id === $this->chantier->id, 404);
        $tache->delete();
        $this->chargerAvancements();
        $this->succes('Tâche supprimée.');
    }

    /** Enregistre en une fois les avancements modifiés dans le tableau du planning. */
    public function enregistrerAvancements(): void
    {
        $this->authorize('chantier.suivre', $this->chantier);
        $this->validate(['avancements.*' => 'required|integer|between:0,100'], [], ['avancements.*' => 'avancement']);
        DB::transaction(function () {
            foreach ($this->chantier->taches()->get() as $t) {
                if (isset($this->avancements[$t->id]) && (int) $this->avancements[$t->id] !== $t->avancement) {
                    $t->update(['avancement' => (int) $this->avancements[$t->id]]);
                }
            }
        });
        $this->chargerAvancements();
        $this->succes('Avancement mis à jour.');
    }

    public function render()
    {
        $c = $this->chantier->load(['marche.client', 'chef', 'taches']);
        $data = ['listes' => $this->listesFormulaire()];

        if ($this->onglet === 'apercu' || $this->onglet === 'finances') {
            $parCategorie = $c->depenses()->validees()
                ->join('categories_depenses', 'categories_depenses.id', '=', 'depenses.categorie_id')
                ->selectRaw('categories_depenses.nom as categorie, SUM(depenses.montant) as total, COUNT(*) as nb')
                ->groupBy('categories_depenses.nom')->orderByDesc('total')->get();
            $materiaux = $c->mouvementsStock()->with('article')->where('type', 'sortie')->latest('date_mouvement')->get();
            $data += [
                'parCategorie' => $parCategorie,
                'materiaux' => $materiaux,
                'totalDepenses' => (float) $parCategorie->sum('total'),
                'totalMateriaux' => $materiaux->sum(fn ($m) => $m->valeur),
                'enAttente' => (float) $c->depenses()->where('statut', StatutDepense::EnAttente)->sum('montant'),
                'parTache' => $c->depenses()->validees()->whereNotNull('tache_id')->selectRaw('tache_id, SUM(montant) as total')->groupBy('tache_id')->pluck('total', 'tache_id'),
            ];
        }
        if ($this->onglet === 'depenses') {
            $data['depenses'] = $c->depenses()->with(['categorie', 'tache', 'fournisseur'])->latest('date_depense')->latest('id')->limit(200)->get();
        }
        if ($this->onglet === 'journal') {
            $data['rapports'] = $c->rapports()->with(['auteur', 'lignes'])->limit(60)->get();
        }
        if ($this->onglet === 'ressources') {
            $data['employes'] = $c->employes()->actifs()->orderBy('nom')->get();
            $data['materiels'] = $c->materiels()->orderBy('designation')->get();
        }

        return view('livewire.chantiers.show', $data)->title($c->nom);
    }
}
