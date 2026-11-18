<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /** Crée le premier administrateur (identifiants dans .env : ADMIN_EMAIL / ADMIN_PASSWORD). */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@entreprise.local');
        $password = env('ADMIN_PASSWORD', 'ChangezMoi2026!');

        User::firstOrCreate(['email' => $email], [
            'name' => 'Administrateur',
            'role' => Role::Admin,
            'actif' => true,
            'password' => $password,
        ]);

        $this->command?->info("Administrateur : $email (pensez à changer le mot de passe).");
    }
}
