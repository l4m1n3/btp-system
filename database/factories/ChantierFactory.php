<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChantierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'CH-'.fake()->unique()->numerify('####'),
            'nom' => 'Chantier '.fake()->city(),
            'budget' => 50_000_000,
            'statut' => 'en_cours',
            'date_debut' => now()->subMonth(),
            'date_fin_prevue' => now()->addMonths(3),
        ];
    }
}
