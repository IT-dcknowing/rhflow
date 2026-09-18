@extends('layouts.app')

@section('title', 'Gestion des ruptures')

@section('content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête de la Gestion des Congés -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">❌ Gestion des Ruptures
                            @if($periode) | Exercice :
                                {{ $periode->exercice->nom }} - <span
                                    class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($periode->exercice->statut) }}
                            @endif
                        </h4>
                        <p class="text-muted mb-0">Gérez les ruptures de contrat et sanctions des employés</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                                </li>
                                <li class="breadcrumb-item active">Gestion des ruptures @if($periode) - Période :
                                    {{ $periode->nom }} - <span
                                        class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($periode->statut) }}</span>
                                @endif</li>
                            </ol>
                        </nav>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        @if($periode)
                            <a href="{{ route('company.ruptures.create') }}?periode_id={{$periode->id}}"
                                class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Ajouter
                            </a>
                            <a href="{{ route('company.ruptures.types') }}" class="btn btn-outline-primary">
                                <i class="ti ti-settings me-1"></i> Types de rupture
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if($periode)
            <div class="card">
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <div class="row me-2">
                            <div class="col-md-2">
                                <div class="me-3">
                                    <div class="dataTables_length" id="DataTables_Table_0_length">
                                        <label>
                                            <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0"
                                                class="form-select">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div
                                    class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                    <div class="dataTables_filter me-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Rechercher..."
                                                aria-controls="DataTables_Table_0">
                                        </div>
                                    </div>
                                    <div class="dt-buttons btn-group flex-wrap">
                                        <div class="btn-group">
                                            <button class="btn btn-success dropdown-toggle" type="button"
                                                id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ti ti-file-export me-1"></i> Exporter
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                <li><a class="dropdown-item" href="javascript:void(0);">Excel</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);">PDF</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <label for="employee_filter" class="form-label">Employé</label>
                                            <select id="employee_filter" class="select2 form-select" data-allow-clear="true">
                                                <option value="">Tous les employés</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type_filter" class="form-label">Type de rupture</label>
                                            <select id="type_filter" class="select2 form-select" data-allow-clear="true">
                                                <option value="">Tous les types</option>
                                                @foreach($types as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="status_filter" class="form-label">Statut</label>
                                            <select id="status_filter" class="select2 form-select" data-allow-clear="true">
                                                <option value="">Tous les statuts</option>
                                                <option value="pending">En attente</option>
                                                <option value="approved">Approuvé</option>
                                                <option value="rejected">Rejeté</option>
                                                <option value="completed">Terminé</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <table class="datatables-users table border-top dataTable no-footer dtr-column"
                                    id="DataTables_Table_0">
                                    <thead>
                                        <tr>
                                            <th>Référence</th>
                                            <th>Employé</th>
                                            <th>Type de rupture</th>
                                            <th>Date de demande</th>
                                            <th>Date effective</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ruptures as $rupture)
                                            <tr>
                                                <td>#{{ $rupture->id }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-start align-items-center user-name">
                                                        <div class="avatar-wrapper">
                                                            <div class="avatar avatar-sm me-3">
                                                                <img src="{{ $rupture->employee->avatar ? asset('storage/' . $rupture->employee->avatar) : asset('img/avatars/1.png') }}"
                                                                    alt="Avatar" class="rounded-circle">
                                                            </div>
                                                        </div>
                                                        <div class="d-flex flex-column">
                                                            <a href="{{ route('company.employees.show', $rupture->employee->id) }}"
                                                                class="text-body text-truncate">
                                                                <span class="fw-semibold">{{ $rupture->employee->name }}</span>
                                                            </a>
                                                            <small
                                                                class="text-muted">{{ $rupture->employee->designation->name ?? '-' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $rupture->ruptureType->name }}</td>
                                                <td>{{ \Carbon\Carbon::parse($rupture->notice_date)->locale('fr')->isoFormat('DD/MM/YYYY') }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($rupture->termination_date)->locale('fr')->isoFormat('DD/MM/YYYY') }}
                                                </td>
                                                <td>
                                                    @if($rupture->status == 'pending')
                                                        <span class="badge bg-label-warning">En attente</span>
                                                    @elseif($rupture->status == 'approved')
                                                        <span class="badge bg-label-success">Approuvé</span>
                                                    @elseif($rupture->status == 'rejected')
                                                        <span class="badge bg-label-danger">Rejeté</span>
                                                    @elseif($rupture->status == 'completed')
                                                        <span class="badge bg-label-info">Terminé</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="{{ route('company.ruptures.show', $rupture->id) }}"
                                                            class="btn btn-icon btn-sm btn-label-info me-2"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Voir">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                        <a href="{{ route('company.ruptures.edit', $rupture->id) }}"
                                                            class="btn btn-icon btn-sm btn-label-warning me-2"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Modifier">
                                                            <i class="ti ti-pencil"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-icon btn-sm btn-label-danger delete-record"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer"
                                                            data-id="{{ $rupture->id }}">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">
                                    Affichage de 1 à {{ count($ruptures) > 10 ? '10' : count($ruptures) }} sur
                                    {{ count($ruptures) }} entrées
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                                    {{ $ruptures->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row mb-4">
                <!-- Périodes de l'exercice -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Exercice de paie</h5>
                            <span class="badge bg-label-primary">{{ $exercices->count() }} exercice(s)</span>
                        </div>
                        <div class="card-body">
                            @if($exercices->count() > 0)
                                <div class="timeline">
                                    @foreach($exercices as $exercice)
                                        <div class="timeline-item">
                                            <div class="timeline-badge d-flex align-items-center">
                                                <h5 class="text-muted"> <i class="fas fa-calendar-alt me-2 text-primary"></i> Exercice :
                                                    {{ $exercice->nom }}</h5>
                                            </div>
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h5 class="mb-1">
                                                                <a href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}"
                                                                    class="text-primary">{{ $exercice->nom }}</a>
                                                            </h5>
                                                            <p class="mb-1">
                                                                <span class="text-muted">Du</span>
                                                                {{ $exercice->date_debut->format('d/m/Y') }}
                                                                <span class="text-muted">au</span>
                                                                {{ $exercice->date_fin->format('d/m/Y') }}
                                                            </p>
                                                            <p class="mb-0">
                                                                <span
                                                                    class="badge bg-label-{{ $exercice->statut === 'brouillon' ? 'warning' : ($exercice->statut === 'en_cours' ? 'info' : ($exercice->statut === 'cloture' ? 'success' : 'danger')) }}">
                                                                    {{ ucfirst($exercice->statut) }}
                                                                </span>
                                                                <span class="ms-2 text-muted">
                                                                    Nombre de périodes : {{ $exercice->periodes->count() }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                        <div class="dropdown">
                                                            <button class="btn btn-outline-primary btn-sm dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-expanded="false" type="button"
                                                                id="exerciceActions">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end"
                                                                aria-labelledby="exerciceActions">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}">
                                                                    <i class="fas fa-eye me-2"></i>Voir les exercices
                                                                </a>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('company.paiesalaries.exercices.edit', $exercice->id) }}">
                                                                    <i class="fas fa-edit me-2"></i>Modifier
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                                <form
                                                                    action="{{ route('company.paiesalaries.exercices.destroy', $exercice->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger delete-periode">
                                                                        <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-alt fa-4x text-muted"></i>
                                    </div>
                                    <h5 class="mb-2">Aucun exercice</h5>
                                    <p class="text-muted mb-4">Commencez par ajouter un exercice</p>
                                    <a href="{{ route('company.paiesalaries.exercices.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Ajouter un exercice
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            'use strict';

            // Initialisation de Select2
            $('.select2').each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Sélectionner une option',
                    dropdownParent: $this.parent()
                });
            });

            // Filtrage des données
            $('#employee_filter, #type_filter, #status_filter').on('change', function () {
                // Logique de filtrage à implémenter
            });

            // Suppression d'un enregistrement
            $('.delete-record').on('click', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Cette action est irréversible!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3',
                        cancelButton: 'btn btn-label-danger'
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.value) {
                        // Logique de suppression à implémenter
                        window.location.href = '/company/ruptures/delete/' + id;
                    }
                });
            });
        });
    </script>
@endpush