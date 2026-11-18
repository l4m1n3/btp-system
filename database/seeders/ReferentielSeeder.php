<?php

namespace Database\Seeders;

use App\Enums\PorteeDepense;
use App\Models\CategorieDepense;
use Illuminate\Database\Seeder;

class ReferentielSeeder extends Seeder
{
    public const CATEGORIES = [
        'chantier' => [
            'Matériaux de construction', 'Main-d\'œuvre temporaire (tâcherons)', 'Location d\'engins', 'Carburant et lubrifiants',
            'Transport et manutention', 'Sous-traitance', 'Petit outillage et consommables', 'Frais de chantier (eau, électricité, gardiennage)',
            'Entretien et réparation matériel', 'Essais et contrôles (laboratoire)', 'Divers chantier',
        ],
        'interne' => [
            'Loyer et charges du siège', 'Électricité, eau et internet', 'Fournitures de bureau', 'Carburant véhicules de service',
            'Frais bancaires', 'Impôts et taxes', 'Frais de soumission et cautions', 'Communication et téléphonie',
            'Achats matériaux pour stock', 'Entretien et réparation matériel', 'Missions et déplacements', 'Divers frais généraux',
        ],
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $portee => $noms) {
            foreach ($noms as $nom) {
                CategorieDepense::firstOrCreate(['nom' => $nom, 'portee' => $portee]);
            }
            CategorieDepense::salaires(PorteeDepense::from($portee));
        }
    }
}
