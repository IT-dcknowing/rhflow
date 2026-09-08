@extends('layouts.app')

@section('title', 'Gestion des Prêts')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📅 Gestion des Prêts
                        @if($periode) | Exercice :
                         {{ $periode->exercice->nom }} - Statut : <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                        {{ ucfirst($periode->exercice->statut) }}
                        @endif
                        </span>
                    </h4>
                    <p class="text-muted mb-0">Gérez les prêts accordés aux employés</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.paiesalaries.dashboard') }}">Paie</a>
                            </li>
                            <li class="breadcrumb-item active">Prêts @if($periode) - Période : {{ $periode->nom }} | Statut : <span class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($periode->statut) }}</span> @endif</li>
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
                        <i class="fas fa-arrow-left me-2"></i>Période
                    </a>
                    <a type="button" class="btn btn-primary" href="{{ route('company.loans.create') }}@if($periode)?periode_id={{ $periode->id }}@endif">
                        <i class="fas fa-plus me-2"></i>Ajouter
                    </a>
                    @endif
                    <a type="button" class="btn btn-success" href="{{ route('company.settings.loan-types.index') }}">
                        <i class="fas fa-plus me-2"></i>Types de Prêts
                    </a>
                </div>
            </div>
        </div>
    </div>
    @if($periode)
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title">Liste des Prêts</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top dataTable no-footer dtr-column" id="loans-table">
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Type de Prêt</th>
                        <th>Montant</th>
                        <th>Mensualité</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loan->employee->name }}</td>
                            <td>{{ $loan->title }}</td>
                            <td>{{ number_format($loan->amount, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($loan->amount_deduc, 0, ',', ' ') }} FCFA</td>
                            <td>{{ \Carbon\Carbon::parse($loan->start_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($loan->end_date)->format('d/m/Y') }}</td>
                            <td>
                                @if($loan->statut == 'pending')
                                    <span class="badge bg-label-primary">En attente</span>
                                @elseif($loan->statut == 'running')
                                    <span class="badge bg-label-success">En cours</span>
                                @elseif($loan->statut == 'completed')
                                    <span class="badge bg-label-danger">Terminé</span>
                                @else
                                    <span class="badge bg-label-warning">Annulé</span>
                                @endif
                                @if(!$loan->is_active)
                                    <span class="badge bg-label-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('company.loans.show', $loan->id) }}" class="btn btn-sm btn-info me-2">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('company.loans.edit', $loan->id) }}" class="btn btn-sm btn-primary me-2">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    @if($loan->is_active)
                                        <form action="{{ route('company.loans.deactivate', $loan->id) }}"
                                              method="POST" class="d-inline me-2"
                                              onsubmit="return confirm('Désactiver ce prêt ? Sa retenue sera retirée de la paie des périodes encore modifiables. Les bulletins déjà générés ne sont pas touchés.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Désactiver ce prêt">
                                                <i class="fas fa-toggle-off"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('company.loans.activate', $loan->id) }}"
                                              method="POST" class="d-inline me-2"
                                              onsubmit="return confirm('Réactiver ce prêt ? Sa retenue sera régénérée sur les périodes ouvertes.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Réactiver ce prêt">
                                                <i class="fas fa-toggle-on"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" id="delete-loan">
                                        <i class="fas fa-trash me-1"></i>
                                    </button>
                                    <form id="delete-form" action="{{ route('company.loans.destroy', $loan->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-hand-holding-usd fa-3x mb-3"></i>
                                    <p class="mb-0">Aucun prêt enregistré pour cette période.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                <p>Êtes-vous sûr de vouloir supprimer ce prêt ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
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

        // Initialisation Conditionnelle de la DataTable
        @if($loans->count() > 0)
        var dt_table = $('#loans-table').DataTable({
            processing: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
            },
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
        });
        @endif

        // Gestion de la suppression
        // Confirmation de suppression du prêt
        $('#delete-loan').on('click', function () {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            });
        });
    });
</script>
@endpush