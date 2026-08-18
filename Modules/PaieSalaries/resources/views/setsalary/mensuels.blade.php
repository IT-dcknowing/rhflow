@php
    use Carbon\Carbon;
    $plan = Utility::getChatGPTSettings();
    // Définir la locale en français
    setlocale(LC_TIME, 'fr_FR.utf8');
    $date = Carbon::parse($monthpaie);
@endphp
@extends('layouts.admin')

@section('page-title')
    {{ __('Gestion des Mensuels') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Accueil') }}</a></li>
    <li class="breadcrumb-item">{{ __('Gestion des Mensuels') }}</li>
@endsection

@section('content')
    <div class="row">
        @foreach($employees as $employee)
            @php
                $emp_id = $employees->where('is_active', 1)->first()->id;
            @endphp
        @endforeach
        <div class="col-md-12 mb-4">
            <div class="card bg-success text-white" style="padding:10px;">
                <h5 class="mb-0" style="color:#fff;">{{ __('Mois de traitement : ') }} <strong>{{$date->formatLocalized('%B %Y')}}</strong></h5>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="circle-design bg-primary">
                                <span class="circle-number">1</span>
                            </div>
                            <div class="card-body" style="display: flex; justify-content: flex-end;">
                                <div class="position-relative">
                                    <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}"
                                    alt="wizard-create-deal" width="120px" class="img-fluid" style="height: 150px;" />
                                </div>
                                <style>
                                    .position-relative {
                                        position: relative;
                                    }
                                    .circle-design {
                                        position: absolute;
                                        top: 10px;
                                        left: 10px;
                                        width: 80px;
                                        height: 80px;
                                        border-radius: 50%;
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                    }
                                    .circle-number {
                                        color: white;
                                        font-size: 60px;
                                        font-weight: bold;
                                    }
                                </style>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="{{ asset('assets/img/illustrations/bulb-light.png') }}" alt="Ampoule" class="img-fluid" style="width: 70px; height: auto; position: relative; bottom: 0px; right: 0px;">
                                    </div>
                                    <div class="col-md-10">
                                        <h5 class="card-title">Données de paie</h5>
                                        <p class="card-text">Gérez les salaires, les primes et les déductions pour vos employés mensuels.</p>
                                    </div>
                                </div>
                                <p></p>
                                <a href="{{ route('setsalary.employee_details_mois', ['id' => $emp_id, 'monthpaie' => $monthpaie]) }}" class="btn btn-primary mt-auto">Gérer la paie</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="circle-design bg-primary">
                                <span class="circle-number">2</span>
                            </div>
                            <div class="card-body" style="display: flex; justify-content: flex-end;">
                                <img src="{{ asset('assets/img/illustrations/wizard-create-deal-confirm.png') }}"
                                alt="wizard-create-deal" width="150px" class="img-fluid" style="height: 150px;" />
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="{{ asset('assets/img/illustrations/bulb-light.png') }}" alt="Ampoule" class="img-fluid" style="width: 70px; height: auto; position: relative; bottom: 0px; right: 0px;">
                                    </div>
                                    <div class="col-md-10">
                                        <h5 class="card-title">États de paie</h5>
                                        <p class="card-text">Générez et consultez les différents états de paie pour vos employés mensuels.</p>
                                    </div>
                                </div>
                                <p></p>
                                <a href="{{ route('payslip.payslipmonths', ['monthpaie' => $monthpaie]) }}" class="btn btn-primary mt-auto">Gérer les états de paie</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>

    function setActive(element) {
        $('.list-group-item-action').removeClass('active');
        $(element).addClass('active');
    }
</script>
@endpush
