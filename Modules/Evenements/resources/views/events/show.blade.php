@extends('layouts.app')

@section('title', $event->title)

@push('styles')
<style>
    .event-header {
        border-left: 5px solid {{ $event->type->color ?? '#3b82f6' }};
        padding-left: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .event-details-card {
        border-left: 3px solid {{ $event->type->color ?? '#3b82f6' }};
    }
    .participant-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1rem;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 1rem;
    }
    .status-pending {
        background-color: #e9ecef;
        color: #495057;
    }
    .status-accepted {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-declined {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .status-tentative {
        background-color: #fef3c7;
        color: #92400e;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">{{ $event->title }}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.evenements.events.index') }}">Événements</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $event->title }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @if(auth()->check())
                        <a href="{{ route('company.evenements.events.edit', $event) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>
                    @endif
                    <a href="{{ route('company.evenements.events.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <!-- Détails de l'événement -->
                    <div class="card event-details-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: {{ $event->type->color ?? '#3b82f6' }}; border-radius: 8px; color: white;">
                                        <i class="fas {{ $event->type->icon ?? 'fa-calendar' }} fa-2x"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h4 class="mb-1">{{ $event->title }}</h4>
                                            <p class="mb-1">
                                                <i class="far fa-calendar-alt me-2"></i>
                                                @if($event->all_day)
                                                    {{ $event->start_date->format('d/m/Y') }}
                                                    @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                                        au {{ $event->end_date->format('d/m/Y') }}
                                                    @endif
                                                    (Toute la journée)
                                                @else
                                                    {{ $event->start_date->format('d/m/Y H:i') }}
                                                    @if($event->end_date)
                                                        au {{ $event->end_date->format('d/m/Y H:i') }}
                                                        <span class="text-muted">({{ $event->duration }})</span>
                                                    @endif
                                                @endif
                                            </p>
                                            @if($event->location)
                                                <p class="mb-1">
                                                    <i class="fas fa-map-marker-alt me-2"></i>
                                                    {{ $event->location }}
                                                </p>
                                            @endif
                                            @if($event->branch)
                                                <p class="mb-0">
                                                    <i class="fas fa-building me-2"></i>
                                                    {{ $event->branch->name }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                    id="eventActions" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="eventActions">
                                                <li>
                                                    <a class="dropdown-item" href="#" id="addToCalendar">
                                                        <i class="far fa-calendar-plus me-2"></i>Ajouter à mon calendrier
                                                    </a>
                                                </li>
                                                @if($event->google_calendar_event_id || $event->outlook_event_id)
                                                    <li><hr class="dropdown-divider"></li>
                                                    @if($event->google_calendar_event_id)
                                                        <li>
                                                            <a class="dropdown-item" href="#" id="viewInGoogleCalendar">
                                                                <i class="fab fa-google me-2"></i>Voir dans Google Calendar
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($event->outlook_event_id)
                                                        <li>
                                                            <a class="dropdown-item" href="#" id="viewInOutlook">
                                                                <i class="fab fa-microsoft me-2"></i>Voir dans Outlook
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif
                                                @if(auth()->check())
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('company.evenements.events.edit', $event) }}">
                                                            <i class="fas fa-edit me-2"></i>Modifier l'événement
                                                        </a>
                                                    </li>
                                                    @if($event->status !== 'cancelled')
                                                        <li>
                                                            <a class="dropdown-item text-warning" href="#" id="cancelEvent">
                                                                <i class="fas fa-ban me-2"></i>Annuler l'événement
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                                                            <i class="fas fa-trash me-2"></i>Supprimer l'événement
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    @if($event->description)
                                        <div class="mt-4">
                                            <h6>Description</h6>
                                            <p class="mb-0">{!! nl2br(e($event->description)) !!}</p>
                                        </div>
                                    @endif
                                    
                                    @if($event->recurrence_rule)
                                        <div class="mt-3">
                                            <span class="badge bg-info">
                                                <i class="fas fa-sync-alt me-1"></i>
                                                @switch($event->recurrence_rule)
                                                    @case('daily') Tous les jours @break
                                                    @case('weekly') Toutes les semaines @break
                                                    @case('monthly') Tous les mois @break
                                                    @case('yearly') Tous les ans @break
                                                @endswitch
                                                @if($event->recurrence_until)
                                                    jusqu'au {{ $event->recurrence_until->format('d/m/Y') }}
                                                @else
                                                    sans fin
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions rapides -->
                            @if($userParticipation)
                                <div class="mt-4 pt-3 border-top">
                                    <h6>Votre participation</h6>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-success d-flex align-items-center gap-2 {{ $userParticipation->status === 'accepted' ? 'active' : '' }}"
                                                data-status="accepted">
                                            <i class="fas fa-check"></i>
                                            <span>Je participe</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning d-flex align-items-center gap-2 {{ $userParticipation->status === 'tentative' ? 'active' : '' }}"
                                                data-status="tentative">
                                            <i class="fas fa-question"></i>
                                            <span>Peut-être</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger d-flex align-items-center gap-2 {{ $userParticipation->status === 'declined' ? 'active' : '' }}"
                                                data-status="declined">
                                            <i class="fas fa-times"></i>
                                            <span>Je ne participe pas</span>
                                        </button>
                                    </div>
                                    <div class="mt-2" id="participationNotes" style="display: none;">
                                        <textarea class="form-control" rows="2" placeholder="Ajouter un commentaire (optionnel)"></textarea>
                                        <div class="d-flex justify-content-end mt-2">
                                            <button type="button" class="btn btn-sm btn-primary" id="saveParticipation">Enregistrer</button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Participants -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Participants</h5>
                            <span class="badge bg-primary rounded-pill">{{ $event->participants->count() }}</span>
                        </div>
                        <div class="card-body">
                            @if($event->participants->isEmpty())
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p class="mb-0">Aucun participant pour le moment</p>
                                </div>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($event->participants as $participant)
                                        <li class="list-group-item px-0">
                                            <div class="d-flex align-items-center">
                                                <div class="participant-avatar me-3" 
                                                     style="background-color: {{ $participant->participant->color ?? '#6c757d' }}">
                                                    {{ substr($participant->name ?? $participant->participant->name ?? '?', 0, 1) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">
                                                        {{ $participant->name ?? $participant->participant->name ?? 'Participant inconnu' }}
                                                        @if($participant->participant_type === 'App\\Models\\Department')
                                                            <span class="badge bg-secondary ms-2">Département</span>
                                                        @endif
                                                    </h6>
                                                    <small class="text-muted">
                                                        @if($participant->email || ($participant->participant && $participant->participant->email))
                                                            {{ $participant->email ?? $participant->participant->email }}
                                                        @else
                                                            Aucun email
                                                        @endif
                                                    </small>
                                                </div>
                                                <div>
                                                    @if($participant->status === 'accepted')
                                                        <span class="badge status-badge status-accepted">
                                                            <i class="fas fa-check-circle me-1"></i> Accepté
                                                        </span>
                                                    @elseif($participant->status === 'declined')
                                                        <span class="badge status-badge status-declined">
                                                            <i class="fas fa-times-circle me-1"></i> Refusé
                                                        </span>
                                                    @elseif($participant->status === 'tentative')
                                                        <span class="badge status-badge status-tentative">
                                                            <i class="fas fa-question-circle me-1"></i> Peut-être
                                                        </span>
                                                    @else
                                                        <span class="badge status-badge status-pending">
                                                            <i class="far fa-clock me-1"></i> En attente
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($participant->response_notes)
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <small class="text-muted">
                                                        <i class="fas fa-comment-alt me-1"></i>
                                                        {{ $participant->response_notes }}
                                                    </small>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            @if(auth()->check())
                                <div class="mt-3">
                                    <button class="btn btn-primary btn-sm" id="addParticipantsBtn">
                                        <i class="fas fa-user-plus me-1"></i> Ajouter des participants
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Fichiers joints -->
                    @if(($event->media ?? collect())->count() > 0)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Fichiers joints</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    @foreach($event->media as $media)
                                        <a href="{{ $media->getUrl() }}" target="_blank" class="list-group-item list-group-item-action">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if(str_starts_with($media->mime_type, 'image/'))
                                                        <i class="far fa-image fa-2x text-primary"></i>
                                                    @elseif(str_starts_with($media->mime_type, 'application/pdf'))
                                                        <i class="far fa-file-pdf fa-2x text-danger"></i>
                                                    @elseif(in_array($media->mime_type, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']))
                                                        <i class="far fa-file-word fa-2x text-primary"></i>
                                                    @elseif(in_array($media->mime_type, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']))
                                                        <i class="far fa-file-excel fa-2x text-success"></i>
                                                    @elseif(in_array($media->mime_type, ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation']))
                                                        <i class="far fa-file-powerpoint fa-2x text-warning"></i>
                                                    @else
                                                        <i class="far fa-file fa-2x text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ $media->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $media->human_readable_size }} • {{ $media->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                                <div class="ms-3">
                                                    <a href="{{ $media->getUrl() }}" class="btn btn-sm btn-outline-primary" download>
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Commentaires -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Commentaires</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <textarea class="form-control" id="commentText" rows="3" placeholder="Ajouter un commentaire..."></textarea>
                                <div class="d-flex justify-content-end mt-2">
                                    <button class="btn btn-primary" id="postComment">
                                        <i class="fas fa-paper-plane me-1"></i> Envoyer
                                    </button>
                                </div>
                            </div>
                            
                            <div id="commentsList">
                                <div class="text-center text-muted py-4">
                                    <i class="far fa-comments fa-3x mb-3"></i>
                                    <p class="mb-0">Aucun commentaire pour le moment</p>
                                    <small>Soyez le premier à réagir !</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Organisateur -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Organisateur</h5>
                        </div>
                        <div class="card-body">
                        @if($event->creator ?? null)
                            <div class="d-flex align-items-center">
                                <div class="participant-avatar me-3" 
                                     style="background-color: {{ $event->creator->color ?? '#3b82f6' }}">
                                    {{ substr($event->creator->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $event->creator->name ?? 'N/A' }}</h6>
                                    <p class="text-muted mb-0">{{ $event->creator->email ?? '' }}</p>
                                    @if(($event->creator->department ?? null))
                                        <small class="text-muted">{{ $event->creator->department->name }}</small>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0"><i class="fas fa-user-slash me-1"></i>Organisateur non renseigné</p>
                        @endif
                        @if(auth()->check())
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-secondary w-100" id="transferOwnership">
                                    <i class="fas fa-exchange-alt me-1"></i> Transférer l'organisation
                                </button>
                            </div>
                        @endif
                        </div>
                    </div>
                    
                    <!-- Détails supplémentaires -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Détails</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <div class="d-flex">
                                        <div class="me-2 text-muted">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Type</small>
                                            <span>{{ $event->type->name ?? 'Non spécifié' }}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-2">
                                    <div class="d-flex">
                                        <div class="me-2 text-muted">
                                            <i class="fas fa-calendar-plus"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Créé le</small>
                                            <span>{{ $event->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="mb-2">
                                    <div class="d-flex">
                                        <div class="me-2 text-muted">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Dernière modification</small>
                                            <span>{{ $event->updated_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </li>
                                @if($event->send_reminder && $event->reminder_minutes_before)
                                    <li class="mb-2">
                                        <div class="d-flex">
                                            <div class="me-2 text-muted">
                                                <i class="fas fa-bell"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Rappel</small>
                                                <span>{{ $event->reminder_minutes_before }} minutes avant</span>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                                @if($event->google_calendar_event_id || $event->outlook_event_id)
                                    <li class="mb-2">
                                        <div class="d-flex">
                                            <div class="me-2 text-muted">
                                                <i class="fas fa-sync-alt"></i>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Synchronisation</small>
                                                <div class="d-flex mt-1">
                                                    @if($event->google_calendar_event_id)
                                                        <span class="badge bg-danger me-2">
                                                            <i class="fab fa-google me-1"></i> Google
                                                        </span>
                                                    @endif
                                                    @if($event->outlook_event_id)
                                                        <span class="badge bg-primary">
                                                            <i class="fab fa-microsoft me-1"></i> Outlook
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Statistiques de participation -->
                    @if($event->participants->isNotEmpty())
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Statistiques</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Participants confirmés</span>
                                        <span>{{ $event->participants->where('status', 'accepted')->count() }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $event->participants->where('status', 'accepted')->count() / $event->participants->count() * 100 }}%" 
                                             aria-valuenow="{{ $event->participants->where('status', 'accepted')->count() }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="{{ $event->participants->count() }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Peut-être</span>
                                        <span>{{ $event->participants->where('status', 'tentative')->count() }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: {{ $event->participants->where('status', 'tentative')->count() / $event->participants->count() * 100 }}%" 
                                             aria-valuenow="{{ $event->participants->where('status', 'tentative')->count() }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="{{ $event->participants->count() }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Ne participe pas</span>
                                        <span>{{ $event->participants->where('status', 'declined')->count() }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-danger" role="progressbar" 
                                             style="width: {{ $event->participants->where('status', 'declined')->count() / $event->participants->count() * 100 }}%" 
                                             aria-valuenow="{{ $event->participants->where('status', 'declined')->count() }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="{{ $event->participants->count() }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>En attente de réponse</span>
                                        <span>{{ $event->participants->where('status', 'pending')->count() }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-secondary" role="progressbar" 
                                             style="width: {{ $event->participants->where('status', 'pending')->count() / $event->participants->count() * 100 }}%" 
                                             aria-valuenow="{{ $event->participants->where('status', 'pending')->count() }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="{{ $event->participants->count() }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Actions rapides -->
                    <x-quick-actions stacked>
                        <x-quick-action icon="far fa-calendar-plus" label="Exporter vers le calendrier"
                            variant="outline" color="primary" id="exportToCalendar" />

                        <x-quick-action icon="fas fa-bell" label="Envoyer un rappel"
                            variant="outline" id="sendReminder" />

                        @if($event->recurrence_rule)
                            <x-quick-action icon="fas fa-sync-alt" label="Modifier la série"
                                variant="outline" color="warning" id="editSeries" />
                        @endif

                        @if(auth()->check())
                            <x-quick-action icon="fas fa-trash-alt" label="Supprimer l'événement"
                                variant="outline" color="danger"
                                data-bs-toggle="modal" data-bs-target="#deleteEventModal" />
                        @endif
                    </x-quick-actions>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.</p>
                <p class="mb-0"><strong>Titre :</strong> {{ $event->title }}</p>
                <p class="mb-0"><strong>Date :</strong> {{ $event->start_date->format('d/m/Y H:i') }}</p>
                @if($event->recurrence_rule)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cet événement fait partie d'une série récurrente. Souhaitez-vous supprimer uniquement cet événement ou toute la série ?
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="delete_series" id="delete_this_event" value="this" checked>
                        <label class="form-check-label" for="delete_this_event">
                            Supprimer uniquement cet événement
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="delete_series" id="delete_all_events" value="all">
                        <label class="form-check-label" for="delete_all_events">
                            Supprimer tous les événements de la série
                        </label>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('company.evenements.events.destroy', $event) }}" method="POST" id="deleteEventForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="delete_series" value="this">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(auth()->check())
<div class="modal fade" id="addParticipantsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter des participants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="addParticipantsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="add-employees-tab" data-bs-toggle="tab" 
                                data-bs-target="#add-employees" type="button" role="tab">
                            Employés
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="add-departments-tab" data-bs-toggle="tab" 
                                data-bs-target="#add-departments" type="button" role="tab">
                            Départements
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="add-external-tab" data-bs-toggle="tab" 
                                data-bs-target="#add-external" type="button" role="tab">
                            Contacts externes
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="addParticipantsTabContent">
                    <!-- Employés -->
                    <div class="tab-pane fade show active" id="add-employees" role="tabpanel">
                        <select class="form-select select2" id="add_employee_participants" multiple>
                            @isset($employees)
                                @foreach($employees as $department => $deptEmployees)
                                    <optgroup label="{{ $department }}">
                                        @foreach($deptEmployees as $employee)
                                            @if(!$event->participants->contains('participant_id', $employee->id))
                                                <option value="{{ $employee->id }}">
                                                    {{ $employee->full_name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    
                    <!-- Départements -->
                    <div class="tab-pane fade" id="add-departments" role="tabpanel">
                        <select class="form-select select2" id="add_department_participants" multiple>
                            @isset($departments)
                                @foreach($departments as $department)
                                    @if(!$event->participants->contains('participant_id', $department->id))
                                        <option value="{{ $department->id }}">
                                            {{ $department->name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    
                    <!-- Contacts externes -->
                    <div class="tab-pane fade" id="add-external" role="tabpanel">
                        <div id="add_external_participants_container">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="add_external_name" placeholder="Nom">
                                <input type="email" class="form-control" id="add_external_email" placeholder="Email">
                                <button type="button" class="btn btn-primary" id="add_external_participant">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div id="add_external_participants_list"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="send_invitations_now" name="send_invitations_now" value="1" checked>
                    <label class="form-check-label" for="send_invitations_now">
                        Envoyer des invitations par email aux nouveaux participants
                    </label>
                </div>
                
                <div class="form-group mt-3">
                    <label for="invitation_message" class="form-label">Message personnalisé (optionnel)</label>
                    <textarea class="form-control" id="invitation_message" rows="3" 
                              placeholder="Ajoutez un message personnalisé à l'invitation"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveParticipants">
                    <i class="fas fa-user-plus me-1"></i> Ajouter les participants
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@if(auth()->check())
<div class="modal fade" id="transferOwnershipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transférer l'organisation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Vous êtes sur le point de transférer la propriété de cet événement à un autre utilisateur. 
                   Cette action est irréversible et vous ne pourrez plus modifier cet événement sauf si le nouveau propriétaire vous en redonne les droits.</p>
                
                <div class="mb-3">
                    <label for="new_owner" class="form-label">Nouvel organisateur</label>
                    <select class="form-select" id="new_owner">
                        @isset($employees)
                            @foreach($employees->flatten() as $employee)
                                @if($employee->id !== $event->created_by)
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->full_name }} ({{ $employee->email }})
                                        @if($employee->department)
                                            - {{ $employee->department->name }}
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        @endisset
                    </select>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="notify_new_owner" checked>
                    <label class="form-check-label" for="notify_new_owner">
                        Notifier le nouvel organisateur par email
                    </label>
                </div>
                
                <div class="form-group mt-3">
                    <label for="transfer_message" class="form-label">Message (optionnel)</label>
                    <textarea class="form-control" id="transfer_message" rows="3" 
                              placeholder="Expliquez pourquoi vous transférez cet événement"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmTransfer">
                    <i class="fas fa-exchange-alt me-1"></i> Transférer
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal d'annulation d'événement -->
@if(auth()->check())
<div class="modal fade" id="cancelEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Annuler l'événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir annuler cet événement ?</p>
                <p class="mb-0"><strong>Titre :</strong> {{ $event->title }}</p>
                <p class="mb-3"><strong>Date :</strong> {{ $event->start_date->format('d/m/Y H:i') }}</p>
                
                <div class="form-group">
                    <label for="cancellation_reason" class="form-label">Raison de l'annulation (optionnel)</label>
                    <textarea class="form-control" id="cancellation_reason" rows="3" 
                              placeholder="Pourquoi annulez-vous cet événement ?"></textarea>
                </div>
                
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="notify_participants" checked>
                    <label class="form-check-label" for="notify_participants">
                        Notifier les participants par email
                    </label>
                </div>
                
                @if($event->recurrence_rule)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cet événement fait partie d'une série récurrente. Souhaitez-vous annuler uniquement cet événement ou toute la série ?
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_series" id="cancel_this_event" value="this" checked>
                        <label class="form-check-label" for="cancel_this_event">
                            Annuler uniquement cet événement
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="cancel_series" id="cancel_all_events" value="all">
                        <label class="form-check-label" for="cancel_all_events">
                            Annuler tous les événements de la série
                        </label>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-warning" id="confirmCancelEvent">
                    <i class="fas fa-ban me-1"></i> Confirmer l'annulation
                </button>
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation de Select2
        $('.select2').select2({
            placeholder: 'Sélectionner des participants',
            allowClear: true,
            width: '100%',
            closeOnSelect: false
        });
        
        // Gestion de la participation
        const participationButtons = document.querySelectorAll('button[data-status]');
        const participationNotes = document.getElementById('participationNotes');
        const saveParticipationBtn = document.getElementById('saveParticipation');
        let selectedStatus = '';
        
        participationButtons.forEach(button => {
            button.addEventListener('click', function() {
                selectedStatus = this.getAttribute('data-status');
                participationButtons.forEach(btn => {
                    if (btn === this) {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                });
                participationNotes.style.display = 'block';
            });
        });
        
        // Enregistrement de la participation
        saveParticipationBtn.addEventListener('click', function() {
            const notes = document.querySelector('#participationNotes textarea').value;
            
            // Ici, vous devez ajouter le code pour envoyer la mise à jour au serveur
            fetch(`/api/events/{{ $event->id }}/participation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: selectedStatus,
                    notes: notes
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour voir les changements
                    window.location.reload();
                } else {
                    alert('Une erreur est survenue lors de la mise à jour de votre participation.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la mise à jour de votre participation.');
            });
        });
        
        // Gestion de l'ajout de participants
        const addParticipantsBtn = document.getElementById('addParticipantsBtn');
        if (addParticipantsBtn) {
            addParticipantsBtn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('addParticipantsModal'));
                modal.show();
            });
        }
        
        // Gestion du transfert de propriété
        const transferOwnershipBtn = document.getElementById('transferOwnership');
        if (transferOwnershipBtn) {
            transferOwnershipBtn.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('transferOwnershipModal'));
                modal.show();
            });
        }
        
        // Gestion de l'annulation d'événement
        const cancelEventBtn = document.getElementById('cancelEvent');
        if (cancelEventBtn) {
            cancelEventBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const modal = new bootstrap.Modal(document.getElementById('cancelEventModal'));
                modal.show();
            });
        }
        
        // Gestion de la suppression d'événement
        const deleteEventForm = document.getElementById('deleteEventForm');
        if (deleteEventForm) {
            const deleteRadios = document.querySelectorAll('input[name="delete_series"]');
            deleteRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    deleteEventForm.querySelector('input[name="delete_series"]').value = this.value;
                });
            });
        }
        
        // Gestion de l'exportation vers un calendrier
        const exportToCalendarBtn = document.getElementById('exportToCalendar');
        if (exportToCalendarBtn) {
            exportToCalendarBtn.addEventListener('click', function() {
                // Format de date pour iCalendar
                function formatDate(date) {
                    return date.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '').replace('Z', 'Z');
                }
                
                const startDate = new Date('{{ $event->start_date->format('Y-m-d H:i:s') }}');
                const endDate = '{{ $event->end_date ? $event->end_date->format('Y-m-d H:i:s') : '' }}';
                
                // Créer le contenu du fichier iCal
                let icsContent = [
                    'BEGIN:VCALENDAR',
                    'VERSION:2.0',
                    'PRODID:-//RH Flow//FR',
                    'CALSCALE:GREGORIAN',
                    'METHOD:PUBLISH',
                    'BEGIN:VEVENT',
                    'UID:' + '{{ $event->id }}' + '@rhflow',
                    'SUMMARY:' + '{{ addslashes($event->title) }}',
                    'DTSTART:' + formatDate(startDate),
                    'DTEND:' + (endDate ? formatDate(new Date(endDate)) : formatDate(new Date(startDate.getTime() + 3600000))),
                    'DTSTAMP:' + formatDate(new Date('{{ $event->created_at->format('Y-m-d H:i:s') }}')),
                    'LAST-MODIFIED:' + formatDate(new Date('{{ $event->updated_at->format('Y-m-d H:i:s') }}')),
                    'STATUS:CONFIRMED',
                    'SEQUENCE:0',
                    'TRANSP:OPAQUE',
                ];
                
                if ('{{ $event->location }}') {
                    icsContent.push('LOCATION:' + '{{ addslashes($event->location) }}');
                }
                
                if ('{{ $event->description }}') {
                    icsContent.push('DESCRIPTION:' + '{{ addslashes(strip_tags($event->description)) }}');
                }
                
                if ('{{ $event->recurrence_rule }}') {
                    let rrule = 'RRULE:FREQ=' + '{{ strtoupper($event->recurrence_rule ?? '') }}';
                    @if($event->recurrence_until)
                    rrule += ';UNTIL=' + formatDate(new Date('{{ $event->recurrence_until->format('Y-m-d') }}'));
                    @endif
                    icsContent.push(rrule);
                }
                
                icsContent.push('END:VEVENT');
                icsContent.push('END:VCALENDAR');
                
                // Créer un blob et un lien de téléchargement
                const blob = new Blob([icsContent.join('\r\n')], { type: 'text/calendar;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                
                const a = document.createElement('a');
                a.href = url;
                a.download = 'evenement-{{ Str::slug($event->title) }}.ics';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });
        }
        
        // Initialisation des tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
