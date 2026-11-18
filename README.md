# Gestion interne BTP/H

Application de gestion interne pour une entreprise de BTP et d'hydraulique : **marchés**, **chantiers** (planning et avancement), **dépenses** internes et de chantier, **logistique** (matériel, stock), **RH** et **paie**.

Laravel 12 · Livewire 3 · Tailwind CSS 4 · PostgreSQL · API mobile Sanctum · PDF (dompdf) · export Excel.

## Modules

| Module | Contenu |
|---|---|
| Tableau de bord | Marchés par statut, carnet de commandes, encaissements, dépenses du mois (interne / chantiers), avancement et budget des chantiers, alertes (retards, dépassements, stock bas, pannes, contrats, dépenses à valider). |
| Marchés & clients | Marchés en attente / en cours / suspendus / terminés / annulés, montants HT/TTC, décomptes et encaissements, reste à réaliser. |
| Chantiers | Rattachés à un marché ; planning des tâches (avancement pondéré par le coût prévu), journal de chantier quotidien, personnel et matériel affectés, situation financière (budget, dépenses, matériaux, écart) en PDF. |
| Dépenses | Une seule liste pour les dépenses **internes** (frais généraux) et **de chantier** (rattachables à une tâche). Catégories, fournisseurs, justificatif (privé), circuit de validation : seules les dépenses **validées** entrent dans les totaux. Exports PDF et Excel. |
| Logistique | Matériel et engins (affectation, panne, maintenance → dépense), stock de matériaux au coût moyen pondéré (entrées, sorties vers chantier, inventaire, seuils d'alerte). |
| RH | Employés et contrats, affectation, pointage journalier par chantier, congés et absences, avances. |
| Paie (simple) | Mensuels : base − 1/30 par jour d'absence non payée ; journaliers : jours pointés × taux ; heures sup majorées ; primes, indemnités, retenues ; avances déduites ; bulletins et journal de paie PDF. À la **clôture**, la masse salariale est enregistrée en dépenses (par chantier d'imputation, sinon en interne). |
| Rapports | Synthèse sur une période : facturé, encaissé, dépenses par catégorie, situation de chaque chantier. Rapport hebdomadaire envoyé chaque lundi 7 h à la direction. |

## Rôles

| Rôle | Accès |
|---|---|
| Administrateur | Tout, y compris les utilisateurs et la réouverture d'une paie clôturée. |
| Direction | Vue complète, marchés, chantiers, validation des dépenses, clôture de paie. |
| Comptable | Dépenses (saisie et validation), catégories/fournisseurs, rapports, clôture de paie. |
| Chef de chantier | **Uniquement ses chantiers** : planning, journal, dépenses (soumises à validation), pointage, sorties de stock. |
| RH | Employés, pointage, congés, avances, préparation de la paie. |
| Logistique | Matériel et stock. |

Les droits sont définis dans `config/permissions.php`.

## Installation

Prérequis : PHP 8.4, Composer, Node 20+, PostgreSQL.

```bash
composer install
npm install && npm run build
cp .env.example .env        # puis renseigner DB_*, ADMIN_*, BTP_ENTREPRISE_*
php artisan key:generate
php artisan migrate:fresh --seed       # ⚠ efface la base : schéma + catégories + compte admin
php artisan db:seed --class=DemoSeeder # facultatif : données de démonstration
php artisan serve
```

Comptes de démonstration (mot de passe `password`) : `admin@demo.local`, `direction@demo.local`, `comptable@demo.local`, `chef1@demo.local`, `chef2@demo.local`, `rh@demo.local`, `logistique@demo.local`.

Rapport hebdomadaire : lancer le planificateur (`php artisan schedule:work` en local, ou une tâche cron `* * * * * php artisan schedule:run` en production) et configurer `MAIL_*`. Test manuel : `php artisan rapport:hebdomadaire`.

### Mise à jour depuis l'ancienne version

La refonte remplace l'ancien schéma (entreprises clientes, abonnements, devis…). Après avoir récupéré les fichiers :

```bash
bash scripts/nettoyer-ancienne-version.sh   # supprime les anciens fichiers devenus inutiles
composer install && npm install && npm run build
php artisan migrate:fresh --seed
```

## API mobile

Authentification par jeton : `POST /api/login` (`email`, `password`, `appareil`) puis en-têtes `Authorization: Bearer <token>` et `Accept: application/json`.

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/api/me` | Profil et droits |
| GET | `/api/references` | Catégories, fournisseurs, modes de paiement |
| GET | `/api/chantiers`, `/api/chantiers/{id}` | Chantiers visibles avec tâches, avancement, budget |
| PATCH | `/api/chantiers/{id}/taches/{tache}` | Avancement d'une tâche (`avancement` 0–100) |
| GET/POST | `/api/chantiers/{id}/journal` | Rapports journaliers |
| GET/POST | `/api/chantiers/{id}/pointage` | Pointage du jour (`date`, `pointages[]`) |
| GET | `/api/articles` | Stock |
| POST | `/api/chantiers/{id}/sorties-stock` | Sortie de matériaux |
| GET/POST | `/api/depenses` | Dépenses (multipart avec `justificatif`) |
| POST | `/api/logout` | Révoque le jeton |

## Tests

```bash
php artisan test
```

41 tests : droits par rôle, isolement des chefs de chantier, circuit des dépenses, calculs de paie, stock, journal, API, rendu de toutes les pages et des PDF.

## Organisation du code

- `app/Enums` : statuts et listes (libellés et couleurs).
- `app/Models` : modèles Eloquent et calculs simples (avancement, coût engagé…).
- `app/Services` : règles métier (`DepenseService`, `PaieService`, `StockService`, `JournalService`, tableau de bord, rapport hebdomadaire).
- `app/Livewire` : écrans ; `resources/views/livewire` : vues ; `resources/views/components` : composants d'interface.
- `app/Http/Controllers/Api` : API mobile ; `DocumentController` : PDF, export Excel, justificatifs.
- `config/btp.php` : identité de l'entreprise, paramètres de paie et d'alertes.
