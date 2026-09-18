                                            <div class="progress mb-3" style="height: 10px;">
                                                @php
                                                    $accepted = $meeting->participants->where('status', 'accepted')->count();
                                                    $declined = $meeting->participants->where('status', 'declined')->count();
                                                    $tentative = $meeting->participants->where('status', 'tentative')->count();
                                                    $pending = $meeting->participants->where('status', 'pending')->count();
                                                    $total = $meeting->participants->count() ?: 1;
                                                    
                                                    $acceptedPercent = ($accepted / $total) * 100;
                                                    $declinedPercent = ($declined / $total) * 100;
                                                    $tentativePercent = ($tentative / $total) * 100;
                                                    $pendingPercent = ($pending / $total) * 100;
                                                @endphp
                                                
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $acceptedPercent }}%" 
                                                     data-bs-toggle="tooltip" title="{{ $accepted }} accepté(s)"></div>
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $tentativePercent }}%" 
                                                     data-bs-toggle="tooltip" title="{{ $tentative }} peut-être"></div>
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $declinedPercent }}%" 
                                                     data-bs-toggle="tooltip" title="{{ $declined }} refusé(s)"></div>
                                                <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $pendingPercent }}%" 
                                                     data-bs-toggle="tooltip" title="{{ $pending }} en attente"></div>
                                            </div>

                                            <div class="participant-status mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="d-flex align-items-center">
                                                        <span class="status-indicator bg-success me-2"></span>
                                                        <span>Accepté ({{ $accepted }})</span>
                                                    </span>
                                                    <span class="text-muted">{{ number_format($acceptedPercent, 0) }}%</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="d-flex align-items-center">
                                                        <span class="status-indicator bg-warning me-2"></span>
                                                        <span>Peut-être ({{ $tentative }})</span>
                                                    </span>
                                                    <span class="text-muted">{{ number_format($tentativePercent, 0) }}%</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="d-flex align-items-center">
                                                        <span class="status-indicator bg-danger me-2"></span>
                                                        <span>Refusé ({{ $declined }})</span>
                                                    </span>
                                                    <span class="text-muted">{{ number_format($declinedPercent, 0) }}%</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="d-flex align-items-center">
                                                        <span class="status-indicator bg-secondary me-2"></span>
                                                        <span>En attente ({{ $pending }})</span>
                                                    </span>
                                                    <span class="text-muted">{{ number_format($pendingPercent, 0) }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Participants -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Liste des participants</h5>
                                    <span class="badge bg-primary rounded-pill">{{ $meeting->participants->count() }}</span>
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
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($meeting->participants as $participant)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ $participant->participant->avatar_url ?? asset('images/default-avatar.png') }}" 
                                                                     alt="{{ $participant->participant->full_name ?? $participant->name }}" 
                                                                     class="rounded-circle me-2" width="32" height="32">
                                                                <div>
                                                                    <div class="fw-medium">{{ $participant->participant->full_name ?? $participant->name }}</div>
                                                                    @if($participant->participant_type === 'App\\Models\\Employee' && $participant->participant->department)
                                                                        <small class="text-muted">{{ $participant->participant->department->name }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
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
                                                            @if($participant->responded_at)
                                                                <div class="small text-muted mt-1">Le {{ \Carbon\Carbon::parse($participant->responded_at)->format('d/m/Y H:i') }}</div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                @if($participant->participant_type === 'App\\Models\\Employee')
                                                                    <a href="{{ route('company.employees.show', $participant->participant_id) }}" 
                                                                       class="btn btn-icon btn-sm btn-label-info" title="Voir le profil">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                @endif
                                                                @can('update', $meeting)
                                                                    <button type="button" class="btn btn-icon btn-sm btn-label-warning" 
                                                                            data-bs-toggle="modal" data-bs-target="#participantStatusModal" 
                                                                            data-participant-id="{{ $participant->id }}"
                                                                            data-current-status="{{ $participant->status }}"
                                                                            title="Modifier le statut">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4">Aucun participant pour le moment</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Pièces jointes -->
                            @if($meeting->attachments->isNotEmpty() || auth()->user()->can('update', $meeting))
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Pièces jointes</h5>
                                    @can('update', $meeting)
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAttachmentModal">
                                            <i class="fas fa-plus me-1"></i> Ajouter
                                        </button>
                                    @endcan
                                </div>
                                <div class="card-body p-0">
                                    @if($meeting->attachments->isNotEmpty())
                                        <div class="list-group list-group-flush">
                                            @foreach($meeting->attachments as $attachment)
                                                <div class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <i class="{{ getFileIconClass($attachment->filename) }} me-3" style="font-size: 1.5rem;"></i>
                                                            <div>
                                                                <div class="fw-medium">{{ $attachment->original_filename }}</div>
                                                                <small class="text-muted">{{ formatFileSize($attachment->size) }} • Ajouté le {{ $attachment->created_at->format('d/m/Y') }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="btn-group">
                                                            <a href="{{ route('company.meetings.attachments.download', ['meeting' => $meeting->id, 'attachment' => $attachment->id]) }}" 
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
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">Aucune pièce jointe pour le moment</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Commentaires -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Commentaires</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <form action="{{ route('company.meetings.comments.store', $meeting->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group mb-2">
                                                <label for="comment" class="form-label">Ajouter un commentaire</label>
                                                <textarea class="form-control" id="comment" name="content" rows="3" required></textarea>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-paper-plane me-1"></i> Envoyer
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="comments">
                                        @forelse($meeting->comments->sortByDesc('created_at') as $comment)
                                            <div class="d-flex mb-4">
                                                <img src="{{ $comment->user->avatar_url ?? asset('images/default-avatar.png') }}" 
                                                     alt="{{ $comment->user->full_name }}" 
                                                     class="rounded-circle me-3" width="48" height="48">
                                                <div class="flex-grow-1">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <div>
                                                                    <h6 class="mb-0">{{ $comment->user->full_name }}</h6>
                                                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                                </div>
                                                                @can('delete', $comment)
                                                                    <form action="{{ route('company.meetings.comments.destroy', ['meeting' => $meeting->id, 'comment' => $comment->id]) }}" 
                                                                          method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-link text-danger" 
                                                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')">
                                                                            <i class="far fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            </div>
                                                            <div class="comment-content">
                                                                {!! nl2br(e($comment->content)) !!}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4">
                                                <i class="far fa-comments fa-3x text-muted mb-3"></i>
                                                <p class="text-muted mb-0">Aucun commentaire pour le moment</p>
                                                <small class="text-muted">Soyez le premier à laisser un commentaire</small>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Actions rapides -->
                            <x-quick-actions class="mb-4" stacked>
                                @if($meeting->status === 'scheduled')
                                    @if($meeting->start_date > now())
                                        <x-quick-action icon="far fa-bell" label="Envoyer un rappel"
                                            variant="outline" color="primary"
                                            data-bs-toggle="modal" data-bs-target="#sendReminderModal" />

                                        @if(auth()->user()->can('update', $meeting))
                                            <x-quick-action icon="fas fa-edit" label="Modifier la réunion"
                                                :href="route('company.meetings.edit', $meeting->id)" variant="outline" />

                                            <x-quick-action icon="fas fa-times-circle" label="Annuler la réunion"
                                                variant="outline" color="danger"
                                                data-bs-toggle="modal" data-bs-target="#cancelMeetingModal" />
                                        @endif
                                    @elseif($meeting->end_date < now() && $meeting->status !== 'completed')
                                        <x-quick-action icon="fas fa-check-circle" label="Marquer comme terminée"
                                            :href="route('company.meetings.complete', $meeting->id)" color="success" />
                                    @endif

                                    <x-quick-action icon="fas fa-copy" label="Dupliquer la réunion"
                                        :href="route('company.meetings.duplicate', $meeting->id)"
                                        variant="outline" color="primary" />
                                @endif

                                {{-- L'export garde son menu : le bouton porte la classe du composant. --}}
                                <div class="dropdown">
                                    <x-quick-action icon="fas fa-download" label="Exporter" variant="outline"
                                        class="w-100 dropdown-toggle" id="exportDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false" />
                                    <ul class="dropdown-menu w-100" aria-labelledby="exportDropdown">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('company.meetings.export.pdf', $meeting->id) }}" target="_blank">
                                                <i class="far fa-file-pdf text-danger me-2"></i> En PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('company.meetings.export.excel', $meeting->id) }}" target="_blank">
                                                <i class="far fa-file-excel text-success me-2"></i> En Excel
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('company.meetings.ical', $meeting->id) }}">
                                                <i class="far fa-calendar-plus me-2"></i> Fichier iCal
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </x-quick-actions>

                            <!-- Prochaine occurrence -->
                            @if($meeting->is_recurring && $meeting->next_occurrence)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Prochaine occurrence</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="mb-2">
                                                <div class="h4 mb-0">{{ \Carbon\Carbon::parse($meeting->next_occurrence)->format('d') }}</div>
                                                <div class="text-uppercase text-muted">{{ \Carbon\Carbon::parse($meeting->next_occurrence)->locale('fr')->monthName }}</div>
                                            </div>
                                            <div class="text-muted mb-3">
                                                {{ \Carbon\Carbon::parse($meeting->next_occurrence)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($meeting->next_occurrence)->addMinutes($meeting->duration)->format('H:i') }}
                                            </div>
                                            <a href="{{ route('company.meetings.show', $meeting->id) }}?occurrence={{ $meeting->next_occurrence->format('Y-m-d') }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                Voir les détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Statistiques -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Statistiques</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-users me-2 text-primary"></i> Participants</span>
                                            <span class="badge bg-primary rounded-pill">{{ $meeting->participants->count() }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-check-circle me-2 text-success"></i> Acceptés</span>
                                            <span class="badge bg-success rounded-pill">{{ $accepted }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-question-circle me-2 text-warning"></i> Peut-être</span>
                                            <span class="badge bg-warning rounded-pill">{{ $tentative }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-times-circle me-2 text-danger"></i> Refusés</span>
                                            <span class="badge bg-danger rounded-pill">{{ $declined }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="far fa-clock me-2 text-secondary"></i> En attente</span>
                                            <span class="badge bg-secondary rounded-pill">{{ $pending }}</span>
                                        </li>
                                        @if($meeting->status === 'completed')
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                <span><i class="fas fa-user-clock me-2 text-info"></i> Taux de participation</span>
                                                <span class="fw-medium">{{ $participationRate }}%</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>

                            <!-- Fichiers récents -->
                            @if($meeting->attachments->isNotEmpty())
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Fichiers récents</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            @foreach($meeting->attachments->sortByDesc('created_at')->take(3) as $attachment)
                                                <a href="{{ route('company.meetings.attachments.download', ['meeting' => $meeting->id, 'attachment' => $attachment->id]) }}" 
                                                   class="list-group-item list-group-item-action d-flex align-items-center">
                                                    <i class="{{ getFileIconClass($attachment->filename) }} me-3"></i>
                                                    <div class="flex-grow-1 text-truncate" style="max-width: 200px;">
                                                        <div class="text-truncate">{{ $attachment->original_filename }}</div>
                                                        <small class="text-muted">{{ formatFileSize($attachment->size) }}</small>
                                                    </div>
                                                </a>
                                            @endforeach
                                            @if($meeting->attachments->count() > 3)
                                                <a href="#attachments" class="list-group-item list-group-item-action text-center text-primary">
                                                    Voir tous les fichiers ({{ $meeting->attachments->count() }})
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Annuler la réunion -->
<div class="modal fade" id="cancelMeetingModal" tabindex="-1" aria-labelledby="cancelMeetingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('company.meetings.cancel', $meeting->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelMeetingModalLabel">Annuler la réunion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir annuler cette réunion ?</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Raison de l'annulation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notify_participants" name="notify_participants" checked>
                        <label class="form-check-label" for="notify_participants">
                            Notifier tous les participants par email
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Envoyer un rappel -->
<div class="modal fade" id="sendReminderModal" tabindex="-1" aria-labelledby="sendReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('company.meetings.remind', $meeting->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="sendReminderModalLabel">Envoyer un rappel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reminder_message" class="form-label">Message personnalisé (optionnel)</label>
                        <textarea class="form-control" id="reminder_message" name="message" rows="3" 
                                  placeholder="Ajoutez un message personnalisé..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="include_agenda" name="include_agenda" checked>
                        <label class="form-check-label" for="include_agenda">
                            Inclure l'ordre du jour
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="include_location" name="include_location" checked>
                        <label class="form-check-label" for="include_location">
                            Inclure le lieu/le lien de la réunion
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer le rappel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ajouter une pièce jointe -->
<div class="modal fade" id="addAttachmentModal" tabindex="-1" aria-labelledby="addAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('company.meetings.attachments.store', $meeting->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addAttachmentModalLabel">Ajouter une pièce jointe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Sélectionner un fichier</label>
                        <input class="form-control" type="file" id="attachment" name="attachment" required>
                        <div class="form-text">Formats acceptés : PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, GIF. Taille maximale : 10 Mo</div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optionnel)</label>
                        <input type="text" class="form-control" id="description" name="description">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Téléverser</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier le statut d'un participant -->
<div class="modal fade" id="participantStatusModal" tabindex="-1" aria-labelledby="participantStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateParticipantStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="participantStatusModalLabel">Modifier le statut</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Nouveau statut</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="accepted">Accepté</option>
                            <option value="tentative">Peut-être</option>
                            <option value="declined">Refusé</option>
                            <option value="pending">En attente</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Supprimer la réunion -->
<div class="modal fade" id="deleteMeetingModal" tabindex="-1" aria-labelledby="deleteMeetingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('company.meetings.destroy', $meeting->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteMeetingModalLabel">Supprimer la réunion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer définitivement cette réunion ? Cette action est irréversible.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirm_delete" name="confirm_delete" required>
                        <label class="form-check-label" for="confirm_delete">
                            Je confirme vouloir supprimer cette réunion
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .participant-avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
    }
    .comment-content {
        white-space: pre-line;
    }
    .file-icon {
        font-size: 1.5rem;
        min-width: 30px;
        text-align: center;
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

        // Gestion de la suppression d'une pièce jointe
        document.querySelectorAll('.delete-attachment').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir supprimer cette pièce jointe ?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("company.meetings.attachments.destroy", ["meeting" => $meeting->id, "attachment" => "ATTACHMENT_ID"]) }}'
                        .replace('ATTACHMENT_ID', this.dataset.id);
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    form.appendChild(methodInput);
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Gestion de la modification du statut d'un participant
        const participantStatusModal = document.getElementById('participantStatusModal');
        if (participantStatusModal) {
            participantStatusModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const participantId = button.getAttribute('data-participant-id');
                const currentStatus = button.getAttribute('data-current-status');
                
                const form = participantStatusModal.querySelector('form');
                form.action = '{{ route("company.meetings.participants.update", ["meeting" => $meeting->id, "participant" => "PARTICIPANT_ID"]) }}'
                    .replace('PARTICIPANT_ID', participantId);
                
                const statusSelect = participantStatusModal.querySelector('#status');
                statusSelect.value = currentStatus;
            });
        }

        // Soumission du formulaire de statut du participant
        const updateParticipantStatusForm = document.getElementById('updateParticipantStatusForm');
        if (updateParticipantStatusForm) {
            updateParticipantStatusForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        status: formData.get('status')
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Une erreur est survenue lors de la mise à jour du statut.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de la mise à jour du statut.');
                });
            });
        }

        // Affichage d'une prévisualisation des fichiers sélectionnés
        const fileInput = document.getElementById('attachment');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const fileName = this.files[0]?.name || 'Aucun fichier sélectionné';
                const fileSize = this.files[0] ? formatFileSize(this.files[0].size) : '';
                
                let preview = document.getElementById('filePreview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'filePreview';
                    this.parentNode.appendChild(preview);
                }
                
                preview.innerHTML = `
                    <div class="alert alert-info mt-2">
                        <i class="fas fa-file me-2"></i> ${fileName} <small class="text-muted">(${fileSize})</small>
                    </div>
                `;
            });
        }
    });

    // Fonction utilitaire pour formater la taille des fichiers
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Octet';
        
        const k = 1024;
        const sizes = ['Octets', 'Ko', 'Mo', 'Go'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Fonction utilitaire pour obtenir l'icône en fonction du type de fichier
    function getFileIconClass(filename) {
        if (!filename) return 'far fa-file';
        
        const extension = filename.split('.').pop().toLowerCase();
        const icons = {
            // Documents
            'pdf': 'far fa-file-pdf text-danger',
            'doc': 'far fa-file-word text-primary',
            'docx': 'far fa-file-word text-primary',
            'txt': 'far fa-file-alt text-secondary',
            'rtf': 'far fa-file-alt text-secondary',
            
            // Feuilles de calcul
            'xls': 'far fa-file-excel text-success',
            'xlsx': 'far fa-file-excel text-success',
            'csv': 'far fa-file-csv text-success',
            
            // Présentations
            'ppt': 'far fa-file-powerpoint text-warning',
            'pptx': 'far fa-file-powerpoint text-warning',
            
            // Images
            'jpg': 'far fa-file-image text-info',
            'jpeg': 'far fa-file-image text-info',
            'png': 'far fa-file-image text-info',
            'gif': 'far fa-file-image text-info',
            'bmp': 'far fa-file-image text-info',
            'svg': 'far fa-file-image text-info',
            
            // Archives
            'zip': 'far fa-file-archive text-secondary',
            'rar': 'far fa-file-archive text-secondary',
            '7z': 'far fa-file-archive text-secondary',
            'tar': 'far fa-file-archive text-secondary',
            'gz': 'far fa-file-archive text-secondary',
            
            // Audio
            'mp3': 'far fa-file-audio text-primary',
            'wav': 'far fa-file-audio text-primary',
            'ogg': 'far fa-file-audio text-primary',
            
            // Vidéo
            'mp4': 'far fa-file-video text-danger',
            'avi': 'far fa-file-video text-danger',
            'mov': 'far fa-file-video text-danger',
            'wmv': 'far fa-file-video text-danger',
            
            // Autres
            'exe': 'far fa-file-code text-secondary',
            'dll': 'far fa-file-code text-secondary',
            'ini': 'far fa-file-code text-secondary',
            'log': 'far fa-file-alt text-secondary'
        };
        
        return icons[extension] || 'far fa-file';
    }
</script>
@endpush