@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Gestion des Transferts</h4>
                    <p class="text-muted mb-0">Gérez les transferts et distinctions des employés</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small> 
                </div>
                <div>
                    <a href="{{ route('company.evenements.transfers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouveau Transfert
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-ul text-primary me-2"></i> Liste des Transferts
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
                    <table id="transfers-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Type</th>
                                <th>Détails</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Les données seront chargées via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#transfers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("company.evenements.transfers.index") }}',
        columns: [
            { data: 'employee_name', name: 'employee.full_name' },
            { data: 'transfer_type', name: 'transfer_type' },
            { data: 'transfer_details', name: 'transfer_details', orderable: false, searchable: false },
            { 
                data: 'transfer_date', 
                name: 'transfer_date',
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY');
                }
            },
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    let statusClass = '';
                    switch(data) {
                        case 'pending':
                            statusClass = 'warning';
                            break;
                        case 'approved':
                            statusClass = 'success';
                            break;
                        case 'rejected':
                            statusClass = 'danger';
                            break;
                        case 'completed':
                            statusClass = 'info';
                            break;
                    }
                    return `<span class="badge badge-${statusClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            { 
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                className: 'text-center'
            },
        ],
        order: [[3, 'desc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        responsive: true
    });

    // Gestion de la suppression
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        
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
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Supprimé !',
                            response.message || 'Le transfert a été supprimé avec succès.',
                            'success'
                        );
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Erreur !',
                            xhr.responseJSON?.message || 'Une erreur est survenue lors de la suppression.',
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
