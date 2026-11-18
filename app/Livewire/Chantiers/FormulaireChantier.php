<?php

namespace App\Livewire\Chantiers;

use App\Enums\Role;
use App\Enums\StatutChantier;
use App\Models\Chantier;
use App\Models\Marche;
use App\Models\User;
use Illuminate\Validation\Rule;

/**
 * Formulaire de création / modification d'un chantier (liste et fiche).
 */
trait FormulaireChantier
{
    public bool $modal = false;

    public ?int $editId = null;

    public array $form = [];

    public function creer(?int $marcheId = null): void
    {
        $this->authorize('chantiers.manage');
        $this->resetValidation();
        $this->editId = null;
        $marche = $marcheId ? Marche::find($marcheId) : null;
        $this->form = [
            'code' => 'CH-'.now()->format('y').'-'.str_pad((string) (Chantier::max('id') + 1), 3, '0', STR_PAD_LEFT),
            'nom' => $marche?->intitule ?? '', 'marche_id' => $marche?->id ?? '', 'chef_chantier_id' => '',
            'localisation' => $marche?->localisation ?? '', 'budget' => '', 'statut' => StatutChantier::EnPreparation->value,
            'date_debut' => $marche?->date_ordre_service?->toDateString() ?? '', 'date_fin_prevue' => $marche?->date_fin_prevue?->toDateString() ?? '',
            'date_fin_reelle' => '', 'description' => '',
        ];
        $this->modal = true;
    }

    public function modifier(Chantier $chantier): void
    {
        $this->authorize('chantiers.manage');
        $this->resetValidation();
        $this->editId = $chantier->id;
        $this->form = collect($chantier->only($chantier->getFillable()))->map(fn ($v) => $v instanceof \BackedEnum ? $v->value : ($v instanceof \DateTimeInterface ? $v->format('Y-m-d') : ($v ?? '')))->all();
        $this->modal = true;
    }

    public function enregistrer(): void
    {
        $this->authorize('chantiers.manage');
        $data = $this->validate([
            'form.code' => ['required', 'string', 'max:50', Rule::unique('chantiers', 'code')->ignore($this->editId)],
            'form.nom' => 'required|string|max:255',
            'form.marche_id' => 'nullable|exists:marches,id',
            'form.chef_chantier_id' => ['nullable', Rule::exists('users', 'id')->where('role', Role::ChefChantier->value)],
            'form.localisation' => 'nullable|string|max:255',
            'form.budget' => 'required|numeric|min:0',
            'form.statut' => ['required', Rule::enum(StatutChantier::class)],
            'form.date_debut' => 'nullable|date',
            'form.date_fin_prevue' => 'nullable|date|after_or_equal:form.date_debut',
            'form.date_fin_reelle' => 'nullable|date',
            'form.description' => 'nullable|string',
        ], [], ['form.chef_chantier_id' => 'chef de chantier', 'form.date_fin_prevue' => 'fin prévue'])['form'];

        $data = array_map(fn ($v) => $v === '' ? null : $v, $data);
        if ($data['statut'] === StatutChantier::Termine->value) {
            $data['date_fin_reelle'] ??= now()->toDateString();
        }
        $chantier = Chantier::updateOrCreate(['id' => $this->editId], $data);
        $this->modal = false;
        $this->succes('Chantier enregistré.');
        if (! $this->editId) {
            $this->redirectRoute('chantiers.show', $chantier, navigate: true);
        } elseif (property_exists($this, 'chantier')) {
            $this->chantier->refresh();
        }
    }

    protected function listesFormulaire(): array
    {
        return [
            'marches' => Marche::orderByDesc('id')->pluck('reference', 'id'),
            'chefs' => User::where('role', Role::ChefChantier)->where('actif', true)->orderBy('name')->pluck('name', 'id'),
        ];
    }
}
