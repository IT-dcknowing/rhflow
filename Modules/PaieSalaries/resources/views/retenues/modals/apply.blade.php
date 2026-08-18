@php
    $real = 0; $real2 = 0; $totalimpots = 0;
    $bareme = 0; $bareme2 = 0; $salaryBase = 0;
    $avtg_reat = 0; $avtg_reat2 = 0;
    $avtg_bareme = 0; $avtg_bareme2 = 0;
    $avtg_reat_arg = 0; $avtg_reat_arg2 = 0;
    $avtg_bareme_arg = 0; $avtg_bareme_arg2 = 0;
    $total_brut = 0; $totalbase = 0; $total = 0;
    $total_brut_base_dix = 0; $total_sbi = 0; $total_sbs = 0;
    $totoezro = 0; $resultimpricf = 0; $resultricf = 0;
    $exo = 0; $impots = 0; $retenue2 = 0;
    $resltexo = 0;
    foreach ($employee->avantages as $avtg){
        if($avtg->type_avantage == 'avantage_en_nature'){
            $real += $avtg->montant_reel;
            $bareme += $avtg->amount;
        }else{
            $avtg_reat_arg += $avtg->montant_reel;
            $avtg_bareme_arg += $avtg->amount;
        }
        $avtg_reat += $avtg->montant_reel;
        $avtg_bareme += $avtg->amount;
    }

    foreach ($employee->avantages as $avtg){
        if($avtg->type_avantage == 'avantage_en_nature'){
            $real2 += $avtg->montant_reel;
            $bareme2 += $avtg->amount;
        }else{
            $avtg_reat_arg2 += $avtg->montant_reel;
            $avtg_bareme_arg2 += $avtg->amount;
        }
        $avtg_reat2 += $avtg->montant_reel;
        $avtg_bareme2 += $avtg->amount;
    }

    if($employee->tax_payer_id == '30'){
        $salaryBase = $employee->salary;
    }else{
        $salaryBase = $employee->salary;
    }
@endphp

