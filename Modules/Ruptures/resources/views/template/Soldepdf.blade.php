
@extends('layouts.doc')
@section('page-title')
    {{ __('ATTESTATION DE SOLDE DE TOUT COMPTE') }}
@endsection
@php
    $employee_cate="";
    foreach($categories as $cate){
        if($emp_cate==$cate->id){
            $employee_cate = $cate->title;
        }
    }
@endphp
@section('content')
<div class="row justify-content-center align-items-center">
    <div class="col-lg-6">
        <div class="card mt-5" id="printTable">
            <div class="card-body" id="boxes">
                <div class="row invoice-title mt-2">
                    <p data-v-f2a183a6="" >
                        <div style="padding: 40px;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <h3 style="text-align: center;"><strong><u>ATTESTATION DE SOLDE DE TOUT COMPTE</u></strong></h3>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <p style="text-align:justify;"> À l’occasion de la rupture du contrat de travail par convention entre @if($genre == 'Male') Mr. @else Mme/Me @endif <strong>{{$emp_name}}</strong> et L’entité <strong>{{ $company->name }}</strong>, la somme de <strong>{{number_format($terminations->solde,'0','.',' ')}} FCFA</strong> a été retenue comme l’ensemble des droits dus à l’issue de la transaction.</p>	
                            <p style="text-align:justify;">L’employé reconnaît avoir reçu ces sommes et en donne reçu à son employeur.</p>	
                            <p style="text-align:justify;">Le présent reçu a été établi en double exemplaire, dont un exemplaire a été remis à @if($genre == 'Male') Mr. @else Mme/Me @endif <strong>{{$emp_name}}.</strong></p>
                            <br>
                            <div class="row">
                                <div class="col-md-6"><p align="left"><strong>Signature du salarié</strong></p></div>  
                                <div class="col-md-6"><p align="right"><strong>Signature de l’employeur</strong></p></div>
                            </div>
                            <div class="d-flex justify-content-end"> 
                                <img src="{{ url($company->electronic_signature_url) }}"
                                    width="170px;">  
                            </div>
                            <br>
                            <br> 
                            <p align="right">Fait à Abidjan, le {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</p>   
                            <br>
                            <p align="right"></p>
                            <p align="right"><strong><u>La Direction</u></strong></p>
                        </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
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