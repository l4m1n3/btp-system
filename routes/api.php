<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// /api/depenses
Route::apiResource('depenses', App\Http\Controllers\DepenseController::class);
// /api/rapports/generate
Route::post('rapports/generate', [App\Http\Controllers\RapportController::class, 'generate']);
// /api/chantiers
Route::apiResource('chantiers', App\Http\Controllers\ChantierController::class);