@extends('evenements::layouts.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Gestion des Annonces</h4>
                    <p class="text-muted mb-0">Modifier les annonces et distinctions des employés</p>
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

    <!-- Contenu principal -->
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Modifier les informations de l'annonce</h4>
                    <p class="card-description">
                        Mettez à jour les détails de l'annonce. Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
                    </p>
                    
                    <form id="announcementForm" action="{{ route('company.evenements.annonces.update', $announcement->id) }}" method="POST" class="forms-sample">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Titre <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-heading"></i></span>
                                        </div>
                                        <input type="text" name="title" id="title" 
                                               class="form-control @error('title') is-invalid @enderror" 
                                               value="{{ old('title', $announcement->title) }}" 
                                               placeholder="Entrez le titre de l'annonce" required>
                                    </div>
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_id">Succursale <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" 
                                            class="form-control select2 @error('branch_id') is-invalid @enderror" 
                                            style="width: 100%;" required>
                                        <option value="">Sélectionner une succursale</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" 
                                                {{ old('branch_id', $announcement->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Date de début <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="start_date" id="start_date" 
                                               class="form-control @error('start_date') is-invalid @enderror" 
                                               value="{{ old('start_date', $announcement->start_date->format('Y-m-d')) }}" 
                                               required>
                                    </div>
                                    @error('start_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Date de fin <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="end_date" id="end_date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               value="{{ old('end_date', $announcement->end_date->format('Y-m-d')) }}" 
                                               required>
                                    </div>
                                    @error('end_date')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_id">Département <span class="text-danger">*</span></label>
                                    <select name="department_id" id="department_id" 
                                            class="form-control select2 @error('department_id') is-invalid @enderror" 
                                            style="width: 100%;" required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" 
                                                {{ old('department_id', $announcement->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employees">Employés concernés <span class="text-danger">*</span></label>
                                    <select name="employees[]" id="employees" 
                                            class="form-control select2 @error('employees') is-invalid @enderror" 
                                            multiple="multiple" style="width: 100%;" required>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ in_array($employee->id, old('employees', $selectedEmployees)) ? 'selected' : '' }}>
                                                {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})
                                            </option>
                                    </select>
                                    @error('employees')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" 
                                              class="form-control @error('description') is-invalid @enderror" 
                                              rows="6"
                                              placeholder="Saisissez la description détaillée de l'annonce"
                                              required>{{ old('description', $announcement->description) }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-light" onclick="window.history.back()">
                                        <i class="fas fa-arrow-left mr-2"></i> Annuler
                                    </button>
                                    <div>
                                        <a href="{{ route('company.evenements.annonces.show', $announcement->id) }}" class="btn btn-outline-secondary mr-2">
                                            <i class="fas fa-eye mr-1"></i> Aperçu
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Enregistrer les modifications
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<style>
    .select2-container--default .select2-selection--multiple {
        min-height: 42px;
        border: 1px solid #e4e6fc;
        border-radius: 4px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        color: #495057;
        padding: 0 8px;
        margin-top: 6px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #6c757d;
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #dc3545;
    }
    .note-editor.note-frame {
        border: 1px solid #e4e6fc;
        border-radius: 4px;
    }
    .note-editor.note-frame .note-toolbar {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e4e6fc;
    }
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-fr-FR.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialisation de Select2
        $('.select2').select2({
            placeholder: 'Sélectionner une option',
            allowClear: true,
            width: '100%',
            dropdownParent: $('.card-body')
        });

        // Initialisation de l'éditeur de texte riche
        $('#description').summernote({
            height: 200,
            lang: 'fr-FR',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onInit: function() {
                    // Ajuster la hauteur de l'éditeur
                    $('.note-editable').css('min-height', '150px');
                }
            }
        });

        // Validation des dates
        $('#start_date, #end_date').change(function() {
            var startDate = new Date($('#start_date').val());
            var endDate = new Date($('#end_date').val());
            
            if (startDate && endDate && endDate < startDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de date',
                    text: 'La date de fin doit être postérieure ou égale à la date de début',
                    confirmButtonText: 'Compris'
                });
                $(this).val('');
            }
        });

        // Chargement dynamique des départements en fonction de la succursale sélectionnée
        $('#branch_id').change(function() {
            var branchId = $(this).val();
            var departmentSelect = $('#department_id');
            
            departmentSelect.html('<option value="">Chargement...</option>');
            
            if (branchId) {
                $.ajax({
                    url: '{{-- route("departments.by-branch") --}}',
                    type: 'GET',
                    data: { branch_id: branchId },
                    success: function(data) {
                        departmentSelect.html('<option value="">Sélectionner un département</option>');
                        $.each(data, function(key, value) {
                            departmentSelect.append('<option value="' + key + '">' + value + '</option>');
                        });
                        // Réinitialiser la sélection
                        departmentSelect.val('{{ old('department_id', $announcement->department_id) }}').trigger('change');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible de charger les départements. Veuillez réessayer.',
                            confirmButtonText: 'OK'
                        });
                        departmentSelect.html('<option value="">Sélectionner un département</option>');
                    }
                });
            } else {
                departmentSelect.html('<option value="">Sélectionner un département</option>');
            }
        });

        // Déclencher le changement de succursale au chargement si une succursale est déjà sélectionnée
        @if(old('branch_id', $announcement->branch_id))
            $('#branch_id').trigger('change');
        @endif

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
                    greaterThanOrEqual: '#start_date'
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
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                title: {
                    required: 'Le titre est requis',
                    minlength: 'Le titre doit contenir au moins 5 caractères',
                    maxlength: 'Le titre ne peut pas dépasser 255 caractères'
                },
                start_date: {
                    required: 'La date de début est requise',
                    date: 'Veuillez entrer une date valide'
                },
                end_date: {
                    required: 'La date de fin est requise',
                    date: 'Veuillez entrer une date valide',
                    greaterThanOrEqual: 'La date de fin doit être postérieure ou égale à la date de début'
                },
                branch_id: {
                    required: 'La succursale est requise'
                },
                department_id: {
                    required: 'Le département est requis'
                },
                'employees[]': {
                    required: 'Veuillez sélectionner au moins un employé',
                    minlength: 'Veuillez sélectionner au moins un employé'
                },
                description: {
                    required: 'La description est requise',
                    minlength: 'La description doit contenir au moins 10 caractères'
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
            },
            submitHandler: function(form) {
                // Afficher un indicateur de chargement
                const submitButton = $(form).find('button[type="submit"]');
                const originalText = submitButton.html();
                submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enregistrement...');
                
                // Soumettre le formulaire
                form.submit();
            }
        });

        // Règle de validation personnalisée pour la date de fin
        $.validator.addMethod("greaterThanOrEqual", function(value, element, params) {
            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) >= new Date($(params).val());
            }
            return isNaN(value) && isNaN($(params).val()) || (Number(value) >= Number($(params).val())); 
        }, 'La date de fin doit être postérieure ou égale à la date de début.');
    });
</script>
@endpush
