@extends('layouts.app')

@section('title', 'Dossiers des Employés')  

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête des Paramètres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 text-primary">Dossiers des Employés</h4>
                    <p class="text-muted mb-0">Gérez tous les dossiers des employés de votre entreprise</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>  
               
            </div>
        </div>
    </div>
    <hr>

    <!-- Recherche et filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('company.employees.dossiers.index') }}" id="search-form">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label for="search" class="form-label">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ $search }}"
                                placeholder="Nom, matricule, email, téléphone, poste...">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="branch" class="form-label">Branche</label>
                        <select class="form-select" id="branch" name="branch">
                            @foreach($brances as $id => $name)
                                <option value="{{ $id }}" {{ (string) request('branch') === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="department" class="form-label">Département</label>
                        <select class="form-select" id="department" name="department">
                            @foreach($departments as $id => $name)
                                <option value="{{ $id }}" {{ (string) request('department') === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="designation" class="form-label">Poste</label>
                        <select class="form-select" id="designation" name="designation">
                            @foreach($designations as $id => $name)
                                <option value="{{ $id }}" {{ (string) request('designation') === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6 d-flex gap-2">
                        <button type="submit" class="btn bg-primary text-white flex-grow-1">
                            <i class="fas fa-filter me-1"></i>Filtrer
                        </button>
                        @if($search !== '' || request('branch') || request('department') || request('designation'))
                            <a href="{{ route('company.employees.dossiers.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- User Pills -->
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#actif" data-bs-toggle="tab">
                <i class="fas fa-user-check ti-xs me-1"></i>
                <span class="fw-bold">{{ __('Personnel Actif') }}</span>
                <span class="badge bg-label-primary ms-1">{{ $employeesActifs->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#inactif" data-bs-toggle="tab">
                <i class="fas fa-user-x ti-xs me-1"></i>
                <span class="fw-bold">{{ __('Personnel Inactif') }}</span>
                <span class="badge bg-label-secondary ms-1">{{ $employeesInactifs->count() }}</span>
            </a>
        </li>
    </ul>
    <!--/ User Pills -->
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="tab-content p-3" style="border: 1px solid #ccc; border-radius: 5px;">
                <div id="actif" class="tab-pane active" role="tabpanel" aria-labelledby="actif-tab">
                    {{-- Tableau plutôt que cartes : recherche, tri et pagination sont assurés par
                         DataTables, sur l'effectif complet chargé par le contrôleur. --}}
                    <div class="table-responsive">
                        <table class="table table-hover" id="dossiers-actifs-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Poste</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employeesActifs as $employee)
                                    <tr>
                                        <td style="width: 52px;">
                                            @php $hasPhoto = false; @endphp
                                            @foreach ($avatar as $photo)
                                                @if($photo->employee_id == $employee['employee_id'] && $photo->document_id == 3)
                                                    @php $hasPhoto = true; @endphp
                                                    <img class="rounded-circle"
                                                        src="{{ asset(Storage::url('documents')) . '/' . $photo->document_value }}"
                                                        height="40" width="40" alt="Photo de {{ $employee->name }}">
                                                    @break
                                                @endif
                                            @endforeach
                                            @if(!$hasPhoto)
                                                <img class="rounded-circle" src="{{ asset(Storage::url('avatars')) }}/avatar.png"
                                                    height="40" width="40" alt="Photo de {{ $employee->name }}">
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('company.employees.show', $employee->id) }}"
                                                class="badge bg-label-primary">
                                                {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                            </a>
                                        </td>
                                        <td>
                                            <h6 class="mb-0">{{ $employee->name }}</h6>
                                            @if($employee->email)
                                                <small class="text-muted">{{ $employee->email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $employee->designation->name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @if($employee->statut_emp == 'Sanctionné')
                                                <span class="badge bg-label-warning">Sanctionné</span>
                                            @elseif($employee->statut_emp == 'En congé')
                                                <span class="badge bg-label-warning">En congé</span>
                                            @else
                                                <span class="badge bg-label-success">Actif</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end">
                                                <a href="{{ route('company.employees.show', $employee->id) }}"
                                                    class="btn btn-icon btn-sm btn-outline-info me-1" data-bs-toggle="tooltip"
                                                    title="Voir le dossier">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('company.employees.edit', $employee->id) }}"
                                                    class="btn btn-icon btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-icon btn-sm btn-outline-warning toggle-employee"
                                                    data-id="{{ $employee->id }}" data-status="{{ $employee->is_active }}"
                                                    data-name="{{ $employee->name }}" data-bs-toggle="tooltip"
                                                    title="Désactiver">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="inactif" class="tab-pane" role="tabpanel" aria-labelledby="inactif-tab">
                    <div class="table-responsive">
                        <table class="table table-hover" id="dossiers-inactifs-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Matricule</th>
                                    <th>Nom</th>
                                    <th>Poste</th>
                                    <th>Statut</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employeesInactifs as $employee)
                                    <tr>
                                        <td style="width: 52px;">
                                            @php $hasPhoto = false; @endphp
                                            @foreach ($avatar as $photo)
                                                @if($photo->employee_id == $employee['employee_id'] && $photo->document_id == 3)
                                                    @php $hasPhoto = true; @endphp
                                                    <img class="rounded-circle"
                                                        src="{{ asset(Storage::url('documents')) . '/' . $photo->document_value }}"
                                                        height="40" width="40" alt="Photo de {{ $employee->name }}">
                                                    @break
                                                @endif
                                            @endforeach
                                            @if(!$hasPhoto)
                                                <img class="rounded-circle" src="{{ asset(Storage::url('avatars')) }}/avatar.png"
                                                    height="40" width="40" alt="Photo de {{ $employee->name }}">
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('company.employees.show', $employee->id) }}"
                                                class="badge bg-label-secondary">
                                                {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                            </a>
                                        </td>
                                        <td>
                                            <h6 class="mb-0">{{ $employee->name }}</h6>
                                            @if($employee->email)
                                                <small class="text-muted">{{ $employee->email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $employee->designation->name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-danger">
                                                <i class="fas fa-lock me-1"></i>Inactif
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end">
                                                <a href="{{ route('company.employees.show', $employee->id) }}"
                                                    class="btn btn-icon btn-sm btn-outline-info me-1" data-bs-toggle="tooltip"
                                                    title="Voir le dossier">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('company.employees.edit', $employee->id) }}"
                                                    class="btn btn-icon btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-icon btn-sm btn-outline-success toggle-employee"
                                                    data-id="{{ $employee->id }}" data-status="{{ $employee->is_active }}"
                                                    data-name="{{ $employee->name }}" data-bs-toggle="tooltip" title="Activer">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')     
<script>
$(document).ready(function() {
    // Réactiver le bon onglet après une pagination (#actif / #inactif dans l'URL)
    var hash = window.location.hash;
    if (hash === '#inactif' || hash === '#actif') {
        var trigger = document.querySelector('.nav-pills a[href="' + hash + '"]');
        if (trigger) {
            new bootstrap.Tab(trigger).show();
        }
    }

    // Les deux listes en DataTables : recherche, tri et pagination côté navigateur.
    // La colonne photo et la colonne Actions ne sont ni triables ni cherchables.
    ['#dossiers-actifs-table', '#dossiers-inactifs-table'].forEach(function (selecteur) {
        if ($(selecteur).length === 0 || $(selecteur).find('tbody tr').length === 0) {
            return;
        }

        $(selecteur).DataTable({
            order: [[2, 'asc']],
            columnDefs: [
                { targets: [0, -1], orderable: false, searchable: false }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });
    });

    // Délégation d'événement : DataTables retire du DOM les lignes des autres pages,
    // un écouteur posé directement sur les boutons cesserait d'agir dès la page 2.
    $(document).on('click', '.toggle-employee', function(e) {
        e.preventDefault();
        
        var employeeId = $(this).data('id');
        var currentStatus = $(this).data('status');
        var employeeName = $(this).data('name');
        var $this = $(this);
        
        var actionText = currentStatus == 1 ? 'désactiver' : 'activer';
        var actionTitle = currentStatus == 1 ? 'Désactiver' : 'Activer';
        var confirmButtonText = currentStatus == 1 ? 'Oui, désactiver' : 'Oui, activer';
        var iconType = currentStatus == 1 ? 'warning' : 'info';
        
        Swal.fire({
            title: actionTitle + ' l\'employé ?',
            html: 'Êtes-vous sûr de vouloir <strong>' + actionText + '</strong> l\'employé <br><strong>' + employeeName + '</strong> ?',
            icon: iconType,
            showCancelButton: true,
            confirmButtonColor: currentStatus == 1 ? '#f5365c' : '#2dce89',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Annuler',
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }),
                $.post('{{ route("company.employees.toggle", ":id") }}'.replace(':id', employeeId))
                    .fail(function(response) {
                        Swal.showValidationMessage(
                            'Erreur: ' + response.responseJSON.message
                        );
                    });
            },
            allowOutsideClick: function () {
                !Swal.isLoading();
            }
        }).then(function (result) {
            if (result.value) {
                var response = result.value;
                
                if (response.success) {
                    // Mettre à jour l'interface
                    var newStatus = response.status;
                    var newActionText = newStatus == 1 ? 'Désactiver' : 'Activer';
                    var newIconClass = newStatus == 1 ? 'text-success' : 'text-danger';
                    
                    // Mettre à jour le bouton
                    $this.find('span').text(newActionText);
                    $this.find('i').removeClass('text-success text-danger').addClass(newIconClass);
                    $this.data('status', newStatus);
                    
                    // Afficher le message de succès
                    Swal.fire({
                        title: 'Succès!',
                        html: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(function() {
                        // Recharger la page pour voir les changements dans les tabs
                        location.reload();
                    });
                }
            }
        });
    });
});
</script>
@endpush