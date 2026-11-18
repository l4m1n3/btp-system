<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

function utilisateur(Role|string $role = Role::Admin, array $attributs = []): User
{
    return User::factory()->role($role)->create($attributs);
}
