@php
    $status = "";
    $code = $retenues->count() + 501; 
@endphp
@extends('layouts.app')

@section('title', 'Gestion des retenues')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Gestion des retenues
                            @if($periode) | Exercice :
                                {{ $periode->exercice->nom }} - Statut : <span
                                    class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($periode->exercice->statut) }}
                            @endif
                            </span>
                        </h4>
                        <p class="text-muted mb-0">Gérez les retenues de paie des employés</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                                </li>
                                <li class="breadcrumb-item active">Retenues @if($periode) - Période : {{ $periode->nom }} |
                                    Statut : <span
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
                                <i class="fas fa-arrow-left me-2"></i>Retour à la période
                            </a>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addRetenueModal">
                                <i class="fas fa-plus me-2"></i>Ajouter une retenue
                            </button>
                            {{--
                            <button type="button" class="btn btn-success" id="btnApplyAllDefault">
                                <i class="fas fa-check-double me-2"></i>Appliquer à tous
                            </button>
                            --}}
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
                            <ul class="nav nav-tabs" id="retenuesTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="retenues-tab-default" data-bs-toggle="tab"
                                        data-bs-target="#retenues-default" type="button" role="tab"
                                        aria-controls="retenues-default" aria-selected="true">Retenues par défaut</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="retenues-tab-created" data-bs-toggle="tab"
                                        data-bs-target="#retenues-created" type="button" role="tab"
                                        aria-controls="retenues-created" aria-selected="true">Retenues créées</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="retenuesTabContent">
                                <div class="tab-pane fade show active" id="retenues-default" role="tabpanel"
                                    aria-labelledby="retenues-tab-default">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered" id="retenuesTableDefault">
                                            <thead>
                                                <tr>
                                                    <th>Employé</th>
                                                    <th>Salaire Brut Imposable</th>
                                                    <th>Salaire Brut Social</th>
                                                    <th>Charges Employé</th>
                                                    <th>Charges Employeur</th>
                                                    <th>Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($employees as $employee)
                                                    <tr>
                                                        <td>
                                                            {{ $employee->name }}<br>
                                                            <span class="text-muted badge bg-label-success">-
                                                                {{ $employee->department->name ?? '-' }} </span><br>
                                                            <span class="text-muted badge bg-label-primary">-
                                                                {{ $employee->designation->name ?? '-' }} </span>
                                                        </td>
                                                        <td align="right">
                                                            {{ number_format($employee->get_salary_imposable($periode->id), 0, ',', ' ') . ' FCFA' ?? '-' }}
                                                        </td>
                                                        <td align="right">
                                                            {{ number_format($employee->get_salary_social($periode->id), 0, ',', ' ') . ' FCFA' ?? '-' }}
                                                        </td>
                                                        <td align="right">
                                                            {{ number_format($employee->get_retenue($periode->id), 0, ',', ' ') . ' FCFA' ?? '-' }}
                                                        </td>
                                                        <td align="right">
                                                            {{ number_format($employee->get_patronale($periode->id), 0, ',', ' ') . ' FCFA' ?? '-' }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $status = $employee->retenues()
                                                                    ->where('periode_id', $periode->id)
                                                                    ->where(function ($query) {
                                                                        $query->where('type', 'default')
                                                                            ->orWhereIn('code', [301, 302, 403]);
                                                                    })
                                                                    ->count();
                                                            @endphp
                                                            {{-- Appliquées automatiquement chaque mois (SalaryService) : aucune action manuelle --}}
                                                            @if($status > 0)
                                                                <span class="badge bg-success" title="Calculées et enregistrées automatiquement">
                                                                    Appliqué automatiquement
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning" title="Bulletins déjà générés avant l'application automatique">
                                                                    Non appliqué
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">Aucune retenue trouvée</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="retenues-created" role="tabpanel"
                                    aria-labelledby="retenues-tab-created">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="retenuesTableCreated">
                                            <thead>
                                                <tr>
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
                                                        <td>
                                                            {{$retenue->code ? $retenue->code : '-'}}
                                                        </td>
                                                        <td>{{ $retenue->libelle }}</td>
                                                        <td>{{ $retenue->typeRetenue->libelle }}</td>
                                                        <td>
                                                            <span class="badge bg-label-danger">
                                                                {{ $retenue->base ? $retenue->base : '-' }} </span> :
                                                            <span class="badge bg-label-danger">
                                                                {{ number_format($retenue->amount, 0, ',', ' ') . ' FCFA' }} </span>
                                                            :
                                                            <span class="badge bg-label-info">
                                                                {{  $retenue->taux ? $retenue->taux . '%' : '-' }} </span>
                                                        </td>
                                                        <td>{{ $retenue->periode->nom }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $retenue->is_active ? 'success' : 'secondary' }}">
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
                                                                    <a class="dropdown-item align-items-center btn-add-retenue"
                                                                        href="#" data-id="{{ $retenue->id }}"
                                                                        data-libelle="{{ $retenue->libelle }}"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#addEmployeeRetenueModal">
                                                                        <i class="fas fa-plus me-2"></i>Ajouter aux employés
                                                                    </a>
                                                                    <a class="dropdown-item align-items-center edit-retenue"
                                                                        href="#" data-id="{{ $retenue->id }}"
                                                                        data-libelle="{{ $retenue->libelle }}"
                                                                        data-bs-toggle="modal" data-bs-target="#editRetenueModal">
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
                                                                    <a class="dropdown-item align-items-center show-retenue"
                                                                        href="#" data-id="{{ $retenue->id }}"
                                                                        data-libelle="{{ $retenue->libelle }}"
                                                                        data-bs-toggle="modal" data-bs-target="#showRetenueModal">
                                                                        <i class="fas fa-eye me-2"></i>Liste des employés
                                                                    </a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <form
                                                                        action="{{ route('company.paiesalaries.retenues.destroy', $retenue->id) }}"
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
                                                        <td colspan="6" class="text-center">Aucune retenue enregistrée</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
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
                                                    {{ $exercice->nom }}
                                                </h5>
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
        <!-- Modal Ajout Retenue -->
        <div class="modal fade" id="addRetenueModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une retenue</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('company.paiesalaries.retenues.store') }}" method="POST">
                        @csrf
                        <input type="text" id="periode_id" name="periode_id" value="{{$periode->id}}" hidden="">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="libelle" class="form-label">Libellé <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="libelle" name="libelle1" required>
                            </div>
                            <div class="row">
                                <div class="mb-3">
                                    <label for="ordre" class="form-label">Ordre d'affichage</label>
                                    <input type="number" class="form-control" name="ordre1" id="ordre"
                                        value="{{ $lastOrder->ordre ?? 1 + 1 }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="part" class="form-label">Part</label>
                                    <select name="part" id="part" class="form-select">
                                        <option value="">Sélectionnez la part</option>
                                        <option value="1">Salariale</option>
                                        <option value="0">Patronale</option>
                                    </select>
                                    <input type="hidden" name="salariale1" id="salariale">
                                    <input type="hidden" name="patronale1" id="patronale">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="type_retenue_id" class="form-label">Type retenue<span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="type_retenue_id" name="type_retenue_id1" required>
                                        <option value="">Sélectionnez un type</option>
                                        @foreach($typesRetenues as $type)
                                            <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="taux_type" class="form-label">Taux <span class="text-danger">*</span></label>
                                    <select class="form-select" id="taux_type" name="item_brut1" required>
                                        <option value="">Sélectionnez un type</option>
                                        <option value="fixe">Montant fixe</option>
                                        <option value="percentage">Pourcentage</option>
                                    </select>
                                    <input type="number" class="form-control" id="code1" name="code1" value="{{$code}}" hidden>
                                    <input type="number" class="form-control" id="jours_work1" name="jours_work1" value=""
                                        hidden>
                                    <input type="text" class="form-control" id="base1" name="base1" value="" hidden>
                                </div>
                            </div>
                            <div class="mb-3" id="montantField">
                                <label for="montant" class="form-label">Montant (FCFA) <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="montant" name="amount1" min="0" value="0">
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
                                <input type="number" class="form-control" id="taux" name="taux1" value="0">
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="statut" name="statut" checked>
                                    <label class="form-check-label" for="statut">Activer cette retenue</label>
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

        <!-- Modal Modification Retenue -->
        <div class="modal fade" id="editRetenueModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier la retenue</h5>
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

        <!-- Modal Ajout Retenue aux employés -->
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

        <!-- Modal Liste Retenue aux employés -->
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
            $('#retenuesTableDefault').DataTable({
                responsive: true,
                order: [[0, 'asc']],
                language: {
                    url: "{{ asset('libs/datatables/i18n/fr-FR.json') }}"
                }
            });

            // Gestion du clic sur le bouton d'ajout d'éléments
            $('.btn-add-retenue').on('click', function () {
                var retenueId = $(this).data('id');
                var libelle = $(this).data('libelle');
                // Mettre à jour le titre du modal
                $('#addEmployeeRetenueModal .modal-title').text('Ajouter la retenue : ' + libelle);

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.retenues.add-employee", ":id") }}'.replace(':id', retenueId),
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
                $('#showRetenueModal .modal-title').text('Liste des employés de la retenue : ' + libelle);

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.retenues.add.show", ":id") }}'.replace(':id', retenueId),
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
                    $('#base1').val('');
                } else if ($(this).val() === 'percentage') {
                    $('#tauxField').removeClass('d-none');
                    $('#montantField').addClass('d-none');
                    $('#montant').val('');
                    $('#baseField').removeClass('d-none');
                }
            });

            $('#part').on('change', function () {
                if ($(this).val() === '1') {
                    $('#salariale').val('1');
                    $('#patronale').val('0');
                } else {
                    $('#salariale').val('0');
                    $('#patronale').val('1');
                }
            });

            // Gestion de l'affichage des champs selon le type
            $('#selectBase').on('change', function () {
                var selectBase = $(this).find('option:selected').text();
                $('#base1').val(selectBase);
            });

            // Gestion du clic sur le bouton de modification
            $('.edit-retenue').on('click', function () {
                var retenueId = $(this).data('id');
                var libelle = $(this).data('libelle');

                // Mettre à jour le titre du modal
                $('#editRetenueModal .modal-title').text('Modifier la retenue : ' + libelle);

                // Charger le formulaire via AJAX
                $.ajax({
                    url: '{{ route("company.paiesalaries.retenues.edit", ":id") }}'.replace(':id', retenueId),
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
                    title: 'Activer la retenue',
                    text: 'Êtes-vous sûr de vouloir activer cette retenue ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, activer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("company.paiesalaries.retenues.activate", ":id") }}'.replace(':id', retenueId),
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
                    title: 'Désactiver la retenue',
                    text: 'Êtes-vous sûr de vouloir désactiver cette retenue ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, désactiver',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("company.paiesalaries.retenues.deactivate", ":id") }}'.replace(':id', retenueId),
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

        // Automatisation : Appliquer à tous
        $(document).on('click', '#btnApplyAllDefault', function () {
            let periodeId = "{{ $periode->id ?? '' }}";
            if (!periodeId) return;

            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cela appliquera automatiquement les retenues légales (IRS, CNPS, CMU) pour TOUS les employés de cette période.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, tout appliquer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Traitement en cours...',
                        text: 'Veuillez patienter pendant que nous calculons les retenues pour tout le personnel.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "{{ route('company.paiesalaries.retenues.applyAll') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            periode_id: periodeId
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Succès !', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Erreur !', response.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            Swal.fire('Erreur !', 'Une erreur technique est survenue.', 'error');
                        }
                    });
                }
            });
        });
    </script>

    <!-- Guide IA pour les Retenues -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function runRetenueGuide() {
                if (typeof showAiTip !== 'function') {
                    setTimeout(runRetenueGuide, 1000);
                    return;
                }

                // Séquence de conseils
                const steps = [
                    {
                        msg: "✅ <b>Bienvenue dans la gestion des retenues !</b> Ici, vous contrôlez ce qui est déduit du salaire brut.",
                        delay: 1500
                    },
                    {
                        msg: "📁 <b>Retenues par défaut :</b> Ce sont les taxes légales (CNPS, IGR) calculées <u>automatiquement</u> par le système.",
                        delay: 8000,
                        highlight: "retenues-tab-default"
                    },
                    {
                        msg: "➕ <b>Retenues créées :</b> Utilisez cet onglet pour gérer vos propres retenues (Prêts, Mutuelles, Avances).",
                        delay: 10000,
                        highlight: "retenues-tab-created"
                    },
                    {
                        msg: "✅ <b>Automatique :</b> les retenues légales sont appliquées à chaque salarié, tous les mois, sans action de votre part.",
                        delay: 10000
                    }
                ];

                let currentStep = 0;
                function nextStep() {
                    if (currentStep < steps.length) {
                        const s = steps[currentStep];
                        showAiTip(s.msg, 9000);
                        if (s.highlight) {
                            const el = document.getElementById(s.highlight);
                            if (el) {
                                el.style.boxShadow = "0 0 15px #253e87";
                                setTimeout(() => el.style.boxShadow = "none", 5000);
                            }
                        }
                        currentStep++;
                        setTimeout(nextStep, s.delay);
                    }
                }
                nextStep();
            }

            // Lancer le guide
            setTimeout(runRetenueGuide, 2000);
        });
    </script>
@endpush