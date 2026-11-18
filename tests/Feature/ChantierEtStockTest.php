<?php

use App\Enums\Role;
use App\Enums\StatutTache;
use App\Livewire\Chantiers\Journal;
use App\Livewire\Chantiers\Show;
use App\Models\Article;
use App\Models\Chantier;
use App\Services\StockService;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

it("calcule l'avancement pondéré par le coût prévu des tâches", function () {
    $c = Chantier::factory()->create();
    $c->taches()->create(['libelle' => 'A', 'date_debut_prevue' => now(), 'date_fin_prevue' => now()->addWeek(), 'cout_prevu' => 3_000_000, 'avancement' => 100]);
    $c->taches()->create(['libelle' => 'B', 'date_debut_prevue' => now(), 'date_fin_prevue' => now()->addWeek(), 'cout_prevu' => 1_000_000, 'avancement' => 0]);

    expect($c->fresh()->avancement)->toBe(75.0);
});

it('met la tâche à « terminée » quand elle atteint 100 %', function () {
    $c = Chantier::factory()->create();
    $t = $c->taches()->create(['libelle' => 'A', 'date_debut_prevue' => now(), 'date_fin_prevue' => now()->addWeek(), 'avancement' => 40]);
    expect($t->statut)->toBe(StatutTache::EnCours);

    $t->update(['avancement' => 100]);
    expect($t->fresh()->statut)->toBe(StatutTache::Termine)->and($t->fresh()->date_fin_reelle)->not->toBeNull();
});

it("met à jour l'avancement depuis le journal de chantier sans le faire reculer", function () {
    $chef = utilisateur(Role::ChefChantier);
    $c = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    $t = $c->taches()->create(['libelle' => 'Fondations', 'date_debut_prevue' => now(), 'date_fin_prevue' => now()->addWeek(), 'avancement' => 30]);

    Livewire::actingAs($chef)->test(Journal::class, ['chantier' => $c])
        ->set('lignes', [['type' => 'travaux', 'designation' => 'Coulage semelles', 'quantite' => null, 'unite' => null, 'tache_id' => $t->id, 'avancement' => 60]])
        ->call('enregistrer')->assertHasNoErrors();
    expect($t->fresh()->avancement)->toBe(60);

    Livewire::actingAs($chef)->test(Journal::class, ['chantier' => $c])
        ->set('date_rapport', now()->subDay()->toDateString())
        ->set('lignes', [['type' => 'travaux', 'designation' => 'Reprise', 'quantite' => null, 'unite' => null, 'tache_id' => $t->id, 'avancement' => 20]])
        ->call('enregistrer')->assertHasNoErrors();
    expect($t->fresh()->avancement)->toBe(60);
});

it('empêche deux rapports journaliers le même jour', function () {
    $chef = utilisateur(Role::ChefChantier);
    $c = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    $c->rapports()->create(['date_rapport' => now()->toDateString()]);

    Livewire::actingAs($chef)->test(Journal::class, ['chantier' => $c])->call('enregistrer')->assertHasErrors('date_rapport');
});

it("permet au chef de chantier d'enregistrer l'avancement de ses tâches seulement", function () {
    $chef = utilisateur(Role::ChefChantier);
    $c = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    $t = $c->taches()->create(['libelle' => 'A', 'date_debut_prevue' => now(), 'date_fin_prevue' => now()->addWeek()]);

    Livewire::actingAs($chef)->test(Show::class, ['chantier' => $c])
        ->set("avancements.{$t->id}", 45)->call('enregistrerAvancements')->assertHasNoErrors();
    expect($t->fresh()->avancement)->toBe(45);

    Livewire::actingAs(utilisateur(Role::Comptable))->test(Show::class, ['chantier' => $c])
        ->call('enregistrerAvancements')->assertForbidden();
});

it('valorise le stock au coût moyen pondéré et impute les sorties au chantier', function () {
    $stock = app(StockService::class);
    $a = Article::create(['code' => 'C1', 'designation' => 'Ciment', 'unite' => 'sac']);
    $c = Chantier::factory()->create();

    $stock->entree($a, 100, 6_000, ['date_mouvement' => now()]);
    $stock->entree($a, 100, 7_000, ['date_mouvement' => now()]);
    expect((float) $a->fresh()->cout_moyen)->toBe(6_500.0);

    $stock->sortie($a, 40, $c->id, ['date_mouvement' => now()]);
    expect((float) $a->fresh()->stock_actuel)->toBe(160.0)
        ->and($c->totalMateriauxConsommes())->toBe(260_000.0)
        ->and($c->coutTotal())->toBe(260_000.0);
});

it('refuse une sortie supérieure au stock', function () {
    $a = Article::create(['code' => 'C1', 'designation' => 'Ciment', 'unite' => 'sac']);
    app(StockService::class)->sortie($a, 5, Chantier::factory()->create()->id, ['date_mouvement' => now()]);
})->throws(ValidationException::class);
