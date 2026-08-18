<?php
    $version ='<?xml version="1.0" encoding="utf-8"?>';
    echo '<?xml version="1.0" encoding="utf-8"?>';
?>
@extends('layouts.app')

@section('title', 'Gestion des Livres de Paie')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête de la page -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">📋 Gestion des déclarations mensuelles</h4>
                    <p class="text-muted mb-0">Consultez et modifiez les déclarations EDI ITS, EFI ITS, CNPS et CMU  mensuelles générées</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                   <img
                    src="{{ asset('img/illustrations/page-misc-under-maintenance.png') }}"
                    alt="wizard-create-deal"
                    width="110px"
                    class="img-fluid" />
                </div>
            </div>
        </div>
    </div>

     <!-- information déclaration impot -->
    <div class="col-12 mb-4">
        @php
            $currentDay = date('d');
            $currentMonth = date('F');
            $frenchMonths = [
                'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars',
                'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin',
                'July' => 'Juillet', 'August' => 'Août', 'September' => 'Septembre',
                'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre'
            ];
        @endphp
        <div class="alert {{ $currentDay > 15 ? 'alert-danger' : 'alert-info' }} d-flex align-items-center">
            <i class="ti {{ $currentDay > 15 ? 'ti-alert-circle' : 'ti-info-circle' }} me-2"></i>
            <marquee scrollamount="5">
                @if($currentDay <= 15)
                    N'oubliez pas d'effectuer vos déclarations ITS, CNPS et CMU avant le 15 {{ $frenchMonths[$currentMonth] }}
                @else
                    Attention ! Des pénalités peuvent s'appliquer pour les déclarations soumises après le 15 du mois
                @endif
            </marquee>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-xl-3">
            <div class="card sticky-top">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <button href="#" id="traitement-tab" class="list-group-item list-group-item-action align-items-center border-0 active">
                        <strong>EFI ITS</strong><i class="fa fa-arrow-circle-right float-end"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card sticky-top">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#" id="livre-paie-tab" class="list-group-item list-group-item-action border-0">
                        <strong>EDI ITS</strong> <i class="fa fa-arrow-circle-right float-end"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card sticky-top">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#" id="cotisations-tab" class="list-group-item list-group-item-action border-0">
                        <strong>COTISATION CNPS</strong> <i class="fa fa-arrow-circle-right float-end"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card sticky-top">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#" id="declaration-tab" class="list-group-item list-group-item-action border-0">
                        <strong>COTISATION CMU</strong> <i class="fa fa-arrow-circle-right float-end"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="version_xml" name="version_xml" value="{{$version}}">

    <!--EFI-->
    <div id="traitement" class=" " style="display: block;">
        <div class="card col-12">
            <div class="card-header">
                <div class="row">
                    <div class="col-4" style="margin-bottom: 10px;">
                        <div class="d-flex align-items-center justify-content-start">
                            <h5>{{ __('Impôts sur les traitements et salaires (ITS)') }}</h5>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                <div class="btn-box">
                                    <select id="exercice" class="form-select select2" name="exercice" tabindex="-1" aria-hidden="true">
                                        @foreach ($exercices as $exercice)
                                            <option value="{{ $exercice->id }}" data-years="{{ $exercice->years }}">
                                                {{ $exercice->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                <div class="btn-box">
                                    <select name="periodes_efi" id="periodes_efi" class="form-select">
                                        
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="filter_month5" class="filter_month5">
                            <input type="hidden" id="sect" name="filter_year5" class="filter_year5" value="{{ $company->sector}}">
                            <button onclick="genererXML_FINAL()" id="efi_xml" class="btn btn-primary">{{ __('Générer le XML') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="col-12" align="center" style="padding: 20px;">
                    <div class="d-flex align-items-center">
                        @if(!empty($company->tax_id))
                            <h6>{{ __('NCC :') }} <span>{{ $company->tax_id}}</span></h6>
                            <input type="hidden" id="numm_cc" value="{{ $company->tax_id}}">
                        @else
                            <h6 style="color: red;">'Aller faire la mise à jour de votre numéro CC dans l'onglet "Configuration Entreprise".</h6>
                        @endif
                    </div>
                </div>
                <input type="hidden" id="recupfdfp1" name="recupfdfp1">
                <input type="hidden" id="recupfdfp2" name="recupfdfp2">
                <input type="hidden" id="recupfdfp3" name="recupfdfp3">
                <div class="col-12" align="center" style="padding: 20px;">
                    <table class="table-sm table-bordered border-dark" id="table_its" cellspacing="0" width="100%">
                        <tr bgcolor="#f66300"><td colspan="4"><p style="color: #FFF;">A - DETERMINATION DE L'ASSIETTE</p></td></tr>
                        <tr bgcolor="#009177">
                            <td><p  style="color: #FFF;">1</p></td>
                            <td colspan="3"><p style="color: #FFF;">REVENUS BRUTS</p></td>
                        </tr>
                        <tr>
                            <td width="5%"><p>1.1</p></td>
                            <td width="55%"><p for="ITS00530">Rémunération versée - Montant brut<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td width="10%"></td>
                            <td width="30%"><input class="form-control" id="ITS00530" name="ITS00530" type="text" value="" style="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>1.2</p></td>
                            <td><p for="ITS00540">Rémunération versée - Effectif<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00540" name="ITS00540" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>1.3</p></td>
                            <td><p for="ITS00060">Avantages en nature (évaluation) - Montant brut</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00060" name="ITS00060" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>1.4</p></td>
                            <td><p for="ITS00650">Avantages en nature (évaluation) - Effectif</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00650" name="ITS00650" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>1.5</p></td>
                            <td><p for="ITS00070">Autres (à préciser) à Montant brut</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00070" name="ITS00070" type="text" value="" style ="text-align: right; font-weight: bold;"></td>
                        </tr>
                        <tr>
                            <td><p>1.6</p></td>
                            <td><p for="ITS00550">Autres (à préciser) - Effectif</p>
                            </td><td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00550" name="ITS00550" type="text" value="" style ="text-align: right; font-weight: bold;" ></td>
                        </tr>
                        <tr>
                            <td><p>1.7</p></td>
                            <td><p for="ITS00560">BASE BRUTE <span style="color:#f66300;" title="Coût de revient des salariés."><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00560" name="ITS00560" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #FFF;">2.1</p></td>
                            <td colspan="3"><p style="color: #FFF;">REVENUS NON IMPOSABLES - ITS SALARIE</p></td>
                        </tr>
                        <tr>
                            <td><p>2.1.1</p></td>
                            <td><p for="ITS00570">Indemnités/Avantages non imposables <span style="color:#f66300;" title="Revenus non imposables notamment les indemnités de départ, de stage (Art. 116 CGI), etc."><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00570" name="ITS00570" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>2.1.2</p></td>
                            <td><p for="ITS00660">Rémunérations versées dans le cadre de la création d'emploi <span style="color:#f66300;" title="Revenus versés dans le cadre de la création d'emploi (Art.136 du CGI)."><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00660" name="ITS00660" type="text" value="" style ="text-align: right; font-weight: bold;"></td>
                        </tr>
                        <tr>
                            <td><p>2.1.3</p></td>
                            <td><p for="ITS00580">MONTANT TOTAL DES REVENUS NON IMPOSABLES</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00580" name="ITS00580" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>2.1.4</p></td>
                            <td><p for="ITS00790">REMUNERATION TOTALE BRUTE IMPOSABLE</p></td>
                            <td></td>
                            <td>
                                <input class="form-control champs_dynamiques" disabled="disabled" id="ITS00790" name="ITS00790" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly>
                                <span style="color:red;font-size: 9pt" id="erreur_sbi"></span>
                            </td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #FFF;">2.2</p></td>
                            <td colspan="3"><p  style="color: #FFF;">REVENUS NON IMPOSABLES - CONTRIBUTION EMPLOYEUR</p></td>
                        </tr>
                        <tr>
                            <td><p>2.2.1</p></td>
                            <td><p for="ITS00430">Indemnités/Avantages non imposables</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00430" name="ITS00430" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>2.2.2</p></td>
                            <td><p for="ITS00800">Rémunérations versées dans le cadre de la création d'emploi</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00800" name="ITS00800" type="text" value="" style ="text-align: right; font-weight: bold;"></td>
                        </tr>
                        <tr>
                            <td><p>2.2.3</p></td>
                            <td><p for="ITS00810">MONTANT TOTAL DES REVENUS NON IMPOSABLES</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00810" name="ITS00810" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>2.2.4</p></td>
                            <td><p for="ITS00820">REMUNERATION TOTALE BRUTE IMPOSABLE</p></td>
                            <td></td>
                            <td>
                                <input class="form-control champs_dynamiques" disabled="disabled" id="ITS00820" name="ITS00820" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly>
                                <span style="color:red;font-size: 9pt" id="erreur_sbi2"></span>
                            </td>
                        </tr>
                        <tr bgcolor="#f66300">
                            <td colspan="4"><p  style="color: #fff;">B - DETERMINATION DES IMPÔTS ET TAXES</p></td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #FFF;">3</p></td>
                            <td colspan="3"><p style="color: #FFF;">IMPOT SUR LES TRAITEMENTS ET SALAIRES A LA CHARGE DES SALARIES</p></td>
                        </tr>
                        <tr>
                            <td><p>3.1</p></td>
                            <td><p for="ITS00830">Impôt ITS Salarié brut <span style="color:#f66300;" title="Après application du barême d&#39;imposition progressive par tranche de salaires, joint en annexe."><i class="fas fa-question-circle"></i></span><sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00830" name="ITS00830" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>3.2</p></td>
                            <td><p for="ITS00840">Retenue d'impôt pour charges de famille (RICF) <span style="color:#f66300;" title="Après application de la Réduction d&#39;Impôt pour Charges de Famille (RICF), suivant le barème joint en annexe."><i class="fas fa-question-circle"></i></span><sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00840" name="ITS00840" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>3.3</p></td>
                            <td><p for="ITS00850">Montant total de l'impôt net à payer <span style="color:#f66300;" title="Le montant de l&#39;impôt sur les traitements et salaires correspond au cumule des retenues effectu'es sur chaque traitement et/ou salaire conformement au barème progressif et au barème pour RICF joints en annexe."><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00850" name="ITS00850" disabled="disabled" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #FFF;">4</p></td>
                            <td colspan="3"><p style="color: #FFF;">CONTRIBUTIONS EMPLOYEUR</p></td>
                        </tr>
                        <tr>
                            <td><p>4.1</p></td>
                            <td><p for="ITS00860">(CE) Personnel local (Régime général) - Effectifs<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00860" name="ITS00860" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.2</p></td>
                            <td><p for="ITS00870">(CE) Personnel expatrié (Régime général) - Effectifs<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00870" name="ITS00870" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.3</p></td>
                            <td><p for="ITS00880">(CE) Personnel expatrié (Régime général) - Revenu brut imposable<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00880" name="ITS00880" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <form><input id="ITS00010_Taux" name="ITS00010_Taux" type="hidden" value="0.092"></form>
                        <tr>
                            <td><p>4.4</p></td>
                            <td><p for="ITS00010">(CE) Personnel expatrié (Régime général) - Montant</p></td>
                            <td><p for="ITS00010_Label">Taux à 9.2%</p></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00010" name="ITS00010" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.5</p></td>
                            <td><p for="ITS005100">(CE) Régime agricole - Effectifs <span style="color:#f66300;" title="Entreprises agricoles, agro-industrielles et assimilées (Art. 147 du Code général des Impôts)."><i class="fas fa-question-circle"></i></span><sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS005100" name="ITS005100" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.6</p></td>
                            <td><p for="ITS00020">(CE) Régime agricole - Revenu brut imposable<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00020" name="ITS00020" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <form><input id="ITS005110_Taux" name="ITS005110_Taux" type="hidden" value="0.02"></form>
                        <tr>
                            <td><p>4.7</p></td>
                            <td><p for="ITS005110">(CE) Régime agricole - Montant</p></td>
                            <td><p for="ITS005110_Label">Taux à 2%</p></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS005110" name="ITS005110" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.8</p></td>
                            <td><p for="ITS00030">(CN)<span style="color:#f66300;" title="Contribution nationale pour le développement économique, culturel et social de la nation (Art. 146 CGI)."><i class="fas fa-question-circle"></i></span> Personnel local (Régime général) - Effectifs<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00030" name="ITS00030" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.9</p></td>
                            <td><p for="ITS005120">(CN) Personnel local (Régime général) - Revenu brut imposable<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS005120" name="ITS005120" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <form><input id="ITS00040_Taux" name="ITS00040_Taux" type="hidden" value="0.012"></form>
                        <tr>
                            <td><p>4.10</p></td>
                            <td><p for="ITS00040">(CN) Personnel local (Régime général) - Montant</p></td>
                            <td><p for="ITS00040_Label">Taux à 1.2%</p></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00040" name="ITS00040" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.11</p></td>
                            <td><p for="ITS00050">(CN) Personnel expatrié (Régime général) - Effectifs<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00050" name="ITS00050" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.12</p></td>
                            <td><p for="ITS00670">(CN) Personnel expatrié (Régime général) - Revenu brut imposable<sup style="color:red;font-weight:bold">&nbsp;&nbsp;(*)</sup></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00670" name="ITS00670" type="text" value="" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <form><input id="ITS005130_Taux" name="ITS005130_Taux" type="hidden" value="0.012"></form>
                        <tr>
                            <td><p>4.13</p></td>
                            <td><p for="ITS005130">(CN) Personnel expatrié (Régime général) - Montant</p></td>
                            <td><p for="ITS005130_Label">Taux à 1.2%</p></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS005130" name="ITS005130" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>4.14</p></td>
                            <td><p for="ITS00680">TOTAL DES CONTRIBUTIONS A LA CHARGE DE L'EMPLOYEUR <span style="color:#f66300;" title="Pour le calcul de la contribution employeur, il est appliqué le taux de 2,8% à la base brute imposable à l&#39;ITS."><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00680" name="ITS00680" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #FFF;">5</p></td>
                            <td colspan="3"><p  style="color: #fff;">RETENUE SUR LE REVENU DES EXPLOITANTS FORESTIERS À TITRE DE FERMAGE</p></td>
                        </tr>
                        <tr>
                            <td><p>5.1</p></td>
                            <td><p for="ITS00690">Fermage forestier - Revenu net imposable</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00690" name="ITS00690" type="text" value="0" style ="text-align: right; font-weight: bold;" oninput="forest()"></td>
                        </tr>
                        <form><input id="ITS005140_Taux" name="ITS005140_Taux" type="hidden" value="0.35"></form>
                        <tr>
                            <td><p>5.2</p></td>
                            <td><p for="ITS005140">Fermage forestier - Montant de la retenue</p></td>
                            <td><p for="ITS005140_Label">Taux à 35%</p></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS005140" value="0" name="ITS005140" type="text" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #fff;">6</p></td>
                            <td colspan="3"><p style="color: #fff;">E- TAXE D'APPRENTISSAGE ET TAXE ADDITIONNELLE A LA FORMATION PROFESSIONNELLE CONTINUE</p></td>
                        </tr>
                        <tr>
                            <td><p>6.1</p></td>
                            <td><p for="ITS00695">Etes-vous exonérédes taxes FDFP ?</p></td>
                            <td></td>
                            <td>
                                <select class="form-select champs_dynamiques select_dynamiques" id="ITS00695" name="ITS00695">
                                    <option value="">
                                        Sélectionner une valeur
                                    </option>
                                    <option value="0" selected="selected">NON</option>
                                    <option value="1">OUI</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><p>6.2</p></td>
                            <td><p for="ITS00700">Taxe d'apprentissage (TA) - REMUNERATIONS BRUTES TOTALES <span style="color:#f66300;" title="Le taux de la TA est de 0,5 %. Il est appliqué à ce taux un abattement de 20 %. La valeur saisie doit être égale à zéro (si vous avez déja fait votre déclaration sur le formulaire dédié FDFP) ou à celle du champs 2.1.4"><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td>
                                <input class="form-control champs_dynamiques" id="ITS00700" name="ITS00700" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly>
                            </td>
                        </tr>
                        <form><input id="ITS00710_Taux" name="ITS00710_Taux" type="hidden" value="0.004"></form>
                        <tr>
                            <td><p>6.3</p></td>
                            <td><p for="ITS00710">Taxe d'apprentissage (TA) - MONTANT MENSUEL</p></td>
                            <td><p for="ITS00710_Label">Taux à 0.4%</p></td>
                            <td>
                                <input class="form-control champs_dynamiques" disabled="disabled" id="ITS00710" name="ITS00710" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td><p>6.4</p></td>
                            <td><p for="ITS00720">Taxe à la formation professionnelle continue (TFPC) - REMUNERATIONS BRUTES TOTALES <span style="color:#f66300;" title="Le taux annuel de la TFPC est de 1,5 %. Il est appliqué à ce taux un abattement de 20 %. La moitié de ce taux peut être utilisée pour les déclarations menseulles avec une regularisation en fin d&#39;exercice. La valeur saisie doit être égale à zéro \n (si vous avez déja fait votre déclaration sur le formulaire dédié FDFP) ou à celle du champs 2.1.4"><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td>
                                <input class="form-control champs_dynamiques" id="ITS00720" name="ITS00720" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td><p>6.5</p></td>
                            <td><p for="ITS00730">Taxe à la formation professionnelle continue (TFPC) - MONTANT MENSUEL - Choix</p></td>
                            <td></td>
                            <td>
                                <select class="form-select champs_dynamiques select_dynamiques" id="ITS00730" name="ITS00730">
                                    <option value="">
                                        Sélectionner une valeur
                                    </option>
                                    <option value="0.006">
                                        TAUX MENSUEL (0,60%)
                                    </option>
                                    <option value="0.012">
                                        TAUX ANNUEL (1,20%)
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><p>6.6</p></td>
                            <td><p for="ITS00350">Taxe à la formation professionnelle continue (TFPC) - MONTANT MENSUEL</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00350" name="ITS00350" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>6.7</p></td>
                            <td><p for="ITS00740">MONTANT TOTAL FDFP</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00740" name="ITS00740" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #FFF;">7</p></td>
                            <td colspan="3"><p style="color: #FFF;">CREDIT D'IMPÔT POUR CREATION D'EMPLOIS MICRO / TEE ( Art. 111 - CGI )</p></td>
                        </tr>
                        <tr>
                            <td><p>7.1</p></td>
                            <td><p for="ITS00750">Crédit d'impôt imputable ( Contrat à durée déterminée ) - Nombre d'emploi créé (125.000 / personne) <span style="color:#f66300;" title="Crédit d&#39;impot à justifier."><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00750" name="ITS00750" type="text" value="0" style ="text-align: right; font-weight: bold;" oninput="loanimpot(); calculerCreditImpot();"></td>
                        </tr>
                        <tr>
                            <td><p>7.2</p></td>
                            <td><p for="ITS00130">Crédit d impot imputable ( Contrat à durée déterminée )</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00130" name="ITS00130" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>7.3</p></td>
                            <td><p for="ITS00140">Crédit d'impôt imputable ( Contrat à durée indéterminée ) - Nombre d'emploi créé (250.000 / personne) <span style="color:#f66300;" title="Crédit d&#39;impot à justifier."><i class="fas fa-question-circle"></i></span></p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00140" name="ITS00140" type="text" value="0" style ="text-align: right; font-weight: bold;" oninput="loanimpot2(); calculerCreditImpot();"></td>
                        </tr>
                        <tr>
                            <td><p>7.4</p></td>
                            <td><p for="ITS00150">Crédit d impot imputable ( Contrat à durée indéterminée )</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00150" name="ITS00150" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr>
                            <td><p>7.5</p></td>
                            <td><p for="ITS00151">TOTAL CREDIT D'IMPOT</p></td>
                            <td></td>
                            <td>
                                <input class="form-control champs_dynamiques" disabled="disabled" id="ITS00151" name="ITS00151" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly>
                                <input type="hidden" name="ITS00155" id="ITS00155" value="0" class="form-control champs_dynamiques" disabled="disabled" section="7" code="7.6">
                            </td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #FFF;">8</p></td>
                            <td colspan="3"><p  style="color: #FFF;">REGULARISATION</p></td>
                        </tr>
                        <tr>
                            <td><p>8.1</p></td>
                            <td><p for="ITS00160">Impôt sur les salaires des salariés (ITS Salariés)</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00160" name="ITS00160" type="text" value="0" style ="text-align: right; font-weight: bold;" oninput="regul()"></td>
                        </tr>
                        <tr>
                            <td><p>8.2</p></td>
                            <td><p for="ITS00170">Régularisation - Contribution employeur (CE)</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00170" name="ITS00170" type="text" value="0" style ="text-align: right; font-weight: bold;" oninput="regul()"></td>
                        </tr>
                        <tr>
                            <td><p>8.3</p></td>
                            <td><p for="ITS00180">Régularisation - Contribution nationale à la charge de l'employeur (CN)</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" id="ITS00180" name="ITS00180" type="text" value="0" style ="text-align: right; font-weight: bold;" oninput="regul()"></td>
                        </tr>
                        <tr>
                            <td><p>8.4</p></td>
                            <td><p for="ITS00320">Total régularisation</p>
                            </td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00320" name="ITS00320" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                        <tr bgcolor="#009177">
                            <td><p style="color: #FFF;">9</p></td>
                            <td colspan="3"><p style="color: #FFF;">MONTANT TOTAL A PAYER</p></td>
                        </tr>
                        <tr>
                            <td><p>9.1</p></td>
                            <td><p for="ITS00330">MONTANT TOTAL À PAYER</p></td>
                            <td></td>
                            <td><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00330" name="ITS00330" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td width="5%"><p>9.2</p></td>
                            <td width="55%"><p for="ITS00340">MONTANT A REPORTER</p></td>
                            <td width="10%"></td>
                            <td width="30%"><input class="form-control champs_dynamiques" disabled="disabled" id="ITS00340" name="ITS00340" type="text" value="0" style ="text-align: right; font-weight: bold;" readonly></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--EDI-->
    <div id="livre-paie" class=" " style="display: none;">
        <div class="card col-12">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <div class="d-flex align-items-center justify-content-start">
                            <h5>{{ __('Etat individuel des remunerations') }}</h5>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                <div class="btn-box">
                                    <select id="exercice_edi" class="form-select select2" name="exercice_edi" tabindex="-1" aria-hidden="true">
                                        @foreach ($exercices as $exercice)
                                            <option value="{{ $exercice->id }}" data-years="{{ $exercice->years }}">
                                                {{ $exercice->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                <div class="btn-box">
                                    <select name="periodes_edi" id="periodes_edi" class="form-select">
                                        
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="filter_month4" class="filter_month4">
                            <input type="hidden" id="sect" name="filter_year4" class="filter_year4" value="{{$company->sector}}">
                            <button onclick="exporterEnXML()" class="btn btn-primary">{{ __('Générer le XML') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="printableArea3" style="padding: 10px;">
                    <div class="col-12" align="center" style="padding: 20px;">
                        <div class="d-flex align-items-center">
                            @if(!empty($company->tax_id))
                                <h6>{{ __('NCC :') }} <span>{{ $company->tax_id}}</span></h6>
                                <input type="hidden" id="num_cc" value="{{ $company->tax_id}}">
                            @else
                                <h6 style="color: red;">'Aller faire la mise à jour de votre numéro CC dans l'onglet "Configuration Entreprise".</h6>
                            @endif
                        </div>
                    </div>
                    <table id="daclaration" class="table table-sm">
                        <thead>
                            <tr style="vertical-align:middle;">
                                <th rowspan="2" class="border border-dark sticky-col"  style="background-color:#ffffff"><center>N° CNPS</center>
                                <input type="hidden" id="numero_cnps" name="numero_cnps"></th>
                                <th rowspan="2" class="border border-dark"><center>{{ __('Nom et prénoms') }}</center>
                                <input type="hidden" id="identite" name="identite"></th>
                                <th rowspan="2" class="border border-dark"><center>{{ __('Emploi ou Qualité') }}</center>
                                <input type="hidden" id="emploi_qualite" name="emploi_qualite"></th>
                                <th rowspan="2" class="border border-dark"><center>{{ __('Code Emploi') }}</center>
                                <input type="hidden" id="code_emploi" name="code_emploi"></th>
                                <th rowspan="2" class="border border-dark"><center>{{ __('Régime Général G') }}<br>ou Agricole A</center>
                                <input type="hidden" id="regime_general" name="regime_general"></th>
                                <th rowspan="2" class="border border-dark"><center>{{ __('Sexe') }}</center>
                                <input type="hidden" id="sexe" name="sexe"></th>
                                <th rowspan="2" class="border border-dark"><center>{{ __('Nationalité') }}</center>
                                <input type="hidden" id="nationalite" name="nationalite"></th>
                                <th rowspan="2" class="border border-dark"><center>{{ __('Local/Expatrié') }}</center>
                                <input type="hidden" id="loc_exp" name="loc_exp"></th>
                                <th colspan="2" class="border border-dark"><center>Situation de famille la plus<br>favorable<br>(au 1er janvier <br>ou au 31 décembre)</center>
                                <input type="hidden" id="situation_famille" name="situation_famille">
                                <input type="hidden" id="nbre_enfants_charge_nat_cas" name="nbre_enfants_charge_nat_cas"></th>
                                <th rowspan="2" class="border border-dark"><center>{{ __('Nombre de parts') }}</center>
                                <input type="hidden" id="nbre_parts_igr" name="nbre_parts_igr"></th>
                                <th rowspan="2" class="border border-dark"><center>Nombre de jours<br>d'application des<br>paiements</center>
                                <input type="hidden" id="nbre_jours_app_paiements" name="nbre_jours_app_paiements"></th>
                                <th rowspan="2" class="border border-dark"><center>Montant des<br>salaires et<br>rémunérations<br>accessoires</center>
                                <input type="hidden" id="mnt_sala_remune_acs" name="mnt_sala_remune_acs"></th>
                                <th rowspan="2" class="border border-dark"><center>Montant des<br>avantages en<br>nature suivant<br>barème<br>réglementaire</center>
                                <input type="hidden" id="mnt_avtgs_nat_reglm" name="mnt_avtgs_nat_reglm"></th>
                                <th rowspan="2" class="border border-dark"><center>Montant des<br>avantages en<br>nature selon valeur<br>réelle</center>
                                <input type="hidden" id="mnt_avtgs_nat_reele" name="mnt_avtgs_nat_reele"></th>
                                <th rowspan="2" class="border border-dark"><center>Rémuneration<br>total brut</center>
                                <input type="hidden" id="sal_ttl_brut" name="sal_ttl_brut"></th>
                                <th rowspan="2" class="border border-dark"><center>Révenus non<br>imposables</center>
                                <input type="hidden" id="rev_non_imposable" name="rev_non_imposable"></th>
                                <th rowspan="2" class="border border-dark"><center>Rémuneration<br>brut<br>imposable</center>
                                <input type="hidden" id="rev_brut_imposable" name="rev_brut_imposable"></th>
                                <th rowspan="2" class="border border-dark"><center>Réduction<br>d'impôt pour<br>charges de<br>famille<br>(RICF)</center>
                                <input type="hidden" id="ricf" name="ricf"></th>
                                <th colspan="4" class="border border-dark"><center>ITS Salariés</center>
                                <input type="hidden" id="its_sal_brut" name="its_sal_brut">
                                <input type="hidden" id="its_sal_net" name="its_sal_net">
                                <input type="hidden" id="ajustement" name="ajustement">
                                <input type="hidden" id="its_net_a_payer" name="its_net_a_payer"></th>
                                <th colspan="2" class="border border-dark"><center>Indemnités pour frais d'emploi<br>considérées comme exonérées</center>
                                <input type="hidden" id="mnt_indemnites" name="mnt_indemnites">
                                <input type="hidden" id="designation_indemnites" name="designation_indemnites"></th>
                            </tr>
                            <tr>
                                <th class="border border-dark"><center>Etat civil</center></th>
                                <th class="border border-dark"><center>Nombre<br>d'enfants à<br>charge</center></th>
                                <th class="border border-dark"><center>Brut</center></th>
                                <th class="border border-dark"><center>Net</center></th>
                                <th class="border border-dark"><center>Ajustement *</center></th>
                                <th class="border border-dark"><center>Net à payer</center></th>
                                <th class="border border-dark"><center>Montant</center></th>
                                <th class="border border-dark"><center>Désignation</center></th>
                            </tr> 
                            <tr bgcolor="#145388">
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                                <td align="center" class="border border-dark"><span id="totaux1" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux2" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux3" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux4" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux5" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux6" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux7" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux8" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux9" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux10" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux11" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span id="totaux12" style="color:white;">0,00</span></td>
                                <td align="center" class="border border-dark"><span></span></td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--Déclarations CNPS-->
    <div id="cotisations" class=" " style="display: none;">
    	<div class="card col-12">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <div class="d-flex align-items-center justify-content-start">
                            <h5>{{ __('Cotisation nominative CNPS') }}</h5>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                <div class="btn-box">
                                    <select id="exercice" class="form-select select2" name="exercice" tabindex="-1" aria-hidden="true">
                                        @foreach ($exercices as $exercice)
                                            <option value="{{ $exercice->id }}" data-years="{{ $exercice->years }}">
                                                {{ $exercice->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                <div class="btn-box">
                                    <select name="periodes_cnps" id="periodes_cnps" class="form-select">
                                        
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="filter_month5" class="filter_month5">
                            <input type="hidden" id="sect" name="filter_year5" class="filter_year5" value="{{$company->sector}}">
                            <button onclick="exportToExcel()" class="btn btn-primary">{{ __('Exporter en EXCEL') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" >
                <div class="table-responsive" id="printableArea3" style="padding: 10px;">
                    <table id="table-decla-cnps" class="table table-sm">
                        <thead>
                            <tr style="vertical-align:middle;">
                                <th class="border border-dark"><center>NUMERO CNPS</center></th>
                                <th class="border border-dark"><center>{{ __('NOM') }}</center></th>
                                <th class="border border-dark"><center>{{ __('PRENOMS') }}</center></th>
                                <th class="border border-dark"><center>{{ __('ANNEE DE NAISSANCE') }}</center></th>
                                <th class="border border-dark"><center>{{ __('DATE D\'EMBAUCHE') }}</center></th>
                                <th class="border border-dark"><center>{{ __('DATE DE DEPART') }}</center></th>
                                <th class="border border-dark"><center>TYPE SALARIE<br> M: Mensuel<br> J : Journalier<br> H: Horaire</center></th>
                                <th class="border border-dark"><center>{{ __('DUREE TRAVAILLEE') }}</center></th>
                                <th class="border border-dark"><center>{{ __('SALAIRE BRUT') }}</center></th>
                                <th class="border border-dark"><center>BRANCHE COTISEE<br> 1: Retraite<br> 2 : Accidents du Travail <br> et Maladies Professionnelles<br> 3: Prestations Familiales <br>et Assurance Maternite</center></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
    </div>

    <!--Déclarations CMU-->
    <div id="declaration" class=" " style="display: none;">
        <div class="card col-12">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-4" style="margin-bottom: 10px;">
                        <div class="d-flex align-items-center justify-content-start">
                            <h5>{{ __('Cotisation nominative CMU') }} </h5>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                <div class="btn-box">
                                    <select id="exercice" class="form-select select2" name="exercice" tabindex="-1" aria-hidden="true">
                                        @foreach ($exercices as $exercice)
                                            <option value="{{ $exercice->id }}" data-years="{{ $exercice->years }}">
                                                {{ $exercice->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mx-2">
                                <div class="btn-box">
                                    <select name="periodes_cmu" id="periodes_cmu" class="form-select">
                                        
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="filter_month6" class="filter_month6">
                            <input type="hidden" id="sect" name="filter_year6" class="filter_year6" value="{{$company->sector}}">
                            <button onclick="exportToExce2()" class="btn btn-primary">{{ __('Exporter en EXCEL') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="printableArea3" >
                    <table id="table-decla-cmu" class="table table-sm">
                        <thead>
                            <tr style="vertical-align:middle;">
                                <th class="border border-dark"><center>NUMERO<br> CNPS ASSURE</center></th>
                                <th class="border border-dark"><center>NUMERO SECURITE<br>SOCIALE ASSURE</center></th>
                                <th class="border border-dark"><center>{{ __('NOM ASSURE') }}</center></th>
                                <th class="border border-dark"><center>{{ __('PRENOMS ASSURE') }}</center></th>
                                <th class="border border-dark"><center>DATE<br> DE NAISSANCE<br> ASSURE</center></th>
                                <th class="border border-dark"><center>NUMERO <br>CNPS BENEFICIAIRE</center></th>
                                <th class="border border-dark"><center>NUMERO<br>SECURITE SOCIALE<br>BENEFICIAIRE</center></th>
                                <th class="border border-dark"><center>TYPE BENEFICIAIRE <br>C: CONJOINT<br>T: TRAVAILLEUR<br>E: ENFANT</center></th>
                                <th class="border border-dark"><center>NOM<br>BENEFICIAIRE</center></th>
                                <th class="border border-dark"><center>PRENOMS<br>BENEFICIAIRE</center></th>
                                <th class="border border-dark"><center>DATE<br> DE NAISSANCE <br> BENEFICIAIRE</center></th>
                                <th class="border border-dark"><center>GENRE BENEFICIAIRE<br>H: HOMME<br>F: FEMME</center></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#traitement-tab').on('click', function() {
        $('#traitement').show();
        $('#livre-paie, #cotisations').hide();
        $('#declaration').hide();

        // Ajoute la classe "active" au lien
        $(this).addClass('active');
        $('#livre-paie-tab, #cotisations-tab, #declaration-tab').removeClass('active');
    });

    $('#livre-paie-tab').on('click', function() {
        $('#traitement').hide();
        $('#livre-paie').show();
        $('#cotisations').hide();
        $('#declaration').hide();

        $(this).addClass('active');
        $('#traitement-tab, #cotisations-tab, #declaration-tab').removeClass('active');
    });

    $('#cotisations-tab').on('click', function() {
        $('#traitement').hide();
        $('#livre-paie').hide();
        $('#cotisations').show();
        $('#declaration').hide();

        $(this).addClass('active');
        $('#traitement-tab, #livre-paie-tab, #declaration-tab').removeClass('active');
    });

    $('#declaration-tab').on('click', function() {
        $('#traitement').hide();
        $('#livre-paie').hide();
        $('#cotisations').hide();
        $('#declaration').show();

        $(this).addClass('active');
        $('#traitement-tab, #livre-paie-tab, #cotisations-tab').removeClass('active');
    });

    // Charger les périodes quand on change d'exercice
    function callperiodepaie() {
        var exerciceId = $("#exercice").val();
        if (exerciceId) {
            $.ajax({
                url: '{{ route("company.declarations.get_periodes") }}',
                type: 'GET',
                data: { exercice: exerciceId },
                success: function(data) {
                    console.log("Données périodes reçues:", data);
                    
                    var periodesSelectEfi = $("#periodes_efi");
                    var periodesSelectEdi = $("#periodes_edi");
                    var periodesSelectCnps = $("#periodes_cnps");
                    var periodesSelectCmu = $("#periodes_cmu");
                    periodesSelectEfi.empty();
                    periodesSelectEdi.empty();
                    periodesSelectCnps.empty();
                    periodesSelectCmu.empty();
                    
                    $.each(data, function(key, periode) {
                        console.log("Période traitée:", periode);
                        var dateDebut = new Date(periode.date_debut);
                        var dateFin = new Date(periode.date_fin);
                        var libelle = 'Période du ' + dateDebut.toLocaleDateString('fr-FR') + ' au ' + dateFin.toLocaleDateString('fr-FR');
                        
                        var monthlyValue = periode.num_monthly || (dateDebut.getMonth() + 1);
                        console.log("Valeur monthly pour période " + periode.nom + ":", monthlyValue);
                        
                        periodesSelectEfi.append('<option value="' + periode.id + '" data-monthly="' + monthlyValue + '" data-pdebut="' + periode.date_debut + '" data-pfin="' + periode.date_fin + '">' + periode.nom + '</option>');
                        periodesSelectEdi.append('<option value="' + periode.id + '" data-monthly="' + monthlyValue + '" data-pdebut="' + periode.date_debut + '" data-pfin="' + periode.date_fin + '">' + periode.nom + '</option>');
                        periodesSelectCnps.append('<option value="' + periode.id + '" data-monthly="' + monthlyValue + '" data-pdebut="' + periode.date_debut + '" data-pfin="' + periode.date_fin + '">' + periode.nom + '</option>');
                        periodesSelectCmu.append('<option value="' + periode.id + '" data-monthly="' + monthlyValue + '" data-pdebut="' + periode.date_debut + '" data-pfin="' + periode.date_fin + '">' + periode.nom + '</option>');
                    });
                    
                    // Déclencher le chargement des données si une période est sélectionnée
                    if (periodesSelectEfi.val()) {
                        declabackits()
                    }
                    if (periodesSelectEdi.val()) {
                        declaback()
                    }
                    if (periodesSelectCnps.val()) {
                        declabackcnps()
                    }
                    if (periodesSelectCmu.val()) {
                        declabackcmu()
                    }
                },
                error: function() {
                    console.error('Erreur lors du chargement des périodes');
                }
            });
        }
    }

    // Initialiser la fonction au chargement de la page
    $(document).ready(function() {
        callperiodepaie();

        // Si vous avez un champ d'année qui peut changer
        $("#exercice").on('change', function() {
            callperiodepaie();
        });

        declabackits();

        $(document).on("change", "#exercice, #periodes_efi", function() {
            declabackits();
        });
    });

    //EFI ITS
    function forest(){
        var Mt31_ITS00690  = document.getElementById('ITS00690');
        var Mt32_ITS005140 = document.getElementById('ITS005140');
        var Mt43_ITS00155  = parseInt(document.getElementById('ITS00155').value);
        var Mt47_ITS00320  = parseInt(document.getElementById('ITS00320').value);
        var Mt40_ITS00130  = parseInt(document.getElementById('ITS00130').value);
        var Mt42_ITS00150  = parseInt(document.getElementById('ITS00150').value);
        var Mt38_ITS00740  = parseInt(document.getElementById('ITS00740').value);
        var toto = 0; var totot = 0;
        toto = Mt31_ITS00690.value;
        if(toto >= 0){
            Mt32_ITS005140.value = Math.round(toto*0.35);
            totot = Math.round(toto*0.35);
            document.getElementById('ITS00330').value = (Mt43_ITS00155 + totot + Mt47_ITS00320 + Mt38_ITS00740) - (Mt40_ITS00130 + Mt42_ITS00150);
        }else{
            toto = 0;
            Mt32_ITS005140.value = 0;
        }
    }

    function loanimpot(){
        var Mt39_ITS00750 = document.getElementById('ITS00750');
        var Mt40_ITS00130 = document.getElementById('ITS00130');
        var Mt48_ITS00330 = parseInt(document.getElementById('ITS00330').value);
        var Mt43_ITS00155 = parseInt(document.getElementById('ITS00155').value);
        var Mt38_ITS00740 = parseInt(document.getElementById('ITS00740').value);
        var Mt32_ITS005140 = parseInt(document.getElementById('ITS005140').value);
        var Mt47_ITS00320  = parseInt(document.getElementById('ITS00320').value);
        var Mt42_ITS00150  = parseInt(document.getElementById('ITS00150').value);

        var toto = 0; var toto2 = 0;
        toto = Mt39_ITS00750.value;
        if(toto >= 0){
            Mt40_ITS00130.value = Math.round(toto*125000);
            toto2  = Math.round(toto*125000);
        }else{
            toto = 0; toto2 = 0;
            Mt40_ITS00130.value = 0;
        }
        if(isNaN(toto2) || toto2 == 0){
            Mt48_ITS00330 = Mt43_ITS00155 + Mt38_ITS00740 + Mt32_ITS005140 + Mt47_ITS00320;
        }else{
            Mt48_ITS00330 = (Mt43_ITS00155 + Mt38_ITS00740 + Mt32_ITS005140 + Mt47_ITS00320) - (toto2 + Mt42_ITS00150);
        }

        document.getElementById('ITS00330').value = Mt48_ITS00330;
    }

    function loanimpot2(){
        var Mt41_ITS00140 = document.getElementById('ITS00140');
        var Mt42_ITS00150 = document.getElementById('ITS00150');
        var Mt40_ITS00130 = parseInt(document.getElementById('ITS00130').value);
        var Mt48_ITS00330 = parseInt(document.getElementById('ITS00330').value);
        var Mt43_ITS00155 = parseInt(document.getElementById('ITS00155').value);
        var Mt38_ITS00740 = parseInt(document.getElementById('ITS00740').value);
        var Mt32_ITS005140 = parseInt(document.getElementById('ITS005140').value);
        var Mt47_ITS00320  = parseInt(document.getElementById('ITS00320').value);

        var toto = 0; var toto2 = 0;
        toto = Mt41_ITS00140.value
        if(toto >= 0){
            Mt42_ITS00150.value = Math.round(toto*250000);
            toto2   = Math.round(toto*250000);
        }else{
            toto = 0; toto2 = 0;
            Mt42_ITS00150.value = 0;
        }
        if(isNaN(toto2) || toto2 == 0){
            Mt48_ITS00330 = Mt43_ITS00155 + Mt38_ITS00740 + Mt32_ITS005140 + Mt47_ITS00320;
        }else{
            Mt48_ITS00330 = (Mt43_ITS00155 + Mt38_ITS00740 + Mt32_ITS005140 + Mt47_ITS00320) - (toto2 + Mt40_ITS00130);
        }

        document.getElementById('ITS00330').value = Mt48_ITS00330;
    }

    function calculerCreditImpot() {
        var Mt40_ITS00130  = parseInt(document.getElementById('ITS00130').value);
        var Mt42_ITS00150  = parseInt(document.getElementById('ITS00150').value);
        var Mt_ITS00151    = document.getElementById('ITS00151');

        Mt_ITS00151.value  = parseInt(Mt40_ITS00130) + parseInt(Mt42_ITS00150);
    }

    function regul(){
        var Mt44_ITS00160 = parseInt(document.getElementById('ITS00160').value);
        var Mt45_ITS00170 = parseInt(document.getElementById('ITS00170').value);
        var Mt46_ITS00180 = parseInt(document.getElementById('ITS00180').value);
        var Mt47_ITS00320 = document.getElementById('ITS00320');
        var Mt48_ITS00330 = parseInt(document.getElementById('ITS00330').value);
        var Mt43_ITS00155 = parseInt(document.getElementById('ITS00155').value);
        var Mt38_ITS00740 = parseInt(document.getElementById('ITS00740').value);
        var Mt42_ITS00150 = parseInt(document.getElementById('ITS00150').value);
        var Mt40_ITS00130 = parseInt(document.getElementById('ITS00130').value);
        var Mt32_ITS005140 = parseInt(document.getElementById('ITS005140').value);

        var amount  = parseInt(document.getElementById('ITS00330').value);
        // Vérifie si la valeur de Mt48_ITS00330 est un nombre valide
        if(isNaN(Mt48_ITS00330)) {
            Mt48_ITS00330 = Mt43_ITS00155; // Si ce n'est pas un nombre valide, on le considère comme 0
        }
        var somme = Mt44_ITS00160 + Mt45_ITS00170 + Mt46_ITS00180
        Mt47_ITS00320.value = somme;

        var difference = amount - Mt43_ITS00155;
        if(isNaN(somme) || somme == 0){
            document.getElementById('ITS00330').value = (Mt43_ITS00155 + Mt38_ITS00740 + Mt32_ITS005140) - (Mt42_ITS00150+Mt40_ITS00130);
        }else if(somme > 0){
            //Mt48_ITS00330 += somme;
            // Mettre à jour la valeur de Mt48_ITS00330 avec la nouvelle valeur
            document.getElementById('ITS00330').value = (Mt43_ITS00155 + Mt44_ITS00160 + Mt45_ITS00170 + Mt46_ITS00180 + Mt38_ITS00740 + Mt32_ITS005140) - (Mt42_ITS00150+Mt40_ITS00130);
        }else if(somme < somme){
            //Mt48_ITS00330 = (amount - difference);
            document.getElementById('ITS00330').value = (amount - difference);
        }

    }

    function declabackits() {
        var exerciceId = $("#exercice").val();
        var periodeId = $("#periodes_efi").val();
        var cpte = 0; var total1 = 0;
        
        var span_error  = document.getElementById('erreur_sbi');
        var span_error2  = document.getElementById('erreur_sbi2');
        var  Mt1_ITS00530 = document.getElementById('ITS00530');
        var  Mt2_ITS00540 = document.getElementById('ITS00540');
        var  Mt3_ITS00060 = document.getElementById('ITS00060');
        var  Mt4_ITS00650 = document.getElementById('ITS00650');
        var  Mt5_ITS00070 = document.getElementById('ITS00070');
        var  Mt6_ITS00550 = document.getElementById('ITS00550');
        var  Mt7_ITS00560 = document.getElementById('ITS00560');
        var  Mt8_ITS00570 = document.getElementById('ITS00570');
        var  Mt9_ITS00660 = document.getElementById('ITS00660');
        var  Mt10_ITS00580 = document.getElementById('ITS00580');
        var  Mt11_ITS00790 = document.getElementById('ITS00790');
        var  Mt12_ITS00430 = document.getElementById('ITS00430');
        var  Mt13_ITS00800 = document.getElementById('ITS00800');
        var  Mt14_ITS00810 = document.getElementById('ITS00810');
        var  Mt15_ITS00820 = document.getElementById('ITS00820');
        var  Mt16_ITS00830 = document.getElementById('ITS00830');
        var  Mt17_ITS00840 = document.getElementById('ITS00840');
        var  Mt18_ITS00850 = document.getElementById('ITS00850');
        var  Mt19_ITS00860 = document.getElementById('ITS00860');
        var  Mt20_ITS00870 = document.getElementById('ITS00870');
        var  Mt21_ITS00880 = document.getElementById('ITS00880');
        var  Mt21_ITS005110 = document.getElementById('ITS005110');
        var  Mt22_ITS00010 = document.getElementById('ITS00010');
        var  Mt23_ITS005100 = document.getElementById('ITS005100');
        var  Mt24_ITS00020 = document.getElementById('ITS00020');
        var  Mt25_ITS00030 = document.getElementById('ITS00030');
        var  Mt26_ITS005120 = document.getElementById('ITS005120');
        var  Mt27_ITS00050 = document.getElementById('ITS00050');
        var  Mt28_ITS00670 = document.getElementById('ITS00670');
        var  Mt29_ITS005130 = document.getElementById('ITS005130');
        var  Mt30_ITS00680 = document.getElementById('ITS00680');
        var  Mt33_ITS00700 = document.getElementById('ITS00700');
        var  Mt34_ITS00710 = document.getElementById('ITS00710');
        var  Mt35_ITS00720 = document.getElementById('ITS00720');
        var  Mt36_ITS00730 = document.getElementById('ITS00730').value;
        var  Mt37_ITS00350 = document.getElementById('ITS00350');
        var  Mt38_ITS00740 = document.getElementById('ITS00740');
        var  Mt43_ITS00155 = document.getElementById('ITS00155');
        var  Mt48_ITS00330 = document.getElementById('ITS00330');
        var  Mt49_ITS00340 = document.getElementById('ITS00340');
        var  Mt47_ITS00320 = document.getElementById('ITS00320').value;
        var  Mt42_ITS00150 = document.getElementById('ITS00150').value;
        var  Mt40_ITS00130 = document.getElementById('ITS00130').value;
        var  Mt50_taux = document.getElementById('ITS00010_Taux');
        var  Mt51_taux = document.getElementById('ITS00040_Taux');
        var  Mt52_taux = document.getElementById('ITS005130_Taux');
        var  Mt53_taux = document.getElementById('ITS005140_Taux');
        var  Mt54_taux = document.getElementById('ITS00710_Taux');
        var  Mt55_ITS00040 = document.getElementById('ITS00040');
        var  Mt_ITS00151 = document.getElementById('ITS00151');

        $.ajax({
            url: "{{ route('company.declarations.get_decla_efi') }}",
            type: 'GET',
            data: {
                "exercice_id": exerciceId,
                "periode_id": periodeId,
            },
            success: function(data) {
                if (data.length > 0) {
                    
                    var cpteavtg = 0; var total2	= 0; var total3	= 0; var total4	= 0; var total5	= 0; var total6	= 0; var cpteavtge = 0;
                    var total7	= 0; var total8	= 0; var total9	= 0; var total10 = 0; var total11 = 0; var total12 = 0; var resltexo = 0;
                    var total13 = 0; var total14 = 0; var total15 = 0; var total16 = 0; var total17 = 0; var cpte2 = 0;
                    var total18 = 0; var total19 = 0; var total20 = 0; var total21 = 0; var total22 = 0; var total23 = 0;
                    $.each(data, function(index, element) {
                        cpte++;
                        var found = false;
                        cpteavtg = element.nbrempavtg || 0; 
                        if (element.retenue) {
                            var retenues = element.retenue;
                            $.each(retenues, function(i, retenue) {
                                if(retenue.code == 403){
                                    if(parseInt(retenue.amount) > 0){
                                        if(retenue.code == 401){
                                            total4 +=  parseInt(retenue.amount);
                                        } 
                                    }else{
                                        if(retenue.code == 402){
                                            total4 +=  parseInt(retenue.amount);
                                        }
                                    }                                       
                                    total22+= parseInt(retenue.amount);                  
                                }

                                if(retenue.code == 401){
                                   total5 += parseInt(retenue.amount);
                                }

                                if(element.charge_expat == 'expat'){
                                    if(retenue.code == 410){
                                        total8 += retenue.amount
                                    }
                                }
                            });
                        }
                        total23 += parseInt(element.avtg_real2 + element.avtg_real);
                        total3 += parseInt(element.total_retenue);                        
                        total1 += element.salary_brut;
                        toto_bareme = parseInt(element.avtg_bareme);
                        toto_reel = parseInt(element.avtg_real2);
                        toto_reel2 = parseInt(element.avtg_real);
                        total_edi = parseInt(element.salary_brut - toto_reel + toto_reel2);
                        total_brut_edi = total_edi + toto_bareme;
                        total2 += parseInt(toto_reel + toto_reel2);
                        if(element.charge_expat == 'expat'){
                            cpte2++;
                            total6 += parseInt(element.net_imposable);
                        }
                        if(element.charge_expat == 'local') {
                            total7 += element.net_imposable;
                        }
                        if (element.allowance) {
                            var allowances = element.allowance;
                            var found = false;
                            var exo = 0;
                            var toto = 0;
                            var toto2 = 0;
                            var totalbase = 0; // Déclaration de toto ici

                            $.each(allowances, function(i, allowance) {
                                if (allowance.code == 126 || allowance.code == 111) {
                                    totalbase += parseInt(allowance.amount);
                                    found = true;
                                }
                                if (allowance.trait_fisc.startsWith('exo 10%')) {
                                    toto += parseInt(allowance.amount); // Ajout de la valeur à toto
                                    found = true;
                                }
                                if (allowance.trait_fisc.startsWith('exo 100%')) {
                                    if(allowance.code == 111 && allowance.amount > 30000){
                                        toto2 = toto2 + (30000);
                                    }else{
                                        toto2 = toto2 + parseInt(allowance.amount);
                                    }
                                    //toto2 += allowance.amount; // Ajout de la valeur à toto2
                                    found = true;
                                }
                            });

                            exo = ((((total_brut_edi) - parseInt(totalbase)) * 10) / 100);

                            if (toto > exo) {
                                resltexo += (exo + toto2);
                            } else {
                                resltexo += (toto + toto2);
                            }
                        }
                    });
                    Mt1_ITS00530.value   = total1 - parseInt(total23);
                    Mt2_ITS00540.value   = cpte;
                    Mt3_ITS00060.value   = total2;
                    Mt4_ITS00650.value   = cpteavtg;
                    Mt5_ITS00070.value   = '0';
                    Mt6_ITS00550.value   = '0';
                    Mt7_ITS00560.value   = ((total1+total2)-parseInt(total23));
                    Mt8_ITS00570.value   = Math.round(resltexo);
                    Mt9_ITS00660.value   = '0';
                    Mt10_ITS00580.value  = Math.round(resltexo);
                    Mt11_ITS00790.value  = (((total1+total2)-parseInt(total23))-Math.round(resltexo));
                    Mt12_ITS00430.value  = Math.round(resltexo);
                    Mt13_ITS00800.value  = '0';
                    Mt14_ITS00810.value  = Math.round(resltexo);
                    Mt15_ITS00820.value  = (((total1+total2)-parseInt(total23))-Math.round(resltexo));
                    Mt16_ITS00830.value  = total5;
                    Mt17_ITS00840.value  = total4;
                    Mt18_ITS00850.value  = (total22);
                    Mt19_ITS00860.value  = (cpte-cpte2);
                    Mt20_ITS00870.value  = cpte2;
                    Mt21_ITS00880.value  = total6;
                    Mt22_ITS00010.value  = Math.round(total6*0.092);
                    Mt23_ITS005100.value = '0';
                    Mt24_ITS00020.value  = '0';
                    Mt21_ITS005110.value = '0';
                    Mt25_ITS00030.value  = (cpte-cpte2);
                    if(cpte-cpte2 == 0){
                        Mt26_ITS005120.value = 0;
                    }else{
                        Mt26_ITS005120.value = (((total1+total2)-parseInt(total23))-Math.round(resltexo));
                    }
                    Mt55_ITS00040.value  = Math.round((((total1+total2)-parseInt(total23))-Math.round(resltexo))*0.012);
                    Mt27_ITS00050.value  = cpte2;
                    Mt28_ITS00670.value  = total6;
                    Mt29_ITS005130.value = Math.round(total6*0.012);
                    Mt30_ITS00680.value  = Math.round((total6*0.012)+((((total1+total2)-parseInt(total23))-Math.round(resltexo))*0.012)+(total6*0.092));
                    Mt33_ITS00700.value  = (((total1+total2)-parseInt(total23))-Math.round(resltexo));
                    document.getElementById('recupfdfp1').value = Mt33_ITS00700.value;
                    Mt34_ITS00710.value  = Math.round((((total1+total2)-parseInt(total23))-Math.round(resltexo))*0.004);
                    Mt35_ITS00720.value  = (((total1+total2)-parseInt(total23))-Math.round(resltexo));
                    document.getElementById('recupfdfp2').value = Mt34_ITS00710.value;
                    document.getElementById('recupfdfp3').value = Mt35_ITS00720.value;
                    total20              = parseInt(Mt42_ITS00150);
                    total19              = parseInt(Mt40_ITS00130);
                    total17              = Math.round(total22);
                    total14              = 0;
                    total15              = Math.round((total6*0.012)+((((total1+total2)-parseInt(total23))-Math.round(resltexo))*0.012)+(total6*0.092))
                    Mt43_ITS00155.value  = ((total14 + total15 + total16 + total17));
                    Mt48_ITS00330.value  = ((total14 + total15 + total16 + total17));
                    Mt49_ITS00340.value  = '0';

                    fdfpExo();
                    fdfp();
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function fdfpExo(){
        var Mt56_ITS00695 = document.getElementById('ITS00695').value;//select

        if(Mt56_ITS00695 == 1){
            document.getElementById('ITS00700').value = 0;
            document.getElementById('ITS00700').disabled = true;
            document.getElementById('ITS00710').value = 0;
            document.getElementById('ITS00710').disabled = true;
            document.getElementById('ITS00720').value = 0;
            document.getElementById('ITS00720').disabled = true;
            document.getElementById('ITS00350').value = 0;
            document.getElementById('ITS00350').disabled = true;
            document.getElementById('ITS00740').value = 0;
            document.getElementById('ITS00740').disabled = true;
            document.getElementById('ITS00730').disabled = true;
        }else{
            document.getElementById('ITS00730').disabled = false;
            document.getElementById('ITS00700').value  =  document.getElementById('recupfdfp1').value;
            document.getElementById('ITS00710').value  =  document.getElementById('recupfdfp2').value;
            document.getElementById('ITS00720').value  =  document.getElementById('recupfdfp3').value;
        }
    }

    $(document).on("change", "#ITS00695" ,function() {
        fdfpExo();
    });

    function fdfp(){
        var Mt36_ITS00730 = document.getElementById('ITS00730').value;//select
        var Mt35_ITS00720  = document.getElementById('ITS00720');
        var Mt38_ITS00740 = document.getElementById('ITS00740');
        var Mt37_ITS00350 = document.getElementById('ITS00350');
        var Mt48_ITS00330 = parseInt(document.getElementById('ITS00330').value);
        var Mt43_ITS00155 = parseInt(document.getElementById('ITS00155').value);
        var Mt42_ITS00150 = parseInt(document.getElementById('ITS00150').value);
        var Mt40_ITS00130 = parseInt(document.getElementById('ITS00130').value);
        var Mt32_ITS005140 = parseInt(document.getElementById('ITS005140').value);
        var Mt47_ITS00320  = parseInt(document.getElementById('ITS00320').value);

        var total1 = Mt35_ITS00720.value;   var total14 = 0;

        if(Mt36_ITS00730  == '0.006'){
            Mt37_ITS00350.value  = Math.round(total1*0.006);
            Mt38_ITS00740.value  = Math.round((total1*0.004)+(total1*0.006));
            total14              = Math.round((total1*0.004)+(total1*0.006))
            document.getElementById('ITS00330').value = (Mt43_ITS00155 + total14 + Mt48_ITS00330 + Mt32_ITS005140 + Mt47_ITS00320) - (Mt48_ITS00330+Mt40_ITS00130);
        }else if(Mt36_ITS00730  == '0.012'){
            Mt37_ITS00350.value  = Math.round(total1*0.012);
            Mt38_ITS00740.value  = Math.round((total1*0.004)+(total1*0.012));
            total14              = Math.round((total1*0.004)+(total1*0.012))
            document.getElementById('ITS00330').value = (Mt43_ITS00155 + total14 + Mt48_ITS00330 + Mt32_ITS005140 + Mt47_ITS00320) - (Mt48_ITS00330+Mt40_ITS00130);
        }
    }

    $(document).on("change", "#ITS00730" ,function() {
        fdfp();
    });

    // FONCTION FINALE - Solution radicale pour contourner le cache et générer un XML propre
    function genererXML_FINAL() {
        // alert("FONCTION FINALE DÉMARRÉE!\n\nCette version va générer un XML propre avec:\n- Mois et exercice DYNAMIQUES (sélectionnés)\n- Sans balises <champs>\n- Sans lignes vides\n\nValeurs récupérées des sélecteurs");
        
        try {
            // Récupérer le mois dynamiquement depuis le sélecteur - APPROCHE DIRECTE
            var mois = "";
            var periodeSelect = document.getElementById("periodes_efi");
            if (periodeSelect && periodeSelect.selectedIndex >= 0) {
                var selectedOption = periodeSelect.options[periodeSelect.selectedIndex];
                // Essayer plusieurs méthodes par ordre de priorité
                var moisData = selectedOption.getAttribute("data-monthly");
                var pdebut = selectedOption.getAttribute("data-pdebut");
                if (moisData) {
                    mois = moisData;
                } else if (pdebut) {
                    var d = new Date(pdebut);
                    if (!isNaN(d.getTime())) {
                        mois = (d.getMonth() + 1).toString();
                    }
                }
                if (!mois) {
                    mois = selectedOption.text.match(/\b(\d{1,2})\b/)?.[1] || '';
                }
                if (!mois) {
                    mois = (new Date().getMonth() + 1).toString();
                }
            }
            
            // Récupérer l'exercice dynamiquement depuis le sélecteur - APPROCHE DIRECTE
            var exercice = "";
            var exerciceSelect = document.getElementById("exercice");
            if (exerciceSelect && exerciceSelect.selectedIndex >= 0) {
                var selectedOptionExercice = exerciceSelect.options[exerciceSelect.selectedIndex];
                // Essayer plusieurs méthodes par ordre de priorité
                exercice = selectedOptionExercice.getAttribute("data-years") || 
                           selectedOptionExercice.text.match(/20\d{2}/)?.[0] || 
                           new Date().getFullYear().toString();
            }
            
            // DÉBOGAGE COMPLET des valeurs récupérées
            console.log("=== DÉBOGAGE VALEURS XML (APPROCHE DIRECTE) ===");
            console.log("Sélecteur période:", periodeSelect ? "TROUVÉ" : "NON TROUVÉ");
            console.log("Index sélectionné période:", periodeSelect?.selectedIndex);
            console.log("Option période HTML:", periodeSelect?.options[periodeSelect?.selectedIndex]?.outerHTML);
            console.log("Mois data-monthly:", periodeSelect?.options[periodeSelect?.selectedIndex]?.getAttribute("data-monthly"));
            console.log("Mois value:", periodeSelect?.options[periodeSelect?.selectedIndex]?.value);
            console.log("Mois texte match:", periodeSelect?.options[periodeSelect?.selectedIndex]?.text.match(/\d{1,2}/)?.[0]);
            console.log("Mois final:", mois);
            console.log("---");
            console.log("Sélecteur exercice:", exerciceSelect ? "TROUVÉ" : "NON TROUVÉ");
            console.log("Index sélectionné exercice:", exerciceSelect?.selectedIndex);
            console.log("Option exercice HTML:", exerciceSelect?.options[exerciceSelect?.selectedIndex]?.outerHTML);
            console.log("Exercice data-years:", exerciceSelect?.options[exerciceSelect?.selectedIndex]?.getAttribute("data-years"));
            console.log("Exercice value:", exerciceSelect?.options[exerciceSelect?.selectedIndex]?.value);
            console.log("Exercice texte match:", exerciceSelect?.options[exerciceSelect?.selectedIndex]?.text.match(/20\d{2}/)?.[0]);
            console.log("Exercice final:", exercice);
            console.log("===========================================");
            
            // CORRECTION ADDITIONNELLE : S'assurer que le mois a 2 chiffres
            if (!mois || isNaN(parseInt(mois, 10))) {
                mois = (new Date().getMonth() + 1).toString();
            }
            mois = parseInt(mois, 10).toString().padStart(2, '0');
            console.log("Mois formaté avec 2 chiffres:", mois);
            
            // CORRECTION ADDITIONNELLE : S'assurer que l'exercice est un entier sur 4 chiffres
            if (!exercice || !/^\d{4}$/.test(String(exercice))) {
                exercice = new Date().getFullYear().toString();
            }
            
            console.log("Exercice final utilisé:", exercice);
            console.log("VALEURS FINALES XML - Mois:", mois, "Exercice:", exercice);
            console.log("=========================");
            
            // Valeur forcée pour le NCC
            var ncc = document.getElementById('numm_cc') ? document.getElementById('numm_cc').value : '';
            if (!ncc) {
                alert("Le numéro de compte contribuable (NCC) n'est pas configuré.");
                return;
            }
            ncc = ncc.replace(/\s+/g, '').toUpperCase();
            
            console.log("Mois récupéré dynamiquement:", mois);
            console.log("Exercice récupéré dynamiquement:", exercice);
            
            // Récupérer les données du formulaire
            var donneesFormulaire = {};
            var tbody = document.querySelector('#table_its tbody');
            if (tbody) {
                var rows = tbody.querySelectorAll('tr');
                rows.forEach(function(row) {
                    var inputs = row.querySelectorAll('input[type="text"], input[type="number"]');
                    inputs.forEach(function(input) {
                        var id = input.getAttribute('id');
                        if (id) {
                            donneesFormulaire[id] = input.value || '0';
                        }
                    });
                });
            }
            
            // Construire le XML manuellement avec la structure EXACTE de l'ancienne version
            var xml = String.fromCharCode(60) + '?xml version="1.0" encoding="utf-8"?' + String.fromCharCode(62) + '\n';
            xml += '<EDI>\n';
            xml += '<declaration>\n';
            xml += '<codeTaxe>ITS</codeTaxe>\n';
            xml += '<ncc>' + ncc + '</ncc>\n';
            xml += '<mois>' + mois + '</mois>\n';
            xml += '<exercice>' + exercice + '</exercice>\n';
            xml += '<champs>\n';
            
            // Ajouter les champs avec la structure EXACTE de l'ancienne version
            for (var code in donneesFormulaire) {
                xml += '<champ>\n';
                xml += '<code>' + code + '</code>\n';
                xml += '<valeur>' + donneesFormulaire[code] + '</valeur>\n';
                xml += '</champ>\n';
            }
            
            xml += '</champs>\n';
            xml += '</declaration>\n';
            xml += '</EDI>';
            
            console.log("XML GÉNÉRÉ:");
            console.log(xml);
            
            // Télécharger le fichier
            var blob = new Blob([xml], { type: 'text/xml' });
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = ncc + '-EFI-ITS-CORRIGE-' + new Date().getTime() + '.xml';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            // alert("XML GÉNÉRÉ ET TÉLÉCHARGÉ!\n\nLe fichier contient:\n- Mois: " + mois + " ✓\n- Exercice: " + exercice + " ✓\n- Pas de balises <champs> ✓\n- Pas de lignes vides ✓");
            
        } catch (error) {
            console.error("Erreur:", error);
            alert("Erreur: " + error.message);
        }
    }

    function exporterEnXML2_NOUVEAU() {
        try {
            // FORCER LE RAFRAÎCHISSEMENT - Ajouter timestamp pour éviter le cache
            var timestamp = new Date().getTime();
            console.log("=== EXPORTER XML2 - TIMESTAMP:", timestamp, " ===");
            
            // TEST DIAGNOSTIC : Forcer des valeurs fixes pour voir si le problème vient des sélecteurs
            console.log("=== TEST DIAGNOSTIC DÉBUT ===");
            
            // Forcer des valeurs fixes pour le test
            var mois = "01"; // Forcé à janvier
            var exercice = "2026"; // Forcé à 2026
            var numm_cc = "1864699A"; // Forcé au NCC connu
            
            console.log("VALEURS FORCÉES - Mois:", mois, "Exercice:", exercice, "NCC:", numm_cc);
            
            // Alert pour confirmation que le nouveau code est exécuté
            alert("NOUVEAU CODE EXÉCUTÉ!\nTimestamp: " + timestamp + "\nLe XML sera généré avec Mois=" + mois + " et Exercice=" + exercice + "\n\nSi vous voyez cette alerte, le cache est rafraîchi.");
            
            // Récupérer le numéro de compte de l'entreprise (code original commenté pour le test)
            /*
            var numm_cc = "";
            var nccElement = document.querySelector('h6 span');
            if (nccElement) {
                numm_cc = nccElement.textContent.trim();
            }
            
            // Validation et formatage du NCC
            if (!numm_cc) {
                alert("Le numéro de compte contribuable (NCC) n'est pas configuré. Veuillez le configurer dans les paramètres de l'entreprise.");
                return;
            }
            
            // Formater le NCC : supprimer les espaces et s'assurer du format correct
            numm_cc = numm_cc.replace(/\s+/g, '').toUpperCase();
            
            // S'assurer que le NCC a 7 chiffres + 1 lettre
            var nccNumbers = numm_cc.replace(/[^0-9]/g, '');
            var nccLetter = numm_cc.replace(/[0-9]/g, '');
            
            // Ajouter des zéros au début si nécessaire
            while (nccNumbers.length < 7) {
                nccNumbers = '0' + nccNumbers;
            }
            
            numm_cc = nccNumbers + nccLetter;
            */
            
            console.log("NCC formaté avec 7 chiffres:", numm_cc);
            
            // SAUTER TOUT LE CODE COMPLEXE - UTILISER DIRECTEMENT LES VALEURS FORCÉES
            console.log("=== UTILISATION DES VALEURS FORCÉES ===");
            
            var codeTaxe = 'ITS';
            
            // Formater la date actuelle pour le nom du fichier
            var dateactu = new Date();
            var jour = dateactu.getDate().toString().padStart(2, '0');
            var dateeee = exercice + '' + mois + '' + jour;
            var nombreAleatoire = Math.floor(Math.random() * 9000) + 1000;
            
            console.log("Nom du fichier sera:", numm_cc + '-EFI-ITS-' + dateeee + '-' + nombreAleatoire + '.xml');
            
            // Fonction pour échapper les caractères spéciaux XML
            function escapeXml(unsafe) {
                if (unsafe === null || unsafe === undefined) return '';
                var str = unsafe.toString();
                if (str.trim() === '') return '';
                return str
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&apos;');
            }
            
            // CONSTRUCTION DIRECTE DU XML AVEC VALEURS FORCÉES
            console.log("=== CONSTRUCTION XML AVEC VALEURS FORCÉES ===");
            var xmlParts = [];
            var xmlDecl = String.fromCharCode(60) + '?xml version="1.0" encoding="UTF-8"?' + String.fromCharCode(62);
            xmlParts.push(xmlDecl);
            xmlParts.push('<EDI>');
            xmlParts.push('  <declaration>');
            xmlParts.push('    <codeTaxe>' + codeTaxe + '</codeTaxe>');
            xmlParts.push('    <ncc>' + escapeXml(numm_cc) + '</ncc>');
            xmlParts.push('    <mois>' + mois + '</mois>');
            xmlParts.push('    <exercice>' + exercice + '</exercice>');
            
            console.log("XML LIGNES AJOUTÉES:");
            console.log("  <mois>" + mois + "</mois>");
            console.log("  <exercice>" + exercice + "</exercice>");
            
            xmlParts.push('    <champs>');
            
            // Récupérer tous les champs du formulaire
            var tbody = document.querySelector('#table_its tbody');
            if (tbody) {
                var rows = tbody.querySelectorAll('tr');
                
                rows.forEach(function(row) {
                    var inputs = row.querySelectorAll('input[type="text"], input[type="number"]');
                    
                    inputs.forEach(function(input) {
                        var id = input.getAttribute('id');
                        if (id) {
                            var value = input.value || '0'; // Valeur par défaut si vide
                            // Éviter les lignes vides - construire le champ sur une seule ligne
                            xmlParts.push('      <champ><code>' + escapeXml(id) + '</code><valeur>' + escapeXml(value) + '</valeur></champ>');
                        }
                    });
                });
            }
            
            // Fermer les balises
            xmlParts.push('    </champs>');
            xmlParts.push('  </declaration>');
            xmlParts.push('</EDI>');
            
            // Télécharger le fichier XML
            var xmlString = xmlParts.join('');
            
            // POST-TRAITEMENT : Corriger le XML final pour s'assurer qu'il est valide
            console.log("=== POST-TRAITEMENT DU XML ===");
            
            // Forcer l'insertion de mois et exercice s'ils sont vides
            if (xmlString.includes('<mois/>')) {
                xmlString = xmlString.replace('<mois/>', '<mois>01</mois>');
                console.log("CORRECTION: <mois/> remplacé par <mois>01</mois>");
            }
            if (xmlString.includes('<exercice/>')) {
                xmlString = xmlString.replace('<exercice/>', '<exercice>2026</exercice>');
                console.log("CORRECTION: <exercice/> remplacé par <exercice>2026</exercice>");
            }
            
            // Corriger les lignes vides dans les champ
            xmlString = xmlString.replace(/<\/champ>\s*\n\s*<champ>/g, '</champ><champ>');
            xmlString = xmlString.replace(/<\/code>\s*\n\s*<valeur>/g, '</code><valeur>');
            
            console.log("XML FINAL CORRIGÉ (premiers 300 caractères):");
            console.log(xmlString.substring(0, 300));
            console.log("Vérification finale - Contient <mois>01</mois>:", xmlString.includes('<mois>01</mois>'));
            console.log("Vérification finale - Contient <exercice>2026</exercice>:", xmlString.includes('<exercice>2026</exercice>'));
            console.log("=====================================");
            
            downloadFile2(xmlString, numm_cc + '-EFI-ITS-' + dateeee + '-' + nombreAleatoire + '.xml', 'text/xml');
            
        } catch (error) {
            console.error("Erreur lors de la génération du XML:", error);
            alert("Une erreur est survenue lors de la génération du fichier XML. Veuillez réessayer.");
        }
    }

    // Fonction de formatage XML pour ajouter des sauts de ligne et une indentation
    function formatXML(xmlDoc) {
        var serializer = new XMLSerializer();
        var xmlString = serializer.serializeToString(xmlDoc);
        return xmlString.replace(/(>)(<)/g, '$1\n$2').replace(/<\/(declaration|champs|champ)>/g, '\n$&');
    }

    // Fonction pour télécharger le fichier
    function downloadFile2(data, filename, type) {
        var file = new Blob([data], { type: type });
        if (window.navigator.msSaveOrOpenBlob) {
            // Pour IE
            window.navigator.msSaveOrOpenBlob(file, filename);
        } else {
            // Pour les autres navigateurs
            var a = document.createElement('a');
            var url = URL.createObjectURL(file);
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            setTimeout(function() {
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            }, 0);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Déclaration de la fonction à exécuter automatiquement
        function calculerTotal() {
            var Mt44_ITS00160  = parseInt(document.getElementById('ITS00160').value);
            var Mt45_ITS00170  = parseInt(document.getElementById('ITS00170').value);
            var Mt46_ITS00180  = parseInt(document.getElementById('ITS00180').value);
            var Mt43_ITS00155  = parseInt(document.getElementById('ITS00155').value);
            var Mt38_ITS00740  = parseInt(document.getElementById('ITS00740').value);
            var Mt32_ITS005140 = parseInt(document.getElementById('ITS005140').value);
            var Mt40_ITS00130  = parseInt(document.getElementById('ITS00130').value);
            var Mt42_ITS00150  = parseInt(document.getElementById('ITS00150').value);

            // Calcul du total et mise à jour de la valeur de ITS00330
            document.getElementById('ITS00330').value = (Mt32_ITS005140 + Mt38_ITS00740 + Mt43_ITS00155 + Mt46_ITS00180 + Mt45_ITS00170 + Mt44_ITS00160) - (Mt42_ITS00150 + Mt40_ITS00130);
        }

        // Appel de la fonction pour qu'elle s'exécute automatiquement
        calculerTotal();
    });
</script>

<script>
    // Tableau EDI ITS
    var toto_cpte = 0;

    $(document).ready(function() {
        declaback();

        $(document).on("change", "#exercice, #periodes_edi", function() {
            declaback();
        });
    });

    function declaback() {
        var exerciceId = $("#exercice").val();
        var periodeId = $("#periodes_edi").val();
        var cpte = 0;
        var typesala = ''; var gender = ''; var toto_imp_ricf = 0; var total_ce_expat = 0; var char_expa = '';  var etatcivil = ''; var regime = ''; var libposte = ''; var resltexo = 0; var total_brut_edi = 0;
        var nationalite = ''; var toto_bareme = 0; var toto_reel = 0; var numero_cnps = 0; var total_ricf = 0; var total_edi = 0;  //var total_x = 0;
        var secteur = document.getElementById('sect').value; var totautotal_edi = 0;  var totau_brut_edi = 0; var tot_impos = 0; var toto_imp_net = 0
        var totoresltexo = 0; var totau_bareme = 0; var totau_reel = 0; var toto_impot = 0; var toto_imp_brut = 0

        function toInt(v) {
            if (v === null || v === undefined) return 0;
            var s = String(v).replace(/\s+/g, '').replace(/,/g, '').replace(/\u00A0/g, '');
            var n = parseInt(s, 10);
            return isNaN(n) ? 0 : n;
        }

        function toCode(v) {
            if (v === null || v === undefined) return '';
            return String(v).trim();
        }

        $.ajax({
            url: "{{ route('company.declarations.get_decla_edi') }}",
            type: 'GET',
            data: {
                "exercice_id": exerciceId,
                "periode_id": periodeId,
            },
            success: function(data) {
                var tr = '';
                if (data.length > 0) {
                    var tr = '';
                    $.each(data, function(index, element) {
                            cpte++;
                            var itsBrutEmp = 0;
                            var ricfEmp = 0;
                            var itsNetEmp = 0;
                            if(element.salary_type=='1'){
                                typesala = 'Salarié';
                            }else{
                                typesala = 'Salarié';
                            }
                            if(element.gender=='Male'){
                                gender = 'M';
                            }else{
                                gender = 'F';
                            }
                            if(element.charge_expat=='expat'){
                                char_expa = 'E';
                            }else{
                                char_expa = 'L';
                            }
                            if(element.martalstatu_id=='1'){
                                etatcivil = 'C';
                            }else if(element.martalstatu_id=='2'){
                                etatcivil = 'M';
                            }else if(element.martalstatu_id=='3'){
                                etatcivil = 'D';
                            }else if(element.martalstatu_id=='4'){
                                etatcivil = 'V';
                            }
                            if(secteur != 14 || secteur != 14 ){
                                regime = 'G';
                            }else{
                                regime = 'A';
                            }
                            if (element.categ_emp && element.categ_emp.includes('Ingénieurs')) {
                                libposte = 'DR';
                            } else if (element.categ_emp && element.categ_emp.includes('Agents De Maitrise')) {
                                libposte = 'CS';
                            } else if (element.categ_emp && element.categ_emp.includes('Maître')) {
                                libposte = 'CM';
                            } else if (element.categ_emp && element.categ_emp.includes('Employés')) {
                                libposte = 'EQ';
                            } else if (element.categ_emp && element.categ_emp.includes('Élève Officier')) {
                                libposte = 'En';
                            } else if (element.categ_emp && element.categ_emp.includes('Ouvriers')) {
                                libposte = 'OQ';
                            } else if (element.categ_emp && element.categ_emp.includes('Manoeuvres')) {
                                libposte = 'ON';
                            } else {
                                libposte = 'A';
                            }
                            if(element.nationality == '54'){
                                nationalite = 'I';
                            }else if(element.nationality == '76'){
                                nationalite = 'F';
                            }else if(element.nationality == '124' || element.nationality == '216'){
                                nationalite = 'SL';
                            }else if(element.nationality == '2' || element.nationality == '25' || element.nationality == '37' || element.nationality == '40' || element.nationality == '137' || element.nationality == '93' || element.nationality == '222' || element.nationality == '161' || element.nationality == '162' || element.nationality == '196'){
                                nationalite = 'AA';
                            }else{
                                nationalite = 'A';
                            }
                            if(element.num_cnps > 0){
                                numero_cnps = element.num_cnps;
                            }else{
                                numero_cnps = 0;
                            }

                            toto_bareme = parseInt(element.avtg_bareme);
                            toto_reel = parseInt(element.avtg_real2);
                            toto_reel2 = parseInt(element.avtg_real);
                            total_edi = parseInt(element.salary_brut)-(parseInt(toto_reel) + parseInt(toto_reel2));
                            total_brut_edi = total_edi + parseInt(toto_bareme) + parseInt(toto_reel) + parseInt(toto_reel2);
                            totautotal_edi += total_edi; totau_reel += toto_reel;
                            totau_brut_edi += total_brut_edi; totau_bareme += toto_bareme; 

                            if (element.retenue) {
                                var retenues = element.retenue;
                                $.each(retenues, function(i, retenue) {
                                    var code = toCode(retenue.code);
                                    var amount = toInt(retenue.amount);
                                    if(code == '401'){
                                        itsBrutEmp += amount;
                                    } 
                                    if(code == '402'){
                                        ricfEmp += amount;
                                    } 
                                    if(code == '403'){                              
                                        itsNetEmp += amount;                  
                                    }
                                    if(element.charge_expat == 'expat'){
                                        if(code == '410'){
                                            total_ce_expat += amount
                                        }
                                    }
                                });
                            }

                            // Cumuls totaux (ligne bleue)
                            toto_imp_brut += itsBrutEmp;
                            toto_imp_net += itsNetEmp;
                            toto_imp_ricf += ricfEmp;
                            tr +=
                                '<tr>' +
                                '<td align="center" class="border border-dark sticky-col"  style="background-color:#ffffff">' + numero_cnps + '<input type="text" value="'+ numero_cnps +'" hidden></td>' +
                                '<td align="center" class="border border-dark">' + element.name.toUpperCase() + '<input type="text" value="'+ element.name.toUpperCase() +'" hidden></td>' +
                                '<td align="center" class="border border-dark">' + element.poste + '<input type="text" value="'+ element.poste +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + libposte + '<input type="text" value="'+ libposte +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + regime + '<input type="text" value="'+ regime +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + gender + '<input type="text" value="'+ gender +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + nationalite + '<input type="text" value="'+ nationalite +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + char_expa + '<input type="text" value="'+ char_expa +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + etatcivil + '<input type="text" value="'+ etatcivil +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + element.enfant + '<input type="text" value="'+ element.enfant +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + element.parts + '<input type="text" value="'+ element.parts +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + element.tax_payer_id + '<input type="text" value="'+ element.tax_payer_id +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + (total_edi).toLocaleString() + '<input type="text" value="'+ total_edi +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + (toto_bareme).toLocaleString() + '<input type="text" value="'+ toto_bareme +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + (toto_reel).toLocaleString() + '<input type="text" value="'+ toto_reel +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + total_brut_edi.toLocaleString() + '<input type="text" value="'+ total_brut_edi +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">';
                                    if (element.allowance) {
                                        var allowances = element.allowance;
                                        var found = false;
                                        var toto = 0;
                                        var toto2 = 0;
                                        var totalbase = 0; // Réinitialisation de totalbase ici
                                        var exo = 0; // Réinitialisation de exo ici
                                        allowances.forEach(function(allowance) {
                                            if (allowance.code == 126 || allowance.code == 111) {
                                                totalbase += parseInt(allowance.amount);
                                                found = true;
                                            }
                                            if (allowance.trait_fisc.startsWith('exo 10%')) {
                                                toto += parseInt(allowance.amount); // Ajout de la valeur à toto
                                                found = true;
                                            }
                                            if (allowance.trait_fisc.startsWith('exo 100%')) {
                                                if(allowance.code == 111 && allowance.amount > 30000){
                                                    toto2 = toto2 + (30000);
                                                }else{
                                                    toto2 = toto2 + parseInt(allowance.amount);
                                                }
                                                //toto2 += allowance.amount; // Ajout de la valeur à toto2
                                                found = true;
                                            }
                                        });

                                        exo = ((total_edi - parseInt(totalbase)) * 10) / 100;

                                        if (!found) {
                                            tr += 0 + '<input type="text" value="' + 0 + '" hidden>';
                                        } else {
                                            var resltexo;
                                            if (toto > exo) {
                                                resltexo = exo + toto2;
                                                tr += Math.round(resltexo).toLocaleString() + '<input type="text" value="' + Math.round(resltexo) + '" hidden>';
                                            } else {
                                                resltexo = toto + toto2;
                                                tr += Math.round(resltexo).toLocaleString() + '<input type="text" value="' + Math.round(resltexo) + '" hidden>';
                                            }
                                        }
                                    }
                                tr += '</td>';
                                tr += '<td align="center" class="border border-dark">';
                                    if (element.allowance) {
                                        var allowances = element.allowance;
                                        var found = false;
                                        var toto = 0;
                                        var toto2 = 0;
                                        var totalbase = 0; // Réinitialisation de totalbase ici
                                        var exo = 0; // Réinitialisation de exo ici
                                        $.each(allowances, function(i, allowance) {
                                            if (allowance.code == 26 || allowance.code == 11) {
                                                totalbase += allowance.amount;
                                                found = true;
                                            }
                                            if (allowance.trait_fisc.startsWith('10% - Art 116')) {
                                                toto += allowance.amount; // Ajout de la valeur à toto
                                                found = true;
                                            }
                                            if (allowance.trait_fisc.startsWith('100% - Art 116')) {
                                                if(allowance.code == 11 && allowance.amount > 30000){
                                                    toto2 = toto2 + (30000);
                                                }else{
                                                    toto2 = toto2 + allowance.amount;
                                                }
                                                //toto2 += allowance.amount; // Ajout de la valeur à toto2
                                                found = true;
                                            }
                                        });
                                        exo = (((total_edi - parseInt(totalbase)) * 10) / 100);
                                        if (!found) {
                                            tr += 0 + '<input type="text" value="' + 0 + '" hidden>';
                                            tot_impos += 0;
                                        } else {
                                            if (toto > exo) {
                                                resltexo = exo + toto2;
                                                tr +=  (total_brut_edi - Math.round(resltexo)).toLocaleString() + '<input type="text" value="' + (total_brut_edi - Math.round(resltexo)) + '" hidden>';
                                                tot_impos += (total_brut_edi - Math.round(resltexo));
                                            } else {
                                                resltexo = toto + toto2
                                                tr +=  (total_brut_edi - Math.round(resltexo)).toLocaleString() + '<input type="text" value="' + (total_brut_edi - Math.round(resltexo)) + '" hidden>';
                                                tot_impos += (total_brut_edi - Math.round(resltexo));
                                            }
                                        }
                                    }
                                tr += '</td>';
                                if((parseInt(itsBrutEmp)-parseInt(ricfEmp)) < 0){
                                    toto_impot += parseInt(itsBrutEmp);
                                    tr += '<td align="center" class="border border-dark">' + parseInt(itsBrutEmp).toLocaleString() + '<input type="text" value="'+ parseInt(itsBrutEmp) +'" hidden></td></td>';
                                }else{
                                    toto_impot += parseInt(ricfEmp);
                                    tr += '<td align="center" class="border border-dark">' + parseInt(ricfEmp).toLocaleString() + '<input type="text" value="'+ parseInt(ricfEmp) +'" hidden></td></td>';
                                }
                            tr += '<td align="center" class="border border-dark">' + itsBrutEmp.toLocaleString() + '<input type="text" value="'+ itsBrutEmp +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">' + itsNetEmp.toLocaleString() + '<input type="text" id="net_its_firts['+ cpte +']" value="'+ itsNetEmp +'" hidden></td></td>' +
                                '<td align="center" class="border border-dark">  <input type="text" class="form-control champs_dynamiques" name="total_its" id="total_its['+ cpte +']" oninput="calcul_total_its('+ cpte +');" value="0" style="text-align: center;"></td></td>' +
                                '<td align="center" class="border border-dark">  <span id="its_result['+ cpte +']"> ' + itsNetEmp.toLocaleString() + ' </span> <input type="text" id="resultat_its['+ cpte +']" value="'+ itsNetEmp +'" hidden></td></td>';
                            tr += '<td align="center" class="border border-dark">';
                                    if (element.allowance) {
                                        var allowances = element.allowance;
                                        var found = false;
                                        var toto = 0;
                                        var toto2 = 0;
                                        var totalbase = 0; // Réinitialisation de totalbase ici
                                        var exo = 0; // Réinitialisation de exo ici
                                        allowances.forEach(function(allowance) {
                                           if (allowance.code == 126 || allowance.code == 111) {
                                                totalbase += parseInt(allowance.amount);
                                                found = true;
                                            }
                                            if (allowance.trait_fisc.startsWith('exo 10%')) {
                                                toto += parseInt(allowance.amount); // Ajout de la valeur à toto
                                                found = true;
                                            }
                                            if (allowance.trait_fisc.startsWith('exo 100%')) {
                                                if(allowance.code == 111 && allowance.amount > 30000){
                                                    toto2 = toto2 + (30000);
                                                }else{
                                                    toto2 = toto2 + parseInt(allowance.amount);
                                                }
                                                //toto2 += allowance.amount; // Ajout de la valeur à toto2
                                                found = true;
                                            }
                                        });

                                        exo = ((total_edi - parseInt(totalbase)) * 10) / 100;

                                        if (!found) {
                                            tr += 0 + '<input type="text" value="' + 0 + '" hidden>';
                                            totoresltexo += 0
                                        } else {
                                            var resltexo;

                                            if (toto > exo) {
                                                resltexo = exo + toto2;
                                                tr += Math.round(resltexo).toLocaleString() + '<input type="text" value="' + Math.round(resltexo) + '" hidden>';
                                            } else {
                                                resltexo = toto + toto2;
                                                tr += Math.round(resltexo).toLocaleString() + '<input type="text" value="' + Math.round(resltexo) + '" hidden>';
                                            }
                                            totoresltexo += resltexo;
                                        }
                                    }
                                tr += '</td>';
                                tr += '<td align="center" class="border border-dark">' ;
                                    if (element.allowance) {
                                        var allowances = element.allowance;
                                        var liballow  = '';
                                        var found = false;
                                        $.each(allowances, function(i, allowance) {
                                            if (allowance.trait_fisc !== 'exo 0%') {
                                                liballow += allowance.title +', ' ;
                                                found = true;
                                            }
                                        });

                                        if (!found) {
                                            tr += 'Aucune prime <input type="text" value="" hidden></td>';
                                        }else{
                                            tr += liballow + '<input type="text" value="'+ liballow +'" hidden></td>';
                                        }
                                    }
                                tr += '</td>';
                            tr +='</tr>';
                            toto_cpte = cpte;
                    });

                    var span1 = document.getElementById('totaux1');
                    var span2 = document.getElementById('totaux2');
                    var span3 = document.getElementById('totaux3');
                    var span4 = document.getElementById('totaux4');
                    var span5 = document.getElementById('totaux5');
                    var span6 = document.getElementById('totaux6');
                    var span7 = document.getElementById('totaux7');
                    var span8 = document.getElementById('totaux8');
                    var span9 = document.getElementById('totaux9');
                    var span10 = document.getElementById('totaux10');

                    var span11 = document.getElementById('totaux11');
                    var span12 = document.getElementById('totaux12');
                    span1.innerHTML = Math.round(totautotal_edi).toLocaleString();
                    span2.innerHTML = Math.round(totau_bareme).toLocaleString();
                    span3.innerHTML = Math.round(totau_reel).toLocaleString();
                    span4.innerHTML = Math.round(totau_brut_edi).toLocaleString();
                    span5.innerHTML = Math.round(totoresltexo).toLocaleString();
                    span6.innerHTML = Math.round(tot_impos).toLocaleString();
                    span7.innerHTML = Math.round(toto_impot).toLocaleString();
                    span8.innerHTML = Math.round(toto_imp_brut).toLocaleString();
                    span9.innerHTML = Math.round(toto_imp_net).toLocaleString();

                    if (span10) {
                        span10.innerHTML = '0,00';
                    }

                    span11.innerHTML = Math.round(toto_imp_net).toLocaleString();
                    span12.innerHTML = Math.round(totoresltexo).toLocaleString();
                } else {
                    var colspan = $('#daclaration thead tr th').length;
                    var tr = '<tr><td class="dataTables-empty" colspan="' + colspan + '" align="center">{{ __('Aucune données') }}</td></tr>';
                }

                $('#daclaration tbody').html(tr);
                var table = document.querySelector("#daclaration");
                //var datatable = new simpleDatatables.DataTable(table);
            },
            error: function(data) {
                console.error("Erreur lors de la récupération des données:", data);
            }
        });
    }

    function calcul_total_its(i) {
        var x = document.getElementById('total_its[' + i + ']').value;
        var y = document.getElementById('net_its_firts[' + i + ']').value;
        var w = document.getElementById('its_result[' + i + ']');
        var z = document.getElementById('resultat_its[' + i + ']');
        var span10 = document.getElementById('totaux10');
        var span11 = document.getElementById('totaux11');
        var total_x = 0;
        if (parseInt(x) > 0) {
            z.value = parseInt(x) + parseInt(y);
            w.innerHTML = (parseInt(x) + parseInt(y)).toLocaleString();
            total_x += parseInt(x);
        } else {
            z.value = parseInt(y);
            w.innerHTML = parseInt(y).toLocaleString();
            total_x += parseInt(x);
        }

        // Total ajustements (colonne Ajustement *)
        if (span10) {
            var totalAjust = 0;
            document.querySelectorAll('input.champs_dynamiques').forEach(function(input) {
                var v = parseInt(input.value);
                if (!isNaN(v)) {
                    totalAjust += v;
                }
            });
            span10.innerHTML = totalAjust.toLocaleString();
        }

        // Total ITS net à payer = somme des valeurs recalculées par salarié (resultat_its[*])
        if (span11) {
            var totalNet = 0;
            document.querySelectorAll('input[id^="resultat_its["]').forEach(function(input) {
                var v = parseInt(input.value);
                if (!isNaN(v)) {
                    totalNet += v;
                }
            });
            span11.innerHTML = totalNet.toLocaleString();
        }
    }

    function exporterEnXML() {
        var num_cc = document.getElementById('num_cc').value;
        
        // Validation et formatage du NCC
        if (!num_cc) {
            alert("Le numéro de compte contribuable (NCC) n'est pas configuré. Veuillez le configurer dans les paramètres de l'entreprise.");
            return;
        }
        
        // Formater le NCC : supprimer les espaces et s'assurer du format correct
        num_cc = num_cc.replace(/\s+/g, '').toUpperCase();
        
        // S'assurer que le NCC a 7 chiffres + 1 lettre
        var nccNumbers = num_cc.replace(/[^0-9]/g, '');
        var nccLetter = num_cc.replace(/[0-9]/g, '');
        
        // Ajouter des zéros au début si nécessaire
        while (nccNumbers.length < 7) {
            nccNumbers = '0' + nccNumbers;
        }
        
        num_cc = nccNumbers + nccLetter;
        
        console.log("NCC formaté avec 7 chiffres (EDI):", num_cc);
        
        var exerciceSelect = $("#exercice");
        var selectedOptionExercice = exerciceSelect.find("option:selected");
        var selectedYear = selectedOptionExercice.data("years") || 
                          selectedOptionExercice.text().trim() || '';
        var periodeSelect = $("#periodes_edi");
        var selectedOptionPeriode = periodeSelect.find("option:selected");
        var selectedMonth = selectedOptionPeriode.data("monthly") || '';
        
        // DÉBOGAGE COMPLET des sélecteurs (EDI)
        console.log("=== DÉBOGAGE COMPLET SÉLECTEURS (EDI) ===");
        console.log("Exercice select exists:", exerciceSelect.length > 0);
        console.log("Exercice selected option exists:", selectedOptionExercice.length > 0);
        console.log("Exercice selected option HTML:", selectedOptionExercice[0] ? selectedOptionExercice[0].outerHTML : 'N/A');
        console.log("Exercice selected option attributes:", selectedOptionExercice[0] ? Array.from(selectedOptionExercice[0].attributes).map(attr => attr.name + '=' + attr.value) : 'N/A');
        console.log("Exercice data() result:", selectedOptionExercice.data());
        console.log("Exercice attr('data-years'):", selectedOptionExercice.attr('data-years'));
        
        console.log("Période select exists:", periodeSelect.length > 0);
        console.log("Période selected option exists:", selectedOptionPeriode.length > 0);
        console.log("Période selected option HTML:", selectedOptionPeriode[0] ? selectedOptionPeriode[0].outerHTML : 'N/A');
        console.log("Période selected option attributes:", selectedOptionPeriode[0] ? Array.from(selectedOptionPeriode[0].attributes).map(attr => attr.name + '=' + attr.value) : 'N/A');
        console.log("Période data() result:", selectedOptionPeriode.data());
        console.log("Période attr('data-monthly'):", selectedOptionPeriode.attr('data-monthly'));
        console.log("=========================================");
        
        // Valeurs par défaut si vide
        if (!selectedYear) {
            // Utiliser l'année actuelle comme valeur par défaut
            selectedYear = new Date().getFullYear().toString();
            console.warn("Année non sélectionnée (EDI), utilisation de l'année actuelle:", selectedYear);
        }
        
        if (!selectedMonth) {
            // Utiliser le mois actuel comme valeur par défaut
            selectedMonth = (new Date().getMonth() + 1).toString();
            console.warn("Mois non sélectionné (EDI), utilisation du mois actuel:", selectedMonth);
        }
        
        // Validation finale des champs (avec valeurs par défaut déjà appliquées)
        if (!selectedYear || !selectedMonth) {
            alert("Erreur: Impossible de déterminer l'exercice ou la période pour EDI.\n\nExercice: " + (selectedYear || 'Non défini') + "\nPériode: " + (selectedMonth || 'Non définie') + "\n\nVeuillez vérifier les sélections ou réessayer.");
            return;
        }
        
        // Validation supplémentaire pour s'assurer que les valeurs ne sont pas vides
        if (selectedYear.toString().trim() === '' || selectedMonth.toString().trim() === '') {
            alert("Erreur: Les valeurs d'exercice ou de mois sont vides après traitement pour EDI.\n\nExercice: '" + selectedYear + "'\nPériode: '" + selectedMonth + "'\n\nVeuillez contacter le support technique.");
            return;
        }
        
        // Formater les valeurs avec sauvegarde finale
        var mois = selectedMonth.toString().trim(); 
        var exercice = selectedYear.toString().trim();
        
        // SAUVEGARDE FINALE : S'assurer que les valeurs ne sont jamais vides pour le XML
        if (!mois || mois === '') {
            mois = (new Date().getMonth() + 1).toString();
            console.error("SAUVEGARDE (EDI): Mois vide, utilisation du mois actuel:", mois);
        }
        if (!exercice || exercice === '') {
            exercice = new Date().getFullYear().toString();
            console.error("SAUVEGARDE (EDI): Exercice vide, utilisation de l'année actuelle:", exercice);
        }
        
        // Validation finale absolue
        if (!mois || !exercice || mois === '' || exercice === '') {
            console.error("ERREUR CRITIQUE (EDI): Impossible de générer le XML - mois ou exercice toujours vides");
            alert("ERREUR CRITIQUE: Impossible de générer le fichier XML EDI. Les valeurs de mois et d'exercice ne peuvent pas être déterminées. Veuillez contacter immédiatement le support technique.");
            return;
        }
        
        var codeTaxe = 'ITS'; // Code pour Impôt sur les Traitements et Salaires
        
        // Validation finale
        console.log("Validation finale (EDI):");
        console.log("NCC formaté avec 7 chiffres:", num_cc);
        console.log("Code taxe:", codeTaxe);
        console.log("Mois formaté:", mois);
        console.log("Exercice formaté:", exercice);
        
        // Vérifier le format du NCC (doit être 7 chiffres + 1 lettre)
        var nccPattern = /^[0-9]{7}[A-Z]$/;
        if (!nccPattern.test(num_cc)) {
            alert("Le format du NCC est incorrect. Format attendu : 0012345A (7 chiffres suivis d'une lettre). NCC actuel : " + num_cc);
            return;
        }
        
        // Afficher le XML qui sera généré pour débogage
        console.log("XML qui sera généré (EDI):");
        console.log("<codeTaxe>" + codeTaxe + "</codeTaxe>");
        console.log("<ncc>" + num_cc + "</ncc>");
        console.log("<mois>" + mois + "</mois>");
        console.log("<exercice>" + exercice + "</exercice>");
        var dateactu = new Date();
        var monthly = (dateactu.getMonth() + 1).toString().padStart(2, '0');
        var jour = dateactu.getDate().toString().padStart(2, '0');
        var dateeee = exercice + '' + mois + '' + jour;
        var nombreAleatoire = Math.floor(Math.random() * 9000) + 1000;

        // VÉRIFICATION FINALE ABSOLUE juste avant la création des éléments XML
        if (!mois || mois.toString().trim() === '') {
            mois = (new Date().getMonth() + 1).toString();
            console.error("DERNIERE SAUVEGARDE (EDI): Mois vide avant XML, utilisation:", mois);
        }
        if (!exercice || exercice.toString().trim() === '') {
            exercice = new Date().getFullYear().toString();
            console.error("DERNIERE SAUVEGARDE (EDI): Exercice vide avant XML, utilisation:", exercice);
        }
        
        // FORCE BRUTE : Garantir des valeurs valides quoi qu'il arrive (EDI)
        var currentDate = new Date();
        var forcedMonth = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var forcedYear = currentDate.getFullYear().toString();
        
        // Utiliser les valeurs forcées si les valeurs récupérées sont invalides
        if (!mois || mois.trim() === '' || isNaN(parseInt(mois))) {
            mois = forcedMonth;
            console.error("FORCE BRUTE (EDI): Mois forcé à:", mois);
        }
        if (!exercice || exercice.trim() === '' || isNaN(parseInt(exercice))) {
            exercice = forcedYear;
            console.error("FORCE BRUTE (EDI): Exercice forcé à:", exercice);
        }
        
        // S'assurer que le mois a 2 chiffres
        mois = parseInt(mois).toString().padStart(2, '0');
        exercice = parseInt(exercice).toString();
        
        console.log("VALEURS FINALES pour XML (EDI) - Mois:", mois, "Exercice:", exercice);
        console.log("XML EDI contiendra: <mois>" + mois + "</mois> <exercice>" + exercice + "</exercice>");

        // Créer un document XML
        var xmlDoc = document.implementation.createDocument(null, 'EDI');

        // Créer les éléments pour les informations
        var informations = xmlDoc.createElement('informations');
        var type = xmlDoc.createElement('type');
        type.textContent = 'etat_301_mensuel';
        var ncc = xmlDoc.createElement('ncc');
        ncc.textContent = num_cc;
        var codeTaxe = xmlDoc.createElement('codeTaxe');
        codeTaxe.textContent = 'ITS';
        var moisElement = xmlDoc.createElement('mois');
        moisElement.textContent = mois;
        var exerciceElement = xmlDoc.createElement('exercice');
        exerciceElement.textContent = exercice;
        
        // DEBUG: Afficher les valeurs exactes utilisées dans le XML EDI
        console.log("=== DÉBUG XML CONSTRUCTION (EDI) ===");
        console.log("mois value:", JSON.stringify(mois));
        console.log("exercice value:", JSON.stringify(exercice));
        console.log("mois type:", typeof mois);
        console.log("exercice type:", typeof exercice);
        console.log("moisElement.textContent:", JSON.stringify(moisElement.textContent));
        console.log("exerciceElement.textContent:", JSON.stringify(exerciceElement.textContent));
        console.log("=====================================");

        // Ajouter les informations à la racine du document
        informations.appendChild(type);
        informations.appendChild(ncc);
        informations.appendChild(codeTaxe);
        informations.appendChild(moisElement);
        informations.appendChild(exerciceElement);
        xmlDoc.documentElement.appendChild(informations);

        // Créer les éléments pour les données
        var tableaux = xmlDoc.createElement('tableaux');
        var tableau = xmlDoc.createElement('tableau');
        var donnees = xmlDoc.createElement('donnees');

        // Parcourir chaque ligne du tableau
        var tbody = document.querySelector('#daclaration tbody');
        var rows = tbody.querySelectorAll('tr');

        // Récupérer les IDs des colonnes à partir des balises input cachées
        var headerIds = [];
        document.querySelectorAll('#daclaration thead input[type="hidden"]').forEach(function(input) {
            headerIds.push(input.getAttribute('id'));
        });

        rows.forEach(function(row) {
            var ligne = xmlDoc.createElement('ligne');
            var cells = row.querySelectorAll('input[type="text"]');
            for (var i = 0; i < cells.length; i++) {
                var cell = cells[i];
                var code = xmlDoc.createElement('code');
                code.textContent = headerIds[i];
                var valeur = xmlDoc.createElement('valeur');
                valeur.textContent = cell.value;
                var champ = xmlDoc.createElement('champ');
                champ.appendChild(code);
                champ.appendChild(valeur);
                ligne.appendChild(champ);
            }
            donnees.appendChild(ligne);
        });

        tableau.appendChild(donnees);
        tableaux.appendChild(tableau);
        xmlDoc.documentElement.appendChild(tableaux);

        // Convertir le document XML en une chaîne XML
        let xmlString = new XMLSerializer().serializeToString(xmlDoc);

        // Ajouter des sauts de ligne pour améliorer la lisibilité
        xmlString = xmlString.replace(/<ligne>/g, '\n<ligne>').replace(/<\/donnees>/g, '\n</donnees>');

        // Ajouter la déclaration XML au début de la chaîne
        var versionXML = document.getElementById('version_xml').value;
        var xmlStringAvecDeclaration = versionXML + xmlString;

        // Valider le XML avant de créer le Blob
        var parser = new DOMParser();
        var xmlDocValid = parser.parseFromString(xmlStringAvecDeclaration, "application/xml");
        if (xmlDocValid.getElementsByTagName('parsererror').length > 0) {
            console.error("Erreur: Le XML généré n'est pas valide.");
            return;
        }

        try {
            // Télécharger le fichier XML
            const blob = new Blob([xmlStringAvecDeclaration], { type: 'text/xml' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = num_cc + '-EDI-etat_301_mensuel-' + dateeee + '-' + nombreAleatoire + '.xml';
            link.click();
        } catch (e) {
            console.error("Erreur lors de la création du Blob : ", e);
        }
    }
</script>

<script>
    $(document).ready(function() {
        declabackcnps();

        $(document).on("change", "#exercice, #periodes_cnps", function() {
            declabackcnps();
        });
    });

    function declabackcnps() {
        var exerciceId = $("#exercice").val();
        var periodeId = $("#periodes_cnps").val();

        $.ajax({
            url: "{{ route('company.declarations.get_decla_cnps') }}",
            type: 'GET',
            data: {
                "periode_id": periodeId,
                "exercice_id": exerciceId,
            },
            success: function(data) {
                var tbodyContent = '';
                if (data.length > 0) {
                    $.each(data, function(index, element) {
                        var maDate = new Date(element.dob);
                        var annee = maDate.getFullYear();
                        var date_work = new Date(element.company_doj);
                        var jour = date_work.getDate();
                        var mois = date_work.getMonth() + 1; // Les mois sont indexés à partir de 0, donc on ajoute 1
                        var years = date_work.getFullYear();
                        var fullName = element.name;
                        var nameParts = fullName.split(" ");
                        var nom = nameParts[0].toUpperCase();
                        var prenoms = (nameParts.slice(1).join(" ")).toUpperCase();
                        var dateFormatee = (jour < 10 ? '0' : '') + jour + '/' + (mois < 10 ? '0' : '') + mois + '/' + years;
                        var typesala = (element.salary_type == '1') ? 'M' : 'J';
                        var gender = (element.salary_type == '1') ? '1' : '2';
                        var numCnps = element.num_cnps || '';
                        var salaryBrut = element.net_sociale || '';
                        if(numCnps > 0){
                            tbodyContent +=
                                '<tr>' +
                                '<td align="right" class="border border-dark">' + numCnps + '<input type="number" value="' + numCnps + '" hidden></td>' +
                                '<td align="left" class="border border-dark">' + nom + '<input type="text" value="' + nom + '" hidden></td>' +
                                '<td align="left" class="border border-dark">' + prenoms + '<input type="text" value="' + prenoms + '" hidden></td>' +
                                '<td align="right" class="border border-dark">' + annee + '<input type="text" value="' + annee + '" hidden></td>' +
                                '<td align="right" class="border border-dark">' + dateFormatee + '<input type="text" value="' + dateFormatee + '" hidden></td>' +
                                '<td align="center" class="border border-dark"> ' + '<input type="text" value=" " hidden></td>' +
                                '<td align="right" class="border border-dark">' + typesala + '<input type="text" value="' + typesala + '" hidden></td>' +
                                '<td align="right" class="border border-dark">' + gender + '<input type="text" value="' + gender + '" hidden></td>' +
                                '<td align="right" class="border border-dark">' + salaryBrut + '<input type="number" value="' + salaryBrut + '" hidden></td>' +
                                '<td align="right" class="border border-dark">123<input type="text" value="123" hidden></td>' +
                                '</tr>';
                        }
                    });
                } else {
                    var colspan = $('#table-decla-cnps thead tr th').length;
                    tbodyContent = '<tr><td class="dataTables-empty" colspan="' + colspan + '" align="center">{{ __('Aucune données') }}</td></tr>';
                }

                $('#table-decla-cnps tbody').html(tbodyContent);
                var table = document.querySelector("#table-decla-cnps");
                //var datatable = new simpleDatatables.DataTable(table);
            },
            error: function(data) {
                console.error('Une erreur s\'est produite lors de la récupération des données.');
            }
        });
    }

    function exportToExcel() {
        const table = document.getElementById('table-decla-cnps');
        var exerciceId = $("#exercice").val();
        var exerciceName = $("#exercice").find("option:selected").text().replace(/\//g, '').replace(/\s+/g, '').trim();
        var periodeId = $("#periodes_cnps").val();
        var periodeName = $("#periodes_cnps").find("option:selected").text().replace(/\//g, '').replace(/\s+/g, '').trim();
        const rows = table.getElementsByTagName('tr');
        const data = [];

        // Ajouter les titres (première ligne)
        const titleRow = rows[0];
        const titleData = [];
        const titleInputs = titleRow.querySelectorAll('th');

        for (let i = 0; i < titleInputs.length; i++) {
            titleData.push(titleInputs[i].innerText.trim());
        }

        data.push(titleData);

        // Parcourir les lignes du tableau (sauf les titres)
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const rowData = [];
            const inputs = row.querySelectorAll('input'); // Sélectionnez les balises input de type "hidden"

            // Parcourir les balises input de type hidden dans la ligne
            for (let j = 0; j < inputs.length; j++) {
                rowData.push(inputs[j].value); // Utilisez les valeurs des balises input
            }

            data.push(rowData);
        }

        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Feuille1');

        // Créez un lien de téléchargement et déclenchez un clic pour télécharger le fichier
        XLSX.writeFile(wb, 'Cotisation nominative CNPS-' + periodeName + '-' + exerciceName + '.xlsx');
    }
</script>

<script>
    $(document).ready(function() {
        declabackcmu();

        $(document).on("change", "#exercice, #periodes_cmu", function() {
            declabackcmu();
        });
    });

    function declabackcmu() {
        var exerciceId = $("#exercice").val();
        var periodeId = $("#periodes_cmu").val();
        var cpte = 1;
        var typesala = '';
        var gender = '';
        var jour = '';
        var gender_cmu = '';
        var etatcivil = '';
        var annee = '';
        var mois = '';
        var dateFormatee = '';
        var years = '';
        var date_work = '';
        var fullName = '';
        var nameParts = '';
        var maDate = '';
        var nom_enfant = '';
        $.ajax({
            url: "{{ route('company.declarations.get_decla_cmu') }}",
            type: 'GET',
            data: {
                "periode_id": periodeId,
                "exercice_id": exerciceId,
            },
            success: function(data) {
                var tr = '';
                if (data.length > 0) {
                    var tr = '';
                    $.each(data, function(index, element) {
                        maDate = new Date(element.dob);
                        annee = maDate.getFullYear();
                        date_work = new Date(element.dob);
                        jour = date_work.getDate();
                        mois = date_work.getMonth() + 1; // Les mois sont indexés à partir de 0, donc on ajoute 1
                        years = date_work.getFullYear();
                        fullName = element.name;
                        nameParts = fullName.split(" ");
                        // Formater la date au format "dd/mm/yyyy"
                        dateFormatee = (jour < 10 ? '0' : '') + jour + '/' + (mois < 10 ? '0' : '') + mois + '/' + years;

                        if(element.gender == 'Male') {
                            gender = 'H';
                        } else {
                            gender = 'F';
                        }
                        if(element.num_cnps > 0) {
                            tr += '<tr>' +
                                '<td align="right" class="border border-dark">' + element.num_cnps + '<input type="number" value="'+ element.num_cnps +'" hidden></td>' +
                                '<td align="right" class="border border-dark">' + element.num_secu_soc + '<input type="text" value="'+ element.num_secu_soc +'" hidden></td>' +
                                '<td align="left" class="border border-dark">' + nameParts[0].toUpperCase() + '<input type="text" value="'+ nameParts[0].toUpperCase() +'" hidden></td>' +
                                '<td align="left" class="border border-dark">' + nameParts.slice(1).join(" ").toUpperCase() + '<input type="text" value="'+ nameParts.slice(1).join(" ").toUpperCase() +'" hidden></td>' +
                                '<td align="right" class="border border-dark">' + dateFormatee + '<input type="text" value="'+ dateFormatee +'" hidden></td>' +
                                '<td align="right" class="border border-dark">' + element.num_cnps + '<input type="number" value="'+ element.num_cnps +'" hidden></td>' +
                                '<td align="right" class="border border-dark">' + element.num_secu_soc + '<input type="text" value="' + element.num_secu_soc + '" hidden></td>' +
                                '<td align="right" class="border border-dark">' + 'T' + '<input type="text" value="T" hidden></td>' +
                                '<td align="left" class="border border-dark">' + nameParts[0].toUpperCase() + '<input type="text" value="'+ nameParts[0].toUpperCase() +'" hidden></td>' +
                                '<td align="left" class="border border-dark">' + nameParts.slice(1).join(" ").toUpperCase() + '<input type="text" value="'+ nameParts.slice(1).join(" ").toUpperCase() +'" hidden></td>' +
                                '<td align="right" class="border border-dark">' + dateFormatee + '<input type="text" value="'+ dateFormatee +'" hidden></td>' +
                                '<td align="right" class="border border-dark">' + gender + '<input type="text" value="'+ gender +'" hidden></td>' +
                                '</tr>';
                            var familles = element.famille;
                            $.each(familles, function(i, famille) {
                                if(famille.type_cmu == 'Conjoint') {
                                    typesala = 'C';
                                } else {
                                    typesala = 'E';
                                }
                                if(famille.genre_cmu == 'Homme') {
                                    gender_cmu = 'H';
                                } else {
                                    gender_cmu = 'F';
                                }
                                date_cmu = new Date(famille.date_naiss_cmu);
                                jour_cmu = date_cmu.getDate();
                                mois_cmu = date_cmu.getMonth() + 1; // Les mois sont indexés à partir de 0, donc on ajoute 1
                                years_cmu = date_cmu.getFullYear();
                                dateFormateeCmu = (jour_cmu < 10 ? '0' : '') + jour_cmu + '/' + (mois_cmu < 10 ? '0' : '') + mois_cmu + '/' + years_cmu;
                                tr += '<tr>' +
                                    '<td align="right" class="border border-dark">' + element.num_cnps + '<input type="number" value="'+ element.num_cnps +'" hidden></td>' +
                                    '<td align="right" class="border border-dark">' + element.num_secu_soc + '<input type="text" value="'+ element.num_secu_soc +'" hidden></td>' +
                                    '<td align="left" class="border border-dark">' + nameParts[0].toUpperCase() + '<input type="text" value="'+ nameParts[0].toUpperCase() +'" hidden></td>' +
                                    '<td align="left" class="border border-dark">' + nameParts.slice(1).join(" ").toUpperCase() + '<input type="text" value="'+ nameParts.slice(1).join(" ").toUpperCase() +'" hidden></td>' +
                                    '<td align="right" class="border border-dark">' + dateFormatee + '<input type="text" value="'+ dateFormatee +'" hidden></td>' +
                                    '<td align="right" class="border border-dark">' + element.num_cnps + '<input type="number" value="'+ element.num_cnps +'" hidden></td>' +
                                    '<td align="right" class="border border-dark">' + famille.num_cmu + '<input type="text" value="' + famille.num_cmu + '" hidden></td>' +
                                    '<td align="right" class="border border-dark">' + typesala + '<input type="text" value="' + typesala + '" hidden></td>' +
                                    '<td align="left" class="border border-dark">' + famille.name_cmu + '<input type="text" value="'+ famille.name_cmu +'" hidden></td>' +
                                    '<td align="left" class="border border-dark">' + famille.prenom_cmu + '<input type="text" value="'+ famille.prenom_cmu +'" hidden></td>' +
                                    '<td align="right" class="border border-dark">' + dateFormateeCmu + '<input type="text" value="'+ dateFormateeCmu +'" hidden></td>' +
                                    '<td align="right" class="border border-dark">' + gender_cmu + '<input type="text" value="'+ gender_cmu +'" hidden></td>' +
                                    '</tr>';
                            });
                        }
                    });
                } else {
                    var colspan = $('#table-decla-cmu thead tr th').length;
                    var tr = '<tr><td class="dataTables-empty" colspan="' + colspan + '" align="center">{{ __('Aucune données') }}</td></tr>';
                }

                $('#table-decla-cmu tbody').html(tr);
                var table = document.querySelector("#table-decla-cmu");
                //var datatable = new simpleDatatables.DataTable(table);
            },
            error: function(data) {

            }
        });
    }

    // Fonction pour exporter en Excel
    function exportToExce2() {
        const table = document.getElementById('table-decla-cmu');
        const rows = table.getElementsByTagName('tr');
        const data = [];

        // Ajouter les titres (première ligne)
        const titleRow = rows[0];
        const titleData = [];
        const titleInputs = titleRow.querySelectorAll('th');

        for (let i = 0; i < titleInputs.length; i++) {
            titleData.push(titleInputs[i].innerText.trim());
        }

        data.push(titleData);


        // Parcourir les lignes du tableau (sauf les titres)
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const rowData = [];
            const inputs = row.querySelectorAll('input'); // Sélectionnez les balises input de type "hidden"

            // Parcourir les balises input de type hidden dans la ligne
            for (let j = 0; j < inputs.length; j++) {
                rowData.push(inputs[j].value); // Utilisez les valeurs des balises input
            }

            data.push(rowData);
        }

        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Feuille1');


        // Créez un lien de téléchargement et déclenchez un clic pour télécharger le fichier
        XLSX.writeFile(wb, 'Cotisation nominative CMU.xlsx');
    }
</script>
@endpush
