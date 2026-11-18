<?php

namespace App\Livewire\Chantiers;

use App\Enums\TypeLigneJournal;
use App\Models\Chantier;
use App\Models\RapportJournalier;
use App\Services\JournalService;
use Livewire\Component;

/**
 * Rapport journalier de chantier (création / modification / consultation).
 * Les lignes « travaux » liées à une tâche mettent à jour l'avancement de celle-ci.
 */
class Journal extends Component
{
    public Chantier $chantier;

    public ?RapportJournalier $rapport = null;

    public string $date_rapport = '';

    public string $meteo = '';

    public string $observations = '';

    public string $incidents = '';

    /** @var array<int, array{type:string, designation:string, quantite:?string, unite:?string, tache_id:?string, avancement:?string}> */
    public array $lignes = [];

    public bool $lectureSeule = false;

    public function mount(Chantier $chantier, ?RapportJournalier $rapport = null): void
    {
        $this->authorize('chantier.voir', $chantier);
        $this->chantier = $chantier;
        $this->lectureSeule = ! auth()->user()->can('chantier.suivre', $chantier);

        if ($rapport?->exists) {
            abort_unless($rapport->chantier_id === $chantier->id, 404);
            $this->rapport = $rapport;
            $this->date_rapport = $rapport->date_rapport->toDateString();
            $this->meteo = $rapport->meteo ?? '';
            $this->observations = $rapport->observations ?? '';
            $this->incidents = $rapport->incidents ?? '';
            $this->lignes = $rapport->lignes->map(fn ($l) => [
                'type' => $l->type->value, 'designation' => $l->designation, 'quantite' => $l->quantite,
                'unite' => $l->unite, 'tache_id' => $l->tache_id, 'avancement' => $l->avancement,
            ])->all();
        } else {
            abort_if($this->lectureSeule, 403);
            $this->date_rapport = now()->toDateString();
            foreach (TypeLigneJournal::cases() as $type) {
                $this->ajouterLigne($type->value);
            }
        }
    }

    public function ajouterLigne(string $type): void
    {
        $this->lignes[] = ['type' => $type, 'designation' => '', 'quantite' => null, 'unite' => null, 'tache_id' => null, 'avancement' => null];
    }

    public function retirerLigne(int $index): void
    {
        unset($this->lignes[$index]);
    }

    /** Pré-remplit la désignation et l'avancement quand une tâche est choisie. */
    public function updatedLignes($valeur, ?string $cle = null): void
    {
        if (! $cle || ! str_contains($cle, '.')) {
            return;
        }
        [$i, $champ] = explode('.', $cle);
        if ($champ === 'tache_id' && $valeur && $tache = $this->chantier->taches()->find($valeur)) {
            $this->lignes[$i]['designation'] = $this->lignes[$i]['designation'] ?: $tache->libelle;
            $this->lignes[$i]['avancement'] ??= $tache->avancement;
        }
    }

    public function enregistrer(JournalService $journal)
    {
        $this->authorize('chantier.suivre', $this->chantier);
        $this->lignes = array_values(array_filter($this->lignes, fn ($l) => trim((string) $l['designation']) !== ''));

        $data = $this->validate(
            JournalService::regles($this->chantier, $this->rapport),
            ['date_rapport.unique' => 'Un rapport existe déjà pour ce chantier à cette date.'],
            ['lignes.*.designation' => 'désignation'],
        );
        $journal->enregistrer($this->chantier, $data, auth()->user(), $this->rapport);

        session()->flash('success', 'Rapport journalier enregistré.');

        return $this->redirectRoute('chantiers.show', ['chantier' => $this->chantier, 'onglet' => 'journal'], navigate: true);
    }

    public function render()
    {
        return view('livewire.chantiers.journal', [
            'taches' => $this->chantier->taches()->get(['id', 'libelle', 'avancement']),
        ])->title('Journal — '.$this->chantier->nom);
    }
}
