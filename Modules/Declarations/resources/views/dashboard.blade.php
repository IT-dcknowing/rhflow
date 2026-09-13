@extends('layouts.app')

@section('title', 'Tableau de bord - Déclarations et Livres de Paie')

@push('css')
    <link rel="stylesheet" href="{{ asset('libs/apex-charts/apex-charts.css') }}">
    <style>
        .stat-card {
            border-left: 4px solid;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .border-left-primary {
            border-left-color: #696cff !important;
        }

        .border-left-success {
            border-left-color: #71dd37 !important;
        }

        .border-left-warning {
            border-left-color: #ffab00 !important;
        }

        .border-left-info {
            border-left-color: #03c3ec !important;
        }

        .border-left-danger {
            border-left-color: #ff3e1d !important;
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .priority-elevee {
            color: #ff3e1d;
        }

        .priority-moyenne {
            color: #ffab00;
        }

        .priority-normale {
            color: #71dd37;
        }

        .status-soumis {
            background-color: #e7f9e7;
            color: #2e7d32;
        }

        .status-encours {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .status-brouillon {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .status-valide {
            background-color: #e3f2fd;
            color: #1976d2;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Tableau de bord - Déclarations et Livres de Paie</h4>
                        <p class="text-muted mb-0">Gestion des livres de paie annuels, mensuels, individuels et déclarations
                            (ITS, CMU, CNPS)</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-calendar me-1"></i>Période
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Mois en cours</a></li>
                                <li><a class="dropdown-item" href="#">Trimestre</a></li>
                                <li><a class="dropdown-item" href="#">Année {{ $stats['exercice_encours'] }}</a></li>
                            </ul>
                        </div>
                        <!--<button class="btn btn-primary">-->
                        <!--    <i class="fas fa-plus me-1"></i>Nouvelle déclaration-->
                        <!--</button>-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes de statistiques principales -->
        <div class="row mb-4">
            <!-- Bulletins annuels -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1 text-muted">Bulletins annuels</span>
                                <h3 class="card-title mb-0">
                                    {{ number_format($stats['total_bulletins_annuel'], 0, ',', ' ') }}
                                </h3>
                                <div class="d-flex align-items-center mt-2">
                                    <small class="text-success me-1">
                                        <i class="fas fa-arrow-up"></i> 8.5%
                                    </small>
                                    <small class="text-muted">vs année dernière</small>
                                </div>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-file-invoice stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bulletins mensuels -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1 text-muted">Bulletins mensuels</span>
                                <h3 class="card-title mb-0">
                                    {{ number_format($stats['total_bulletins_mensuel'], 0, ',', ' ') }}
                                </h3>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="badge bg-success me-1">{{ $stats['periode_en_cours'] }}</span>
                                    <small class="text-muted">En cours</small>
                                </div>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-calendar-alt stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Déclarations CNPS -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-left-warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1 text-muted">Déclarations CNPS</span>
                                <h3 class="card-title mb-0">
                                    {{ number_format($stats['total_declarations_cnps'], 0, ',', ' ') }}
                                </h3>
                                <div class="d-flex align-items-center mt-2">
                                    <small class="text-warning me-1">
                                        <i class="fas fa-exclamation-triangle"></i> 2 en retard
                                    </small>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-building stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total déclarations fiscales -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1 text-muted">ITS + CMU</span>
                                <h3 class="card-title mb-0">
                                    {{ number_format($stats['total_declarations_its'] + $stats['total_declarations_cmu'], 0, ',', ' ') }}
                                </h3>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="badge bg-info me-1">ITS: {{ $stats['total_declarations_its'] }}</span>
                                    <span class="badge bg-info">CMU: {{ $stats['total_declarations_cmu'] }}</span>
                                </div>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-receipt stat-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique d'évolution et dernières activités -->
        <div class="row mb-4">
            <!-- Graphique d'évolution des déclarations -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Évolution des déclarations ({{ $stats['exercice_encours'] }})</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                Tous types
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">CNPS</a></li>
                                <li><a class="dropdown-item" href="#">ITS</a></li>
                                <li><a class="dropdown-item" href="#">CMU</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item active" href="#">Tous types</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="declarationsEvolutionChart" data-cnps="@json($declarationsEvolution['cnps'])"
                            data-its="@json($declarationsEvolution['its'])" data-cmu="@json($declarationsEvolution['cmu'])"
                            data-labels="@json($declarationsEvolution['labels'])" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- Échéances à venir -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Échéances à venir</h5>
                        <span class="badge bg-label-danger">{{ $echeancesAvenir->count() }} urgent(es)</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($echeancesAvenir as $echeance)
                                <div class="list-group-item border-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span
                                                    class="badge bg-{{ $echeance->type === 'CNPS' ? 'warning' : ($echeance->type === 'ITS' ? 'info' : 'primary') }} me-2">
                                                    {{ $echeance->type }}
                                                </span>
                                                <small
                                                    class="priority-{{ $echeance->priorite === 'Élevée' ? 'elevee' : ($echeance->priorite === 'Moyenne' ? 'moyenne' : 'normale') }}">
                                                    <i class="fas fa-flag"></i> {{ $echeance->priorite }}
                                                </small>
                                            </div>
                                            <h6 class="mb-1">{{ $echeance->libelle }}</h6>
                                            <small class="text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $echeance->date_limite->format('d/m/Y') }}
                                                <span class="ms-2">
                                                    ({{ (int) $echeance->date_limite->diffInDays(now(), false) }} jours)
                                                </span>
                                            </small>
                                        </div>
                                        <div>
                                            <span
                                                class="badge status-{{ $echeance->statut === 'Soumis' ? 'soumis' : ($echeance->statut === 'En cours' ? 'encours' : 'brouillon') }}">
                                                {{ $echeance->statut }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center p-4">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                    <p class="text-muted mb-0">Aucune échéance imminente</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derniers bulletins et déclarations -->
        <div class="row mb-4">
            <!-- Derniers bulletins générés -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Derniers bulletins générés</h5>

                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Employé</th>
                                        <th>Période</th>
                                        <th>Type</th>
                                        <th>Net à payer</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($derniersBulletins as $bulletin)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ substr($bulletin->employee->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <span>{{ $bulletin->employee->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $bulletin->periode?->nom ?? ($bulletin->periode?->date_debut ? \Carbon\Carbon::parse($bulletin->periode->date_debut)->translatedFormat('M Y') : '—') }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $bulletin->type === 'Annuel' ? 'success' : 'primary' }}">
                                                    {{ $bulletin->type }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-semibold">{{ number_format($bulletin->salaire_net, 0, ',', ' ') }}
                                                    FCFA</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge status-{{ $bulletin->statut === 'Validé' ? 'valide' : ($bulletin->statut === 'Généré' ? 'soumis' : 'brouillon') }}">
                                                    {{ $bulletin->statut }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fas fa-eye me-2"></i>Voir</a></li>
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fas fa-download me-2"></i>Télécharger</a></li>
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fas fa-print me-2"></i>Imprimer</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">Aucun bulletin récent</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières déclarations -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Dernières déclarations</h5>

                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Période</th>
                                        <th>Employés</th>
                                        <th>Montant total</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dernieresDeclarations as $declaration)
                                        <tr>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $declaration->type === 'CNPS' ? 'warning' : ($declaration->type === 'ITS' ? 'info' : 'primary') }}">
                                                    {{ $declaration->type }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>{{ $declaration->periode }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-label-secondary">{{ $declaration->employes_concernes }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-semibold">{{ number_format($declaration->montant_total, 0, ',', ' ') }}
                                                    FCFA</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge status-{{ $declaration->statut === 'Soumis' ? 'soumis' : ($declaration->statut === 'En cours' ? 'encours' : 'brouillon') }}">
                                                    {{ $declaration->statut }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fas fa-eye me-2"></i>Voir</a></li>
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fas fa-download me-2"></i>Télécharger</a></li>
                                                        <li><a class="dropdown-item" href="#"><i
                                                                    class="fas fa-edit me-2"></i>Modifier</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">Aucune déclaration récente</div>
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

        <!-- Actions rapides -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"> Actions rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="#"
                                    class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center"
                                    style="min-height: 100px;">
                                    <i class="fas fa-file-invoice fa-2x mb-2"></i>
                                    <span>Générer bulletins mensuels</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#"
                                    class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center"
                                    style="min-height: 100px;">
                                    <i class="fas fa-building fa-2x mb-2"></i>
                                    <span>Nouvelle déclaration CNPS</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#"
                                    class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center"
                                    style="min-height: 100px;">
                                    <i class="fas fa-receipt fa-2x mb-2"></i>
                                    <span>Déclaration ITS</span>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#"
                                    class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center"
                                    style="min-height: 100px;">
                                    <i class="fas fa-heartbeat fa-2x mb-2"></i>
                                    <span>Déclaration CMU</span>
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4 mb-3">
                                <a href="#" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-book me-2"></i>Livre de paie annuel
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="#" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-users me-2"></i>Bulletins individuels
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="#" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-download me-2"></i>Exporter tout
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
    <script src="{{ asset('libs/apex-charts/apex-charts.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Graphique d'évolution des déclarations
            const chartEl = document.querySelector('#declarationsEvolutionChart');
            if (chartEl) {
                // Récupérer les données depuis les attributs data
                const chartData = {
                    cnps: JSON.parse(chartEl.dataset.cnps),
                    its: JSON.parse(chartEl.dataset.its),
                    cmu: JSON.parse(chartEl.dataset.cmu),
                    labels: JSON.parse(chartEl.dataset.labels)
                };

                const options = {
                    series: [
                        {
                            name: 'CNPS',
                            data: chartData.cnps
                        },
                        {
                            name: 'ITS',
                            data: chartData.its
                        },
                        {
                            name: 'CMU',
                            data: chartData.cmu
                        }
                    ],
                    chart: {
                        type: 'line',
                        height: 300,
                        toolbar: {
                            show: false
                        }
                    },
                    colors: ['#ffab00', '#03c3ec', '#696cff'],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    markers: {
                        size: 4,
                        colors: ['#ffab00', '#03c3ec', '#696cff'],
                        strokeColors: '#fff',
                        strokeWidth: 2,
                        hover: {
                            size: 6
                        }
                    },
                    xaxis: {
                        categories: chartData.labels,
                        labels: {
                            style: {
                                colors: '#7c7c8d',
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#7c7c8d',
                                fontSize: '12px'
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 3
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        labels: {
                            colors: '#7c7c8d',
                            useSeriesColors: false
                        }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function (val) {
                                return val + ' déclaration(s)';
                            }
                        }
                    }
                };

                const chart = new ApexCharts(chartEl, options);
                chart.render();
            }
        });
    </script>
@endpush