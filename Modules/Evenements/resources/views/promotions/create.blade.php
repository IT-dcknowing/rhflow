@extends('layouts.app')

@section('title', 'Ajouter une Promotion - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📈 Ajouter une Promotion</h4>
                    <p class="text-muted mb-0">Enregistrez une nouvelle évolution de carrière</p>
                </div>
                <div>
                    <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte du formulaire -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📝 Formulaire de Promotion</h5>
                    <span class="badge bg-label-primary">Nouvelle entrée</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('promotions.store') }}" method="POST" enctype="multipart/form-data" id="promotionForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Colonne de gauche -->
                            <div class="col-md-6">
                                <!-- Sélection de l'employé -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">👤 Employé concerné</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="employee_id" class="form-label">Employé <span class="text-danger">*</span></label>
                                            <select class="form-select select2 @error('employee_id') is-invalid @enderror" 
                                                    id="employee_id" name="employee_id" required>
                                                <option value="" selected disabled>Sélectionner un employé</option>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}" 
                                                            data-department="{{ $employee->department->name ?? 'N/A' }}"
                                                            data-position="{{ $employee->position ?? 'N/A' }}"
                                                            data-salary="{{ number_format($employee->salary ?? 0, 2, ',', ' ') }} {{ $employee->currency ?? 'MAD' }}"
                                                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->full_name }} ({{ $employee->matricule }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('employee_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="employee-details border rounded p-3 bg-light d-none">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar avatar-md me-3">
                                                    <img id="employeeAvatar" src="{{ asset('images/avatars/default-avatar.png') }}" 
                                                         alt="Avatar" class="rounded-circle">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0" id="employeeName">-</h6>
                                                    <div class="text-muted mb-1">
                                                        <span id="employeePosition">-</span> • 
                                                        <span id="employeeDepartment">-</span>
                                                    </div>
                                                    <div class="text-success fw-medium">
                                                        <i class="fas fa-money-bill-wave me-1"></i>
                                                        <span id="employeeSalary">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Détails de la promotion -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">📊 Détails de la Promotion</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="promotion_type" class="form-label">Type de Promotion <span class="text-danger">*</span></label>
                                            <select class="form-select @error('promotion_type') is-invalid @enderror" 
                                                    id="promotion_type" name="promotion_type" required>
                                                <option value="" selected disabled>Sélectionner un type</option>
                                                @foreach(config('evenements.promotion_types') as $type => $details)
                                                    <option value="{{ $type }}" 
                                                            data-icon="{{ $details['icon'] }}"
                                                            data-color="{{ $details['color'] }}"
                                                            {{ old('promotion_type') == $type ? 'selected' : '' }}>
                                                        {{ $details['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('promotion_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="promotion_date" class="form-label">Date d'effet <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control @error('promotion_date') is-invalid @enderror" 
                                                           id="promotion_date" name="promotion_date" 
                                                           value="{{ old('promotion_date', now()->format('Y-m-d')) }}" required>
                                                    @error('promotion_date')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                                    <select class="form-select @error('status') is-invalid @enderror" 
                                                            id="status" name="status" required>
                                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approuvée</option>
                                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                                                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>En attente RH</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <div>
                                                    <h6 class="alert-heading mb-1">Information importante</h6>
                                                    <p class="mb-0">Les modifications de poste et de salaire seront appliquées automatiquement après validation de la promotion.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne de droite -->
                            <div class="col-md-6">
                                <!-- Détails du poste et de la rémunération -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">💼 Détails du Poste</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="previous_position" class="form-label">Poste actuel</label>
                                            <input type="text" class="form-control bg-light" 
                                                   id="previous_position" 
                                                   value="{{ old('previous_position') }}" 
                                                   readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="new_position" class="form-label">Nouveau poste <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('new_position') is-invalid @enderror" 
                                                   id="new_position" name="new_position" 
                                                   value="{{ old('new_position') }}" required>
                                            @error('new_position')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="department_id" class="form-label">Nouveau département <span class="text-danger">*</span></label>
                                            <select class="form-select @error('department_id') is-invalid @enderror" 
                                                    id="department_id" name="department_id" required>
                                                <option value="" selected disabled>Sélectionner un département</option>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('department_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <hr class="my-4">

                                        <h6 class="mb-3">Rémunération</h6>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="current_salary" class="form-label">Salaire actuel</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control bg-light" 
                                                               id="current_salary" readonly>
                                                        <span class="input-group-text">MAD</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="new_salary" class="form-label">Nouveau salaire</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" 
                                                               class="form-control @error('new_salary') is-invalid @enderror" 
                                                               id="new_salary" name="new_salary" 
                                                               value="{{ old('new_salary') }}">
                                                        <span class="input-group-text">MAD</span>
                                                        @error('new_salary')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="salary_increase_percentage" class="form-label">Pourcentage d'augmentation</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" 
                                                       class="form-control @error('salary_increase_percentage') is-invalid @enderror" 
                                                       id="salary_increase_percentage" name="salary_increase_percentage" 
                                                       value="{{ old('salary_increase_percentage') }}">
                                                <span class="input-group-text">%</span>
                                                @error('salary_increase_percentage')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-text">Laissez vide pour calculer automatiquement</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations complémentaires -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">📝 Informations Complémentaires</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="reason" class="form-label">Raison de la promotion</label>
                                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                                      id="reason" name="reason" rows="3" 
                                                      placeholder="Détaillez les raisons de cette promotion...">{{ old('reason') }}</textarea>
                                            @error('reason')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes internes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" name="notes" rows="3" 
                                                      placeholder="Notes et commentaires internes...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="attachments" class="form-label">Pièces jointes</label>
                                            <input class="form-control @error('attachments') is-invalid @enderror" 
                                                   type="file" id="attachments" name="attachments[]" multiple>
                                            <div class="form-text">Vous pouvez joindre des documents justificatifs (PDF, images, etc.)</div>
                                            @error('attachments')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div id="fileList" class="mt-2">
                                                <!-- Liste des fichiers sélectionnés -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Annuler
                                    </a>
                                    <div>
                                        <button type="submit" name="save_and_new" value="1" class="btn btn-outline-primary">
                                            <i class="fas fa-save me-1"></i> Enregistrer et nouveau
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check me-1"></i> Enregistrer
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

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        padding: 5px 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .employee-details {
        transition: all 0.3s ease;
    }
    .promotion-preview {
        border-left: 4px solid #696cff;
    }
    .salary-change {
        font-size: 0.9rem;
    }
    .salary-increase {
        color: #28a745;
    }
    .salary-decrease {
        color: #dc3545;
    }
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation de Select2
        $('.select2').select2({
            placeholder: 'Sélectionner une option',
            allowClear: true,
            width: '100%'
        });

        // Éléments du DOM
        const employeeSelect = document.getElementById('employee_id');
        const employeeDetails = document.querySelector('.employee-details');
        const employeeAvatar = document.getElementById('employeeAvatar');
        const employeeName = document.getElementById('employeeName');
        const employeePosition = document.getElementById('employeePosition');
        const employeeDepartment = document.getElementById('employeeDepartment');
        const employeeSalary = document.getElementById('employeeSalary');
        const currentSalaryInput = document.getElementById('current_salary');
        const previousPositionInput = document.getElementById('previous_position');
        const newSalaryInput = document.getElementById('new_salary');
        const salaryIncreasePercentageInput = document.getElementById('salary_increase_percentage');
        
        // Mise à jour des détails de l'employé
        function updateEmployeeDetails(selectedOption) {
            if (selectedOption && selectedOption.value) {
                // Mise à jour des détails de l'employé
                employeeName.textContent = selectedOption.text.split(' (')[0];
                employeePosition.textContent = selectedOption.dataset.position || '-';
                employeeDepartment.textContent = selectedOption.dataset.department || '-';
                employeeSalary.textContent = selectedOption.dataset.salary || '-';
                
                // Mettre à jour les champs du formulaire
                if (selectedOption.dataset.position) {
                    previousPositionInput.value = selectedOption.dataset.position;
                }
                
                if (selectedOption.dataset.salary) {
                    const salaryValue = selectedOption.dataset.salary.replace(/[^0-9,]/g, '').replace(',', '.');
                    currentSalaryInput.value = parseFloat(salaryValue).toFixed(2);
                }
                
                // Afficher la section des détails
                employeeDetails.classList.remove('d-none');
            } else {
                employeeDetails.classList.add('d-none');
            }
        }

        // Gestion du changement d'employé
        if (employeeSelect) {
            employeeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                updateEmployeeDetails(selectedOption);
                
                // Réinitialiser les champs liés au salaire
                newSalaryInput.value = '';
                salaryIncreasePercentageInput.value = '';
            });

            // Déclencher l'événement change si une valeur est déjà sélectionnée
            if (employeeSelect.value) {
                employeeSelect.dispatchEvent(new Event('change'));
            }
        }

        // Calcul automatique du pourcentage d'augmentation
        if (newSalaryInput && salaryIncreasePercentageInput && currentSalaryInput) {
            newSalaryInput.addEventListener('input', function() {
                const currentSalary = parseFloat(currentSalaryInput.value) || 0;
                const newSalary = parseFloat(this.value) || 0;
                
                if (newSalary > 0 && currentSalary > 0) {
                    const increase = ((newSalary - currentSalary) / currentSalary) * 100;
                    salaryIncreasePercentageInput.value = increase.toFixed(2);
                } else {
                    salaryIncreasePercentageInput.value = '';
                }
            });

            // Calcul automatique du nouveau salaire à partir du pourcentage
            salaryIncreasePercentageInput.addEventListener('input', function() {
                const currentSalary = parseFloat(currentSalaryInput.value) || 0;
                const increasePercentage = parseFloat(this.value) || 0;
                
                if (increasePercentage > 0 && currentSalary > 0) {
                    const newSalary = currentSalary * (1 + (increasePercentage / 100));
                    newSalaryInput.value = newSalary.toFixed(2);
                } else {
                    newSalaryInput.value = '';
                }
            });
        }

        // Gestion des pièces jointes
        const fileInput = document.getElementById('attachments');
        const fileList = document.getElementById('fileList');
        
        if (fileInput && fileList) {
            fileInput.addEventListener('change', function() {
                fileList.innerHTML = ''; // Vider la liste
                
                if (this.files.length > 0) {
                    const list = document.createElement('ul');
                    list.className = 'list-unstyled';
                    
                    Array.from(this.files).forEach((file, index) => {
                        const listItem = document.createElement('li');
                        listItem.className = 'd-flex justify-content-between align-items-center mb-2';
                        
                        const fileInfo = document.createElement('div');
                        fileInfo.className = 'd-flex align-items-center';
                        fileInfo.innerHTML = `
                            <i class="far fa-file me-2"></i>
                            <div>
                                <div class="file-name">${file.name}</div>
                                <small class="text-muted">${formatFileSize(file.size)}</small>
                            </div>
                        `;
                        
                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-sm btn-icon btn-label-danger';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.onclick = () => {
                            // Créer un nouveau DataTransfer et y ajouter uniquement les fichiers non supprimés
                            const dataTransfer = new DataTransfer();
                            Array.from(fileInput.files).forEach((f, i) => {
                                if (i !== index) {
                                    dataTransfer.items.add(f);
                                }
                            });
                            
                            // Mettre à jour l'input file
                            fileInput.files = dataTransfer.files;
                            
                            // Mettre à jour l'affichage
                            fileInput.dispatchEvent(new Event('change'));
                        };
                        
                        listItem.appendChild(fileInfo);
                        listItem.appendChild(removeBtn);
                        list.appendChild(listItem);
                    });
                    
                    fileList.appendChild(list);
                }
            });
        }
        
        // Fonction utilitaire pour formater la taille des fichiers
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Validation du formulaire
        const form = document.getElementById('promotionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Vérifier que le nouveau salaire est supérieur à l'ancien si augmentation
                const currentSalary = parseFloat(currentSalaryInput.value) || 0;
                const newSalary = parseFloat(newSalaryInput.value) || 0;
                
                if (newSalary > 0 && newSalary < currentSalary) {
                    if (!confirm('Le nouveau salaire est inférieur au salaire actuel. Voulez-vous vraiment continuer ?')) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                // Ajoutez ici d'autres validations si nécessaire
            });
        }
    });
</script>
@endpush
@endsection