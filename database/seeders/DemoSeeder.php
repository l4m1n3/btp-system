<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\StatutDepense;
use App\Models\Article;
use App\Models\CategorieDepense;
use App\Models\Chantier;
use App\Models\Client;
use App\Models\Conge;
use App\Models\Employe;
use App\Models\Fournisseur;
use App\Models\Marche;
use App\Models\Materiel;
use App\Models\Pointage;
use App\Models\RapportJournalier;
use App\Models\User;
use App\Services\PaieService;
use App\Services\StockService;
use Illuminate\Database\Seeder;

/**
 * Jeu de données de démonstration (fictif) : php artisan db:seed --class=DemoSeeder
 * Tous les comptes de démo ont le mot de passe « password ».
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026);
        $this->call(ReferentielSeeder::class);

        // ── Utilisateurs ───────────────────────────────────────────────
        $u = fn (string $nom, string $email, Role $role) => User::firstOrCreate(['email' => $email], ['name' => $nom, 'role' => $role, 'password' => 'password', 'actif' => true]);
        $admin = $u('Administrateur démo', 'admin@demo.local', Role::Admin);
        $direction = $u('Aïchatou Mahamadou', 'direction@demo.local', Role::Direction);
        $comptable = $u('Ibrahim Moussa', 'comptable@demo.local', Role::Comptable);
        $chef1 = $u('Abdoulaye Hamani', 'chef1@demo.local', Role::ChefChantier);
        $chef2 = $u('Ramatou Issoufou', 'chef2@demo.local', Role::ChefChantier);
        $u('Mariama Garba', 'rh@demo.local', Role::Rh);
        $logisticien = $u('Oumarou Seydou', 'logistique@demo.local', Role::Logistique);

        // ── Clients, fournisseurs ──────────────────────────────────────
        $clients = collect([
            ['nom' => 'Direction régionale de l\'Hydraulique de Tillabéri', 'type' => 'public', 'contact' => 'M. le Directeur régional'],
            ['nom' => 'Société de patrimoine des eaux (démo)', 'type' => 'public', 'contact' => 'Service des marchés'],
            ['nom' => 'Commune urbaine de Niamey III (démo)', 'type' => 'public', 'contact' => 'Secrétaire général'],
            ['nom' => 'Projet d\'appui à l\'eau potable — PAEP (démo)', 'type' => 'ong', 'contact' => 'Coordonnateur'],
            ['nom' => 'Sahel Immobilier SA (démo)', 'type' => 'prive', 'contact' => 'Directeur technique'],
        ])->map(fn ($c) => Client::firstOrCreate(['nom' => $c['nom']], $c + ['telephone' => '+227 20 '.mt_rand(10, 99).' '.mt_rand(10, 99).' '.mt_rand(10, 99)]));

        $fournisseurs = collect(['Quincaillerie du Fleuve', 'Cimenterie du Sahel (démo)', 'Station Total Route de Ouallam', 'Transports Alhéri', 'Forage Équipement Niger', 'Location Engins Plus'])
            ->map(fn ($n) => Fournisseur::firstOrCreate(['nom' => $n]));

        // ── Marchés ────────────────────────────────────────────────────
        $m = fn (array $d) => Marche::firstOrCreate(['reference' => $d['reference']], $d);
        $marcheForages = $m(['reference' => 'AO-DRH-TL-2026-03', 'intitule' => 'Réalisation de 10 forages équipés de pompes à motricité humaine', 'client_id' => $clients[0]->id, 'domaine' => 'hydraulique', 'statut' => 'en_cours', 'localisation' => 'Région de Tillabéri', 'montant_ht' => 185_000_000, 'taux_tva' => 0, 'avance_demarrage' => 27_750_000, 'date_soumission' => now()->subMonths(8), 'date_signature' => now()->subMonths(6), 'date_ordre_service' => now()->subMonths(5), 'delai_jours' => 240, 'date_fin_prevue' => now()->subMonths(5)->addDays(240)]);
        $marcheChateau = $m(['reference' => 'SPEN-2026-017', 'intitule' => 'Construction d\'un château d\'eau de 300 m³ et extension du réseau AEP', 'client_id' => $clients[1]->id, 'domaine' => 'hydraulique', 'statut' => 'en_cours', 'localisation' => 'Dosso', 'montant_ht' => 420_000_000, 'taux_tva' => 19, 'avance_demarrage' => 63_000_000, 'date_soumission' => now()->subMonths(7), 'date_signature' => now()->subMonths(4), 'date_ordre_service' => now()->subMonths(3), 'delai_jours' => 300, 'date_fin_prevue' => now()->subMonths(3)->addDays(300)]);
        $marcheEcole = $m(['reference' => 'CUN3-2025-044', 'intitule' => 'Réhabilitation de 4 salles de classe et d\'un bloc de latrines', 'client_id' => $clients[2]->id, 'domaine' => 'batiment', 'statut' => 'termine', 'localisation' => 'Niamey, Commune III', 'montant_ht' => 68_500_000, 'taux_tva' => 19, 'date_signature' => now()->subMonths(11), 'date_ordre_service' => now()->subMonths(10), 'delai_jours' => 120, 'date_fin_prevue' => now()->subMonths(6), 'date_reception' => now()->subMonths(5)]);
        $m(['reference' => 'AO-CUN3-2026-08', 'intitule' => 'Pavage de la voie d\'accès au marché (1,2 km) et assainissement', 'client_id' => $clients[2]->id, 'domaine' => 'travaux_publics', 'statut' => 'en_attente', 'localisation' => 'Niamey', 'montant_ht' => 310_000_000, 'taux_tva' => 19, 'date_soumission' => now()->subWeeks(3), 'delai_jours' => 180]);
        $m(['reference' => 'SIS-2026-002', 'intitule' => 'Construction d\'un bloc administratif R+1', 'client_id' => $clients[4]->id, 'domaine' => 'batiment', 'statut' => 'en_attente', 'localisation' => 'Niamey, Koira Kano', 'montant_ht' => 152_000_000, 'taux_tva' => 19, 'date_soumission' => now()->subWeeks(6), 'delai_jours' => 210]);
        $m(['reference' => 'PAEP-2026-05', 'intitule' => 'Réalisation de 3 mini-AEP solaires', 'client_id' => $clients[3]->id, 'domaine' => 'hydraulique', 'statut' => 'suspendu', 'localisation' => 'Tahoua', 'montant_ht' => 240_000_000, 'taux_tva' => 0, 'date_signature' => now()->subMonths(5), 'date_ordre_service' => now()->subMonths(4), 'delai_jours' => 270]);

        foreach ([[$marcheForages, 1, 4, 38_500_000, 38_500_000], [$marcheForages, 2, 2, 41_200_000, 0], [$marcheChateau, 1, 1, 52_000_000, 52_000_000], [$marcheEcole, 1, 8, 30_000_000, 30_000_000], [$marcheEcole, 2, 6, 38_500_000, 38_500_000]] as [$marche, $num, $moisAvant, $montant, $paye]) {
            $marche->decomptes()->firstOrCreate(['numero' => $num], [
                'date_decompte' => now()->subMonths($moisAvant), 'montant_ht' => $montant, 'montant_paye' => $paye,
                'date_paiement' => $paye ? now()->subMonths($moisAvant)->addWeeks(5) : null, 'statut' => $paye ? 'paye' : 'valide',
            ]);
        }

        // ── Chantiers et planning ──────────────────────────────────────
        $ch = fn (array $d) => Chantier::firstOrCreate(['code' => $d['code']], $d);
        $forages = $ch(['code' => 'CH-26-001', 'nom' => 'Forages Tillabéri (lot 1)', 'marche_id' => $marcheForages->id, 'chef_chantier_id' => $chef1->id, 'localisation' => 'Ouallam, Tillabéri', 'budget' => 142_000_000, 'statut' => 'en_cours', 'date_debut' => now()->subMonths(5), 'date_fin_prevue' => $marcheForages->date_fin_prevue]);
        $chateau = $ch(['code' => 'CH-26-002', 'nom' => 'Château d\'eau de Dosso', 'marche_id' => $marcheChateau->id, 'chef_chantier_id' => $chef2->id, 'localisation' => 'Dosso', 'budget' => 330_000_000, 'statut' => 'en_cours', 'date_debut' => now()->subMonths(3), 'date_fin_prevue' => $marcheChateau->date_fin_prevue]);
        $ecole = $ch(['code' => 'CH-25-014', 'nom' => 'Salles de classe CEG — Niamey III', 'marche_id' => $marcheEcole->id, 'chef_chantier_id' => $chef1->id, 'localisation' => 'Niamey', 'budget' => 54_000_000, 'statut' => 'termine', 'date_debut' => now()->subMonths(10), 'date_fin_prevue' => now()->subMonths(6), 'date_fin_reelle' => now()->subMonths(5)->subWeeks(2)]);

        $planning = [
            $forages->id => [['Installation de chantier et implantation', 0, 1, 4_500_000, 100], ['Foration des 10 forages (DTH)', 1, 4, 62_000_000, 80], ['Équipement : tubage, crépines, massif filtrant', 2, 5, 28_000_000, 60], ['Essais de pompage et analyses d\'eau', 3, 6, 9_500_000, 30], ['Superstructures et pose des pompes PMH', 4, 7, 24_000_000, 10], ['Repli et dossier de récolement', 7, 8, 2_500_000, 0]],
            $chateau->id => [['Installation et études d\'exécution', 0, 1, 12_000_000, 100], ['Fondations et radier', 1, 3, 68_000_000, 70], ['Fût et cuve béton armé (300 m³)', 2, 7, 145_000_000, 15], ['Pose de conduites (réseau AEP 8 km)', 3, 8, 78_000_000, 5], ['Équipements hydrauliques et essais', 8, 10, 22_000_000, 0]],
            $ecole->id => [['Démolitions et terrassements', -10, -9, 6_000_000, 100], ['Gros œuvre', -9, -7, 24_000_000, 100], ['Toiture, menuiseries, peinture', -7, -6, 17_000_000, 100], ['Latrines et VRD', -7, -6, 6_000_000, 100]],
        ];
        foreach ($planning as $chantierId => $taches) {
            $chantier = Chantier::find($chantierId);
            foreach ($taches as $i => [$libelle, $debut, $fin, $cout, $avancement]) {
                $debutPrevu = $chantier->date_debut->copy()->addMonths(max(0, $debut + ($chantierId === $ecole->id ? 10 : 0)));
                $finPrevue = $chantier->date_debut->copy()->addMonths($fin + ($chantierId === $ecole->id ? 10 : 0))->subDay();
                $chantier->taches()->firstOrCreate(['libelle' => $libelle], [
                    'date_debut_prevue' => $debutPrevu, 'date_fin_prevue' => $finPrevue,
                    'date_debut_reelle' => $avancement > 0 ? $debutPrevu : null,
                    'date_fin_reelle' => $avancement >= 100 ? $finPrevue->copy()->subDays(mt_rand(0, 5)) : null,
                    'cout_prevu' => $cout, 'avancement' => $avancement, 'ordre' => $i + 1,
                ]);
            }
        }

        // ── Dépenses (6 derniers mois) ─────────────────────────────────
        $cat = fn (string $nom, string $portee) => CategorieDepense::where('nom', $nom)->where('portee', $portee)->value('id');
        $depensesChantier = [
            ['Matériaux de construction', 'Ciment CPJ 45 et fer à béton', [2_500_000, 9_000_000]],
            ['Location d\'engins', 'Location atelier de forage / camion-grue', [3_000_000, 12_000_000]],
            ['Carburant et lubrifiants', 'Gasoil engins et groupe électrogène', [400_000, 1_800_000]],
            ['Transport et manutention', 'Transport de matériaux', [250_000, 900_000]],
            ['Main-d\'œuvre temporaire (tâcherons)', 'Tâcherons — semaine', [300_000, 1_200_000]],
            ['Petit outillage et consommables', 'Consommables et petit outillage', [80_000, 450_000]],
            ['Frais de chantier (eau, électricité, gardiennage)', 'Gardiennage et eau de chantier', [150_000, 400_000]],
        ];
        foreach ([$forages, $chateau] as $chantier) {
            // Imputation sur les tâches commencées, au prorata de leur coût prévu.
            $taches = $chantier->taches()->where('avancement', '>', 0)->get();
            $tirage = $taches->flatMap(fn ($t) => array_fill(0, max(1, (int) round($t->cout_prevu / 2_000_000)), $t->id))->all();
            for ($k = 0; $k < 34; $k++) {
                [$categorie, $libelle, [$min, $max]] = $depensesChantier[mt_rand(0, count($depensesChantier) - 1)];
                $date = now()->subDays(mt_rand(0, min(170, (int) $chantier->date_debut->diffInDays(now()))));
                $statut = $date->gt(now()->subDays(7)) && mt_rand(0, 2) === 0 ? StatutDepense::EnAttente : StatutDepense::Validee;
                $chantier->depenses()->create([
                    'portee' => 'chantier', 'tache_id' => mt_rand(0, 3) ? $tirage[mt_rand(0, count($tirage) - 1)] : null, 'categorie_id' => $cat($categorie, 'chantier'),
                    'fournisseur_id' => $fournisseurs->random()->id, 'date_depense' => $date, 'libelle' => $libelle,
                    'montant' => round(mt_rand($min, $max), -3), 'mode_paiement' => ['especes', 'cheque', 'virement', 'mobile_money'][mt_rand(0, 3)],
                    'reference_piece' => 'FAC-'.mt_rand(1000, 9999), 'statut' => $statut,
                    'saisi_par' => $chantier->chef_chantier_id, 'valide_par' => $statut === StatutDepense::Validee ? $comptable->id : null,
                    'valide_le' => $statut === StatutDepense::Validee ? $date->copy()->addDay() : null,
                ]);
            }
        }
        foreach ([[6_000_000, 'Matériaux de construction'], [4_500_000, 'Main-d\'œuvre temporaire (tâcherons)'], [2_000_000, 'Transport et manutention']] as [$montant, $categorie]) {
            $ecole->depenses()->create(['portee' => 'chantier', 'categorie_id' => $cat($categorie, 'chantier'), 'date_depense' => now()->subMonths(7), 'libelle' => $categorie.' (réhabilitation)', 'montant' => $montant * 3, 'mode_paiement' => 'virement', 'statut' => 'validee', 'saisi_par' => $chef1->id, 'valide_par' => $comptable->id, 'valide_le' => now()->subMonths(7)]);
        }
        $internes = [
            ['Loyer et charges du siège', 'Loyer du siège', 750_000, 'mensuel'],
            ['Électricité, eau et internet', 'Factures NIGELEC / SEEN / internet', 210_000, 'mensuel'],
            ['Carburant véhicules de service', 'Carburant véhicules de liaison', 320_000, 'mensuel'],
            ['Communication et téléphonie', 'Forfaits téléphoniques', 95_000, 'mensuel'],
            ['Fournitures de bureau', 'Papeterie et cartouches', 140_000, 'ponctuel'],
            ['Frais de soumission et cautions', 'Achat DAO et caution de soumission', 1_250_000, 'ponctuel'],
            ['Frais bancaires', 'Agios et frais de tenue de compte', 85_000, 'mensuel'],
        ];
        for ($mois = 5; $mois >= 0; $mois--) {
            foreach ($internes as [$categorie, $libelle, $montant, $frequence]) {
                if ($frequence === 'ponctuel' && mt_rand(0, 2) > 0) {
                    continue;
                }
                $date = now()->subMonths($mois)->startOfMonth()->addDays(mt_rand(1, 20));
                if ($date->isFuture()) {
                    $date = now();
                }
                \App\Models\Depense::create([
                    'portee' => 'interne', 'categorie_id' => $cat($categorie, 'interne'), 'date_depense' => $date,
                    'libelle' => $libelle.' — '.$date->translatedFormat('F Y'), 'montant' => round($montant * (0.9 + mt_rand(0, 20) / 100), -3),
                    'mode_paiement' => 'virement', 'statut' => 'validee', 'saisi_par' => $comptable->id, 'valide_par' => $comptable->id, 'valide_le' => $date,
                ]);
            }
        }

        // ── Logistique ─────────────────────────────────────────────────
        $stock = app(StockService::class);
        $articles = collect([
            ['ART-001', 'Ciment CPJ 45', 'sac', 200, 6_500, 900],
            ['ART-002', 'Fer à béton HA 12', 'barre', 150, 5_800, 600],
            ['ART-003', 'Gravier 5/15', 'm³', 30, 22_000, 120],
            ['ART-004', 'Sable', 'm³', 30, 9_000, 140],
            ['ART-005', 'Tube PVC 125 mm (forage)', 'ml', 100, 7_500, 450],
            ['ART-006', 'Crépine PVC 125 mm', 'ml', 40, 12_000, 30],
            ['ART-007', 'Pompe à motricité humaine', 'unité', 3, 1_650_000, 6],
        ])->map(function ($a) use ($stock, $fournisseurs, $logisticien) {
            [$code, $designation, $unite, $min, $pu, $qte] = $a;
            $article = Article::firstOrCreate(['code' => $code], ['designation' => $designation, 'unite' => $unite, 'stock_minimum' => $min]);
            if ($article->mouvements()->doesntExist()) {
                $stock->entree($article, $qte, $pu, ['date_mouvement' => now()->subMonths(4), 'fournisseur_id' => $fournisseurs->random()->id, 'reference' => 'BL-'.mt_rand(100, 999), 'saisi_par' => $logisticien->id]);
            }

            return $article->fresh();
        });
        foreach ([[$forages, 'ART-005', 380], [$forages, 'ART-006', 25], [$forages, 'ART-007', 4], [$forages, 'ART-001', 120], [$chateau, 'ART-001', 640], [$chateau, 'ART-002', 410], [$chateau, 'ART-003', 85], [$chateau, 'ART-004', 90]] as [$chantier, $code, $qte]) {
            $article = $articles->firstWhere('code', $code);
            if ($chantier->mouvementsStock()->where('article_id', $article->id)->doesntExist()) {
                $stock->sortie($article, $qte, $chantier->id, ['date_mouvement' => now()->subWeeks(mt_rand(1, 10)), 'reference' => 'BS-'.mt_rand(100, 999), 'saisi_par' => $logisticien->id]);
            }
        }

        foreach ([
            ['MAT-001', 'Atelier de forage DTH sur camion', 'engin', 'Foremost', 'NG-4521-A', $forages->id, 'affecte'],
            ['MAT-002', 'Compresseur 25 bars', 'engin', 'Atlas Copco', null, $forages->id, 'affecte'],
            ['MAT-003', 'Camion-benne 20 t', 'vehicule', 'Mercedes Actros', 'NG-7810-B', $chateau->id, 'affecte'],
            ['MAT-004', 'Bétonnière 500 L', 'equipement', 'Altrad', null, $chateau->id, 'affecte'],
            ['MAT-005', 'Pelle hydraulique 20 t', 'engin', 'Caterpillar 320', null, null, 'en_panne'],
            ['MAT-006', 'Pick-up de liaison', 'vehicule', 'Toyota Hilux', 'NG-1123-C', null, 'disponible'],
            ['MAT-007', 'Groupe électrogène 60 kVA', 'equipement', 'SDMO', null, null, 'en_maintenance'],
        ] as [$code, $designation, $type, $marque, $immat, $chantierId, $etat]) {
            $materiel = Materiel::firstOrCreate(['code' => $code], compact('designation', 'type', 'marque', 'etat') + ['immatriculation' => $immat, 'chantier_id' => $chantierId, 'date_acquisition' => now()->subYears(mt_rand(1, 6))]);
            if ($chantierId && $materiel->affectations()->doesntExist()) {
                $materiel->affectations()->create(['chantier_id' => $chantierId, 'date_debut' => Chantier::find($chantierId)->date_debut]);
            }
        }
        Materiel::where('code', 'MAT-005')->first()->maintenances()->firstOrCreate(['description' => 'Fuite vérin de flèche — diagnostic'], ['date_maintenance' => now()->subDays(4), 'type' => 'corrective', 'cout' => 0]);

        // ── RH ─────────────────────────────────────────────────────────
        $personnel = [
            ['Hamani', 'Abdoulaye', 'Conducteur des travaux', 'Cadre', null, 'cdi', 'mensuel', 650_000],
            ['Issoufou', 'Ramatou', 'Ingénieure génie civil', 'Cadre', null, 'cdi', 'mensuel', 700_000],
            ['Garba', 'Mariama', 'Responsable RH', 'Cadre', null, 'cdi', 'mensuel', 450_000],
            ['Moussa', 'Ibrahim', 'Comptable', 'Agent de maîtrise', null, 'cdi', 'mensuel', 400_000],
            ['Seydou', 'Oumarou', 'Magasinier-logisticien', 'Agent de maîtrise', null, 'cdi', 'mensuel', 280_000],
            ['Adamou', 'Souley', 'Foreur', 'Ouvrier qualifié', $forages->id, 'cdi', 'mensuel', 220_000],
            ['Boubacar', 'Hassane', 'Aide-foreur', 'Ouvrier', $forages->id, 'cdd', 'mensuel', 130_000],
            ['Maman', 'Lawali', 'Mécanicien', 'Ouvrier qualifié', $forages->id, 'cdi', 'mensuel', 200_000],
            ['Idrissa', 'Kadri', 'Chef d\'équipe maçon', 'Agent de maîtrise', $chateau->id, 'cdi', 'mensuel', 260_000],
            ['Abdou', 'Zakari', 'Ferrailleur', 'Ouvrier qualifié', $chateau->id, 'cdd', 'mensuel', 160_000],
            ['Harouna', 'Salifou', 'Conducteur de camion', 'Ouvrier qualifié', $chateau->id, 'cdi', 'mensuel', 180_000],
            ['Saidou', 'Amadou', 'Manœuvre', 'Ouvrier', $chateau->id, 'journalier', 'journalier', 3_500],
            ['Yacouba', 'Ali', 'Manœuvre', 'Ouvrier', $chateau->id, 'journalier', 'journalier', 3_500],
            ['Mounkaila', 'Issa', 'Maçon', 'Ouvrier qualifié', $chateau->id, 'journalier', 'journalier', 6_000],
            ['Hama', 'Djibo', 'Manœuvre', 'Ouvrier', $forages->id, 'journalier', 'journalier', 3_500],
        ];
        foreach ($personnel as $i => [$nom, $prenom, $fonction, $categorie, $chantierId, $contrat, $mode, $montant]) {
            Employe::firstOrCreate(['matricule' => 'EMP-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT)], [
                'nom' => $nom, 'prenom' => $prenom, 'sexe' => in_array($prenom, ['Ramatou', 'Mariama']) ? 'F' : 'M', 'fonction' => $fonction, 'categorie' => $categorie,
                'chantier_id' => $chantierId, 'type_contrat' => $contrat, 'date_embauche' => now()->subMonths(mt_rand(7, 60)),
                'date_fin_contrat' => $contrat === 'cdd' ? now()->addDays(mt_rand(10, 120)) : null,
                'mode_remuneration' => $mode, 'salaire_base' => $mode === 'mensuel' ? $montant : 0, 'taux_journalier' => $mode === 'journalier' ? $montant : 0,
                'telephone' => '+227 9'.mt_rand(0, 9).' '.mt_rand(10, 99).' '.mt_rand(10, 99).' '.mt_rand(10, 99), 'mode_paiement' => $mode === 'journalier' ? 'especes' : 'virement',
            ]);
        }
        $chef1->update(['employe_id' => Employe::where('matricule', 'EMP-0001')->value('id')]);
        $chef2->update(['employe_id' => Employe::where('matricule', 'EMP-0002')->value('id')]);

        // Pointage du mois précédent et du mois en cours (jours ouvrés)
        $chantierEmployes = Employe::whereNotNull('chantier_id')->get();
        for ($d = now()->subMonthNoOverflow()->startOfMonth(); $d->lte(now()->subDay()); $d->addDay()) {
            if ($d->isSunday()) {
                continue;
            }
            foreach ($chantierEmployes as $e) {
                $present = mt_rand(0, 20) > 0;
                $pointage = Pointage::pour($e->id, $d->toDateString());
                if ($pointage->exists) {
                    continue;
                }
                $pointage->fill([
                    'chantier_id' => $e->chantier_id, 'present' => $present, 'heures_sup' => $present && mt_rand(0, 5) === 0 ? 2 : 0,
                    'saisi_par' => $e->chantier_id === $forages->id ? $chef1->id : $chef2->id,
                ])->save();
            }
        }
        $e = Employe::where('matricule', 'EMP-0010')->first();
        Conge::firstOrCreate(['employe_id' => $e->id, 'type' => 'conge_annuel'], ['date_debut' => now()->addWeeks(2), 'date_fin' => now()->addWeeks(4), 'motif' => 'Congé annuel', 'statut' => 'demande']);
        $e = Employe::where('matricule', 'EMP-0007')->first();
        Conge::firstOrCreate(['employe_id' => $e->id, 'type' => 'maladie'], ['date_debut' => now()->subMonthNoOverflow()->startOfMonth()->addDays(9), 'date_fin' => now()->subMonthNoOverflow()->startOfMonth()->addDays(11), 'motif' => 'Certificat médical', 'statut' => 'approuve', 'traite_par' => $admin->id]);
        foreach (['EMP-0009' => 50_000, 'EMP-0011' => 30_000, 'EMP-0006' => 40_000] as $mat => $montant) {
            \App\Models\Avance::firstOrCreate(['employe_id' => Employe::where('matricule', $mat)->value('id'), 'periode_retenue' => now()->subMonthNoOverflow()->format('Y-m')], ['date_avance' => now()->subMonthNoOverflow()->startOfMonth()->addDays(14), 'montant' => $montant, 'motif' => 'Avance sur salaire']);
        }

        // ── Paie : mois précédent clôturé, mois en cours préparé ───────
        $paie = app(PaieService::class);
        $precedente = $paie->ouvrirPeriode(now()->subMonthNoOverflow()->format('Y-m'));
        if (! $precedente->estCloturee()) {
            $paie->generer($precedente);
            $bulletin = $precedente->bulletins()->whereHas('employe', fn ($q) => $q->where('matricule', 'EMP-0006'))->first();
            if ($bulletin && $bulletin->lignes()->doesntExist()) {
                $paie->ajouterLigne($bulletin, \App\Enums\TypeLignePaie::Prime, 'Prime de rendement (forages réceptionnés)', 25_000);
            }
            $paie->cloturer($precedente, $direction);
        }
        $paie->generer($paie->ouvrirPeriode(now()->format('Y-m')));

        // ── Journal de chantier (quelques jours récents) ───────────────
        foreach ([$forages, $chateau] as $chantier) {
            for ($j = 1; $j <= 3; $j++) {
                $date = now()->subDays($j);
                if (RapportJournalier::where('chantier_id', $chantier->id)->whereDate('date_rapport', $date)->exists()) {
                    continue;
                }
                $rapport = $chantier->rapports()->create(['date_rapport' => $date, 'meteo' => ['Ensoleillé', 'Chaud et sec', 'Vent de sable le matin'][$j - 1], 'observations' => 'Travaux conformes au planning.', 'redige_par' => $chantier->chef_chantier_id]);
                $tache = $chantier->taches()->where('statut', 'en_cours')->first();
                $rapport->lignes()->createMany([
                    ['type' => 'personnel', 'designation' => 'Ouvriers et manœuvres', 'quantite' => $chantier->employes()->count(), 'unite' => 'personnes'],
                    ['type' => 'materiel', 'designation' => $chantier->materiels()->value('designation') ?? 'Petit matériel', 'quantite' => 1, 'unite' => 'unité'],
                    ['type' => 'travaux', 'designation' => $tache?->libelle ?? 'Travaux divers', 'tache_id' => $tache?->id, 'avancement' => $tache?->avancement],
                ]);
            }
        }

        $this->command?->info('Données de démonstration chargées. Comptes : admin@ / direction@ / comptable@ / chef1@ / chef2@ / rh@ / logistique@demo.local — mot de passe « password ».');
    }
}
