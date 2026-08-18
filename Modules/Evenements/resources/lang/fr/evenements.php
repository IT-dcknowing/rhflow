<?php

return [
    // Titres des pages
    'dashboard' => 'Tableau de bord',
    'transfers' => 'Transferts',
    'announcements' => 'Annonces',
    'events' => 'Événements',
    'meetings' => 'Réunions',
    'awards' => 'Récompenses',
    'promotions' => 'Promotions',
    'reports' => 'Rapports',
    'settings' => 'Paramètres',
    
    // Boutons d'action
    'add_new' => 'Ajouter un nouveau',
    'edit_item' => 'Modifier :item',
    'delete_item' => 'Supprimer :item',
    'view_details' => 'Voir les détails',
    'back_to_list' => 'Retour à la liste',
    'save_changes' => 'Enregistrer les modifications',
    'cancel' => 'Annuler',
    'confirm' => 'Confirmer',
    'approve' => 'Approuver',
    'reject' => 'Rejeter',
    'complete' => 'Terminer',
    'print' => 'Imprimer',
    'export' => 'Exporter',
    'import' => 'Importer',
    'search' => 'Rechercher...',
    
    // Messages de statut
    'status' => [
        'draft' => 'Brouillon',
        'pending' => 'En attente',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'completed' => 'Terminé',
        'cancelled' => 'Annulé',
    ],
    
    // Messages de confirmation
    'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.',
    'confirm_approve' => 'Êtes-vous sûr de vouloir approuver cet élément ?',
    'confirm_reject' => 'Êtes-vous sûr de vouloir rejeter cet élément ?',
    'confirm_complete' => 'Êtes-vous sûr de vouloir marquer cet élément comme terminé ?',
    'no_items_found' => 'Aucun élément trouvé.',
    
    // Messages de succès
    'success' => [
        'created' => 'L\'élément a été créé avec succès.',
        'updated' => 'L\'élément a été mis à jour avec succès.',
        'deleted' => 'L\'élément a été supprimé avec succès.',
        'approved' => 'L\'élément a été approuvé avec succès.',
        'rejected' => 'L\'élément a été rejeté avec succès.',
        'completed' => 'L\'élément a été marqué comme terminé avec succès.',
        'imported' => 'Les données ont été importées avec succès.',
        'exported' => 'Les données ont été exportées avec succès.',
    ],
    
    // Messages d'erreur
    'error' => [
        'not_found' => 'L\'élément demandé n\'a pas été trouvé.',
        'create' => 'Une erreur est survenue lors de la création de l\'élément.',
        'update' => 'Une erreur est survenue lors de la mise à jour de l\'élément.',
        'delete' => 'Une erreur est survenue lors de la suppression de l\'élément.',
        'approve' => 'Une erreur est survenue lors de l\'approbation de l\'élément.',
        'reject' => 'Une erreur est survenue lors du rejet de l\'élément.',
        'complete' => 'Une erreur est survenue lors du marquage de l\'élément comme terminé.',
        'import' => 'Une erreur est survenue lors de l\'importation des données.',
        'export' => 'Une erreur est survenue lors de l\'exportation des données.',
        'validation' => 'Veuillez corriger les erreurs de validation avant de continuer.',
        'unauthorized' => 'Vous n\'êtes pas autorisé à effectuer cette action.',
        'forbidden' => 'Accès interdit.',
    ],
    
    // Libellés des champs
    'fields' => [
        'id' => 'ID',
        'name' => 'Nom',
        'title' => 'Titre',
        'description' => 'Description',
        'start_date' => 'Date de début',
        'end_date' => 'Date de fin',
        'status' => 'Statut',
        'created_at' => 'Créé le',
        'updated_at' => 'Mis à jour le',
        'created_by' => 'Créé par',
        'updated_by' => 'Mis à jour par',
        'actions' => 'Actions',
        'select_option' => 'Sélectionner une option',
        'select_file' => 'Sélectionner un fichier',
        'browse' => 'Parcourir',
        'no_file_chosen' => 'Aucun fichier choisi',
    ],
    
    // Types de transfert
    'transfer_types' => [
        'department' => 'Changement de département',
        'location' => 'Changement de localisation',
        'position' => 'Changement de poste',
    ],
    
    // Types d'événements
    'event_types' => [
        'meeting' => 'Réunion',
        'training' => 'Formation',
        'holiday' => 'Jour férié',
        'leave' => 'Congé',
        'other' => 'Autre',
    ],
    
    // Types de récompenses
    'award_types' => [
        'employee_of_month' => 'Employé du mois',
        'excellence' => 'Prix d\'excellence',
        'innovation' => 'Prix d\'innovation',
        'service' => 'Prix d\'ancienneté',
        'other' => 'Autre',
    ],
    
    // Types de promotions
    'promotion_types' => [
        'grade' => 'Changement de grade',
        'salary' => 'Augmentation de salaire',
        'responsibility' => 'Élargissement des responsabilités',
        'title' => 'Changement de titre de poste',
        'other' => 'Autre',
    ],
    
    // Filtres
    'filters' => [
        'all' => 'Tous',
        'active' => 'Actifs',
        'inactive' => 'Inactifs',
        'pending' => 'En attente',
        'approved' => 'Approuvés',
        'rejected' => 'Rejetés',
        'completed' => 'Terminés',
        'this_month' => 'Ce mois-ci',
        'last_month' => 'Le mois dernier',
        'this_year' => 'Cette année',
        'last_year' => 'L\'année dernière',
        'custom_range' => 'Période personnalisée',
    ],
    
    // Pagination
    'pagination' => [
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'showing' => 'Affichage de',
        'to' => 'à',
        'of' => 'sur',
        'results' => 'résultats',
        'no_results' => 'Aucun résultat trouvé',
    ],
    
    // Téléchargement de fichiers
    'file_upload' => [
        'max_size' => 'La taille maximale du fichier est de :size Mo.',
        'allowed_types' => 'Types de fichiers autorisés : :types',
        'drag_and_drop' => 'Glissez et déposez un fichier ici ou cliquez pour le sélectionner',
        'browse' => 'Parcourir',
        'remove' => 'Supprimer',
    ],
    
    // Calendrier
    'calendar' => [
        'today' => 'Aujourd\'hui',
        'month' => 'Mois',
        'week' => 'Semaine',
        'day' => 'Jour',
        'list' => 'Liste',
        'no_events' => 'Aucun événement à afficher',
        'all_day' => 'Toute la journée',
        'add_event' => 'Ajouter un événement',
        'edit_event' => 'Modifier l\'événement',
        'delete_event' => 'Supprimer l\'événement',
        'event_details' => 'Détails de l\'événement',
    ],
    
    // Widgets du tableau de bord
    'widgets' => [
        'upcoming_events' => 'Événements à venir',
        'pending_approvals' => 'Approbations en attente',
        'recent_activities' => 'Activités récentes',
        'quick_actions' => 'Actions rapides',
        'statistics' => 'Statistiques',
        'view_all' => 'Voir tout',
    ],
];
