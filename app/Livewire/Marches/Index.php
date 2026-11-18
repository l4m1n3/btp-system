<?php

namespace App\Livewire\Marches;

use App\Livewire\Concerns\Notifie;
use App\Models\Client;
use App\Models\Marche;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Marchés')]
class Index extends Component
{
    use FormulaireMarche, Notifie, WithPagination;

    #[Url]
    public string $recherche = '';

    #[Url]
    public string $statut = '';

    #[Url]
    public string $domaine = '';

    #[Url]
    public string $client = '';

    public function mount(): void
    {
        $this->authorize('marches.view');
    }

    public function updating($name): void
    {
        if (in_array($name, ['recherche', 'statut', 'domaine', 'client'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $marches = Marche::with(['client', 'decomptes'])
            ->when($this->recherche, fn ($q) => $q->where(fn ($q) => $q->whereLike('reference', "%{$this->recherche}%")->orWhereLike('intitule', "%{$this->recherche}%")))
            ->when($this->statut, fn ($q) => $q->where('statut', $this->statut))
            ->when($this->domaine, fn ($q) => $q->where('domaine', $this->domaine))
            ->when($this->client, fn ($q) => $q->where('client_id', $this->client))
            ->latest('date_signature')->latest('id')->paginate(15);

        $compteurs = Marche::selectRaw('statut, COUNT(*) as nb')->groupBy('statut')->pluck('nb', 'statut');

        return view('livewire.marches.index', [
            'marches' => $marches,
            'compteurs' => $compteurs,
            'clients' => Client::orderBy('nom')->pluck('nom', 'id'),
        ]);
    }
}
