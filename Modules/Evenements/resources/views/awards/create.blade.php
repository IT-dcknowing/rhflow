@extends('layouts.app')

@section('title', 'Ajouter une Récompense - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Ajouter une Récompense</h4>
                    <p class="text-muted mb-0">Créez une nouvelle récompense pour un employé</p>
                </div>
                <div>
                    <a href="{{ route('awards.index') }}" class="btn btn-outline-secondary">
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
                    <h5 class="mb-0">📝 Formulaire de Récompense</h5>
                    <span class="badge bg-label-primary">Nouvelle entrée</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('awards.store') }}" method="POST" enctype="multipart/form-data" id="awardForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Colonne de gauche -->
                            <div class="col-md-6">
                                <!-- Sélection de l'employé -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">👤 Bénéficiaire</h6>
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
                                                <div>
                                                    <h6 class="mb-0" id="employeeName">-</h6>
                                                    <div class="text-muted">
                                                        <span id="employeePosition">-</span> • 
                                                        <span id="employeeDepartment">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Détails de la récompense -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">🏆 Détails de la Récompense</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                   id="title" name="title" value="{{ old('title') }}" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="award_type" class="form-label">Type de Récompense <span class="text-danger">*</span></label>
                                            <select class="form-select @error('award_type') is-invalid @enderror" 
                                                    id="award_type" name="award_type" required>
                                                <option value="" selected disabled>Sélectionner un type</option>
                                                @foreach(config('evenements.award_types') as $type => $details)
                                                    <option value="{{ $type }}" 
                                                            data-icon="{{ $details['icon'] }}"
                                                            data-color="{{ $details['color'] }}"
                                                            {{ old('award_type') == $type ? 'selected' : '' }}>
                                                        {{ $details['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('award_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="award_date" class="form-label">Date d'attribution <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('award_date') is-invalid @enderror" 
                                                   id="award_date" name="award_date" 
                                                   value="{{ old('award_date', now()->format('Y-m-d')) }}" required>
                                            @error('award_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="gift" class="form-label">Récompense (optionnel)</label>
                                            <input type="text" class="form-control @error('gift') is-invalid @enderror" 
                                                   id="gift" name="gift" value="{{ old('gift') }}" 
                                                   placeholder="Ex: Chèque-cadeau de 100€">
                                            @error('gift')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne de droite -->
                            <div class="col-md-6">
                                <!-- Description et pièces jointes -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">📝 Description</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description détaillée</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="8" 
                                                      placeholder="Décrivez les raisons de cette récompense et son impact...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="attachments" class="form-label">Pièces jointes</label>
                                            <input class="form-control @error('attachments') is-invalid @enderror" 
                                                   type="file" id="attachments" name="attachments[]" multiple>
                                            <div class="form-text">Vous pouvez sélectionner plusieurs fichiers (PDF, images, etc.)</div>
                                            @error('attachments')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div id="fileList" class="mt-2">
                                            <!-- Liste des fichiers sélectionnés -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Options supplémentaires -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">⚙️ Options</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="is_public" name="is_public" value="1" 
                                                       {{ old('is_public') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_public">
                                                    Rendre cette récompense visible par tous les employés
                                                </label>
                                            </div>
                                            <div class="form-text">
                                                Si coché, cette récompense sera visible dans l'espace personnel de l'employé et pourra être affichée publiquement.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="notify_employee" class="form-label">Notification</label>
                                            <select class="form-select" id="notify_employee" name="notify_employee">
                                                <option value="1" selected>Notifier l'employé par email</option>
                                                <option value="0">Ne pas notifier pour l'instant</option>
                                            </select>
                                            <div class="form-text">
                                                L'employé recevra une notification avec les détails de sa récompense.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('awards.index') }}" class="btn btn-outline-secondary">
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
    .award-preview {
        border-left: 4px solid #696cff;
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

        // Gestion de l'affichage des détails de l'employé
        const employeeSelect = document.getElementById('employee_id');
        const employeeDetails = document.querySelector('.employee-details');
        const employeeAvatar = document.getElementById('employeeAvatar');
        const employeeName = document.getElementById('employeeName');
        const employeePosition = document.getElementById('employeePosition');
        const employeeDepartment = document.getElementById('employeeDepartment');

        if (employeeSelect) {
            employeeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.value) {
                    // Mise à jour des détails de l'employé
                    employeeName.textContent = selectedOption.text.split(' (')[0];
                    employeePosition.textContent = selectedOption.dataset.position || '-';
                    employeeDepartment.textContent = selectedOption.dataset.department || '-';
                    
                    // Afficher la section des détails
                    employeeDetails.classList.remove('d-none');
                    
                    // Mettre à jour la photo de profil (à implémenter avec une vraie logique)
                    // employeeAvatar.src = selectedOption.dataset.avatar || '{{ asset('images/avatars/default-avatar.png') }}';
                } else {
                    employeeDetails.classList.add('d-none');
                }
            });

            // Déclencher l'événement change si une valeur est déjà sélectionnée
            if (employeeSelect.value) {
                employeeSelect.dispatchEvent(new Event('change'));
            }
        }

        // Aperçu du type de récompense
        const awardTypeSelect = document.getElementById('award_type');
        
        if (awardTypeSelect) {
            awardTypeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const previewCard = document.getElementById('awardPreview');
                
                if (selectedOption.value && previewCard) {
                    const icon = selectedOption.dataset.icon || 'fa-award';
                    const color = selectedOption.dataset.color || 'primary';
                    
                    // Mettre à jour l'aperçu
                    previewCard.querySelector('.award-icon').className = `fas ${icon} fa-3x text-${color}`;
                    previewCard.querySelector('.award-type').textContent = selectedOption.text;
                    previewCard.querySelector('.award-badge').className = `badge bg-label-${color} mb-2`;
                    previewCard.querySelector('.award-badge i').className = `${icon} me-1`;
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
                        fileInfo.innerHTML = `
                            <i class="far fa-file me-2"></i>
                            <span class="file-name">${file.name}</span>
                            <small class="text-muted ms-2">${formatFileSize(file.size)}</small>
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
            
            // Déclencher l'événement change si des fichiers sont déjà sélectionnés
            if (fileInput.files.length > 0) {
                fileInput.dispatchEvent(new Event('change'));
            }
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
        const form = document.getElementById('awardForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Ajoutez ici votre logique de validation si nécessaire
                // Par exemple, vérifier que les champs requis sont remplis
                
                // Si tout est valide, le formulaire sera soumis
                // Sinon, empêcher la soumission avec e.preventDefault()
            });
        }
    });
</script>
@endpush
@endsection