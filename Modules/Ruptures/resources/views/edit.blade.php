@extends('layouts.app')

@section('title', 'Modifier une rupture')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Modifier une rupture</h4>
                        <p class="text-muted mb-0">Gérez les ruptures de contrat et sanctions des employés</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                                </li>
                                <li class="breadcrumb-item active">Modifier une rupture</li>
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
                        <a href="{{ route('company.ruptures.index') }}?periode_id={{$rupture->periode_id}}"
                            class="btn bg-label-primary">
                            <i class="ti ti-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Formulaire de modification</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('company.ruptures.update', $rupture->id) }}" method="POST"
                            enctype="multipart/form-data" id="editRuptureForm">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="employee_id">Employé <span
                                            class="text-danger">*</span></label>
                                    <select id="employee_id" name="employee_id" class="form-select select2"
                                        data-allow-clear="true" required>
                                        <option value="">Sélectionner un employé</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $rupture->employee_id == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback d-block">{{ $message }} </div>
                                    @enderror
                                </div>
                                {{ $rupture->contract }}
                                <div class="col-md-6">
                                    <label class="form-label" for="type_contrat">Contrat de l'employé <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="type_contrat" name="type_contrat" class="form-control"
                                        value="{{ $contracts->type->name ?? '' }}" readonly />
                                    <input type="hidden" id="type_contrat_id" name="type_contrat_id"
                                        value="{{ $contracts->type->id ?? '' }}" />
                                    <input type="hidden" id="periode_id" name="periode_id"
                                        value="{{ $rupture->periode_id }}" required />
                                    <input type="hidden" id="contract_id" name="contract_id"
                                        value="{{ $rupture->contract_id }}" />
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="rupture_type_id">Type de rupture <span
                                            class="text-danger">*</span></label>
                                    <select id="rupture_type_id" name="rupture_type_id" class="form-select select2"
                                        required>
                                        <option value="">Sélectionner un type</option>
                                        @foreach($ruptureTypes as $type)
                                            <option value="{{ $type->id }}" {{ $rupture->rupture_type_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rupture_type_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="request_date">Date du préavis <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="request_date" name="request_date"
                                        class="form-control flatpickr-date" placeholder="DD-MM-YYYY"
                                        value="{{ \Carbon\Carbon::parse($rupture->notice_date)->format('d-m-Y') }}"
                                        required />
                                    @error('request_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="effective_date">Date de départ <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="effective_date" name="effective_date"
                                        class="form-control flatpickr-date" placeholder="DD-MM-YYYY"
                                        value="{{ \Carbon\Carbon::parse($rupture->termination_date)->format('d-m-Y') }}"
                                        required />
                                    @error('effective_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label" for="description">Motif de la rupture</label>
                                    <textarea name="description" id="description"
                                        class="form-control">{{ $rupture->description }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card mb-4">
                                <h5 class="card-header">Droits de la rupture</h5>
                                <div class="card-body">
                                    <div id="legal_rights" style="display: block;">
                                        <h6>I- Les droits légaux :</h6>
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-md-6">
                                                <input type="checkbox" name="legal_rights[]" value="gratification"
                                                    id="gratification" {{ $rupture->indem_comp > 0 ? 'checked' : '' }}>
                                                <label for="gratification">Indemnité compensatrice de Gratification</label>
                                                <div id="gratification_amount"
                                                    style="display: {{ $rupture->indem_comp > 0 ? 'block' : 'none' }};">
                                                    <input type="number" name="gratification_amount_input"
                                                        class="form-control"
                                                        placeholder="Montant de l'indemnité compensatrice de Gratification"
                                                        id="gratification_amount_input" value="{{ $rupture->indem_comp }}"
                                                        oninput="getRetenues()">
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6">
                                                <input type="checkbox" name="legal_rights[]" value="conge_2021"
                                                    id="conge_2021" {{ $rupture->indem_comp_cong > 0 ? 'checked' : '' }}>
                                                <label for="conge_2021">Indemnité compensatrice de congé</label>
                                                <div id="conge_2021_amount"
                                                    style="display: {{ $rupture->indem_comp_cong > 0 ? 'block' : 'none' }};">
                                                    <input type="number" name="conge_2021_amount_input" class="form-control"
                                                        placeholder="Montant de l'indemnité compensatrice de congé"
                                                        id="conge_2021_amount_input" value="{{ $rupture->indem_comp_cong }}"
                                                        oninput="getRetenues()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="specific_rights" style="display: block;">
                                        <h6>II- Les droits spécifiques :</h6>
                                        <div class="row">
                                            <div class="form-group col-lg-6 col-md-6 mb-3">
                                                <input type="checkbox" name="specific_rights[]" value="preavis" id="preavis"
                                                    {{ $rupture->imdem_prea > 0 ? 'checked' : '' }}>
                                                <label for="preavis">Indemnité de préavis</label>
                                                <div id="preavis_amount"
                                                    style="display: {{ $rupture->imdem_prea > 0 ? 'block' : 'none' }};">
                                                    <input type="number" name="preavis_amount_input" class="form-control"
                                                        placeholder="Montant de l'indemnité de préavis"
                                                        id="preavis_amount_input" value="{{ $rupture->imdem_prea }}"
                                                        oninput="getRetenues()">
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 mb-3">
                                                <input type="checkbox" name="specific_rights[]" value="aggravation_preavis"
                                                    id="aggravation_preavis" {{ $rupture->indem_licence > 0 ? 'checked' : '' }}>
                                                <label for="aggravation_preavis">Aggravation de l'indemnité compensatrice de
                                                    préavis</label>
                                                <div id="aggravation_preavis_amount"
                                                    style="display: {{ $rupture->indem_licence > 0 ? 'block' : 'none' }};">
                                                    <input type="number" name="aggravation_preavis_amount_input"
                                                        class="form-control"
                                                        placeholder="Montant de l'aggravation de l'indemnité compensatrice de préavis"
                                                        id="aggravation_preavis_amount_input"
                                                        value="{{ $rupture->indem_licence }}" oninput="getRetenues()">
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 mb-3">
                                                <input type="checkbox" name="specific_rights[]" value="licenciement"
                                                    id="licenciement" {{ $rupture->aggravation > 0 ? 'checked' : '' }}>
                                                <label for="licenciement">Indemnité de licenciement / fin contrat</label>
                                                <div id="licenciement_amount"
                                                    style="display: {{ $rupture->aggravation > 0 ? 'block' : 'none' }};">
                                                    <input type="number" name="licenciement_amount_input"
                                                        class="form-control"
                                                        placeholder="Montant de l'indemnité de licenciement"
                                                        id="licenciement_amount_input" value="{{ $rupture->aggravation }}"
                                                        oninput="getRetenues()">
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-6 mb-3">
                                                <input type="checkbox" name="specific_rights[]" value="dommages_interets"
                                                    id="dommages_interets" {{ $rupture->dom_inter > 0 ? 'checked' : '' }}>
                                                <label for="dommages_interets">Dommages et intérêts</label>
                                                <div id="dommages_interets_amount"
                                                    style="display: {{ $rupture->dom_inter > 0 ? 'block' : 'none' }};">
                                                    <input type="number" name="dommages_interets_amount_input"
                                                        class="form-control" placeholder="Montant des dommages et intérêts"
                                                        id="dommages_interets_amount_input"
                                                        value="{{ $rupture->dom_inter }}" oninput="getRetenues()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="specific_rights" style="display: block;">
                                        <h6>III- Les retenues :</h6>
                                        <div class="row">
                                            <div class="form-group col-lg-4 col-md-4 mb-3">
                                                <input type="checkbox" name="specific_rights[]" value="cnps" id="cnps"
                                                    checked>
                                                <label for="cnps">CNPS</label>
                                                <div id="cpns_amount" style="display: block;">
                                                    <input type="number" name="amount_cnps" class="form-control"
                                                        id="cpns_amount_input" readonly value="{{ $rupture->amount_cnps }}">
                                                    <input type="hidden" name="nbre_parts" class="form-control" required
                                                        value="{{ $rupture->employee->parts ?? 1 }}">
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4">
                                                <input type="checkbox" name="specific_rights[]" value="its" id="its"
                                                    checked>
                                                <label for="its">ITS</label>
                                                <div id="its_amount" style="display: block;">
                                                    <input type="number" name="amount_its" class="form-control"
                                                        id="its_amount_input" readonly value="{{ $rupture->amount_its }}">
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-4 col-md-4">
                                                <input type="checkbox" name="specific_rights[]" value="loan" id="loan" {{ $rupture->amount_loan > 0 ? 'checked' : '' }}>
                                                <label for="loan">Prêts</label>
                                                <div id="loan_amount"
                                                    style="display: {{ $rupture->amount_loan > 0 ? 'block' : 'none' }};">
                                                    <input type="number" name="amount_loan" class="form-control"
                                                        placeholder="Montant du prêt" id="loan_amount_input"
                                                        value="{{ $rupture->amount_loan }}" oninput="getRetenues()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-sm-12 d-flex justify-content-between">
                                    <a href="{{ route('company.ruptures.show', $rupture->id) }}"
                                        class="btn btn-label-danger">Annuler</a>
                                    <button type="submit" class="btn btn-primary me-2">Enregistrer les
                                        modifications</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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

            // Gestion de l'affichage des champs d'entrée pour les droits légaux
            $('input[type="checkbox"]').change(function () {
                const targetId = $(this).attr('id') + '_amount';
                if ($(this).is(':checked')) {
                    $('#' + targetId).show();
                } else {
                    $('#' + targetId).hide();
                    $('#' + targetId + '_input').val('0');
                }
                getRetenues();
            });

            // Fonction pour récupérer les informations du contrat
            function getContract() {
                var employeeId = $('#employee_id').val();

                if (employeeId) {
                    $.ajax({
                        url: '{{ route("company.ruptures.get-contract-type", ":employeeId") }}'.replace(':employeeId', employeeId),
                        method: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            if (response) {
                                $('#type_contrat').val(response.type_contrat);
                                $('#type_contrat_id').val(response.type_contrat_id);
                                $('#contract_id').val(response.contract_id);
                                $('#loan_amount_input').val(response.amount_loan);
                                $('#nbre_parts').val(response.parts);
                                getRetenues();
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Erreur lors de la récupération du type de contrat :', error);
                        }
                    });
                }
            }

            // Appeler getContract lorsqu'un changement est détecté dans le champ d'employé
            $(document).on("change", "#employee_id", function () {
                getContract();
            });

            // Soumission du formulaire avec les pièces jointes
            $('#editRuptureForm').on('submit', function (e) {
                if (myDropzone.getQueuedFiles().length > 0) {
                    e.preventDefault();
                    myDropzone.processQueue();
                }
            });

            // Fonction pour calculer les retenues
            window.getRetenues = function () {
                var amount1 = parseFloat($('#gratification_amount_input').val()) || 0;
                var amount2 = parseFloat($('#conge_2021_amount_input').val()) || 0;
                var amount3 = parseFloat($('#preavis_amount_input').val()) || 0;
                var amount4 = parseFloat($('#aggravation_preavis_amount_input').val()) || 0;
                var amount5 = parseFloat($('#licenciement_amount_input').val()) || 0;
                var amount6 = parseFloat($('#dommages_interets_amount_input').val()) || 0;
                var cnps = $('#cpns_amount_input');
                var its = $('#its_amount_input');
                var impo_amount = 0;

                if (amount5 > 75000) {
                    impo_amount = Math.round(amount5 / 2);
                } else {
                    impo_amount = 0;
                }

                var droit = amount1 + amount2 + amount3 + amount4 + impo_amount + amount6;

                // Calcul CNPS (6.3% du montant brut)
                var cnpsValue = Math.round((droit * 6.3) / 100);
                cnps.val(cnpsValue);

                // Calcul ITS (Impôt sur le Traitement des Salaires)
                var resultimpricf = 0;
                var baseImposable = droit;

                if (baseImposable <= 75000) {
                    resultimpricf = 0;
                } else if (baseImposable <= 240000) {
                    resultimpricf = Math.round((baseImposable - 75000) * 0.16);
                } else if (baseImposable <= 800000) {
                    resultimpricf = Math.round((165000 * 0.16) + ((baseImposable - 240000) * 0.21));
                } else if (baseImposable <= 2400000) {
                    resultimpricf = Math.round((165000 * 0.16) + (560000 * 0.21) + ((baseImposable - 800000) * 0.24));
                } else if (baseImposable <= 8000000) {
                    resultimpricf = Math.round((165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + ((baseImposable - 2400000) * 0.28));
                } else {
                    resultimpricf = Math.round((165000 * 0.16) + (560000 * 0.21) + (1600000 * 0.24) + (5600000 * 0.28) + ((baseImposable - 8000000) * 0.32));
                }

                // Déduction pour charge de famille
                var nbreParts = parseFloat($('#nbre_parts').val()) || 1;
                var deductionFamille = 0;

                if (nbreParts > 1) {
                    // 5 500 FCFA par part supplémentaire
                    deductionFamille = (nbreParts - 1) * 5500;
                }

                var total = Math.max(0, resultimpricf - deductionFamille);
                its.val(total);

                // Calcul du solde net
                var totalRetenues = cnpsValue + total + (parseFloat($('#loan_amount_input').val()) || 0);
                var soldeNet = droit - totalRetenues;

                // Mise à jour du solde affiché
                $('#solde_net').val(soldeNet.toLocaleString('fr-FR') + ' FCFA');
            };
        });
    </script>
@endpush