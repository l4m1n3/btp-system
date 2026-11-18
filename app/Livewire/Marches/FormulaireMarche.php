<?php

namespace App\Livewire\Marches;

use App\Enums\Domaine;
use App\Enums\StatutMarche;
use App\Models\Marche;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Formulaire de création / modification d'un marché (partagé entre la liste et la fiche).
 */
trait FormulaireMarche
{
    public bool $modal = false;

    public ?int $editId = null;

    public array $form = [];

    public function creer(): void
    {
        $this->authorize('marches.manage');
        $this->resetValidation();
        $this->editId = null;
        $this->form = [
            'reference' => '', 'intitule' => '', 'client_id' => '', 'domaine' => Domaine::Batiment->value,
            'statut' => StatutMarche::EnAttente->value, 'localisation' => '', 'montant_ht' => '', 'taux_tva' => 19,
            'taux_retenue_garantie' => 5, 'avance_demarrage' => 0, 'date_soumission' => '', 'date_signature' => '',
            'date_ordre_service' => '', 'delai_jours' => '', 'date_fin_prevue' => '', 'date_reception' => '', 'description' => '',
        ];
        $this->modal = true;
    }

    public function modifier(Marche $marche): void
    {
        $this->authorize('marches.manage');
        $this->resetValidation();
        $this->editId = $marche->id;
        $this->form = collect($marche->getAttributes())->only($marche->getFillable())
            ->map(fn ($v) => $v ?? '')->all();
        foreach (['date_soumission', 'date_signature', 'date_ordre_service', 'date_fin_prevue', 'date_reception'] as $d) {
            $this->form[$d] = $marche->{$d}?->format('Y-m-d') ?? '';
        }
        $this->modal = true;
    }

    public function enregistrer(): void
    {
        $this->authorize('marches.manage');
        $data = $this->validate([
            'form.reference' => ['required', 'string', 'max:100', Rule::unique('marches', 'reference')->ignore($this->editId)],
            'form.intitule' => 'required|string|max:255',
            'form.client_id' => 'required|exists:clients,id',
            'form.domaine' => ['required', Rule::enum(Domaine::class)],
            'form.statut' => ['required', Rule::enum(StatutMarche::class)],
            'form.localisation' => 'nullable|string|max:255',
            'form.montant_ht' => 'required|numeric|min:0',
            'form.taux_tva' => 'required|numeric|between:0,100',
            'form.taux_retenue_garantie' => 'required|numeric|between:0,100',
            'form.avance_demarrage' => 'nullable|numeric|min:0',
            'form.date_soumission' => 'nullable|date',
            'form.date_signature' => 'nullable|date',
            'form.date_ordre_service' => 'nullable|date',
            'form.delai_jours' => 'nullable|integer|min:1',
            'form.date_fin_prevue' => 'nullable|date',
            'form.date_reception' => 'nullable|date',
            'form.description' => 'nullable|string',
        ], [], ['form.reference' => 'référence', 'form.client_id' => 'client', 'form.montant_ht' => 'montant HT'])['form'];

        $data = array_map(fn ($v) => $v === '' ? null : $v, $data);
        $data['avance_demarrage'] ??= 0;
        // Date de fin calculée depuis l'ordre de service et le délai, si non saisie.
        if (! $data['date_fin_prevue'] && $data['date_ordre_service'] && $data['delai_jours']) {
            $data['date_fin_prevue'] = Carbon::parse($data['date_ordre_service'])->addDays((int) $data['delai_jours'])->toDateString();
        }

        $marche = Marche::updateOrCreate(['id' => $this->editId], $data);
        $this->modal = false;
        $this->succes('Marché enregistré.');
        if (! $this->editId) {
            $this->redirectRoute('marches.show', $marche, navigate: true);
        } elseif (property_exists($this, 'marche')) {
            $this->marche->refresh();
        }
    }
}
