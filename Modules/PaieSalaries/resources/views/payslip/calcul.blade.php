
@extends('layouts.app')

@section('title', 'Configuration des paies mensuelles')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">⚙️ Configuration des paies mensuelles @if($periode) - Exercice :                       
                         {{ $periode->exercice->nom }} - Statut : <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                        {{ ucfirst($periode->exercice->statut) }}
                        @endif
                    </span></h4>
                    <p class="text-muted mb-0">Gérez la configuration des salaires des employés</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                            </li>
                            <li class="breadcrumb-item active">Paies mensuelles</li>
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
                    <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" type="button" class="align-items-center btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la période
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($periode)
    <div class="card">
        <div class="card-header">
           <h5><i class="fas fa-eye"></i> Prévisualisation de la paie mensuelle @if($periode) - Période : {{ $periode->nom }} | Statut : <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($periode->statut) }}</span> @endif</h5>
            <div class="alert alert-warning">
                <strong>Prévisualisation :</strong> Consultez la paie mensuelle pour <b>tous les employés mensuels du mois sélectionné</b>. Vous pouvez vérifier les détails avant de procéder au traitement effectif de la paie.
            </div>
        </div>
        <div class="card-body"> 
            <!-- Barre de recherche et pagination -->
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <input type="text" id="previous-search" class="form-control" style="width: 300px;" placeholder="Rechercher un employé mensuel...">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="previous-pagination-info" class="text-muted"></span>
                    <button id="previous-prev" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button id="previous-next" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <button id="ExcelToPrevious" class="btn btn-success btn-sm">
                        <i class="fas fa-file-pdf me-2"></i> {{ __('Exporter en excel') }}
                    </button>
                </div>
            </div>
            <!-- Table responsive -->
            <div class="table-responsive">
                <table class="table table-hover" id="previous-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">ID Employé</th>
                            <th>Nom & Prénoms</th>
                            <th>Jours travaillés</th>
                            <th class="text-center">Salaire de base</th>
                            <th class="brut-total text-center">Brut Total</th>
                            <th class="text-center">Prêts & Autres</th>
                            <th class="net-salary text-center">Salaire Net</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $tota_net_previous = 0;
                            $total_brut_previous = 0;
                        @endphp
                        @forelse($employees as $listemp)
                            @if($listemp->is_active == 1)
                                <tr>
                                    <td align="center">
                                        {{ $listemp->employee_id }}
                                    </td>
                                    <td >{{ $listemp->name }}</td>
                                    <td align="center">
                                        <strong class="text-danger">{{ $listemp->tax_payer_id ?? '-' }}</strong> 
                                        <hr> 
                                        Mise à jour ici 👉 
                                        <a class="btn btn-primary btn-sm btn-update-days" href="#" data-bs-toggle="modal" data-bs-size="xl" data-bs-target="#showDaysWorkModal"
                                                data-employee-id="{{ $listemp->id }}" data-employee-name="{{ $listemp->name }}" data-periode-id="{{ $periode->id }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                    <td align="right">
                                        @php
                                            // Même logique que get_brut_salary() : proratisation sur 30 jours
                                            $jours_travailles = intval($listemp->get_jours_work($periode->id));
                                            if ($jours_travailles <= 0) $jours_travailles = 30;
                                            $base_salary_proratise = ($jours_travailles == 30)
                                                ? $listemp->salary
                                                : round(($listemp->salary / 30) * $jours_travailles);
                                        @endphp
                                        {{ number_format($base_salary_proratise, 0, '.', ' ') }} FCFA
                                        @if($jours_travailles != 30)
                                            <br>
                                            <small class="text-muted">
                                                ({{ number_format($listemp->salary, 0, '.', ' ') }} × {{ $jours_travailles }}/30)
                                            </small>
                                        @endif
                                    </td>
                                    <td align="right">
                                        {{ number_format($listemp->get_brut_salary($periode->id), 0, '.', ' ') }} FCFA
                                    </td>
                                    <td  align="right">
                                        Prêt: {{ number_format($listemp->get_loan_retenue($periode->id), 0, '.', ' ') }} FCFA <br>
                                        A. Retenue: {{ number_format($listemp->get_Autre_retenue($periode->id), 0, '.', ' ') }} FCFA <br>
                                        Remboursement: {{ number_format($listemp->get_Rembourssement($periode->id), 0, '.', ' ') }} FCFA
                                    </td>
                                    <td class="net-salary" align="right">
                                        <strong class="badge rounded bg-label-black p-1" style="background-color:#000; color:yellow;">
                                            @if(!empty($periode))
                                                {{ number_format($listemp->get_net_salary($periode->id), 0, '.', ' ') }}
                                            @else
                                                0  
                                            @endif FCFA
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <!-- Bouton Ajouter éléments -->
                                            <a class="dropdown-item align-items-center btn-add-elements" href="#" data-bs-toggle="modal" data-bs-size="xl" data-bs-target="#addElementsModal"
                                                data-employee-id="{{ $listemp->id }}" data-employee-name="{{ $listemp->name }}" data-periode-id="{{ $periode->id }}">
                                                <i class="fas fa-plus me-2"></i>Ajouter des éléments
                                            </a>

                                            <!-- Bouton Afficher éléments -->
                                            <a class="dropdown-item align-items-center btn-show-elements" href="#" data-bs-toggle="modal" data-bs-size="xl" data-bs-target="#showElementsModal"
                                                data-employee-id="{{ $listemp->id }}" data-employee-name="{{ $listemp->name }}" data-periode-id="{{ $periode->id }}">
                                                <i class="fas fa-eye me-2"></i>Afficher des éléments
                                            </a>

                                            <!-- Bouton Modifier éléments -->
                                            <a class="dropdown-item align-items-center btn-edit-elements" href="#" data-bs-toggle="modal" data-bs-size="xl" data-bs-target="#editElementsModal"
                                                data-employee-id="{{ $listemp->id }}" data-employee-name="{{ $listemp->name }}" data-periode-id="{{ $periode->id }}">
                                                <i class="fas fa-pencil me-2"></i>Modifier des éléments
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <!-- Bouton Aperçu bulletin -->
                                            <a href="#" class="dropdown-item align-items-center bg-label-info btn-aperçu-bulletin" 
                                                data-bs-toggle="modal" data-bs-size="xl" data-bs-target="#showBulletinModal" data-employee-id="{{ $listemp->id }}" data-employee-name="{{ $listemp->name }}"
                                                data-exercice-id="{{ $periode->exercice->id }}" data-periode-id="{{ $periode->id }}">
                                                <i class="fas fa-eye me-2"></i>Aperçu du bulletin
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @php
                                $tota_net_previous += $listemp->get_net_salary($periode->id);
                                $total_brut_previous += $listemp->get_brut_salary($periode->id);
                            @endphp
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">{{ __('Aucun employé mensuel pour ce mois.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="6" class="text-end"><strong>Total :</strong></td>
                            <td class="text-end">
                                <strong class="badge rounded bg-label-black p-1" style="background-color:#000; color:yellow;">
                                    {{ number_format($tota_net_previous, 0, '.', ' ') }} FCFA
                                </strong>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
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
                                                        Nombre de pariodes : {{ $exercice->periodes->count() }}
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
<!-- Modification du nombre de jour travaillés -->
<div class="modal fade" id="showDaysWorkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-header modal-header">
                <h5 class="modal-title mb-3">Modifier le nombre de jours travaillés</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateDaysWork" method="POST">
                @csrf
                <input type="text" id="periode_id" name="periode_id" value="{{$periode->id}}" hidden="">
                <div id="modalBodyContentUpdate">
                    <!-- Le contenu sera chargé dynamiquement via AJAX -->
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour ajouter des éléments -->
<div class="modal fade" id="addElementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-header modal-header">
                <h5 class="modal-title mb-3">Ajouter des éléments de paie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('company.paiesalaries.allowance.store') }}" id="addElementsForm" method="POST">
                @csrf
                <input type="text" id="periode_id" name="periode_id" value="{{$periode->id}}" hidden="">
                <div class="modal-body" id="modalBodyContent">
                    <!-- Le contenu sera chargé dynamiquement via AJAX -->
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div> 
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour afficher des éléments -->
<div class="modal fade" id="showElementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-header modal-header">
                <h5 class="modal-title mb-3">Historique des éléments de paie </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContentShow">
                <!-- Le contenu sera chargé dynamiquement via AJAX -->
                <div class="text-center my-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour modifier des éléments -->
