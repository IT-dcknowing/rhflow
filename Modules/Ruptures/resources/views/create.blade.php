@extends('layouts.app')

@section('title', 'Nouvelle rupture de contrat')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête de la Gestion des Ruptures -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">❌ Nouvelle rupture</h4>
                    <p class="text-muted mb-0">Gérez les ruptures de contrat et sanctions des employés</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item active">Nouvelle rupture</li>
                        </ol>
                    </nav>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ ucfirst(Carbon\Carbon::now()->locale('fr_FR')->isoFormat('dddd D MMMM YYYY')) }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ Carbon\Carbon::now()->locale('fr_FR')->isoFormat('HH:mm') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.ruptures.index') }}" class="btn bg-label-primary">
                        <i class="ti ti-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
    <form id="createRuptureForm" method="POST" action="{{ route('company.ruptures.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Détails de la rupture</h5>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="employee_id">Employé <span class="text-danger">*</span></label>
                                <select id="employee_id" name="employee_id" class="form-select select2" data-allow-clear="true" required>
                                    <option value="">Sélectionner un employé</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="type_contrat">Contrat de l'employé <span class="text-danger">*</span></label>
                                <input type="text" id="type_contrat" name="type_contrat" class="form-control" readonly />
                                <input type="hidden" id="type_contrat_id" name="type_contrat_id" class="form-control" />
                                <input type="hidden" id="periode_id" name="periode_id" class="form-control" value="{{ $periode->id }}" required />
                                <input type="hidden" id="contract_id" name="contract_id" class="form-control" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="rupture_type_id">Type de rupture <span class="text-danger">*</span></label>
                                <select id="rupture_type_id" name="rupture_type_id" class="select2 form-select" data-allow-clear="true" required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('rupture_type_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="request_date">Date du préavis <span class="text-danger">*</span></label>
                                <input type="text" id="request_date" name="request_date" class="form-control flatpickr-date" placeholder="DD-MM-YYYY" required />
                                @error('request_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label" for="effective_date">Date de départ <span class="text-danger">*</span></label>
                                <input type="text" id="effective_date" name="effective_date" class="form-control flatpickr-date" placeholder="DD-MM-YYYY" required />
                                @error('effective_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" for="description">Motif de la rupture</label>
                                <textarea name="description" id="description" class="form-control"></textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Droits de la rupture</h5>
                    <div class="card-body">
                        <div id="legal_rights" style="display: block;">
                            <h6>I- Les droits légaux :</h6>
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6">
                                    <input type="checkbox" name="legal_rights[]" value="gratification" id="gratification">
                                    <label for="gratification">Indemnité compensatrice de Gratification</label>
                                    <div id="gratification_amount" style="display: none;">
                                        <input type="number" name="gratification_amount_input" class="form-control" placeholder="Montant de l'indemnité compensatrice de Gratification" id="gratification_amount_input" oninput="getRetenues()">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6">
                                    <input type="checkbox" name="legal_rights[]" value="conge_2021" id="conge_2021">
                                    <label for="conge_2021">Indemnité compensatrice de congé</label>
                                    <div id="conge_2021_amount" style="display: none;">
                                        <input type="number" name="conge_2021_amount_input" class="form-control" placeholder="Montant de l'indemnité compensatrice de congé" id="conge_2021_amount_input" oninput="getRetenues()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="specific_rights" style="display: block;">
                            <h6>II- Les droits spécifiques :</h6>
                            <div class="row">
                                <div class="form-group col-lg-6 col-md-6 mb-3">
                                    <input type="checkbox" name="specific_rights[]" value="preavis" id="preavis">
                                    <label for="preavis">Indemnité de préavis</label>
                                    <div id="preavis_amount" style="display: none;">
                                        <input type="number" name="preavis_amount_input" class="form-control" placeholder="Montant de l'indemnité de préavis" id="preavis_amount_input" oninput="getRetenues()">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 mb-3">
                                    <input type="checkbox" name="specific_rights[]" value="aggravation_preavis" id="aggravation_preavis">
                                    <label for="aggravation_preavis">Aggravation de l'indemnité compensatrice de préavis</label>
                                    <div id="aggravation_preavis_amount" style="display: none;">
                                        <input type="number" name="aggravation_preavis_amount_input" class="form-control" placeholder="Montant de l'aggravation de l'indemnité compensatrice de préavis" id="aggravation_preavis_amount_input" oninput="getRetenues()">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 mb-3">
                                    <input type="checkbox" name="specific_rights[]" value="licenciement" id="licenciement">
                                    <label for="licenciement">Indemnité de licenciement / fin contrat</label>
                                    <div id="licenciement_amount" style="display: none;">
                                        <input type="number" name="licenciement_amount_input" class="form-control" placeholder="Montant de l'indemnité de licenciement" id="licenciement_amount_input" oninput="getRetenues()">
                                    </div>
                                </div>
                                <div class="form-group col-lg-6 col-md-6 mb-3">
                                    <input type="checkbox" name="specific_rights[]" value="dommages_interets" id="dommages_interets">
                                    <label for="dommages_interets">Dommages et intérêts</label>
                                    <div id="dommages_interets_amount" style="display: none;">
                                        <input type="number" name="dommages_interets_amount_input" class="form-control" placeholder="Montant des dommages et intérêts" id="dommages_interets_amount_input" oninput="getRetenues()">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div id="specific_rights" style="display: block;">
                            <h6>III- Les retenues :</h6>
                            <div class="row">
                                <div class="form-group col-lg-4 col-md-4 mb-3">
                                    <input type="checkbox" name="specific_rights[]" value="cnps" id="cnps">
                                    <label for="cnps">CNPS</label>
                                    <div id="amount_cnps" style="display: block;">
                                        <input type="number" name="amount_cnps" class="form-control" id="montant_cnps" readonly>
                                        <input type="text" name="nbre_parts" class="form-control" required hidden id="nbre_parts">
                                    </div>
                                </div>
                                <div class="form-group col-lg-4 col-md-4">
                                    <input type="checkbox" name="specific_rights[]" value="its" id="its">
                                    <label for="its">ITS</label>
                                    <div id="amount_its" style="display: block;">
                                        <input type="number" name="amount_its" class="form-control" id="montant_its" readonly>
                                    </div>
                                </div>
                                <div class="form-group col-lg-4 col-md-4">
                                    <input type="checkbox" name="specific_rights[]" value="loan" id="loan">
                                    <label for="loan">Prêts</label>
                                    <div id="amount_loan" style="display: block;">
                                        <input type="number" name="amount_loan" class="form-control" placeholder="Prêts" id="montant_loan">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 d-flex justify-content-between">
                <button type="button" class="btn btn-label-danger" onclick="window.history.back();">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        'use strict';

        // Initialisation de Flatpickr
        $('.flatpickr-date').flatpickr({
            dateFormat: 'd-m-Y',
            locale: 'fr_FR'
        });

        // Initialisation de Select2
        $('.select2').each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Sélectionner une option',
                dropdownParent: $this.parent()
            });
        });
    });

    // Gérer l'affichage des champs d'entrée pour les droits légaux
    $('#gratification').change(function() {
        if (this.checked) {
            $('#gratification_amount').show();
        } else {
            $('#gratification_amount').hide();
        }
    });

    $('#conge_2021').change(function() {
        if (this.checked) {
            $('#conge_2021_amount').show();
        } else {
            $('#conge_2021_amount').hide();
        }
    });

    // Gérer l'affichage des champs d'entrée pour les droits spécifiques
    $('#preavis').change(function() {
        if (this.checked) {
            $('#preavis_amount').show();
        } else {
            $('#preavis_amount').hide();
        }
    });

    $('#aggravation_preavis').change(function() {
        if (this.checked) {
            $('#aggravation_preavis_amount').show();
        } else {
            $('#aggravation_preavis_amount').hide();
        }
    });

    $('#licenciement').change(function() {
        if (this.checked) {
            $('#licenciement_amount').show();
        } else {
            $('#licenciement_amount').hide();
        }
    });

    $('#dommages_interets').change(function() {
        if (this.checked) {
            $('#dommages_interets_amount').show();
        } else {
            $('#dommages_interets_amount').hide();
        }
    });

    $('#cnps').change(function() {
        if (this.checked) {
            $('#amount_cnps').show();
        } else {
            $('#amount_cnps').hide();
        }
    });

    $('#its').change(function() {
        if (this.checked) {
            $('#amount_its').show();
        } else {
            $('#amount_its').hide();
        }
    });

    $('#loan').change(function() {
        if (this.checked) {
            $('#amount_loan').show();
        } else {
            $('#amount_loan').hide();
        }
    });

    // Appeler getContract lorsqu'un changement est détecté dans le champ d'employé
    $(document).on("change", "#employee_id", function() {
        getContract();
    });

    // Si vous souhaitez recharger la liste des employés lors de l'ouverture du modal
    $('#modal_termi').on('show.bs.modal', function () {
        // Rechargez ou réinitialisez la liste déroulante ici si nécessaire
        $('#employee_id').val(null).trigger('change');
    });

    $('#modal_termi').on('show.bs.modal', function () {
        $('#employee_id').val(null).trigger('change');
        // Réinitialiser les autres champs également
        $('#type_contrat').val('');
        $('#type_contrat_id').val('');
        $('#montant_loan').val('');
        $('#nbre_parts').val('');
    });
    

    function getContract() {
        var employeeId = $('#employee_id').val();

        // Vérifiez si l'employeeId est défini
        if (employeeId) {
            $.ajax({
                url: '{{ route("company.ruptures.get-contract-type", ":employeeId") }}'.replace(':employeeId', employeeId),
                method: 'GET',
                dataType: 'json', 
                success: function(response) {
                    $('#type_contrat').val(response.type_contrat);
                    $('#type_contrat_id').val(response.type_contrat_id);
                    $('#contract_id').val(response.contract_id);
                    $('#montant_loan').val(response.amount_loan);
                    $('#nbre_parts').val(response.parts);
                },
                error: function(xhr, status, error) {
                    console.error('Une erreur s\'est produite lors de la récupération du type de contrat :', error);
                }
            });
        } else {
            console.warn('Aucun employé sélectionné.');
            // Réinitialiser les champs si aucun employé n'est sélectionné
            $('#type_contrat').val('');
            $('#type_contrat_id').val('');
            $('#montant_loan').val('');
            $('#nbre_parts').val('');
        }
    }

    function getRetenues() {
        var amount1 = parseInt(document.getElementById('gratification_amount_input').value) || 0;
        var amount2 = parseInt(document.getElementById('conge_2021_amount_input').value) || 0;
        var amount3 = parseInt(document.getElementById('preavis_amount_input').value) || 0;
        var amount4 = parseInt(document.getElementById('aggravation_preavis_amount_input').value) || 0;
        var amount5 = parseInt(document.getElementById('licenciement_amount_input').value) || 0;
        var amount6 = parseInt(document.getElementById('dommages_interets_amount_input').value) || 0;
        var cnps = document.getElementById('montant_cnps');
        var its = document.getElementById('montant_its');
        var impo_amount = 0;

        if (amount5 > 75000) {
            impo_amount = Math.round(amount5 / 2);
        } else {
            impo_amount = 0;
        }

        var droit = amount1 + amount2 + amount3 + amount4 + impo_amount + amount6;
        
        cnps.value = Math.round((droit * 6.3) / 100);
        //alert(cnps.value);
        var resultimpricf = 0;

        if(droit >= 0 && droit <= 75000){
            var tot = (droit*0)/100;
            resultimpricf = Math.round(tot);
        }else if(droit > 75000 && droit <= 240000){
            var mt1 = droit-75000;
            var tot1 = (((75000*0)/100)+((mt1*16)/100));
            //r = (((droit*80)/100)-(its+total))*85/100;
            resultimpricf = Math.round(tot1);
        }else if(droit > 240000 && droit <= 800000){
            var mt2 = droit-240000;
            var tot2 = (((75000*0)/100)+((165000*16)/100)+((mt2*21)/100));
            //r = (((droit*80)/100)-(its+total1))*85/100;
            resultimpricf = Math.round(tot2);
        }else if(droit > 800000 && droit <= 2400000){
            var mt3 = droit-800000;
            var tot3 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((mt3*24)/100));
            //r = (((droit*80)/100)-(its+total1))*85/100;
            resultimpricf = Math.round(tot3);
        }else if(droit > 2400000 && droit <= 8000000){
            var mt4 = droit-2400000;
            var tot4 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+((mt4*28)/100));
            //r = (((droit*80)/100)-(its+total1))*85/100;
            resultimpricf = Math.round(tot4);
        }else if(droit > 8000000){
            var mt5 = droit-8000000;
            var tot5 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+((5600000*28)/100)+((mt5*32)/100));
            //r = (((droit*80)/100)-(its+total2))*85/100;
            resultimpricf = Math.round(tot5); 
        }

        // Fix: Table 2023 certifiée avec gestion robuste des virgules
        var rawNbre = document.getElementById("nbre_parts").value;
        var p = parseFloat((rawNbre || "").toString().replace(",", ".")) || 0;
        var resultricf = 0;
        
        var fixedTable = {
            1: 0, 1.5: 5500, 2: 11000, 2.5: 16500,
            3: 22000, 3.5: 27500, 4: 33000, 4.5: 38500, 5: 44000
        };
        
        for (var key in fixedTable) {
            if (Math.abs(p - parseFloat(key)) < 0.01) {
                resultricf = fixedTable[key];
                break;
            }
        }
        if (p > 5) {
            resultricf = 44000;
        }

        var total = 0;
        total = Math.round(resultimpricf - resultricf);

        if(total < 0){
            its.value = 0;
        }else{
            its.value = total;
        }
    }
</script>
@endpush