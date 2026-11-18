<?php

use App\Enums\Role;
use App\Models\Bulletin;
use App\Models\Chantier;
use App\Models\Employe;
use App\Models\Marche;
use App\Models\PeriodePaie;
use App\Models\RapportJournalier;
use Database\Seeders\DemoSeeder;

it('redirige les visiteurs non connectés vers la connexion', function () {
    $this->get('/')->assertRedirect('/login');
    $this->get('/depenses')->assertRedirect('/login');
});

it('connecte un utilisateur actif et refuse un compte désactivé', function () {
    $actif = utilisateur(Role::Comptable, ['email' => 'c@test.local']);
    utilisateur(Role::Comptable, ['email' => 'off@test.local', 'actif' => false]);

    $this->post('/login', ['email' => 'off@test.local', 'password' => 'password'])->assertSessionHasErrors('email');
    $this->assertGuest();

    $this->post('/login', ['email' => 'c@test.local', 'password' => 'password'])->assertRedirect('/');
    $this->assertAuthenticatedAs($actif);
});

it('affiche toutes les pages sans erreur pour un administrateur (données de démo)', function () {
    $this->seed(DemoSeeder::class);
    $admin = utilisateur(Role::Admin);
    $chantier = Chantier::where('code', 'CH-26-001')->first();

    $pages = [
        '/', '/profil', '/marches', '/marches/'.Marche::first()->id, '/clients',
        '/chantiers', '/chantiers/'.$chantier->id,
        '/chantiers/'.$chantier->id.'/journal', '/chantiers/'.$chantier->id.'/journal/'.RapportJournalier::first()->id,
        '/depenses', '/depenses?statut=en_attente', '/parametres/depenses',
        '/materiels', '/stock', '/employes', '/employes/'.Employe::first()->id,
        '/pointage', '/conges', '/avances', '/paie', '/paie/'.PeriodePaie::first()->id,
        '/rapports', '/utilisateurs',
    ];
    foreach (['apercu', 'planning', 'depenses', 'journal', 'ressources', 'finances'] as $onglet) {
        $pages[] = '/chantiers/'.$chantier->id.'?onglet='.$onglet;
    }
    foreach ($pages as $url) {
        $this->actingAs($admin)->get($url)->assertOk();
    }

    // Documents PDF et export Excel
    $this->actingAs($admin)->get(route('pdf.bulletin', Bulletin::first()))->assertOk()->assertHeader('content-type', 'application/pdf');
    $this->actingAs($admin)->get(route('pdf.journal-paie', PeriodePaie::first()))->assertOk();
    $this->actingAs($admin)->get(route('pdf.chantier', $chantier))->assertOk();
    $this->actingAs($admin)->get(route('pdf.depenses', ['portee' => 'chantier']))->assertOk();
    $this->actingAs($admin)->get(route('export.depenses'))->assertOk()->assertDownload();
});

it('applique les droits par rôle', function (Role $role, array $autorise, array $interdit) {
    $user = utilisateur($role);
    foreach ($autorise as $url) {
        $this->actingAs($user)->get($url)->assertOk();
    }
    foreach ($interdit as $url) {
        $this->actingAs($user)->get($url)->assertForbidden();
    }
})->with([
    'direction' => [Role::Direction, ['/', '/marches', '/chantiers', '/depenses', '/rapports', '/paie'], ['/utilisateurs']],
    'comptable' => [Role::Comptable, ['/', '/marches', '/depenses', '/rapports', '/paie', '/parametres/depenses'], ['/employes', '/utilisateurs', '/materiels']],
    'chef de chantier' => [Role::ChefChantier, ['/', '/chantiers', '/depenses', '/pointage', '/stock'], ['/marches', '/rapports', '/employes', '/paie', '/utilisateurs']],
    'rh' => [Role::Rh, ['/', '/employes', '/pointage', '/conges', '/avances', '/paie'], ['/marches', '/depenses', '/chantiers', '/utilisateurs']],
    'logistique' => [Role::Logistique, ['/', '/materiels', '/stock', '/chantiers'], ['/depenses', '/employes', '/paie', '/marches']],
]);

it('limite le chef de chantier à ses propres chantiers', function () {
    $chef = utilisateur(Role::ChefChantier);
    $autreChef = utilisateur(Role::ChefChantier);
    $sien = Chantier::factory()->create(['chef_chantier_id' => $chef->id, 'nom' => 'Mon chantier']);
    $autre = Chantier::factory()->create(['chef_chantier_id' => $autreChef->id, 'nom' => 'Chantier voisin']);

    $this->actingAs($chef)->get('/chantiers/'.$sien->id)->assertOk();
    $this->actingAs($chef)->get('/chantiers/'.$autre->id)->assertForbidden();
    $this->actingAs($chef)->get('/chantiers')->assertSee('Mon chantier')->assertDontSee('Chantier voisin');
    $this->actingAs($chef)->get(route('pdf.chantier', $autre))->assertForbidden();
});

it('empêche un administrateur désactivé d\'agir', function () {
    $admin = utilisateur(Role::Admin, ['actif' => false]);
    $this->actingAs($admin)->get('/utilisateurs')->assertForbidden();
});

it('affiche les pages autorisées pour chaque rôle de démonstration', function () {
    $this->seed(DemoSeeder::class);
    $chantier = Chantier::where('code', 'CH-26-001')->first();
    $pages = ['/', '/profil', '/marches', '/clients', '/chantiers', '/chantiers/'.$chantier->id, '/chantiers/'.$chantier->id.'?onglet=finances',
        '/chantiers/'.$chantier->id.'?onglet=depenses', '/depenses', '/parametres/depenses', '/materiels', '/stock', '/employes', '/pointage',
        '/conges', '/avances', '/paie', '/paie/'.PeriodePaie::first()->id, '/rapports'];

    foreach (['direction', 'comptable', 'chef1', 'rh', 'logistique'] as $login) {
        $user = \App\Models\User::where('email', "$login@demo.local")->first();
        foreach ($pages as $url) {
            $statut = $this->actingAs($user)->get($url)->status();
            expect($statut)->toBeIn([200, 403], "$login $url → $statut");
        }
    }
});
