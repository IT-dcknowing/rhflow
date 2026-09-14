@extends('layouts.app')

@section('title', 'Tableau de bord - Gestion de la paie')

@push('css')
    <link rel="stylesheet" href="{{ asset('libs/apex-charts/apex-charts.css') }}">
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y ds">
        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 id="guide-dashboard-title" class="mb-1"> Tableau de bord - Gestion de la paie</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.paiesalaries.exercices.create') }}">Paie</a>
                                </li>
                                <li class="breadcrumb-item active">Tableau de bord</li>
                            </ol>
                        </nav>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                        </small>
                    </div>
                    <div>
                        <a href="{{ route('company.paiesalaries.exercices.create') }}" class="btn btn-primary"
                            id="guide-btn-new-exercice">
                            <i class="fas fa-plus me-1"></i>Nouvelle Exercice
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="row mb-4">
            <div id="guide-stats-employees" class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1">Employés</span>
                                <h3 class="card-title mb-0">{{ number_format($stats['total_employes'], 0, ',', ' ') }}</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="fas fa-users"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="guide-stats-payroll" class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1">Masse salariale</span>
                                <h3 class="card-title mb-0">
                                    {{ number_format($stats['masse_salariale_mensuelle'], 0, ',', ' ') }} FCFA</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="fas fa-coins"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="guide-stats-loans" class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1">Prêts en cours</span>
                                <h3 class="card-title mb-0">{{ number_format($stats['total_pret_en_cours'], 0, ',', ' ') }}
                                    FCFA</h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold d-block mb-1">Salaire moyen</span>
                                <h3 class="card-title mb-0">{{ number_format($stats['salaire_moyen'], 0, ',', ' ') }} FCFA
                                </h3>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique et dernières fiches de paie -->
        <div class="row mb-4">
            <!-- Graphique d'évolution de la masse salariale -->
            <div class="col-12 col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Évolution de la masse salariale</h5>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="salesReportTabs" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="javascript:void(0);">Derniers 6 mois</a>
                                <a class="dropdown-item" href="javascript:void(0);">Cette année</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="masseSalarialeChart"></div>
                    </div>
                </div>
            </div>

            <!-- Dernières fiches de paie -->
            <div class="col-12 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Dernières fiches de paie</h5>
                        <a href="{{ route('company.paiesalaries.exercices.create') }}"
                            class="btn btn-sm btn-outline-primary">Tout voir</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    @forelse($dernieres_paies as $paie)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ $paie->employee ? substr($paie->employee->name, 0, 1) : '?' }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">
                                                            {{ $paie->employee ? $paie->employee->name : 'Employé inconnu' }}
                                                        </h6>
                                                        <small
                                                            class="text-muted">{{ $paie->periode?->nom ?? ($paie->periode?->date_debut ? \Carbon\Carbon::parse($paie->periode->date_debut)->translatedFormat('M Y') : '—') }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-semibold">{{ number_format($paie->salaire_net, 0, ',', ' ') }}
                                                    FCFA</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4">
                                                <div class="text-muted">Aucune fiche de paie récente</div>
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


        <!-- Échéances et répartition -->
        <div class="row">
            <!-- Échéances de prêts -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Échéances de prêts à venir</h5>
                        <a href="{{ route('company.loans.index') }}" class="btn btn-sm btn-outline-primary">Tout voir</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    @forelse($echeances_pret as $pret)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial rounded-circle bg-label-warning">
                                                            <i class="fas fa-hand-holding-usd"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $pret->employee->name ?? 'Employé inconnu' }}</h6>
                                                        <div class="d-flex align-items-center">
                                                            <small class="text-muted me-2">
                                                                <i class="far fa-calendar-alt me-1"></i>
                                                                {{ \Carbon\Carbon::parse($pret->prochaine_echeance)->format('d/m/Y') }}
                                                            </small>
                                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                                <div class="progress-bar bg-success" role="progressbar"
                                                                    style="width: {{ min(100, $pret->repayment_percentage ?? 0) }}%"
                                                                    aria-valuenow="{{ $pret->repayment_percentage ?? 0 }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <small
                                                                class="ms-2 text-muted">{{ round($pret->repayment_percentage ?? 0) }}%</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="fw-semibold">{{ number_format($pret->next_payment['amount'] ?? $pret->amount_deduc, 0, ',', ' ') }}
                                                        FCFA</span>
                                                    <small class="text-muted">
                                                        Reste :
                                                        {{ number_format($pret->remaining_amount ?? $pret->amount, 0, ',', ' ') }}
                                                        FCFA
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-check-circle text-success mb-2"
                                                        style="font-size: 2rem;"></i>
                                                    <div class="text-muted">Aucune échéance de prêt à venir cette semaine</div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($echeances_pret->isNotEmpty())
                            <div class="card-footer bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ $loanStats['total_active_loans'] }} prêts actifs
                                    </small>
                                    <small class="text-primary fw-semibold">
                                        Total : {{ number_format($loanStats['total_loans_amount'], 0, ',', ' ') }} FCFA
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Répartition des charges -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Répartition des charges du mois</h5>
                    </div>
                    <div class="card-body">
                        <div id="repartitionChargesChart" class="mb-4"></div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-label-primary me-2" style="width: 12px; height: 12px;"></span>
                                    <span>Salaire de base</span>
                                    <span
                                        class="ms-auto fw-semibold">{{ number_format($stats['masse_salariale_mensuelle'] - $stats['total_primes_mois'], 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-label-success me-2" style="width: 12px; height: 12px;"></span>
                                    <span>Primes</span>
                                    <span
                                        class="ms-auto fw-semibold">{{ number_format($stats['total_primes_mois'], 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-label-warning me-2" style="width: 12px; height: 12px;"></span>
                                    <span>Retenues</span>
                                    <span
                                        class="ms-auto fw-semibold">{{ number_format($stats['total_retenues_mois'], 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-label-info me-2" style="width: 12px; height: 12px;"></span>
                                    <span>Cotisations</span>
                                    <span
                                        class="ms-auto fw-semibold">{{ number_format(($stats['masse_salariale_mensuelle'] * 0.1), 0, ',', ' ') }}
                                        FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Graphique d'évolution de la masse salariale
        const masseSalarialeEl = document.querySelector('#masseSalarialeChart');
        if (masseSalarialeEl) {
            const masseSalarialeChart = new ApexCharts(masseSalarialeEl, {
                series: [{
                    name: 'Masse salariale',
                    data: @json($masse_salariale['data'])
                }],
                chart: {
                    height: 300,
                    type: 'area',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    sparkline: {
                        enabled: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                colors: ['#696cff'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.4,
                        inverseColors: false,
                        opacityFrom: 0.5,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                },
                xaxis: {
                    categories: @json($masse_salariale['labels']),
                    labels: {
                        style: {
                            colors: '#7c7c8d',
                            fontSize: '12px'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#7c7c8d',
                            fontSize: '12px'
                        },
                        formatter: function (val) {
                            return (val / 1000).toFixed(0) + 'k';
                        }
                    }
                }
            });
            masseSalarialeChart.render();
        }

        // Graphique de répartition des charges
        const repartitionChargesEl = document.querySelector('#repartitionChargesChart');
        if (repartitionChargesEl) {
            const repartitionChargesChart = new ApexCharts(repartitionChargesEl, {
                series: [
                {{ $stats['masse_salariale_mensuelle'] - $stats['total_primes_mois'] }},
                {{ $stats['total_primes_mois'] }},
                {{ $stats['total_retenues_mois'] }},
                    {{ $stats['masse_salariale_mensuelle'] * 0.1 }}
                ],
                chart: {
                    type: 'donut',
                    height: 200,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    }
                },
                labels: ['Salaire de base', 'Primes', 'Retenues', 'Cotisations'],
                colors: ['#696cff', '#71dd37', '#ffab00', '#03c3ec'],
                stroke: {
                    width: 0
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString('fr-FR') + ' FCFA';
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                value: {
                                    offsetY: 5,
                                    formatter: function (val) {
                                        return (val / {{ $stats['masse_salariale_mensuelle'] }} * 100).toFixed(1) + '%';
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function (w) {
                                        const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return total.toLocaleString('fr-FR') + ' FCFA';
                                    }
                                }
                            }
                        }
                    }
                }
            });
            repartitionChargesChart.render();
        }

        // --- Guide IA Interactif : Le Parcours de Paie ---
        document.addEventListener('DOMContentLoaded', function () {
            // Séquence de conseils pour guider l'utilisateur dans sa paie
            const payrollSteps = [
                {
                    msg: "👋 Bonjour ! Prêt pour la paie ? <b>Étape 1 :</b> Vérifiez que votre période est bien ouverte (en haut à droite).",
                    delay: 1500,
                    highlight: "period-selector" // ID du sélecteur de période
                },
                {
                    msg: "<b>Étape 2 :</b> Avant de calculer, vérifiez les **Variables** (Congés, Prêts, Avances) dans le menu 'Gestion de paie'.",
                    delay: 10000
                },
                {
                    msg: "<b>Étape 3 :</b> Cliquez sur le bouton <span class='badge bg-primary'>Nouvelle Exercice</span> ici à droite pour lancer le calcul.",
                    delay: 10000,
                    highlight: "guide-btn-new-exercice"
                },
                {
                    msg: "<b>Étape 4 :</b> Une fois calculé, vérifiez la <b>Repartition des charges</b> (le graphique en bas) pour détecter des anomalies.",
                    delay: 10000,
                    highlight: "repartitionChargesChart"
                },
                {
                    msg: "<b>Étape 5 :</b> Enfin, rendez-vous dans 'Gestion des états' pour vos déclarations CNPS et livre de paie.",
                    delay: 10000
                }
            ];

            let currentStep = 0;
            function highlightElement(id) {
                // Chercher par ID ou par data-guide-id
                let el = document.getElementById(id) || document.querySelector(`[data-guide-id="${id}"]`);

                if (el) {
                    el.style.transition = "all 0.5s ease";
                    el.style.boxShadow = "0 0 20px rgba(105, 108, 255, 0.8)";
                    el.style.transform = "scale(1.05)";
                    el.style.zIndex = "10000";
                    setTimeout(() => {
                        el.style.boxShadow = "none";
                        el.style.transform = "scale(1)";
                        el.style.zIndex = "";
                    }, 4000);
                }
            }

            function runPayrollGuide() {
                if (typeof showAiTip !== 'function') {
                    console.log("IA non prête, nouvelle tentative...");
                    setTimeout(runPayrollGuide, 1000);
                    return;
                }

                if (currentStep < payrollSteps.length) {
                    const step = payrollSteps[currentStep];
                    showAiTip(step.msg, 8000);
                    if (step.highlight) highlightElement(step.highlight);

                    currentStep++;
                    if (currentStep < payrollSteps.length) {
                        setTimeout(runPayrollGuide, step.delay);
                    }
                }
            }

            // Lancer le guide après un court délai
            setTimeout(runPayrollGuide, 1500);

            // Conseils additionnels au survol
            const loansCard = document.getElementById('guide-stats-loans');
            if (loansCard) {
                loansCard.addEventListener('mouseenter', function () {
                    showAiTip("📌 <b>Point Vigilance :</b> Vérifiez les échéances de prêt avant de clôturer la paie pour éviter les erreurs de retenue.");
                });
            }

            const payrollCard = document.getElementById('guide-stats-payroll');
            if (payrollCard) {
                payrollCard.addEventListener('mouseenter', function () {
                    showAiTip("💰 <b>Masse Salariale :</b> Si ce montant est anormalement élevé, vérifiez les rappels de salaire ou les primes exceptionnelles.");
                });
            }
        });
    </script>
@endpush