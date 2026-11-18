<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Livewire;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', Livewire\Dashboard::class)->name('dashboard');
    Route::get('/profil', Livewire\Profil::class)->name('profil');

    // Marchés et clients
    Route::get('/marches', Livewire\Marches\Index::class)->name('marches.index');
    Route::get('/marches/{marche}', Livewire\Marches\Show::class)->name('marches.show');
    Route::get('/clients', Livewire\Clients\Index::class)->name('clients.index');

    // Chantiers
    Route::get('/chantiers', Livewire\Chantiers\Index::class)->name('chantiers.index');
    Route::get('/chantiers/{chantier}', Livewire\Chantiers\Show::class)->name('chantiers.show');
    Route::get('/chantiers/{chantier}/journal/{rapport?}', Livewire\Chantiers\Journal::class)->name('chantiers.journal');

    // Dépenses
    Route::get('/depenses', Livewire\Depenses\Index::class)->name('depenses.index');
    Route::get('/depenses/{depense}/justificatif', [DocumentController::class, 'justificatif'])->name('depenses.justificatif');
    Route::get('/parametres/depenses', Livewire\Parametres\Depenses::class)->name('parametres.depenses');

    // Logistique
    Route::get('/materiels', Livewire\Logistique\Materiels::class)->name('materiels.index');
    Route::get('/stock', Livewire\Logistique\Stock::class)->name('stock.index');

    // RH et paie
    Route::get('/employes', Livewire\Rh\Employes::class)->name('employes.index');
    Route::get('/employes/{employe}', Livewire\Rh\EmployeShow::class)->name('employes.show');
    Route::get('/pointage', Livewire\Rh\Pointage::class)->name('pointage.index');
    Route::get('/conges', Livewire\Rh\Conges::class)->name('conges.index');
    Route::get('/avances', Livewire\Rh\Avances::class)->name('avances.index');
    Route::get('/paie', Livewire\Paie\Index::class)->name('paie.index');
    Route::get('/paie/{periode}', Livewire\Paie\Periode::class)->name('paie.show');

    // Rapports, documents et exports
    Route::get('/rapports', Livewire\Rapports\Index::class)->name('rapports.index');
    Route::prefix('documents')->group(function () {
        Route::get('/bulletins/{bulletin}', [DocumentController::class, 'bulletin'])->name('pdf.bulletin');
        Route::get('/paie/{periode}/journal', [DocumentController::class, 'journalPaie'])->name('pdf.journal-paie');
        Route::get('/chantiers/{chantier}/situation', [DocumentController::class, 'chantier'])->name('pdf.chantier');
        Route::get('/depenses.pdf', [DocumentController::class, 'depenses'])->name('pdf.depenses');
        Route::get('/depenses.xlsx', [DocumentController::class, 'exportDepenses'])->name('export.depenses');
    });

    // Administration
    Route::get('/utilisateurs', Livewire\Admin\Utilisateurs::class)->name('utilisateurs.index');
});
