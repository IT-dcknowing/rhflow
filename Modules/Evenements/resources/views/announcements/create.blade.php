@extends('layouts.app')

@section('title', 'Créer une annonce')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Gestion des Annonces</h4>
                    <p class="text-muted mb-0">Créé une annonce et distinctions des employés</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small> 
                </div>
                <div>
                    <a href="{{ route('company.evenements.annonces.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit text-primary me-2"></i> Informations de l'annonce
                    </h5>
                </div>
                
                <form action="{{ route('company.evenements.annonces.store') }}" method="POST" id="announcementForm">
                    @csrf
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Titre -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title" class="form-label fw-semibold">
                                        Titre <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                        <input type="text" 
                                               name="title" 
                                               id="title" 
                                               class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                               value="{{ old('title') }}" 
                                               placeholder="Entrez le titre de l'annonce"
                                               required>
                                        @error('title')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <small class="form-text text-muted">Donnez un titre clair et concis à votre annonce</small>
                                </div>
                            </div>

                            <!-- Période -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date" class="form-label fw-semibold">
                                        Date de début <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        <input type="date" 
                                               name="start_date" 
                                               id="start_date" 
                                               class="form-control @error('start_date') is-invalid @enderror" 
                                               value="{{ old('start_date', now()->format('Y-m-d')) }}" 
                                               required>
                                        @error('start_date')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date" class="form-label fw-semibold">
                                        Date de fin <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="far fa-calendar-check"></i></span>
                                        <input type="date" 
                                               name="end_date" 
                                               id="end_date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               value="{{ old('end_date', now()->addDays(7)->format('Y-m-d')) }}" 
                                               required>
                                        @error('end_date')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Succursale et Département -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_id" class="form-label fw-semibold">
                                        Succursale <span class="text-danger">*</span>
                                    </label>
                                    <select name="branch_id" 
                                            id="branch_id" 
                                            class="form-select select2 @error('branch_id') is-invalid @enderror" 
                                            required>
                                        <option value="">Sélectionner une succursale</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_id" class="form-label fw-semibold">
                                        Département <span class="text-danger">*</span>
                                    </label>
                                    <select name="department_id" 
                                            id="department_id" 
                                            class="form-select select2 @error('department_id') is-invalid @enderror" 
                                            required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Employés concernés -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="employees" class="form-label fw-semibold">
                                        Employés concernés <span class="text-danger">*</span>
                                    </label>
                                    <select name="employees[]" 
                                            id="employees" 
                                            class="form-select select2 @error('employees') is-invalid @enderror" 
                                            multiple="multiple" 
                                            required>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ in_array($employee->id, old('employees', [])) ? 'selected' : '' }}>
                                                {{ $employee->full_name }} ({{ $employee->matricule ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employees')
                                        <div class="invalid-feedback d-block">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="form-text text-muted">Sélectionnez un ou plusieurs employés concernés par cette annonce</small>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label fw-semibold">
                                        Description détaillée
                                    </label>
                                    <textarea name="description" 
                                              id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="6"
                                              placeholder="Décrivez en détail le contenu de l'annonce...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                        </div>
                                    @enderror
                                    <div class="form-text">
                                        <small>Vous pouvez utiliser le formatage de texte riche pour mettre en valeur certaines informations.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light border-top">
                        <div class="d-flex justify-content-between py-4">
                            <a href="{{ route('company.evenements.annonces.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i> Retour à la liste
                            </a>
                            <div class="btn-group">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i> Réinitialiser
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer l'annonce
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 0.9375rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        padding: 0;
    }
    
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        display: inline-flex;
        align-items: center;
        background-color: #e9ecef;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
        margin: 0;
        font-size: 0.8125rem;
    }
    
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        color: #6c757d;
        margin-right: 0.375rem;
        border: none;
        background: none;
        padding: 0;
    }
    
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #dc3545;
    }
    
    .form-text {
        font-size: 0.75rem;
    }
    
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
    }
    
    .btn-group .btn {
        margin: 0;
    }
    
    .btn-group .btn:not(:last-child) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .btn-group .btn:not(:first-child) {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        margin-left: -1px;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialisation de Select2 avec le thème Bootstrap 5
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionner une option',
            allowClear: true,
            closeOnSelect: false,
            language: {
                noResults: function() {
                    return "Aucun résultat trouvé";
                },
                searching: function() {
                    return "Recherche en cours...";
                },
                inputTooShort: function(args) {
                    return "Veuillez saisir au moins " + args.minimum + " caractères";
                }
            }
        });
        
        // Validation du formulaire
        $('#announcementForm').validate({
            rules: {
                title: {
                    required: true,
                    minlength: 5,
                    maxlength: 255
                },
                start_date: {
                    required: true,
                    date: true
                },
                end_date: {
                    required: true,
                    date: true,
                    greaterThanOrEqual: "#start_date"
                },
                branch_id: {
                    required: true
                },
                department_id: {
                    required: true
                },
                'employees[]': {
                    required: true,
                    minlength: 1
                },
                description: {
                    maxlength: 2000
                }
            },
            messages: {
                title: {
                    required: "Le titre est obligatoire",
                    minlength: "Le titre doit contenir au moins 5 caractères",
                    maxlength: "Le titre ne peut pas dépasser 255 caractères"
                },
                start_date: {
                    required: "La date de début est obligatoire",
                    date: "Veuillez entrer une date valide"
                },
                end_date: {
                    required: "La date de fin est obligatoire",
                    date: "Veuillez entrer une date valide",
                    greaterThanOrEqual: "La date de fin doit être postérieure ou égale à la date de début"
                },
                branch_id: {
                    required: "Veuillez sélectionner une succursale"
                },
                department_id: {
                    required: "Veuillez sélectionner un département"
                },
                'employees[]': {
                    required: "Veuillez sélectionner au moins un employé",
                    minlength: "Veuillez sélectionner au moins un employé"
                },
                description: {
                    maxlength: "La description ne peut pas dépasser 2000 caractères"
                }
            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
        
        // Règle personnalisée pour vérifier que la date de fin est postérieure ou égale à la date de début
        $.validator.addMethod("greaterThanOrEqual", function(value, element, params) {
            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) >= new Date($(params).val());
            }
            return isNaN(value) && isNaN($(params).val()) 
                || (Number(value) >= Number($(params).val())); 
        }, 'La date de fin doit être postérieure ou égale à la date de début');
        
        // Mise à jour dynamique des départements en fonction de la succursale sélectionnée
        $('#branch_id').on('change', function() {
            const branchId = $(this).val();
            const departmentSelect = $('#department_id');
            
            if (branchId) {
                // Désactiver le champ pendant le chargement
                departmentSelect.prop('disabled', true);
                
                // Charger les départements de la succursale sélectionnée via AJAX
                $.ajax({
                    url: '{{-- route("api.departments.by-branch") --}}',
                    type: 'GET',
                    data: { branch_id: branchId },
                    success: function(data) {
                        // Vider et remplir les options du select
                        departmentSelect.empty().append('<option value="">Sélectionner un département</option>');
                        
                        $.each(data, function(key, value) {
                            departmentSelect.append(`<option value="${value.id}">${value.name}</option>`);
                        });
                        
                        // Réactiver le champ
                        departmentSelect.prop('disabled', false);
                    },
                    error: function() {
                        console.error('Erreur lors du chargement des départements');
                        departmentSelect.prop('disabled', false);
                    }
                });
            } else {
                // Si aucune succursale n'est sélectionnée, vider et désactiver le champ département
                departmentSelect.empty().append('<option value="">Sélectionner un département</option>').prop('disabled', true);
                $(this).val('');
            }
        });
    });
</script>
@endpush
