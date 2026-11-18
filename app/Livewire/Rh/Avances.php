<?php

namespace App\Livewire\Rh;

use App\Livewire\Concerns\Notifie;
use App\Models\Avance;
use App\Models\Employe;
use App\Models\PeriodePaie;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Avances sur salaire')]
class Avances extends Component
{
    use Notifie, WithPagination;

    #[Url]
    public string $periode = '';

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
        $this->form = ['employe_id' => '', 'date_avance' => now()->toDateString(), 'montant' => '', 'motif' => '', 'periode_retenue' => now()->format('Y-m')];
        $this->modal = true;
    }

    public function enregistrer(): void
    {
        $this->authorize('rh.manage');
        $data = $this->validate([
            'form.employe_id' => 'required|exists:employes,id',
            'form.date_avance' => 'required|date|before_or_equal:today',
            'form.montant' => 'required|numeric|min:1',
            'form.motif' => 'nullable|string|max:255',
            'form.periode_retenue' => ['required', 'date_format:Y-m'],
        ], [], ['form.employe_id' => 'employé', 'form.periode_retenue' => 'mois de retenue'])['form'];

        if (PeriodePaie::where('mois', $data['periode_retenue'])->where('statut', 'cloturee')->exists()) {
            $this->addError('form.periode_retenue', 'La paie de ce mois est clôturée : choisissez un mois suivant.');

            return;
        }
        Avance::create([...$data, 'motif' => $data['motif'] ?: null]);
        $this->modal = false;
        $this->succes('Avance enregistrée. Elle sera retenue sur la paie de '.$data['periode_retenue'].'.');
    }

    public function supprimer(Avance $avance): void
    {
        $this->authorize('rh.manage');
        if ($avance->bulletin_id) {
            $this->erreur('Avance déjà retenue sur un bulletin : régénérez la paie après suppression si besoin.');

            return;
        }
        $avance->delete();
        $this->succes('Avance supprimée.');
    }

    public function render()
    {
        return view('livewire.rh.avances', [
            'avances' => Avance::with(['employe', 'bulletin'])
                ->when($this->periode, fn ($q) => $q->where('periode_retenue', $this->periode))
                ->latest('date_avance')->paginate(20),
            'total' => (float) Avance::when($this->periode, fn ($q) => $q->where('periode_retenue', $this->periode))->sum('montant'),
            'employes' => Employe::actifs()->orderBy('nom')->get(['id', 'nom', 'prenom', 'matricule']),
        ]);
    }
}
