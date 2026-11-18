<?php

namespace App\Livewire\Rh;

use App\Enums\ModePaiement;
use App\Enums\ModeRemuneration;
use App\Enums\StatutEmploye;
use App\Enums\TypeContrat;
use App\Livewire\Concerns\Notifie;
use App\Models\Chantier;
use App\Models\Employe;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Employés')]
class Employes extends Component
{
    use Notifie, WithPagination;

    #[Url]
    public string $recherche = '';

    #[Url]
    public string $statut = 'actif';

    #[Url]
    public string $chantier = '';

    #[Url]
    public string $contrat = '';

    public bool $modal = false;

    public ?int $editId = null;

    public array $form = [];

    public function mount(): void
    {
        $this->authorize('rh.view');
    }

    public function updating($name): void
    {
        if (in_array($name, ['recherche', 'statut', 'chantier', 'contrat'])) {
            $this->resetPage();
        }
    }

    public function editer(?int $id = null): void
    {
        $this->authorize('rh.manage');
        $this->resetValidation();
        $e = $id ? Employe::findOrFail($id) : null;
        $this->editId = $id;
        $this->form = $e
            ? collect($e->only($e->getFillable()))->map(fn ($v) => $v instanceof \BackedEnum ? $v->value : ($v instanceof \DateTimeInterface ? $v->format('Y-m-d') : ($v ?? '')))->all()
            : [
                'matricule' => 'EMP-'.str_pad((string) (Employe::max('id') + 1), 4, '0', STR_PAD_LEFT), 'nom' => '', 'prenom' => '',
                'sexe' => '', 'date_naissance' => '', 'telephone' => '', 'adresse' => '', 'numero_cnss' => '', 'fonction' => '',
                'categorie' => '', 'chantier_id' => '', 'type_contrat' => TypeContrat::Cdi->value, 'date_embauche' => now()->toDateString(),
                'date_fin_contrat' => '', 'mode_remuneration' => ModeRemuneration::Mensuel->value, 'salaire_base' => '', 'taux_journalier' => '',
                'statut' => StatutEmploye::Actif->value, 'mode_paiement' => ModePaiement::Especes->value, 'numero_compte' => '',
            ];
        $this->modal = true;
    }

    public function enregistrer(): void
    {
        $this->authorize('rh.manage');
        $mensuel = ($this->form['mode_remuneration'] ?? '') === ModeRemuneration::Mensuel->value;
        $data = $this->validate([
            'form.matricule' => ['required', 'string', 'max:30', Rule::unique('employes', 'matricule')->ignore($this->editId)],
            'form.nom' => 'required|string|max:100',
            'form.prenom' => 'required|string|max:100',
            'form.sexe' => 'nullable|in:M,F',
            'form.date_naissance' => 'nullable|date|before:today',
            'form.telephone' => 'nullable|string|max:30',
            'form.adresse' => 'nullable|string|max:255',
            'form.numero_cnss' => 'nullable|string|max:50',
            'form.fonction' => 'required|string|max:100',
            'form.categorie' => 'nullable|string|max:100',
            'form.chantier_id' => 'nullable|exists:chantiers,id',
            'form.type_contrat' => ['required', Rule::enum(TypeContrat::class)],
            'form.date_embauche' => 'required|date',
            'form.date_fin_contrat' => ['nullable', 'date', 'after_or_equal:form.date_embauche', Rule::requiredIf(($this->form['type_contrat'] ?? '') === TypeContrat::Cdd->value)],
            'form.mode_remuneration' => ['required', Rule::enum(ModeRemuneration::class)],
            'form.salaire_base' => [$mensuel ? 'required' : 'nullable', 'numeric', 'min:0'],
            'form.taux_journalier' => [$mensuel ? 'nullable' : 'required', 'numeric', 'min:0'],
            'form.statut' => ['required', Rule::enum(StatutEmploye::class)],
            'form.mode_paiement' => ['required', Rule::enum(ModePaiement::class)],
            'form.numero_compte' => 'nullable|string|max:100',
        ], [], [
            'form.date_fin_contrat' => 'fin de contrat', 'form.salaire_base' => 'salaire de base',
            'form.taux_journalier' => 'taux journalier', 'form.date_embauche' => "date d'embauche",
        ])['form'];

        $data = array_map(fn ($v) => $v === '' ? null : $v, $data);
        $data['salaire_base'] ??= 0;
        $data['taux_journalier'] ??= 0;
        Employe::updateOrCreate(['id' => $this->editId], $data);
        $this->modal = false;
        $this->succes('Employé enregistré.');
    }

    public function render()
    {
        $employes = Employe::with('chantier')
            ->when($this->recherche, fn ($q) => $q->where(fn ($q) => $q->whereLike('nom', "%{$this->recherche}%")->orWhereLike('prenom', "%{$this->recherche}%")->orWhereLike('matricule', "%{$this->recherche}%")->orWhereLike('fonction', "%{$this->recherche}%")))
            ->when($this->statut, fn ($q) => $q->where('statut', $this->statut))
            ->when($this->chantier === 'siege', fn ($q) => $q->whereNull('chantier_id'))
            ->when($this->chantier && $this->chantier !== 'siege', fn ($q) => $q->where('chantier_id', $this->chantier))
            ->when($this->contrat, fn ($q) => $q->where('type_contrat', $this->contrat))
            ->orderBy('nom')->orderBy('prenom')->paginate(20);

        $actifs = Employe::actifs();

        return view('livewire.rh.employes', [
            'employes' => $employes,
            'effectif' => (clone $actifs)->count(),
            'masseMensuelle' => (float) (clone $actifs)->where('mode_remuneration', 'mensuel')->sum('salaire_base'),
            'journaliers' => (clone $actifs)->where('mode_remuneration', 'journalier')->count(),
            'chantiers' => Chantier::actifs()->orderBy('nom')->pluck('nom', 'id'),
        ]);
    }
}
