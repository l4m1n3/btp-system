<?php

namespace App\Livewire\Rh;

use App\Livewire\Concerns\Notifie;
use App\Models\Chantier;
use App\Models\Employe;
use App\Models\Pointage as PointageModel;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Feuille de pointage journalière par chantier (ou siège).
 */
#[Title('Pointage')]
class Pointage extends Component
{
    use Notifie;

    #[Url]
    public string $chantier = '';

    #[Url]
    public string $date = '';

    /** @var array<int, array{present:bool, heures_sup:string|float, observation:string}> */
    public array $lignes = [];

    public function mount(): void
    {
        $this->authorize('pointage.saisir');
        $this->date = $this->date ?: now()->toDateString();
        if (! $this->chantier) {
            $this->chantier = (string) ($this->chantiers()->keys()->first() ?? (auth()->user()->voitTousLesChantiers() ? 'siege' : ''));
        }
        $this->charger();
    }

    public function updatedChantier(): void
    {
        $this->charger();
    }

    public function updatedDate(): void
    {
        $this->charger();
    }

    private function chantiers()
    {
        return Chantier::visiblePour(auth()->user())->actifs()->orderBy('nom')->pluck('nom', 'id');
    }

    private function employes()
    {
        if ($this->chantier === 'siege') {
            abort_unless(auth()->user()->voitTousLesChantiers(), 403);

            return Employe::actifs()->whereNull('chantier_id')->orderBy('nom')->get();
        }
        if (! $this->chantier || ! $this->chantiers()->has((int) $this->chantier)) {
            return collect();
        }

        return Employe::actifs()->where('chantier_id', $this->chantier)->orderBy('nom')->get();
    }

    private function charger(): void
    {
        $employes = $this->employes();
        $existants = PointageModel::whereIn('employe_id', $employes->pluck('id'))->whereDate('date_pointage', $this->date)->get()->keyBy('employe_id');
        $this->lignes = $employes->mapWithKeys(fn ($e) => [$e->id => [
            'present' => $existants[$e->id]->present ?? true,
            'heures_sup' => (float) ($existants[$e->id]->heures_sup ?? 0),
            'observation' => $existants[$e->id]->observation ?? '',
        ]])->all();
    }

    public function toutPresent(bool $present = true): void
    {
        foreach ($this->lignes as $id => $l) {
            $this->lignes[$id]['present'] = $present;
        }
    }

    public function enregistrer(): void
    {
        $this->authorize('pointage.saisir');
        $this->validate([
            'date' => 'required|date|before_or_equal:today',
            'lignes.*.heures_sup' => 'nullable|numeric|between:0,12',
            'lignes.*.observation' => 'nullable|string|max:255',
        ], [], ['lignes.*.heures_sup' => 'heures sup']);

        $autorises = $this->employes()->pluck('id')->all();
        $chantierId = $this->chantier === 'siege' ? null : (int) $this->chantier;

        DB::transaction(function () use ($autorises, $chantierId) {
            foreach ($this->lignes as $employeId => $l) {
                if (! in_array((int) $employeId, $autorises, true)) {
                    continue;
                }
                PointageModel::pour((int) $employeId, $this->date)->fill(
                    [
                        'chantier_id' => $chantierId,
                        'present' => (bool) $l['present'],
                        'heures_sup' => $l['present'] ? (float) ($l['heures_sup'] ?: 0) : 0,
                        'observation' => $l['observation'] ?: null,
                        'saisi_par' => auth()->id(),
                    ],
                )->save();
            }
        });

        $this->succes('Pointage du '.date_fr($this->date).' enregistré ('.count($this->lignes).' employé(s)).');
    }

    public function render()
    {
        return view('livewire.rh.pointage', [
            'chantiers' => $this->chantiers(),
            'employes' => $this->employes()->keyBy('id'),
            'dejaSaisi' => PointageModel::whereIn('employe_id', array_keys($this->lignes))->whereDate('date_pointage', $this->date)->exists(),
        ]);
    }
}
