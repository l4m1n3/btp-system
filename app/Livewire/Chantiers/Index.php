<?php

namespace App\Livewire\Chantiers;

use App\Livewire\Concerns\Notifie;
use App\Models\Chantier;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Chantiers')]
class Index extends Component
{
    use FormulaireChantier, Notifie, WithPagination;

    #[Url]
    public string $recherche = '';

    #[Url]
    public string $statut = '';

    public function mount(): void
    {
        $this->authorize('chantiers.view');
        // Lien « + Chantier » depuis la fiche d'un marché
        if ($marcheId = request()->integer('nouveau')) {
            $this->creer($marcheId);
        }
    }

    public function updating($name): void
    {
        if (in_array($name, ['recherche', 'statut'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $chantiers = Chantier::visiblePour(auth()->user())
            ->with(['marche', 'chef', 'taches'])
            ->when($this->recherche, fn ($q) => $q->where(fn ($q) => $q->whereLike('nom', "%{$this->recherche}%")->orWhereLike('code', "%{$this->recherche}%")))
            ->when($this->statut, fn ($q) => $q->where('statut', $this->statut))
            ->orderByRaw("CASE statut WHEN 'en_cours' THEN 0 WHEN 'en_preparation' THEN 1 WHEN 'suspendu' THEN 2 ELSE 3 END")
            ->orderBy('nom')->paginate(15);

        return view('livewire.chantiers.index', ['chantiers' => $chantiers, ...$this->listesFormulaire()]);
    }
}
