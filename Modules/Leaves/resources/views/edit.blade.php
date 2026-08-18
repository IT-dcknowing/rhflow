@extends('layouts.app')

@section('title', 'Modifier une demande de congé')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/fullcalendar/main.min.css') }}">
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 5px 10px;
        border: 1px solid #d9dee3;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .leave-summary {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .leave-summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .leave-summary-item strong {
        color: #566a7f;
    }
    #leave-calendar {
        min-height: 300px;
    }
    .status-badge {
        font-size: 0.9em;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-edit me-2"></i>Modifier la demande de congé
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.leaves.index') }}?periode_id={{$leave->periode_id}}">Gestion des congés</a>
                            </li>
                            <li class="breadcrumb-item active">Modifier la demande #{{ $leave->id }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('company.leaves.index') }}?periode_id={{$leave->periode_id}}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
    <form id="leaveForm" action="{{ route('company.leaves.update', $leave->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier la demande de congé</h5>
                        <span class="badge bg-{{ 
                            $leave->status == 'Pending' ? 'warning' : 
                            ($leave->status == 'Approuvé' ? 'success' : 
                            ($leave->status == 'Rejeté' ? 'danger' : 
                            ($leave->status == 'Terminé' ? 'secondary' : 'warning'))) 
                        }} status-badge">
                            {{ ucfirst($leave->status) }}
                        </span>
                    </div>
                    <div class="card-body">

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label">Employé <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" {{ $leave->status != 'Pending' ? 'disabled' : '' }}>
                                    <option value="">Sélectionner un employé</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $leave->employee_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ \Auth::user()->employeeIdFormat($employee->employee_id) ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="leave_type_id" class="form-label">Type de congé <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('leave_type_id') is-invalid @enderror" id="leave_type_id" name="leave_type_id" {{ $leave->status != 'Pending' ? 'disabled' : '' }}>
                                    <option value="">Sélectionner un type de congé</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" 
                                            data-days="{{ $type->days_allowed }}"
                                            {{ old('leave_type_id', $leave->leave_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('leave_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="back_date" class="form-label">Date retour dernier congé <span id="back_date_span"></span></label>
                                    <div class="input-group">
                                        <input type="date" class="form-control @error('back_date') is-invalid @enderror" 
                                        id="back_date" name="back_date" 
                                            value="{{ old('back_date', $leave->leave_back) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                        id="start_date" name="start_date" 
                                        value="{{ old('start_date', $leave->start_date) }}" 
                                        required {{ $leave->status != 'Pending' ? 'readonly' : '' }}>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                        id="end_date" name="end_date" 
                                        value="{{ old('end_date', $leave->end_date) }}" 
                                        required {{ $leave->status != 'Pending' ? 'readonly' : '' }}>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                @if($leave->status == 'Pending')
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="Pending" {{ old('status', $leave->status) == 'Pending' ? 'selected' : '' }}>En attente</option>
                                            <option value="Apprové" {{ old('status', $leave->status) == 'Apprové' ? 'selected' : '' }}>Approuvé</option>
                                            <option value="Rejeté" {{ old('status', $leave->status) == 'Rejeté' ? 'selected' : '' }}>Rejeté</option>
                                            <option value="Terminé" {{ old('status', $leave->status) == 'Terminé' ? 'selected' : '' }}>Terminé</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Résumé du congé</h5>
                    </div>
                    <div class="card-body">
                        <div class="leave-summary">
                            <div class="leave-summary-item">
                                <span>Type de congé:</span>
                                <strong id="summary-type">{{ $leave->leaveType->name ?? '-' }}</strong>
                            </div>
                            <div class="leave-summary-item">
                                <span>Période:</span>
                                <strong id="summary-period">
                                    {{ $leave->start_date }} - {{ $leave->end_date }}
                                </strong>
                            </div>
                            <div class="leave-summary-item">
                                <span>Durée:</span>
                                <strong id="summary-duration">{{ $leave->total_leave_days }} jour{{ $leave->total_leave_days > 1 ? 's' : '' }}</strong>
                            </div>
                            <div class="leave-summary-item">
                                <span>Statut:</span>
                                <span class="badge bg-{{ 
                                    $leave->status == 'Apprové' ? 'success' : 
                                    ($leave->status == 'Rejeté' ? 'danger' : 
                                    ($leave->status == 'Terminé' ? 'secondary' : 'warning')) 
                                }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                            @if($leave->approver)
                            <div class="leave-summary-item">
                                <span>Traîté par:</span>
                                <strong>{{ $leave->approver->name }}</strong>
                            </div>
                            @endif
                        </div>
                        
                        @if($leave->status == 'pending')
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small>
                                Les modifications sont limitées pour les demandes en attente. Une fois approuvées ou rejetées, les demandes ne peuvent plus être modifiées.
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
              
            @if($leave->leave_type_id == 1)
                <!-- Champs supplémentaires pour congé annuel -->
                <div class="col-md-12">
                    <div id="annual_leave_details" class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations complémentaires</h5>
                            <small class="text-muted float-end">Motif et options</small>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="form-group col-md-12 mb-4">
                                    <label for="categorySelect">Jours supplémentaires :</label>
                                    <select id="categorySelect" class="form-select" onchange="showNumberField()">
                                        <option value="">-- Choisissez une option --</option>
                                        <option value="anciennete">Ancienneté</option>
                                        <option value="femme_apprentie">Femme apprentie ou salarié</option>
                                        <option value="medaille_honneur">Médaillé d'honneur</option>
                                    </select>
                                </div>
                                <div id="numberFieldContainer" class="row" style="display: none;">
                                    <div class="col-md-12">
                                        <div class="alert alert-info mb-4">
                                            Art. 25.2. <br>
                                            – Sauf disposition plus favorable des conventions collectives ou du contrat individuel, la durée annuelle du congé défini à l'article précédent est augmentée de :<br>
                                            — 1 jour ouvrable supplémentaire après 5 ans d'ancienneté dans l'entreprise ;<br>
                                            — 2 jours ouvrables supplémentaires après 10 ans ;<br>
                                            — 3 jours ouvrables supplémentaires après 15 ans ;<br>
                                            — 5 jours ouvrables supplémentaires après 20 ans ;<br>
                                            — 7 jours ouvrables supplémentaires après 25 ans ;<br>
                                            — 8 jours ouvrables supplémentaires après 30 ans.<br>
                                            La femme salariée ou apprentie bénéficie d'un congé supplémentaire payé sur les bases suivantes :<br>
                                            — 2 jours ouvrables de congé supplémentaires par enfant à charge si elle a moins de 21 ans au dernier jour de la période de référence ;<br>
                                            — 2 jours ouvrables de congé supplémentaires par enfant à charge à compter du 4ème si elle a plus de 21 ans au dernier jour de la période de référence.<br>
                                            Le travailleur titulaire de la médaille d'honneur du travail bénéficie d'un jour ouvrable de congé supplémentaire par an en sus du congé légal.<br>
                                            Le travailleur logé dans l'établissement dont il a la garde et astreint à une durée de présence de 24 heures continues par jour, a droit à un congé annuel payé de 2 semaines par an en sus du congé légal et bénéficie des dispositions de l'alinéa 2 du présent article.<br>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <h4> Ancienneté :  <span id="date_pa"></span> an(s) </h4>
                                        <label for="numberInput">Veuillez entrer une valeur :</label>
                                        <input type="number" id="numberInput" class="form-control" value="0" placeholder="Entrez une valeur" oninput="updateTotalBrut()">
                                    </div>
                                </div>
                            </div>
                        
                            <div class="row mb-4">
                                <div class="form-group mb-4">
                                    <p>Durée du congé (jours ouvrables) :
                                    <strong align="right"><span id="days_net_span" style="color:brown">0</span></strong></p>
                                    <input type="hidden" id="dure_days" name="dure_days" value="">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="periode_reference">Période de référence</label>
                                        <input type="text" class="form-control" id="periode_reference" name="periode_reference" readonly>
                                        <input type="hidden" class="form-control" id="nbre_parts" readonly>
                                        <input type="hidden" class="form-control" id="type_emp" readonly>
                                        <input type="hidden" id="nbre_jours" name="nbre_jours" value="">
                                        <input type="hidden" id="employee_company_doj" name="employee_company_doj" value="">
                                        <input type="hidden" id="nbre_parts2" name="nbre_parts2">
                                        <input type="hidden" id="employee_salary" name="employee_salary" value="">
                                    </div>
                                    <div class="form-group">  
                                        <p>Salaire moyen mensuel :</p>
                                        <p><strong>Allocation congé brute :</strong></p>
                                        <p>Impôt brut :</p>
                                        <p>RICF :</p>
                                        <p>Impôt net :</p>
                                        <p>CNPS :</p>
                                        <p>Total retenues :</p>
                                        <hr>
                                        <p><strong>Allocation congé nette :</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nb_jours_conge">Durée du congé (jrs calendaires)</label>
                                        <input type="text" class="form-control" id="nb_jours_conge" name="nb_jours_conge" readonly>
                                    </div>
                                    <div class="form-group">
                                        <p align="right"><span id="allo_smm" style="color:black">0</span></p>
                                        <input type="hidden" name="allo_smm_amount" id="allo_smm_amount">
                                        <p align="right"><strong><span id="allo_conge_brut_span" style="color:brown">0</span></strong></p>
                                        <input type="hidden" name="allo_conge_brut" id="allo_conge_brut">
                                        <p align="right"><span id="allo_impot_brut" style="color:black">0</span></p>
                                        <p align="right"><span id="allo_ricf_brut" style="color:black">0</span></p>
                                        <p align="right"><span id="allo_impot_net" style="color:black">0</span></p>
                                        <p align="right"><span id="allo_cnps" style="color:black">0</span></p>
                                        <p align="right"><span id="allo_total_retenue" style="color:black">0</span></p>
                                        <hr>
                                        <p align="right"><strong><span id="allo_conge_net_span" style="color:brown">0</span></strong></p>
                                        <input type="hidden" name="allo_conge_net" id="allo_conge_net">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <table id="mois_table" class="table">
                                        <thead>
                                            <tr>
                                                <th>SMM</th>
                                                <th>SB CONGE</th>
                                                <th>JRS PAYES</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mois_body">

                                        </tbody>
                                        <tr>
                                            <td colspan="2" align="center">
                                                <p>Total des salaires bruts : <strong><span id="total_brut">0</span></strong></p>
                                                <input type="hidden" name="total_salary_brut" id="total_salary_brut">
                                                <input type="hidden" name="cpte_salary_brut" id="cpte_salary_brut">
                                            </td>
                                            <td>
                                                <p>Total des jours : <strong><span id="total_jours_payés">0</span></strong></p>
                                                <input type="hidden" name="nb_jours_payés" id="nb_jours_payés">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($leave->status == 'Pending')
            <div class="col-md-12">
                <div class="card card-body mb-3">
                    <label for="remark" class="form-label">Commentaire (optionnel)</label>
                    <textarea class="form-control @error('remark') is-invalid @enderror" 
                                id="remark" name="remark" 
                                rows="2" 
                                placeholder="Ajouter un commentaire...">{{ old('remark', $leave->remark) }}</textarea>
                    @error('remark')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @elseif($leave->status != 'Pending')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Cette demande ne peut plus être modifiée car son statut est "{{ ucfirst($leave->status) }}".
                    @if($leave->remark)
                        <div class="mt-2">
                            <strong>Commentaire :</strong>
                            <p class="mb-0">{{ $leave->remark }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="col-md-12">
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('company.leaves.index') }}?periode_id={{$leave->periode_id}}" class="btn btn-outline-danger">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    @if($leave->status == 'Pending')
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Mettre à jour
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialisation de Select2
        $('.select2').select2({
            placeholder: 'Sélectionner une option',
            allowClear: true,
            width: '100%',
            {{ $leave->status != 'Pending' ? 'disabled: true' : '' }}
        });

        // Mise à jour du résumé
        function updateSummary() {
            const leaveType = $('#leave_type_id option:selected').text();
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            
            if (startDate && endDate) {
                const start = moment(startDate);
                const end = moment(endDate);
                const duration = end.diff(start, 'days') + 1;
                
                $('#summary-type').text(leaveType || '-');
                $('#summary-period').text(`${start.format('DD/MM/YYYY')} - ${end.format('DD/MM/YYYY')}`);
                $('#summary-duration').text(`${duration} jour${duration > 1 ? 's' : ''}`);
            }
        }

        // Écouteurs d'événements pour la mise à jour du résumé si le statut est 'pending'
        @if($leave->status == 'pending')
            $('#leave_type_id, #start_date, #end_date').on('change', updateSummary);
        @endif

        // Validation du formulaire
        $('#leaveForm').on('submit', function(e) {
            const startDate = moment($('#start_date').val());
            const endDate = moment($('#end_date').val());
            
            if (endDate.isBefore(startDate)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'La date de fin doit être postérieure ou égale à la date de début.',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            return true;
        });
    });

    function showNumberField() {
        var selectBox = document.getElementById("categorySelect");
        var numberFieldContainer = document.getElementById("numberFieldContainer");

        if (selectBox.value) {
            // Afficher le champ si une option est sélectionnée
            numberFieldContainer.style.display = "block";
        } else {
            // Masquer le champ si aucune option n'est sélectionnée
            numberFieldContainer.style.display = "none";
        }
    }
</script>

<script>
    $('.modal-body').on('shown.bs.modal', function () {
        calculateDateDifference();
        fillMonths();
    });

    $(document).ready(function() {
        setTimeout(() => {
            var employee_id = $('#employee_id').val();
            if (employee_id) {
                $('#employee_id').trigger('change');
            }
        }, 100);
    });

    $('#leave_type_id').on('change', function() {
        var leavetype = $(this).val();
        var nbreJours  = $('#leave_type_id option:selected').data('daysval');

        $('#nbre_jours').val(nbreJours);
    }); 

    $(document).ready(function() {
        // Fonction pour obtenir la date de retour du dernier congé de l'employé sélectionné
        function leave_end() {
            var employeeId = $('#employee_id').val();
            var resultricf = 0;
            // Effectuer une requête AJAX pour obtenir les données nécessaires
            $.ajax({
                url: "{{ route('company.leaves.get_employee_leave_date') }}",
                type: "GET",
                data: {
                    employee_id: employeeId,
                },
                success: function(response) {
                    // Mettre à jour les champs avec les données récupérées
                    if(response.type_emp == 'local'){
                        $('#type_emp').val('Local');
                    }else{
                        $('#type_emp').val('Expatrié');
                    }

                    $('#nbre_parts').val(response.nb_parts);
                    $('#back_date').val(response.leave_date);
                    $('#employee_company_doj').val(response.company_doj);
                    $('#employee_salary').val(response.leave_salary);

                    if(response.nb_parts==1){
                        resultricf = 0;
                    }else if(response.nb_parts=="1,5"){
                        resultricf = 5500;
                    }else if(response.nb_parts=="2"){
                        resultricf = 11000;
                    }else if(response.nb_parts=="2,5"){
                        resultricf = 16500;
                    }else if(response.nb_parts=="3"){
                        resultricf = 22000;
                    }else if(response.nb_parts=="3,5"){
                        resultricf = 27500;
                    }else if(response.nb_parts=="4"){
                        resultricf = 33000;
                    }else if(response.nb_parts=="4,5"){
                        resultricf = 38500;
                    }else if(response.nb_parts=="5"){
                        resultricf = 44000;
                    }
                    $('#nbre_parts2').val(resultricf);

                    // Gestion des dates
                    const leaveBackDate = new Date(response.leave_date);
                    const startDate = new Date($('#start_date').val());
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

         // Attacher la fonction leave_end à l'événement de changement de sélection de l'employé
        $(document).on('change', '#employee_id', function() {
            leave_end();
        });
    });

    $(document).ready(function() {
        var now = new Date();
        var month = (now.getMonth() + 1);
        var day = now.getDate();
        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;
        var today = now.getFullYear() + '-' + month + '-' + day;
        $('.current_date').val(today);
        calculateDateDifference();
        fillMonths();
        if(document.getElementById('leave_type_id').value == 1){
            document.getElementById('annual_leave_details').style.display = 'block';
        }
    });

    document.getElementById('leave_type_id').addEventListener('change', function() {
        var leaveType = this.value;
        var annualLeaveDetails = document.getElementById('annual_leave_details');

        if (leaveType == '1') {  // Remplacez 'conge_annuel' par la valeur réelle pour congé annuel
            annualLeaveDetails.style.display = 'block';
            document.getElementById('autre_congé').style.display = 'none';
        } else {
            annualLeaveDetails.style.display = 'none';
            document.getElementById('autre_congé').style.display = 'block';
        }
    })

    function calculateDateDifference() {
        var backDate = document.getElementById('back_date').value;
        var startDate = document.getElementById('start_date').value;

        if (backDate && startDate) {
            var backDateObj = new Date(backDate);
            var startDateObj = new Date(startDate);

            // Calculer la différence en millisecondes
            var diffTime = Math.abs(startDateObj - backDateObj);

            // Convertir la différence en jours
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            // Afficher la différence dans le champ "periode_reference"
            document.getElementById('periode_reference').value = diffDays;
            document.getElementById('nb_jours_conge').value = Math.round((diffDays * 2.2 * 1.25)/30);

            var nbreJours = parseInt($('#nb_jours_conge').val());

            // Ajouter le nombre de jours spécifié à la date de début
            var endDate = new Date(startDate);
            endDate.setDate(endDate.getDate() + nbreJours);

            // Formatage de la date de fin pour l'afficher dans le champ de formulaire
            var formattedEndDate = endDate.getFullYear() + '-' + ('0' + (endDate.getMonth() + 1)).slice(-2) + '-' + ('0' + endDate.getDate()).slice(-2);

            // Mettre à jour la valeur du champ de formulaire 'end_date' avec la date de fin calculée
            $('#end_date').val(formattedEndDate);
        } else {
            // Si une des dates est manquante, on efface la période de référence
            document.getElementById('periode_reference').value = '';
            document.getElementById('nb_jours_conge').value = '';
        }
    }

    function fillMonths() {
        var backDate = document.getElementById('back_date').value;
        var startDate = document.getElementById('start_date').value;
        var employeeId = $('#employee_id').val();
        var leaveId = '{{ $leave->id }}'; // Get the current leave ID

        // AJAX request to get necessary data including existing salary data for this leave
        $.ajax({
            url: "{{ route('company.leaves.get_employee_leave_sb') }}",
            type: "GET",
            data: {
                employee_id: employeeId,
                leave_id: leaveId // Pass the leave ID to get existing data
            },
            success: function(response) {
                if (backDate && startDate) {
                    var backDateObj = new Date(backDate);
                    var startDateObj = new Date(startDate);
                    var cpte = 0;

                    var tableBody = document.getElementById('mois_body');
                    tableBody.innerHTML = '';

                    var netPayables = response.netPayables;
                    var netMonth = response.netMonth;
                    var existingSalaries = response.existing_salaries || {};

                    while (backDateObj <= startDateObj) {
                        var monthYear = backDateObj.toLocaleString('fr-FR', { month: 'short', year: 'numeric' });
                        var formattedMonth = backDateObj.toISOString().slice(0, 7);
                        cpte++;

                        var newRow = document.createElement('tr');
                        var smmCell = document.createElement('td');
                        var mlCell = document.createElement('td');
                        var sbCell = document.createElement('td');
                        var nbCell = document.createElement('td');
                        smmCell.textContent = monthYear;

                        var input = document.createElement('input');
                        input.type = 'number';
                        input.name = 'salary_brut['+ cpte + ']';
                        input.className = 'form-control salaire-brut';

                        var inputJours = document.createElement('input');
                        inputJours.type = 'number';
                        inputJours.name = 'nbre_jour['+ cpte + ']';
                        inputJours.className = 'form-control total-jours';

                        var inputMonth = document.createElement('input');
                        inputMonth.type = 'hidden';
                        inputMonth.name = 'month_leave['+ cpte +']'; // Ajouter un nom pour l'input
                        inputMonth.className = 'form-control month-leave';

                        if (existingSalaries[formattedMonth]) {
                            input.value = existingSalaries[formattedMonth];
                            input.dataset.salaireBrut = existingSalaries[formattedMonth];
                        } else {
                            var salaryFound = false;
                            $.each(netPayables, function(i, brut) {
                                if (brut.salary_month == formattedMonth) {
                                    inputMonth.value = brut.salary_month; // Placer le mois dans l'input
                                    input.value = brut.salary_brut - (parseInt(brut.nbre_jour)*1000);
                                    input.dataset.salaireBrut = brut.salary_brut - (parseInt(brut.nbre_jour)*1000);
                                    inputJours.value = brut.nbre_jour;
                                    inputJours.dataset.totalJours = brut.nbre_jour;
                                    salaryFound = true;
                                    return false;
                                }
                            });

                            if (!salaryFound) {
                                inputMonth.value = 'N/A';
                                input.value = 0;
                                input.dataset.salaireBrut = 0;
                                inputJours.value = 0;
                                inputJours.dataset.totalJours = 0;
                            }
                        }

                        input.oninput = updateTotalBrut;
                        inputJours.oninput = updateTotalBrut;

                        sbCell.appendChild(input);
                        nbCell.appendChild(inputJours);
                        mlCell.appendChild(inputMonth).hidden = true; // Hide the month input

                        newRow.appendChild(mlCell).hidden = true; // Hide the month input;
                        newRow.appendChild(smmCell);
                        newRow.appendChild(sbCell);
                        newRow.appendChild(nbCell);
                        tableBody.appendChild(newRow);

                        backDateObj.setMonth(backDateObj.getMonth() + 1);
                    }

                    document.getElementById('cpte_salary_brut').value = cpte;
                    updateTotalBrut();
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function updateTotalBrut() {
        var totalBrut = 0; var totalJours = 0; var nbrejour = 0;
        var periode_reference = parseInt(document.getElementById('periode_reference').value);
        var nombre_jours = parseInt(document.getElementById('nb_jours_conge').value);
        var smm_amount = document.getElementById('allo_smm_amount');
        var nbr_salary = document.getElementById('cpte_salary_brut').value;
        //var days_sup = parseInt(document.getElementById('numberInput').value);
        //nombre de part en FCFA
        nbrejour = parseInt(document.getElementById('nbre_parts2').value);
        var resultricf = 0;
        var dayold = 0;

        document.querySelectorAll('.salaire-brut').forEach(function(cell) {
            var value = 0;

            if (cell.tagName === 'TD') {
                value = parseFloat(cell.dataset.salaireBrut) || 0;
            } else if (cell.tagName === 'INPUT') {
                value = parseFloat(cell.value) || 0;
            }

            totalBrut += value;
        });

        document.querySelectorAll('.total-jours').forEach(function(cell) {
            var value = 0;

            if (cell.tagName === 'TD') {
                value = parseFloat(cell.dataset.totalJours) || 0;
            } else if (cell.tagName === 'INPUT') {
                value = parseFloat(cell.value) || 0;
            }

            totalJours += value;
        });

        // Afficher la somme totale des salaires bruts
        document.getElementById('nb_jours_payés').value = totalJours;
        var nombre_jours2 = parseInt(Math.round((totalJours/30) * 2.2));
        var nombre_jours3 = parseInt(Math.round((totalJours/30) * 2.2 * 1.25));
        document.getElementById('total_brut').innerHTML = totalBrut.toLocaleString();
        document.getElementById('total_salary_brut').value = totalBrut;
        document.getElementById('allo_smm').innerHTML = Math.round((totalBrut/totalJours)*30).toLocaleString();
        smm_amount.value = Math.round((totalBrut/totalJours)*30);
        document.getElementById('allo_conge_brut_span').innerHTML = Math.round((smm_amount.value/30)*nombre_jours).toLocaleString();
        document.getElementById('allo_conge_brut').value = Math.round((smm_amount.value/30)*nombre_jours);
        document.getElementById('total_jours_payés').innerHTML = totalJours;
        //document.getElementById('days_net_span').innerHTML = Math.round((totalJours/30) * 2.2);
        //document.getElementById('dure_days').value = Math.round((totalJours/30) * 2.2);
        console.log(nbrejour.value);
        var brut = parseInt(Math.round((smm_amount.value/30)*nombre_jours));
        var resultimpricf = 0;
        var resultcnps = (brut*6.3)/100;

        if(brut >= 0 && brut <= 75000){
            var tot = (brut*0)/100;
            resultimpricf = Math.round(tot);
        }else if(brut > 75000 && brut <= 240000){
            var mt1 = brut-75000;
            var tot1 = (((75000*0)/100)+((mt1*16)/100));
            resultimpricf = Math.round(tot1);
        }else if(brut > 240000 && brut <= 800000){
            var mt2 = brut-240000;
            var tot2 = (((75000*0)/100)+((165000*16)/100)+((mt2*21)/100));
            resultimpricf = Math.round(tot2);
        }else if(brut > 800000 && brut <= 2400000){
            var mt3 = brut-800000;
            var tot3 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((mt3*24)/100));
            resultimpricf = Math.round(tot3);
        }else if(brut > 2400000 && brut <= 8000000){
            var mt4 = brut-2400000;
            var tot4 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+((mt4*28)/100));
            resultimpricf = Math.round(tot4);
        }else if(brut > 8000000){
            var mt5 = brut-8000000;
            var tot5 = (((75000*0)/100)+((165000*16)/100)+((560000*21)/100)+((1600000*24)/100)+((5600000*28)/100)+((mt5*32)/100));
            resultimpricf = Math.round(tot5);
        }
        var ricf = document.getElementById('nbre_parts2').value;
        var impots = 0;
        var retenue = (resultimpricf) - (ricf);

        if(retenue>0){
                impots = retenue + resultcnps;
        }else{
                impots = resultcnps;
                retenue = 0;
        }

        document.getElementById('allo_impot_brut').innerHTML = Math.round(resultimpricf).toLocaleString();
        document.getElementById('allo_ricf_brut').innerHTML = Math.round(ricf).toLocaleString();
        document.getElementById('allo_impot_net').innerHTML = Math.round(retenue).toLocaleString();
        document.getElementById('allo_cnps').innerHTML = Math.round(resultcnps).toLocaleString();
        document.getElementById('allo_total_retenue').innerHTML = Math.round(impots).toLocaleString();
        document.getElementById('allo_conge_net_span').innerHTML = Math.round(brut-impots).toLocaleString();
        document.getElementById('allo_conge_net').value = Math.round(brut-impots);
    }

    // Ajouter des écouteurs d'événements pour déclencher le calcul lors du changement des dates
    document.getElementById('back_date').addEventListener('change', calculateDateDifference);
    document.getElementById('back_date').addEventListener('change', fillMonths);

    document.getElementById('start_date').addEventListener('change', calculateDateDifference);
    document.getElementById('start_date').addEventListener('change', fillMonths);
</script>
@endpush