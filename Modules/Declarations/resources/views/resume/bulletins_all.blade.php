<style>
    .border_cell{
        border-bottom-style:solid;
        border-bottom-width:2pt;
        border-bottom-color:#0070C0;
    }
    .bulletin-container {
        margin-bottom: 30px;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        font-size: 36px;
        font-family: "Verdana", "Segoe UI", Tahoma, Arial, sans-serif;
        font-weight: 400;
    }
    .bulletin-container table {
        font-family: "Verdana", "Segoe UI", Tahoma, Arial, sans-serif;
        font-size: 36px !important;
        font-weight: 400;
    }
    .bulletin-container td,
    .bulletin-container th {
        font-family: "Verdana", "Segoe UI", Tahoma, Arial, sans-serif;
        font-size: 36px !important;
        font-weight: 400;
        padding: 10px 12px !important;
    }
    .bulletin-container .nav-pills .nav-link {
        font-size: 1.2rem;
    }
</style>
@php
    \Carbon\Carbon::setLocale('fr');
    $cpte_bulletin = 0;
    $cpte_bulletin2 = 0;
    $cpte_bulletin3 = 0;
@endphp
@if(isset($bulletins) && count($bulletins) > 0)
    <div class="bulletin-container">
        <!-- En-tête unique avec logo et bouton télécharger -->
        <div class="card-header d-flex justify-content-between align-items-center" style="padding: 10px;">
            <!-- Navigation des bulletins -->
            <ul class="nav nav-pills flex-column flex-md-row">
                <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull1show" id="affichebull1"><i class="ti ti-note ti-xs me-1"></i> Bulletin 1</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull2show" id="affichebull2"><i class="ti ti-note ti-xs me-1"></i> Bulletin 2</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull3show" id="affichebull3"><i class="ti ti-note ti-xs me-1"></i> Bulletin 3</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull4show"><i class="ti ti-note ti-xs me-1"></i> Bulletin 4</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull5show"><i class="ti ti-note ti-xs me-1"></i> Modèle 5</a></li>
            </ul>
        </div>

        <div class="tab-content mb-4">
            <div class="text-end  mb-3">
                <input type="hidden" name="salary_month" id="salary_month" value="{{ \Carbon\Carbon::parse($periode->date_debut)->format('F Y') }}">
            </div>

            <!-- Bulletin 1 -->
            <div class="tab-pane fade show active" id="bull1show">
                <style>
                    #bull1show .b1-wrap table { font-size: 8.5px !important; font-weight: 400 !important; }
                    #bull1show .b1-wrap td { font-size: 8.5px !important; font-weight: 400 !important; padding: 3px 5px !important; }
                    #bull1show .b1-wrap th { font-size: 8px !important; font-weight: 700 !important; padding: 3px 5px !important; }
                    #bull1show .b1-wrap .b1-total-row td,
                    #bull1show .b1-wrap .b1-recap td { font-weight: 700 !important; }
                    #bull1show .b1-wrap .net-cell { font-size: 12px !important; font-weight: 900 !important; }
                </style>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Boutons logo -->
                    <div class="me-3">
                        <button class="btn btn-success" id="mainShowlogo" onclick="afficheLogSign()">{{ __('Avec logo') }}</button>
                        <button class="btn btn-warning" id="mainHidelogo" onclick="cacheLogSign()">{{ __('Sans logo') }}</button>
                    </div>
                    <!-- Bouton télécharger unique -->
                    <button class="btn btn-sm btn-primary" id="mainDownloadButton"
                        onclick="downloadBulletinsAuto(1, {{ $periode->id }}, 'bulletin_v1_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->format('F Y') }}')">
                        <span class="fa fa-download me-1"></span> Télécharger
                    </button>
                </div>

                @foreach($bulletins as $index => $paySlip)
                    @include('declarations::pdf.bulk_bulletins', ['bulletin' => $paySlip, 'company' => $company, 'periode' => $periode])
                    @continue
                                    @php
                                                    // Récupérer et dédoublonner les données depuis le paySlip
                                        $allowances = collect(json_decode($paySlip->allowances ?? '[]', true))->unique('code')->values()->all();
                                        $retenues = collect(json_decode($paySlip->retenues ?? '[]', true))->unique('code')->values()->all();

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
                                        $cpteAvtg = 0;
                                        $compte = 0;
                                        $toto_retenue = 0;
                                        $toto_patronales = 0;
                                        $toto_nature = 0;
                                        $toto_impos = 0;
                                        $toto_brut = 0;
                                        $toto_overtimes = 0;

                                        $monthpaie = \Carbon\Carbon::parse($periode->date_fin)->format('Y-m');

                                        list($selectedYear, $selectedMonth) = explode('-', $monthpaie);

                                        if ($paySlipss->count() < 1) {
                                            $compte = 1;
                                        } else {
                                            // Boucle sur chaque bulletin de paie
                                            foreach ($paySlipss as $pay) {
                                                if ($pay->employee_id == $paySlip->employee_id) {
                                                    // Extraire l'année et le mois à partir de salary_month
                                                    list($year, $month) = explode('-', $pay->salary_month);

                                                    // Vérifier si le bulletin de paie est dans l'année du mois sélectionné
                                                    if ($year == $selectedYear) {
                                                        // Vérifier si le mois du bulletin de paie est inférieur ou égal au mois sélectionné
                                                        if ($month <= $selectedMonth) {
                                                            // Cumuler les valeurs des éléments
                                                            $compte++;
                                                            $toto_brut += $pay->salary_brut;
                                                            $toto_retenue += $pay->total_retenue;
                                                            $toto_patronales += $pay->total_patronale;
                                                            $toto_nature += $pay->avantage_nature;
                                                            $toto_impos += $pay->net_imposable;
                                                            $toto_overtimes += $pay->overtimes;
                                                            foreach ($allowances as $allowance) {
                                                                if ($allowance['code'] == '103') {
                                                                    $totoverstimes += $allowance['amount'] ?? 0;
                                                                    $totoverstimes2 += $allowance['amount'] ?? 0;
                                                                    $totoverstimes3 += $allowance['amount'] ?? 0;
                                                                }
                                                            }
                                                            if ($pay->avtg_real > 0) {
                                                                $cpteAvtg++;
                                                            }
                                                        } else {
                                                            // Sortir de la boucle si le mois du bulletin de paie est supérieur au mois sélectionné
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $cpte_bulletin++;
                                        // Calculer l'ancienneté - taux = années ENTIÈREMENT complètes (ex: 4a 6m = 4%)
                                        $date_embauche = new DateTime($paySlip->employee->company_doj ?? $paySlip->employee->start_date);
                                        $date_ref_paie = new DateTime($periode->date_fin);
                                        $difference = $date_embauche->diff($date_ref_paie);
                                        $date_pa = intval($difference->format('%y')); // années complètes uniquement
                                        $date_m = intval($difference->format('%m'));

                                        // Unique IDs for this bulletin
                                        $bulletinId = $paySlip->id;
                                        $uniquePrefix = 'bull_' . $bulletinId . '_';
                                    @endphp
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card-body pagebulletin1" id="bull-{{ $cpte_bulletin }}">
                                                <!-- Logo -->
                                                <div class="logoBull1 me-3" id="mainLogoShow">
                                                    @if($company->logo)
                                                        <img src="{{ url($company->logo_url) }}" alt="Logo" width="80px;">
                                                    @else
                                                        <div class="avatar-initial bg-label-secondary rounded">
                                                            <i class="fas fa-image fa-24px"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <table class="table table-sm" style="font-family: Arial; font-size: 12px;">
                                                    <!-- En-tête du bulletin -->
                                                    <tr class="table-success">
                                                        <td colspan="9" class="text-center">
                                                            <h2><strong>BULLETIN DE PAIE</strong></h2>
                                                            <p class="mb-1">Période: {{ $periode->nom }} | {{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</p>
                                                        </td>
                                                    </tr>
                                                    <!-- Informations employé -->
                                                    <tr class="table-primary">
                                                        <td colspan="3">
                                                            EMPLOYEUR
                                                        </td>
                                                        <td colspan="6">
                                                            MATRICULE DU SALARIE:   {{ \Auth::user()->employeeIdFormat($paySlip->employee->employee_id ?? 'N/A') }}
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
                                                            Grille salariale : <strong>{{ $company->sector->name }}</strong><br>
                                                        </td>
                                                        <td colspan="6">
                                                            Nom et Prénom :  {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                                            Adresse :  {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                                            Situation matrimoniale : {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                                            Enfants à charge : {{ $paySlip->enfant_emp ?? 0 }}<br>
                                                            Numéro CNPS :  {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                                            Ancienneté :  {{ $paySlip->anciennete_emp }}<br>
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
                                                                <td class="text-end">{{ number_format(($paySlip->basic_salary / ($paySlip->nbre_jour ?? 30)), 0, '.', ' ') }}</td>
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
                                                                        <br/>
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
                                                                <td class="text-end">{{ number_format($amount_avtg, 0, '.', ' ') }}</td>
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
                                                                        <td class="text-end">{{ $retenue['code'] == 403 ? '' : number_format($retenue['amount'], 0, ',', ' ') }}</td>
                                                                        <td class="text-end"></td>
                                                                        <td class="text-end"></td>
                                                                        <td class="text-end">{{ $retenue['code'] == 403 ? number_format($retenue['amount'], 0, ',', ' ') : ''}}</td>
                                                                        <td class="text-end"></td>
                                                                        <td  class="text-end"></td>
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
                                                                        <td class="text-end">{{ number_format($retenue['base'], 0, ',', ' ') }}</td>
                                                                        <td class="text-end">{{ $retenue['taux'] }}</td>
                                                                        <td class="text-end"></td>
                                                                        <td class="text-end">{{ number_format($retenue['amount'], 0, ',', ' ')}}</td>
                                                                        <td class="text-end">{{ $retenue['salariale'] }}</td>
                                                                        <td class="text-end">{{ number_format($retenue['patronale'], 0, ',', ' ') }}</td>
                                                                    </tr>
                                                                @else
                                                                    @if($retenue['type'] != 'add')
                                                                        @php
                                                                            $totalretenuesemp += $retenue['amount'];
                                                                        @endphp
                                                                        <tr>
                                                                            <td  class="text-end">{{ $retenue['code'] }}</td>
                                                                            <td>{{ $retenue['libelle'] }}</td>
                                                                            <td class="text-end">{{ $retenue['jours_work'] }}</td>
                                                                            <td class="text-end">{{ number_format($retenue['base'], 0, ',', ' ') }}</td>
                                                                            <td class="text-end"></td>
                                                                            <td class="text-end"></td>
                                                                            <td class="text-end"></td>
                                                                            <td class="text-end">{{ $retenue['taux'] }}</td>
                                                                            <td  class="text-end">{{ number_format($retenue['amount'], 0, ',', ' ') }}</td>
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
                                    <td class="text-end">{{ ($retenue['libelle'] ?? '') == 'TOTAL FDFP' ? '413' : ($retenue['code'] ?? '') }}</td>
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
                            <td class="text-end">{{ $retenue['jours_work'] ?? '' }}</td>
                            <td class="text-end">{{ (isset($base_val) && $base_val !== '' && $base_val != 0) ? number_format($base_val, 0, ',', ' ') : ($fallback_base > 0 ? number_format($fallback_base, 0, ',', ' ') : '') }}</td>
                            <td class="text-end">{{ (isset($taux_val) && $taux_val !== '' && $taux_val != 0) ? $taux_val : $fallback_taux }}</td>
                            <td class="text-end"></td>
                                    <td class="text-end">{{ number_format($retenue['amount'] ?? 0, 0, ',', ' ') }}</td>
                                    <td class="text-end"></td>
                                    <td  class="text-end"></td>
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
                                                            <td align="right"><strong>{{ number_format($totalretenuessal, 0, ',', ' ') }}</strong></td>
                                                            <td></td>
                                                            <td align="right"><strong>{{ number_format($totalretenuesemp, 0, ',', ' ') }}</strong></td>
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
                                                        <td>Heures<br/>supps</td>
                                                        <td>NET A PAYER</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">
                                                            Période<hr/>
                                                            Année
                                                        </td>
                                                        <td class="text-center">
                                                            {{number_format($paySlip->salary_brut ?? 0, 0, '.', ' ')}}<hr/>
                                                            {{number_format($toto_brut, 0, '.', ' ')}}</td>
                                                        <td class="text-center">
                                                            {{number_format(round($totalretenuessal + $total_cantine), 0, '.', ' ')}}<hr/>
                                                            {{number_format($toto_retenue, 0, '.', ' ')}}</td>
                                                        <td class="text-center">
                                                            {{number_format(round($totalretenuesemp), 0, '.', ' ')}}<hr/>
                                                            {{number_format($toto_patronales, 0, '.', ' ')}}</td>
                                                        <td class="text-center">
                                                            {{number_format($amount_avtg, 0, '.', ' ')}}<hr/>
                                                            {{number_format(($cpteAvtg * $amount_avtg), 0, '.', ' ')}}</td>
                                                        <td class="text-center">
                                                            {{number_format($paySlip->net_imposable ?? 0, 0, '.', ' ')}}<hr/>
                                                            {{number_format($toto_impos, 0, '.', ' ')}}</td>
                                                        <td class="text-center">
                                                            173,33<hr/>
                                                            {{round(173.33 * $compte)}}</td>
                                                        <td class="text-center">
                                                            {{number_format($totoverstimes, 0, '.', ' ')}}<hr/>{{(number_format($totoverstimes, 0, '.', ' '))}}</td>
                                                        <td class="text-center">
                                                            <strong style="color:#000;">
                                                                {{number_format($paySlip->net_payble ?? 0, 0, '.', ' ')}}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- Signatures -->
                                                <div class="row mt-4">
                                                    <div class="col-md-6">
                                                        <div class="project-amnt pt-1" align="" style="color:#000;">
                                                            <b><i>Payé par : {{ $paySlip->employee->paytypeEmp->name ?? '-' }} </i></b>
                                                        </div>
                                                        <p style="font-size: 14px; font-style: italic; color: #666;">
                                                            <i>Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.</i>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <p style="font-size: 14px; margin-bottom: 20px;">Fait à {{ $paySlip->adresse_etp ?? 'Lieu' }}, le {{ \Carbon\Carbon::parse($periode->date_fin)->day(25)->format('d/m/Y') }}</p>
                                                        <p style="border-top: 1px solid #000; padding-top: 5px; width: 200px; margin-left: auto; text-align: center;">
                                                            <strong class="mb-2">LA DIRECTION</strong>
                                                        </p>
                                                        <div class="d-flex justify-content-end">
                                                            <div class="d-flex justify-content-center align-items-center signatureBull1" id="signatureShow" style="width: 200px; position: relative; display: inline-block;">
                                                                @if($company->electronic_stamp)
                                                                    <img src="{{ url($company->electronic_stamp_url) }}" alt="Cachet" width="80px" style="position: absolute;">
                                                                @else
                                                                    <div class="avatar-initial bg-label-secondary rounded" style="position: absolute;">
                                                                        <i class="fas fa-stamp fa-24px"></i>
                                                                    </div>
                                                                @endif

                                                                @if($company->electronic_signature)
                                                                    <img src="{{ url($company->electronic_signature_url) }}" alt="Signature" width="80px" style="position: absolute;">
                                                                @else
                                                                    <div class="avatar-initial bg-label-secondary rounded" style="position: absolute;">
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
                @endforeach
            </div>

            <!-- Bulletin 4 -->
            <div class="tab-pane fade" id="bull4show">
                <style>
                    #bull4show .b1-wrap table { font-size: 8.5px !important; font-weight: 400 !important; }
                    #bull4show .b1-wrap td { font-size: 8.5px !important; font-weight: 400 !important; padding: 3px 5px !important; }
                    #bull4show .b1-wrap th { font-size: 8px !important; font-weight: 700 !important; padding: 3px 5px !important; }
                    #bull4show .b1-wrap .b1-total-row td,
                    #bull4show .b1-wrap .b1-recap td { font-weight: 700 !important; }
                    #bull4show .b1-wrap .net-cell { font-size: 12px !important; font-weight: 900 !important; }
                </style>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="me-3">
                        <button class="btn btn-success" id="mainShowlogo" onclick="afficheLogSign4()">{{ __('Avec logo') }}</button>
                        <button class="btn btn-warning" id="mainHidelogo" onclick="cacheLogSign4()">{{ __('Sans logo') }}</button>
                    </div>
                    <button class="btn btn-sm btn-primary" id="mainDownloadButton4"
                        onclick="downloadBulletinsAuto(4, {{ $periode->id }}, 'bulletin_v4_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->format('F Y') }}')">
                        <span class="fa fa-download me-1"></span> Télécharger
                    </button>
                </div>

                @foreach($bulletins as $index => $paySlip)
                    @include('declarations::pdf.bulk_bulletins', ['bulletin' => $paySlip, 'company' => $company, 'periode' => $periode])
                    @continue
                                    @php
                                        $allowances = collect(json_decode($paySlip->allowances ?? '[]', true))->unique('code')->values()->all();
                                        $retenues = collect(json_decode($paySlip->retenues ?? '[]', true))->unique('code')->values()->all();

                                        $totalAllowances4 = 0;
                                        $totalretenuessal4 = 0;
                                        $totalretenuesemp4 = 0;
                                        $totoverstimes4 = 0;

                                        $base_salary = $paySlip->basic_salary ?? 0;
                                        $amount_avtg = $paySlip->avtg_real ?? 0;
                                        $cpteAvtg = 0;
                                        $compte = 0;
                                        $toto_retenue = 0;
                                        $toto_patronales = 0;
                                        $toto_impos = 0;
                                        $toto_brut = 0;

                                        $monthpaie = \Carbon\Carbon::parse($periode->date_fin)->format('Y-m');
                                        list($selectedYear, $selectedMonth) = explode('-', $monthpaie);

                                        if (isset($paySlipss) && $paySlipss->count() < 1) {
                                            $compte = 1;
                                        } elseif(isset($paySlipss)) {
                                            foreach ($paySlipss as $pay) {
                                                if ($pay->employee_id == $paySlip->employee_id) {
                                                    list($year, $month) = explode('-', $pay->salary_month);
                                                    if ($year == $selectedYear) {
                                                        if ($month <= $selectedMonth) {
                                                            $compte++;
                                                            $toto_brut += $pay->salary_brut;
                                                            $toto_retenue += $pay->total_retenue;
                                                            $toto_patronales += $pay->total_patronale;
                                                            $toto_impos += $pay->net_imposable;
                                                            foreach ($allowances as $allowance) {
                                                                if ($allowance['code'] == '103') {
                                                                    $totoverstimes4 += $allowance['amount'] ?? 0;
                                                                }
                                                            }
                                                            if ($pay->avtg_real > 0) {
                                                                $cpteAvtg++;
                                                            }
                                                        } else {
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        // Ancienneté : années complètes basées sur la fin de période
                                        $date_embauche = new DateTime($paySlip->employee->company_doj ?? $paySlip->employee->start_date);
                                        $date_ref_paie = new DateTime($periode->date_fin);
                                        $difference = $date_embauche->diff($date_ref_paie);
                                        $date_pa = intval($difference->format('%y')); // années complètes uniquement

                                        if(!isset($cpte_bulletin4)) $cpte_bulletin4 = 0;
                                        $cpte_bulletin4++;
                                    @endphp
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card-body pagebulletin4" id="bull4-{{ $cpte_bulletin4 }}">
                                                <div class="logoBull4 me-3" id="mainLogoShow" style="display: block; margin-bottom:10px;">
                                                    @if($company->logo)
                                                        <img src="{{ url($company->logo_url) }}" alt="Logo" width="80px;">
                                                    @endif
                                                </div>

                                            <div class="table-responsive mb-4">
                                                <table class="bull4-table table table-sm mb-4" style="border-collapse: collapse; font-family: Arial, sans-serif; font-size: 14px; border: 1px solid #000;">
                                                    <!-- LIGNE 1 : BULLETIN DE PAIE -->
                                                    <tr>
                                                        <td colspan="9" style="background-color: {{ $company->getThemeHeaderBgColor() ?? '#8c6b5d' }}; color: {{ ($company->getThemeHeaderBgColor() ?? '#8c6b5d') == '#ffffff' ? '#000000' : '#ffffff' }}; text-align: center; padding: 10px; border: 1px solid #000;">
                                                            <h2 style="margin: 0; font-size: 18px;">BULLETIN DE PAIE</h2>
                                                            <p style="margin: 5px 0 0 0;">Période : {{ \Carbon\Carbon::parse($periode->date_debut)->format('d-m-Y') }} au {{ \Carbon\Carbon::parse($periode->date_fin)->format('d-m-Y') }}</p>
                                                        </td>
                                                    </tr>

                                                    <!-- LIGNE 2 : EN-TETES EMPLOYEUR / SALARIE -->
                                                    <tr>
                                                        <td colspan="4" style="background-color: {{ $company->getThemeSecondaryColor() ?? '#e0e0e0' }}; color: {{ ($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff' }}; border: 1px solid #000; padding: 5px; font-weight: bold;">
                                                            EMPLOYEUR
                                                        </td>
                                                        <td colspan="5" style="background-color: {{ $company->getThemeSecondaryColor() ?? '#e0e0e0' }}; color: {{ ($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff' }}; border: 1px solid #000; padding: 5px; font-weight: bold;">
                                                            MATRICULE DU SALARIE: {{ \Auth::user()->employeeIdFormat($paySlip->employee->employee_id ?? 'N/A') }}
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
                                                            <strong>Grille salariale :</strong> {{ $company->sector->name ?? 'N/A' }}
                                                        </td>
                                                        <td colspan="5" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                                                            <strong>Nom et Prénom :</strong> {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                                            <strong>Adresse :</strong> {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                                            <strong>Situation matrimoniale :</strong> {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                                            <strong>Enfants à charge :</strong> {{ $paySlip->enfant_emp ?? 0 }}<br>
                                                            <strong>Numéro CNPS :</strong> {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                                            <strong>Ancienneté :</strong> {{ $paySlip->anciennete_emp }}<br>
                                                            <strong>Catégorie :</strong> {{ $paySlip->categories_emp ?? 'N/A' }}<br>
                                                            <strong>Emploi :</strong> {{ $paySlip->emploi ?? 'N/A' }}<br>
                                                            <strong>Tel / E-mail :</strong> {{ $paySlip->phone_emp ?? 'N/A' }}
                                                        </td>
                                                    </tr>

                                                    <!-- LIGNE 4 & 5 : EN-TETES COLONNES GAINS / RETENUES -->
                                                    <tr style="background-color: {{ $company->getThemeSecondaryColor() ?? '#e0e0e0' }}; color: {{ ($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff' }}; text-align: center; font-weight: bold;">
                                                        <td rowspan="2" style="width: 5%; border: 1px solid #000; padding: 5px;">N°</td>
                                                        <td rowspan="2" style="width: 30%; border: 1px solid #000; padding: 5px;">DÉSIGNATION</td>
                                                        <td rowspan="2" style="width: 5%; border: 1px solid #000; padding: 5px;">NOMBRE</td>
                                                        <td rowspan="2" style="width: 10%; border: 1px solid #000; padding: 5px;">BASE</td>
                                                        <td colspan="3" style="width: 30%; border: 1px solid #000; padding: 5px;">PART SALARIALE</td>
                                                        <td colspan="2" style="width: 20%; border: 1px solid #000; padding: 5px;">PART PATRONALE</td>
                                                    </tr>
                                                    <tr style="background-color: {{ $company->getThemeSecondaryColor() ?? '#e0e0e0' }}; color: {{ ($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff' }}; text-align: center; font-weight: bold;">
                                                        <td style="border: 1px solid #000; padding: 5px;">TAUX</td>
                                                        <td style="border: 1px solid #000; padding: 5px;">GAIN</td>
                                                        <td style="border: 1px solid #000; padding: 5px;">RETENUES</td>
                                                        <td style="border: 1px solid #000; padding: 5px;">TAUX</td>
                                                        <td style="border: 1px solid #000; padding: 5px;">MONTANT</td>
                                                    </tr>

                                                    <!-- CORPS DU BULLETIN (NO HORIZONTAL BORDERS) -->
                                                    @if($base_salary > 0)
                                                        <tr>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">100</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">Salaire de base</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)($paySlip->basic_salary / ($paySlip->nbre_jour ?? 30)), 0, '.', ' ') }}</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$base_salary, 0, '.', ' ') }}</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        </tr>
                                                    @endif

                                                    @forelse($allowances as $allowance)
                                                        @php $totalAllowances4 += $allowance['amount'] ?? 0; @endphp
                                                        <tr>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $allowance['code'] ?? '' }}</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">{{ $allowance['title'] ?? '' }}</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                                @if(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)') {{ $paySlip->enfant_emp ?? 0 }} @else {{ $paySlip->nbre_jour ?? 30 }} @endif
                                                            </td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                                {{ number_format((float)round(($allowance['montant'] ?? 0) / 30), 0, '.', ' ')}}
                                                            </td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">
                                                                @if(($allowance['title'] ?? '') == 'Prime d\'ancienneté') {{ number_format((float)$date_pa, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de panier') 3
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de salissure') 13
                                                                @elseif(($allowance['title'] ?? '') == 'Prime d\'outillage') 10
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de tenue de travail') 7
                                                                @elseif(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)') 1 500
                                                                @endif
                                                            </td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)round($allowance['amount'] ?? 0), 0, '.', ' ') }}</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        </tr>
                                                    @empty
                                                    @endforelse

                                                    @if($amount_avtg > 0)
                                                        <tr>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">150</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">Avantages en nature et en argent</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$amount_avtg, 0, '.', ' ') }}</td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                            <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        </tr>
                                                    @endif

                                                    <tr>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                                                            <div style="border-top: 1px solid #000; display: inline-block; padding-top: 2px;">Total Brut</div>
                                                        </td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                                            <div style="border-top: 1px solid #000; padding-top: 2px;">{{ number_format((float)round($base_salary + $totalAllowances4 + $amount_avtg), 0, '.', ' ') }}</div>
                                                        </td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                    </tr>

                                                    @forelse($retenues as $retenue)
                                                        @if($retenue['code'] && $retenue['code'] != 307 && $retenue['code'] != 308)
                                                            @if($retenue['code'] < 404 && $retenue['code'] > 400)
                                                                @php $retenue['code'] == 403 ? $totalretenuessal4 += $retenue['amount'] : $totalretenuessal4 += 0; @endphp
                                                                <tr>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">{{ $retenue['libelle'] }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] == 402 ? $retenue['jours_work'] : '' }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] == 403 ? '' : number_format((float)$retenue['amount'], 0, ',', ' ') }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] == 403 ? number_format((float)$retenue['amount'], 0, ',', ' ') : ''}}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                </tr>
                                                            @elseif($retenue['code'] == 301 || $retenue['code'] == 302)
                                                                @php $totalretenuessal4 += $retenue['amount']; $totalretenuesemp4 += $retenue['patronale']; @endphp
                                                                <tr>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">{{ $retenue['libelle'] }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['base'], 0, ',', ' ') }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['taux'] }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['amount'], 0, ',', ' ')}}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['salariale'] }}</td>
                                                                    <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['patronale'], 0, ',', ' ') }}</td>
                                                                </tr>
                                                            @else
                                                                @if($retenue['type'] != 'add')
                                                                    @php $totalretenuesemp4 += $retenue['amount']; @endphp
                                                                    <tr>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] }}</td>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;">{{ $retenue['libelle'] }}</td>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['base'], 0, ',', ' ') }}</td>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px;"></td>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['taux'] }}</td>
                                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['amount'], 0, ',', ' ') }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @empty
                                                    @endforelse

                                                    <tr>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px; text-align: center; font-weight: bold;">
                                                            <div style="border-top: 1px solid #000; display: inline-block; padding-top: 2px;">Total Cotisations</div>
                                                        </td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                                            <div style="border-top: 1px solid #000; padding-top: 2px;">{{ number_format((float)$totalretenuessal4, 0, ',', ' ') }}</div>
                                                        </td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px;"></td>
                                                        <td style="border-left: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">
                                                            {{ number_format((float)$totalretenuesemp4, 0, ',', ' ') }}
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- SECTION CUMULS & NET A PAYER -->
                                                <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10px; margin-top: 10px;">
                                                    <tr style="background-color: {{ $company->getThemeSecondaryColor() ?? '#e0e0e0' }}; color: {{ ($company->getThemeSecondaryColor() ?? '#e0e0e0') == '#ffffff' ? '#000000' : '#ffffff' }}; text-align: center;">
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
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$totalretenuessal4, 0, '.', ' ')}}</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$totalretenuesemp4, 0, '.', ' ')}}</td>
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
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$toto_brut, 0, '.', ' ')}}</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$toto_retenue, 0, '.', ' ')}}</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$toto_patronales, 0, '.', ' ')}}</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)($cpteAvtg * $amount_avtg), 0, '.', ' ')}}</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{number_format((float)$toto_impos, 0, '.', ' ')}}</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{round(173.33 * $compte)}}</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">0</td>
                                                    </tr>
                                                </table>

                                                <!-- FOOTER NOTE & SIGNATURE -->
                                                <div style="margin-top: 5px; font-size: 9px; font-style: italic; font-weight: bold;">
                                                    Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.
                                                </div>
                                                <div style="text-align: right; margin-top: 15px; font-size: 10px; font-weight: bold; text-decoration: underline;">
                                                    LA DIRECTION
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                @endforeach
            </div>

            <!-- Bulletin 2 -->
            <div class="tab-pane fade" id="bull2show">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Boutons logo -->
                    <div class="me-3">
                        <button class="btn btn-success" id="mainShowlogo" onclick="afficheLogSign2()">{{ __('Avec logo') }}</button>
                        <button class="btn btn-warning" id="mainHidelogo" onclick="cacheLogSign2()">{{ __('Sans logo') }}</button>
                    </div>
                    <!-- Bouton télécharger unique -->
                    <button class="btn btn-sm btn-primary" id="mainDownloadButton2"
                        onclick="downloadBulletinsAuto(2, {{ $periode->id }}, 'bulletin_v2_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->format('F Y') }}')">
                        <span class="fa fa-download me-1"></span> Télécharger
                    </button>
                </div>

                @foreach($bulletins as $index => $paySlip)
                                    @php
                                                    // Récupérer et dédoublonner les données depuis le paySlip
                                        $allowances = collect(json_decode($paySlip->allowances ?? '[]', true))->unique('code')->values()->all();
                                        $retenues = collect(json_decode($paySlip->retenues ?? '[]', true))->unique('code')->values()->all();

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
                                        $cpteAvtg = 0;
                                        $compte = 0;
                                        $toto_retenue = 0;
                                        $toto_patronales = 0;
                                        $toto_nature = 0;
                                        $toto_impos = 0;
                                        $toto_brut = 0;
                                        $toto_overtimes = 0;

                                        $monthpaie = \Carbon\Carbon::parse($periode->date_fin)->format('Y-m');

                                        list($selectedYear, $selectedMonth) = explode('-', $monthpaie);

                                        if ($paySlipss->count() < 1) {
                                            $compte = 1;
                                        } else {
                                            // Boucle sur chaque bulletin de paie
                                            foreach ($paySlipss as $pay) {
                                                if ($pay->employee_id == $paySlip->employee_id) {
                                                    // Extraire l'année et le mois à partir de salary_month
                                                    list($year, $month) = explode('-', $pay->salary_month);

                                                    // Vérifier si le bulletin de paie est dans l'année du mois sélectionné
                                                    if ($year == $selectedYear) {
                                                        // Vérifier si le mois du bulletin de paie est inférieur ou égal au mois sélectionné
                                                        if ($month <= $selectedMonth) {
                                                            // Cumuler les valeurs des éléments
                                                            $compte++;
                                                            $toto_brut += $pay->salary_brut;
                                                            $toto_retenue += $pay->total_retenue;
                                                            $toto_patronales += $pay->total_patronale;
                                                            $toto_nature += $pay->avantage_nature;
                                                            $toto_impos += $pay->net_imposable;
                                                            $toto_overtimes += $pay->overtimes;
                                                            foreach ($allowances as $allowance) {
                                                                if ($allowance['code'] == '103') {
                                                                    $totoverstimes += $allowance['amount'] ?? 0;
                                                                    $totoverstimes2 += $allowance['amount'] ?? 0;
                                                                    $totoverstimes3 += $allowance['amount'] ?? 0;
                                                                }
                                                            }
                                                            if ($pay->avtg_real > 0) {
                                                                $cpteAvtg++;
                                                            }
                                                        } else {
                                                            // Sortir de la boucle si le mois du bulletin de paie est supérieur au mois sélectionné
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $cpte_bulletin2++;

                                        // Calculer l'ancienneté - taux = années ENTIÈREMENT complètes (ex: 4a 6m = 4%)
                                        $date_embauche = new DateTime($paySlip->employee->company_doj ?? $paySlip->employee->start_date);
                                        $date_ref_paie = new DateTime($periode->date_fin);
                                        $difference = $date_embauche->diff($date_ref_paie);
                                        $date_pa = intval($difference->format('%y')); // années complètes uniquement
                                        $date_m = intval($difference->format('%m'));

                                        // Unique IDs for this bulletin
                                        $bulletinId = $paySlip->id;
                                        $uniquePrefix = 'bull_' . $bulletinId . '_';
                                    @endphp
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card-body pagebulletin2" id="bull2-{{ $cpte_bulletin2 }}">
                                                <!-- Logo -->
                                                <div class="logoBull2 me-3" id="mainLogoShow" style="display: block;">
                                                    @if($company->logo)
                                                        <img src="{{ url($company->logo_url) }}" alt="Logo" width="80px;">
                                                    @else
                                                        <div class="avatar-initial bg-label-secondary rounded">
                                                            <i class="fas fa-image fa-24px"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <table class="table table-bordered" style="font-family: Arial; font-size: 14px;">
                                                    <!-- En-tête -->
                                                    <tr>
                                                        <td colspan="8" style="border: 2px solid #000; padding: 10px; text-align: center; background-color: #f0f0f0;">
                                                            <h3 style="margin: 0; color: #000;"><strong>Bulletin de paie</strong></h3>
                                                            <p style="margin: 5px 0 0 0; font-weight: bold;">Période : {{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</p>
                                                        </td>
                                                    </tr>

                                                    <!-- Informations Employeur -->
                                                    <tr>
                                                        <td colspan="4" style="border: 1px solid #000; padding: 5px; background-color: #e8e8e8;">
                                                            <strong>Employeur</strong>
                                                        </td>
                                                        <td colspan="4" style="border: 1px solid #000; padding: 5px; background-color: #e8e8e8;">
                                                            <strong>Adresse</strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                                                            <strong>Nom :</strong> {{ $paySlip->nom_etp ?? 'Entreprise' }}<br>
                                                            <strong>N°RCCM :</strong> {{ $company->registration_number ?? 'N/A' }}<br>
                                                            <strong>N°NCC :</strong> {{ $company->tax_id ?? 'N/A' }}<br>
                                                            <strong>Grille salariale :</strong> {{ $company->sector->name ?? 'N/A' }}<br>
                                                            <strong> Horaire mensuelle :</strong> 173,33
                                                        </td>
                                                        <td colspan="4" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                                                            {{ $paySlip->adresse_etp ?? 'N/A' }}<br>
                                                            {{ $paySlip->phone_etp ?? 'N/A' }}<br>
                                                            {{ $paySlip->btp_etp ?? 'N/A' }}
                                                        </td>
                                                    </tr>

                                                    <!-- Informations Salarié -->
                                                    <tr>
                                                        <td colspan="8" style="border: 1px solid #000; padding: 5px; background-color: #e8e8e8;">
                                                            <strong>Salarié</strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                                                            <strong>Nom & Prénom :</strong> {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                                            <strong>Matricule :</strong> {{ \Auth::user()->employeeIdFormat($paySlip->employee->employee_id ?? 'N/A') }}<br>
                                                            <strong>Date d'entrée :</strong> {{ \Carbon\Carbon::parse($paySlip->employee->company_doj ?? $paySlip->employee->start_date)->format('d/m/Y') }}<br>
                                                            <strong>Ancienneté :</strong> {{ $paySlip->anciennete_emp }}<br>
                                                            <strong>Emploi :</strong> {{ $paySlip->emploi ?? 'N/A' }}<br>
                                                            <strong>Catégorie :</strong> {{ $paySlip->categories_emp ?? 'N/A' }}
                                                        </td>
                                                        <td colspan="4" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                                                            <strong>Numéro CNPS :</strong> {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                                            <strong>Adresse :</strong> {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                                            <strong>Situation familiale :</strong> {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                                            <strong>Enfants à charge :</strong> {{ $paySlip->enfant_emp ?? 0 }}<br>
                                                            <strong>Parts fiscales :</strong> {{ $paySlip->parts_emp ?? 1 }}
                                                        </td>
                                                    </tr>

                                                    <!-- En-tête tableau gains -->
                                                    <tr>
                                                        <td colspan="2" style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 40%;">
                                                            <strong>Rubriques</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Nombre</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Base</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Taux Salarial</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Coti. Salariales</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Taux Patronal</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 15%;">
                                                            <strong>Coti. Patronales</strong>
                                                        </td>
                                                    </tr>

                                                    <!-- Salaire de base -->
                                                    @if($base_salary > 0)
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">100</td>
                                                            <td style="border: 1px solid #000; padding: 3px;">Salaire de base</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)($paySlip->basic_salary / ($paySlip->nbre_jour ?? 30)), 0, '.', ' ') }}</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;"></td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: right; font-weight: bold;">{{ number_format((float)$base_salary, 0, '.', ' ') }}</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;"></td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;"></td>
                                                        </tr>
                                                    @endif

                                                    <!-- Allocations -->
                                                    @forelse($allowances as $allowance)
                                                        @php
                                                            $totalAllowances2 += $allowance['amount'] ?? 0;
                                                        @endphp
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $allowance['code'] ?? '' }}</td>
                                                            <td style="border: 1px solid #000; padding: 3px;">{{ $allowance['title'] ?? '' }}</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">
                                                                @if(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                                    {{ $paySlip->enfant_emp ?? 0 }}
                                                                @else
                                                                    {{ $paySlip->nbre_jour ?? 30 }}
                                                                @endif
                                                            </td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">
                                                            {{ number_format((float)round(($allowance['montant'] ?? 0) / 30), 0, '.', ' ')}}
                                                            </td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">
                                                                @if(($allowance['title'] ?? '') == 'Prime d\'ancienneté')
                                                                    {{ number_format((float)$date_pa, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de panier')
                                                                    {{ number_format((float)3, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de salissure')
                                                                    {{ number_format((float)13, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime d\'outillage')
                                                                    {{ number_format((float)10, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de tenue de travail')
                                                                    {{ number_format((float)7, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                                    {{ number_format((float)1500, 0, '.', ' ') }}
                                                                @else
                                                                    <br/>
                                                                @endif
                                                            </td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)round($allowance['amount'] ?? 0), 0, '.', ' ') }}</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" style="border: 1px solid #000; padding: 3px; text-align: center; color: #666;">Aucune allocation</td>
                                                        </tr>
                                                    @endforelse

                                                    <!-- Avantages en nature -->
                                                    @if($amount_avtg > 0)
                                                        <tr>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">150</td>
                                                            <td style="border: 1px solid #000; padding: 3px;">Avantages en nature et en argent</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;"></td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;"></td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$amount_avtg, 0, '.', ' ') }}</td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;"></td>
                                                            <td style="border: 1px solid #000; padding: 3px; text-align: center;"></td>
                                                        </tr>
                                                    @endif

                                                    <!-- Total Brut -->
                                                    <tr style="background-color: #f0f0f0;">
                                                        <td colspan="5" style="border: 1px solid #000; padding: 5px; text-align: right; font-weight: bold;">TOTAL BRUT</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: right; font-weight: bold;">{{ number_format((float)round($base_salary + $totalAllowances2 + $amount_avtg), 0, '.', ' ') }}</td>
                                                        <td colspan="2" style="border: 1px solid #000; padding: 3px; text-align: center;"></td>
                                                    </tr>

                                                    <!-- En-tête tableau gains -->
                                                    <tr>
                                                        <td colspan="2" style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 40%;">
                                                            <strong>Retenues</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Nombre</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Base</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Taux Salarial</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Rete. Salariales</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 10%;">
                                                            <strong>Taux Patronal</strong>
                                                        </td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background-color: #d0d0d0; width: 15%;">
                                                            <strong>Rete. Patronales</strong>
                                                        </td>
                                                    </tr>
                                                    <!-- Retenues -->
                                                    @forelse($retenues as $retenue)
                                                        @if($retenue['code'] && $retenue['code'] != 307 && $retenue['code'] != 308)
                                                            @if($retenue['code'] < 404 && $retenue['code'] > 400)
                                                                @php
                                                                    $retenue['code'] == 403 ? $totalretenuessal2 += $retenue['amount'] : $totalretenuessal2 += 0;
                                                                @endphp
                                                                <tr>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $retenue['code'] }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px;">{{ $retenue['libelle'] }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['jours_work'] ?? '' }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] == 403 ? '' : number_format((float)$retenue['amount'], 0, ',', ' ') }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['code'] == 403 ? number_format((float)$retenue['amount'], 0, ',', ' ') : ''}}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                                </tr>
                                                            @elseif($retenue['code'] == 301 || $retenue['code'] == 302)
                                                                @php
                                                                    $totalretenuessal2 += $retenue['amount'];
                                                                    $totalretenuesemp2 += $retenue['patronale'];
                                                                @endphp
                                                                <tr>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $retenue['code'] }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px;">{{ $retenue['libelle'] }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['jours_work'] ?? '' }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['base'], 0, ',', ' ') }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['taux'] }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['amount'], 0, ',', ' ')}}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['salariale'] }}</td>
                                                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['patronale'], 0, ',', ' ') }}</td>
                                                                </tr>
                                                            @else
                                                                @if($retenue['type'] != 'add')
                                                                    @php
                                                                        $totalretenuesemp2 += $retenue['amount'];
                                                                    @endphp
                                                                    <tr>
                                                                        <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ $retenue['code'] }}</td>
                                                                        <td style="border: 1px solid #000; padding: 3px;">{{ $retenue['libelle'] }}</td>
                                                                        <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['jours_work'] ?? '' }}</td>
                                                                        <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['base'], 0, ',', ' ') }}</td>
                                                                        <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                                        <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                                        <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['taux'] }}</td>
                                                                        <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['amount'], 0, ',', ' ') }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" style="border: 1px solid #000; padding: 3px; text-align: center; color: #666;">Aucune déduction</td>
                                                        </tr>
                                                    @endforelse

                                                    <!-- Total Cotisations -->
                    @foreach($retenues as $retenue)
                        @if(($retenue['type'] ?? '') == 'add' && !in_array($retenue['code'] ?? '', [301, 302, 307, 308, 401, 402, 403]) && ($retenue['libelle'] ?? '') !== 'TOTAL FDFP')
                                <tr>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: center;">{{ ($retenue['libelle'] ?? '') == 'TOTAL FDFP' ? '413' : ($retenue['code'] ?? '') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px;">{{ $retenue['libelle'] ?? '' }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ $retenue['jours_work'] ?? '' }}</td>
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
                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ (isset($base_val) && $base_val !== '' && $base_val != 0) ? number_format((float)$base_val, 0, ',', ' ') : ($fallback_base > 0 ? number_format((float)$fallback_base, 0, ',', ' ') : '') }}</td>
                            <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ (isset($taux_val) && $taux_val !== '' && $taux_val != 0) ? $taux_val : $fallback_taux }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;">{{ number_format((float)$retenue['amount'] ?? 0, 0, ',', ' ') }}</td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                    <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                </tr>
                        @endif
                    @endforeach

                                                    <tr style="background-color: #f0f0f0;">
                                                        <td colspan="5" style="border: 1px solid #000; padding: 5px; text-align: right; font-weight: bold;">TOTAL COTISATIONS</td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: right; font-weight: bold;">{{ number_format((float)$totalretenuessal2, 0, ',', ' ') }}</td>
                                                        <td style="border: 1px solid #000; padding: 3px; text-align: right;"></td>
                                                        <td style="border: 1px solid #000; padding: 5px; text-align: right; font-weight: bold;">{{ number_format((float)$totalretenuesemp2, 0, ',', ' ') }}</td>
                                                    </tr>

                                                    <!-- Net à payer -->
                                                    <tr class="text-center" style="background-color: #e8e8e8; font-weight: bold;">
                                                        <td class="border border-dark color:#000;">Cumuls</td>
                                                        <td class="border border-dark color:#000;">Salaire brut</td>
                                                        <td class="border border-dark color:#000;">Charges salariales</td>
                                                        <td class="border border-dark color:#000;">Charges patronales</td>
                                                        <td class="border border-dark color:#000;">Avantages en nature</td>
                                                        <td class="border border-dark color:#000;">Net imposable</td>
                                                        <td class="border border-dark color:#000;">Heures travaillées</td>
                                                        <td class="border border-dark color:#000;">Heures<br/>supplémentaires</td>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <td class="border border-dark">
                                                            Période<hr/>
                                                            Année
                                                        </td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)$paySlip->salary_brut ?? 0, 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)$toto_brut ?? 0, 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)round($totalretenuessal2 + $total_cantine3), 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)$toto_retenue, 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)round($totalretenuesemp2), 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)$toto_patronales, 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)$amount_avtg, 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)($cpteAvtg * $amount_avtg), 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)$paySlip->net_imposable ?? 0, 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)$toto_impos ?? 0, 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            173,33<hr/>
                                                            {{round(173.33 * $compte)}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)$totoverstimes2, 0, '.', ' ')}}<hr/>{{(number_format((float)$totoverstimes, 0, '.', ' '))}}
                                                        </td>
                                                    </tr>
                                                    <tr style="background-color: #e8e8e8; font-weight: bold;">
                                                        <td colspan="7" style="border: 1px solid #000; padding: 8px; text-align: right;">NET À PAYER</td>
                                                        <td style="border: 1px solid #000; padding: 8px; text-align: center; font-size: 14px;">{{ number_format((float)$paySlip->net_payble ?? 0, 0, '.', ' ') }}</td>
                                                    </tr>

                                                    <!-- Informations complémentaires -->
                                                    <tr>
                                                        <td colspan="8" style="border: 1px solid #000; padding: 5px; font-size: 10px;">
                                                            <strong>Mode de règlement :</strong> {{ $paySlip->employee->paytypeEmp->name ?? '-' }} |
                                                            <strong>Date de paiement :</strong> {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }} |
                                                            <strong>Lieu :</strong> {{ $paySlip->adresse_etp ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- Signatures -->
                                                <div class="row mt-4">
                                                    <div class="col-md-6">
                                                        <div class="project-amnt pt-1" align="" style="color:#000;">
                                                            <b><i>Payé par : {{ $paySlip->employee->paytypeEmp->name ?? '-' }} </i></b>
                                                        </div>
                                                        <p style="font-size: 14px; font-style: italic; color: #666;">
                                                            <i>Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.</i>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <p style="font-size: 14px; margin-bottom: 20px;">Fait à {{ $paySlip->adresse_etp ?? 'Lieu' }}, le {{ \Carbon\Carbon::parse($periode->date_fin)->day(25)->format('d/m/Y') }}</p>
                                                        <p style="border-top: 1px solid #000; padding-top: 5px; width: 200px; margin-left: auto; text-align: center;">
                                                            <strong>LA DIRECTION</strong>
                                                        </p>
                                                        <div class="d-flex justify-content-end">
                                                            <div class="d-flex justify-content-center align-items-center signatureBull2" id="signatureShow2" style="width: 200px; position: relative; display: inline-block;">
                                                                @if($company->electronic_stamp)
                                                                    <img src="{{ url($company->electronic_stamp_url) }}" alt="Cachet" width="80px" style="position: absolute;">
                                                                @else
                                                                    <div class="avatar-initial bg-label-secondary rounded" style="position: absolute;">
                                                                        <i class="fas fa-stamp fa-24px"></i>
                                                                    </div>
                                                                @endif

                                                                @if($company->electronic_signature)
                                                                    <img src="{{ url($company->electronic_signature_url) }}" alt="Signature" width="80px" style="position: absolute;">
                                                                @else
                                                                    <div class="avatar-initial bg-label-secondary rounded" style="position: absolute;">
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
                @endforeach
            </div>

            <!-- Bulletin 3 -->
            <div class="tab-pane fade" id="bull3show">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Boutons logo -->
                    <div class="me-3">
                        <button class="btn btn-success" id="mainShowlogo" onclick="afficheLogSign3()">{{ __('Avec logo') }}</button>
                        <button class="btn btn-warning" id="mainHidelogo" onclick="cacheLogSign3()">{{ __('Sans logo') }}</button>
                    </div>
                    <!-- Bouton télécharger unique -->
                    <button class="btn btn-sm btn-primary" id="mainDownloadButton3"
                        onclick="downloadBulletinsAuto(3, {{ $periode->id }}, 'bulletin_v3_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->format('F Y') }}')">
                        <span class="fa fa-download me-1"></span> Télécharger
                    </button>
                </div>

                @foreach($bulletins as $index => $paySlip)
                                    @php
                                                    // Récupérer et dédoublonner les données depuis le paySlip
                                        $allowances = collect(json_decode($paySlip->allowances ?? '[]', true))->unique('code')->values()->all();
                                        $retenues = collect(json_decode($paySlip->retenues ?? '[]', true))->unique('code')->values()->all();

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
                                        $cpteAvtg = 0;
                                        $compte = 0;
                                        $toto_retenue = 0;
                                        $toto_patronales = 0;
                                        $toto_nature = 0;
                                        $toto_impos = 0;
                                        $toto_brut = 0;
                                        $toto_overtimes = 0;

                                        $monthpaie = \Carbon\Carbon::parse($periode->date_fin)->format('Y-m');

                                        list($selectedYear, $selectedMonth) = explode('-', $monthpaie);

                                        if ($paySlipss->count() < 1) {
                                            $compte = 1;
                                        } else {
                                            // Boucle sur chaque bulletin de paie
                                            foreach ($paySlipss as $pay) {
                                                if ($pay->employee_id == $paySlip->employee_id) {
                                                    // Extraire l'année et le mois à partir de salary_month
                                                    list($year, $month) = explode('-', $pay->salary_month);

                                                    // Vérifier si le bulletin de paie est dans l'année du mois sélectionné
                                                    if ($year == $selectedYear) {
                                                        // Vérifier si le mois du bulletin de paie est inférieur ou égal au mois sélectionné
                                                        if ($month <= $selectedMonth) {
                                                            // Cumuler les valeurs des éléments
                                                            $compte++;
                                                            $toto_brut += $pay->salary_brut;
                                                            $toto_retenue += $pay->total_retenue;
                                                            $toto_patronales += $pay->total_patronale;
                                                            $toto_nature += $pay->avantage_nature;
                                                            $toto_impos += $pay->net_imposable;
                                                            $toto_overtimes += $pay->overtimes;
                                                            foreach ($allowances as $allowance) {
                                                                if ($allowance['code'] == '103') {
                                                                    $totoverstimes += $allowance['amount'] ?? 0;
                                                                    $totoverstimes2 += $allowance['amount'] ?? 0;
                                                                    $totoverstimes3 += $allowance['amount'] ?? 0;
                                                                }
                                                            }
                                                            if ($pay->avtg_real > 0) {
                                                                $cpteAvtg++;
                                                            }
                                                        } else {
                                                            // Sortir de la boucle si le mois du bulletin de paie est supérieur au mois sélectionné
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $cpte_bulletin3++;

                                        // Calculer l'ancienneté - taux = années ENTIÈREMENT complètes (ex: 4a 6m = 4%)
                                        $date_embauche = new DateTime($paySlip->employee->company_doj ?? $paySlip->employee->start_date);
                                        $date_ref_paie = new DateTime($periode->date_fin);
                                        $difference = $date_embauche->diff($date_ref_paie);
                                        $date_pa = intval($difference->format('%y')); // années complètes uniquement
                                        $date_m = intval($difference->format('%m'));

                                        // Unique IDs for this bulletin
                                        $bulletinId = $paySlip->id;
                                        $uniquePrefix = 'bull_' . $bulletinId . '_';
                                    @endphp
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="card-body pagebulletin3" id="bull3-{{ $cpte_bulletin3 }}">
                                                <!-- Logo -->
                                                <div class="logoBull3 me-3" id="mainLogoShow" style="display: block;">
                                                    @if($company->logo)
                                                        <img src="{{ url($company->logo_url) }}" alt="Logo" width="80px;">
                                                    @else
                                                        <div class="avatar-initial bg-label-secondary rounded">
                                                            <i class="fas fa-image fa-24px"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <table class="table-sm mb-3" style="font-family: Arial; font-size: 12px;">
                                                    <tr>
                                                        <td class="border border-dark bg-primary" colspan ="9" align="center" style="vertical-align: middle;">
                                                            <h2 style="color:#fff;"><strong>BULLETIN DE PAIE</strong></h2>
                                                            <p style="color:#fff;">Période : {{ $periode->nom }} | <strong>{{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</strong></p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="50%" bgcolor="#C0C0C0" class="border border-dark" colspan ="4">EMPLOYEUR</td>
                                                        <td width="50%" bgcolor="#C0C0C0" class="border border-dark" colspan ="5">MATRICULE DU SALARIE:   {{ \Auth::user()->employeeIdFormat($paySlip->employee->employee_id ?? 'N/A') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="border border-dark" colspan ="4">
                                                            Nom :  {{ $paySlip->nom_etp ?? 'Entreprise' }}<br>
                                                            Adresse :  {{ $paySlip->adresse_etp ?? 'N/A' }}<br>
                                                            Téléphone : {{ $paySlip->phone_etp ?? 'N/A' }}<br>
                                                            Boite postale :  {{ $paySlip->btp_etp ?? 'N/A' }}<br>
                                                            Horaire mensuelle :  173,33<br>
                                                            Nombre de jours travaillés : {{ $paySlip->nbre_jour ?? 30 }} <br>
                                                            Grille salariale : <strong>{{ $company->sector->name }}</strong><br>
                                                        </td>
                                                        <td class="border border-dark" colspan ="5">
                                                            Nom et Prénom :  {{ $paySlip->employee->name ?? 'N/A' }}<br>
                                                            Adresse :  {{ $paySlip->address_emp ?? 'N/A' }}<br>
                                                            Situation matrimoniale : {{ $paySlip->situation_emp ?? 'N/A' }}<br>
                                                            Enfants à charge : {{ $paySlip->enfant_emp ?? 0 }}<br>
                                                            Numéro CNPS :  {{ $paySlip->num_cnps_emp ?? 'N/A' }}<br>
                                                            Ancienneté :  {{ $paySlip->anciennete_emp }}<br>
                                                            Catégorie : {{ $paySlip->categories_emp ?? 'N/A' }}<br>
                                                            Emploi :   {{ $paySlip->emploi ?? 'N/A' }} <br>
                                                            Tel / E-mail : {{ $paySlip->phone_emp ?? 'N/A' }} <br>
                                                            Nombre de parts : {{ $paySlip->parts_emp ?? 1 }}<br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="5%"bgcolor="#C0C0C0" class="border border-dark" rowspan="2" style="vertical-align: middle; color:#000;" width="3%">N°</td>
                                                        <td width="35%" bgcolor="#C0C0C0" class="border border-dark" rowspan="2" style="vertical-align: middle; color:#000;" width="30%">DÉSIGNATION</td>
                                                        <td width="5%" bgcolor="#C0C0C0" class="border border-dark" rowspan="2" style="vertical-align: middle; color:#000;">NOMBRE</td>
                                                        <td width="5%"bgcolor="#C0C0C0" class="border border-dark" rowspan="2" style="vertical-align: middle; color:#000;">BASE</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark" colspan="3" style="vertical-align: middle; color:#000;">PART SALARIALE</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark" colspan="2" style="vertical-align: middle; color:#000;">PART PATRONALE</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="border border-dark color:#000;">TAUX</td>
                                                        <td class="border border-dark color:#000;">GAIN</td>
                                                        <td class="border border-dark color:#000;">RETENUES</td>
                                                        <td class="border border-dark color:#000;">TAUX</td>
                                                        <td class="border border-dark color:#000;">MONTANT</td>
                                                    </tr>
                                                    @if($base_salary > 0)
                                                        <tr >
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">100</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;">Salaire de base</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)($paySlip->basic_salary / ($paySlip->nbre_jour ?? 30)), 0, '.', ' ') }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)$base_salary, 0, '.', ' ') }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        </tr>
                                                    @endif
                                                    @forelse($allowances as $allowance)
                                                        @php
                                                            $totalAllowances3 += $allowance['amount'] ?? 0;
                                                        @endphp
                                                        <tr >
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $allowance['code'] ?? '' }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;">{{ $allowance['title'] ?? '' }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">
                                                                @if(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                                    {{ $paySlip->enfant_emp ?? 0 }}
                                                                @else
                                                                    {{ $paySlip->nbre_jour ?? 30 }}
                                                                @endif
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">
                                                                {{ number_format((float)round(($allowance['montant'] ?? 0) / 30), 0, '.', ' ') }}
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">
                                                                @if(($allowance['title'] ?? '') == 'Prime d\'ancienneté')
                                                                    {{ number_format((float)$date_pa, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de panier')
                                                                    {{ number_format((float)3, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de salissure')
                                                                    {{ number_format((float)13, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime d\'outillage')
                                                                    {{ number_format((float)10, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Prime de tenue de travail')
                                                                    {{ number_format((float)7, 0, '.', ' ') }}
                                                                @elseif(($allowance['title'] ?? '') == 'Allocation familiales (CNPS)')
                                                                    {{ number_format((float)1500, 0, '.', ' ') }}
                                                                @else
                                                                    <br/>
                                                                @endif
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">
                                                                {{ number_format((float)round($allowance['amount'] ?? 0), 0, '.', ' ') }}
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
                                                        <tr >
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" align="right">
                                                                150
                                                            </td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;">Avantages en nature et en argent</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $paySlip->nbre_jour ?? 30 }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)$amount_avtg, 0, '.', ' ') }}</td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                            <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        </tr>
                                                    @endif
                                                    <tr >
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" align="right"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" align="center"><strong>Total Brut</strong> <hr></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">
                                                            <strong>{{number_format((float)round($base_salary + $totalAllowances3 + $amount_avtg), 0, '.', ' ')}} <hr></strong>
                                                        </td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                    </tr>
                                                    @forelse($retenues as $retenue)
                                                        @if($retenue['code'] && $retenue['code'] != 307 && $retenue['code'] != 308)
                                                            @if($retenue['code'] < 404 && $retenue['code'] > 400)
                                                                @php
                                                                    $retenue['code'] == 403 ? $totalretenuessal3 += $retenue['amount'] : $totalretenuessal3 += 0;
                                                                @endphp
                                                                <tr >
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['code'] }}</td>
                                                                    <td>{{ $retenue['libelle'] }}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['jours_work'] }}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['code'] == 403 ? '' : number_format((float)$retenue['amount'], 0, ',', ' ') }}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['code'] == 403 ? number_format((float)$retenue['amount'], 0, ',', ' ') : ''}}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                                                </tr>
                                                            @elseif($retenue['code'] == 301 || $retenue['code'] == 302)
                                                                @php
                                                                    $totalretenuessal3 += $retenue['amount'];
                                                                    $totalretenuesemp3 += $retenue['patronale'];
                                                                @endphp
                                                                <tr >
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['code'] }}</td>
                                                                    <td>{{ $retenue['libelle'] }}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['jours_work'] }}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)$retenue['base'], 0, ',', ' ') }}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['taux'] }}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)$retenue['amount'], 0, ',', ' ')}}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['salariale'] }}</td>
                                                                    <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)$retenue['patronale'], 0, ',', ' ') }}</td>
                                                                </tr>
                                                            @else
                                                                @if($retenue['type'] != 'add')
                                                                    @php
                                                                        $totalretenuesemp3 += $retenue['amount'];
                                                                    @endphp
                                                                    <tr >
                                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['code'] }}</td>
                                                                        <td>{{ $retenue['libelle'] }}</td>
                                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['jours_work'] }}</td>
                                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)$retenue['base'], 0, ',', ' ') }}</td>
                                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['taux'] }}</td>
                                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)$retenue['amount'], 0, ',', ' ') }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @empty
                                                        <tr >
                                                            <td colspan="9" class="text-center">Aucune déduction</td>
                                                        </tr>
                                                    @endforelse
                    @foreach($retenues as $retenue)
                        @if(($retenue['type'] ?? '') == 'add' && !in_array($retenue['code'] ?? '', [301, 302, 307, 308, 401, 402, 403]) && ($retenue['libelle'] ?? '') !== 'TOTAL FDFP')
                            <tr >
                                <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ ($retenue['libelle'] ?? '') == 'TOTAL FDFP' ? '413' : ($retenue['code'] ?? '') }}</td>
                                <td style="border-left: solid 1px black; border-right: solid 1px black;">{{ $retenue['libelle'] ?? '' }}</td>
                                <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ $retenue['jours_work'] ?? '' }}</td>
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
                            <td class="text-end">{{ (isset($base_val) && $base_val !== '' && $base_val != 0) ? number_format((float)$base_val, 0, ',', ' ') : ($fallback_base > 0 ? number_format((float)$fallback_base, 0, ',', ' ') : '') }}</td>
                            <td class="text-end">{{ (isset($taux_val) && $taux_val !== '' && $taux_val != 0) ? $taux_val : $fallback_taux }}</td>
                            <td class="text-end"></td>
                                <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end">{{ number_format((float)$retenue['amount'] ?? 0, 0, ',', ' ') }}</td>
                                <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                                <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-end"></td>
                            </tr>
                        @endif
                    @endforeach

                                                    <tr >
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" align="right"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" class="text-center"><strong>Total Cotisations</strong><hr></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" align="right"><strong>{{ number_format((float)$totalretenuessal3, 0, ',', ' ') }}<hr></strong></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;"></td>
                                                        <td style="border-left: solid 1px black; border-right: solid 1px black;" align="right"><strong>{{ number_format((float)$totalretenuesemp3, 0, ',', ' ') }}<hr></strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="border border-dark" style="border-top-color: solid 1px #000;" colspan="9">
                                                            <div class="project-amnt pt-1" align="" style="color:#000;">
                                                                <b><i>Payé par : {{ $paySlip->employee->paytypeEmp->name ?? '-' }} </i></b>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">Cumuls</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">Salaire brut</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">Charges salariales</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">Charges patronales</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">Avantages en nature</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">Net imposable</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">Heures travaillées</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">Heures<br/>supplémentaires</td>
                                                        <td bgcolor="#C0C0C0" class="border border-dark color:#000;">NET A PAYER</td>
                                                    </tr>
                                                    <tr class="text-center">
                                                        <td class="border border-dark">
                                                            Période<hr/>
                                                            Année
                                                        </td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)$paySlip->salary_brut ?? 0, 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)$toto_brut ?? 0, 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)round($totalretenuessal3 + $total_cantine3), 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)$toto_retenue, 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)round($totalretenuesemp3), 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)$toto_patronales, 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)$amount_avtg, 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)($cpteAvtg * $amount_avtg), 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)$paySlip->net_imposable ?? 0, 0, '.', ' ')}}<hr/>
                                                            {{number_format((float)$toto_impos ?? 0, 0, '.', ' ')}}</td>
                                                        <td class="border border-dark montant">
                                                            173,33<hr/>
                                                            {{round(173.33 * $compte)}}</td>
                                                        <td class="border border-dark montant">
                                                            {{number_format((float)$totoverstimes3, 0, '.', ' ')}}<hr/>{{(number_format((float)$totoverstimes, 0, '.', ' '))}}</td>
                                                        <td style="vertical-align:middle; border-top-style:solid;border-top-width:3pt;border-left-style:solid;border-left-width:3pt;border-bottom-style:solid;border-bottom-width:3pt;border-right-style:solid;border-right-width:3pt; border-right-color: #000; border-left-color: #000; border-top-color: #000; border-bottom-color: #000">
                                                            <div class="project-amnt pt-1" align="center" style="color:#000;">
                                                                {{number_format((float)$paySlip->net_payble ?? 0, 0, '.', ' ')}}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- Signatures -->
                                                <div class="row mt-4">
                                                    <div class="col-md-6">
                                                        <div class="project-amnt pt-1" align="" style="color:#000;">
                                                            <b><i>Payé par : {{ $paySlip->employee->paytypeEmp->name ?? '-' }} </i></b>
                                                        </div>
                                                        <p style="font-size: 14px; font-style: italic; color: #666;">
                                                            <i>Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.</i>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <p style="font-size: 14px; margin-bottom: 20px;">Fait à {{ $paySlip->adresse_etp ?? 'Lieu' }}, le {{ \Carbon\Carbon::parse($periode->date_fin)->day(25)->format('d/m/Y') }}</p>
                                                        <p style="border-top: 1px solid #000; padding-top: 5px; width: 200px; margin-left: auto; text-align: center;">
                                                            <strong>LA DIRECTION</strong>
                                                        </p>
                                                        <div class="d-flex justify-content-end">
                                                            <div class="d-flex justify-content-center align-items-center signatureBull3" id="signatureShow3" style="width: 200px; position: relative; display: inline-block;">
                                                                @if($company->electronic_stamp)
                                                                    <img src="{{ url($company->electronic_stamp_url) }}" alt="Cachet" width="80px" style="position: absolute;">
                                                                @else
                                                                    <div class="avatar-initial bg-label-secondary rounded" style="position: absolute;">
                                                                        <i class="fas fa-stamp fa-24px"></i>
                                                                    </div>
                                                                @endif

                                                                @if($company->electronic_signature)
                                                                    <img src="{{ url($company->electronic_signature_url) }}" alt="Signature" width="80px" style="position: absolute;">
                                                                @else
                                                                    <div class="avatar-initial bg-label-secondary rounded" style="position: absolute;">
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
                @endforeach
            </div>

            <!-- Modèle 4 : présentation encadrée, inspirée du premier exemple fourni -->
            <div class="tab-pane fade" id="bull4show">
                <div class="d-flex justify-content-end align-items-center mb-3">
                    <button class="btn btn-sm btn-primary" id="mainDownloadButton4"
                        onclick="downloadBulletinsAuto(4, {{ $periode->id }}, 'bulletin_v4_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->format('F Y') }}')">
                        <span class="fa fa-download me-1"></span> Télécharger
                    </button>
                </div>
                @foreach($bulletins as $bulletin)
                    <div class="card-body pagebulletin4 mb-4" style="background:#fff; padding:12px;">
                        @include('declarations::pdf.bulletin_reference', ['bulletin' => $bulletin, 'company' => $company, 'periode' => $periode, 'variant' => 4])
                    </div>
                @endforeach
            </div>

            <!-- Modèle 5 : présentation épurée bleue, inspirée du second exemple fourni -->
            <div class="tab-pane fade" id="bull5show">
                <div class="d-flex justify-content-end align-items-center mb-3">
                    <button class="btn btn-sm btn-primary" id="mainDownloadButton5"
                        onclick="downloadBulletinsAuto(5, {{ $periode->id }}, 'bulletin_v5_{{$company->name}}_{{\Carbon\Carbon::parse($periode->date_debut)->format('F Y') }}')">
                        <span class="fa fa-download me-1"></span> Télécharger
                    </button>
                </div>
                @foreach($bulletins as $bulletin)
                    <div class="card-body pagebulletin5 mb-4" style="background:#fff; padding:12px;">
                        @include('declarations::pdf.bulletin_reference', ['bulletin' => $bulletin, 'company' => $company, 'periode' => $periode, 'variant' => 5])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center">
        <h4>Aucun bulletin trouvé pour cette période</h4>
        <p>Veuillez sélectionner une autre période ou vérifier que des bulletins ont été générés.</p>
    </div>
@endif
