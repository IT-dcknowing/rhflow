@extends('layouts.app')

@section('title', 'Dossiers des Employés')  

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
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
                <span class="badge bg-label-primary ms-1">{{ $employeesActifs->total() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#inactif" data-bs-toggle="tab">
                <i class="fas fa-user-x ti-xs me-1"></i>
                <span class="fw-bold">{{ __('Personnel Inactif') }}</span>
                <span class="badge bg-label-secondary ms-1">{{ $employeesInactifs->total() }}</span>
            </a>
        </li>
    </ul>
    <!--/ User Pills -->
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="tab-content p-3" style="border: 1px solid #ccc; border-radius: 5px;">
                <div id="actif" class="tab-pane active" role="tabpanel" aria-labelledby="actif-tab">
                    <div class="row">
                        @forelse($employeesActifs as $employee)
                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-end mb-3">
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="{{ route('company.employees.edit', $employee->id) }}"
                                                            class="dropdown-item" data-url="" data-size="md" data-ajax-popup="true"
                                                            data-title="{{ __('Modifier le dossier') }}"><i class="fas fa-edit "></i><span
                                                                class="ms-2">Modifier</span></a>

                                                        <a href="#" class="dropdown-item toggle-employee"
                                                            data-id="{{ $employee->id }}"
                                                            data-status="{{ $employee->is_active }}"
                                                            data-name="{{ $employee->name }}">
                                                            <i class="fas fa-power-off {{ $employee->is_active == 1 ? 'text-success' : 'text-danger' }}"></i>
                                                            <span class="ms-2">{{ $employee->is_active == 1 ? 'Désactiver' : 'Activer' }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                @php
                                                    $hasPhoto = false;
                                                @endphp
                                                @foreach ($avatar as $photo)
                                                    @if($photo->employee_id == $employee['employee_id'] && $photo->document_id == 3)
                                                        @php
                                                            $hasPhoto = true;
                                                        @endphp
                                                        <img class="img-fluid rounded-circle mb-3" src="{{ asset(Storage::url('documents')) . '/' .$photo->document_value }}" height="110" width="110" alt="User avatar">
                                                        @break
                                                    @endif
                                                @endforeach
                                                @if(!$hasPhoto)
                                                    <img class="img-fluid rounded-circle mb-3" src="{{ asset(Storage::url('avatars')) }}/avatar.png" height="110" width="110" alt="User avatar">
                                                @endif
                                                <h4>{{$employee->name}}</h4>
                                                <span class="badge bg-label-secondary mt-1">{{ !empty($employee->designation) ? $employee->designation->name : '' }}</span>
                                                <hr>
                                                @can('Show Employee Profile')
                                                    <a class="btn btn-outline-primary"
                                                        href="{{ route('company.employees.show', $employee->id) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                                @else
                                                    <a class="btn btn-outline-primary"
                                                        href="{{ route('company.employees.show', $employee->id) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                                @endcan
                                                @if($employee->statut_emp == 'Sanctionné')
                                                    <span class="status_badge badge bg-warning p-2 px-3 rounded d-block mt-2">Sanctionné</span>
                                                @elseif($employee->statut_emp == 'En congé')
                                                    <span class="status_badge badge bg-warning p-2 px-3 rounded d-block mt-2">En congé</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center mb-0">
                                    <h6 class="mb-0">Aucun employé actif trouvé</h6>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @if($filtreActif)
                        @if($employeesActifs->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $employeesActifs->links() }}
                            </div>
                        @endif
                    @elseif($employeesActifs->total() > $employeesActifs->count())
                        {{-- Apercu limite : le reste se retrouve via la recherche --}}
                        <div class="alert alert-light border text-center mt-3 mb-0">
                            <i class="fas fa-search me-1"></i>
                            {{ $employeesActifs->count() }} dossier(s) affiché(s) sur {{ $employeesActifs->total() }} actifs.
                            <a href="#search" onclick="document.getElementById('search').focus(); return false;">
                                Utilisez la recherche
                            </a>
                            pour retrouver les autres.
                        </div>
                    @endif
                </div>
                <div id="inactif" class="tab-pane" role="tabpanel" aria-labelledby="inactif-tab">
                    <div class="row">
                        @forelse($employeesInactifs as $employee)
                                <div class="col-xl-3 col-lg-4 col-sm-6">
                                    <div class="card h-100 mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <i class="fas fa-lock text-danger"></i>
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="{{ route('company.employees.edit', $employee->id) }}"
                                                            class="dropdown-item" data-url="" data-size="md" data-ajax-popup="true"
                                                            data-title="{{ __('Modifier le dossier') }}"><i class="fas fa-edit "></i><span
                                                                class="ms-2">Modifier</span></a>

                                                        <a href="#" class="dropdown-item toggle-employee"
                                                            data-id="{{ $employee->id }}"
                                                            data-status="{{ $employee->is_active }}"
                                                            data-name="{{ $employee->name }}">
                                                            <i class="fas fa-power-off {{ $employee->is_active == 1 ? 'text-success' : 'text-danger' }}"></i>
                                                            <span class="ms-2">{{ $employee->is_active == 1 ? 'Désactiver' : 'Activer' }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                @php
                                                    $hasPhoto = false;
                                                @endphp
                                                @foreach ($avatar as $photo)
                                                    @if($photo->employee_id == $employee['employee_id'] && $photo->document_id == 3)
                                                        @php
                                                            $hasPhoto = true;
                                                        @endphp
                                                        <img class="img-fluid rounded-circle mb-3" src="{{ asset(Storage::url('documents')) . '/' .$photo->document_value }}" height="110" width="110" alt="User avatar">
                                                        @break
                                                    @endif
                                                @endforeach
                                                @if(!$hasPhoto)
                                                    <img class="img-fluid rounded-circle mb-3" src="{{ asset(Storage::url('avatars')) }}/avatar.png" height="110" width="110" alt="User avatar">
                                                @endif
                                                <h4>{{$employee->name}}</h4>
                                                <span class="badge bg-label-secondary mt-1">{{ !empty($employee->designation) ? $employee->designation->name : '' }}</span>
                                                <hr>
                                                @can('Show Employee Profile')
                                                    <a class="btn btn-outline-primary"
                                                        href="{{ route('company.employees.show', $employee->id) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                                @else
                                                    <a class="btn btn-outline-primary"
                                                        href="{{ route('company.employees.show', $employee->id) }}">{{ \Auth::user()->employeeIdFormat($employee->employee_id) }}</a>
                                                @endcan
                                                @if($employee['is_active'] == 3)
                                                    <span class="status_badge badge bg-warning p-2 px-3 rounded d-block mt-2">Sanctionné</span>
                                                @elseif($employee['is_active'] == 4)
                                                    <span class="status_badge badge bg-warning p-2 px-3 rounded d-block mt-2">En congé</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center mb-0">
                                    <h6 class="mb-0">Aucun employé inactif trouvé</h6>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @if($filtreActif)
                        @if($employeesInactifs->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $employeesInactifs->links() }}
                            </div>
                        @endif
                    @elseif($employeesInactifs->total() > $employeesInactifs->count())
                        {{-- Apercu limite : le reste se retrouve via la recherche --}}
                        <div class="alert alert-light border text-center mt-3 mb-0">
                            <i class="fas fa-search me-1"></i>
                            {{ $employeesInactifs->count() }} dossier(s) affiché(s) sur {{ $employeesInactifs->total() }} inactifs.
                            <a href="#search" onclick="document.getElementById('search').focus(); return false;">
                                Utilisez la recherche
                            </a>
                            pour retrouver les autres.
                        </div>
                    @endif
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

    // Toggle employé avec SweetAlert
    $('.toggle-employee').on('click', function(e) {
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