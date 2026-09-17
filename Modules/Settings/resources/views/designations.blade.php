@extends('layouts.app')

@section('title', 'Postes - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        {{-- Affichage des erreurs dans la page : la modale se referme au rechargement et le
             message ne dépend ainsi d'aucun JavaScript pour être vu. --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                <h6 class="alert-heading mb-1">Le poste n'a pas été enregistré</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif

        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Postes</h4>
                        <p class="text-muted mb-0">Gérez les différents postes de votre entreprise</p>
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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDesignationModal">
                            <i class="fas fa-plus me-1"></i>Nouveau Poste
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <x-kpi-grid>
            <x-kpi icon="fas fa-user-tie" color="warning" label="Total" sublabel="Postes"
                :value="$stats['total_designations']" />

            <x-kpi icon="fas fa-check-circle" color="success" label="Actifs" sublabel="Postes"
                :value="$stats['active_designations']" />

            <x-kpi icon="fas fa-pause-circle" color="warning" label="Inactifs" sublabel="Postes"
                :value="$stats['inactive_designations']" />

            <x-kpi icon="fas fa-sitemap" color="info" label="Services" sublabel="Au total"
                :value="$departments->count()" />
        </x-kpi-grid>

        <!-- Liste des Postes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Postes</h5>
                        <span class="badge bg-label-primary">{{ $stats['total_designations'] }} postes</span>
                    </div>
                    <div class="card-body">
                        @if($designations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover border-top dataTable no-footer" id="designations-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Poste</th>
                                            <th>Service</th>
                                            <th>Salaire de base</th>
                                            <th>Employés</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($designations as $designation)
                                            <tr>
                                                <td><span class="badge bg-label-secondary">{{ $designation->code }}</span></td>
                                                <td>
                                                    <div>
                                                        <h6 class="mb-0">{{ $designation->name }}</h6>
                                                        @if($designation->description)
                                                            <small class="text-muted">{{ $designation->description }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($designation->department)
                                                        <small><i
                                                                class="fas fa-sitemap me-1 text-muted"></i>{{ $designation->department->name }}</small>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                {{-- data-order : tri numerique sur le montant brut --}}
                                                <td data-order="{{ $designation->base_salary ?? 0 }}">
                                                    @if($designation->base_salary)
                                                        <span
                                                            class="text-success">{{ number_format($designation->base_salary, 0, ',', ' ') }}</span>
                                                    @else
                                                        <small class="text-muted">-</small>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-label-info">{{ $designation->employees->count() }}</span>
                                                </td>
                                                {{-- data-order : DataTables trie sur la valeur brute, pas sur le libelle --}}
                                                <td data-order="{{ $designation->is_active ? 1 : 0 }}">
                                                    <span
                                                        class="badge {{ $designation->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                        {{ $designation->is_active ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex gap-1">
                                                        <button class="btn btn-sm btn-outline-primary"
                                                            onclick="editDesignation({{ $designation->id }})" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if(!$designation->is_active)
                                                            <button class="btn btn-sm btn-outline-success"
                                                                onclick="toggleDesignation({{ $designation->id }}, '{{ addslashes($designation->name) }}', false)"
                                                                title="Activer">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-outline-warning"
                                                                onclick="toggleDesignation({{ $designation->id }}, '{{ addslashes($designation->name) }}', true)"
                                                                title="Désactiver">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteDesignation({{ $designation->id }}, '{{ addslashes($designation->name) }}')"
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
                                            <i class="fas fa-user-tie fa-32px"></i>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="text-muted">Aucun poste configuré</h5>
                                <p class="text-muted mb-4">Commencez par créer votre premier poste</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDesignationModal">
                                    <i class="fas fa-plus me-1"></i>Créer un Poste
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Création -->
    <div class="modal fade" id="createDesignationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nouveau Poste</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('company.settings.designations.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du Poste <span class="text-danger">*</span></label>
                                {{-- old() : en cas de refus (nom déjà utilisé), la saisie est conservée
                                     au lieu d'être à retaper entièrement. --}}
                                <input type="text" class="form-control" name="name" id="name" required
                                    value="{{ old('name') }}"
                                    placeholder="Ex: Développeur, Manager, Comptable...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control designationCode" name="code" required
                                    value="{{ old('code') }}"
                                    placeholder="POST-XXXX" id="designationCode" readonly>
                                <small class="text-muted">Généré automatiquement</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"
                                placeholder="Description du poste...">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Service <span class="text-danger">*</span></label>
                                <select class="form-select" name="department_id">
                                    <option value="">Sélectionner un service</option>
                                    @foreach($departments as $department)
                                        {{-- La succursale est affichée avec « ?-> » : un service sans succursale
                                             valide provoquerait sinon une erreur fatale sur toute la page. --}}
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                            @if($department->branch?->name) ({{ $department->branch->name }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                checked>
                            <label class="form-check-label" for="isActive">
                                Poste actif
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
    <div class="modal fade" id="editDesignationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Modifier le Poste</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="" id="editDesignationForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom du Poste <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="editName" required
                                    placeholder="Ex: Développeur, Manager, Comptable...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control designationCode" name="code" required
                                    placeholder="POST-XXXX" id="editCode" readonly>
                                <small class="text-muted">Généré automatiquement</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Description du poste..."
                                id="editDescription"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Service <span class="text-danger">*</span></label>
                                <select class="form-select" name="department_id" id="editDepartmentId">
                                    <option value="">Sélectionner un service</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ $department->id == old('department_id', $designation->department_id ?? '') ? 'selected' : '' }}>{{ $department->name }}
                                            ({{ $department->branch->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="is_active" value="0" id="editIsActiveHidden">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editIsActive" value="1">
                            <label class="form-check-label" for="editIsActive">
                                Poste actif
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

        /* Code input styling */
        .designationCode {
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
        // Après un refus, Laravel recharge la page : la modale se referme et la saisie
        // restaurée par old() resterait invisible. On la rouvre donc sur l'erreur.
        // Création et modification ne sont pas distinguées ici : le bandeau affiché en
        // haut de page couvre le second cas.
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                const modaleCreation = document.getElementById('createDesignationModal');
                if (modaleCreation) {
                    bootstrap.Modal.getOrCreateInstance(modaleCreation).show();
                }
            });
        @endif

        // Tableau en DataTable (colonne Actions non triable / non filtrable)
        $(function () {
            'use strict';

            @if($designations->count() > 0)
                $('#designations-table').DataTable({
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

        function editDesignation(id) {
            // Récupérer les données de la désignation via AJAX
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('Erreur: Token CSRF non trouvé');
                return;
            }

            fetch(`{{ url('/company/settings/designations') }}/${id}/edit`, {
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
                        const designation = data.designation;

                        // Vérifier que tous les éléments existent avant de les manipuler
                        const editName = document.getElementById('editName');
                        const editCode = document.getElementById('editCode');
                        const editDescription = document.getElementById('editDescription');
                        const editDepartmentId = document.getElementById('editDepartmentId');
                        const editIsActive = document.getElementById('editIsActive');
                        const editDesignationForm = document.getElementById('editDesignationForm');

                        // Remplir le formulaire d'édition seulement si les éléments existent
                        if (editName) editName.value = designation.name;
                        if (editCode) editCode.value = designation.code;
                        if (editDescription) editDescription.value = designation.description || '';
                        if (editDepartmentId) editDepartmentId.value = designation.department_id || '';
                        if (editIsActive) editIsActive.checked = designation.is_active;
                        if (editDesignationForm) editDesignationForm.action = `{{ url('/company/settings/designations') }}/${id}`;

                        // Ouvrir le modal seulement si l'élément existe
                        const editModal = document.getElementById('editDesignationModal');
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

        function deleteDesignation(id, name) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer le poste "${name}" ? Cette action est irréversible.`)) {
                // Créer un formulaire de suppression avec la route nommée
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/company/settings/designations') }}/${id}`;

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

        function toggleDesignation(id, name, isActive) {
            const action = isActive ? 'désactiver' : 'activer';
            const confirmMessage = `Êtes-vous sûr de vouloir ${action} le poste "${name}" ?`;

            if (confirm(confirmMessage)) {
                // Créer un formulaire pour la route toggle avec méthode PUT
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/company/settings/designations') }}/${id}/toggle`;

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

        // Générer automatiquement le code de la désignation
        function generateDesignationCode() {
            const nameInput = document.getElementById('name');
            const codeInput = document.getElementById('designationCode');

            if (nameInput && codeInput) {
                nameInput.addEventListener('input', function () {
                    const name = this.value.trim();
                    let code = 'POST-';

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

        // Générer automatiquement le code de la désignation pour l'édition
        function generateEditDesignationCode() {
            const nameInput = document.getElementById('editName');
            const codeInput = document.getElementById('editCode');

            if (nameInput && codeInput) {
                nameInput.addEventListener('input', function () {
                    const name = this.value.trim();
                    let code = 'POST-';

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

        // Fonction d'initialisation robuste
        function initializeDesignationFunctions() {
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
            generateDesignationCode();
            generateEditDesignationCode();

            // Réinitialiser le formulaire de création quand le modal se ferme
            const createModal = document.getElementById('createDesignationModal');
            if (createModal) {
                createModal.addEventListener('hidden.bs.modal', function () {
                    // Vérifier que les éléments existent avant de les manipuler
                    const nameInput = document.querySelector('input[name="name"]');
                    const codeInput = document.getElementById('designationCode');
                    const description = document.querySelector('textarea[name="description"]');
                    const departmentSelect = document.querySelector('select[name="department_id"]');
                    const isActive = document.getElementById('isActive');

                    if (nameInput) nameInput.value = '';
                    if (codeInput) codeInput.value = '';
                    if (description) description.value = '';
                    if (departmentSelect) departmentSelect.value = '';
                    if (isActive) isActive.checked = true;
                });
            }

            // Réinitialiser le formulaire d'édition quand le modal se ferme
            const editModal = document.getElementById('editDesignationModal');
            if (editModal) {
                editModal.addEventListener('hidden.bs.modal', function () {
                    // Vérifier que les éléments existent avant de les manipuler
                    const editName = document.getElementById('editName');
                    const editCode = document.getElementById('editCode');
                    const editDescription = document.getElementById('editDescription');
                    const editDepartmentId = document.getElementById('editDepartmentId');
                    const editIsActive = document.getElementById('editIsActive');
                    const editDesignationForm = document.getElementById('editDesignationForm');

                    if (editName) editName.value = '';
                    if (editCode) editCode.value = '';
                    if (editDescription) editDescription.value = '';
                    if (editDepartmentId) editDepartmentId.value = '';
                    if (editIsActive) editIsActive.checked = true;
                    if (editDesignationForm) editDesignationForm.action = '';
                });
            }
        }

        // Initialiser quand le DOM est prêt
        document.addEventListener('DOMContentLoaded', function () {
            initializeDesignationFunctions();
        });
    </script>
@endpush