<!-- retenues -->
<input type="text" name="employee_id" value="{{$employee->id}}" hidden>
<div class="row" id="retenues" role="tabpanel">
    <div class="mb-4">
        <h5 class="text-center">Nombre de jours travaillés : <strong class="text-info">{{ $employee->tax_payer_id }}</strong></h5>
    </div>
    <ul class="nav nav-pills flex-column flex-md-row mb-4">
        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#retenuesSalariales">Retenues Salairiales &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
        <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#imposable">Salaire Brut Imposable (SBI) &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#sociale">Salaire Brut Social (SBS)  &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#employes">Charges Employé  &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#employeurs">Charges Employeur  &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
    </ul>
    <div class="tab-content border-top">
        <div class="tab-pane fade " id="retenuesSalariales" role="tabpanel" align="center">
            <div class="col-xl-12 col-md-12 col-12 mb-md-0 mb-4">
                <div class="mb-4"  style="background-color:#fffff; padding:15px;border-radius: 10px;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Libellé</th>
                                <th>Base/Montant/Taux</th>
                                <th>Période</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->retenues()->where('periode_id', $periode->id)->get() as $retenue)
                                @if($retenue->type_retenue_id != 5)
                                <tr>
                                    <td>{{ $retenue->ordre ? $retenue->ordre : $retenue->ordre + 1 }}</td>
                                    <td>
                                        {{$retenue->code ? $retenue->code : '-'}}
                                    </td>
                                    <td>{{ $retenue->libelle }}</td>
                                    <td>
                                        <span class="badge bg-label-dark"> {{  $retenue->base ? $retenue->base : '-' }} </span> :
                                        <span class="badge bg-label-danger"> {{ number_format($retenue->amount, 0, ',', ' ') . ' FCFA' }} </span> :
                                        <span class="badge bg-label-info"> {{  $retenue->taux ? $retenue->taux. '%' : '-' }} </span>
                                    </td>
                                    <td>{{ $retenue->periode->nom }}</td>
                                    <td>
                                        <span class="badge bg-{{ $retenue->is_active ? 'success' : 'secondary' }}">
                                            {{ $retenue->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                </tr>
                                @endif
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Aucune retenue enregistrée</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade show active" id="imposable" role="tabpanel" align="center">
            <div class="col-xl-12 col-md-12 col-12 mb-md-0 mb-4">
                <div class="mb-4"  style="background-color:#1c232f; padding:15px;border-radius: 10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column m-sm-3 m-0">
                            <div class="mb-xl-0 mb-4">
                                <h6 class="fw-medium mb-2" style="color: #fff;">Salaire Brut Total :  <strong style="background-color:#ddcd08; color: #000;">{{number_format($employee->get_brut_salary($periode->id), 0 ,'.',' ')}} FCFA</strong></h6>
                            </div>
                            <div>
                                <h6 class="fw-medium mb-2" style="color: #fff;">Salaire Brut Imposable :  <strong style="background-color:#ddcd08; color: #000;">{{number_format($employee->get_salary_imposable($periode->id), 0 ,'.',' ')}} FCFA</strong></h6>
                            </div>
                        </div>
                    </div>
                    <table class="table-sm table-bordered" style="color: #fff;" width="100%">
                        <tbody>
                            <tr>
                                <td align="left" width="50%"><strong>Elements</strong></td>
                                <td colspan="2" align="center" width="25%"><strong>Montant</strong></td>
                                <td align="center" width="25%"><strong>Ventilation</strong></td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td align="left" width="50%" style="border-bottom-color:#1c232f;"><strong>Salaire brut</strong></td>
                                <td colspan="2" align="center" width="25%" style="border-bottom-color:#1c232f;"><strong><br></strong></td>
                                <td align="left" width="25%" style="border-bottom-color:#1c232f;"><strong>Base 10%</strong></td>
                            </tr>
                            <!-- Ligne pour le Salaire de base -->
                            <tr style="border-bottom-color: #1c232f">
                                <td>Salaire de base</td>
                                <td style="text-align: right; border-right-color: #1c232f">
                                    <b class="m-0">
                                        {{number_format($salaryBase, 0 , '.' , ' ')}}
                                    </b>
                                </td>
                                <td></td>
                                <td style="text-align: right;">
                                    <b class="m-0">
                                        {{number_format($salaryBase * 0.1, 0 , '.' , ' ')}}
                                    </b>
                                </td>
                            </tr>
                            <!-- Ligne pour chaque prime -->
                            @foreach ($employee->allowances()->where('periode_id', $periode->id)->get() as $allowance)
                            <tr style="border-bottom-color: #1c232f">
                                <td>{{$allowance->title}}</td>
                                <td style="text-align: right;border-right-color: #1c232f">
                                    <b class="m-0">{{ number_format($allowance->amount, 0 , '.' , ' ') }}</b>
                                </td>
                                <td></td>
                                <td style="text-align: right;">
                                    @if(strpos($allowance->trait_fisc, 'exo 100%') === 0)
                                        <b> </b>
                                    @else
                                        <b class="m-0">{{ number_format($allowance->amount, 0 , '.' , ' ') }}</b>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            <!-- Ligne pour le Total Brut -->
                            <tr>
                                <td><b style="color: #ddcd08;">Total Brut (A)</b></td>
                                <td style="text-align: right;border-right-color: #1c232f">
                                    @php
                                        $total_brut = $salaryBase + $employee->allowances()->where('periode_id', $periode->id)->get()->sum('amount');
                                    @endphp
                                    <b class="m-0" style="color: #ddcd08;">{{ number_format($total_brut, 0 , '.' , ' ') }}</b>
                                </td>
                                <td></td>
                                <td style="text-align: right;">
                                    @php
                                        $total_brut_base_dix = $total_brut - $employeeBaseDix;
                                    @endphp
                                    <b class="m-0" style="color: #ddcd08;">{{ number_format($total_brut_base_dix, 0 , '.' , ' ') }}</b>
                                </td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td><b class="m-0">Avantages en Nature ou en Argent</b></td>
                                <td style="border-right-color: #1c232f;"><b class="m-0" align="left">Montant réel</b></td>
                                <td></td>
                                <td><b class="m-0" align="left">Montant au barème</b></td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td>Montant des avantages au barème</td>
                                <td style="border-right-color: #1c232f;" align="right">{{number_format($real, 0 ,'.',' ')}}</td>
                                <td></td>
                                <td align="right">{{number_format($bareme, 0 ,'.',' ')}}</td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td>Montant des autres avantages</td>
                                <td style="border-right-color: #1c232f;" align="right">{{number_format($avtg_reat_arg, 0 ,'.',' ')}}</td>
                                <td></td>
                                <td align="right">{{number_format($avtg_reat_arg2, 0 ,'.',' ')}}</td>
                            </tr>
                            <tr>
                                <td><b>Total des avantages</b></td>
                                <td style="border-right-color: #1c232f;" align="right"><b>{{number_format(($avtg_reat), 0 ,'.',' ')}}</b></td>
                                <td></td>
                                <td align="right"><b>(B) {{number_format(($avtg_bareme + $avtg_reat_arg2), 0 ,'.',' ')}}</b></td>
                            </tr>
                            <tr>
                                <td align="left"><b class="m-0" style="color: #ddcd08;">Total Brut avec avantages</b></td>
                                <td style="border-right-color: #1c232f" align="right"><b class="m-0" style="color: #ddcd08;">{{ number_format($total_brut + ($real + $avtg_reat_arg2), 0 , '.' , ' ') }} </b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td><b class="m-0">Exonérations</b></td>
                                <td style="text-align: right;border-right-color: #1c232f"></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                                $mont_cant=0;
                                foreach ($employee->allowances()->where('periode_id', $periode->id)->get() as $allowance){
                                    if($allowance->trait_fisc =='' || str_contains($allowance->trait_fisc,'exo 10%')){
                                        if (str_contains($allowance->allowanceOption->name, 'Frais de restauration (Cantine)')){
                                            if ($allowance->amount > 30000){
                                                $mont_cant = $allowance->amount - 30000;
                                            }else{
                                                $mont_cant = 0;
                                            }
                                        }
                                        if (!str_contains($allowance->allowanceOption->name, 'Frais de restauration (Cantine)')){
                                            $totoezro = $totoezro + $allowance->amount;
                                        }
                                    }
                                }
                                $totoezro = $totoezro + $mont_cant;
                            ?>
                            @foreach ($employee->allowances()->where('periode_id', $periode->id)->get() as $allowance)
                                @if($allowance->trait_fisc =='' || str_contains($allowance->trait_fisc,'exo 10%'))
                                    @if (str_contains($allowance->allowanceOption->name, 'Frais de restauration (Cantine)'))
                                        <tr style="border-bottom-color: #1c232f">
                                            <td>
                                                {{  $allowance->title }}
                                            </td>
                                            <td style="border-right-color: #1c232f" align = "right">
                                                @if (str_contains($allowance->allowanceOption->name, 'Frais de restauration (Cantine)'))
                                                    @if ($allowance->amount > 30000)
                                                        {{number_format($allowance->amount-30000, 0 ,'.',' ')}}
                                                    @else
                                                        {{number_format(0, 0 ,'.',' ')}}
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @else
                                        <tr style="border-bottom-color: #1c232f">
                                            <td>
                                                {{  $allowance->title }}
                                            </td>
                                            <td style="border-right-color: #1c232f" align = "right">
                                                {{number_format($allowance->amount, 0 ,'.',' ')}}
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                            <?php

                                $exo = round((($employee->get_brut_salary()-($totalbase + $avtg_reat))*10)/100);

                                if($totoezro>$exo){
                                    $resltexo =	$exo;
                                }else{
                                    $resltexo =	$totoezro;
                                }
                            ?>
                            <tr style="border-bottom-color: #1c232f">
                                <td align="left">Total des primes à 10%</td>
                                <td style="border-right-color: #1c232f " align="right"><b class="m-0">{{number_format($totoezro, 0 ,'.',' ')}}</b></td>
                                <td align="left"><b class="m-0" style="color: #ddcd08;">{{number_format($resltexo, 0 ,'.',' ')}}</td>
                                <td></td>
                            </tr>

                            <tr style="border-bottom-color: #1c232f">
                                <td></td>
                                <td style="border-right-color: #1c232f "></td>
                                <td></td>
                                <td></td>
                            </tr>

                            @foreach ($employee->allowances()->where('periode_id', $periode->id)->get() as $allowance)
                                @if (!str_contains($allowance->allowanceOption->name, 'Frais de restauration (Cantine)'))
                                    @if(strpos($allowance->trait_fisc, 'exo 100%') === 0)
                                        <tr style="border-bottom-color: #1c232f">
                                            <td>
                                                {{  $allowance->title }}
                                            </td>
                                            <td style="border-right-color: #1c232f " align = "right">
                                                {{number_format($allowance->amount, 0 ,'.',' ')}}
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @if(strpos($allowance->trait_fisc, 'exo 100%') === 0)
                                        <tr style="border-bottom-color: #1c232f">
                                            <td>
                                                {{ $allowance->title }}
                                            </td>
                                            <td style="border-right-color: #1c232f " align="right">
                                                <b class="m-0">{{number_format($allowance->amount, 0 ,'.',' ')}}</b>
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @endforeach
                            <?php
                                foreach ($employee->allowances()->where('periode_id', $periode->id)->get() as $allowance){
                                    if(strpos($allowance->trait_fisc, 'exo 100%') === 0){
                                        if($allowance->allowance_option == 11 && $allowance->amount > 30000){
                                            $total = $total + (30000);
                                        }else{
                                            $total = $total + $allowance->amount;
                                        }
                                    }
                                }
                            ?>
                            <tr>
                                <td>Total des primes exonérés à 100% dans la limite légale</td>
                                <td style="border-right-color: #1c232f "></td>
                                <td align="left"><b class="m-0" style="color: #ddcd08;">{{number_format($total, 0 ,'.',' ')}}</b></td>
                                <td></td>
                            </tr>

                            <!-- Total des exonérations -->
                            <tr>
                                <td style="border-top-color: #1c232f;" align="left">
                                    <b class="m-0" style="color: #ddcd08;">Total des Exonérations (C)</b>
                                </td>
                                <td style="border-right-color: #1c232f !important; border-top-color: #1c232f;" align="left">

                                </td>
                                <td></td>
                                <td align="right">
                                    <b class="m-0" style="color: #ddcd08;">{{number_format(($resltexo + $total), 0 ,'.',' ')}}</b>
                                </td>
                            </tr>

                            <!-- Salaire brut imposable -->
                            <tr bgcolor="#fff">
                                <td colspan="3" style="border-left-width:2pt; border-right-style:solid;border-right-width:2pt;border-right-color: #1c232f;">
                                    <b class="m-0"  style="color: #000;">Salaire brut imposable (SBI) [A+B-C]</b>
                                </td>
                                <td style="border-left-width:2pt; border-right-style:solid;border-right-width:2pt;" align="right">
                                    <b class="m-0" style="color: #000;">{{number_format(($total_brut + $avtg_bareme + $avtg_reat_arg2)-($resltexo+$total), 0 ,'.',' ')}}</b>
                                    @php $total_sbi = ($total_brut + $avtg_bareme + $avtg_reat_arg2)-($resltexo+$total) @endphp
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="sociale" role="tabpanel" align="center">
            <div class="col-xl-12 col-md-12 col-12 mb-md-0 mb-4">
                <div  style="background-color:#1c232f; padding:15px;border-radius: 10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column m-sm-3 m-0">
                            <div class="mb-xl-0 mb-4">
                                <h6 class="fw-medium mb-2" style="color: #fff;">Salaire Brut Total :  <strong style="background-color:#ddcd08; color: #000;">{{number_format($employee->get_brut_salary($periode->id), 0 ,'.',' ')}} FCFA</strong></h6>
                            </div>
                            <div>
                                <h6 class="fw-medium mb-2" style="color: #fff;">Salaire Brut Social :  <strong style="background-color:#ddcd08; color: #000;">{{number_format($employee->get_salary_social($periode->id), 0 ,'.',' ')}} FCFA</strong></h6>
                            </div>
                        </div>
                    </div>
                    <table class="table-sm table-bordered" style="color: #fff;" width="100%">
                        <thead>
                            <tr>
                                <td align="left" width="50%"><strong>Elements</strong></td>
                                <td colspan="3" align="center" width="50%"><strong>Montant</strong></td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td align="left" width="50%" style="border-bottom-color:#1c232f;"><strong>Salaire brut</strong></td>
                                <td colspan="2" align="center" width="25%" style="border-bottom-color:#1c232f;"><strong><br></strong></td>
                                <td align="left" width="25%" style="border-bottom-color:#1c232f;"><strong></strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom-color: #1c232f">
                                <!-- Ligne pour le Salaire de base -->
                                <td>Salaire de base</td>
                                <td style="text-align: right; border-right-color: #1c232f">
                                    <b class="m-0">
                                        {{$salaryBase}}
                                    </b>
                                </td>
                                <td></td>
                                <td style="text-align: right;"></td>
                            </tr>
                            <!-- Ligne pour chaque prime -->
                            @foreach ($employee->allowances()->where('periode_id', $periode->id)->get() as $allowance)
                            <tr style="border-bottom-color: #1c232f">
                                <td>{{$allowance->title}}</td>
                                <td style="text-align: right;border-right-color: #1c232f">
                                    <b class="m-0">{{ number_format($allowance->amount, 0 , '.' , ' ') }}</b>
                                </td>
                                <td></td>
                                <td style="text-align: right;"></td>
                            </tr>
                            @endforeach
                            <!-- Ligne pour le Total Brut -->
                            <tr>
                                <td><b style="color: #ddcd08;">Total Brut (A)</b></td>
                                <td style="text-align: right;border-right-color: #1c232f">
                                    <b class="m-0" style="color: #ddcd08;">{{ number_format($total_brut, 0 , '.' , ' ') }}</b>
                                </td>
                                <td></td>
                                <td style="text-align: right;"></td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td><b class="m-0">Avantages en Nature ou en Argent</b></td>
                                <td style="border-right-color: #1c232f;"><b class="m-0" align="left">Montant réel</b></td>
                                <td></td>
                                <td><b class="m-0" align="left">Montant au barème</b></td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td>Montant des avantages au barème</td>
                                <td style="border-right-color: #1c232f;" align="right">{{number_format($real, 0 ,'.',' ')}}</td>
                                <td></td>
                                <td align="right">{{number_format($bareme, 0 ,'.',' ')}}</td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td>Montant des autres avantages</td>
                                <td style="border-right-color: #1c232f;" align="right">{{number_format($avtg_reat_arg, 0 ,'.',' ')}}</td>
                                <td></td>
                                <td align="right">{{number_format($avtg_reat_arg2, 0 ,'.',' ')}}</td>
                            </tr>
                            <tr>
                                <td><b>Total des avantages</b></td>
                                <td style="border-right-color: #1c232f;" align="right"><b>{{number_format(($avtg_reat), 0 ,'.',' ')}}</b></td>
                                <td></td>
                                <td align="right"><b>(B) {{number_format(($avtg_bareme + $avtg_reat_arg2), 0 ,'.',' ')}}</b></td>
                            </tr>
                            <tr>
                                <td align="left"><b class="m-0" style="color: #ddcd08;">Total Brut avec avantages</b></td>
                                <td style="border-right-color: #1c232f" align="right"><b class="m-0" style="color: #ddcd08;">{{ number_format($total_brut + ($avtg_bareme + $avtg_reat_arg2), 0 , '.' , ' ') }} </b></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="border-bottom-color: #1c232f">
                                <td><b class="m-0">Exonérations</td>
                                <td style="text-align: right;border-right-color: #1c232f"></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($employee->allowances()->where('periode_id', $periode->id)->get() as $allowance)
                                @if(strpos($allowance->trait_fisc, 'exo 100%') === 0)
                                    <tr style="border-bottom-color: #1c232f">
                                        <td>
                                            {{ $allowance->title}}
                                        </td>
                                        <td style="border-right-color: #1c232f " align="right">
                                            <b class="m-0">{{number_format($allowance->amount, 0 ,'.',' ')}}</b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            <?php
                                $total1=0;
                                foreach ($employee->allowances()->where('periode_id', $periode->id)->get() as $allowance){
                                    if(strpos($allowance->trait_fisc, 'exo 100%') === 0){
                                        if($allowance->allowance_option == 11 && $allowance->amount > 30000){
                                            $total1 = $total1 + (30000);
                                        }else{
                                            $total1 = $total1 + $allowance->amount;
                                        }
                                    }
                                }
                            ?>
                            <tr>
                                <td>Total des primes exonérés à 100% dans la limite légale</td>
                                <td style="border-right-color: #1c232f "></td>
                                <td align="left"><b class="m-0" style="color: #ddcd08;">{{number_format($total, 0 ,'.',' ')}}</b></td>
                                <td></td>
                            </tr>
                            <!-- Total des exonérations -->
                            <tr>
                                <td style="border-top-color: #1c232f;" align="left">
                                    <b class="m-0" style="color: #ddcd08;">Total des Exonérations (C)</b>
                                </td>
                                <td style="border-right-color: #1c232f !important; border-top-color: #1c232f;" align="left"></td>
                                <td></td>
                                <td align="right">
                                    <b class="m-0" style="color: #ddcd08;">{{number_format(($total1), 0 ,'.',' ')}}</b>
                                </td>
                            </tr>
                            <!-- Salaire brut social -->
                            <tr bgcolor="#fff">
                                <td colspan="3" style="border-left-width:2pt; border-right-style:solid;border-right-width:2pt;border-right-color: #1c232f;">
                                    <b class="m-0"  style="color: #000;">Salaire brut imposable (SBI) [A+B-C]</b>
                                </td>
                                <td style="border-left-width:2pt; border-right-style:solid;border-right-width:2pt;" align="right">
                                    <b class="m-0" style="color: #000;">{{number_format(($total_brut + ($avtg_bareme + $avtg_reat_arg2)) - ($total1), 0 ,'.',' ')}}</b>
                                    @php $total_sbs = ($total_brut + ($avtg_bareme + $avtg_reat_arg2)) - ($total1) @endphp
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @php
            //calculons les charges des employés
            $cmu = $employee->cmu;
            $resultricf = 0;
            $nbre_jours = 0;
            if($cmu < 7){
                $coticmu = ($cmu*500);
                $coticmuemp = ($cmu*500);
            }else{
                $coticmu = 3000+round(($cmu-6)*1000);
                $coticmuemp = 3000;
            }
            $cnps = round($total_sbs*6.3)/100;
            $cnpsemp = round(($total_sbs*7.7)/100);

            $resultcmu =$coticmu;
            $resultcnps = $cnps;
            $resultimpricf = 0;
            $tauxact = 0;
            $act = $company->accident_taux;
            $prt = 0;
            $local = $employee->charge_expat;
            if($act=='0,03'){
                $tauxact= 75000*0.03;
                $prt = 100*0.03;
            }else if($act=='0,02'){
                $tauxact= 75000*0.02;
                $prt = 100*0.02;
            } else if($act=='0,04'){
                $tauxact= 75000*0.04;
                $prt = 100*0.04;
            } else if($act=='0,05'){
                $tauxact= 75000*0.05;
                $prt = 100*0.05;
            }

            $pf=0;
            $pf=((75000*5.75)/100);
            $tt_cnpsemp = $cnpsemp+$tauxact+((75000*5.75)/100)+$pf;

            $tax1 = ($total_sbi*1.2)/100;
            $tax2 = ($total_sbi*9.2)/100;
            $tax3 = ($total_sbi*1.6)/100;
            $tax4 = ($total_sbi*0.4)/100;
            $tax5 = ($total_sbi*1.2)/100;
            $resultcmu = round($coticmu);
            $resultcnps = round($cnps);
            $resulttax1 = round($tax1);
            $resulttax2 = round($tax2);
            $resulttax3 = round($tax3);
            if($local == 'local'){
                $parpatronal = $tt_cnpsemp+$coticmuemp+$tax1+$tax3;
            }else{
                $parpatronal = $tt_cnpsemp+$coticmuemp+$tax1+$tax2+$tax3;
            }

            $cnps = round($total_sbs*6.3)/100;
            $cnpsemp = round(($total_sbs*7.7)/100);

            $p = (float) str_replace(',', '.', $employee->parts);
            $fixedTable = [
                "1"     => 0,
                "1.5"   => 5500,
                "2"     => 11000,
                "2.5"   => 16500,
                "3"     => 22000,
                "3.5"   => 27500,
                "4"     => 33000,
                "4.5"   => 38500,
                "5"     => 44000,
            ];
            $valeurFixe = 0;
            foreach ($fixedTable as $partsKey => $amount) {
                if (abs($p - (float)$partsKey) < 0.01) {
                    $valeurFixe = $amount;
                    break;
                }
            }
            if ($p > 5) {
                $valeurFixe = 44000;
            }

            // --- CALCUL LEGAL DE L'IMPOT (ALIGNÉ SUR SALARYSERVICE) ---
            // 1. Détermination de la base imposable réelle (Réforme 2024 sans abattement de 20%)
            $base_imposable_reelle = $total_sbi;
            if ($base_imposable_reelle < 0) $base_imposable_reelle = 0;

            // 2. Gestion de la proratisation des jours travaillés
            $j = intval($employee->tax_payer_id);
            $nbre_jours = ($j == 28 || $j == 29 || $j == 31 || $j == 0) ? 30 : $j;

            // Mise à l'échelle mensuelle 30j pour application du barème
            $base_mensuelle = ($nbre_jours >= 30) ? $base_imposable_reelle : ($base_imposable_reelle * 30 / $nbre_jours);

            // 3. Application du Barème Unique Officiel 2024 sur la base mensuelle
            if ($base_mensuelle <= 75000) {
                $irs_mensuel = 0;
            } else if ($base_mensuelle <= 240000) {
                $irs_mensuel = ($base_mensuelle - 75000) * 0.16;
            } else if ($base_mensuelle <= 800000) {
                $irs_mensuel = (165000 * 0.16) + (($base_mensuelle - 240000) * 0.21);
            } else if ($base_mensuelle <= 2400000) {
                $irs_mensuel = (165000 * 0.16) + (560000 * 0.21) + (($base_mensuelle - 800000) * 0.24);
            } else if ($base_mensuelle <= 8000000) {
                $irs_mensuel = (165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (($base_mensuelle - 2400000) * 0.28);
            } else {
                $irs_mensuel = (165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (5600000 * 0.28) + (($base_mensuelle - 8000000) * 0.32);
            }

            // 4. Rétablissement au prorata réel des jours travaillés
            $resultimpricf = round(($nbre_jours >= 30) ? $irs_mensuel : ($irs_mensuel * $nbre_jours / 30));

            // 5. Application de la réduction RICF fixe 2023 (Proratisée si moins de 30 jours)
            if ($nbre_jours < 30 && $nbre_jours > 0) {
                $resultricf = round(($valeurFixe / 30) * $nbre_jours);
            } else {
                $resultricf = $valeurFixe;
            }

            // 6. Impôt net et retenues
            $impots = max(0, $resultimpricf - $resultricf);
            $retenue2 = $impots;
            $totalimpots = $impots + $resultcnps + $resultcmu;
        @endphp

        <div class="tab-pane fade" id="employes" role="tabpanel">
           <div class="row">
                <div class="col-xl-12" style="color: #fff; padding:5px">
                    <div class="card" style="padding:10px; background-color:#1c232f;">
                        <table class="table-sm table-bordered" style="color: #fff;">
                            <tr>
                                <td><strong>ITS </strong></td>
                                <td align="right" width="25%">
                                    <strong>SBI : {{number_format($total_sbi,0 , '.' , ' ')}} </strong>
                                </td>
                            </tr>
                            <tr>
                                <td>Impôts brut : </td>
                                <td align="right" width="25%">
                                    <strong>{{ number_format($resultimpricf, 0 , '.' , ' ') }} </strong>
                                    <input type="text" name="item_brut1" value="1" hidden>
                                    <input type="text" name="libelle1" value="Impôts bruts avant RICF" hidden>
                                    <input type="text" name="code1" value="401" hidden>
                                    <input type="text" name="ordre1" value="1" hidden>
                                    <input type="text" name="salariale1" value="1" hidden>
                                    <input type="text" name="patronale1" value="0" hidden>
                                    <input type="text" name="base1" value="{{$total_sbi}}" hidden>
                                    <input type="text" name="taux1" value="" hidden>
                                    <input type="text" name="amount1" value="{{$resultimpricf}}" hidden>
                                    <input type="text" name="jours_work1" value="" hidden>
                                </td>
                            </tr>
                            <tr>
                                <td>RICF ({{ $employee->parts }} parts) : </td>
                                <td align="right" width="25%">
                                    <strong>{{ number_format($resultricf, 0 , '.' , ' ') }} </strong>
                                    <input type="text" name="item_brut2" value="2" hidden>
                                    <input type="text" name="libelle2" value="Réduction pour Charges de Famille" hidden>
                                    <input type="text" name="code2" value="402" hidden>
                                    <input type="text" name="ordre2" value="2" hidden>
                                    <input type="text" name="salariale2" value="1" hidden>
                                    <input type="text" name="patronale2" value="0" hidden>
                                    <input type="text" name="base2" value="{{$total_sbi}}" hidden>
                                    <input type="text" name="taux2" value="" hidden>
                                    <input type="text" name="amount2" value="{{$resultricf}}" hidden>
                                    <input type="text" name="jours_work2" value="{{$employee->parts}}" hidden>
                                </td>
                            </tr>
                            <tr>
                                <td>Impôts Net :</td>
                                <td align="right" width="25%">
                                    <strong>{{ number_format($impots, 0 , '.' , ' ') }}</strong>
                                    <input type="text" name="item_brut3" value="3" hidden>
                                    <input type="text" name="libelle3" value="Impôts Nets" hidden>
                                    <input type="text" name="code3" value="403" hidden>
                                    <input type="text" name="ordre3" value="3" hidden>
                                    <input type="text" name="salariale3" value="1" hidden>
                                    <input type="text" name="patronale3" value="0" hidden>
                                    <input type="text" name="base3" value="{{$total_sbi}}" hidden>
                                    <input type="text" name="taux3" value="" hidden>
                                    <input type="text" name="amount3" value="{{$impots}}" hidden>
                                    <input type="text" name="jours_work3" value="" hidden>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-xl-12" style="color: #fff; padding:5px;">
                    <div class="card" style="padding:10px; background-color:#1c232f;">
                        <table class="table-sm table-bordered" style="color: #fff;">
                            <tr>
                                <td><strong>CNPS</strong></td>
                                <td align="right" width="25%">
                                    <strong>SBS : {{number_format($total_sbs,0 , '.' , ' ')}}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>Cotisation Retraite : </td>
                                <td align="right" width="25%">
                                    <strong>{{number_format($resultcnps, 0 , '.' , ' ') }} </strong>
                                    <input type="text" name="item_brut4" value="4" hidden>
                                    <input type="text" name="libelle4" value="Cotisation Retraite CNPS" hidden>
                                    <input type="text" name="code4" value="301" hidden>
                                    <input type="text" name="ordre4" value="4" hidden>
                                    <input type="text" name="salariale4" value="7,70" hidden>
                                    <input type="text" name="patronale4" value="{{$cnpsemp}}" hidden>
                                    <input type="text" name="base4" value="{{$total_sbs}}" hidden>
                                    <input type="text" name="taux4" value="6,3" hidden>
                                    <input type="text" name="amount4" value="{{$resultcnps}}" hidden>
                                    <input type="text" name="jours_work4" value="" hidden>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-xl-12" style="color: #fff; padding:5px">
                    <div class="card" style="padding:10px; background-color:#1c232f;">
                        <table class="table-sm table-bordered" style="color: #fff;">
                            <tr><td colspan = "2"><strong>CMU</strong></td></tr>
                            <tr>
                                <td>Nombre de bénéficiaires : </td>
                                <td align="right" width="25%">
                                    <strong> {{ ($cmu)}} </strong>
                                </td>
                            </tr>
                            <?php if( $cmu < '7'){ ?>
                                <tr>
                                    <td>Limité à 6 bénéficiaires : </td>
                                    <td align="right" width="25%">
                                        <strong>{{ (6 - $cmu)}} Restants</strong>
                                        <input type="text" name="cmu" value="{{(6 - $cmu)}}" hidden>
                                    </td>
                                </tr>
                            <?php }else{ ?>
                                <tr>
                                    <td colspan = "2">Limite de bénéficiaires atteint : <strong> 6 </strong></td>
                                </tr>
                                <tr>
                                    <td colspan = "2">La part de l'employé passe de 500 à 1000 FCFA par bénéficiaires pour <strong>{{($cmu - 6)}}</strong> bénéficiaire(s)</td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td>Part Salarié :</td>
                                <td align="right" width="25%">
                                    <strong>{{ number_format($resultcmu, 0 , '.' , ' ')}}</strong>
                                    <input type="text" name="item_brut5" value="5" hidden>
                                    <input type="text" name="libelle5" value="Couverture Maladie Universelle" hidden>
                                    <input type="text" name="code5" value="302" hidden>
                                    <input type="text" name="ordre5" value="5" hidden>
                                    <input type="text" name="salariale5" value="0,50" hidden>
                                    <input type="text" name="patronale5" value="{{$coticmuemp}}" hidden>
                                    <input type="text" name="jours_work5" value="{{$employee->cmu}}" hidden>
                                    <input type="text" name="base5" value="1000" hidden>
                                    <input type="text" name="taux5" value="0,50" hidden>
                                    <input type="text" name="amount5" value="{{$resultcmu}}" hidden>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                @if ($total_cantine>0)
                <div class="col-xl-12" style="color: #fff; padding:5px">
                    <div class="card" style="padding:10px; background-color:#1c232f;">
                        <table class="table-sm table-bordered" style="color: #fff;">
                            <tr>
                                <td><strong>CANTINE</strong></td>
                                <td align="right" width="25%">
                                    <strong>{{number_format($total_cantine, 0 , '.' , ' ') }} </strong>
                                    <input type="text" name="total_cantine" value="{{$total_cantine}}" hidden>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                @endif
                <hr/>
                <div class="project-amnt pt-3" align="left">Total Retenues : <b style="background-color: #ddcd08; color:black;">{{ number_format($totalimpots+$total_cantine, 0 , '.' , ' ') }} FCFA </b></div>
                <div class="project-amnt pt-3" align="right">Estimation du Net : <b style="background-color: #ddcd08; color:black;">{{ number_format($employee->get_net_salary($periode->id), 0 , '.' , ' ') }} FCFA </b></div>
            </div>
        </div>

        <div class="tab-pane fade" id="employeurs" role="tabpanel">
            <div class="row">
                <div class="col-xl-12" style="color: #fff; padding:5px">
                    <div class="card" style="padding:10px; background-color:#1c232f;">
                        <table class="table-sm table-bordered" style="color: #fff;">
                            <tr>
                                <td><strong>Impôts sur Salaire</strong></td>
                                <td align="right" width="25%">
                                    <strong> SBI : {{number_format($total_sbi,0 , '.' , ' ')}} </strong>
                                    <input type="text" name="imp_sal_patron" value="{{$total_sbi}}" hidden>
                                </td>
                            </tr>
                            <tr>
                                <td>Contribution Employeur (CNE) : </td>
                                <td align="right">
                                    <strong>{{ number_format($resulttax1, 0 , '.' , ' ') }} </strong>
                                    <input type="text" name="item_brut6" value="6" hidden>
                                    <input type="text" name="libelle6" value="Contribution Employeur" hidden>
                                    <input type="text" name="code6" value="409" hidden>
                                    <input type="text" name="ordre6" value="6" hidden>
                                    <input type="text" name="salariale6" value="0" hidden>
                                    <input type="text" name="patronale6" value="1" hidden>
                                    <input type="text" name="base6" value="{{$total_sbs}}" hidden>
                                    <input type="text" name="taux6" value="1,20" hidden>
                                    <input type="text" name="amount6" value="{{$resulttax1}}" hidden>
                                    <input type="text" name="jours_work6" value="" hidden>
                                </td>
                            </tr>
                            @if($local=='local')
                                <tr>
                                    <td>Contribution employeur (Expatrié) : </td>
                                    <td align="right"><strong>  0 </strong></td>
                                    <input type="text" name="item_brut7" value="7" hidden>
                                    <input type="text" name="libelle7" value="Contribution employeur (Expatrié)" hidden>
                                    <input type="text" name="code7" value="410" hidden>
                                    <input type="text" name="ordre7" value="7" hidden>
                                    <input type="text" name="salariale7" value="0" hidden>
                                    <input type="text" name="patronale7" value="1" hidden>
                                    <input type="text" name="base7" value="0" hidden>
                                    <input type="text" name="taux7" value="9,20" hidden>
                                    <input type="text" name="amount7" value="" hidden>
                                    <input type="text" name="jours_work7" value="" hidden>
                                </tr>
                            @else
                                <tr>
                                    <td>Contribution employeur (Expatrié) : </td>
                                    <td align="right">
                                        <strong>{{ number_format($resulttax2, 0 , '.' , ' ') }} </strong>
                                        <input type="text" name="item_brut7" value="7" hidden>
                                        <input type="text" name="libelle7" value="Contribution employeur (Expatrié)" hidden>
                                        <input type="text" name="code7" value="410" hidden>                                        
                                        <input type="text" name="ordre7" value="7" hidden>
                                        <input type="text" name="salariale7" value="0" hidden>
                                        <input type="text" name="patronale7" value="1" hidden>
                                        <input type="text" name="base7" value="{{$total_sbs}}" hidden>
                                        <input type="text" name="taux7" value="9,20" hidden>
                                        <input type="text" name="amount7" value="{{$resulttax2}}" hidden>
                                        <input type="text" name="jours_work7" value="" hidden>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td>Taxe d’Apprentissage :<br/>Taxe à la F. P. C. :  </td>
                                <td align="right">
                                    <strong>{{round($tax4)}}<br/>{{round($tax5)}} </strong>
                                    <input type="text" name="item_brut8" value="8" hidden>
                                    <input type="text" name="libelle8" value="Taxe d’Apprentissage" hidden>
                                    <input type="text" name="code8" value="411" hidden>
                                    <input type="text" name="ordre8" value="8" hidden>
                                    <input type="text" name="salariale8" value="0" hidden>
                                    <input type="text" name="patronale8" value="1" hidden>
                                    <input type="text" name="base8" value="{{$total_sbs}}" hidden>
                                    <input type="text" name="taux8" value="0,40" hidden>
                                    <input type="text" name="amount8" value="{{round($tax4)}}" hidden>
                                    <input type="text" name="jours_work8" value="" hidden>

                                    <input type="text" name="item_brut9" value="9" hidden>
                                    <input type="text" name="libelle9" value="Taxe.F.P.C" hidden>
                                    <input type="text" name="code9" value="412" hidden>                                    
                                    <input type="text" name="ordre9" value="9" hidden>
                                    <input type="text" name="salariale9" value="0" hidden>
                                    <input type="text" name="patronale9" value="1" hidden>
                                    <input type="text" name="base9" value="{{$total_sbs}}" hidden>
                                    <input type="text" name="taux9" value="1,20" hidden>
                                    <input type="text" name="amount9" value="{{round($tax5)}}" hidden>
                                    <input type="text" name="jours_work9" value="" hidden>
                                </td>
                            </tr>
                            <tr>
                                <td>TOTAL FDFP : </td>
                                <td align="right">
                                    <strong>{{ number_format($resulttax3, 0 , '.' , ' ')}} </strong>
                                    <input type="text" name="item_brut10" value="10" hidden>
                                    <input type="text" name="libelle10" value="TOTAL FDFP" hidden>
                                    <input type="text" name="code10" value="" hidden>
                                    <input type="text" name="ordre10" value="10" hidden>
                                    <input type="text" name="salariale10" value="0" hidden>
                                    <input type="text" name="patronale10" value="1" hidden>
                                    <input type="text" name="base10" value="{{$total_sbs}}" hidden>
                                    <input type="text" name="taux10" value="" hidden>
                                    <input type="text" name="amount10" value="{{round($resulttax3)}}" hidden>
                                    <input type="text" name="jours_work10" value="" hidden>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-xl-12" style="color: #fff; padding:5px">
                    <div class="card" style="padding:10px; background-color:#1c232f;">
                        <table class="table-sm table-bordered" style="color: #fff;">
                            <tr>
                                <td><strong>CNPS </strong></td>
                                <td align="right" width="25%">
                                    <strong> SBS : {{number_format($total_sbs,0 , '.' , ' ')}} </strong>
                                    <input type="text" name="total_sbs_patron" value="{{$total_sbs}}" hidden>
                                </td>
                            </tr>
                            <tr>
                                <td>Cotisation retraite employeur : </td>
                                <td align="right">
                                    <strong>{{ number_format($cnpsemp, 0 , '.' , ' ')}} </strong>
                                    <input type="text" name="item_brut13" value="13" hidden>
                                    <input type="text" name="libelle13" value="Cotisation retraite employeur" hidden>
                                    <input type="text" name="code13" value="308" hidden>
                                    <input type="text" name="ordre13" value="13" hidden>
                                    <input type="text" name="salariale13" value="0" hidden>
                                    <input type="text" name="patronale13" value="1" hidden>
                                    <input type="text" name="base13" value="{{$total_sbs}}" hidden>
                                    <input type="text" name="taux13" value="7,70" hidden>
                                    <input type="text" name="amount13" value="{{$cnpsemp}}" hidden>
                                    <input type="text" name="jours_work13" value="" hidden>
                                </td>
                            </tr>
                            <tr>
                                <td>Accident de travail (<b>{{$prt}}%</b>) : </td>
                                <td align="right">
                                    <strong>{{number_format($tauxact, 0 , '.' , ' ') }} </strong>
                                    <input type="text" name="item_brut11" value="11" hidden>
                                    <input type="text" name="libelle11" value="Accident de travail" hidden>
                                    <input type="text" name="code11" value="305" hidden>
                                    <input type="text" name="ordre11" value="11" hidden>
                                    <input type="text" name="salariale11" value="0" hidden>
                                    <input type="text" name="patronale11" value="1" hidden>
                                    <input type="text" name="base11" value="75000" hidden>
                                    <input type="text" name="taux11" value="{{$prt}},00" hidden>
                                    <input type="text" name="amount11" value="{{round($tauxact)}}" hidden>
                                    <input type="text" name="jours_work11" value="" hidden>
                                </td>
                            </tr>
                            <tr>
                                <td>Prestation Familiale : </td>
                                <td align="right">
                                    <strong>{{number_format($pf, 0 , '.' , ' ') }} </strong>
                                    <input type="text" name="item_brut12" value="12" hidden>
                                    <input type="text" name="libelle12" value="Prestation Familiale" hidden>
                                    <input type="text" name="code12" value="306" hidden>
                                    <input type="text" name="ordre12" value="12" hidden>
                                    <input type="text" name="salariale12" value="0" hidden>
                                    <input type="text" name="patronale12" value="1" hidden>
                                    <input type="text" name="base12" value="75000" hidden>
                                    <input type="text" name="taux12" value="5,75" hidden>
                                    <input type="text" name="amount12" value="{{round($pf)}}" hidden>
                                    <input type="text" name="jours_work12" value="" hidden>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-xl-12" style="color: #fff; padding:5px">
                    <div class="card" style="padding:10px; background-color:#1c232f;">
                        <table class="table-sm table-bordered" style="color: #fff;">
                            <tr>
                                <td colspan = 2><strong>CMU</strong></td>
                            </tr>
                            <tr>
                                <td>Part employeur : </td>
                                <td align="right" width="25%">
                                    <strong>{{ number_format($coticmuemp, 0 , '.' , ' ')}} </strong>
                                    <input type="text" name="coticmuemp_patron" value="{{$coticmuemp}}" hidden>
                                    <input type="text" name="item_brut14" value="14" hidden>
                                    <input type="text" name="libelle14" value="Couverture Maladie Universelle Employeur" hidden>
                                    <input type="text" name="code14" value="307" hidden>
                                    <input type="text" name="ordre14" value="14" hidden>
                                    <input type="text" name="salariale14" value="0" hidden>
                                    <input type="text" name="patronale14" value="1" hidden>
                                    <input type="text" name="jours_work14" value="{{$employee->cmu}}" hidden>
                                    <input type="text" name="base14" value="1000" hidden>
                                    <input type="text" name="taux14" value="0,50" hidden>
                                    <input type="text" name="amount14" value="{{$coticmuemp}}" hidden>
                                </td>
                            </tr>
                            <?php if( $cmu < '7'){ ?>
                                <tr>
                                    <td>Bénéficiaires à charges partagées Limités à 6 : </td>
                                    <td align="right">
                                        <strong>{{($cmu)}}</strong>
                                        <input type="text" name="cmu_patron" value="{{$cmu}}" hidden>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bénéficiaires restants: </td>
                                    <td align="right">
                                        <strong>{{(6 - $cmu)}}</strong>
                                        <input type="text" name="cmu_patron" value="{{(6 - $cmu)}}" hidden>
                                    </td>
                                </tr>
                            <?php }else{ ?>
                                <tr>
                                    <td>Limite de bénéficiaires atteint : </td>
                                    <td align="right"> <strong> 6 </strong> </td>
                                </tr>
                                <tr>
                                    <td>La part de l'employé passe de 500 à 1000 FCFA par bénéficiaires pour </td>
                                    <td align="right">
                                        <strong>{{($cmu - 6)}}</strong> bénéficiaire(s)
                                        <input type="text" name="cmu_patron" value="{{($cmu - 6)}}" hidden>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
            <div class="project-amnt pt-3" align="left">Total Retenues : <b style="background-color: #ddcd08; color:black;">{{ number_format(round($parpatronal), 0 , '.' , ' ') }} FCFA </b></div>
            <div class="project-amnt pt-3" align="right">Estimation du Net : <b style="background-color: #ddcd08; color:black;">{{ number_format($employee->get_net_salary($periode->id), 0 , '.' , ' ') }} FCFA </b></div>
        </div>
    </div>
</div>
  