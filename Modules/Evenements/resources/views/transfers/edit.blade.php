@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Gestion des Transferts</h4>
                    <p class="text-muted mb-0">Modifier un transfert pour un employé</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small> 
                </div>
                <div>
                    <a href="{{ route('company.evenements.transfers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier le Transfert</h3>
                    <div class="card-tools">
                        <a href="{{ route('company.evenements.transfers.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <form action="{{ route('company.evenements.transfers.update', $transfer->id) }}" method="POST" id="transfer-form">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employee_id" class="mb-1">Employé <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-select select2" required {{ $transfer->status === 'completed' ? 'disabled' : '' }}>
                                        <option value="">Sélectionner un employé</option>
                                        @foreach($employees as $id => $name)
                                            <option value="{{ $id }}" {{ old('employee_id', $transfer->employee_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transfer_type" class="mb-1">Type de transfert <span class="text-danger">*</span></label>
                                    <select name="transfer_type" id="transfer_type" class="form-select" required {{ $transfer->status === 'completed' ? 'disabled' : '' }}>
                                        <option value="">Sélectionner un type</option>
                                        <option value="department" {{ old('transfer_type', $transfer->transfer_type) == 'department' ? 'selected' : '' }}>Changement de département</option>
                                        <option value="location" {{ old('transfer_type', $transfer->transfer_type) == 'location' ? 'selected' : '' }}>Changement de localisation</option>
                                        <option value="position" {{ old('transfer_type', $transfer->transfer_type) == 'position' ? 'selected' : '' }}>Changement de poste</option>
                                    </select>
                                    @error('transfer_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Champs dynamiques en fonction du type de transfert -->
                        <div id="department-fields" class="transfer-type-fields" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_department_id" class="mb-1">Département d'origine <span class="text-danger">*</span></label>
                                        <select name="from_department_id" id="from_department_id" class="form-select select2" {{ $transfer->status === 'completed' ? 'disabled' : '' }}>
                                            <option value="">Sélectionner un département</option>
                                            @foreach($departments as $id => $name)
                                                <option value="{{ $id }}" {{ old('from_department_id', $transfer->from_department_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('from_department_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_department_id" class="mb-1">Nouveau département <span class="text-danger">*</span></label>
                                        <select name="to_department_id" id="to_department_id" class="form-control select2" {{ $transfer->status === 'completed' ? 'disabled' : '' }}>
                                            <option value="">Sélectionner un département</option>
                                            @foreach($departments as $id => $name)
                                                <option value="{{ $id }}" {{ old('to_department_id', $transfer->to_department_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('to_department_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="location-fields" class="transfer-type-fields" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_location_id" class="mb-1">Localisation d'origine <span class="text-danger">*</span></label>
                                        <select name="from_location_id" id="from_location_id" class="form-control select2" {{ $transfer->status === 'completed' ? 'disabled' : '' }}>
                                            <option value="">Sélectionner une localisation</option>
                                            @foreach($locations as $id => $name)
                                                <option value="{{ $id }}" {{ old('from_location_id', $transfer->from_location_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('from_location_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_location_id" class="mb-1">Nouvelle localisation <span class="text-danger">*</span></label>
                                        <select name="to_location_id" id="to_location_id" class="form-control select2" {{ $transfer->status === 'completed' ? 'disabled' : '' }}>
                                            <option value="">Sélectionner une localisation</option>
                                            @foreach($locations as $id => $name)
                                                <option value="{{ $id }}" {{ old('to_location_id', $transfer->to_location_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('to_location_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="position-fields" class="transfer-type-fields" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="from_position" class="mb-1">Poste actuel <span class="text-danger">*</span></label>
                                        <input type="text" name="from_position" id="from_position" class="form-control" 
                                            value="{{ old('from_position', $transfer->from_position) }}" 
                                            placeholder="Poste actuel"
                                            {{ $transfer->status === 'completed' ? 'readonly' : '' }}>
                                        @error('from_position')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_position" class="mb-1">Nouveau poste <span class="text-danger">*</span></label>
                                        <input type="text" name="to_position" id="to_position" class="form-control" 
                                            value="{{ old('to_position', $transfer->to_position) }}" 
                                            placeholder="Nouveau poste"
                                            {{ $transfer->status === 'completed' ? 'readonly' : '' }}>
                                        @error('to_position')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="transfer_date" class="mb-1">Date du transfert <span class="text-danger">*</span></label>
                                    <input type="date" name="transfer_date" id="transfer_date" class="form-control" 
                                        value="{{ old('transfer_date', $transfer->transfer_date->format('Y-m-d')) }}" 
                                        required {{ $transfer->status === 'completed' ? 'readonly' : '' }}>
                                    @error('transfer_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="effective_date" class="mb-1">Date d'effet <span class="text-danger">*</span></label>
                                    <input type="date" name="effective_date" id="effective_date" class="form-control" 
                                        value="{{ old('effective_date', $transfer->effective_date->format('Y-m-d')) }}" 
                                        required {{ $transfer->status === 'completed' ? 'readonly' : '' }}>
                                    @error('effective_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="reason" class="mb-1">Raison du transfert <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" required {{ $transfer->status === 'completed' ? 'readonly' : '' }}>{{ old('reason', $transfer->reason) }}</textarea>
                            @error('reason')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="notes" class="mb-1">Notes supplémentaires</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" {{ $transfer->status === 'completed' ? 'readonly' : '' }}>{{ old('notes', $transfer->notes) }}</textarea>
                            @error('notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="status" class="mb-1">Statut <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required {{ $transfer->status === 'completed' ? 'disabled' : '' }}>
                                <option value="pending" {{ old('status', $transfer->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="approved" {{ old('status', $transfer->status) == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="rejected" {{ old('status', $transfer->status) == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                                @if($transfer->status === 'completed')
                                    <option value="completed" selected>Terminé</option>
                                @endif
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        @if($transfer->status === 'completed')
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Ce transfert a été marqué comme terminé. Les champs ne peuvent plus être modifiés.
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            @if($transfer->status !== 'completed')
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                            @endif
                            <a href="{{ route('company.evenements.transfers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Retour à la liste
                            </a>
                            
                            @if($transfer->status === 'pending')
                                <div class="btn-group float-right">
                                    <button type="button" class="btn btn-success approve-btn" data-id="{{ $transfer->id }}">
                                        <i class="fas fa-check"></i> Approuver
                                    </button>
                                    <button type="button" class="btn btn-danger reject-btn" data-id="{{ $transfer->id }}">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'approbation -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Confirmer l'approbation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir approuver ce transfert ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                <form id="approve-form" method="POST" action="">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Confirmer l'approbation
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Confirmer le rejet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir rejeter ce transfert ?</p>
                <div class="form-group">
                    <label for="reject_reason">Raison du rejet (optionnel)</label>
                    <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Annuler</button>
                <form id="reject-form" method="POST" action="">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="reject_reason" id="modal_reject_reason">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Confirmer le rejet
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialisation de Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        disabled: {{ $transfer->status === 'completed' ? 'true' : 'false' }}
    });
    
    // Afficher les champs correspondant au type de transfert sélectionné
    function toggleTransferTypeFields() {
        const transferType = $('#transfer_type').val();
        $('.transfer-type-fields').hide();
        
        if (transferType === 'department') {
            $('#department-fields').show();
        } else if (transferType === 'location') {
            $('#location-fields').show();
        } else if (transferType === 'position') {
            $('#position-fields').show();
        }
    }
    
    // Initialisation des champs en fonction du type de transfert
    toggleTransferTypeFields();
    
    // Écouteur sur le changement de type de transfert
    $('#transfer_type').on('change', toggleTransferTypeFields);
    
    // Gestion du bouton d'approbation
    $('.approve-btn').on('click', function() {
        const transferId = $(this).data('id');
        $('#approve-form').attr('action', `/company/transfers/${transferId}/approve`);
        $('#approveModal').modal('show');
    });
    
    // Gestion du bouton de rejet
    $('.reject-btn').on('click', function() {
        const transferId = $(this).data('id');
        $('#reject-form').attr('action', `/company/transfers/${transferId}/reject`);
        $('#rejectModal').modal('show');
    });
    
    // Mise à jour du champ caché avec la raison du rejet
    $('#reject-form').on('submit', function() {
        $('#modal_reject_reason').val($('#reject_reason').val());
    });
    
    // Désactiver les champs si le statut est 'completed'
    @if($transfer->status === 'completed')
        $('input, select, textarea').not('[name="_token"], [name="_method"], .btn').prop('disabled', true);
    @endif
    
    // Validation du formulaire
    $('#transfer-form').validate({
        rules: {
            employee_id: 'required',
            transfer_type: 'required',
            transfer_date: 'required',
            effective_date: {
                required: true,
                greaterThanOrEqual: '#transfer_date'
            },
            reason: 'required',
            status: 'required',
            'from_department_id': {
                required: function() {
                    return $('#transfer_type').val() === 'department';
                }
            },
            'to_department_id': {
                required: function() {
                    return $('#transfer_type').val() === 'department';
                },
                notEqual: '#from_department_id'
            },
            'from_location_id': {
                required: function() {
                    return $('#transfer_type').val() === 'location';
                }
            },
            'to_location_id': {
                required: function() {
                    return $('#transfer_type').val() === 'location';
                },
                notEqual: '#from_location_id'
            },
            'from_position': {
                required: function() {
                    return $('#transfer_type').val() === 'position';
                }
            },
            'to_position': {
                required: function() {
                    return $('#transfer_type').val() === 'position';
                },
                notEqual: '#from_position'
            }
        },
        messages: {
            employee_id: 'Veuillez sélectionner un employé',
            transfer_type: 'Veuillez sélectionner un type de transfert',
            transfer_date: 'Veuillez saisir une date de transfert',
            effective_date: {
                required: 'Veuillez saisir une date d\'effet',
                greaterThanOrEqual: 'La date d\'effet doit être postérieure ou égale à la date de transfert'
            },
            reason: 'Veuillez saisir une raison pour ce transfert',
            status: 'Veuillez sélectionner un statut',
            'from_department_id': 'Veuillez sélectionner un département d\'origine',
            'to_department_id': {
                required: 'Veuillez sélectionner un nouveau département',
                notEqual: 'Le nouveau département doit être différent du département actuel'
            },
            'from_location_id': 'Veuillez sélectionner une localisation d\'origine',
            'to_location_id': {
                required: 'Veuillez sélectionner une nouvelle localisation',
                notEqual: 'La nouvelle localisation doit être différente de la localisation actuelle'
            },
            'from_position': 'Veuillez saisir le poste actuel',
            'to_position': {
                required: 'Veuillez saisir le nouveau poste',
                notEqual: 'Le nouveau poste doit être différent du poste actuel'
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
    
    // Règle de validation personnalisée pour vérifier qu'une date est postérieure ou égale à une autre
    $.validator.addMethod("greaterThanOrEqual", function(value, element, param) {
        const target = $(param);
        
        if (this.settings.onfocusout) {
            target.unbind(".validate-greaterThanOrEqual").bind("blur.validate-greaterThanOrEqual", function() {
                $(element).valid();
            });
        }
        
        const targetValue = target.val();
        
        if (!value || !targetValue) {
            return true; // La validation passe si l'un des champs est vide (géré par required)
        }
        
        // Convertir les dates en objets Date pour comparaison
        const date1 = new Date(targetValue);
        const date2 = new Date(value);
        
        return date2 >= date1;
    }, "La date doit être postérieure ou égale à la date de référence");
    
    // Règle de validation personnalisée pour vérifier que deux champs sont différents
    $.validator.addMethod("notEqual", function(value, element, param) {
        return this.optional(element) || value !== $(param).val();
    }, "Les valeurs doivent être différentes");
});
</script>
@endpush

@push('styles')
<style>
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
        border-color: #006fe6;
        color: white;
        padding: 0 10px;
        margin-top: 0.3rem;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255, 255, 255, 0.7);
        margin-right: 5px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: white;
    }
    
    .transfer-type-fields {
        margin: 20px 0;
        padding: 15px;
        border: 1px solid #e9ecef;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    
    /* Styles pour les champs désactivés */
    .form-control:disabled, .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
    }
    
    /* Styles pour les boutons d'action */
    .btn-group .btn {
        margin: 0 2px;
    }
    
    /* Styles pour les modaux */
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .modal-footer {
        border-top: 1px solid #dee2e6;
    }
    
    /* Style pour les badges de statut */
    .badge {
        font-size: 0.875em;
        padding: 0.4em 0.6em;
    }
    
    /* Style pour les icônes dans les boutons */
    .btn i {
        margin-right: 5px;
    }
</style>
@endpush
