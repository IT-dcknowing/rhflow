@extends('layouts.app')

@section('title', 'Créer une nature d\'avantage')

@push('vendor-styles')
    <link rel="stylesheet" href="{{asset('vendor/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/libs/formvalidation/dist/css/formValidation.min.css')}}">
@endpush

@push('vendor-scripts')
    <script src="{{asset('vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
    <script src="{{asset('vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
    <script src="{{asset('vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Créer un avantage en nature
                            @if($periode) | Exercice :
                                {{ $periode->exercice->nom }} - <span
                                    class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($periode->exercice->statut) }}
                            @endif
                        </h4>
                        <p class="text-muted mb-0">Gérez les avantages en natures des employés</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                                </li>
                                <li class="breadcrumb-item active">Gestion avantages en natures @if($periode) - Période :
                                    {{ $periode->nom }} - <span
                                        class="badge bg-label-{{ $periode->statut === 'en_cours' ? 'success' : ($periode->statut === 'cloture' ? 'secondary' : 'warning') }}">{{ ucfirst($periode->statut) }}</span>
                                @endif</li>
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
                        @if($periode)
                            <a href="{{ route('company.avantages.index') }}?periode_id={{$periode->id}}"
                                class="btn btn-primary me-2">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Nouvelle avantage en nature</h5>
                    </div>
                    <div class="card-body">
                        <form id="avantageForm" class="row g-3" action="{{ route('company.avantages.store') }}"
                            method="POST">
                            @csrf

                            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="form-label" for="employee_id">Employé <span
                                        class="text-danger">*</span></label>
                                <select id="employee_id" name="employee_id" class="form-select select2"
                                    data-allow-clear="true" required>
                                    <option value="">Sélectionner un employé</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <span class="invalid-employee_id" role="alert">
                                        <strong class="text-danger">{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="form-label" for="type_avantage">Type d\'avantage <span
                                        class="text-danger">*</span></label>
                                <select id="type_avantage" name="type_avantage" class="form-select select2" required>
                                    <option>Sélectionnez un avantage</option>
                                    <option value="avantage_en_nature">Avantage en nature</option>
                                    <option value="avantage_en_argent">Avantage en argent</option>
                                    <option value="assurance_vie_complementaire">Assurance-vie complémentaire</option>
                                    <option value="assurance_sante">Assurance santé</option>
                                </select>
                            </div>
                            <div id="avantage_argent" style="display: none;">
                                <div class="form-group  col-lg-12">
                                    <label class="form-label" for="description">Libellé de l'avantage en argent <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="description" name="description" class="form-control"
                                        placeholder="Nom de l'avantage en argent" />
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="form-label" for="traitement">Traitement <span
                                        class="text-danger">*</span></label>
                                <select id="traitement" name="traitement" class="form-control select2"
                                    data-allow-clear="true" required>
                                    <option value="">Sélectionner un traitement</option>
                                    <option value="mensuel">Mensuel </option>
                                    <option value="annuel">Annuel </option>
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="form-label" for="amount_real">Montant réel <span
                                        class="text-danger">*</span></label>
                                <input type="number" id="amount_real" name="amount_real" class="form-control"
                                    placeholder="Montant réel" />
                            </div>
                            <div id="avantage_nature" style="display: none;" class="mb-4">
                                <hr>
                                <div class="card-header">
                                    <h5>Logement et Accessoires</h5>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 mb-3">
                                        <label class="form-label" for="nombre_pieces">Nombre de pièces <span
                                                class="text-danger">*</span></label>
                                        <select id="nombre_pieces" name="nombre_pieces" class="form-select select2"
                                            placeholder="Nombre de pièces" required>
                                            <option>Sélectionnez</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7 et plus">7 et plus</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="montant_logement">Montant pour le logement <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="montant_logement" name="montant_logement"
                                            class="form-control" placeholder="Montant logement" />
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="montant_mobilier">Montant pour le mobilier <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="montant_mobilier" name="montant_mobilier"
                                            class="form-control" placeholder="Montant mobilier" />
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="electric">Montant pour l'électricité <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="electric" name="electric" class="form-control"
                                            placeholder="Montant électricité" />
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="montant_eau">Montant pour l'eau <span
                                                class="text-danger">*</span></label>
                                        <input type="number" id="montant_eau" name="montant_eau" class="form-control"
                                            placeholder="Montant eau" />
                                    </div>
                                </div>
                                <hr>
                                <div class="card-header">
                                    <h5>Domesticité</h5>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-4 col-md-4">
                                        <input type="checkbox" name="specific_rights[]" value="gardien" id="gardien">
                                        <label for="gardien">Gardien, jardinier</label>
                                        <div id="gardien_jardinier" style="display: none;">
                                            <input type="number" name="amount_garde" id="amount_garde" class="form-control"
                                                placeholder="Montant gardien">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4">
                                        <input type="checkbox" name="specific_rights[]" value="house_man" id="house_man">
                                        <label for="house_man">Gens de maison</label>
                                        <div id="gens_maison" style="display: none;">
                                            <input type="number" name="amount_maison" id="amount_maison"
                                                class="form-control" placeholder="Montant maison">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4">
                                        <input type="checkbox" name="specific_rights[]" value="cuisto" id="cuisto">
                                        <label for="cuisto">Cuisinier</label>
                                        <div id="cuisinier" style="display: none;">
                                            <input type="number" name="amount_cuisto" id="amount_cuisto"
                                                class="form-control" placeholder="Montant cuisinier">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="divFractionExoneree" style="display: none;" class="mb-4">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="taxe_its" class="form-label">Fraction exonérée FISC</label>
                                        <input type="number" name="taxe_its" id="taxe_its" class="form-control"
                                            placeholder="Montant FISC">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="taxe_cnps" class="form-label">Fraction exonérée CNPS</label>
                                        <input type="number" name="taxe_cnps" id="taxe_cnps" class="form-control"
                                            placeholder="Montant CNPS">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between mt-4">
                                <button type="reset" class="btn btn-outline-secondary">Annuler</button>
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
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

            // Initialisation de Select2
            $('.select2').each(function () {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Sélectionner une option',
                    dropdownParent: $this.parent()
                });
            });
        });

        $(document).ready(function () {
            $('#type_avantage').change(function () {
                if ($(this).val() == 'avantage_en_nature') { // Si avantage en nature est sélectionné
                    $('#avantage_nature').show(); // Affiche le div pour les avantages en nature
                    $('#avantage_argent').hide();
                } else {
                    $('#avantage_nature').hide(); // Cache le div pour les avantages en nature
                    $('#avantage_argent').show();
                    $('#divFractionExoneree').show();
                }
            });

            $('#nombre_pieces').change(function () {
                // Récupérer la valeur sélectionnée
                var selectedValue = $(this).val();

                // Sélectionnez le champ de montant approprié en fonction de la valeur sélectionnée
                switch (selectedValue) {
                    case '1':
                        $('#montant_logement').val('60000');
                        $('#montant_mobilier').val('10000');
                        $('#electric').val('10000');
                        $('#montant_eau').val('10000');
                        $('#description').val('Logement et Accessoires')
                        break;
                    case '2':
                        $('#montant_logement').val('80000');
                        $('#montant_mobilier').val('20000');
                        $('#electric').val('20000');
                        $('#montant_eau').val('15000');
                        $('#description').val('Logement et Accessoires')
                        break;
                    case '3':
                        $('#montant_logement').val('160000');
                        $('#montant_mobilier').val('40000');
                        $('#electric').val('30000');
                        $('#montant_eau').val('20000');
                        $('#description').val('Logement et Accessoires')
                        break;
                    case '4':
                        $('#montant_logement').val('300000');
                        $('#montant_mobilier').val('60000');
                        $('#electric').val('40000');
                        $('#montant_eau').val('30000');
                        $('#description').val('Logement et Accessoires')
                        break;
                    case '5':
                        $('#montant_logement').val('480000');
                        $('#montant_mobilier').val('80000');
                        $('#electric').val('50000');
                        $('#montant_eau').val('40000');
                        $('#description').val('Logement et Accessoires')
                        break;
                    case '6':
                        $('#montant_logement').val('600000');
                        $('#montant_mobilier').val('100000');
                        $('#electric').val('60000');
                        $('#montant_eau').val('50000');
                        $('#description').val('Logement et Accessoires')
                        break;
                    case '7 et plus':
                        $('#montant_logement').val('800000');
                        $('#montant_mobilier').val('150000');
                        $('#electric').val('70000');
                        $('#montant_eau').val('60000');
                        $('#description').val('Logement et Accessoires')
                        break;
                }
            });

            $('#gardien').change(function () {
                if (this.checked) {
                    $('#gardien_jardinier').show();
                    $('#amount_garde').val('50000');
                } else {
                    $('#gardien_jardinier').hide();
                }
            });

            $('#house_man').change(function () {
                if (this.checked) {
                    $('#gens_maison').show();
                    $('#amount_maison').val('60000');
                } else {
                    $('#gens_maison').hide();
                }
            });

            $('#cuisto').change(function () {
                if (this.checked) {
                    $('#cuisinier').show();
                    $('#amount_cuisto').val('90000');
                } else {
                    $('#cuisinier').hide();
                }
            });
        });
        document.getElementById('avtg_nature').addEventListener('change', function () {
            var autreAvantageDiv = document.getElementById('autre_avantage');
            var montantAvantageInput = document.getElementById('montant_avantage');

            if (this.value === 'autre') {
                autreAvantageDiv.style.display = 'block';
                montantAvantageInput.value = ''; // Réinitialiser la valeur du montant si l'utilisateur choisit "Autre"
            } else {
                autreAvantageDiv.style.display = 'none';
            }
        });

        function showDiv() {
            var montant = document.getElementById('montant_avantage').value;
            if (montant.trim() !== '') {
                document.getElementById('divFractionExoneree').style.display = 'block';
            } else {
                document.getElementById('divFractionExoneree').style.display = 'none';
            }
        }
    </script>
@endpush