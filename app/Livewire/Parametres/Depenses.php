<?php

namespace App\Livewire\Parametres;

use App\Enums\PorteeDepense;
use App\Livewire\Concerns\Notifie;
use App\Models\CategorieDepense;
use App\Models\Fournisseur;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Catégories & fournisseurs')]
class Depenses extends Component
{
    use Notifie;

    public bool $modalCategorie = false;

    public ?int $categorieId = null;

    public array $categorie = [];

    public bool $modalFournisseur = false;

    public ?int $fournisseurId = null;

    public array $fournisseur = [];

    public string $rechercheFournisseur = '';

    public function mount(): void
    {
        $this->authorize('parametres.manage');
    }

    public function editerCategorie(?int $id = null): void
    {
        $this->resetValidation();
        $c = $id ? CategorieDepense::findOrFail($id) : null;
        abort_if($c?->systeme, 403, 'Catégorie système non modifiable.');
        $this->categorieId = $id;
        $this->categorie = ['nom' => $c?->nom ?? '', 'portee' => $c?->portee->value ?? PorteeDepense::Chantier->value, 'description' => $c?->description ?? ''];
        $this->modalCategorie = true;
    }

    public function enregistrerCategorie(): void
    {
        $data = $this->validate([
            'categorie.nom' => ['required', 'string', 'max:100', Rule::unique('categories_depenses', 'nom')->where('portee', $this->categorie['portee'] ?? '')->ignore($this->categorieId)],
            'categorie.portee' => ['required', Rule::enum(PorteeDepense::class)],
            'categorie.description' => 'nullable|string|max:255',
        ], [], ['categorie.nom' => 'nom'])['categorie'];
        CategorieDepense::updateOrCreate(['id' => $this->categorieId], $data);
        $this->modalCategorie = false;
        $this->succes('Catégorie enregistrée.');
    }

    public function supprimerCategorie(CategorieDepense $categorie): void
    {
        if ($categorie->systeme || $categorie->depenses()->exists()) {
            $this->erreur('Catégorie utilisée ou système : suppression impossible.');

            return;
        }
        $categorie->delete();
        $this->succes('Catégorie supprimée.');
    }

    public function editerFournisseur(?int $id = null): void
    {
        $this->resetValidation();
        $f = $id ? Fournisseur::findOrFail($id) : new Fournisseur;
        $this->fournisseurId = $id;
        $this->fournisseur = ['nom' => $f->nom ?? '', 'nif' => $f->nif ?? '', 'telephone' => $f->telephone ?? '', 'email' => $f->email ?? '', 'adresse' => $f->adresse ?? ''];
        $this->modalFournisseur = true;
    }

    public function enregistrerFournisseur(): void
    {
        $data = $this->validate([
            'fournisseur.nom' => 'required|string|max:255',
            'fournisseur.nif' => 'nullable|string|max:50',
            'fournisseur.telephone' => 'nullable|string|max:30',
            'fournisseur.email' => 'nullable|email|max:255',
            'fournisseur.adresse' => 'nullable|string|max:255',
        ], [], ['fournisseur.nom' => 'nom'])['fournisseur'];
        Fournisseur::updateOrCreate(['id' => $this->fournisseurId], $data);
        $this->modalFournisseur = false;
        $this->succes('Fournisseur enregistré.');
    }

    public function supprimerFournisseur(Fournisseur $fournisseur): void
    {
        if ($fournisseur->depenses()->exists()) {
            $this->erreur('Ce fournisseur a des dépenses : suppression impossible.');

            return;
        }
        $fournisseur->delete();
        $this->succes('Fournisseur supprimé.');
    }

    public function render()
    {
        return view('livewire.parametres.depenses', [
            'categories' => CategorieDepense::withCount('depenses')->orderBy('portee')->orderBy('nom')->get()->groupBy(fn ($c) => $c->portee->value),
            'fournisseurs' => Fournisseur::withCount('depenses')->withSum('depenses', 'montant')
                ->when($this->rechercheFournisseur, fn ($q) => $q->whereLike('nom', "%{$this->rechercheFournisseur}%"))
                ->orderBy('nom')->limit(100)->get(),
        ]);
    }
}