<div class="modal fade" id="editElementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-header modal-header">
                <h5 class="modal-title mb-3">Modifier des éléments de paie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editElementsForm" method="POST">
                @csrf
                <input type="text" id="periode_id" name="periode_id" value="{{$periode->id}}" hidden="">
                <div class="modal-body" id="modalBodyContentEdit">
                    <!-- Le contenu sera chargé dynamiquement via AJAX -->
                    <div class="text-center my-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour afficher le bulletin -->
<div class="modal fade" id="showBulletinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="card-header modal-header">
                <h5 class="modal-title mb-3">Afficher le bulletin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContentShowBulletin">
                <!-- Le contenu sera chargé dynamiquement via AJAX -->
                <div class="text-center my-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Gérer l'ouverture du modal pour le nombre de jour travaillés 
            $('.btn-update-days').on('click', function() {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var periodeId = $(this).data('periode-id');
                
                // Mettre à jour le titre du modal
                $('#showDaysWorkModal .modal-title').text('Modifier le nombre de jours travaillés pour ' + employeeName);
                
                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.show", [":id" , ":periode_id"] ) }}'.replace(':id', employeeId).replace(':periode_id', periodeId),
                    type: 'GET',
                    success: function(response) {
                        $('#modalBodyContentUpdate').html(response);
                        $('#showDaysWorkModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        $('#modalBodyContentUpdate').html('<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>');
                    }
                });
            });

            // Gérer l'ouverture du modal d'ajout d'éléments
            $('.btn-add-elements').on('click', function() {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var periodeId = $(this).data('periode-id');

                // Mettre à jour le titre du modal
                $('#addElementsModal .modal-title').text('Ajouter des éléments pour ' + employeeName);
                
                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.allowance.create", [":id" , ":periode_id"]) }}'.replace(':id', employeeId).replace(':periode_id', periodeId),
                    type: 'GET',
                    success: function(response) {
                        $('#modalBodyContent').html(response);
                    },
                    error: function() {
                        $('#modalBodyContent').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });

            // Gérer l'ouverture du modal d'affichage d'éléments
            $('.btn-show-elements').on('click', function() {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var periodeId = $(this).data('periode-id');
                
                // Mettre à jour le titre du modal
                $('#showElementsModal .modal-title').text('Historique des éléments de paie - ' + employeeName);
                
                // Charger le formulaire via AJAX 
                $.ajax({
                    url: '{{ route("company.paiesalaries.allowance.show", [":id", ":periode_id"]) }}'.replace(':id', employeeId).replace(':periode_id', periodeId),
                    type: 'GET',
                    success: function(response) {
                        $('#modalBodyContentShow').html(response);
                    },
                    error: function() {
                        $('#modalBodyContentShow').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });

            // Gérer l'ouverture du modal de modification des éléments
            $('.btn-edit-elements').on('click', function() {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var periodeId = $(this).data('periode-id');
                
                // Mettre à jour le titre du modal
                $('#editElementsModal .modal-title').text('Modifier des éléments pour ' + employeeName);
                
                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.allowance.edit", [":id", ":periode_id"]) }}'
                        .replace(':id', encodeURIComponent(employeeId))
                        .replace(':periode_id', encodeURIComponent(periodeId)),
                    type: 'GET',
                    success: function(response) {
                        $('#modalBodyContentEdit').html(response);
                    },
                    error: function() {
                        $('#modalBodyContentEdit').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });

            // Gérer l'ouverture du modal d'aperçu du bulletins
            $('.btn-aperçu-bulletin').on('click', function() {
                var employeeId = $(this).data('employee-id');
                var employeeName = $(this).data('employee-name');
                var exerciceId = $(this).data('exercice-id');
                var periodeId = $(this).data('periode-id');
                
                // Mettre à jour le titre du modal
                $('#showBulletinModal .modal-title').text('Aperçu du bulletin - ' + employeeName);
                
                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.preview-bulletin", [":id", ":exercice_id", ":periode_id"]) }}'
                        .replace(':id', encodeURIComponent(employeeId))
                        .replace(':exercice_id', encodeURIComponent(exerciceId))
                        .replace(':periode_id', encodeURIComponent(periodeId)),
                    type: 'GET',
                    success: function(response) {
                        $('#modalBodyContentShowBulletin').html(response);
                    },
                    error: function() {
                        $('#modalBodyContentShowBulletin').html(
                            '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                        );
                    }
                });
            });
        });
    </script>
@endpush
