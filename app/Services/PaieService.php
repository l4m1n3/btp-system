<?php

namespace App\Services;

use App\Enums\ModeRemuneration;
use App\Enums\PorteeDepense;
use App\Enums\StatutConge;
use App\Enums\StatutDepense;
use App\Enums\StatutEmploye;
use App\Enums\StatutPeriodePaie;
use App\Enums\TypeConge;
use App\Enums\TypeLignePaie;
use App\Models\Avance;
use App\Models\Bulletin;
use App\Models\CategorieDepense;
use App\Models\Chantier;
use App\Models\Depense;
use App\Models\Employe;
use App\Models\PeriodePaie;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Paie simple :
 *  - mensuel   : salaire dû = base − (base / 30) × jours d'absence non payés
 *  - journalier: salaire dû = jours pointés présents × taux journalier
 *  - heures sup: heures × taux horaire × majoration (config/btp.php)
 *  - brut = salaire dû + heures sup + primes/indemnités
 *  - net  = brut − avances du mois − retenues
 * À la clôture, la masse salariale (brut) est enregistrée en dépenses
 * validées : par chantier d'imputation, le reste en dépense interne.
 */
class PaieService
{
    public function ouvrirPeriode(string $mois): PeriodePaie
    {
        if (! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mois)) {
            throw ValidationException::withMessages(['mois' => 'Format attendu : AAAA-MM.']);
        }

