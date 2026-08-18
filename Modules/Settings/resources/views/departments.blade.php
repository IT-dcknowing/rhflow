@extends('layouts.app')

@section('title', 'Services - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏬 Services</h4>
                    <p class="text-muted mb-0">Gérez les différents services de votre entreprise</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.settings.config') }}" class="btn btn-outline-info">
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
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-info rounded">
                            <i class="fas fa-sitemap fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-info">{{ $stats['total_departments'] }}</h3>
                    <p class="text-muted mb-2">Total Services</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-success rounded">
                            <i class="fas fa-check-circle fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-success">{{ $stats['active_departments'] }}</h3>
                    <p class="text-muted mb-2">Services Actifs</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-warning rounded">
                            <i class="fas fa-pause-circle fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-warning">{{ $stats['inactive_departments'] }}</h3>
                    <p class="text-muted mb-2">Services Inactifs</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-primary rounded">
                            <i class="fas fa-user-tie fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-primary">{{ $company->designations()->count() }}</h3>
                    <p class="text-muted mb-2">Postes Totaux</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Services -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📋 Services Configurés</h5>
                    <span class="badge bg-label-primary">{{ $stats['total_departments'] }} services</span>
                </div>
                <div class="card-body">
                    @if($departments->count() > 0)
                        <div class="row">
                            @foreach($departments as $department)
                            <div class="col-xl-4 col-lg-4 mb-4">
                                <div class="card border h-100 {{ $department->is_active ? 'border-info' : 'border-secondary' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                    <div class="avatar-initial {{ $department->is_active ? 'bg-label-info' : 'bg-label-secondary' }} rounded">
                                                        <i class="fas fa-sitemap fa-20px"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $department->name }}</h6>
                                                    <small class="text-muted">Code: {{ $department->code }}</small>
                                                </div>
                                            </div>
                                             <div class="dropdown">
                                                <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="editDepartment({{ $department->id }})">
                                                        <i class="fas fa-edit me-1"></i>Modifier
                                                    </a></li>
                                                    <li><a class="dropdown-item {{ $department->is_active ? 'text-warning' : 'text-success' }}" href="#" onclick="toggleDepartment({{ $department->id }}, '{{ addslashes($department->name) }}', {{ $department->is_active ? 'true' : 'false' }})">
                                                        <i class="fas fa-{{ $department->is_active ? 'toggle-off' : 'toggle-on' }} me-1"></i>{{ $department->is_active ? 'Désactiver' : 'Activer' }}
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteDepartment({{ $department->id }}, '{{ addslashes($department->name) }}')">
                                                        <i class="fas fa-trash me-1"></i>Supprimer
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        @if($department->description)
                                            <div class="mb-3">
                                                <small class="text-muted">{{ $department->description }}</small>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            @if($department->branch)
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-building me-2 text-muted"></i>
                                                    <small class="text-muted">{{ $department->branch->name }}</small>
                                                </div>
                                            @endif

                                            @if($department->manager)
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="fas fa-user-tie me-2 text-muted"></i>
                                                    <small class="text-muted">Manager: {{ $department->manager }}</small>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge {{ $department->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                    {{ $department->is_active ? '✅ Actif' : '❌ Inactif' }}
                                                </span>
                                                <span class="badge bg-label-warning ms-1">{{ $department->designations->count() }} postes</span>
                                                @if($department->branch)
                                                    <span class="badge bg-label-primary ms-1">{{ $department->branch->name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted">{{ $department->designations->count() }} postes</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
                            <input type="text" class="form-control" name="name" id="nameService"  required placeholder="Ex: Ressources Humaines, Informatique...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control departmentCode" name="code" required placeholder="SRV-XXXX" id="departmentCode" readonly>
                            <small class="text-muted">Généré automatiquement</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Description du service..."></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Succursale<span class="text-danger">*</span></label>
                            <select class="form-select" name="branch_id">
                                <option value="">Sélectionner une succursale</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Manager du Service <span class="text-danger">*</span></label>
                            @if($companyUsers->count() > 0)
                                @if($companyUsers->count() > 10)
                                    <!-- Select avec recherche pour plus de 100 utilisateurs -->
                                    <select class="form-select select2" name="manager_id" data-placeholder="Rechercher un utilisateur...">
                                        <option value="">Aucun manager</option>
                                        @foreach($companyUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                @else
                                    <!-- Select simple pour moins de 100 utilisateurs -->
                                    <select class="form-select" name="manager_id">
                                        <option value="">Aucun manager</option>
                                        @foreach($companyUsers as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                @endif
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
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                        <label class="form-check-label" for="isActive">
                            Service actif
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
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
                            <input type="text" class="form-control" name="name" id="editNameService" required placeholder="Ex: Ressources Humaines, Informatique...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control departmentCode" name="code" required placeholder="SRV-XXXX" id="editDepartmentCode" readonly>
                            <small class="text-muted">Généré automatiquement</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Description du service..." id="editDepartmentDescription"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Succursale</label>
                            <select class="form-select" name="branch_id" id="editBranchId">
                                <option value="">Sélectionner une succursale</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $branch->id == old('branch_id', $department->branch_id ?? '') ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Manager du Service</label>
                            @if($companyUsers->count() > 0)
                                @if($companyUsers->count() > 10)
                                    <!-- Select avec recherche pour plus de 10 utilisateurs -->
                                    <select class="form-select select2" name="manager_id" id="editManagerId" data-placeholder="Rechercher un utilisateur...">
                                        @foreach($companyUsers as $user)
                                            <option value="{{ $user->id }}" {{ $user->id == old('manager_id', $department->manager_id ?? '') ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                @else
                                    <!-- Select simple pour moins de 10 utilisateurs -->
                                    <select class="form-select" name="manager_id" id="editManagerId">
                                        @foreach($companyUsers as $user)
                                            <option value="{{ $user->id }}" {{ $user->id == old('manager_id', $department->manager_id ?? '') ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                @endif
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
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

    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 8px;
        border: 1px solid #d4d4d8;
        min-height: 38px;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding: 6px 12px;
        line-height: 1.5;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        top: 50%;
        transform: translateY(-50%);
        right: 12px;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow b {
        border-color: #6c757d transparent transparent transparent;
        border-width: 5px 4px 0 4px;
        margin-top: -2px;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 8px;
        border: 1px solid #d4d4d8;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
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
            nameInput.addEventListener('input', function() {
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
            nameInput.addEventListener('input', function() {
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

    // Initialiser Select2 si plus de 10 utilisateurs
    function initSelect2() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.select2 && $('.select2').length > 0) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Rechercher un utilisateur...',
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    noResults: function() {
                        return "Aucun utilisateur trouvé";
                    },
                    searching: function() {
                        return "Recherche...";
                    }
                }
            });
        }
    }

    // Fonction d'initialisation robuste
    function initializeDepartmentFunctions() {
        // Attendre que le DOM soit complètement chargé
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
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
            createModal.addEventListener('hidden.bs.modal', function() {
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
            editModal.addEventListener('hidden.bs.modal', function() {
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