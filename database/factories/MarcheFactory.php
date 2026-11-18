<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarcheFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference' => 'M-'.fake()->unique()->numerify('####'),
            'intitule' => 'Travaux '.fake()->words(3, true),
            'client_id' => Client::factory(),
            'domaine' => 'batiment',
            'statut' => 'en_cours',
            'montant_ht' => 100_000_000,
            'taux_tva' => 19,
        ];
    }
}
