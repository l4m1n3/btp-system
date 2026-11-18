<?php

namespace App\Livewire\Paie;

use App\Enums\TypeLignePaie;
use App\Livewire\Concerns\Notifie;
use App\Models\Bulletin;
use App\Models\PeriodePaie;
use App\Services\PaieService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Periode extends Component
{
    use Notifie;

    public PeriodePaie $periode;

    public bool $modal = false;

    public ?int $bulletinId = null;

    public array $ligne = [];

    public array $imputation = [];

    public function mount(PeriodePaie $periode): void
    {
        $this->authorize('paie.view');
        $this->periode = $periode;
    }

    public function generer(PaieService $paie): void
    {
        $this->authorize('paie.manage');
        $this->executer(fn () => $this->succes($paie->generer($this->periode).' bulletin(s) calculé(s) à partir du pointage, des absences et des avances.'));
    }

    public function ouvrirBulletin(int $id): void
    {
        $this->bulletinId = $id;
        $b = $this->bulletin();
        $this->ligne = ['type' => TypeLignePaie::Prime->value, 'libelle' => '', 'montant' => ''];
        $this->imputation = ['chantier_id' => $b->chantier_id ?? ''];
        $this->resetValidation();
        $this->modal = true;
    }

    public function ajouterLigne(PaieService $paie): void
    {
        $this->authorize('paie.manage');
        $this->validate([
            'ligne.type' => ['required', Rule::enum(TypeLignePaie::class)],
            'ligne.libelle' => 'required|string|max:100',
            'ligne.montant' => 'required|numeric|min:1',
        ], [], ['ligne.libelle' => 'libellé', 'ligne.montant' => 'montant']);
        $this->executer(function () use ($paie) {
            $paie->ajouterLigne($this->bulletin(), TypeLignePaie::from($this->ligne['type']), $this->ligne['libelle'], (float) $this->ligne['montant']);
            $this->ligne['libelle'] = '';
            $this->ligne['montant'] = '';
        });
    }

    public function supprimerLigne(int $ligneId, PaieService $paie): void
    {
        $this->authorize('paie.manage');
        $this->executer(fn () => $paie->supprimerLigne($this->bulletin(), $ligneId));
    }

    public function changerImputation(): void
    {
        $this->authorize('paie.manage');
        abort_if($this->periode->estCloturee(), 422);
        $this->validate(['imputation.chantier_id' => 'nullable|exists:chantiers,id']);
        $this->bulletin()->update(['chantier_id' => $this->imputation['chantier_id'] ?: null]);
        $this->succes('Imputation modifiée.');
    }

    public function basculerPaye(int $id): void
    {
        $this->authorize('paie.manage');
        $this->bulletinId = $id;
        $b = $this->bulletin();
        $b->update(['paye' => ! $b->paye, 'date_paiement' => $b->paye ? null : now()]);
    }

    public function cloturer(PaieService $paie): void
    {
        $this->authorize('paie.cloturer');
        $this->executer(function () use ($paie) {
            $paie->cloturer($this->periode, auth()->user());
            $this->periode->refresh();
            $this->succes('Paie clôturée : la masse salariale a été enregistrée dans les dépenses.');
        });
    }

    public function rouvrir(PaieService $paie): void
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);
        $paie->rouvrir($this->periode);
        $this->periode->refresh();
        $this->succes('Période rouverte ; les dépenses de salaires générées ont été supprimées.');
    }

    private function bulletin(): Bulletin
    {
        return $this->periode->bulletins()->with(['employe', 'lignes', 'avances'])->findOrFail($this->bulletinId);
    }

    private function executer(callable $action): void
    {
        try {
            $action();
        } catch (ValidationException $e) {
            $this->erreur($e->validator->errors()->first());
        }
    }

    public function render()
    {
        $bulletins = $this->periode->bulletins()->with(['employe', 'chantier'])->get()->sortBy(fn ($b) => $b->employe->nom);

        return view('livewire.paie.periode', [
            'bulletins' => $bulletins,
            'bulletin' => $this->modal && $this->bulletinId ? $this->bulletin() : null,
            'chantiers' => \App\Models\Chantier::orderBy('nom')->pluck('nom', 'id'),
            'parImputation' => $bulletins->groupBy(fn ($b) => $b->chantier?->nom ?? 'Siège (interne)')->map(fn ($g) => $g->sum(fn ($b) => (float) $b->salaire_brut)),
        ])->title('Paie '.$this->periode->libelle);
    }
}
