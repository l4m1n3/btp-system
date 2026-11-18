<?php

namespace App\Livewire\Admin;

use App\Enums\Role;
use App\Livewire\Concerns\Notifie;
use App\Models\Employe;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Utilisateurs')]
class Utilisateurs extends Component
{
    use Notifie;

    public bool $modal = false;

    public ?int $editId = null;

    public array $form = [];

    public ?string $motDePasseGenere = null;

    public function mount(): void
    {
        $this->authorize('users.manage');
    }

    public function editer(?int $id = null): void
    {
        $this->resetValidation();
        $u = $id ? User::findOrFail($id) : null;
        $this->editId = $id;
        $this->motDePasseGenere = null;
        $this->form = [
            'name' => $u?->name ?? '', 'email' => $u?->email ?? '', 'telephone' => $u?->telephone ?? '',
            'role' => $u?->role->value ?? Role::ChefChantier->value, 'employe_id' => $u?->employe_id ?? '',
            'actif' => $u?->actif ?? true, 'password' => '',
        ];
        $this->modal = true;
    }

    public function enregistrer(): void
    {
        $data = $this->validate([
            'form.name' => 'required|string|max:255',
            'form.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editId)],
            'form.telephone' => 'nullable|string|max:30',
            'form.role' => ['required', Rule::enum(Role::class)],
            'form.employe_id' => ['nullable', 'exists:employes,id', Rule::unique('users', 'employe_id')->ignore($this->editId)],
            'form.actif' => 'boolean',
            'form.password' => ['nullable', Password::min(8)],
        ], [], ['form.name' => 'nom', 'form.password' => 'mot de passe', 'form.employe_id' => 'employé'])['form'];

        if ($this->editId === auth()->id() && ($data['role'] !== Role::Admin->value || ! $data['actif'])) {
            $this->addError('form.role', 'Vous ne pouvez pas retirer vos propres droits administrateur ni désactiver votre compte.');

            return;
        }

        $data['employe_id'] = $data['employe_id'] ?: null;
        $motDePasse = $data['password'] ?: null;
        unset($data['password']);
        if (! $this->editId && ! $motDePasse) {
            $motDePasse = Str::password(12, symbols: false);
            $this->motDePasseGenere = $motDePasse;
        }
        if ($motDePasse) {
            $data['password'] = $motDePasse; // haché par le cast du modèle
        }

        $user = User::updateOrCreate(['id' => $this->editId], $data);
        if (! $user->actif) {
            $user->tokens()->delete();
        }
        $this->succes('Utilisateur enregistré.');
        if (! $this->motDePasseGenere) {
            $this->modal = false;
        }
    }

    public function render()
    {
        return view('livewire.admin.utilisateurs', [
            'utilisateurs' => User::with('employe')->orderBy('name')->get(),
            'employes' => Employe::actifs()->orderBy('nom')->get(['id', 'nom', 'prenom', 'matricule']),
        ]);
    }
}
