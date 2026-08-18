@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Modifier la réunion</h4>
                    <div>
                        <a href="{{ route('company.meetings.show', $meeting->id) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-eye me-1"></i> Voir
                        </a>
                        <a href="{{ route('company.meetings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('company.meetings.update', $meeting->id) }}" method="POST" id="meetingForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informations de base -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="mb-3">Informations de la réunion</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Titre de la réunion <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $meeting->title) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="meeting_type">Type de réunion <span class="text-danger">*</span></label>
                                            <select class="form-select" id="meeting_type" name="meeting_type" required>
                                                <option value="">Sélectionner un type</option>
                                                <option value="team" {{ old('meeting_type', $meeting->meeting_type) == 'team' ? 'selected' : '' }}>Équipe</option>
                                                <option value="project" {{ old('meeting_type', $meeting->meeting_type) == 'project' ? 'selected' : '' }}>Projet</option>
                                                <option value="client" {{ old('meeting_type', $meeting->meeting_type) == 'client' ? 'selected' : '' }}>Client</option>
                                                <option value="other" {{ old('meeting_type', $meeting->meeting_type) == 'other' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date">Date et heure de début <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                                value="{{ old('start_date', \Carbon\Carbon::parse($meeting->start_date)->format('Y-m-d\TH:i')) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date">Date et heure de fin <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                                value="{{ old('end_date', \Carbon\Carbon::parse($meeting->end_date)->format('Y-m-d\TH:i')) }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="location">Lieu ou lien de la réunion <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="location" name="location" 
                                                value="{{ old('location', $meeting->location) }}" required>
                                            <small class="form-text text-muted">Indiquez une salle ou un lien de visioconférence</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="description">Ordre du jour <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $meeting->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($meeting->status === 'completed')
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="meeting_notes">Compte-rendu</label>
                                            <textarea class="form-control" id="meeting_notes" name="meeting_notes" rows="4">{{ old('meeting_notes', $meeting->meeting_notes) }}</textarea>
                                            <small class="form-text text-muted">Rédigez le compte-rendu de la réunion</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Participants -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Participants</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#participantHelpModal">
                                        <i class="fas fa-question-circle me-1"></i> Aide
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Employés</label>
                                            <select class="form-select select2" id="employee_participants" name="employee_participants[]" multiple>
                                                @foreach($employees as $employee)
                                                    <option value="{{ $employee->id }}" 
                                                        {{ in_array($employee->id, old('employee_participants', $meeting->participants->where('participant_type', 'App\\Models\\Employee')->pluck('participant_id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $employee->full_name }} ({{ $employee->department->name ?? 'Sans département' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Départements</label>
                                            <select class="form-select select2" id="department_participants" name="department_participants[]" multiple>
                                                @foreach($departments as $department)
                                                    <option value="{{ $department->id }}"
                                                        {{ in_array($department->id, old('department_participants', $meeting->departments->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $department->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Invitations externes (emails séparés par des virgules)</label>
                                            <textarea class="form-control" id="external_participants" name="external_participants" 
                                                rows="2" placeholder="exemple1@domaine.com, exemple2@domaine.com">{{ old('external_participants', $meeting->external_participants) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($meeting->participants->isNotEmpty())
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">Statut des participants</h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-hover mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Nom</th>
                                                                <th>Email</th>
                                                                <th>Type</th>
                                                                <th>Statut</th>
                                                                <th>Réponse le</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($meeting->participants as $participant)
                                                                <tr>
                                                                    <td>{{ $participant->participant->full_name ?? $participant->name }}</td>
                                                                    <td>{{ $participant->participant->email ?? $participant->email }}</td>
                                                                    <td>
                                                                        @if($participant->participant_type === 'App\\Models\\Employee')
                                                                            <span class="badge bg-primary">Interne</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">Externe</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($participant->status === 'accepted')
                                                                            <span class="badge bg-success">Accepté</span>
                                                                        @elseif($participant->status === 'declined')
                                                                            <span class="badge bg-danger">Refusé</span>
                                                                        @elseif($participant->status === 'tentative')
                                                                            <span class="badge bg-warning">Peut-être</span>
                                                                        @else
                                                                            <span class="badge bg-secondary">En attente</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($participant->responded_at)
                                                                            {{ \Carbon\Carbon::parse($participant->responded_at)->format('d/m/Y H:i') }}
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Pièces jointes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Pièces jointes</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="refreshAttachments">
                                        <i class="fas fa-sync-alt me-1"></i> Actualiser
                                    </button>
                                </div>
                                
                                @if($meeting->attachments->isNotEmpty())
                                <div class="table-responsive mb-4">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nom du fichier</th>
                                                <th>Taille</th>
                                                <th>Ajouté par</th>
                                                <th>Date d'ajout</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($meeting->attachments as $attachment)
                                            <tr>
                                                <td>
                                                    <i class="{{ getFileIconClass($attachment->filename) }} me-2"></i>
                                                    {{ $attachment->original_filename }}
                                                </td>
                                                <td>{{ formatFileSize($attachment->size) }}</td>
                                                <td>{{ $attachment->uploader->full_name ?? 'Système' }}</td>
                                                <td>{{ $attachment->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('company.meetings.attachments.download', [$meeting->id, $attachment->id]) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="Télécharger">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        @can('delete', $attachment)
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-attachment" 
                                                                data-id="{{ $attachment->id }}" title="Supprimer">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                                
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
                                
                                @if($meeting->status !== 'completed')
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="notify_participants" name="notify_participants" checked>
                                    <label class="form-check-label" for="notify_participants">Notifier les participants des modifications</label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="cancel_meeting" name="cancel_meeting">
                                    <label class="form-check-label text-danger" for="cancel_meeting">Annuler cette réunion</label>
                                </div>
                                
                                <div id="cancelReason" class="ms-4 mb-3" style="display: none;">
                                    <label for="cancellation_reason" class="form-label">Raison de l'annulation <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="2"></textarea>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="notify_on_cancel" name="notify_on_cancel" checked>
                                        <label class="form-check-label" for="notify_on_cancel">Envoyer une notification aux participants</label>
                                    </div>
                                </div>
                                @endif
                                
                                @if($meeting->status === 'scheduled' && $meeting->start_date > now())
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="send_reminder" name="send_reminder" checked>
                                    <label class="form-check-label" for="send_reminder">Envoyer un rappel aux participants</label>
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
                                @endif
                                
                                @if($meeting->status === 'scheduled' && $meeting->start_date <= now() && $meeting->end_date >= now())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Cette réunion est en cours en ce moment.
                                </div>
                                @elseif($meeting->status === 'scheduled' && $meeting->end_date < now())
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Cette réunion est terminée. Souhaitez-vous la marquer comme complétée ?
                                </div>
                                @endif
                                
                                @if($meeting->status === 'completed')
                                <div class="form-group">
                                    <label>Statut</label>
                                    <div>
                                        <span class="badge bg-success">Terminée</span>
                                        <span class="text-muted ms-2">Le {{ $meeting->completed_at->format('d/m/Y à H:i') }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                @if($meeting->status === 'scheduled' && $meeting->start_date <= now() && $meeting->end_date >= now())
                                <a href="{{ route('company.meetings.complete', $meeting->id) }}" class="btn btn-success me-2">
                                    <i class="fas fa-check-circle me-1"></i> Marquer comme terminée
                                </a>
                                @endif
                                
                                @if($meeting->status !== 'cancelled' && $meeting->status !== 'completed')
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                                @endif
                                
                                <a href="{{ route('company.meetings.index') }}" class="btn btn-light">
                                    <i class="fas fa-times me-1"></i> Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression de pièce jointe -->
<div class="modal fade" id="deleteAttachmentModal" tabindex="-1" aria-labelledby="deleteAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAttachmentModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette pièce jointe ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <form id="deleteAttachmentForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
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
    .participant-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 0.5rem;
    }
    .participant-name {
        display: inline-flex;
        align-items: center;
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
            width: '100%',
            templateResult: formatParticipant,
            templateSelection: formatParticipantSelection
        });
        
        // Fonction pour formater l'affichage des participants dans Select2
        function formatParticipant(participant) {
            if (!participant.id) {
                return participant.text;
            }
            
            const $participant = $(
                '<div class="d-flex align-items-center">' +
                '   <div class="participant-name">' + participant.text + '</div>' +
                '</div>'
            );
            
            return $participant;
        }
        
        // Fonction pour formater la sélection dans Select2
        function formatParticipantSelection(participant) {
            return participant.text;
        }
        
        // Gestion de l'affichage des options d'annulation
        $('#cancel_meeting').change(function() {
            if($(this).is(':checked')) {
                $('#cancelReason').slideDown();
            } else {
                $('#cancelReason').slideUp();
            }
        });
        
        // Initialisation de l'état des options d'annulation
        if(!$('#cancel_meeting').is(':checked')) {
            $('#cancelReason').hide();
        }
        
        // Gestion de l'affichage des options de rappel
        if ($('#send_reminder').length) {
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
        
        // Gestion de la suppression des pièces jointes existantes
        $('.delete-attachment').click(function() {
            const attachmentId = $(this).data('id');
            const formAction = '{{ route("company.meetings.attachments.destroy", ["meeting" => $meeting->id, "attachment" => "ATTACHMENT_ID"]) }}';
            $('#deleteAttachmentForm').attr('action', formAction.replace('ATTACHMENT_ID', attachmentId));
            $('#deleteAttachmentModal').modal('show');
        });
        
        // Rafraîchissement de la liste des pièces jointes
        $('#refreshAttachments').click(function() {
            window.location.reload();
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
            
            // Vérification de la raison d'annulation si nécessaire
            if ($('#cancel_meeting').is(':checked') && $('#cancellation_reason').val().trim() === '') {
                e.preventDefault();
                alert('Veuillez indiquer la raison de l\'annulation.');
                return false;
            }
            
            return true;
        });
    });
</script>
@endpush