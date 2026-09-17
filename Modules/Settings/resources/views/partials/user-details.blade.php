<div class="row">
    <div class="col-md-6">
        <!-- Informations Personnelles -->
        <div class="mb-4">
            <h6 class="mb-3"> Informations Personnelles</h6>
            <div class="row">
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Nom Complet:</small>
                    <div class="fw-semibold">{{ $user->name }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Identifiant / Username:</small>
                    <div class="fw-semibold text-primary">{{ $user->username ?? '-' }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Email:</small>
                    <div class="fw-semibold">{{ $user->email }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Téléphone:</small>
                    <div>{{ $user->phone ?? ($employee->phone ?? '-') }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Type / Profil:</small>
                    <div>
                        @php
                            $typeLabels = [
                                'company' => ['label' => 'Entreprise', 'color' => 'primary'],
                                'hr' => ['label' => 'RH', 'color' => 'success'],
                                'payroll' => ['label' => 'Paie', 'color' => 'info'],
                                'employee' => ['label' => 'Employé', 'color' => 'secondary']
                            ];
                            $typeInfo = $typeLabels[$user->type] ?? ['label' => 'Inconnu', 'color' => 'secondary'];
                        @endphp
                        <span class="badge bg-label-{{ $typeInfo['color'] }}">
                            {{ $typeInfo['label'] }}
                        </span>
                    </div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Statut:</small>
                    <div>
                        <span class="badge {{ $user->is_active ? 'bg-label-success' : 'bg-label-warning' }}">
                            {{ $user->is_active ? '✅ Actif' : '❌ Inactif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations d'Affectation -->
        <div class="mb-4">
            <h6 class="mb-3">🏢 Affectation Organisationnelle</h6>
            <div class="row">
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Matricule:</small>
                    <div class="fw-semibold text-dark">{{ $employee->employee_id ?? '-' }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Entreprise:</small>
                    <div class="fw-semibold">{{ $company->name ?? '-' }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Succursale / Branche:</small>
                    <div>{{ $employee->branch->name ?? $user->branch->name ?? '-' }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Département:</small>
                    <div>{{ $employee->department->name ?? $user->department->name ?? '-' }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Poste:</small>
                    <div id="previewDesignation">{{ $employee->designation->name ?? $user->designation->name ?? '-' }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Date d'embauche:</small>
                    <div>{{ !empty($employee->company_doj) ? \Carbon\Carbon::parse($employee->company_doj)->format('d/m/Y') : (!empty($employee->start_date) ? \Carbon\Carbon::parse($employee->start_date)->format('d/m/Y') : '-') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Informations Système -->
        <div class="mb-4">
            <h6 class="mb-3"> Informations Système</h6>
            <div class="row">

                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Créé par:</small>
                    <div>{{ $user->creator->name ?? 'Inconnu' }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Date de Création:</small>
                    <div>{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Dernière Modification:</small>
                    <div>{{ $user->updated_at->format('d/m/Y à H:i') }}</div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Dernière Connexion:</small>
                    <div>
                        @if($user->last_login)
                            {{ $user->last_login->format('d/m/Y à H:i') }}
                        @else
                            <span class="text-muted">Jamais connecté</span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 mb-2">
                    <small class="text-muted">Email Vérifié:</small>
                    <div>
                        @if($user->email_verified_at)
                            <span class="badge bg-label-success">✅ Vérifié</span>
                        @else
                            <span class="badge bg-label-warning">❌ Non vérifié</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions et Rôles -->
        <div class="mb-4">
            <h6 class="mb-3"> Permissions</h6>
            <div>
                @php
                    $permissions = [
                        'company' => [
                            'Gestion complète de l\'entreprise',
                            'Paramètres système',
                            'Gestion des utilisateurs',
                            'Accès à tous les modules'
                        ],
                        'hr' => [
                            'Gestion des employés',
                            'Congés et absences',
                            'Formations',
                            'Évaluations'
                        ],
                        'payroll' => [
                            'Gestion des salaires',
                            'Paies et bulletins',
                            'Déclarations sociales',
                            'Rapports financiers'
                        ],
                        'employee' => [
                            'Profil personnel',
                            'Demandes de congés',
                            'Bulletins de paie',
                            'Documents personnels'
                        ]
                    ];
                    $userPermissions = $permissions[$user->type] ?? [];
                @endphp

                @if(!empty($userPermissions))
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($userPermissions as $permission)
                            <span class="badge bg-label-secondary small">{{ $permission }}</span>
                        @endforeach
                    </div>
                @else
                    <span class="text-muted">Aucune permission définie</span>
                @endif
            </div>
        </div>
    </div>
</div>



<style>
    /* Badge improvements */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
    }

    .bg-label-primary {
        background-color: rgba(105, 110, 255, 0.1) !important;
        color: #253e87 !important;
    }

    .bg-label-success {
        background-color: rgba(40, 200, 72, 0.1) !important;
        color: #28c848 !important;
    }

    .bg-label-warning {
        background-color: rgba(255, 205, 7, 0.1) !important;
        color: #ffcd07 !important;
    }

    .bg-label-info {
        background-color: rgba(3, 195, 236, 0.1) !important;
        color: #03c3ec !important;
    }

    .bg-label-secondary {
        background-color: rgba(133, 146, 163, 0.1) !important;
        color: #8592a3 !important;
    }
</style>

<script>
    function resetPassword(userId) {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur ?')) {
            fetch(`/company/settings/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Erreur lors de la réinitialisation', 'error');
                });
        }
    }

    function sendWelcomeEmail(userId) {
        if (confirm('Êtes-vous sûr de vouloir envoyer un email de bienvenue à cet utilisateur ?')) {
            fetch(`/company/settings/users/${userId}/send-welcome-email`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Erreur lors de l\'envoi de l\'email', 'error');
                });
        }
    }

    function activateUser(userId) {
        toggleUserStatus(userId, true);
    }

    function deactivateUser(userId) {
        toggleUserStatus(userId, false);
    }

    function toggleUserStatus(userId, activate = null) {
        const action = activate === true ? 'activer' : (activate === false ? 'désactiver' : 'basculer le statut de');
        const confirmMessage = `Êtes-vous sûr de vouloir ${action} cet utilisateur ?`;

        if (confirm(confirmMessage)) {
            fetch(`/company/settings/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Recharger pour voir les changements
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Erreur lors de la mise à jour du statut', 'error');
                });
        }
    }

    function showNotification(message, type = 'info') {
        // Créer une notification toast
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

        // Ajouter au conteneur de toasts
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        toastContainer.appendChild(toast);

        // Afficher le toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Supprimer automatiquement après 5 secondes
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }
</script>