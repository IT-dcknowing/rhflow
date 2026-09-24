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
        {{-- Les id guide-stats-* servent d'ancres au guide interactif : ils restent sur les colonnes. --}}
        <x-kpi-grid>
            <x-kpi id="guide-stats-employees" col="col-md-6 col-lg-3" icon="fas fa-users" color="primary"
                label="Employés" :value="number_format($stats['total_employes'], 0, ',', ' ')" />

            <x-kpi id="guide-stats-payroll" col="col-md-6 col-lg-3" icon="fas fa-coins" color="success"
                label="Masse salariale" sublabel="FCFA"
                :value="number_format($stats['masse_salariale_mensuelle'], 0, ',', ' ')" />

            <x-kpi id="guide-stats-loans" col="col-md-6 col-lg-3" icon="fas fa-hand-holding-usd" color="warning"
                label="Prêts en cours" sublabel="FCFA"
                :value="number_format($stats['total_pret_en_cours'], 0, ',', ' ')" />

            <x-kpi col="col-md-6 col-lg-3" icon="fas fa-chart-line" color="info" label="Salaire moyen" sublabel="FCFA"
                :value="number_format($stats['salaire_moyen'], 0, ',', ' ')" />
        </x-kpi-grid>

        <!-- Graphique et dernières fiches de paie -->
        <div class="row mb-4">
            <!-- Graphique d'évolution de la masse salariale -->
            <div class="col-12 col-lg-5 mb-4">
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
            <div class="col-12 col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Dernières fiches de paie</h5>
                        <a href="{{ route('company.paiesalaries.exercices.create') }}"
                            class="btn btn-sm btn-outline-primary">Tout voir</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
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
                                            <td class="text-end" style="white-space:nowrap;">
                                                {{-- S\u00e9parateur : une espace, comme partout ailleurs. En
                                                     PHP, '\u00a0' entre apostrophes n'est pas une espace
                                                     ins\u00e9cable mais les six caract\u00e8res eux-m\u00eames, qui
                                                     s'affichaient tels quels dans le montant. --}}
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

            {{-- Les quatre parts viennent des bulletins du mois, pas d'une estimation :
                 le total cotisations etait auparavant un forfait de 10 % de la masse
                 salariale, et les retenues ne comptaient que les primes negatives. --}}
            @php
                $charges = $stats['repartition_charges'] ?? [
                    'base' => 0, 'primes' => 0, 'retenues' => 0, 'patronales' => 0,
                    'bulletins' => 0, 'mois' => now()->format('Y-m'),
                ];
                $moisCharges = $charges['mois'] ?? now()->format('Y-m');
                $chargesDecalees = $moisCharges !== now()->format('Y-m');
                $libelleMoisCharges = \Carbon\Carbon::createFromFormat('Y-m', $moisCharges)
                    ->locale('fr')->isoFormat('MMMM YYYY');
            @endphp

            <!-- Répartition des charges -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Répartition des charges du mois</h5>
                        {{-- Le mois affiché n'est pas toujours le mois courant : on le dit. --}}
                        <small class="text-muted">
                            @if($charges['bulletins'] == 0)
                                Aucun bulletin émis pour le moment
                            @elseif($chargesDecalees)
                                Dernier mois traité : {{ $libelleMoisCharges }}
                                ({{ $charges['bulletins'] }} bulletin{{ $charges['bulletins'] > 1 ? 's' : '' }})
                            @else
                                {{ $libelleMoisCharges }} ·
                                {{ $charges['bulletins'] }} bulletin{{ $charges['bulletins'] > 1 ? 's' : '' }}
                            @endif
                        </small>
                    </div>
                    <div class="card-body">
                        <div id="repartitionChargesChart" class="mb-2"></div>
                        {{-- Le total est le coût employeur : base + primes + charges
                             patronales. Les retenues salarié ne s'y ajoutent pas, elles
                             sont prélevées sur le brut — les compter doublerait la somme. --}}
                        <div class="text-center mb-3">
                            <span class="text-muted small">Coût total employeur</span><br>
                            <strong class="fs-5" style="white-space:nowrap;">
                                {{ number_format($charges['base'] + $charges['primes'] + $charges['patronales'], 0, ',', ' ') }} FCFA
                            </strong>
                        </div>
                        {{-- Tableau de répartition aligné --}}
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="ps-0 border-0">
                                        <span class="badge me-1" style="background:#696cff;width:10px;height:10px;padding:0;display:inline-block;border-radius:2px;"></span>
                                        Salaire de base
                                    </td>
                                    <td class="text-end fw-semibold border-0" style="white-space:nowrap;">
                                        {{ number_format($charges['base'], 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-0 border-0">
                                        <span class="badge me-1" style="background:#71dd37;width:10px;height:10px;padding:0;display:inline-block;border-radius:2px;"></span>
                                        Primes et indemnités
                                    </td>
                                    <td class="text-end fw-semibold border-0" style="white-space:nowrap;">
                                        {{ number_format($charges['primes'], 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-0 border-0">
                                        <span class="badge me-1" style="background:#03c3ec;width:10px;height:10px;padding:0;display:inline-block;border-radius:2px;"></span>
                                        Charges patronales
                                    </td>
                                    <td class="text-end fw-semibold border-0" style="white-space:nowrap;">
                                        {{ number_format($charges['patronales'], 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                {{-- Hors donut : prélevé sur le brut, pas ajouté au coût. --}}
                                <tr>
                                    <td class="ps-0 text-muted small">
                                        <span class="me-1" style="width:10px;height:10px;display:inline-block;"></span>
                                        dont retenues salarié (impôt, CNPS, CMU, prêts)
                                    </td>
                                    <td class="text-end text-muted small" style="white-space:nowrap;">
                                        &minus; {{ number_format($charges['retenues'], 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-0 fw-semibold">Net à payer</td>
                                    <td class="text-end fw-semibold" style="white-space:nowrap;">
                                        {{ number_format($charges['base'] + $charges['primes'] - $charges['retenues'], 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                    height: 220,
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
                    {{ (int) $charges['base'] }},
                    {{ (int) $charges['primes'] }},
                    {{ (int) $charges['patronales'] }}
                ],
                chart: {
                    type: 'donut',
                    height: 160,
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    }
                },
                labels: ['Salaire de base', 'Primes et indemnités', 'Charges patronales'],
                colors: ['#696cff', '#71dd37', '#03c3ec'],
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
                                show: false
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