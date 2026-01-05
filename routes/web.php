<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatronController;
use App\Models\Patron;
use App\Http\Controllers\ChantierController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard',[PatronController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::resource('patrons', PatronController::class)->middleware('auth');
Route::get('/entreprise', [PatronController::class, 'companies'])->middleware('auth')->name('entreprise');
Route::post('/entreprise', [PatronController::class, 'store'])->middleware('auth')->name('entreprise.store');
Route::get('/entreprise/details/{id}', [PatronController::class, 'details'])->middleware('auth')->name('entreprise.details');
Route::post('/entreprise/details/{id}/chantier', [ChantierController::class, 'store'])->middleware('auth')->name('chantier.store');
Route::get('/rapports/entreprise/{id}', [App\Http\Controllers\RapportController::class, 'details'])->middleware('auth')->name('rapport.entreprise.details');