<?php

/*
|--------------------------------------------------------------------------
| Sections du menu par type d'utilisateur
|--------------------------------------------------------------------------
|
| Ce fichier est le SEUL endroit à modifier pour ouvrir ou fermer une
| section de menu à un type d'utilisateur. Il pilote à la fois la barre
| latérale (resources/views/layouts/app.blade.php) et les accès rapides
| du dashboard RH/Paie (resources/views/hr/dashboard.blade.php).
|
| Sections disponibles :
|   'configuration' : Configuration Entreprise
|   'employes'      : Gestion des Employés (dossiers, contrats, présences)
|   'paie'          : Gestion de Paie et Retenues
|   'declarations'  : Bulletins et Déclarations
|   'evenements'    : Événements
|   'simulateur'    : Simulateur
|
| Pour ajouter une section à un rôle, ajoutez simplement son nom dans son
| tableau. Exemple, pour que la Paie accède aussi aux bulletins :
|
|     'payroll' => ['paie', 'declarations'],
|
| Une section absente du tableau n'apparaît pas dans le menu. Attention :
| cela masque l'entrée, cela ne bloque pas l'accès par URL directe — les
| routes de ces modules ne contrôlent pas encore le type d'utilisateur.
|
| Note : 'paie' et 'payroll' désignent le même rôle. Le formulaire des
| Paramètres enregistre 'payroll', l'écran Super Admin enregistre 'paie'.
| Gardez les deux lignes identiques.
|
*/

return [

    // Le compte propriétaire de l'entreprise : accès complet
    'company' => [
        'configuration',
        'employes',
        'paie',
        'declarations',
        'evenements',
        'simulateur',
    ],

    // Gestion des employés
    'hr' => [
        'employes',
    ],

    // Gestion de la paie
    'paie' => [
        'paie',
    ],
    'payroll' => [
        'paie',
    ],

    // L'employé n'a que son espace personnel, aucune section de gestion
    'employee' => [],

];
