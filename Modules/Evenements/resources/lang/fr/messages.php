<?php

return [
    // Général
    'back' => 'Retour',
    'save' => 'Enregistrer',
    'edit' => 'Modifier',
    'delete' => 'Supprimer',
    'cancel' => 'Annuler',
    'confirm' => 'Confirmer',
    'actions' => 'Actions',
    'search' => 'Rechercher...',
    'no_results' => 'Aucun résultat trouvé',
    'created_at' => 'Créé le',
    'updated_at' => 'Mis à jour le',
    'status' => 'Statut',
    'active' => 'Actif',
    'inactive' => 'Inactif',
    'all' => 'Tous',
    'select' => 'Sélectionner...',
    
    // Statuts des transferts
    'statuses' => [
        'pending' => 'En attente',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'completed' => 'Terminé',
    ],
    
    // Types de transfert
    'transfer_types' => [
        'department' => 'Changement de département',
        'location' => 'Changement de localisation',
        'position' => 'Changement de poste',
    ],
    
    // Messages de succès
    'success' => [
        'created' => 'L\'enregistrement a été créé avec succès.',
        'updated' => 'L\'enregistrement a été mis à jour avec succès.',
        'deleted' => 'L\'enregistrement a été supprimé avec succès.',
        'approved' => 'Le transfert a été approuvé avec succès.',
        'rejected' => 'Le transfert a été rejeté avec succès.',
        'completed' => 'Le transfert a été marqué comme terminé.',
    ],
    
    // Messages d'erreur
    'error' => [
        'not_found' => 'L\'enregistrement demandé n\'a pas été trouvé.',
        'delete' => 'Une erreur est survenue lors de la suppression de l\'enregistrement.',
        'update' => 'Une erreur est survenue lors de la mise à jour de l\'enregistrement.',
        'create' => 'Une erreur est survenue lors de la création de l\'enregistrement.',
        'approve' => 'Une erreur est survenue lors de l\'approbation du transfert.',
        'reject' => 'Une erreur est survenue lors du rejet du transfert.',
        'complete' => 'Une erreur est survenue lors du marquage du transfert comme terminé.',
    ],
    
    // Messages de confirmation
    'confirm' => [
        'delete' => 'Êtes-vous sûr de vouloir supprimer cet enregistrement ?',
        'approve' => 'Êtes-vous sûr de vouloir approuver ce transfert ?',
        'reject' => 'Êtes-vous sûr de vouloir rejeter ce transfert ?',
        'complete' => 'Êtes-vous sûr de vouloir marquer ce transfert comme terminé ?',
    ],
    
    // Champs du formulaire de transfert
    'transfer' => [
        'employee' => 'Employé',
        'transfer_type' => 'Type de transfert',
        'transfer_date' => 'Date du transfert',
        'effective_date' => 'Date d\'effet',
        'reason' => 'Raison du transfert',
        'notes' => 'Notes supplémentaires',
        'status' => 'Statut',
        'from_department' => 'Département d\'origine',
        'to_department' => 'Nouveau département',
        'from_location' => 'Localisation d\'origine',
        'to_location' => 'Nouvelle localisation',
        'from_position' => 'Poste actuel',
        'to_position' => 'Nouveau poste',
        'reject_reason' => 'Raison du rejet',
    ],
    
    // Boutons d'action
    'buttons' => [
        'create' => 'Créer',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'save' => 'Enregistrer',
        'cancel' => 'Annuler',
        'back' => 'Retour',
        'approve' => 'Approuver',
        'reject' => 'Rejeter',
        'complete' => 'Terminer',
        'print' => 'Imprimer',
        'download' => 'Télécharger',
        'upload' => 'Téléverser',
    ],
    
    // Titres des pages
    'titles' => [
        'transfers' => 'Gestion des transferts',
        'create_transfer' => 'Créer un transfert',
        'edit_transfer' => 'Modifier le transfert',
        'view_transfer' => 'Détails du transfert',
        'transfer_list' => 'Liste des transferts',
    ],
    
    // Messages de validation
    'validation' => [
        'required' => 'Le champ :attribute est obligatoire.',
        'date' => 'Le champ :attribute doit être une date valide.',
        'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale à :date.',
        'different' => 'Les champs :attribute et :other doivent être différents.',
        'min' => [
            'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        ],
    ],
    
    // Libellés pour les attributs
    'attributes' => [
        'employee_id' => 'employé',
        'transfer_type' => 'type de transfert',
        'transfer_date' => 'date du transfert',
        'effective_date' => 'date d\'effet',
        'reason' => 'raison',
        'notes' => 'notes',
        'status' => 'statut',
        'from_department_id' => 'département d\'origine',
        'to_department_id' => 'nouveau département',
        'from_location_id' => 'localisation d\'origine',
        'to_location_id' => 'nouvelle localisation',
        'from_position' => 'poste actuel',
        'to_position' => 'nouveau poste',
    ],
];
