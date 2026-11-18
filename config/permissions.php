<?php

/*
|--------------------------------------------------------------------------
| Droits par rôle
|--------------------------------------------------------------------------
| Chaque capacité (ability) liste les rôles autorisés. L'administrateur a
| tous les droits. Utilisation : @can('depenses.valider'), $this->authorize(),
| middleware 'can:rh.manage'.
*/

return [
    'dashboard.view' => ['direction', 'comptable', 'chef_chantier', 'rh', 'logistique'],

    'marches.view' => ['direction', 'comptable'],
    'marches.manage' => ['direction'],

    'chantiers.view' => ['direction', 'comptable', 'chef_chantier', 'logistique'],
    'chantiers.manage' => ['direction'],
    'chantiers.suivi' => ['direction', 'chef_chantier'],   // planning, avancement, journal

    'depenses.view' => ['direction', 'comptable', 'chef_chantier'],
    'depenses.create' => ['direction', 'comptable', 'chef_chantier', 'logistique'],
    'depenses.interne' => ['direction', 'comptable'],      // saisir des frais généraux
    'depenses.valider' => ['direction', 'comptable'],
    'parametres.manage' => ['direction', 'comptable'],     // catégories, fournisseurs

    'logistique.view' => ['direction', 'logistique', 'chef_chantier'],
    'logistique.manage' => ['logistique'],
    'stock.sortie' => ['logistique', 'chef_chantier'],

    'rh.view' => ['direction', 'rh'],
    'rh.manage' => ['rh'],
    'pointage.saisir' => ['rh', 'chef_chantier'],

    'paie.view' => ['direction', 'rh', 'comptable'],
    'paie.manage' => ['rh'],
    'paie.cloturer' => ['direction', 'comptable'],

    'rapports.view' => ['direction', 'comptable'],

    'users.manage' => [],
];
