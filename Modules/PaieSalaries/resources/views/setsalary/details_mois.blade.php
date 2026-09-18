@php
    use Carbon\Carbon;
    $plan = Utility::getChatGPTSettings();
    // Définir la locale en français
    setlocale(LC_TIME, 'fr_FR.utf8');
    $date_trait = Carbon::parse($monthpaie);
@endphp
<?php
    //$employees->martalstatu_id.' '.$employees->enfant.' '.$employees->personneinf.' '..' '.;
	$brut_total = $employee->get_brut_salary();
	$brut = $employee->salary;
	$brut_cnps = $employee->salary;
    $autreretenue = $employee->get_Autre_retenue();
	$autreretenuetype = $employee->get_Autre_retenue_type();
    $getleave = $employee->get_allo_conge();
	$nbre_jours = intval($employee->get_jours_work());
	$total_sbi = $employee->get_salary_imposable();
	//calculons les charges des employés
	$cmu = $employee->cmu;
	$resultricf = 0;
	if($cmu < 7){
		$coticmu = ($cmu*500);
		$coticmuemp = ($cmu*500);
	}else{
		$coticmu = 3000+round(($cmu-6)*1000);
		$coticmuemp = 3000;
	}

	$cnps = round($brut_cnps*6.3)/100;
    $cnpsemp = round(($brut_cnps*7.7)/100);
	//$tt_cnps = ($brut_cnps*6.3)/100;
	$resultcmu =$coticmu;
	$resultcnps = $cnps;
	$resultimpricf = 0;
    $tauxact = 0;
    $act = \App\Models\Utility::getValByName('company_state');
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

    //alert(tauxact);
    $tax1 = ($brut*1.2)/100;
    $tax2 = ($brut*9.2)/100;
    $tax3 = ($brut*1.6)/100;
	$tax4 = ($brut*0.4)/100;
	$tax5 = ($brut*1.2)/100;
    //tax4 = (brut*0.6)/100;
    $resultcmu = round($coticmu);
    $resultcnps = round($cnps);
    $resulttax1 = round($tax1);
    $resulttax2 = round($tax2);
    $resulttax3 = round($tax3);
    if($local == 'local'){
        $parpatronal = $tt_cnpsemp+$coticmuemp+$tax1+$tax3;//echo $local;
    }else{
        $parpatronal = $tt_cnpsemp+$coticmuemp+$tax1+$tax2+$tax3;//echo $local;
    }
    $brut_imposable_reelle = $brut;
	if($nbre_jours =='30'){
		if($total_sbi >= 0 && $total_sbi <= 75000){
			$tot = ($total_sbi*0)/100;
			$resultimpricf = round($tot);
		}else if($total_sbi > 75000 && $total_sbi <= 240000){
			$mt1 = $total_sbi-75000;
			$tot1 = (((75000*0)/100)+(($mt1*16)/100));
			$resultimpricf = round($tot1);
		}else if($total_sbi > 240000 && $total_sbi <= 800000){
			$mt2 = $total_sbi-240000;
			$tot2 = (((75000*0)/100)+((165000*16)/100)+(($mt2*21)/100));
			$resultimpricf = round($tot2);
		}else if($total_sbi > 800000 && $total_sbi <= 2400000){
			$mt3 = $total_sbi-800000;
			$tot3 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+(($mt3*24)/100));
			$resultimpricf = round($tot3);
		}else if($total_sbi > 2400000 && $total_sbi <= 8000000){
			$mt4 = $total_sbi-2400000;
			$tot4 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+(($mt4*28)/100));
			$resultimpricf = round($tot4);
		}else if($total_sbi > 8000000){
			$mt5 = $total_sbi-8000000;
			$tot5 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+((5600000*28)/100)+(($mt5*32)/100));
			$resultimpricf = round($tot5);
		}
		// Fix: Table 2023 certifiée
		$p = (float) str_replace(',', '.', $employee->parts);
		$fixedTable = [
			"1"   => 0, "1.5" => 5500, "2"   => 11000, "2.5" => 16500,
			"3"   => 22000, "3.5" => 27500, "4"   => 33000, "4.5" => 38500, "5"   => 44000
		];
		$valeurFixe = 0;
		foreach ($fixedTable as $partsKey => $amount) {
			if (abs($p - (float)$partsKey) < 0.01) { $valeurFixe = $amount; break; }
		}
		if ($p > 5) { $valeurFixe = 44000; }
		$resultricf = $valeurFixe;
		$impots = 0;

		$retenue2 = ($resultimpricf - $resultricf);

		if($retenue2>0){
			$totalimpots = ($resultimpricf - $resultricf) + $resultcnps + $resultcmu;
			$impots = $resultimpricf - $resultricf;
		}else{
			$totalimpots = $resultcnps + $resultcmu;
			$impots = 0;
		}
	}elseif($nbre_jours =='0' && $total_sbi > 0){
		if($total_sbi >= 0 && $total_sbi <= 75000){
			$tot = ($total_sbi*0)/100;
			$resultimpricf = round($tot);
		}else if($total_sbi > 75000 && $total_sbi <= 240000){
			$mt1 = $total_sbi-75000;
			$tot1 = (((75000*0)/100)+(($mt1*16)/100));
			$resultimpricf = round($tot1);
		}else if($total_sbi > 240000 && $total_sbi <= 800000){
			$mt2 = $total_sbi-240000;
			$tot2 = (((75000*0)/100)+((165000*16)/100)+(($mt2*21)/100));
			$resultimpricf = round($tot2);
		}else if($total_sbi > 800000 && $total_sbi <= 2400000){
			$mt3 = $total_sbi-800000;
			$tot3 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+(($mt3*24)/100));
			$resultimpricf = round($tot3);
		}else if($total_sbi > 2400000 && $total_sbi <= 8000000){
			$mt4 = $total_sbi-2400000;
			$tot4 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+(($mt4*28)/100));
			$resultimpricf = round($tot4);
		}else if($total_sbi > 8000000){
			$mt5 = $total_sbi-8000000;
			$tot5 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+((5600000*28)/100)+(($mt5*32)/100));
			$resultimpricf = round($tot5);
		}
		// Fix: Table 2023 certifiée
		$p = (float) str_replace(',', '.', $employee->parts);
		$fixedTable = [
			"1"   => 0, "1.5" => 5500, "2"   => 11000, "2.5" => 16500,
			"3"   => 22000, "3.5" => 27500, "4"   => 33000, "4.5" => 38500, "5"   => 44000
		];
		$valeurFixe = 0;
		foreach ($fixedTable as $partsKey => $amount) {
			if (abs($p - (float)$partsKey) < 0.01) { $valeurFixe = $amount; break; }
		}
		if ($p > 5) { $valeurFixe = 44000; }
		$resultricf = $valeurFixe;
		$impots = 0;

		$retenue2 = ($resultimpricf - $resultricf);

		if($retenue2>0){
			$totalimpots = ($resultimpricf - $resultricf) + $resultcnps + $resultcmu;
			$impots = $resultimpricf - $resultricf;
		}else{
			$totalimpots = $resultcnps + $resultcmu;
			$impots = 0;
		}
	}else{
		$day_salary = $total_sbi/30;
		if($day_salary >= 0 && $day_salary <= 2500){
			$tot = ($day_salary*0)/100;
			$resultimpricf = round($tot*$nbre_jours);
		}else if($day_salary > 2500 && $day_salary <= 8000){
			$mt1 = $day_salary-2500;
			$tot1 = (((2500*0)/100)+(($mt1*16)/100));
			$resultimpricf = round($tot1*$nbre_jours);
		}else if($day_salary > 8000 && $day_salary <= 26667){
			$mt2 = $day_salary-8000;
			$tot2 = (((2500*0)/100)+((5500*16)/100)+(($mt2*21)/100));
			$resultimpricf = round($tot2*$nbre_jours);
		}else if($day_salary > 26667 && $day_salary <= 80000){
			$mt3 = $day_salary-26667;
			$tot3 = (((2500*0)/100)+((5500*16)/100)+((18667*21)/100)+(($mt3*24)/100));
			$resultimpricf = round($tot3*$nbre_jours);
		}else if($day_salary > 80000 && $day_salary <= 266667){
			$mt4 = $day_salary-80000;
			$tot4 = (((2500*0)/100)+((5500*16)/100)+((18667*21)/100)+((53333*24)/100)+(($mt4*28)/100));
			$resultimpricf = round($tot4*$nbre_jours);
		}else if($day_salary > 266667){
			$mt5 = $day_salary-266667;
			$tot5 = (((2500*0)/100)+((5500*16)/100)+((18667*21)/100)+((53333*24)/100)+((186667*28)/100)+(($mt5*32)/100));
			$resultimpricf = round($tot5*$nbre_jours);
		}
		// Fix: Table 2023 certifiée avec proratisation
		$p = (float) str_replace(',', '.', $employee->parts);
		$fixedTable = [
			"1"   => 0, "1.5" => 5500, "2"   => 11000, "2.5" => 16500,
			"3"   => 22000, "3.5" => 27500, "4"   => 33000, "4.5" => 38500, "5"   => 44000
		];
		$valeurFixe = 0;
		foreach ($fixedTable as $partsKey => $amount) {
			if (abs($p - (float)$partsKey) < 0.01) { $valeurFixe = $amount; break; }
		}
		if ($p > 5) { $valeurFixe = 44000; }
		$resultricf = round(($valeurFixe / 30) * $nbre_jours);
		$impots = 0;

		$retenue2 = ($resultimpricf - $resultricf);

		if($retenue2>0){
			$totalimpots = ($resultimpricf - $resultricf) + $resultcnps + $resultcmu;
			$impots = $resultimpricf - $resultricf;
		}else{
			$totalimpots = $resultcnps + $resultcmu;
			$impots = 0;
		}
	}
    if ($employee->statut_emp == 'Stagiaire' || $employee->statut_emp == 'Apprenti') {
        $impots = 0;
        $resultcmu = 0; $resulttax1 = 0;
        $resultimpricf = 0;$pf=0; $tt_cnpsemp = 0; $cnpsemp=0; $tauxact=0;
        $resultricf = 0; $tax1=0; $tax2=0; $tax3=0; $tax4 = 0; $tax5 =0;
        $resultcnps  = 0; $parpatronal = 0; $tt_cnpsemp=0; $resulttax2 =0;
        $resultcmu = 0; $totalimpots = 0;
        $cnps= 0; $cnpsemp = 0; $coticmu=0; $coticmuemp=0;
    }
	$total_cantine = 0;
	foreach ($allowances as $allowance){
		if ($allowance->allowance_option == 31){
				$total_cantine = $allowance->amount;
		}
	}

	$date = new DateTime(date("d-m-Y"));//($payslip->salary_month);

	// Premier jour du mois
	$firstDay = $date->format('01-m-Y');
	//$heures = 0;
	// Dernier jour du mois
	$lastDay = $date->format('t-m-Y');
    $jours_work = $employee->tax_payer_id;
	$postevalue = '';
    $valueposte = '';

	$postevalue = $employee->sous_categorie;

	foreach ($categorie as $poste){
		if ($employee->categorie == $poste->id){
				$valueposte = $poste->title;
			break;
		}
	}

	if($employee->martalstatu_id == '1'){
		$situation = 'Célibataire';
	}else if($employee->martalstatu_id == '2'){
		$situation = 'Marié(e)';
	}else if($employee->martalstatu_id == '3'){
		$situation = 'Divorcé(e)';
	}else if($employee->martalstatu_id == '4'){
		$situation = 'Veuf(ve)';
	}

	$compte=0;
	if($payslipss->isEmpty()){
		$compte=1;
	}else{
		foreach ($payslipss as $payslip) {
			// Vous pouvez maintenant accéder à la propriété 'salary_month' en toute sécurité
			$salaryMonth = $payslip->salary_month;
			$compte++;
		}
	}
	foreach($secteurs as $secteur){
		if($secteur->id == \Utility::getValByName('secteur_activite')){
			$sect = $secteur->nom_secteur;
		}
	}

    $totoezro1=0;  $real = 0; $bareme = 0; $avtg_reat = 0; $avtg_bareme = 0; $avtg_reat_arg = 0; $avtg_bareme_arg = 0;
    foreach ($allowances as $allowance){
        //if($allowance->trait_fisc =='' || $allowance->trait_fisc =='0%'){
            $totoezro1 = $totoezro1 + $allowance->amount;
        //}
    }
    foreach ($avantages as $avtg){
        if($avtg->type_avantage == 1){
            $real += $avtg->montant_reel;
            $bareme += $avtg->amount;
        }else{
            $avtg_reat_arg += $avtg->montant_reel;
            $avtg_bareme_arg += $avtg->amount;
        }
        $avtg_reat += $avtg->montant_reel;
        $avtg_bareme += $avtg->amount;
    }
    // Anciennete basee sur la fin du mois de paie (ex: 2025-01 => fin = 31/01/2025)
    $date_embauche = new DateTime($employee->company_doj ?? $employee->start_date);
    $date_fin_mois = \Carbon\Carbon::parse($monthpaie)->endOfMonth()->toDateString();
    $date_ref_paie = new DateTime($date_fin_mois);
    $difference = $date_embauche->diff($date_ref_paie);
    $date_pa = intval($difference->format('%y')); // annees completes uniquement
    $date_m  = intval($difference->format('%m'));
    foreach($branche as $brh => $value){
      $city = $value;
    }
    $real2 = 0; $bareme2 = 0; $avtg_reat_arg2 = 0; $avtg_bareme_arg2 = 0; $avtg_reat2 = 0; $totoezro = 0; $avtg_bareme2 = 0; $mont_cant = 0; $mont_cant2 = 0; $totalbase = 0; $resltexo = 0; $total = 0;


    foreach ($avantages as $avtg){
        if($avtg->type_avantage == 1){
            $real2 += $avtg->montant_reel;
            $bareme2 += $avtg->amount;
        }else{
            $avtg_reat_arg2 += $avtg->montant_reel;
            $avtg_bareme_arg2 += $avtg->amount;
        }
        $avtg_reat2 += $avtg->montant_reel;
        $avtg_bareme2 += $avtg->amount;
    }

