@extends('evenements::layouts.master')

@section('content')
<div class="content-wrapper">
    <!-- En-tête de page -->
    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('company.evenements.annonces.index') }}">Annonces</a></li>
                <li class="breadcrumb-item active" aria-current="page">Détails de l'annonce #{{ $announcement->id }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="page-title">
                <i class="fas fa-bullhorn text-primary"></i> Détails de l'annonce
            </h3>
            <div class="actions">
                <a href="{{ route('company.evenements.annonces.edit', $announcement->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> Modifier
                </a>
                <a href="{{ route('company.evenements.annonces.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- En-tête avec statut et dates -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            @php
                                $now = now();
                                $startDate = $announcement->start_date;
                                $endDate = $announcement->end_date;
                                $status = '';
                                $statusClass = '';
                                
                                if ($now < $startDate) {
                                    $status = 'À venir';
                                    $statusClass = 'warning';
                                } elseif ($now > $endDate) {
                                    $status = 'Expirée';
                                    $statusClass = 'danger';
                                } else {
                                    $status = 'Active';
                                    $statusClass = 'success';
                                }
                            @endphp
                            <span class="badge badge-{{ $statusClass }} px-3 py-2">
                                <i class="fas {{ $statusClass == 'success' ? 'fa-check-circle' : ($statusClass == 'warning' ? 'fa-clock' : 'fa-exclamation-circle') }} mr-1"></i>
                                {{ $status }}
                            </span>
                        </div>
                        <div class="text-muted">
                            <small>
                                <i class="far fa-calendar-alt mr-1"></i> Du {{ $announcement->start_date->format('d/m/Y') }} au {{ $announcement->end_date->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>

                    <!-- Titre et métadonnées -->
                    <h2 class="mb-3">{{ $announcement->title }}</h2>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-light-primary rounded-circle p-3 mr-3">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">Succursale</h6>
                                    <p class="mb-0 font-weight-bold">{{ $announcement->branch->name ?? 'Toutes les succursales' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box bg-light-info rounded-circle p-3 mr-3">
                                    <i class="fas fa-sitemap text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-muted">Département</h6>
                                    <p class="mb-0 font-weight-bold">{{ $announcement->department->name ?? 'Tous les départements' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-align-left text-primary mr-2"></i>Description</h5>
                        </div>
                        <div class="card-body">
                            <div class="p-3 bg-light rounded">
                                {!! $announcement->description !!}
                            </div>
                        </div>
                    </div>

                    <!-- Employés concernés -->
                    <div class="card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-users text-primary mr-2"></i>Employés concernés
                                <span class="badge badge-primary ml-2">{{ $announcement->employees->count() }}</span>
                            </h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#exportModal">
                                    <i class="fas fa-file-export mr-1"></i> Exporter
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-center" width="60">#</th>
                                            <th>Matricule</th>
                                            <th>Nom complet</th>
                                            <th>Département</th>
                                            <th>Poste</th>
                                            <th class="text-center">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($announcement->employees as $key => $employee)
                                            <tr>
                                                <td class="text-center">{{ $key + 1 }}</td>
                                                <td>{{ $employee->employee_id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm mr-3">
                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $employee->first_name }} {{ $employee->last_name }}</h6>
                                                            <small class="text-muted">{{ $employee->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-soft-primary">
                                                        {{ $employee->department->name ?? 'Non défini' }}
                                                    </span>
                                                </td>
                                                <td>{{ $employee->designation->name ?? 'Non défini' }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $status = $employee->pivot->status ?? 'pending';
                                                        $statusClass = [
                                                            'read' => 'success',
                                                            'unread' => 'warning',
                                                            'pending' => 'secondary'
                                                        ][$status] ?? 'secondary';
                                                        
                                                        $statusText = [
                                                            'read' => 'Lu',
                                                            'unread' => 'Non lu',
                                                            'pending' => 'En attente'
                                                        ][$status] ?? 'Inconnu';
                                                    @endphp
                                                    <span class="badge badge-soft-{{ $statusClass }}">
                                                        <i class="fas fa-{{ $status == 'read' ? 'check-circle' : ($status == 'unread' ? 'envelope' : 'clock') }} mr-1"></i>
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                                        <p class="mb-0">Aucun employé n'est concerné par cette annonce</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Métadonnées -->
                    <div class="card mt-4">
                        <div class="card-body bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-user-plus text-muted mr-2"></i>
                                        <small class="text-muted">
                                            Créé le {{ $announcement->created_at->format('d/m/Y à H:i') }}
                                            @if($announcement->createdBy)
                                                par 
                                                <span class="font-weight-bold">
                                                    {{ $announcement->createdBy->name }}
                                                </span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                @if($announcement->updated_at != $announcement->created_at)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-edit text-muted mr-2"></i>
                                            <small class="text-muted">
                                                Dernière mise à jour le {{ $announcement->updated_at->format('d/m/Y à H:i') }}
                                                @if($announcement->updatedBy && $announcement->created_by != $announcement->updated_by)
                                                    par 
                                                    <span class="font-weight-bold">
                                                        {{ $announcement->updatedBy->name }}
                                                    </span>
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'export -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-file-export text-primary mr-2"></i>Exporter la liste
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Veuillez sélectionner le format d'export :</p>
                <div class="d-flex justify-content-around">
                    <a href="{{ route('company.evenements.annonces.export', ['announcement' => $announcement->id, 'format' => 'pdf']) }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                    <a href="{{ route('company.evenements.annonces.export', ['announcement' => $announcement->id, 'format' => 'excel']) }}" class="btn btn-success">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </a>
                    <a href="{{ route('company.evenements.annonces.export', ['announcement' => $announcement->id, 'format' => 'csv']) }}" class="btn btn-info">
                        <i class="fas fa-file-csv mr-1"></i> CSV
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .badge-soft-primary {
        color: #4e73df;
        background-color: rgba(78, 115, 223, 0.18);
    }
    .badge-soft-success {
        color: #1cc88a;
        background-color: rgba(28, 200, 138, 0.18);
    }
    .badge-soft-warning {
        color: #f6c23e;
        background-color: rgba(246, 194, 62, 0.18);
    }
    .badge-soft-danger {
        color: #e74a3b;
        background-color: rgba(231, 74, 59, 0.18);
    }
    .badge-soft-secondary {
        color: #858796;
        background-color: rgba(133, 135, 150, 0.18);
    }
    .avatar-sm {
        width: 36px;
        height: 36px;
        line-height: 36px;
        text-align: center;
    }
    .avatar-title {
        display: inline-block;
        font-weight: 600;
        font-size: 0.8rem;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation des tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialisation de DataTable pour la table des employés
        $('.table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            },
            "pageLength": 10,
            "order": [[2, 'asc']], // Tri par nom par défaut
            "responsive": true,
            "dom": '<"d-flex justify-content-between align-items-center mb-3"f<"d-flex align-items-center"><"d-flex">>t<"d-flex justify-content-between align-items-center"<"dataTables_info"i><"dataTables_paginate"p>>',
            "initComplete": function() {
                // Ajout d'un champ de recherche personnalisé
                $('.dataTables_filter').addClass('d-none');
                
                // Bouton d'export
                $('.dataTables_wrapper .dataTables_length').append(`
                    <div class="dropdown ml-3">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-download mr-1"></i> Exporter
                        </button>
                        <div class="dropdown-menu" aria-labelledby="exportDropdown">
                            <a class="dropdown-item" href="{{ route('company.evenements.annonces.export', ['announcement' => $announcement->id, 'format' => 'pdf']).'?search=' }}" + $('input[type=search]').val()>
                                <i class="fas fa-file-pdf text-danger mr-2"></i> PDF
                            </a>
                            <a class="dropdown-item" href="{{ route('company.evenements.annonces.export', ['announcement' => $announcement->id, 'format' => 'excel']).'?search=' }}" + $('input[type=search]').val()>
                                <i class="fas fa-file-excel text-success mr-2"></i> Excel
                            </a>
                            <a class="dropdown-item" href="{{ route('company.evenements.annonces.export', ['announcement' => $announcement->id, 'format' => 'csv']).'?search=' }}" + $('input[type=search]').val()>
                                <i class="fas fa-file-csv text-info mr-2"></i> CSV
                            </a>
                        </div>
                    </div>
                `);
                
                // Champ de recherche personnalisé
                $('.dataTables_wrapper .dataTables_filter').removeClass('d-none');
                
                // Mise à jour des liens d'export avec le terme de recherche
                $('input[type=search]').on('keyup', function() {
                    const searchTerm = $(this).val();
                    $('.export-link').each(function() {
                        const href = $(this).data('base-href') + encodeURIComponent(searchTerm);
                        $(this).attr('href', href);
                    });
                });
            }
        });
    });
    
    // Fonction pour confirmer la suppression
    function confirmDelete() {
        Swal.fire({
            title: 'Confirmer la suppression',
            text: 'Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm').submit();
            }
        });
    }
</script>
@endpush
