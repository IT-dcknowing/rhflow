@extends('layouts.app')

@section('title', 'Tableau de bord - Événements')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.css">
<style>  
    .card-hover:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .stat-card {
        border-left: 4px solid;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }
    .stat-card .card-body {
        padding: 1.25rem 1.5rem;
    }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    .trend-up {
        color: #10b981;
    }
    .trend-down {
        color: #ef4444;
    }
    .trend-neutral {
        color: #6b7280;
    }
    .fc {
        font-size: 0.875rem;
    }
    .fc-daygrid-day-number {
        padding: 4px;
    }
    .fc-daygrid-event {
        white-space: normal;
        font-size: 0.75rem;
        line-height: 1.2;
        padding: 2px 4px;
    }
    .activity-chart-container {
        position: relative;
        height: 300px;
    }
    .event-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .legend-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
    }
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 0.5rem;
    }
    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }
    .trend-indicator {
        display: inline-flex;
        align-items: center;
        font-size: 0.875rem;
        margin-left: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête du tableau de bord -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📅 Tableau de bord Événements</h4>
                    <p class="text-muted mb-0">Vue d'ensemble des activités et événements de votre entreprise</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['period' => 'month']) }}" class="btn btn-outline-primary {{ $period === 'month' ? 'active' : '' }}">Mois</a>
                        <a href="{{ request()->fullUrlWithQuery(['period' => 'quarter']) }}" class="btn btn-outline-primary {{ $period === 'quarter' ? 'active' : '' }}">Trimestre</a>
                        <a href="{{ request()->fullUrlWithQuery(['period' => 'year']) }}" class="btn btn-outline-primary {{ $period === 'year' ? 'active' : '' }}">Année</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <!-- Annonces actives -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-label">Annonces actives</div>
                            <div class="stat-value">
                                {{ $stats['total_announcements']['value'] ?? 0 }}
                                @if(isset($stats['total_announcements']['trend']))
                                    <span class="trend-indicator text-{{ $stats['total_announcements']['trend_type'] === 'up' ? 'success' : ($stats['total_announcements']['trend_type'] === 'down' ? 'danger' : 'muted') }}">
                                        @if($stats['total_announcements']['trend_type'] === 'up')
                                            <i class="fas fa-arrow-up"></i>
                                        @elseif($stats['total_announcements']['trend_type'] === 'down')
                                            <i class="fas fa-arrow-down"></i>
                                        @else
                                            <i class="fas fa-minus"></i>
                                        @endif
                                        {{ $stats['total_announcements']['trend'] }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-bullhorn stat-icon"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('company.evenements.annonces.index') }}" class="btn btn-sm btn-outline-primary">
                            Voir tout <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Événements à venir -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-label">Événements à venir</div>
                            <div class="stat-value">
                                {{ $stats['upcoming_events']['value'] ?? 0 }}
                                @if(isset($stats['upcoming_events']['trend']))
                                    <span class="trend-indicator text-{{ $stats['upcoming_events']['trend_type'] === 'up' ? 'success' : ($stats['upcoming_events']['trend_type'] === 'down' ? 'danger' : 'muted') }}">
                                        @if($stats['upcoming_events']['trend_type'] === 'up')
                                            <i class="fas fa-arrow-up"></i>
                                        @elseif($stats['upcoming_events']['trend_type'] === 'down')
                                            <i class="fas fa-arrow-down"></i>
                                        @else
                                            <i class="fas fa-minus"></i>
                                        @endif
                                        {{ $stats['upcoming_events']['trend'] }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-calendar-alt stat-icon"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('company.evenements.events.index') }}" class="btn btn-sm btn-outline-success">
                            Voir le calendrier <i class="fas fa-calendar-day ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Réunions récentes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-label">Réunions (30j)</div>
                            <div class="stat-value">
                                {{ $stats['total_meetings']['value'] ?? 0 }}
                                @if(isset($stats['total_meetings']['trend']))
                                    <span class="trend-indicator text-{{ $stats['total_meetings']['trend_type'] === 'up' ? 'success' : ($stats['total_meetings']['trend_type'] === 'down' ? 'danger' : 'muted') }}">
                                        @if($stats['total_meetings']['trend_type'] === 'up')
                                            <i class="fas fa-arrow-up"></i>
                                        @elseif($stats['total_meetings']['trend_type'] === 'down')
                                            <i class="fas fa-arrow-down"></i>
                                        @else
                                            <i class="fas fa-minus"></i>
                                        @endif
                                        {{ $stats['total_meetings']['trend'] }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-users stat-icon"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('company.evenements.meetings.index') }}" class="btn btn-sm btn-outline-info">
                            Voir les réunions <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Récompenses -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="stat-label">Récompenses (12 mois)</div>
                            <div class="stat-value">
                                {{ $stats['recent_awards']['value'] ?? 0 }}
                                @if(isset($stats['recent_awards']['trend']))
                                    <span class="trend-indicator text-{{ $stats['recent_awards']['trend_type'] === 'up' ? 'success' : ($stats['recent_awards']['trend_type'] === 'down' ? 'danger' : 'muted') }}">
                                        @if($stats['recent_awards']['trend_type'] === 'up')
                                            <i class="fas fa-arrow-up"></i>
                                        @elseif($stats['recent_awards']['trend_type'] === 'down')
                                            <i class="fas fa-arrow-down"></i>
                                        @else
                                            <i class="fas fa-minus"></i>
                                        @endif
                                        {{ $stats['recent_awards']['trend'] }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-trophy stat-icon"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('company.evenements.awards.index') }}" class="btn btn-sm btn-outline-warning">
                            Voir les récompenses <i class="fas fa-award ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphique d'activité et calendrier -->
    <div class="row mb-4">
        <!-- Graphique d'activité -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📊 Activité récente</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="activityRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Derniers 6 mois
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="activityRangeDropdown">
                            <li><a class="dropdown-item" href="#" data-months="3">3 derniers mois</a></li>
                            <li><a class="dropdown-item active" href="#" data-months="6">6 derniers mois</a></li>
                            <li><a class="dropdown-item" href="#" data-months="12">12 derniers mois</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="activity-chart-container">
                        <canvas id="activityChart"></canvas>
                    </div>
                    <div class="event-legend">
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #3b82f6;"></span>
                            <span>Événements</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #10b981;"></span>
                            <span>Réunions</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #8b5cf6;"></span>
                            <span>Annonces</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #f59e0b;"></span>
                            <span>Récompenses</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mini calendrier -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">📅 Calendrier du mois</h5>
                </div>
                <div class="card-body p-0">
                    <div id="miniCalendar"></div>
                    <div class="p-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Légende :</span>
                            <div class="d-flex gap-2">
                                @foreach($eventTypes as $type)
                                    <span class="badge" style="background-color: {{ $type['color'] }};">{{ $type['name'] }} ({{ $type['count'] }})</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Prochains événements -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📅 Prochains événements (7 jours)</h5>
                    <a href="{{ route('company.evenements.events.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Nouvel événement
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($upcomingEvents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingEvents as $event)
                                <div class="list-group-item border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3" style="background-color: {{ $event->color }}1A;">
                                                <div class="avatar-content" style="color: {{ $event->color }};">
                                                    <i class="fas fa-calendar-day"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $event->title }}</h6>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $event->start_date->format('d/m/Y') }}
                                                    @if($event->end_date && !$event->start_date->isSameDay($event->end_date))
                                                        au {{ $event->end_date->format('d/m/Y') }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <a href="{{ route('company.evenements.events.show', $event->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <div class="avatar avatar-lg mb-3">
                                <div class="avatar-initial bg-label-secondary rounded">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                            <h5>Aucun événement à venir</h5>
                            <p class="text-muted">Planifiez de nouveaux événements pour les voir apparaître ici</p>
                            <a href="{{ route('company.evenements.events.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Créer un événement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dernières annonces -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📢 Dernières annonces</h5>
                    <a href="{{ route('company.evenements.annonces.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Nouvelle annonce
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentAnnouncements->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentAnnouncements as $announcement)
                                <div class="list-group-item border-0">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex align-items-start">
                                            <div class="avatar avatar-sm me-3">
                                                <div class="avatar-initial bg-label-primary rounded">
                                                    <i class="fas fa-bullhorn"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $announcement->title }}</h6>
                                                <p class="mb-1 text-muted small">
                                                    <i class="far fa-calendar-alt me-1"></i>
                                                    Du {{ $announcement->start_date->format('d/m/Y') }}
                                                    au {{ $announcement->end_date->format('d/m/Y') }}
                                                </p>
                                                <p class="mb-0 text-muted small">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $announcement->branch->name ?? 'Toutes les succursales' }}
                                                    • {{ $announcement->department->name ?? 'Tous les départements' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('company.evenements.annonces.show', $announcement->id) }}">
                                                    <i class="fas fa-eye me-2"></i>Voir
                                                </a>
                                                <a class="dropdown-item" href="{{ route('company.evenements.annonces.edit', $announcement->id) }}">
                                                    <i class="fas fa-edit me-2"></i>Modifier
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('delete-announcement-{{ $announcement->id }}').submit();">
                                                    <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                </a>
                                                <form id="delete-announcement-{{ $announcement->id }}" action="{{ route('company.evenements.annonces.destroy', $announcement->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <div class="avatar avatar-lg mb-3">
                                <div class="avatar-initial bg-label-secondary rounded">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                            </div>
                            <h5>Aucune annonce récente</h5>
                            <p class="text-muted">Créez des annonces pour informer vos employés</p>
                            <a href="{{ route('company.evenements.annonces.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Créer une annonce
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Prochaines réunions -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📋 Prochaines réunions</h5>
                    <a href="{{ route('company.evenements.meetings.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Planifier
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($upcomingMeetings->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingMeetings as $meeting)
                                <div class="list-group-item border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <div class="avatar-initial bg-label-info rounded">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $meeting->title }}</h6>
                                                <small class="text-muted">
                                                    <i class="far fa-calendar me-1"></i>
                                                    {{ $meeting->date->format('d/m/Y') }}
                                                    à {{ $meeting->time->format('H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                        <a href="{{ route('company.evenements.meetings.show', $meeting->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <div class="avatar avatar-lg mb-3">
                                <div class="avatar-initial bg-label-secondary rounded">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <h5>Aucune réunion à venir</h5>
                            <p class="text-muted">Planifiez des réunions pour les voir apparaître ici</p>
                            <a href="{{ route('company.evenements.meetings.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Planifier une réunion
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dernières récompenses -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🏆 Dernières récompenses</h5>
                    <a href="{{ route('company.evenements.awards.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Nouvelle récompense
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentAwards->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentAwards as $award)
                                <div class="list-group-item border-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial bg-label-warning rounded">
                                                <i class="fas fa-trophy"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">{{ $award->employee->first_name }} {{ $award->employee->last_name }}</h6>
                                                <span class="badge bg-label-primary">{{ $award->awardType->name }}</span>
                                            </div>
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-gift me-1"></i> {{ $award->gift }}
                                                <span class="mx-2">•</span>
                                                <i class="far fa-calendar me-1"></i> {{ $award->date->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4">
                            <div class="avatar avatar-lg mb-3">
                                <div class="avatar-initial bg-label-secondary rounded">
                                    <i class="fas fa-trophy"></i>
                                </div>
                            </div>
                            <h5>Aucune récompense récente</h5>
                            <p class="text-muted">Récompensez vos employés pour leurs réalisations</p>
                            <a href="{{ route('company.evenements.awards.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Ajouter une récompense
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>
<script>
    // Données pour le graphique d'activité
    const activityData = @json($activityStats);
    
    // Initialisation du graphique d'activité
    function initActivityChart(months = 6) {
        const ctx = document.getElementById('activityChart').getContext('2d');
        
        // Filtrer les données pour le nombre de mois sélectionné
        const filteredData = activityData.slice(-months);
        
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: filteredData.map(item => item.label),
                datasets: [
                    {
                        label: 'Événements',
                        data: filteredData.map(item => item.events),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Réunions',
                        data: filteredData.map(item => item.meetings),
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Annonces',
                        data: filteredData.map(item => item.announcements),
                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
                        borderColor: 'rgba(139, 92, 246, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Récompenses',
                        data: filteredData.map(item => item.awards),
                        backgroundColor: 'rgba(245, 158, 11, 0.7)',
                        borderColor: 'rgba(245, 158, 11, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
        
        return chart;
    }
    
    // Initialisation du mini-calendrier
    function initMiniCalendar() {
        const calendarEl = document.getElementById('miniCalendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            locale: 'fr',
            firstDay: 1,
            height: 'auto',
            events: @json($currentMonthEvents),
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            },
            eventContent: function(arg) {
                return {
                    html: `<div class="fc-event-title" style="white-space: normal; font-size: 0.7rem; line-height: 1.1; padding: 1px 2px;">${arg.event.title}</div>`
                };
            }
        });
        
        calendar.render();
        return calendar;
    }
    
    // Initialisation au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser le graphique d'activité
        let activityChart = initActivityChart();
        
        // Gérer le changement de période pour le graphique d'activité
        document.querySelectorAll('[data-months]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const months = parseInt(this.getAttribute('data-months'));
                
                // Mettre à jour le bouton actif
                document.querySelectorAll('[data-months]').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
                
                // Détruire l'ancien graphique et en créer un nouveau
                if (activityChart) {
                    activityChart.destroy();
                }
                activityChart = initActivityChart(months);
            });
        });
        
        // Initialiser le mini-calendrier
        initMiniCalendar();
        
        // Animation des cartes au survol
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card-hover');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
            });
        });
    });
</script>
@endpush

@endsection
