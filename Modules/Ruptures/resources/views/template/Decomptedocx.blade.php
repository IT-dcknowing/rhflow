
@extends('layouts.doc')
@section('page-title')
    {{ __('DÉCOMPTE DES DROITS DE LICENCIEMENT') }}
@endsection
@php
    $employee_cate="";
    foreach($categories as $cate){   
        if($emp_cate==$cate->id){
            $employee_cate = $cate->title;
        }
    }
    $city = $company->city;
    $company_logo = $company->logo;
    $date_embauche = new DateTime($emp_doj);
    $date_actuelle = new DateTime(date('Y-m-d'));
    
    $difference = $date_embauche->diff($date_actuelle);
    $date_pa = $difference->format('%y'); 
	$date_m  = $difference->format('%m');
    $date_j  = $difference->format('%d');
@endphp
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mt-5" id="printTable">
            <div class="card-body" id="exportContent">
                <div class="row invoice-title mt-2">
                    <p data-v-f2a183a6="">
                        <div>
                            <div>
                                <div class="invoice-number">
                                    <img src="{{ url($company->logo_url) }}"
                                        width="170px;">
                                </div>
                            </div>
                            <br>
                            <br>
                            <p align="right">{{ucfirst($city)}}, le {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p> 
                            <br>
                            <br>
                            <h3 style="text-align: center;"><strong><u>DÉCOMPTE DES DROITS DE LICENCIEMENT</u></strong></h3>
                            <p style="text-align:justify;"><strong><u>@if($genre == 'Male') Mr. @else Mme/Me @endif {{$emp_name}}</u></strong></p>
                            <br>
                            <p style="text-align:justify;">@if($employees->martalstatu_id == 1) Célibataire @elseif ($employees->martalstatu_id == 2) Marié(e) @elseif ($employees->martalstatu_id == 3) Divorcé(e) @else Veuf(ve) @endif ....</p>
                            <br>
                            <p style="text-align:justify;">Date d’embauche :{{$emp_doj}}.</p>
                            <br>
                            <p style="text-align:justify;">Date d’arrêt pour cause de licenciement : {{$terminations->termination_date}}.</p>
                            <br>
                            <p style="text-align:justify;">Ancienneté : {{$date_pa}} an(s) {{$date_m}} mois {{$date_j}} jours.</p>
                            <br>
                            <p style="text-align:justify;"><strong>I- Les droits légaux</strong></p>
                                <p style="text-align:justify;">1- Indemnité compensatrice de Gratification : <strong>{{number_format($terminations->indem_comp,'0','.',' ')}} FCFA</strong></p>
                                <p style="text-align:justify;">2- Indemnité compensatrice : <strong>{{number_format($terminations->indem_comp_cong,'0','.',' ')}} FCFA</strong></p>
                            <br>
                            <p style="text-align:justify;"><strong>II- Les droits spécifiques</strong></p>
                                <p style="text-align:justify;">1- Indemnité de préavis : <strong>{{number_format($terminations->imdem_prea,'0','.',' ')}} FCFA</strong></p>
                                <p style="text-align:justify;">2- Aggravation de l'indemnité compensatrice de préavis :	<strong>{{number_format($terminations->indem_licence,'0','.',' ')}} FCFA</strong></p>
                                <p style="text-align:justify;">3- Indemnité de licenciement : <strong>{{number_format($terminations->aggravation,'0','.',' ')}} FCFA</strong></p>
                                <p style="text-align:justify;">4- Dommages et intérêts : <strong>{{number_format($terminations->dom_inter,'0','.',' ')}} FCFA</strong></p>
                                <p style="text-align:justify;">Total Droits Brut :	<strong>{{number_format(($terminations->indem_comp+$terminations->indem_comp_cong+$terminations->imdem_prea+$terminations->indem_licence+$terminations->aggravation+$terminations->dom_inter),'0','.',' ')}} FCFA</strong></p>
                            <br>
                            <p style="text-align:justify;">III- Les retenues</p>
                                <p style="text-align:justify;">1- CNPS : <strong>{{number_format($terminations->amount_cnps,'0','.',' ')}} FCFA</strong></p>
                                <p style="text-align:justify;">2- ITS :	<strong>{{number_format($terminations->amount_its,'0','.',' ')}} FCFA</strong></p>
                                <p style="text-align:justify;">3- Prêts : <strong>{{number_format($terminations->amount_loan,'0','.',' ')}} FCFA</strong></p>
                                <p style="text-align:justify;">Total Droits Retenues :	<strong>{{number_format(($terminations->amount_cnps+$terminations->amount_its+$terminations->amount_loan),'0','.',' ')}} FCFA</strong></p>
                            <br>
                            <p style="text-align:justify;">Total Droits Net : <strong>{{number_format($terminations->solde,'0','.',' ')}} FCFA</strong></p>
                                
                            <br>
                            <p align="right"></p>
                            <p align="right"><strong>{{ $company->name }}</strong></p>
                            <div class="d-flex justify-content-end"> 
                                <div class="invoice-number">
                                    <img src="{{ url($company->electronic_signature_url) }}"
                                        width="170px;">
                                </div>
                            </div>
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
            var filename = '{{$employees->name}}';
            var element = 'exportContent';
            var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML To Doc</title></head><body>";
            var postHtml = "</body></html>";
            var html = preHtml+document.getElementById(element).innerHTML+postHtml;

            var blob = new Blob(['\ufeff', html], {
                type: 'application/msword'
            });
            
            // Specify link url
            var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
            
            // Specify file name
            filename = filename?filename+'.doc':'document.doc';
            
            // Create download link element
            var downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);
            
            if(navigator.msSaveOrOpenBlob ){
                navigator.msSaveOrOpenBlob(blob, filename);
            }else{
                // Create a link to the file
                downloadLink.href = url;
                
                // Setting the file name
                downloadLink.download = filename;
                
                //triggering the function
                downloadLink.click();
            }
            
            document.body.removeChild(downloadLink);
        });        
    </script>
@endpush