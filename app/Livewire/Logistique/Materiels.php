<?php

namespace App\Livewire\Logistique;

use App\Enums\EtatMateriel;
use App\Enums\PorteeDepense;
use App\Enums\StatutDepense;
use App\Enums\TypeMateriel;
use App\Livewire\Concerns\Notifie;
use App\Models\CategorieDepense;
use App\Models\Chantier;
use App\Models\Depense;
use App\Models\Materiel;
use App\Services\DepenseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Matériel & engins')]
class Materiels extends Component
{
    use Notifie, WithPagination;

    #[Url]
    public string $recherche = '';

    #[Url]
    public string $etat = '';

    #[Url]
    public string $type = '';

    public bool $modal = false;

    public ?int $editId = null;

    public array $form = [];

    public bool $modalAffectation = false;

    public ?int $materielId = null;

    public array $affectation = [];

    public bool $modalMaintenance = false;

    public array $maintenance = [];

    public bool $modalHistorique = false;

    public function mount(): void
    {
        $this->authorize('logistique.view');
    }

    public function updating($name): void
    {
        if (in_array($name, ['recherche', 'etat', 'type'])) {
            $this->resetPage();
        }
    }

    public function editer(?int $id = null): void
    {
        $this->authorize('logistique.manage');
        $this->resetValidation();
        $m = $id ? Materiel::findOrFail($id) : null;
        $this->editId = $id;
        $this->form = [
            'code' => $m?->code ?? 'MAT-'.str_pad((string) (Materiel::max('id') + 1), 3, '0', STR_PAD_LEFT),
            'designation' => $m?->designation ?? '', 'type' => $m?->type->value ?? TypeMateriel::Engin->value,
            'marque' => $m?->marque ?? '', 'immatriculation' => $m?->immatriculation ?? '',
            'date_acquisition' => $m?->date_acquisition?->toDateString() ?? '', 'valeur_acquisition' => $m?->valeur_acquisition ?? '',
            'notes' => $m?->notes ?? '',
        ];
        $this->modal = true;
    }

    public function enregistrer(): void
    {
        $this->authorize('logistique.manage');
        $data = $this->validate([
            'form.code' => ['required', 'string', 'max:50', Rule::unique('materiels', 'code')->ignore($this->editId)],
            'form.designation' => 'required|string|max:255',
            'form.type' => ['required', Rule::enum(TypeMateriel::class)],
            'form.marque' => 'nullable|string|max:100',
            'form.immatriculation' => 'nullable|string|max:50',
            'form.date_acquisition' => 'nullable|date',
            'form.valeur_acquisition' => 'nullable|numeric|min:0',
            'form.notes' => 'nullable|string',
        ], [], ['form.designation' => 'désignation'])['form'];
        Materiel::updateOrCreate(['id' => $this->editId], array_map(fn ($v) => $v === '' ? null : $v, $data));
        $this->modal = false;
        $this->succes('Matériel enregistré.');
    }

    public function ouvrirAffectation(int $id): void
    {
        $this->authorize('logistique.manage');
        $this->resetValidation();
        $this->materielId = $id;
        $this->affectation = ['chantier_id' => '', 'date_debut' => now()->toDateString()];
        $this->modalAffectation = true;
    }

    public function affecter(): void
    {
        $this->authorize('logistique.manage');
        $this->validate([
            'affectation.chantier_id' => 'required|exists:chantiers,id',
            'affectation.date_debut' => 'required|date',
        ], [], ['affectation.chantier_id' => 'chantier']);
        $materiel = Materiel::findOrFail($this->materielId);
        if (in_array($materiel->etat, [EtatMateriel::EnPanne, EtatMateriel::HorsService, EtatMateriel::EnMaintenance])) {
            $this->addError('affectation.chantier_id', 'Ce matériel n\'est pas en état d\'être affecté ('.$materiel->etat->label().').');

            return;
        }
        DB::transaction(function () use ($materiel) {
            $this->cloturerAffectation($materiel, $this->affectation['date_debut']);
            $materiel->affectations()->create($this->affectation);
            $materiel->update(['chantier_id' => $this->affectation['chantier_id'], 'etat' => EtatMateriel::Affecte]);
        });
        $this->modalAffectation = false;
        $this->succes('Matériel affecté au chantier.');
    }

    public function liberer(Materiel $materiel): void
    {
        $this->authorize('logistique.manage');
        DB::transaction(function () use ($materiel) {
            $this->cloturerAffectation($materiel, now()->toDateString());
            $materiel->update(['chantier_id' => null, 'etat' => $materiel->etat === EtatMateriel::Affecte ? EtatMateriel::Disponible : $materiel->etat]);
        });
        $this->succes('Matériel retourné au parc.');
    }

