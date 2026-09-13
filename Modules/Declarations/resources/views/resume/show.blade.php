@php
    // Récupérer et dédoublonner les données depuis le paySlip
    $allowances = collect(json_decode($paySlip->allowances ?? '[]', true))->unique('code')->values()->all();
    $retenues = collect(json_decode($paySlip->retenues ?? '[]', true))->unique('code')->values()->all();

    // Forcer Taxe d'Apprentissage (411) et Taxe FPC (412) à avoir le même montant que Contribution Employeur (410)
    $base_ce = null;
    $amount_ce = null;
    foreach ($retenues as $r) {
        if ((string) ($r['code'] ?? '') === '409') {
            $base_ce = $r['base'] ?? null;
            $amount_ce = $r['amount'] ?? 0;
            break;
        }
    }
    if ($amount_ce !== null || $base_ce !== null) {
        foreach ($retenues as &$r) {
            if (in_array((string) ($r['code'] ?? ''), ['411', '412'])) {
                $r['base'] = $base_ce;
                $r['amount'] = $amount_ce;
            }
        }
        unset($r);
    }

    $totalAllowances = 0;
    $totalretenuessal = 0;
    $totalretenuesemp = 0;
    $total_cantine = 0;
    $totoverstimes = 0;

    $totalAllowances2 = 0;
    $totalretenuessal2 = 0;
    $totalretenuesemp2 = 0;
    $total_cantine2 = 0;
    $totoverstimes2 = 0;

    $totalAllowances3 = 0;
    $totalretenuessal3 = 0;
    $totalretenuesemp3 = 0;
    $total_cantine3 = 0;
    $totoverstimes3 = 0;

    $base_salary = $paySlip->basic_salary ?? 0;
    $amount_avtg = $paySlip->avtg_real ?? 0;
    $compte = 1;

    // Calculer l'ancienneté
    $date_embauche = new DateTime($paySlip->employee->company_doj ?? $paySlip->employee->start_date);
    $date_ref_paie = new DateTime($periode->date_fin);
    $difference = $date_embauche->diff($date_ref_paie);
    $date_pa = intval($difference->format('%y')); // annees completes uniquement
    $date_m = intval($difference->format('%m'));
@endphp
@extends('layouts.app')

@section('title', 'Gestion des Bulletins de Paie')

