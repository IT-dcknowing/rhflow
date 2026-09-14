@extends('layouts.app')

@section('title', 'Types de Congés - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Types de Congés</h4>
                        <p class="text-muted mb-0">Gérez les différents types de congés disponibles</p>
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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLeaveTypeModal">
                            <i class="fas fa-plus me-1"></i>Nouveau Type
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des Types de Congés -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Types de Congés</h5>
                        <span class="badge bg-label-primary">{{ $leaveTypes->count() }} types</span>
                    </div>
                    <div class="card-body">
                        @if($leaveTypes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover border-top dataTable no-footer" id="leave-types-table">
                                    <thead>
                                        <tr>
                                            <th>Type de Congé</th>
                                            <th>Jours</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($leaveTypes as $leaveType)
                                            <tr>
                                                <td>
                                                    <h6 class="mb-0">{{ $leaveType->title }}</h6>
                                                </td>
                                                {{-- data-order : tri numerique sur le nombre de jours --}}
                                                <td data-order="{{ $leaveType->days }}">
                                                    <span class="badge bg-label-primary">{{ $leaveType->days }} jours</span>
                                                </td>
                                                {{-- data-order : DataTables trie sur la valeur brute, pas sur le libelle --}}
                                                <td data-order="{{ $leaveType->is_active ? 1 : 0 }}">
                                                    <span
                                                        class="badge {{ $leaveType->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                        {{ $leaveType->is_active ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            onclick="editLeaveType({{ $leaveType->id }}, '{{ addslashes($leaveType->title) }}', {{ $leaveType->days }}, {{ $leaveType->is_active ? 'true' : 'false' }})"
                                                            title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if(!$leaveType->is_active)
                                                            <button class="btn btn-sm btn-outline-success"
                                                                onclick="toggleLeaveType({{ $leaveType->id }}, '{{ addslashes($leaveType->title) }}', false)"
                                                                title="Activer">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline-warning"
                                                                onclick="toggleLeaveType({{ $leaveType->id }}, '{{ addslashes($leaveType->title) }}', true)"
                                                                title="Désactiver">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteLeaveType({{ $leaveType->id }}, '{{ addslashes($leaveType->title) }}')"
                                                            title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="avatar avatar-xl mb-3" style="width: 80px; height: 80px;">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="fas fa-calendar-times fa-32px"></i>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="text-muted">Aucun type de congé configuré</h5>
                                <p class="text-muted mb-4">Commencez par créer votre premier type de congé</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLeaveTypeModal">
                                    <i class="fas fa-plus me-1"></i>Créer un Type de Congé
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Création -->
    <div class="modal fade" id="createLeaveTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nouveau Type de Congé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('company.settings.leave-types.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du Type de Congé *</label>
                            <input type="text" class="form-control" name="title" required
                                placeholder="Ex: Congé Annuel, Congé Maladie...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre (en jours) *</label>
                            <input type="number" class="form-control" name="days" required min="1" max="365"
                                placeholder="Ex: 30">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'Édition -->
    <div class="modal fade" id="editLeaveTypeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Modifier le Type de Congé</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="editLeaveTypeForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du Type de Congé *</label>
                            <input type="text" class="form-control" name="title" id="editTitle" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre de Jours *</label>
                            <input type="number" class="form-control" name="days" id="editDays" required min="1" max="365">
                            <small class="text-muted">Nombre de jours alloués par an</small>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" name="is_active" value="0" id="editIsActiveHidden">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive"
                                    value="1">
                                <label class="form-check-label" for="editIsActive">
                                    Type de congé actif
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fonctions JavaScript
        function editLeaveType(id, title, days, isActive) {
            document.getElementById('editLeaveTypeForm').action = `{{ url('company/settings/leave-types') }}/${id}`;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDays').value = days;
            document.getElementById('editIsActive').checked = isActive;

            const modal = new bootstrap.Modal(document.getElementById('editLeaveTypeModal'));
            modal.show();
        }

        function deleteLeaveType(id, title) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le type de congé "${title}" ? Cette action est irréversible.`)) {
                // Créer un formulaire de suppression
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('company/settings/leave-types') }}/${id}`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

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

        function toggleLeaveType(id, title, isActive) {
            const action = isActive ? 'désactiver' : 'activer';
            const confirmMessage = `Êtes-vous sûr de vouloir ${action} le type de congé "${title}" ?`;

            if (confirm(confirmMessage)) {
                // Créer un formulaire pour la route toggle avec méthode PUT
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('company/settings/leave-types') }}/${id}/toggle`;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'PUT';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Validation des formulaires
        document.addEventListener('DOMContentLoaded', function () {
            // Validation du formulaire de création
            const createForm = document.querySelector('#createLeaveTypeModal form');
            if (createForm) {
                createForm.addEventListener('submit', function (e) {
                    const title = createForm.querySelector('input[name="title"]').value.trim();

                    if (title.length < 2) {
                        e.preventDefault();
                        alert('Le nom du type de congé doit contenir au moins 2 caractères');
                        return;
                    }
                });
            }

            // Validation du formulaire d'édition
            const editForm = document.querySelector('#editLeaveTypeForm');
            if (editForm) {
                editForm.addEventListener('submit', function (e) {
                    const title = editForm.querySelector('input[name="title"]').value.trim();
                    const days = editForm.querySelector('input[name="days"]').value;

                    if (title.length < 2) {
                        e.preventDefault();
                        alert('Le nom du type de congé doit contenir au moins 2 caractères');
                        return;
                    }

                    if (days < 1 || days > 365) {
                        e.preventDefault();
                        alert('Le nombre de jours doit être entre 1 et 365');
                        return;
                    }
                });
            }

            // Empêcher la fermeture automatique du dropdown lors du survol des cartes
            const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('shown.bs.dropdown', function () {
                    // Désactiver la fermeture automatique au survol
                    const dropdown = bootstrap.Dropdown.getInstance(this);
                    if (dropdown) {
                        // Le dropdown restera ouvert jusqu'à ce qu'on clique ailleurs
                        this.setAttribute('data-bs-auto-close', 'outside');
                    }
                });

                // Empêcher la fermeture lors du clic sur les items du dropdown
                const dropdownMenu = this.nextElementSibling;
                if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                    dropdownMenu.addEventListener('click', function (e) {
                        e.stopPropagation(); // Empêche la fermeture du dropdown
                    });
                }
            });

            // Fermer manuellement les dropdowns UNIQUEMENT quand on clique sur le bouton ou sur un autre dropdown
            document.addEventListener('click', function (e) {
                // Si on clique sur un autre bouton dropdown, fermer les autres
                if (e.target.closest('[data-bs-toggle="dropdown"]')) {
                    const clickedDropdown = e.target.closest('[data-bs-toggle="dropdown"]');
                    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
                    openDropdowns.forEach(dropdown => {
                        const toggle = dropdown.previousElementSibling;
                        if (toggle && toggle !== clickedDropdown) {
                            const dropdownInstance = bootstrap.Dropdown.getInstance(toggle);
                            if (dropdownInstance) {
                                dropdownInstance.hide();
                            }
                        }
                    });
                }
                // Si on clique en dehors de TOUS les dropdowns, les fermer
                else if (!e.target.closest('.dropdown')) {
                    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
                    openDropdowns.forEach(dropdown => {
                        const toggle = dropdown.previousElementSibling;
                        if (toggle) {
                            const dropdownInstance = bootstrap.Dropdown.getInstance(toggle);
                            if (dropdownInstance) {
                                dropdownInstance.hide();
                            }
                        }
                    });
                }
            });

            // Empêcher la fermeture lors du survol des cartes
            document.addEventListener('mouseover', function (e) {
                // Ne rien faire lors du survol, laisser le dropdown ouvert
                return;
            });
        });
    </script>

    <style>
        /* Card styling */
        .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 12px;
            position: relative;
            overflow: visible !important;
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

        /* Progress bars */
        .progress {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
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

        /* Empty state styling */
        .empty-state .avatar-initial {
            background-color: rgba(133, 146, 163, 0.1) !important;
            color: #8592a3 !important;
        }

        /* Dropdown menu fixes - Simplified approach */
        .dropdown-menu {
            z-index: 1050 !important;
            position: absolute !important;
        }

        /* Ensure cards and containers don't clip dropdowns */
        .card-body {
            overflow: visible !important;
        }

        /* Ensure grid containers don't clip content */
        .row {
            overflow: visible !important;
        }

        .col-xl-4,
        .col-lg-6 {
            overflow: visible !important;
        }

        /* Modals should be above everything else */
        .modal {
            z-index: 1060 !important;
        }

        .modal-backdrop {
            z-index: 1055 !important;
        }

        .modal-dialog {
            z-index: 1065 !important;
        }

        .modal-content {
            z-index: 1070 !important;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
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
@endsection

@push('scripts')
    <script>
        // Tableau en DataTable (colonne Actions non triable / non filtrable)
        $(function () {
            'use strict';

            @if($leaveTypes->count() > 0)
                $('#leave-types-table').DataTable({
                    processing: true,
                    order: [[0, 'asc']],
                    columnDefs: [
                        { targets: -1, orderable: false, searchable: false }
                    ],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
                    },
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
                });
            @endif
                                });
    </script>
@endpush