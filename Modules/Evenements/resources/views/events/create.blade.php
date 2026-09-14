@extends('layouts.app')

@section('title', 'Créer un événement')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3b82f6;
        border: none;
        color: white;
        padding: 2px 10px;
        margin-top: 5px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255, 255, 255, 0.7);
        margin-right: 5px;
    }
    .select2-container--default .select2-selection--multiple {
        min-height: 42px;
        padding-bottom: 5px;
    }
    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        display: inline-block;
        margin-right: 10px;
        vertical-align: middle;
        border: 1px solid #e2e8f0;
    }
    .event-type-option {
        padding: 8px 12px;
        margin: 2px 0;
        border-radius: 4px;
        cursor: pointer;
    }
    .event-type-option:hover {
        background-color: #f1f5f9;
    }
    .event-type-option.selected {
        background-color: #e0f2fe;
        border-left: 3px solid #3b82f6;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Créer un nouvel événement</h4>
                <a href="{{ route('company.evenements.events.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour à la liste
                </a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('company.evenements.events.store') }}" method="POST" id="eventForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Informations de base -->
                                <div class="mb-3">
                                    <label for="title" class="form-label">Titre de l'événement <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Date et heure de début <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control datetimepicker @error('start_date') is-invalid @enderror" 
                                                   id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                            @error('start_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">Date et heure de fin</label>
                                            <input type="text" class="form-control datetimepicker @error('end_date') is-invalid @enderror" 
                                                   id="end_date" name="end_date" value="{{ old('end_date') }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="all_day" name="all_day">
                                    <label class="form-check-label" for="all_day">
                                        Toute la journée
                                    </label>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="location" class="form-label">Lieu</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                           id="location" name="location" value="{{ old('location') }}">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="branch_id" class="form-label">Agence <span class="text-danger">*</span></label>
                                            <select class="form-select @error('branch_id') is-invalid @enderror" 
                                                    id="branch_id" name="branch_id" required>
                                                <option value="">Sélectionner une agence</option>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('branch_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Statut</label>
                                            <select class="form-select @error('status') is-invalid @enderror" 
                                                    id="status" name="status">
                                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié</option>
                                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Récurrence -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Récurrence</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="recurrence_rule" class="form-label">Répéter</label>
                                                    <select class="form-select" id="recurrence_rule" name="recurrence_rule">
                                                        <option value="">Ne pas répéter</option>
                                                        <option value="daily" {{ old('recurrence_rule') == 'daily' ? 'selected' : '' }}>Tous les jours</option>
                                                        <option value="weekly" {{ old('recurrence_rule') == 'weekly' ? 'selected' : '' }}>Toutes les semaines</option>
                                                        <option value="monthly" {{ old('recurrence_rule') == 'monthly' ? 'selected' : '' }}>Tous les mois</option>
                                                        <option value="yearly" {{ old('recurrence_rule') == 'yearly' ? 'selected' : '' }}>Tous les ans</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3" id="recurrence_until_container" style="display: none;">
                                                    <label for="recurrence_until" class="form-label">Jusqu'au</label>
                                                    <input type="text" class="form-control datepicker" 
                                                           id="recurrence_until" name="recurrence_until" 
                                                           value="{{ old('recurrence_until') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rappels -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                   id="send_reminder" name="send_reminder" value="1" 
                                                   {{ old('send_reminder') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="send_reminder">
                                                <h5 class="mb-0">Rappels</h5>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body" id="reminder_options" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="reminder_minutes_before" class="form-label">Envoyer un rappel</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="reminder_minutes_before" 
                                                           name="reminder_minutes_before" min="1" 
                                                           value="{{ old('reminder_minutes_before', 30) }}">
                                                    <span class="input-group-text">minutes avant</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Participants -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Participants</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="nav nav-tabs mb-3" id="participantsTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="employees-tab" data-bs-toggle="tab" 
                                                        data-bs-target="#employees" type="button" role="tab">
                                                    Employés
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="departments-tab" data-bs-toggle="tab" 
                                                        data-bs-target="#departments" type="button" role="tab">
                                                    Départements
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="external-tab" data-bs-toggle="tab" 
                                                        data-bs-target="#external" type="button" role="tab">
                                                    Contacts externes
                                                </button>
                                            </li>
                                        </ul>
                                        
                                        <div class="tab-content" id="participantsTabContent">
                                            <!-- Employés -->
                                            <div class="tab-pane fade show active" id="employees" role="tabpanel">
                                                <select class="form-select select2" id="employee_participants" multiple>
                                                    @foreach($employees as $department => $deptEmployees)
                                                        <optgroup label="{{ $department }}">
                                                            @foreach($deptEmployees as $employee)
                                                                <option value="{{ $employee->id }}">
                                                                    {{ $employee->full_name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="employee_participants" id="employee_participants_input" value="">
                                            </div>
                                            
                                            <!-- Départements -->
                                            <div class="tab-pane fade" id="departments" role="tabpanel">
                                                <select class="form-select select2" id="department_participants" multiple>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->id }}">
                                                            {{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="department_participants" id="department_participants_input" value="">
                                            </div>
                                            
                                            <!-- Contacts externes -->
                                            <div class="tab-pane fade" id="external" role="tabpanel">
                                                <div id="external_participants_container">
                                                    <div class="input-group mb-2">
                                                        <input type="text" class="form-control" id="external_name" placeholder="Nom">
                                                        <input type="email" class="form-control" id="external_email" placeholder="Email">
                                                        <button type="button" class="btn btn-primary" id="add_external">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <div id="external_participants_list"></div>
                                                    <input type="hidden" name="external_participants" id="external_participants_input" value="">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="checkbox" id="send_invitations" name="send_invitations" value="1" checked>
                                            <label class="form-check-label" for="send_invitations">
                                                Envoyer des invitations par email aux participants
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <!-- Type d'événement -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Type d'événement</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <select class="form-select" id="event_type_id" name="event_type_id">
                                                <option value="">Sélectionner un type</option>
                                                @foreach($eventTypes as $type)
                                                    <option value="{{ $type->id }}" 
                                                            data-color="{{ $type->color }}"
                                                            {{ old('event_type_id') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="color" class="form-label">Couleur</label>
                                            <div class="input-group">
                                                <span class="input-group-text p-0 border-0">
                                                    <input type="color" class="form-control form-control-color" 
                                                           id="color" name="color" value="{{ old('color', '#3b82f6') }}" 
                                                           title="Choisir une couleur">
                                                </span>
                                                <input type="text" class="form-control" id="color_hex" 
                                                       value="{{ old('color', '#3b82f6') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Visibilité -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">Visibilité</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                   id="is_private" name="is_private" value="1" 
                                                   {{ old('is_private') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_private">
                                                Événement privé
                                            </label>
                                        </div>
                                        <small class="text-muted">
                                            Les événements privés ne sont visibles que par les participants et les administrateurs.
                                        </small>
                                    </div>
                                </div>
                                
                                <!-- Synchronisation des calendriers -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">Synchronisation</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-primary w-100 mb-2" id="sync_google">
                                                <i class="fab fa-google me-2"></i> Google Calendar
                                            </button>
                                            <button type="button" class="btn btn-outline-primary w-100" id="sync_outlook">
                                                <i class="fab fa-microsoft me-2"></i> Outlook Calendar
                                            </button>
                                        </div>
                                        <small class="text-muted">
                                            Connectez vos comptes dans les paramètres pour activer la synchronisation.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" name="draft" value="1" class="btn btn-outline-secondary">
                                Enregistrer comme brouillon
                            </button>
                            <div>
                                <a href="{{ route('company.evenements.events.index') }}" class="btn btn-outline-secondary me-2">
                                    Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des sélecteurs de date et heure
        const dateTimeConfig = {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            locale: 'fr',
            minDate: 'today',
            allowInput: true
        };
        
        const dateConfig = {
            dateFormat: 'Y-m-d',
            locale: 'fr',
            minDate: 'today',
            allowInput: true
        };
        
        flatpickr('.datetimepicker', dateTimeConfig);
        flatpickr('.datepicker', dateConfig);
        
        // Gestion de l'événement toute la journée
        const allDayCheckbox = document.getElementById('all_day');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        allDayCheckbox.addEventListener('change', function() {
            if (this.checked) {
                startDateInput._flatpickr.set('enableTime', false);
                startDateInput._flatpickr.set('dateFormat', 'Y-m-d');
                
                if (endDateInput.value) {
                    endDateInput._flatpickr.set('enableTime', false);
                    endDateInput._flatpickr.set('dateFormat', 'Y-m-d');
                }
            } else {
                startDateInput._flatpickr.set('enableTime', true);
                startDateInput._flatpickr.set('dateFormat', 'Y-m-d H:i');
                
                if (endDateInput.value) {
                    endDateInput._flatpickr.set('enableTime', true);
                    endDateInput._flatpickr.set('dateFormat', 'Y-m-d H:i');
                }
            }
        });
        
        // Initialisation de Select2 pour les participants
        $('.select2').select2({
            placeholder: 'Sélectionner des participants',
            allowClear: true,
            width: '100%',
            closeOnSelect: false
        });
        
        // Gestion des participants
        const employeeSelect = $('#employee_participants');
        const departmentSelect = $('#department_participants');
        const externalContainer = $('#external_participants_container');
        const externalList = $('#external_participants_list');
        const addExternalBtn = $('#add_external');
        
        // Mise à jour des champs cachés avec les participants sélectionnés
        function updateParticipantsInput() {
            // Employés
            const selectedEmployees = employeeSelect.val() || [];
            $('#employee_participants_input').val(JSON.stringify(selectedEmployees));
            
            // Départements
            const selectedDepartments = departmentSelect.val() || [];
            $('#department_participants_input').val(JSON.stringify(selectedDepartments));
            
            // Mettre à jour le formulaire
            updateFormParticipants();
        }
        
        // Mettre à jour le champ participants du formulaire
        function updateFormParticipants() {
            const participants = [];
            
            // Ajouter les employés
            const employeeIds = JSON.parse($('#employee_participants_input').val() || '[]');
            employeeIds.forEach(id => {
                participants.push({
                    id: id,
                    type: 'employee',
                    email: '',
                    name: ''
                });
            });
            
            // Ajouter les départements
            const departmentIds = JSON.parse($('#department_participants_input').val() || '[]');
            departmentIds.forEach(id => {
                participants.push({
                    id: id,
                    type: 'department',
                    email: '',
                    name: ''
                });
            });
            
            // Ajouter les contacts externes
            const externals = JSON.parse($('#external_participants_input').val() || '[]');
            externals.forEach(ext => {
                participants.push({
                    id: ext.id,
                    type: 'external',
                    email: ext.email,
                    name: ext.name
                });
            });
            
            // Mettre à jour le champ caché
            $('input[name="participants"]').remove();
            $('<input>').attr({
                type: 'hidden',
                name: 'participants',
                value: JSON.stringify(participants)
            }).appendTo('form');
        }
        
        // Écouteurs d'événements pour les sélecteurs
        employeeSelect.on('change', updateParticipantsInput);
        departmentSelect.on('change', updateParticipantsInput);
        
        // Gestion des contacts externes
        addExternalBtn.on('click', function() {
            const nameInput = $('#external_name');
            const emailInput = $('#external_email');
            const name = nameInput.val().trim();
            const email = emailInput.val().trim();
            
            if (name && email && validateEmail(email)) {
                const id = 'ext-' + Date.now();
                const external = { id, name, email };
                
                // Ajouter à la liste
                const item = $(`
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded" data-id="${id}">
                        <div>
                            <strong>${name}</strong><br>
                            <small class="text-muted">${email}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-external">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
                
                externalList.append(item);
                
                // Mettre à jour le champ caché
                const externals = JSON.parse($('#external_participants_input').val() || '[]');
                externals.push(external);
                $('#external_participants_input').val(JSON.stringify(externals));
                
                // Réinitialiser les champs
                nameInput.val('');
                emailInput.val('');
                
                // Mettre à jour le formulaire
                updateFormParticipants();
            } else {
                alert('Veuillez saisir un nom et un email valides.');
            }
        });
        
        // Supprimer un contact externe
        $(document).on('click', '.remove-external', function() {
            const item = $(this).closest('[data-id]');
            const id = item.data('id');
            
            // Supprimer du tableau
            let externals = JSON.parse($('#external_participants_input').val() || '[]');
            externals = externals.filter(ext => ext.id !== id);
            $('#external_participants_input').val(JSON.stringify(externals));
            
            // Supprimer visuellement
            item.remove();
            
            // Mettre à jour le formulaire
            updateFormParticipants();
        });
        
        // Validation de l'email
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Gestion du type d'événement et de la couleur
        const eventTypeSelect = $('#event_type_id');
        const colorInput = $('#color');
        const colorHexInput = $('#color_hex');
        
        eventTypeSelect.on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const color = selectedOption.data('color');
            
            if (color) {
                colorInput.val(color);
                colorHexInput.val(color);
            }
        });
        
        colorInput.on('input', function() {
            colorHexInput.val($(this).val());
        });
        
        // Gestion des rappels
        const reminderCheckbox = $('#send_reminder');
        const reminderOptions = $('#reminder_options');
        
        reminderCheckbox.on('change', function() {
            reminderOptions.toggle(this.checked);
        }).trigger('change');
        
        // Gestion de la récurrence
        const recurrenceSelect = $('#recurrence_rule');
        const recurrenceUntilContainer = $('#recurrence_until_container');
        
        recurrenceSelect.on('change', function() {
            recurrenceUntilContainer.toggle(!!$(this).val());
        }).trigger('change');
        
        // Initialisation des participants existants
        @if(old('employee_participants'))
            const oldEmployees = {!! json_encode(old('employee_participants')) !!};
            employeeSelect.val(JSON.parse(oldEmployees)).trigger('change');
        @endif
        
        @if(old('department_participants'))
            const oldDepartments = {!! json_encode(old('department_participants')) !!};
            departmentSelect.val(JSON.parse(oldDepartments)).trigger('change');
        @endif
        
        @if(old('external_participants'))
            const oldExternals = {!! json_encode(old('external_participants')) !!};
            if (oldExternals) {
                const externals = JSON.parse(oldExternals);
                externals.forEach(ext => {
                    const item = $(`
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded" data-id="${ext.id}">
                            <div>
                                <strong>${ext.name}</strong><br>
                                <small class="text-muted">${ext.email}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-external">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `);
                    externalList.append(item);
                });
                $('#external_participants_input').val(oldExternals);
            }
        @endif
        
        // Initialiser les participants
        updateParticipantsInput();
        
        // Soumission du formulaire
        $('#eventForm').on('submit', function() {
            // Désactiver le bouton pour éviter les soumissions multiples
            $(this).find('button[type="submit"]').prop('disabled', true);
        });
    });
</script>
@endpush
