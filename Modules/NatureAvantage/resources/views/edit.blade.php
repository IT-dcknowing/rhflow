@extends('layouts.app')

@section('title', 'Modifier une nature d\'avantage')

@push('styles')
    <link rel="stylesheet" href="{{asset('vendor/libs/select2/select2.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/libs/formvalidation/dist/css/formValidation.min.css')}}">
@endpush

@push('scripts')
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
                        <h4 class="mb-1">
                            ➕ Modifier un avantage en nature
                        </h4>
                        <p class="text-muted mb-0">Gérez les avantages en natures des employés</p>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                                </li>
                                <li class="breadcrumb-item active">Gestion avantages en natures</li>
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
                        <a href="{{ route('company.avantages.show', $avantage->id) }}" class="btn btn-label-info me-2">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Modifier la nature d'avantage</h5>
                    </div>
                    <div class="card-body">
                        <form id="avantageForm" class="row g-3" action="{{ route('company.avantages.update', $avantage->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="form-label" for="employee_id">Employé <span class="text-danger">*</span></label>
                                <select id="employee_id" name="employee_id" class="form-select select2" data-allow-clear="true" required>
                                    <option value="">Sélectionner un employé</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $employee->id == $avantage->employee_id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                <span class="invalid-employee_id" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <input type="hidden" name="id_avantage" id="id_avantage" class="form-control" value="{{$avantage->id}}">
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="form-label" for="type_avantage">Type d\'avantage <span class="text-danger">*</span></label>
                                <select id="type_avantage" name="type_avantage" class="form-select select2" required >
                                    <option value="">Sélectionnez un avantage</option>
                                    <option value="avantage_en_nature" {{ $avantage->type_avantage == 'avantage_en_nature' ? 'selected' : '' }}>Avantage en nature</option>
                                    <option value="avantage_en_argent" {{ $avantage->type_avantage == 'avantage_en_argent' ? 'selected' : '' }}>Avantage en argent</option>
                                    <option value="assurance_vie_complementaire" {{ $avantage->type_avantage == 'assurance_vie_complementaire' ? 'selected' : '' }}>Assurance-vie complémentaire</option>
                                    <option value="assurance_sante" {{ $avantage->type_avantage == 'assurance_sante' ? 'selected' : '' }}>Assurance santé</option>
                                </select>
                                @error('type_avantage')
                                <span class="invalid-type_avantage" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div> 
                            <div id="avantage_argent" style="display: none;">
                                <div class="form-group  col-lg-12">
                                    <label class="form-label" for="description">Libellé de l'avantage <span class="text-danger">*</span></label>
                                    <input type="text" id="description" name="description" class="form-control" placeholder="Nom de l'avantage en argent" value="{{$avantage->libelle}}" />
                                    @error('description')
                                    <span class="invalid-description" role="alert">
                                                <strong class="text-danger">{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="form-label" for="traitement">Traitement <span class="text-danger">*</span></label>
                                <select id="traitement" name="traitement" class="form-control select2" data-allow-clear="true" required>
                                    <option value="">Sélectionner un traitement</option>
                                        <option value="mensuel" {{ $avantage->traitement == 'mensuel' ? 'selected' : '' }}>Mensuel </option>
                                        <option value="annuel" {{ $avantage->traitement == 'annuel' ? 'selected' : '' }}>Annuel </option>
                                </select>
                                @error('traitement')
                                <span class="invalid-traitement_id" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>  
                            <div class="form-group col-lg-6 col-md-6">
                                <label class="form-label" for="amount_real">Montant réel <span class="text-danger">*</span></label>
                                <input type="number" id="amount_real" name="amount_real" class="form-control" placeholder="Montant réel" value="{{$avantage->montant_reel}}" />
                                @error('amount_real')
                                <span class="invalid-amount_real" role="alert">
                                            <strong class="text-danger">{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div id="avantage_nature" style="display: none;" class="mb-4">
                                <hr>
                                <div class="card-header">
                                    <h5>Logement et Accessoires</h5>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-12 col-md-12 mb-3">
                                        <label class="form-label" for="nombre_pieces">Nombre de pièces <span class="text-danger">*</span></label>
                                        <select id="nombre_pieces" name="nombre_pieces" class="form-select select2" placeholder ="Nombre de pièces" required >
                                            <option >Sélectionnez</option>
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
                                        <label class="form-label" for="montant_logement">Montant  pour le logement <span class="text-danger">*</span></label>
                                        <input type="number" id="montant_logement" name="montant_logement" class="form-control" placeholder="Montant logement" value="{{$avantage->montant_logement}}" />
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="montant_mobilier">Montant  pour le mobilier <span class="text-danger">*</span></label>
                                        <input type="number" id="montant_mobilier" name="montant_mobilier" class="form-control" placeholder="Montant mobilier" value="{{$avantage->montant_mobilier}}" />
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="electric">Montant  pour l'électricité <span class="text-danger">*</span></label>
                                        <input type="number" id="electric" name="electric" class="form-control" placeholder="Montant électricité" value="{{$avantage->montant_electricite}}" />
                                    </div>
                                    <div class="form-group col-lg-3 col-md-3 mb-3">
                                        <label class="form-label" for="montant_eau">Montant  pour l'eau <span class="text-danger">*</span></label>
                                        <input type="number" id="montant_eau" name="montant_eau" class="form-control" placeholder="Montant eau" value="{{$avantage->montant_eau}}" />
                                    </div>
                                </div>
                                <hr>
                                <div class="card-header">
                                    <h5>Domesticité</h5>
                                </div>
                                <div class="row">
                                    <div class="form-group col-lg-4 col-md-4">
                                        <input type="checkbox" name="specific_rights[]" value="gardien" id="gardien">
                                        <label for="jardinier">Gardien, jardinier</label>
                                        <div id="gardien_jardinier" style="display: none;">
                                            <input type="number" name="amount_garde" id="amount_garde" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4">
                                        <input type="checkbox" name="specific_rights[]" value="house_man" id="house_man">
                                        <label for="its">Gens de maison</label>
                                        <div id="gens_maison" style="display: none;">
                                            <input type="number" name="amount_maison" id="amount_maison" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4">
                                        <input type="checkbox" name="specific_rights[]" value="cuisto" id="cuisto">
                                        <label for="licenciement">Cuisinier</label>
                                        <div id="cuisinier" style="display: none;">
                                            <input type="number" name="amount_cuisto" id="amount_cuisto" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-between mt-4">
                                <a href="{{ route('company.avantages.show', $avantage->id) }}" class="btn btn-label-danger">Annuler</a>
                                <button type="submit" class="btn btn-primary me-2">Mettre à jour</button>
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

     $(document).ready(function(){
        $('#type_avantage').change(function(){
            if($(this).val() == 'avantage_en_nature'){ // Si avantage en nature est sélectionné
                $('#avantage_nature').show(); // Affiche le div pour les avantages en nature
                $('#avantage_argent').hide();
            } else {
                $('#avantage_nature').hide(); // Cache le div pour les avantages en nature
                $('#avantage_argent').show();
            }
        });

        $('#nombre_pieces').change(function(){
            // Récupérer la valeur sélectionnée
            var selectedValue = $(this).val();
            
            // Sélectionnez le champ de montant approprié en fonction de la valeur sélectionnée
            switch(selectedValue){
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

        $('#gardien').change(function() {
            if (this.checked) {
                $('#gardien_jardinier').show();
                $('#amount_garde').val('50000'); 
            } else {
                $('#gardien_jardinier').hide();
            }
        });

        $('#house_man').change(function() {
            if (this.checked) {
                $('#gens_maison').show();
                $('#amount_maison').val('60000');
            } else {
                $('#gens_maison').hide();
            }
        });

        $('#cuisto').change(function() {
            if (this.checked) {
                $('#cuisinier').show();
                $('#amount_cuisto').val('90000'); 
            } else {
                $('#cuisinier').hide();
            } 
        });
    });  
    
    $(document).ready(function() {
        function callback() {
            var autreAvantageDiv = document.getElementById('autre_avantage');
            var montantAvantageInput = document.getElementById('montant_avantage');

            if (this.value === 'autre') {
                autreAvantageDiv.style.display = 'block';
                montantAvantageInput.value = ''; // Réinitialiser la valeur du montant si l'utilisateur choisit "Autre"
            } else {
                autreAvantageDiv.style.display = 'none';
            }
        }

        $(document).on("change", "#avtg_nature", function() {
            callback();
        });
    });
</script>
@endpush