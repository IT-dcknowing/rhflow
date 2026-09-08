@php
    $totalLoan = 0;
    // Compatibilité avec les versions de contrôleur antérieures au contrôle du pack.
    $hasPreviousBulletins = $hasPreviousBulletins ?? false;
    $isPackActive = $isPackActive ?? false;

    $hasCurrentElements = (isset($retenues) && $retenues->count() > 0) ||
        (isset($allowances) && $allowances->count() > 0) ||
        (isset($avantages) && $avantages->count() > 0);

    $hasPreviousElementsToCopy = isset($previousPeriode) && $previousPeriode &&
        isset($previousElements) && $previousElements->count() > 0;

    if (!isset($previousPeriode) || !$previousPeriode) {
        $guideState = 'first_time';
    } elseif (!$hasCurrentElements && $hasPreviousElementsToCopy) {
        $guideState = 'needs_copy';
    } else {
        $guideState = 'ready_for_calc';
    }

    // Alertes automatiques
    $alerts = [];
    if (isset($activeLoans)) {
        foreach ($activeLoans as $loan) {
            $startDate = \Carbon\Carbon::parse($loan->start_date);
            $totalPaid = $loan->payments->sum('amount') ?? 0;
            for ($i = 0; $i < $loan->nbre_mois; $i++) {
                $paymentDate = (clone $startDate)->addMonths($i);
                $isPaid = $totalPaid >= ($i + 1) * $loan->amount_deduc;
                if (!$isPaid && $paymentDate->isPast()) {
                    $alerts[] = [
                        'type' => 'warning',
                        'message' => ($loan->employee->name ?? '?') . ' — prêt en retard (échéance ' . $paymentDate->format('d/m/Y') . ')'
                    ];
                    break;
                }
            }
        }
    }
    if (isset($conges)) {
        foreach ($conges as $conge) {
            $alerts[] = [
                'type' => 'info',
                'message' => ($conge->employee->name ?? '?') . ' — congé du ' . $conge->date_debut->format('d/m/Y') . ' au ' . $conge->date_fin->format('d/m/Y')
            ];
        }
    }
@endphp

@extends('layouts.app')

@section('title', 'Période : ' . $periode->nom)

@push('styles')
    <style>
        /* =====================================================
       VARIABLES & BASE
       ===================================================== */
        .paie-wrap {
            padding: 1.5rem 0;
        }

        /* TOGGLE MODE */
        .mode-toggle-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        .mode-toggle {
            display: flex;
            gap: 4px;
            background: #f1f3f5;
            border-radius: 10px;
            padding: 4px;
        }

        .mode-btn {
            padding: 7px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            background: transparent;
            color: #6c757d;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .mode-btn.active {
            background: #fff;
            color: #253e87;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .1);
        }

        /* BREADCRUMB ZONE */
        .periode-header-title {
            font-size: 18px;
            font-weight: 600;
            color: #32475c;
            margin-bottom: 2px;
        }

        .periode-header-sub {
            font-size: 12px;
            color: #6c757d;
        }

        /* BADGE STATUT */
        .badge-statut {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        /* =====================================================
       MODE PAIE RAPIDE
       ===================================================== */
        .quick-panel {
            display: none;
        }

        .quick-panel.active {
            display: block;
        }

        .quick-section {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            margin-bottom: 1.25rem;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }

        .quick-section:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-color: #d1d5db;
        }

        .quick-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #f1f3f5;
            gap: 12px;
            flex-wrap: wrap;
        }

        .quick-section-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .quick-section-icon.green {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .quick-section-icon.blue {
            background: #e3f2fd;
            color: #1565c0;
        }

        .quick-section-icon.amber {
            background: #fff8e1;
            color: #f57f17;
        }

        .quick-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #32475c;
        }

        .quick-section-sub {
            font-size: 13px;
            color: #6c757d;
            margin-top: 1px;
        }

        /* TABLEAU EMPLOYES RAPIDE */
        .emp-quick-table {
            width: 100%;
            border-collapse: collapse;
        }

        .emp-quick-table tbody tr {
            border-bottom: 1px solid #f1f3f5;
            transition: background .1s;
        }

        .emp-quick-table tbody tr:last-child {
            border-bottom: none;
        }

        .emp-quick-table tbody tr:hover {
            background: #fafbfc;
        }

        .emp-quick-table td {
            padding: 10px 1.25rem;
            vertical-align: middle;
        }

        .emp-name {
            font-size: 13px;
            font-weight: 500;
            color: #32475c;
        }

        .emp-mat {
            font-size: 11px;
            color: #6c757d;
        }

        .pill-group {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .pill-label {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid #dee2e6;
            background: #f8f9fa;
            color: #6c757d;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: all .15s;
            user-select: none;
        }

        .pill-label input[type="checkbox"] {
            width: 12px;
            height: 12px;
            accent-color: #253e87;
            cursor: pointer;
        }

        .pill-label:has(input:checked) {
            background: #e8edf8;
            color: #253e87;
            border-color: #b3c0e0;
        }

        .emp-total {
            font-size: 13px;
            font-weight: 600;
            color: #253e87;
            text-align: right;
            white-space: nowrap;
        }

        /* ALERTES RAPIDES */
        .quick-alert-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 1.25rem;
            font-size: 12px;
            color: #32475c;
            border-bottom: 1px solid #f1f3f5;
        }

        .quick-alert-item:last-child {
            border-bottom: none;
        }

        .alert-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .alert-dot.warning {
            background: #f57f17;
        }

        .alert-dot.info {
            background: #1565c0;
        }

        .alert-dot.success {
            background: #2e7d32;
        }

        /* RESUME BAR */
        .resume-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 1rem 1.25rem;
            background: #fafbfc;
            border-top: 1px solid #f1f3f5;
        }

        .res-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: .75rem 1rem;
        }

        .res-label {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 3px;
        }

        .res-val {
            font-size: 18px;
            font-weight: 700;
            color: #253e87;
        }

        .res-val.danger {
            color: #c62828;
        }

        .res-val.success {
            color: #2e7d32;
        }

        /* BOUTON CALCUL PRINCIPAL */
        .btn-calc-main {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #28c76f 0%, #1a9e55 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all .25s;
            position: relative;
            overflow: hidden;
            margin-top: 1rem;
        }

        .btn-calc-main:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 199, 111, .35);
            color: #fff;
        }

        .btn-calc-main::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, .25), transparent);
            transform: skewX(-20deg);
            animation: btnshine 3s infinite;
        }

        @keyframes btnshine {
            0% {
                left: -100%
            }

            20% {
                left: 200%
            }

            100% {
                left: 200%
            }
        }

        /* =====================================================
       MODE DÉTAILLÉ — STEPPER
       ===================================================== */
        .detail-panel {
            display: none;
        }

        .detail-panel.active {
            display: block;
        }

        .stepper-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .stepper-grid::before {
            content: '';
            position: absolute;
            top: 28px;
            left: 14%;
            right: 14%;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }

        .step-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem .75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all .2s;
            position: relative;
            z-index: 1;
        }

        .step-card:hover {
            border-color: #b3c0e0;
            box-shadow: 0 4px 12px rgba(37, 62, 135, .08);
        }

        .step-card.active {
            border-color: #253e87;
            background: #f0f4ff;
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(37, 62, 135, 0.15);
        }

        .step-card.done {
            border-color: #a5d6a7;
            background: #f1f8f2;
        }

        .step-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f1f3f5;
            color: #6c757d;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -8px;
            right: -8px;
            border: 2px solid #fff;
            transition: all .2s;
        }

        .step-card.active .step-badge {
            background: #253e87;
            color: #fff;
        }

        .step-card.done .step-badge {
            background: #2e7d32;
            color: #fff;
        }

        .step-ico {
            font-size: 22px;
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #32475c;
            text-align: center;
        }

        .step-desc {
            font-size: 10px;
            color: #6c757d;
            text-align: center;
            line-height: 1.4;
        }

        /* CONTENU DYNAMIQUE DE L'ÉTAPE */
        .step-content-box {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .step-content-header {
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid #f1f3f5;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .step-content-links {
            padding: .75rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .step-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            text-decoration: none;
            color: #32475c;
            font-size: 13px;
            font-weight: 500;
            transition: all .15s;
        }

        .step-link:hover {
            background: #fff;
            border-color: #b3c0e0;
            color: #253e87;
            box-shadow: 0 2px 8px rgba(37, 62, 135, .08);
        }

        .step-link i {
            width: 18px;
            text-align: center;
            font-size: 13px;
            opacity: .7;
        }

        .step-link:hover i {
            opacity: 1;
            color: #253e87;
        }

        .step-link .link-arr {
            margin-left: auto;
            color: #adb5bd;
            font-size: 12px;
        }

        /* ACCORDION DONNÉES SALARIALES */
        .salary-accordion .accordion-button {
            font-size: 13px;
            padding: .5rem 1rem;
        }

        .salary-accordion .accordion-body {
            padding: 1rem;
        }

        /* ACTIONS PRÉALABLES */
        .preliminary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            padding: 0.85rem 1.25rem;
        }

        .pre-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem;
        }

        .pre-title {
            font-size: 15px;
            font-weight: 600;
            color: #32475c;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pre-links {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .pre-link-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            text-decoration: none;
            color: #444;
            font-size: 14px;
            font-weight: 600;
            transition: all .15s;
        }

        .pre-link-item:hover {
            transform: translateX(3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
        }

        .pre-link-item.prime-card {
            background: #f0fdf4;
            border-color: #dcfce7;
            color: #166534;
        }
        .pre-link-item.prime-card:hover {
            background: #dcfce7;
            border-color: #bbf7d0;
        }

        .pre-link-item.retenue-card {
            background: #fef2f2;
            border-color: #fee2e2;
            color: #991b1b;
        }
        .pre-link-item.retenue-card:hover {
            background: #fee2e2;
            border-color: #fecaca;
        }

        .pre-link-item i {
            width: 20px;
            text-align: center;
            opacity: .9;
            font-size: 18px;
        }

        /* RESPONSIVE */
        @media (max-width: 767px) {
            .preliminary-grid {
                grid-template-columns: 1fr;
            }

            .stepper-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stepper-grid::before {
                display: none;
            }

            .resume-grid {
                grid-template-columns: 1fr 1fr;
            }

            .emp-quick-table thead {
                display: none;
            }

            .emp-quick-table td {
                display: block;
                padding: 4px 1rem;
            }

            .emp-quick-table tr {
                padding: 8px 0;
            }

            .pill-group {
                margin: 4px 0;
            }
        }

        /* GUIDE MODAL STYLES */
        .guide-step {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            position: relative;
        }

        .guide-step:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 35px;
            bottom: -15px;
            width: 2px;
            background: #e9ecef;
            border-left: 2px dashed #dee2e6;
        }

        .guide-step-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eef1f9;
            color: #253e87;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            z-index: 1;
            box-shadow: 0 0 0 4px #fff;
        }

        .guide-step.active .guide-step-icon {
            background: #253e87;
            color: #fff;
        }

        .guide-step-content {
            padding-top: 5px;
        }

        .guide-step-title {
            font-size: 14px;
            font-weight: 600;
            color: #32475c;
            margin-bottom: 3px;
        }

        .guide-step-desc {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.4;
        }

        .guide-note {
            background: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 12px;
            border-radius: 6px;
            font-size: 12px;
            color: #856404;
            margin-top: 15px;
        }

        .custom-toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #253e87;
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            z-index: 9999;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            font-size: 14px;
            opacity: 0;
        }
        .custom-toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y paie-wrap">

        {{-- ===== EN-TÊTE ===== --}}
        <div class="mode-toggle-bar mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a
                                href="{{ route('company.paiesalaries.exercices.index') }}">Exercices</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('company.paiesalaries.exercices.show', $periode->exercice_id) }}">{{ $periode->exercice->nom }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $periode->nom }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="periode-header-title mb-0">📅 {{ $periode->nom }}</h4>
                    <span
                        class="badge-statut badge bg-label-{{ $periode->statut === 'validee' ? 'success' : ($periode->statut === 'payee' ? 'info' : ($periode->statut === 'annulee' ? 'danger' : 'warning')) }}">
                        {{ $periode->statut }}
                    </span>
                </div>
                <div class="periode-header-sub mt-1">
                    {{ $periode->date_debut->format('d/m/Y') }} → {{ $periode->date_fin->format('d/m/Y') }}
                    &nbsp;·&nbsp; Paiement : {{ $periode->date_paiement->format('d/m/Y') }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-label-info" data-bs-toggle="modal" data-bs-target="#guidePaieModal">
                    <i class="fas fa-question-circle me-1"></i>Guide
                </button>
                <a href="{{ route('company.paiesalaries.periodes.edit', $periode->id) }}"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-edit me-1"></i>Modifier
                </a>
                <a href="{{ route('company.paiesalaries.exercices.show', $periode->exercice_id) }}"
                    class="btn btn-sm btn-label-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
                <form action="{{ route('company.paiesalaries.periodes.recopier-primes', $periode->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Recopier les primes de la période précédente vers {{ $periode->nom }} ?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-warning text-white">
                        <i class="fas fa-copy me-1"></i>Recopier les primes
                    </button>
                </form>
                {{-- TOGGLE MODE --}}
                <div class="mode-toggle" id="modeToggle">
                    <button class="mode-btn active" onclick="setMode('quick')" id="btnQuick">
                        <i class="fas fa-bolt"></i> Paie rapide
                    </button>
                    <button class="mode-btn" onclick="setMode('detail')" id="btnDetail">
                        <i class="fas fa-list-ol"></i> Détaillé
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================================================
        MODE PAIE RAPIDE
        ============================================================ --}}
        <div class="quick-panel active" id="quickPanel">

            {{-- 1. REPORTER LES ÉLÉMENTS --}}
            @if($periode->statut !== 'validee' && $periode->statut !== 'payee' && $previousPeriode && $previousElements->count() > 0)
                    <div class="quick-section">
                        <div class="quick-section-header">
                            <div class="d-flex align-items-center gap-3">
                                <div class="quick-section-icon green"><i class="fas fa-copy"></i></div>
                                <div>
                                    <div class="quick-section-title">Reporter les éléments du mois précédent</div>
                                    <div class="quick-section-sub">
                                        {{ $previousPeriode->nom }} &nbsp;·&nbsp;
                                        {{ $previousElements->count() }} employé(s)
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary" onclick="quickToggleAll(true)">
                                    <i class="fas fa-check-double me-1"></i>Tout cocher
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="quickToggleAll(false)">
                                    <i class="fas fa-times me-1"></i>Tout décocher
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('company.paiesalaries.periodes.duplicate-elements', $periode->id) }}" method="POST"
                            id="quickReportForm">
                            @csrf
                            <div class="table-responsive">
                                <table class="emp-quick-table">
                                    <thead>
                                        <tr
                                            style="background:#fafbfc; font-size:11px; color:#6c757d; text-transform:uppercase; letter-spacing:.5px;">
                                            <td style="padding:8px 1.25rem;">Employé</td>
                                            <td style="padding:8px 1.25rem;">Éléments à reporter</td>
                                            <td style="padding:8px 1.25rem; text-align:right;">Total estimé</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($previousElements as $employeeId => $employeeData)
                                            @php
                                                $empTotal = $employeeData['allowances']->sum('amount')
                                                    + $employeeData['avantages']->sum('amount_reel')
                                                    + $employeeData['retenues']->sum('amount');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="emp-name">{{ $employeeData['employee']->name }}</div>
                                                    @if($employeeData['employee']->matricule)
                                                        <div class="emp-mat">Mat : {{ $employeeData['employee']->matricule }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="pill-group">
                                                        @foreach($employeeData['allowances'] as $allowance)
                                                            <label class="pill-label">
                                                                <input type="checkbox" name="elements[]"
                                                                    value="allowance_{{ $allowance->id }}" class="quick-check"
                                                                    data-amount="{{ $allowance->amount }}" data-emp="{{ $employeeId }}"
                                                                    @if(!in_array($allowance->code, ['103', '123', '138'])) checked @endif>
                                                                {{ $allowance->title }}
                                                            </label>
                                                        @endforeach

                                                        @foreach($employeeData['avantages'] as $avantage)
                                                            <label class="pill-label">
                                                                <input type="checkbox" name="elements[]"
                                                                    value="avantage_{{ $avantage->id }}" class="quick-check"
                                                                    data-amount="{{ $avantage->amount_reel }}" data-emp="{{ $employeeId }}"
                                                                    checked>
                                                                {{ $avantage->libelle }}
                                                            </label>
                                                        @endforeach

                                                        @foreach($employeeData['retenues'] as $retenue)
                                                            <label class="pill-label">
                                                                <input type="checkbox" name="elements[]" value="retenue_{{ $retenue->id }}"
                                                                    class="quick-check" data-amount="{{ $retenue->amount }}"
                                                                    data-emp="{{ $employeeId }}" checked>
                                                                {{ $retenue->libelle }}
                                                            </label>
                                                        @endforeach

                                                        @foreach($employeeData['loans'] as $loan)
                                                            <label class="pill-label">
                                                                <input type="checkbox" name="elements[]" value="loan_{{ $loan->id }}"
                                                                    class="quick-check" data-amount="{{ $loan->amount_deduc }}"
                                                                    data-emp="{{ $employeeId }}" checked>
                                                                Prêt · {{ $loan->loanOption->name ?? '' }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="emp-total" id="empTotal_{{ $employeeId }}">
                                                        {{ number_format($empTotal, 0, ',', ' ') }} FCFA
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div
                                style="padding:1rem 1.25rem; border-top:1px solid #f1f3f5; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                                <div style="font-size:13px; color:#6c757d;">
                                    Total sélectionné : <strong id="grandTotalQuick" style="color:#253e87;">
                                        {{ number_format(
                    $previousElements->sum(fn($e) => $e['allowances']->sum('amount') + $e['avantages']->sum('amount_reel') + $e['retenues']->sum('amount')),
                    0,
                    ',',
                    ' '
                ) }} FCFA
                                    </strong>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-copy me-2"></i>Reporter les éléments sélectionnés
                                </button>
                            </div>
                        </form>
                    </div>
            @endif
 
            {{-- 2. ACTIONS PRÉALABLES --}}
            <div class="quick-section">
                <div class="quick-section-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="quick-section-icon blue"><i class="fas fa-tasks"></i></div>
                        <div>
                            <div class="quick-section-title">Actions préalables</div>
                            <div class="quick-section-sub">Gérez les éléments variables avant le calcul</div>
                        </div>
                    </div>
                </div>
                
                <div class="preliminary-grid">
                    {{-- Bloc Primes --}}
                    <a href="{{ route('company.paiesalaries.allowance.index') }}?periode_id={{ $periode->id }}" class="pre-link-item prime-card">
                        <i class="fas fa-plus-circle text-success fa-lg"></i>
                        <div class="ms-2">
                            <div class="fw-bold">Primes & Indemnités</div>
                            <div class="text-muted" style="font-size:12px;">Gérer les éléments du brut</div>
                        </div>
                    </a>

                    {{-- Bloc Retenues --}}
                    <a href="{{ route('company.paiesalaries.retenues.index') }}?periode_id={{ $periode->id }}" class="pre-link-item retenue-card">
                        <i class="fas fa-minus-circle text-danger fa-lg"></i>
                        <div class="ms-2">
                            <div class="fw-bold">Retenues sur paie</div>
                            <div class="text-muted" style="font-size:12px;">Gérer les retenues diverses</div>
                        </div>
                    </a>
                </div>

                {{-- DONNÉES SALARIALES APPLIQUÉES (Limité aux Primes et Retenues) --}}
                @if($retenues->count() > 0 || $allowances->count() > 0)
                    <div class="px-4 pb-4">
                        <div class="accordion salary-accordion" id="salaryDataAccordion">
                            @if($allowances->count() > 0)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colPrimes">
                                            <i class="fas fa-plus-circle text-success me-2"></i>Primes ({{ $allowances->count() }})
                                        </button>
                                    </h2>
                                    <div id="colPrimes" class="accordion-collapse collapse" data-bs-parent="#salaryDataAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead><tr><th>Code</th><th>Titre</th><th>Employé</th><th class="text-end">Montant</th></tr></thead>
                                                    <tbody>
                                                        @foreach($allowances as $allowance)
                                                            <tr>
                                                                <td><span class="badge bg-label-info">{{ $allowance->code }}</span></td>
                                                                <td>{{ $allowance->title }}</td>
                                                                <td>{{ $allowance->employee->name ?? '-' }}</td>
                                                                <td class="text-end"><strong>{{ number_format($allowance->amount, 0, ',', ' ') }}</strong></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($retenues->count() > 0)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#colRetenues">
                                            <i class="fas fa-minus-circle text-danger me-2"></i>Retenues ({{ $retenues->count() }})
                                        </button>
                                    </h2>
                                    <div id="colRetenues" class="accordion-collapse collapse" data-bs-parent="#salaryDataAccordion">
                                        <div class="accordion-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead><tr><th>Code</th><th>Libellé</th><th>Employé</th><th class="text-end">Montant</th></tr></thead>
                                                    <tbody>
                                                        @foreach($retenues as $retenue)
                                                            <tr>
                                                                <td><span class="badge bg-label-danger">{{ $retenue->code }}</span></td>
                                                                <td>{{ $retenue->libelle }}</td>
                                                                <td>{{ $retenue->employee->name ?? '-' }}</td>
                                                                <td class="text-end"><strong>{{ number_format($retenue->amount, 0, ',', ' ') }}</strong></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- 2 bis. ÉCHÉANCES DE PRÊT --}}
            @if($loanPayments->count() > 0)
                <div class="quick-section">
                    <div class="quick-section-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="quick-section-icon amber"><i class="fas fa-university"></i></div>
                            <div>
                                <div class="quick-section-title">Échéances de prêt ({{ $loanPayments->count() }})</div>
                                <div class="quick-section-sub">
                                    Appliquez l'échéance du mois pour qu'elle soit retenue sur le bulletin.
                                    Un prêt n'entre dans la paie que période par période.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 pb-4">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Employé</th>
                                        <th>Prêt</th>
                                        <th class="text-center">Avancement</th>
                                        <th class="text-end">Reste dû</th>
                                        <th class="text-end">Échéance</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loanPayments as $echeance)
                                        <tr>
                                            <td>{{ $echeance->loan->employee->name ?? '-' }}</td>
                                            <td>
                                                {{ $echeance->loan->title }}
                                                @if($echeance->hors_periode)
                                                    <br>
                                                    <small class="text-warning">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        Hors échéancier ({{ \Carbon\Carbon::parse($echeance->loan->start_date)->format('m/Y') }}
                                                        → {{ \Carbon\Carbon::parse($echeance->loan->end_date)->format('m/Y') }})
                                                    </small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-info">
                                                    {{ $echeance->echeances_payees }} / {{ $echeance->nbre_mois }} mois
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <strong>{{ number_format($echeance->remaining_amount, 0, ',', ' ') }}</strong> FCFA
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($echeance->amount, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="text-end">
                                                @if($echeance->applied)
                                                    <span class="badge bg-label-success me-1">
                                                        <i class="fas fa-check me-1"></i>Appliquée
                                                    </span>
                                                    @unless(in_array($periode->statut, ['validee', 'payee', 'cloture', 'annulee']))
                                                        <form action="{{ route('company.paiesalaries.periodes.loanpaiement.retirer', $echeance->loan->id) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Retirer l\'échéance de ce prêt pour {{ $periode->nom }} ? Le prêt reste actif, il ne sera simplement pas retenu ce mois-ci.');">
                                                            @csrf
                                                            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                                    title="Ne pas retenir ce prêt sur cette période">
                                                                <i class="fas fa-ban me-1"></i>Retirer
                                                            </button>
                                                        </form>
                                                    @endunless
                                                @elseif(in_array($periode->statut, ['validee', 'payee', 'cloture', 'annulee']))
                                                    <span class="badge bg-label-secondary">Période {{ $periode->statut }}</span>
                                                @else
                                                    <form action="{{ route('company.paiesalaries.periodes.loanpaiement', $echeance->loan->id) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Appliquer une échéance de {{ number_format($echeance->amount, 0, ',', ' ') }} FCFA sur cette période ?');">
                                                        @csrf
                                                        <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                                                        <input type="hidden" name="amount" value="{{ (int) $echeance->amount }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-plus me-1"></i>Appliquer
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 2. ALERTES --}}
            @if(count($alerts) > 0)
                <div class="quick-section">
                    <div class="quick-section-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="quick-section-icon amber"><i class="fas fa-bell"></i></div>
                            <div>
                                <div class="quick-section-title">Points d'attention ({{ count($alerts) }})</div>
                                <div class="quick-section-sub">Vérifiez ces éléments avant de calculer</div>
                            </div>
                        </div>
                    </div>
                    @foreach($alerts as $alert)
                        <div class="quick-alert-item">
                            <span class="alert-dot {{ $alert['type'] }}"></span>
                            {{ $alert['message'] }}
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 3. RÉSUMÉ + CALCUL --}}
            <div class="quick-section">
                <div class="quick-section-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="quick-section-icon blue"><i class="fas fa-rocket"></i></div>
                        <div>
                            <div class="quick-section-title">Calcul de la paie</div>
                            <div class="quick-section-sub">Générez les bulletins pour tous les employés</div>
                        </div>
                    </div>
                </div>

                <div class="resume-grid">
                    <div class="res-card">
                        <div class="res-label">Bulletins</div>
                        <div class="res-val">{{ $periode->bulletins_count ?? 0 }}</div>
                    </div>
                    <div class="res-card">
                        <div class="res-label">Total brut</div>
                        <div class="res-val">{{ number_format($periode->bulletins_sum_brut ?? 0, 0, ',', ' ') }}</div>
                    </div>
                    <div class="res-card">
                        <div class="res-label">Total charges</div>
                        <div class="res-val danger">
                            {{ number_format(($periode->bulletins_sum_brut ?? 0) - ($periode->bulletins_sum_net ?? 0), 0, ',', ' ') }}
                        </div>
                    </div>
                </div>

                <div style="padding:0 1.25rem 1.25rem;">
                    <a href="{{ route('company.paiesalaries.calcule') }}?periode_id={{ $periode->id }}"
                        class="btn-calc-main">
                        <i class="fas fa-calculator"></i> Calculer la paie maintenant
                    </a>
                    @if($hasPreviousBulletins && !$isPackActive)
                        <a href="{{ route('company.plan.pricing') }}" class="btn btn-danger w-100 mt-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>Pack expiré - Renouveler
                        </a>
                    @else
                        @if(($periode->bulletins_count ?? 0) > 0)
                            <div class="alert alert-info mt-2 mb-0" style="font-size: 0.85rem; border-left: 4px solid #00cfe8;">
                                <i class="fas fa-info-circle me-2"></i> Aucun bulletin ne peut être encore généré pour ce mois. Passez à la paie suivante pour générer.
                            </div>
                        @else
                            <button type="button" class="btn btn-primary w-100 mt-2" data-bs-toggle="modal"
                                data-bs-target="#genererBulletinsModal">
                                <i class="fas fa-file-invoice me-2"></i>Générer les bulletins
                            </button>
                        @endif
                    @endif
                    @if($periode->bulletins_count > 0)
                        <a href="{{ route('company.declarations.dashboard') }}" class="btn btn-outline-info w-100 mt-2">
                            <i class="fas fa-file me-2"></i>Consulter les états de paie
                        </a>
                    @endif
                    @if(in_array($periode->statut, ['brouillon', 'en_cours']))
                        <a href="#" class="btn btn-outline-success w-100 mt-2" data-bs-toggle="modal"
                            data-bs-target="#validerPaiementModal">
                            <i class="fas fa-check-circle me-2"></i>Valider le paiement
                        </a>
                    @endif
                </div>
            </div>

        </div>{{-- /quick-panel --}}


        {{-- ============================================================
        MODE DÉTAILLÉ
        ============================================================ --}}
        <div class="detail-panel" id="detailPanel">

            {{-- STEPPER --}}
            <div class="stepper-grid" id="stepperGrid">
 
                <div class="step-card active" onclick="showStep(0)" id="step_0">
                    <span class="step-badge">1</span>
                    <div class="step-ico">🎁</div>
                    <div class="step-label">Avantages & H.S</div>
                    <div class="step-desc">Nature · Argent · H.Supp</div>
                </div>
 
                <div class="step-card" onclick="showStep(1)" id="step_1">
                    <span class="step-badge">2</span>
                    <div class="step-ico">🏦</div>
                    <div class="step-label">Prêts & Frais</div>
                    <div class="step-desc">Echéances · Frais prof.</div>
                </div>
 
                <div class="step-card" onclick="showStep(2)" id="step_2">
                    <span class="step-badge">3</span>
                    <div class="step-ico">🗓️</div>
                    <div class="step-label">Temps & abs.</div>
                    <div class="step-desc">Absences · Congés · Ruptures</div>
                </div>
 
                <div class="step-card" onclick="showStep(3)" id="step_3">
                    <span class="step-badge">4</span>
                    <div class="step-ico">🧮</div>
                    <div class="step-label">Calcul final</div>
                    <div class="step-desc">Générer les bulletins</div>
                </div>
 
            </div>

            {{-- CONTENU DYNAMIQUE SELON L'ÉTAPE --}}
            <div class="step-content-box" id="stepContentBox">
                {{-- rempli par JS --}}
            </div>

        </div>{{-- /detail-panel --}}

    </div>

    {{-- MODALS --}}
    @include('paiesalaries::periodes.modals.generer-bulletins')
    @include('paiesalaries::periodes.modals.valider-paiement')

    {{-- PRE-CHECK MODAL (si période précédente existe) --}}
    @if($previousPeriode)
    <div class="modal fade" id="precheckPaieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-0 p-3 position-absolute end-0 top-0" style="z-index: 10;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <div class="icon-circle bg-label-primary mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                            <i class="fas fa-sync-alt fa-2x animate__animated animate__rotateIn animate__infinite" style="--animate-duration: 3s"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-2">Changements ce mois-ci ?</h4>
                    <p class="text-muted px-3 mb-4">
                        Souhaitez-vous ajuster les éléments de paie par rapport au mois de <span class="badge bg-label-primary">{{ $previousPeriode->nom }}</span> ?
                    </p>
                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-primary btn-lg shadow-sm" onclick="handlePrecheck(true)">
                            <i class="fas fa-pencil-alt me-2"></i>Oui, j'ai des modifications
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg" onclick="handlePrecheck(false)">
                            <i class="fas fa-check-circle me-2"></i>Non, copier tout à l'identique
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- GUIDE MODAL --}}
    <div class="modal fade" id="guidePaieModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">🚀 Guide de traitement de la paie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="guide-step">
                        <div class="guide-step-icon">1</div>
                        <div class="guide-step-content">
                            <div class="guide-step-title">Primes & Retenues</div>
                            <div class="guide-step-desc">Ajoutez les primes et appliquez les retenues dans l'onglet <strong>Paie rapide</strong>.</div>
                        </div>
                    </div>

                    <div class="guide-step">
                        <div class="guide-step-icon">2</div>
                        <div class="guide-step-content">
                            <div class="guide-step-title">Calcul de la paie</div>
                            <div class="guide-step-desc">Cliquez sur le bouton vert <strong>Calculer la paie</strong> pour mettre à jour les montants.</div>
                        </div>
                    </div>

                    <div class="guide-step">
                        <div class="guide-step-icon">3</div>
                        <div class="guide-step-content">
                            <div class="guide-step-title">Bulletins</div>
                            <div class="guide-step-desc">Une fois le calcul fait, générez les bulletins de paie pour vos employés.</div>
                        </div>
                    </div>

                    <div class="guide-note">
                        <i class="fas fa-info-circle me-1"></i> <strong>Note :</strong> Pour ajouter des éléments variables complexes (absences, congés, avantages), cliquez sur l'onglet <strong>Détaillé</strong>.
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">J'ai compris</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script>
        /* =====================================================
           DONNÉES DES ÉTAPES (injectées côté serveur)
           ===================================================== */
        const PERIODE_ID = {{ $periode->id }};
        const BASE_URL = '{{ url('') }}';

        const STEPS = [
            {
                ico: '🎁', title: 'Étape 1 — Avantages & Heures Supp.',
                sub: 'Gérez les avantages en nature/argent et les heures supplémentaires',
                links: [
                    { icon: 'fas fa-gift', color: '#2e7d32', label: 'Avantages', url: '{{ route("company.avantages.index") }}?periode_id=' + PERIODE_ID },
                    { icon: 'fas fa-clock', color: '#6a1b9a', label: 'Heures supplémentaires', url: '{{ route("company.times.overtime.index") }}?periode_id=' + PERIODE_ID },
                ]
            },
            {
                ico: '🏦', title: 'Étape 2 — Prêts & Frais',
                sub: 'Suivi des prêts et remboursement de frais professionnels',
                links: [
                    { icon: 'fas fa-university', color: '#e65100', label: 'Prêts en cours', url: '{{ route("company.loans.index") }}?periode_id=' + PERIODE_ID },
                    { icon: 'fas fa-receipt', color: '#f57f17', label: 'Remboursement de frais', url: '{{ route("company.paiesalaries.remboursements") }}?periode_id=' + PERIODE_ID },
                ]
            },
            {
                ico: '🗓️', title: 'Étape 3 — Temps & absences',
                sub: 'Vérifiez absences, congés et ruptures de contrat',
                links: [
                    { icon: 'fas fa-user-clock', color: '#0277bd', label: 'Absences', url: '{{ route("company.times.absences.index") }}?periode_id=' + PERIODE_ID },
                    { icon: 'fas fa-umbrella-beach', color: '#00695c', label: 'Congés', url: '{{ route("company.leaves.index") }}?periode_id=' + PERIODE_ID },
                    { icon: 'fas fa-user-slash', color: '#b71c1c', label: 'Ruptures de contrat', url: '{{ route("company.ruptures.index") }}?periode_id=' + PERIODE_ID },
                ]
            },
            {
                ico: '🧮', title: 'Étape 4 — Calcul final',
                sub: 'Générez les bulletins de paie après validation des étapes précédentes',
                links: [
                    { icon: 'fas fa-rocket', color: '#2e7d32', label: 'Lancer le calcul', url: '{{ route("company.paiesalaries.calcule") }}?periode_id=' + PERIODE_ID },
                    { 
                        icon: 'fas fa-file-invoice', 
                        color: '#696cff', 
                        label: '{{ ($hasPreviousBulletins && !$isPackActive) ? "Pack expiré - Renouveler" : (($periode->bulletins_count > 0) ? "Bulletins déjà générés" : "Générer les bulletins") }}', 
                        url: '{{ ($hasPreviousBulletins && !$isPackActive) ? route("company.plan.pricing") : "#" }}', 
                        modal: '{{ ($hasPreviousBulletins && !$isPackActive) ? "" : (($periode->bulletins_count > 0) ? "" : "#genererBulletinsModal") }}',
                        color_override: '{{ ($hasPreviousBulletins && !$isPackActive) ? "#b71c1c" : (($periode->bulletins_count > 0) ? "#a1acb8" : "") }}'
                    },
                    { icon: 'fas fa-file-alt', color: '#1565c0', label: 'Consulter les états', url: '{{ route("company.declarations.dashboard") }}' },
                ]
            }
        ];

        /* =====================================================
           MODE TOGGLE
           ===================================================== */
        function setMode(mode) {
            document.getElementById('quickPanel').classList.toggle('active', mode === 'quick');
            document.getElementById('detailPanel').classList.toggle('active', mode === 'detail');
            document.getElementById('btnQuick').classList.toggle('active', mode === 'quick');
            document.getElementById('btnDetail').classList.toggle('active', mode === 'detail');

            if (mode === 'detail') {
                const activeStep = document.querySelector('.step-card.active');
                const idx = activeStep ? parseInt(activeStep.id.split('_')[1]) : 0;
                showStep(idx);
            }

            localStorage.setItem('paieMode_{{ $periode->id }}', mode);
        }

        /* =====================================================
           STEPPER DÉTAILLÉ
           ===================================================== */
        function showStep(idx) {
            document.querySelectorAll('.step-card').forEach((el, i) => {
                el.classList.toggle('active', i === idx);
            });

            const s = STEPS[idx];
            const linksHtml = s.links.map(l => {
                if (l.modal) {
                    return `
                        <a href="javascript:void(0);" class="step-link" data-bs-toggle="modal" data-bs-target="${l.modal}">
                            <i class="${l.icon}" style="color:${l.color_override || l.color}"></i>
                            ${l.label}
                            <span class="link-arr"><i class="fas fa-chevron-right"></i></span>
                        </a>`;
                }
                return `
                    <a href="${l.url}" class="step-link">
                        <i class="${l.icon}" style="color:${l.color_override || l.color}"></i>
                        ${l.label}
                        <span class="link-arr"><i class="fas fa-chevron-right"></i></span>
                    </a>`;
            }).join('');

            document.getElementById('stepContentBox').innerHTML = `
            <div class="step-content-header">
                <div style="font-size:24px">${s.ico}</div>
                <div>
                    <div style="font-size:14px;font-weight:600;color:#32475c">${s.title}</div>
                    <div style="font-size:12px;color:#6c757d">${s.sub}</div>
                </div>
            </div>
            <div class="step-content-links">${linksHtml}</div>
        `;
        }

        /* =====================================================
           PAIE RAPIDE — CASES À COCHER
           ===================================================== */
        function quickToggleAll(state) {
            document.querySelectorAll('.quick-check').forEach(cb => cb.checked = state);
            updateQuickTotal();
        }

        function updateQuickTotal() {
            let grand = 0;
            document.querySelectorAll('.quick-check').forEach(cb => {
                if (cb.checked) grand += parseFloat(cb.dataset.amount || 0);
            });
            const el = document.getElementById('grandTotalQuick');
            if (el) el.textContent = numberFormat(grand) + ' FCFA';
        }

        function numberFormat(n) {
            return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        document.querySelectorAll('.quick-check').forEach(cb => {
            cb.addEventListener('change', updateQuickTotal);
        });

        /* =====================================================
           INIT
           ===================================================== */
        /* =====================================================
           LOGIQUE DE GUIDAGE & PRE-CHECK
           ===================================================== */
        const HAS_PREVIOUS = {{ $previousPeriode ? 'true' : 'false' }};
        const PRECHECK_MODAL = HAS_PREVIOUS ? new bootstrap.Modal(document.getElementById('precheckPaieModal')) : null;
        const GUIDE_MODAL = new bootstrap.Modal(document.getElementById('guidePaieModal'));

        function handlePrecheck(hasChanges) {
            if (PRECHECK_MODAL) PRECHECK_MODAL.hide();
            
            if (hasChanges) {
                // Montrer les étapes de modification
                setTimeout(() => GUIDE_MODAL.show(), 400);
            } else {
                // Tout est identique, on guide vers le report et le calcul
                const toast = document.createElement('div');
                toast.className = 'custom-toast';
                toast.innerHTML = '<i class="fas fa-info-circle me-2"></i> Parfait ! Vous pouvez reporter les données ci-dessous puis lancer le calcul.';
                document.body.appendChild(toast);
                setTimeout(() => toast.classList.add('show'), 100);
                setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 500); }, 5000);
            }
            sessionStorage.setItem('guideShown_{{ $periode->id }}', 'true');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const savedMode = localStorage.getItem('paieMode_{{ $periode->id }}') || 'quick';
            setMode(savedMode);
 
            if (savedMode === 'detail') showStep(0);
 
            updateQuickTotal();

            // Gestion de l'affichage automatique des popups
            console.log("Guide Logic - HAS_PREVIOUS:", HAS_PREVIOUS);
            console.log("Guide Logic - Already Shown:", sessionStorage.getItem('guideShown_{{ $periode->id }}'));

            if (!sessionStorage.getItem('guideShown_{{ $periode->id }}')) {
                if (HAS_PREVIOUS) {
                    console.log("Showing Precheck Modal");
                    if(PRECHECK_MODAL) PRECHECK_MODAL.show();
                } else {
                    console.log("Showing Guide Modal");
                    if(GUIDE_MODAL) GUIDE_MODAL.show();
                    sessionStorage.setItem('guideShown_{{ $periode->id }}', 'true');
                }
            }
        });
    </script>
@endpush
