<?php

namespace App\Livewire\Rh;

use App\Enums\StatutConge;
use App\Enums\TypeConge;
use App\Livewire\Concerns\Notifie;
use App\Models\Conge;
use App\Models\Employe;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Congés & absences')]
class Conges extends Component
{
    use Notifie, WithPagination;

    #[Url]
    public string $statut = '';

    #[Url]
    public string $type = '';

    public bool $modal = false;

    public array $form = [];

    public function mount(): void
    {
        $this->authorize('rh.view');
    }

    public function creer(): void
    {
        $this->authorize('rh.manage');
        $this->resetValidation();
        $this->form = ['employe_id' => '', 'type' => TypeConge::CongeAnnuel->value, 'date_debut' => now()->toDateString(), 'date_fin' => now()->toDateString(), 'motif' => '', 'approuver' => false];
        $this->modal = true;
    }

    public function enregistrer(): void
    {
        $this->authorize('rh.manage');
        $data = $this->validate([
            'form.employe_id' => 'required|exists:employes,id',
            'form.type' => ['required', Rule::enum(TypeConge::class)],
            'form.date_debut' => 'required|date',
            'form.date_fin' => 'required|date|after_or_equal:form.date_debut',
            'form.motif' => 'nullable|string|max:255',
            'form.approuver' => 'boolean',
        ], [], ['form.employe_id' => 'employé', 'form.date_fin' => 'date de fin'])['form'];

        $chevauche = Conge::where('employe_id', $data['employe_id'])->where('statut', '!=', StatutConge::Refuse)
            ->whereDate('date_debut', '<=', $data['date_fin'])->whereDate('date_fin', '>=', $data['date_debut'])->exists();
        if ($chevauche) {
            $this->addError('form.date_debut', 'Cette période chevauche un congé déjà enregistré pour cet employé.');

            return;
        }

        $approuve = $data['approuver'] || $data['type'] === TypeConge::AbsenceInjustifiee->value;
        Conge::create([
            ...collect($data)->except('approuver')->all(),
            'motif' => $data['motif'] ?: null,
            'statut' => $approuve ? StatutConge::Approuve : StatutConge::Demande,
            'traite_par' => $approuve ? auth()->id() : null,
        ]);
        $this->modal = false;
        $this->succes('Enregistré.');
    }

    public function traiter(Conge $conge, string $decision): void
    {
        $this->authorize('rh.manage');
        abort_unless($conge->statut === StatutConge::Demande, 422);
        $conge->update(['statut' => $decision === 'approuve' ? StatutConge::Approuve : StatutConge::Refuse, 'traite_par' => auth()->id()]);
        $this->succes($decision === 'approuve' ? 'Congé approuvé.' : 'Congé refusé.');
    }

    public function supprimer(Conge $conge): void
    {
        $this->authorize('rh.manage');
        $conge->delete();
        $this->succes('Supprimé.');
    }

    public function render()
    {
        return view('livewire.rh.conges', [
            'conges' => Conge::with('employe')
                ->when($this->statut, fn ($q) => $q->where('statut', $this->statut))
                ->when($this->type, fn ($q) => $q->where('type', $this->type))
                ->latest('date_debut')->paginate(20),
            'absentsAujourdhui' => Conge::with('employe')->where('statut', StatutConge::Approuve)
                ->whereDate('date_debut', '<=', today())->whereDate('date_fin', '>=', today())->get(),
            'employes' => Employe::actifs()->orderBy('nom')->get(['id', 'nom', 'prenom', 'matricule']),
        ]);
    }
}