        return PeriodePaie::firstOrCreate(['mois' => $mois], ['statut' => StatutPeriodePaie::Brouillon]);
    }

    /** Crée ou recalcule les bulletins de tous les employés concernés (lignes manuelles conservées). */
    public function generer(PeriodePaie $periode): int
    {
        $this->verifierModifiable($periode);

        $employes = Employe::query()
            ->where(function ($q) use ($periode) {
                $q->where('statut', StatutEmploye::Actif)
                    ->orWhereHas('pointages', fn ($p) => $p->whereBetween('date_pointage', [$periode->debut(), $periode->fin()]));
            })
            ->whereDate('date_embauche', '<=', $periode->fin())
            ->get();

        DB::transaction(function () use ($employes, $periode) {
            foreach ($employes as $employe) {
                $bulletin = Bulletin::firstOrNew(['periode_paie_id' => $periode->id, 'employe_id' => $employe->id]);
                if (! $bulletin->exists) {
                    $bulletin->chantier_id = $employe->chantier_id;
                }
                $this->calculer($bulletin, $employe, $periode);
            }
        });

        return $employes->count();
    }

    public function calculer(Bulletin $bulletin, ?Employe $employe = null, ?PeriodePaie $periode = null): Bulletin
    {
        $employe ??= $bulletin->employe;
        $periode ??= $bulletin->periode;
        $cfg = config('btp.paie');
        $debut = $periode->debut();
        $fin = $periode->fin();

        $pointages = $employe->pointages()->whereBetween('date_pointage', [$debut, $fin])->get();
        $heuresSup = (float) $pointages->sum('heures_sup');

        if ($employe->mode_remuneration === ModeRemuneration::Journalier) {
            $jours = $pointages->where('present', true)->count();
            $absences = 0;
            $taux = (float) $employe->taux_journalier;
            $salaireDu = $jours * $taux;
            $tauxHoraire = $taux / $cfg['heures_par_jour'];
            $base = $taux;
        } else {
            $absences = $this->joursAbsenceNonPayes($employe, $debut, $fin, $pointages);
            $base = (float) $employe->salaire_base;
            $joursMois = $cfg['jours_par_mois'];
            // Embauche en cours de mois : prorata des jours calendaires restants.
            $joursContrat = $employe->date_embauche->gt($debut)
                ? min($joursMois, (int) $employe->date_embauche->copy()->startOfDay()->diffInDays($fin->copy()->startOfDay()) + 1)
                : $joursMois;
            $jours = max(0, $joursContrat - $absences);
            $salaireDu = round($base / $joursMois * $jours, 0);
            $tauxHoraire = $base / $cfg['heures_par_mois'];
        }

        $montantHs = round($heuresSup * $tauxHoraire * $cfg['majoration_heures_sup'], 0);

        $bulletin->fill([
            'mode_remuneration' => $employe->mode_remuneration,
            'salaire_base' => $base,
            'jours_travailles' => $jours,
            'jours_absence' => $absences,
            'salaire_du' => $salaireDu,
            'heures_sup' => $heuresSup,
            'montant_heures_sup' => $montantHs,
        ]);
        $bulletin->save();

        // Rattache les avances du mois non encore retenues.
        Avance::where('employe_id', $employe->id)
            ->where('periode_retenue', $periode->mois)
            ->whereNull('bulletin_id')
            ->update(['bulletin_id' => $bulletin->id]);

        return $this->totaliser($bulletin);
    }

    /** Recalcule brut / net à partir des montants et des lignes du bulletin. */
    public function totaliser(Bulletin $bulletin): Bulletin
    {
        $lignes = $bulletin->lignes()->get();
        $primes = (float) $lignes->whereIn('type', [TypeLignePaie::Prime, TypeLignePaie::Indemnite])->sum('montant');
        $retenues = (float) $lignes->where('type', TypeLignePaie::Retenue)->sum('montant');
        $avances = (float) $bulletin->avances()->sum('montant');
        $brut = (float) $bulletin->salaire_du + (float) $bulletin->montant_heures_sup + $primes;

        $bulletin->update([
            'total_primes' => $primes,
            'salaire_brut' => $brut,
            'total_avances' => $avances,
            'total_retenues' => $retenues,
            'net_a_payer' => $brut - $avances - $retenues,
        ]);

        return $bulletin;
    }

    public function ajouterLigne(Bulletin $bulletin, TypeLignePaie $type, string $libelle, float $montant): void
    {
        $this->verifierModifiable($bulletin->periode);
        if ($montant <= 0) {
            throw ValidationException::withMessages(['montant' => 'Le montant doit être positif.']);
        }
        $bulletin->lignes()->create(['type' => $type, 'libelle' => $libelle, 'montant' => $montant]);
        $this->totaliser($bulletin);
    }

    public function supprimerLigne(Bulletin $bulletin, int $ligneId): void
    {
        $this->verifierModifiable($bulletin->periode);
        $bulletin->lignes()->whereKey($ligneId)->delete();
        $this->totaliser($bulletin);
    }

    public function cloturer(PeriodePaie $periode, User $user): void
    {
        $this->verifierModifiable($periode);
        $bulletins = $periode->bulletins()->get();
        if ($bulletins->isEmpty()) {
            throw ValidationException::withMessages(['periode' => 'Aucun bulletin à clôturer : générez la paie d\'abord.']);
        }
        if ($bulletins->contains(fn ($b) => (float) $b->net_a_payer < 0)) {
            throw ValidationException::withMessages(['periode' => 'Un ou plusieurs bulletins ont un net à payer négatif. Corrigez-les avant de clôturer.']);
        }

        DB::transaction(function () use ($periode, $user, $bulletins) {
            $dateDepense = $periode->fin()->isFuture() ? Carbon::today() : $periode->fin();
            foreach ($bulletins->groupBy(fn ($b) => $b->chantier_id ?? 0) as $chantierId => $groupe) {
                $portee = $chantierId ? PorteeDepense::Chantier : PorteeDepense::Interne;
                $libelle = $chantierId
                    ? 'Salaires '.$periode->libelle.' — '.Chantier::find($chantierId)?->nom
                    : 'Salaires '.$periode->libelle.' — personnel du siège';
                Depense::create([
                    'portee' => $portee,
                    'chantier_id' => $chantierId ?: null,
                    'categorie_id' => CategorieDepense::salaires($portee)->id,
                    'date_depense' => $dateDepense,
                    'libelle' => $libelle.' ('.$groupe->count().' employé(s))',
                    'montant' => $groupe->sum(fn ($b) => (float) $b->salaire_brut),
                    'mode_paiement' => 'virement',
                    'statut' => StatutDepense::Validee,
                    'saisi_par' => $user->id,
                    'valide_par' => $user->id,
                    'valide_le' => now(),
                    'source_type' => PeriodePaie::class,
                    'source_id' => $periode->id,
                ]);
            }
            $periode->update(['statut' => StatutPeriodePaie::Cloturee, 'cloturee_le' => now(), 'cloturee_par' => $user->id]);
        });
    }

    /** Réouverture (administrateur) : supprime les dépenses générées. */
    public function rouvrir(PeriodePaie $periode): void
    {
        DB::transaction(function () use ($periode) {
            Depense::where('source_type', PeriodePaie::class)->where('source_id', $periode->id)->delete();
            $periode->update(['statut' => StatutPeriodePaie::Brouillon, 'cloturee_le' => null, 'cloturee_par' => null]);
        });
    }

    /**
     * Jours non payés d'un mensuel : jours pointés « absent » non couverts par un congé payé approuvé,
     * plus les absences injustifiées enregistrées par les RH (sans double comptage).
     */
    private function joursAbsenceNonPayes(Employe $employe, Carbon $debut, Carbon $fin, $pointages): int
    {
        $conges = $employe->conges()
            ->where('statut', StatutConge::Approuve)
            ->whereDate('date_debut', '<=', $fin)->whereDate('date_fin', '>=', $debut)
            ->get();

        $joursPayes = [];
        $joursNonPayes = [];
        foreach ($conges as $conge) {
            $d = $conge->date_debut->max($debut)->copy();
            $f = $conge->date_fin->min($fin);
            for (; $d->lte($f); $d->addDay()) {
                if ($conge->type === TypeConge::AbsenceInjustifiee) {
                    $joursNonPayes[$d->toDateString()] = true;
                } else {
                    $joursPayes[$d->toDateString()] = true;
                }
            }
        }
        foreach ($pointages->where('present', false) as $p) {
            $jour = $p->date_pointage->toDateString();
            if (! isset($joursPayes[$jour])) {
                $joursNonPayes[$jour] = true;
            }
        }

        return count($joursNonPayes);
    }

    private function verifierModifiable(PeriodePaie $periode): void
    {
        if ($periode->estCloturee()) {
            throw ValidationException::withMessages(['periode' => 'Cette période de paie est clôturée.']);
        }
    }
}
