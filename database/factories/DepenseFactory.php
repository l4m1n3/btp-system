<?php

namespace Database\Factories;

use App\Models\CategorieDepense;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'portee' => 'interne',
            'categorie_id' => fn (array $a) => CategorieDepense::firstOrCreate(['nom' => 'Divers', 'portee' => $a['portee']])->id,
            'date_depense' => now()->toDateString(),
            'libelle' => fake()->sentence(3),
            'montant' => 10_000,
            'mode_paiement' => 'especes',
            'statut' => 'validee',
        ];
    }
}
