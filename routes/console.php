<?php

use App\Services\RapportHebdomadaireService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('rapport:hebdomadaire', function (RapportHebdomadaireService $service) {
    $n = $service->envoyer();
    $this->info("Rapport envoyé à $n destinataire(s).");
})->purpose('Envoie le rapport de la semaine écoulée à la direction');

// Chaque lundi à 7 h (heure de Niamey) : rapport de la semaine précédente.
Schedule::command('rapport:hebdomadaire')
    ->weeklyOn(1, '07:00')
    ->timezone('Africa/Niamey')
    ->withoutOverlapping()
    ->onOneServer();
