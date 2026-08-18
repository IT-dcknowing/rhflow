
@extends('layouts.contractheader')
@section('page-title')
    {{ __('NOC') }}
@endsection
@php
    $employee_cate="";
    foreach($categories as $cate){
        if($emp_cate==$cate->id){
            $employee_cate = $cate->title; 
        }
    }
    if($situation == '1'){
        $situat = 'Célibataire';
    }else if($situation == '2'){
        $situat = 'Marié(e)';
    }else if($situation == '3'){
        $situat = 'Divorcé(e)';
    }else if($situation == '4'){
        $situat = 'Veuf(ve)';
    } 
    if($sexe == 'Male'){
        $titre = 'Monsieur ';
    }else{
        if($situation == '1' || $situation == '3'){
            $titre = 'Mademoiselle ';
        }else{
            $titre = 'Madame ';
        }
    }
    // $logo = asset(Storage::url('uploads/logo/'));
    $logo = \App\Models\Utility::get_file('uploads/logo/');
    $company_logo = \App\Models\Utility::getValByName('company_logo');
    $company_logo_light = \App\Models\Utility::getValByName('company_logo_light');
@endphp
@section('content')
<div class="row" >
    <div class="col-lg-1"></div>
    <div class="col-lg-10">
        <div class="card mt-5" id="printTable" style="padding: 20px;">
            <div class="card-body" id="boxes">
                <div class="row invoice-title mt-2">
                    <p data-v-f2a183a6="" >
                        <div style="padding: 40px;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <h3 style="text-align: center;"><strong>ATTESTATION DE TRAVAIL</strong></h3>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <p> Nous soussignés <strong>{{ \Utility::getValByName('company_name') }}</strong>, <strong>{{ \Utility::getValByName('company_zipcode') }};</strong></p>	
                            <p style="text-align:justify;">Attestons que <strong>{{$titre}}{{$emp_name}}</strong>, matricule <strong>{{\Auth::user()->employeeIdFormat($emp_ids)}}</strong>, numéro CNPS: <strong>{{$emp_cnps}}</strong>, est employé(e) dans notre société en qualité de <strong>{{$poste->name}}</strong>, catégorie <strong>{{$employee_cate}} - {{$sous_categorie}}</strong>, depuis le <strong>{{\Auth::user()->dateFormat($emp_doj)}}</strong>;</p>	
                            <p>En foit de quoi, nous lui délivrons la présente attestation, pour servir et valoir ce que de droit.</p>
                            <br>
                            <p align="right">Fait à Abidjan, le {{ $datefin->translatedFormat('d F Y') }}</p>   
                            <br>
                            <p align="right"></p>
                            <p align="right"><strong>{{ \Utility::getValByName('company_name') }}</strong></p>
                            <div class="text-end" align="right">
                                    @if(isset($company_logo_light) && !empty($company_logo_light))
                                    <img id="image1" alt="your image"
                                        src="{{ $logo . (isset($company_logo_light) && !empty($company_logo_light) ? $company_logo_light . '?' . time() : 'logo-light.png' . '?' . time()) }}"
                                        width="150px"
                                        class="big-logo img-fluid">
                                     @endif
                            </div>
                        </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-1"></div>
</div>

@endsection
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        function closeScript() {
            setTimeout(function () {
                window.open(window.location, '_self').close();
            }, 1000);
        }

        $(window).on('load', function () {
            var element = document.getElementById('boxes');
            var opt = {
                filename: '{{$employees->name}}',
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A4'}
            };

            html2pdf().set(opt).from(element).save().then(closeScript);
        });

        
    </script>
    
@endpush