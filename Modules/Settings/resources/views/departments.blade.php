@extends('layouts.app')

@section('title', 'Services - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Services</h4>
                        <p class="text-muted mb-0">Gérez les différents services de votre entreprise</p>
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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                            <i class="fas fa-plus me-1"></i>Nouveau Service
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <x-kpi-grid>
            <x-kpi icon="fas fa-sitemap" color="info" label="Total" sublabel="Services"
                :value="$stats['total_departments']" />

            <x-kpi icon="fas fa-check-circle" color="success" label="Actifs" sublabel="Services"
                :value="$stats['active_departments']" />

            <x-kpi icon="fas fa-ban" color="warning" label="Inactifs" sublabel="Services"
                :value="$stats['inactive_departments']" />

            <x-kpi icon="fas fa-user-tie" color="primary" label="Postes" sublabel="Au total"
                :value="$company->designations()->count()" />
        </x-kpi-grid>

        <!-- Liste des Services -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Services </h5>
                        <span class="badge bg-label-primary">{{ $stats['total_departments'] }} services</span>
                    </div>
                    <div class="card-body">
                        @if($departments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover border-top dataTable no-footer" id="departments-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Service</th>
                                            <th>Site</th>
                                            <th>Manager</th>
                                            <th>Postes</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departments as $department)
                                            <tr>
                                                <td><span class="badge bg-label-secondary">{{ $department->code }}</span></td>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-0">{{ $department->name }}</h6>
                                                        @if($department->description)
                                                            <small class="text-muted">{{ $department->description }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($department->branch)
                                                        <small><i
                                                                class="fas fa-building me-1 text-muted"></i>{{ $department->branch->name }}</small>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($department->manager)
                                                        <small><i
                                                                class="fas fa-user-tie me-1 text-muted"></i>{{ $department->manager }}</small>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td><span
                                                        class="badge bg-label-warning">{{ $department->designations->count() }}</span>
                                                </td>
                                                {{-- data-order : DataTables trie sur la valeur brute, pas sur le libelle --}}
                                                <td data-order="{{ $department->is_active ? 1 : 0 }}">
                                                    <span
                                                        class="badge {{ $department->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                        {{ $department->is_active ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-icon btn-sm btn-label-warning"
                                                            onclick="editDepartment({{ $department->id }})" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if(!$department->is_active)
                                                            <button class="btn btn-icon btn-sm btn-label-success"
                                                                onclick="toggleDepartment({{ $department->id }}, '{{ addslashes($department->name) }}', false)"
                                                                title="Activer">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-icon btn-sm btn-label-secondary"
                                                                onclick="toggleDepartment({{ $department->id }}, '{{ addslashes($department->name) }}', true)"
                                                                title="Désactiver">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-icon btn-sm btn-label-danger"
                                                            onclick="deleteDepartment({{ $department->id }}, '{{ addslashes($department->name) }}')"
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
                                <div class="mb-3 d-flex justify-content-center align-items-center">
                                    <div class="avatar avatar-xl" style="width: 80px; height: 80px;">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="fas fa-sitemap fa-32px"></i>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="text-muted">Aucun service configuré</h5>
                                <p class="text-muted mb-4">Commencez par créer votre premier service</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                                    <i class="fas fa-plus me-1"></i>Créer un Service
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Création -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">➕ Nouveau Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('company.settings.departments.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du Service <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="nameService" required
                                    placeholder="Ex: Ressources Humaines, Informatique...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control departmentCode" name="code" required
                                    placeholder="SRV-XXXX" id="departmentCode" readonly>
                                <small class="text-muted">Généré automatiquement</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"
                                placeholder="Description du service..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Succursale<span class="text-danger">*</span></label>
                                <select class="form-select" name="branch_id" required>
                                    <option value="">Sélectionner une succursale</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manager du Service <span class="text-danger">*</span></label>
                                @if($companyUsers->count() > 0)
                                    {{-- Une seule liste, quel que soit le nombre d'utilisateurs. Le markup était
                                         dupliqué selon un seuil (« > 10 »), avec deux rendus différents pour le
                                         même champ. Seules LIMITE_MANAGERS entrées sont proposées à la fois,
                                         les autres s'atteignent par la barre de recherche (voir initSelect2). --}}
                                    <select class="form-select select2" name="manager_id" required
                                        data-placeholder="Sélectionner un manager">
                                        <option value="">Sélectionner un manager</option>
                                        @foreach($companyUsers as $user)
                                            {{-- Parenthèses seulement si l'email existe, sinon le libellé se termine par « () ». --}}
                                            <option value="{{ $user->id }}">{{ $user->name }}@if($user->email) ({{ $user->email }})@endif</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Aucun utilisateur trouvé. Créez d'abord des utilisateurs dans votre entreprise.
                                    </div>
                                    <input type="hidden" name="manager_id" value="">
                                @endif
                            </div>
                        </div>

                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                checked>
                            <label class="form-check-label" for="isActive">
                                Service actif
                            </label>
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
    <div class="modal fade" id="editDepartmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Modifier le Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="editDepartmentForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du Service <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="editNameService" required
                                    placeholder="Ex: Ressources Humaines, Informatique...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control departmentCode" name="code" required
                                    placeholder="SRV-XXXX" id="editDepartmentCode" readonly>
                                <small class="text-muted">Généré automatiquement</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"
                                placeholder="Description du service..." id="editDepartmentDescription"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Succursale <span class="text-danger">*</span></label>
                                <select class="form-select" name="branch_id" id="editBranchId" required>
                                    <option value="">Sélectionner une succursale</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ $branch->id == old('branch_id', $department->branch_id ?? '') ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manager du Service <span class="text-danger">*</span></label>
                                @if($companyUsers->count() > 0)
                                    {{-- Même liste unique que dans la modale de création. L'option vide en tête
                                         est nécessaire : sans elle, Select2 n'affiche pas son placeholder. --}}
                                    <select class="form-select select2" name="manager_id" id="editManagerId" required
                                        data-placeholder="Sélectionner un manager">
                                        <option value="">Sélectionner un manager</option>
                                        @foreach($companyUsers as $user)
                                            <option value="{{ $user->id }}" {{ $user->id == old('manager_id', $department->manager_id ?? '') ? 'selected' : '' }}>{{ $user->name }}@if($user->email) ({{ $user->email }})@endif</option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Aucun utilisateur trouvé. Créez d'abord des utilisateurs dans votre entreprise.
                                    </div>
                                    <input type="hidden" name="manager_id" value="">
                                @endif
                            </div>
                        </div>

                        <input type="hidden" name="is_active" value="0" id="editIsActiveHidden">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1">
                            <label class="form-check-label" for="editIsActive">
                                Service actif
                            </label>
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

        /* Select2 Custom Styles */
        .select2-container {
            width: 100% !important;
        }

        /* Thème « default » : c'est le seul dont la feuille est chargée par le layout.
           Ces règles ciblaient « bootstrap-5 », dont la CSS est absente du projet :
           la sélection n'était alors stylée par rien et le libellé sortait du cadre. */
        .select2-container--default .select2-selection--single {
            border-radius: 8px;
            border: 1px solid #d4d4d8;
            height: 38px;
        }

        /* Le layout pose déjà padding: 5px 10px sur la sélection : on ne rajoute pas
           le retrait horizontal par défaut de Select2, qui décalerait le texte. */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            padding-left: 0;
            padding-right: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #6c757d transparent transparent transparent;
            border-width: 5px 4px 0 4px;
            margin-top: -2px;
        }

        .select2-container--default .select2-dropdown {
            border-radius: 8px;
            border: 1px solid #d4d4d8;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Filet de sécurité : le nombre d'entrées est déjà borné côté JS (LIMITE_MANAGERS). */
        .select2-container--default .select2-results > .select2-results__options {
            max-height: 190px;
        }

        /* Au-dessus du fond de la modale, sinon la liste s'ouvre derrière. */
        .select2-container--open {
            z-index: 1060;
        }

        .select2-results__option {
            padding: 8px 12px;
        }

        .select2-results__option--selected {
            background-color: #696cff;
        }

        .select2-results__option--highlighted {
            background-color: rgba(105, 110, 255, 0.1);
            color: #253e87;
        }

        /* Code input styling */
        .departmentCode {
            background-color: #f8f9fa;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 1px;
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
@endpush

@push('scripts')
    <script>
        // Tableau en DataTable (colonne Actions non triable / non filtrable)
        $(function () {
            'use strict';

            @if($departments->count() > 0)
                $('#departments-table').DataTable({
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

        function editDepartment(id) {
            // Récupérer les données du service via AJAX
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('Erreur: Token CSRF non trouvé');
                return;
            }

            fetch(`{{ url('/company/settings/departments') }}/${id}/edit`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const department = data.department;

                        // Vérifier que tous les éléments existent avant de les manipuler
                        const editNameService = document.getElementById('editNameService');
                        const editDepartmentCode = document.getElementById('editDepartmentCode');
                        const editDepartmentDescription = document.getElementById('editDepartmentDescription');
                        const editBranchId = document.getElementById('editBranchId');
                        const editManagerId = document.getElementById('editManagerId');
                        const editIsActive = document.getElementById('editIsActive');
                        const editDepartmentForm = document.getElementById('editDepartmentForm');

                        // Remplir le formulaire d'édition seulement si les éléments existent
                        if (editNameService) editNameService.value = department.name;
                        if (editDepartmentCode) editDepartmentCode.value = department.code;
                        if (editDepartmentDescription) editDepartmentDescription.value = department.description || '';
                        if (editBranchId) editBranchId.value = department.branch_id || '';
                        if (editManagerId) editManagerId.value = department.manager_id || '';
                        if (editIsActive) editIsActive.checked = department.is_active;
                        if (editDepartmentForm) editDepartmentForm.action = `{{ url('/company/settings/departments') }}/${id}`;

                        // Réinitialiser Select2 si nécessaire
                        if (typeof $ !== 'undefined' && $('.select2').length > 0) {
                            $('.select2').trigger('change');
                        }

                        // Ouvrir le modal seulement si l'élément existe
                        const editModal = document.getElementById('editDepartmentModal');
                        if (editModal) {
                            const modal = new bootstrap.Modal(editModal);
                            modal.show();
                        }
                    } else {
                        alert('Erreur lors du chargement des données: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors du chargement des données');
                });
        }

        function deleteDepartment(id, name) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le service "${name}" ? Cette action est irréversible.`)) {
                // Créer un formulaire de suppression avec la route nommée
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/company/settings/departments') }}/${id}`;

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

        function toggleDepartment(id, name, isActive) {
            const action = isActive ? 'désactiver' : 'activer';
            const confirmMessage = `Êtes-vous sûr de vouloir ${action} le service "${name}" ?`;

            if (confirm(confirmMessage)) {
                // Créer un formulaire pour la route toggle avec méthode PUT
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/company/settings/departments') }}/${id}/toggle`;

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

        // Générer automatiquement le code du service
        function generatedepartmentCode() {
            const nameInput = document.getElementById('nameService');
            const codeInput = document.getElementById('departmentCode');

            if (nameInput && codeInput) {
                nameInput.addEventListener('input', function () {
                    const name = this.value.trim();
                    let code = 'SRV-';

                    if (name.length > 0) {
                        // Prendre les 4 premières lettres du nom et les mettre en majuscules
                        const namePart = name.substring(0, 4).toUpperCase().replace(/[^A-Z0-9]/g, '');

                        // Ajouter un nombre aléatoire pour éviter les doublons
                        const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

                        code += namePart + randomNum;
                    }

                    if (codeInput) codeInput.value = code;
                });
            }
        }

        // Générer automatiquement le code du service pour l'édition
        function generateEditdepartmentCode() {
            const nameInput = document.getElementById('editNameService');
            const codeInput = document.getElementById('editDepartmentCode');

            if (nameInput && codeInput) {
                nameInput.addEventListener('input', function () {
                    const name = this.value.trim();
                    let code = 'SRV-';

                    if (name.length > 0) {
                        // Prendre les 4 premières lettres du nom et les mettre en majuscules
                        const namePart = name.substring(0, 4).toUpperCase().replace(/[^A-Z0-9]/g, '');

                        // Ajouter un nombre aléatoire pour éviter les doublons
                        const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

                        code += namePart + randomNum;
                    }

                    if (codeInput) codeInput.value = code;
                });
            }
        }

        // Nombre d'entrées proposées à la fois dans la liste des managers.
        // Les autres ne sont pas déroulées : on les atteint par la barre de recherche.
        const LIMITE_MANAGERS = 4;

        // Select2 sur la liste des managers.
        //
        // Corrections par rapport à la version d'origine :
        // - plus de theme 'bootstrap-5' : sa feuille de style n'est pas chargée par le projet,
        //   les classes émises n'étaient stylées par rien et le libellé sortait du cadre ;
        // - plus de minimumInputLength : il fallait taper 2 caractères pour voir la moindre
        //   entrée, ce qui donnait une liste vide à l'ouverture ;
        // - dropdownParent sur la modale : sans lui la liste s'ouvre derrière le fond ;
        // - destroy préalable : le layout initialise déjà .select2 au chargement de la page
        //   (resources/views/layouts/app.blade.php), sans dropdownParent. On repart de zéro.
        function initSelect2() {
            if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
                return;
            }

            $('.select2').each(function () {
                const $select = $(this);
                const $modale = $select.closest('.modal');

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.select2({
                    width: '100%',
                    placeholder: $select.data('placeholder') || 'Sélectionner un manager',
                    allowClear: true,
                    dropdownParent: $modale.length ? $modale : $(document.body),
                    // Source locale : les <option> du select, filtrées sur la saisie puis
                    // tronquées, et relues à chaque appel plutôt que mises en cache.
                    ajax: {
                        delay: 0,
                        transport: function (parametres, reussite) {
                            const saisie = (parametres.data && parametres.data.term ? parametres.data.term : '')
                                .trim()
                                .toLowerCase();

                            const resultats = $select.find('option')
                                .toArray()
                                .filter(option => option.value !== '')
                                .map(option => ({ id: option.value, text: option.textContent.trim() }))
                                .filter(option => saisie === '' || option.text.toLowerCase().includes(saisie))
                                .slice(0, LIMITE_MANAGERS);

                            reussite({ results: resultats });

                            // Select2 attend un objet annulable en retour du transport.
                            return { abort: function () { } };
                        }
                    },
                    language: {
                        noResults: function () {
                            return "Aucun utilisateur trouvé";
                        },
                        searching: function () {
                            return "Recherche...";
                        }
                    }
                });
            });
        }

        // Fonction d'initialisation robuste
        function initializeDepartmentFunctions() {
            // Attendre que le DOM soit complètement chargé
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(initAllFunctions, 100);
                });
            } else {
                setTimeout(initAllFunctions, 100);
            }
        }

        function initAllFunctions() {
            // Générer le code automatiquement
            generatedepartmentCode();
            generateEditdepartmentCode();

            // Initialiser Select2 si nécessaire
            initSelect2();

            // Réinitialiser le formulaire de création quand le modal se ferme
            const createModal = document.getElementById('createDepartmentModal');
            if (createModal) {
                createModal.addEventListener('hidden.bs.modal', function () {
                    // Vérifier que les éléments existent avant de les manipuler
                    const nameService = document.getElementById('nameService');
                    const departmentCode = document.getElementById('departmentCode');
                    const description = document.querySelector('textarea[name="description"]');
                    const branchSelect = document.querySelector('select[name="branch_id"]');
                    const managerSelect = document.querySelector('select[name="manager_id"]');
                    const isActive = document.getElementById('isActive');

                    if (nameService) nameService.value = '';
                    if (departmentCode) departmentCode.value = '';
                    if (description) description.value = '';
                    if (branchSelect) branchSelect.value = '';
                    if (managerSelect) managerSelect.value = '';
                    if (isActive) isActive.checked = true;
                });
            }

            // Réinitialiser le formulaire d'édition quand le modal se ferme
            const editModal = document.getElementById('editDepartmentModal');
            if (editModal) {
                editModal.addEventListener('hidden.bs.modal', function () {
                    // Vérifier que les éléments existent avant de les manipuler
                    const editNameService = document.getElementById('editNameService');
                    const editDepartmentCode = document.getElementById('editDepartmentCode');
                    const editDescription = document.getElementById('editDepartmentDescription');
                    const editBranchId = document.getElementById('editBranchId');
                    const editManagerId = document.getElementById('editManagerId');
                    const editIsActive = document.getElementById('editIsActive');
                    const editDepartmentForm = document.getElementById('editDepartmentForm');

                    if (editNameService) editNameService.value = '';
                    if (editDepartmentCode) editDepartmentCode.value = '';
                    if (editDescription) editDescription.value = '';
                    if (editBranchId) editBranchId.value = '';
                    if (editManagerId) editManagerId.value = '';
                    if (editIsActive) editIsActive.checked = true;
                    if (editDepartmentForm) editDepartmentForm.action = '';
                });
            }
        }

        // Initialiser quand le DOM est prêt
        initializeDepartmentFunctions();
    </script>
@endpush