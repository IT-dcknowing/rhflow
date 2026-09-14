@php
    $count = 0;
@endphp
@extends('layouts.app')

@section('title', 'Types de contrats - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📋 Types de contrats</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.contracts.index') }}">Gestion des contrats</a>
                            </li>  
                            <li class="breadcrumb-item active">Types de contrats</li>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                        <i class="fas fa-plus me-1"></i>Nouveau type
                    </button>
                    <a href="{{ route('company.contracts.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-tags me-1"></i>Liste des Contrats
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des types de contrats -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Liste des types de contrats</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Contrats associés</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contractTypes as $type)
                                <tr>
                                    <td>{{ $type->name }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $type->type == 'default' ? 'primary' : 'dark' }}">
                                            {{ $type->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $type->is_active ? 'success' : 'danger' }}">
                                            {{ $type->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $type->contracts->where('company_id', Auth::user()->company_id)->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-primary me-1 {{ $type->type == 'default' ? 'disabled' : '' }}" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editTypeModal" 
                                                data-id="{{ $type->id }}"
                                                data-name="{{ $type->name }}"
                                                data-active="{{ $type->is_active }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger {{ $type->type == 'default' ? 'disabled' : '' }}" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteTypeModal"
                                                data-id="{{ $type->id }}"
                                                data-name="{{ $type->name }}"
                                                data-count="{{ $type->contract_count ?? 0 }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">Aucun type de contrat trouvé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">Total: {{ $contractTypes->total() ?? count($contractTypes) }}</small>
                        </div>
                        @if(method_exists($contractTypes, 'links'))
                            <div>
                                {{ $contractTypes->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout de type -->
<div class="modal fade" id="addTypeModal" tabindex="-1" aria-labelledby="addTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTypeModalLabel">Ajouter un type de contrat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('company.contracts.types.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Actif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de modification de type -->
<div class="modal fade" id="editTypeModal" tabindex="-1" aria-labelledby="editTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTypeModalLabel">Modifier le type de contrat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTypeForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nom du type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name" name="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">Actif</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de suppression de type -->
<div class="modal fade" id="deleteTypeModal" tabindex="-1" aria-labelledby="deleteTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTypeModalLabel">Supprimer le type de contrat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce type de contrat : <strong id="delete-type-name"></strong> ?</p>
                <div id="delete-warning" class="alert alert-warning d-none">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Ce type est utilisé par <strong id="delete-contract-count"></strong> contrat(s). La suppression pourrait affecter ces contrats.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteTypeForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration du modal d'édition
        $('#editTypeModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const active = button.data('active');
            
            const modal = $(this);
            modal.find('#edit_name').val(name);
            modal.find('#edit_is_active').prop('checked', active == 1);
            
            const form = document.getElementById('editTypeForm');
            form.action = `{{ route('company.contracts.types.update', ':id') }}`.replace(':id', id);
        });
        
        // Configuration du modal de suppression
        $('#deleteTypeModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const count = button.data('count');
            
            const modal = $(this);
            modal.find('#delete-type-name').text(name);
            
            const warningDiv = modal.find('#delete-warning');
            const countElement = modal.find('#delete-contract-count');
            
            if (count > 0) {
                countElement.text(count);
                warningDiv.removeClass('d-none');
            } else {
                warningDiv.addClass('d-none');
            }
            
            const form = document.getElementById('deleteTypeForm');
            form.action = `{{ route('company.contracts.types.destroy', ':id') }}`.replace(':id', id);
        });
    });
</script>
@endpush