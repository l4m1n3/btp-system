<?php

return [
    /*
    | Identité de l'entreprise (en-têtes des PDF : bulletins, rapports…)
    */
    'entreprise' => [
        'nom' => env('BTP_ENTREPRISE_NOM', 'Entreprise BTP/H'),
        'adresse' => env('BTP_ENTREPRISE_ADRESSE', 'Niamey, Niger'),
        'telephone' => env('BTP_ENTREPRISE_TELEPHONE', ''),
        'email' => env('BTP_ENTREPRISE_EMAIL', ''),
        'nif' => env('BTP_ENTREPRISE_NIF', ''),
        'rccm' => env('BTP_ENTREPRISE_RCCM', ''),
    ],

    'devise' => 'FCFA',

    /*
    | Paramètres de la paie simple
    */
    'paie' => [
        'jours_par_mois' => 30,             // base de proratisation des absences (salaire mensuel)
        'heures_par_mois' => 173.33,         // pour le taux horaire des mensuels
        'heures_par_jour' => 8,              // pour le taux horaire des journaliers
        'majoration_heures_sup' => 1.25,     // +25 %
    ],

    /*
    | Alertes du tableau de bord
    */
    'alertes' => [
        'contrat_jours' => 30,               // fin de contrat dans moins de N jours
        'seuil_budget' => 90,                // % de budget consommé
    ],

    /*
    | Destinataires du rapport hebdomadaire (rôles)
    */
    'rapport_hebdomadaire_roles' => ['admin', 'direction'],
];
