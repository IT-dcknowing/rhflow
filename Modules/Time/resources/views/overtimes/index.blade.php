@extends('layouts.app')

@section('page-title')
{{ __('Gestion des Heures Supplémentaires') }}
@stop

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Gestion des Heures Supplémentaires
                            @if($periode) | Exercice :
                                {{ $periode->exercice->nom }} - Statut : <span
                                    class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($periode->exercice->statut) }}
                            @endif
                            </span>
                        </h4>
                        <p class="text-muted mb-0">Gérez les heures supplémentaires des employés pour la période en cours.
                        </p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                                </li>
                                <li class="breadcrumb-item active">Heures Supplémentaires @if($periode) - Période :
                                    {{ $periode->nom }} | Statut : <span
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
                    <div>
                        @if($periode)
                            <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" type="button"
                                class="align-items-center btn btn-info">
                                <i class="fas fa-arrow-left me-2"></i>Période
                            </a>

                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#overtimeCreateModal">
                                <i class="fa fa-plus me-2"></i> Ajouter
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if($periode)
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="card-title">{{ __('Liste des Heures Supplémentaires') }}
                                        ({{$overtimes->count()}})</h4>
                                </div>
                                <div class="col-6 text-end">
                                    <a href="{{ route('company.times.overtime.export') }}"
                                        class="btn btn-success btn-sm align-items-center">
                                        <i class="fa fa-file-excel me-2"></i> {{ __('Exporter') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="month_filter">{{ __('Filtrer par mois') }}</label>
                                        <input type="month" id="month_filter" name="month" class="form-control"
                                            value="{{ $selectedMonth ?? '' }}">
                                    </div>
                                </div>
                                @if(auth()->user()->type != 'employee')
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="employee_filter">{{ __('Filtrer par employé') }}</label>
                                            <select id="employee_filter" class="form-select">
                                                <option value="">{{ __('Tous les employés') }}</option>
                                                @foreach($filtreEmployees as $id => $name)
                                                    <option value="{{ $id }}">{{ $id }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status_filter">{{ __('Filtrer par statut') }}</label>
                                        <select id="status_filter" class="form-select">
                                            <option value="">{{ __('Tous les statuts') }}</option>
                                            <option value="pending">{{ __('En attente') }}</option>
                                            <option value="approved">{{ __('Approuvé') }}</option>
                                            <option value="rejected">{{ __('Rejeté') }}</option>
                                            <option value="paid">{{ __('Payé') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="overtime-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('ID') }}</th>
                                            <th>{{ __('Employé') }}</th>
                                            <th>{{ __('Date de début') }}</th>
                                            <th>{{ __('Date de fin') }}</th>
                                            <th>{{ __('Heures totales') }}</th>
                                            <th>{{ __('Montant') }}</th>
                                            <th>{{ __('Statut') }}</th>
                                            <th>{{ __('Période') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($overtimes as $overtime)
                                            <tr>
                                                <td>{{ $overtime->id }}</td>
                                                <td>{{ $overtime->employee->name ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($overtime->start_date)->format('d/m/Y H:i') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($overtime->end_date)->format('d/m/Y H:i') }}</td>
                                                <td>{{ $overtime->quar_heure + $overtime->heure_audd + $overtime->heure_nuit_ferie + $overtime->heure_dim_ferie + $overtime->heure_nuit_dim_ferie }}
                                                </td>
                                                <td>{{ number_format($overtime->montant, 0, ',', ' ') }} {{ __('FCFA') }}</td>
                                                <td>
                                                    @if($overtime->paid == 'paid')
                                                        <span class="badge bg-success">{{ __('Payé') }}</span>
                                                    @else
                                                        <span
                                                            class="badge bg-{{ $overtime->statut == 'approved' ? 'primary' : ($overtime->statut == 'rejected' ? 'danger' : 'warning') }}">
                                                            {{ __(ucfirst($overtime->statut) == 'approved' ? 'Approuvé' : ($overtime->statut == 'rejected' ? 'Rejeté' : 'En attente')) }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $overtime->periode->nom }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-icon btn-sm btn-label-info view-overtime"
                                                            data-id="{{ $overtime->id }}"
                                                            data-periode="{{ $overtime->periode->nom }}" data-bs-toggle="tooltip"
                                                            title="{{ __('Voir') }}">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                        <a href="{{ route('company.times.overtime.edit', $overtime->id) }}"
                                                            class="btn btn-icon btn-sm btn-label-warning edit-overtime"
                                                            title="{{ __('Modifier') }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-icon btn-sm btn-label-danger delete-overtime"
                                                            data-id="{{ $overtime->id }}" data-bs-toggle="tooltip"
                                                            title="{{ __('Supprimer') }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                        @if(!$overtime->paid && $overtime->statut == 'approved')
                                                            <button type="button"
                                                                class="btn btn-icon btn-sm btn-label-success mark-as-paid"
                                                                data-id="{{ $overtime->id }}" data-bs-toggle="tooltip"
                                                                title="{{ __('Marquer comme payé') }}">
                                                                <i class="fa fa-check"></i>
                                                            </button>
                                                        @endif
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
        @if($periode)
            <!-- Modal de création -->
            @include('time::overtimes.modals.create')

            <!-- Modal d'édition -->
            <div class="modal fade" id="overtimeEditModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Modifier Heure Supplémentaire') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="editOvertimeContent">
                            <!-- Le contenu sera chargé dynamiquement via AJAX -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de visualisation -->
            <div class="modal fade" id="overtimeShowModal" tabindex="-1" aria-hidden="true">
                <!-- Le contenu sera chargé dynamiquement via AJAX -->
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialisation du sélecteur de date et d'heure
            $('.datetimepicker').flatpickr({
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: true,
                locale: 'fr',
                onChange: function (selectedDates, dateStr, instance) {
                    updateEditPeriod();
                }
            });

            // Initialisation de DataTable
            var table = $('#overtime-table').DataTable({
                responsive: true,
                order: [[2, 'desc']], // Trier par date de début par défaut
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
                }
            });

            // Initialisation de Select2
            $('.select2').each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Sélectionner un employé',
                    dropdownParent: $this.parent()
                });
            });

            // Filtrage par mois
            $('#month_filter').on('change', function () {
                var month = $(this).val();
                window.location.href = '{{ route("company.times.overtime.index") }}?month=' + month;
            });

            // Filtrage par employé
            $('#employee_filter').on('change', function () {
                table.column(1).search($(this).val()).draw();
            });

            // Filtrage par statut
            $('#status_filter').on('change', function () {
                table.column(6).search($(this).val()).draw();
            });

            // Gestion de la soumission du formulaire de création
            $('#overtimeCreateForm').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route("company.times.overtime.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            $('#overtimeCreateModal').modal('hide');
                            toastr.success('Heure supplémentaire enregistrée avec succès.', 'Succès');
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error(errorMessage, 'Erreur');
                        }
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        $.each(errors, function (key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        toastr.error(errorMessage, 'Erreur');
                    }
                });
            });

            // Chargement du modal d'édition
            $('.edit-overtime').on('click', function () {
                var overtimeId = $(this).data('overtime-id');

                // Mettre à jour le titre du modal
                $('#overtimeEditModal .modal-title').text('Modification de l\'heure supplémentaire');

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.times.overtime.edit", ":id") }}'.replace(':id', overtimeId),
                    type: 'GET',
                    success: function (response) {
                        $('#editOvertimeContent').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Erreur:', error); // Ajout d'un log pour le débogage
                        $('#editOvertimeContent').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });

            // Soumission du formulaire d'édition
            $(document).on('submit', '#overtimeEditForm', function (e) {
                e.preventDefault();

                var formData = new FormData(this);
                var overtimeId = $('#overtime_id').val();

                $.ajax({
                    url: '{{ route("company.times.overtime.update", ":id") }}'.replace(":id", overtimeId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#overtimeEditModal').modal('hide');
                            toastr.success('Succès', response.message, 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error('Erreur', response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        $.each(errors, function (key, value) {
                            errorMessage += value[0] + '\n';
                        });

                        toastr.error('Erreur', errorMessage, 'error');
                    }
                });
            });

            // Affichage des détails d'une heure supplémentaire
            $(document).on('click', '.view-overtime', function () {
                var overtimeId = $(this).data('id');
                var periode = $(this).data('periode');
                $.ajax({
                    url: '{{ route("company.times.overtime.show", ":id") }}'.replace(":id", overtimeId),
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            var overtime = response.data;
                            var html = `
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Détails de l'heure supplémentaire #${overtime.id}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p><strong>Employé:</strong> ${overtime.employee ? overtime.employee.name : 'N/A'}</p>
                                                        <p><strong>Date de début:</strong> ${formatDate(overtime.start_date)}</p>
                                                        <p><strong>Date de fin:</strong> ${formatDate(overtime.end_date)}</p>
                                                        <p><strong>Période:</strong> ${periode}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Statut:</strong> ${getStatusBadge(overtime.statut, overtime.paid)}</p>
                                                        <p><strong>Taux horaire:</strong> ${parseFloat(overtime.taux_hour).toLocaleString()} FCFA</p>
                                                        <p><strong>Montant total:</strong> ${Math.round(parseFloat(overtime.montant)).toLocaleString()} FCFA</p>
                                                    </div>
                                                </div>

                                                <div class="table-responsive mb-3">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2">Type d'heure</th>
                                                                <th>Nombre d'heures</th>
                                                                <th>Montant</th>
                                                            </tr>   
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">De la 41è à la 46è heure</td>
                                                                <td>${parseFloat(overtime.quar_heure).toLocaleString()}</td>
                                                                <td>
                                                                    ${overtime.quar_heure > 0 ? Math.round((parseFloat(overtime.quar_heure) * parseFloat(overtime.taux_hour) * 0.15) + parseFloat(overtime.taux_hour)).toLocaleString() : 0}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">Au délà de la 46è heure</td>
                                                                <td>${parseFloat(overtime.heure_audd).toLocaleString()}</td>
                                                                <td>
                                                                    ${overtime.heure_audd > 0 ? Math.round((parseFloat(overtime.heure_audd) * parseFloat(overtime.taux_hour) * 0.50) + parseFloat(overtime.taux_hour)).toLocaleString() : 0}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">Nuit (autres jours que jours fériés et dimanche)</td>
                                                                <td>${parseFloat(overtime.heure_nuit_ferie).toLocaleString()}</td>
                                                                <td>
                                                                    ${overtime.heure_nuit_ferie > 0 ? Math.round((parseFloat(overtime.heure_nuit_ferie) * parseFloat(overtime.taux_hour) * 0.75) + parseFloat(overtime.taux_hour)).toLocaleString() : 0}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">Journée dimanche et jours fériés</td>
                                                                <td>${parseFloat(overtime.heure_dim_ferie).toLocaleString()}</td>
                                                                <td>
                                                                    ${overtime.heure_dim_ferie > 0 ? Math.round((parseFloat(overtime.heure_dim_ferie) * parseFloat(overtime.taux_hour) * 0.75) + parseFloat(overtime.taux_hour)).toLocaleString() : 0}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">Nuit dimanche et jours fériés</td>
                                                                <td>${parseFloat(overtime.heure_nuit_dim_ferie).toLocaleString()}</td>
                                                                <td>
                                                                    ${overtime.heure_nuit_dim_ferie > 0 ? Math.round((parseFloat(overtime.heure_nuit_dim_ferie) * parseFloat(overtime.taux_hour) * 1) + parseFloat(overtime.taux_hour)).toLocaleString() : 0}
                                                                </td>
                                                            </tr>
                                                            <tr class="table-active">
                                                                <td colspan="2"><strong>Total</strong></td>
                                                                <td>${(parseFloat(overtime.quar_heure) + parseFloat(overtime.heure_audd) + parseFloat(overtime.heure_nuit_ferie) + parseFloat(overtime.heure_dim_ferie) + parseFloat(overtime.heure_nuit_dim_ferie)).toLocaleString()}</td>
                                                                <td><strong>${parseFloat(Math.round(overtime.montant)).toLocaleString()} FCFA</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label"><strong>Remarques:</strong></label>
                                                    <p>${overtime.remark || 'Aucune remarque.'}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-between">
                                                <button type="button" class="btn btn-success mark-as-paid ${overtime.statut == 'approved' || overtime.paid == 'paid' ? 'd-none' : ''}" data-id="${overtime.id}">
                                                    <i class="fa fa-check me-2"></i> Marquer comme payé
                                                </button>
                                                <button type="button" class="btn btn-danger mark-as-unpaid ${overtime.statut == 'pending' || overtime.statut == 'rejected' ? 'd-none' : ''}" data-id="${overtime.id}">
                                                    <i class="fa fa-check me-2"></i> Marquer comme annulé
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                `;

                            $('#overtimeShowModal').html(html).modal('show');
                        } else {
                            toastr.error('Erreur', response.message, 'error');
                        }
                    },
                    error: function () {
                        toastr.error('Erreur', 'Une erreur est survenue lors du chargement des détails.', 'error');
                    }
                });
            });

            // Suppression d'une heure supplémentaire
            $(document).on('click', '.delete-overtime', function () {
                var overtimeId = $(this).data('id');

                Swal.fire({
                    title: 'Êtes-vous sûr ?',
                    text: "Vous ne pourrez pas revenir en arrière !",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("company.times.overtime.destroy", ":id") }}'.replace(':id', overtimeId),
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    toastr.success('Succès', response.message, 'success');
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    toastr.error('Erreur', response.message, 'error');
                                }
                            },
                            error: function () {
                                toastr.error('Erreur', 'Une erreur est survenue lors de la suppression.', 'error');
                            }
                        });
                    }
                });
            });

            // Marquer comme payé
            $(document).on('click', '.mark-as-paid', function () {
                var overtimeId = $(this).data('id');

                $.ajax({
                    url: '{{ route("company.times.overtime.mark-as-paid", ":id") }}'.replace(':id', overtimeId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success('Succès', response.message, 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error('Erreur', response.message, 'error');
                        }
                    },
                    error: function () {
                        toastr.error('Erreur', 'Une erreur est survenue lors du marquage comme payé.', 'error');
                    }
                });
            });

            $(document).on('click', '.mark-as-unpaid', function () {
                var overtimeId = $(this).data('id');

                $.ajax({
                    url: '{{ route("company.times.overtime.mark-as-unpaid", ":id") }}'.replace(':id', overtimeId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success('Succès', response.message, 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            toastr.error('Erreur', response.message, 'error');
                        }
                    },
                    error: function () {
                        toastr.error('Erreur', 'Une erreur est survenue lors du marquage comme non payé.', 'error');
                    }
                });
            });

            // Fonction utilitaire pour formater la date
            function formatDate(dateString) {
                if (!dateString) return 'N/A';

                var date = new Date(dateString);
                return date.toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Fonction utilitaire pour obtenir le badge de statut
            function getStatusBadge(status, isPaid) {
                if (isPaid) {
                    return '<span class="badge bg-success">Payé</span>';
                }

                var badgeClass = 'warning';
                var statusText = status;

                switch (status) {
                    case 'approved':
                        badgeClass = 'primary';
                        statusText = 'Approuvé';
                        break;
                    case 'rejected':
                        badgeClass = 'danger';
                        statusText = 'Rejeté';
                        break;
                    case 'pending':
                    default:
                        statusText = 'En attente';
                        break;
                }

                return '<span class="badge bg-' + badgeClass + '">' + statusText + '</span>';
            }

            function calculateEditOvertime() {
                // Récupération des valeurs des champs
                var brut = parseFloat($('#brut').val()) || 0;
                var tauxHoraire = brut / 173.33; // Calcul du taux horaire à partir du brut

                // Récupération des heures saisies
                var quarHeure = parseFloat($('#edit_quar_heure').val()) || 0;
                var heureAudd = parseFloat($('#edit_heure_audd').val()) || 0;
                var heureNuitFerie = parseFloat($('#edit_heure_nuit_ferie').val()) || 0;
                var heureDimFerie = parseFloat($('#edit_heure_dim_ferie').val()) || 0;
                var heureNuitDimFerie = parseFloat($('#edit_heure_nuit_dim_ferie').val()) || 0;

                // Calcul des majorations
                // 1. Heures 41-46 : majoration de 15%
                var montantQuarHeure = (tauxHoraire * 1.15) * quarHeure;

                // 2. Heures > 46 : majoration de 50%
                var montantHeureAudd = (tauxHoraire * 1.5) * heureAudd;

                // 3. Nuit fériée : majoration de 75%
                var montantNuitFerie = (tauxHoraire * 1.75) * heureNuitFerie;

                // 4. Dimanche/jour férié : majoration de 75%
                var montantDimFerie = (tauxHoraire * 1.75) * heureDimFerie;

                // 5. Nuit dimanche/férié : majoration de 100%
                var montantNuitDimFerie = (tauxHoraire * 2.0) * heureNuitDimFerie;

                // Calcul du total des heures et du montant total
                var totalHeures = quarHeure + heureAudd + heureNuitFerie + heureDimFerie + heureNuitDimFerie;
                var montantTotal = montantQuarHeure + montantHeureAudd + montantNuitFerie + montantDimFerie + montantNuitDimFerie;

                // Mise à jour de l'interface utilisateur
                $('#edit_montant_quar_heure').text(Math.round(montantQuarHeure).toLocaleString('fr-FR'));
                $('#edit_montant_heure_audd').text(Math.round(montantHeureAudd).toLocaleString('fr-FR'));
                $('#edit_montant_nuit_ferie').text(Math.round(montantNuitFerie).toLocaleString('fr-FR'));
                $('#edit_montant_dim_ferie').text(Math.round(montantDimFerie).toLocaleString('fr-FR'));
                $('#edit_montant_nuit_dim_ferie').text(Math.round(montantNuitDimFerie).toLocaleString('fr-FR'));

                $('#edit_total_heures').text(totalHeures.toFixed(2));
                $('#edit_montant_total').text(Math.round(montantTotal).toLocaleString('fr-FR'));

                // Mise à jour des champs cachés
                $('#edit_montant').val(Math.round(montantTotal));

                // Mise à jour du taux horaire affiché
                $('#taux_quar_heure').text(Math.round(tauxHoraire).toLocaleString('fr-FR'));
                $('#taux_hour').val(Math.round(tauxHoraire));
            }
        });
    </script>
@endpush