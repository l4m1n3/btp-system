<?php

namespace App\Livewire\Paie;

use App\Livewire\Concerns\Notifie;
use App\Models\PeriodePaie;
use App\Services\PaieService;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Paie')]
class Index extends Component
{
    use Notifie;

    public string $mois = '';

    public function mount(): void
    {
        $this->authorize('paie.view');
        $this->mois = now()->format('Y-m');
    }

    public function ouvrir(PaieService $paie)
    {
        $this->authorize('paie.manage');
        $this->validate(['mois' => 'required|date_format:Y-m']);
        $periode = $paie->ouvrirPeriode($this->mois);

        return $this->redirectRoute('paie.show', $periode, navigate: true);
    }

    public function render()
    {
        return view('livewire.paie.index', [
            'periodes' => PeriodePaie::withCount('bulletins')->withSum('bulletins', 'salaire_brut')->withSum('bulletins', 'net_a_payer')
                ->orderByDesc('mois')->get(),
        ]);
    }
}
