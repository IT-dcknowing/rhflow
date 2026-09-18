@extends('layouts.app')

@section('title', 'Tableau de bord RH - Gestion des Employés')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
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
            </div>
        </div>
    </div>

    <!-- Métriques principales -->
    <x-kpi-grid title="Métriques principales">
        <x-kpi icon="fas fa-users" color="primary" label="Total" sublabel="Employés"
            :value="$stats['total_employees'] ?? 0" />

        <x-kpi icon="fas fa-calendar" color="info" label="Mensuels" sublabel="Employés"
            :value="$stats['total_monthly'] ?? 0" />

        <x-kpi icon="fas fa-clock" color="warning" label="Journaliers" sublabel="Employés"
            :value="$stats['total_daily'] ?? 0" />

        <x-kpi icon="fas fa-dollar" color="success" label="Masse Salariale" sublabel="FCFA"
            :value="number_format($totalSalary ?? 0, 0, ',', ' ')" />
    </x-kpi-grid>

    <!-- Graphiques supplémentaires -->
    <div class="row mb-4">
        <!-- Répartition par secteur -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"> Répartition par Services</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 280px;">
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
                    <div class="chart-container" style="position: relative; height: 280px;">
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
                        $baseTotal = ($stats['total_employees'] ?? 0) > 0 ? $stats['total_employees'] : $totalEmployees;
                        $colors = ['primary', 'info', 'warning', 'success', 'secondary'];
                    @endphp
                    
                    @foreach($topDepartments as $index => $department)
                        @php
                            $percentage = $baseTotal > 0 ? round(($department->employee_count / $baseTotal) * 100, 1) : 0;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 1px solid #e0e0e0; padding-bottom: 0.5rem;">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2" style="width: 35px; height: 35px;">
                                    <div class="avatar-initial bg-label-{{ $colors[$index] ?? 'secondary' }} rounded">{{ $index + 1 }}</div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $department->department }}</h6>
                                    <small class="text-muted">{{ $department->employee_count }} employé{{ $department->employee_count > 1 ? 's' : '' }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 text-{{ $colors[$index] ?? 'secondary' }} fw-bold">{{ $percentage }}%</h6>
                                <div class="progress" style="width: 70px; height: 6px;">
                                    <div class="progress-bar bg-{{ $colors[$index] ?? 'secondary' }}" style="width: {{ $percentage }}%"></div>
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
    <x-quick-actions>
        <x-quick-action icon="fas fa-user-plus"
            label="Nouvel Employé (Mensuel)"
            :href="isModuleActive('employee') ? route('company.employees.create') : '#'" />

        <x-quick-action icon="fas fa-user-plus"
            label="Nouvel Employé (Journalier)"
            :href="isModuleActive('employee') ? route('company.employees.create') : '#'" />

        <x-quick-action icon="fas fa-dollar"
            label="Générer la paie (Mois en cours)"
            :href="isModuleActive('salary') ? route('company.paiesalaries.exercices.index') : '#'" />

        {{-- Deuxième ligne : actions secondaires à gauche. --}}
        <x-slot:secondary>
            <x-quick-action icon="fas fa-calendar"
                label="Approuver les congés"
                :href="isModuleActive('leaves') ? route('company.leaves.index') : '#'"
                variant="outline" color="warning" />

            <x-quick-action icon="fas fa-file-text"
                label="Rapport Mensuel"
                :href="isModuleActive('Declarations') ? route('company.declarations.livrepaie.mensuel') : '#'"
                variant="outline" />
        </x-slot:secondary>

        {{-- ... et actions de service sans cadre, repoussées à droite. --}}
        <x-slot:end>
            <x-quick-action icon="fas fa-cog"
                label="Paramètres"
                :href="route('company.settings.settings')"
                variant="ghost" />

            <x-quick-action icon="fas fa-headset"
                label="Support"
                href="#"
                variant="ghost"
                :disabled="true" />

            <x-quick-action icon="fas fa-chart-bar"
                label="Analytics"
                :href="isModuleActive('PaieSalaries') ? route('company.paiesalaries.dashboard') : '#'"
                variant="ghost"
                :disabled="!isModuleActive('PaieSalaries')" />
        </x-slot:end>
    </x-quick-actions>
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
                    labels: sectorData.map(item => `${item.count} - ${item.sector}`),
                    datasets: [{
                        data: sectorData.map(item => item.count),
                        backgroundColor: [
                            '#253e87',
                            '#4f6cc8',
                            '#8aa0e0',
                            '#c7d2fe',
                            '#1f7a4d'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 5,
                            bottom: 10,
                            left: 5,
                            right: 5
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            align: 'center',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 12,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed || 0;
                                    return ` ${value} employé${value > 1 ? 's' : ''}`;
                                }
                            }
                        }
                    },
                    cutout: '65%'
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
                    labels: maritalData.map(item => `${item.count} - ${item.status}`),
                    datasets: [{
                        data: maritalData.map(item => item.count),
                        backgroundColor: [
                            '#253e87',
                            '#4f6cc8',
                            '#96650a',
                            '#a0a8c0'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 5,
                            bottom: 10,
                            left: 5,
                            right: 5
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            align: 'center',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                boxHeight: 8,
                                padding: 12,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed || 0;
                                    return ` ${value} employé${value > 1 ? 's' : ''}`;
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
