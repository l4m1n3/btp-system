<?php

namespace Tests;

use Database\Seeders\ReferentielSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** Catégories de dépenses disponibles dans chaque test. */
    protected bool $seed = true;

    protected string $seeder = ReferentielSeeder::class;
}
