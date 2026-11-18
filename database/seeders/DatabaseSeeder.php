<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Données minimales : catégories de dépenses + compte administrateur.
     * Données de démonstration : php artisan db:seed --class=DemoSeeder
     */
    public function run(): void
    {
        $this->call([ReferentielSeeder::class, AdminSeeder::class]);
    }
}