?>
<style>
    .montant {
        text-align: right;
    }
    .h2{
        animation-duration: .8s;
        animation-name: clignoter;
        animation-iteration-count: infinite;
        transition: none;
        color:#000;
    }
    @keyframes clignoter {
        0%   { color:#000000; }
        40%   {color:#F1A200; }
        100% { opacity:#000000; }
    }
</style>


@extends('layouts.admin')

@section('page-title')
    {{ __('Gestion des Mensuels') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Accueil') }}</a></li>
    <li class="breadcrumb-item">{{ __('Détails de l\'employé') }}</li>
@endsection

@section('content')
<div class="row">
    <input type="text" id="salarybase" name="salarybase" value="{{$employee->salary}}" hidden="">
    <input type="text" id="salarybase2" name="salarybase2" value="{{$employee->branch_location}}" hidden="">
    <input type="text" id="baraJours" name="baraJours" value="{{$employee->tax_payer_id}}" hidden="">
    <input type="text" id="dateancien" name="dateancien" value="{{ $date_pa }}" hidden="">
    <div class="col-md-12 d-flex mb-4">
        <div class="col-2 text-start">
            <a href="{{ route('payslip.payrolldays') }}" class="btn btn-warning">Liste des journaliers</a>
        </div>
        <div class="card bg-success text-white col-8" style="padding:10px;">
            <h5 class="mb-0" style="color:#fff;">{{ __('Mois de traitement : ') }} <strong>{{$date_trait->formatLocalized('%B %Y')}}</strong></h5>
        </div>
        <div class="col-2 text-end">
            <a href="{{ route('payslip.payslipmonths', ['monthpaie' => $monthpaie]) }}" class="btn btn-secondary">Allez à l'Etat de paie</a>
        </div>
    </div>
    @php
        // Vérifier que la collection des employés précédents n'est pas vide
        if ($employeeprevious->isNotEmpty()) {
            // Trouver l'index de l'employé actuel
            $currentEmployeeIndex2 = $employeeprevious->search(function ($emp) use ($employee) {
                return $emp->id === $employee->id;
            });
            // Vérifier que l'employé actuel a été trouvé
            if ($currentEmployeeIndex2 !== false) {
                // Déterminer l'index du précédent employé
                $previousEmployeeIndex = ($currentEmployeeIndex2 - 1 + $employeeprevious->count()) % $employeeprevious->count();
                $previousEmployee = $employeeprevious[$previousEmployeeIndex];
            } else {
                $previousEmployee = null; // L'employé actuel n'a pas été trouvé
            }
        } else {
            $previousEmployee = null; // La liste des employés précédents est vide
        }
    @endphp
    <div class="col-2 mb-4" align="left" >
        <div class="row" style="justify-content: end;">
            <div class="col-12">
                <a href="{{ route('setsalary.employee_details_mois', ['id' => $previousEmployee->id, 'monthpaie' => $monthpaie]) }}" class="btn btn-sm btn-info" style="justify-content: end;"><i class="ti ti-arrow-left"></i> {{ __('Employé Précédent') }}</a>
            </div>
        </div>
    </div>
    <!-- Colonne de gauche avec la liste des employés -->
    <div class="col-8 mb-4">
        @include('setsalary.menu_mois')
    </div>
    @php
        // Trouver l'index de l'employé actuel
        $currentEmployeeIndex = $employeenext->search(function ($emp) use ($employee) {
            return $emp->id === $employee->id;
        });

        // Déterminer l'index du prochain employé
        $nextEmployeeIndex = ($currentEmployeeIndex + 1) % $employeenext->count();
        $nextEmployee = $employeenext[$nextEmployeeIndex];
    @endphp
    <div class="col-2 mb-4" align="right">
        <div class="row" style="justify-content: end;">
            <div class="col-12">
                <a href="{{ route('setsalary.employee_details_mois', ['id' => $nextEmployee->id, 'monthpaie' => $monthpaie]) }}" class="btn btn-sm btn-info" style="justify-content: end;">{{ __('Employé Suivant') }} <i class="ti ti-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <input type="hidden" id="monthpaie" value="{{ $monthpaie }}">
    <input type="hidden" id="employee_id" value="{{ $employee->id }}">
        <div class="col-md-12">
            <div class="card mb-4" style="background-image: url('{{ asset('assets/img/pages/header.png') }}');">
                <div class="card-body d-flex">
                    <div class="col-md-4 d-flex align-items-start align-items-sm-center gap-4">
                        @php
                            $hasPhoto = false;
                        @endphp
                        @foreach ($avatar as $photo)
                            @if($photo->employee_id == $employee['employee_id'] && $photo->document_id == 3)
                                @php
                                    $hasPhoto = true;
                                @endphp
                                <img src="{{ asset(Storage::url('uploads/document')) . '/' .$photo->document_value }}" class="rounded-circle" style="width: 20%;">
                                @break
                            @endif
                        @endforeach
                        @if (!$hasPhoto)
                            <img src="{{ asset(Storage::url('uploads/avatar')) }}/avatar.png" class="rounded-circle" style="width:20%;">
                        @endif
                        <div class="button-wrapper">
                            <h4 class="mb-2" id="employee-name">{{ $employee->name }}</h4>
                            <p class="text-muted mb-0" id="employee-position">{{ !empty(\Auth::user()->getDesignation($employee['designation_id'])) ? \Auth::user()->getDesignation($employee['designation_id'])->name : '-' }}  -  {{ !empty(\Auth::user()->getDepartment($employee['department_id'])) ? \Auth::user()->getDepartment($employee['department_id'])->name : '-' }}<br>{{ !empty(\Auth::user()->getBranch($employee['branch_id'])) ? \Auth::user()->getBranch($employee['branch_id'])->name : '' }}</p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5 class="mb-3">Informations salariales</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <h6 class="card-title">Brut total :
                                <span class="card-text" id="salaire-brut">{{ number_format($employee->get_brut_salary(), 0, ',', ' ') }} FCFA</span></h6>
                                <h6 class="card-title">Salaire brut imposable :
                                <span class="card-text" id="salaire-imposable">{{ number_format($employee->get_salary_imposable(), 0, ',', ' ') }} FCFA</span></h6>
                            </div>
                            <div class="col-md-6 mb-2">
                                <h6 class="card-title">Salaire brut social :
                                <span class="card-text" id="salaire-social">{{ number_format($employee->get_salary_social(), 0, ',', ' ') }} FCFA</span></h6>
                                <h6 class="card-title">Salaire net :
                                <span class="card-text" id="salaire-net">{{ number_format($employee->get_net_salary() , 0, ',', ' ') }} FCFA</span></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="nav nav-pills flex-column flex-md-row mb-4">
                <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#basic"><i class="ti ti-calendar-off ti-xs me-1"></i> Absences</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#allowances"><i class="ti ti-clock-play ti-xs me-1"></i> Heures Supps</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#deductions"><i class="ti ti-cash ti-xs me-1"></i> Prêts</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#overtime"><i class="ti ti-gift ti-xs me-1"></i> Avantages en Nature</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#leave"><i class="ti ti-beach ti-xs me-1"></i> Congés</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#ruptures"><i class="ti ti-alert-triangle ti-xs me-1"></i> Sanction & Ruptures</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#salaire"><i class="ti ti-currency-dollar ti-xs me-1"></i> Salaire</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#retenues"><i class="ti ti-arrow-left-to-arc ti-xs me-1"></i> Retenues</a></li>
                <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#apercu"><i class="ti ti-file-text ti-xs me-1"></i> Aperçu du Bulletin</a></li>
            </ul>
            <div class="tab-content">
                <!-- Absence -->
                <div class="tab-pane fade show active table-responsive" id="basic" role="tabpanel">
                    <div class="d-flex mb-2" style="justify-content: end;">
                        <a href="#" data-bs-toggle="modal" data-url="{{ route('timesheet.create', ['monthpaie' => $monthpaie, 'id' => $employee->id]) }}"
                        data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                        data-title="{{ __('Create New Timesheet') }}" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                            <i class="ti ti-plus"></i>
                        </a>
                    </div>
                    <table class="table table-striped" style="font-size: 13px;">
                        <thead style="background-color: #000;">
                            <tr>
                                <th style="color: #fff;">{{ __('Date') }}</th>
                                <th style="color: #fff;">Nombre</th>
                                <th style="color: #fff;">Justifiée?</th>
                                <th style="color: #fff;">{{ __('Déductible?') }}</th>
                                <th style="color: #fff;">{{ __('Motifs') }}</th>
                                <th style="color: #fff;">{{ __('Statut') }}</th>
                                <th style="color: #fff;">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($timesheets->isNotEmpty())
                                @foreach ($timesheets as $timesheet)
                                    @php
                                        $startDate = new DateTime($timesheet->date);
                                            $formattedStartDate = $startDate->format('d/m/Y');
                                            $endDate = new DateTime($timesheet->arrival_date);
                                            $formattedEndDate = $endDate->format('d/m/Y');
                                            $url = route('timesheet.edit', ['monthpaie' => $monthpaie, 'id' => $timesheet->id]);
                                            $url2 = route('timesheet.destroy', $timesheet->id);
                                        @endphp
                                        <tr>
                                            <td> Départ : {{ $formattedStartDate }} <br> Arrivée : {{ $timesheet->arrival_date ? $formattedEndDate : '-' }}</td>
                                            <td align="center"><strong>{{ $timesheet->hours }} jours</strong></td>
                                            <td align="center"><strong>{{ $timesheet->motif_justify }}</strong></td>
                                            <td align="center">
                                                @if($timesheet->statut == 1)
                                                    <a class="btn btn-icon btn-sm btn-label-danger disabled" href="{{ URL::to('timesheet/destroyState') }}/{{ $monthpaie }}/{{ $timesheet->id }}">
                                                        <i class="fa fa-times-circle"></i>
                                                    </a>
                                                    <hr/>
                                                    <label class="form-check-label" for="toggleButton" id="status"><strong>Montant : {{ $timesheet->retenue }} FCFA</strong></label>
                                                @elseif($timesheet->deduc_abs == 1)
                                                    <a class="btn btn-icon btn-sm btn-label-danger" href="{{ URL::to('timesheet/destroyState') }}/{{ $monthpaie }}/{{ $timesheet->id }}">
                                                        <i class="fa fa-times-circle"></i>
                                                    </a>
                                                    <hr/>
                                                    <label class="form-check-label" for="toggleButton" id="status"><strong>Montant : {{ $timesheet->retenue }} FCFA</strong></label>
                                                @else
                                                    <a class="btn btn-icon btn-sm btn-label-success" href="{{ URL::to('timesheet/updateState') }}/{{ $monthpaie }}/{{ $timesheet->id }}">
                                                        <i class="fa fa-check-circle"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td align="center"><strong>{{ $timesheet->remark }}</strong></td>
                                            <td align="center">
                                                @if($timesheet->statut == 1)
                                                    <center><span class="status_badge badge p-2 px-3 rounded" style="background-color: #145388;">{{ __('Traité') }}</span></center>
                                                @else
                                                    <center><span class="status_badge badge p-2 px-3 rounded" style="background-color:red;">{{ __('Non Traité') }}</span></center>
                                                @endif
                                            </td>
                                            <td class="Action" align="center">
                                                @if($timesheet->statut == 1)
                                                    -
                                                @else
                                                    <div class="d-flex">
                                                        @can('Edit TimeSheet')
                                                            <div class="action-btn me-2">
                                                                <a href="#" class="btn btn-icon btn-sm btn-label-warning"
                                                                    data-url="{{ $url }}"
                                                                    data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                                    title="" data-title="{{ __('Edit Timesheet') }}"
                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                    <i class="ti ti-pencil"></i>
                                                                </a>
                                                            </div>
                                                        @endcan
                                                        @can('Delete TimeSheet')
                                                        <div class="action-btn">
                                                            <form method="POST" action="{{ $url2 }}" id="delete-form-{{ $timesheet->id }}">
                                                                @method('DELETE')
                                                                @csrf
                                                                <a href="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                    data-bs-original-title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                            </form>
                                                        </div>
                                                        @endcan
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="text-center">{{ __('Aucun enregistrement trouvé') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- heure sup -->
                <div class="tab-pane fade table-responsive" id="allowances" role="tabpanel">
                    <div class="d-flex mb-2" style="justify-content: end;">
                        @can('Create Overtime')
                            <div class="col-1 text-end">
                                <a data-url="{{ route('heuresup.create',['monthpaie'=>$monthpaie,'id'=> $employee->id]) }}" data-ajax-popup="true" data-title="{{ __('Create Overtime') }}" data-bs-toggle="tooltip"
                                title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                                    <i class="ti ti-plus" style="color: #fff;"></i>
                                </a>
                            </div>
                        @endcan
                    </div>
                    <table class="table table-striped" id="table-heursup" style="font-size: 13px;">
                        <thead style="background-color: #000;">
                            <tr>
                                <th style="color: #fff;"><center>Périodes</center></th>
                                <th style="color: #fff;"><center>Heure travaillée</center></th>
                                <th style="color: #fff;"><center>Taux horaire</center></th>
                                <th style="color: #fff;"><center>Gain</center></th>
                                <th style="color: #fff;"><center>{{ __('Statut') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Action') }}</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($heures_sup) && $heures_sup->isNotEmpty())
                                @foreach($heures_sup as $heuresup)
                                    @php
                                        $startDate = new DateTime($heuresup->start_date);
                                        $endDate = new DateTime($heuresup->end_date);
                                        $total = $heuresup->quar_heure + $heuresup->heure_audd + $heuresup->heure_nuit_ferie + $heuresup->heure_dim_ferie + $heuresup->heure_nuit_dim_ferie;
                                    @endphp
                                    <tr>
                                        <td align="center">
                                            {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                                        </td>
                                        <td align="center">
                                            De la 41è à la 46è heure : <strong>{{ $heuresup->quar_heure ?? '0' }}</strong><br>
                                            Au délà de la 46è heure : <strong>{{ $heuresup->heure_audd ?? '0' }}</strong><br>
                                            Jours ouvrés Nuit : <strong>{{ $heuresup->heure_nuit_ferie ?? '0' }}</strong><br>
                                            Dimanche et jours fériés : <strong>{{ $heuresup->heure_dim_ferie ?? '0' }}</strong><br>
                                            Nuit dimanche et jours fériés : <strong>{{ $heuresup->heure_nuit_dim_ferie ?? '0' }}</strong><hr>
                                            <strong>Total : {{ $total }}</strong>
                                        </td>
                                        <td align="center">{{ $heuresup->taux_hour }}</td>
                                        <td align="center">
                                            <strong>{{ $heuresup->montant }} FCFA</strong><hr>
                                            @if($heuresup->statut !== 1)
                                                <div class="action-btn bg-success ms-2">
                                                    <a class="btn btn-success" href="{{ route('heuresup.updateHours', ['monthpaie' => $monthpaie, 'id' => $heuresup->id]) }}" title="{{ __('Payé') }}">
                                                        <span>{{ __('Payable ce mois ?') }}</span>
                                                    </a>
                                                </div>
                                            @elseif($heuresup->statut == 1 && $heuresup->paid == 0)
                                                <div class="action-btn bg-warning ms-2">
                                                    <span class="status_badge badge p-2 px-3 rounded" style="background-color: orange;">{{ __('Paiement en cours') }}</span>
                                                </div>
                                            @elseif($heuresup->paid == 1)
                                                <div class="action-btn bg-success ms-2">
                                                    <span class="status_badge badge p-2 px-3 rounded" style="background-color: green;">{{ __('Payé') }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td align="center">
                                            @if($heuresup->statut == 1)
                                                <span class="status_badge badge p-2 px-3 rounded" style="background-color: #145388;">{{ __('Traité') }}</span>
                                            @else
                                                <span class="status_badge badge p-2 px-3 rounded" style="background-color:red;">{{ __('Non Traité') }}</span>
                                            @endif
                                        </td>
                                        <td class="action">
                                            @if($heuresup->paid != 1)
                                                <span>
                                                    @can('Edit Overtime')
                                                        <div class="action-btn me-2">
                                                            <a href="#" class="btn btn-icon btn-sm btn-label-warning"
                                                                data-url="{{ route('heuresup.edit', ['monthpaie' => $monthpaie, 'id' => $heuresup->id]) }}"
                                                                data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit Overtime') }}"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete Overtime')
                                                        <div class="action-btn">
                                                            <form method="POST" action="{{ route('heuresup.destroy', ['monthpaie' => $monthpaie, 'id' => $heuresup->id]) }}" id="delete-form-{{ $heuresup->id }}">
                                                                @method('DELETE')
                                                                @csrf
                                                                <a href="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para"
                                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                                    data-bs-original-title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                            </form>
                                                        </div>
                                                    @endcan
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">{{ __('Aucune heure supplémentaire trouvée') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- prets -->
                <div class="tab-pane fade table-responsive" id="deductions" role="tabpanel">
                    <div class="d-flex mb-2" style="justify-content: end;">
                        @can('Create Loan')
                            <div class="col-4 text-end m-2">
                                <a data-url="{{ route('loans.create',['monthpaie' => $monthpaie, 'id' => $employee->id]) }}" data-ajax-popup="true"
                                    data-title="{{ __('Create Loan') }}" data-bs-toggle="tooltip" title=""
                                    data-size="xl" class="btn btn-sm btn-primary"
                                    data-bs-original-title="{{ __('Create') }}">
                                    <i class="ti ti-plus" style="color:white;"></i><span style="color:white;">Ajouter un prêt</span>
                                </a>

                                <a data-url="{{ route('setsalary.create_autres_retenue',['monthpaie' => $monthpaie, 'id' => $employee->id]) }}" data-ajax-popup="true" data-size="xl" data-title="{{ __('Créer une retenue') }}" data-bs-toggle="tooltip" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus" style="color:white;"></i> <span style="color:white;">Ajouter une autre retenue</span>
                                </a>
                            </div>
                        @endcan
                    </div>
                    <hr>
                    <ul class="nav nav-pills flex-column flex-md-row mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" href="#" role="tab" aria-controls="nav-home" aria-selected="true">
                                <i class="ti ti-calendar-stats ti-xs me-1"></i>
                                <strong>Prêts en cours ce mois-ci</strong>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-recap-tab" data-bs-toggle="tab" data-bs-target="#nav-recap" href="#" role="tab" aria-controls="nav-recap" aria-selected="false">
                                <i class="ti ti-list ti-xs me-1"></i>
                                <strong>Liste des Prêts</strong>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="nav-autres-tab" data-bs-toggle="tab" data-bs-target="#nav-autres" href="#" role="tab" aria-controls="nav-autres" aria-selected="false">
                                <i class="ti ti-note ti-xs me-1"></i>
                                <strong>Autres Retenues</strong>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="card-datatable table-responsive pt-0">
                                <div class="row align-items-center" align="center">
                                    <marquee behavior="" direction=""><h6 class="h2"><b class="fw-bold">Si vous avez des prêts dont le remboursement a été effectué, n'oubliez pas de cliquer sur le bouton "Prêt remboursé" après avoir généré la paie du mois concerné.</b></h6></marquee>
                                </div>
                                <table class="table table-striped table-hover" style="font-size: 12px;">
                                    <thead style="background-color: #000; color: #fff; font-size: 11px;">
                                        <tr>
                                            <th width="35%" style="color: #fff;"><center>{{ __('Détails des prêts') }}</center></th>
                                            <th width="35%" style="color: #fff;"><center>{{ __('Total') }}</center></th>
                                            <th width="10%" style="color: #fff;"><center>{{ __(key: 'Applicable?') }}</center></th>
                                            <th width="10%" style="color: #fff;"><center>{{ __(key: 'Historique') }}</center></th>
                                            <th width="10%" style="color: #fff;"><center>{{ __('Actions') }}</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $groupedLoansThisMonth = $loansOngoingThisMonth->groupBy('employee_id');
                                        @endphp

                                        @if ($groupedLoansThisMonth->isEmpty())
                                            <tr>
                                                <td colspan="6"><center>{{ __('Aucune données') }}</center></td>
                                            </tr>
                                        @else
                                            @foreach ($groupedLoansThisMonth as $employeeId => $employeeLoans)
                                                @php
                                                    $firstLoan = $employeeLoans->first();
                                                    $totalAmount = $employeeLoans->sum('amount');
                                                    $totalAmountPaid = $employeeLoans->sum('amountpaie');
                                                    $totalAmountRemaining = $totalAmount - $totalAmountPaid;
                                                    $totalMonthlyDeduction = $employeeLoans->sum('amount_deduc');
                                                @endphp
                                                <tr>
                                                    <td>
                                                        @foreach ($employeeLoans as $loan)
                                                            <div class="mb-3 p-2 border rounded">
                                                                <strong>{{ $loan->title }}</strong><br>
                                                                Échéance: {{ \Auth::user()->dateFormat($loan->fin_date) }}<br>
                                                                À rembourser: {{ number_format($loan->amount, 0, '.', ' ') }} FCFA<br>
                                                                Remboursé: {{ number_format($loan->amount - $loan->amountpaie, 0, '.', ' ') }} FCFA<br>
                                                                Restant: {{ number_format( $loan->amountpaie, 0, '.', ' ') }} FCFA<br>
                                                                Retenue mensuelle: {{ number_format($loan->amount_deduc, 0, '.', ' ') }} FCFA<br>
                                                                Statut:
                                                                @if ($loan->statut == '2')
                                                                    <span class="badge bg-success">Remboursé</span>
                                                                @else
                                                                    <span class="badge bg-warning">En cours</span>
                                                                @endif
                                                                @if ($loan->amountpaie == '0')
                                                                    @if ($loan->statut == '1' || $loan->statut == '4')
                                                                        <a class="badge bg-success" href="{{URL::to('loan/deductFinish/' . $loan->id_loan )}}" data-title="{{ __('Fini') }}">
                                                                            Prêt remboursé <i class="ti ti-check text-white"></i>
                                                                        </a>
                                                                    @else

                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <strong>Mt à rembourser :</strong> {{ number_format($totalAmount, 0, '.', ' ') }} FCFA<br>
                                                        <strong>Mt remboursé :</strong> {{ number_format($totalAmountRemaining, 0, '.', ' ') }} FCFA<br>
                                                        <strong>Mt restant :</strong> {{ number_format($totalAmountPaid, 0, '.', ' ') }} FCFA<br>
                                                        <strong>Mt Retenue :</strong> {{ number_format($totalMonthlyDeduction, 0, '.', ' ') }} FCFA
                                                    </td>
                                                    <td  align="center" style="vertical-align:middle;">
                                                        @foreach ($employeeLoans as $loan)
                                                            @if ($loan->month_paie == $monthpaie)
                                                                <div class="d-flex">
                                                                    <a class="btn btn-icon btn-sm btn-label-success disabled me-2" href="#" data-title="{{ __('Déduit') }}">
                                                                        <i class="ti ti-check"></i>
                                                                    </a>
                                                                    <a class="btn btn-icon btn-sm btn-label-danger" href="{{URL::to('loan/deductNotMonth/' . $loan->id_loan )}}" data-title="{{ __('Annuler') }}">
                                                                        <i class="fa fa-times"></i>
                                                                    </a>
                                                                </div>
                                                            @else
                                                                @if($loan->statut == 1)
                                                                    <a class="btn btn-sm btn-primary" href="#" data-bs-toggle="modal" data-bs-target="#deductModal-{{$loan->id_loan}}">
                                                                        Oui <i class="ti ti-check text-white"></i>
                                                                    </a>
                                                                @else
                                                                    <a class="btn btn-sm btn-primary disabled" href="#">
                                                                        Oui <i class="ti ti-check text-white"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            <hr>
                                                        @endforeach
                                                    </td>
                                                    <td  align="center" style="vertical-align:middle;">
                                                        @foreach ($employeeLoans as $loan)
                                                            <a class="btn btn-icon btn-sm btn-label-info"
                                                                data-url="{{ URL::to('loan/' . $loan->id_loan . '/voir') }}"
                                                                data-ajax-popup="true" data-size="xl"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-title="{{ __('Détails prêt') }}"
                                                                data-bs-original-title="{{ __('Voir') }}" style="color:#fff;">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                            <hr>
                                                        @endforeach
                                                    </td>
                                                    <td align="center" style="vertical-align:middle;">
                                                        @foreach ($employeeLoans as $loan)
                                                            <div class="d-flex" style="justify-content: center;">
                                                                @if ($loan->statut !== '2')
                                                                    @can('Edit Loan')
                                                                    <div class="action-btn me-2">
                                                                        <a class="btn btn-icon btn-sm btn-label-warning"
                                                                            data-url="{{ URL::to('loan/' . $loan->id_loan . '/edit') }}"
                                                                            data-ajax-popup="true" data-size="xl"
                                                                            data-bs-toggle="tooltip" title=""
                                                                            data-title="{{ __('Modifier prêt') }}"
                                                                            data-bs-original-title="{{ __('Modifier') }}">
                                                                            <i class="ti ti-pencil"></i>
                                                                        </a>
                                                                    </div>
                                                                    @endcan
                                                                @endif
                                                                @can('Delete Loan')
                                                                    <div class="action-btn">
                                                                        {{ Form::open([
                                                                                'method' => 'DELETE',
                                                                                'route' => ['loan.destroy', $loan->id_loan],
                                                                                'id' => 'delete-form-' . $loan->id_loan,
                                                                            ]) }}
                                                                            <a chref="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para" data-bs-toggle="tooltip"
                                                                            title="" data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                                                <i class="ti ti-trash"></i>
                                                                            </a>
                                                                        </form>
                                                                    </div>
                                                                @endcan
                                                            </div>
                                                            <hr>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                                @foreach ($employeeLoans as $loan)
                                                    <div class="modal fade" id="deductModal-{{$loan->id_loan}}" tabindex="-1" aria-labelledby="deductModalLabel-{{$loan->id_loan}}" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deductModalLabel-{{$loan->id_loan}}">Détails du Prêt</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="form-group col-md-6">
                                                                            <p>Employé: <strong>{{ $firstLoan->name }}</strong></p>
                                                                            <p>Titre du prêt: <strong>{{ $loan->title }}</strong></p>
                                                                            <p>Date d'échéance: <strong>{{ \Auth::user()->dateFormat($loan->fin_date) }}</strong></p>
                                                                        </div>
                                                                        <div class="form-group col-md-6">
                                                                            <p>Montant à rembourser: <strong>{{ number_format($loan->amount, 0, '.', ' ') }} FCFA</strong></p>
                                                                            @if($loan->amountpaie == null)
                                                                                <p>Montant remboursé: <strong>0 FCFA</strong></p>
                                                                                <p>Montant restant: <strong>{{ number_format($loan->amount, 0, '.', ' ') }} FCFA</strong></p>
                                                                            @else
                                                                                <p>Montant remboursé: <strong>{{ number_format($loan->amountpaie, 0, '.', ' ') }} FCFA</strong></p>
                                                                                <p>Montant restant: <strong>{{ number_format($loan->amount - $loan->amountpaie, 0, '.', ' ') }} FCFA</strong></p>
                                                                            @endif
                                                                        </div>
                                                                        <div class="form-group col-md-12" align="center">
                                                                            <h6>Nombre d'échéances restant :
                                                                                <span style="color: red;">
                                                                                    <?php
                                                                                        $vartot = 0;

                                                                                        // Vérifie si $loan->amount_deduc est différent de 0 pour éviter la division par zéro
                                                                                        if ($loan->amount_deduc != 0) {
                                                                                            $vartot = $loan->amount / $loan->amount_deduc;
                                                                                        }

                                                                                        // Affichage basé sur les conditions
                                                                                    ?>
                                                                                    @if($loan->nbre_mois < $vartot)
                                                                                        {{$loan->nbre_mois}}
                                                                                    @else
                                                                                        @if($vartot <= 0 || $loan->amount_deduc <= 0)
                                                                                            0
                                                                                        @else
                                                                                            {{ $vartot }}
                                                                                        @endif
                                                                                    @endif
                                                                                </span>
                                                                            </h6>
                                                                        </div>
                                                                        <form action="{{ URL::to('loan/deductThisMonth/' . $loan->id_loan ) }}" method="POST">
                                                                            @csrf
                                                                            <div class="mb-3">
                                                                                <label for="amount_deduc" class="form-label">Montant à déduire ce mois</label>
                                                                                <input type="number" class="form-control" id="amount_deduc" name="amount_deduc" value="{{ $loan->amount_deduc }}">
                                                                                <input type="hidden" name="date_pay" id="date_pay" value="{{$monthpaie}}" />
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-recap" role="tabpanel" aria-labelledby="nav-recap-tab">
                            <div class="card-datatable table-responsive pt-0">
                                <table class="table table-striped table-hover" style="font-size: 12px;">
                                    <thead style="background-color: #000; color: #fff; font-size: 11px;">
                                        <tr>
                                            <th width="35%" style="color: #fff;"><center>{{ __('Détails des prêts') }}</center></th>
                                            <th width="35%" style="color: #fff;"><center>{{ __('Total') }}</center></th>
                                            <th width="10%" style="color: #fff;"><center>{{ __(key: 'Applicable?') }}</center></th>
                                            <th width="10%" style="color: #fff;"><center>{{ __(key: 'Historique') }}</center></th>
                                            <th width="10%" style="color: #fff;"><center>{{ __('Actions') }}</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $groupedLoans = $loans->groupBy('employee_id');
                                        @endphp
                                        @if ($groupedLoans->isEmpty())
                                            <tr>
                                                <td colspan="9"><center>{{ __('Aucune données') }}</center></td>
                                            </tr>
                                        @else
                                            @foreach ($groupedLoans as $employeeId => $employeeLoans)
                                                @php
                                                    $firstLoan = $employeeLoans->first();
                                                    $totalAmount = $employeeLoans->sum('amount');
                                                    $totalAmountPaid = $employeeLoans->sum('amountpaie');
                                                    $totalAmountRemaining = $totalAmount - $totalAmountPaid;
                                                    $totalMonthlyDeduction = $employeeLoans->sum('amount_deduc');
                                                    $amountpaie = 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        @foreach ($employeeLoans as $loan)
                                                            <div class="mb-3 p-2 border rounded">
                                                                <strong>{{ $loan->title }}</strong><br>
                                                                Échéance: {{ \Auth::user()->dateFormat($loan->fin_date) }}<br>
                                                                À rembourser: {{ number_format($loan->amount, 0, '.', ' ') }} FCFA<br>
                                                                Remboursé: {{ number_format($loan->amount - $loan->amountpaie, 0, '.', ' ') }} FCFA<br>
                                                                Restant: {{ number_format( $loan->amountpaie, 0, '.', ' ') }} FCFA<br>
                                                                Retenue mensuelle: {{ number_format($loan->amount_deduc, 0, '.', ' ') }} FCFA<br>
                                                                Statut:
                                                                @if ($loan->statut == '2')
                                                                    <span class="badge bg-success">Remboursé</span>
                                                                @else
                                                                    <span class="badge bg-warning">En cours</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <strong>Mt à rembourser :</strong> {{ number_format($totalAmount, 0, '.', ' ') }} FCFA<br>
                                                        <strong>Mt remboursé :</strong> {{ number_format($totalAmountRemaining, 0, '.', ' ') }} FCFA<br>
                                                        <strong>Mt restant :</strong> {{ number_format($totalAmountPaid, 0, '.', ' ') }} FCFA<br>
                                                        <strong>Mt Retenue :</strong> {{ number_format($totalMonthlyDeduction, 0, '.', ' ') }} FCFA
                                                    </td>
                                                    <td  align="center" style="vertical-align:middle;">
                                                        @foreach ($employeeLoans as $loan)
                                                            @if ($loan->month_paie == $monthpaie )
                                                                <a class="btn btn-sm btn-info disabled" href="#">
                                                                    Déduit
                                                                </a>
                                                            @else
                                                                @if($loan->statut == 1)
                                                                    <a class="btn btn-icon btn-sm btn-label-success disabled" href="#" data-bs-toggle="modal" data-bs-target="#deductModal-{{$loan->id_loan}}">
                                                                        <i class="ti ti-check"></i>
                                                                    </a>
                                                                @elseif($loan->statut == 4)
                                                                    <a class="btn btn-icon btn-sm btn-label-danger disabled" href="{{URL::to('loan/deductNotMonth/' . $loan->id_loan )}}">
                                                                        <i class="ti ti-close"></i>
                                                                    </a>
                                                                @else
                                                                    <a class="btn btn-icon btn-sm btn-label-success disabled" href="#">
                                                                        <i class="ti ti-check"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                            <hr>
                                                        @endforeach
                                                    </td>
                                                    <td  align="center" style="vertical-align:middle;">
                                                        @foreach ($employeeLoans as $loan)
                                                            <a class="btn btn-icon btn-sm btn-label-info"
                                                                data-url="{{ URL::to('loan/' . $loan->id_loan . '/voir') }}"
                                                                data-ajax-popup="true" data-size="xl"
                                                                data-bs-toggle="tooltip" title=""
                                                                data-title="{{ __('Détails prêt') }}"
                                                                data-bs-original-title="{{ __('Voir') }}" style="color:#fff;">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                            <hr>
                                                        @endforeach
                                                    </td>
                                                    <td align="center" style="vertical-align:middle;">
                                                        @foreach ($employeeLoans as $loan)
                                                            <div class="d-flex" style="justify-content: center;">
                                                                @if ($loan->statut == '2')
                                                                    @can('Edit Loan')
                                                                    <div class="action-btn me-2">
                                                                        <a class="btn btn-icon btn-sm btn-label-warning disabled"
                                                                            data-url="{{ URL::to('loan/' . $loan->id_loan . '/edit') }}"
                                                                            data-ajax-popup="true" data-size="xl"
                                                                            data-bs-toggle="tooltip" title=""
                                                                            data-title="{{ __('Modifier prêt') }}"
                                                                            data-bs-original-title="{{ __('Modifier') }}">
                                                                            <i class="ti ti-pencil"></i>
                                                                        </a>
                                                                    </div>
                                                                    @endcan

                                                                    @can('Delete Loan')
                                                                        <div class="action-btn">
                                                                            {{ Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['loan.destroy', $loan->id_loan],
                                                                                    'id' => 'delete-form-' . $loan->id_loan,
                                                                                ]) }}
                                                                                <a chref="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para disabled" data-bs-toggle="tooltip"
                                                                                title="" data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                                                    <i class="ti ti-trash"></i>
                                                                                </a>
                                                                            </form>
                                                                        </div>
                                                                    @endcan
                                                                @else
                                                                    @can('Edit Loan')
                                                                    <div class="action-btn me-2">
                                                                        <a class="btn btn-icon btn-sm btn-label-warning"
                                                                            data-url="{{ URL::to('loan/' . $loan->id_loan . '/edit') }}"
                                                                            data-ajax-popup="true" data-size="xl"
                                                                            data-bs-toggle="tooltip" title=""
                                                                            data-title="{{ __('Modifier prêt') }}"
                                                                            data-bs-original-title="{{ __('Modifier') }}">
                                                                            <i class="ti ti-pencil"></i>
                                                                        </a>
                                                                    </div>
                                                                    @endcan

                                                                    @can('Delete Loan')
                                                                        <div class="action-btn">
                                                                            {{ Form::open([
                                                                                    'method' => 'DELETE',
                                                                                    'route' => ['loan.destroy', $loan->id_loan],
                                                                                    'id' => 'delete-form-' . $loan->id_loan,
                                                                                ]) }}
                                                                                <a chref="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para" data-bs-toggle="tooltip"
                                                                                title="" data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                                                    <i class="ti ti-trash"></i>
                                                                                </a>
                                                                            </form>
                                                                        </div>
                                                                    @endcan
                                                                @endif
                                                            </div>
                                                            <hr>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </di>
                        </div>
                        </div>
                        <div class="tab-pane fade" id="nav-autres" role="tabpanel" aria-labelledby="nav-autres-tab">
                            <div class="table-responsive">
                                <table class="table" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th><center>{{ __('Employés') }}</center></th>
                                            <th><center>{{ __('Nom de la retenue') }}</center></th>
                                            <th><center>{{ __('Type de retenue') }}</center></th>
                                            <th><center>{{ __('Montant') }}</center></th>
                                            <th><center>{{ __('Action') }}</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cpte=1;
                                        @endphp
                                        @if ($autresRetenues->isEmpty())
                                            <tr>
                                                <td colspan="5"><center>{{ __('Aucune données') }}</center></td>
                                            </tr>
                                        @else
                                            @foreach($autresRetenues as $retenue)
                                                <tr>
                                                    <td align="center">{{ $employee->name }}</td>
                                                    <td align="center">{{ $retenue->lib_retenue }}</td>
                                                    <td align="center">{{ $retenue->type_retenue }}</td>
                                                    <td align="center">{{ number_format($retenue->montant, 0 , '.' , ' ') }} FCFA</td>
                                                    <td align="center">
                                                        <div class="row">
                                                            <div class="col-md-6" align="right">
                                                                @can('Edit Loan')
                                                                    <a class="btn btn-icon btn-sm btn-label-warning"
                                                                        data-url="{{ URL::to('setsalary/edit_autres_retenue/' . $retenue->id . '/edit') }}"
                                                                        data-ajax-popup="true" data-size="xl"
                                                                        data-bs-toggle="tooltip" title=""
                                                                        data-title="{{ __('Modifier la retenue') }}"
                                                                        data-bs-original-title="{{ __('Modifier') }}">
                                                                        <i class="ti ti-pencil"></i>
                                                                    </a>
                                                                @endcan
                                                            </div>
                                                            <div  class="col-md-6" align="left">
                                                                {{ Form::open([
                                                                        'method' => 'DELETE',
                                                                        'route' => ['setsalary.destroyAutreRetenue', $retenue->id],
                                                                        'id' => 'delete-form-' . $retenue->id,
                                                                    ]) }}
                                                                    <a class="btn btn-icon btn-sm btn-label-danger bs-pass-para"
                                                                        data-bs-toggle="tooltip" title=""
                                                                        data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                                        <i class="ti ti-trash"></i>
                                                                    </a>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- avantage en nature -->
                <div class="tab-pane fade table-responsive" id="overtime" role="tabpanel">
                    <div class="d-flex mb-2" style="justify-content: end;">
                        @can('Create Overtime')
                            <div class="col-1 text-end">
                                <a href="#" data-size="xl" data-url="{{ route('avantages.create') }}" data-ajax-popup="true"
                                    data-bs-toggle="tooltip" title="{{ __('Créer un avantage') }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>
                        @endcan
                    </div>
                    <table class="table" id="pc-dt-simple" style="font-size: 13px;">
                        <thead style="background-color: #000; color: #fff;">
                            <tr>
                                <th style="color: #fff;"><center>{{ __('Type d\'avantage') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Montant réel') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Montant selon le barème') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Traitement') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Action') }}</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($avantages->isEmpty())
                                <tr>
                                    <td colspan="9"><center>{{ __('Aucune données') }}</center></td>
                                </tr>
                            @else
                                @foreach ($avantages as $avantage)
                                    @foreach($traitements as $traitement)
                                        @if($avantage->traitement_id == $traitement->id)
                                            <tr>
                                                <td>
                                                    @if($avantage->type_avantage == 1)
                                                        Avantage en Nature
                                                    @elseif($avantage->type_avantage == 2)
                                                        Avantage en Argent
                                                    @elseif($avantage->type_avantage == 3)
                                                        Assurance-vie complémentaire
                                                    @else
                                                        Assurance santé
                                                    @endif
                                                </td>
                                                <td align="right">{{ number_format($avantage->montant_reel,'0','.',' ') }} FCFA</td>
                                                <td align="right">
                                                    @if($avantage->type_avantage == 1)
                                                        {{ number_format(($avantage->amount),'0','.',' ') }} FCFA
                                                    @else
                                                        {{ number_format(($avantage->montant_reel),'0','.',' ') }} FCFA
                                                    @endif
                                                </td>
                                                <td>{{ $traitement->libelle }}</td>
                                                <td class="Action" align="center">
                                                    @if($avantage->statut == 1)
                                                        <a class="btn btn-warning" href="{{ URL::to('payslip/updateState', $avantage->id) }}">
                                                            Suspendre
                                                        </a>
                                                        @else
                                                        <a class="btn btn-info" href="{{ URL::to('payslip/updateContinue', $avantage->id) }}">
                                                            Reprendre
                                                        </a>
                                                    @endif
                                                    <hr>
                                                    <div class="d-flex" style="justify-content: center;">
                                                        <div class="action-btn me-2">
                                                            <a href="#" class="btn btn-icon btn-sm btn-label-warning"
                                                                data-size="xl"
                                                                data-url="{{ URL::to('avantages/' . $avantage->id . '/edit') }}"
                                                                data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit') }}"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil"></i>
                                                            </a>
                                                        </div>
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['avantages.deleted', $avantage->id],
                                                            'id' => 'delete-form-' . $avantage->id,
                                                        ]) !!}
                                                            <div class="action-btn">
                                                                <a href="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para"
                                                                    data-bs-toggle="tooltip" title=""
                                                                    data-bs-original-title="Supprimer" aria-label="Supprimer"><i
                                                                        class="ti ti-trash"></i></a>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- leave -->
                <div class="tab-pane fade table-responsive" id="leave" role="tabpanel">
                    <div class="d-flex mb-2" style="justify-content: end;">
                        @can('Create Leave')
                            <div class="col-2 text-end">
                                <a href="#" data-url="{{ route('leave.createLeave',['monthpaie'=>$monthpaie,'id'=> $employee->id]) }}" data-ajax-popup="true"
                                    data-title="{{ __('Create New Leave') }}" data-size="xl" data-bs-toggle="tooltip" title=""
                                    class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>
                        @endcan
                    </div>
                    <table class="table table-hover" id="pc-dt-simple">
                        <thead style="background-color: #000; color: #fff;">
                            <tr>
                                <th style="color: #fff;"><center>{{ __('Dernier congé') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Prochain congé') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Statut') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Type de Congé') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Allocation congé') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Approbation') }}</center></th>
                                <th style="color: #fff;"><center>{{ __('Action') }}</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $leaveFound = false;
                            @endphp
                            @foreach ($leaves as $leave)
                                @if($employee->id == $leave->employee_id)
                                    <tr>
                                        <td><center>{{ \Auth::user()->dateFormat($employee->end_leave) }}</center></td>
                                        @php
                                            $dateDernierConge = Carbon::createFromFormat('Y-m-d', $employee->end_leave);
                                            $dateactuelle     = date('Y-m-d');
                                            $dateProchainConge = $dateDernierConge->copy()->addYear();
                                        @endphp
                                        <td><center>{{ \Auth::user()->dateFormat($dateProchainConge) }}</center></td>
                                        <td align="center">
                                            @if (($leave->end_date < $dateactuelle ) && ($leave->status == 'Approuvé') || ($leave->status == 'Terminé'))
                                                <span class="badge bg-danger">{{ __('Terminé') }}</span>
                                            @elseif ((($leave->end_date >= $dateactuelle) && ($leave->start_date <= $dateactuelle)) && ($leave->status == 'Démarré'))
                                                <span class="badge bg-info">{{ __('En cours') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('En attente') }}</span>
                                            @endif
                                        </td>
                                        <td align="center"><strong>{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}</strong><br>Du: {{ \Auth::user()->dateFormat($leave->start_date) }} au : {{ \Auth::user()->dateFormat($leave->end_date) }}<br>Nbre de jours : {{$leave->total_leave_days}}</td>
                                        <td align="center">
                                            @if($leave->leave_type_id == 1)
                                                <strong>{{ number_format($leave->amount_leave,'0','.',' ') }} FCFA</strong><br>
                                                @if($leave->lave_sit == 2)
                                                    <span class="badge bg-success">{{ __('Payé') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('Non Payé') }}</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td align="center">
                                            @if ($leave->status == 'Pending')
                                                <span class="badge bg-warning">{{ __('En attente') }}</span>
                                            @elseif($leave->status == 'Approuvé')
                                                <span class="badge bg-success">{{ __('Approuvé') }}</span>
                                                @if($leave->leave_type_id == 1)
                                                <hr>
                                                    <a href="#" data-url="{{ route('leave.attestation', ['id' => $leave->id]) }}" data-ajax-popup="true"
                                                        data-title="{{ __('Attestation de Congé') }}" data-size="xl" data-bs-toggle="tooltip" title=""
                                                        class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Attestation') }}">
                                                        {{ __('Attestation') }}
                                                    </a>
                                                @endif
                                            @elseif($leave->status == 'Rejeté')
                                                <span class="badge bg-danger">{{ __('Réjété') }}</span>
                                            @elseif($leave->status == 'Terminé')
                                                <span class="badge bg-info">{{ __('Terminé') }}</span>
                                                @if($leave->leave_type_id == 1)
                                                <hr>
                                                    <a href="#" data-url="{{ route('leave.attestation', ['id' => $leave->id]) }}" data-ajax-popup="true"
                                                        data-title="{{ __('Attestation de Congé') }}" data-size="xl" data-bs-toggle="tooltip" title=""
                                                        class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Attestation') }}">
                                                        {{ __('Attestation') }}
                                                    </a>
                                                @endif
                                            @else
                                                <span class="badge bg-info">{{ __('Démarré') }}</span>
                                                <hr>
                                                <a href="#" data-url="{{ route('leave.attestation', ['id' => $leave->id]) }}" data-ajax-popup="true"
                                                    data-title="{{ __('Attestation de Congé') }}" data-size="xl" data-bs-toggle="tooltip" title=""
                                                    class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Attestation') }}">
                                                    {{ __('Attestation') }}
                                                </a>
                                            @endif
                                        </td>
                                        <td align="center">
                                            <div class="d-flex justify-content-center">
                                                @if (\Auth::user()->type != 'employee')
                                                    @if($leave->status == 'Approuvé' || $leave->status == 'Démarré')
                                                        {{-- @can('Edit Leave')
                                                            <a href="#" class="btn btn-icon btn-sm btn-label-warning me-2" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-ajax-popup="true" data-title="{{ __('Edit Leave') }}" data-size="xl" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil"></i>
                                                            </a>
                                                        @endcan --}}
                                                        @if($leave->status == 'Démarré')
                                                            <a href="#" class="btn btn-sm bg-warning me-2" data-url="{{ URL::to('leave/updateLeave', $leave->id) }}">{{ __('Date retour') }}</a>
                                                        @elseif($leave->status == 'Approuvé')
                                                            <a href="#" class="btn btn-sm bg-info me-2" data-url="{{ URL::to('leave/startLeave', $leave->id) }}" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette action ?');">{{ __('Démarrer le congé') }}</a>
                                                            @can('Delete Leave')
                                                                {!! Form::open(['method' => 'DELETE', 'route' => ['leave.destroy', $leave->id], 'id' => 'delete-form-' . $leave->id]) !!}
                                                                    <a href="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                                        <i class="ti ti-trash"></i>
                                                                    </a>
                                                                {!! Form::close() !!}
                                                            @endcan
                                                        @endif
                                                    @elseif($leave->status == 'Terminé' || $leave->status == 'Rejeté')
                                                        @can('Edit Leave')
                                                            <a href="#" class="btn btn-icon btn-sm btn-label-warning me-2" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil"></i>
                                                            </a>
                                                        @endcan
                                                        @can('Delete Leave')
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['leave.destroy', $leave->id], 'id' => 'delete-form-' . $leave->id]) !!}
                                                                <a href="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                            {!! Form::close() !!}
                                                        @endcan
                                                    @elseif($leave->status == 'Pending')
                                                        <a href="#" class="btn btn-icon btn-sm btn-label-info me-2" data-url="{{ URL::to('leave/' . $leave->id . '/action') }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{ __('Manage Leave') }}">
                                                            <i class="ti ti-caret-right"></i>
                                                        </a>
                                                        @can('Edit Leave')
                                                            <a href="#" class="btn btn-icon btn-sm btn-label-warning me-2" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil"></i>
                                                            </a>
                                                        @endcan
                                                        @can('Delete Leave')
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['leave.destroy', $leave->id], 'id' => 'delete-form-' . $leave->id]) !!}
                                                                <a href="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                                                    <i class="ti ti-trash"></i>
                                                                </a>
                                                            {!! Form::close() !!}
                                                        @endcan
                                                    @endif
                                                @else
                                                    <a href="#" class="btn btn-icon btn-sm btn-label-info me-2" data-url="{{ URL::to('leave/' . $leave->id . '/action') }}" data-ajax-popup="true" data-title="{{ __('Leave Action') }}" data-bs-toggle="tooltip" title="{{ __('Manage Leave') }}">
                                                        <i class="ti ti-caret-right"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @php
                                        $leaveFound = true;
                                    @endphp
                                @endif
                            @endforeach
                            @if(!$leaveFound)
                                <tr>
                                    <td><center>{{ \Auth::user()->dateFormat($employee->end_leave) }}</center></td>
                                    @php
                                        if (!empty($employee->end_leave)) {
                                            try {
                                                $dateDernierConge = Carbon::createFromFormat('Y-m-d', $employee->end_leave);
                                                $dateactuelle     = date('Y-m-d');
                                                $dateProchainConge = $dateDernierConge->copy()->addYear();
                                            } catch (\Exception $e) {
                                                $dateProchainConge = 'Date incorrecte ou non définie';
                                            }
                                        } else {
                                            $dateProchainConge = 'Date non disponible';
                                        }
                                    @endphp
                                    <td><center>{{ \Auth::user()->dateFormat($dateProchainConge) }}</center></td>
                                    <td align="center"><span class="badge bg-warning">{{ __('Non effectué') }}</span></td>
                                    <td align="center">-</td>
                                    <td align="center">-</td>
                                    <td align="center">-</td>
                                    <td align="center">-</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- rupture -->
                <div class="tab-pane fade table-responsive" id="ruptures" role="tabpanel">
                    <div class="d-flex mb-2" style="justify-content: end;">
                        @can('Create Termination')
                            <div class="col-1 text-end">

                                <a href="#" data-url="{{ route('termination.create', ['monthpaie' => $monthpaie]) }}" data-ajax-popup="true"
                                    data-title="{{ __('Créer une nouvelle rupture ou sanctions') }}" data-size="xl" data-bs-toggle="tooltip" title=""
                                    class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                                    <i class="ti ti-plus"></i>
                                </a>
                            </div>
                        @endcan
                    </div>
                    <table class="table" id="pc-dt-simple" style="font-size: 13px;">
                        <thead style="background-color:#000">
                            <tr>
                                <th style="color:#fff;">{{ __('Termination Type') }}</th>
                                <th style="color:#fff;">{{ __('Droit à payer') }}</th>
                                <th style="color:#fff;">{{ __('Description') }}</th>
                                <th style="color:#fff;">{{ __('Statut') }}</th>
                                @if (Gate::check('Edit Termination') || Gate::check('Delete Termination'))
                                    <th style="color:#fff;">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($ruptures->isEmpty())
                                <tr>
                                    <td colspan="5"><center>{{ __('Aucune données') }}</center></td>
                                </tr>
                            @else
                                @foreach ($ruptures as $termination)
                                    @foreach($termination_types as $termine_types)
                                        @if($termination->termination_type == $termine_types->id)
                                            <tr>
                                                @php
                                                    $droitrupture = ($termination->indem_comp + $termination->indem_comp_cong + $termination->imdem_prea + $termination->indem_licence + $termination->aggravation + $termination->dom_inter)-($termination->amount_cnps + $termination->amount_its + $termination->amount_loan);
                                                @endphp
                                                <td>
                                                    {{ !empty($termination->terminationType()) ? $termination->terminationType()->name : '' }}
                                                    Date du préavis : {{ \Auth::user()->dateFormat($termination->notice_date) }} <br> Date de fin : {{ \Auth::user()->dateFormat($termination->termination_date) }}
                                                </td>
                                                <td align="right">{{number_format($droitrupture,'0','.',' ') }} FCFA</td>
                                                <td align="center">
                                                    @if($termination->statut == 1 || $termination->statut == 3)
                                                        <a href="#" class="action-item" data-url="{{ route('termination.description',$termination->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Desciption')}}" data-title="{{__('Desciption')}}"><i class="icon_desc fa fa-comment"></i></a>
                                                    @else
                                                        <h6>Vérifier  <a href="#" class="action-item" data-url="{{ route('termination.description',$termination->id) }}" data-ajax-popup="true" data-bs-toggle="tooltip" title="{{__('Desciption')}}" data-title="{{__('Desciption')}}"><i class="icon_desc fa fa-comment"></i></a> </h6>
                                                    @endif
                                                </td>
                                                <td align="center">
                                                    @if($termination->statut == 1)
                                                        <a class="btn btn-success disabled" href="#">
                                                            Validé <i class="fa fa-check"></i>
                                                        </a>
                                                    @else
                                                        @if($termination->termination_type == 6 || $termination->termination_type == 7)
                                                            @if($termination->statut == 3)
                                                                <a class="btn btn-warning disabled" href="{{ URL::to('termination/updateSanction/'.$monthpaie.'/'.$termination->id) }}" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette action ? Assurez-vous d\'avoir vérifié attentivement les informations de l\'employé.')">
                                                                    Fin de sanction <br> {{$termination->termination_date}}
                                                                </a>
                                                                @else
                                                                <a class="btn btn-warning" href="{{ URL::to('termination/updateSanction/'.$monthpaie.'/'.$termination->id) }}" onclick="return confirm('Êtes-vous sûr de vouloir terminer cette action ? Assurez-vous d\'avoir vérifié attentivement les informations de l\'employé.')">
                                                                    Fin de sanction ?
                                                                </a>
                                                            @endif
                                                        @else
                                                            <span class="status_badge badge p-2 px-3 rounded" style="background-color: #145388;">
                                                                En attente de validation
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="Action">
                                                    @if (Gate::check('Edit Termination') || Gate::check('Delete Termination'))
                                                        <div class="d-flex">
                                                            @if($termination->statut == 0)
                                                                @can('Edit Termination')
                                                                    <div class="action-btn me-2">
                                                                        <a href="#" class="btn btn-icon btn-sm btn-label-warning" data-size="xl"
                                                                            data-url="{{ URL::to('termination/' . $monthpaie . '/' . $termination->id . '/edit') }}"
                                                                            data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                                            title="" data-title="{{ __('Edit Termination') }}"
                                                                            data-bs-original-title="{{ __('Edit') }}">
                                                                            <i class="ti ti-pencil"></i>
                                                                        </a>
                                                                    </div>
                                                                @endcan
                                                                @can('Delete Termination')
                                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['termination.destroy', $termination->id], 'id' => 'delete-form-' . $termination->id]) !!}
                                                                        <div class="action-btn">
                                                                            <a href="#" class="btn btn-icon btn-sm btn-label-danger bs-pass-para"
                                                                                data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer"
                                                                                aria-label="Supprimer"><i
                                                                                    class="ti ti-trash"></i></a>
                                                                        </div>
                                                                    </form>
                                                                @endcan
                                                            @else

                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- salaire -->
                <div class="tab-pane fade" id="salaire" role="tabpanel">
                    <!--salary-->
                    <div id="salary">
                        <!-- Header -->
                        <div class="text-center">
                            Nombre de jours travaillés : <strong style="color: red;">{{$employee->tax_payer_id}} </strong> | Mise à jour ici ðŸ‘‰
                            <a data-url="{{ route('employee.basic.salary', $employee->id) }}" data-size="xl" data-ajax-popup="true"
                                class="btn btn-sm bg-warning align-items-center" data-bs-toggle="tooltip"
                                title="" data-bs-original-title="{{ __('View') }}">
                                <i class="ti ti-eye text-white"></i>
                            </a>
                        </div>
                        <div class="card-header py-4">
                            <div class="d-flex">
                                <div class="col-10" style="justify-content: start;">
                                    <h5 class="card-action-title mb-0">
                                        Dernière mise à jour du salaire : <strong style="color: red;">{{ Auth::user()->dateFormat($employee->updated_at) }}</strong>
                                    </h5>
                                </div>
                                <div class="col-2 text-end">
                                    @can('Create Allowance')
                                    <a data-url="{{ route('allowances.create', $employee->id) }}" data-size="xl" data-ajax-popup="true" class="btn btn-label-primary">
                                        <span class="text-#000"> Ajouter éléments brut <i class="ti ti-plus #000"></i></span>
                                    </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <!-- Body -->
                        <div class="card-body">
                            <div class="accordion accordion-flush accordion-arrow-left mb-4" id="ecommerceBillingAccordionAddress">
                                <!-- Salaire de Base -->
                                @if($employee->statut_emp =='Stagiaire' || $employee->statut_emp =='Apprenti')
                                    <div class="accordion-item border-bottom border-top">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHome">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHome" aria-expanded="false" aria-controls="headingHome" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            {{ __('Prime de Stage') }}
                                                        </span>
                                                        <span class="badge bg-label-success">Stagiaire</span>
                                                    </span>
                                                    <span class="mb-0 text-muted">La prime de stage est...</span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b>
                                                    @if($employee->tax_payer_id == '30')
                                                        {{ number_format($employee->salary, 0 , '.' , ' ') }} FCFA
                                                    @else
                                                        {{ number_format($employee->branch_location, 0 , '.' , ' ') }} FCFA
                                                    @endif
                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                @if(!$allowances->isEmpty())
                                                    @foreach ($allowances as $allowance)
                                                        @if($allowance->amount >= '0')
                                                            @if($allowance->title == 'Prime de stage')
                                                                @can('Edit Allowance')
                                                                    <a data-url="{{ URL::to('allowance/' . $allowance->id . '/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="" data-title="{{ __('Edit Allowance') }}" data-bs-original-title="{{ __('Edit') }}">
                                                                        <i class="ti ti-pencil text-secondary ti-sm"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('Delete Allowance')
                                                                    {!! Form::open([
                                                                        'method' => 'DELETE',
                                                                        'route' => ['allowance.destroy', $allowance->id],
                                                                        'id' => 'delete-form-' . $allowance->id,
                                                                    ]) !!}
                                                                        <a href="#" class="bs-pass-para" data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                                            <i class="ti ti-trash text-secondary ti-sm"></i>
                                                                        </a>
                                                                    </form>
                                                                @endcan
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <a href="{{ route('employee.getprime', $employee->id) }}" class="btn btn-success"><i class="ti ti-check text-white ti-sm me-2"></i> Valider</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div
                                            id="ecommerceBillingAddressHome"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1">la partie fixe qui est liée à la fonction du stagiaire</h6>
                                                <p class="mb-1">et/ou à la catégorie professionnelle du stagiaire</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="accordion-item border-bottom border-top">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHome">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHome" aria-expanded="false" aria-controls="headingHome" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            {{ __('Salaire de base') }}
                                                        </span>
                                                        <span class="badge bg-label-success">Catégoriel</span>
                                                    </span>
                                                    <span class="mb-0 text-muted">Le salaire de base est...</span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b>
                                                    @if($employee->tax_payer_id == '30')
                                                        {{ number_format($employee->salary, 0 , '.' , ' ') }} FCFA
                                                    @else
                                                        {{ number_format($employee->branch_location, 0 , '.' , ' ') }} FCFA
                                                    @endif
                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a href="javascript:void(0);" class="disabled"><i class="ti ti-pencil text-secondary ti-sm"></i></a>
                                                <a href="javascript:void(0);" class="disabled"><i class="ti ti-trash text-secondary ti-sm"></i></a>
                                            </div>
                                        </div>
                                        <div
                                            id="ecommerceBillingAddressHome"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1">la partie fixe qui est liée à la fonction du travailleur</h6>
                                                <p class="mb-1">et/ou à la catégorie professionnelle du Travailleur</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($date_pa < 2)
                                    <div class="accordion-item border-bottom border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomePrime">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeAnc" aria-expanded="false" aria-controls="headingHomePrime" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            Prime d'ancienneté
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted"> Ancienneté :  {{$date_pa}} an(s) et {{$date_m}} mois</span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b id="primeancien">

                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                @can('Edit Allowance')
                                                    <a data-url="#" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="" data-title="{{ __('Edit Allowance') }}" data-bs-original-title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-secondary ti-sm"></i>
                                                    </a>
                                                @endcan
                                                @can('Delete Allowance')
                                                    <a class="bs-pass-para" data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                        <i class="ti ti-trash text-secondary ti-sm"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                        <div
                                            id="ecommerceBillingAddressHomeAnc"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1">et 1% de salaire par année </h6>
                                                <p class="mb-1">de service jusqu'a la 25ieme années</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Détails du salaire (prime, avantages, etc.) -->
                                @if(!$allowances->isEmpty())
                                    @foreach ($allowances as $allowance)
                                        @if($allowance->amount >= '0')
                                            @if($allowance->title == 'Prime d\'ancienneté')
                                                <!-- Prime de d'ancienneté -->
                                                @if($date_pa < 2)

                                                @else
                                                    <div class="accordion-item border-bottom border-top-0">
                                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomePrime">
                                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeAnc" aria-expanded="false" aria-controls="headingHomePrime" role="button">
                                                                <span>
                                                                    <span class="d-flex gap-2 align-items-baseline">
                                                                        <span class="h6 mb-1">
                                                                            Prime d'ancienneté
                                                                        </span>
                                                                    </span>
                                                                    <span class="mb-0 text-muted"> Ancienneté :  {{$date_pa}} an(s) et {{$date_m}} mois | Mettre à jour l'anciènneté : <b id="primeancien" style="color:#fff; background-color:green;"></b></span>
                                                                </span>
                                                            </a>
                                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                                <b>{{ number_format($allowance->amount, 0 , '.' , ' ') }} FCFA</b>

                                                            </div>
                                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                                @can('Edit Allowance')
                                                                    <a data-url="{{ URL::to('allowance/' . $allowance->id . '/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="" data-title="{{ __('Edit Allowance') }}" data-bs-original-title="{{ __('Edit') }}">
                                                                        <i class="ti ti-pencil text-secondary ti-sm"></i>
                                                                    </a>
                                                                @endcan
                                                                @can('Delete Allowance')
                                                                    {!! Form::open([
                                                                        'method' => 'DELETE',
                                                                        'route' => ['allowance.destroy', $allowance->id],
                                                                        'id' => 'delete-form-' . $allowance->id,
                                                                    ]) !!}
                                                                        <a class="bs-pass-para" data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                                            <i class="ti ti-trash text-secondary ti-sm"></i>
                                                                        </a>
                                                                    </form>
                                                                @endcan
                                                            </div>
                                                        </div>
                                                        <div
                                                            id="ecommerceBillingAddressHomeAnc"
                                                            class="accordion-collapse collapse"
                                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                                            <div class="accordion-body ps-4 ms-2">
                                                                <h6 class="mb-1">et 1% de salaire par année </h6>
                                                                <p class="mb-1">de service jusqu'a la 25ieme années</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif($allowance->title == 'Prime de stage')

                                            @else
                                                <div class="accordion-item border-bottom border-top-0">
                                                    <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingOffice{{$allowance->id}}">
                                                        <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressOffice{{$allowance->id}}" aria-expanded="false" aria-controls="headingOffice{{$allowance->id}}" role="button">
                                                            <span class="d-flex flex-column">
                                                                <span class="h6 mb-0">
                                                                    @if($allowance->allowance_option != '30')
                                                                        {{ $allowance->allowance_option == '27' ? 'Prime de stage' : $allowance->allowance_option()->name }}
                                                                    @else
                                                                        {{ $allowance->title }}
                                                                    @endif

                                                                </span>
                                                                <span class="mb-0 text-muted">{{$allowance->details}}</span>
                                                            </span>
                                                        </a>
                                                        <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                            <b>
                                                                {{ number_format($allowance->amount, 0 , '.' , ' ') }} FCFA
                                                            </b>
                                                        </div>
                                                        <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                            @can('Edit Allowance')
                                                                <a data-url="{{ URL::to('allowance/' . $allowance->id . '/edit') }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="" data-title="{{ __('Edit Allowance') }}" data-bs-original-title="{{ __('Edit') }}">
                                                                    <i class="ti ti-pencil text-secondary ti-sm"></i>
                                                                </a>
                                                            @endcan
                                                            @can('Delete Allowance')
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['allowance.destroy', $allowance->id],
                                                                    'id' => 'delete-form-' . $allowance->id,
                                                                ]) !!}
                                                                    <a href="#" class="bs-pass-para" data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                                        <i class="ti ti-trash text-secondary ti-sm"></i>
                                                                    </a>
                                                                </form>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                    <div
                                                        id="ecommerceBillingAddressOffice{{$allowance->id}}"
                                                        class="accordion-collapse collapse"
                                                        aria-labelledby="headingOffice"
                                                        data-bs-parent="#ecommerceBillingAccordionAddress">
                                                        <div class="accordion-body ps-4 ms-2">
                                                            <h6 class="mb-1">ITS : Exonération {{$allowance->trait_fisc}}</h6>
                                                            <p class="mb-1">CNPS : {{$allowance->trait_cnps}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif

                                <!-- Heures Supplémentaires -->
                                <?php
                                    $totoverstimes = 0;
                                    foreach ($overtimes as $overtime){
                                        $totoverstimes = $totoverstimes + $overtime->montant;
                                    }
                                ?>
                                @if(!$overtimes->isEmpty())
                                    @foreach ($overtimes as $overtime )
                                    <div class="accordion-item border-bottom border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeHeure">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeHeure" aria-expanded="false" aria-controls="headingHomeHeure" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                             Heures Supplémentaires
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted">Les heures supplémentaires sont décomptées à la semaine...</span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b>
                                                    {{ number_format($overtime->montant, 0 , '.' , ' ') }} FCFA
                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a href="#" data-url="{{ route('heuresup.edit', ['monthpaie' => $monthpaie, 'id' => $overtime->id]) }}" data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit Overtime') }}"
                                                                data-bs-original-title="{{ __('Edit') }}"><i class="ti ti-pencil text-secondary ti-sm"></i></a>
                                                <form method="POST" action="{{ route('heuresup.destroy', ['monthpaie' => $monthpaie, 'id' => $overtime->id]) }}" id="delete-form-{{ $heuresup->id }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <a href="#" class="bs-pass-para"
                                                        data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                        data-bs-original-title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">
                                                        <i class="ti ti-trash text-secondary ti-sm"></i>
                                                    </a>
                                                </form>
                                            </div>
                                        </div>
                                        <div
                                            id="ecommerceBillingAddressHomeHeure"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1">Elles donnent lieu à des majorations de salaire.</h6>
                                                <p class="mb-1">Le plafond applicable aux heures supplémentaires est :</p>
                                                <p class="mb-1">de 15h/ supplémentaires max. par semaine par salarié</p>
                                                <p class="mb-1">et de 3 heures max. par jour au-delà de la durée journalière de travail prévue pour le salarié</p>
                                                <p class="mb-1">et de 75 heures max par an par salarié</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif

                                <!-- Allocation Congé -->
                                @if($employee->get_allo_conge() > 0)
                                    <div class="accordion-item border-bottom border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeConge">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#headingHomeAlloConge" aria-expanded="false" aria-controls="headingHomeHeure" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            Allocation congé
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted">L'allocation congé, aussi appelée indemnité de congés payés,</span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b>
                                                    {{ number_format($employee->get_allo_conge(), 0 , '.' , ' ') }} FCFA
                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a href="javascript:void(0);" class="disabled"><i class="ti ti-pencil text-secondary ti-sm"></i></a>
                                                <a href="javascript:void(0);" class="disabled"><i class="ti ti-trash text-secondary ti-sm"></i></a>
                                            </div>
                                        </div>
                                        <div
                                            id="headingHomeAlloConge"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1">est une somme versée au salarié lorsqu'il prend ses congés annuels.</h6>
                                                <p class="mb-1">Elle vise à compenser la perte de salaire due à l'absence du salarié.</p>
                                                <p class="mb-1">Cette allocation est calculée en fonction de la rémunération habituelle du salarié,</p>
                                                <p class="mb-1">et est soumise à des règles spécifiques définies par la législation et les conventions collectives.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Avantages en nature -->
                                @if($avtg_reat <= 0)

                                @else
                                    <div class="accordion-item border-bottom border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeAvantage">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeAvantage" aria-expanded="false" aria-controls="headingHomeAvantage" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            Avantages en nature
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted">consiste dans la fourniture ou ...</span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b>
                                                    {{ number_format($avtg_reat, 0 , '.' , ' ') }} FCFA
                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a href="javascript:void(0);" class="disabled"><i class="ti ti-pencil text-secondary ti-sm"></i></a>
                                                <a href="javascript:void(0);" class="disabled"><i class="ti ti-trash text-secondary ti-sm"></i></a>
                                            </div>
                                        </div>
                                        <div
                                            id="ecommerceBillingAddressHomeAvantage"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1">la mise à disposition d'un bien ou d'un service, </h6>
                                                <p class="mb-1">permettant au salarié de faire l'économie </p>
                                                <p class="mb-1">de frais qu'il aurait dû normalement supporter</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Total brut -->
                                @if($avtg_reat <= 0)
                                    <!-- Total autres retenues -->
                                    @if($employee->get_loan_retenue() > 0)
                                    <div class="accordion-item border-bottom border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            Prêts
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted">
                                                        Le "prêt" est le contrat par...
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b class="bg-label-info" style="color:#000;">
                                                    {{ number_format($employee->get_loan_retenue(), 0 , '.' , ' ') }} FCFA</b>
                                                    </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a href="#"  class="disabled"
                                                    data-size="xl"
                                                    data-url="#"
                                                    data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip"
                                                    title="" data-title="{{ __('Edit') }}"
                                                    data-bs-original-title="{{ __('Edit') }}">
                                                    <i class="ti ti-pencil text-secondary ti-sm"></i>
                                                </a>
                                                <a href="#" class="disabled"><i class="ti ti-trash text-secondary ti-sm"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div id="ecommerceBillingAddressHomeTotal" class="accordion-collapse collapse" data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1"> </h6>
                                                <p class="mb-1">Le "prêt" est le contrat par lequel une personne remet à une autre, à titre précaire, un objet, du matériel, ou</p>
                                                <p class="mb-1"> des matériaux, des marchandises, ou une somme d'argent, à charge de restitution au terme qu'elles conviennent.</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if ($retenues->isEmpty())

                                    @else
                                        @foreach($retenues as $retenue)
                                            <div class="accordion-item border-bottom border-top-0">
                                                <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                                    <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                        <span>
                                                            <span class="d-flex gap-2 align-items-baseline">
                                                                <span class="h6 mb-1">
                                                                    {{$retenue->lib_retenue}}
                                                                </span>
                                                            </span>
                                                            <span class="mb-0 text-muted">
                                                                Autre retenue déductible
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                        <b class="bg-label-danger" style="color:#000;"> {{ number_format($retenue->montant, 0 , '.' , ' ') }} FCFA</b>
                                                    </div>
                                                    <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                        <a data-url="{{ URL::to('setsalary/edit_autres_retenue/' . $retenue->id . '/edit') }}"  data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="" data-title="{{ __('Modifier la retenue') }}" data-bs-original-title="{{ __('Modifier') }}"><i class="ti ti-pencil text-secondary ti-sm"></i></a>
                                                        {{ Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['setsalary.destroyAutreRetenue', $retenue->id],
                                                                'id' => 'delete-form-' . $retenue->id,
                                                            ]) }}
                                                            <a data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer" aria-label="Supprimer"><i class="ti ti-trash text-secondary ti-sm"></i></a>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div id="ecommerceBillingAddressHomeTotal" class="accordion-collapse collapse" data-bs-parent="#ecommerceBillingAccordionAddress">
                                                    <div class="accordion-body ps-4 ms-2">
                                                        <h6 class="mb-1"> </h6>
                                                        <p class="mb-1">Le montant sera déduit du salaire net</p>
                                                        <p class="mb-1">de l'employé.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if(isset($retenuesbonus) && ($retenuesbonus->count() > 0))
                                        @foreach($retenuesbonus as $retenue)
                                            <div class="accordion-item border-bottom border-top-0">
                                                <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                                    <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                        <span>
                                                            <span class="d-flex gap-2 align-items-baseline">
                                                                <span class="h6 mb-1">
                                                                    Rétrocession de retenue
                                                                </span>
                                                            </span>
                                                            <span class="mb-0 text-muted">
                                                                Fait de restituer un droit acquis...
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                        <b class="bg-label-success" style="color:#000;">{{ number_format($retenue->montant, 0 , '.' , ' ') }} FCFA</b>
                                                    </div>
                                                    <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                        <a data-url="{{ URL::to('setsalary/edit_autres_retenue/' . $retenue->id . '/edit') }}"  data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="" data-title="{{ __('Modifier la retenue') }}" data-bs-original-title="{{ __('Modifier') }}"><i class="ti ti-pencil text-secondary ti-sm"></i></a>
                                                        {{ Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['setsalary.destroyAutreRetenue', $retenue->id],
                                                                'id' => 'delete-form-' . $retenue->id,
                                                            ]) }}
                                                            <a data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer" aria-label="Supprimer"><i class="ti ti-trash text-secondary ti-sm"></i></a>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div id="ecommerceBillingAddressHomeTotal" class="accordion-collapse collapse" data-bs-parent="#ecommerceBillingAccordionAddress">
                                                    <div class="accordion-body ps-4 ms-2">
                                                        <h6 class="mb-1"> </h6>
                                                        <p class="mb-1">Acte de reverser une partie ou la totalité d'une recette </p>
                                                        <p class="mb-1">ou des honoraires à un tiers.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="accordion-item border-bottom border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            Total brut
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted">
                                                        Le montant ou total brut représente...
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b class="bg-label-info" style="color:#000;">
                                                    {{ number_format($employee->get_brut_salary(), 0 , '.' , ' ') }} FCFA
                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a class="disabled"><i class="ti ti-minus text-secondary ti-sm"></i></a>
                                                <a class="disabled"><i class="ti ti-minus text-secondary ti-sm"></i></a>
                                            </div>
                                        </div>
                                        <div
                                            id="ecommerceBillingAddressHomeTotal"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1"> </h6>
                                                <p class="mb-1">le montant de la rémunération brute avant déduction </p>
                                                <p class="mb-1">des différentes retenue fiscales et sociales (part salariale).</p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                <!-- Total autres retenues -->
                                    @if($employee->get_loan_retenue() > 0)
                                    <div class="accordion-item border-bottom border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            Prêts
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted">
                                                        Le "prêt" est le contrat par...
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b class="bg-label-info" style="color:#000;">
                                                    {{ number_format($employee->get_loan_retenue(), 0 , '.' , ' ') }} FCFA</b>
                                                    </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a href="#"  class="disabled"><i class="ti ti-pencil text-secondary ti-sm"></i>
                                                </a>
                                                <a href="#" class="disabled"
                                                    data-bs-toggle="tooltip" title=""
                                                    data-bs-original-title="Supprimer" aria-label="Supprimer">
                                                    <i class="ti ti-trash text-secondary ti-sm"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div id="ecommerceBillingAddressHomeTotal" class="accordion-collapse collapse" data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1"> </h6>
                                                <p class="mb-1">Le "prêt" est le contrat par lequel une personne remet à une autre, à titre précaire, un objet, du matériel, ou</p>
                                                <p class="mb-1"> des matériaux, des marchandises, ou une somme d'argent, à charge de restitution au terme qu'elles conviennent.</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if ($retenues->isEmpty())

                                    @else
                                        @foreach($retenues as $retenue)
                                            <div class="accordion-item border-bottom border-top-0">
                                                <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                                    <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                        <span>
                                                            <span class="d-flex gap-2 align-items-baseline">
                                                                <span class="h6 mb-1">
                                                                    {{$retenue->lib_retenue}}
                                                                </span>
                                                            </span>
                                                            <span class="mb-0 text-muted">
                                                                Autre retenue déductible
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                        <b class="bg-label-danger" style="color:#000;"> {{ number_format($retenue->montant, 0 , '.' , ' ') }} FCFA</b>
                                                    </div>
                                                    <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                        <a data-url="{{ URL::to('setsalary/edit_autres_retenue/' . $retenue->id . '/edit') }}"  data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="" data-title="{{ __('Modifier la retenue') }}" data-bs-original-title="{{ __('Modifier') }}"><i class="ti ti-pencil text-secondary ti-sm"></i></a>
                                                        {{ Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['setsalary.destroyAutreRetenue', $retenue->id],
                                                                'id' => 'delete-form-' . $retenue->id,
                                                            ]) }}
                                                            <a data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer" aria-label="Supprimer"><i class="ti ti-trash text-secondary ti-sm"></i></a>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div id="ecommerceBillingAddressHomeTotal" class="accordion-collapse collapse" data-bs-parent="#ecommerceBillingAccordionAddress">
                                                    <div class="accordion-body ps-4 ms-2">
                                                        <h6 class="mb-1"> </h6>
                                                        <p class="mb-1">Le montant sera déduit du salaire net</p>
                                                        <p class="mb-1">de l'employé.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    @if(isset($retenuesbonus) && ($retenuesbonus->count() > 0))
                                        @foreach($retenuesbonus as $retenue)
                                            <div class="accordion-item border-bottom border-top-0">
                                                <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                                    <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                        <span>
                                                            <span class="d-flex gap-2 align-items-baseline">
                                                                <span class="h6 mb-1">
                                                                    Rétrocession de retenue
                                                                </span>
                                                            </span>
                                                            <span class="mb-0 text-muted">
                                                                Fait de restituer un droit acquis...
                                                            </span>
                                                        </span>
                                                    </a>
                                                    <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                        <b class="bg-label-success" style="color:#000;">{{ number_format($retenue->montant, 0 , '.' , ' ') }} FCFA</b>
                                                    </div>
                                                    <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                        <a data-url="{{ URL::to('setsalary/edit_autres_retenue/' . $retenue->id . '/edit') }}"  data-ajax-popup="true" data-size="xl" data-bs-toggle="tooltip" title="" data-title="{{ __('Modifier la retenue') }}" data-bs-original-title="{{ __('Modifier') }}"><i class="ti ti-pencil text-secondary ti-sm"></i></a>
                                                        {{ Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['setsalary.destroyAutreRetenue', $retenue->id],
                                                                'id' => 'delete-form-' . $retenue->id,
                                                            ]) }}
                                                            <a data-bs-toggle="tooltip" title="" data-bs-original-title="Supprimer" aria-label="Supprimer"><i class="ti ti-trash text-secondary ti-sm"></i></a>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div id="ecommerceBillingAddressHomeTotal" class="accordion-collapse collapse" data-bs-parent="#ecommerceBillingAccordionAddress">
                                                    <div class="accordion-body ps-4 ms-2">
                                                        <h6 class="mb-1"> </h6>
                                                        <p class="mb-1">Acte de reverser une partie ou la totalité d'une recette </p>
                                                        <p class="mb-1">ou des honoraires à un tiers.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="accordion-item border-bottom border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            Total brut sans avantages en nature
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted">
                                                        Le montant ou total brut représente...
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b class="bg-label-info" style="color:#000;">
                                                    {{ number_format($employee->get_brut_salary()-$avtg_reat, 0 , '.' , ' ') }} FCFA
                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a class="disabled"><i class="ti ti-minus text-secondary ti-sm"></i></a>
                                                <a class="disabled"><i class="ti ti-minus text-secondary ti-sm"></i></a>
                                            </div>
                                        </div>
                                        <div
                                            id="ecommerceBillingAddressHomeTotal"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1"> </h6>
                                                <p class="mb-1">le montant de la rémunération brute avant déduction </p>
                                                <p class="mb-1">des différentes retenue fiscales et sociales (part salariale).</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item border-top-0">
                                        <div class="accordion-header d-flex justify-content-between align-items-center flex-wrap flex-sm-nowrap" id="headingHomeTotal">
                                            <a class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#ecommerceBillingAddressHomeTotal" aria-expanded="false" aria-controls="headingHomeTotal" role="button">
                                                <span>
                                                    <span class="d-flex gap-2 align-items-baseline">
                                                        <span class="h6 mb-1">
                                                            Total brut avec avantages en nature
                                                        </span>
                                                    </span>
                                                    <span class="mb-0 text-muted">
                                                        Le montant ou total brut représente...
                                                    </span>
                                                </span>
                                            </a>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0 w-25">
                                                <b class="bg-label-danger" style="color:#000;">
                                                    {{ number_format($employee->get_brut_salary(), 0 , '.' , ' ') }} FCFA
                                                </b>
                                            </div>
                                            <div class="d-flex gap-3 p-4 p-sm-0 pt-0 ms-1 ms-sm-0">
                                                <a class="disabled"><i class="ti ti-minus text-secondary ti-sm"></i></a>
                                                <a class="disabled"><i class="ti ti-minus text-secondary ti-sm"></i></a>
                                            </div>
                                        </div>
                                        <div
                                            id="ecommerceBillingAddressHomeTotal"
                                            class="accordion-collapse collapse"
                                            data-bs-parent="#ecommerceBillingAccordionAddress">
                                            <div class="accordion-body ps-4 ms-2">
                                                <h6 class="mb-1"> </h6>
                                                <p class="mb-1">le montant de la rémunération brute avant déduction </p>
                                                <p class="mb-1">des différentes retenue fiscales et sociales (part salariale).</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <div class="">
                                    Estimation du Net : <b style="background-color: #ddcd08;">{{ number_format($employee->get_net_salary(), 0 , '.' , ' ') }} FCFA</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- retenues -->
                <div class="tab-pane fade" id="retenues" role="tabpanel">
                    <ul class="nav nav-pills flex-column flex-md-row mb-4">
                        <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#imposable">Salaire Brut Imposable (SBI) &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#sociale">Salaire Brut Social (SBS)  &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#employes">Charges Employé  &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#employeurs">Charges Employeur  &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>
                        {{--<li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#recap">Recapitulatif des Retenues  &nbsp;<i class="fa fa-arrow-circle-right"></i></a></li>--}}
                    </ul>
                    <div class="tab-content border-top">
                        <div class="tab-pane fade align-center show active" id="imposable" role="tabpanel" align="center">
                            <div class="col-xl-9 col-md-9 col-12 mb-md-0 mb-4">
                                <div  style="background-color:#1c232f; padding:15px;border-radius: 10px;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column m-sm-3 m-0">
                                            <div class="mb-xl-0 mb-4">
                                                <h6 class="fw-medium mb-2" style="color: #fff;">Salaire Brut Total :  <strong style="background-color:#ddcd08; color: #000;">{{number_format($employee->get_brut_salary(), 0 ,'.',' ')}} FCFA</strong></h6>
                                            </div>
                                            <div>
                                                <h6 class="fw-medium mb-2" style="color: #fff;">Salaire Brut Imposable :  <strong style="background-color:#ddcd08; color: #000;">{{number_format($employee->get_salary_imposable(), 0 ,'.',' ')}} FCFA</strong></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table-sm table-bordered" style="color: #fff;" width="100%">
                                        <thead>
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
                                        </thead>
                                        <tbody>
                                            @if($employee->statut_emp =='Stagiaire' || $employee->statut_emp =='Apprenti')
                                                @if(!$allowances->isEmpty())
                                                    @foreach ($allowances as $allowance)
                                                        @if($allowance->amount >= '0')
                                                            @if($allowance->title == 'Prime de stage')
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @else
                                                <tr style="border-bottom-color: #1c232f">
                                                    <!-- Ligne pour le Salaire de base -->
                                                    <td>Salaire de base</td>
                                                    <td style="text-align: right; border-right-color: #1c232f">
                                                        <b class="m-0">
                                                            @if($employee->tax_payer_id == '30')
                                                                {{ number_format($employee->salary, 0 , '.' , ' ') }}
                                                            @else
                                                                {{ number_format($employee->branch_location, 0 , '.' , ' ') }}
                                                            @endif
                                                        </b>
                                                    </td>
                                                    <td>

                                                    </td>
                                                    <td style="text-align: right;">
                                                        <b class="m-0">
                                                            @if($employee->tax_payer_id == '30')
                                                                {{ number_format($employee->salary, 0 , '.' , ' ') }}
                                                            @else
                                                                {{ number_format($employee->branch_location, 0 , '.' , ' ') }}
                                                            @endif
                                                        </b>
                                                    </td>
                                                </tr>
                                            @endif
                                            <!-- Ligne pour chaque prime -->
                                            @foreach ($allowances as $allowance)
                                            <tr style="border-bottom-color: #1c232f">
                                                <td>
                                                    @if($allowance->allowance_option != '30')
                                                        {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
                                                    @else
                                                        {{$allowance->title}}
                                                    @endif
                                                </td>
                                                <td style="text-align: right;border-right-color: #1c232f">
                                                    <b class="m-0">{{ number_format($allowance->amount, 0 , '.' , ' ') }}</b>
                                                </td>
                                                <td>

                                                </td>
                                                <td style="text-align: right;">
                                                    @if($allowance->allowance_option == 26 || $allowance->allowance_option == 11)
                                                        <?php $totalbase += $allowance->amount; ?>
                                                    @else
                                                        <b class="m-0">{{ number_format($allowance->amount, 0 , '.' , ' ') }}</b>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach

                                            <!-- Ligne pour les Heures supplémentaires (si présentes) -->
                                            @if(!$overtimes->isEmpty())
                                            <tr style="border-bottom-color: #1c232f">
                                                <td>Heures Supplémentaires</td>
                                                <td style="text-align: right;border-right-color: #1c232f">
                                                    <b class="m-0">{{ number_format($totoverstimes, 0 , '.' , ' ') }}</b>
                                                </td>
                                                <td>

                                                </td>
                                                <td style="text-align: right;">
                                                     <b class="m-0">{{ number_format($totoverstimes, 0 , '.' , ' ') }}</b>
                                                </td>
                                            </tr>
                                            @endif

                                            <!-- Ligne pour le Total Brut -->
                                            <tr>
                                                <td><b style="color: #ddcd08;">Total Brut (A)</b></td>
                                                <td style="text-align: right;border-right-color: #1c232f">
                                                    <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary() - $avtg_reat), 0 , '.' , ' ') }}</b>
                                                </td>
                                                <td>

                                                </td>
                                                <td style="text-align: right;">
                                                    <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary()-($totalbase+$avtg_reat)), 0 , '.' , ' ') }}</b>
                                                </td>
                                            </tr>

                                            <tr style="border-bottom-color: #1c232f">
                                                <td>
                                                    <b class="m-0">Avantages en Nature ou en Argent</b>
                                                </td>
                                                <td style="border-right-color: #1c232f;">
                                                    <b class="m-0" align="left">Montant réel</b>
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                    <b class="m-0" align="left">Montant au barème</b>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom-color: #1c232f">
                                                <td>
                                                    Montant des avantages au barème
                                                </td>
                                                <td style="border-right-color: #1c232f;" align="right">
                                                    {{number_format($real, 0 ,'.',' ')}}
                                                </td>
                                                <td>
                                                </td>
                                                <td align="right">
                                                    {{number_format($bareme, 0 ,'.',' ')}}
                                                </td>
                                            </tr>
                                            <tr style="border-bottom-color: #1c232f">
                                                <td>
                                                    Montant des autres avantages
                                                </td>
                                                <td style="border-right-color: #1c232f;" align="right">
                                                    {{number_format($avtg_reat_arg, 0 ,'.',' ')}}
                                                </td>
                                                <td>
                                                </td>
                                                <td align="right">
                                                    {{number_format($avtg_reat_arg2, 0 ,'.',' ')}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Total des avantages</b>
                                                </td>
                                                <td style="border-right-color: #1c232f;" align="right">
                                                    <b>{{number_format(($avtg_reat), 0 ,'.',' ')}}</b>
                                                </td>
                                                <td >
                                                </td>
                                                <td align="right">
                                                    <b>(B) {{number_format(($avtg_bareme + $avtg_reat_arg2), 0 ,'.',' ')}}</b>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td align="left">
                                                    <b class="m-0" style="color: #ddcd08;">Total Brut avec avantages</b>
                                                </td>
                                                <td style="border-right-color: #1c232f" align="right">
                                                    <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary()), 0 , '.' , ' ') }} </b>
                                                </td>
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
                                                foreach ($allowances as $allowance){
                                                    if($allowance->trait_fisc =='' || $allowance->trait_fisc =='10% - Art 116-1'){
                                                        if ($allowance->allowance_option == 31){
                                                            if ($allowance->amount > 30000){
                                                                $mont_cant = $allowance->amount - 30000;
                                                            }else{
                                                                $mont_cant = 0;
                                                            }
                                                        }
                                                        if ($allowance->allowance_option != 31){
                                                            $totoezro = $totoezro + $allowance->amount;
                                                        }
                                                    }
                                                }
                                                $totoezro = $totoezro + $mont_cant;
                                            ?>
                                            @foreach ($allowances as $allowance)
                                                @if($allowance->allowance_option != '30')
                                                    @if($allowance->trait_fisc =='' || $allowance->trait_fisc =='10% - Art 116-1')
                                                        @if ($allowance->allowance_option == 31)
                                                            <tr style="border-bottom-color: #1c232f">
                                                                <td>
                                                                    {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
                                                                </td>
                                                                <td style="border-right-color: #1c232f" align = "right">
                                                                    @if ($allowance->allowance_option == 31)
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
                                                        @endif
                                                        @if ($allowance->allowance_option != 31)
                                                            <tr style="border-bottom-color: #1c232f">
                                                                <td>
                                                                    {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
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
                                                @endif
                                            @endforeach
                                            @foreach ($allowances as $allowance)
                                                @if($allowance->allowance_option == '30')
                                                    @if($allowance->trait_fisc =='' || $allowance->trait_fisc =='10% - Art 116-1')
                                                        <tr style="border-bottom-color: #1c232f">
                                                            <td>
                                                                {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
                                                            </td>
                                                            <td style="border-right-color: #1c232f " align = "right">
                                                                @if ($allowance->amount > 30000)
                                                                    {{number_format($allowance->amount-30000, 0 ,'.',' ')}}
                                                                @else
                                                                    {{number_format(0, 0 ,'.',' ')}}
                                                                @endif
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
                                                $resltexo = 0;
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

                                            @foreach ($allowances as $allowance)
                                                @if ($allowance->allowance_option != 30)
                                                    @if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
                                                        <tr style="border-bottom-color: #1c232f">
                                                            <td>
                                                                {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
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
                                                    @if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
                                                        <tr style="border-bottom-color: #1c232f">
                                                            <td>
                                                                {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
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
                                                foreach ($allowances as $allowance){
                                                    if(strpos($allowance->trait_fisc, '100% - Art 116') === 0){
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
                                                    <b class="m-0" style="color: #000;">{{number_format((($employee->get_brut_salary()-$avtg_reat)+(($avtg_bareme+$avtg_reat_arg2)-($resltexo+$total))), 0 ,'.',' ')}}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="sociale" role="tabpanel" align="center">
                            <div class="col-xl-9 col-md-9 col-12 mb-md-0 mb-4">
                                <div  style="background-color:#1c232f; padding:15px;border-radius: 10px;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column m-sm-3 m-0">
                                            <div class="mb-xl-0 mb-4">
                                                <h6 class="fw-medium mb-2" style="color: #fff;">Salaire Brut Total :  <strong style="background-color:#ddcd08; color: #000;">{{number_format($employee->get_brut_salary(), 0 ,'.',' ')}} FCFA</strong></h6>
                                            </div>
                                            <div>
                                                <h6 class="fw-medium mb-2" style="color: #fff;">Salaire Brut Social :  <strong style="background-color:#ddcd08; color: #000;">{{number_format($employee->get_salary_social(), 0 ,'.',' ')}} FCFA</strong></h6>
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
                                            @if($employee->statut_emp =='Stagiaire' || $employee->statut_emp =='Apprenti')
                                                @if(!$allowances->isEmpty())
                                                    @foreach ($allowances as $allowance)
                                                        @if($allowance->amount >= '0')
                                                            @if($allowance->title == 'Prime de stage')
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @else
                                            <tr style="border-bottom-color: #1c232f">
                                                <!-- Ligne pour le Salaire de base -->
                                                <td>Salaire de base</td>
                                                <td style="text-align: right; border-right-color: #1c232f">
                                                    <b class="m-0">
                                                        @if($employee->tax_payer_id == '30')
                                                            {{ number_format($employee->salary, 0 , '.' , ' ') }}
                                                        @else
                                                            {{ number_format($employee->branch_location, 0 , '.' , ' ') }}
                                                        @endif
                                                    </b>
                                                </td>
                                                <td>

                                                </td>
                                                <td style="text-align: right;">
                                                </td>
                                            </tr>
                                            @endif
                                            <!-- Ligne pour chaque prime -->
                                            @foreach ($allowances as $allowance)
                                            <tr style="border-bottom-color: #1c232f">
                                                <td>
                                                    @if($allowance->allowance_option != '30')
                                                        {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
                                                    @else
                                                        {{$allowance->title}}
                                                    @endif
                                                </td>
                                                <td style="text-align: right;border-right-color: #1c232f">
                                                    <b class="m-0">{{ number_format($allowance->amount, 0 , '.' , ' ') }}</b>
                                                </td>
                                                <td>

                                                </td>
                                                <td style="text-align: right;">
                                                </td>
                                            </tr>
                                            @endforeach

                                            <!-- Ligne pour les Heures supplémentaires (si présentes) -->
                                            @if(!$overtimes->isEmpty())
                                            <tr style="border-bottom-color: #1c232f">
                                                <td>Heures Supplémentaires</td>
                                                <td style="text-align: right;border-right-color: #1c232f">
                                                    <b class="m-0">{{ number_format($totoverstimes, 0 , '.' , ' ') }}</b>
                                                </td>
                                                <td>

                                                </td>
                                                <td style="text-align: right;">
                                                </td>
                                            </tr>
                                            @endif

                                            <!-- Ligne pour le Total Brut -->
                                            <tr>
                                                <td><b style="color: #ddcd08;">Total Brut (A)</b></td>
                                                <td style="text-align: right;border-right-color: #1c232f">
                                                    <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary() - $avtg_reat), 0 , '.' , ' ') }}</b>
                                                </td>
                                                <td>

                                                </td>
                                                <td style="text-align: right;">

                                                </td>
                                            </tr>

                                            <tr style="border-bottom-color: #1c232f">
                                                <td>
                                                    <b class="m-0">Avantages en Nature ou en Argent</b>
                                                </td>
                                                <td style="border-right-color: #1c232f;">
                                                    <b class="m-0" align="left">Montant réel</b>
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                    <b class="m-0" align="left">Montant au barème</b>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom-color: #1c232f">
                                                <td>
                                                    Montant des avantages au barème
                                                </td>
                                                <td style="border-right-color: #1c232f;" align="right">
                                                    {{number_format($real, 0 ,'.',' ')}}
                                                </td>
                                                <td>
                                                </td>
                                                <td align="right">
                                                    {{number_format($bareme, 0 ,'.',' ')}}
                                                </td>
                                            </tr>
                                            <tr style="border-bottom-color: #1c232f">
                                                <td>
                                                    Montant des autres avantages
                                                </td>
                                                <td style="border-right-color: #1c232f;" align="right">
                                                    {{number_format($avtg_reat_arg, 0 ,'.',' ')}}
                                                </td>
                                                <td>
                                                </td>
                                                <td align="right">
                                                    {{number_format($avtg_reat_arg2, 0 ,'.',' ')}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <b>Total des avantages</b>
                                                </td>
                                                <td style="border-right-color: #1c232f;" align="right">
                                                    <b>{{number_format(($avtg_reat), 0 ,'.',' ')}}</b>
                                                </td>
                                                <td >
                                                </td>
                                                <td align="right">
                                                    <b>(B) {{number_format(($avtg_bareme + $avtg_reat_arg2), 0 ,'.',' ')}}</b>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td align="left">
                                                    <b class="m-0" style="color: #ddcd08;">Total Brut avec avantages</b>
                                                </td>
                                                <td style="border-right-color: #1c232f" align="right">
                                                    <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary()), 0 , '.' , ' ') }} </b>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            <tr style="border-bottom-color: #1c232f">
                                                <td><b class="m-0">Exonérations</td>
                                                <td style="text-align: right;border-right-color: #1c232f"></td>
                                                <td></td>
                                                <td></td>
                                            </tr>

                                            @foreach ($allowances as $allowance)
                                                @if ($allowance->allowance_option != 30)
                                                    @if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
                                                        <tr style="border-bottom-color: #1c232f">
                                                            <td>
                                                                {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
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
                                                @else
                                                    @if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
                                                        <tr style="border-bottom-color: #1c232f">
                                                            <td>
                                                                {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
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
                                                $total1=0;
                                                foreach ($allowances as $allowance){
                                                    if(strpos($allowance->trait_fisc, '100% - Art 116') === 0){
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
                                                <td style="border-right-color: #1c232f !important; border-top-color: #1c232f;" align="left">

                                                </td>
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
                                                    <b class="m-0" style="color: #000;">{{number_format((($employee->get_brut_salary()-$avtg_reat)+(($avtg_bareme+$avtg_reat_arg2)-($total1))), 0 ,'.',' ')}}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="employes" role="tabpanel">
                            <div class="row">
                                <div class="col-xl-12" style="color: #fff; padding:5px">
                                    <div class="card" style="padding:10px; background-color:#1c232f;">
                                        <table class="table-sm table-bordered" style="color: #fff;">
                                            <tr>
                                                <td><strong>ITS </strong></td>
                                                <td align="right" width="25%"><strong>SBI : {{number_format($employee->get_salary_imposable(),0 , '.' , ' ')}} </strong></td>
                                            </tr>
                                            <tr>
                                                <td>Impôts brut : </td>
                                                <td align="right" width="25%"><strong>{{ number_format($resultimpricf, 0 , '.' , ' ') }} </strong></td>
                                            </tr>
                                            <tr>
                                                <td>RICF ({{ $employee->parts }} parts) : </td>
                                                <td align="right" width="25%"><strong>{{ number_format($resultricf, 0 , '.' , ' ') }} </strong></td>
                                            </tr>
                                            <tr>
                                                <td>Impôts Net :</td>
                                                <td align="right" width="25%">
                                                    <strong>@if($impots < 0)
                                                        0
                                                    @else
                                                        {{ number_format($impots, 0 , '.' , ' ') }}
                                                    @endif
                                                    </strong>
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
                                                <td align="right" width="25%"><strong>SBS : {{number_format($employee->get_salary_social(),0 , '.' , ' ')}}</strong></td>
                                            </tr>
                                                <td>Cotisation Retraite : </td>
                                                <td align="right" width="25%"<strong>{{number_format($resultcnps, 0 , '.' , ' ') }} </strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-xl-12" style="color: #fff; padding:5px">
                                    <div class="card" style="padding:10px; background-color:#1c232f;">
                                        <table class="table-sm table-bordered" style="color: #fff;">
                                            <tr>
                                                <td colspan = "2"><strong>CMU</strong></td>
                                            </tr>
                                            <tr>
                                                <td>Nombre de bénéficiaires : </td><td align="right" width="25%"><strong> {{ ($cmu)}} </strong></td>
                                            </tr>
                                            <?php if( $cmu < '7'){ ?>
                                                <tr>
                                                    <td>Limité à 6 bénéficiaires : </td><td align="right" width="25%"><strong>{{ (6 - $cmu)}} Restants</strong></td>
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
                                                <td>Part Salarié :</td><td align="right" width="25%"><strong>{{ number_format($resultcmu, 0 , '.' , ' ')}}</strong></td>
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
                                                <td align="right" width="25%"<strong>{{number_format($total_cantine, 0 , '.' , ' ') }} </strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                @endif
                                <hr/>
                                <div class="project-amnt pt-3" align="left">Total Retenues : <b style="background-color: #ddcd08;">{{ number_format($totalimpots+$total_cantine, 0 , '.' , ' ') }} FCFA </b></div>
                                <div class="project-amnt pt-3" align="right">Estimation du Net : <b style="background-color: #ddcd08;">{{ number_format($employee->get_net_salary(), 0 , '.' , ' ') }} FCFA </b></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="employeurs" role="tabpanel">
                            <div class="row">
                                <div class="col-xl-12" style="color: #fff; padding:5px">
                                    <div class="card" style="padding:10px; background-color:#1c232f;">
                                        <table class="table-sm table-bordered" style="color: #fff;">
                                            <tr>
                                                <td><strong>Impôts sur Salaire</strong></td>
                                                <td align="right" width="25%"><strong> SBI : {{number_format($employee->get_salary_imposable(),0 , '.' , ' ')}} </strong></td>
                                            </tr>
                                            <tr>
                                                <td>Contribution Employeur (CNE) : </td>
                                                <td align="right"><strong>{{ number_format($resulttax1, 0 , '.' , ' ') }} </strong></td>
                                            </tr>
                                            @if($local=='local')
                                                <tr>
                                                    <td>Contribution employeur (Expatrié) : </td>
                                                    <td align="right"><strong>  0 </strong></td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>Contribution employeur (Expatrié) : </td>
                                                    <td align="right"><strong>{{ number_format($resulttax2, 0 , '.' , ' ') }} </strong></td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td>Taxe d’Apprentissage :<br/>Taxe à la F. P. C. :  </td>
                                                <td align="right"><strong>{{round($tax4)}}<br/>{{round($tax5)}} </strong></td>
                                            </tr>
                                            <tr>
                                                <td>TOTAL FDFP : </td>
                                                <td align="right"><strong>{{ number_format($resulttax3, 0 , '.' , ' ')}} </strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-xl-12" style="color: #fff; padding:5px">
                                    <div class="card" style="padding:10px; background-color:#1c232f;">
                                        <table class="table-sm table-bordered" style="color: #fff;">
                                            <tr>
                                                <td><strong>CNPS </td>
                                                <td align="right" width="25%"> SBS : {{number_format($employee->get_salary_social(),0 , '.' , ' ')}} </strong></td>
                                            </tr>
                                            <tr>
                                                <td>Cotisation retraite employeur : </td>
                                                <td align="right">{{ number_format($cnpsemp, 0 , '.' , ' ')}} </td>
                                            </tr>
                                            <tr>
                                                <td>Accident de travail (<b>{{$prt}}%</b>) : </td>
                                                <td align="right">{{number_format($tauxact, 0 , '.' , ' ') }} </td>
                                            </tr>
                                            <tr>
                                                <td>Prestation Familiale : </td>
                                                <td align="right">{{number_format($pf, 0 , '.' , ' ') }} </td>
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
                                                <td align="right" width="25%">{{ number_format($coticmuemp, 0 , '.' , ' ')}} </td>
                                            </tr>
                                            <?php if( $cmu < '7'){ ?>
                                                <tr>
                                                    <td>Bénéficiaires à charges partagées Limités à 6 : </td>
                                                    <td align="right"><strong>{{($cmu)}}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Bénéficiaires restants: </td>
                                                    <td align="right"><strong>{{(6 - $cmu)}}</strong></td>
                                                </tr>
                                            <?php }else{ ?>
                                                <tr>
                                                    <td>Limite de bénéficiaires atteint : </td>
                                                    <td align="right"> <strong> 6 </strong> </td>
                                                </tr>
                                                <tr>
                                                    <td>La part de l'employé passe de 500 à 1000 FCFA par bénéficiaires pour </td>
                                                    <td align="right"><strong>{{($cmu - 6)}}</strong> bénéficiaire(s)</td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="project-amnt pt-3" align="left">Total Retenues : <b style="background-color: #ddcd08;">{{ number_format(round($parpatronal), 0 , '.' , ' ') }} FCFA </b></div>
                            <div class="project-amnt pt-3" align="right">Estimation du Net : <b style="background-color: #ddcd08;">{{ number_format($employee->get_net_salary(), 0 , '.' , ' ') }} FCFA </b></div>
                        </div>
                    </div>
                </div>
                <!-- bulletin -->
                <div class="tab-pane fade table-responsive" id="apercu" role="tabpanel"  align="center">
                    <div class= "col-12">
                        <ul class="nav nav-pills flex-column flex-md-row mb-4">
                            <li class="nav-item"><a class="nav-link active" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull1"><i class="ti ti-note ti-xs me-1"></i> Bulletin 1</a></li>
                            <li class="nav-item"><a class="nav-link" href="javascript:void(0);" data-bs-toggle="tab" data-bs-target="#bull2"><i class="ti ti-note ti-xs me-1"></i> Bulletin 2</a></li>
                        </ul>
                    </div>
                    <div class="tab-content mb-4">
                        <div class="tab-pane fade show active table-responsive" id="bull1" role="tabpanel"  align="center">
                            <div class="col-xl-9" style="font-family: Arial; font-size: 11px;">
                                <table class="table-sm table-bordered" width="100%" style="font-family: Arial; font-size: 11px;">
                                    <tr>
                                        <td class="border border-dark bg-primary" colspan ="9" align="center" style="vertical-align: middle;">
                                            <h2 style="color:#fff;"><strong>BULLETIN DE PAIE</strong></h2>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" bgcolor="#C0C0C0" class="border border-dark" colspan ="4">EMPLOYEUR</td>
                                        <td width="50%" bgcolor="#C0C0C0" class="border border-dark" colspan ="5">MATRICULE DU SALARIE:   {{ \Auth::user()->employeeIdFormat($employee['employee_id']) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-dark" colspan ="4">
                                            Nom :  {{ \Utility::getValByName('company_name') }}<br>
                                            Adresse :  {{ \Utility::getValByName('company_city') }}, {{ \Utility::getValByName('company_address') }}<br>
                                            Téléphone : {{ \Utility::getValByName('company_telephone') }}<br>
                                            Boite postale :  {{ \Utility::getValByName('company_zipcode') }}<br>
                                            Période :  {{$firstDay}} au {{$lastDay}}<br>
                                            Horaire mensuelle :  173,33<br>
                                            Nombre de jours travaillés : {{$employee->tax_payer_id}} <br>
                                            Grille salariale : <strong>{{ $sect }}</strong><br>
                                        </td>
                                        <td class="border border-dark" colspan ="5">
                                            Nom et Prénom :  {{ $employee->name }}<br>
                                            Adresse :  {{ $employee->address }}<br>
                                            Situation matrimoniale : {{ $situation }}<br>
                                            Enfants à charge : {{ $employee->enfant }}<br>
                                            Numéro CNPS :  {{ $employee->num_cnps }}<br>
                                            Ancienneté :  {{$date_pa}} an(s) et {{$date_m}} mois<br>
                                            @foreach($categorie as $cate)
                                                @if($employee->categorie==$cate->id)
                                                    Catégorie : {{$postevalue}} / {{$valueposte}}<br>
                                                @endif
                                            @endforeach
                                            Emploi :   {{ !empty(\Auth::user()->getDesignation($employee['designation_id'])) ? \Auth::user()->getDesignation($employee['designation_id'])->name : '-' }} <br>
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
                                    @if($employee->statut_emp =='Stagiaire' || $employee->statut_emp =='Apprenti')
                                        @if(!$allowances->isEmpty())
                                            @foreach ($allowances as $allowance)
                                                @if($allowance->amount >= '0')
                                                    @if($allowance->title == 'Prime de stage')
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    @else
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right"><?php $code = 101; echo $code-1; ?></td>
                                        <td>Salaire de base</td>
                                        <td class="montant">{{$employee->tax_payer_id}}</td>
                                        <td class="montant">{{ number_format(($employee->salary/30), 0, '.', ' ') }}</td>
                                        <td></td>
                                        @if($employee->tax_payer_id == '30')
                                            <td class="montant">{{ number_format($employee->salary, 0, '.', ' ') }}</td>
                                        @else
                                            <td class="montant">{{ number_format($employee->branch_location, 0, '.', ' ') }}</td>
                                        @endif
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    @endif
                                    @foreach ($allowances as $allowance)
                                        @if($allowance->amount > '0')
                                        <tr style="border-bottom-color: inherit;">
                                            <td align="right">
                                                @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                    {{$allowance->code}}
                                                @elseif($allowance->allowance_option != '11')
                                                    {{$allowance->code}}
                                                @endif
                                                @if($allowance->allowance_option == '11')
                                                    {{$allowance->code}}
                                                @endif
                                                <?php $code = ($allowance->code+1);?>
                                            </td>
                                            <td>
                                                @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                    {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
                                                @elseif($allowance->allowance_option != '11')
                                                    {{$allowance->title}}
                                                @endif
                                                @if($allowance->allowance_option == '11')
                                                    {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
                                                @endif
                                            </td>
                                            <td class="montant">
                                                @if($allowance->allowance_option == '1')
                                                    {{$jours_work}}
                                                @elseif($allowance->allowance_option == '2')
                                                    {{$jours_work}}
                                                @elseif($allowance->allowance_option == '4')
                                                    {{$jours_work}}
                                                @elseif($allowance->allowance_option == '10')
                                                    {{$jours_work}}
                                                @elseif($allowance->allowance_option == '7')
                                                    {{$jours_work}}
                                                @elseif($allowance->allowance_option == '11')
                                                    {{$employee->tax_payer_id}}
                                                @elseif($allowance->allowance_option == '12')
                                                    {{$jours_work}}
                                                @elseif($allowance->allowance_option == '13')
                                                    {{$jours_work}}
                                                @elseif($allowance->allowance_option == '17')
                                                    {{$jours_work}}
                                                @elseif($allowance->allowance_option == '26')
                                                    {{$employee->enfant}}
                                                @else
                                                    {{$jours_work}}
                                                @endif
                                            </td>
                                            <td class="montant">
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
                                                    @if(\Utility::getValByName('company_city') =='ABIDJAN' || \Utility::getValByName('company_city') == 'Abidjan' || \Utility::getValByName('company_city') =='abidjan' || strpos(\Utility::getValByName('company_city'), 'Abidjan') !== false)
                                                        {{ number_format(30000/30, 0, '.', ' ') }}
                                                    @elseif (\Utility::getValByName('company_city')=='Bouaké' || \Utility::getValByName('company_city')=='BOUAKE' || \Utility::getValByName('company_city')=='bouaké')
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
                                            <td class="montant">
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
                                                @elseif($allowance->allowance_option == '11')
                                                    {{--
                                                    @if(\Utility::getValByName('company_city') =='Abidjan' || \Utility::getValByName('company_city') =='ABIDJAN' || \Utility::getValByName('company_city') =='abidjan' || strpos(\Utility::getValByName('company_city'), 'Abidjan') !== false)
                                                        {{ number_format(30000/30, 0, '.', ' ') }}
                                                    @elseif (\Utility::getValByName('company_city')=='Bouaké' || \Utility::getValByName('company_city')=='BOUAKE' || \Utility::getValByName('company_city')=='bouaké')
                                                        {{ number_format(round(24000/30), 0, '.', ' ') }}
                                                    @else
                                                        {{ number_format(round(22000/30), 0, '.', ' ') }}
                                                    @endif
                                                    --}}
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
                                            <td class="montant">
                                                {{ number_format(round($allowance->amount), 0, '.', ' ') }}
                                            </td>
                                            <td>
                                                @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                    <br/>
                                                @elseif($allowance->allowance_option != '11')
                                                    <br/>
                                                @endif
                                                @if($allowance->allowance_option == '11')
                                                    <br/>
                                                @endif
                                            </td>
                                            <td>
                                                @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                    <br/>
                                                @elseif($allowance->allowance_option != '11')
                                                    <br/>
                                                @endif
                                                @if($allowance->allowance_option == '11')
                                                    <br/>
                                                @endif
                                            </td>
                                            <td>
                                                @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                    <br/>
                                                @elseif($allowance->allowance_option != '11')
                                                    <br/>
                                                @endif
                                                @if($allowance->allowance_option == '11')
                                                    <br/>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    @if($employee->get_allo_conge() <= 0)

                                    @else
                                        <tr style="border-bottom-color: inherit;">
                                            <td align="right">
                                                <?php $code++;?>
                                            </td>
                                            <td>
                                                Allocation congé
                                            </td>
                                            <td class="montant">

                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td class="montant">
                                                {{number_format($employee->get_allo_conge(), 0 , '.' , ' ')}}
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            @if($overtimes->isEmpty())

                                            @else
                                                <?php $code++; ?>
                                            @endif
                                        </td>
                                        <td>
                                            @if($overtimes->isEmpty())

                                            @else
                                                Heures Supplémentaires
                                            @endif
                                        </td>
                                        <td class="montant">
                                            @if($overtimes->isEmpty())

                                            @else
                                                {{$jours_work}}
                                            @endif
                                        </td>
                                        <td class="montant">
                                            <?php
                                                $totoverstimes = 0;
                                                foreach ($overtimes as $overtime){
                                                    $totoverstimes = $totoverstimes + $overtime->montant;
                                                }
                                            ?>
                                            @if($overtimes->isEmpty())

                                            @else
                                                {{number_format($totoverstimes, 0 , '.' , ' ')}}
                                            @endif
                                            <?php
                                                $totoezro1=0;
                                                foreach ($allowances as $allowance){
                                                    if($allowance->trait_fisc =='' || $allowance->trait_fisc =='0%'){
                                                        $totoezro1 = $totoezro1 + $allowance->amount;
                                                    }
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            @if($overtimes->isEmpty())

                                            @else
                                                <br/>
                                            @endif
                                        </td>
                                        <td class="montant">
                                            <?php
                                                $totoverstimes = 0;
                                                foreach ($overtimes as $overtime){
                                                    $totoverstimes = $totoverstimes + $overtime->montant;
                                                }
                                            ?>
                                            @if($overtimes->isEmpty())

                                            @else
                                            {{number_format($totoverstimes, 0 , '.' , ' ')}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($overtimes->isEmpty())

                                            @else
                                                <br/>
                                            @endif
                                        </td>
                                        <td>
                                            @if($overtimes->isEmpty())

                                            @else
                                                <br/>
                                            @endif
                                        </td>
                                        <td>
                                            @if($overtimes->isEmpty())

                                            @else
                                                <br/>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($avantages->isEmpty())

                                    @else
                                        <tr style="border-bottom-color: inherit;">
                                            <td align="right">
                                                <?php $num_atg = 0; echo $code++; $num_atg = $code;?>
                                            </td>
                                            <td>
                                                Avantages en nature et en argent
                                            </td>
                                            <td class="montant">
                                                {{$jours_work}}
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td class="montant">
                                                {{number_format($avtg_reat2, 0 , '.' , ' ')}}
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($ruptures->isEmpty())

                                    @else
                                        @foreach ($ruptures as $termine)
                                            @if($termine->indem_comp > 0)
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right">
                                                        <?php echo $code++; ?>
                                                    </td>
                                                    <td>
                                                        Indemnité de Gratification
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->indem_comp, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->indem_comp, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($termine->indem_comp_cong > 0)
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right">
                                                        <?php echo $code++; ?>
                                                    </td>
                                                    <td>
                                                        Indemnité compensatrice de congé
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->indem_comp_cong, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->indem_comp_cong, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($termine->imdem_prea > 0)
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right">
                                                        <?php echo $code++; ?>
                                                    </td>
                                                    <td>
                                                        Indemnité de préavis
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->imdem_prea, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->imdem_prea, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($termine->indem_licence > 0)
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right">
                                                        <?php echo $code++; ?>
                                                    </td>
                                                    <td>
                                                        Indemnité de licenciement
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->indem_licence, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->indem_licence, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($termine->aggravation > 0)
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right">
                                                        <?php echo $code++; ?>
                                                    </td>
                                                    <td>
                                                        Dommages et intérêts
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->aggravation, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->aggravation, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($termine->dom_inter > 0)
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right">
                                                        <?php echo $code++; ?>
                                                    </td>
                                                    <td>
                                                        Dommages et intérêts
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->dom_inter, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">
                                                        {{number_format($termine->dom_inter, 0 , '.' , ' ')}}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">

                                        </td>
                                        <td align="center">
                                            Total Brut
                                        </td>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td class="montant">
                                            {{number_format(round($employee->get_brut_salary()), 0 , '.' , ' ')}}
                                        </td>
                                        <td class="montant">

                                        </td>
                                        <td class="montant">

                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            401
                                        </td>
                                        <td>
                                            Impôts bruts avant RICF
                                        </td>
                                        <td>

                                        </td>
                                        <td class="montant">
                                            {{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                            <br>
                                        </td>
                                        <td class="montant">{{number_format($resultimpricf, 0 , '.' , ' ')}}</td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            402
                                        </td>
                                        <td>
                                            Réduction pour Charges de Famille
                                        </td>
                                        <td class="montant">
                                            {{$employee->parts}}
                                        </td>
                                        <td class="montant">
                                            {{number_format($resultricf, 0 , '.' , ' ')}}
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br>
                                        </td>
                                        <td></td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            403
                                        </td>
                                        <td>
                                            Impôts Nets
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>

                                        </td>
                                        <td class="montant">
                                            {{number_format($impots, 0 , '.' , ' ')}}
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            301
                                        </td>
                                        <td>
                                            Cotisation Retraite CNPS
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td class="montant">
                                            {{number_format($employee->get_salary_social(), 0 , '.' , ' ')}}
                                        </td>
                                        <td class="montant">
                                            6,3
                                        </td>
                                        <td>

                                        </td>
                                        <td class="montant">{{number_format(round($cnps), 0 , '.' , ' ')}}</td>
                                        <td class="montant">
                                            7,70
                                        </td>
                                        <td class="montant">
                                            {{number_format($cnpsemp,0,'.',' ')}}
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            302
                                        </td>
                                        <td>
                                            Couverture Maladie Universelle
                                        </td>
                                        <td class="montant">
                                            {{$employee->cmu}}
                                        </td>
                                        <td class="montant">
                                            {{number_format(1000, 0 , '.' , ' ')}}
                                        </td>
                                        <td class="montant">
                                            0,50
                                        </td>
                                        <td>
                                        </td>
                                        <td class="montant">
                                            {{number_format($resultcmu, 0 , '.' , ' ')}}
                                        </td>
                                        <td class="montant">
                                            0,50
                                        </td>
                                        <td class="montant">
                                            {{number_format($coticmuemp,0,'.',' ')}}
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            409
                                        </td>
                                        <td>
                                            Contribution Employeur
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td class="montant">
                                            {{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td></td>
                                        <td class="montant">
                                            1,20
                                        </td>
                                        <td class="montant">
                                            {{number_format($resulttax1, 0, '.',' ')}}
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            410
                                        </td>
                                        <td>
                                            Contribution employeur (Expatrié)
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td class="montant">
                                            @if($local=='local')
                                                {{number_format(0, 0 , '.' , ' ')}}
                                            @else
                                                {{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($local=='local')
                                                <br/>
                                            @else
                                                <br/>
                                            @endif
                                        </td>
                                        <td>
                                            @if($local=='local')
                                                <br/>
                                            @else
                                                <br/>
                                            @endif
                                        </td>
                                        <td></td>
                                        <td class="montant">
                                            @if($local=='local')
                                                0
                                            @else
                                                9,20
                                            @endif
                                        </td>
                                        <td class="montant">
                                            @if($local=='local')
                                                0
                                            @else
                                                {{number_format($resulttax2,0,'.',' ')}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            411
                                        </td>
                                        <td>
                                            Taxe d’Apprentissage
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td class="montant">
                                            {{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td></td>
                                        <td class="montant">
                                            0,40
                                        </td>
                                        <td class="montant">
                                            {{number_format(round($tax4), 0 , '.' , ' ')}}
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            412
                                        </td>
                                        <td>
                                            Taxe.F.P.C
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td class="montant">
                                            {{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td></td>
                                        <td class="montant">
                                            1,20
                                        </td>
                                        <td class="montant">
                                            {{number_format(round($tax5), 0 , '.' , ' ')}}
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            305
                                        </td>
                                        <td>
                                            Accident de travail
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td class="montant">
                                            75 000
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td></td>
                                        <td class="montant">
                                            {{$prt}},00
                                        </td>
                                        <td class="montant">
                                            {{number_format(round($tauxact), 0 , '.' , ' ')}}
                                        </td>
                                    </tr>
                                    <tr style="border-bottom-color: inherit;">
                                        <td align="right">
                                            306
                                        </td>
                                        <td>
                                            Prestation Familiale
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td class="montant">
                                            75 000
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td>
                                            <br/>
                                        </td>
                                        <td></td>
                                        <td class="montant">
                                            5,75
                                        </td>
                                        <td class="montant">
                                            {{number_format(round($pf),0,'.',' ')}}
                                        </td>
                                    </tr>
                                    @php
                                        $autrecode = 500;
                                    @endphp
                                    @foreach ($allowances as $allowance)
                                        @if($allowance->amount > '0')
                                            @if($allowance->allowance_option == '31')
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right">
                                                        {{$allowance->code}}

                                                    </td>
                                                    <td>
                                                        {{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>

                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td>
                                                        <br/>
                                                    </td>
                                                    <td class="montant">{{number_format(round($allowance->amount), 0 , '.' , ' ')}}</td>
                                                    <td>

                                                    </td>
                                                    <td>

                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                    @if($employee->get_loan_retenue() || isset($avantages))
                                        <tr style="border-bottom-color: inherit;">
                                            <td align="right">

                                            </td>
                                            <td align="center">
                                                Total Cotisations
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                            </td>
                                            <td class="montant">
                                                    {{number_format(round($totalimpots+$total_cantine), 0 , '.' , ' ')}}

                                            </td>
                                            <td >
                                                <br/>
                                            </td>
                                            <td class="montant">
                                                {{number_format(round($parpatronal), 0 , '.' , ' ')}}
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td align="right" >
                                                <br/>
                                            </td>
                                            <td>
                                                Total Cotisations
                                            </td>
                                            <td >
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td >
                                            </td>
                                            <td class="montant">
                                                    {{number_format(round($totalimpots+$total_cantine), 0 , '.' , ' ')}}
                                            </td>
                                            <td >
                                                <br/>
                                            </td>
                                            <td class="montant">
                                                {{number_format(round($parpatronal), 0 , '.' , ' ')}}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($avantages->isEmpty())

                                    @else
                                        <tr style="border-top-color: #fff;">
                                            <td align="right">
                                                <?php echo 501; ?>
                                            </td>
                                            <td >
                                                Avantages en nature et en argent
                                            </td>
                                            <td >

                                            </td>
                                            <td>
                                                <br>
                                            </td>
                                            <td >
                                                <br/>
                                            </td>
                                            <td>

                                            </td>
                                            <td class="montant">
                                                {{number_format($avtg_reat2, 0 , '.' , ' ')}}
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                            <td>
                                                <br/>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($employee->get_loan_retenue() > 0)
                                        @if(isset($autreretenue) && ($autreretenue > 0))
                                            @foreach($retenues as $retenue)
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right"  align="right" >
                                                        <?php echo 502; ?>
                                                    </td>
                                                    <td ><div class="project-amnt pt-1" align="left">{{$retenue->lib_retenue}}</td>
                                                    <td ><p></p></td>
                                                    <td  align="right"></td>
                                                    <td ></td>
                                                    <td ><p> </p></td>
                                                    <td  align="right">{{number_format($retenue->montant, 0 , '.' , ' ')}}</td>
                                                    <td ><p></p></td>
                                                    <td ><p></p></td>
                                                </tr>
                                            @endforeach
                                        @else

                                        @endif
                                        <tr>
                                            <td align="right"  align="right" style="border-buttom: solid 1px white;">
                                                <?php echo 500; ?>
                                            </td>
                                            <td ><div class="project-amnt pt-1" align="left">Prêts</td>
                                            <td ><p></p></td>
                                            <td  align="right">{{number_format($employee->get_loan(), 0 , '.' , ' ')}}</td>
                                            <td ></td>
                                            <td ><p> </p></td>
                                            <td  align="right">{{number_format($employee->get_loan_retenue(), 0 , '.' , ' ')}}</td>
                                            <td ><p></p></td>
                                            <td ><p></p></td>
                                        </tr>
                                        @if(isset($autreretenuetype) && ($autreretenuetype > 0))
                                            <tr style="border-bottom-color: inherit;">
                                                <td align="right"  align="right" >
                                                    <?php echo 503; ?>
                                                </td>
                                                <td>Rétrocession de retenue</td>
                                                <td><p></p></td>
                                                <td align="right"></td>
                                                <td></td>
                                                <td align="right">{{number_format($autreretenuetype, 0 , '.' , ' ')}}</td>
                                                <td></td>
                                                <td><p></p></td>
                                                <td><p></p></td>
                                            </tr>
                                        @else

                                        @endif
                                    @else
                                        @if(isset($autreretenue) && ($autreretenue > 0))
                                            @foreach($retenues as $retenue)
                                                <tr style="border-bottom-color: inherit;">
                                                    <td align="right"  align="right" >
                                                        <?php echo 502; ?>
                                                    </td>
                                                    <td ><div class="project-amnt pt-1" align="left">{{$retenue->lib_retenue}}</td>
                                                    <td ><p></p></td>
                                                    <td  align="right"></td>
                                                    <td ></td>
                                                    <td ><p> </p></td>
                                                    <td  align="right">{{number_format($retenue->montant, 0 , '.' , ' ')}}</td>
                                                    <td ><p></p></td>
                                                    <td ><p></p></td>
                                                </tr>
                                            @endforeach
                                        @else

                                        @endif
                                        @if(isset($autreretenuetype) && ($autreretenuetype > 0))
                                            <tr style="border-bottom-color: inherit;">
                                                <td align="right"  align="right" >
                                                    <?php echo 503; ?>
                                                </td>
                                                <td>Rétrocession de retenue</td>
                                                <td><p></p></td>
                                                <td align="right"></td>
                                                <td></td>
                                                <td align="right">{{number_format($autreretenuetype, 0 , '.' , ' ')}}</td>
                                                <td></td>
                                                <td><p></p></td>
                                                <td><p></p></td>
                                            </tr>
                                        @else

                                        @endif
                                    @endif
                                    <tr>
                                        <td class="border border-dark" style="border-top-color: solid 1px #000;" colspan="9">
                                            <div class="project-amnt pt-1" align="" style="color:#000;">
                                                @foreach ($paytype as $type)
                                                    @if($type->id == $employee->paytype)
                                                        <b><i>Payé par : {{ $type->name }} </i></b>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
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
                                    <tr>
                                        <td class="border border-dark">
                                            Période<hr/>
                                            Année
                                        </td>
                                        <td class="border border-dark montant">
                                            {{number_format($employee->get_brut_salary(), 0 ,'.',' ')}}<hr/>
                                            {{number_format($employee->getTotalSalarybrut(), 0 ,'.',' ')}}</td>
                                        <td class="border border-dark montant">
                                            {{number_format(round($totalimpots+$total_cantine),0,'.',' ')}}<hr/>
                                            {{number_format($employee->getTotalSalaryRetenue(), 0 ,'.',' ')}}</td>
                                        <td class="border border-dark montant">
                                            {{round($parpatronal)}}<hr/>
                                            {{number_format($employee->getTotalSalaryPatronale(), 0 ,'.',' ')}}</td>
                                        <td class="border border-dark montant">
                                            {{number_format($avtg_reat2, 0 , '.' , ' ')}}<hr/>
                                            {{number_format(($compte*$avtg_reat2), 0 , '.' , ' ')}}</td>
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
                                                {{number_format(($employee->get_net_salary()), 0 ,'.',' ')}}
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
                        <div class="tab-pane fade table-responsive" id="bull2" role="tabpanel"  align="center">
                            <div class="bulletin">
                                <div class="color-picker">
                                    <label for="colorPicker">Choisissez une couleur :</label>
                                    <input type="color" id="colorPicker" value="#0070C0">
                                </div>
                                <hr>
                                @include('payslip.bulletin')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        var span = document.getElementById('primeancien');
        var salarybase  = parseInt(document.getElementById("salarybase").value); // Convertir en nombre
        var salarybase2 = parseInt(document.getElementById("salarybase2").value); // Convertir en nombre
        var baraJours   = parseInt(document.getElementById("baraJours").value); // Convertir en nombre
        var dateancien  = parseInt(document.getElementById("dateancien").value); // Convertir en nombre
        var divancien   = document.getElementById('divancien');

        if (dateancien < 2) {
            span.innerHTML = '<strong>0 FCFA</strong>';
        } else if (dateancien === 2) {
            if(baraJours == 30){
                span.innerHTML = '<strong> ' + Math.round((salarybase * 2) / 100).toLocaleString() + ' FCFA</strong>';
            }else{
                span.innerHTML = '<strong> ' + Math.round((((salarybase * 2) / 100)/30)*baraJours).toLocaleString() + ' FCFA</strong>';
            }
        } else if (dateancien > 2 && dateancien < 26) {
            if(baraJours == 30){
                span.innerHTML = '<strong>' + Math.round((salarybase * dateancien) / 100).toLocaleString() + ' FCFA</strong>';
            }else{
                span.innerHTML = '<strong> ' + Math.round((((salarybase * dateancien) / 100)/30)*baraJours).toLocaleString() + ' FCFA</strong>';
            }

        } else {
            if(baraJours == 30){
                span.innerHTML = '<strong> ' + Math.round((salarybase * 25) / 100).toLocaleString() + ' FCFA</strong>';
            }else{
                span.innerHTML = '<strong> ' + Math.round((((salarybase * 25) / 100)/30)*baraJours).toLocaleString() + ' FCFA</strong>';
            }
        }
    </script>
</div>

@endsection
@push('script')

@endpush

