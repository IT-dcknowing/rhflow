<?php

/*
|--------------------------------------------------------------------------
| Abonnements : alerte et blocage
|--------------------------------------------------------------------------
|
| Pilote le rappel d'échéance et le blocage d'accès quand l'abonnement
| n'est pas renouvelé. La date de référence est users.plan_expire_date,
| portée par le compte propriétaire de l'entreprise. Les comptes RH, Paie
| et Employé héritent de l'échéance de leur entreprise.
|
*/

return [

    /*
     | Nombre de jours avant l'échéance à partir duquel le rappel s'affiche.
     | Mettre 0 pour désactiver complètement le rappel.
     */
    'jours_alerte' => 15,

    /*
     | Blocage de l'accès une fois l'abonnement échu.
     |
     | ATTENTION : passer cette valeur à true coupe immédiatement l'accès à
     | toutes les entreprises dont plan_expire_date est dépassée. Vérifiez
     | d'abord combien sont concernées :
     |
     |   SELECT COUNT(*) FROM users
     |   WHERE type = 'company'
     |     AND plan_expire_date IS NOT NULL
     |     AND plan_expire_date < CURDATE();
     |
     | Un compte sans plan_expire_date n'est jamais bloqué.
     */
    'blocage_actif' => env('ABONNEMENT_BLOCAGE', false),

    /*
     | Jours de tolérance après l'échéance avant que le blocage s'applique.
     | Exemple : 7 laisse une semaine pour régulariser.
     */
    'jours_grace' => 0,

    /*
     | Fréquence du rappel, en heures. Le popup ne se réaffiche pas avant
     | ce délai pour le même utilisateur, même s'il change de page.
     */
    'frequence_rappel_heures' => 24,

    /*
     | Routes toujours accessibles, même abonnement échu : renouvellement,
     | paiement, déconnexion, pages publiques. Comparaison par préfixe sur
     | le nom de la route.
     */
    'routes_autorisees' => [
        'login',
        'logout',
        'password.',
        'landingpage',
        'simulateur',
        'contact',
        'register',
        'payment.',
        'maintenance',
        'super-admin.',
        'company.plan.',
        'company.packs.',
        'notifications.',
    ],

];
