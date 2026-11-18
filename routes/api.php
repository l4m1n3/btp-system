<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

/*
| API mobile (Sanctum). Toutes les routes, sauf /login, exigent un jeton :
| Authorization: Bearer <token>   ·   Accept: application/json
*/

Route::post('/login', [Api\AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    Route::get('/me', [Api\AuthController::class, 'me']);
    Route::post('/logout', [Api\AuthController::class, 'logout']);

    Route::get('/references', [Api\DepenseController::class, 'references']);

    Route::get('/chantiers', [Api\ChantierController::class, 'index']);
    Route::get('/chantiers/{chantier}', [Api\ChantierController::class, 'show']);
    Route::patch('/chantiers/{chantier}/taches/{tache}', [Api\ChantierController::class, 'avancement']);

    Route::get('/chantiers/{chantier}/journal', [Api\JournalController::class, 'index']);
    Route::post('/chantiers/{chantier}/journal', [Api\JournalController::class, 'store']);

    Route::get('/chantiers/{chantier}/pointage', [Api\PointageController::class, 'index']);
    Route::post('/chantiers/{chantier}/pointage', [Api\PointageController::class, 'store']);

    Route::get('/articles', [Api\StockController::class, 'articles']);
    Route::post('/chantiers/{chantier}/sorties-stock', [Api\StockController::class, 'sortie']);

    Route::get('/depenses', [Api\DepenseController::class, 'index']);
    Route::post('/depenses', [Api\DepenseController::class, 'store']);
});
