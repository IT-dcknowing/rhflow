@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Gestion des Transferts</h4>
                    <p class="text-muted mb-0">Créé un transfert pour un employé</p>
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
                    <h3 class="card-title">Nouveau Transfert</h3>
                </div>
                <form action="{{ route('company.evenements.transfers.store') }}" method="POST" id="transfer-form">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="employee_id" class="mb-1">Employé <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-select select2" required>
                                        <option value="">Sélectionner un employé</option>
                                        @foreach($employees as $id => $name)
                                            <option value="{{ $id }}" {{ old('employee_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="transfer_type" class="mb-1">Type de transfert <span class="text-danger">*</span></label>
                                    <select name="transfer_type" id="transfer_type" class="form-select" required>
                                        <option value="">Sélectionner un type</option>  
                                        <option value="department" {{ old('transfer_type') == 'department' ? 'selected' : '' }}>Changement de département</option>
                                        <option value="location" {{ old('transfer_type') == 'location' ? 'selected' : '' }}>Changement de localisation</option>
                                        <option value="position" {{ old('transfer_type') == 'position' ? 'selected' : '' }}>Changement de poste</option>
                                    </select>
                                    @error('transfer_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Champs dynamiques en fonction du type de transfert -->
                        <div id="department-fields" class="transfer-type-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="from_department_id" class="mb-1">Département d'origine <span class="text-danger">*</span></label>
                                        <select name="from_department_id" id="from_department_id" class="form-select select2">
                                            <option value="">Sélectionner un département</option>
                                            @foreach($departments as $id => $name)
                                                <option value="{{ $id }}" {{ old('from_department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('from_department_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="to_department_id" class="mb-1">Nouveau département <span class="text-danger">*</span></label>
                                        <select name="to_department_id" id="to_department_id" class="form-control select2">
                                            <option value="">Sélectionner un département</option>
                                            @foreach($departments as $id => $name)
                                                <option value="{{ $id }}" {{ old('to_department_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                                        <select name="from_location_id" id="from_location_id" class="form-control select2">
                                            <option value="">Sélectionner une localisation</option>
                                            @foreach($locations as $id => $name)
                                                <option value="{{ $id }}" {{ old('from_location_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                                        <select name="to_location_id" id="to_location_id" class="form-control select2">
                                            <option value="">Sélectionner une localisation</option>
                                            @foreach($locations as $id => $name)
                                                <option value="{{ $id }}" {{ old('to_location_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                                        <input type="text" name="from_position" id="from_position" class="form-control" value="{{ old('from_position') }}" placeholder="Poste actuel">
                                        @error('from_position')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="to_position" class="mb-1">Nouveau poste <span class="text-danger">*</span></label>
                                        <input type="text" name="to_position" id="to_position" class="form-control" value="{{ old('to_position') }}" placeholder="Nouveau poste">
                                        @error('to_position')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transfer_date" class="mb-1">Date du transfert <span class="text-danger">*</span></label>
                                    <input type="date" name="transfer_date" id="transfer_date" class="form-control" value="{{ old('transfer_date', now()->format('Y-m-d')) }}" required>
                                    @error('transfer_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="effective_date" class="mb-1">Date d'effet <span class="text-danger">*</span></label>
                                    <input type="date" name="effective_date" id="effective_date" class="form-control" value="{{ old('effective_date', now()->addDays(15)->format('Y-m-d')) }}" required>
                                    @error('effective_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="reason" class="mb-1">Raison du transfert <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="notes" class="mb-1">Notes supplémentaires</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="status" class="mb-1">Statut <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                            </select>
                            @error('status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('company.evenements.transfers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer
                        </button>
                    </div>
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
        width: '100%'
    });
    
    // Gestion de l'affichage des champs en fonction du type de transfert
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
    
    // Écouteur sur le changement de type de transfert
    $('#transfer_type').on('change', toggleTransferTypeFields);
    
    // Initialisation au chargement
    toggleTransferTypeFields();
    
    // Pré-remplissage des informations de l'employé
    $('#employee_id').on('change', function() {
        const employeeId = $(this).val();
        
        if (!employeeId) return;
        
        // Ici, vous pouvez ajouter une requête AJAX pour récupérer les informations de l'employé
        // et pré-remplir les champs correspondants (département, localisation, poste actuel, etc.)
        // Exemple :
        /*
        $.get(`/api/employees/${employeeId}`, function(data) {
            $('#from_department_id').val(data.department_id).trigger('change');
            $('#from_location_id').val(data.location_id).trigger('change');
            $('#from_position').val(data.position);
        });
        */
    });
    
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
</style>
@endpush