@section('content')
    <style>
        /* ======================================================
                       FIX BORDURES — Bulletins 1, 2, 3, 4
                       Lignes continues sans quadrillage noir — couleur Bootstrap légère
                       ====================================================== */

        /* --- Bull1 & Bull2 : table légère, séparateurs horizontaux ---*/
        #payslipContentBull1 table,
        #payslipContentBull2 table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        #payslipContentBull1 table tr,
        #payslipContentBull2 table tr {
            border-bottom: 1px solid #dee2e6 !important;
        }

        #payslipContentBull1 table td,
        #payslipContentBull1 table th,
        #payslipContentBull2 table td,
        #payslipContentBull2 table th {
            border: 1px solid #dee2e6 !important;
            padding: 4px 6px !important;
        }

        /* --- Bull3 : garde ses propres bordures (table-bordered dark) ---*/
        #payslipContentBull3 table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        #payslipContentBull3 table td,
        #payslipContentBull3 table th {
            border-top: none !important;
            border-bottom: none !important;
            border-left: 1px solid #dee2e6 !important;
            border-right: 1px solid #dee2e6 !important;
            padding: 4px 6px !important;
        }

        /* --- Bull4 : idem Bull1 ---*/
        #payslipContentBull4 table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        #payslipContentBull4 table td,
        #payslipContentBull4 table th {
            border: 1px solid #dee2e6 !important;
            padding: 4px 6px !important;
        }

        /* --- Lignes d'en-tête colorées : supprimer double bordure ---*/
        #payslipContentBull1 .table-success td,
        #payslipContentBull1 .table-success th,
        #payslipContentBull2 .table-success td,
        #payslipContentBull2 .table-success th,
        #payslipContentBull3 .table-success td,
        #payslipContentBull3 .table-success th,
        #payslipContentBull4 .table-success td,
        #payslipContentBull4 .table-success th,
        #payslipContentBull1 .table-primary td,
        #payslipContentBull1 .table-primary th,
        #payslipContentBull2 .table-primary td,
        #payslipContentBull2 .table-primary th {
            border-color: #c3e6cb !important;
        }

        /* ======================================================
                       MODÈLE 2 — bulletin épuré, sans quadrillage
                       Les règles ci-dessous viennent après celles des bulletins
                       1/3/4 et neutralisent leurs bordures pour ce modèle.
                       ====================================================== */
        #payslipContentBull2 table,
        #payslipContentBull2 table tr,
        #payslipContentBull2 table td,
        #payslipContentBull2 table th {
            border: 0 !important;
            background: transparent !important;
        }

        #payslipContentBull2 {
            background: #fff;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.45;
        }

        #payslipContentBull2 table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-bottom: 18px;
        }

        #payslipContentBull2 table td {
            padding: 1px 6px !important;
            vertical-align: top;
        }

        #payslipContentBull2 .bp2-title {
            font-size: 40px;
            font-weight: 700;
            letter-spacing: -1px;
            margin: 10px 0 26px 0;
            color: #000;
        }

        #payslipContentBull2 .bp2-head td {
            width: 33.33%;
        }

        #payslipContentBull2 .bp2-col-lbl {
            font-size: 11px;
            padding-bottom: 2px !important;
        }

        #payslipContentBull2 .bp2-ident {
            padding-top: 6px !important;
        }

        #payslipContentBull2 .bp2-periode {
            font-size: 11px;
            margin: 0 0 14px 0;
            padding: 3px 6px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }

        #payslipContentBull2 .bp2-num {
            text-align: right;
            white-space: nowrap;
        }

        #payslipContentBull2 .bp2-hd {
            font-weight: 700;
        }

        #payslipContentBull2 .bp2-strong {
            font-weight: 700;
        }

        #payslipContentBull2 .bp2-w-lib {
            width: 38%;
        }

        #payslipContentBull2 .bp2-grp {
            font-weight: 700;
            text-align: left;
        }

        #payslipContentBull2 .bp2-grp-sep {
            padding-left: 22px !important;
        }

        /* Filets horizontaux : inline pour battre les !important ci-dessus */
        #payslipContentBull2 .bp2-sep-top td {
            border-top: 1px solid #000 !important;
        }

        #payslipContentBull2 .bp2-sep-bottom td {
            border-bottom: 1px solid #000 !important;
        }

        #payslipContentBull2 .bp2-lbl-tot {
            text-align: right;
            padding-right: 14px !important;
        }

        #payslipContentBull2 .bp2-tot {
            border-bottom: 1px solid #000 !important;
            width: 150px;
        }

        #payslipContentBull2 .bp2-pied {
            margin-top: 26px;
        }

        #payslipContentBull2 .bp2-signature {
            margin-top: 30px;
        }

        #payslipContentBull2 .bp2-mention {
            font-size: 10px;
            vertical-align: bottom;
        }

        #payslipContentBull2 .bp2-direction {
            text-align: right;
            font-size: 11px;
            margin-bottom: 4px;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- En-tête de la page -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Gestion des Bulletins de Paie</h4>
                        <p class="text-muted mb-0">Consultez et modifiez les bulletins de paie générés</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('company.declarations.resume.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <ul class="nav nav-pills flex-column flex-md-row mb-4">
                    <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab"
                            data-bs-target="#bull1show" id="affichebull1"><i class="ti ti-note ti-xs me-1"></i> Bulletin
                            1</a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"
                            data-bs-target="#bull2show" id="affichebull2"><i class="ti ti-note ti-xs me-1"></i> Bulletin
                            2</a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"
                            data-bs-target="#bull3show" id="affichebull3"><i class="ti ti-note ti-xs me-1"></i> Bulletin
                            3</a></li>
                    <!--<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab"-->
                    <!--        data-bs-target="#bull4show" id="affichebull4"><i class="ti ti-note ti-xs me-1"></i> Bulletin-->
                    <!--        4</a></li>-->
                </ul>
                <h5 class="mb-0">Période :
                    {{ \Carbon\Carbon::parse($paySlip->salary_month)->locale('fr')->isoFormat('MMMM YYYY') }}
                </h5>
            </div>
            <div class="row d-flex justify-content-center align-items-center">
                <div class="col-md-10">
                    <div class="tab-content mb-4">
                        <!-- Bulletin 1 -->
                        <div class="tab-pane fade show active" id="bull1show">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="">
                                            <button class="btn btn-success" id="showlogo"
                                                onclick="afficheLogSign()">{{ __('Avec logo') }}</button>
                                            <button class="btn btn-warning" id="hidelogo"
                                                onclick="cacheLogSign()">{{ __('Sans logo') }}</button>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-primary" id="downloadButtonLogo"
                                            onclick='downloadBulletin("payslipContentBull1", "downloadButtonLogo", "bulletin_{{ strtolower(\Carbon\Carbon::parse($periode->date_debut)->translatedFormat("F")) }}{{ \Carbon\Carbon::parse($periode->date_debut)->format("y") }}_{{ $paySlip->employee->name }}")'><span
                                                class="fa fa-download"></span></a>
                                    </div>
                                    <div class="card-body" id="payslipContentBull1">
                                        <div class="row mb-2" id="logoShow" style="display: block;">
                                            <div class="col-md-4" style="vertical-align: middle;" align="left">
                                                @if($company->logo)
                                                    <img src="{{ url($company->logo_url) }}" alt="Logo" width="80px;">
                                                @else
                                                    <div class="avatar-initial bg-label-secondary rounded">
                                                        <i class="fas fa-image fa-24px"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <table class="table table-sm" style="font-family: Arial; font-size: 12px;">
                                            <!-- En-tête du bulletin -->
                                            <tr class="table-success">
                                                <td colspan="9" class="text-center">
                                                    <h2><strong>BULLETIN DE PAIE</strong></h2>
                                                    <p class="mb-1">Période: {{ $periode->nom }} |
                                                        {{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }} -
                                                        {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}
                                                    </p>
                                                </td>
                                            </tr>
                                            <!-- Informations employé -->
                                            <tr class="table-primary">
                                                <td colspan="3">
                                                    EMPLOYEUR
                                                </td>
                                                <td colspan="6">
                                                    MATRICULE DU SALARIE:
                                                    {{ \Auth::user()->employeeIdFormat($paySlip->employee->employee_id ?? 'N/A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    Nom : {{ $paySlip->nom_etp ?? 'Entreprise' }}<br>
                                                    Adresse : {{ $paySlip->adresse_etp ?? 'N/A' }}<br>
                                                    Téléphone : {{ $paySlip->phone_etp ?? 'N/A' }}<br>
                                                    Boite postale : {{ $paySlip->btp_etp ?? 'N/A' }}<br>
                                                    Horaire mensuelle : 173,33<br>
                                                    Nombre de jours travaillés : {{ $paySlip->nbre_jour ?? 30 }} <br>
                                                    Grille salariale : <strong>{{ $company->sector->name }}</strong><br>
                                                </td>
                                                <td colspan="6">
                                                    Nom et Prénom : {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                                    Adresse : {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                                    Situation matrimoniale : {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                                    Enfants à charge : {{ $paySlip->enfant_emp ?? 0 }}<br>
                                                    Numéro CNPS : {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                                    Ancienneté : {{ $paySlip->anciennete_emp }}<br>
                                                    Catégorie : {{ $paySlip->categories_emp ?? 'N/A' }}<br>
                                                    Emploi : {{ $paySlip->emploi ?? 'N/A' }} <br>
                                                    Tel / E-mail : {{ $paySlip->phone_emp ?? 'N/A' }} <br>
                                                    Nombre de parts : {{ $paySlip->parts_emp ?? 1 }}<br>
                                                </td>
                                            </tr>
                                            <tr class="table-success">
                                                <th width="5%" class="text-center align-middle"
                                                    style="border-bottom: none;">N°</th>
                                                <th width="25%" class="text-center align-middle"
                                                    style="border-bottom: none;">DÉSIGNATION
                                                </th>
                                                <th width="5%" class="text-center align-middle"
                                                    style="border-bottom: none;">NOMBRE</th>
                                                <th class="text-center align-middle" style="border-bottom: none;">BASE
                                                    JOURNALIÈRE</th>
                                                <th colspan="3" class="text-center align-middle">PART SALARIALE</th>
                                                <th colspan="2" class="text-center align-middle">PART PATRONALE</th>
                                            </tr>
                                            <tr class="table-success">
                                                <th style="border-top: none;">&nbsp;</th>
                                                <th style="border-top: none;">&nbsp;</th>
                                                <th style="border-top: none;">&nbsp;</th>
                                                <th style="border-top: none;">&nbsp;</th>
                                                <th class="text-center">TAUX</th>
                                                <th class="text-center">GAIN</th>
                                                <th class="text-center">RETENUES</th>
                                                <th class="text-center">TAUX</th>
                                                <th class="text-center">MONTANT</th>
                                            </tr>
                                            <tbody class="text-muted">
                                                @if($base_salary > 0)
                                                    <tr>
                                                        <td class="text-end">100</td>
                                                        <td>Salaire de base</td>
                                                        <td class="text-end">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                        <td class="text-end">
                                                            {{ number_format(($paySlip->basic_salary / 30), 0, '.', ' ') }}
                                                        </td>
                                                        <td></td>
                                                        <td class="text-end">{{ number_format($base_salary, 0, '.', ' ') }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                                @forelse($allowances as $allowance)
                                                    @php
                                                        $totalAllowances += $allowance['amount'] ?? 0;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-end">{{ $allowance['code'] ?? '' }}</td>
                                                        <td>{{ $allowance['title'] ?? '' }}</td>
                                                        <td class="text-end">
                                                            @if(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                                {{ $paySlip->enfant_emp ?? 0 }}
                                                            @else
                                                                {{ $paySlip->nbre_jour ?? 30 }}
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            {{ number_format(round(($allowance['montant'] ?? 0) / 30), 0, '.', ' ')}}
                                                        </td>
                                                        <td class="text-end">
                                                            @if(($allowance['title'] ?? '') == 'Prime d\'ancienneté')
                                                                {{ number_format($date_pa, 0, '.', ' ') }}
                                                            @elseif(($allowance['title'] ?? '') == 'Prime de panier')
                                                                {{ number_format(3, 0, '.', ' ') }}
                                                            @elseif(($allowance['title'] ?? '') == 'Prime de salissure')
                                                                {{ number_format(13, 0, '.', ' ') }}
                                                            @elseif(($allowance['title'] ?? '') == 'Prime d\'outillage')
                                                                {{ number_format(10, 0, '.', ' ') }}
                                                            @elseif(($allowance['title'] ?? '') == 'Prime de tenue de travail')
                                                                {{ number_format(7, 0, '.', ' ') }}
                                                            @elseif(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                                {{ number_format(1500, 0, '.', ' ') }}
                                                            @else
                                                                <br />
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            {{ number_format(round($allowance['amount'] ?? 0), 0, '.', ' ') }}
                                                        </td>
                                                        <td>
                                                        </td>
                                                        <td>
                                                        </td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center">Aucune allocation</td>
                                                    </tr>
                                                @endforelse
                                                @if($amount_avtg > 0)
                                                    <tr>
                                                        <td align="right">
                                                            150
                                                        </td>
                                                        <td>Avantages en nature et en argent</td>
                                                        <td class="text-end">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-end">{{ number_format($amount_avtg, 0, '.', ' ') }}
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                                <tr class="table-success">
                                                    <td align="right"></td>
                                                    <td align="center"><strong>Total Brut</strong></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-end">
                                                        <strong>{{number_format(round($base_salary + $totalAllowances + $amount_avtg), 0, '.', ' ')}}</strong>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                @forelse($retenues as $retenue)
                                                    @if($retenue['code'] && $retenue['code'] != 307 && $retenue['code'] != 308)
                                                        @if($retenue['code'] < 404 && $retenue['code'] > 400)
                                                            @php
                                                                $retenue['code'] == 403 ? $totalretenuessal += $retenue['amount'] : $totalretenuessal += 0;
                                                            @endphp
                                                            <tr>
                                                                <td class="text-end">{{ $retenue['code'] }}</td>
                                                                <td>{{ $retenue['libelle'] }}</td>
                                                                <td class="text-end">{{ $retenue['jours_work'] }}</td>
                                                                <td class="text-end">
                                                                    {{ $retenue['code'] == 403 ? '' : number_format($retenue['amount'], 0, ',', ' ') }}
                                                                </td>
                                                                <td class="text-end"></td>
                                                                <td class="text-end"></td>
                                                                <td class="text-end">
                                                                    {{ $retenue['code'] == 403 ? number_format($retenue['amount'], 0, ',', ' ') : ''}}
                                                                </td>
                                                                <td class="text-end"></td>
                                                                <td class="text-end"></td>
                                                            </tr>
                                                        @elseif($retenue['code'] == 301 || $retenue['code'] == 302)
                                                            @php
                                                                $totalretenuessal += $retenue['amount'];
                                                                $totalretenuesemp += $retenue['patronale'];
                                                            @endphp
                                                            <tr>
                                                                <td class="text-end">{{ $retenue['code'] }}</td>
                                                                <td>{{ $retenue['libelle'] }}</td>
                                                                <td class="text-end">{{ $retenue['jours_work'] }}</td>
                                                                <td class="text-end">{{ number_format($retenue['base'], 0, ',', ' ') }}
                                                                </td>
                                                                <td class="text-end">{{ $retenue['taux'] }}</td>
                                                                <td class="text-end"></td>
                                                                <td class="text-end">{{ number_format($retenue['amount'], 0, ',', ' ')}}
                                                                </td>
                                                                <td class="text-end">{{ $retenue['salariale'] }}</td>
                                                                <td class="text-end">
                                                                    {{ number_format($retenue['patronale'], 0, ',', ' ') }}
                                                                </td>
                                                            </tr>
                                                        @else
                                                            @if($retenue['type'] != 'add')
                                                                @php
                                                                    $totalretenuesemp += $retenue['amount'];
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-end">{{ $retenue['code'] }}</td>
                                                                    <td>{{ $retenue['libelle'] }}</td>
                                                                    <td class="text-end">{{ $retenue['jours_work'] }}</td>
                                                                    <td class="text-end">{{ number_format($retenue['base'], 0, ',', ' ') }}
                                                                    </td>
                                                                    <td class="text-end"></td>
                                                                    <td class="text-end"></td>
                                                                    <td class="text-end"></td>
                                                                    <td class="text-end">{{ $retenue['taux'] }}</td>
                                                                    <td class="text-end">
                                                                        {{ number_format($retenue['amount'], 0, ',', ' ') }}
                                                                    </td>
                                                                </tr>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center">Aucune déduction</td>
                                                    </tr>
                                                @endforelse
                                                @foreach($retenues as $retenue)
                                                    @if(($retenue['type'] ?? '') == 'add' && !in_array($retenue['code'] ?? '', [301, 302, 307, 308, 401, 402, 403]) && ($retenue['libelle'] ?? '') !== 'TOTAL FDFP')
                                                        <tr>
                                                            <td class="text-end">
                                                                {{ ($retenue['libelle'] ?? '') == 'TOTAL FDFP' ? '413' : ($retenue['code'] ?? '') }}
                                                            </td>
                                                            <td>{{ $retenue['libelle'] ?? '' }}</td>
                                                            @php
                                                                $base_val = $retenue['base'] ?? ($retenue->base ?? '');
                                                                $taux_val = $retenue['taux'] ?? ($retenue->taux ?? '');
                                                                $fallback_base = $paySlip->brut ?? 0;
                                                                $fallback_taux = '';
                                                                $code_val = ($retenue['libelle'] ?? ($retenue->libelle ?? '')) == 'TOTAL FDFP' ? 413 : ($retenue['code'] ?? ($retenue->code ?? ''));
                                                                if ($code_val == 410) {
                                                                    $fallback_taux = '9,20';
                                                                } elseif ($code_val == 409) {
                                                                    $fallback_taux = '1,20';
                                                                } elseif ($code_val == 411) {
                                                                    $fallback_taux = '0,40';
                                                                    // Forcer la même base et montant que Contribution Employeur (409)
                                                                    if (isset($base_ce) && $base_ce !== null)
                                                                        $base_val = $base_ce;
                                                                    if (isset($amount_ce) && $amount_ce !== null)
                                                                        $retenue['amount'] = $amount_ce;
                                                                } elseif ($code_val == 412) {
                                                                    $fallback_taux = '1,20';
                                                                    // Forcer la même base et montant que Contribution Employeur (409)
                                                                    if (isset($base_ce) && $base_ce !== null)
                                                                        $base_val = $base_ce;
                                                                    if (isset($amount_ce) && $amount_ce !== null)
                                                                        $retenue['amount'] = $amount_ce;
                                                                } elseif ($code_val == 305) {
                                                                    $fallback_taux = '3,00';
                                                                } elseif ($code_val == 306) {
                                                                    $fallback_taux = '5,75';
                                                                } elseif ($code_val == 308) {
                                                                    $fallback_taux = '7,70';
                                                                } elseif ($code_val == 307) {
                                                                    $fallback_taux = '0,50';
                                                                    $fallback_base = 1000;
                                                                }
                                                            @endphp
                                                            <td class="text-end">{{ $retenue['jours_work'] ?? '' }}</td>
                                                            <td class="text-end">
                                                                {{ (isset($base_val) && $base_val !== '' && $base_val != 0) ? number_format($base_val, 0, ',', ' ') : ($fallback_base > 0 ? number_format($fallback_base, 0, ',', ' ') : '') }}
                                                            </td>
                                                            <td class="text-end">
                                                                {{ (isset($taux_val) && $taux_val !== '' && $taux_val != 0) ? $taux_val : $fallback_taux }}
                                                            </td>
                                                            <td class="text-end"></td>
                                                            <td class="text-end">
                                                                {{ number_format($retenue['amount'] ?? 0, 0, ',', ' ') }}
                                                            </td>
                                                            <td class="text-end"></td>
                                                            <td class="text-end"></td>
                                                        </tr>
                                                    @endif
                                                @endforeach

                                                <tr class="table-danger">
                                                    <td align="right"></td>
                                                    <td class="text-center"><strong>Total Cotisations</strong></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td align="right">
                                                        <strong>{{ number_format($totalretenuessal, 0, ',', ' ') }}</strong>
                                                    </td>
                                                    <td></td>
                                                    <td align="right">
                                                        <strong>{{ number_format($totalretenuesemp, 0, ',', ' ') }}</strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table table-sm" style="font-family: Arial; font-size: 12px;">
                                            <!-- Récapitulatif final -->
                                            <tr class="table-success text-center">
                                                <td>Cumuls</td>
                                                <td>Salaire brut</td>
                                                <td>Charges salariales</td>
                                                <td>Charges patronales</td>
                                                <td>Avantages en nature</td>
                                                <td>Net imposable</td>
                                                <td>Heures travaillées</td>
                                                <td>Heures<br />supplémentaires</td>
                                                <td>NET A PAYER</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center border-bottom border-dark">Période</td>
                                                <td class="text-center border-bottom border-dark">
                                                    {{number_format($paySlip->salary_brut ?? 0, 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center border-bottom border-dark">
                                                    {{number_format(round($totalretenuessal + $total_cantine), 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center border-bottom border-dark">
                                                    {{number_format(round($totalretenuesemp), 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center border-bottom border-dark">
                                                    {{number_format($amount_avtg, 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center border-bottom border-dark">
                                                    {{number_format($paySlip->salary_imposable ?? 0, 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center border-bottom border-dark">173,33</td>
                                                <td class="text-center border-bottom border-dark">
                                                    {{number_format($totoverstimes, 0, '.', ' ')}}
                                                </td>
                                                <td rowspan="2" class="text-center align-middle">
                                                    <strong style="color:#000; font-size: 14px;">
                                                        {{number_format($paySlip->net_payble ?? 0, 0, '.', ' ')}}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">Année</td>
                                                <td class="text-center">
                                                    {{number_format($paySlip->salary_brut ?? 0, 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center">{{number_format($totalretenuessal, 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center">{{number_format($totalretenuesemp, 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center">
                                                    {{number_format(($compte * $amount_avtg), 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center">
                                                    {{number_format($paySlip->salary_imposable ?? 0, 0, '.', ' ')}}
                                                </td>
                                                <td class="text-center">{{round(173.33 * $compte)}}</td>
                                                <td class="text-center">{{(number_format($totoverstimes, 0, '.', ' '))}}
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- Signatures -->
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <div class="project-amnt pt-1" align="" style="color:#000;">
                                                    <b><i>Payé par : {{ $paySlip->employee->paytypeEmp->name ?? '-' }}
                                                        </i></b>
                                                </div>
                                                <p style="font-size: 14px; font-style: italic; color: #666;">
                                                    <i>Pour vous aider à faire valoir vos droits, conservez ce bulletin de
                                                        paie sans limitation de durée.</i>
                                                </p>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <p style="font-size: 14px; margin-bottom: 20px;">Fait à
                                                    {{ $paySlip->adresse_etp ?? 'Lieu' }}, le
                                                    {{ \Carbon\Carbon::parse($periode->date_fin)->day(25)->format('d/m/Y') }}
                                                </p>
                                                <p
                                                    style="border-top: 1px solid #000; padding-top: 5px; width: 200px; margin-left: auto; text-align: center;">
                                                    <strong class="mb-2">LA DIRECTION</strong>
                                                </p>
                                                <div class="d-flex justify-content-end">
                                                    <div class="d-flex justify-content-center align-items-center"
                                                        id="signatureShow"
                                                        style="width: 200px; position: relative; display: inline-block;">
                                                        @if($company->electronic_stamp)
                                                            <img src="{{ url($company->electronic_stamp_url) }}" alt="Cachet"
                                                                width="80px" style="position: absolute;">
                                                        @else
                                                            <div class="avatar-initial bg-label-secondary rounded"
                                                                style="position: absolute;">
                                                                <i class="fas fa-stamp fa-24px"></i>
                                                            </div>
                                                        @endif

                                                        @if($company->electronic_signature)
                                                            <img src="{{ url($company->electronic_signature_url) }}"
                                                                alt="Signature" width="80px" style="position: absolute;">
                                                        @else
                                                            <div class="avatar-initial bg-label-secondary rounded"
                                                                style="position: absolute;">
                                                                <i class="fas fa-signature fa-24px"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bulletin 2 -->
                        <div class="tab-pane fade" id="bull2show">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="">
                                            <button class="btn btn-success" id="showlogo2"
                                                onclick="afficheLogSign2()">{{ __('Avec logo') }}</button>
                                            <button class="btn btn-warning" id="hidelogo2"
                                                onclick="cacheLogSign2()">{{ __('Sans logo') }}</button>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-primary" id="downloadButtonLogo2"
                                            onclick='downloadBulletin("payslipContentBull2", "downloadButtonLogo2", "bulletin_{{ strtolower(\Carbon\Carbon::parse($periode->date_debut)->translatedFormat("F")) }}{{ \Carbon\Carbon::parse($periode->date_debut)->format("y") }}_{{ $paySlip->employee->name }}_v2")'><span
                                                class="fa fa-download"></span></a>
                                    </div>

                                    @php
                                        // --- Préparation des données du modèle 2 -------------------
                                        $retCol2 = collect($retenues);

                                        // Retrouve une retenue par code (hors ligne d'agrégat TOTAL FDFP)
                                        $findRet2 = function ($code) use ($retCol2) {
                                            return $retCol2->first(function ($r) use ($code) {
                                                return (string) ($r['code'] ?? '') === (string) $code
                                                    && trim((string) ($r['libelle'] ?? '')) !== 'TOTAL FDFP';
                                            });
                                        };
                                        $amt2 = function ($r) {
                                            return $r === null ? null : (float) ($r['amount'] ?? 0);
                                        };
                                        // Les taux sont stockés sous la forme "6.3%" : on retire le %
                                        // et on passe au séparateur décimal français.
                                        $taux2 = function ($r) {
                                            $t = trim((string) ($r['taux'] ?? ''));
                                            $t = rtrim($t, '%');
                                            return ($t === '' || $t === '-') ? '' : str_replace('.', ',', $t);
                                        };
                                        $base2 = function ($r) {
                                            $b = $r['base'] ?? null;
                                            return ($b === null || $b === '') ? '' : number_format((float) $b, 0, ',', ' ');
                                        };
                                        $money2 = function ($v) {
                                            return number_format(round((float) $v), 0, ',', ' ');
                                        };

                                        // Impôts : 401 sert de base, 402 la réduction, 403 le net dû
                                        $ret401 = $findRet2(401);
                                        $ret402 = $findRet2(402);
                                        $ret403 = $findRet2(403);

                                        // Lignes mixtes : part salariale et part patronale sur la même ligne
                                        $cnpsSal2 = $findRet2(301);   // Cotisation Retraite CNPS (salarié)
                                        $cnpsPat2 = $findRet2(308);   // Cotisation retraite employeur
                                        $cmuSal2 = $findRet2(302);   // Couverture Maladie Universelle (salarié)
                                        $cmuPat2 = $findRet2(307);   // CMU Employeur

                                        // Lignes exclusivement patronales, dans l'ordre du modèle
                                        $patronales2 = [];
                                        foreach ([409, 410, 411, 412, 305, 306] as $codePat2) {
                                            $lignePat2 = $findRet2($codePat2);
                                            if ($lignePat2 !== null) {
                                                $patronales2[] = $lignePat2;
                                            }
                                        }

                                        // Cumuls : somme stricte des lignes affichées
                                        $cumulSal2 = ($amt2($ret403) ?? 0) + ($amt2($cnpsSal2) ?? 0) + ($amt2($cmuSal2) ?? 0);
                                        $cumulPat2 = ($amt2($cnpsPat2) ?? 0) + ($amt2($cmuPat2) ?? 0);
                                        foreach ($patronales2 as $lignePat2) {
                                            $cumulPat2 += (float) ($lignePat2['amount'] ?? 0);
                                        }

                                        // Gains
                                        $gainsTotal2 = (float) $base_salary + (float) $amount_avtg;
                                        foreach ($allowances as $allowance2) {
                                            $gainsTotal2 += (float) ($allowance2['amount'] ?? 0);
                                        }

                                        // Autres retenues
                                        $pret2 = (float) ($paySlip->loan ?? 0);
                                        $pretRetenu2 = (float) ($paySlip->loan_echeance ?? 0);
                                        $afficheAutres2 = ($pret2 > 0 || $amount_avtg > 0);
                                    @endphp

                                    <div class="card-body bp2" id="payslipContentBull2">

                                        <div class="row mb-2" id="logoShow2" style="display: block;">
                                            <div class="col-md-4" style="vertical-align: middle;" align="left">
                                                @if($company->logo)
                                                    <img src="{{ url($company->logo_url) }}" alt="Logo" width="80px;">
                                                @else
                                                    <div class="avatar-initial bg-label-secondary rounded">
                                                        <i class="fas fa-image fa-24px"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <h1 class="bp2-title">Bulletin de paie</h1>

                                        {{-- ---------- Identités : employeur / salarié / complément ---------- --}}
                                        <table class="bp2-head">
                                            <tr>
                                                <td class="bp2-col-lbl">Employeur</td>
                                                <td class="bp2-col-lbl">Salarié</td>
                                                <td class="bp2-col-lbl"></td>
                                            </tr>
                                            <tr class="bp2-sep-top">
                                                <td class="bp2-ident">
                                                    Nom : {{ $paySlip->nom_etp ?? '-' }}<br>
                                                    Adresse : {{ $paySlip->adresse_etp ?? '-' }}<br>
                                                    Boite postale : {{ $paySlip->btp_etp ?? '-' }}<br>
                                                    Téléphone : {{ $paySlip->phone_etp ?? '-' }}<br>
                                                    N° Contribuable : {{ $company->tax_id ?? '-' }}
                                                </td>
                                                <td class="bp2-ident">
                                                    Matricule :
                                                    {{ \Auth::user()->employeeIdFormat($paySlip->employee->employee_id ?? '-') }}<br>
                                                    Nom et Prénom : {{ $paySlip->employee->name ?? '-' }}<br>
                                                    Nombre de jours travaillés : {{ $paySlip->nbre_jour ?? 30 }}<br>
                                                    Adresse : {{ $paySlip->address_emp ?: '-' }}<br>
                                                    Ancienneté : {{ $paySlip->anciennete_emp ?? '-' }}<br>
                                                    Catégorie : {{ $paySlip->categories_emp ?? '-' }}<br>
                                                    Type de contrat : {{ $typeContrat ?? '-' }}<br>
                                                    Sec. Cat. : {{ $company->sector->name ?? '-' }}
                                                </td>
                                                <td class="bp2-ident">
                                                    Nb Part(s) : {{ $paySlip->parts_emp ?? '-' }}<br>
                                                    Situation matrimoniale : {{ $paySlip->situation_emp ?? '-' }}<br>
                                                    Nombre d'enfants : {{ $paySlip->enfant_emp ?? 0 }}<br>
                                                    Contacts : {{ trim($paySlip->phone_emp ?? '', ' /') ?: '-' }}<br>
                                                    Email : {{ $paySlip->employee->email ?: '-' }}<br>
                                                    Emploi : {{ $paySlip->emploi ?? '-' }}<br>
                                                    C.NPS : {{ $paySlip->num_cnps_emp ?? '-' }}
                                                </td>
                                            </tr>
                                        </table>

                                        <p class="bp2-periode">
                                            Période : {{ \Carbon\Carbon::parse($periode->date_debut)->format('d-m-Y') }}
                                            au {{ \Carbon\Carbon::parse($periode->date_fin)->format('d-m-Y') }}
                                        </p>

                                        {{-- ---------- Gains ---------- --}}
                                        <table class="bp2-table">
                                            <tr class="bp2-sep-bottom">
                                                <td class="bp2-w-lib"></td>
                                                <td class="bp2-num bp2-hd">Nombre</td>
                                                <td class="bp2-num bp2-hd">Base</td>
                                                <td class="bp2-num bp2-hd">Taux</td>
                                                <td class="bp2-num bp2-hd">A payer</td>
                                            </tr>

                                            @if($base_salary > 0)
                                                <tr>
                                                    <td>6610 Salaire de base</td>
                                                    <td class="bp2-num">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                    <td class="bp2-num">{{ $money2($base_salary) }}</td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num">{{ $money2($base_salary) }}</td>
                                                </tr>
                                            @endif

                                            @forelse($allowances as $allowance)
                                                <tr>
                                                    <td>{{ $allowance['code'] ?? '' }} {{ $allowance['title'] ?? '' }}</td>
                                                    <td class="bp2-num">
                                                        @if(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                            {{ $paySlip->enfant_emp ?? 0 }}
                                                        @else
                                                            {{ $paySlip->nbre_jour ?? 30 }}
                                                        @endif
                                                    </td>
                                                    <td class="bp2-num">{{ $money2(($allowance['montant'] ?? 0) / 30) }}</td>
                                                    <td class="bp2-num">
                                                        @if(($allowance['title'] ?? '') == 'Prime d\'ancienneté')
                                                            {{ $date_pa }}
                                                        @elseif(($allowance['title'] ?? '') == 'Prime de panier')
                                                            3
                                                        @elseif(($allowance['title'] ?? '') == 'Prime de salissure')
                                                            13
                                                        @elseif(($allowance['title'] ?? '') == 'Prime d\'outillage')
                                                            10
                                                        @elseif(($allowance['title'] ?? '') == 'Prime de tenue de travail')
                                                            7
                                                        @elseif(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                            {{ $money2(1500) }}
                                                        @endif
                                                    </td>
                                                    <td class="bp2-num">{{ $money2($allowance['amount'] ?? 0) }}</td>
                                                </tr>
                                            @empty
                                            @endforelse

                                            @if($amount_avtg > 0)
                                                <tr>
                                                    <td>150 Avantages en nature et en argent</td>
                                                    <td class="bp2-num">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num">{{ $money2($amount_avtg) }}</td>
                                                </tr>
                                            @endif

                                            <tr class="bp2-sep-top">
                                                <td class="bp2-strong">TOTAL BRUT</td>
                                                <td colspan="3"></td>
                                                <td class="bp2-num bp2-strong">{{ $money2($gainsTotal2) }}</td>
                                            </tr>
                                        </table>

                                        {{-- ---------- Retenues ---------- --}}
                                        <table class="bp2-table bp2-ret">
                                            <tr>
                                                <td class="bp2-w-lib bp2-strong">RETENUES</td>
                                                <td colspan="3" class="bp2-grp">SALARIALES</td>
                                                <td colspan="3" class="bp2-grp bp2-grp-sep">PATRONALES</td>
                                            </tr>
                                            <tr class="bp2-sep-bottom">
                                                <td></td>
                                                <td class="bp2-num bp2-hd">Base</td>
                                                <td class="bp2-num bp2-hd">Taux</td>
                                                <td class="bp2-num bp2-hd">Montant</td>
                                                <td class="bp2-num bp2-hd bp2-grp-sep">Base</td>
                                                <td class="bp2-num bp2-hd">Taux</td>
                                                <td class="bp2-num bp2-hd">Montant</td>
                                            </tr>

                                            {{-- Impôts : brut, réduction pour charges de famille, net --}}
                                            @if($ret401)
                                                <tr>
                                                    <td>{{ $ret401['code'] }} {{ $ret401['libelle'] }}</td>
                                                    <td class="bp2-num">{{ $money2($ret401['amount'] ?? 0) }}</td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num bp2-grp-sep"></td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num"></td>
                                                </tr>
                                            @endif
                                            @if($ret402)
                                                <tr>
                                                    <td>{{ $ret402['code'] }} {{ $ret402['libelle'] }}</td>
                                                    <td class="bp2-num">{{ $money2($ret402['amount'] ?? 0) }}</td>
                                                    <td class="bp2-num">{{ $taux2($ret402) }}</td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num bp2-grp-sep"></td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num"></td>
                                                </tr>
                                            @endif
                                            @if($ret403)
                                                <tr>
                                                    <td>{{ $ret403['code'] }} {{ $ret403['libelle'] }}</td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num">{{ $money2($ret403['amount'] ?? 0) }}</td>
                                                    <td class="bp2-num bp2-grp-sep"></td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num"></td>
                                                </tr>
                                            @endif

                                            {{-- CNPS : part salariale et part patronale sur une seule ligne --}}
                                            @if($cnpsSal2 || $cnpsPat2)
                                                <tr>
                                                    <td>{{ ($cnpsSal2['code'] ?? $cnpsPat2['code'] ?? '') }}
                                                        {{ $cnpsSal2['libelle'] ?? 'Cotisation Retraite CNPS' }}
                                                    </td>
                                                    <td class="bp2-num">{{ $cnpsSal2 ? $base2($cnpsSal2) : '' }}</td>
                                                    <td class="bp2-num">{{ $cnpsSal2 ? $taux2($cnpsSal2) : '' }}</td>
                                                    <td class="bp2-num">{{ $cnpsSal2 ? $money2($cnpsSal2['amount'] ?? 0) : '' }}
                                                    </td>
                                                    <td class="bp2-num bp2-grp-sep">{{ $cnpsPat2 ? $base2($cnpsPat2) : '' }}
                                                    </td>
                                                    <td class="bp2-num">{{ $cnpsPat2 ? $taux2($cnpsPat2) : '' }}</td>
                                                    <td class="bp2-num">{{ $cnpsPat2 ? $money2($cnpsPat2['amount'] ?? 0) : '' }}
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- CMU : idem --}}
                                            @if($cmuSal2 || $cmuPat2)
                                                <tr>
                                                    <td>{{ ($cmuSal2['code'] ?? $cmuPat2['code'] ?? '') }}
                                                        {{ $cmuSal2['libelle'] ?? 'Couverture Maladie Universelle' }}
                                                    </td>
                                                    <td class="bp2-num">{{ $cmuSal2 ? $base2($cmuSal2) : '' }}</td>
                                                    <td class="bp2-num">{{ $cmuSal2 ? $taux2($cmuSal2) : '' }}</td>
                                                    <td class="bp2-num">{{ $cmuSal2 ? $money2($cmuSal2['amount'] ?? 0) : '' }}
                                                    </td>
                                                    <td class="bp2-num bp2-grp-sep">{{ $cmuPat2 ? $base2($cmuPat2) : '' }}</td>
                                                    <td class="bp2-num">{{ $cmuPat2 ? $taux2($cmuPat2) : '' }}</td>
                                                    <td class="bp2-num">{{ $cmuPat2 ? $money2($cmuPat2['amount'] ?? 0) : '' }}
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- Lignes exclusivement patronales --}}
                                            @foreach($patronales2 as $lignePat)
                                                <tr>
                                                    <td>{{ $lignePat['code'] ?? '' }} {{ $lignePat['libelle'] ?? '' }}</td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num"></td>
                                                    <td class="bp2-num bp2-grp-sep">{{ $base2($lignePat) }}</td>
                                                    <td class="bp2-num">{{ $taux2($lignePat) }}</td>
                                                    <td class="bp2-num">{{ $money2($lignePat['amount'] ?? 0) }}</td>
                                                </tr>
                                            @endforeach

                                            <tr class="bp2-sep-top">
                                                <td class="bp2-strong">CUMUL RETENUES FISCALES ET SOCIALES</td>
                                                <td class="bp2-num"></td>
                                                <td class="bp2-num"></td>
                                                <td class="bp2-num bp2-strong">{{ $money2($cumulSal2) }}</td>
                                                <td class="bp2-num bp2-grp-sep"></td>
                                                <td class="bp2-num"></td>
                                                <td class="bp2-num bp2-strong">{{ $money2($cumulPat2) }}</td>
                                            </tr>
                                        </table>

                                        {{-- ---------- Autres retenues ---------- --}}
                                        @if($afficheAutres2)
                                            <table class="bp2-table bp2-autres">
                                                <tr class="bp2-sep-bottom">
                                                    <td class="bp2-w-lib bp2-strong">AUTRES RETENUES</td>
                                                    <td class="bp2-num bp2-hd">Montant</td>
                                                    <td class="bp2-num bp2-hd bp2-grp-sep">Retenues</td>
                                                </tr>
                                                @if($pret2 > 0)
                                                    <tr>
                                                        <td>Prêt</td>
                                                        <td class="bp2-num">{{ $money2($pret2) }}</td>
                                                        <td class="bp2-num bp2-grp-sep">{{ $money2($pretRetenu2) }}</td>
                                                    </tr>
                                                @endif
                                                @if($amount_avtg > 0)
                                                    <tr>
                                                        <td>Avantages en nature et en argent</td>
                                                        <td class="bp2-num">{{ $money2($amount_avtg) }}</td>
                                                        <td class="bp2-num bp2-grp-sep">{{ $money2(0) }}</td>
                                                    </tr>
                                                @endif
                                                <tr class="bp2-sep-top">
                                                    <td class="bp2-strong">CUMUL AUTRES RETENUES</td>
                                                    <td class="bp2-num bp2-strong">{{ $money2($pret2 + $amount_avtg) }}</td>
                                                    <td class="bp2-num bp2-strong bp2-grp-sep">{{ $money2($pretRetenu2) }}</td>
                                                </tr>
                                            </table>
                                        @endif

                                        {{-- ---------- Pied : règlement et totaux ---------- --}}
                                        <table class="bp2-table bp2-pied">
                                            <tr>
                                                <td class="bp2-w-lib">
                                                    Mode de règlement : <em
                                                        class="bp2-strong">{{ $paySlip->employee->paytypeEmp->name ?? '-' }}</em>
                                                </td>
                                                <td class="bp2-lbl-tot">BRUT TOTAL</td>
                                                <td class="bp2-num bp2-tot">{{ $money2($gainsTotal2) }}</td>
                                            </tr>
                                            <tr>
                                                <td>Date : {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}
                                                </td>
                                                <td class="bp2-lbl-tot">RETENUES</td>
                                                <td class="bp2-num bp2-tot">{{ $money2($cumulSal2) }}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td class="bp2-lbl-tot bp2-strong">NET A PAYER</td>
                                                <td class="bp2-num bp2-tot bp2-strong">
                                                    {{ $money2($paySlip->net_payble ?? 0) }}
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- ---------- Mentions et signature ---------- --}}
                                        <table class="bp2-signature">
                                            <tr>
                                                <td class="bp2-mention">
                                                    <em>Bulletin de paie à conserver sans limitation de durée.</em>
                                                </td>
                                                <td class="bp2-num" style="width: 240px;">
                                                    <div class="bp2-direction">LA DIRECTION</div>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="d-flex justify-content-center align-items-center"
                                                            id="signatureShow2"
                                                            style="width: 200px; height: 70px; position: relative; display: inline-block;">
                                                            @if($company->electronic_stamp)
                                                                <img src="{{ url($company->electronic_stamp_url) }}"
                                                                    alt="Cachet" width="80px" style="position: absolute;">
                                                            @else
                                                                <div class="avatar-initial bg-label-secondary rounded"
                                                                    style="position: absolute;">
                                                                    <i class="fas fa-stamp fa-24px"></i>
                                                                </div>
                                                            @endif

                                                            @if($company->electronic_signature)
                                                                <img src="{{ url($company->electronic_signature_url) }}"
                                                                    alt="Signature" width="80px" style="position: absolute;">
                                                            @else
                                                                <div class="avatar-initial bg-label-secondary rounded"
                                                                    style="position: absolute;">
                                                                    <i class="fas fa-signature fa-24px"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bulletin 3 -->
                        <div class="tab-pane fade" id="bull3show">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="">
                                            <button class="btn btn-success" id="showlogo3"
                                                onclick="afficheLogSign3()">{{ __('Avec logo') }}</button>
                                            <button class="btn btn-warning" id="hidelogo3"
                                                onclick="cacheLogSign3()">{{ __('Sans logo') }}</button>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-primary" id="downloadButtonLogo3"
                                            onclick='downloadBulletin("payslipContentBull3", "downloadButtonLogo3", "bulletin_{{ strtolower(\Carbon\Carbon::parse($periode->date_debut)->translatedFormat("F")) }}{{ \Carbon\Carbon::parse($periode->date_debut)->format("y") }}_{{ $paySlip->employee->name }}_v3")'><span
                                                class="fa fa-download"></span></a>
                                    </div>
                                    <div class="card-body" id="payslipContentBull3">
                                        <div class="row mb-2" id="logoShow3" style="display: block;">
                                            <div class="col-md-4" style="vertical-align: middle;" align="left">
                                                @if($company->logo)
                                                    <img src="{{ url($company->logo_url) }}" alt="Logo" width="80px;">
                                                @else
                                                    <div class="avatar-initial bg-label-secondary rounded">
                                                        <i class="fas fa-image fa-24px"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <table class="table-sm mb-3" cellspacing="0" cellpadding="0"
                                            style="font-family: Arial; font-size: 12px; border-collapse: collapse;">
                                            <tr>
                                                <td class="border border-dark" colspan="9" align="center"
                                                    style="vertical-align: middle; background-color: {{ isset($company) ? ($company->getThemeHeaderBgColor() ?? '#2b4291') : '#2b4291' }};">
                                                    <h2
                                                        style="color: {{ isset($company) ? (($company->getThemeHeaderBgColor() ?? '#2b4291') == '#ffffff' ? '#000000' : '#ffffff') : '#ffffff' }};">
                                                        <strong>BULLETIN DE PAIE</strong>
                                                    </h2>
                                                    <p
                                                        style="color: {{ isset($company) ? (($company->getThemeHeaderBgColor() ?? '#2b4291') == '#ffffff' ? '#000000' : '#ffffff') : '#ffffff' }};">
                                                        Période : {{ $periode->nom }} |
                                                        <strong>{{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }}
                                                            au
                                                            {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</strong>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%"
                                                    bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark" colspan="4">
                                                    EMPLOYEUR</td>
                                                <td width="50%"
                                                    bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark" colspan="5">
                                                    MATRICULE DU SALARIE:
                                                    {{ \Auth::user()->employeeIdFormat($paySlip->employee->employee_id ?? 'N/A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border border-dark" colspan="4">
                                                    Nom : {{ $paySlip->nom_etp ?? 'Entreprise' }}<br>
                                                    Adresse : {{ $paySlip->adresse_etp ?? 'N/A' }}<br>
                                                    Téléphone : {{ $paySlip->phone_etp ?? 'N/A' }}<br>
                                                    Boite postale : {{ $paySlip->btp_etp ?? 'N/A' }}<br>
                                                    Horaire mensuelle : 173,33<br>
                                                    Nombre de jours travaillés : {{ $paySlip->nbre_jour ?? 30 }} <br>
                                                    Grille salariale : <strong>{{ $company->sector->name }}</strong><br>
                                                </td>
                                                <td class="border border-dark" colspan="5">
                                                    Nom et Prénom : {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                                    Adresse : {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                                    Situation matrimoniale : {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                                    Enfants à charge : {{ $paySlip->enfant_emp ?? 0 }}<br>
                                                    Numéro CNPS : {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                                    Ancienneté : {{ $paySlip->anciennete_emp }}<br>
                                                    Catégorie : {{ $paySlip->categories_emp ?? 'N/A' }}<br>
                                                    Emploi : {{ $paySlip->emploi ?? 'N/A' }} <br>
                                                    Tel / E-mail : {{ $paySlip->phone_emp ?? 'N/A' }} <br>
                                                    Nombre de parts : {{ $paySlip->parts_emp ?? 1 }}<br>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="5%"
                                                    bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark"
                                                    style="vertical-align: middle; color:#000; border-bottom: none;"
                                                    width="3%">N°</td>
                                                <td width="35%"
                                                    bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark"
                                                    style="vertical-align: middle; color:#000; border-bottom: none;"
                                                    width="30%">DÉSIGNATION</td>
                                                <td width="5%"
                                                    bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark"
                                                    style="vertical-align: middle; color:#000; border-bottom: none;">NOMBRE
                                                </td>
                                                <td width="5%"
                                                    bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark"
                                                    style="vertical-align: middle; color:#000; border-bottom: none;">BASE
                                                </td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark" colspan="3"
                                                    style="vertical-align: middle; color:#000; text-align: center;">PART
                                                    SALARIALE</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark" colspan="2"
                                                    style="vertical-align: middle; color:#000; text-align: center;">PART
                                                    PATRONALE</td>
                                            </tr>
                                            <tr>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark" style="border-top: none;">&nbsp;</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark" style="border-top: none;">&nbsp;</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark" style="border-top: none;">&nbsp;</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark" style="border-top: none;">&nbsp;</td>
                                                <td class="border border-dark color:#000; text-align: center;">TAUX</td>
                                                <td class="border border-dark color:#000; text-align: center;">GAIN</td>
                                                <td class="border border-dark color:#000; text-align: center;">RETENUES</td>
                                                <td class="border border-dark color:#000; text-align: center;">TAUX</td>
                                                <td class="border border-dark color:#000; text-align: center;">MONTANT</td>
                                            </tr>
                                            @if($base_salary > 0)
                                                <tr>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">100</td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                        Salaire de base</td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">
                                                        {{ number_format(($paySlip->basic_salary / 30), 0, '.', ' ') }}
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">{{ number_format($base_salary, 0, '.', ' ') }}</td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                </tr>
                                            @endif
                                            @forelse($allowances as $allowance)
                                                @php
                                                    $totalAllowances3 += $allowance['amount'] ?? 0;
                                                @endphp
                                                <tr>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">{{ $allowance['code'] ?? '' }}</td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                        {{ $allowance['title'] ?? '' }}
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">
                                                        @if(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                            {{ $paySlip->enfant_emp ?? 0 }}
                                                        @else
                                                            {{ $paySlip->nbre_jour ?? 30 }}
                                                        @endif
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">
                                                        {{ number_format(round(($allowance['montant'] ?? 0) / 30), 0, '.', ' ') }}
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">
                                                        @if(($allowance['title'] ?? '') == 'Prime d\'ancienneté')
                                                            {{ number_format($date_pa, 0, '.', ' ') }}
                                                        @elseif(($allowance['title'] ?? '') == 'Prime de panier')
                                                            {{ number_format(3, 0, '.', ' ') }}
                                                        @elseif(($allowance['title'] ?? '') == 'Prime de salissure')
                                                            {{ number_format(13, 0, '.', ' ') }}
                                                        @elseif(($allowance['title'] ?? '') == 'Prime d\'outillage')
                                                            {{ number_format(10, 0, '.', ' ') }}
                                                        @elseif(($allowance['title'] ?? '') == 'Prime de tenue de travail')
                                                            {{ number_format(7, 0, '.', ' ') }}
                                                        @elseif(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                            {{ number_format(1500, 0, '.', ' ') }}
                                                        @else
                                                            <br />
                                                        @endif
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">
                                                        {{ number_format(round($allowance['amount'] ?? 0), 0, '.', ' ') }}
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Aucune allocation</td>
                                                </tr>
                                            @endforelse
                                            @if($amount_avtg > 0)
                                                <tr>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        align="right">
                                                        150
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                        Avantages en nature et en argent</td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                        class="text-end">{{ number_format($amount_avtg, 0, '.', ' ') }}</td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;"
                                                    align="right"></td>
                                                <td style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;"
                                                    align="center"><strong>Total Brut</strong>
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;"
                                                    class="text-end">
                                                    <strong>{{number_format(round($base_salary + $totalAllowances3 + $amount_avtg), 0, '.', ' ')}}
                                                    </strong>
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                            </tr>
                                            @forelse($retenues as $retenue)
                                                @if($retenue['code'] && $retenue['code'] != 307 && $retenue['code'] != 308)
                                                    @if($retenue['code'] < 404 && $retenue['code'] > 400)
                                                        @php
                                                            $retenue['code'] == 403 ? $totalretenuessal3 += $retenue['amount'] : $totalretenuessal3 += 0;
                                                        @endphp
                                                        <tr>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ $retenue['code'] }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                                {{ $retenue['libelle'] }}
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ $retenue['jours_work'] }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">
                                                                {{ $retenue['code'] == 403 ? '' : number_format($retenue['amount'], 0, ',', ' ') }}
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">&nbsp;</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">&nbsp;</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">
                                                                {{ $retenue['code'] == 403 ? number_format($retenue['amount'], 0, ',', ' ') : ''}}
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">&nbsp;</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">&nbsp;</td>
                                                        </tr>
                                                    @elseif($retenue['code'] == 301 || $retenue['code'] == 302)
                                                        @php
                                                            $totalretenuessal3 += $retenue['amount'];
                                                            $totalretenuesemp3 += $retenue['patronale'];
                                                        @endphp
                                                        <tr>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ $retenue['code'] }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                                {{ $retenue['libelle'] }}
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ $retenue['jours_work'] }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ number_format($retenue['base'], 0, ',', ' ') }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ $retenue['taux'] }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">&nbsp;</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ number_format($retenue['amount'], 0, ',', ' ')}}
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ $retenue['salariale'] }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                class="text-end">{{ number_format($retenue['patronale'], 0, ',', ' ') }}
                                                            </td>
                                                        </tr>
                                                    @else
                                                        @if($retenue['type'] != 'add')
                                                            @php
                                                                $totalretenuesemp3 += $retenue['amount'];
                                                            @endphp
                                                            <tr>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                    class="text-end">{{ $retenue['code'] }}</td>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                                    {{ $retenue['libelle'] }}
                                                                </td>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                    class="text-end">{{ $retenue['jours_work'] }}</td>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                    class="text-end">{{ number_format($retenue['base'], 0, ',', ' ') }}</td>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                    class="text-end">&nbsp;</td>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                    class="text-end">&nbsp;</td>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                    class="text-end">&nbsp;</td>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                    class="text-end">{{ $retenue['taux'] }}</td>
                                                                <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                                    class="text-end">{{ number_format($retenue['amount'], 0, ',', ' ') }}
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center">Aucune déduction</td>
                                                </tr>
                                            @endforelse
                                            @foreach($retenues as $retenue)
                                                @if(($retenue['type'] ?? '') == 'add' && !in_array($retenue['code'] ?? '', [301, 302, 307, 308, 401, 402, 403]) && ($retenue['libelle'] ?? '') !== 'TOTAL FDFP')
                                                    <tr>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                            class="text-end">
                                                            {{ ($retenue['libelle'] ?? '') == 'TOTAL FDFP' ? '413' : ($retenue['code'] ?? '') }}
                                                        </td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;">
                                                            {{ $retenue['libelle'] ?? '' }}
                                                        </td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                            class="text-end">{{ $retenue['jours_work'] ?? '' }}</td>
                                                        @php
                                                            $base_val = $retenue['base'] ?? ($retenue->base ?? '');
                                                            $taux_val = $retenue['taux'] ?? ($retenue->taux ?? '');
                                                            $fallback_base = $paySlip->brut ?? 0;
                                                            $fallback_taux = '';
                                                            $code_val = ($retenue['libelle'] ?? ($retenue->libelle ?? '')) == 'TOTAL FDFP' ? 413 : ($retenue['code'] ?? ($retenue->code ?? ''));
                                                            if ($code_val == 410) {
                                                                $fallback_taux = '9,20';
                                                            } elseif ($code_val == 409) {
                                                                $fallback_taux = '1,20';
                                                            } elseif ($code_val == 411) {
                                                                $fallback_taux = '0,40';
                                                            } elseif ($code_val == 412) {
                                                                $fallback_taux = '1,20';
                                                            } elseif ($code_val == 305) {
                                                                $fallback_taux = '3,00';
                                                            } elseif ($code_val == 306) {
                                                                $fallback_taux = '5,75';
                                                            } elseif ($code_val == 308) {
                                                                $fallback_taux = '7,70';
                                                            } elseif ($code_val == 307) {
                                                                $fallback_taux = '0,50';
                                                                $fallback_base = 1000;
                                                            }
                                                        @endphp
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                            class="text-end">
                                                            {{ (isset($base_val) && $base_val !== '' && $base_val != 0) ? number_format($base_val, 0, ',', ' ') : ($fallback_base > 0 ? number_format($fallback_base, 0, ',', ' ') : '') }}
                                                        </td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                            class="text-end">
                                                            {{ (isset($taux_val) && $taux_val !== '' && $taux_val != 0) ? $taux_val : $fallback_taux }}
                                                        </td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                            class="text-end">&nbsp;</td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                            class="text-end">
                                                            {{ number_format($retenue['amount'] ?? 0, 0, ',', ' ') }}
                                                        </td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                            class="text-end">&nbsp;</td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"
                                                            class="text-end">&nbsp;</td>
                                                    </tr>
                                                @endif
                                            @endforeach

                                            <tr>
                                                <td style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;"
                                                    align="right"></td>
                                                <td style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;"
                                                    class="text-center"><strong>Total Cotisations</strong>
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;"
                                                    align="right">
                                                    <strong>{{ number_format($totalretenuessal3, 0, ',', ' ') }}
                                                    </strong>
                                                </td>
                                                <td
                                                    style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;">
                                                </td>
                                                <td style="border-left: solid 1px black; border-right: solid 1px black; border-bottom: solid 1px black;"
                                                    align="right">
                                                    <strong>{{ number_format($totalretenuesemp3, 0, ',', ' ') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border border-dark" style="border-top-color: solid 1px #000;"
                                                    colspan="9">
                                                    <div class="project-amnt pt-1" align="" style="color:#000;">
                                                        <b><i>Payé par : {{ $paySlip->employee->paytypeEmp->name ?? '-' }}
                                                            </i></b>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">Cumuls</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">Salaire brut
                                                </td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">Charges
                                                    salariales</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">Charges
                                                    patronales</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">Avantages en
                                                    nature</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">Net imposable
                                                </td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">Heures
                                                    travaillées</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">
                                                    Heures<br />supplémentaires</td>
                                                <td bgcolor="{{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#C0C0C0') : '#C0C0C0' }}"
                                                    class="border border-dark color:#000;">NET A PAYER
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td class="border border-dark">Période</td>
                                                <td class="border border-dark montant">
                                                    {{number_format($paySlip->salary_brut ?? 0, 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">
                                                    {{number_format(round($totalretenuessal3 + $total_cantine3), 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">
                                                    {{number_format(round($totalretenuesemp3), 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">
                                                    {{number_format($amount_avtg, 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">
                                                    {{number_format($paySlip->salary_imposable ?? 0, 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">173,33</td>
                                                <td class="border border-dark montant">
                                                    {{number_format($totoverstimes, 0, '.', ' ')}}
                                                </td>
                                                <td rowspan="2"
                                                    style="vertical-align:middle; border-top-style:solid;border-top-width:3pt;border-left-style:solid;border-left-width:3pt;border-bottom-style:solid;border-bottom-width:3pt;border-right-style:solid;border-right-width:3pt; border-right-color: #000; border-left-color: #000; border-top-color: #000; border-bottom-color: #000">
                                                    <div class="project-amnt pt-1" align="center" style="color:#000;">
                                                        <strong>{{number_format($paySlip->net_payble ?? 0, 0, '.', ' ')}}</strong>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td class="border border-dark">Année</td>
                                                <td class="border border-dark montant">
                                                    {{number_format($paySlip->salary_brut ?? 0, 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">
                                                    {{number_format($totalretenuessal3, 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">
                                                    {{number_format($totalretenuesemp3, 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">
                                                    {{number_format(($compte * $amount_avtg), 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">
                                                    {{number_format($paySlip->salary_imposable ?? 0, 0, '.', ' ')}}
                                                </td>
                                                <td class="border border-dark montant">{{round(173.33 * $compte)}}</td>
                                                <td class="border border-dark montant">
                                                    {{(number_format($totoverstimes, 0, '.', ' '))}}
                                                </td>
                                            </tr>
                                        </table>
                                        <!-- Signatures -->
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <p style="font-size: 14px; font-style: italic; color: #666;">
                                                    <i>Pour vous aider à faire valoir vos droits, conservez ce bulletin de
                                                        paie sans limitation de durée.</i>
                                                </p>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <p style="font-size: 14px; margin-bottom: 20px;">Fait à
                                                    {{ $paySlip->adresse_etp ?? 'Lieu' }}, le
                                                    {{ \Carbon\Carbon::parse($periode->date_fin)->day(25)->format('d/m/Y') }}
                                                </p>
                                                <p
                                                    style="border-top: 1px solid #000; padding-top: 5px; width: 200px; margin-left: auto; text-align: center;">
                                                    <strong>LA DIRECTION</strong>
                                                </p>
                                                <div class="d-flex justify-content-end">
                                                    <div class="d-flex justify-content-center align-items-center"
                                                        id="signatureShow3"
                                                        style="width: 200px; position: relative; display: inline-block;">
                                                        @if($company->electronic_stamp)
                                                            <img src="{{ url($company->electronic_stamp_url) }}" alt="Cachet"
                                                                width="80px" style="position: absolute;">
                                                        @else
                                                            <div class="avatar-initial bg-label-secondary rounded"
                                                                style="position: absolute;">
                                                                <i class="fas fa-stamp fa-24px"></i>
                                                            </div>
                                                        @endif

                                                        @if($company->electronic_signature)
                                                            <img src="{{ url($company->electronic_signature_url) }}"
                                                                alt="Signature" width="80px" style="position: absolute;">
                                                        @else
                                                            <div class="avatar-initial bg-label-secondary rounded"
                                                                style="position: absolute;">
                                                                <i class="fas fa-signature fa-24px"></i>
                                                            </div>
                                                        @endif
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

            <!-- Bulletin 4 -->
            <div class="tab-pane fade" id="bull4show">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-end align-items-center mb-4">
                            <div>
                                <button class="btn btn-success"
                                    onclick="$('.logoBull4').show();">{{ __('Avec logo') }}</button>
                                <button class="btn btn-warning"
                                    onclick="$('.logoBull4').hide();">{{ __('Sans logo') }}</button>
                                <a href="#" onclick="window.print()" class="btn btn-success ms-2">
                                    <i class="fas fa-download me-2"></i>Télécharger PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body pagebulletin4" id="payslipContent4">
                            <div class="table-responsive mb-4">
                                <div class="logoBull4 me-3" style="display: block; margin-bottom:10px;">
                                    @if(isset($company) && $company->logo)
                                        <img src="{{ url($company->logo_url) }}" alt="Logo" width="80px;">
                                    @endif
                                </div>

                                <table class="bull4-table" cellspacing="0" cellpadding="0"
                                    style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 20px; border: 1px solid #000;">
                                    <!-- LIGNE 1 : BULLETIN DE PAIE -->
                                    <tr>
                                        <td colspan="9"
                                            style="background-color: {{ isset($company) ? ($company->getThemeHeaderBgColor() ?? '#8c6b5d') : '#8c6b5d' }}; color: {{ isset($company) ? (($company->getThemeHeaderBgColor() ?? '#8c6b5d') == '#ffffff' ? '#000000' : '#ffffff') : '#ffffff' }}; text-align: center; padding: 10px; border: 1px solid #000;">
                                            <h2 style="margin: 0; font-size: 24px;">BULLETIN DE PAIE</h2>
                                            <p style="margin: 5px 0 0 0;">Période :
                                                {{ \Carbon\Carbon::parse($paySlip->salary_month)->translatedFormat('F Y') }}
                                            </p>
                                        </td>
                                    </tr>

                                    <!-- LIGNE 2 : EN-TETES EMPLOYEUR / SALARIE -->
                                    <tr>
                                        <td colspan="4"
                                            style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; border: 1px solid #000; padding: 5px; font-weight: bold;">
                                            EMPLOYEUR
                                        </td>
                                        <td colspan="5"
                                            style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; border: 1px solid #000; padding: 5px; font-weight: bold;">
                                            MATRICULE DU SALARIE: {{ $paySlip->employee->employee_id ?? 'N/A' }}
                                        </td>
                                    </tr>

                                    <!-- LIGNE 3 : DETAILS EMPLOYEUR / SALARIE -->
                                    <tr>
                                        <td colspan="4" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                                            <strong>Nom :</strong> {{ $paySlip->nom_etp ?? 'Entreprise' }}<br>
                                            <strong>Adresse :</strong> {{ $paySlip->adresse_etp ?? 'N/A' }}<br>
                                            <strong>Téléphone :</strong> {{ $paySlip->phone_etp ?? 'N/A' }}<br>
                                            <strong>Boîte postale :</strong> {{ $paySlip->btp_etp ?? 'N/A' }}<br>
                                            <strong>Nombre de parts :</strong> {{ $paySlip->parts_emp ?? 1 }}<br>
                                            <strong>Horaire mensuelle :</strong> 173,33<br>
                                            <strong>Nombre de jours travaillés :</strong>
                                            {{ $paySlip->nbre_jour ?? 30 }}<br>
                                            <strong>Grille salariale :</strong> {{ $paySlip->categories_emp ?? 'N/A' }}
                                        </td>
                                        <td colspan="5" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                                            <strong>Nom et Prénom :</strong> {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                            <strong>Adresse :</strong> {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                            <strong>Situation matrimoniale :</strong>
                                            {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                            <strong>Enfants à charge :</strong> {{ $paySlip->enfant_emp ?? 0 }}<br>
                                            <strong>Numéro CNPS :</strong> {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                            <strong>Ancienneté :</strong> {{$date_pa}} an(s) et {{$date_m}} mois<br>
                                            <strong>Catégorie :</strong> {{ $paySlip->categories_emp ?? 'N/A' }}<br>
                                            <strong>Emploi :</strong> {{ $paySlip->emploi ?? 'N/A' }}<br>
                                            <strong>Tel / E-mail :</strong> {{ $paySlip->phone_emp ?? 'N/A' }}
                                        </td>
                                    </tr>

                                    <!-- LIGNE 4 & 5 : EN-TETES COLONNES GAINS / RETENUES -->
                                    <tr
                                        style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; text-align: center; font-weight: bold;">
                                        <td style="width: 5%; border: 1px solid #000; padding: 5px; border-bottom: none;">N°
                                        </td>
                                        <td style="width: 30%; border: 1px solid #000; padding: 5px; border-bottom: none;">
                                            DÉSIGNATION</td>
                                        <td style="width: 5%; border: 1px solid #000; padding: 5px; border-bottom: none;">
                                            NOMBRE</td>
                                        <td style="width: 10%; border: 1px solid #000; padding: 5px; border-bottom: none;">
                                            BASE</td>
                                        <td colspan="3" style="width: 30%; border: 1px solid #000; padding: 5px;">PART
                                            SALARIALE</td>
                                        <td colspan="2" style="width: 20%; border: 1px solid #000; padding: 5px;">PART
                                            PATRONALE</td>
                                    </tr>
                                    <tr
                                        style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; text-align: center; font-weight: bold;">
                                        <td style="border: 1px solid #000; padding: 5px; border-top: none;">&nbsp;</td>
                                        <td style="border: 1px solid #000; padding: 5px; border-top: none;">&nbsp;</td>
                                        <td style="border: 1px solid #000; padding: 5px; border-top: none;">&nbsp;</td>
                                        <td style="border: 1px solid #000; padding: 5px; border-top: none;">&nbsp;</td>
                                        <td style="border: 1px solid #000; padding: 5px;">TAUX</td>
                                        <td style="border: 1px solid #000; padding: 5px;">GAIN</td>
                                        <td style="border: 1px solid #000; padding: 5px;">RETENUES</td>
                                        <td style="border: 1px solid #000; padding: 5px;">TAUX</td>
                                        <td style="border: 1px solid #000; padding: 5px;">MONTANT</td>
                                    </tr>

                                    <!-- CORPS DU BULLETIN (NO HORIZONTAL BORDERS) -->
                                    @if($base_salary > 0)
                                        <tr>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                100</td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                                Salaire de base</td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ $paySlip->nbre_jour ?? 30 }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) ($paySlip->basic_salary / 30), 0, '.', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) $base_salary, 0, '.', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                        </tr>
                                    @endif

                                    @forelse($allowances as $allowance)
                                        <tr>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ $allowance['code'] ?? '' }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                                {{ $allowance['title'] ?? '' }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                @if(($allowance['allowance_option'] ?? '') == '26')
                                                {{ $paySlip->enfant_emp ?? 0 }} @else {{ $paySlip->nbre_jour ?? 30 }} @endif
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) round(($allowance['montant'] ?? 0) / 30), 0, '.', ' ')}}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) ($allowance['taux'] ?? 0), 0, '.', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) round($allowance['amount'] ?? 0), 0, '.', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse

                                    @if($amount_avtg > 0)
                                        <tr>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                150</td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                                Avantages en nature et en argent</td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ $paySlip->nbre_jour ?? 30 }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) $amount_avtg, 0, '.', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                            </td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                                            <div
                                                style="border-top: 1px solid #000; display: inline-block; padding-top: 2px;">
                                                Total Brut</div>
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                            <div style="border-top: 1px solid #000; padding-top: 2px;">
                                                {{ number_format((float) round($base_salary + $totalAllowances + $amount_avtg), 0, '.', ' ') }}
                                            </div>
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                        </td>
                                    </tr>

                                    @forelse($retenues as $retenue)
                                        <tr>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ $retenue['code'] ?? '' }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">
                                                {{ $retenue['libelle'] ?? '' }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ $retenue['jours_work'] ?? '' }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) $retenue['base'] ?? 0, 0, ',', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) $retenue['taux'] ?? 0, 0, ',', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) $retenue['amount'] ?? 0, 0, ',', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) $retenue['salariale'] ?? 0, 0, ',', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) $retenue['patronale'] ?? 0, 0, ',', ' ') }}
                                            </td>
                                            <td
                                                style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                {{ number_format((float) $retenue['amount'] ?? 0, 0, ',', ' ') }}
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse

                                    <tr>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                                            <div
                                                style="border-top: 1px solid #000; display: inline-block; padding-top: 2px;">
                                                Total Cotisations</div>
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                            <div style="border-top: 1px solid #000; padding-top: 2px;">
                                                {{ number_format((float) $totalretenuessal, 0, ',', ' ') }}
                                            </div>
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;">
                                        </td>
                                        <td
                                            style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                            {{ number_format((float) $totalretenuesemp, 0, ',', ' ') }}
                                        </td>
                                    </tr>
                                </table>

                                <!-- SECTION CUMULS & NET A PAYER -->
                                <table
                                    style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 20px; margin-top: 10px;">
                                    <tr
                                        style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; text-align: center;">
                                        <td style="border: 1px solid #000; padding: 5px;">Cumuls</td>
                                        <td style="border: 1px solid #000; padding: 5px;">Salaire brut</td>
                                        <td style="border: 1px solid #000; padding: 5px;">Charges<br>salariales</td>
                                        <td style="border: 1px solid #000; padding: 5px;">Charges<br>patronales</td>
                                        <td style="border: 1px solid #000; padding: 5px;">Avantages en<br>nature</td>
                                        <td style="border: 1px solid #000; padding: 5px;">Net Imposable</td>
                                        <td style="border: 1px solid #000; padding: 5px;">Heures<br>travaillées</td>
                                        <td style="border: 1px solid #000; padding: 5px;">Heures<br>supp</td>
                                        <td style="border: 1px solid #000; padding: 5px;">NET A PAYER</td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: center;">Période</td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $paySlip->salary_brut ?? 0, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $totalretenuessal, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $totalretenuesemp, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $amount_avtg, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $paySlip->net_imposable ?? 0, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            173,33</td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            0</td>
                                        <td rowspan="2"
                                            style="border: 4px solid #000; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; vertical-align: middle;">
                                            {{number_format((float) $paySlip->net_payble ?? 0, 0, '.', ' ')}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid #000; padding: 5px; text-align: center;">Année</td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $paySlip->salary_brut ?? 0, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $totalretenuessal, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $totalretenuesemp, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) ($compte * $amount_avtg), 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{number_format((float) $paySlip->salary_imposable ?? 0, 0, '.', ' ')}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            {{round(173.33 * $compte)}}
                                        </td>
                                        <td
                                            style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">
                                            0</td>
                                    </tr>
                                </table>

                                <!-- FOOTER NOTE & SIGNATURE -->
                                <div class="row mt-5">
                                    <div class="col-md-8 align-items-center" style="color:#000;">
                                        <b><i>Payé par : {{ $paySlip->type_paiement ?? '-' }} </i></b><br>
                                        <i> Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans
                                            limitation de durée.</i>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <p>Fait à {{ $paySlip->adresse_etp ?? 'Lieu' }}, le
                                            {{ \Carbon\Carbon::parse($periode->date_fin)->day(25)->format('d/m/Y') }}
                                        </p>
                                        <p><strong> LA DIRECTION </strong></p>
                                        <p class="border-top pt-2">Signature</p>
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

@push('scripts')
    <script>
        // Gestion des onglets de bulletins
        $(document).ready(function () {
            // Afficher le bulletin 1 par défaut
            $('#bull1show').addClass('show active');
            $('#affichebull1').addClass('active');

            // Fonction pour gérer l'affichage des bulletins
            function showBulletin(bulletinId, tabLink) {
                $('.tab-pane').removeClass('show active').hide(); // Masquer tous les bulletins
                $('.nav-link').removeClass('active'); // Retirer active des onglets

                // Afficher le bulletin spécifié
                $(bulletinId).addClass('show active').show();
                $(tabLink).addClass('active'); // Ajouter active à l'onglet cliqué
            }

            // Gestion des clics sur les onglets
            $('#affichebull1').on('click', function (e) {
                e.preventDefault();
                showBulletin('#bull1show', this);
            });

            $('#affichebull2').on('click', function (e) {
                e.preventDefault();
                showBulletin('#bull2show', this);
            });

            $('#affichebull3').on('click', function (e) {
                e.preventDefault();
                showBulletin('#bull3show', this);
            });
        });

        // Gérer l'affichage du logo
        function afficheLogSign() {
            var logo = document.getElementById("logoShow");
            var signature = document.getElementById("signatureShow");

            logo.style.display = 'block';
            signature.classList.remove('d-none');
            console.log('Logo et signature affichés');
        }

        function cacheLogSign() {
            var logo = document.getElementById("logoShow");
            var signature = document.getElementById("signatureShow");

            logo.style.display = 'none';
            signature.classList.add('d-none');
            console.log('Logo et signature cachés');
        }

        function afficheLogSign2() {
            var logo = document.getElementById("logoShow2");
            var signature = document.getElementById("signatureShow2");

            logo.style.display = 'block';
            signature.classList.remove('d-none');
            console.log('Logo et signature affichés');
        }

        function cacheLogSign2() {
            var logo = document.getElementById("logoShow2");
            var signature = document.getElementById("signatureShow2");

            logo.style.display = 'none';
            signature.classList.add('d-none');
            console.log('Logo et signature cachés');
        }

        function afficheLogSign3() {
            var logo = document.getElementById("logoShow3");
            var signature = document.getElementById("signatureShow3");

            logo.style.display = 'block';
            signature.classList.remove('d-none');
            console.log('Logo et signature affichés');
        }

        function cacheLogSign3() {
            var logo = document.getElementById("logoShow3");
            var signature = document.getElementById("signatureShow3");

            logo.style.display = 'none';
            signature.classList.add('d-none');
            console.log('Logo et signature cachés');
        }

        // Fonction générique pour télécharger un bulletin en PDF
        async function downloadBulletin(elementId, btnId, filename) {
            var element = document.getElementById(elementId);
            const button = document.getElementById(btnId);

            if (!element) {
                console.error(`Element with id ${elementId} not found`);
                return;
            }
            console.log(element);
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="fa fa-spinner fa-spin me-2"></span> Génération...';

            try {
                // Charger les bibliothèques si elles ne sont pas déjà disponibles
                if (typeof jspdf === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
                }

                // Créer un nouveau document PDF
                const { jsPDF } = window.jspdf;
                var doc = new jsPDF('p', 'mm', 'a4');

                // Utiliser doc.html() pour générer du texte vectoriel
                if (element && element.innerHTML.trim() !== '') {
                    await doc.html(element, {
                        startY: 15,
                        margin: [15, 3, 3, 3],
                        width: 100,
                        windowWidth: element.scrollWidth || element.clientWidth,
                        autoPaging: 'text',
                        html2canvas: {
                            scale: 0.19,
                            useCORS: true,
                            allowTaint: true,
                            logging: false
                        },
                        callback: function (doc) {
                            // Supprimer les pages vides à la fin
                            const pageCount = doc.internal.getNumberOfPages();
                            for (let i = pageCount; i > 1; i--) {
                                doc.deletePage(i);
                            }
                        }
                    });
                } else {
                    throw new Error('Contenu du bulletin non trouvé ou vide');
                }

                // Sauvegarder le PDF
                doc.save(`${filename}.pdf`);

            } catch (error) {
                console.error('Erreur lors de la génération du PDF :', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la génération du PDF'
                });
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }

        // Fonction utilitaire pour charger un script
        function loadScript(src) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }
    </script>
@endpush
script.onload = resolve;
script.onerror = reject;
document.head.appendChild(script);
});
}
</script>