<?php

namespace App\Livewire\Depenses;

use App\Enums\ModePaiement;
use App\Enums\StatutDepense;
use App\Livewire\Concerns\Notifie;
use App\Models\CategorieDepense;
use App\Models\Chantier;
use App\Models\Depense;
use App\Models\Fournisseur;
use App\Services\DepenseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Dépenses')]
class Index extends Component
{
    use Notifie, WithFileUploads, WithPagination;

    #[Url]
    public string $recherche = '';

    #[Url]
    public string $portee = '';

    #[Url]
    public string $statut = '';

    #[Url]
    public string $chantier = '';

    #[Url]
    public string $categorie = '';

    #[Url]
    public string $du = '';

    #[Url]
    public string $au = '';

    public bool $modal = false;

    public ?int $editId = null;

    public array $form = [];

    public $justificatif = null;

    public bool $modalRejet = false;

    public ?int $rejetId = null;

    public string $motif = '';

    public function mount(): void
    {
        $this->authorize('depenses.view');
        if (request()->boolean('nouveau')) {
            $this->creer();
        }
    }

    public function updating($name): void
    {
        if (in_array($name, ['recherche', 'portee', 'statut', 'chantier', 'categorie', 'du', 'au'])) {
            $this->resetPage();
        }
    }

    public function reinitialiserFiltres(): void
    {
        $this->reset(['recherche', 'portee', 'statut', 'chantier', 'categorie', 'du', 'au']);
        $this->resetPage();
    }

    public function creer(): void
    {
        $this->authorize('depenses.create');
        $this->resetValidation();
        $this->editId = null;
        $this->justificatif = null;
        $interne = Gate::allows('depenses.interne') && ! $this->chantier;
        $this->form = [
            'portee' => $interne && $this->portee === 'interne' ? 'interne' : 'chantier',
            'chantier_id' => $this->chantier, 'tache_id' => '', 'categorie_id' => '', 'fournisseur_id' => '',
            'date_depense' => now()->toDateString(), 'libelle' => '', 'montant' => '',
            'mode_paiement' => ModePaiement::Especes->value, 'reference_piece' => '',
        ];
        $this->modal = true;
    }

    public function modifier(Depense $depense): void
    {
        $this->autoriserModification($depense);
        $this->resetValidation();
        $this->editId = $depense->id;
        $this->justificatif = null;
        $this->form = collect($depense->only(['portee', 'chantier_id', 'tache_id', 'categorie_id', 'fournisseur_id', 'date_depense', 'libelle', 'montant', 'mode_paiement', 'reference_piece']))
            ->map(fn ($v) => $v instanceof \BackedEnum ? $v->value : ($v instanceof \DateTimeInterface ? $v->format('Y-m-d') : ($v ?? '')))->all();
        $this->modal = true;
    }

    public function updatedFormPortee(): void
    {
        $this->form['categorie_id'] = '';
        $this->form['tache_id'] = '';
    }

    public function updatedFormChantierId(): void
    {
        $this->form['tache_id'] = '';
    }

