<?php

namespace App\Livewire\Rh;

use App\Models\Employe;
use Livewire\Attributes\Url;
use Livewire\Component;

class EmployeShow extends Component
{
    public Employe $employe;

    #[Url]
    public string $mois = '';

    public function mount(Employe $employe): void
    {
        $this->authorize('rh.view');
        $this->employe = $employe;
        $this->mois = $this->mois ?: now()->format('Y-m');
    }

    public function render()
    {
        $debut = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $this->mois.'-01')->startOfMonth();
        $pointages = $this->employe->pointages()->with('chantier')
            ->whereBetween('date_pointage', [$debut, $debut->copy()->endOfMonth()])->orderBy('date_pointage')->get();

        return view('livewire.rh.employe-show', [
            'e' => $this->employe->load(['chantier', 'user']),
            'debut' => $debut,
            'pointages' => $pointages,
            'conges' => $this->employe->conges()->limit(20)->get(),
            'avances' => $this->employe->avances()->limit(20)->get(),
            'bulletins' => $this->employe->bulletins()->with('periode')->latest('id')->limit(12)->get(),
        ])->title($this->employe->nom_complet);
    }
}
