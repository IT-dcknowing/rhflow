@extends('layouts.super-admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-stats-up me-2"></i>
                        Rapports et Analytics
                    </h5>
                    <small class="text-muted">Statistiques sur les entreprises et les utilisateurs</small>
                </div>
                <div class="d-flex gap-2">
                    <form id="reportHeaderForm" method="GET" action="{{ route('super-admin.reports.index') }}" class="d-flex gap-2">
                        <select class="form-select" id="periodSelect" name="period" style="width: auto;">
                            <option value="7" {{ request('period',30)==7 ? 'selected' : '' }}>7 derniers jours</option>
                            <option value="30" {{ request('period',30)==30 ? 'selected' : '' }}>30 derniers jours</option>
                            <option value="90" {{ request('period',30)==90 ? 'selected' : '' }}>3 derniers mois</option>
                            <option value="365" {{ request('period',30)==365 ? 'selected' : '' }}>Cette année</option>
                        </select>
                        <button class="btn btn-outline-primary bg-label-primary" type="submit">
                            <i class="ti ti-filter me-2"></i>Appliquer
                        </button>
                    </form>
                    <div class="dropdown">
                        <button class="btn btn-outline-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-download me-2"></i>Exporter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('super-admin.reports.export.xlsx', request()->query()) }}">
                                <i class="ti ti-file-spreadsheet me-2"></i>Excel (CSV)
                            </a>
                            <a class="dropdown-item" href="{{ route('super-admin.reports.export.pdf', request()->query()) }}">
                                <i class="ti ti-file-text me-2"></i>PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Métriques clés -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-1 text-white">{{ formatPrice($reportData['total_revenue']) }}</h4>
                                                <small>Revenus Totaux</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="ti ti-currency-euro fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="opacity-75">
                                                @php $dr = $reportData['delta_revenue'] ?? 0; @endphp
                                                <i class="ti {{ ($dr ?? 0) >= 0 ? 'ti-trend-up' : 'ti-trend-down' }} me-1"></i>
                                                {{ ($dr >= 0 ? '+' : '') . $dr }}% vs période précédente
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-1 text-white">{{ $reportData['total_companies'] ?? '0' }}</h4>
                                                <small>Entreprises Actives</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="ti ti-building fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="opacity-75">
                                                @php $dc = $reportData['delta_companies'] ?? 0; @endphp
                                                <i class="ti {{ ($dc ?? 0) >= 0 ? 'ti-trend-up' : 'ti-trend-down' }} me-1"></i>
                                                {{ ($dc >= 0 ? '+' : '') . $dc }}% vs période précédente
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-1 text-white">{{ $reportData['total_users'] ?? '0' }}</h4>
                                                <small>Utilisateurs Système</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="ti ti-users fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="opacity-75">
                                                @php $du = $reportData['delta_users'] ?? 0; @endphp
                                                <i class="ti {{ ($du ?? 0) >= 0 ? 'ti-trend-up' : 'ti-trend-down' }} me-1"></i>
                                                {{ ($du >= 0 ? '+' : '') . $du }}% vs période précédente
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-1 text-white">{{ $reportData['conversion_rate_period'] ?? ($reportData['conversion_rate'] ?? '0') }}%</h4>
                                                <small>Taux de Conversion</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="ti ti-percentage fs-1"></i>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <small class="opacity-75">
                                                Période sélectionnée
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtres avancés -->
                        <form id="reportFiltersForm" method="GET" action="{{ route('super-admin.reports.index') }}" class="row mb-4 g-2">
                            <div class="col-md-3">
                                <select class="form-select" id="metricType" name="metricType">
                                    <option value="all">Toutes les métriques</option>
                                    <option value="revenue">Revenus</option>
                                    <option value="users">Utilisateurs</option>
                                    <option value="companies">Entreprises</option>
                                    <option value="plans">Plans</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="startDate" name="startDate" value="{{ request('startDate', date('Y-m-d', strtotime('-30 days'))) }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="endDate" name="endDate" value="{{ request('endDate', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-outline-warning w-100" onclick="applyFilters(event)">
                                    <i class="ti ti-filter me-2"></i>
                                    Appliquer les Filtres
                                </button>
                            </div>
                        </form>

                        <!-- Graphiques -->
                        <div class="row mb-4">
                            <div class="col-lg-8 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="card-title m-0">Évolution des Revenus</h6>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="ti ti-calendar me-1"></i>
                                                Période
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" onclick="setChartPeriod('week')">Cette semaine</a>
                                                <a class="dropdown-item" href="#" onclick="setChartPeriod('month')">Ce mois</a>
                                                <a class="dropdown-item" href="#" onclick="setChartPeriod('quarter')">Ce trimestre</a>
                                                <a class="dropdown-item" href="#" onclick="setChartPeriod('year')">Cette année</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="revenueChart" height="300"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title m-0">Répartition par Packs</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="planDistributionChart" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau détaillé -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="card-title m-0">Détail des Entreprises</h6>
                                        <div class="d-flex gap-2">
                                            <input type="text" class="form-control form-control-sm" id="tableSearch" placeholder="Rechercher..." style="width: 200px;">
                                            <select class="form-select form-select-sm" id="tableFilter" style="width: auto;">
                                                <option value="">Tous</option>
                                                <option value="active">Actives</option>
                                                <option value="trial">Essai</option>
                                                <option value="expired">Expirées</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="reportsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Entreprise</th>
                                                        <th>Pack</th>
                                                        <th>Statut</th>
                                                        <th>Employés</th>
                                                        <th>Stockage</th>
                                                        <th>Date création</th>
                                                        <th>Revenus</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($reportData['companies'] ?? [] as $company)
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-sm me-3">
                                                                    <span class="avatar-initial bg-primary rounded-circle">{{ substr($company['name'], 0, 1) }}</span>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0">{{ $company['name'] }}</h6>
                                                                    <small class="text-muted">{{ $company['email'] }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-{{ $company['plan_name'] == 'Gratuit' ? 'primary' : ($company['plan_name'] == 'Basic' ? 'info' :($company['plan_name'] == 'Pro' ? 'success' : 'danger')) }}">{{ $company['plan_name'] }}</span>
                                                        </td>
                                                        <td>
                                                            @if($company['is_active'])
                                                                <span class="badge bg-success">Actif</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactif</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="fw-semibold">{{ $company['employee_count'] ?? 0 }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="progress me-2" style="width: 60px; height: 6px;">
                                                                    <div class="progress-bar bg-info" style="width: {{ $company['storage_usage'] ?? 0 }}%"></div>
                                                                </div>
                                                                <small>{{ $company['storage_used'] ?? '0' }} GB</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <small>{{ $company['created_at']->format('d/m/Y') }}</small>
                                                        </td>
                                                        <td>
                                                            <strong>{{ formatPrice($company['revenue'] ?? '0') }}</strong>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-icon btn-sm btn-label-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                                    <i class="ti ti-menu"></i>
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    <a class="dropdown-item" href="{{ route('super-admin.enterprises.show', $company['id']) }}">
                                                                        <i class="ti ti-eye me-2"></i>
                                                                        Voir détails
                                                                    </a>
                                                                    <a class="dropdown-item" href="{{ route('super-admin.enterprises.showactivity', $company['id']) }}">
                                                                        <i class="ti ti-bar-chart me-2"></i>
                                                                        Rapport détaillé
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center py-4">
                                                            <i class="ti ti-chart-off fs-1 text-muted mb-3 d-block"></i>
                                                            <h5 class="text-muted">Aucune donnée disponible</h5>
                                                            <p class="text-muted">Les données apparaîtront ici une fois que des entreprises seront créées.</p>
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Métriques avancées -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title m-0">Croissance Mensuelle</h6>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="growthChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title m-0">Top Entreprises</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            @forelse($reportData['top_companies'] ?? [] as $company)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial bg-primary rounded-circle">{{ substr($company['name'], 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $company['name'] }}</h6>
                                                        <small class="text-muted">{{ $company['plan_name'] }}</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <strong>{{ formatPrice($company['revenue']) }}</strong><br>
                                                    <small class="text-muted">{{ $company['employee_count'] }} employés</small>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="list-group-item text-center py-4">
                                                <small class="text-muted">Aucune entreprise à afficher</small>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(38, 61, 136, 0.1);
    border: 1px solid #e9ecef;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    border-color: #e9ecef;
}

.badge {
    font-size: 0.75rem;
}

.avatar-initial {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2.5rem;
    height: 2.5rem;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 50%;
}

.progress {
    height: 6px;
}

.dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(38, 61, 136, 0.15);
    border: none;
}

.btn-outline-secondary:hover {
    background-color: #263d88;
    border-color: #263d88;
    color: white;
}

.section-title {
    color: #263d88;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

#tableSearch {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

#tableSearch:focus {
    border-color: #263d88;
    box-shadow: 0 0 0 0.2rem rgba(38, 61, 136, 0.25);
}

.form-select-sm:focus {
    border-color: #263d88;
    box-shadow: 0 0 0 0.2rem rgba(38, 61, 136, 0.25);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche en temps réel dans le tableau
    const tableSearch = document.getElementById('tableSearch');
    const tableFilter = document.getElementById('tableFilter');

    function filterTable() {
        const searchTerm = tableSearch.value.toLowerCase();
        const filterValue = tableFilter.value;
        const rows = document.querySelectorAll('#reportsTable tbody tr');

        rows.forEach(row => {
            if (row.cells.length < 8) return; // Skip header or empty rows

            const companyName = row.cells[0].textContent.toLowerCase();
            const email = row.cells[0].querySelector('small').textContent.toLowerCase();
            const status = row.cells[2].textContent.toLowerCase().trim();

            const matchesSearch = companyName.includes(searchTerm) || email.includes(searchTerm);
            const matchesFilter = !filterValue || status.includes(filterValue);

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    tableSearch.addEventListener('input', filterTable);
    tableFilter.addEventListener('change', filterTable);

    // Application des filtres: soumettre le formulaire GET
    window.applyFilters = function(e) {
        if (e) e.preventDefault();
        const form = document.getElementById('reportFiltersForm');
        if (form) form.submit();
    };

    // L'export est géré par les liens (routes) avec conservation des filtres

    // Configuration du graphique de revenus
    if (typeof Chart !== 'undefined') {
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(($reportData['revenue_series']['labels'] ?? [])) !!},
                    datasets: [{
                        label: 'Revenus',
                        data: {!! json_encode(($reportData['revenue_series']['data'] ?? [])) !!},
                        borderColor: '#263d88',
                        backgroundColor: 'rgba(38, 61, 136, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(ctx){
                                    const val = ctx.parsed.y ?? 0;
                                    return 'Revenus: ' + new Intl.NumberFormat('fr-FR').format(val);
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Graphique de répartition des plans
        const planCtx = document.getElementById('planDistributionChart');
        if (planCtx) {
            new Chart(planCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(collect($reportData['plan_distribution'] ?? [])->pluck('plan')) !!},
                    datasets: [{
                        data: {!! json_encode(collect($reportData['plan_distribution'] ?? [])->pluck('count')) !!},
                        backgroundColor: [
                            '#263d88', '#3d5aa6', '#5d7bc4', '#7d9ce2', '#9dbdff',
                            '#2b8a3e', '#e8590c', '#d6336c', '#1971c2', '#0b7285'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const label = ctx.label || '';
                                    const val = ctx.parsed;
                                    const total = ctx.chart.data.datasets[0].data.reduce((a,b)=>a+b,0) || 1;
                                    const pct = Math.round((val/total)*1000)/10;
                                    return `${label}: ${val} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Graphique de croissance
        const growthCtx = document.getElementById('growthChart');
        if (growthCtx) {
            new Chart(growthCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(($reportData['growth_series']['labels'] ?? [])) !!},
                    datasets: [{
                        label: 'Nouvelles entreprises',
                        data: {!! json_encode(($reportData['growth_series']['data'] ?? [])) !!},
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: '#28a745',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }

    // Fonction utilitaire pour afficher les alertes
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="ti ti-info-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        const container = document.querySelector('.card-body');
        const existingAlert = container.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Insérer après les filtres
        const filtersRow = container.querySelector('.row');
        filtersRow.insertAdjacentHTML('afterend', alertHtml);

        // Auto-dismiss après 3 secondes
        setTimeout(() => {
            const alert = container.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 3000);
    }
});
</script>
@endpush
