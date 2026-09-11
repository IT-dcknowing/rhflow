@php
    // Récupérer les données depuis le paySlip
    $allowances = json_decode($paySlip->allowances ?? '[]', true);
    $retenues = json_decode($paySlip->retenues ?? '[]', true);

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
    $date_embauche = new DateTime($paySlip->employee->company_doj ?? date('Y-m-d'));
    $date_actuelle = new DateTime(date('Y-m-d'));
    $difference = $date_embauche->diff($date_actuelle);
    $date_pa = $difference->format('%y');
	$date_m  = $difference->format('%m');
@endphp
@extends('layouts.app')

@section('title', 'Gestion des Bulletins de Paie')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">ðŸ“‹ Gestion des Bulletins de Paie</h4>
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
    <div class= "row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <ul class="nav nav-pills flex-column flex-md-row mb-4">
                    <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull1show" id="affichebull1"><i class="ti ti-note ti-xs me-1"></i> Bulletin 1</a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull2show" id="affichebull2"><i class="ti ti-note ti-xs me-1"></i> Bulletin 2</a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull3show" id="affichebull3"><i class="ti ti-note ti-xs me-1"></i> Bulletin 3</a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull4show" id="affichebull4"><i class="ti ti-note ti-xs me-1"></i> Bulletin 4</a></li>
                </ul>
                <h5 class="mb-0">Période : {{ \Carbon\Carbon::parse($paySlip->salary_month)->translatedFormat('F Y') }}</h5>
            </div>
        </div>
        <hr>
    </div>
    <div class="tab-content mb-4">
        <div class="tab-pane fade show active" id="bull1show">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end align-items-center mb-4">
                        <div>
                            <a href="#" onclick="window.print()"
                                class="btn btn-success">
                                <i class="fas fa-download me-2"></i>Télécharger PDF
                            </a>
                        </div>
                    </div>
                    <div class="card-body" id="payslipContent">
                        <div class="table-responsive mb-4">
                            <table class="table table-sm mb-4" style="font-family: Arial; font-size: 18px;">
                                <!-- En-tête du bulletin -->
                                <tr class="table-success">
                                    <td colspan="9" class="text-center">
                                        <h4 style="font-size: 24px;"><strong>BULLETIN DE PAIE</strong></h4>
                                        <p class="mb-1">Période: {{ \Carbon\Carbon::parse($paySlip->salary_month)->translatedFormat('F Y') }}</p>
                                    </td>
                                </tr>
                                <!-- Informations employé -->
                                <tr class="table-primary">
                                    <td colspan="3">
                                        EMPLOYEUR
                                    </td>
                                    <td colspan="6">
                                        MATRICULE DU SALARIE:   {{ $paySlip->employee->employee_id ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        Nom :  {{ $paySlip->nom_etp ?? 'Entreprise' }}<br>
                                        Adresse :  {{ $paySlip->adresse_etp ?? 'N/A' }}<br>
                                        Téléphone : {{ $paySlip->phone_etp ?? 'N/A' }}<br>
                                        Boite postale :  {{ $paySlip->btp_etp ?? 'N/A' }}<br>
                                        Horaire mensuelle :  173,33<br>
                                        Nombre de jours travaillés : {{ $paySlip->nbre_jour ?? 30 }} <br>
                                        Grille salariale : <strong>{{ $paySlip->categories_emp ?? 'N/A' }}</strong><br>
                                    </td>
                                    <td colspan="6">
                                        Nom et Prénom :  {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                        Adresse :  {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                        Situation matrimoniale : {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                        Enfants à charge : {{ $paySlip->enfant_emp ?? 0 }}<br>
                                        Numéro CNPS :  {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                        Ancienneté :  {{$date_pa}} an(s) et {{$date_m}} mois<br>
                                        Catégorie : {{ $paySlip->categories_emp ?? 'N/A' }}<br>
                                        Emploi :   {{ $paySlip->emploi ?? 'N/A' }} <br>
                                        Tel / E-mail : {{ $paySlip->phone_emp ?? 'N/A' }} <br>
                                        Nombre de parts : {{ $paySlip->parts_emp ?? 1 }}<br>
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <th width="5%" rowspan="2" class="text-center align-middle">N°</th>
                                    <th width="25%" rowspan="2" class="text-center align-middle">DÉSIGNATION</th>
                                    <th width="5%" rowspan="2" class="text-center align-middle">NOMBRE</th>
                                    <th rowspan="2" class="text-center align-middle">BASE JOURNALIÈRE</th>
                                    <th colspan="3" class="text-center align-middle">PART SALARIALE</th>
                                    <th colspan="2" class="text-center align-middle">PART PATRONALE</th>
                                </tr>
                                <tr>
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
                                        <td class="text-end">{{ number_format(($paySlip->basic_salary/($paySlip->nbre_jour ?? 30)), 0, '.', ' ') }}</td>
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
                                            @if(($allowance['allowance_option'] ?? '') == '26')
                                                {{ $paySlip->enfant_emp ?? 0 }}
                                            @else
                                                {{ $paySlip->nbre_jour ?? 30 }}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ number_format(round(($allowance['montant'] ?? 0)/30), 0, '.', ' ')}}
                                        </td>
                                        <td class="text-end">
                                            {{ number_format(($allowance['taux'] ?? 0), 0, '.', ' ') }}
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
                                            <td class="text-end">{{ number_format($amount_avtg, 0 , '.' , ' ') }}</td>
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
                                            <strong>{{number_format(round($base_salary + $totalAllowances + $amount_avtg), 0 , '.' , ' ')}}</strong>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @forelse($retenues as $retenue)
                                        @php
                                            if(($retenue['code'] ?? '') == 403) {
                                                $totalretenuessal += $retenue['amount'] ?? 0;
                                            }
                                            if(($retenue['code'] ?? '') == 301 || ($retenue['code'] ?? '') == 302) {
                                                $totalretenuessal += $retenue['amount'] ?? 0;
                                                $totalretenuesemp += $retenue['patronale'] ?? 0;
                                            }
                                            if(($retenue['type'] ?? '') != 'add') {
                                                $totalretenuesemp += $retenue['amount'] ?? 0;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="text-end">{{ $retenue['code'] ?? '' }}</td>
                                            <td>{{ $retenue['libelle'] ?? '' }}</td>
                                            <td class="text-end">{{ $retenue['jours_work'] ?? '' }}</td>
                                            <td class="text-end">{{ number_format($retenue['base'] ?? 0, 0, ',', ' ') }}</td>
                                            <td class="text-end">{{ number_format($retenue['taux'] ?? 0, 0, ',', ' ') }}</td>
                                            <td class="text-end">{{ number_format($retenue['amount'] ?? 0, 0, ',', ' ') }}</td>
                                            <td class="text-end">{{ number_format($retenue['salariale'] ?? 0, 0, ',', ' ') }}</td>
                                            <td class="text-end">{{ number_format($retenue['patronale'] ?? 0, 0, ',', ' ') }}</td>
                                            <td class="text-end">{{ number_format($retenue['amount'] ?? 0, 0, ',', ' ') }}</td>
                                        </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Aucune déduction</td>
                                    </tr>
                                    @endforelse
                                    <tr class="table-danger">
                                        <td align="right"></td>
                                        <td class="text-center"><strong>Total Cotisations</strong></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td align="right"><strong>{{ number_format($totalretenuessal, 0, ',', ' ') }}</strong></td>
                                        <td></td>
                                        <td align="right"><strong>{{ number_format($totalretenuesemp, 0, ',', ' ') }}</strong></td>
                                    </tr>
                                    @foreach($retenues as $retenue)
                                        @if(($retenue['type'] ?? '') == 'add' && !in_array($retenue['code'] ?? '', [301, 302, 401, 402, 403]))
                                            <tr>
                                                <td class="text-end">{{ $retenue['code'] ?? '' }}</td>
                                                <td>{{ $retenue['libelle'] ?? '' }}</td>
                                                <td class="text-end">{{ $retenue['jours_work'] ?? '' }}</td>
                                                <td class="text-end"></td>
                                                <td class="text-end"></td>
                                                <td class="text-end"></td>
                                                <td class="text-end">{{ number_format($retenue['amount'] ?? 0, 0, ',', ' ') }}</td>
                                                <td class="text-end"></td>
                                                <td  class="text-end"></td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                            <table class="table table-sm" style="font-family: Arial; font-size: 18px;">
                                <!-- Récapitulatif final -->
                                <tr class="table-success text-center">
                                    <td>Cumuls</td>
                                    <td>Salaire brut</td>
                                    <td>Charges salariales</td>
                                    <td>Charges patronales</td>
                                    <td>Avantages en nature</td>
                                    <td>Net imposable</td>
                                    <td>Heures travaillées</td>
                                    <td>Heures<br/>supplémentaires</td>
                                    <td>NET A PAYER</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        Période<hr/>
                                        Année
                                    </td>
                                    <td class="text-center">
                                        {{number_format($paySlip->salary_brut ?? 0, 0 ,'.',' ')}}<hr/>
                                        {{number_format($paySlip->salary_brut ?? 0, 0 ,'.',' ')}}</td>
                                    <td class="text-center">
                                        {{number_format(round($totalretenuessal+$total_cantine), 0 ,'.',' ')}}<hr/>
                                        {{number_format($totalretenuessal, 0 ,'.',' ')}}</td>
                                    <td class="text-center">
                                        {{number_format(round($totalretenuesemp), 0 ,'.',' ')}}<hr/>
                                        {{number_format($totalretenuesemp, 0 ,'.',' ')}}</td>
                                    <td class="text-center">
                                        {{number_format($amount_avtg, 0 , '.' , ' ')}}<hr/>
                                        {{number_format(($compte*$amount_avtg), 0 , '.' , ' ')}}</td>
                                    <td class="text-center">
                                        {{number_format($paySlip->salary_imposable ?? 0, 0 ,'.',' ')}}<hr/>
                                        {{number_format($paySlip->salary_imposable ?? 0, 0 ,'.',' ')}}</td>
                                    <td class="text-center">
                                        173,33<hr/>
                                        {{round(173.33*$compte)}}</td>
                                    <td class="text-center">
                                        {{number_format($totoverstimes,0,'.',' ')}}<hr/>{{(number_format($totoverstimes,0,'.',' '))}}</td>
                                    <td class="text-center">
                                        <strong style="color:#000;">
                                            {{number_format($paySlip->net_payble ?? 0, 0 ,'.',' ')}}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- Signature -->
                        <div class="row mt-5">
                            <div class="col-md-8 align-items-center" style="color:#000;">
                                <b><i>Payé par : {{ $paySlip->type_paiement ?? '-' }} </i></b><br>
                                <i> Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.</i>
                            </div>
                            <div class="col-md-4 text-center">
                                <p>Le {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</p>
                                <p><strong> LA DIRECTION </strong></p>
                                <p class="border-top pt-2">Signature</p>
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
                            <button class="btn btn-success" onclick="$('.logoBull4').show();">{{ __('Avec logo') }}</button>
                            <button class="btn btn-warning" onclick="$('.logoBull4').hide();">{{ __('Sans logo') }}</button>
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

                            <table class="bull4-table" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; border: 1px solid #000;">
                            <!-- LIGNE 1 : BULLETIN DE PAIE -->
                            <tr>
                                <td colspan="9" style="background-color: {{ isset($company) ? ($company->getThemeHeaderBgColor() ?? '#8c6b5d') : '#8c6b5d' }}; color: {{ isset($company) ? (($company->getThemeHeaderBgColor() ?? '#8c6b5d') == '#ffffff' ? '#000000' : '#ffffff') : '#ffffff' }}; text-align: center; padding: 10px; border: 1px solid #000;">
                                    <h2 style="margin: 0; font-size: 18px;">BULLETIN DE PAIE</h2>
                                    <p style="margin: 5px 0 0 0;">Période : {{ \Carbon\Carbon::parse($paySlip->salary_month)->translatedFormat('F Y') }}</p>
                                </td>
                            </tr>

                            <!-- LIGNE 2 : EN-TETES EMPLOYEUR / SALARIE -->
                            <tr>
                                <td colspan="4" style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; border: 1px solid #000; padding: 5px; font-weight: bold;">
                                    EMPLOYEUR
                                </td>
                                <td colspan="5" style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; border: 1px solid #000; padding: 5px; font-weight: bold;">
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
                                    <strong>Nombre de jours travaillés :</strong> {{ $paySlip->nbre_jour ?? 30 }}<br>
                                    <strong>Grille salariale :</strong> {{ $paySlip->categories_emp ?? 'N/A' }}
                                </td>
                                <td colspan="5" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                                    <strong>Nom et Prénom :</strong> {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                    <strong>Adresse :</strong> {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                    <strong>Situation matrimoniale :</strong> {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                    <strong>Enfants à charge :</strong> {{ $paySlip->enfant_emp ?? 0 }}<br>
                                    <strong>Numéro CNPS :</strong> {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                    <strong>Ancienneté :</strong> {{$date_pa}} an(s) et {{$date_m}} mois<br>
                                    <strong>Catégorie :</strong> {{ $paySlip->categories_emp ?? 'N/A' }}<br>
                                    <strong>Emploi :</strong> {{ $paySlip->emploi ?? 'N/A' }}<br>
                                    <strong>Tel / E-mail :</strong> {{ $paySlip->phone_emp ?? 'N/A' }}
                                </td>
                            </tr>

                            <!-- LIGNE 4 & 5 : EN-TETES COLONNES GAINS / RETENUES -->
                            <tr style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; text-align: center; font-weight: bold;">
                                <td rowspan="2" style="width: 5%; border: 1px solid #000; padding: 5px;">N°</td>
                                <td rowspan="2" style="width: 30%; border: 1px solid #000; padding: 5px;">DÉSIGNATION</td>
                                <td rowspan="2" style="width: 5%; border: 1px solid #000; padding: 5px;">NOMBRE</td>
                                <td rowspan="2" style="width: 10%; border: 1px solid #000; padding: 5px;">BASE</td>
                                <td colspan="3" style="width: 30%; border: 1px solid #000; padding: 5px;">PART SALARIALE</td>
                                <td colspan="2" style="width: 20%; border: 1px solid #000; padding: 5px;">PART PATRONALE</td>
                            </tr>
                            <tr style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; text-align: center; font-weight: bold;">
                                <td style="border: 1px solid #000; padding: 5px;">TAUX</td>
                                <td style="border: 1px solid #000; padding: 5px;">GAIN</td>
                                <td style="border: 1px solid #000; padding: 5px;">RETENUES</td>
                                <td style="border: 1px solid #000; padding: 5px;">TAUX</td>
                                <td style="border: 1px solid #000; padding: 5px;">MONTANT</td>
                            </tr>

                            <!-- CORPS DU BULLETIN (NO HORIZONTAL BORDERS) -->
                            @if($base_salary > 0)
                                <tr>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">100</td>
                                    <td style="border: 1px solid #000; padding: 3px;">Salaire de base</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)($paySlip->basic_salary / ($paySlip->nbre_jour ?? 30)), 0, '.', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$base_salary, 0, '.', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                </tr>
                            @endif

                            @forelse($allowances as $allowance)
                                <tr>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $allowance['code'] ?? '' }}</td>
                                    <td style="border: 1px solid #000; padding: 3px;">{{ $allowance['title'] ?? '' }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">
                                        @if(($allowance['allowance_option'] ?? '') == '26') {{ $paySlip->enfant_emp ?? 0 }} @else {{ $paySlip->nbre_jour ?? 30 }} @endif
                                    </td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">
                                        {{ number_format((float)round(($allowance['montant'] ?? 0) / 30), 0, '.', ' ')}}
                                    </td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">
                                        {{ number_format((float)($allowance['taux'] ?? 0), 0, '.', ' ') }}
                                    </td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)round($allowance['amount'] ?? 0), 0, '.', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                </tr>
                            @empty
                            @endforelse

                            @if($amount_avtg > 0)
                                <tr>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">150</td>
                                    <td style="border: 1px solid #000; padding: 3px;">Avantages en nature et en argent</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$amount_avtg, 0, '.', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                    <td style="border: 1px solid #000; padding: 3px;"></td>
                                </tr>
                            @endif

                            <tr>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                                    <div style="border-top: 1px solid #000; display: inline-block; padding-top: 2px;">Total Brut</div>
                                </td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                    <div style="border-top: 1px solid #000; padding-top: 2px;">{{ number_format((float)round($base_salary + $totalAllowances + $amount_avtg), 0, '.', ' ') }}</div>
                                </td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                            </tr>

                            @forelse($retenues as $retenue)
                                <tr>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] ?? '' }}</td>
                                    <td style="border: 1px solid #000; padding: 3px;">{{ $retenue['libelle'] ?? '' }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['jours_work'] ?? '' }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['base'] ?? 0, 0, ',', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['taux'] ?? 0, 0, ',', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['amount'] ?? 0, 0, ',', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['salariale'] ?? 0, 0, ',', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['patronale'] ?? 0, 0, ',', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['amount'] ?? 0, 0, ',', ' ') }}</td>
                                </tr>
                            @empty
                            @endforelse

                            <tr>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                                    <div style="border-top: 1px solid #000; display: inline-block; padding-top: 2px;">Total Cotisations</div>
                                </td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                    <div style="border-top: 1px solid #000; padding-top: 2px;">{{ number_format((float)$totalretenuessal, 0, ',', ' ') }}</div>
                                </td>
                                <td style="border: 1px solid #000; padding: 3px;"></td>
                                <td style="border: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                    {{ number_format((float)$totalretenuesemp, 0, ',', ' ') }}
                                </td>
                            </tr>
                        </table>

                        <!-- SECTION CUMULS & NET A PAYER -->
                        <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; margin-top: 10px;">
                            <tr style="background-color: {{ isset($company) ? ($company->getThemeSecondaryColor() ?? '#e0e0e0') : '#e0e0e0' }}; color: {{ isset($company) ? (($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff') : '#000000' }}; text-align: center;">
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
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$paySlip->salary_brut ?? 0, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$totalretenuessal, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$totalretenuesemp, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$amount_avtg, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$paySlip->net_imposable ?? 0, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">173,33</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">0</td>
                                <td rowspan="2" style="border: 4px solid #000; padding: 10px; text-align: center; font-size: 14px; font-weight: bold; vertical-align: middle;">
                                    {{number_format((float)$paySlip->net_payble ?? 0, 0, '.', ' ')}}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center;">Année</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$paySlip->salary_brut ?? 0, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$totalretenuessal, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$totalretenuesemp, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)($compte*$amount_avtg), 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$paySlip->salary_imposable ?? 0, 0, '.', ' ')}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{round(173.33*$compte)}}</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">0</td>
                            </tr>
                        </table>

                        <!-- FOOTER NOTE & SIGNATURE -->
                        <div class="row mt-5">
                            <div class="col-md-8 align-items-center" style="color:#000;">
                                <b><i>Payé par : {{ $paySlip->type_paiement ?? '-' }} </i></b><br>
                                <i> Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.</i>
                            </div>
                            <div class="col-md-4 text-center">
                                <p>Fait à {{ $paySlip->adresse_etp ?? 'Lieu' }}, le {{ \Carbon\Carbon::parse($periode->date_fin)->day(25)->format('d/m/Y') }}</p>
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
    $(document).ready(function() {
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
        $('#affichebull1').on('click', function(e) {
            e.preventDefault();
            showBulletin('#bull1show', this);
        });

        $('#affichebull2').on('click', function(e) {
            e.preventDefault();
            showBulletin('#bull2show', this);
        });


        $('#affichebull4').on('click', function(e) {
            e.preventDefault();
            showBulletin('#bull4show', this);
        });

        $('#affichebull3').on('click', function(e) {

            e.preventDefault();
            showBulletin('#bull3show', this);
        });
    });

    // Imprimer le bulletin
    $('.btn-print').on('click', function() {
        window.print();
    });
</script>
@endpush


