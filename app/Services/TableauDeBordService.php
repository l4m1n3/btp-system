<?php

namespace App\Services;

use App\Enums\EtatMateriel;
use App\Enums\PorteeDepense;
use App\Enums\StatutConge;
use App\Enums\StatutDepense;
use App\Enums\StatutEmploye;
use App\Enums\StatutMarche;
use App\Models\Article;
use App\Models\Chantier;
use App\Models\Conge;
use App\Models\Decompte;
use App\Models\Depense;
use App\Models\Employe;
use App\Models\Marche;
use App\Models\Materiel;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

/**
 * Indicateurs du tableau de bord, filtrés selon les droits de l'utilisateur.
 */
class TableauDeBordService
{
    public function pour(User $user): array
    {
        $gate = Gate::forUser($user);
        $data = [];

        if ($gate->allows('marches.view')) {
            $data['marches'] = $this->marches();
        }
        if ($gate->allows('chantiers.view')) {
            $data['chantiers'] = $this->chantiers($user);
        }
        if ($gate->allows('depenses.view')) {
            $data['depenses'] = $this->depenses($user);
        }
        $data['alertes'] = $this->alertes($user);

        return $data;
    }

    private function marches(): array
    {
        $parStatut = Marche::selectRaw('statut, COUNT(*) as nb, COALESCE(SUM(montant_ht),0) as montant')
            ->groupBy('statut')->get()->keyBy(fn ($r) => $r->statut->value);

        $enCours = Marche::with('decomptes')->whereIn('statut', [StatutMarche::EnCours, StatutMarche::Suspendu])->get();

        return [
            'par_statut' => collect(StatutMarche::cases())->mapWithKeys(fn ($s) => [$s->value => [
                'label' => $s->label(), 'color' => $s->color(),
                'nb' => (int) ($parStatut[$s->value]->nb ?? 0),
                'montant' => (float) ($parStatut[$s->value]->montant ?? 0),
            ]])->all(),
            'carnet_commandes' => $enCours->sum(fn ($m) => $m->reste_a_realiser),
            'encaisse_annee' => (float) Decompte::whereYear('date_paiement', now()->year)->sum('montant_paye'),
            'a_encaisser' => (float) Decompte::whereIn('statut', ['soumis', 'valide'])->get()
                ->sum(fn ($d) => (float) $d->montant_ht - (float) $d->montant_paye),
        ];
    }

    private function chantiers(User $user): array
    {
        $chantiers = Chantier::visiblePour($user)->actifs()->with(['taches', 'marche'])->orderBy('nom')->get();

        return [
            'actifs' => $chantiers->count(),
            'liste' => $chantiers->map(fn (Chantier $c) => [
                'model' => $c,
                'avancement' => $c->avancement,
                'cout' => $c->coutTotal(),
                'conso' => $c->tauxConsommationBudget(),
                'retard' => $c->estEnRetard(),
            ]),
        ];
    }

    private function depenses(User $user): array
    {
        $base = fn () => Depense::visiblePour($user)->validees();
        $debutMois = now()->startOfMonth();

        // 6 derniers mois : interne vs chantier
        $mois = collect(range(5, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $serie = $mois->map(function (Carbon $m) use ($base) {
            $q = $base()->whereBetween('date_depense', [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()]);

            return [
                'mois' => ucfirst($m->translatedFormat('M y')),
                'interne' => (float) (clone $q)->where('portee', PorteeDepense::Interne)->sum('montant'),
                'chantier' => (float) (clone $q)->where('portee', PorteeDepense::Chantier)->sum('montant'),
            ];
        });

        $parCategorie = $base()->where('date_depense', '>=', now()->startOfYear())
            ->join('categories_depenses', 'categories_depenses.id', '=', 'depenses.categorie_id')
            ->selectRaw('categories_depenses.nom as categorie, SUM(depenses.montant) as total')
            ->groupBy('categories_depenses.nom')->orderByDesc('total')->limit(6)->get();

        return [
            'mois_total' => (float) $base()->where('date_depense', '>=', $debutMois)->sum('montant'),
            'mois_interne' => (float) $base()->where('date_depense', '>=', $debutMois)->where('portee', PorteeDepense::Interne)->sum('montant'),
            'mois_chantier' => (float) $base()->where('date_depense', '>=', $debutMois)->where('portee', PorteeDepense::Chantier)->sum('montant'),
            'annee_total' => (float) $base()->where('date_depense', '>=', now()->startOfYear())->sum('montant'),
            'en_attente' => Depense::visiblePour($user)->where('statut', StatutDepense::EnAttente)->count(),
            'serie' => $serie,
            'par_categorie' => $parCategorie,
        ];
    }

    /** @return array<int, array{niveau:string, texte:string, lien:?string}> */
    private function alertes(User $user): array
    {
        $gate = Gate::forUser($user);
        $alertes = [];

        if ($gate->allows('chantiers.view')) {
            $retard = Tache::enRetard()->whereHas('chantier', fn ($q) => $q->visiblePour($user)->actifs())->count();
            if ($retard) {
                $alertes[] = ['niveau' => 'red', 'texte' => "$retard tâche(s) de planning en retard", 'lien' => route('chantiers.index')];
            }
            $seuil = config('btp.alertes.seuil_budget');
            foreach (Chantier::visiblePour($user)->actifs()->where('budget', '>', 0)->get() as $c) {
                $taux = $c->tauxConsommationBudget();
                if ($taux >= $seuil) {
                    $alertes[] = ['niveau' => $taux >= 100 ? 'red' : 'amber', 'texte' => "Chantier {$c->nom} : {$taux} % du budget consommé", 'lien' => route('chantiers.show', $c)];
                }
            }
        }
        if ($gate->allows('depenses.valider')) {
            $n = Depense::where('statut', StatutDepense::EnAttente)->count();
            if ($n) {
                $alertes[] = ['niveau' => 'amber', 'texte' => "$n dépense(s) en attente de validation", 'lien' => route('depenses.index', ['statut' => 'en_attente'])];
            }
        }
        if ($gate->allows('logistique.view')) {
            $n = Article::sousMinimum()->count();
            if ($n) {
                $alertes[] = ['niveau' => 'amber', 'texte' => "$n article(s) de stock sous le seuil minimum", 'lien' => route('stock.index', ['alerte' => 1])];
            }
            $n = Materiel::where('etat', EtatMateriel::EnPanne)->count();
            if ($n) {
                $alertes[] = ['niveau' => 'red', 'texte' => "$n matériel(s) en panne", 'lien' => route('materiels.index', ['etat' => 'en_panne'])];
            }
        }
        if ($gate->allows('rh.view')) {
            $jours = config('btp.alertes.contrat_jours');
            $n = Employe::where('statut', StatutEmploye::Actif)->whereNotNull('date_fin_contrat')
                ->whereBetween('date_fin_contrat', [now()->startOfDay(), now()->addDays($jours)])->count();
            if ($n) {
                $alertes[] = ['niveau' => 'amber', 'texte' => "$n contrat(s) arrivent à échéance dans moins de $jours jours", 'lien' => route('employes.index')];
            }
            $n = Conge::where('statut', StatutConge::Demande)->count();
            if ($n) {
                $alertes[] = ['niveau' => 'slate', 'texte' => "$n demande(s) de congé à traiter", 'lien' => route('conges.index')];
            }
        }

        return $alertes;
    }
}
