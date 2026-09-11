@extends('layouts.app')

@section('title', 'Détails de la Promotion - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📋 Détails de la Promotion</h4>
                    <p class="text-muted mb-0">Informations complètes sur la promotion</p>
                </div>
                <div>
                    <div class="btn-group">
                        <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                        @can('update', $promotion)
                        <a href="{{ route('promotions.edit', $promotion->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                        @endcan
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#historyModal">
                                    <i class="fas fa-history me-2"></i>Historique des modifications
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            @can('delete', $promotion)
                            <li>
                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne de gauche -->
        <div class="col-md-8">
            <!-- Carte des détails de la promotion -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informations générales</h5>
                    <span class="badge bg-label-{{ $promotion->status_color }} fs-6">
                        <i class="fas fa-{{ $promotion->status_icon }} me-1"></i> {{ $promotion->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Type de promotion</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2" style="background-color: {{ $promotion->type_color }}; color: white;">
                                        <span class="avatar-initial rounded"><i class="fas fa-{{ $promotion->type_icon }}"></i></span>
                                    </div>
                                    <h6 class="mb-0">{{ $promotion->type_label }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Date de la promotion</label>
                                <p class="mb-0">{{ $promotion->promotion_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3">Détails de la promotion</h6>
                            <ul class="timeline">
                                <li class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-primary">
                                        <i class="fas fa-user-tie"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0">Poste</h6>
                                            <span class="badge bg-label-info">Modification</span>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-muted text-nowrap me-3">
                                                <i class="fas fa-arrow-right text-success"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0"><s class="text-muted">{{ $promotion->previous_position }}</s></p>
                                                <p class="mb-0 fw-bold">{{ $promotion->new_position }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-success">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0">Département</h6>
                                            <span class="badge bg-label-info">Modification</span>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-muted text-nowrap me-3">
                                                <i class="fas fa-arrow-right text-success"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0"><s class="text-muted">{{ $promotion->previous_department->name ?? 'Non spécifié' }}</s></p>
                                                <p class="mb-0 fw-bold">{{ $promotion->department->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-warning">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0">Rémunération</h6>
                                            <span class="badge bg-label-success">+{{ $promotion->salary_increase_percentage }}%</span>
                                        </div>
                                        <div class="d-flex">
                                            <div class="text-muted text-nowrap me-3">
                                                <i class="fas fa-arrow-right text-success"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0"><s class="text-muted">{{ number_format($promotion->previous_salary, 2, ',', ' ') }} {{ $promotion->employee->currency ?? 'MAD' }}</s></p>
                                                <p class="mb-0 fw-bold">{{ number_format($promotion->new_salary, 2, ',', ' ') }} {{ $promotion->employee->currency ?? 'MAD' }}</p>
                                                <p class="text-success small mb-0">
                                                    <i class="fas fa-caret-up me-1"></i>
                                                    {{ number_format($promotion->new_salary - $promotion->previous_salary, 2, ',', ' ') }} {{ $promotion->employee->currency ?? 'MAD' }} d'augmentation
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @if($promotion->effective_date)
                                <li class="timeline-item">
                                    <span class="timeline-indicator timeline-indicator-info">
                                        <i class="fas fa-calendar-check"></i>
                                    </span>
                                    <div class="timeline-event">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0">Date d'effet</h6>
                                            <span class="badge bg-label-success">Appliquée</span>
                                        </div>
                                        <p class="mb-0">
                                            <i class="far fa-calendar-alt me-2"></i>
                                            {{ $promotion->effective_date->format('d/m/Y') }}
                                            <span class="text-muted ms-2">({{ $promotion->effective_date->diffForHumans() }})</span>
                                        </p>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    @if($promotion->reason)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3">Raison de la promotion</h6>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    {!! nl2br(e($promotion->reason)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($promotion->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="mb-3">Notes internes</h6>
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    {!! nl2br(e($promotion->notes)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pièces jointes -->
            @if($promotion->attachments->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Pièces jointes</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($promotion->attachments as $attachment)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <span class="avatar-initial rounded bg-label-{{ $attachment->file_icon_color }}">
                                                <i class="fas fa-{{ $attachment->file_icon }}"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 text-truncate" style="max-width: 200px;" data-bs-toggle="tooltip" title="{{ $attachment->original_filename }}">
                                                {{ $attachment->original_filename }}
                                            </h6>
                                            <small class="text-muted">{{ $attachment->file_size_formatted }}</small>
                                        </div>
                                        <div class="dropdown
                                            <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('promotions.download', $attachment->id) }}">
                                                        <i class="fas fa-download me-2"></i>Télécharger
                                                    </a>
                                                </li>
                                                @can('delete', $promotion)
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" 
                                                       data-bs-toggle="modal" data-bs-target="#deleteAttachmentModal"
                                                       data-id="{{ $attachment->id }}" data-filename="{{ $attachment->original_filename }}">
                                                        <i class="fas fa-trash me-2"></i>Supprimer
                                                    </a>
                                                </li>
                                                @endcan
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne de droite -->
        <div class="col-md-4">
            <!-- Carte de l'employé -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Employé concerné</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="{{ $promotion->employee->avatar_url ?? asset('images/avatars/default-avatar.png') }}" 
                             alt="Avatar" class="rounded-circle" width="100" height="100">
                    </div>
                    <h5 class="mb-1">{{ $promotion->employee->full_name }}</h5>
                    <p class="text-muted mb-2">{{ $promotion->employee->matricule }}</p>
                    <span class="badge bg-label-{{ $promotion->employee->status_color }}">
                        {{ $promotion->employee->status_label }}
                    </span>
                    
                    <div class="mt-4 text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Département</span>
                            <span class="fw-medium">{{ $promotion->employee->department->name ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Poste actuel</span>
                            <span class="fw-medium">{{ $promotion->employee->position ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Date d'embauche</span>
                            <span class="fw-medium">{{ $promotion->employee->hire_date ? $promotion->employee->hire_date->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Ancienneté</span>
                            <span class="fw-medium">
                                @if($promotion->employee->hire_date)
                                    {{ $promotion->employee->hire_date->diffForHumans(null, true) }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('employees.show', $promotion->employee_id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user me-1"></i> Voir le profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Détails de la promotion -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails</h5>
                    <span class="badge bg-label-{{ $promotion->status_color }}">{{ $promotion->status_label }}</span>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Type</span>
                                <span class="fw-medium">{{ $promotion->type_label }}</span>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Date de la promotion</span>
                                <span class="fw-medium">{{ $promotion->promotion_date->format('d/m/Y') }}</span>
                            </div>
                        </li>
                        @if($promotion->effective_date)
                        <li class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Date d'effet</span>
                                <span class="fw-medium">{{ $promotion->effective_date->format('d/m/Y') }}</span>
                            </div>
                        </li>
                        @endif
                        <li class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Statut</span>
                                <span class="badge bg-label-{{ $promotion->status_color }}">{{ $promotion->status_label }}</span>
                            </div>
                        </li>
                        <li class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Créée le</span>
                                <span class="fw-medium">{{ $promotion->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </li>
                        @if($promotion->created_by)
                        <li class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Créée par</span>
                                <span class="fw-medium">{{ $promotion->creator->name ?? 'Système' }}</span>
                            </div>
                        </li>
                        @endif
                        @if($promotion->updated_at != $promotion->created_at)
                        <li>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Dernière mise à jour</span>
                                <span class="fw-medium">{{ $promotion->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Actions rapides -->
            @canany(['update', 'delete'], $promotion)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('update', $promotion)
                            @if($promotion->status == 'pending' || $promotion->status == 'on_hold')
                            <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="fas fa-check-circle me-1"></i> Approuver la promotion
                            </button>
                            @elseif($promotion->status == 'approved' && !$promotion->effective_date)
                            <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#applyPromotionModal">
                                <i class="fas fa-check-double me-1"></i> Appliquer les changements
                            </button>
                            @endif
                            
                            @if($promotion->status == 'pending' || $promotion->status == 'on_hold')
                            <button type="button" class="btn btn-warning mb-2" data-bs-toggle="modal" data-bs-target="#onHoldModal">
                                <i class="fas fa-pause-circle me-1"></i> Mettre en attente
                            </button>
                            @endif
                            
                            @if($promotion->status != 'rejected')
                            <button type="button" class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times-circle me-1"></i> Rejeter la promotion
                            </button>
                            @endif
                            
                            <a href="{{ route('promotions.edit', $promotion->id) }}" class="btn btn-outline-primary mb-2">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                        @endcan
                        
                        @can('delete', $promotion)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt me-1"></i> Supprimer
                        </button>
                        @endcan
                        
                        <a href="#" class="btn btn-outline-secondary" onclick="window.print();">
                            <i class="fas fa-print me-1"></i> Imprimer
                        </a>
                    </div>
                </div>
            </div>
            @endcanany
        </div>
    </div>
</div>

<!-- Modal de suppression -->
@can('delete', $promotion)
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
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
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
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
@endcan

<!-- Modal de suppression de pièce jointe -->
@can('update', $promotion)
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
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
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
@endcan

<!-- Modal d'approbation -->
@can('update', $promotion)
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approuver la promotion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('promotions.approve', $promotion->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Vous êtes sur le point d'approuver cette promotion. Voulez-vous définir une date d'effet ?</p>
                    
                    <div class="mb-3">
                        <label for="effective_date" class="form-label">Date d'effet</label>
                        <input type="date" class="form-control" id="effective_date" name="effective_date" 
                               value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}">
                        <div class="form-text">Si aucune date n'est spécifiée, la date du jour sera utilisée.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="approval_notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="approval_notes" name="approval_notes" rows="3" 
                                  placeholder="Ajoutez des notes concernant cette approbation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Confirmer l'approbation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Modal de mise en attente -->
@can('update', $promotion)
<div class="modal fade" id="onHoldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mettre en attente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('promotions.on-hold', $promotion->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Pourquoi souhaitez-vous mettre cette promotion en attente ?</p>
                    <div class="mb-3">
                        <label for="on_hold_reason" class="form-label">Raison</label>
                        <textarea class="form-control" id="on_hold_reason" name="on_hold_reason" rows="3" 
                                  placeholder="Détaillez la raison de la mise en attente..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-pause-circle me-1"></i> Mettre en attente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Modal de rejet -->
@can('update', $promotion)
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeter la promotion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('promotions.reject', $promotion->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Veuillez indiquer la raison du rejet de cette promotion :</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Raison du rejet <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                  placeholder="Détaillez les raisons du rejet..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times-circle me-1"></i> Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Modal d'application des changements -->
@can('update', $promotion)
<div class="modal fade" id="applyPromotionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appliquer les changements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('promotions.apply', $promotion->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h6 class="alert-heading">Attention !</h6>
                        <p class="mb-0">Vous êtes sur le point d'appliquer les changements de cette promotion. Cette action mettra à jour les informations de l'employé de manière permanente.</p>
                    </div>
                    
                    <p>Les modifications suivantes seront appliquées :</p>
                    <ul>
                        <li>Poste : <strong>{{ $promotion->previous_position }}</strong> → <strong>{{ $promotion->new_position }}</strong></li>
                        <li>Département : <strong>{{ $promotion->previous_department->name ?? 'N/A' }}</strong> → <strong>{{ $promotion->department->name }}</strong></li>
                        <li>Salaire : <strong>{{ number_format($promotion->previous_salary, 2, ',', ' ') }} {{ $promotion->employee->currency ?? 'MAD' }}</strong> → <strong>{{ number_format($promotion->new_salary, 2, ',', ' ') }} {{ $promotion->employee->currency ?? 'MAD' }}</strong> ({{ $promotion->salary_increase_percentage }}%)</li>
                    </ul>
                    
                    <div class="mb-3">
                        <label for="application_notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="application_notes" name="application_notes" rows="3" 
                                  placeholder="Ajoutez des notes concernant l'application de cette promotion..."></textarea>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notify_employee" name="notify_employee" checked>
                        <label class="form-check-label" for="notify_employee">
                            Notifier l'employé par email
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-double me-1"></i> Confirmer l'application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Modal d'historique -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historique des modifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($promotion->history->count() > 0)
                    <div class="timeline">
                        @foreach($promotion->history as $history)
                        <div class="timeline-item">
                            <span class="timeline-indicator timeline-indicator-{{ $history->status_color }}">
                                <i class="fas fa-{{ $history->status_icon }}"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">{{ $history->status_label }}</h6>
                                    <small class="text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                @if($history->user)
                                <p class="mb-1">Par {{ $history->user->name }}</p>
                                @endif
                                @if($history->notes)
                                <div class="alert alert-light p-2 mb-0">
                                    <p class="mb-0">{{ $history->notes }}</p>
                                </div>
                                @endif
                                @if($history->changes->count() > 0)
                                <div class="mt-2">
                                    @foreach($history->changes as $field => $values)
                                    <div class="d-flex mb-1">
                                        <div class="text-muted text-nowrap me-3">
                                            <i class="fas fa-arrow-right text-success"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="text-capitalize">{{ str_replace('_', ' ', $field) }}:</span>
                                            @if(is_array($values))
                                                @if(isset($values['old']) && isset($values['new']))
                                                    <span class="text-muted">{{ $values['old'] ?? 'N/A' }}</span>
                                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                    <span class="fw-bold">{{ $values['new'] ?? 'N/A' }}</span>
                                                @else
                                                    <span class="text-muted">Modifications multiples</span>
                                                @endif
                                            @else
                                                <span class="fw-bold">{{ $values }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="avatar avatar-lg mb-3">
                            <span class="avatar-initial rounded-circle bg-label-secondary">
                                <i class="fas fa-history"></i>
                            </span>
                        </div>
                        <h5>Aucun historique disponible</h5>
                        <p class="text-muted">Aucune modification n'a été enregistrée pour cette promotion.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
        margin: 0 0 0 1rem;
        border-left: 1px solid #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-indicator {
        position: absolute;
        left: -1.5rem;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transform: translateX(-50%);
    }
    
    .timeline-indicator-primary {
        background-color: #696cff;
    }
    
    .timeline-indicator-success {
        background-color: #71dd37;
    }
    
    .timeline-indicator-warning {
        background-color: #ffab00;
    }
    
    .timeline-indicator-danger {
        background-color: #ff3e1d;
    }
    
    .timeline-indicator-info {
        background-color: #03c3ec;
    }
    
    .timeline-indicator-secondary {
        background-color: #8592a3;
    }
    
    .timeline-event {
        background-color: #fff;
        border-radius: 0.375rem;
        padding: 0.5rem 1rem;
    }
    
    .timeline-event h6 {
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .timeline-event p {
        font-size: 0.8125rem;
        margin-bottom: 0.25rem;
    }
    
    .timeline-event .alert {
        margin-bottom: 0.5rem;
    }
    
    .file-icon {
        font-size: 1.5rem;
        margin-right: 0.5rem;
    }
    
    .file-info {
        flex: 1;
    }
    
    .file-actions {
        margin-left: 0.5rem;
    }
    
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        body, html {
            background: white !important;
            font-size: 12px !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .container-xxl {
            max-width: 100% !important;
            padding: 0 !important;
        }
    }
</style>
@endpush

@push('scripts')
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
                
                var attachmentNameElement = deleteAttachmentModal.querySelector('#attachmentName');
                var form = document.getElementById('deleteAttachmentForm');
                
                attachmentNameElement.textContent = attachmentName;
                form.action = "{{ route('promotions.attachments.destroy', '') }}/" + attachmentId;
            });
        }
        
        // Initialisation de la date d'effet par défaut à aujourd'hui
        var effectiveDateInput = document.getElementById('effective_date');
        if (effectiveDateInput) {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            
            effectiveDateInput.min = yyyy + '-' + mm + '-' + dd;
        }
    });
    
    // Fonction pour confirmer la suppression
    function confirmDelete() {
        return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ? Cette action est irréversible.');
    }
    
    // Fonction pour confirmer l'application des changements
    function confirmApplyChanges() {
        return confirm('Êtes-vous sûr de vouloir appliquer les changements de cette promotion ? Cette action mettra à jour les informations de l\'employé de manière permanente.');
    }
</script>
@endpush
@endsection