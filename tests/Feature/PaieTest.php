<?php

use App\Enums\PorteeDepense;
use App\Enums\Role;
use App\Enums\StatutDepense;
use App\Enums\TypeLignePaie;
use App\Models\Avance;
use App\Models\Chantier;
use App\Models\Conge;
use App\Models\Depense;
use App\Models\Employe;
use App\Models\Pointage;
use App\Services\PaieService;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Carbon::setTestNow('2026-10-31 12:00:00');
    $this->paie = app(PaieService::class);
    $this->periode = $this->paie->ouvrirPeriode('2026-10');
});

afterEach(fn () => Carbon::setTestNow());

it('calcule le bulletin d\'un mensuel : absence non payée, heures sup, prime, avance', function () {
    $e = Employe::factory()->create(['salaire_base' => 300_000, 'date_embauche' => '2025-01-01']);
    // 2 jours absents dont 1 couvert par un congé maladie approuvé → 1 jour retenu
    Pointage::create(['employe_id' => $e->id, 'date_pointage' => '2026-10-05', 'present' => false]);
    Pointage::create(['employe_id' => $e->id, 'date_pointage' => '2026-10-06', 'present' => false]);
    Pointage::create(['employe_id' => $e->id, 'date_pointage' => '2026-10-07', 'present' => true, 'heures_sup' => 4]);
    Conge::create(['employe_id' => $e->id, 'type' => 'maladie', 'date_debut' => '2026-10-06', 'date_fin' => '2026-10-06', 'statut' => 'approuve']);
    Avance::create(['employe_id' => $e->id, 'date_avance' => '2026-10-15', 'montant' => 50_000, 'periode_retenue' => '2026-10']);

    $this->paie->generer($this->periode);
    $b = $e->bulletins()->first();
    $this->paie->ajouterLigne($b, TypeLignePaie::Prime, 'Prime de rendement', 20_000);
    $b->refresh();

    // 300 000 / 30 × 29 = 290 000 ; HS = 4 × (300 000 / 173,33) × 1,25 = 8 654
    expect((float) $b->jours_absence)->toBe(1.0)
        ->and((float) $b->salaire_du)->toBe(290_000.0)
        ->and((float) $b->montant_heures_sup)->toBe(round(4 * 300_000 / 173.33 * 1.25))
        ->and((float) $b->salaire_brut)->toBe(290_000 + round(4 * 300_000 / 173.33 * 1.25) + 20_000)
        ->and((float) $b->total_avances)->toBe(50_000.0)
        ->and((float) $b->net_a_payer)->toBe((float) $b->salaire_brut - 50_000);
});

it('paie un journalier au nombre de jours pointés présents', function () {
    $e = Employe::factory()->journalier(4_000)->create();
    foreach (['2026-10-01' => true, '2026-10-02' => true, '2026-10-03' => false, '2026-10-04' => true] as $jour => $present) {
        Pointage::create(['employe_id' => $e->id, 'date_pointage' => $jour, 'present' => $present]);
    }

    $this->paie->generer($this->periode);

    expect((float) $e->bulletins()->first()->net_a_payer)->toBe(12_000.0);
});

it('conserve les primes manuelles lors d\'un recalcul', function () {
    $e = Employe::factory()->create(['salaire_base' => 150_000]);
    $this->paie->generer($this->periode);
    $this->paie->ajouterLigne($e->bulletins()->first(), TypeLignePaie::Indemnite, 'Transport', 10_000);

    $this->paie->generer($this->periode);

    expect((float) $e->bulletins()->first()->salaire_brut)->toBe(160_000.0);
});

it('enregistre la masse salariale en dépenses à la clôture puis verrouille la période', function () {
    $chantier = Chantier::factory()->create();
    Employe::factory()->create(['salaire_base' => 200_000, 'chantier_id' => $chantier->id]);
    Employe::factory()->create(['salaire_base' => 500_000]); // siège

    $this->paie->generer($this->periode);
    $this->paie->cloturer($this->periode, utilisateur(Role::Comptable));

    $depenses = Depense::where('source_type', \App\Models\PeriodePaie::class)->get();
    expect($depenses)->toHaveCount(2)
        ->and($depenses->every(fn ($d) => $d->statut === StatutDepense::Validee))->toBeTrue()
        ->and((float) $depenses->firstWhere('portee', PorteeDepense::Chantier)->montant)->toBe(200_000.0)
        ->and((float) $depenses->firstWhere('portee', PorteeDepense::Interne)->montant)->toBe(500_000.0)
        ->and($chantier->totalDepensesValidees())->toBe(200_000.0);

    expect(fn () => $this->paie->generer($this->periode->fresh()))->toThrow(ValidationException::class);

    $this->paie->rouvrir($this->periode->fresh());
    expect(Depense::where('source_type', \App\Models\PeriodePaie::class)->count())->toBe(0);
});

it('refuse de clôturer avec un net négatif', function () {
    $e = Employe::factory()->create(['salaire_base' => 100_000]);
    Avance::create(['employe_id' => $e->id, 'date_avance' => '2026-10-10', 'montant' => 150_000, 'periode_retenue' => '2026-10']);
    $this->paie->generer($this->periode);

    $this->paie->cloturer($this->periode, utilisateur(Role::Comptable));
})->throws(ValidationException::class);
