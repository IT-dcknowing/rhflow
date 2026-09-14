@extends('layouts.app')

@section('title', 'Gestion des Événements - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">🏆 Gestion des Événements</h4>
                    <p class="text-muted mb-0">Gérez les événements des employés et de votre entreprise</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.evenements.events.calendar') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt me-1"></i>Vue Calendrier
                    </a>
                    <a href="{{ route('company.evenements.events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvel Événement
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-primary rounded">
                            <i class="fas fa-calendar-check fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-primary">{{ $events->total() }}</h3>
                    <p class="text-muted mb-2">Événements au total</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-success rounded">
                            <i class="fas fa-check-circle fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-success">{{ $events->where('status', 'published')->count() }}</h3>
                    <p class="text-muted mb-2">Événements publiés</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-warning rounded">
                            <i class="fas fa-clock fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-warning">{{ $events->where('start_date', '>', now())->count() }}</h3>
                    <p class="text-muted mb-2">Événements à venir</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-info rounded">
                            <i class="fas fa-users fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-info">{{ $events->sum('max_participants') }}</h3>
                    <p class="text-muted mb-2">Places disponibles</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et Recherche -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filtres et Recherche</h5>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="fas fa-filter me-1"></i>Filtres
                </button>
                <a href="{{ route('company.evenements.events.index') }}" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-undo me-1"></i>Réinitialiser
                </a>
            </div>
        </div>
        <div class="card-body collapse {{ request()->hasAny(['type', 'status', 'start_date', 'end_date', 'search']) ? 'show' : '' }}" id="filtersCollapse">
            <form action="{{ route('company.evenements.events.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type d'événement</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Tous les types</option>
                            @foreach($eventTypes as $type)
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
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publié</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
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
                            <input type="text" class="form-control" placeholder="Rechercher un événement..." name="search" value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search me-1"></i>Rechercher
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des événements -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des Événements</h5>
            <div class="d-flex align-items-center">
                <span class="badge bg-label-primary me-2">
                    {{ $events->firstItem() }}-{{ $events->lastItem() }} sur {{ $events->total() }}
                </span>
                <div class="btn-group" role="group">
                    <a href="{{ $events->withQueryString()->url(1) }}" class="btn btn-outline-secondary {{ $events->onFirstPage() ? 'disabled' : '' }}">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    <a href="{{ $events->previousPageUrl() }}" class="btn btn-outline-secondary {{ $events->onFirstPage() ? 'disabled' : '' }}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <a href="{{ $events->nextPageUrl() }}" class="btn btn-outline-secondary {{ $events->hasMorePages() ? '' : 'disabled' }}">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="{{ $events->withQueryString()->url($events->lastPage()) }}" class="btn btn-outline-secondary {{ $events->hasMorePages() ? '' : 'disabled' }}">
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
                        <th>Statut</th>
                        <th>Participants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($events as $event)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-{{ $event->status_color }}">
                                            <i class="fas {{ $event->type->icon ?? 'fa-calendar' }}"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $event->title }}</h6>
                                        <small class="text-muted">{{ $event->type->name ?? 'Non spécifié' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $event->type->color ?? 'primary' }}">
                                    {{ $event->type->name ?? 'Non spécifié' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $event->start_date->format('d/m/Y') }}</span>
                                    <small class="text-muted">
                                        {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <span>{{ $event->location ?? 'Non spécifié' }}</span>
                                </div>
                                @if($event->branch)
                                    <small class="text-muted">{{ $event->branch->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $event->status_color }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress w-100 me-2" style="height: 6px;">
                                        @php
                                            $participantCount = $event->participants->count();
                                            $maxParticipants = $event->max_participants > 0 ? $event->max_participants : 1;
                                            $percentage = min(100, ($participantCount / $maxParticipants) * 100);
                                        @endphp
                                        <div class="progress-bar bg-{{ $percentage >= 90 ? 'danger' : 'success' }}" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%" 
                                             aria-valuenow="{{ $percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="text-nowrap">{{ $participantCount }}/{{ $event->max_participants > 0 ? $event->max_participants : '∞' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('company.evenements.events.show', $event) }}">
                                            <i class="fas fa-eye me-2"></i>Voir
                                        </a>
                                        <a class="dropdown-item" href="{{ route('company.evenements.events.edit', $event) }}">
                                            <i class="fas fa-edit me-2"></i>Modifier
                                        </a>
                                        @if($event->status == 'published')
                                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('cancel-event-{{ $event->id }}').submit();">
                                                <i class="fas fa-times-circle me-2"></i>Annuler
                                            </a>
                                            <form id="cancel-event-{{ $event->id }}" action="{{ route('company.evenements.events.cancel', $event) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('PATCH')
                                            </form>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) { event.preventDefault(); document.getElementById('delete-event-{{ $event->id }}').submit(); }">
                                            <i class="fas fa-trash-alt me-2"></i>Supprimer
                                        </a>
                                        <form id="delete-event-{{ $event->id }}" action="{{ route('company.evenements.events.destroy', $event) }}" method="POST" class="d-none">
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
                                    <h5 class="mb-2">Aucun événement trouvé</h5>
                                    <p class="text-muted mb-0">Commencez par créer votre premier événement</p>
                                    <a href="{{ route('company.evenements.events.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-1"></i>Créer un événement
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">
                        Affichage de <strong>{{ $events->firstItem() }}</strong> à <strong>{{ $events->lastItem() }}</strong> sur <strong>{{ $events->total() }}</strong> événements
                    </p>
                </div>
                <div>
                    {{ $events->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Actions Rapides -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">⚡ Actions Rapides</h5>
                    <span class="badge bg-label-primary">Événements</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('company.evenements.events.create') }}" class="btn btn-outline-primary">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        <span>Nouvel Événement</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="{{ route('company.evenements.events.calendar') }}" class="btn btn-outline-info">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <span>Voir le Calendrier</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="#" class="btn btn-outline-success">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-file-export me-2"></i>
                                        <span>Exporter en Excel</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="d-grid">
                                <a href="#" class="btn btn-outline-warning">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="fas fa-cog me-2"></i>
                                        <span>Paramètres</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .event-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .event-avatar {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        border-radius: 0.5rem;
    }
    .event-participants {
        position: relative;
        height: 30px;
    }
    .participant-avatar {
        width: 30px;
        height: 30px;
        border: 2px solid #fff;
        margin-left: -10px;
    }
    .participant-avatar:first-child {
        margin-left: 0;
    }
    .more-participants {
        width: 30px;
        height: 30px;
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
        var deleteModal = document.getElementById('deleteEventModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var eventId = button.getAttribute('data-event-id');
                var confirmButton = deleteModal.querySelector('#confirmDelete');
                
                confirmButton.onclick = function() {
                    document.getElementById('delete-event-' + eventId).submit();
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
    function confirmDelete(eventId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
            document.getElementById('delete-event-' + eventId).submit();
        }
    }

    // Fonction pour exporter les événements au format Excel
    function exportToExcel() {
        // Implémentation de l'export Excel
        alert('Fonctionnalité d\'export Excel à implémenter');
    }
</script>
@endpush