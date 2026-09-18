@extends('layouts.app')

@section('title', 'Types de rupture')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">❌ Gestion des Types de rupture</h4>
                    <p class="text-muted mb-0">Gérez les types de ruptures</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item active">Gestion des Types de rupture</li>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                        <i class="ti ti-plus me-1"></i> Ajouter un type
                    </button>
                    <a href="{{ route('company.ruptures.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des types de rupture</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">  
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Statut</th>  
                            <th class="text-center">Ruptures associées</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td>{{ $type->description }}</td>
                            <td>
                                @if($type->is_active)
                                    <span class="badge bg-label-success">Actif</span>
                                @else
                                    <span class="badge bg-label-danger">Inactif</span>
                                @endif
                            </td>
                            <td align="center">{{ $type->ruptures_count }}</td>
                            <td>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-icon btn-sm btn-label-warning me-2 edit-type" 
                                            data-id="{{ $type->id }}" 
                                            data-name="{{ $type->name }}" 
                                            data-description="{{ $type->description }}" 
                                            data-is-active="{{ $type->is_active }}">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-sm btn-label-danger delete-type" 
                                            data-id="{{ $type->id }}" 
                                            data-ruptures-count="{{ $type->ruptures_count }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun type de rupture trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $types->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter Type -->
<div class="modal fade" id="addTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un type de rupture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTypeForm" action="{{ route('company.ruptures.types.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="add_name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" id="add_name" name="name" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="add_description" class="form-label">Description</label>
                        <textarea id="add_description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="add_is_active" name="is_active" value="1" checked />
                            <label class="form-check-label" for="add_is_active">Actif</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="addTypeForm" class="btn btn-primary">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modifier Type -->
<div class="modal fade" id="editTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le type de rupture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTypeForm" action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id" />
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name" name="name" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1" />
                            <label class="form-check-label" for="edit_is_active">Actif</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="editTypeForm" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Supprimer Type -->
<div class="modal fade" id="deleteTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Supprimer le type de rupture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="deleteTypeForm" action="#" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="delete_id" name="id" />
                    <p>Êtes-vous sûr de vouloir supprimer ce type de rupture ?</p>
                    <div id="warning-associated-ruptures" class="alert alert-warning d-none">
                        <div class="d-flex">
                            <i class="ti ti-alert-triangle ti-sm me-2"></i>
                            <span>Ce type est associé à <span id="ruptures-count"></span> rupture(s). La suppression affectera ces ruptures.</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="deleteTypeForm" class="btn btn-danger">Supprimer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        'use strict';

        // Ouvrir le modal de modification
        $('.edit-type').on('click', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var description = $(this).data('description');
            var isActive = $(this).data('is-active');
            
            $('#edit_id').val(id);
            $('#edit_name').val(name);
            $('#edit_description').val(description);
            $('#edit_is_active').prop('checked', isActive == 1);
            
            // Mettre à jour l'URL du formulaire avec l'ID
            var baseUrl = '{{ route("company.ruptures.types.update", ":id") }}';
            $('#editTypeForm').attr('action', baseUrl.replace(':id', id));
            
            // Ouvrir le modal
            $('#editTypeModal').modal('show');
        });
        
        // Ouvrir le modal de suppression
        $('.delete-type').on('click', function() {
            var id = $(this).data('id');
            var rupturesCount = $(this).data('ruptures-count');
            
            $('#delete_id').val(id);
            
            // Mettre à jour l'URL du formulaire
            var baseUrl = '{{ route("company.ruptures.types.destroy", ":id") }}';
            $('#deleteTypeForm').attr('action', baseUrl.replace(':id', id));
            
            // Afficher l'avertissement si nécessaire
            if (rupturesCount > 0) {
                $('#ruptures-count').text(rupturesCount);
                $('#warning-associated-ruptures').removeClass('d-none');
            } else {
                $('#warning-associated-ruptures').addClass('d-none');
            }
            
            // Ouvrir le modal
            $('#deleteTypeModal').modal('show');
        });
    });
</script>
@endpush