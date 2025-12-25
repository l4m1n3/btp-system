<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\RapportService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::call(function (RapportService $rapportService) {
    $rapportService->envoyerRapportHebdomadaire();
})
    ->name('rapport.hebdomadaire')
    ->description('Envoi automatique du rapport récapitulatif de la semaine')
    ->weeklyOn(1, '08:00')           // ← lundi matin 8h
    // ->weekly()                    // ← lundi 00:00 par défaut (moins précis)
    // ->sundays()->at('23:45')      // ← autre option très courante
    ->timezone('Europe/Paris')
    ->withoutOverlapping(600)         // bloque 10 minutes si ça prend trop de temps
    ->onOneServer()                   // important si tu as plusieurs serveurs
    ->when(fn () => app()->environment('production')); // uniquement en prod