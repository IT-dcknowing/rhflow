@php
    $status = "";
    $code = $retenues->count() + 601;
@endphp
@extends('layouts.app')

@section('title', 'Gestion des remboursements de frais')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">✅ Gestion des remboursements de frais
                            @if($periode) | Exercice :
                                {{ $periode->exercice->nom }} - Statut : <span
                                    class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($periode->exercice->statut) }}
                            @endif
                            </span>
                        </h4>
                        <p class="text-muted mb-0">Gérez les remboursements de frais de paie des employés</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                                </li>
                                <li class="breadcrumb-item active">Remboursements de frais @if($periode) - Période :
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
                        <div class="d-flex gap-2">
                            @if($periode)
                                <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" type="button"
                                    class="btn btn-info d-flex align-items-center">
                                    <i class="fas fa-arrow-left me-2"></i>Retour à la période
                                </a>
                                <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal"
                                    data-bs-target="#addRetenueModal">
                                    <i class="fas fa-plus me-2"></i>Ajouter un remboursement
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @if($periode)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="retenuesTableCreated">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Code</th>
                                                <th>Libellé</th>
                                                <th>Type</th>
                                                <th>Base/Montant/Taux</th>
                                                <th>Période</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($retenues as $retenue)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{$retenue->code ? $retenue->code : '-'}}
                                                    </td>
                                                    <td>{{ $retenue->libelle }}</td>
                                                    <td>{{ $retenue->typeRetenue->libelle }}</td>
                                                    <td>
                                                        <span class="badge bg-label-danger">
                                                            {{ $retenue->base ? $retenue->base : '-' }} </span> :
                                                        <span class="badge bg-label-danger">
                                                            {{ number_format($retenue->amount, 0, ',', ' ') . ' FCFA' }} </span> :
                                                        <span class="badge bg-label-info">
                                                            {{  $retenue->taux ? $retenue->taux . '%' : '-' }} </span>
                                                    </td>
                                                    <td>{{ $retenue->periode->nom }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $retenue->is_active ? 'success' : 'secondary' }}">
                                                            {{ $retenue->is_active ? 'Actif' : 'Inactif' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" class="btn btn-light btn-sm dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item align-items-center btn-add-retenue" href="#"
                                                                    data-id="{{ $retenue->id }}"
                                                                    data-libelle="{{ $retenue->libelle }}" data-bs-toggle="modal"
                                                                    data-bs-target="#addEmployeeRetenueModal">
                                                                    <i class="fas fa-plus me-2"></i>Ajouter aux employés
                                                                </a>
                                                                <a class="dropdown-item align-items-center edit-retenue" href="#"
                                                                    data-id="{{ $retenue->id }}"
                                                                    data-libelle="{{ $retenue->libelle }}" data-bs-toggle="modal"
                                                                    data-bs-target="#editRetenueModal">
                                                                    <i class="fas fa-edit me-2"></i>Modifier
                                                                </a>
                                                                @if($retenue->is_active)
                                                                    <a class="dropdown-item align-items-center deactivate-retenue"
                                                                        href="#" data-id="{{ $retenue->id }}">
                                                                        <i class="fas fa-times me-1"></i>Désactiver
                                                                    </a>
                                                                @else
                                                                    <a class="dropdown-item align-items-center activate-retenue"
                                                                        href="#" data-id="{{ $retenue->id }}">
                                                                        <i class="fas fa-check me-1"></i>Activer
                                                                    </a>
                                                                @endif
                                                                <a class="dropdown-item align-items-center show-retenue" href="#"
                                                                    data-id="{{ $retenue->id }}"
                                                                    data-libelle="{{ $retenue->libelle }}" data-bs-toggle="modal"
                                                                    data-bs-target="#showRetenueModal">
                                                                    <i class="fas fa-eye me-2"></i>Liste des employés
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                                <form
                                                                    action="{{ route('company.paiesalaries.remboursements.destroy', $retenue->id) }}"
                                                                    method="POST" class="delete-retenue-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item align-items-center text-danger">
                                                                        <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">Aucun remboursement enregistré</td>
                                                </tr>
                                            @endforelse
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
                                                    <h5 class="text-muted"> <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                                        Exercice : {{ $exercice->nom }}</h5>
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
        @if($periode)
            <!-- Modal Ajout Remboursement -->
            <div class="modal fade" id="addRetenueModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter un Remboursement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('company.paiesalaries.remboursements.store') }}" method="POST">
                            @csrf
                            <input type="text" id="periode_id" name="periode_id" value="{{$periode->id}}" hidden="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="libelle" name="libelle" required>
                                </div>
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="ordre" class="form-label">Ordre d'affichage</label>
                                        <input type="number" class="form-control" name="ordre1" id="ordre"
                                            value="{{$lastOrder->ordre + 1}}" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <input type="hidden" name="salariale1" id="salariale" value="1">
                                        <input type="hidden" name="patronale1" id="patronale" value="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="taux_type" class="form-label">Taux <span class="text-danger">*</span></label>
                                    <select class="form-select" id="taux_type" name="item_brut" required>
                                        <option value="">Sélectionnez un type</option>
                                        <option value="fixe">Montant fixe</option>
                                        <option value="percentage">Pourcentage</option>
                                    </select>
                                    <input type="number" class="form-control" id="code" name="code" value="{{$code}}" hidden>
                                    <input type="number" class="form-control" id="jours_work" name="jours_work" value="" hidden>
                                    <input type="text" class="form-control" id="base" name="base" value="" hidden>
                                    <input class="form-control" id="type_retenue_id" name="type_retenue_id" value="5" hidden
                                        required>
                                </div>
                                <div class="mb-3" id="montantField">
                                    <label for="montant" class="form-label">Montant (FCFA) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="montant" name="amount" min="0" value="0">
                                </div>
                                <div class="mb-3 d-none" id="baseField">
                                    <label for="montant" class="form-label">Base de calcul <span
                                            class="text-danger">*</span></label>
                                    <select name="base" id="selectBase" class="form-select">
                                        <option value="">Sélectionnez une base</option>
                                        <option value="sb">Salaire de base</option>
                                        <option value="sbi">Salaire brut imposable</option>
                                        <option value="sbs">Salaire brut social</option>
                                        <option value="sbt">Salaire brut total</option>
                                        <option value="sn">Salaire net</option>
                                    </select>
                                </div>
                                <div class="mb-3 d-none" id="tauxField">
                                    <label for="taux" class="form-label">Nombre (%) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="taux" name="tauxRetenue" value="0">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="statut" name="statut" checked>
                                        <label class="form-check-label" for="statut">Activer ce remboursement</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Modification Remboursement -->
            <div class="modal fade" id="editRetenueModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modifier le remboursement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="editRetenueModalBody">
                            <!-- Le contenu sera chargé dynamiquement via AJAX -->
                            <div class="text-center my-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Ajout Remboursement aux employés -->
            <div class="modal fade" id="addEmployeeRetenueModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter la retenue aux employés</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="addEmployeeRetenueModalBody">
                            <!-- Le contenu sera chargé dynamiquement via AJAX -->
                            <div class="text-center my-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Liste Remboursement aux employés -->
            <div class="modal fade" id="showRetenueModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Liste des employés</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="showRetenueModalBody">
                            <!-- Le contenu sera chargé dynamiquement via AJAX -->
                            <div class="text-center my-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
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
            $(document).ready(function () {
                // Initialisation de DataTable
                var table = $('#retenuesTableDefault').DataTable({
                    responsive: true,
                    order: [[1, 'desc']],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
                    },
                    dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                });

                // Gestion du clic sur le bouton d'ajout d'éléments
                $('.btn-add-retenue').on('click', function () {
                    var retenueId = $(this).data('id');
                    var libelle = $(this).data('libelle');
                    // Mettre à jour le titre du modal
                    $('#addEmployeeRetenueModal .modal-title').text('Ajouter le remboursement : ' + libelle);

                    // Charger le formulaire via AJAX
                    $.ajax({
                        url: '{{ route("company.paiesalaries.remboursements.add-employee", ":id") }}'.replace(':id', retenueId),
                        type: 'GET',
                        success: function (response) {
                            $('#addEmployeeRetenueModalBody').html(response);
                        },
                        error: function () {
                            $('#addEmployeeRetenueModalBody').html(
                                '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                            );
                        }
                    });
                });

                $('.show-retenue').on('click', function () {
                    var retenueId = $(this).data('id');
                    var libelle = $(this).data('libelle');
                    // Mettre à jour le titre du modal
                    $('#showRetenueModal .modal-title').text('Liste des employés du remboursement : ' + libelle);

                    // Charger le formulaire via AJAX
                    $.ajax({
                        url: '{{ route("company.paiesalaries.remboursements.add.show", ":id") }}'.replace(':id', retenueId),
                        type: 'GET',
                        success: function (response) {
                            $('#showRetenueModalBody').html(response);
                        },
                        error: function () {
                            $('#showRetenueModalBody').html(
                                '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                            );
                        }
                    });
                });

                // Gestion de l'affichage des champs selon le type
                $('#taux_type').on('change', function () {
                    if ($(this).val() === 'fixe') {
                        $('#montantField').removeClass('d-none');
                        $('#tauxField').addClass('d-none');
                        $('#taux').val('');
                        $('#baseField').addClass('d-none');
                        $('#base').val('');
                    } else if ($(this).val() === 'percentage') {
                        $('#tauxField').removeClass('d-none');
                        $('#montantField').addClass('d-none');
                        $('#montant').val('');
                        $('#baseField').removeClass('d-none');
                    }
                });

                // Gestion de l'affichage des champs selon le type
                $('#selectBase').on('change', function () {
                    var selectBase = $(this).find('option:selected').text();
                    $('#base').val(selectBase);
                });

                // Gestion du clic sur le bouton de modification
                $('.edit-retenue').on('click', function () {
                    var retenueId = $(this).data('id');
                    var libelle = $(this).data('libelle');

                    // Mettre à jour le titre du modal
                    $('#editRetenueModal .modal-title').text('Modifier le remboursement : ' + libelle);

                    // Charger le formulaire via AJAX
                    $.ajax({
                        url: '{{ route("company.paiesalaries.remboursements.edit", ":id") }}'.replace(':id', retenueId),
                        type: 'GET',
                        success: function (response) {
                            $('#editRetenueModalBody').html(response);
                        },
                        error: function () {
                            $('#editRetenueModalBody').html(
                                '<div class="alert alert-danger">Une erreur est survenue lors du chargement du formulaire.</div>'
                            );
                        }
                    });
                });

                // Fonction pour activer une retenue
                $('.activate-retenue').on('click', function () {
                    var retenueId = $(this).data('id');
                    Swal.fire({
                        title: 'Activer le remboursement',
                        text: 'Êtes-vous sûr de vouloir activer ce remboursement ?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Oui, activer',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route("company.paiesalaries.remboursements.activate", ":id") }}'.replace(':id', retenueId),
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'POST'
                                },
                                success: function (response) {
                                    Swal.fire({
                                        title: 'Succès',
                                        text: response.success,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function (xhr) {
                                    Swal.fire({
                                        title: 'Erreur',
                                        text: xhr.responseJSON?.message || 'Une erreur est survenue',
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                });

                // Fonction pour désactiver une retenue
                $('.deactivate-retenue').on('click', function () {
                    var retenueId = $(this).data('id');
                    Swal.fire({
                        title: 'Désactiver le remboursement',
                        text: 'Êtes-vous sûr de vouloir désactiver ce remboursement ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Oui, désactiver',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route("company.paiesalaries.remboursements.deactivate", ":id") }}'.replace(':id', retenueId),
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'POST'
                                },
                                success: function (response) {
                                    Swal.fire({
                                        title: 'Succès',
                                        text: response.success,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function (xhr) {
                                    Swal.fire({
                                        title: 'Erreur',
                                        text: xhr.responseJSON?.message || 'Une erreur est survenue',
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                });

                // Gestion de la suppression
                $('.delete-retenue-form').on('submit', function (e) {
                    e.preventDefault();
                    var form = $(this);

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        text: "Cette action est irréversible !",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Oui, supprimer !',
                        cancelButtonText: 'Annuler'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: form.attr('action'),
                                type: 'POST',
                                data: form.serialize() + '&_method=DELETE',
                                dataType: 'json',
                                success: function (response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'Supprimé !',
                                            response.message,
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    }
                                },
                                error: function (xhr) {
                                    Swal.fire(
                                        'Erreur !',
                                        'Une erreur est survenue lors de la suppression.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush