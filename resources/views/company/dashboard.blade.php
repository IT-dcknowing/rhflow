@php
    setlocale(LC_TIME, 'fr_FR.utf8');
    $totalBrut_total = 0;
    $totalNet_total = 0;
    $totalBrutImposable_total = 0;
    $totalBrutSocial_total = 0;
    $totalNetImposable_total = 0;
    $totalNetSocial = 0;
    $totalRetenue_total = 0;
    foreach ($paySlip as $paysilp) {
        $totalBrut_total += $paysilp->salary_brut;
        $totalNet_total += $paysilp->net_payble;
        $totalBrutImposable_total += $paysilp->net_imposable;
        $totalBrutSocial_total += $paysilp->net_sociale;
        $totalNetImposable_total += $paysilp->net_imposable;
        $totalNetSocial += $paysilp->net_sociale;
        $totalRetenue_total += $paysilp->total_retenue;
    }
@endphp

@extends('layouts.app')

@section('title', 'Tableau de bord Entreprise - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- En-tête du Dashboard -->
        <div class="row mb-4">
            <div class="col-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Tableau De Bord </h4>
                        <p class="text-muted mb-0">Vue d'ensemble complète de votre activité</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ ucfirst(\Carbon\Carbon::now()->translatedFormat('l d F Y')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ now()->format('H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        
                    </div>
                </div>
            </div>
            <!-- information déclaration impot -->
            <div class="col-12">
                @php
                    $currentDay = date('d');
                    $currentMonth = date('F');
                    $frenchMonths = [
                        'January' => 'Janvier',
                        'February' => 'Février',
                        'March' => 'Mars',
                        'April' => 'Avril',
                        'May' => 'Mai',
                        'June' => 'Juin',
                        'July' => 'Juillet',
                        'August' => 'Août',
                        'September' => 'Septembre',
                        'October' => 'Octobre',
                        'November' => 'Novembre',
                        'December' => 'Décembre'
                    ];
                @endphp
                <div class="alert {{ $currentDay > 15 ? 'alert-danger' : 'alert-info' }} d-flex align-items-center p-2" style="overflow: hidden;">
                    <i class="ti {{ $currentDay > 15 ? 'ti-alert-circle' : 'ti-info-circle' }} me-2 fs-4"></i>
                    <marquee scrollamount="5" style="width: 100%; white-space: nowrap;">
                        @if($currentDay <= 15)
                            <span class="fw-bold">Rappel :</span> N'oubliez pas d'effectuer vos déclarations ITS, CNPS et CMU avant le 15
                            {{ $frenchMonths[$currentMonth] ?? now()->translatedFormat('F') }}
                        @else
                            <span class="fw-bold">Attention :</span> Des pénalités peuvent s'appliquer pour les déclarations soumises après le 15 du mois. Veuillez régulariser votre situation au plus vite.
                        @endif
                    </marquee>
                </div>
            </div>
        </div>

        <!-- Métriques Principales -->
        <div class="row mb-4">
            <!-- Website Analytics -->
            <div class="col-lg-6 mb-4">
                <div class="card bg-primary swiper-container swiper-container-horizontal swiper swiper-card-advance-bg h-100"
                    id="swiper-with-pagination-cards" data-swiper-autoplay="9000">
                    <div class="swiper-wrapper card-body">
                        <div class="swiper-slide">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-white mb-0 mt-2">Analyse du Personnel</h5>
                                    <small>Total : {{ $totalEmployes }} Employés</small>
                                </div>
                                <div class="row">
                                    <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
                                        <h6 class="text-white mt-0 mt-md-3 mb-3">Statuts du Personnel</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-flex mb-4 align-items-center">
                                                        <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                            {{ $activeJob }}</p>
                                                        <p class="mb-0">En poste</p>
                                                    </li>
                                                    <li class="d-flex align-items-center mb-2">
                                                        <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                            {{ $inActiveJOb }}</p>
                                                        <p class="mb-0">Partis</p>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-6">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="d-flex mb-4 align-items-center">
                                                        <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                            {{ $employesEnConge }}</p>
                                                        <p class="mb-0">En Congé</p>
                                                    </li>
                                                    <li class="d-flex align-items-center mb-2">
                                                        <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                            {{ $countTimeSheet }}</p>
                                                        <p class="mb-0">Permissions</p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                        <img src="{{asset('img/illustrations/card-website-analytics-1.png')}}"
                                            alt="Website Analytics" width="170" class="card-website-analytics-img" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-white mb-0 mt-2">Analyse des contrats</h5>
                                    @php
                                        $totalNet = 0;
                                        $totalBrut = 0;
                                        $totalBrutImposable = 0;
                                        $totalRetenue = 0;
                                        $totalBrutSocial = 0;
                                    @endphp
                                    <small>Total : {{ $contractsCDI + $contractsCDD + $contractsStages + $contractsAutres }}
                                        Contrats</small>
                                </div>
                                <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
                                    <h6 class="text-white mt-0 mt-md-3 mb-3">Les différents types de contrats</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex mb-4 align-items-center">
                                                    <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                        {{ $contractsCDI }}</p>
                                                    <p class="mb-0">CDI</p>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                        {{ $contractsCDD }}</p>
                                                    <p class="mb-0">CDD</p>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex mb-4 align-items-center">
                                                    <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                        {{ $contractsStages }}</p>
                                                    <p class="mb-0">Stages</p>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                        {{ $contractsAutres }}</p>
                                                    <p class="mb-0">Autres</p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                    <img src="{{asset('img/illustrations/card-website-analytics-2.png') }}"
                                        alt="Website Analytics" width="170" class="card-website-analytics-img" />
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-white mb-0 mt-2">Analyse des éléments de la paie</h5>
                                    <small>Total : {{ $countTimeSheet2 + $totalOvertime + $totalLoan + $totalAvantage }}
                                        éléments</small>
                                </div>
                                <div class="col-lg-7 col-md-9 col-12 order-2 order-md-1">
                                    <h6 class="text-white mt-0 mt-md-3 mb-3">Eléments de la paie</h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex mb-4 align-items-center">
                                                    <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                        {{ $countTimeSheet2 }}</p>
                                                    <p class="mb-0">Absences</p>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                        {{ $totalOvertime }}</p>
                                                    <p class="mb-0">Heures supps</p>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-6">
                                            <ul class="list-unstyled mb-0">
                                                <li class="d-flex mb-4 align-items-center">
                                                    <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                        {{ $totalLoan }}</p>
                                                    <p class="mb-0">Prêts</p>
                                                </li>
                                                <li class="d-flex align-items-center mb-2">
                                                    <p class="mb-0 fw-medium me-2 website-analytics-text-bg">
                                                        {{ $totalAvantage }}</p>
                                                    <p class="mb-0">Avantages en nature</p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-3 col-12 order-1 order-md-2 my-4 my-md-0 text-center">
                                    <img src="{{asset('img/illustrations/card-website-analytics-3.png')}}"
                                        alt="Website Analytics" width="170" class="card-website-analytics-img" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
            <!--/ Website Analytics -->

            <!-- Analyse Employés -->
            <div class="col-lg-3 mb-4">
                @php
                    $totalNet = 0;
                    $totalBrut = 0;
                    $totalBrutImposable = 0;
                    $totalRetenue = 0;
                    $totalBrutSocial = 0;
                    $totalFiscal = 0;
                    $totalcnps = 0;
                    $totalcmu = 0;
                    $totalloan = 0;
                    $totalcne = 0;
                    $totalce = 0;
                    $totalfdfp = 0;
                    $totalcmupat = 0;
                    $totalcnpspat = 0;
                    $partEmploye = 0;
                    $partEmployeur = 0;
                    $empMensuel = 0;
                    $empJour = 0;
                @endphp
                @foreach ($getEmployee as $employee)
                    @php
                        if ($periode) {
                            if ($employee->salary_type == 1) {
                                $empMensuel++;
                            } else {
                                $empJour++;
                            }
                            $totalNet += $employee->get_net_salary($periode->id);
                            $totalBrut += $employee->get_brut_salary($periode->id);
                            $totalBrutImposable += $employee->get_salary_imposable($periode->id);
                            $totalRetenue += $employee->get_retenue($periode->id);
                            $totalBrutSocial += $employee->get_salary_social($periode->id);
                            $totalFiscal += $employee->get_imp_net();
                            $totalcnps += $employee->get_cnps_sal();
                            $totalcmu += $employee->get_cmu_sal();
                            $totalloan += $employee->get_loan();
                            $totalcne += $employee->get_ce_emp();
                            $totalce += $employee->get_ce_exp_emp();
                            $totalfdfp += ($employee->get_taxe_appr() + $employee->get_taxe_fpc());
                            $totalcmupat += $employee->get_cmu_emp();
                            $totalcnpspat += $employee->get_cnps_emp();
                            $partEmploye += $employee->get_retenue($periode->id);
                            $partEmployeur += $employee->get_patronale($periode->id);
                        }
                    @endphp
                @endforeach
                @php
                    $mois1 = null;
                    $mois2 = null;
                    $totalNet1 = 0;
                    $totalNet2 = 0;
                    $frenchMonths = [
                        '01' => 'Jan',
                        '02' => 'Fév',
                        '03' => 'Mar',
                        '04' => 'Avr',
                        '05' => 'Mai',
                        '06' => 'Juin',
                        '07' => 'Juil',
                        '08' => 'Aoû',
                        '09' => 'Sep',
                        '10' => 'Oct',
                        '11' => 'Nov',
                        '12' => 'Déc'
                    ];
                    foreach ($paySlip as $slip) {
                        $mois1 = date('m', strtotime($derniersMois2));
                        $mois1 = $frenchMonths[$mois1];
                        $mois2 = date('m', strtotime($derniersMois));
                        $mois2 = $frenchMonths[$mois2];
                        if ($slip->salary_month == $derniersMois2) {
                            $totalNet1 += ($slip->net_payble - $slip->saturation_deduction);
                        }
                        if ($slip->salary_month == $derniersMois) {
                            $totalNet2 += ($slip->net_payble - $slip->saturation_deduction);
                        }
                    }

                @endphp
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <small class="d-block mb-1 text-muted">Analyse des types de salaires</small>
                            <p class="card-text {{ ($empMensuel > $getEmployeeDay) ? 'text-success' : 'text-info' }}">
                                @if($getEmployeeDay != 0)
                                    {{ ($empMensuel > $getEmployeeDay) ? '+' : '-' }}{{ number_format(abs(($empMensuel / ($empMensuel + $getEmployeeDay)) * 100), 1) }}%
                                @else
                                    100%
                                @endif
                            </p>
                        </div>
                        <h4 class="card-title mb-1">{{ $empMensuel + $getEmployeeDay }} Employés</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="d-flex gap-2 align-items-center mb-2">
                                    <span class="badge bg-label-info p-1 rounded"><i class="fas fa-user"></i></span>
                                    <p class="mb-0">Mensuels</p>
                                </div>
                                <h5 class="mb-0 pt-1 text-nowrap">
                                    @if($empMensuel != 0)
                                        {{ number_format(($empMensuel / ($empMensuel + $getEmployeeDay)) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </h5>
                                <small class="text-muted">{{ $empMensuel }} employés</small>
                            </div>
                            <div class="col-2">
                                <div class="divider divider-vertical">
                                    <div class="divider-text">
                                        <span class="badge-divider-bg bg-label-secondary">VS</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5 text-end">
                                <div class="d-flex gap-2 justify-content-end align-items-center mb-2">
                                    <p class="mb-0">Journaliers</p>
                                    <span class="badge bg-label-primary p-1 rounded"><i class="fas fa-user"></i></span>
                                </div>
                                <h5 class="mb-0 pt-1 text-nowrap ms-lg-n3 ms-xl-0">
                                    @if($getEmployeeDay != 0)
                                        {{ number_format(($getEmployeeDay / ($empMensuel + $getEmployeeDay)) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </h5>
                                <small class="text-muted">{{ $getEmployeeDay }} employés</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-4">
                            <div class="progress w-100" style="height: 8px">
                                <div id="progressBar1" class="progress-bar bg-info" role="progressbar" style="width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                <div id="progressBar2" class="progress-bar bg-primary" role="progressbar" style="width: 0%"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <script>
                                function updateProgressBars(mensuel, jour) {
                                    const total = mensuel + jour;
                                    if (total === 0) return;

                                    const percentMensuel = (mensuel / total) * 100;
                                    const percentJour = (jour / total) * 100;

                                    const progressBar1 = document.getElementById('progressBar1');
                                    const progressBar2 = document.getElementById('progressBar2');

                                    progressBar1.style.width = `${percentMensuel}%`;
                                    progressBar2.style.width = `${percentJour}%`;

                                    progressBar1.setAttribute('aria-valuenow', percentMensuel);
                                    progressBar2.setAttribute('aria-valuenow', percentJour);
                                }

                                updateProgressBars({{ $empMensuel }}, {{ $empJour }});
                            </script>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Sales Overview -->

            <!-- Analyse du packs -->
            <div class="col-lg-3 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <small class="d-block mb-1 text-muted">Analyse du pack</small>
                            <p id="progress-percentage" class="card-text text-success">0%</p>
                        </div>
                        @if (\Auth::user()->plan == '99')
                            <div class="progress mt-3" style="height: 8px;">
                                <div id="plan-progress" class="progress-bar bg-danger" role="progressbar" style="width: 100%;"
                                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @else
                            <div class="progress mt-3" style="height: 8px;">
                                <div id="plan-progress" class="progress-bar" role="progressbar" style="width: 0%;"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endif
                    </div>
                    @if (\Auth::user()->plan == '99')
                        <div class="card-body pb-0">
                            <small>{{ __('Pack Expiré') }}</small>
                            <h5 class="text-danger text-end"> - </h5>
                            <h6 class="card-title mb-0 mt-2">{{ __('Nombre de jours restant') }}</h6>
                            <span class="col-auto text-end">
                                <h6 class="m-0 text-danger">0 Jours</h6>
                            </span>
                            <span>Changer d'offre <a href="{{ route('company.plan.pricing') }}"
                                    class="badge bg-label-primary">{{ __('ici') }}</a></span>
                        </div>
                    @else
                                    <div class="card-body pb-0" <small>{{ __('Pack Actifs') }}</small>
                                        <h5 class="text-success text-end">{{ $plan->name }}</h5>

                                        <h6 class="card-title mb-0 mt-2">{{ __('Nombre de jours restant') }}</h6>
                                        <div class="col-auto text-end">
                                            <?php
                        $percentage = 0;
                        $dat = date_create(\Auth::user()->plan_expire_date);
                        $dat2 = date_create(date("Y-m-d"));
                        $jours = date_diff($dat, $dat2);
                        $total_days = $jours->days;
                        $days_left = max(0, $total_days);
                        $percentage = 100 - min(100, ($days_left / 30) * 100); // Supposons que le plan est de 30 jours
                                                ?>
                                            <h6 class="m-0 text-success"><?php    echo $jours->format("%a"); ?> Jours</h6>
                                            <input type="hidden" id="percentage" value="<?php    echo $percentage; ?>">
                                        </div>

                                        <span>Changer d'offre <a href="{{ route('company.plan.pricing') }}"
                                                class="badge bg-label-primary">{{ __('ici') }}</a></span>
                                    </div>
                    @endif
                    <br>
                </div>
            </div>
            <!--/ Analyse du packs -->
        </div>

        <!-- Graphiques de Performance -->
        <div class="row mb-4">
            <!-- Earning Reports -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0 d-flex justify-content-between mb-4">
                        <div class="card-title mb-0">
                            <h5 class="mb-0">Les éléments de la paie</h5>
                            <small class="text-muted">du mois en cours</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-4 d-flex flex-column align-self-center">
                                <small>Total bruts</small>
                                <div class="d-flex gap-2 align-items-center mb-2 pb-1 flex-wrap">
                                    <h4 class="mb-0">{{number_format($totalBrut_total, 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                            <div class="col-12 col-md-8 align-item-center">
                                <div class="bg-label-primary rounded-3 text-center mb-3">
                                    <img class="img-fluid"
                                        src="{{asset('img/illustrations/page-misc-under-maintenance.png')}}"
                                        alt="Card girl image" width="140" />
                                </div>
                            </div>
                        </div>
                        <div class="border rounded p-3 mt-4">
                            <div class="gap-2 d-flex justify-content-between">
                                <div class="col-12 col-sm-4">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="badge rounded bg-label-primary p-1">
                                            <i class="fas fa-dollar fa-sm"></i>
                                        </div>
                                        <h6 class="mb-0">Total SBI</h6>
                                    </div>
                                    <h6 class="my-2 pt-1">{{number_format($totalBrutImposable_total, 0, ',', ' ') }} FCFA
                                    </h6>
                                    <div class="progress w-75" style="height: 4px">
                                        <div class="progress-bar" role="progressbar" style="width: 65%" aria-valuenow="65"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="badge rounded bg-label-info p-1"><i class="fas fa-dollar fa-sm"></i>
                                        </div>
                                        <h6 class="mb-0">Total SBS</h6>
                                    </div>
                                    <h6 class="my-2 pt-1">{{number_format($totalBrutSocial_total, 0, ',', ' ') }} FCFA</h6>
                                    <div class="progress w-75" style="height: 4px">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%"
                                            aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="d-flex gap-2 align-items-center">
                                        <div class="badge rounded bg-label-danger p-1">
                                            <i class="fas fa-dollar fa-sm"></i>
                                        </div>
                                        <h6 class="mb-0">Total Retenues</h6>
                                    </div>
                                    <h6 class="my-2 pt-1">{{number_format($totalRetenue_total, 0, ',', ' ') }} FCFA</h6>
                                    <div class="progress w-75" style="height: 4px">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 65%"
                                            aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Earning Reports -->

            <!-- Support Tracker -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between pb-0">
                        <div class="card-title mb-0">
                            <h5 class="mb-0">Charges patronales & Employés</h5>
                            <small class="text-muted">du mois en cours</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-4 col-md-12 col-lg-4">
                                <div class="mt-lg-4 mt-lg-2 mb-lg-4 mb-2 pt-1">
                                    <p class="mb-0">Employés</p>
                                    <small>Total: {{ number_format($partEmploye, 0, ',', ' ') }} FCFA</small>
                                    <input type="hidden" id="partEmploye" value="{{ $partEmploye }}">
                                    
                                </div>
                                <ul class="p-0 m-0">
                                    <li class="d-flex gap-3 align-items-center mb-lg-3 pt-2 pb-1">
                                        <div class="badge rounded bg-label-primary p-1"><i class="fas fa-receipt fa-sm"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-nowrap">Retenue fisc</h6>
                                            <small class="text-muted">{{ number_format($totalFiscal, 0, ',', ' ') }}
                                                FCFA</small>
                                        </div>
                                    </li>
                                    <li class="d-flex gap-3 align-items-center mb-lg-3 pb-1">
                                        <div class="badge rounded bg-label-info p-1">
                                            <i class="fas fa-bank fa-sm"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-nowrap">Retenue Cnps</h6>
                                            <small class="text-muted">{{ number_format($totalcnps, 0, ',', ' ') }}
                                                FCFA</small>
                                        </div>
                                    </li>
                                    <li class="d-flex gap-3 align-items-center mb-lg-3 pb-1">
                                        <div class="badge rounded bg-label-warning p-1"><i class="fas fa-heart fa-sm"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-nowrap">CMU</h6>
                                            <small class="text-muted">{{ number_format($totalcmu, 0, ',', ' ') }}
                                                FCFA</small>
                                        </div>
                                    </li>
                                    <li class="d-flex gap-3 align-items-center pb-1">
                                        <div class="badge rounded bg-label-warning p-1"><i class="fas fa-dollar fa-sm"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 text-nowrap">Prêts</h6>
                                            <small class="text-muted">{{ number_format($totalloan, 0, ',', ' ') }}
                                                FCFA</small>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-12 col-sm-4 col-md-12 col-lg-4"
                                style="display: flex; justify-content: center; align-items: center;">
                                <div id="chargesDiagram"></div>
                            </div>
                            <div class="col-12 col-sm-4 col-md-12 col-lg-4 ">
                                <div class="mt-lg-4 mt-lg-2 mb-lg-4 mb-2 pt-1 text-end">
                                    <p class="mb-0 text-end">Patronales</p>
                                    <small class="mb-0">Total: {{ number_format($partEmployeur, 0, ',', ' ') }} FCFA</small>
                                    <input type="hidden" id="partEmployeur" value="{{ $partEmployeur }}">
                                  
                                </div>
                                <ul class="p-0 m-0">
                                    <li class="d-flex gap-3 align-items-center mb-lg-3 pt-2 pb-1 justify-content-end">
                                        <div class="text-end">
                                            <h6 class="mb-0 text-nowrap">C E (CNE)</h6>
                                            <small>{{ number_format($totalcne, 0, ',', ' ') }} FCFA</small>
                                        </div>
                                        <div class="badge rounded bg-label-primary p-1"><i
                                                class="fas fa-building fa-sm"></i></div>
                                    </li>
                                    <li class="d-flex gap-3 align-items-center mb-lg-3 pb-1 justify-content-end">
                                        <div class="text-end">
                                            <h6 class="mb-0 text-nowrap">C E (Expatrié)</h6>
                                            <small>{{ number_format($totalce, 0, ',', ' ') }} FCFA</small>
                                        </div>
                                        <div class="badge rounded bg-label-info p-1">
                                            <i class="fas fa-earth fa-sm"></i>
                                        </div>
                                    </li>
                                    <li class="d-flex gap-3 align-items-center mb-lg-3 pb-1 justify-content-end">
                                        <div class="text-end">
                                            <h6 class="mb-0 text-nowrap">FDFP</h6>
                                            <small>{{ number_format($totalfdfp, 0, ',', ' ') }} FCFA</small>
                                        </div>
                                        <div class="badge rounded bg-label-warning p-1"><i class="fas fa-school fa-sm"></i>
                                        </div>
                                    </li>
                                    <li class="d-flex gap-3 align-items-center pb-1 justify-content-end">
                                        <div class="text-end">
                                            <h6 class="mb-0 text-nowrap">CNPS & CMU</h6>
                                            <small>{{ number_format($totalcmupat + $totalcnpspat, 0, ',', ' ') }} FCFA</small>
                                        </div>
                                        <div class="badge rounded bg-label-warning p-1"><i
                                                class="fas fa-hospital fa-sm"></i></div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activités par Module -->
        <div class="row mb-4">
            <!-- RH & Effectifs -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Ressources Humaines</h5>
                        <div class="d-flex gap-2">
                           
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                        <div class="avatar-initial bg-label-success rounded">
                                            <i class="fas fa-user-check fa-20px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $stats['monthly_employees'] ?? 0 }}</h6>
                                        <small class="text-muted">Employés actifs</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                        <div class="avatar-initial bg-label-warning rounded">
                                            <i class="fas fa-user-clock fa-20px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $stats['daily_employees'] ?? 0 }}</h6>
                                        <small class="text-muted">Journaliers</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                        <div class="avatar-initial bg-label-info rounded">
                                            <i class="fas fa-calendar fa-20px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $stats['current_leaves'] ?? 0 }}</h6>
                                        <small class="text-muted">Congés en cours</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                        <div class="avatar-initial bg-label-danger rounded">
                                            <i class="fas fa-user-times fa-20px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $stats['recent_departures'] ?? 0 }}</h6>
                                        <small class="text-muted">Départs récents</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            @if(($stats['total_employees'] ?? 0) > 0)
                                <div class="progress-bar bg-success"
                                    style="width: {{ (($stats['monthly_employees'] ?? 0) / ($stats['total_employees'] ?? 1)) * 100 }}%">
                                </div>
                                <div class="progress-bar bg-warning"
                                    style="width: {{ (($stats['daily_employees'] ?? 0) / ($stats['total_employees'] ?? 1)) * 100 }}%">
                                </div>
                            @else
                                <div class="progress-bar bg-success" style="width: 0%"></div>
                                <div class="progress-bar bg-warning" style="width: 0%"></div>
                            @endif
                        </div>
                        <small class="text-muted">Répartition Mensuels/Journaliers</small>
                    </div>
                </div>
            </div>

            <!-- Finances & Paie -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Finances & Paie</h5>
                        <div class="d-flex gap-2">
                           
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                        <div class="avatar-initial bg-label-success rounded">
                                            <i class="fas fa-dollar fa-20px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $stats['payroll_processed'] ?? 0 }}%</h6>
                                        <small class="text-muted">Paies traitées</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                        <div class="avatar-initial bg-label-warning rounded">
                                            <i class="fas fa-clock fa-20px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $stats['pending_payroll'] ?? 0 }}%</h6>
                                        <small class="text-muted">En attente</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                        <div class="avatar-initial bg-label-info rounded">
                                            <i class="fas fa-file-text fa-20px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $stats['generated_payrolls'] ?? 0 }}</h6>
                                        <small class="text-muted">Bulletins générés</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                        <div class="avatar-initial bg-label-danger rounded">
                                            <i class="fas fa-exclamation-triangle fa-20px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $stats['payroll_anomalies'] ?? 0 }}</h6>
                                        <small class="text-muted">Anomalies</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h6 class="mb-1">Prochaine paie :</h6>
                            <span class="badge bg-label-{{ $stats['is_payroll_late'] ? 'danger' : 'success' }} fs-6">
                                {{ \Carbon\Carbon::parse($stats['next_payroll_date'])->translatedFormat('d F Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activités Récentes et Alertes -->
        <div class="row mb-4">
            <!-- Activité Récente -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Activité Récente</h5>
                        <div class="dropdown">
                           
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('all')">Toutes</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('employees')">Employés</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('finance')">Finance</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterActivity('time')">Temps</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($recentActivity ?? [] as $activity)
                                <div class="timeline-item mb-4">
                                    <div class="d-flex">
                                        <div class="avatar avatar-sm me-3" style="width: 35px; height: 35px;">
                                            <div class="avatar-initial bg-label-{{ $activity['color'] }} rounded">
                                                <i class="fas fa-{{ $activity['icon'] }} fa-18px"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ $activity['title'] }}</h6>
                                                    <p class="text-muted mb-0">{{ $activity['description'] }}</p>
                                                </div>
                                                <small class="text-muted">{{ $activity['time'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertes et Notifications -->
            <div class="col-xl-4 col-lg-5 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Alertes & Notifications</h5>
                        <span class="badge bg-label-danger">{{ count($alerts ?? []) }} urgentes</span>
                    </div>
                    <div class="card-body">
                        @foreach($alerts ?? [] as $alert)
                            <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-{{ $alert['icon'] }} me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">{{ $alert['title'] }}</h6>
                                        <p class="mb-0">{{ $alert['message'] }}</p>
                                        <small class="text-muted">{{ $alert['deadline'] }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Rapides et Raccourcis -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Actions Rapides</h5>
                       
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Module Employés -->
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ $quickActions['monthly_employee']['route'] }}"
                                        class="btn btn-outline-primary">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-{{ $quickActions['monthly_employee']['icon'] }} me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $quickActions['monthly_employee']['title'] }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $quickActions['monthly_employee']['subtitle'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ $quickActions['daily_employee']['route'] }}" class="btn btn-outline-info">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-{{ $quickActions['daily_employee']['icon'] }} me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $quickActions['daily_employee']['title'] }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $quickActions['daily_employee']['subtitle'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Module Paie -->
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ $quickActions['generate_payroll']['route'] }}"
                                        class="btn btn-outline-success">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-{{ $quickActions['generate_payroll']['icon'] }} me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $quickActions['generate_payroll']['title'] }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $quickActions['generate_payroll']['subtitle'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Module Congés -->
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ $quickActions['approve_leaves']['route'] }}"
                                        class="btn btn-outline-warning">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-{{ $quickActions['approve_leaves']['icon'] }} me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $quickActions['approve_leaves']['title'] }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $quickActions['approve_leaves']['subtitle'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Module Rapports -->
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ $quickActions['monthly_report']['route'] }}"
                                        class="btn btn-outline-secondary">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-{{ $quickActions['monthly_report']['icon'] }} me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $quickActions['monthly_report']['title'] }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $quickActions['monthly_report']['subtitle'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Module Paramètres -->
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ $quickActions['company_settings']['route'] }}" class="btn btn-outline-dark">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-{{ $quickActions['company_settings']['icon'] }} me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $quickActions['company_settings']['title'] }}
                                                </div>
                                                <small
                                                    class="text-muted">{{ $quickActions['company_settings']['subtitle'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Module Support -->
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ $quickActions['support']['route'] }}" 
                                       class="btn btn-outline-secondary {{ !($quickActions['support']['is_active'] ?? true) ? 'disabled' : '' }}"
                                       @if(!($quickActions['support']['is_active'] ?? true)) style="opacity: 0.6; pointer-events: none; border-color: #d9dee3 !important; color: #a1acb8 !important;" @endif>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-{{ $quickActions['support']['icon'] }} me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $quickActions['support']['title'] }}</div>
                                                <small class="text-muted">{{ $quickActions['support']['subtitle'] }}</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Module Analytics -->
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ $quickActions['analytics']['route'] }}" 
                                       class="btn btn-outline-secondary {{ !($quickActions['analytics']['is_active'] ?? true) ? 'disabled' : '' }}"
                                       @if(!($quickActions['analytics']['is_active'] ?? true)) style="opacity: 0.6; pointer-events: none; border-color: #d9dee3 !important; color: #a1acb8 !important;" @endif>
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-{{ $quickActions['analytics']['icon'] }} me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">{{ $quickActions['analytics']['title'] }}</div>
                                                <small class="text-muted">{{ $quickActions['analytics']['subtitle'] }}</small>
                                            </div>
                                        </div>
                                    </a>
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
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/plugins/main.min.js') }}"></script>
    <script src="{{ asset('js/plugins/apexcharts.min.js') }}"></script>
    <script>
        (function () {
            var options = {
                series: [{{ round($storage_limit, 2) }}],
                chart: {
                    height: 350,
                    type: 'radialBar',
                    offsetY: -20,
                    sparkline: {
                        enabled: true
                    }
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -90,
                        endAngle: 90,
                        track: {
                            background: "#e7e7e7",
                            strokeWidth: '97%',
                            margin: 5, // margin is in pixels
                        },
                        dataLabels: {
                            name: {
                                show: true
                            },
                            value: {
                                offsetY: -50,
                                fontSize: '20px'
                            }
                        }
                    }
                },
                grid: {
                    padding: {
                        top: -10
                    }
                },
                colors: ["#6FD943"],
                labels: ['Used'],
            };
            var chart = new ApexCharts(document.querySelector("#device-chart"), options);
            chart.render();
        })();
        // Données pour les graphiques
        const revenueData = {
            labels: {{ json_encode($revenueData['labels'] ?? []) }},
            datasets: [{
                label: 'Chiffre d\'Affaires (M FCFA)',
                data: {{ json_encode($revenueData['values'] ?? []) }},
                borderColor: 'rgba(105, 110, 255, 1)',
                backgroundColor: 'rgba(105, 110, 255, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgba(105, 110, 255, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }, {
                label: 'Objectif (M FCFA)',
                data: {{ json_encode($revenueData['targets'] ?? []) }},
                borderColor: 'rgba(255, 77, 77, 1)',
                backgroundColor: 'rgba(255, 77, 77, 0.1)',
                borderDash: [5, 5],
                tension: 0.4,
                fill: false,
                pointRadius: 0
            }]
        };

        const expensesData = {
            labels: {{ json_encode($expensesData['labels'] ?? []) }},
            datasets: [{
                data: {{ json_encode($expensesData['values'] ?? []) }},
                backgroundColor: [
                    'rgba(105, 110, 255, 0.8)',
                    'rgba(3, 195, 236, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(40, 208, 148, 0.8)'
                ],
                borderColor: [
                    'rgba(105, 110, 255, 1)',
                    'rgba(3, 195, 236, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(40, 208, 148, 1)'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        };

        // Configuration des graphiques
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        };

        // Initialisation des graphiques
        document.addEventListener('DOMContentLoaded', function () {
            // Graphique du chiffre d'affaires
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: revenueData,
                options: {
                    ...chartOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return value + 'M';
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Graphique des charges
            const expensesCtx = document.getElementById('expensesChart').getContext('2d');
            new Chart(expensesCtx, {
                type: 'doughnut',
                data: expensesData,
                options: {
                    ...chartOptions,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 11
                                },
                                generateLabels: function (chart) {
                                    const data = chart.data;
                                    if (data.labels && data.datasets.length) {
                                        const totalSalary = {{ $stats['total_salary'] ?? 0 }};
                                        const percentages = [63.1, 11.9, 17.1, 8.0]; // Pourcentages fictifs
                                        return data.labels.map(function (label, i) {
                                            const value = data.datasets[0].data[i];
                                            const percentage = percentages[i];
                                            return {
                                                text: label + ' (' + percentage + '%)',
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        }
                    }
                }
            });
        });

        // Fonctions utilitaires
        function refreshDashboard() {
            // Afficher un loader
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-loader me-1"></i>Actualisation...';
            btn.disabled = true;

            // Simulation de l'actualisation
            setTimeout(() => {
                location.reload();
            }, 1500);
        }

        function setPeriod(period) {
            console.log('Période changée vers:', period);
            // Mettre à jour les données selon la période sélectionnée
            updateDashboardData(period);
        }

        function changeChartPeriod(period) {
            console.log('Période du graphique changée vers:', period);
            // Mettre à jour les données du graphique
            updateChartData(period);
        }

        function filterActivity(type) {
            console.log('Filtre d\'activité:', type);
            // Filtrer les éléments de la timeline
            filterTimelineItems(type);
        }

        function exportDashboard(format) {
            console.log('Export du dashboard en:', format);
            // Implémenter l'export
            if (format === 'pdf') {
                alert('Export PDF en cours de développement');
            } else if (format === 'excel') {
                alert('Export Excel en cours de développement');
            }
        }

        function updateDashboardData(period) {
            // Simulation de la mise à jour des données
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.style.opacity = '0.5';
            });

            setTimeout(() => {
                cards.forEach(card => {
                    card.style.opacity = '1';
                });
                // Ici on mettrait à jour les données depuis l'API
            }, 1000);
        }

        function updateChartData(period) {
            // Simulation de la mise à jour des graphiques
            console.log('Mise à jour des graphiques pour la période:', period);
        }

        function filterTimelineItems(type) {
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach(item => {
                if (type === 'all') {
                    item.style.display = 'block';
                } else {
                    // Logique de filtrage selon le type
                    item.style.display = Math.random() > 0.3 ? 'block' : 'none';
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function () {
            var swiper = new Swiper("#swiper-with-pagination-cards", {
                autoplay: {
                    delay: 9000, // 9 secondes
                    disableOnInteraction: false,
                },
                // Ajoutez ici d'autres options si nécessaire
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var progressBar = document.getElementById('plan-progress');
            var percentage = document.getElementById('percentage').value;
            var percentageDisplay = document.getElementById('progress-percentage');
            var currentWidth = 0;
            var interval = setInterval(function () {
                if (currentWidth >= percentage) {
                    clearInterval(interval);
                } else {
                    currentWidth++;
                    progressBar.style.width = currentWidth + '%';
                    progressBar.setAttribute('aria-valuenow', currentWidth);
                    percentageDisplay.textContent = currentWidth + '%';
                }
            }, 20);
        });

        document.addEventListener('DOMContentLoaded', function () {
            var partEmployee = Math.round(document.getElementById('partEmploye').value);
            var partEmployeur = Math.round(document.getElementById('partEmployeur').value);
            var options = {
                series: [partEmployeur, partEmployee],
                chart: {
                    type: 'donut',
                    height: 200,
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                labels: ['Patronales', 'Employés'],
                colors: ['#008FFB', '#00E396'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%'
                        }
                    }
                },
                dataLabels: {
                    formatter: function (val, opts) {
                        return Math.round(opts.w.config.series[opts.seriesIndex]).toLocaleString('fr-FR') + ' FCFA';
                    }
                },
                legend: {
                    show: false
                }
            };

            var chart = new ApexCharts(document.querySelector("#chargesDiagram"), options);
            chart.render();
        });
    </script>
@endpush