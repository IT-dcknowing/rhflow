
<style>
    /* Bulletin 3 : forcer les bordures sur toutes les cellules */
    #payslipContentBull3 table,
    #payslipContentBull3 table td,
    #payslipContentBull3 table th,
    #payslipContentBull3 table tr {
        border: 1px solid #000 !important;
        border-collapse: collapse;
    }
    #payslipContentBull3 table {
        border-collapse: collapse !important;
        width: 100% !important;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #payslipContent, #payslipContent * {
            visibility: visible;
        }
        #payslipContent {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        #payslipContentBull3, #payslipContentBull3 * {
            visibility: visible;
        }
        #payslipContentBull3 {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn-print {
            display: none !important;
        }
    }
</style>
@php 
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
    
    $base_salary = 0;
    $amount_avtg = 0;
    $compte = 0;
    // Anciennete basee sur la FIN DE PERIODE (ex: paie Janvier 2025 = date ref 31/01/2025)
    $date_embauche = new DateTime($employee->company_doj ?: ($employee->start_date ?: 'now'));
    $date_ref_paie = new DateTime($periode->date_fin ?: 'now');
    $difference = $date_embauche->diff($date_ref_paie);
    $date_pa = intval($difference->format('%y')); // annees completes uniquement
    $date_m  = intval($difference->format('%m'));
    // Jours travailles : meme source que get_brut_salary() pour eviter toute divergence
    $nbre_jours_base = intval($employee->get_jours_work($periode->id));
    if($nbre_jours_base <= 0){
        $nbre_jours_base = 30;
    }

    // Proratisation du salaire categoriel sur une base de 30 jours
    if($nbre_jours_base == 30){
        $base_salary = $employee->salary;
    }else{
        $base_salary = round(($employee->salary / 30) * $nbre_jours_base);
    }

    // Dédoublonnage des rubriques par code
    $allowances = $allowances->unique('code');
    $retenuesEmp = $retenuesEmp->unique('code');
@endphp
<div class= "row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <ul class="nav nav-pills flex-column flex-md-row mb-4">
                <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull1show" id="affichebull1"><i class="ti ti-note ti-xs me-1"></i> Bulletin 1</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull3show" id="affichebull3"><i class="ti ti-note ti-xs me-1"></i> Bulletin 3</a></li>
            </ul>
            <h5 class="mb-0">Exercice : {{$exercice->nom}} | {{$periode->nom}}</h5>
        </div>
    </div>
    <hr>