    public function changerEtat(Materiel $materiel, string $etat): void
    {
        $this->authorize('logistique.manage');
        $etat = EtatMateriel::from($etat);
        if ($etat === EtatMateriel::Disponible && $materiel->chantier_id) {
            $etat = EtatMateriel::Affecte; // réparé et toujours sur le chantier
        }
        $materiel->update(['etat' => $etat]);
        $this->succes('État mis à jour : '.$etat->label().'.');
    }

    public function ouvrirMaintenance(int $id): void
    {
        $this->authorize('logistique.manage');
        $this->resetValidation();
        $this->materielId = $id;
        $this->maintenance = ['date_maintenance' => now()->toDateString(), 'type' => 'corrective', 'description' => '', 'cout' => 0, 'etat_apres' => '', 'creer_depense' => true];
        $this->modalMaintenance = true;
    }

    public function enregistrerMaintenance(DepenseService $depenses): void
    {
        $this->authorize('logistique.manage');
        $data = $this->validate([
            'maintenance.date_maintenance' => 'required|date|before_or_equal:today',
            'maintenance.type' => 'required|in:preventive,corrective',
            'maintenance.description' => 'required|string|max:255',
            'maintenance.cout' => 'required|numeric|min:0',
            'maintenance.etat_apres' => ['nullable', Rule::enum(EtatMateriel::class)],
            'maintenance.creer_depense' => 'boolean',
        ])['maintenance'];
        $materiel = Materiel::findOrFail($this->materielId);

        DB::transaction(function () use ($data, $materiel, $depenses) {
            $depenseId = null;
            // Le coût de l'entretien est enregistré comme dépense (soumise à validation),
            // imputée au chantier si l'engin y est affecté, sinon en frais généraux.
            if ($data['creer_depense'] && (float) $data['cout'] > 0) {
                $portee = $materiel->chantier_id ? PorteeDepense::Chantier : PorteeDepense::Interne;
                $categorie = CategorieDepense::firstOrCreate(['nom' => 'Entretien et réparation matériel', 'portee' => $portee->value]);
                $depense = Depense::create([
                    'portee' => $portee, 'chantier_id' => $materiel->chantier_id, 'categorie_id' => $categorie->id,
                    'date_depense' => $data['date_maintenance'], 'libelle' => 'Maintenance '.$materiel->code.' : '.$data['description'],
                    'montant' => $data['cout'], 'mode_paiement' => 'especes', 'statut' => StatutDepense::EnAttente,
                    'saisi_par' => auth()->id(),
                ]);
                if (Gate::allows('depenses.valider')) {
                    $depenses->valider($depense, auth()->user());
                }
                $depenseId = $depense->id;
            }
            $materiel->maintenances()->create([...collect($data)->only(['date_maintenance', 'type', 'description', 'cout'])->all(), 'depense_id' => $depenseId]);
            if ($data['etat_apres']) {
                $this->changerEtat($materiel, $data['etat_apres']);
            }
        });
        $this->modalMaintenance = false;
        $this->succes('Maintenance enregistrée.');
    }

    public function voirHistorique(int $id): void
    {
        $this->materielId = $id;
        $this->modalHistorique = true;
    }

    private function cloturerAffectation(Materiel $materiel, string $date): void
    {
        $materiel->affectations()->whereNull('date_fin')->update(['date_fin' => $date]);
    }

    public function render()
    {
        $materiels = Materiel::with('chantier')
            ->when($this->recherche, fn ($q) => $q->where(fn ($q) => $q->whereLike('designation', "%{$this->recherche}%")->orWhereLike('code', "%{$this->recherche}%")->orWhereLike('immatriculation', "%{$this->recherche}%")))
            ->when($this->etat, fn ($q) => $q->where('etat', $this->etat))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->orderBy('code')->paginate(20);

        return view('livewire.logistique.materiels', [
            'materiels' => $materiels,
            'compteurs' => Materiel::selectRaw('etat, COUNT(*) as nb')->groupBy('etat')->pluck('nb', 'etat'),
            'chantiers' => Chantier::actifs()->orderBy('nom')->pluck('nom', 'id'),
            'historique' => $this->modalHistorique && $this->materielId
                ? Materiel::with(['affectations.chantier', 'maintenances'])->find($this->materielId) : null,
        ]);
    }
}
