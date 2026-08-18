@extends('layouts.super-admin')

@section('title', 'Activités récentes - RH Flow')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Activités récentes</h4>
                <p class="text-muted mb-0">Historique complet des activités du système</p>
            </div>
            <div>
                <a href="{{ route('super-admin.dashboard') }}" class="btn btn-primary bg-label-primary">
                    <i class="ti ti-arrow-left me-2"></i>
                    Retour au dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Toutes les activités</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm" onclick="refreshActivities()">
                        <i class="ti ti-refresh me-1"></i>
                        Actualiser
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i>
                            Filtrer
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="filterActivities('all')">Toutes</a>
                            <a class="dropdown-item" href="#" onclick="filterActivities('login')">Connexions</a>
                            <a class="dropdown-item" href="#" onclick="filterActivities('employee')">Employés</a>
                            <a class="dropdown-item" href="#" onclick="filterActivities('company')">Entreprises</a>
                            <a class="dropdown-item" href="#" onclick="filterActivities('plan')">Plans</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="timeline" id="activities-container">
                    @foreach($all_activities as $activity)
                    <div class="timeline-item timeline-item-transparent activity-item" data-type="{{ $activity['type'] }}">
                        <span class="timeline-point timeline-point-{{ $activity['color'] }}">
                            <i class="{{ $activity['icon'] }}"></i>
                        </span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-2">
                                <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                <small class="text-muted">
                                    <i class="ti ti-clock me-1"></i>
                                    {{ $activity['time'] }}
                                </small>
                            </div>
                            <p class="mb-2">{{ $activity['description'] }}</p>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-{{ $activity['color'] }} fs-tiny">
                                    {{ ucfirst($activity['type']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(count($all_activities) == 0)
                <div class="text-center py-5">
                    <div class="avatar avatar-xl mb-3 mx-auto">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="ti ti-activity ti-lg"></i>
                        </span>
                    </div>
                    <h5 class="mb-2">Aucune activité récente</h5>
                    <p class="text-muted mb-4">Les activités apparaîtront ici dès qu'elles se produiront.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
        padding-left: 3rem;
        transition: all 0.3s ease;
    }

    .timeline-item:hover {
        background-color: rgba(105, 108, 255, 0.04);
        border-radius: 0.375rem;
        padding-left: 3.5rem;
    }

    .timeline-point {
        position: absolute;
        left: 0.75rem;
        top: 0.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    .timeline-point i {
        font-size: 0.5rem;
    }

    .timeline-point-primary {
        background: #696cff;
        color: white;
    }

    .timeline-point-success {
        background: #71dd37;
        color: white;
    }

    .timeline-point-info {
        background: #03c3ec;
        color: white;
    }

    .timeline-point-warning {
        background: #ffab00;
        color: #8b5d00;
    }

    .timeline-event {
        position: relative;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .activity-item {
        border-bottom: 1px solid #f1f1f4;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .activity-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    function refreshActivities() {
        // Simulation de rechargement des activités
        const container = document.getElementById('activities-container');

        // Animation de chargement
        container.style.opacity = '0.6';
        container.style.pointerEvents = 'none';

        setTimeout(() => {
            container.style.opacity = '1';
            container.style.pointerEvents = 'auto';

            // Afficher une notification
            showAlert('Activités actualisées avec succès', 'success');
        }, 1000);
    }

    function filterActivities(type) {
        const items = document.querySelectorAll('.activity-item');

        items.forEach(item => {
            const itemType = item.dataset.type;

            if (type === 'all' || itemType === type) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });

        // Mettre à jour le bouton actif
        const filterButtons = document.querySelectorAll('.dropdown-item');
        filterButtons.forEach(button => {
            button.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    function showAlert(message, type) {
        // Créer l'alerte
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alert);

        // Supprimer automatiquement après 3 secondes
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 3000);
    }
</script>
@endpush
