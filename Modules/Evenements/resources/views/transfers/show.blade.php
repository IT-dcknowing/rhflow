@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Détails du Transfert</h4>
                    <p class="text-muted mb-0">Détails du transfert</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small> 
                </div>
                <div>
                    <a href="{{ route('company.evenements.transfers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                 <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i> Détails du Transfert
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Informations de base</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%;">Employé</th>
                                    <td>{{ $transfer->employee->full_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Type de transfert</th>
                                    <td>
                                        @if($transfer->transfer_type === 'department')
                                            <span class="badge badge-info">Changement de département</span>
                                        @elseif($transfer->transfer_type === 'location')
                                            <span class="badge badge-info">Changement de localisation</span>
                                        @elseif($transfer->transfer_type === 'position')
                                            <span class="badge badge-info">Changement de poste</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date du transfert</th>
                                    <td>{{ $transfer->transfer_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Date d'effet</th>
                                    <td>{{ $transfer->effective_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        @if($transfer->status === 'pending')
                                            <span class="badge badge-warning">En attente</span>
                                        @elseif($transfer->status === 'approved')
                                            <span class="badge badge-success">Approuvé</span>
                                        @elseif($transfer->status === 'rejected')
                                            <span class="badge badge-danger">Rejeté</span>
                                        @elseif($transfer->status === 'completed')
                                            <span class="badge badge-info">Terminé</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            
                            <h4 class="mt-4">Détails du transfert</h4>
                            <table class="table table-bordered">
                                @if($transfer->transfer_type === 'department')
                                    <tr>
                                        <th style="width: 40%;">Département d'origine</th>
                                        <td>{{ $transfer->fromDepartment->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nouveau département</th>
                                        <td>{{ $transfer->toDepartment->name ?? 'N/A' }}</td>
                                    </tr>
                                @elseif($transfer->transfer_type === 'location')
                                    <tr>
                                        <th style="width: 40%;">Localisation d'origine</th>
                                        <td>{{ $transfer->fromLocation->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nouvelle localisation</th>
                                        <td>{{ $transfer->toLocation->name ?? 'N/A' }}</td>
                                    </tr>
                                @elseif($transfer->transfer_type === 'position')
                                    <tr>
                                        <th style="width: 40%;">Poste actuel</th>
                                        <td>{{ $transfer->from_position ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nouveau poste</th>
                                        <td>{{ $transfer->to_position ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Informations complémentaires</h4>
                            <div class="card">
                                <div class="card-header">
                                    <h5>Raison du transfert</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $transfer->reason ?? 'Aucune raison spécifiée' }}</p>
                                </div>
                            </div>
                            
                            @if($transfer->notes)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5>Notes supplémentaires</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $transfer->notes }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($transfer->reject_reason)
                                <div class="card mt-3 border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">Raison du rejet</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-danger">{{ $transfer->reject_reason }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Historique</h5>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-calendar-plus text-primary mr-2"></i>
                                                <span>Créé le {{ $transfer->created_at->format('d/m/Y à H:i') }}</span>
                                            </div>
                                            <small class="text-muted">Par {{ $transfer->creator->name ?? 'Système' }}</small>
                                        </li>
                                        @if($transfer->status !== 'pending')
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    @if($transfer->status === 'approved')
                                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                                        <span>Approuvé le {{ $transfer->updated_at->format('d/m/Y à H:i') }}</span>
                                                    @elseif($transfer->status === 'rejected')
                                                        <i class="fas fa-times-circle text-danger mr-2"></i>
                                                        <span>Rejeté le {{ $transfer->updated_at->format('d/m/Y à H:i') }}</span>
                                                    @elseif($transfer->status === 'completed')
                                                        <i class="fas fa-check-double text-info mr-2"></i>
                                                        <span>Terminé le {{ $transfer->updated_at->format('d/m/Y à H:i') }}</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">Par {{ $transfer->updater->name ?? 'Système' }}</small>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Documents associés</h5>
                                </div>
                                <div class="card-body">
                                    @if($transfer->documents && $transfer->documents->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom du document</th>
                                                        <th>Type</th>
                                                        <th>Taille</th>
                                                        <th>Date d'ajout</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($transfer->documents as $document)
                                                        <tr>
                                                            <td>{{ $document->original_filename }}</td>
                                                            <td>{{ strtoupper($document->extension) }}</td>
                                                            <td>{{ number_format($document->size / 1024, 2) }} Ko</td>
                                                            <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>
                                                                <a href="{{ route('company.evenements.transfers.document.download', $document->id) }}" class="btn btn-sm btn-primary" title="Télécharger">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                                @if(auth()->user()->can('delete documents'))
                                                                    <button type="button" class="btn btn-sm btn-danger delete-document" data-id="{{ $document->id }}" title="Supprimer">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle"></i> Aucun document n'a été ajouté à ce transfert.
                                        </div>
                                    @endif
                                    
                                    @if($transfer->status !== 'completed' && $transfer->status !== 'rejected')
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#uploadDocumentModal">
                                                <i class="fas fa-upload"></i> Ajouter un document
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            @if($transfer->status === 'pending')
                                <form action="{{ route('company.evenements.transfers.approve', $transfer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir approuver ce transfert ?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Approuver
                                    </button>
                                </form>
                                
                                <button type="button" class="btn btn-danger ml-2" data-toggle="modal" data-target="#rejectModal">
                                    <i class="fas fa-times"></i> Rejeter
                                </button>
                            @endif
                            
                            @if($transfer->status === 'approved' && $transfer->transfer_date <= now())
                                <form action="{{ route('company.evenements.transfers.complete', $transfer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir marquer ce transfert comme terminé ? Cette action est irréversible.')">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-check-double"></i> Marquer comme terminé
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div>
                            <a href="{{ route('company.evenements.transfers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour à la liste
                            </a>
                            
                            @if($transfer->status !== 'completed' && $transfer->status !== 'rejected')
                                <a href="{{ route('company.evenements.transfers.edit', $transfer->id) }}" class="btn btn-primary ml-2">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                
                                <button type="button" class="btn btn-danger ml-2" data-toggle="modal" data-target="#deleteModal">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            @endif
                            
                            <a href="{{ route('company.evenements.transfers.print', $transfer->id) }}" class="btn btn-secondary ml-2" target="_blank">
                                <i class="fas fa-print"></i> Imprimer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('company.evenements.transfers.reject', $transfer->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Rejeter le transfert</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_reason">Veuillez indiquer la raison du rejet <span class="text-danger">*</span></label>
                        <textarea name="reject_reason" id="reject_reason" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('company.evenements.transfers.destroy', $transfer->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce transfert ? Cette action est irréversible.</p>
                    <p class="mb-0"><strong>Tous les documents associés seront également supprimés.</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer définitivement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'ajout de document -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('company.evenements.transfers.document.upload', $transfer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Ajouter un document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="document">Sélectionner un fichier <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="document" name="document" required>
                            <label class="custom-file-label" for="document">Choisir un fichier</label>
                        </div>
                        <small class="form-text text-muted">Formats acceptés : PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (max: 5 Mo)</small>
                    </div>
                    <div class="form-group">
                        <label for="document_notes">Notes (optionnel)</label>
                        <textarea name="document_notes" id="document_notes" class="form-control" rows="2" placeholder="Ajoutez des notes sur ce document"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Téléverser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression de document -->
<div class="modal fade" id="deleteDocumentModal" tabindex="-1" role="dialog" aria-labelledby="deleteDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="deleteDocumentForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteDocumentModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce document ? Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Afficher le nom du fichier sélectionné dans l'input file
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Gestion de la suppression d'un document
    $('.delete-document').on('click', function() {
        const documentId = $(this).data('id');
        const url = '{{ route("company.evenements.transfers.document.destroy", "") }}/' + documentId;
        $('#deleteDocumentForm').attr('action', url);
        $('#deleteDocumentModal').modal('show');
    });
    
    // Validation du formulaire de rejet
    $('#rejectForm').validate({
        rules: {
            reject_reason: {
                required: true,
                minlength: 10
            }
        },
        messages: {
            reject_reason: {
                required: 'Veuillez indiquer la raison du rejet',
                minlength: 'La raison doit contenir au moins 10 caractères'
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
    
    // Gestion de la soumission du formulaire de suppression de document
    $('#deleteDocumentForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const url = form.attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#deleteDocumentModal').modal('hide');
                    showToast('success', 'Document supprimé', 'Le document a été supprimé avec succès.');
                    // Recharger la page pour mettre à jour la liste des documents
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('error', 'Erreur', 'Une erreur est survenue lors de la suppression du document.');
                }
            },
            error: function(xhr) {
                showToast('error', 'Erreur', xhr.responseJSON?.message || 'Une erreur est survenue lors de la suppression du document.');
            }
        });
    });
    
    // Fonction utilitaire pour afficher des notifications toast
    function showToast(type, title, message) {
        const toast = `
            <div class="toast toast-${type} align-items-center text-white bg-${type === 'error' ? 'danger' : 'success'} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>${message}
                    </div>
                    <button type="button" class="mr-2 mb-1 close text-white" data-dismiss="toast" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        `;
        
        $('.toast-container').append(toast);
        $('.toast').toast('show');
        
        // Supprimer le toast du DOM après sa disparition
        $('.toast').on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
});
</script>
@endpush

@push('styles')
<style>
    /* Styles pour les badges de statut */
    .badge {
        font-size: 0.875em;
        padding: 0.4em 0.6em;
    }
    
    /* Styles pour les cartes */
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        padding: 0.75rem 1.25rem;
    }
    
    .card-header h3, .card-header h4, .card-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    /* Styles pour les tableaux */
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    /* Styles pour les boutons d'action */
    .btn-sm i {
        font-size: 0.8rem;
    }
    
    /* Styles pour les onglets */
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: 600;
    }
    
    /* Styles pour les icônes dans les boutons */
    .btn i {
        margin-right: 0.25rem;
    }
    
    /* Styles pour les modaux */
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .modal-footer {
        border-top: 1px solid #dee2e6;
    }
    
    /* Styles pour les notifications toast */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    
    .toast {
        margin-bottom: 10px;
    }
    
    /* Styles pour les champs obligatoires */
    .required:after {
        content: ' *';
        color: #dc3545;
    }
    
    /* Styles pour les listes d'historique */
    .list-group-item {
        padding: 0.75rem 1.25rem;
    }
    
    /* Styles pour les messages d'alerte */
    .alert {
        border: 1px solid transparent;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
        padding: 0.75rem 1.25rem;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
</style>
@endpush
