<?php

namespace App\Livewire\Clients;

use App\Livewire\Concerns\Notifie;
use App\Models\Client;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Clients')]
class Index extends Component
{
    use Notifie, WithPagination;

    #[Url]
    public string $recherche = '';

    public bool $modal = false;

    public ?int $editId = null;

    public array $form = [];

    public function mount(): void
    {
        $this->authorize('marches.view');
    }

    public function updatedRecherche(): void
    {
        $this->resetPage();
    }

    public function creer(): void
    {
        $this->authorize('marches.manage');
        $this->resetValidation();
        $this->editId = null;
        $this->form = ['nom' => '', 'type' => 'public', 'contact' => '', 'telephone' => '', 'email' => '', 'adresse' => '', 'notes' => ''];
        $this->modal = true;
    }

    public function modifier(Client $client): void
    {
        $this->authorize('marches.manage');
        $this->resetValidation();
        $this->editId = $client->id;
        $this->form = $client->only(['nom', 'type', 'contact', 'telephone', 'email', 'adresse', 'notes']);
        $this->modal = true;
    }

    public function enregistrer(): void
    {
        $this->authorize('marches.manage');
        $data = $this->validate([
            'form.nom' => 'required|string|max:255',
            'form.type' => 'required|in:'.implode(',', array_keys(Client::TYPES)),
            'form.contact' => 'nullable|string|max:255',
            'form.telephone' => 'nullable|string|max:30',
            'form.email' => 'nullable|email|max:255',
            'form.adresse' => 'nullable|string|max:255',
            'form.notes' => 'nullable|string',
        ])['form'];

        Client::updateOrCreate(['id' => $this->editId], $data);
        $this->modal = false;
        $this->succes('Client enregistré.');
    }

    public function supprimer(Client $client): void
    {
        $this->authorize('marches.manage');
        if ($client->marches()->exists()) {
            $this->erreur('Ce client a des marchés : suppression impossible.');

            return;
        }
        $client->delete();
        $this->succes('Client supprimé.');
    }

    public function render()
    {
        $clients = Client::withCount('marches')->withSum('marches', 'montant_ht')
            ->when($this->recherche, fn ($q) => $q->whereLike('nom', "%{$this->recherche}%"))
            ->orderBy('nom')->paginate(15);

        return view('livewire.clients.index', compact('clients'));
    }
}
