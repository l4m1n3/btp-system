<?php

use App\Enums\Role;
use App\Models\CategorieDepense;
use App\Models\Chantier;
use App\Models\Employe;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

it('exige un jeton', function () {
    $this->getJson('/api/chantiers')->assertUnauthorized();
    $this->postJson('/api/depenses')->assertUnauthorized();
});

it('délivre un jeton et les droits de l\'utilisateur', function () {
    utilisateur(Role::ChefChantier, ['email' => 'chef@test.local']);

    $this->postJson('/api/login', ['email' => 'chef@test.local', 'password' => 'mauvais'])->assertUnprocessable();
    $reponse = $this->postJson('/api/login', ['email' => 'chef@test.local', 'password' => 'password', 'appareil' => 'tel'])
        ->assertOk()->assertJsonStructure(['token', 'utilisateur' => ['role', 'droits']]);

    expect($reponse->json('utilisateur.droits'))->toContain('depenses.create')->not->toContain('depenses.valider');
    $this->withToken($reponse->json('token'))->getJson('/api/me')->assertOk()->assertJsonPath('email', 'chef@test.local');
});

it('ne liste au chef que ses chantiers et bloque les autres', function () {
    $chef = utilisateur(Role::ChefChantier);
    $sien = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    $autre = Chantier::factory()->create();
    Sanctum::actingAs($chef);

    $this->getJson('/api/chantiers')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $sien->id);
    $this->getJson('/api/chantiers/'.$autre->id)->assertForbidden();
});

it('enregistre une dépense avec photo du justificatif', function () {
    Storage::fake('local');
    $chef = utilisateur(Role::ChefChantier);
    $chantier = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    Sanctum::actingAs($chef);

    $this->post('/api/depenses', [
        'portee' => 'chantier', 'chantier_id' => $chantier->id,
        'categorie_id' => CategorieDepense::where('portee', 'chantier')->where('systeme', false)->value('id'),
        'date_depense' => now()->toDateString(), 'libelle' => 'Gasoil', 'montant' => 45000, 'mode_paiement' => 'especes',
        'justificatif' => UploadedFile::fake()->image('recu.jpg'),
    ], ['Accept' => 'application/json'])->assertCreated()->assertJsonPath('data.statut', 'en_attente')->assertJsonPath('data.a_justificatif', true);
});

it("enregistre le pointage et refuse un employé d'un autre chantier", function () {
    $chef = utilisateur(Role::ChefChantier);
    $chantier = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    $employe = Employe::factory()->create(['chantier_id' => $chantier->id]);
    $etranger = Employe::factory()->create(['chantier_id' => Chantier::factory()->create()->id]);
    Sanctum::actingAs($chef);

    $this->postJson("/api/chantiers/{$chantier->id}/pointage", ['date' => now()->toDateString(), 'pointages' => [['employe_id' => $employe->id, 'present' => true, 'heures_sup' => 2]]])->assertOk();
    $this->postJson("/api/chantiers/{$chantier->id}/pointage", ['date' => now()->toDateString(), 'pointages' => [['employe_id' => $etranger->id, 'present' => true]]])->assertUnprocessable();

    $this->getJson("/api/chantiers/{$chantier->id}/pointage")->assertOk()->assertJsonPath('data.0.heures_sup', 2);
});

it("met à jour l'avancement d'une tâche", function () {
    $chef = utilisateur(Role::ChefChantier);
    $chantier = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    $tache = $chantier->taches()->create(['libelle' => 'A', 'date_debut_prevue' => now(), 'date_fin_prevue' => now()->addWeek(), 'cout_prevu' => 1000]);
    Sanctum::actingAs($chef);

    $this->patchJson("/api/chantiers/{$chantier->id}/taches/{$tache->id}", ['avancement' => 50])->assertOk()->assertJsonPath('data.avancement', 50);
});
