@extends('layouts.app')

@section('title', 'Modifier une Promotion - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📝 Modifier la Promotion</h4>
                    <p class="text-muted mb-0">Mettez à jour les informations de la promotion</p>
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
                    <h5 class="mb-0">📋 Formulaire de Modification</h5>
                    <span class="badge bg-label-{{ $promotion->status_color }}">{{ $promotion->status_label }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('promotions.update', $promotion->id) }}" method="POST" enctype="multipart/form-data" id="promotionForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Colonne de gauche -->
                            <div class="col-md-6">
                                <!-- Informations de l'employé -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">👤 Employé concerné</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-md me-3">
                                                <img src="{{ $promotion->employee->avatar_url ?? asset('images/avatars/default-avatar.png') }}" 
                                                     alt="Avatar" class="rounded-circle">
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $promotion->employee->full_name }}</h6>
                                                <div class="text-muted mb-1">
                                                    {{ $promotion->employee->position ?? 'N/A' }} • 
                                                    {{ $promotion->employee->department->name ?? 'N/A' }}
                                                </div>
                                                <div class="text-success fw-medium">
                                                    <i class="fas fa-money-bill-wave me-1"></i>
                                                    {{ number_format($promotion->employee->salary ?? 0, 2, ',', ' ') }} {{ $promotion->employee->currency ?? 'MAD' }}
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="employee_id" value="{{ $promotion->employee_id }}">
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
                                                @foreach(config('evenements.promotion_types') as $type => $details)
                                                    <option value="{{ $type }}" 
                                                            data-icon="{{ $details['icon'] }}"
                                                            data-color="{{ $details['color'] }}"
                                                            {{ old('promotion_type', $promotion->promotion_type) == $type ? 'selected' : '' }}>
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
                                                           value="{{ old('promotion_date', $promotion->promotion_date ? $promotion->promotion_date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
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
                                                        <option value="pending" {{ old('status', $promotion->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                                                        <option value="approved" {{ old('status', $promotion->status) == 'approved' ? 'selected' : '' }}>Approuvée</option>
                                                        <option value="rejected" {{ old('status', $promotion->status) == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                                                        <option value="on_hold" {{ old('status', $promotion->status) == 'on_hold' ? 'selected' : '' }}>En attente RH</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        @if($promotion->status == 'approved' && $promotion->effective_date)
                                        <div class="alert alert-success">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle me-2"></i>
                                                <div>
                                                    <h6 class="alert-heading mb-1">Promotion approuvée</h6>
                                                    <p class="mb-0">Cette promotion a été appliquée avec succès le {{ $promotion->effective_date->format('d/m/Y') }}.</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
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
                                            <label for="previous_position" class="form-label">Poste précédent</label>
                                            <input type="text" class="form-control bg-light" 
                                                   id="previous_position" 
                                                   value="{{ $promotion->previous_position }}" 
                                                   readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="new_position" class="form-label">Nouveau poste <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('new_position') is-invalid @enderror" 
                                                   id="new_position" name="new_position" 
                                                   value="{{ old('new_position', $promotion->new_position) }}" required>
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
                                                    <option value="{{ $department->id }}" {{ old('department_id', $promotion->department_id) == $department->id ? 'selected' : '' }}>
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
                                                    <label for="current_salary" class="form-label">Ancien salaire</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control bg-light" 
                                                               id="current_salary" 
                                                               value="{{ number_format($promotion->previous_salary, 2, ',', ' ') }}" 
                                                               readonly>
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
                                                               value="{{ old('new_salary', $promotion->new_salary) }}">
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
                                                       value="{{ old('salary_increase_percentage', $promotion->salary_increase_percentage) }}">
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
                                                      placeholder="Détaillez les raisons de cette promotion...">{{ old('reason', $promotion->reason) }}</textarea>
                                            @error('reason')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes internes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" name="notes" rows="3" 
                                                      placeholder="Notes et commentaires internes...">{{ old('notes', $promotion->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Pièces jointes</label>
                                            @if($promotion->attachments->count() > 0)
                                                <div class="mb-2">
                                                    @foreach($promotion->attachments as $attachment)
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <div class="d-flex align-items-center">
                                                                <i class="far fa-file me-2"></i>
                                                                <div>
                                                                    <div class="file-name">{{ $attachment->original_filename }}</div>
                                                                    <small class="text-muted">{{ $attachment->file_size_formatted }}</small>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <a href="{{ route('promotions.download', $attachment->id) }}" class="btn btn-sm btn-icon btn-label-primary me-1" data-bs-toggle="tooltip" title="Télécharger">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-icon btn-label-danger" 
                                                                        data-bs-toggle="modal" data-bs-target="#deleteAttachmentModal"
                                                                        data-id="{{ $attachment->id }}" data-filename="{{ $attachment->original_filename }}">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <label for="attachments" class="form-label">Ajouter des pièces jointes</label>
                                                <input class="form-control @error('attachments') is-invalid @enderror" 
                                                       type="file" id="attachments" name="attachments[]" multiple>
                                                <div class="form-text">Vous pouvez ajouter des documents justificatifs (PDF, images, etc.)</div>
                                                @error('attachments')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div id="fileList" class="mt-2">
                                                    <!-- Liste des nouveaux fichiers sélectionnés -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-label-danger" data-bs-toggle="modal" data-bs-target="#deletePromotionModal">
                                            <i class="fas fa-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                    <div>
                                        <a href="{{ route('promotions.show', $promotion->id) }}" class="btn btn-label-secondary">
                                            <i class="fas fa-times me-1"></i> Annuler
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Mettre à jour
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deletePromotionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette promotion ? Cette action est irréversible.</p>
                <p class="mb-0"><strong>Employé :</strong> {{ $promotion->employee->full_name }}</p>
                <p class="mb-0"><strong>Poste :</strong> {{ $promotion->new_position }}</p>
                <p class="mb-0"><strong>Date :</strong> {{ $promotion->promotion_date->format('d/m/Y') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('promotions.destroy', $promotion->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression de pièce jointe -->
<div class="modal fade" id="deleteAttachmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le fichier <strong id="attachmentName"></strong> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteAttachmentForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Supprimer
                    </button>
                </form>
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
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Gestion de la suppression des pièces jointes
        var deleteAttachmentModal = document.getElementById('deleteAttachmentModal');
        if (deleteAttachmentModal) {
            deleteAttachmentModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var attachmentId = button.getAttribute('data-id');
                var attachmentName = button.getAttribute('data-filename');
                
                var modalTitle = deleteAttachmentModal.querySelector('.modal-title');
                var attachmentNameElement = document.getElementById('attachmentName');
                var form = document.getElementById('deleteAttachmentForm');
                
                attachmentNameElement.textContent = attachmentName;
                form.action = "{{ route('promotions.attachments.destroy', '') }}/" + attachmentId;
            });
        }

        // Calcul automatique du pourcentage d'augmentation
        const currentSalaryInput = document.getElementById('current_salary');
        const newSalaryInput = document.getElementById('new_salary');
        const salaryIncreasePercentageInput = document.getElementById('salary_increase_percentage');

        if (newSalaryInput && salaryIncreasePercentageInput && currentSalaryInput) {
            // Fonction pour formater le montant du salaire
            function parseSalaryValue(value) {
                return parseFloat(value.replace(/[^0-9,]/g, '').replace(',', '.')) || 0;
            }

            // Récupérer la valeur actuelle du salaire
            const currentSalary = parseSalaryValue(currentSalaryInput.value);
            
            // Mise à jour du pourcentage lorsque le nouveau salaire change
            newSalaryInput.addEventListener('input', function() {
                const newSalary = parseFloat(this.value) || 0;
                
                if (newSalary > 0 && currentSalary > 0) {
                    const increase = ((newSalary - currentSalary) / currentSalary) * 100;
                    salaryIncreasePercentageInput.value = increase.toFixed(2);
                } else {
                    salaryIncreasePercentageInput.value = '';
                }
            });

            // Mise à jour du nouveau salaire lorsque le pourcentage change
            salaryIncreasePercentageInput.addEventListener('input', function() {
                const increasePercentage = parseFloat(this.value) || 0;
                
                if (increasePercentage > 0 && currentSalary > 0) {
                    const newSalary = currentSalary * (1 + (increasePercentage / 100));
                    newSalaryInput.value = newSalary.toFixed(2);
                } else {
                    newSalaryInput.value = '';
                }
            });
        }

        // Gestion des nouvelles pièces jointes
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
    });
</script>
@endpush
@endsection