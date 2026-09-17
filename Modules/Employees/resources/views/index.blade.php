@extends('layouts.app')

@section('title', 'Employés')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête des Paramètres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 text-primary"> Employés </h4>
                        <p class="text-muted mb-0">Gérez les employés mensuels et journaliers de votre entreprise</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->translatedFormat('l d F Y') }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ now()->format('H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#importModal">
                            <i class="fas fa-upload me-1"></i>Importer
                        </button>
                        <a href="{{ route('company.employees.create') }}" class="btn bg-primary text-white">
                            <i class="fas fa-plus me-1"></i>Nouvel Employé
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="row mb-4">
            @if(session('import_errors'))
                <div class="col-12 mb-3">
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <h6 class="alert-heading d-flex align-items-center mb-1">Erreurs lors de l'importation</h6>
                        <ul class="mb-0">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filtres de recherche</h5>
                        @if(request()->hasAny(['search', 'status', 'branch_id', 'department_id', 'contract_type_id']))
                            <a href="{{ route('company.employees.index', $type !== 'tous' ? ['type' => $type] : []) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i>Réinitialiser
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('company.employees.index') }}" id="filterForm">
                            @if($type !== 'tous')
                                <input type="hidden" name="type" value="{{ $type }}">
                            @endif
                            <div class="row g-2 align-items-end">
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label small fw-semibold">Recherche</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Nom, matricule, tél, email..."
                                            value="{{ request('search') }}" id="searchInput">
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label small fw-semibold">Statut</label>
                                    <select class="form-select" name="status" id="statusFilter">
                                        <option value="" {{ request('status') === '' ? 'selected' : '' }}>Tous les statuts</option>
                                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label small fw-semibold">Succursale</label>
                                    <select class="form-select" name="branch_id" id="branchFilter">
                                        <option value="">Toutes les succursales</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label small fw-semibold">Service</label>
                                    <select class="form-select" name="department_id" id="departmentFilter">
                                        <option value="">Tous les services</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-4 d-flex gap-2">
                                    <button class="btn btn-primary flex-grow-1" type="submit">
                                        <i class="fas fa-search me-1"></i>Rechercher
                                    </button>
                                    <a href="{{ route('company.employees.index', $type !== 'tous' ? ['type' => $type] : []) }}"
                                        class="btn btn-outline-secondary" title="Réinitialiser les filtres">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Liste des Employés</h5>
                    </div>
                    {{-- Onglets avec défilement horizontal fluide sans débordement --}}
                    <div class="border-bottom px-3 pt-2 overflow-auto" style="-webkit-overflow-scrolling: touch;">
                        <ul class="nav nav-tabs flex-nowrap border-bottom-0" role="tablist">
                            @foreach(['tous' => 'Tous', 'mensuel' => 'Mensuels', 'journalier' => 'Journaliers'] as $cle => $libelle)
                                <li class="nav-item">
                                    <a class="nav-link text-nowrap {{ $type === $cle ? 'active' : '' }}"
                                        href="{{ route('company.employees.index', array_merge(request()->except('page', 'type'), $cle === 'tous' ? [] : ['type' => $cle])) }}">
                                        {{ $libelle }}
                                        <span class="badge bg-label-primary ms-1">{{ $countTypes[$cle] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body">
                        <!-- Table des employés -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="employeesTable">
                                <thead class="table-info">
                                    <tr>
                                        <th>Matricule</th>
                                        <th>Nom</th>
                                        <th>Succursale</th>
                                        <th>Service</th>
                                        <th>Poste</th>
                                        <th>Type</th>
                                        <th>Salaire</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr data-employee-id="{{ $employee->id }}">
                                            <td>
                                                <span class="badge bg-label-primary" style="font-size: 0.72rem; font-weight: 600; letter-spacing: 0.3px;">
                                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $employee->name }}</div>
                                                    @if($employee->phone)
                                                        <small class="text-muted"><i class="fas fa-phone fa-xs me-1"></i>{{ $employee->phone }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-body">{{ $employee->branch->name ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-body">{{ $employee->department->name ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-body">{{ $employee->designation->name ?? '-' }}</span>
                                            </td>
                                            <td>
                                                @if($employee->salary_type == 2)
                                                    <span class="badge bg-label-warning">Journalier</span>
                                                @else
                                                    <span class="badge bg-label-info">Mensuel</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    {{ number_format($employee->salary, 0, ',', ' ') }} FCFA
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="{{ route('company.employees.show', $employee->id) }}"
                                                        class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Voir détails">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('company.employees.edit', $employee->id) }}"
                                                        class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($employee->is_active)
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            onclick="toggleEmployee({{ $employee->id }}, '{{ addslashes($employee->name) }}', true)"
                                                            data-bs-toggle="tooltip" title="Désactiver">
                                                            <i class="fas fa-user-slash"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                            onclick="toggleEmployee({{ $employee->id }}, '{{ addslashes($employee->name) }}', false)"
                                                            data-bs-toggle="tooltip" title="Activer">
                                                            <i class="fas fa-user-check"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete({{ $employee->id }})" data-bs-toggle="tooltip" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucun employé trouvé</h5>
                                                    <p class="text-muted mb-4">Aucun employé ne correspond aux critères sélectionnés.</p>
                                                    <a href="{{ route('company.employees.create') }}"
                                                        class="btn btn-primary">
                                                        <i class="fas fa-plus me-1"></i>Ajouter un employé
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($employees->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Affichage de {{ $employees->firstItem() }} à {{ $employees->lastItem() }}
                                    sur {{ $employees->total() }} employés
                                </div>
                                {{ $employees->links() }}
                            </div>
                        @endif

                        <!-- Actions groupées -->
                        <div class="d-none" id="bulkActions">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mt-3">
                                <span id="selectedCount">0 employé(s) sélectionné(s)</span>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="exportSelected()">
                                        <i class="fas fa-download me-1"></i>Exporter
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="changeStatus('inactive')">
                                        <i class="fas fa-user-off me-1"></i>Désactiver
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteSelected()">
                                        <i class="fas fa-trash me-1"></i>Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'importation -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Importer des employés</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('company.employees.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            Veuillez utiliser le modèle Excel pour garantir le bon formatage des données.
                            <div class="mt-2">
                                <a href="{{ route('company.employees.import.template') }}"
                                    class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-download me-1"></i>Télécharger le modèle (.csv)
                                </a>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Choisir un fichier (Excel ou CSV)</label>
                            <input class="form-control" type="file" id="importFile" name="file" accept=".xlsx, .xls, .csv"
                                required>
                        </div>
                        <div class="alert alert-warning" role="alert">
                            <small>
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Note :</strong> L'importation créera automatiquement des comptes utilisateurs pour
                                les nouveaux employés.
                                Assurez-vous que les colonnes Succursale, Département et Poste correspondent exactement à
                                celles configurées dans le système.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Lancer l'importation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .table th {
            font-weight: 600;
            font-size: 0.7rem;
            border-top: none;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.7rem;
        }

        .dropdown-item {
            font-size: 0.7rem;
        }

        .empty-state {
            padding: 3rem 1rem;
        }

        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .card {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
        }

        .form-control:focus {
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #696cff;
        }

        #bulkActions {
            border: 1px solid #e0e0e0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Activer / desactiver un employe (route POST company.employees.toggle)
        function toggleEmployee(id, nom, estActif) {
            Swal.fire({
                title: estActif ? 'Désactiver cet employé ?' : 'Activer cet employé ?',
                text: nom,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: estActif ? 'Oui, désactiver' : 'Oui, activer',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: estActif ? 'btn btn-warning me-3' : 'btn btn-success me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                fetch(`{{ url('company/employees') }}/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Échec', text: data.message });
                        }
                    })
                    .catch(() => {
                        Swal.fire({ icon: 'error', title: 'Erreur', text: 'La mise à jour du statut a échoué.' });
                    });
            });
        }

        // Initialisation de DataTable (seulement s'il y a des employés)
        @if($employees->count() > 0)
            var table = $('#employeesTable').DataTable({
                responsive: true,
                order: [[1, 'asc']],
                // Pas de tri sur la colonne actions (index 7)
                columnDefs: [{ orderable: false, targets: [7] }],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
                },
                paging: false,
                info: false,
                lengthChange: false,
                searching: false,
                dom: "<'row'<'col-sm-12'tr>>",
            });
        @endif

        // Initialisation des tooltips Bootstrap
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Confirmation de suppression avec SweetAlert2
        function confirmDelete(employeeId) {
            Swal.fire({
                title: 'Supprimer cet employé ?',
                text: 'Cette action supprimera la fiche de cet employé.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ url('company/employees') }}/${employeeId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush