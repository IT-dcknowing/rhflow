@php
    $plan = Utility::getChatGPTSettings();
	use Carbon\Carbon;
    // DÃ©finir la locale en franÃ§ais
    setlocale(LC_TIME, 'fr_FR.utf8');
@endphp
<?php
    //$employees->martalstatu_id.' '.$employees->enfant.' '.$employees->personneinf.' '..' '.;
	$brut_total = $employee->get_brut_salary();
	$brut = $employee->salary;
	$brut_cnps = $employee->salary;
	$nbre_jours = $employee->tax_payer_id;
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
		// Fix: Table 2023 certifiÃ©e
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
		// Fix: Table 2023 certifiÃ©e avec proratisation exacte
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
		$situation = 'CÃ©libataire';
	}else if($employee->martalstatu_id == '2'){
		$situation = 'MariÃ©(e)';
	}else if($employee->martalstatu_id == '3'){
		$situation = 'DivorcÃ©(e)';
	}else if($employee->martalstatu_id == '4'){
		$situation = 'Veuf(ve)';
	}

	$compte=0;
	if($payslipss->isEmpty()){
		$compte=1;
	}else{
		foreach ($payslipss as $payslip) {
			// Vous pouvez maintenant accÃ©der Ã  la propriÃ©tÃ© 'salary_month' en toute sÃ©curitÃ©
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
?>
<style type="text/css">
    * {margin:0; padding:0; text-indent:0; }
    .s1 { color: black; font-family:Arial, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 7.5pt; }
    .s2 { color: black; font-family:Arial, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 9pt; }
    .s3 { color: #FFF; font-family:Arial, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 27.5pt; }
    .s4 { color: black; font-family:Arial, sans-serif; font-style: italic; font-weight: bold; text-decoration: none; font-size: 9pt; }
    .s5 { color: black; font-family:Arial, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 7.5pt; }
    .s6 { color: black; font-family:Arial, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 12pt; }
    .s7 { color: black; font-family:Arial, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt; }
    .s8 { color: black; font-family:Arial, sans-serif; font-style: italic; font-weight: bold; text-decoration: none; font-size: 7pt; }
    .s9 { color: #FFF; font-family:Arial, sans-serif; font-style: italic; font-weight: bold; text-decoration: none; font-size: 7.5pt; }
    p { color: black; font-family:Arial, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 7.5pt; margin:0pt; }
    table, tbody {vertical-align: top; overflow: visible; }
    .arrow-icon {
        color: black; /* Couleur par dÃ©faut de l'icÃ´ne */
    }

    .nav-link.active .arrow-icon {
        color: black; /* Couleur de l'icÃ´ne lorsque l'onglet est actif */
    }
    .scroll-bottom {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 90px;
        height: 50px;
        cursor: pointer;
    }

    .scroll-top svg {
        fill: none;
        stroke: #007bff;
        stroke-width: 2;
    }

    .calculator {
        position: fixed;
        top: 70%;
        left: 90%;
        transform: translate(-50%, -50%);
        width: 300px;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .calculator-content {
        padding: 10px;
    }

    .calculator-display {
        margin-bottom: 10px;
    }

    #display {
        width: 100%;
        padding: 10px;
        font-size: 1.5em;
        border: none;
        border-radius: 5px;
        text-align: right;
    }

    .calculator-buttons {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .btn {
        padding: 20px;
        font-size: 1.2em;
        border: none;
        border-radius: 5px;
        background-color: #fff;
        cursor: pointer;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #e0e0e0;
    }

    .cacher {
        display: none;
    }
</style>
@php
    $plan = Utility::getChatGPTSettings();
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
@endphp
@php
	$logo = asset(Storage::url('uploads/logo/'));
	$company_logo = Utility::get_company_logo();
    // DÃ©finir la locale en franÃ§ais
    $date = Carbon::parse($monthpaie);
@endphp
@extends('layouts.admin')

@section('page-title')
    {{ __('Employee Set Salary') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Set Salary') }}</li>
    <li class="breadcrumb-item">{{ __('Employee Set Salary') }}</li>
@endsection
@push('script-page')
    <script src="{{ asset('css/summernote/summernote-bs4.js') }}"></script>
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })

        $('.themes-color-change').on('click', function() {
            var color_val = $(this).data('value');
            $('.theme-color').prop('checked', false);
            $('.themes-color-change').removeClass('active_color');
            $(this).addClass('active_color');
            $(`input[value=${color_val}]`).prop('checked', true);

        });
    </script>
	<script>
		$('#salary-tab').on('click', function() {
			// Affiche Div 1 et cache les autres
			$('#salary').show();
			$('#bulletin, #commission, #impotcharges, #overtime, #autrepaie').hide();

			// Ajoute la classe "active" au lien
			$(this).addClass('active');

			// Supprime la classe "active" des autres liens
			$('#bulletin-tab, #commission-tab, #impotcharges-tab, #overtime-tab, #autrepaie-tab').removeClass('active');
		});
		$('#bulletin-tab').on('click', function() {
			$('#bulletin').show();
			$('#salary, #commission, #impotcharges, #overtime, #autrepaie').hide();

			$(this).addClass('active');
			$('#salary-tab, #commission-tab, #impotcharges-tab, #overtime-tab, #autrepaie-tab').removeClass('active');
		});
		$('#commission-tab').on('click', function() {
			$('#commission').show(); // Affiche Div 3
			$('#salary, #bulletin, #impotcharges, #overtime, #autrepaie').hide();

			$(this).addClass('active');
			$('#salary-tab, #bulletin-tab, #impotcharges-tab, #overtime-tab, #autrepaie-tab').removeClass('active');
        });
		$('#impotcharges-tab').on('click', function() {
			$('#impotcharges').show(); // Affiche Div 4
			$('#salary, #bulletin, #commission, #overtime, #autrepaie').hide();

			$(this).addClass('active');
			$('#salary-tab, #bulletin-tab, #commission-tab, #overtime-tab, #autrepaie-tab').removeClass('active');
        });
		$('#overtime-tab').on('click', function() {
			$('#overtime').show(); // Affiche Div 5
			$('#salary, #bulletin, #commission, #impotcharges, #autrepaie').hide();

			$(this).addClass('active');
			$('#salary-tab, #bulletin-tab, #impotcharges-tab, #commission-tab, #autrepaie-tab').removeClass('active');
        });
		$('#autrepaie-tab').on('click', function() {
			$('#autrepaie').show(); // Affiche Div 6
			$('#salary, #bulletin, #commission, #impotcharges, #overtime').hide();

			$(this).addClass('active');
			$('#salary-tab, #bulletin-tab, #impotcharges-tab, #overtime-tab, #commission-tab').removeClass('active');
        });

	</script>
@endpush
@section('content')
    <div class="row">
		@php
			// VÃ©rifier que la collection des employÃ©s prÃ©cÃ©dents n'est pas vide
			if ($employeeprevious->isNotEmpty()) {
				// Trouver l'index de l'employÃ© actuel
				$currentEmployeeIndex2 = $employeeprevious->search(function ($emp) use ($employee) {
					return $emp->id === $employee->id;
				});

				// VÃ©rifier que l'employÃ© actuel a Ã©tÃ© trouvÃ©
				if ($currentEmployeeIndex2 !== false) {
					// DÃ©terminer l'index du prÃ©cÃ©dent employÃ©
					$previousEmployeeIndex = ($currentEmployeeIndex2 - 1 + $employeeprevious->count()) % $employeeprevious->count();
					$previousEmployee = $employeeprevious[$previousEmployeeIndex];
				} else {
					$previousEmployee = null; // L'employÃ© actuel n'a pas Ã©tÃ© trouvÃ©
				}
			} else {
				$previousEmployee = null; // La liste des employÃ©s prÃ©cÃ©dents est vide
			}
		@endphp
		<div class="col-3" align="left" style="margin-bottom: 10px;">
			<div class="row" style="justify-content: end;">
				<div class="col-12">
					<a href="{{ route('setsalary.show', ['eid' => $previousEmployee->id, 'monthpaie' => $monthpaie]) }}" class="btn btn-sm btn-info" style="justify-content: end;"><i class="ti ti-arrow-left"></i> {{ __('EmployÃ© PrÃ©cÃ©dent') }}</a>
				</div>
			</div>
		</div>
		<div class="col-6" align="center" style="margin-bottom: 10px;">
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
				<img src="{{ asset(Storage::url('uploads/avatar')) }}/avatar.png" class="rounded-circle" style="width: 10%;">
			@endif
			<p></p>
			<h6>{{$employee->name}}</h6>
			<div class="alert alert-success" role="alert">
				<h5><strong>Traitement de paie : {{$date->formatLocalized('%B %Y')}}</strong></h5>
				<p>Une fois que la configuration est terminÃ©e, cliquez sur "Liste salariÃ©s".</p>
				<a href="{{ route('setsalary.index', ['monthpaie' => $monthpaie]) }}" class="btn btn-sm btn-warning" style="justify-content: end;">{{ __('Liste salariÃ©s') }} <i class="ti ti-note"></i></a>
			</div>
        </div>
		@php
			// Trouver l'index de l'employÃ© actuel
			$currentEmployeeIndex = $employeenext->search(function ($emp) use ($employee) {
				return $emp->id === $employee->id;
			});

			// DÃ©terminer l'index du prochain employÃ©
			$nextEmployeeIndex = ($currentEmployeeIndex + 1) % $employeenext->count();
			$nextEmployee = $employeenext[$nextEmployeeIndex];
		@endphp
		<div class="col-3" align="right" style="margin-bottom: 10px;">
			<div class="row" style="justify-content: end;">
				<div class="col-12">
					<a href="{{ route('setsalary.show', ['eid' => $nextEmployee->id, 'monthpaie' => $monthpaie]) }}" class="btn btn-sm btn-info" style="justify-content: end;">{{ __('EmployÃ© Suivant') }} <i class="ti ti-arrow-right"></i></a>
				</div>
			</div>
		</div>
        <hr>
        <div class="col-12">
			<div class="row">
				<div class="row" style="align-items: center;">
					<div class="col-xl-2">
						<div class="card sticky-top">
							<div class="list-group list-group-flush" id="useradd-sidenav">
								<a href="#" id="salary-tab"
									class="list-group-item list-group-item-action border-0 active">{{ __('Salaire Brut') }}
									<div class="float-end"><i class="ti ti-chevron-right"></i></div>
								</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3">
						<div class="card sticky-top">
							<div class="list-group list-group-flush" id="useradd-sidenav">
								<a href="#" id="overtime-tab"
									class="list-group-item list-group-item-action border-0">{{ __('Heures Supp & Absences') }}
									<div class="float-end"><i class="ti ti-chevron-right"></i></div>
								</a>
							</div>
						</div>
					</div>
					<div class="col-xl-2">
						<div class="card sticky-top">
							<div class="list-group list-group-flush" id="useradd-sidenav">
								<a href="#" id="impotcharges-tab"
									class="list-group-item list-group-item-action border-0">{{ __('Retenues') }}
									<div class="float-end"><i class="ti ti-chevron-right"></i></div>
								</a>
							</div>
						</div>
					</div>
					<div class="col-xl-3">
						<div class="card sticky-top">
							<div class="list-group list-group-flush" id="useradd-sidenav">
								<a href="#" id="autrepaie-tab"
									class="list-group-item list-group-item-action border-0">{{ __('Charges Employeur') }}
									<div class="float-end"><i class="ti ti-chevron-right"></i></div>
								</a>
							</div>
						</div>
					</div>
					<div class="col-xl-2">
						<div class="card sticky-top">
							<div class="list-group list-group-flush" id="useradd-sidenav">
								<a href="#" id="bulletin-tab"
									class="list-group-item list-group-item-action border-0">{{ __('AperÃ§u bulletin') }}
									<div class="float-end"><i class="ti ti-chevron-right"></i></div>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-12">
					<!--salary-->
					<div id="salary" class=" " style="display: block;">
						<div class="card">
							<div class="card-header">
								<div class="row">
									<div class="col-4">
										<h5>{{ $employee->salary_type() }}</h5>
									</div>
									<div class="col-4" align="center">
										<h6>DerniÃ¨re mise Ã  jour du salaire : <strong style="color: red;">{{ Auth::user()->dateFormat($employee->updated_at) }}</strong></h6>
									</div>
									<div class="col-4 text-end">
										<!--@can('Create Set Salary')
											<b>Ajouter le salaire de base </b>
											<a data-url="{{ route('employee.basic.salary', $employee->id) }}" data-ajax-popup="true"
												data-title="{{ __('Set Basic Salary') }}" data-bs-toggle="tooltip" title=""
												class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Modifier le salaire de base') }}">
												<i class="ti ti-plus"></i>
											</a>
										@endcan -->
										<b>&nbsp;</b>
										@can('Create Allowance')
											<a data-url="{{ route('allowances.create', $employee->id) }}" data-ajax-popup="true"
												data-title="{{ __('CrÃ©er une rubrique') }}" data-bs-toggle="tooltip" title=""
												class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}"><span  style="color:#FFF">Ajouter les Ã©lÃ©ments du brut</span>
												<i class="ti ti-plus"></i>
											</a>
										@endcan
									</div>
								</div>
							</div>
							<div class="card-body">
								<div class="row project-info d-flex text-sm">
									<div class="project-info-inner mr-3 col-3">
										<div class="project-amnt pt-1"><b class="m-0">
											@php
												$salaireDeBaseAffiche = false;
											@endphp

											@foreach ($allowances as $allowance)
												@if($allowance->amount >= 0)
													@if($allowance->allowance_option == '27' || $allowance->allowance_option == '28')
														{{ __('Prime de stage') }}
													@elseif(!$salaireDeBaseAffiche)
														{{ __('Salaire de base') }}
														@php
															$salaireDeBaseAffiche = true;
														@endphp
													@endif
												@endif
											@endforeach
										</b></div>
									</div>
									<div class="project-info-inner mr-3 col-3" align="center">
										<div class="project-amnt pt-1"><b class="m-0">Nombre de jours travaillÃ©s : <strong>{{$employee->tax_payer_id}}</strong></b></div>
									</div>
									<div class="project-info-inner mr-3 col-1">
										<div class="action-btn bg-warning ms-2">
										<a data-url="{{ route('employee.basic.salary', $employee->id) }}" data-ajax-popup="true"
												class="mx-3 btn btn-sm  align-items-center" data-bs-toggle="tooltip"
												title="" data-bs-original-title="{{ __('View') }}">
												<i class="ti ti-eye text-white"></i>
											</a>
										</div>
									</div>
									<div class="project-info-inner mr-3 col-3">
										<div class="project-amnt pt-1" align="right">
											<b>
												@if($employee->tax_payer_id == '30')
													{{ number_format($employee->salary, 0 , '.' , ' ') }} FCFA
												@else
													{{ number_format($employee->branch_location, 0 , '.' , ' ') }} FCFA
												@endif
                                            </b>
                                        </div>
									</div>
									<div class="project-info-inner mr-3 col-2">
									</div>
								</div>
								<hr/>
                                @if($date_pa < 2)

                                @else
                                    <div id="divancien" class="row project-info d-flex text-sm">
                                        <div class="project-info-inner mr-3 col-3">
                                            <div class="project-amnt pt-1"><b class="m-0">Prime d'anciennetÃ©</b></div>
                                        </div>
                                        <div class="project-info-inner mr-3 col-4">
                                            <div class="project-amnt pt-1"><b class="m-0">2% aprÃ¨s 2 annÃ©es d'anciennetÃ© et 1% de salaire par annÃ©e de service jusqu'a la 25ieme annÃ©es</b></div>
                                        </div>
                                        <div class="project-info-inner mr-3 col-3">
                                            <div class="project-amnt pt-1" align="right"><b><span id="primeancien"></span></b></div>
                                        </div>
                                        <div class="project-info-inner mr-3 col-2" align="right">
											@foreach ($allowances as $allowance)
												@if($allowance->amount >= '0')
													@if($allowance->allowance_option == '4')
													<span>
														@can('Edit Allowance')
															<div class="action-btn bg-info ms-2">
																<a class="mx-3 btn btn-sm  align-items-center"
																	data-url="{{ URL::to('allowance/' . $allowance->id . '/edit') }}"
																	data-ajax-popup="true" data-size="md"
																	data-bs-toggle="tooltip" title=""
																	data-title="{{ __('Edit Allowance') }}"
																	data-bs-original-title="{{ __('Edit') }}">
																	<i class="ti ti-pencil text-white"></i>
																</a>
															</div>
														@endcan
														@can('Delete Allowance')
															<div class="action-btn bg-danger ms-2">
																{!! Form::open([
																	'method' => 'DELETE',
																	'route' => ['allowance.destroy', $allowance->id],
																	'id' => 'delete-form-' . $allowance->id,
																]) !!}
																<a class="mx-3 btn btn-sm  align-items-center bs-pass-para"
																	data-bs-toggle="tooltip" title=""
																	data-bs-original-title="Delete" aria-label="Delete"><i
																		class="ti ti-trash text-white text-white"></i></a>
																</form>
															</div>
														@endcan
													</span>
													@endif
												@endif
											@endforeach
                                        </div>
                                    </div>
                                    <hr>
                                @endif
								<div class="row project-info d-flex text-sm">
									@foreach ($allowances as $allowance)
										@if($allowance->amount >= '0')
                                            @if($allowance->allowance_option != '4')
												<div class="project-info-inner mr-3 col-3">
													@if($allowance->allowance_option != '30')
														<div class="project-amnt pt-1"><b class="m-0">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }} </b></div>
													@else
														<div class="project-amnt pt-1"><b class="m-0">{{ $allowance->title }}</b></div>
													@endif
												</div>
												<div class="project-info-inner mr-3 col-4">
													<div class="project-amnt pt-1"><b class="m-0">{{$allowance->details}}</b></div>
												</div>
												<!--<div class="project-info-inner mr-3 col-2">
													<div class="project-amnt pt-1"><b class="m-0"> 30 <=> 173,33 heures </b></div>
												</div>-->
												<div class="project-info-inner mr-3 col-3">
													@if ($allowance->type == 'fixed')
														<div class="project-amnt pt-1" align="right">
															<b>{{ number_format($allowance->amount, 0 , '.' , ' ') }} FCFA </b>
														</div>
													@else
														<div class="project-amnt pt-1" align="right">
															<b>{{ number_format($allowance->tota_allow, 0 , '.' , ' ') }} FCFA</b>
														</div>
													@endif
												</div>
												<div class="project-info-inner mr-3 col-2" align="right">
													<span>
														@can('Edit Allowance')
															<div class="action-btn bg-info ms-2">
																<a class="mx-3 btn btn-sm  align-items-center"
																	data-url="{{ URL::to('allowance/' . $allowance->id . '/edit') }}"
																	data-ajax-popup="true" data-size="md"
																	data-bs-toggle="tooltip" title=""
																	data-title="{{ __('Edit Allowance') }}"
																	data-bs-original-title="{{ __('Edit') }}">
																	<i class="ti ti-pencil text-white"></i>
																</a>
															</div>
														@endcan
														@can('Delete Allowance')
															<div class="action-btn bg-danger ms-2">
																{!! Form::open([
																	'method' => 'DELETE',
																	'route' => ['allowance.destroy', $allowance->id],
																	'id' => 'delete-form-' . $allowance->id,
																]) !!}
																<a class="mx-3 btn btn-sm  align-items-center bs-pass-para"
																	data-bs-toggle="tooltip" title=""
																	data-bs-original-title="Delete" aria-label="Delete"><i
																		class="ti ti-trash text-white text-white"></i></a>
																</form>
															</div>
														@endcan
													</span>
												</div>
												<hr/>
                                            @endif
										@endif
									@endforeach
								</div>
								<div class="row project-info d-flex text-sm">
									<?php
										$totoverstimes = 0;
										foreach ($overtimes as $overtime){
											$totoverstimes = $totoverstimes + $overtime->montant;
										}
									?>
									@if($overtimes->isEmpty())

									@else
										<div class="project-info-inner mr-3 col-6">
											<div class="project-amnt pt-1"><b class="m-0">Heures SupplÃ©mentaires</b></div>
										</div>
										<div class="project-info-inner mr-3 col-4">
											<div class="project-amnt pt-1" align="right">
												<b> {{number_format($totoverstimes, 0 ,'.',' ')}} FCFA
												</b>
											</div>
										</div>
                                        <hr/>
									@endif
								</div>
                                <div class="row project-info d-flex text-sm">
									@if($avtg_reat <= 0)

									@else
										<div class="project-info-inner mr-3 col-6">
											<div class="project-amnt pt-1"><b class="m-0">Avantages en Nature</b></div>
										</div>
										<div class="project-info-inner mr-3 col-4">
											<div class="project-amnt pt-1" align="right">
												<b> {{number_format($avtg_reat, 0 ,'.',' ')}} FCFA
												</b>
											</div>
										</div>
                                        <hr/>
									@endif
								</div>
                                @if($avtg_reat <= 0)
								<div class="row project-info d-flex text-sm">
									<div class="project-info-inner mr-3 col-6">
										<div class="project-amnt pt-1"><b class="m-0">Total brut :</b></div>
									</div>
									<div class="project-info-inner mr-3 col-4">
										<div class="project-amnt pt-1" align="right">
											<b>{{ number_format($employee->get_brut_salary(), 0 , '.' , ' ') }} FCFA</b>
										</div>
									</div>
								</div>
                                @else
                                <div class="row project-info d-flex text-sm">
									<div class="project-info-inner mr-3 col-6">
										<div class="project-amnt pt-1"><b class="m-0">Total brut sans avantages en nature :</b></div>
									</div>
									<div class="project-info-inner mr-3 col-4">
										<div class="project-amnt pt-1" align="right">
											<b>{{ number_format($employee->get_brut_salary()-$avtg_reat, 0 , '.' , ' ') }} FCFA</b>
										</div>
									</div>
								</div>
                                <hr>
                                <div class="row project-info d-flex text-sm">
									<div class="project-info-inner mr-3 col-6">
										<div class="project-amnt pt-1"><b class="m-0">Total brut avec avantages en nature :</b></div>
									</div>
									<div class="project-info-inner mr-3 col-4">
										<div class="project-amnt pt-1" align="right">
											<b>{{ number_format($employee->get_brut_salary(), 0 , '.' , ' ') }} FCFA</b>
										</div>
									</div>
								</div>
                                @endif
								<div class="project-amnt pt-3" align="right">Estimation du Net : <b style="background-color: #ddcd08;">{{ number_format($employee->get_net_salary(), 0 , '.' , ' ') }} FCFA</b></div>
							</div>
						</div>
					</div>
					<!-- bulletin -->
					<div id="bulletin" class=" " style="display: none;">
						<div class="card">
							<div class="card-header">
								<div class="row">
									<div class="col-11">
										<h5>Bulletin de paie</h5>
									</div>
									<!--<div  class="col-1 text-end">
										<div class="text-md-right mb-2">
											<a href="#" class="btn btn-warning"   data-bs-toggle="tooltip" data-bs-placement="bottom"
											title="{{ __('Download') }}" onclick="saveAsPDF()"><span class="fa fa-download"></span></a>
										</div>
									</div>-->
								</div>
							</div>
							<div class=" card-body table-border-style" style=" overflow:auto">
								<!--<div class="text-md-end mb-2">
									<a href="#" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
										title="{{ __('Download') }}" onclick="saveAsPDF()"><span class="fa fa-download"></span></a>

									@if (\Auth::user()->type == 'company' || \Auth::user()->type == 'hr')
										<a title="Mail Send" href="#"
											class="btn btn-sm btn-warning"><span class="fa fa-paper-plane"></span></a>
									@endif
								</div>-->
								<?php
									$real2 = 0; $bareme2 = 0; $avtg_reat_arg2 = 0; $avtg_bareme_arg2 = 0; $avtg_reat2 = 0; $avtg_bareme2 = 0;
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
								<div class="invoice" id="printableArea">
									<div class="row" align="center" style="padding:40px;">
										<table class="table-sm table-bordered" cellspacing="0" style="border-collapse:collapse" width = "100%">
											<tr style="height:40pt">
												<td class="border border-dark bg-primary" colspan ="9" align="center" style="vertical-align: middle;">
                                                @if(\Utility::getValByName('theme_color') == 'theme-9')
                                                    <strong style="color:#fff;" class="s3">BULLETIN DE PAIE</strong>
                                                @else
                                                    <strong style="color:#000;" class="s3">BULLETIN DE PAIE</strong>
                                                @endif
                                                </td>
											</tr>
											<tr>
												<td  width="50%" bgcolor="#C0C0C0" class="border border-dark" colspan ="4"><div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">EMPLOYEUR</b></div></td>
												<td  width="50%" bgcolor="#C0C0C0" class="border border-dark" colspan ="5"><div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">MATRICULE DU SALARIE:   {{ \Auth::user()->employeeIdFormat($employee['employee_id']) }}</b></div></td>
											</tr>
											<tr>
												<td class="border border-dark" colspan ="4">
                                                    <div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Nom :  {{ \Utility::getValByName('company_name') }}</b></div>
                                                    <div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Adresse :  {{ \Utility::getValByName('company_city') }}, {{ \Utility::getValByName('company_address') }}</b></div>
                                                    <div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">TÃ©lÃ©phone : {{ \Utility::getValByName('company_telephone') }}</b></div>
                                                    <div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Boite postale :  {{ \Utility::getValByName('company_zipcode') }}</b></div>
                                                    <div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">PÃ©riode :  {{$firstDay}} au {{$lastDay}}</b></div>
                                                    <div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Horaire mensuelle :  173,33</b></div>
                                                    <div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Nombre de jours travaillÃ©s : {{$employee->tax_payer_id}} </b></div>
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Grille salariale : <strong>{{ $sect }}</strong></b></div>
												</td>
												<td class="border border-dark" colspan ="5">
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Nom et PrÃ©nom :  {{ $employee->name }}</b></div>
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Adresse :  {{ $employee->address }}</b></div>
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Situation matrimoniale : <b>{{ $situation }}</b></div>
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Enfants Ã  charge : <b>{{ $employee->enfant }}</b></div>
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">NumÃ©ro CNPS :  {{ $employee->num_cnps }}</b></div>
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">AnciennetÃ© :  {{$date_pa}} an(s) et {{$date_m}} mois</b></div>
													@foreach($categorie as $cate)
														@if($employee->categorie==$cate->id)
															<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">CatÃ©gorie : {{$postevalue}} / {{$valueposte}}</b></div>
														@endif
													@endforeach
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Emploi :   {{ !empty(\Auth::user()->getDesignation($employee['designation_id'])) ? \Auth::user()->getDesignation($employee['designation_id'])->name : '-' }} </b></div>
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Tel / E-mail : {{$employee->phone}} / {{$employee->email}} </b></div>
													<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1">Nombre de parts : {{ $employee->parts }}</b></div>
												</td>
											</tr>
											<tr>
												<td bgcolor="#C0C0C0" class="border border-dark" rowspan="2" style="vertical-align: middle;"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">NÂ°</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark" rowspan="2" style="vertical-align: middle;"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">DÃ‰SIGNATION</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark" rowspan="2" style="vertical-align: middle;"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">NOMBRE</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark" rowspan="2" style="vertical-align: middle;"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">BASE</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark" colspan="3" style="vertical-align: middle;"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">PART SALARIALE</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark" colspan="2" style="vertical-align: middle;"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">PART PATRONALE</b></div></td>
											</tr>
											<tr>
												<td class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">TAUX</b></div></td>
												<td class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">GAIN</b></div></td>
												<td class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">RETENUES</b></div></td>
												<td class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">TAUX</b></div></td>
												<td class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">MONTANT</b></div></td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-0"><?php $code = 101; echo $code-1; ?></b></div>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1"><b class="m-0">Salaire de base</b></div></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-0">{{$employee->tax_payer_id}}</b></div></td>
												@if($employee->tax_payer_id == '30')
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-1">{{ number_format($employee->salary, 0 , '.' , ' ') }}</b></div></td>
												@else
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-1">{{ number_format($employee->branch_location, 0 , '.' , ' ') }}</b></div></td>
												@endif

													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div></td>

												@if($employee->tax_payer_id == '30')
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-1">{{ number_format($employee->salary, 0 , '.' , ' ') }}</b></div></td>
												@else
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-1">{{ number_format($employee->branch_location, 0 , '.' , ' ') }}</b></div></td>
												@endif
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div></td>
											</tr>
                                            @foreach ($allowances as $allowance)
                                                @if($allowance->amount > '0')
                                                    <tr>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$allowance->code}}</b></div>
                                                            @elseif($allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$allowance->code}}</b></div>
                                                            @endif
                                                            @if($allowance->allowance_option == '11')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$allowance->code}}</b></div>
                                                            @endif
                                                            <?php $code = ($allowance->code+1);?>
                                                        </td>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1"><b class="m-0">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }} </b></div>
                                                            @elseif($allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1"><b class="m-0">{{$allowance->title}}</b></div>
                                                            @endif
                                                            @if($allowance->allowance_option == '11')
                                                                <div class="project-amnt pt-1"><b class="m-0">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}</b></div>
                                                            @endif
                                                        </td>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            @if($allowance->allowance_option == '1')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                                @elseif($allowance->allowance_option == '2')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                                @elseif($allowance->allowance_option == '4')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                                @elseif($allowance->allowance_option == '10')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                                @elseif($allowance->allowance_option == '7')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                                @elseif($allowance->allowance_option == '11')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$employee->tax_payer_id}}</b></div>
                                                                @elseif($allowance->allowance_option == '12')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                                @elseif($allowance->allowance_option == '13')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                                @elseif($allowance->allowance_option == '17')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                                @elseif($allowance->allowance_option == '26')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$employee->enfant}}</b></div>
                                                                @else
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
                                                            @endif
                                                        </td>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            @if($allowance->allowance_option == '1')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                                @elseif($allowance->allowance_option == '2')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                                @elseif($allowance->allowance_option == '4')
                                                                    @if($employee->tax_payer_id == '30')
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-1">{{ number_format($employee->salary, 0 , '.' , ' ') }}</b></div>
                                                                    @else
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-1">{{ number_format($employee->branch_location, 0 , '.' , ' ') }}</b></div>
                                                                    @endif
                                                                @elseif($allowance->allowance_option == '10')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{round(75000/173.33)}}</b></div>
                                                                @elseif($allowance->allowance_option == '7')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{round(75000/173.33)}}</b></div>
                                                                @elseif($allowance->allowance_option == '11')
                                                                    @if(\Utility::getValByName('company_city') =='ABIDJAN' || \Utility::getValByName('company_city') == 'Abidjan' || \Utility::getValByName('company_city') =='abidjan' || strpos(\Utility::getValByName('company_city'), 'Abidjan') !== false)
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(30000, 0 , '.' , ' ')}}</b></div>
                                                                        @elseif (\Utility::getValByName('company_city')=='BouakÃ©' || \Utility::getValByName('company_city')=='BOUAKE' || \Utility::getValByName('company_city')=='bouakÃ©')
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round(24000), 0 , '.' , ' ')}}</b></div>
                                                                        @else
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round(22000), 0 , '.' , ' ')}}</b></div>
                                                                    @endif
                                                                @elseif($allowance->allowance_option == '12')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{round(75000/173.33)}}</b></div>
                                                                @elseif($allowance->allowance_option == '13')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{round(75000/173.33)}}</b></div>
                                                                @elseif($allowance->allowance_option == '17')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                                @elseif($allowance->allowance_option == '26')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                                @else
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                            @endif
                                                        </td>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            @if($allowance->allowance_option == '1')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                                @elseif($allowance->allowance_option == '2')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                                @elseif($allowance->allowance_option == '4')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{$date_pa}}</b></div>
                                                                @elseif($allowance->allowance_option == '10')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{3}}</b></div>
                                                                @elseif($allowance->allowance_option == '7')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{13}}</b></div>
                                                                @elseif($allowance->allowance_option == '11')
                                                                    @if(\Utility::getValByName('company_city') =='Abidjan' || \Utility::getValByName('company_city') =='ABIDJAN' || \Utility::getValByName('company_city') =='abidjan' || strpos(\Utility::getValByName('company_city'), 'Abidjan') !== false)
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(30000/30, 0 , '.' , ' ')}}</b></div>
                                                                        @elseif (\Utility::getValByName('company_city')=='BouakÃ©' || \Utility::getValByName('company_city')=='BOUAKE' || \Utility::getValByName('company_city')=='bouakÃ©')
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round(24000/30), 0 , '.' , ' ')}}</b></div>
                                                                        @else
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round(22000/30), 0 , '.' , ' ')}}</b></div>
                                                                    @endif
                                                                @elseif($allowance->allowance_option == '12')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{10}}</b></div>
                                                                @elseif($allowance->allowance_option == '13')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{7}}</b></div>
                                                                @elseif($allowance->allowance_option == '17')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                                @elseif($allowance->allowance_option == '26')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0">{{1500}}</b></div>
                                                                @else
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                            @endif
                                                        </td>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            <div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round($allowance->amount), 0 , '.' , ' ')}}</b></div>
                                                        </td>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            @elseif($allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            @endif
                                                            @if($allowance->allowance_option == '11')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                            @endif
                                                        </td>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            @elseif($allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            @endif
                                                            @if($allowance->allowance_option == '11')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                            @endif
                                                        </td>
                                                        <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                            @if($allowance->allowance_option != '30' && $allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            @elseif($allowance->allowance_option != '11')
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            @endif
                                                            @if($allowance->allowance_option == '11')
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo $code++; ?></b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1"><b class="m-0">Heures SupplÃ©mentaires</b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<?php
														$totoverstimes = 0;
														foreach ($overtimes as $overtime){
															$totoverstimes = $totoverstimes + $overtime->montant;
														}
													?>
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($totoverstimes, 0 , '.' , ' ')}}</b></div>
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
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<?php
														$totoverstimes = 0;
														foreach ($overtimes as $overtime){
															$totoverstimes = $totoverstimes + $overtime->montant;
														}
													?>
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($totoverstimes, 0 , '.' , ' ')}}</b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($overtimes->isEmpty())

													@else
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													@endif
												</td>
											</tr>
											@if($avantages->isEmpty())

											@else
												<tr>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1" align="right"><b class="m-0"><?php $num_atg = 0; echo $code++; $num_atg = $code;?></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"><b class="m-0">Avantages en nature et en argent</b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1" align="right"><b class="m-0">{{$jours_work}}</b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"  align="right"><b class="m-0"><br></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($avtg_reat2, 0 , '.' , ' ')}}</b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													</td>
												</tr>
											@endif
                                            @if($termination->isEmpty())

                                            @else
                                                @foreach ($termination as $termine)
                                                    @if($termine->indem_comp > 0)
                                                        <tr>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo $code++; ?></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0">IndemnitÃ© de Gratification</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->indem_comp, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->indem_comp, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($termine->indem_comp_cong > 0)
                                                        <tr>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo $code++; ?></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0">Allocution congÃ©</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->indem_comp_cong, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->indem_comp_cong, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($termine->imdem_prea > 0)
                                                        <tr>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo $code++; ?></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0">IndemnitÃ© de prÃ©avis</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->imdem_prea, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->imdem_prea, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($termine->indem_licence > 0)
                                                        <tr>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo $code++; ?></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0">IndemnitÃ© de licenciement</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->indem_licence, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->indem_licence, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($termine->aggravation > 0)
                                                        <tr>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo $code++; ?></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0">Dommages et intÃ©rÃªts</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->aggravation, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->aggravation, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($termine->dom_inter > 0)
                                                        <tr>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo $code++; ?></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0">Dommages et intÃ©rÃªts</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->dom_inter, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"  align="right"><b class="m-0">{{number_format($termine->dom_inter, 0 , '.' , ' ')}}</b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                            <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                                <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="center"><b class="m-0">Total Brut</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="right"><b class="m-0">{{number_format(round($employee->get_brut_salary()), 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="center"><b class="m-0"><br></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">401</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">ImpÃ´ts bruts avant RICF</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"></b><br/></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($resultimpricf, 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">		<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">402</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">RÃ©duction pour Charges de Famille</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{$employee->parts}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($resultricf, 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                	<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">403</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">ImpÃ´ts Nets</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                    <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">

												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                    <div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($impots, 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">301</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">Cotisation Retraite CNPS</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($employee->get_salary_social(), 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">		<div class="project-amnt pt-1" align="right"><b class="m-0">6,3</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">

												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">		<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round($cnps), 0 , '.' , ' ')}}</b></div></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">7,70</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{$cnpsemp}}</b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">302</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">Couverture Maladie Universelle</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{$employee->cmu}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(1000, 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">		<div class="project-amnt pt-1" align="right"><b class="m-0">0,50</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($resultcmu, 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">0,50</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{$coticmuemp}}</b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">409</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">Contribution Employeur </b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">		<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">1,20</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{$resulttax1}}</b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">410</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">Contribution employeur (ExpatriÃ©)</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($local=='local')
														<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(0, 0 , '.' , ' ')}}</b></div>
													@else
														<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}</b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($local=='local')
														<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
													@else
														<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($local=='local')
														<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
													@else
														<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($local=='local')
														<div class="project-amnt pt-1" align="right"><b class="m-0">0</b></div>
													@else
														<div class="project-amnt pt-1" align="right"><b class="m-0">9,20</b></div>
													@endif
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													@if($local=='local')
														<div class="project-amnt pt-1" align="right"><b class="m-0">0</b></div>
													@else
														<div class="project-amnt pt-1" align="right"><b class="m-0">{{$resulttax2}}</b></div>
													@endif
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">411</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">Taxe dâ€™Apprentissage </b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">0,40</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{round($tax4)}}</b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">412</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">Taxe.F.P.C</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($employee->get_salary_imposable(), 0 , '.' , ' ')}}</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                    <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">1,20</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{round($tax5)}}</b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">305</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">Accident de travail</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">75 000</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                    <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{$prt}},00</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{$tauxact}}</b></div>
												</td>
											</tr>
											<tr>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">306</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1"><b class="m-0">Prestation Familiale</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">75 000</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
                                                    <div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">5,75</b></div>
												</td>
												<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
													<div class="project-amnt pt-1" align="right"><b class="m-0">{{round($pf)}}</b></div>
												</td>
											</tr>
											@foreach ($allowances as $allowance)
                                                @if($allowance->amount > '0')
													@if($allowance->allowance_option == '31')
														<tr>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
																<div class="project-amnt pt-1" align="right"><b class="m-0">114</b></div>
															</td>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
																<div class="project-amnt pt-1"><b class="m-0">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}</b></div>
															</td>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
																<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
															</td>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
																<div class="project-amnt pt-1" align="right"><b class="m-0"></b></div>
															</td>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
																<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
															</td>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
																<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
															</td>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"><div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round($allowance->amount), 0 , '.' , ' ')}}</b></div></td>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
																<div class="project-amnt pt-1" align="right"><b class="m-0"></b></div>
															</td>
															<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
																<div class="project-amnt pt-1" align="right"><b class="m-0"></b></div>
															</td>
														</tr>
													@endif
												@endif
											@endforeach
                                            @if(isset($payslip->loan) && ($payslip->loan > 0))
												<tr>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="right"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="center"><b class="m-0">Total Cotisations</b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1" align="right"><b class="m-0">
                                                            {{number_format(round($totalimpots+$total_cantine), 0 , '.' , ' ')}}
                                                        </b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round($parpatronal), 0 , '.' , ' ')}}</b></div>
													</td>
												</tr>
											@else
												<tr>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="right"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="center"><b class="m-0">Total Cotisations</b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1" align="right"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1" align="right"><b class="m-0">
                                                            {{number_format(round($totalimpots+$total_cantine), 0 , '.' , ' ')}}
                                                        </b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"align="center"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format(round($parpatronal), 0 , '.' , ' ')}}</b></div>
													</td>
												</tr>
											@endif
                                            @if($avantages->isEmpty())

											@else
												<tr>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo ($num_atg-1); ?></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"><b class="m-0">Avantages en nature et en argent</b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"><b class="m-0"></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"  align="right"><b class="m-0"><br></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1"  align="right"><b class="m-0"></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: white;">
														<div class="project-amnt pt-1" align="right"><b class="m-0">{{number_format($avtg_reat2, 0 , '.' , ' ')}}</b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													</td>
													<td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;">
														<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
													</td>
												</tr>
											@endif
                                            @if(isset($payslip->loan) && ($payslip->loan > 0))
                                            <tr>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;" align="right" >
                                                    <div class="project-amnt pt-1" align="right"><b class="m-0"><?php echo $code++; ?></b></div>
                                                </td>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"><div class="project-amnt pt-1" align="left"><b class="m-0">PrÃªts </b></div></td>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"><p></p></td>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;" align="right"><div class="project-amnt pt-1" align="right"><b class="m-0">{{$payslip->loan}}</b></div></td>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"></td>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"><p> </p></td>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;" align="right"><div class="project-amnt pt-1" align="right"><b class="m-0">{{$payslip->saturation_deduction}}</b></div></td>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"><p></p></td>
                                                <td style="border-left-color: black; border-right-color: black; border-bottom-color: black; border-top-color: black;"><p></p></td>
                                            </tr>
                                            @else

                                            @endif
											<tr>
												<td class="border border-dark" style="border-top-color: black;" colspan="9">
													<div class="project-amnt pt-1" align="" style="color:#000;">
                                                        @foreach ($paytype as $type)
                                                            @if($type->id == $employee->paytype)
                                                                <b><i>PayÃ© par : {{ $type->name }} </i></b>
                                                            @endif
                                                        @endforeach
													</div>
												</td>
											</tr>
											<tr>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">Cumuls</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">Salaire brut</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">Charges salariales</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">Charges patronales</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">Avantages en nature</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">Net imposable</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">Heures travaillÃ©es</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">Heures<br/>supplÃ©mentaires</b></div></td>
												<td bgcolor="#C0C0C0" class="border border-dark"><div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">NET A PAYER</b></div></td>
											</tr>
											<tr>
												<td class="border border-dark">
													<div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">PÃ©riode</b></div><hr/>
													<div class="project-amnt pt-1" align="center" style="color:#000;"><b class="m-1">AnnÃ©e</b></div></td>
												<td class="border border-dark">
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{number_format($employee->get_brut_salary(), 0 ,'.',' ')}}</b></div><hr/>
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{number_format($employee->getTotalSalarybrut(), 0 ,'.',' ')}}</b></div></td>
												<td class="border border-dark">
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{$totalimpots+$total_cantine}}</b></div><hr/>
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{number_format($employee->getTotalSalaryRetenue(), 0 ,'.',' ')}}</b></div></td>
												<td class="border border-dark">
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{round($parpatronal)}}</b></div><hr/>
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{number_format($employee->getTotalSalaryPatronale(), 0 ,'.',' ')}}</b></div></td>
												<td class="border border-dark">
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{number_format($avtg_reat2, 0 , '.' , ' ')}}</b></div><hr/>
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{number_format(($compte*$avtg_reat2), 0 , '.' , ' ')}}</b></div></td>
												<td class="border border-dark">
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{number_format($employee->get_salary_imposable(), 0 ,'.',' ')}}</b></div><hr/>
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{number_format($employee->getTotalSalaryImposable(), 0 ,'.',' ')}}</b></div></td>
												<td class="border border-dark">
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">173,33</b></div><hr/>
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{round(173.33*$compte)}}</b></div></td>
												<td class="border border-dark">
													<div class="project-amnt pt-1" align="right" style="color:#000;"><b class="m-1">{{$totoverstimes}}<hr/>{{($totoverstimes)}}</b></div></td>
												<td style="vertical-align:middle; border-top-style:solid;border-top-width:3pt;border-left-style:solid;border-left-width:3pt;border-bottom-style:solid;border-bottom-width:3pt;border-right-style:solid;border-right-width:3pt; border-right-color: #000; border-left-color: #000; border-top-color: #000; border-bottom-color: #000">
													<div class="project-amnt pt-1" align="center" style="color:#000;">
                                                        @if(isset($payslip) && ($payslip->loan > 0))
														    <b class="m-1">{{number_format(($employee->get_net_salary())-($payslip->saturation_deduction), 0 ,'.',' ')}}</b>
                                                        @else
                                                            {{number_format(($employee->get_net_salary()), 0 ,'.',' ')}}
                                                        @endif
													</div>
												</td>
											</tr>
										</table>
										<div class="project-amnt pt-1" align="left" style="color:#000;"><b class="m-1"><i> Pour vous aider Ã  faire valoir vos droits, conservez ce bulletin de paie sans limitation de durÃ©e.</i></b></div>
										<div class="project-amnt pt-1" align="right" style="color:#000;"><u><strong> LA DIRECTION </storng></u></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Commission -->
					<div id="commission" class="" style="display: none;">
						<div class="card set-card">
							<div class="card-header">
								<div class="row">
									<div class="col-11">
										<h5></h5>
									</div>
									@can('Create Commission')
										<div class="col-1 text-end">
											<a data-url="{{ route('commissions.create', $employee->id) }}" data-ajax-popup="true"
												data-title="{{ __('Create Commission') }}" data-bs-toggle="tooltip" title=""
												class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
												<i class="ti ti-plus"></i>
											</a>

										</div>
									@endcan
								</div>
							</div>
							<div class=" card-body table-border-style" style=" overflow:auto">
								<div class="table-responsive">
									<table class="table">
										<thead>

											<tr>
												<th>{{ __('Employee Name') }}</th>
												<th>{{ __('Title') }}</th>
												<th>{{ __('Type') }}</th>
												<th>{{ __('Amount') }}</th>
												@if (\Auth::user()->type != 'employee')
													<th>{{ __('Action') }}</th>
												@endif
											</tr>
										</thead>
										<tbody>
											@foreach ($commissions as $commission)
												<tr>
													<td>{{ !empty($commission->employee()) ? $commission->employee()->name : '' }}
													</td>
													<td>{{ $commission->title }}</td>

													<td>{{ ucfirst($commission->type) }}</td>
													@if ($commission->type == 'fixed')
														<td>{{ number_format($commission->amount, 0 , '.' , ' ') }} FCFA</td>
													@else
														<td>{{ $commission->amount }}%
															({{ number_format($commission->tota_allow, 0 , '.' , ' ') }}) FCFA
														</td>
													@endif

													<td class="Action">
														<span>
															@can('Edit Commission')
																<div class="action-btn bg-info ms-2">
																	<a class="mx-3 btn btn-sm  align-items-center"
																		data-url="{{ URL::to('commission/' . $commission->id . '/edit') }}"
																		data-ajax-popup="true" data-size="md"
																		data-bs-toggle="tooltip" title=""
																		data-title="{{ __('Edit Commission') }}"
																		data-bs-original-title="{{ __('Edit') }}">
																		<i class="ti ti-pencil text-white"></i>
																	</a>
																</div>
															@endcan
															@can('Delete Commission')
																<div class="action-btn bg-danger ms-2">
																	{!! Form::open([
																		'method' => 'DELETE',
																		'route' => ['commission.destroy', $commission->id],
																		'id' => 'delete-form-' . $commission->id,
																	]) !!}
																	<a class="mx-3 btn btn-sm  align-items-center bs-pass-para"
																		data-bs-toggle="tooltip" title=""
																		data-bs-original-title="Delete" aria-label="Delete"><i
																			class="ti ti-trash text-white text-white"></i></a>
																	</form>
																</div>
															@endcan
														</span>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<!-- Saturation -->
					<div id="impotcharges" class=" " style="display: none;">
						<div class="card">
							<div class="card-header">
								<div class="row">
									<div class="col-11">
										<h5>Gestion des retenues</h5>
									</div>
									{{-- @can('Create Saturation Deduction')
										<div class="col-1 text-end">
											<a data-url="{{ route('saturationdeductions.create', $employee->id) }}"
												data-ajax-popup="true" data-size="lg"
												data-title="{{ __('Create Saturation Deduction') }}" data-bs-toggle="tooltip"
												title="" class="btn btn-sm btn-primary"
												data-bs-original-title="{{ __('Create') }}">
												<i class="ti ti-plus"></i>
											</a>
										</div>

									@endcan --}}
								</div>
							</div>
							<?php
								$prets=0;
								$ids=''; $text='';
								foreach ($loans as $loan){
									if ($loan->type == 'fixed'){
										$prets=$loan->amount;
									}else{
										$prets=$loan->tota_allow;
									}
									$ids = $loan->employee_id;
								}
								$mont_cant = 0;
								$total=0;
								$total= $resultimpricf + $resultcnps + $resultcmu + $prets;
							?>
							<div class=" card-body table-border-style" style=" overflow:auto">
								<nav>
                                @if(\Utility::getValByName('theme_color') == 'theme-9')
									<div class="nav nav-tabs" id="nav-tab" role="tablist">
										<button class="nav-link active bg-primary" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true"><strong style="color: darkorange;">SALAIRE BRUT IMPOSABLE (SBI) &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong></button>
										<button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false"><strong style="color: darkorange;">SALAIRE BRUT SOCIAL (SBS) &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong></button>
										<button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false"><strong style="color: darkorange;">PRETS EN COURS &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong></button>
										<button class="nav-link" id="nav-recap-tab" data-bs-toggle="tab" data-bs-target="#nav-recap" type="button" role="tab" aria-controls="nav-recap" aria-selected="false"><strong style="color: darkorange;">RECAPITULATIF DES RETENUES &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong></button>
									</div>
                                    @else
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
										<button class="nav-link active bg-primary" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true"><strong style="color: black;">SALAIRE BRUT IMPOSABLE (SBI) &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong></button>
										<button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false"><strong style="color: black;">SALAIRE BRUT SOCIAL (SBS) &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong></button>
										<button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false"><strong style="color: black;">PRETS EN COURS &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong></button>
										<button class="nav-link" id="nav-recap-tab" data-bs-toggle="tab" data-bs-target="#nav-recap" type="button" role="tab" aria-controls="nav-recap" aria-selected="false"><strong style="color: black;">RECAPITULATIF DES RETENUES &nbsp;<i class="fa fa-arrow-circle-right" aria-hidden="true"></i></strong></button>
									</div>
                                @endif
								</nav>
								<div class="tab-content" id="nav-tabContent">
									<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
										<br/>
										<div class="col-xl-12" style="color: #fff; padding:5px">
											<div class="card bg-dark shadow-none" style="padding:10px">
												<div class="row">
													<div class="col-xl-1"></div>
													<div class="col-xl-10">
														<div class="project-info-inner mr-3">
															<div class="project-amnt pt-1">
																<table  style="color: #fff;" width="100%"><tr>
																	<td align="center" width="50%"><b class="m-0"> <strong>SB Total : {{number_format($employee->get_brut_salary(), 0 ,'.',' ')}} FCFA</strong></b></td>
																	<td align="center" width="50%"><b class="m-0"><strong>SB Imposable : {{number_format($employee->get_salary_imposable(), 0 ,'.',' ')}} FCFA</strong></b></td>
																</tr>
															</table>
															</div><hr/>
														</div>
														<table class="table-sm table-bordered" style="color: #fff;" width="100%">
															<thead>
																<tr>
																	<td align="left" width="50%"><strong>Elements</strong></td>
																	<td colspan="2" align="center" width="25%"><strong>Montant</strong></td>
                                                                    <td align="center" width="25%"><strong>Ventilation</strong></td>
																</tr>
                                                                <tr>
																	<td align="left" width="50%" style="border-bottom-color:#1c232f;"><strong>Salaire brut</strong></td>
																	<td colspan="2" align="center" width="25%" style="border-bottom-color:#1c232f;"><strong><br></strong></td>
                                                                    <td align="left" width="25%" style="border-bottom-color:#1c232f;"><strong>Base 10%</strong></td>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td style="padding:5px;">
																		<div class="project-amnt pt-1">Salaire de base</div>
																		@foreach ($allowances as $allowance)
																			@if($allowance->allowance_option != '30')
																				<div class="project-amnt pt-1">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}</div>
																			@else
																				<div class="project-amnt pt-1">{{$allowance->title}}</div>
																			@endif
																		@endforeach
																		@if($overtimes->isEmpty())

																		@else
																			<div class="project-amnt pt-1">Heures SupplÃ©mentaires</div>
																		@endif
																		<div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;">Total Brut (A)</b></div>
																	</td>
																	<td style="padding:5px;border-right-color: #1c232f">
																		<div class="project-amnt pt-1" style="text-align: right;">
																			<b class="m-0">
																				@if($employee->tax_payer_id == '30')
																					{{ number_format($employee->salary, 0 , '.' , ' ') }}</b>
																				@else
																					{{ number_format($employee->branch_location, 0 , '.' , ' ') }}</b>
																				@endif
																			</b>
                                                                        </div>
																		@foreach ($allowances as $allowance)
																			<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($allowance->amount, 0 ,'.',' ')}}</b></div>
																		@endforeach
																		<?php
																			$totoverstimes = 0;
																			foreach ($overtimes as $overtime){
																				$totoverstimes = $totoverstimes + $overtime->montant;
																			}
																		?>
																		@if($overtimes->isEmpty())

																		@else
																			<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($totoverstimes, 0 ,'.',' ')}}</b></div>
																		@endif
                                                                        <div class="project-amnt pt-1" align="right">
                                                                            <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary() - $avtg_reat), 0 , '.' , ' ') }} </b>
																		</div>
																	</td>
																	<td style="padding:5px;">
																		<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0"><br/></b></div>
																		@foreach ($allowances as $allowance)
																			<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0"><br/></b></div>
																		@endforeach
																		<?php
																			$totoverstimes = 0;
																			foreach ($overtimes as $overtime){
																				$totoverstimes = $totoverstimes + $overtime->montant;
																			}
																		?>
																		@if($overtimes->isEmpty())

																		@else
																			<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0"><br/></b></div>
																		@endif
																	</td>
                                                                    <td style="padding:5px;">
																		<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">
																			@if($employee->tax_payer_id == '30')
																				{{ number_format($employee->salary, 0 , '.' , ' ') }}</b>
																			@else
																				{{ number_format($employee->branch_location, 0 , '.' , ' ') }}</b>
																			@endif
																		</b></div>
																		<?php $totalbase = 0; ?>
																		@foreach ($allowances as $allowance)
                                                                            @if($allowance->allowance_option == 26 || $allowance->allowance_option == 11)
                                                                                <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0"><br></b></div>
                                                                                <?php $totalbase += $allowance->amount; ?>
                                                                            @else
                                                                                <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($allowance->amount, 0 ,'.',' ')}}</b></div>
                                                                            @endif
																		@endforeach
																		<?php
																			$totoverstimes = 0;
																			foreach ($overtimes as $overtime){
																				$totoverstimes = $totoverstimes + $overtime->montant;
																			}
																		?>
																		@if($overtimes->isEmpty())

																		@else
																			<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($totoverstimes, 0 ,'.',' ')}}</b></div>
																		@endif
																		<div class="project-amnt pt-1" align="right">
                                                                            <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary()-($totalbase+$avtg_reat)), 0 , '.' , ' ') }} </b>
																		</div>
                                                                    </td>
																</tr>
                                                                <tr>
                                                                    <td style="padding:5px;">
                                                                        <div class="project-amnt pt-1"><b class="m-0">Avantages en Nature ou en Argent</b></div>
                                                                        <div class="project-amnt pt-1">Montant des avantages au barÃ¨me</div>
                                                                        <div class="project-amnt pt-1">Montant des autres avantages</div>
                                                                        <div class="project-amnt pt-1" align="left">Total des avantages</b></div>
                                                                    </td>
                                                                    <td style="padding:5px;border-right-color: #1c232f">
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0">Montant rÃ©el</b></div>
                                                                        <div class="project-amnt pt-1" align="right">{{number_format($real, 0 ,'.',' ')}}</div>
                                                                        <div class="project-amnt pt-1" align="right">{{number_format($avtg_reat_arg, 0 ,'.',' ')}}</div>
                                                                        <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format(($avtg_reat), 0 ,'.',' ')}}</b></div>
                                                                    </td>
                                                                    <td style="padding:5px;">

                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0">Montant au barÃ¨me</b></div>
                                                                        <div class="project-amnt pt-1" align="right">{{number_format($bareme, 0 ,'.',' ')}}</div>
                                                                        <div class="project-amnt pt-1" align="right">{{number_format($avtg_reat_arg2, 0 ,'.',' ')}}</div>
                                                                        <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">(B) {{number_format(($avtg_bareme + $avtg_reat_arg2), 0 ,'.',' ')}} </b></div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding:5px;">
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;">Total Brut avec avantages</b></div>
                                                                    </td>
                                                                    <td style="padding:5px;border-right-color: #1c232f">
                                                                        <div class="project-amnt pt-1" align="right">
                                                                            <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary()), 0 , '.' , ' ') }} </b>
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding:5px;"></td>
                                                                    <td style="padding:5px;"></td>
                                                                </tr>
																<tr>
																	<td style="padding:5px;">
                                                                        <div class="project-amnt pt-1"><b class="m-0">ExonÃ©rations</b></div>
																		@foreach ($allowances as $allowance)
																			@if($allowance->allowance_option != '30')
																				@if($allowance->trait_fisc =='' || $allowance->trait_fisc =='10% - Art 116-1')
																					@if ($allowance->allowance_option == 31)
																						<div class="project-amnt pt-1">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}</div>
																					@endif
																					@if ($allowance->allowance_option != 31)
																						<div class="project-amnt pt-1">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}</div>
																					@endif
																				@endif
																			@else
																				@if($allowance->trait_fisc =='' || $allowance->trait_fisc =='10% - Art 116-1')
																					<div class="project-amnt pt-1">{{$allowance->title}}</div>
																				@endif
																			@endif
																		@endforeach
																		<div class="project-amnt pt-1" align="left">Total des primes Ã  10%</div>
                                                                        <br>
                                                                        @foreach ($allowances as $allowance)
																			@if($allowance->allowance_option != '30')
																				@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																					<div class="project-amnt pt-1">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}</div>
																				@endif
																			@else
																				@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																					<div class="project-amnt pt-1">{{$allowance->title}}</div>
																				@endif
																			@endif
																		@endforeach
																		<div class="project-amnt pt-1" align="left">Total des primes exonÃ©rÃ©s Ã  100% dans la limite lÃ©gale</div>
                                                                        <br>
																	</td>
																	<td style="padding:5px;border-right-color: #1c232f">
                                                                        <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
																		@foreach ($allowances as $allowance)
																			@if($allowance->trait_fisc =='' || strpos($allowance->trait_fisc, '10% - Art 116-1') === 0)
																				@if ($allowance->allowance_option == 31)
																					@if ($allowance->amount > 30000)
                                                                                		<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($allowance->amount-30000, 0 ,'.',' ')}}</b></div>
																					@else
																						<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format(0, 0 ,'.',' ')}}</b></div>
																					@endif
																				@endif
																				@if ($allowance->allowance_option != 31)
																					<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($allowance->amount, 0 ,'.',' ')}}</b></div>
																				@endif
																			@endif

																		@endforeach
																		<?php
																			$totoezro=0;
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
																		<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($totoezro, 0 ,'.',' ')}}</b></div>
                                                                        <br>
                                                                        @foreach ($allowances as $allowance)
																			@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																				    <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($allowance->amount, 0 ,'.',' ')}}</b></div>
																			@endif
																		@endforeach
																		<?php
																			$total=0;
																			foreach ($allowances as $allowance){
																				if(strpos($allowance->trait_fisc, '100% - Art 116') === 0){
                                                                                    $total = $total + $allowance->amount;
																				}
																			}
																		?>
																	</td>
																	<td style="padding:5px;">
                                                                        <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
																		@foreach ($allowances as $allowance)
																			@if($allowance->trait_fisc =='' || $allowance->trait_fisc =='10% - Art 116-1')
																				<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
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
																		<div class="project-amnt pt-1" style="text-align: left;"><b class="m-0" style="color: #ddcd08; text-align: center;">{{number_format($resltexo, 0 ,'.',' ')}}</b></div>
                                                                        <br>
                                                                        @foreach ($allowances as $allowance)
																			@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																				<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
																			@endif
																		@endforeach
																		<?php
																			$total=0;
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
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;">{{number_format($total, 0 ,'.',' ')}}</b></div>
																	</td>
                                                                    <td style="padding:5px;">
                                                                        <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
																		@foreach ($allowances as $allowance)
																			@if($allowance->allowance_option != '30')
																				@if($allowance->trait_fisc =='' || $allowance->trait_fisc =='10% - Art 116-1')
																					<div class="project-amnt pt-1"><br></div>
																				@endif
																			@else
																				@if($allowance->trait_fisc =='' || $allowance->trait_fisc =='10% - Art 116-1')
																					<div class="project-amnt pt-1"><br></div>
																				@endif
																			@endif
																		@endforeach
																		<div class="project-amnt pt-1" align="left"><br></div>
                                                                        <br>
                                                                        @foreach ($allowances as $allowance)
																			@if($allowance->allowance_option != '30')
																				@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																					<div class="project-amnt pt-1"><br></div>
																				@endif
																			@else
																				@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																					<div class="project-amnt pt-1"><br></div>
																				@endif
																			@endif
																		@endforeach
																		<div class="project-amnt pt-1" align="left"><br></div>
                                                                        <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                                    </td>
																</tr>
                                                                <tr>
                                                                    <td style="border-top-color: #1c232f;">
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;">Total des ExonÃ©rations (C )</b></div>
                                                                    </td>
                                                                    <td style="border-right-color: #1c232f !important; border-top-color: #1c232f;">
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;"><br/></b></div>
                                                                    </td>
                                                                    <td></td>
                                                                    <td>
                                                                        <div class="project-amnt pt-1" align="right"><b class="m-0" style="color: #ddcd08;">{{number_format((($resltexo)+$total), 0 ,'.',' ')}}</b></div>
                                                                    </td>
                                                                </tr>
                                                                <tr bgcolor="#fff">
																	<td colspan="3" style="padding:5px;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;border-right-color: #1c232f"><div class="project-amnt pt-1" align="left"><b class="m-0"  style="color: #000;">Salaire brut imposable (SBI) [A+B-C]</b></div></td>
																	<td  style="padding:5px;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><div class="project-amnt pt-1" align="right"><b class="m-0" style="color: #000;">{{number_format((($employee->get_brut_salary()-$avtg_reat)+(($avtg_bareme+$avtg_reat_arg2)-(($resltexo)+$total))), 0 ,'.',' ')}}</b></div></td>
																</tr>
															</tbody>
														</table>
													</div>
													<div class="col-xl-1"></div>
												</div>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
										<div class="col-xl-12" style="color: #fff; padding:5px">
											<br/>
											<div class="card bg-dark shadow-none" style="padding:15px">
												<div class="row">
													<div class="project-info-inner mr-3">
														<table style="color: #fff;" width="100%">
															<tr>
																<td align="center" width="50%"><b class="m-0"> <strong>SB Total : {{number_format($employee->get_brut_salary(), 0 ,'.',' ')}} FCFA</strong></b></td>
																<td align="center" width="50%"><b class="m-0"><strong>SB Social : {{number_format($employee->get_salary_social(), 0 ,'.',' ')}} FCFA</strong></b></td>
															</tr>
														</table>
													</div>
													<hr/>
													<div class="col-xl-8">
														<table class="table-sm table-bordered" width="100%" style="color: #fff;">
															<thead>
																<tr>
																	<td align="letf" width="50%"><strong>ELEMENTS DU BRUT</strong></td>
																	<td align="left" colspan="2" width="25%" style="padding:5px;border-right-color: #1c232f"><strong>MONTANT</strong></td>
																	<td align="left" colspan="2" width="25%"></td>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td style="padding:5px;">
																		<div class="project-amnt pt-1"><b class="m-0">Salaire brut</b></div>
																		<div class="project-amnt pt-1">Salaire de base</div>
																		@foreach ($allowances as $allowance)
																			@if($allowance->allowance_option != '30')

																				<div class="project-amnt pt-1">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}</div>
																			@else
																				<div class="project-amnt pt-1">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }} ({{$allowance->title}})</div>

																			@endif
																		@endforeach
																		@if($overtimes->isEmpty())

																		@else
																			<div class="project-amnt pt-1">Heures SupplÃ©mentaires</div>
																		@endif
																		<div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;">Total Brut (A)</b></div>
																	</td>
																	<td style="padding:5px;border-right-color: #1c232f" width="15%">
																		<div class="project-amnt pt-1"><b class="m-0"><br></b></div>
																		<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">
																				@if($employee->tax_payer_id == '30')
																					{{ number_format($employee->salary, 0 , '.' , ' ') }}</b>
																				@else
																					{{ number_format($employee->branch_location, 0 , '.' , ' ') }}</b>
																				@endif
																		</b></div>
																		@foreach ($allowances as $allowance)
                                                                            <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($allowance->amount, 0 ,'.',' ')}}</b></div>
																		@endforeach
																		<?php
																			$totoverstimes = 0;
																			foreach ($overtimes as $overtime){
																				$totoverstimes = $totoverstimes + $overtime->montant;
																			}
																		?>
																		@if($overtimes->isEmpty())

																		@else
																			<div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($totoverstimes, 0 ,'.',' ')}}</b></div>
																		@endif
																		<?php
																			$totoezro1=0;
																			foreach ($allowances as $allowance){
																				//if($allowance->trait_fisc =='' || $allowance->trait_fisc =='0%'){
																					$totoezro1 = $totoezro1 + $allowance->amount;
																				//}
																			}
																		?>
																		<div class="project-amnt pt-1" align="right"><b class="m-0" style="color: #ddcd08;">
																				{{ number_format(($employee->get_brut_salary()-$avtg_reat), 0 , '.' , ' ') }}</b>
																		</b></div>
																	</td>
																	<td style="padding:5px;">
																	</td>
																	<td>
																	</td>
																</tr>
                                                                <tr>
                                                                    <td style="padding:5px;">
                                                                        <div class="project-amnt pt-1"><b class="m-0">Avantages en Nature ou en Argent</b></div>
                                                                        <div class="project-amnt pt-1">Montant des avantages au barÃ¨me</div>
                                                                        <div class="project-amnt pt-1">Montant des autres avantages</div>
                                                                        <div class="project-amnt pt-1" align="left">Total des avantages</b></div>
                                                                    </td>
                                                                    <td style="padding:5px;border-right-color: #1c232f">
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0">Montant rÃ©el</b></div>
                                                                        <div class="project-amnt pt-1" align="right">{{number_format($real, 0 ,'.',' ')}}</div>
                                                                        <div class="project-amnt pt-1" align="right">{{number_format($avtg_reat_arg, 0 ,'.',' ')}}</div>
                                                                        <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format(($avtg_reat), 0 ,'.',' ')}}</b></div>
                                                                    </td>
                                                                    <td style="padding:5px;">

                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0">Montant au barÃ¨me</b></div>
                                                                        <div class="project-amnt pt-1" align="right">{{number_format($bareme, 0 ,'.',' ')}}</div>
                                                                        <div class="project-amnt pt-1" align="right">{{number_format($avtg_reat_arg2, 0 ,'.',' ')}}</div>
                                                                        <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">(B) {{number_format(($avtg_bareme + $avtg_reat_arg2), 0 ,'.',' ')}}</b></div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="padding:5px;">
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;">Total Brut avec avantages</b></div>
                                                                    </td>
                                                                    <td style="padding:5px;border-right-color: #1c232f">
                                                                        <div class="project-amnt pt-1" align="right">
                                                                            <b class="m-0" style="color: #ddcd08;">{{ number_format(($employee->get_brut_salary()), 0 , '.' , ' ') }} </b>
                                                                        </div>
                                                                    </td>
                                                                    <td style="padding:5px;"></td>
                                                                    <td style="padding:5px;"></td>
                                                                </tr>
																<tr>
																	<td style="padding:5px;">
                                                                        <div class="project-amnt pt-1"><b class="m-0">ExonÃ©rations</b></div>
                                                                        @foreach ($allowances as $allowance)
																			@if($allowance->allowance_option != '30')
																				@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																					<div class="project-amnt pt-1">{{ !empty($allowance->allowance_option()) ? $allowance->allowance_option()->name : '' }}</div>
																				@endif
																			@else
																				@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																					<div class="project-amnt pt-1">{{$allowance->title}}</div>
																				@endif
																			@endif
																		@endforeach
																		<div class="project-amnt pt-1" align="left">Total des primes exonÃ©rÃ©s Ã  100% dans la limite lÃ©gale</div>
                                                                        <br>
																	</td>
																	<td style="padding:5px;border-right-color: #1c232f">
                                                                        <div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
                                                                        @foreach ($allowances as $allowance)
																			@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
                                                                                <div class="project-amnt pt-1" style="text-align: right;"><b class="m-0">{{number_format($allowance->amount, 0 ,'.',' ')}}</b></div>
																			@endif
																		@endforeach
																		<?php
																			$total=0;
																			foreach ($allowances as $allowance){
																				if(strpos($allowance->trait_fisc, '100% - Art 116') === 0){
                                                                                    $total = $total + $allowance->amount;
																				}
																			}
																		?>
																		<div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;"><br/></b></div>
																	</td>
																	<td style="padding:5px;">
																		<div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;"><br/></b></div>
                                                                        @foreach ($allowances as $allowance)
																			@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																				<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
																			@endif
																		@endforeach
																		<?php
																			$total=0;
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
																		<div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;">{{number_format($total, 0 ,'.',' ')}}</b></div>
																	</td>
                                                                    <td style="padding:5px;">
																		<div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;"><br/></b></div>
																		@foreach ($allowances as $allowance)
																			@if(strpos($allowance->trait_fisc, '100% - Art 116') === 0)
																				<div class="project-amnt pt-1"><b class="m-0"><br/></b></div>
																			@endif
																		@endforeach
																		<div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;"><br/></b></div>
																		<div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;"><br/></b></div>
                                                                        <div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;"><br/></b></div>
                                                                    </td>
																</tr>
                                                                <tr>
                                                                    <td><div class="project-amnt pt-1" align="left"><b class="m-0" style="color: #ddcd08;">Total des ExonÃ©rations (C )</b></div></td>
                                                                    <td style="padding:5px;border-right-color: #1c232f"></td>
                                                                    <td></td>
                                                                    <td><div class="project-amnt pt-1" align="right"><b class="m-0" style="color: #ddcd08;">{{number_format(($total), 0 ,'.',' ')}}</b></div></td>
                                                                </tr>
																<tr bgcolor="#fff">
																	<td colspan="3" style="padding:5px;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;border-right-color: #1c232f"><div class="project-amnt pt-1" align="left"><b class="m-0"  style="color: #000;">Salaire brut social (SBS) [A+B-C]</b></div></td>
																	<td  style="padding:5px;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><div class="project-amnt pt-1" align="right"><b class="m-0" style="color: #000;">{{number_format((($employee->get_brut_salary()-$avtg_reat)+(($avtg_bareme+$avtg_reat_arg2)-($total))), 0 ,'.',' ')}}</b></div></td>
																</tr>
															</tbody>
														</table>
													</div>
													<div class="col-xl-4" align="center">
														<table class="table-sm table-bordered" style="color: #fff;">
															<tr>
																<td colspan = "2"><strong>CMU</strong></td>
															</tr>
															<tr>
																<td>Nombre de bÃ©nÃ©ficiaires : </td><td align="right"><strong> {{ ($cmu)}} </strong></td>
															</tr>
															<?php if( $cmu < '7'){ ?>
																<tr>
																	<td>LimitÃ© Ã  6 bÃ©nÃ©ficiaires : </td><td align="right"><strong>{{ (6 - $cmu)}} Restants</strong></td>
																</tr>
															<?php }else{ ?>
																<tr>
																	<td colspan = "2">Limite de bÃ©nÃ©ficiaires atteint : <strong> 6 </strong></td>
																</tr>
																<tr>
																	<td colspan = "2">La part de l'employÃ© passe de 500 Ã  1000 FCFA par bÃ©nÃ©ficiaires pour <strong>{{($cmu - 6)}}</strong> bÃ©nÃ©ficiaire(s)</td>
																</tr>
															<?php } ?>
															<tr>
																<td>Part SalariÃ© :</td><td align="right"><strong>{{ number_format($resultcmu, 0 , '.' , ' ')}}</strong></td>
															</tr>
														</table>
													</div>
												</div>
												<br/>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
										<br/>
										<div class="col-xl-12" style="color: #fff; padding:5px">
											<div class="card bg-dark shadow-none" style="padding:10px">
												<div class="project-info-inner mr-3">
													<div class="row">
														<div class="project-info-inner mr-3 col-10"><strong>PrÃªts</strong></div>
														{{--@can('Create Loan')
															<div class="col-1">
																<a data-url="{{ route('loanemp.create', $employee->id) }}" data-ajax-popup="true"
																	data-title="{{ __('Create Loan') }}" data-bs-toggle="tooltip" title=""
																	data-size="lg" class="btn btn-sm btn-primary"
																	data-bs-original-title="{{ __('Create') }}">
																	<i class="ti ti-plus"></i>
																</a>
															</div>
														@endcan --}}
													</div>
													<hr/>
												</div>
												<?php $cpte_loan = 1; ?>
												<div class="project-info-inner mr-3">
													@php
														setlocale(LC_TIME, 'fr_FR.utf8');
													@endphp
													<?php if($employee->id == $ids){$text=''; } else { echo 'Pas de prÃªt en cours. <hr/>';} ?>
													@foreach ($loans as $loan)

														@php
															$cpteloan = $loan->nbre_mois;
															$start_date = Carbon::parse($loan->start_date);
															//echo  ('%B %Y', $start_date->getTimestamp());
															$end_date = Carbon::parse($loan->end_date);
															$currentDate = clone $start_date;
															$datepret = Carbon::parse($loan->deduc_loan);
															$totalAmountRetenue = 0;  $totalAmountRetenue2 = 0;
															$totalSoldeAmount   = 0;  $totalSoldeAmount2   = 0;
															$loanIndex = 0;
														@endphp
														<div class="project-amnt pt-1">
															@if ($loan && !empty($loan->loan_option()))
															 PrÃªt NÂ° {{ $cpte_loan++ }} - {{ $loan->loan_option()->name }} :
															@endif
															@if ($loan->type == 'fixed')
																<b align="right">{{ number_format($loan->amount, 0 , '.' , ' ') }} FCFA</b>
															@else
																<b align="right">{{ $loan->amount }}%
																	{{ number_format($loan->tota_allow, 0 , '.' , ' ') }} FCFA
																</b>
															@endif
															 | Date de dÃ©but de prÃ©lÃ¨vement : {{$datepret->formatLocalized('%e %B %Y')}}
														</div>
														<br>
														@if($loan->amountpaie === 0)
                                                            <?php { echo 'PrÃªt remboursÃ©. <hr/>';} ?>
                                                        @else
                                                            <table class="table" style="color: white;">
                                                                <thead>
                                                                    <tr>
                                                                        <th><center>{{ __('Ã‰CHÃ‰ANCES') }}</center></th>
                                                                        <th><center>{{ __('Date de paiement') }}</center></th>
                                                                        <th><center>{{ __('Retenue mensuelle') }}</center></th>
                                                                        <th><center>{{ __('Solde du prÃªt') }}</center></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @for ($i = 0; $i <= ($cpteloan); $i++)
                                                                        @if($i == 0)
                                                                            <tr>
                                                                                <td align="center">
                                                                                    @php
                                                                                        echo "Date du prÃªt";
                                                                                    @endphp
                                                                                </td>
                                                                                <td align="center">
                                                                                    {{ $datepret->formatLocalized('%e %B %Y') }}
                                                                                </td>
                                                                                <!-- Les deux premiÃ¨res colonnes ajoutÃ©es par dÃ©faut -->

                                                                                @if (!empty($loanretenue) && $loanIndex < count($loanretenue))
                                                                                    <!-- Si nous avons encore des Ã©lÃ©ments dans $loanretenue -->
                                                                                    @php
                                                                                        $loans = $loanretenue[$loanIndex];
                                                                                        $loanIndex++; // IncrÃ©menter l'index pour passer Ã  l'Ã©lÃ©ment suivant
                                                                                        // Calculer le total du montant retenu ;
                                                                                        $totalAmountRetenue += $loans['amount_retenue'];
                                                                                        $totalSoldeAmount = $loans['solde_retenue'];
                                                                                    @endphp
                                                                                    @if($loan->id == $loans['loan_id'])
                                                                                        @if($loans['amount_retenue'] > 0)
                                                                                            <td align="center">
                                                                                                {{ number_format($loans['amount_retenue'], 0, '.', ' ') }} FCFA
                                                                                            </td>
                                                                                            <td align="center">
                                                                                                {{ number_format($loans['solde_retenue'], 0, '.', ' ') }} FCFA
                                                                                            </td>
                                                                                        @else
                                                                                            <td align="center">-</td>
                                                                                            <td align="center">-</td>
                                                                                        @endif
                                                                                    @endif
                                                                                    <!-- Remplissage des colonnes avec les valeurs de $loanretenue -->
                                                                                @else
                                                                                    <!-- Si nous avons Ã©puisÃ© tous les Ã©lÃ©ments de $loanretenue, laissez les colonnes vides -->
                                                                                    <td align="center">-</td>
                                                                                    <td align="center">-</td>
                                                                                @endif
                                                                            </tr>
                                                                        @else
                                                                            <tr>
                                                                                <td align="center">
                                                                                    @php
                                                                                        echo "NÂ°".($i);
                                                                                    @endphp
                                                                                </td>
                                                                                <td align="center">
                                                                                    {{ $start_date->formatLocalized('%B %Y') }}
                                                                                </td>
                                                                                <!-- Les deux premiÃ¨res colonnes ajoutÃ©es par dÃ©faut -->

                                                                                @if ($loanIndex < count($loanretenue))
                                                                                    <!-- Si nous avons encore des Ã©lÃ©ments dans $loanretenue -->
                                                                                    @php
                                                                                        $loans = $loanretenue[$loanIndex];
                                                                                        $loanIndex++; // IncrÃ©menter l'index pour passer Ã  l'Ã©lÃ©ment suivant
                                                                                        // Calculer le total du montant retenu ;
                                                                                        $totalAmountRetenue += $loans['amount_retenue'];
                                                                                        $totalSoldeAmount = $loans['solde_retenue'];
                                                                                    @endphp
                                                                                    @if($loan->id == $loans['loan_id'])
                                                                                        <td align="center">
                                                                                        {{ number_format($loans['amount_retenue'], 0, '.', ' ') }} FCFA
                                                                                        </td>
                                                                                        <td align="center">
                                                                                        {{ number_format($loans['solde_retenue'], 0, '.', ' ') }} FCFA
                                                                                        </td>
                                                                                    @endif
                                                                                    <!-- Remplissage des colonnes avec les valeurs de $loanretenue -->
                                                                                @else
                                                                                    <!-- Si nous avons Ã©puisÃ© tous les Ã©lÃ©ments de $loanretenue, laissez les colonnes vides -->
                                                                                    <td align="center">-</td>
                                                                                    <td align="center">-</td>
                                                                                @endif
                                                                            </tr>
                                                                        @endif
                                                                        @php
                                                                            $start_date->modify('+1 month');
                                                                        @endphp
                                                                    @endfor
                                                                    <tr>
                                                                        <td colspan="2" align="right"><strong>Total :</strong></td>
                                                                        <td align="center"><strong>{{ number_format($totalAmountRetenue2, '0', '.', ' ') }} FCFA</strong></td>
                                                                        <td align="center">
                                                                            @if($totalSoldeAmount2 == 0)
                                                                                0
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        @endif
														<br>
														<br>
													@endforeach
                                                </div>
											</div>
										</div>
									</div>
									<div class="tab-pane fade" id="nav-recap" role="tabpanel" aria-labelledby="nav-recap-tab">
										<br/>
										<div class="row">
											<div class="col-xl-12" style="color: #fff; padding:5px">
												<div class="card bg-dark shadow-none" style="padding:10px">
													<table class="table-sm table-bordered" style="color: #fff;">
														<tr>
															<td><strong>ITS </strong></td>
															<td align="right" width="25%"><strong>SBI : {{number_format($employee->get_salary_imposable(),0 , '.' , ' ')}} </strong></td>
														</tr>
														<tr>
															<td>ImpÃ´ts brut : </td>
															<td align="right" width="25%"><strong>{{ number_format($resultimpricf, 0 , '.' , ' ') }} </strong></td>
														</tr>
														<tr>
															<td>RICF ({{ $employee->parts }} parts) : </td>
															<td align="right" width="25%"><strong>{{ number_format($resultricf, 0 , '.' , ' ') }} </strong></td>
														</tr>
														<tr>
															<td>ImpÃ´ts Net :</td>
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
											<div class="col-xl-12" style="color: #fff; padding:5px">
												<div class="card bg-dark shadow-none" style="padding:10px">
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
												<div class="card bg-dark shadow-none" style="padding:10px">
													<table class="table-sm table-bordered" style="color: #fff;">
														<tr>
															<td colspan = "2"><strong>CMU</strong></td>
														</tr>
														<tr>
															<td>Nombre de bÃ©nÃ©ficiaires : </td><td align="right" width="25%"><strong> {{ ($cmu)}} </strong></td>
														</tr>
														<?php if( $cmu < '7'){ ?>
															<tr>
																<td>LimitÃ© Ã  6 bÃ©nÃ©ficiaires : </td><td align="right" width="25%"><strong>{{ (6 - $cmu)}} Restants</strong></td>
															</tr>
														<?php }else{ ?>
															<tr>
																<td colspan = "2">Limite de bÃ©nÃ©ficiaires atteint : <strong> 6 </strong></td>
															</tr>
															<tr>
																<td colspan = "2">La part de l'employÃ© passe de 500 Ã  1000 FCFA par bÃ©nÃ©ficiaires pour <strong>{{($cmu - 6)}}</strong> bÃ©nÃ©ficiaire(s)</td>
															</tr>
														<?php } ?>
														<tr>
															<td>Part SalariÃ© :</td><td align="right" width="25%"><strong>{{ number_format($resultcmu, 0 , '.' , ' ')}}</strong></td>
														</tr>
													</table>
												</div>
											</div>
                                            <div class="col-xl-12" style="color: #fff; padding:5px">
												<div class="card bg-dark shadow-none" style="padding:10px">
													<table class="table-sm table-bordered" style="color: #fff;">
                                                        <?php $cpte_lo = 0; ?>
                                                        @php
                                                            setlocale(LC_TIME, 'fr_FR.utf8');
                                                        @endphp
                                                        @foreach ($loans_emp as $loans_empployee)
                                                            @php
                                                                $cpte_lo++;
                                                            @endphp
                                                        @endforeach
                                                        <tr>
															<td><strong>PRETS</strong></td><td align="right" width="25%"><strong> {{$cpte_lo}} </strong></td>
														</tr>
                                                        @if($loans_emp->isEmpty())
                                                            <tr><td colspan="2"><?php if($employee->id == $ids){$text=''; } else { echo 'Pas de prÃªt en cours.';} ?></td></tr>
                                                        @else
                                                            @foreach ($loans_emp as $loans_empployee)
                                                                @if ($loans_empployee && !empty($loans_empployee->loan_option()))
                                                                    <tr>
                                                                        <td>{{ $loans_empployee->loan_option()->name }} | Montant remboursÃ©</td><td align="right" width="25%"><strong> {{ !empty($loans_empployee->amountpaie) ? number_format($loans_empployee->amountpaie, 0 , '.' , ' ') : '0' }}</strong></td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endif
													</table>
												</div>
											</div>
											<hr/>
											<div class="project-amnt pt-3" align="left">Total Retenues : <b style="background-color: #ddcd08;">{{ number_format($totalimpots+$total_cantine, 0 , '.' , ' ') }} FCFA</b></div>
											<div class="project-amnt pt-3" align="right">Estimation du Net : <b style="background-color: #ddcd08;">{{ number_format($employee->get_net_salary(), 0 , '.' , ' ') }} FCFA</b></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <!--overtime-->
					<div id="overtime" class=" " style="display: none;">
						<div class="card">
							<div class="card-header">
								<div class="row">
									<div class="col-11">
										<h5>Heures SupplÃ©mentaires & Absences</h5>
									</div>
									{{--@can('Create Overtime')
										<div class="col-1 text-end">
											<a data-url="{{ route('overtimes.create', $employee->id) }}" data-ajax-popup="true"
												data-title="{{ __('Create Overtime') }}" data-bs-toggle="tooltip"
												title="" class="btn btn-sm btn-primary"
												data-bs-original-title="{{ __('Create') }}">
												<i class="ti ti-plus"></i>
											</a>
										</div>
									@endcan--}}
								</div>
							</div>
							<div class=" card-body table-border-style" style=" overflow:auto">
								<div class="row">
									<div class="col-md-5" style="color: #fff; padding:5px">
										<div class="card bg-dark shadow-none" style="padding:10px">
											<table class="table-sm table-bordered">
												<thead style="color: #fff;">
													<tr>
														<td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><strong>DATE</strong></td>
														<td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><strong>HEURES TRAVAILLÃ‰ES</strong></td>
														<td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><strong>GAINS</strong></td>
													</tr>
												</thead>
												<tbody style="color: #fff;">
													@if($overtimes->isEmpty())
														<tr>
															<td colspan="4" align="center">Pas d'heures supplÃ©mentaires constatÃ©es.</td>
														</tr>
													@else
														@foreach ($overtimes as $overtime)
															<tr>
																<td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;">Du : {{ date('d-m-Y', strtotime($overtime->start_date)) }} <br/>au : {{ date('d-m-Y', strtotime($overtime->end_date)) }}</td>
																<td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;">
																	De la 41Ã¨ Ã  la 46Ã¨ heure : <strong>{{ $overtime->quar_heure }}</strong><br/>
																	Au dÃ©lÃ  de la 46Ã¨ heure : <strong>{{ $overtime->heure_audd }}</strong><br/>
																	Jours ouvrÃ©s Nuit : <strong>{{ $overtime->heure_nuit_ferie }}</strong><br/>
																	Dimanche et jours fÃ©riÃ©s : <strong>{{ $overtime->heure_dim_ferie }}</strong><br/>
																	Nuit dimanche et jours fÃ©riÃ©s : <strong>{{ $overtime->heure_nuit_dim_ferie }}</strong><hr/>
																	<strong>Total : {{ $overtime->quar_heure+$overtime->heure_audd+$overtime->heure_nuit_ferie+$overtime->heure_dim_ferie+$overtime->heure_nuit_dim_ferie }}</strong>
																</td>
																<td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;">
																	@if($overtime->statut == 1)
																		Statut : En cours
																	@endif
																	<hr><strong>{{ number_format($overtime->montant,'0','.',' ') }} FCFA</strong></td>
															</tr>
														@endforeach
													@endif
												</tbody>
											</table>
										</div>
									</div>
									<div class="col-md-7" style="color: #fff; padding:5px">
										<div class="card bg-dark shadow-none" style="padding:10px">
                                            <table class="table-sm table-bordered" style="color: #fff;">
                                                <thead>
                                                    <tr>
                                                        <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><strong>PÃ‰RIODE D'ABSENCES</strong></td>
                                                        <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><strong>HEURES D'ABSENCES</strong></td>
                                                        <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><strong>JUSTIFIÃ‰E ?</strong></td>
                                                        <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><strong>DÃ‰DUCTIBLE ?</strong></td>
                                                        <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;"><strong>DEDUCTION </strong></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($timesheets->isEmpty())
                                                            <tr>
                                                                <td colspan="4" align="center">Pas d'absence constatÃ©e.</td>
                                                            </tr>
                                                    @else
                                                        @foreach ($timesheets as $timeSheet)
                                                            <tr>
                                                                <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;">Du : {{ date('d-m-Y', strtotime($timeSheet->date)) }} <br/> au : {{ date('d-m-Y', strtotime($timeSheet->arrival_date)) }}</td>
                                                                <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;">{{ $timeSheet->hours }}</td>
                                                                <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;">{{ $timeSheet->motif_justify }}</td>
                                                                <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;">
                                                                    @if($timeSheet->deduc_abs == 1)
                                                                        Oui
                                                                    @else
                                                                        Non
                                                                    @endif
                                                                </td>
                                                                <td align="center" style="border-left-style:solid;border-left-width:2pt; border-right-style:solid;border-right-width:2pt;">{{ number_format($timeSheet->retenue,'0','.',' ') }} FCFA</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- other payment-->
					<div id="autrepaie" class=" " style="display: none;">
						<div class="card">
							<div class="card-header">
								<div class="row">
									<div class="col-11">
										<h5>Charges Patronales</h5>
									</div>
									@can('Create Other Payment')
										<!--<div class="col-1 text-end">

											<a data-url="{{ route('otherpayments.create', $employee->id) }}"
												data-ajax-popup="true" data-title="{{ __('Create Other Payment') }}"
												data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
												data-bs-original-title="{{ __('Create') }}">
												<i class="ti ti-plus"></i>
											</a>
										</div>-->
									@endcan
								</div>
							</div>
							<div class="card-body table-border-style" style=" overflow:auto">
								<div class="row">
									<div class="col-xl-12" style="color: #fff; padding:5px">
										<div class="card bg-dark shadow-none" style="padding:10px">
											<table class="table-sm table-bordered" style="color: #fff;">
												<tr>
													<td><strong>ImpÃ´ts sur Salaire</strong></td>
													<td align="right" width="25%"><strong> SBI : {{number_format($employee->get_salary_imposable(),0 , '.' , ' ')}} </strong></td>
												</tr>
												<tr>
													<td>Contribution Employeur (CNE) : </td>
													<td align="right"><strong>{{ number_format($resulttax1, 0 , '.' , ' ') }} </strong></td>
												</tr>
												@if($local=='local')
													<tr>
														<td>Contribution employeur (ExpatriÃ©) : </td>
														<td align="right"><strong>  0 </strong></td>
													</tr>
												@else
													<tr>
														<td>Contribution employeur (ExpatriÃ©) : </td>
														<td align="right"><strong>{{ number_format($resulttax2, 0 , '.' , ' ') }} </strong></td>
													</tr>
												@endif
												<tr>
													<td>Taxe dâ€™Apprentissage :<br/>Taxe Ã  la F. P. C. :  </td>
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
										<div class="card bg-dark shadow-none" style="padding:10px">
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
										<div class="card bg-dark shadow-none" style="padding:10px">
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
														<td>BÃ©nÃ©ficiaires Ã  charges partagÃ©es LimitÃ©s Ã  6 : </td>
														<td align="right"><strong>{{($cmu)}}</strong></td>
													</tr>
													<tr>
														<td>BÃ©nÃ©ficiaires restants: </td>
														<td align="right"><strong>{{(6 - $cmu)}}</strong></td>
													</tr>
												<?php }else{ ?>
													<tr>
														<td>Limite de bÃ©nÃ©ficiaires atteint : </td>
														<td align="right"> <strong> 6 </strong> </td>
													</tr>
													<tr>
														<td>La part de l'employÃ© passe de 500 Ã  1000 FCFA par bÃ©nÃ©ficiaires pour </td>
														<td align="right"><strong>{{($cmu - 6)}}</strong> bÃ©nÃ©ficiaire(s)</td>
													</tr>
												<?php } ?>
											</table>
										</div>
									</div>
								</div>
								<div class="project-amnt pt-3" align="left">Total Retenues : <b style="background-color: #ddcd08;">{{ number_format(round($parpatronal), 0 , '.' , ' ') }} FCFA</b></div>
								<div class="project-amnt pt-3" align="right">Estimation du Net : <b style="background-color: #ddcd08;">{{ number_format($employee->get_net_salary(), 0 , '.' , ' ') }} FCFA</b></div>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
    <div class="scroll-bottom">
        <button class="btn btn-primary" id="toggle-calculator">{{ __('Calculatrice') }}</button>
    </div>
    <div id="calculator" class="calculator cacher">
        <div class="calculator-content">
            <div class="calculator-display">
                <input type="text" id="display" disabled>
            </div>
            <div class="calculator-buttons">
                <button class="btn" onclick="clearDisplay()">C</button>
                <button class="btn" onclick="deleteLast()">â†</button>
                <button class="btn" onclick="appendToDisplay('/')">/</button>
                <button class="btn" onclick="appendToDisplay('*')">*</button>
                <button class="btn" onclick="appendToDisplay('7')">7</button>
                <button class="btn" onclick="appendToDisplay('8')">8</button>
                <button class="btn" onclick="appendToDisplay('9')">9</button>
                <button class="btn" onclick="appendToDisplay('-')">-</button>
                <button class="btn" onclick="appendToDisplay('4')">4</button>
                <button class="btn" onclick="appendToDisplay('5')">5</button>
                <button class="btn" onclick="appendToDisplay('6')">6</button>
                <button class="btn" onclick="appendToDisplay('+')">+</button>
                <button class="btn" onclick="appendToDisplay('1')">1</button>
                <button class="btn" onclick="appendToDisplay('2')">2</button>
                <button class="btn" onclick="appendToDisplay('3')">3</button>
                <button class="btn" onclick="calculateResult()">=</button>
                <button class="btn" onclick="appendToDisplay('0')">0</button>
                <button class="btn" onclick="appendToDisplay('.')">.</button>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
	<script>
        document.addEventListener('DOMContentLoaded', function() {
            primeancienete();
        });
		// RÃ©cupÃ©rer tous les boutons de navigation
		var tabs = document.querySelectorAll('.nav-link');

		// Parcourir chaque bouton de navigation
		tabs.forEach(function(tab) {
			// Ã‰couter l'Ã©vÃ©nement de clic sur chaque bouton
			tab.addEventListener('click', function() {
				// Supprimer la classe "active bg-primary" de tous les boutons de navigation
				tabs.forEach(function(t) {
					t.classList.remove('active', 'bg-primary');
				});
				// Ajouter la classe "active bg-primary" au bouton cliquÃ©
				this.classList.add('active', 'bg-primary');
			});
		});
	</script>

    <script type="text/javascript">
        $(document).on('change', '.amount_type', function() {

            var val = $(this).val();
            var label_text = 'Montant';
            if (val == 'percentage') {
                var label_text = 'Pourcentage';
            }
            $('.amount_label').html(label_text);
        });


        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });

        function getDesignation(did) {
            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#designation_id').empty();
                    $('#designation_id').append(
                        '<option value="">{{ __('Select any Designation') }}</option>');
                    $.each(data, function(key, value) {
                        var select = '';
                        if (key == '{{ $employee->designation_id }}') {
                            select = 'selected';
                        }

                        $('#designation_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                }
            });
        }
    </script>
	<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
	<script>
		function saveAsPDF() {
			var element = document.getElementById('printableArea');
			var opt = {
				margin: 0.2,
				filename: 'bulletin_{{ strtolower($date->translatedFormat("F")) }}{{ $date->format("y") }}_{{ $employee->name }}.pdf',
				image: {
					type: 'jpeg',
					quality: 1
				},
				html2canvas: {
					scale: 4,
					dpi: 72,
					letterRendering: true
				},
				jsPDF: {
					unit: 'in',
					format: 'A4'
				}
			};
			html2pdf().set(opt).from(element).save();
		}

        function primeancienete() {
            var span = document.getElementById('primeancien');
            var salarybase  = parseInt(document.getElementById("salarybase").value, 10); // Convertir en nombre
            var salarybase2 = parseInt(document.getElementById("salarybase2").value, 10); // Convertir en nombre
            var baraJours   = parseInt(document.getElementById("baraJours").value); // Convertir en nombre
            var dateancien  = parseInt(document.getElementById("dateancien").value, 10); // Convertir en nombre
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
        }
	</script>
    <script>
    	document.getElementById('toggle-calculator').addEventListener('click', function() {
            const calculator = document.getElementById('calculator');
            calculator.classList.toggle('cacher');
        });

        function appendToDisplay(value) {
            document.getElementById('display').value += value;
        }

        function clearDisplay() {
            document.getElementById('display').value = '';
        }

        function deleteLast() {
            let currentValue = document.getElementById('display').value;
            document.getElementById('display').value = currentValue.slice(0, -1);
        }

        function calculateResult() {
            let currentValue = document.getElementById('display').value;
            try {
                let result = eval(currentValue);
                document.getElementById('display').value = result;
            } catch (e) {
                document.getElementById('display').value = 'Erreur';
            }
        }

        document.addEventListener('keydown', function(event) {
            const key = event.key;
            if (/\d/.test(key)) {
                appendToDisplay(key);
            } else if (key === '+') {
                appendToDisplay('+');
            } else if (key === '-') {
                appendToDisplay('-');
            } else if (key === '*') {
                appendToDisplay('*');
            } else if (key === '/') {
                appendToDisplay('/');
            } else if (key === '.') {
                appendToDisplay('.');
            } else if (key === 'Enter') {
                calculateResult();
            } else if (key === 'Backspace') {
                deleteLast();
            }
        });
    </script>
@endpush
<input type="text" id="salarybase" name="salarybase" value="{{$employee->salary}}" hidden="">
<input type="text" id="salarybase2" name="salarybase2" value="{{$employee->branch_location}}" hidden="">
<input type="text" id="baraJours" name="baraJours" value="{{$employee->tax_payer_id}}" hidden="">
<input type="text" id="dateancien" name="dateancien" value="{{ $date_pa }}" hidden="">

