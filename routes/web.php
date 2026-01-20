<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatronController;
use App\Models\Patron;
use App\Http\Controllers\ChantierController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\RapportController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logoute');

Route::get('/admin/dashboard',[PatronController::class, 'dashboardAdmin'])->middleware('auth')->name('admin.dashboard');
Route::resource('patrons', PatronController::class)->middleware('auth');
Route::get('/admin/entreprise', [PatronController::class, 'companies'])->middleware('auth')->name('admin.entreprise');
Route::post('/admin/entreprise', [PatronController::class, 'store'])->middleware('auth')->name('admin.entreprise.store');
Route::get('/admin/entreprise/details/{id}', [PatronController::class, 'details'])->middleware('auth')->name('admin.entreprise.details');
Route::post('/admin/entreprise/details/{id}/chantier', [ChantierController::class, 'store'])->middleware('auth')->name('admin.chantier.store');
Route::get('/rapports/entreprise/{id}', [RapportController::class, 'details'])->middleware('auth')->name('rapport.entreprise.details');
//routes pour utilisateur simple
Route::get('/dashboard',[PatronController::class, 'dashboardClient'])->middleware('auth')->name('dashboard');
Route::resource('patrons', PatronController::class)->middleware('auth');
Route::get('/entreprise', [PatronController::class, 'companies'])->middleware('auth')->name('entreprise');
Route::post('/entreprise', [PatronController::class, 'store'])->middleware('auth')->name('entreprise.store');
Route::get('/entreprise/details/{id}', [PatronController::class, 'details'])->middleware('auth')->name('entreprise.details');
Route::post('/entreprise/details/{id}/chantier', [ChantierController::class, 'store'])->middleware('auth')->name('chantier.store');
Route::post('/devis/import', [DevisController::class, 'import'])
    ->name('devis.import');
Route::get('devis/{id}/formulaire',[DevisController::class,'show'])->name('devis.form');
Route::post('devis/store',[DevisController::class,'store'])->name('devis.store');
Route::get('/rapports/entreprise/{id}', [RapportController::class, 'depenseEntreprise'])->middleware('auth')->name('rapport.depenses.entreprise');
Route::get('/depenses/liste', [RapportController::class, 'depenseListe'])->middleware('auth')->name('rapport.depenses.liste');
Route::post('/depenses/index', [RapportController::class, 'depenseListe'])->middleware('auth')->name('depenses.search');
Route::get('/rapports/generate-hebdomadaire', [RapportController::class, 'generateHebdomadaire'])->middleware('auth')->name('rapport.generateHebdomadaire');