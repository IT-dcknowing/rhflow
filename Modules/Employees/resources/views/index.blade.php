@extends('layouts.app')

@section('title', 'Employés')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête des Paramètres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 text-primary"> Employés </h4>
                        <p class="text-muted mb-0">Gérez les employés mensuels et journaliers de votre entreprise</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->format('l d F Y') }} •
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Filtre</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" placeholder="Rechercher un employé..."
                                        id="searchInput">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="statusFilter">
                                    <option value="">Tous les statuts</option>
                                    <option value="active">Actif</option>
                                    <option value="inactive">Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="departmentFilter">
                                    <option value="">Tous les départements</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" id="contractFilter">
                                    <option value="">Tous les contrats</option>
                                    <option value="1">CDI</option>
                                    <option value="2">CDD</option>
                                    <option value="3">Stage</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-warning w-100" type="button" onclick="resetFilters()">
                                    <i class="fas fa-refresh me-1"></i>Réinitialiser
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Employés</h5>
                    </div>
                    {{-- Filtrage serveur sur salary_type : le lien recharge la page --}}
                    <ul class="nav nav-tabs px-3 pt-2">
                        @foreach(['tous' => 'Tous', 'mensuel' => 'Mensuels', 'journalier' => 'Journaliers'] as $cle => $libelle)
                            <li class="nav-item">
                                <a class="nav-link {{ $type === $cle ? 'active' : '' }}"
                                    href="{{ route('company.employees.index', $cle === 'tous' ? [] : ['type' => $cle]) }}">
                                    {{ $libelle }}
                                    <span class="badge bg-label-primary ms-1">{{ $countTypes[$cle] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="card-body">
                        <!-- Table des employés -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="employeesTable">
                                <thead class="table-info">
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Matricule</th>
                                        <th>Nom</th>
                                        <th>Succursale </th>
                                        <th>Type</th>
                                        <th>Salaire de base</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($employees as $employee)
                                        <tr data-employee-id="{{ $employee->id }}">
                                            <td>
                                                <input type="checkbox" class="form-check-input employee-checkbox"
                                                    value="{{ $employee->id }}">
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-label-primary fs-6">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <div class="avatar-initial bg-label-info rounded-circle">
                                                            {{ substr($employee->name, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $employee->name }}</h6>
                                                        <small class="text-muted">{{ $employee->email ?? '-' }}</small><br>
                                                        <small class="text-muted">{{ $employee->phone ?? '-' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="text-info">{{ $employee->branch->name ?? '-' }}</strong><br>
                                                - <small
                                                    class="text-muted me-2">{{ $employee->department->name ?? '-' }}</small><br>
                                                - <small
                                                    class="text-muted me-2">{{ $employee->designation->name ?? '-' }}</small>
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
                                            <td>
                                                @if($employee->is_active)
                                                    <span class="badge bg-label-success">Actif</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Inactif</span>
                                                @endif
                                            </td>
                                            <td align="center">
                                                <div class="dropdown">
                                                    <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-bars fa-sm"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item"
                                                            href="{{ route('company.employees.show', $employee->id) }}">
                                                            <i class="fas fa-eye me-1"></i>Voir détails
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('company.employees.edit', $employee->id) }}">
                                                            <i class="fas fa-edit me-1"></i>Modifier
                                                        </a>
                                                        @if($employee->is_active)
                                                            <a class="dropdown-item text-warning" href="#"
                                                                onclick="toggleEmployee({{ $employee->id }}, '{{ addslashes($employee->name) }}', true)">
                                                                <i class="fas fa-times me-1"></i>Désactiver
                                                            </a>
                                                        @else
                                                            <a class="dropdown-item text-success" href="#"
                                                                onclick="toggleEmployee({{ $employee->id }}, '{{ addslashes($employee->name) }}', false)">
                                                                <i class="fas fa-check me-1"></i>Activer
                                                            </a>
                                                        @endif
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-info"
                                                            href="{{ route('company.contracts.index', ['employee_id' => $employee->id]) }}">
                                                            <i class="fas fa-file-text me-1"></i>Contrats
                                                        </a>
                                                        <a class="dropdown-item text-warning"
                                                            href="{{ route('company.leaves.index', ['employee_id' => $employee->id]) }}">
                                                            <i class="fas fa-calendar me-1"></i>Congés
                                                        </a>
                                                        <a class="dropdown-item text-success"
                                                            href="{{ route('company.leaves.create', ['employee_id' => $employee->id]) }}">
                                                            <i class="fas fa-umbrella-beach me-1"></i>Créer un congé
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            onclick="confirmDelete({{ $employee->id }})">
                                                            <i class="fas fa-trash me-1"></i>Supprimer
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                                    <h5 class="text-muted">Aucun employé mensuel</h5>
                                                    <p class="text-muted mb-4">Commencez par ajouter votre premier employé
                                                        mensuel</p>
                                                    <a href="{{ route('company.employees.create') }}"
                                                        class="btn bg-primary text-white">
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
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSelected()">
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
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
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
                order: [[1, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
                },
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            });
        @endif

        // Recherche personnalisée liée à DataTable
        $('#searchInput').on('keyup', function () {
            @if($employees->count() > 0)
                table.search(this.value).draw();
            @else
                                            // Filtrage simple quand DataTable n'est pas initialisé
                                            var val = this.value.toLowerCase();
                $('#employeesTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
                });
            @endif
                        });
        // Sélection multiple
        document.getElementById('selectAll').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            updateBulkActions();
        });

        document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActions);
        });

        function updateBulkActions() {
            const selected = document.querySelectorAll('.employee-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');

            if (selected.length > 0) {
                bulkActions.classList.remove('d-none');
                document.getElementById('selectedCount').textContent = `${selected.length} employé(s) sélectionné(s)`;
            } else {
                bulkActions.classList.add('d-none');
            }
        }

        // Confirmation de suppression
        function confirmDelete(employeeId) {
            if (confirm('Êtes-vous sûr de vouloir désactiver cet employé ?')) {
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
        }

        // Actions pour les autres fonctionnalités
        function viewContracts(employeeId) {
            alert('Fonctionnalité contrats à implémenter pour l\'employé ' + employeeId);
        }

        function viewLeaves(employeeId) {
            alert('Fonctionnalité congés à implémenter pour l\'employé ' + employeeId);
        }

        function exportSelected() {
            alert('Export des employés sélectionnés à implémenter');
        }

        function changeStatus(status) {
            alert('Changement de statut à implémenter');
        }

        function deleteSelected() {
            alert('Suppression groupée à implémenter');
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('departmentFilter').value = '';
            document.getElementById('contractFilter').value = '';
            // Recharger la page ou filtrer les résultats
        }
    </script>
@endpush