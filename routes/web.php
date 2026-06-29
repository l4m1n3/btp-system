<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatronController;
use App\Models\Patron;
use App\Http\Controllers\ChantierController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\FicheJournaliereController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logoute');

Route::get('/admin/dashboard', [PatronController::class, 'dashboardAdmin'])->middleware('auth')->name('admin.dashboard');
Route::resource('patrons', PatronController::class)->middleware('auth');
Route::get('/admin/entreprise', [PatronController::class, 'companies'])->middleware('auth')->name('admin.entreprise');
Route::post('/admin/entreprise', [PatronController::class, 'store'])->middleware('auth')->name('admin.entreprise.store');
Route::get('/admin/entreprise/details/{id}', [PatronController::class, 'details'])->middleware('auth')->name('admin.entreprise.details');
Route::post('/admin/entreprise/details/{id}/chantier', [ChantierController::class, 'store'])->middleware('auth')->name('admin.chantier.store');
Route::get('/rapports/entreprise/{id}', [RapportController::class, 'details'])->middleware('auth')->name('rapport.entreprise.details');
//routes pour utilisateur simple
Route::get('/dashboard', [PatronController::class, 'dashboardClient'])->middleware('auth')->name('dashboard');
Route::resource('patrons', PatronController::class)->middleware('auth');
Route::get('/entreprise', [PatronController::class, 'companies'])->middleware('auth')->name('entreprise');
Route::post('/entreprise', [PatronController::class, 'store'])->middleware('auth')->name('entreprise.store');
Route::get('/entreprise/details/{id}', [PatronController::class, 'details'])->middleware('auth')->name('entreprise.details');
Route::post('/entreprise/details/{id}/chantier', [ChantierController::class, 'store'])->middleware('auth')->name('chantier.store');
Route::post('/devis/import', [DevisController::class, 'import'])
    ->name('devis.import');
Route::get('devis/{id}/formulaire', [DevisController::class, 'show'])->name('devis.form');
Route::post('devis/store', [DevisController::class, 'store'])->name('devis.store');
Route::get('/rapports/entreprise/{id}', [RapportController::class, 'depenseEntreprise'])->middleware('auth')->name('rapport.depenses.entreprise');
Route::get('/depenses/liste', [RapportController::class, 'depenseListe'])->middleware('auth')->name('rapport.depenses.liste');
Route::post('/depenses/index', [RapportController::class, 'depenseListe'])->middleware('auth')->name('depenses.search');
Route::post('/depenses/store', [RapportController::class, 'storeDepense'])->middleware('auth')->name('depenses.stores');
Route::get('/rapports/generate-hebdomadaire', [RapportController::class, 'generateHebdomadaire'])->middleware('auth')->name('rapport.generateHebdomadaire');
Route::get('/rapports/generate-mensuel', [RapportController::class, 'generateMensuel'])->middleware('auth')->name('rapport.generateMensuel');
Route::post('/rapports/generate', [RapportController::class, 'generateRapport'])->middleware('auth')->name('rapport.generate');
Route::get('/planning/{id}', [ChantierController::class, 'planning'])->middleware('auth')->name('planning.show');
Route::post('/planning/store', [ChantierController::class, 'storePlanning'])->middleware('auth')->name('planning.store');
Route::get('/planning/edit/{id}', [ChantierController::class, 'editPlanning'])->middleware('auth')->name('planning.edit');
Route::put('/planning/update/{id}', [ChantierController::class, 'updatePlanning'])->middleware('auth')->name('planning.update');
Route::post('/planning/delete/{id}', [ChantierController::class, 'deletePlanning'])->middleware('auth')->name('planning.destroy');
Route::prefix('chantiers/{chantier}/fiches')->name('fiches.')->group(function () {
    Route::get('/',         [FicheJournaliereController::class, 'index'])->name('index');
    Route::get('/create',   [FicheJournaliereController::class, 'create'])->name('create');
    Route::post('/',        [FicheJournaliereController::class, 'store'])->name('store');
    Route::get('/{fiche}',  [FicheJournaliereController::class, 'show'])->name('show');
    Route::get('/{fiche}/edit', [FicheJournaliereController::class, 'edit'])->name('edit');
    Route::put('/{fiche}',  [FicheJournaliereController::class, 'update'])->name('update');
    Route::delete('/{fiche}', [FicheJournaliereController::class, 'destroy'])->name('destroy');
});
Route::get('/chantiers/{id}/details', [ChantierController::class, 'details'])->middleware('auth')->name('chantiers.details');
