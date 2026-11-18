<?php

namespace App\Livewire;

use App\Livewire\Concerns\Notifie;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Mon profil')]
class Profil extends Component
{
    use Notifie;

    public string $name = '';

    public string $telephone = '';

    public string $actuel = '';

    public string $nouveau = '';

    public string $nouveau_confirmation = '';

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->telephone = auth()->user()->telephone ?? '';
    }

    public function enregistrerProfil(): void
    {
        $this->validate(['name' => 'required|string|max:255', 'telephone' => 'nullable|string|max:30']);
        auth()->user()->update(['name' => $this->name, 'telephone' => $this->telephone ?: null]);
        $this->succes('Profil mis à jour.');
    }

    public function changerMotDePasse(): void
    {
        $this->validate([
            'actuel' => 'required|current_password',
            'nouveau' => ['required', 'confirmed', Password::min(8)],
        ], [], ['actuel' => 'mot de passe actuel', 'nouveau' => 'nouveau mot de passe']);
        auth()->user()->update(['password' => $this->nouveau]);
        $this->reset(['actuel', 'nouveau', 'nouveau_confirmation']);
        $this->succes('Mot de passe modifié.');
    }

    public function render()
    {
        return view('livewire.profil');
    }
}
