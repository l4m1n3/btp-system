<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'matricule' => 'EMP-'.fake()->unique()->numerify('#####'),
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'fonction' => 'Maçon',
            'type_contrat' => 'cdi',
            'date_embauche' => now()->subYear(),
            'mode_remuneration' => 'mensuel',
            'salaire_base' => 150_000,
            'taux_journalier' => 0,
            'statut' => 'actif',
        ];
    }

    public function journalier(float $taux = 4000): static
    {
        return $this->state(fn () => ['type_contrat' => 'journalier', 'mode_remuneration' => 'journalier', 'salaire_base' => 0, 'taux_journalier' => $taux]);
    }
}