</div>
<div class="tab-content mb-4">
    <div class="tab-pane fade show active" id="bull1show">
        <div class="row">
            <div class="col-12">
                <div class="card-body" id="payslipContent">
                    <div class="table-responsive mb-4">
                        <table class="table table-sm mb-4" style="font-family: Arial; font-size: 12px;">
                            <!-- En-tête du bulletin -->
                            <tr class="table-success">
                                <td colspan="9" class="text-center">
                                    <h4><strong>BULLETIN DE PAIE</strong></h4>
                                    <p class="mb-1">Période: {{ $periode->nom }} | {{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</p>
                                </td>
                            </tr>
                            <!-- Informations employé -->
                            <tr class="table-primary">
                                <td colspan="3">
                                    EMPLOYEUR
                                </td>
                                <td colspan="6">
                                    MATRICULE DU SALARIE:   {{ \Auth::user()->employeeIdFormat($employee['employee_id']) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    Nom :  {{ $company->name }}<br>
                                    Adresse :  {{ $company->city }}, {{ $company->address }}<br>
                                    Téléphone : {{ $company->phone }}<br>
                                    Boite postale :  {{ $company->postal_code }}<br>
                                    Horaire mensuelle :  173,33<br>
                                    Nombre de jours travaillés : {{ $nbre_jours_base }} <br>
                                    Grille salariale : <strong>{{ $company->sector->name ?? '-' }}</strong><br>
                                </td>
                                <td colspan="6">
                                    Nom et Prénom :  {{ $employee->name }}<br>
                                    Adresse :  {{ $employee->address }}<br>
                                    Situation matrimoniale : {{ $employee->situation->name ?? '-' }}<br>
                                    Enfants à charge : {{ $employee->enfant }}<br>
                                    Numéro CNPS :  {{ $employee->num_cnps }}<br>
                                    Ancienneté :  {{$date_pa}} an(s) et {{$date_m}} mois<br>
                                    Catégorie : {{ $employee->categorieEmp->title ?? '-' }} / {{$employee->sous_categorie}}<br>
                                    Emploi :   {{ $employee->designation->name ?? '-' }} <br>
                                    Tel / E-mail : {{$employee->phone}} / {{$employee->email}} <br>
                                    Nombre de parts : {{ $employee->parts }}<br>
                                </td>
                            </tr>     
                            <tr class="table-success">
                                <th width="5%" rowspan="2" class="text-center align-middle">N°</th>
                                <th width="25%" rowspan="2" class="text-center align-middle">DÉSIGNATION</th>
                                <th width="5%" rowspan="2" class="text-center align-middle">NOMBRE</th>
                                <th rowspan="2" class="text-center align-middle">BASE</th>
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
                                    <td class="text-end">{{ $nbre_jours_base }}</td>
                                    <td class="text-end">{{ number_format(($employee->salary/30), 0, '.', ' ') }}</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($base_salary, 0, '.', ' ') }}</td>                       
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endif
                                @forelse($allowances as $allowance)
                                @php
                                    $totalAllowances += $allowance->amount;
                                @endphp
                                <tr>
                                    <td class="text-end">{{ $allowance->code }}</td>
                                    <td>{{ $allowance->title }}</td>
                                    <td class="text-end">
                                        @if($allowance->allowance_option == '26')
                                            {{$employee->enfant}}
                                        @else
                                            {{ $nbre_jours_base }}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($allowance->allowance_option == '1')
                                            {{ number_format(round($allowance->montant/30), 0, '.', ' ')}}
                                        @elseif($allowance->allowance_option == '2')
                                            <br/>
                                        @elseif($allowance->allowance_option == '4')
                                                {{ number_format($employee->salary, 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '10')
                                            {{ number_format(round(75000/173.33), 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '7')
                                            {{ number_format(round(75000/173.33), 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '11')
                                            @if($company->city =='ABIDJAN' || $company->city == 'Abidjan' || $company->city =='abidjan' || strpos($company->city, 'Abidjan') !== false)
                                                {{ number_format(30000/30, 0, '.', ' ') }}
                                            @elseif ($company->city=='Bouaké' || $company->city=='BOUAKE' || $company->city=='bouaké')
                                                {{ number_format(round(24000/30), 0, '.', ' ') }}
                                            @else
                                                {{ number_format(round(22000/30), 0, '.', ' ') }}
                                            @endif
                                        @elseif($allowance->allowance_option == '12')
                                            {{ number_format(round(75000/173.33), 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '13')
                                            {{ number_format(round(75000/173.33), 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '17')
                                            <br/>
                                        @elseif($allowance->allowance_option == '26')
                                            <br/>
                                        @else
                                            {{ number_format(round($allowance->montant/30), 0, '.', ' ')}}
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($allowance->allowance_option == '1')
                                            <br/>
                                        @elseif($allowance->allowance_option == '2')
                                            <br/>
                                        @elseif($allowance->allowance_option == '4')
                                            {{ number_format($date_pa, 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '10')
                                            {{ number_format(3, 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '7')
                                            {{ number_format(13, 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '12')
                                            {{ number_format(10, 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '13')
                                            {{ number_format(7, 0, '.', ' ') }}
                                        @elseif($allowance->allowance_option == '17')
                                            <br/>
                                        @elseif($allowance->allowance_option == '26')
                                            {{ number_format(1500, 0, '.', ' ') }}
                                        @else
                                            <br/>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{ number_format(round($allowance->amount), 0, '.', ' ') }}
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
                                    <td></td><td></td><td></td><td></td><td></td><td></td>
                                </tr>
                                @endforelse
                                @if($employee->avantages()->where('is_active', 1)->exists())
                                @php
                                    $amount_avtg = $employee->avantages()->where('is_active', 1)->sum('amount_reel');
                                @endphp
                                    <tr>
                                        <td align="right">
                                            <?php echo '150'; ?>
                                        </td>
                                        <td>Avantages en nature et en argent</td>
                                        <td class="text-end">{{ $employee->tax_payer_id }}</td>
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
                                @forelse($retenuesEmp as $retenue)
                                    @if($retenue->code && $retenue->code!=307 && $retenue->code!=308)
                                        @if($retenue->code < 404 && $retenue->code >400)
                                        @php
                                            $retenue->code == 403 ? $totalretenuessal += $retenue->amount : $totalretenuessal +=0;
                                        @endphp  
                                        <tr>
                                            <td class="text-end">{{ $retenue->code }}</td>
                                            <td>{{ $retenue->libelle }}</td>
                                            <td class="text-end">{{ $retenue->jours_work }}</td>
                                            <td class="text-end">{{ $retenue->code == 403 ? '' : number_format($retenue->amount, 0, ',', ' ') }}</td>
                                            <td class="text-end"></td>
                                            <td class="text-end"></td>
                                            <td class="text-end">{{ $retenue->code == 403 ? number_format($retenue->amount, 0, ',', ' ') : ''}}</td>
                                            <td class="text-end"></td>
                                            <td  class="text-end"></td>
                                        </tr>
                                        @elseif($retenue->code == 301 || $retenue->code == 302)
                                        @php
                                            $pat_val = $retenue->patronale > 0 ? $retenue->patronale : ($retenue->code == 301 ? ($retenuesEmp->firstWhere('code', 308)->amount ?? 0) : ($retenuesEmp->firstWhere('code', 307)->amount ?? 0));
                                            $pat_rate = $retenue->code == 301 ? '7.70%' : '0.50%';
                                            $totalretenuessal += $retenue->amount;
                                            $totalretenuesemp += $pat_val;
                                        @endphp
                                        <tr>
                                            <td class="text-end">{{ $retenue->code }}</td>
                                            <td>{{ $retenue->libelle }}</td>
                                            <td class="text-end">{{ $retenue->jours_work }}</td>
                                            <td class="text-end">{{ number_format($retenue->base, 0, ',', ' ') }}</td>
                                            <td class="text-end">{{ $retenue->taux }}</td>
                                            <td class="text-end"></td>
                                            <td class="text-end">{{ number_format($retenue->amount, 0, ',', ' ')}}</td>
                                            <td class="text-end">{{ $pat_rate }}</td>
                                            <td class="text-end">{{ number_format($pat_val, 0, ',', ' ') }}</td>
                                        </tr>
                                        @else
                                             @if($retenue->type != 'add' || in_array($retenue->code, [305, 306, 307, 308, 409, 410, 411, 412]))
                                             @php
                                                $totalretenuesemp += $retenue->amount;
                                            @endphp
                                            <tr>
                                                <td  class="text-end">{{ $retenue->code }}</td>
                                                <td>{{ $retenue->libelle }}</td>
                                                <td class="text-end">{{ $retenue->jours_work }}</td>
                                                <td class="text-end">{{ number_format($retenue->base, 0, ',', ' ') }}</td>
                                                <td class="text-end"></td>
                                                <td class="text-end"></td>
                                                <td class="text-end"></td>
                                                <td class="text-end">{{ $retenue->taux }}</td>
                                                <td  class="text-end">{{ number_format($retenue->amount, 0, ',', ' ') }}</td>
                                            </tr>
                                            @endif
                                        @endif
                                    @endif
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">Aucune déduction</td>
                                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
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
                                 @foreach($retenuesEmp as $retenue)
                                     @if($retenue->type == 'add' && !in_array($retenue->code, [301, 302, 305, 306, 307, 308, 401, 402, 403, 409, 410, 411, 412]))
                                         <tr>
                                            <td class="text-end">{{ $retenue->code }}</td>
                                            <td>{{ $retenue->libelle }}</td>
                                            <td class="text-end">{{ $retenue->jours_work }}</td>
                                            <td class="text-end"></td>
                                            <td class="text-end"></td>
                                            <td class="text-end"></td>
                                            <td class="text-end">{{ number_format($retenue->amount, 0, ',', ' ') }}</td>
                                            <td class="text-end"></td>
                                            <td  class="text-end"></td>
                                        </tr>
                                    @endif
                                @endforeach
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
                                <td>Heures<br/>supplémentaires</td>
                                <td>NET A PAYER</td>
                            </tr>
                            <tr>
                                <td class="text-center">
                                    Période<hr/>
                                    Année
                                </td>
                                <td class="text-center">
                                    {{number_format($employee->get_brut_salary($periode->id), 0 ,'.',' ')}}<hr/>
                                    {{number_format($employee->getTotalSalarybrut(), 0 ,'.',' ')}}</td>
                                <td class="text-center">
                                    {{number_format(round($totalretenuessal+$total_cantine), 0 ,'.',' ')}}<hr/>
                                    {{number_format($employee->getTotalSalaryRetenue(), 0 ,'.',' ')}}</td>
                                <td class="text-center">
                                    {{number_format(round($totalretenuesemp), 0 ,'.',' ')}}<hr/>
                                    {{number_format($employee->getTotalSalaryPatronale(), 0 ,'.',' ')}}</td>
                                <td class="text-center">
                                    {{number_format($amount_avtg, 0 , '.' , ' ')}}<hr/>
                                    {{number_format(($compte*$amount_avtg), 0 , '.' , ' ')}}</td>
                                <td class="text-center">
                                    {{number_format($employee->get_salary_imposable($periode->id), 0 ,'.',' ')}}<hr/>
                                    {{number_format($employee->getTotalSalaryImposable(), 0 ,'.',' ')}}</td>
                                <td class="text-center">
                                    173,33<hr/>
                                    {{round(173.33*$compte)}}</td>
                                <td class="text-center">
                                    {{number_format($totoverstimes,0,'.',' ')}}<hr/>{{(number_format($totoverstimes,0,'.',' '))}}</td>
                                <td class="text-center">
                                    <strong style="color:#000;">
                                        {{number_format(($employee->get_net_salary($periode->id)), 0 ,'.',' ')}}
                                    </stong>
                                </td>
                            </tr>
                        </table>
                    </div>
                     <!-- Signature -->
                    <div class="row mt-5">
                        <div class="col-md-8 align-items-center" style="color:#000;">
                            <b><i>Payé par : {{ $employee->paytypeEmp->name ?? '-' }} </i></b><br>
                            <i> Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.</i>
                        </div>
                        <div class="col-md-4 text-center">
                            <p>Le {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</p>   
                            <p><strong> LA DIRECTION </storng></p>
                            <p class="border-top pt-2">Signature</p>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="bull3show" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card-body" id="payslipContentBull3">
                    <table class="table-sm table-bordered" style="font-family: Arial; font-size: 12px; border-collapse: collapse; width: 100%;">
                        <tr>
                            <td class="border border-dark bg-primary" colspan ="9" align="center" style="vertical-align: middle;">
                                <h2 style="color:#fff;"><strong>BULLETIN DE PAIE</strong></h2>
                                <p style="color:#fff;">Période : <strong>{{ \Carbon\Carbon::parse($periode->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($periode->date_fin)->format('d/m/Y') }}</strong></p>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%" bgcolor="#C0C0C0" class="border border-dark" colspan ="4">EMPLOYEUR</td>
                            <td width="50%" bgcolor="#C0C0C0" class="border border-dark" colspan ="5">MATRICULE DU SALARIE:   {{ \Auth::user()->employeeIdFormat($employee['employee_id']) }}</td>
                        </tr>
                        <tr>   
                            <td class="border border-dark" colspan ="4">
                                Nom :  {{ $company->name }}<br>
                                Adresse :  {{ $company->city }}, {{ $company->address }}<br>
                                Téléphone : {{ $company->phone }}<br>
                                Boite postale :  {{ $company->postal_code }}<br>
                                Horaire mensuelle :  173,33<br>
                                Nombre de jours travaillés : {{ $nbre_jours_base }} <br>
                                Grille salariale : <strong>{{ $company->sector->name ?? '-' }}</strong><br>
                            </td>
                            <td class="border border-dark" colspan ="5">
                                Nom et Prénom :  {{ $employee->name }}<br>
                                Adresse :  {{ $employee->address }}<br>
                                Situation matrimoniale : {{ $employee->situation->name ?? '-' }}<br>
                                Enfants à charge : {{ $employee->enfant }}<br>
                                Numéro CNPS :  {{ $employee->num_cnps }}<br>
                                Ancienneté :  {{$date_pa}} an(s) et {{$date_m}} mois<br>
                                Catégorie : {{ $employee->categorieEmp->title ?? '-' }} / {{$employee->sous_categorie}}<br>
                                Emploi :   {{ $employee->designation->name ?? '-' }} <br>
                                Tel / E-mail : {{$employee->phone}} / {{$employee->email}} <br>
                                Nombre de parts : {{ $employee->parts }}<br>
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
                        <tr style="border-bottom-color: inherit;">
                            <td class="text-end">100</td>
                            <td>Salaire de base</td>
                            <td class="text-end">{{ $nbre_jours_base }}</td>
                            <td class="text-end">{{ number_format(($employee->salary/30), 0, '.', ' ') }}</td>
                            <td></td>
                            <td class="text-end">{{ number_format($base_salary, 0, '.', ' ') }}</td>                       
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endif
                        @forelse($allowances as $allowance)
                        @php
                            $totalAllowances3 += $allowance->amount;
                        @endphp
                        <tr style="border-bottom-color: inherit;">
                            <td class="text-end">{{ $allowance->code }}</td>
                            <td>{{ $allowance->title }}</td>
                            <td class="text-end">
                                @if($allowance->allowance_option == '26')
                                    {{$employee->enfant}}
                                @else
                                    {{ $nbre_jours_base }}
                                @endif
                            </td>
                            <td class="text-end">
                                @if($allowance->allowance_option == '1')
                                    {{ number_format(round($allowance->montant/30), 0, '.', ' ')}}
                                @elseif($allowance->allowance_option == '2')
                                    <br/>
                                @elseif($allowance->allowance_option == '4')
                                        {{ number_format($employee->salary, 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '10')
                                    {{ number_format(round(75000/173.33), 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '7')
                                    {{ number_format(round(75000/173.33), 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '11')
                                    @if($company->city =='ABIDJAN' || $company->city == 'Abidjan' || $company->city =='abidjan' || strpos($company->city, 'Abidjan') !== false)
                                        {{ number_format(30000/30, 0, '.', ' ') }}
                                    @elseif ($company->city=='Bouaké' || $company->city=='BOUAKE' || $company->city=='bouaké')
                                        {{ number_format(round(24000/30), 0, '.', ' ') }}
                                    @else
                                        {{ number_format(round(22000/30), 0, '.', ' ') }}
                                    @endif
                                @elseif($allowance->allowance_option == '12')
                                    {{ number_format(round(75000/173.33), 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '13')
                                    {{ number_format(round(75000/173.33), 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '17')
                                    <br/>
                                @elseif($allowance->allowance_option == '26')
                                    <br/>
                                @else
                                    {{ number_format(round($allowance->montant/30), 0, '.', ' ')}}
                                @endif
                            </td>
                            <td class="text-end">
                                @if($allowance->allowance_option == '1')
                                    <br/>
                                @elseif($allowance->allowance_option == '2')
                                    <br/>
                                @elseif($allowance->allowance_option == '4')
                                    {{ number_format($date_pa, 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '10')
                                    {{ number_format(3, 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '7')
                                    {{ number_format(13, 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '12')
                                    {{ number_format(10, 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '13')
                                    {{ number_format(7, 0, '.', ' ') }}
                                @elseif($allowance->allowance_option == '17')
                                    <br/>
                                @elseif($allowance->allowance_option == '26')
                                    {{ number_format(1500, 0, '.', ' ') }}
                                @else
                                    <br/>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format(round($allowance->amount), 0, '.', ' ') }}
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
                            <td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>
                        @endforelse
                        @if($employee->avantages()->where('is_active', 1)->exists())
                        @php
                            $amount_avtg = $employee->avantages()->where('is_active', 1)->sum('amount_reel');
                        @endphp
                            <tr style="border-bottom-color: inherit;">
                                <td align="right">
                                    <?php echo '150'; ?>
                                </td>
                                <td>Avantages en nature et en argent</td>
                                <td class="text-end">{{ $employee->tax_payer_id }}</td>
                                <td></td>
                                <td></td>
                                <td class="text-end">{{ number_format($amount_avtg, 0 , '.' , ' ') }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endif
                        <tr style="border-bottom-color: inherit;">                        
                            <td align="right"></td>
                            <td align="center"><strong>Total Brut</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end">
                                <strong>{{number_format(round($base_salary + $totalAllowances3 + $amount_avtg), 0 , '.' , ' ')}}</strong>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>               
                        @forelse($retenuesEmp as $retenue)
                            @if($retenue->code && $retenue->code!=307 && $retenue->code!=308)
                                @if($retenue->code < 404 && $retenue->code >400)
                                @php
                                    $retenue->code == 403 ? $totalretenuessal3 += $retenue->amount : $totalretenuessal3 +=0;
                                @endphp
                                <tr style="border-bottom-color: inherit;">
                                    <td class="text-end">{{ $retenue->code }}</td>
                                    <td>{{ $retenue->libelle }}</td>
                                    <td class="text-end">{{ $retenue->jours_work }}</td>
                                    <td class="text-end">{{ $retenue->code == 403 ? '' : number_format($retenue->base, 0, ',', ' ') }}</td>
                                    <td class="text-end"></td>
                                    <td class="text-end"></td>
                                    <td class="text-end">{{ number_format($retenue->amount, 0, ',', ' ') }}</td>
                                    <td class="text-end"></td>
                                    <td class="text-end"></td>
                                </tr>
                                @elseif($retenue->code == 301 || $retenue->code == 302)
                                @php
                                    $pat_val = $retenue->patronale > 0 ? $retenue->patronale : ($retenue->code == 301 ? ($retenuesEmp->firstWhere('code', 308)->amount ?? 0) : ($retenuesEmp->firstWhere('code', 307)->amount ?? 0));
                                    $pat_rate = $retenue->code == 301 ? '7.70%' : '0.50%';
                                    $totalretenuessal3 += $retenue->amount;
                                    $totalretenuesemp3 += $pat_val;
                                @endphp
                                <tr style="border-bottom-color: inherit;">
                                    <td class="text-end">{{ $retenue->code }}</td>
                                    <td>{{ $retenue->libelle }}</td>
                                    <td class="text-end">{{ $retenue->jours_work }}</td>
                                    <td class="text-end">{{ number_format($retenue->base, 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ $retenue->taux }}</td>
                                    <td class="text-end"></td>
                                    <td class="text-end">{{ number_format($retenue->amount, 0, ',', ' ')}}</td>
                                    <td class="text-end">{{ $pat_rate }}</td>
                                    <td  class="text-end">{{ number_format($pat_val, 0, ',', ' ') }}</td>
                                </tr>
                                @else
                                    @if($retenue->type != 'add' || in_array($retenue->code, [305, 306, 307, 308, 409, 410, 411, 412]))
                                    @php
                                        $totalretenuesemp3 += $retenue->amount;
                                    @endphp
                                    <tr style="border-bottom-color: inherit;">
                                        <td class="text-end">{{ $retenue->code }}</td>
                                        <td>{{ $retenue->libelle }}</td>
                                        <td class="text-end">{{ $retenue->jours_work }}</td>
                                        <td class="text-end">{{ number_format($retenue->base, 0, ',', ' ') }}</td>
                                        <td class="text-end"></td>
                                        <td class="text-end"></td>
                                        <td class="text-end"></td>
                                        <td class="text-end">{{ $retenue->taux }}</td>
                                        <td  class="text-end">{{ number_format($retenue->amount, 0, ',', ' ') }}</td>
                                    </tr>
                                    @endif
                                @endif
                            @endif
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">Aucune déduction</td>
                            <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>
                        @endforelse                        
                        <tr style="border-bottom-color: inherit;">
                            <td align="right"></td>
                            <td class="text-center"><strong>Total Cotisations</strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td align="right"><strong>{{ number_format($totalretenuessal3, 0, ',', ' ') }}</strong></td>
                            <td></td>
                            <td align="right"><strong>{{ number_format($totalretenuesemp3, 0, ',', ' ') }}</strong></td>
                        </tr>
                        @foreach($retenuesEmp as $retenue)
                            @if($retenue->type == 'add' && !in_array($retenue->code, [301, 302, 305, 306, 307, 308, 401, 402, 403, 409, 410, 411, 412]))
                                <tr style="border-bottom-color: inherit;">
                                    <td class="text-end">{{ $retenue->code }}</td>
                                    <td>{{ $retenue->libelle }}</td>
                                    <td class="text-end">{{ $retenue->jours_work }}</td>
                                    <td class="text-end"></td>
                                    <td class="text-end"></td>
                                    <td class="text-end"></td>
                                    <td class="text-end">{{ number_format($retenue->amount, 0, ',', ' ') }}</td>
                                    <td class="text-end"></td>
                                    <td  class="text-end"></td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td class="border border-dark" style="border-top-color: solid 1px #000;" colspan="9">
                                <div class="project-amnt pt-1" align="" style="color:#000;">
                                    <b><i>Payé par : {{ $employee->paytypeEmp->name ?? '-' }} </i></b>
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
                                {{number_format($employee->get_brut_salary(), 0 ,'.',' ')}}<hr/>
                                {{number_format($employee->getTotalSalarybrut(), 0 ,'.',' ')}}</td>
                            <td class="border border-dark montant">
                                {{number_format(round($totalretenuessal3+$total_cantine3), 0 ,'.',' ')}}<hr/>
                                {{number_format($employee->getTotalSalaryRetenue(), 0 ,'.',' ')}}</td>
                            <td class="border border-dark montant">
                                {{number_format(round($totalretenuesemp3), 0 ,'.',' ')}}<hr/> 
                                {{number_format($employee->getTotalSalaryPatronale(), 0 ,'.',' ')}}</td>
                            <td class="border border-dark montant">
                                {{number_format($amount_avtg, 0 , '.' , ' ')}}<hr/>
                                {{number_format(($compte*$amount_avtg), 0 , '.' , ' ')}}</td>
                            <td class="border border-dark montant">
                                {{number_format($employee->get_salary_imposable(), 0 ,'.',' ')}}<hr/>
                                {{number_format($employee->getTotalSalaryImposable(), 0 ,'.',' ')}}</td>
                            <td class="border border-dark montant">
                                173,33<hr/>
                                {{round(173.33*$compte)}}</td>
                            <td class="border border-dark montant">
                                {{number_format($totoverstimes,0,'.',' ')}}<hr/>{{(number_format($totoverstimes,0,'.',' '))}}</td>
                            <td style="vertical-align:middle; border-top-style:solid;border-top-width:3pt;border-left-style:solid;border-left-width:3pt;border-bottom-style:solid;border-bottom-width:3pt;border-right-style:solid;border-right-width:3pt; border-right-color: #000; border-left-color: #000; border-top-color: #000; border-bottom-color: #000">
                                <div class="project-amnt pt-1" align="center" style="color:#000;">
                                    {{number_format(($employee->get_net_salary($periode->id)), 0 ,'.',' ')}}
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div class="row">
                        <div class="col-md-6 text-start"><i> Pour vous aider à faire valoir vos droits, conservez ce bulletin de paie sans limitation de durée.</i></div>
                        <div class="col-md-6 text-end" style="color:#000;"><u><strong> LA DIRECTION </storng></u></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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



