@extends('layouts.app')

@section('title', 'Dashboard RH - Gestion des Employés')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête du Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1"> Tableau de bord  RH</h4>
                    <p class="text-muted mb-0">Vue d'ensemble de la gestion des employés</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->translatedFormat('l d F Y') }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                        <i class="fas fa-refresh me-1"></i>Actualiser
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar me-1"></i>Période
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="setPeriod('today')">Aujourd'hui</a></li>
                            <li><a class="dropdown-item" href="#" onclick="setPeriod('week')">Cette semaine</a></li>
                            <li><a class="dropdown-item" href="#" onclick="setPeriod('month')">Ce mois</a></li>
                            <li><a class="dropdown-item" href="#" onclick="setPeriod('year')">Cette année</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métriques principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-primary rounded">
                            <i class="fas fa-users fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-primary">{{ $stats['total_employees'] ?? 0 }}</h3>
                    <p class="text-muted mb-2">Total Employés</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-info rounded">
                            <i class="fas fa-calendar fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-info">{{ $stats['total_monthly'] ?? 0 }}</h3>  
                    <p class="text-muted mb-2">Employés Mensuels</p>
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
                    <h3 class="mb-1 text-warning">{{ $stats['total_daily'] ?? 0 }}</h3>
                    <p class="text-muted mb-2">Employés Journaliers</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                        <div class="avatar-initial bg-label-success rounded">
                            <i class="fas fa-dollar fa-28px"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 text-success">{{ number_format($totalSalary ?? 0, 0, ',', ' ') }}</h3>
                    <p class="text-muted mb-2">Masse Salariale (FCFA)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques supplémentaires -->
    <div class="row mb-4">
        <!-- Répartition par secteur -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"> Répartition par Services</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 250px;">
                        <canvas id="sectorChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Répartition par statut matrimonial -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Statut Matrimonial</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 250px;">
                        <canvas id="maritalStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top départements -->
        <div class="col-xl-4 col-lg-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"> Top Départements</h5>
                    <span class="badge bg-label-primary">Top 5</span>
                </div>
                <div class="card-body">
                    @php
                        $totalEmployees = $topDepartments->sum('employee_count');
                        $colors = ['primary', 'info', 'warning', 'success', 'secondary'];
                    @endphp
                    
                    @foreach($topDepartments as $index => $department)
                        <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 1px solid #e0e0e0; padding-bottom: 0.5rem;">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2" style="width: 35px; height: 35px;">
                                    <div class="avatar-initial bg-label-{{ $colors[$index] ?? 'secondary' }} rounded">{{ $index + 1 }}</div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $department->department }}</h6>
                                    <small class="text-muted">{{ $department->employee_count }} employés</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 text-{{ $colors[$index] ?? 'secondary' }}">{{ $department->employee_count }}</h6>
                                <div class="progress" style="width: 60px;">
                                    <div class="progress-bar bg-{{ $colors[$index] ?? 'secondary' }}" style="width: {{ $totalEmployees > 0 ? ($department->employee_count / $totalEmployees * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if($topDepartments->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-building fa-2x mb-2"></i>
                            <p>Aucun département trouvé</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions Rapides</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('company.employees.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-1"></i>Ajouter Employé Mensuel
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-1"></i>Ajouter Employé Journalier
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('company.employees.index') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-list me-1"></i>Voir Tous les Employés
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="#" class="btn btn-outline-success w-100" onclick="exportReport()">
                                <i class="fas fa-download me-1"></i>Exporter Rapport
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialisation des graphiques
        initSectorChart();
        initMaritalStatusChart();
    });

    // Graphique de répartition par secteur
    function initSectorChart() {
        const ctx = document.getElementById('sectorChart');
        if (ctx) {
            const sectorData = @json($sectorStats);
            
            // Vérifier si les données existent
            if (!sectorData || sectorData.length === 0) {
                ctx.parentElement.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-chart-pie fa-2x mb-2"></i><p>Aucune donnée disponible</p></div>';
                return;
            }
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: sectorData.map(item => item.sector),
                    datasets: [{
                        data: sectorData.map(item => item.count),
                        backgroundColor: [
                            '#696cff',
                            '#71dd37',
                            '#ff3e1d',
                            '#03c3ec',
                            '#6f42c1'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Graphique de statut matrimonial
    function initMaritalStatusChart() {
        const ctx = document.getElementById('maritalStatusChart');
        if (ctx) {
            const maritalData = @json($maritalStats);
            
            // Vérifier si les données existent
            if (!maritalData || maritalData.length === 0) {
                ctx.parentElement.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-chart-pie fa-2x mb-2"></i><p>Aucune donnée disponible</p></div>';
                return;
            }
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: maritalData.map(item => item.status),
                    datasets: [{
                        data: maritalData.map(item => item.count),
                        backgroundColor: [
                            '#696cff',
                            '#71dd37',
                            '#ffab00',
                            '#8592a3'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    // Fonction pour exporter le rapport
    function exportReport() {
        // Logique d'exportation du rapport
        alert('Fonctionnalité d\'exportation en cours de développement...');
    }
</script>
@endpush
