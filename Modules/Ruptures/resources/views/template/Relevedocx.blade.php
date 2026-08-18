
@extends('layouts.doc')
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
@endphp
@section('content')  
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mt-5" id="printTable">
            <div class="card-body" id="exportContent">
                <div class="row invoice-title justify-content-center mt-2">
                    <p data-v-f2a183a6="">
                        <div class="card-body">
                            <table cellspacing="0" style="border-collapse: collapse;" width="100%">
                                <tr style="height: 28pt;">
                                    <td rowspan="2" style="width: 126pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p style="padding-left: 8pt; text-indent: 0pt; text-align: left;"><span><img alt="image" height="61" src="{{ asset('images/cnps_logo.jpg') }}" width="153"></span></p>
                                    </td>
                                    <td bgcolor="#B8CCE3" style="width: 312pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s1" style="padding-top: 6pt; padding-left: 43pt; padding-right: 42pt; text-indent: 0pt; text-align: center;">ENREGISTREMENT</p>
                                    </td>
                                    <td rowspan="2" style="width: 106pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s2" style="padding-top: 6pt; padding-left: 5pt; text-indent: 0pt; text-align: left;">Réf. : EN-GDAV-06</p>
                                        <p class="s2" style="padding-top: 7pt; padding-left: 5pt; text-indent: 0pt; text-align: left;">Version : 03</p>
                                        <p class="s3" style="padding-top: 5pt; padding-left: 5pt; text-indent: 0pt; text-align: left;">Page : <span class="s4">1 / 1</span></p>
                                    </td>
                                </tr>
                                <tr style="height: 33pt;">
                                    <td style="width: 312pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s5" style="padding-top: 6pt; padding-left: 43pt; padding-right: 42pt; text-indent: 0pt; text-align: center;">RELEVE NOMINATIF DES SALAIRES</p>
                                    </td>
                                </tr>
                            </table>
                            <p style="text-indent: 0pt; text-align: left;"><br></p>
                            <table cellspacing="0" style="border-collapse: collapse;" width="100%">
                                <tr style="height: 31pt;">
                                    <td style="width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s6" style="padding-left: 22pt; padding-right: 22pt; text-indent: 0pt; line-height: 13pt; text-align: center;">Nom et Adresse de</p>
                                        <p class="s6" style="padding-top: 2pt; padding-left: 22pt; padding-right: 22pt; text-indent: 0pt; text-align: center;">l’Employeur</p>
                                    </td>
                                    <td style="width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s6" style="padding-left: 23pt; padding-right: 22pt; text-indent: 0pt; line-height: 13pt; text-align: center;">Numéro</p>
                                        <p class="s6" style="padding-top: 2pt; padding-left: 23pt; padding-right: 22pt; text-indent: 0pt; text-align: center;">Employeur</p>
                                    </td>
                                    <td rowspan="4" style="width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s6" style="padding-left: 7pt; padding-right: 7pt; text-indent: 0pt; text-align: center;">Salaires bruts</p>
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s6" style="padding-left: 8pt; padding-right: 7pt; text-indent: 0pt; line-height: 114%; text-align: center;">annuels soumis à</p>
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s6" style="padding-left: 7pt; padding-right: 7pt; text-indent: 0pt; line-height: 189%; text-align: center;">cotisations C.N.P.S</p>
                                    </td>
                                    <td rowspan="4" style="width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s6" style="padding-left: 12pt; padding-right: 11pt; text-indent: 0pt; line-height: 189%; text-align: center;">Nombre de mois de travail dans l’année</p>
                                        <p class="s7" style="padding-left: 14pt; padding-right: 13pt; text-indent: 0pt; line-height: 114%; text-align: center;">(congé annuel compris)</p>
                                    </td>
                                    <td rowspan="3" style=" width: 133pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s6" style="padding-left: 29pt; padding-right: 29pt; text-indent: 0pt; line-height: 114%; text-align: center;">Date et Lieu d’établissement du document</p>
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s7" style="padding-left: 29pt; padding-right: 29pt; text-indent: 0pt; text-align: center;">{{ $company->city }} / {{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</u></p>
                                    </td>
                                </tr>
                                <tr style="height: 51pt;">
                                    <td style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;"><span class="s9" >{{ $company->name }}</span></p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;"><span class="s9" >{{ $company->postal_code }}</span></p>
                                    </td>
                                </tr>
                                <tr style="height: 39pt;">
                                    <td style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s6" style="padding-left: 45pt; padding-right: 28pt; text-indent: -15pt; line-height: 114%; text-align: left;">Nom et Prénoms du Salarié</p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><span><br></span></p>
                                    </td>
                                </tr>
                                <tr style="height: 43pt;">
                                    <td style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;"><span class="s9">{{$emp_name}}</span></p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s6" style="padding-left: 31pt; text-indent: 0pt; text-align: left;">Années</p>
                                    </td>
                                    <td style=" width: 133pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s6" style="padding-left: 36pt; text-indent: 0pt; text-align: left;">Observations</p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td rowspan="2" style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; padding-right: 13pt; text-indent: 0pt; line-height: 28pt; text-align: left;"><span class="s8">Matricule CNPS Salarié (e)</span> <span class="s7"></span><span class="s9">{{$emp_cnps}}</span></p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 13pt; text-align: left;"><span class="s9"></span></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 13pt; text-align: left;"><span class="s9"></span></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 13pt; text-align: left;"><span class="s9"></span></p>
                                    </td>
                                    <td rowspan="17" style=" width: 133pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 39pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;"><span class="s9"></span></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;"><span class="s9"></span></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; text-align: left;"><span class="s9"></span></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td rowspan="2" style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s8" style="padding-left: 5pt; padding-right: 50pt; text-indent: 0pt; line-height: 28pt; text-align: left;">Date d’embauche <span class="s7">{{ \Carbon\Carbon::parse($emp_doj)->format('d-m-Y') }}</span></p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 13pt; text-align: left;"><span class="s9"></span></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 13pt; text-align: left;"><span class="s9">270 000</span></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="padding-left: 5pt; text-indent: 0pt; line-height: 13pt; text-align: left;"><span class="s9">1</span></p>
                                    </td>
                                </tr>
                                <tr style="height: 39pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td rowspan="2" style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s8" style="padding-left: 5pt; padding-right: 50pt; text-indent: 0pt; line-height: 28pt; text-align: left;">Date de cessation <span class="s7">{{ \Carbon\Carbon::parse($terminations->termination_date)->format('d-m-Y') }}</span></p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 39pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td rowspan="3" style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                        <p class="s8" style="padding-left: 5pt; padding-right: 26pt; text-indent: 0pt; line-height: 224%; text-align: left;">Périodes de cotisations <span class="s7">du : </span></p>
                                        <p class="s7" style="padding-left: 5pt; text-indent: 0pt; line-height: 9pt; text-align: left;">au : </p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 32pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td rowspan="2" style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s7" style="padding-top: 2pt; padding-left: 21pt; padding-right: 21pt; text-indent: 0pt; line-height: 114%; text-align: center;">Nom & Prénoms Signature - Cachet Qualité du signataire</p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td rowspan="4" style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s7" style="padding-left: 5pt; padding-right: 17pt; text-indent: 0pt; line-height: 189%; text-align: left;"> </p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 31pt;">
                                    <td style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p class="s7" style="padding-left: 22pt; padding-right: 22pt; text-indent: 0pt; line-height: 13pt; text-align: center;">Date d’affiliation de</p>
                                        <p class="s7" style="padding-top: 2pt; padding-left: 22pt; padding-right: 22pt; text-indent: 0pt; text-align: center;">l’employeur</p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                                <tr style="height: 26pt;">
                                    <td style=" width: 136pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p align="left"><strong>{{ $company->name }}</strong></p>
                                    </td>
                                    <td style=" width: 98pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 87pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                    <td style=" width: 92pt; border-top-style: solid; border-top-width: 1pt; border-left-style: solid; border-left-width: 1pt; border-bottom-style: solid; border-bottom-width: 1pt; border-right-style: solid; border-right-width: 1pt;">
                                        <p style="text-indent: 0pt; text-align: left;"><br></p>
                                    </td>
                                </tr>
                            </table>
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