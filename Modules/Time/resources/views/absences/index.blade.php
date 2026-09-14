@php
use Carbon\Carbon;
@endphp
@extends('layouts.app')

@section('page-title')
    {{ __('Gestion des Absences') }}
@stop

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📅 Gestion des absences
                        @if($periode) | Exercice :
                         {{ $periode->exercice->nom }} - Statut : <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                        {{ ucfirst($periode->exercice->statut) }}
                        @endif
                        </span>
                    </h4>
                    <p class="text-muted mb-0">Gérez les absences des employés pour la période en cours.</p>  
                    <nav aria-label="breadcrumb"> 
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                            </li>
                            <li class="breadcrumb-item active">Absence @if($periode) - Période : {{ $periode->nom }} | Statut : <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($periode->statut) }}</span> @endif</li>
                        </ol>
                    </nav>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ ucfirst(\Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ \Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                    </small>
                </div>
                <div>
                    @if($periode)
                    <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" type="button" class="align-items-center btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Période
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAbsenceModal">
                        <i class="fas fa-plus me-2"></i>Ajouter
                    </button> 
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($periode)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Liste des Absences') }}</h5>
            <div class="d-flex">
                <div class="me-2">
                    <input type="month" class="form-control" id="month_filter" value="{{ date('Y-m') }}">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="absenceTable">
                    <thead>
                        <tr>
                            <th>{{ __('Employé') }}</th>
                            <th>{{ __('Date Début') }}</th>
                            <th>{{ __('Date Retour') }}</th>
                            <th>{{ __('Jours') }}</th>
                            <th>{{ __('Heures') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absences as $absence)
                        <tr>
                            <td>{{ $absence->employee->name ?? '' }}</td>
                            <td>{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}</td>
                            <td>{{ $absence->arrival_date ? \Carbon\Carbon::parse($absence->arrival_date)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $absence->retenue ?? '0' }}</td>
                            <td>{{ $absence->hours }}</td>
                            <td>
                                @if($absence->motif_justify == 'Oui')
                                    <span class="badge bg-label-info">{{ $absence->type_permis ?? 'Justifiée' }}</span>
                                @else
                                    <span class="badge bg-label-warning">Non justifiée</span>
                                @endif
                            </td>
                            <td>
                                @if($absence->statut == 'approved')
                                    <span class="badge bg-label-success">{{ __('Approuvée') }}</span>
                                @elseif($absence->statut == 'rejected')
                                    <span class="badge bg-label-danger">{{ __('Rejetée') }}</span>
                                @else
                                    <span class="badge bg-label-warning">{{ __('En attente') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="#" class="btn btn-icon btn-label-info me-2 view-absence" data-absence-id="{{ $absence->id }}" data-bs-toggle="modal" data-bs-target="#showAbsenceModal">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-icon bg-label-primary me-2 edit-absence" data-absence-id="{{ $absence->id }}" data-bs-toggle="modal" data-bs-target="#editAbsenceModal">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('company.times.absences.destroy', $absence->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-label-danger delete-confirm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> 
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                                    <h5 class="text-muted"> <i class="fas fa-calendar-alt me-2 text-primary"></i> Exercice : {{ $exercice->nom }}</h5>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="mb-1">
                                                    <a href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}" class="text-primary">{{ $exercice->nom }}</a>
                                                </h5>
                                                <p class="mb-1">
                                                    <span class="text-muted">Du</span>
                                                    {{ $exercice->date_debut->format('d/m/Y') }}
                                                    <span class="text-muted">au</span>
                                                    {{ $exercice->date_fin->format('d/m/Y') }}
                                                </p>
                                                <p class="mb-0">
                                                    <span class="badge bg-label-{{ $exercice->statut === 'brouillon' ? 'warning' : ($exercice->statut === 'en_cours' ? 'info' : ($exercice->statut === 'cloture' ? 'success' : 'danger')) }}">
                                                        {{ ucfirst($exercice->statut) }}
                                                    </span>
                                                    <span class="ms-2 text-muted">
                                                        Nombre de périodes : {{ $exercice->periodes->count() }}
                                                    </span>
                                                </p>  
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" type="button" id="exerciceActions">
                                                        <i class="fas fa-ellipsis-v"></i> 
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="exerciceActions">
                                                    <a class="dropdown-item" href="{{ route('company.paiesalaries.exercices.show', $exercice->id) }}">
                                                        <i class="fas fa-eye me-2"></i>Voir les exercices
                                                    </a>    
                                                    <a class="dropdown-item" href="{{ route('company.paiesalaries.exercices.edit', $exercice->id) }}">
                                                        <i class="fas fa-edit me-2"></i>Modifier
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('company.paiesalaries.exercices.destroy', $exercice->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger delete-periode">
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
@if($periode)
    <!-- Modal de création d'absence -->
    @include('time::absences.modals.create')

    <!-- Modal de visualisation d'absence -->
    <div class="modal fade" id="showAbsenceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Détails de l\'absence') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="showAbsenceModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement…</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Fermer') }}</button>
                </div>
            </div>
        </div>
    </div> 

    <!-- Modal d'édition d'absence -->
    <div class="modal fade" id="editAbsenceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Modifier l\'absence') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="editAbsenceModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement…</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation de DataTable
        var table = $('#absenceTable').DataTable({
            responsive: true,
            order: [[1, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });

        // Initialisation de Select2
        $('.select2').each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Sélectionner un élément',
                dropdownParent: $this.parent()
            });
        });

        // Filtre par mois
        $('#month_filter').on('change', function() {
            var month = $(this).val();
            window.location.href = '{{ route("company.times.absences.index") }}?month=' + month;
        });

        // Modal de visualisation
        $('.view-absence').on('click', function() {
            var absenceId = $(this).data('absence-id');

            // Mettre à jour le titre du modal
            $('#showAbsenceModal .modal-title').text('Détails de l\'absence');

            // Charger le formulaire via AJAX
            $.ajax({
                url: '{{ route("company.times.absences.show", ":id") }}'.replace(':id', absenceId),
                type: 'GET',
                success: function(response) {
                    $('#showAbsenceModalBody').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', error); // Ajout d'un log pour le débogage
                    $('#showAbsenceModalBody').html(
                        '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                    );
                }
            });
        });

        // Modal de modification
        $('.edit-absence').on('click', function() {
            var absenceId = $(this).data('absence-id');

            // Mettre à jour le titre du modal
            $('#editAbsenceModal .modal-title').text('Modifier l\'absence');

            // Charger le formulaire via AJAX
            $.ajax({
                url: '{{ route("company.times.absences.edit", ":id") }}'.replace(':id', absenceId),
                type: 'GET',
                success: function(response) {
                    $('#editAbsenceModalBody').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur:', error); // Ajout d'un log pour le débogage
                    $('#editAbsenceModalBody').html(
                        '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                    );
                }
            });
        });

        // Confirmation de suppression
        $('.delete-confirm').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: "{{ __('Êtes-vous sûr ?') }}",
                text: "{{ __('Vous ne pourrez pas revenir en arrière !') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('Oui, supprimer !') }}",
                cancelButtonText: "{{ __('Annuler') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
