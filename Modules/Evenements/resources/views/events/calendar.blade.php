@extends('layouts.app')

@section('title', 'Calendrier des Événements - RH Flow')

@push('styles')
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <style>
        .fc {
            font-family: inherit;
        }

        .fc-toolbar-title {
            font-size: 1.2rem !important;
            font-weight: 600;
        }

        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            font-size: 0.78rem;
            padding: 2px 4px;
        }

        .fc-daygrid-event-dot {
            display: none;
        }

        .calendar-wrapper {
            background: #fff;
            border-radius: 0.5rem;
            padding: 1.25rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .event-detail-panel {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            padding: 1.25rem;
            min-height: 200px;
        }

        .event-detail-panel .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            color: #a1acb8;
        }

        .event-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            font-size: 0.82rem;
            margin-bottom: 6px;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📅 Calendrier des Événements</h4>
                    <p class="text-muted mb-0">Visualisez tous les événements de votre entreprise</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.evenements.events.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-1"></i>Vue Liste
                    </a>
                    <a href="{{ route('company.evenements.events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvel Événement
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Calendrier principal -->
        <div class="col-xl-9 col-lg-8 mb-4">
            <div class="calendar-wrapper">
                <!-- Filtres rapides par type -->
                <div class="d-flex flex-wrap gap-2 mb-3 pb-3 border-bottom">
                    <button class="btn btn-sm btn-outline-secondary active" id="btnAllTypes" onclick="filterByType(null, this)">
                        <i class="fas fa-globe me-1"></i>Tous
                    </button>
                    @foreach($eventTypes as $type)
                        <button class="btn btn-sm btn-outline-secondary" 
                                onclick="filterByType({{ $type->id }}, this)"
                                style="border-color: {{ $type->color ?? '#6c757d' }}; color: {{ $type->color ?? '#6c757d' }}">
                            <span class="event-dot" style="background: {{ $type->color ?? '#6c757d' }}"></span>
                            {{ $type->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Calendrier FullCalendar -->
                <div id="mainCalendar"></div>
            </div>
        </div>

        <!-- Panneau latéral -->
        <div class="col-xl-3 col-lg-4 mb-4">
            <!-- Détail de l'événement sélectionné -->
            <div class="event-detail-panel mb-4">
                <h6 class="fw-semibold mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Détails
                </h6>
                <div id="eventDetail">
                    <div class="empty-state">
                        <i class="fas fa-hand-pointer fa-2x mb-2"></i>
                        <small>Cliquez sur un événement pour voir ses détails</small>
                    </div>
                </div>
            </div>

            <!-- Légende des types -->
            <div class="event-detail-panel mb-4">
                <h6 class="fw-semibold mb-3">
                    <i class="fas fa-tag me-2 text-info"></i>Légende
                </h6>
                @foreach($eventTypes as $type)
                    <div class="legend-item">
                        <span class="event-dot" style="background: {{ $type->color ?? '#6c757d' }}"></span>
                        {{ $type->name }}
                    </div>
                @endforeach
                <div class="legend-item">
                    <span class="event-dot" style="background: #6c757d"></span>
                    Non catégorisé
                </div>
            </div>

            <!-- Mini stats du mois -->
            <div class="event-detail-panel">
                <h6 class="fw-semibold mb-3">
                    <i class="fas fa-chart-bar me-2 text-success"></i>Ce mois
                </h6>
                <div id="monthStats">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">Événements affichés</small>
                        <span class="badge bg-label-primary" id="eventsCount">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal détail rapide -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modalHeader">
                <h5 class="modal-title" id="modalTitle">Détail de l'événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Contenu dynamique -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" id="modalViewBtn" class="btn btn-primary">
                    <i class="fas fa-eye me-1"></i>Voir Détails
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales/fr.global.min.js"></script>

    <script>
        let calendar;
        let allEvents = [];
        let activeTypeFilter = null;

        const calendarEndpoint = "{{ route('company.evenements.api.events.index') }}";
        const showRouteBase = "{{ url('company/evenements/events') }}";

        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('mainCalendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: '{{ $view ?? 'dayGridMonth' }}',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste'
                },
                height: 'auto',
                selectable: true,
                selectMirror: true,
                dayMaxEvents: 3,
                navLinks: true,
                businessHours: true,
                events: function (info, successCallback, failureCallback) {
                    fetch(calendarEndpoint + '?start=' + info.startStr + '&end=' + info.endStr, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        allEvents = data;
                        updateMonthStats(data);
                        const filtered = activeTypeFilter
                            ? data.filter(e => e.extendedProps && e.extendedProps.type_id == activeTypeFilter)
                            : data;
                        successCallback(filtered);
                    })
                    .catch(err => {
                        console.warn('Erreur chargement événements:', err);
                        successCallback([]);
                    });
                },
                eventClick: function (info) {
                    showEventDetail(info.event);
                },
                select: function (info) {
                    // Redirection vers la création avec la date pré-remplie
                    window.location.href = "{{ route('company.evenements.events.create') }}?start_date=" + info.startStr;
                },
                eventDidMount: function (info) {
                    // Tooltip Bootstrap
                    const title = info.event.title;
                    info.el.setAttribute('title', title);
                    info.el.setAttribute('data-bs-toggle', 'tooltip');
                    new bootstrap.Tooltip(info.el, { trigger: 'hover' });
                }
            });

            calendar.render();
        });

        function filterByType(typeId, btn) {
            // Mise à jour visuelle des boutons
            document.querySelectorAll('[onclick^="filterByType"]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            activeTypeFilter = typeId;
            calendar.refetchEvents();
        }

        function showEventDetail(event) {
            const props = event.extendedProps || {};
            const startDate = event.start ? event.start.toLocaleDateString('fr-FR', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            }) : 'N/A';
            const startTime = event.start ? event.start.toLocaleTimeString('fr-FR', {
                hour: '2-digit', minute: '2-digit'
            }) : '';
            const endDate = event.end ? event.end.toLocaleDateString('fr-FR', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            }) : '';

            const color = event.backgroundColor || '#6c757d';
            const eventId = event.id;
            const viewUrl = showRouteBase + '/' + eventId;

            // Panel latéral
            document.getElementById('eventDetail').innerHTML = `
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <span style="width:14px;height:14px;border-radius:50%;background:${color};display:inline-block;margin-right:8px;flex-shrink:0"></span>
                        <h6 class="mb-0 fw-semibold">${event.title}</h6>
                    </div>
                    <small class="text-muted d-block mb-1"><i class="fas fa-calendar me-1"></i>${startDate}</small>
                    ${startTime ? `<small class="text-muted d-block mb-1"><i class="fas fa-clock me-1"></i>${startTime}${endDate ? ' → ' + endDate : ''}</small>` : ''}
                    ${props.location ? `<small class="text-muted d-block mb-1"><i class="fas fa-map-marker-alt me-1"></i>${props.location}</small>` : ''}
                    ${props.description ? `<p class="text-muted small mt-2 mb-2">${props.description.substring(0, 120)}${props.description.length > 120 ? '...' : ''}</p>` : ''}
                    <a href="${viewUrl}" class="btn btn-sm btn-primary w-100 mt-2">
                        <i class="fas fa-eye me-1"></i>Voir les détails
                    </a>
                </div>
            `;

            // Modal mobile / grand écran
            document.getElementById('modalHeader').style.borderLeftColor = color;
            document.getElementById('modalHeader').style.borderLeftWidth = '4px';
            document.getElementById('modalHeader').style.borderLeftStyle = 'solid';
            document.getElementById('modalTitle').textContent = event.title;
            document.getElementById('modalViewBtn').href = viewUrl;
            document.getElementById('modalBody').innerHTML = `
                <p><i class="fas fa-calendar text-primary me-2"></i>${startDate}</p>
                ${startTime ? `<p><i class="fas fa-clock text-info me-2"></i>${startTime}${endDate ? ' → ' + endDate : ''}</p>` : ''}
                ${props.location ? `<p><i class="fas fa-map-marker-alt text-danger me-2"></i>${props.location}</p>` : ''}
                ${props.status ? `<p><i class="fas fa-circle me-2" style="color:${color}"></i>Statut : ${props.status}</p>` : ''}
                ${props.description ? `<hr><p class="text-muted small">${props.description}</p>` : ''}
            `;
        }

        function updateMonthStats(events) {
            document.getElementById('eventsCount').textContent = events.length;
        }
    </script>
@endpush
