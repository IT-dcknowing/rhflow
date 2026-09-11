@extends('layouts.app')

@section('title', 'Liste des natures d\'avantage')  

@push('styles')
    <link rel="stylesheet" href="{{asset('vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/libs/sweetalert2/sweetalert2.css')}}">
@endpush

@push('scripts')
    <script src="{{asset('vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">➕ Gestion des avantages en natures
                        @if($periode) | Exercice :
                         {{ $periode->exercice->nom }} - <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                        {{ ucfirst($periode->exercice->statut) }}
                        @endif
                    </h4>
                    <p class="text-muted mb-0">Gérez les avantages en natures des employés</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item active">Gestion avantages en natures @if($periode) - Période : {{ $periode->nom }} - <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($periode->statut) }}</span> @endif</li>
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
                    <a href="{{ route('company.paiesalaries.periodes.show', $periode->id) }}" type="button" class="align-items-center btn btn-info">
                        <i class="fas fa-arrow-left me-2"></i>Période
                    </a>
                    <a href="{{ route('company.avantages.create') }}?periode_id={{$periode->id}}" class="btn btn-primary me-2">
                        <i class="ti ti-plus me-1"></i> Nouvelle avantage
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($periode)
    <!-- Carte principale -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des avantages en natures</h5>
            <div class="d-flex">
                <span class="badge bg-label-primary">{{ $avantages->count() }} avantages(s)</span>
            </div>
        </div>

        <div class="card-body">
            <!-- Filtres 
            <div class="row mb-4">
                <div class="col-md-3 mb-2">
                    <label for="filter-type" class="form-label">Type d'avantage</label>
                    <select id="filter-type" class="select2 form-select" data-placeholder="Tous les types">
                        <option value="">Tous les types</option>
                        <option value="avantage_en_nature">Avantage en nature</option>
                        <option value="avantage_en_argent">Avantage en argent</option>
                        <option value="assurance_vie_complementaire">Assurance-vie complémentaire</option>
                        <option value="assurance_sante">Assurance santé</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="filter-status" class="form-label">Statut</label>
                    <select id="filter-status" class="select2 form-select" data-placeholder="Tous les statuts">
                        <option value="">Tous les statuts</option>
                        <option value="1">Actif</option>
                        <option value="0">Inactif</option>
                    </select>
                </div>
            </div>
            -->
            <!-- Tableau des avantages -->
            <div class="table-responsive">
                <table class="table table-sm dt-responsive" id="avantages-table">
                    <thead>
                        <tr>
                            <th>Employés</th>
                            <th>Type d'avantage</th>
                            <th>Montant selon le barème</th>
                            <th>Montant réel</th>
                            <th>Traitement</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($avantages as $avantage)
                            <tr>
                                <td>{{ $avantage->employee->name }}</td>
                                <td>
                                    @if($avantage->type_avantage == 'avantage_en_nature')
                                        Avantage en Nature
                                    @elseif($avantage->type_avantage == 'avantage_en_argent')
                                        Avantage en Argent
                                    @elseif($avantage->type_avantage == 'assurance_vie_complementaire')
                                        Assurance-vie complémentaire
                                    @else
                                        Assurance santé
                                    @endif
                                </td>
                                <td>
                                    @if($avantage->type_avantage == 'avantage_en_nature')
                                        {{ number_format(($avantage->amount),'0','.',' ') }} FCFA
                                    @else
                                        {{ number_format(($avantage->amount_reel),'0','.',' ') }} FCFA
                                    @endif
                                </td>
                                <td>{{ number_format($avantage->amount_reel, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $avantage->traitement == 'mensuel' ? 'Mensuel' : 'Annuel' }}</td>
                                <td>
                                    @if($avantage->is_active)
                                        <span class="badge bg-label-success">Actif</span>
                                    @else
                                        <span class="badge bg-label-danger">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="{{ route('company.avantages.show', $avantage->id) }}" class="btn btn-sm btn-icon bg-label-info me-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('company.avantages.edit', $avantage->id) }}" class="btn btn-sm btn-icon bg-label-primary me-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-icon bg-label-danger delete-record" data-id="{{ $avantage->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-gift fa-3x mb-3"></i>
                                        <p class="mb-0">Aucun avantage enregistré pour cette période.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
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
<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette nature d'avantage ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    $(function () {
        'use strict';

        // Initialisation de Select2
        $('.select2').select2();

        // Initialisation de DataTables
        @if($avantages->count() > 0)
        var dt_table = $('#avantages-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
            },
            dom: '<"card-body d-flex justify-content-end"<"head-label text-center"><"dt-action-buttons text-end"B>><"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6" f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-success dropdown-toggle me-2',
                    text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Exporter</span>',
                    buttons: [
                        { extend: 'print', text: '<i class="ti ti-printer me-1"></i>Imprimer', className: 'dropdown-item' },
                        { extend: 'csv', text: '<i class="ti ti-file-text me-1"></i>CSV', className: 'dropdown-item' },
                        { extend: 'excel', text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel', className: 'dropdown-item' },
                        { extend: 'pdf', text: '<i class="ti ti-file-description me-1"></i>PDF', className: 'dropdown-item' }
                    ]
                }
            ],
            responsive: true
        });
        @endif

        // Filtrage des données
        $('#filter-type, #filter-status, #filter-taxe').on('change', function() {
            filterData();
        });

        function filterData() {
            var typeFilter = $('#filter-type').val();
            var statusFilter = $('#filter-status').val();
            var taxeFilter = $('#filter-taxe').val();

            // Appliquer le filtrage sur DataTable
            dt_table.column(1).search(typeFilter);   // colonne 1 : Type d'avantage
            dt_table.column(5).search(statusFilter); // colonne 5 : Statut
            dt_table.column(4).search(taxeFilter);   // colonne 4 : Traitement

                dt_table.draw(); // Re-dessiner la table
            }

        // Gestion de la suppression
        $('.delete-record').on('click', function() {
            var id = $(this).data('id');
            $('#deleteForm').attr('action', '{{ route("company.avantages.destroy", ":id") }}'.replace(':id', id));
            $('#deleteModal').modal('show');
        });
    });
</script>
@endpush