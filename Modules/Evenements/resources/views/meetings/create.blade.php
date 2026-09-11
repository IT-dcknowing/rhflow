@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Créer une nouvelle réunion</h4>
                    <a href="{{ route('company.meetings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.meetings.store') }}" method="POST" id="meetingForm">
                        @csrf
                        
                        <!-- Informations de base -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Informations de la réunion</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Titre de la réunion <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="meeting_type">Type de réunion <span class="text-danger">*</span></label>
                                            <select class="form-select" id="meeting_type" name="meeting_type" required>
                                                <option value="">Sélectionner un type</option>
                                                <option value="team">Équipe</option>
                                                <option value="project">Projet</option>
                                                <option value="client">Client</option>
                                                <option value="other">Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Date et heure de début <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date">Date et heure de fin <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="location">Lieu ou lien de la réunion <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="location" name="location" required>
                                            <small class="form-text text-muted">Indiquez une salle ou un lien de visioconférence</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Ordre du jour <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Participants -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Participants</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Employés</label>
                                            <select class="form-select select2" id="employee_participants" name="employee_participants[]" multiple>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->department->name ?? 'Sans département' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Départements</label>
                                            <select class="form-select select2" id="department_participants" name="department_participants[]" multiple>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Invités externes (emails séparés par des virgules)</label>
                                            <textarea class="form-control" id="external_participants" name="external_participants" rows="2" placeholder="exemple1@domaine.com, exemple2@domaine.com"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pièces jointes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Pièces jointes</h5>
                                <div class="form-group">
                                    <div class="file-upload">
                                        <input type="file" id="attachments" name="attachments[]" multiple class="file-upload-input">
                                        <div class="file-upload-area">
                                            <div class="file-upload-icon">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                            </div>
                                            <h4>Glissez et déposez les fichiers ici</h4>
                                            <p>ou</p>
                                            <button type="button" class="btn btn-primary">Parcourir</button>
                                            <p class="mt-2 mb-0">Formats acceptés: .pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .jpg, .png</p>
                                            <p class="mb-0">Taille maximale: 10 Mo par fichier</p>
                                        </div>
                                        <div id="fileList" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Options supplémentaires -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Options supplémentaires</h5>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="send_reminder" name="send_reminder" checked>
                                    <label class="form-check-label" for="send_reminder">Envoyer des rappels aux participants</label>
                                </div>
                                
                                <div id="reminderOptions" class="ms-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Envoyer un rappel</label>
                                                <select class="form-select" id="reminder_time" name="reminder_time">
                                                    <option value="15">15 minutes avant</option>
                                                    <option value="30" selected>30 minutes avant</option>
                                                    <option value="60">1 heure avant</option>
                                                    <option value="1440">1 jour avant</option>
                                                    <option value="2880">2 jours avant</option>
                                                    <option value="10080">1 semaine avant</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8 d-flex align-items-end">
                                            <div class="form-group w-100">
                                                <div class="form-text">Les participants recevront un email de rappel à l'heure spécifiée avant la réunion.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="record_meeting" name="record_meeting">
                                    <label class="form-check-label" for="record_meeting">Enregistrer automatiquement la réunion (si supporté)</label>
                                </div>
                                
                                <div class="form-check form-switch mt-3">
                                    <input class="form-check-input" type="checkbox" id="require_confirmation" name="require_confirmation" checked>
                                    <label class="form-check-label" for="require_confirmation">Demander une confirmation de participation</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back()">Annuler</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-2"></i> Créer la réunion
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'aide pour les participants -->
<div class="modal fade" id="participantHelpModal" tabindex="-1" aria-labelledby="participantHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="participantHelpModalLabel">Aide pour la sélection des participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Employés</h6>
                        <p>Sélectionnez des employés individuels dans la liste déroulante. Vous pouvez effectuer une recherche par nom, prénom ou poste.</p>
                        
                        <h6 class="mt-4">Départements</h6>
                        <p>Sélectionnez des départements entiers. Tous les membres des départements sélectionnés seront invités à la réunion.</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Invitations externes</h6>
                        <p>Ajoutez les adresses email des participants externes, séparées par des virgules. Exemple :</p>
                        <pre class="bg-light p-2 rounded">contact@client.com, partenaire@entreprise.com</pre>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i> Les participants externes recevront un email d'invitation avec les détails de la réunion.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #ced4da;
    }
    .select2-container .select2-selection--single {
        height: 38px;
    }
    .file-upload {
        border: 2px dashed #dee2e6;
        border-radius: 6px;
        padding: 2rem;
        text-align: center;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }
    .file-upload:hover {
        border-color: #0d6efd;
        background-color: #f1f7ff;
    }
    .file-upload-icon {
        font-size: 3rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .file-upload-area {
        cursor: pointer;
    }
    .file-upload-input {
        display: none;
    }
    .file-item {
        display: flex;
        align-items: center;
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 0.5rem;
    }
    .file-item i {
        margin-right: 0.5rem;
        color: #6c757d;
    }
    .file-item .file-name {
        flex-grow: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .file-item .file-size {
        color: #6c757d;
        font-size: 0.875rem;
        margin-left: 1rem;
    }
    .file-item .remove-file {
        color: #dc3545;
        cursor: pointer;
        margin-left: 1rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialisation de Select2
        $('.select2').select2({
            placeholder: 'Sélectionner...',
            allowClear: true,
            width: '100%'
        });
        
        // Gestion de l'affichage des options de rappel
        $('#send_reminder').change(function() {
            if($(this).is(':checked')) {
                $('#reminderOptions').slideDown();
            } else {
                $('#reminderOptions').slideUp();
            }
        });
        
        // Initialisation de l'état des options de rappel
        if(!$('#send_reminder').is(':checked')) {
            $('#reminderOptions').hide();
        }
        
        // Gestion du téléchargement des fichiers
        $('.file-upload-area').click(function() {
            $('#attachments').click();
        });
        
        // Affichage des fichiers sélectionnés
        $('#attachments').change(function() {
            const files = this.files;
            const fileList = $('#fileList');
            fileList.empty();
            
            if (files.length > 0) {
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' Mo';
                    const fileIcon = getFileIcon(file.name);
                    
                    const fileItem = $(
                        '<div class="file-item">' +
                        '   <i class="' + fileIcon + '"></i>' +
                        '   <span class="file-name" title="' + file.name + '">' + file.name + '</span>' +
                        '   <span class="file-size">' + fileSize + '</span>' +
                        '   <span class="remove-file" data-index="' + i + '"><i class="fas fa-times"></i></span>' +
                        '</div>'
                    );
                    
                    fileList.append(fileItem);
                }
            }
        });
        
        // Suppression d'un fichier
        $(document).on('click', '.remove-file', function() {
            const index = $(this).data('index');
            const dt = new DataTransfer();
            const input = document.getElementById('attachments');
            const { files } = input;
            
            for (let i = 0; i < files.length; i++) {
                if (index !== i) {
                    dt.items.add(files[i]);
                }
            }
            
            input.files = dt.files;
            $(this).closest('.file-item').remove();
            
            // Si plus de fichiers, masquer la liste
            if (input.files.length === 0) {
                $('#fileList').empty();
            }
        });
        
        // Fonction pour obtenir l'icône en fonction du type de fichier
        function getFileIcon(filename) {
            const ext = filename.split('.').pop().toLowerCase();
            const icons = {
                'pdf': 'far fa-file-pdf text-danger',
                'doc': 'far fa-file-word text-primary',
                'docx': 'far fa-file-word text-primary',
                'xls': 'far fa-file-excel text-success',
                'xlsx': 'far fa-file-excel text-success',
                'ppt': 'far fa-file-powerpoint text-warning',
                'pptx': 'far fa-file-powerpoint text-warning',
                'jpg': 'far fa-file-image text-info',
                'jpeg': 'far fa-file-image text-info',
                'png': 'far fa-file-image text-info',
                'gif': 'far fa-file-image text-info',
                'zip': 'far fa-file-archive text-secondary',
                'rar': 'far fa-file-archive text-secondary',
                'txt': 'far fa-file-alt text-secondary',
                'default': 'far fa-file text-secondary'
            };
            
            return icons[ext] || icons['default'];
        }
        
        // Validation du formulaire
        $('#meetingForm').on('submit', function(e) {
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($('#end_date').val());
            
            if (startDate >= endDate) {
                e.preventDefault();
                alert('La date de fin doit être postérieure à la date de début.');
                return false;
            }
            
            // Vérification des participants
            const employeeParticipants = $('#employee_participants').val() || [];
            const departmentParticipants = $('#department_participants').val() || [];
            const externalParticipants = $('#external_participants').val().trim();
            
            if (employeeParticipants.length === 0 && departmentParticipants.length === 0 && externalParticipants === '') {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un participant ou ajouter un invité externe.');
                return false;
            }
            
            return true;
        });
        
        // Initialisation de la date et heure
        const now = new Date();
        const startDate = now.toISOString().slice(0, 16);
        const endDate = new Date(now.getTime() + 60 * 60 * 1000).toISOString().slice(0, 16);
        
        $('#start_date').val(startDate);
        $('#end_date').val(endDate);
    });
</script>
@endpush