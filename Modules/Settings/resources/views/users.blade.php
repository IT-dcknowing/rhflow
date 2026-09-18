@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"> Gestion des Utilisateurs</h4>
                    <p class="text-muted mb-0">Gérez les utilisateurs de votre entreprise et leurs permissions</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.settings.config') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                    <a href="{{ route('company.settings.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvel Utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des Utilisateurs -->
    <x-kpi-grid title="Statistiques Globales des Utilisateurs" badge="Vue d'ensemble">
        <x-kpi icon="fas fa-users" color="primary" label="Total" sublabel="Tous les utilisateurs"
            :value="$stats['total']" />

        <x-kpi icon="fas fa-user-check" color="success" label="Actifs" sublabel="Comptes connectés"
            :value="$stats['active']" />

        <x-kpi icon="fas fa-user-times" color="warning" label="Inactifs" sublabel="Comptes désactivés"
            :value="$stats['inactive']" />

        <x-kpi icon="fas fa-user-shield" color="info" label="Gestionnaires" sublabel="Comptes RH & Paie"
            :value="$stats['hr'] + $stats['payroll']" />
    </x-kpi-grid>

    <!-- Utilisateurs par Profil -->
    <x-kpi-grid title="Utilisateurs par Profil" badge="Profils d'accès">
        <x-kpi icon="fas fa-building" color="primary" label="Entreprise" sublabel="Administrateurs système"
            :value="$stats['company']" />

        <x-kpi icon="fas fa-user-tie" color="success" label="Ressources Humaines" sublabel="Gestionnaires RH"
            :value="$stats['hr']" />

        <x-kpi icon="fas fa-file-invoice-dollar" color="info" label="Gestion Paie" sublabel="Gestionnaires Paie"
            :value="$stats['payroll']" />

        <x-kpi icon="fas fa-id-badge" color="secondary" label="Employés" sublabel="Comptes Collaborateurs"
            :value="$stats['employee']" />
    </x-kpi-grid>

    <!-- Liste des Utilisateurs -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- En-tête avec Titre et Boutons d'Action (Import, Export, Ajout, Actualisation) -->
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Liste des Utilisateurs</h5>
                        <small class="text-muted">
                            Total : <strong>{{ $stats['total'] }}</strong> utilisateur{{ $stats['total'] > 1 ? 's' : '' }}
                            @if(request()->hasAny(['search', 'type', 'status', 'branch_id', 'department_id']))
                                (filtré : <strong>{{ $users->total() }}</strong> résultat{{ $users->total() > 1 ? 's' : '' }})
                            @endif
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportUsers()" title="Exporter en Excel">
                            <i class="fas fa-download me-1"></i>Exporter
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="importUsers()" title="Importer depuis un fichier Excel">
                            <i class="fas fa-upload me-1"></i>Importer
                        </button>
                        <a href="{{ route('company.settings.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Nouvel Utilisateur
                        </a>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshUsers()" title="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Barre de Filtres au-dessus du tableau -->
                <div class="card-body border-bottom bg-light bg-opacity-25 py-3">
                    <form method="GET" action="{{ route('company.settings.users.index') }}" id="filterUsersForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small mb-1 fw-semibold text-muted">Recherche</label>
                                <div class="input-group input-group-merge input-group-sm">
                                    <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" class="form-control" name="search" value="{{ request('search') }}"
                                           placeholder="Nom, email, identifiant...">
                                </div>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label class="form-label small mb-1 fw-semibold text-muted">Profil / Rôle</label>
                                <select class="form-select form-select-sm" name="type">
                                    <option value="">Tous les profils</option>
                                    <option value="company" {{ request('type') === 'company' ? 'selected' : '' }}>🏢 Entreprise</option>
                                    <option value="hr" {{ request('type') === 'hr' ? 'selected' : '' }}>👥 RH</option>
                                    <option value="payroll" {{ request('type') === 'payroll' ? 'selected' : '' }}>💰 Paie</option>
                                    <option value="employee" {{ request('type') === 'employee' ? 'selected' : '' }}>👨‍💼 Employé</option>
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label class="form-label small mb-1 fw-semibold text-muted">Statut</label>
                                <select class="form-select form-select-sm" name="status">
                                    <option value="">Tous les statuts</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>✅ Actif</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>❌ Inactif</option>
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label class="form-label small mb-1 fw-semibold text-muted">Succursale</label>
                                <select class="form-select form-select-sm" name="branch_id">
                                    <option value="">Toutes succursales</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label class="form-label small mb-1 fw-semibold text-muted">Département</label>
                                <select class="form-select form-select-sm" name="department_id">
                                    <option value="">Tous départements</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-1 col-md-4 col-sm-12 d-flex gap-1">
                                <button type="submit" class="btn btn-sm btn-primary w-100" title="Filtrer">
                                    <i class="fas fa-filter"></i>
                                </button>
                                @if(request()->hasAny(['search', 'type', 'status', 'branch_id', 'department_id']))
                                    <a href="{{ route('company.settings.users.index') }}" class="btn btn-sm btn-outline-secondary" title="Réinitialiser les filtres">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Type</th>
                                        <th>Téléphone</th>
                                        <th>Affectation</th>
                                        <th>Statut</th>
                                        <th class="text-center" style="width: 110px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $userItem)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                    <div class="avatar-initial bg-label-primary rounded">
                                                        <i class="fas fa-user fa-20px"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">
                                                        {{ $userItem->name }}
                                                        @if($userItem->id === $user->id)
                                                            <span class="badge bg-label-success ms-1">Vous</span>
                                                        @endif
                                                    </h6>
                                                    @if($userItem->userEmployee && $userItem->userEmployee->employee_id)
                                                        <small class="text-muted fw-semibold">{{ \Auth::user()->employeeIdFormat($userItem->userEmployee->employee_id) }}</small>
                                                    @else
                                                        <small class="text-muted">{{ $userItem->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $typeLabels = [
                                                    'company' => ['label' => 'Entreprise', 'color' => 'primary'],
                                                    'hr' => ['label' => 'RH', 'color' => 'success'],
                                                    'payroll' => ['label' => 'Paie', 'color' => 'info'],
                                                    'employee' => ['label' => 'Employé', 'color' => 'secondary']
                                                ];
                                                $typeInfo = $typeLabels[$userItem->type] ?? ['label' => 'Inconnu', 'color' => 'secondary'];
                                            @endphp
                                            <span class="badge bg-label-{{ $typeInfo['color'] }}">
                                                {{ $typeInfo['label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($userItem->type === 'company')
                                                {{ $company->phone ?? '-' }}
                                            @else
                                                {{ $userItem->userEmployee->phone ?? ($userItem->phone ?? '-') }}
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                @if($userItem->type === 'company')
                                                    <span class="text-muted">-</span>
                                                @else
                                                    @if(empty($userItem->company_id))
                                                        {{-- Sans company_id, le compte se connecte mais ne voit aucune donnée.
                                                             Ouvrir sa fiche et l'enregistrer rétablit le rattachement. --}}
                                                        <a href="{{ route('company.settings.users.edit', $userItem->id) }}"
                                                            class="badge bg-label-danger text-decoration-none d-block mb-1"
                                                            title="Ce compte n'est rattaché à aucune entreprise : ses pages resteront vides. Cliquez pour ouvrir sa fiche, puis enregistrez-la pour le rattacher.">
                                                            <i class="fas fa-unlink me-1"></i>Non rattaché
                                                        </a>
                                                    @else
                                                        <small class="text-success d-block" title="Compte rattaché à {{ $company->name }}">
                                                            <i class="fas fa-building me-1"></i>{{ $company->name }}
                                                        </small>
                                                    @endif
                                                    <small class="text-muted d-block">{{ $userItem->userEmployee->branch->name ?? '-' }}</small>
                                                    <small class="text-muted d-block">{{ $userItem->userEmployee->department->name ?? '-' }}</small>
                                                    <small class="text-muted d-block">{{ $userItem->userEmployee->designation->name ?? '-' }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $userItem->is_active ? 'bg-label-success' : 'bg-label-danger' }}"
                                                data-user-status-badge="{{ $userItem->id }}"
                                                style="cursor:pointer" title="Cliquer pour changer le statut"
                                                onclick="toggleUserStatus({{ $userItem->id }}, event)">
                                                {{ $userItem->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <button type="button"
                                                        class="btn btn-icon btn-sm btn-label-info"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Voir détails"
                                                        onclick="viewUserDetails({{ $userItem->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ route('company.settings.users.edit', $userItem) }}"
                                                   class="btn btn-icon btn-sm btn-label-warning"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($userItem->id !== $user->id)
                                                <button type="button"
                                                        class="btn btn-icon btn-sm btn-label-danger"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Supprimer"
                                                        onclick="deleteUser({{ $userItem->id }}, '{{ addslashes($userItem->name) }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination du tableau -->
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                            <div>
                                <small class="text-muted">
                                    Affichage de <strong>{{ $users->firstItem() ?? 0 }}</strong> à <strong>{{ $users->lastItem() ?? 0 }}</strong>
                                    sur <strong>{{ $users->total() }}</strong> utilisateur{{ $users->total() > 1 ? 's' : '' }}
                                </small>
                            </div>
                            <div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    @else
                        <!-- État Vide -->
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl mb-3 mx-auto" style="width: 100px; height: 100px;">
                                <div class="avatar-initial bg-label-secondary rounded">
                                    <i class="fas fa-users fa-40px"></i>
                                </div>
                            </div>
                            @if(request()->hasAny(['search', 'type', 'status', 'branch_id', 'department_id']))
                                <h5 class="mb-1">Aucun utilisateur trouvé</h5>
                                <p class="text-muted mb-4">Aucun utilisateur ne correspond à vos critères de recherche.</p>
                                <a href="{{ route('company.settings.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i>Réinitialiser les filtres
                                </a>
                            @else
                                <h5 class="mb-1">Aucun utilisateur</h5>
                                <p class="text-muted mb-4">Commencez par créer votre premier utilisateur</p>
                                <a href="{{ route('company.settings.users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Créer un Utilisateur
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Détails Utilisateur -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Détails de l'Utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Card hover effects */
    .card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: none;
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        background: linear-gradient(135deg, rgba(105, 110, 255, 0.05) 0%, rgba(3, 195, 236, 0.05) 100%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 12px 12px 0 0 !important;
    }

    .card-header h5 {
        color: #566a7f;
        font-weight: 600;
    }

    /* Table improvements */
    .table th {
        border-top: none;
        font-weight: 600;
        color: #566a7f;
        background-color: rgba(105, 110, 255, 0.05);
    }

    .table td {
        vertical-align: middle;
        border-color: rgba(0, 0, 0, 0.06);
    }

    .table tbody tr:hover {
        background-color: rgba(105, 110, 255, 0.02);
    }

    /* Avatar improvements */
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: white;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

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

    /* Switch improvements */
    .form-check-input:checked {
        background-color: #696cff;
        border-color: #696cff;
    }

    .form-check-input:disabled {
        opacity: 0.5;
    }

    /* Icon button improvements */
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .btn-label-info {
        color: #03c3ec;
        background-color: rgba(3, 195, 236, 0.12);
        border: none;
    }
    .btn-label-info:hover {
        color: #fff;
        background-color: #03c3ec;
    }
    .btn-label-warning {
        color: #ffab00;
        background-color: rgba(255, 171, 0, 0.12);
        border: none;
    }
    .btn-label-warning:hover {
        color: #fff;
        background-color: #ffab00;
    }
    .btn-label-danger {
        color: #ff3e1d;
        background-color: rgba(255, 62, 29, 0.12);
        border: none;
    }
    .btn-label-danger:hover {
        color: #fff;
        background-color: #ff3e1d;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .btn {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        h3 {
            font-size: 1.5rem;
        }

        h5 {
            font-size: 1.125rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualiser les statistiques en temps réel
        updateStats();
    });

    function updateStats() {
        // Cette fonction pourrait mettre à jour les statistiques en temps réel
        // Pour l'instant, elle est vide car l'implémentation nécessiterait plus de JavaScript
    }

    function refreshUsers() {
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        setTimeout(() => {
            location.reload();
        }, 1000);
    }

    function toggleUserStatus(userId, event) {
        // Afficher un indicateur de chargement
        const checkbox = event.target;
        const originalChecked = checkbox.checked;
        checkbox.disabled = true;

        fetch(`{{ url('company/settings/users') }}/${userId}/toggle-status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour l'état du checkbox
                checkbox.checked = data.is_active;

                // Mettre le badge en face en accord avec le nouveau statut
                const badge = document.querySelector(`[data-user-status-badge="${userId}"]`);
                if (badge) {
                    badge.textContent = data.is_active ? '✅ Actif' : '❌ Inactif';
                    badge.classList.toggle('bg-label-success', data.is_active);
                    badge.classList.toggle('bg-label-warning', !data.is_active);
                }

                // Afficher une notification
                showNotification(data.message, 'success');
            } else {
                // Remettre l'état original en cas d'erreur
                checkbox.checked = originalChecked;
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            // Remettre l'état original en cas d'erreur
            checkbox.checked = originalChecked;
            showNotification('Erreur lors de la mise à jour du statut', 'error');
        })
        .finally(() => {
            checkbox.disabled = false;
        });
    }

    function viewUserDetails(userId) {
        // Charger les détails de l'utilisateur via AJAX
        fetch(`{{ url('company/settings/users') }}/${userId}/details`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remplir le modal avec les détails
                document.getElementById('userDetailsContent').innerHTML = data.html;

                // Afficher le modal
                const modal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
                modal.show();
            } else {
                showNotification('Erreur lors du chargement des détails', 'error');
            }
        })
        .catch(error => {
            showNotification('Erreur lors du chargement des détails', 'error');
        });
    }

    function deleteUser(userId, userName) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ? Cette action est irréversible.`)) {
            // Créer un formulaire de suppression
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('company/settings/users') }}/${userId}`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function exportUsers() {
        // Créer un lien de téléchargement en transmettant les filtres en cours
        const currentQuery = window.location.search;
        const link = document.createElement('a');
        link.href = `{{ route('company.settings.users.export') }}` + (currentQuery ? currentQuery : '');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showNotification('Export des utilisateurs en cours...', 'info');
    }

    function importUsers() {
        // Créer un modal d'import
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"> Import d'Utilisateurs</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('company.settings.users.import') }}" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="mb-3">
                                <label class="form-label">Fichier Excel</label>
                                <input type="file" class="form-control" name="file" accept=".xlsx,.xls" required>
                                <small class="text-muted">Formats acceptés: .xlsx, .xls</small>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="skip_duplicates" id="skipDuplicates" value="1">
                                    <label class="form-check-label" for="skipDuplicates">
                                        Ignorer les doublons
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Importer</button>
                        </form>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();

        modal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(modal);
        });
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
@endpush

