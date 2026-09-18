@extends('layouts.app')

@section('title', 'Liste des Annonces')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Gestion des Annonces</h4>
                    <p class="text-muted mb-0">Gérez les annonces et distinctions des employés</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small> 
                </div>
                <div>
                    <a href="{{ route('company.evenements.annonces.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvelle Annonce
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-ul text-primary me-2"></i> Liste des Annonces
                    </h5>
                </div>
                
                <!-- Filtres -->
                <div class="card-body border-bottom" id="filtersContainer">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Recherche</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Rechercher par titre ou description...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="active">Actives</option>
                                <option value="upcoming">À venir</option>
                                <option value="expired">Expirées</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="branch_id" class="form-label">Succursale</label>
                            <select class="form-select" id="branch_id" name="branch_id">
                                <option value="">Toutes les succursales</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger me-2" id="resetFilters">
                                <i class="fas fa-undo me-1"></i> Réinitialiser
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filtrer
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="announcementsTable" class="table table-hover table-striped w-100">
                            <thead class="table-light">
                                <tr>
                                    <th width="25%">Titre</th>
                                    <th width="15%">Période</th>
                                    <th width="15%">Succursale</th>
                                    <th width="15%">Département</th>
                                    <th width="10%" class="text-center">Statut</th>
                                    <th width="15%" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($announcements as $announcement)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                                                        <i class="fas fa-bullhorn text-primary"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0">{{ $announcement->title }}</h6>
                                                    <small class="text-muted">
                                                        {{ $announcement->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-nowrap">
                                                    <i class="far fa-calendar-alt text-muted me-1"></i> 
                                                    {{ $announcement->start_date->format('d/m/Y') }} - {{ $announcement->end_date->format('d/m/Y') }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $announcement->start_date->diffInDays($announcement->end_date) + 1 }} jours
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($announcement->branch)
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    <i class="fas fa-building me-1"></i> {{ $announcement->branch->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    <i class="fas fa-globe me-1"></i> Toutes
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($announcement->department)
                                                <span class="badge bg-info bg-opacity-10 text-info">
                                                    <i class="fas fa-sitemap me-1"></i> {{ $announcement->department->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                    <i class="fas fa-layer-group me-1"></i> Tous
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $now = now();
                                                $statusClass = '';
                                                $statusText = '';
                                                
                                                if ($now->lt($announcement->start_date)) {
                                                    $statusClass = 'bg-warning bg-opacity-10 text-warning';
                                                    $statusText = 'À venir';
                                                    $icon = 'clock';
                                                } elseif ($now->between($announcement->start_date, $announcement->end_date->endOfDay())) {
                                                    $statusClass = 'bg-success bg-opacity-10 text-success';
                                                    $statusText = 'Active';
                                                    $icon = 'check-circle';
                                                } else {
                                                    $statusClass = 'bg-secondary bg-opacity-10 text-secondary';
                                                    $statusText = 'Expirée';
                                                    $icon = 'calendar-times';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }} px-3 py-2">
                                                <i class="fas fa-{{ $icon }} me-1"></i> {{ $statusText }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('company.evenements.annonces.show', $announcement->id) }}" 
                                                   class="btn btn-icon btn-sm btn-label-info" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('company.evenements.annonces.edit', $announcement->id) }}" 
                                                   class="btn btn-icon btn-sm btn-label-warning" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-icon btn-sm btn-label-danger delete-btn" 
                                                        data-id="{{ $announcement->id }}"
                                                        data-bs-toggle="tooltip" 
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="empty-state">
                                                <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                                <h4 class="h5">Aucune annonce trouvée</h4>
                                                <p class="text-muted">
                                                    Commencez par créer votre première annonce en cliquant sur le bouton ci-dessous.
                                                </p>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('company.evenements.annonces.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i> Créer une annonce
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($announcements->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Affichage de {{ $announcements->firstItem() }} à {{ $announcements->lastItem() }} sur {{ $announcements->total() }} annonces
                            </div>
                            <nav aria-label="Page navigation">
                                {{ $announcements->links() }}
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette annonce ?</p>
                <p class="mb-0">
                    <strong>Attention :</strong> Cette action est irréversible et supprimera définitivement l'annonce.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-top: none;
        padding-top: 1rem;
        padding-bottom: 1rem;
        color: #6c757d;
    }
    
    .table td {
        vertical-align: middle;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
        padding: 0.4em 0.8em;
    }
    
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }
    
    .empty-state i {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        opacity: 0.7;
    }
    
    .btn-group .btn {
        border-radius: 0.25rem !important;
        margin: 0 2px;
    }
    
    .btn-group .btn:first-child {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    
    .btn-group .btn:last-child {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    
    .pagination .page-link {
        color: #0d6efd;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .form-select, .form-control {
        border-radius: 0.375rem;
    }
    
    .table-hover > tbody > tr:hover {
        background-color: rgba(13, 110, 253, 0.03);
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialisation de DataTable
        const table = $('#announcementsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json',
                search: "",
                searchPlaceholder: "Rechercher...",
                lengthMenu: "Afficher _MENU_ entrées",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                infoEmpty: "Aucune entrée à afficher",
                infoFiltered: "(filtré à partir de _MAX_ entrées au total)",
                zeroRecords: "Aucune annonce trouvée",
                paginate: {
                    first: "Premier",
                    last: "Dernier",
                    next: "Suivant",
                    previous: "Précédent"
                }
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 10,
            order: [[1, 'desc']],
            columnDefs: [
                { orderable: false, targets: [4, 5] } // Désactiver le tri sur les colonnes Statut et Actions
            ]
        });
        
        // Afficher/masquer les filtres
        $('#filterBtn').click(function() {
            $('#filtersContainer').slideToggle();
        });
        
        // Réinitialiser les filtres
        $('#resetFilters').click(function() {
            $('#filterForm')[0].reset();
            table.search('').columns().search('').draw();
        });
        
        // Appliquer les filtres
        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            const search = $('#search').val();
            const status = $('#status').val();
            const branchId = $('#branch_id').val();
            
            // Filtrer par recherche
            table.search(search).draw();
            
            // Filtrer par statut
            if (status) {
                const now = new Date();
                
                table.column(4).search(status, true, false).draw();
            }
            
            // Filtrer par succursale
            if (branchId) {
                table.column(2).search(branchId, true, false).draw();
            }
            
            // Masquer les filtres après application
            $('#filtersContainer').slideUp();
        });
        
        // Gestion de la suppression
        $('.delete-btn').click(function() {
            const id = $(this).data('id');
            const url = '{{ route("company.evenements.annonces.destroy", ":id") }}'.replace(':id', id);
            
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#deleteForm').attr('action', url).submit();
                }
            });
        });
        
        // Initialiser les tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
