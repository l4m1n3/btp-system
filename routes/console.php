<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\RapportService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
// 📅 Rapport quotidien à 18h
Schedule::call(function (RapportService $rapportService) {
    $rapportService->envoyerRapportQuotidien();
})
    ->dailyAt('18:00')
    ->timezone('Europe/Paris')
    ->name('rapport-quotidien')
    ->withoutOverlapping(600)
    ->onOneServer()
    ->when(fn() => app()->environment('production'));


// 📅 Rapport hebdomadaire chaque lundi à 18h
Schedule::call(function (RapportService $rapportService) {
    $rapportService->envoyerRapportHebdomadaire();
})
    ->weeklyOn(1, '18:00') // 1 = lundi
    ->timezone('Europe/Paris')
    ->name('rapport-hebdomadaire')
    ->withoutOverlapping(600)
    ->onOneServer()
    ->when(fn() => app()->environment('production'));

// 📅 Rapport mensuel chaque 1er du mois à 18h
Schedule::call(function (RapportService $rapportService) {
    $rapportService->envoyerRapportMensuel();
})
    ->monthlyOn(1, '18:00')
    ->timezone('Europe/Paris')
    ->name('rapport-mensuel')
    ->withoutOverlapping(600)
    ->onOneServer()
    ->when(fn() => app()->environment('production'));

// 📅 Rapport annuel chaque 1er janvier à 18h
Schedule::call(function (RapportService $rapportService) {
    $rapportService->envoyerRapportAnnuel();
})
    ->yearlyOn(1, 1, '18:00') // 1er janvier
    ->timezone('Europe/Paris')
    ->name('rapport-annuel')
    ->withoutOverlapping(600)
    ->onOneServer()
    ->when(fn() => app()->environment('production'));