    public function enregistrer(DepenseService $service): void
    {
        $this->authorize('depenses.create');
        $depense = $this->editId ? Depense::findOrFail($this->editId) : null;
        if ($depense) {
            $this->autoriserModification($depense);
        }

        $regles = collect(DepenseService::regles())->mapWithKeys(fn ($r, $k) => ["form.$k" => $r])->all();
        $regles['form.mode_paiement'] = ['required', Rule::enum(ModePaiement::class)];
        $regles['justificatif'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120';
        $data = $this->validate($regles, [], [
            'form.chantier_id' => 'chantier', 'form.categorie_id' => 'catégorie', 'form.date_depense' => 'date', 'form.libelle' => 'libellé',
        ])['form'];

        $service->enregistrer($data, auth()->user(), $this->justificatif, $depense);
        $this->modal = false;
        $this->justificatif = null;
        $this->succes($depense ? 'Dépense modifiée.' : 'Dépense enregistrée.');
    }

    public function valider(Depense $depense, DepenseService $service): void
    {
        $this->authorize('depenses.valider');
        $service->valider($depense, auth()->user());
        $this->succes('Dépense validée.');
    }

    public function validerSelection(DepenseService $service): void
    {
        $this->authorize('depenses.valider');
        $n = 0;
        foreach ($this->requete()->where('statut', StatutDepense::EnAttente)->get() as $d) {
            $service->valider($d, auth()->user());
            $n++;
        }
        $this->succes("$n dépense(s) validée(s).");
    }

    public function demanderRejet(int $id): void
    {
        $this->authorize('depenses.valider');
        $this->rejetId = $id;
        $this->motif = '';
        $this->resetValidation();
        $this->modalRejet = true;
    }

    public function rejeter(DepenseService $service): void
    {
        $this->authorize('depenses.valider');
        $this->validate(['motif' => 'required|string|max:255']);
        $service->rejeter(Depense::findOrFail($this->rejetId), auth()->user(), $this->motif);
        $this->modalRejet = false;
        $this->succes('Dépense rejetée.');
    }

    public function supprimer(Depense $depense): void
    {
        $this->autoriserModification($depense);
        if ($depense->justificatif) {
            Storage::disk('local')->delete($depense->justificatif);
        }
        $depense->delete();
        $this->succes('Dépense supprimée.');
    }

    /** Seules les dépenses en attente sont modifiables : par leur auteur ou un validateur. */
    private function autoriserModification(Depense $depense): void
    {
        abort_unless($depense->estModifiable(), 403, 'Cette dépense ne peut plus être modifiée.');
        abort_unless($depense->saisi_par === auth()->id() || Gate::allows('depenses.valider'), 403);
    }

    private function requete(): Builder
    {
        return Depense::visiblePour(auth()->user())
            ->when($this->recherche, fn ($q) => $q->where(fn ($q) => $q->whereLike('libelle', "%{$this->recherche}%")->orWhereLike('reference_piece', "%{$this->recherche}%")))
            ->when($this->portee, fn ($q) => $q->where('portee', $this->portee))
            ->when($this->statut, fn ($q) => $q->where('statut', $this->statut))
            ->when($this->chantier, fn ($q) => $q->where('chantier_id', $this->chantier))
            ->when($this->categorie, fn ($q) => $q->where('categorie_id', $this->categorie))
            ->when($this->du, fn ($q) => $q->whereDate('date_depense', '>=', $this->du))
            ->when($this->au, fn ($q) => $q->whereDate('date_depense', '<=', $this->au));
    }

    public function render()
    {
        $user = auth()->user();
        $totaux = $this->requete()->selectRaw('statut, SUM(montant) as total, COUNT(*) as nb')->groupBy('statut')->get()->keyBy(fn ($r) => $r->statut->value);
        $porteeForm = $this->form['portee'] ?? 'chantier';

        return view('livewire.depenses.index', [
            'depenses' => $this->requete()->with(['chantier', 'categorie', 'tache', 'fournisseur', 'saisiPar'])->latest('date_depense')->latest('id')->paginate(20),
            'totaux' => $totaux,
            'chantiers' => Chantier::visiblePour($user)->orderBy('nom')->pluck('nom', 'id'),
            'categories' => CategorieDepense::orderBy('nom')->get(),
            'categoriesForm' => CategorieDepense::where('portee', $porteeForm)->where('systeme', false)->orderBy('nom')->pluck('nom', 'id'),
            'tachesForm' => ! empty($this->form['chantier_id']) ? Chantier::find($this->form['chantier_id'])?->taches()->pluck('libelle', 'id') ?? collect() : collect(),
            'fournisseurs' => Fournisseur::orderBy('nom')->pluck('nom', 'id'),
            'parametresUrl' => http_build_query(array_filter(['portee' => $this->portee, 'statut' => $this->statut, 'chantier' => $this->chantier, 'categorie' => $this->categorie, 'du' => $this->du, 'au' => $this->au, 'recherche' => $this->recherche])),
        ]);
    }
}
