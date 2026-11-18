<?php

namespace App\Livewire\Marches;

use App\Enums\StatutDecompte;
use App\Livewire\Concerns\Notifie;
use App\Models\Decompte;
use App\Models\Marche;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    use FormulaireMarche, Notifie;

    public Marche $marche;

    // Décomptes
    public bool $modalDecompte = false;

    public ?int $decompteId = null;

    public array $decompte = [];

    public function mount(Marche $marche): void
    {
        $this->authorize('marches.view');
        $this->marche = $marche;
    }

    public function nouveauDecompte(): void
    {
        $this->authorize('marches.manage');
        $this->resetValidation();
        $this->decompteId = null;
        $this->decompte = [
            'numero' => ($this->marche->decomptes()->max('numero') ?? 0) + 1,
            'date_decompte' => now()->toDateString(), 'montant_ht' => '', 'montant_paye' => 0,
            'date_paiement' => '', 'statut' => StatutDecompte::Soumis->value, 'observations' => '',
        ];
        $this->modalDecompte = true;
    }

    public function modifierDecompte(Decompte $decompte): void
    {
        $this->authorize('marches.manage');
        abort_unless($decompte->marche_id === $this->marche->id, 404);
        $this->resetValidation();
        $this->decompteId = $decompte->id;
        $this->decompte = [
            'numero' => $decompte->numero, 'date_decompte' => $decompte->date_decompte->toDateString(),
            'montant_ht' => $decompte->montant_ht, 'montant_paye' => $decompte->montant_paye,
            'date_paiement' => $decompte->date_paiement?->toDateString() ?? '', 'statut' => $decompte->statut->value,
            'observations' => $decompte->observations ?? '',
        ];
        $this->modalDecompte = true;
    }

    public function enregistrerDecompte(): void
    {
        $this->authorize('marches.manage');
        $data = $this->validate([
            'decompte.numero' => ['required', 'integer', 'min:1', Rule::unique('decomptes', 'numero')->where('marche_id', $this->marche->id)->ignore($this->decompteId)],
            'decompte.date_decompte' => 'required|date',
            'decompte.montant_ht' => 'required|numeric|min:0',
            'decompte.montant_paye' => 'nullable|numeric|min:0',
            'decompte.date_paiement' => 'nullable|date',
            'decompte.statut' => ['required', Rule::enum(StatutDecompte::class)],
            'decompte.observations' => 'nullable|string|max:255',
        ], [], ['decompte.numero' => 'numéro', 'decompte.date_paiement' => 'date de paiement'])['decompte'];

        $data = array_map(fn ($v) => $v === '' ? null : $v, $data);
        $data['montant_paye'] ??= 0;
        if ((float) $data['montant_paye'] <= 0) {
            $data['date_paiement'] = null;
        } elseif ($data['statut'] !== StatutDecompte::Rejete->value) {
            $data['statut'] = StatutDecompte::Paye->value;
            $data['date_paiement'] ??= now()->toDateString();
        }

        $this->marche->decomptes()->updateOrCreate(['id' => $this->decompteId], $data);
        $this->modalDecompte = false;
        $this->succes('Décompte enregistré.');
    }

    public function supprimerDecompte(Decompte $decompte): void
    {
        $this->authorize('marches.manage');
        abort_unless($decompte->marche_id === $this->marche->id, 404);
        $decompte->delete();
        $this->succes('Décompte supprimé.');
    }

    public function render()
    {
        $this->marche->load(['client', 'decomptes', 'chantiers.taches', 'chantiers.chef']);

        return view('livewire.marches.show')->title($this->marche->reference);
    }
}
