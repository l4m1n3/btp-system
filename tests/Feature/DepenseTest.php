<?php

use App\Enums\Role;
use App\Enums\StatutDepense;
use App\Livewire\Depenses\Index;
use App\Models\CategorieDepense;
use App\Models\Chantier;
use App\Models\Depense;
use App\Services\DepenseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

function categorie(string $portee = 'chantier'): CategorieDepense
{
    return CategorieDepense::where('portee', $portee)->where('systeme', false)->first();
}

function donneesDepense(array $surcharge = []): array
{
    return array_merge([
        'portee' => 'chantier', 'categorie_id' => categorie()->id, 'date_depense' => now()->toDateString(),
        'libelle' => 'Achat ciment', 'montant' => 150000, 'mode_paiement' => 'especes',
    ], $surcharge);
}

it("soumet la dépense d'un chef de chantier à validation", function () {
    $chef = utilisateur(Role::ChefChantier);
    $chantier = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);

    $depense = app(DepenseService::class)->enregistrer(donneesDepense(['chantier_id' => $chantier->id]), $chef);

    expect($depense->statut)->toBe(StatutDepense::EnAttente)
        ->and($chantier->totalDepensesValidees())->toBe(0.0);
});

it('valide automatiquement la dépense saisie par le comptable', function () {
    $comptable = utilisateur(Role::Comptable);
    $chantier = Chantier::factory()->create();

    $depense = app(DepenseService::class)->enregistrer(donneesDepense(['chantier_id' => $chantier->id]), $comptable);

    expect($depense->statut)->toBe(StatutDepense::Validee)
        ->and($depense->valide_par)->toBe($comptable->id)
        ->and($chantier->totalDepensesValidees())->toBe(150000.0);
});

it("refuse une dépense sur le chantier d'un autre chef", function () {
    $chef = utilisateur(Role::ChefChantier);
    $autre = Chantier::factory()->create(['chef_chantier_id' => utilisateur(Role::ChefChantier)->id]);

    app(DepenseService::class)->enregistrer(donneesDepense(['chantier_id' => $autre->id]), $chef);
})->throws(ValidationException::class);

it('réserve les dépenses internes aux rôles autorisés', function () {
    $chef = utilisateur(Role::ChefChantier);
    app(DepenseService::class)->enregistrer(donneesDepense(['portee' => 'interne', 'categorie_id' => categorie('interne')->id]), $chef);
})->throws(ValidationException::class);

it('refuse une catégorie qui ne correspond pas au type de dépense', function () {
    $comptable = utilisateur(Role::Comptable);
    app(DepenseService::class)->enregistrer(donneesDepense(['portee' => 'interne', 'categorie_id' => categorie('chantier')->id]), $comptable);
})->throws(ValidationException::class);

it('permet au comptable de valider ou rejeter depuis la liste', function () {
    $chef = utilisateur(Role::ChefChantier);
    $chantier = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    $service = app(DepenseService::class);
    $a = $service->enregistrer(donneesDepense(['chantier_id' => $chantier->id]), $chef);
    $b = $service->enregistrer(donneesDepense(['chantier_id' => $chantier->id, 'montant' => 50000]), $chef);

    Livewire::actingAs(utilisateur(Role::Comptable))->test(Index::class)
        ->call('valider', $a->id)
        ->call('demanderRejet', $b->id)->set('motif', 'Pièce illisible')->call('rejeter');

    expect($a->fresh()->statut)->toBe(StatutDepense::Validee)
        ->and($b->fresh()->statut)->toBe(StatutDepense::Rejetee)
        ->and($b->fresh()->motif_rejet)->toBe('Pièce illisible');
});

it("n'autorise pas un chef de chantier à valider", function () {
    $chef = utilisateur(Role::ChefChantier);
    $chantier = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    $d = app(DepenseService::class)->enregistrer(donneesDepense(['chantier_id' => $chantier->id]), $chef);

    Livewire::actingAs($chef)->test(Index::class)->call('valider', $d->id)->assertForbidden();
});

it('crée une dépense avec justificatif privé via le formulaire', function () {
    Storage::fake('local');
    $chef = utilisateur(Role::ChefChantier);
    $chantier = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);

    Livewire::actingAs($chef)->test(Index::class)
        ->call('creer')
        ->set('form.chantier_id', $chantier->id)
        ->set('form.categorie_id', categorie()->id)
        ->set('form.libelle', 'Gasoil groupe')
        ->set('form.montant', 85000)
        ->set('justificatif', UploadedFile::fake()->image('recu.jpg'))
        ->call('enregistrer')
        ->assertHasNoErrors();

    $depense = Depense::firstOrFail();
    Storage::disk('local')->assertExists($depense->justificatif);
    $this->actingAs($chef)->get(route('depenses.justificatif', $depense))->assertOk();
    $this->actingAs(utilisateur(Role::ChefChantier))->get(route('depenses.justificatif', $depense))->assertNotFound();
});

it('ne laisse pas modifier une dépense validée', function () {
    $comptable = utilisateur(Role::Comptable);
    $d = Depense::factory()->create(['statut' => 'validee', 'saisi_par' => $comptable->id]);

    Livewire::actingAs($comptable)->test(Index::class)->call('modifier', $d->id)->assertForbidden();
});

it('ne montre au chef de chantier que les dépenses de ses chantiers', function () {
    $chef = utilisateur(Role::ChefChantier);
    $sien = Chantier::factory()->create(['chef_chantier_id' => $chef->id]);
    Depense::factory()->create(['portee' => 'chantier', 'chantier_id' => $sien->id, 'libelle' => 'Visible']);
    Depense::factory()->create(['portee' => 'chantier', 'chantier_id' => Chantier::factory()->create()->id, 'libelle' => 'Cachee']);
    Depense::factory()->create(['portee' => 'interne', 'libelle' => 'Interne']);

    expect(Depense::visiblePour($chef)->pluck('libelle')->all())->toBe(['Visible']);
});
