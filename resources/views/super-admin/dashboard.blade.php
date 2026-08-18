@extends('layouts.super-admin')

@section('title', 'Dashboard Super Admin - RH Flow')

@section('content')
<!-- Header Dashboard -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">👋 Bienvenue, {{ auth()->user()->name }} !</h3>
                <p class="text-muted mb-0">Voici un aperçu de votre système RH Flow</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary bg-label-primary" onclick="window.location.reload()">
                    <i class="ti ti-refresh me-2"></i>
                    Actualiser
                </button>
                <a href="{{ route('super-admin.enterprises.create') }}" class="btn btn-primary bg-primary text-white">
                    <i class="ti ti-plus me-2"></i>
                    Nouvelle Entreprise
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Messages de succès/erreur -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-check me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ti ti-alert-triangle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- Dashboard Cards -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Total Entreprises</p>
                        <div class="d-flex align-items-end">
                            <h4 class="card-title mb-0 me-2">{{ $stats['total_enterprises'] ?? 0 }}</h4>
                            <small class="text-{{ $stats['enterprise_growth_rate'] >= 0 ? 'success' : 'danger' }} fw-semibold">
                                <i class="ti ti-chevron-{{ $stats['enterprise_growth_rate'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($stats['enterprise_growth_rate']) }}%
                            </small>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-primary">{{ date('Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Employés Actifs</p>
                        <div class="d-flex align-items-end">
                            <h4 class="card-title mb-0 me-2">{{ $stats['total_employees'] ?? 0 }}</h4>
                            <small class="text-{{ $stats['employee_growth_rate'] >= 0 ? 'success' : 'danger' }} fw-semibold">
                                <i class="ti ti-chevron-{{ $stats['employee_growth_rate'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($stats['employee_growth_rate']) }}%
                            </small>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-success">Actif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Utilisateurs RH</p>
                        <div class="d-flex align-items-end">
                            <h4 class="card-title mb-0 me-2">{{ $stats['total_hr_users'] ?? 0 }}</h4>
                            <small class="text-{{ $stats['hr_user_growth_rate'] >= 0 ? 'success' : 'danger' }} fw-semibold">
                                <i class="ti ti-chevron-{{ $stats['hr_user_growth_rate'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($stats['hr_user_growth_rate']) }}%
                            </small>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-info">RH</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Total Utilisateurs</p>
                        <div class="d-flex align-items-end">
                            <h4 class="card-title mb-0 me-2">{{ $stats['total_users'] ?? 0 }}</h4>
                            <small class="text-{{ $stats['user_growth_rate'] >= 0 ? 'success' : 'danger' }} fw-semibold">
                                <i class="ti ti-chevron-{{ $stats['user_growth_rate'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs($stats['user_growth_rate']) }}%
                            </small>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-warning">Tous</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart and Recent Activity -->
