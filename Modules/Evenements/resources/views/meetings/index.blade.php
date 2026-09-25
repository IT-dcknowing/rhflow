@extends('layouts.app')

@section('title', 'Gestion des Réunions - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📅 Gestion des Réunions</h4>
                    <p class="text-muted mb-0">Planifiez et gérez les réunions de votre entreprise</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.evenements.meetings.calendar') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt me-1"></i>Vue Calendrier
                    </a>
                    <button type="button" class="btn btn-primary" disabled title="Fonctionnalité désactivée">
                        <i class="fas fa-plus me-1"></i>Nouvelle Réunion
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <x-kpi-grid>
        <x-kpi icon="fas fa-calendar-check" color="primary" label="Total" sublabel="Réunions"
            :value="$meetings->total()" />

        <x-kpi icon="fas fa-check-circle" color="success" label="Terminées" sublabel="Réunions"
            :value="$meetings->where('status', 'completed')->count()" />

        <x-kpi icon="fas fa-clock" color="warning" label="À venir" sublabel="Réunions"
            :value="$meetings->where('start_time', '>', now())->count()" />

        <x-kpi icon="fas fa-users" color="info" label="Participants" sublabel="Au total"
            :value="$totalParticipants ?? 0" />
    </x-kpi-grid>

    <!-- Filtres et Recherche -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filtres et Recherche</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="fas fa-filter me-1"></i>Filtres
                </button>
                <a href="{{ route('company.evenements.meetings.index') }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-undo me-1"></i>Réinitialiser
                </a>
            </div>
        </div>
        <div class="card-body collapse {{ request()->anyFilled(['type', 'status', 'start_date', 'end_date', 'search']) ? 'show' : '' }}" id="filtersCollapse">
            <form action="{{ route('company.evenements.meetings.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type de réunion</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tous les types</option>
                            @foreach($meetingTypes as $type)
                                <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Planifiée</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Rechercher une réunion..." name="search" value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search me-1"></i>Rechercher
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des réunions -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Réunions</h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-label-primary me-2">
                    {{ $meetings->firstItem() }}-{{ $meetings->lastItem() }} sur {{ $meetings->total() }}
                </span>
                <div class="btn-group" role="group">
                    <a href="{{ $meetings->withQueryString()->url(1) }}" class="btn btn-outline-secondary {{ $meetings->onFirstPage() ? 'disabled' : '' }}">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="{{ $meetings->previousPageUrl() }}" class="btn btn-outline-secondary {{ $meetings->onFirstPage() ? 'disabled' : '' }}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="{{ $meetings->nextPageUrl() }}" class="btn btn-outline-secondary {{ $meetings->hasMorePages() ? '' : 'disabled' }}">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="{{ $meetings->withQueryString()->url($meetings->lastPage()) }}" class="btn btn-outline-secondary {{ $meetings->hasMorePages() ? '' : 'disabled' }}">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Date et Heure</th>
                        <th>Lieu</th>
                        <th>Participants</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($meetings as $meeting)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-{{ $meeting->status_color }}">
                                            <i class="fas {{ $meeting->type->icon ?? 'fa-calendar' }}"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $meeting->title }}</h6>
                                        <small class="text-muted">{{ $meeting->type->name ?? 'Non spécifié' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $meeting->type->color ?? 'primary' }}">
                                    {{ $meeting->type->name ?? 'Non spécifié' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $meeting->start_time->format('d/m/Y') }}</span>
                                    <small class="text-muted">
                                        {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <span>{{ $meeting->location ?? 'En ligne' }}</span>
                                </div>
                                @if($meeting->is_online)
                                    <small class="text-muted">Lien: {{ $meeting->meeting_url ?? 'Non fourni' }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-group me-2">
                                        @foreach($meeting->participants->take(3) as $participant)
                                            <div class="avatar avatar-xs" data-bs-toggle="tooltip" title="{{ $participant->name }}">
                                                <img src="{{ $participant->avatar_url ?? asset('images/avatars/default-avatar.png') }}" alt="Avatar" class="rounded-circle">
                                            </div>
                                        @endforeach
                                        @if($meeting->participants->count() > 3)
                                            <div class="avatar avatar-xs">
                                                <div class="avatar-initial rounded-circle bg-label-primary">
                                                    +{{ $meeting->participants->count() - 3 }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-nowrap">{{ $meeting->participants->count() }} participants</span>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'scheduled' => 'bg-label-primary',
                                        'in_progress' => 'bg-label-warning',
                                        'completed' => 'bg-label-success',
                                        'cancelled' => 'bg-label-danger'
                                    ][$meeting->status] ?? 'bg-label-secondary';
                                    
                                    $statusText = [
                                        'scheduled' => 'Planifiée',
                                        'in_progress' => 'En cours',
                                        'completed' => 'Terminée',
                                        'cancelled' => 'Annulée'
                                    ][$meeting->status] ?? 'Inconnu';
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-icon btn-sm btn-label-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('company.evenements.meetings.show', $meeting) }}">
                                            <i class="fas fa-eye me-2"></i>Voir
                                        </a>
                                        <a class="dropdown-item" href="{{ route('company.evenements.meetings.edit', $meeting) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                        @if($meeting->status == 'scheduled')
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('start-meeting-{{ $meeting->id }}').submit();">
                                                <i class="fas fa-play-circle me-2"></i>Démarrer
                                            </a>
                                            <form id="start-meeting-{{ $meeting->id }}" action="{{ route('company.evenements.meetings.start', $meeting) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                        @elseif($meeting->status == 'in_progress')
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('complete-meeting-{{ $meeting->id }}').submit();">
                                                <i class="fas fa-check-circle me-2"></i>Terminer
                                            </a>
                                            <form id="complete-meeting-{{ $meeting->id }}" action="{{ route('company.evenements.meetings.complete', $meeting) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                        @endif
                                        @if(in_array($meeting->status, ['scheduled', 'in_progress']))
                                            <a class="dropdown-item text-warning" href="#" onclick="event.preventDefault(); document.getElementById('cancel-meeting-{{ $meeting->id }}').submit();">
                                                <i class="fas fa-times-circle me-2"></i>Annuler
                                            </a>
                                            <form id="cancel-meeting-{{ $meeting->id }}" action="{{ route('company.evenements.meetings.cancel', $meeting) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette réunion ?')) { event.preventDefault(); document.getElementById('delete-meeting-{{ $meeting->id }}').submit(); }">
                                            <i class="fas fa-trash-alt me-2"></i>Supprimer
                                        </a>
                                        <form id="delete-meeting-{{ $meeting->id }}" action="{{ route('company.evenements.meetings.destroy', $meeting) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="avatar avatar-xl mb-3">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="fas fa-calendar-times fa-2x"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-2">Aucune réunion trouvée</h5>
                                    <p class="text-muted mb-0">Commencez par créer votre première réunion</p>
                                    <a href="{{ route('company.evenements.meetings.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-1"></i>Créer une réunion
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($meetings->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">
                        Affichage de <strong>{{ $meetings->firstItem() }}</strong> à <strong>{{ $meetings->lastItem() }}</strong> sur <strong>{{ $meetings->total() }}</strong> réunions
                    </p>
                </div>
                <div>
                    {{ $meetings->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Actions Rapides -->
    <x-quick-actions>
        <x-quick-action icon="fas fa-plus-circle" label="Nouvelle réunion"
            :href="route('company.evenements.meetings.create')" />

        <x-slot:secondary>
            <x-quick-action icon="fas fa-calendar-alt" label="Voir le calendrier"
                :href="route('company.evenements.meetings.calendar')" variant="outline" color="primary" />
        </x-slot:secondary>

        <x-slot:end>
            <x-quick-action icon="fas fa-file-export" label="Exporter en Excel" href="#" variant="ghost" />
            <x-quick-action icon="fas fa-cog" label="Paramètres" href="#" variant="ghost" />
        </x-slot:end>
    </x-quick-actions>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteMeetingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette réunion ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de démarrage de réunion -->
<div class="modal fade" id="startMeetingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Démarrer la réunion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous démarrer cette réunion maintenant ?</p>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="sendNotification" checked>
                    <label class="form-check-label" for="sendNotification">Envoyer une notification aux participants</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmStart">Démarrer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .meeting-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .meeting-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .meeting-avatar {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        border-radius: 0.5rem;
    }
    .participant-avatar {
        width: 32px;
        height: 32px;
        border: 2px solid #fff;
        margin-left: -10px;
    }
    .participant-avatar:first-child {
        margin-left: 0;
    }
    .more-participants {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #f3f3f3;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialisation des tooltips
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Gestion de la suppression avec confirmation
        var deleteModal = document.getElementById('deleteMeetingModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var meetingId = button.getAttribute('data-meeting-id');
                var confirmButton = deleteModal.querySelector('#confirmDelete');
                
                confirmButton.onclick = function() {
                    document.getElementById('delete-meeting-' + meetingId).submit();
                };
            });
        }

        // Gestion du démarrage de réunion
        var startModal = document.getElementById('startMeetingModal');
        if (startModal) {
            startModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var meetingId = button.getAttribute('data-meeting-id');
                var confirmButton = startModal.querySelector('#confirmStart');
                
                confirmButton.onclick = function() {
                    var sendNotification = document.getElementById('sendNotification').checked;
                    var form = document.getElementById('start-meeting-' + meetingId);
                    
                    if (sendNotification) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'send_notification';
                        input.value = '1';
                        form.appendChild(input);
                    }
                    
                    form.submit();
                };
            });
        }

        // Initialisation des selects avec Select2 si disponible
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    });

    // Fonction pour confirmer la suppression
    function confirmDelete(meetingId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette réunion ?')) {
            document.getElementById('delete-meeting-' + meetingId).submit();
        }
    }

    // Fonction pour démarrer une réunion
    function startMeeting(meetingId) {
        if (confirm('Voulez-vous démarrer cette réunion maintenant ?')) {
            document.getElementById('start-meeting-' + meetingId).submit();
        }
    }

    // Fonction pour terminer une réunion
    function completeMeeting(meetingId) {
        if (confirm('Voulez-vous marquer cette réunion comme terminée ?')) {
            document.getElementById('complete-meeting-' + meetingId).submit();
        }
    }

    // Fonction pour annuler une réunion
    function cancelMeeting(meetingId) {
        if (confirm('Voulez-vous annuler cette réunion ? Les participants seront notifiés.')) {
            document.getElementById('cancel-meeting-' + meetingId).submit();
        }
    }

    // Fonction pour exporter les réunions au format Excel
    function exportToExcel() {
        // Implémentation de l'export Excel
        alert('Fonctionnalité d\'export Excel à implémenter');
    }
</script>
@endpush