<div class="row g-4 mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8 col-md-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Vue d'ensemble des revenus</h5>
                    <small class="text-muted">Évolution mensuelle</small>
                    @php
                        $total_current_year = array_sum($stats['revenue_data']['current_year']);
                        $total_last_year = array_sum($stats['revenue_data']['last_year']);
                        $yearly_growth = $total_last_year > 0 ? (($total_current_year - $total_last_year) / $total_last_year) * 100 : 0;
                    @endphp
                    <div class="mt-2">
                        <span class="badge bg-primary">Total {{ date('Y') }}: {{ number_format($total_current_year, 0, ',', ' ') }} FCFA</span>
                        @if($yearly_growth >= 0)
                            <span class="badge bg-success ms-2">+{{ number_format($yearly_growth, 1) }}%</span>
                        @else
                            <span class="badge bg-danger ms-2">{{ number_format($yearly_growth, 1) }}%</span>
                        @endif
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ti ti-calendar me-1"></i> Cette année
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0);">Cette semaine</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);">Ce mois</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0);">Cette année</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="col-lg-4 col-md-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Activité récente</h5>
                <a href="{{ route('super-admin.activities.index') }}" class="btn btn-sm bg-primary text-white">
                    <small>Voir tout</small>
                </a>
            </div>
            <div class="card-body pb-0">
                <ul class="timeline mb-0">
                    @foreach($stats['recent_activity'] as $activity)
                    <li class="timeline-item timeline-item-transparent">
                        <span class="timeline-point timeline-point-{{ $activity['color'] }}"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-1">
                                <h6 class="mb-0">{{ $activity['title'] }}</h6>
                                <small class="text-muted">{{ $activity['time'] }}</small>
                            </div>
                            <p class="mb-2">{{ $activity['description'] }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Actions rapides</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex align-items-center p-3 border rounded cursor-pointer hover-shadow">                           
                            <div class="avatar me-3">
                                <span class="avatar-initial bg-label-primary rounded">
                                    <i class="ti ti-bag ti-md"></i>
                                </span>
                            </div>
                            <div> 
                                <a href="{{route('super-admin.enterprises.create')}}">
                                    <h6 class="mb-0">Créer Entreprise</h6>
                                    <small class="text-muted">Ajouter nouvelle</small> 
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex align-items-center p-3 border rounded cursor-pointer hover-shadow">
                            <div class="avatar me-3">
                                <span class="avatar-initial bg-label-success rounded">
                                    <i class="ti ti-user ti-md"></i>
                                </span>
                            </div>
                            <div>
                                <a href="{{route('super-admin.commandes.index')}}">
                                    <h6 class="mb-0">Gérer les commandes</h6>
                                    <small class="text-muted">Commandes en attentes</small>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex align-items-center p-3 border rounded cursor-pointer hover-shadow">
                            <div class="avatar me-3">
                                <span class="avatar-initial bg-label-info rounded">
                                    <i class="ti ti-package ti-md"></i>
                                </span>
                            </div>
                            <div>
                                <a href="{{route('super-admin.packs.index')}}">
                                    <h6 class="mb-0">Gérer Packs</h6>
                                    <small class="text-muted">Plans & tarifs</small>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="d-flex align-items-center p-3 border rounded cursor-pointer hover-shadow">
                            <div class="avatar me-3">
                                <span class="avatar-initial bg-label-warning rounded">
                                    <i class="ti ti-notepad ti-md"></i>
                                </span>
                            </div>
                            <div>
                                <a href="{{route('super-admin.reports.index')}}">
                                    <h6 class="mb-0">Générer Rapport</h6>
                                    <small class="text-muted">Statistiques</small>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Entreprises récentes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Entreprises récentes</h5>
                    <small class="text-muted">Dernières entreprises ajoutées</small>
                </div>
                <a href="{{ route('super-admin.enterprises.index') }}" class="btn btn-primary btn-sm bg-primary text-white">
                    <i class="ti ti-eye me-1"></i>
                    Voir toutes
                </a>
            </div>
            <div class="card-body">
                @if(isset($stats['recent_enterprises']) && $stats['recent_enterprises']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Entreprise</th>
                                    <th>Contact</th>
                                    <th>Packs</th>
                                    <th>Employés</th>
                                    <th>Statut</th>
                                    <th>Date création</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_enterprises'] as $enterprise)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ strtoupper(substr($enterprise->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $enterprise->name }}</h6>
                                                    <small class="text-muted">{{ $enterprise->userName->username ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="mb-1">{{ $enterprise->email }}</div>
                                                <small class="text-muted">{{ $enterprise->phone ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $enterprise->companyPlan->name == 'Gratuit' ? 'primary' : ($enterprise->companyPlan->name == 'Basic' ? 'info' :($enterprise->companyPlan->name == 'Pro' ? 'success' : 'danger')) }}">
                                                {{ $enterprise->companyPlan->name ?? 'Gratuit' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-users text-muted me-2"></i>
                                                <span>{{ $enterprise->employees_count ?? 0 }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-{{ $enterprise->is_active ? 'success' : 'danger' }}">
                                                {{ $enterprise->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ $enterprise->created_at ? $enterprise->created_at->format('d/m/Y') : 'N/A' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $enterprise->created_at ? $enterprise->created_at->diffForHumans() : '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow bg-primary text-white" data-bs-toggle="dropdown">
                                                    <i class="ti ti-menu"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end" style="position: fixed; z-index: 1050;">
                                                    <a class="dropdown-item bg-label-primary" href="{{ route('super-admin.enterprises.show', $enterprise->user_id ?? $enterprise) }}">
                                                        <i class="ti ti-eye me-2"></i>
                                                        <span>Voir détails</span>
                                                    </a>
                                                    <a class="dropdown-item bg-label-primary" href="{{ route('super-admin.enterprises.edit', $enterprise->user_id ?? $enterprise) }}">
                                                        <i class="ti ti-pencil-alt2 me-2"></i>
                                                        <span>Modifier</span>
                                                    </a>
                                                    <a class="dropdown-item bg-label-primary" href="{{ route('super-admin.enterprises.users', $enterprise->user_id) }}">
                                                        <i class="ti ti-user me-2"></i>
                                                        <span>Gérer employés</span>
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    @if($enterprise->is_active == true)
                                                    <a class="dropdown-item bg-label-warning" href="{{ route('super-admin.enterprises.suspend', $enterprise->user_id ?? $enterprise) }}" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cette entreprise ?')">
                                                        <i class="ti ti-control-pause me-2"></i>
                                                        <span>Suspendre</span>
                                                    </a>
                                                    @else
                                                    <a class="dropdown-item bg-label-success" href="{{ route('super-admin.enterprises.activate', $enterprise->user_id ?? $enterprise) }}" onclick="return confirm('Êtes-vous sûr de vouloir activer cette entreprise ?')">
                                                        <i class="ti ti-control-play me-2"></i>
                                                        <span>Activer</span>
                                                    </a>
                                                    @endif
                                                    <a class="dropdown-item bg-label-danger" href="{{ route('super-admin.enterprises.delete', $enterprise->user_id ?? $enterprise) }}" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')">
                                                        <i class="ti ti-trash me-2"></i>
                                                        <span>Supprimer</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="avatar avatar-xl mb-3 mx-auto">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti ti-building ti-lg"></i>
                            </span>
                        </div>
                        <h5 class="mb-2">Aucune entreprise récente</h5>
                        <p class="text-muted mb-4">Les nouvelles entreprises apparaîtront ici.</p>
                        <a href="{{ route('super-admin.enterprises.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>Créer une entreprise
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>

    /* Timeline improvements */
    .timeline {
        position: relative;
        padding-left: 0;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
        padding-left: 2.5rem;
    }

    .timeline-item:not(:last-child):before {
        content: '';
        position: absolute;
        left: 0.625rem;
        top: 2rem;
        height: calc(100% - 1rem);
        width: 1px;
        background: #e7e7e7;
    }

    .timeline-point {
        position: absolute;
        left: 0.375rem;
        top: 0.5rem;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
    }

    .timeline-point-primary {
        background: #696cff;
    }

    .timeline-point-success {
        background: #71dd37;
    }

    .timeline-point-info {
        background: #03c3ec;
    }

    .timeline-point-warning {
        background: #ffab00;
    }

    .timeline-event {
        position: relative;
    }

    /* Avatar improvements */
    .avatar-initial {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    /* Badge improvements */
    .badge {
        font-weight: 500;
        padding: 0.375rem 0.75rem;
    }

    /* Chart container */
    #revenueChart {
        max-height: 280px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour actualiser les données du dashboard
        window.refreshDashboard = function() {
            fetch('{{ route("super-admin.dashboard") }}')
                .then(response => response.json())
                .then(data => {
                    // Actualiser les compteurs
                    document.querySelectorAll('[data-counter]').forEach(element => {
                        const counter = element.dataset.counter;
                        if (data.stats[counter] !== undefined) {
                            element.textContent = data.stats[counter];
                        }
                    });

                    // Actualiser les pourcentages
                    document.querySelectorAll('[data-growth]').forEach(element => {
                        const growth = element.dataset.growth;
                        if (data.stats[growth] !== undefined) {
                            const rate = data.stats[growth];
                            const isPositive = rate >= 0;
                            element.textContent = (isPositive ? '+' : '') + rate + '%';
                            element.className = 'text-' + (isPositive ? 'success' : 'danger') + ' fw-semibold';
                        }
                    });

                    // Actualiser le graphique
                    if (window.revenueChart) {
                        window.revenueChart.destroy();
                        createRevenueChart(data.stats.revenue_data);
                    }

                    showAlert('Données actualisées avec succès', 'success');
                })
                .catch(error => {
                    console.error('Erreur lors de l\'actualisation:', error);
                    showAlert('Erreur lors de l\'actualisation', 'danger');
                });
        };

        // Fonction pour créer le graphique des revenus
        window.createRevenueChart = function(revenueData) {
            const ctx = document.getElementById('revenueChart');
            if (ctx && window.Chart) {
                window.revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: revenueData.labels,
                        datasets: [{
                            label: 'Revenus {{ date("Y") }}',
                            data: revenueData.current_year,
                            borderColor: '#696cff',
                            backgroundColor: 'rgba(105, 108, 255, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }, {
                            label: 'Revenus {{ date("Y") - 1 }}',
                            data: revenueData.last_year,
                            borderColor: '#03c3ec',
                            backgroundColor: 'rgba(3, 195, 236, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += new Intl.NumberFormat('fr-FR', {
                                            style: 'currency',
                                            currency: 'XOF'
                                        }).format(context.parsed.y);
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('fr-FR') + ' FCFA';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        };

        // Créer le graphique initial
        createRevenueChart(@json($stats['revenue_data']));

        // Simulation de notifications en temps réel
        setTimeout(() => {
            const badge = document.querySelector('.badge-notifications');
            if (badge) {
                const currentCount = parseInt(badge.textContent);
                badge.textContent = currentCount + 1;
                
                // Animation pulse
                badge.classList.add('loading');
                setTimeout(() => {
                    badge.classList.remove('loading');
                }, 2000);
            }
        }, 10000);

        // Gestion des actions rapides
        document.querySelectorAll('.hover-shadow').forEach(element => {
            element.addEventListener('click', function() {
                const action = this.querySelector('h6').textContent;
                console.log('Action rapide:', action);
                // Ajouter la logique de navigation ou modal ici
            });
        });

        console.log('Dashboard Super Admin chargé avec succès ✅');
    });

    // Auto-dismiss notifications after 5 seconds
	const notifications = document.querySelectorAll('.alert-dismissible');
	notifications.forEach(function(notification) {
		setTimeout(function() {
			const bsAlert = new bootstrap.Alert(notification);
			bsAlert.close();
		}, 5000); // 5 seconds
	});
</script>
@endpush