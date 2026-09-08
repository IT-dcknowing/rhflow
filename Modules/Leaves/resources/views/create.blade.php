@extends('layouts.app')

@section('title', 'Nouvelle demande de congé')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<style>
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
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-plus-circle me-2"></i>Nouvelle demande de congé
                    </h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.leaves.index') }}{{ $periode ? '?periode_id=' . $periode->id : '' }}">Gestion des congés</a>
                            </li>
                            <li class="breadcrumb-item active">Nouvelle demande</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('company.leaves.index') }}{{ $periode ? '?periode_id=' . $periode->id : '' }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <form id="leaveForm" action="{{ route('company.leaves.store') }}" method="POST">
        @csrf
        <div class="row">
            @if($periode)
                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
            @endif
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Nouvelle demande de congé</h5>
                    </div>
                    <div class="card-body">
                        @unless($periode)
                            {{-- Arrivée sans période (ex. depuis la fiche employé) : on la fait choisir ici. --}}
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="periode_id" class="form-label">Période de paie <span class="text-danger">*</span></label>
                                    <select class="form-select @error('periode_id') is-invalid @enderror" id="periode_id" name="periode_id" required>
                                        <option value="">Sélectionner une période</option>
                                        @foreach($periodes as $p)
                                            <option value="{{ $p->id }}" {{ old('periode_id') == $p->id ? 'selected' : '' }}>
                                                {{ $p->nom }}@if($p->exercice) — {{ $p->exercice->nom }}@endif ({{ ucfirst($p->statut) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('periode_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Période de rattachement du congé. Vous pourrez choisir une autre période au moment de l'activer pour la paie.</div>
                                </div>
                            </div>
                        @endunless
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label">Employé <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                    <option value="">Sélectionner un employé</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('employee_id', $selectedEmployeeId ?? null) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ \Auth::user()->employeeIdFormat($employee->employee_id) ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="form-group">
                                    <label for="part_igr" class="form-label">Part IGR</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nb_parts" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="form-group">
                                    <label for="type_emp" class="form-label">Type employé</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="type_emp" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="leave_type_id" class="form-label">Type de congé <span class="text-danger">*</span></label>
                                <select class="form-select select2 @error('leave_type_id') is-invalid @enderror" id="leave_type_id" name="leave_type_id" required>
                                    <option value="">Sélectionner un type de congé</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type->id }}" 
                                            data-days="{{ $type->days_allowed }}"
                                            {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('leave_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="hidden" id="nbre_jours" name="nbre_jours" value="">
                                <input type="hidden" id="employee_company_doj" name="employee_company_doj" value="">
                                <input type="hidden" id="employee_salary" name="employee_salary" value="">
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="back_date" class="form-label">Date retour dernier congé <span id="back_date_span"></span></label>
                                    <div class="input-group">
                                        <input type="date" class="form-control @error('back_date') is-invalid @enderror" 
                                        id="back_date" name="back_date" 
                                            value="{{ old('back_date') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                    id="start_date" name="start_date" 
                                    value="{{ old('start_date') }}"
                                    required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                    id="end_date" name="end_date" 
                                    value="{{ old('end_date') }}"
                                    required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <strong id="summary-type">-</strong>
                            </div>
                            <div class="leave-summary-item">
                                <span>Période:</span>
                                <strong id="summary-period">-</strong>
                            </div>
                            <div class="leave-summary-item">
                                <span>Durée:</span>
                                <strong id="summary-duration">-</strong>
                            </div>
                            <div class="leave-summary-item">
                                <span>Jours restants:</span>
                                <strong id="summary-remaining">-</strong>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>
                                Les demandes de congé sont soumises à approbation. Vous serez notifié une fois la demande traitée.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card mb-4" id="autre_congé" style="display:block;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informations complémentaires</h5>
                        <small class="text-muted float-end">Motif et options</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="leave_reason" class="form-label">Motif du congé</label>
                                    <textarea class="form-control @error('leave_reason') is-invalid @enderror" 
                                    id="leave_reason" name="leave_reason" 
                                    rows="3" 
                                    placeholder="Décrivez la raison de votre demande de congé...">{{ old('leave_reason') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Champs supplémentaires pour congé annuel -->
            <div class="col-md-12">
                <div id="annual_leave_details" style="display:none;" class="card mb-4">
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
                                        <input type="text" class="form-control" id="periode_reference" readonly>
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
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('company.leaves.index') }}{{ $periode ? '?periode_id=' . $periode->id : '' }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i>Soumettre la demande
                </button>
            </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('vendor/moment/moment.min.js') }}"></script>
<script src="{{ asset('vendor/fullcalendar/main.min.js') }}"></script>
<script>
    $('#leave_type_id').on('change', function() {
        var leaveType = this.value;
        var annualLeaveDetails = document.getElementById('annual_leave_details');
        const leaveTypeText = $(this).find('option:selected').text();
    
        if (leaveType == '1') {  // Remplacez 'conge_annuel' par la valeur réelle pour congé annuel
            annualLeaveDetails.style.display = 'block';
            document.getElementById('autre_congé').style.display = 'none';
        } else {
            annualLeaveDetails.style.display = 'none';
            document.getElementById('autre_congé').style.display = 'block';
        }
        $('#summary-type').text(leaveTypeText || '-');
    })

    // Initialisation de Select2
    $('.select2').select2({
        placeholder: 'Sélectionner une option',
        allowClear: true,
        width: '100%'
    });

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

    /** Désactiver les dates passées dans le sélecteur de date
    const today = new Date().toISOString().split('T')[0];
    $('#start_date, #end_date').attr('min', today);*/
    
    // Mettre à jour la date minimale de fin lorsque la date de début change
    $('#start_date').on('change', function() {
        $('#end_date').attr('min', $(this).val());
        if ($('#end_date').val() && $('#end_date').val() < $(this).val()) {
            $('#end_date').val($(this).val());
        }
    });

    function calculateDateDifference() {
        var backDate = document.getElementById('back_date').value;
        var startDate = document.getElementById('start_date').value;
        var dayold=0;
        if (backDate && startDate) {
            var backDateObj = new Date(backDate);
            var startDateObj = new Date(startDate);

            // Calculer la différence en millisecondes
            var diffTime = Math.abs(startDateObj - backDateObj);

            // Convertir la différence en jours
            var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            // La période de référence reste l'écart entre le retour du dernier congé et
            // le début du nouveau : elle alimente le calcul de l'allocation congé.
            document.getElementById('periode_reference').value = diffDays;
        } else {
            // Si une des dates est manquante, on efface la période de référence
            document.getElementById('periode_reference').value = '';
        }

        updateLeaveDuration();
    }

    // La durée du congé se déduit uniquement de la date de début et de la date de fin,
    // bornes incluses, quel que soit le type de congé.
    function updateLeaveDuration() {
        var startDate = document.getElementById('start_date').value;
        var endDate = document.getElementById('end_date').value;

        if (!startDate || !endDate) {
            document.getElementById('nb_jours_conge').value = '';
            $('#summary-period').text('-');
            $('#summary-duration').text('-');
            return 0;
        }

        var start = new Date(startDate);
        var end = new Date(endDate);

        if (end < start) {
            document.getElementById('nb_jours_conge').value = '';
            $('#summary-period').text(`${startDate} - ${endDate}`);
            $('#summary-duration').text('Date de fin antérieure à la date de début');
            return 0;
        }

        // Bornes incluses : du 19 au 21 = 3 jours.
        var duration = Math.round((end - start) / 86400000) + 1;

        document.getElementById('nb_jours_conge').value = duration;
        $('#summary-period').text(`${startDate} - ${endDate}`);
        $('#summary-duration').text(`${duration} jour${duration > 1 ? 's' : ''}`);

        // Calcul des jours restants (à implémenter avec une requête AJAX si nécessaire)
        $('#summary-remaining').text('À calculer');

        return duration;
    }

    function fillMonths() {
        var backDate = document.getElementById('back_date').value;
        var startDate = document.getElementById('start_date').value;
        var employeeId = document.getElementById('employee_id').value;

        // Effectuer une requête AJAX pour obtenir les données nécessaires
        $.ajax({
            url: "{{ route('company.leaves.get_employee_leave_sb') }}",
            type: "GET",
            data: {
                employee_id: employeeId,
            },
            success: function(response) {
                if (backDate && startDate) {
                    var backDateObj = new Date(backDate);
                    var startDateObj = new Date(startDate);
                    var cpte = 0;
                    // Effacer le contenu précédent du tableau
                    var tableBody = document.getElementById('mois_body');
                    tableBody.innerHTML = '';

                    var netPayables = response.netPayables;
                    var netMonth = response.netMonth;

                    // Boucler jusqu'à ce que backDateObj dépasse startDateObj
                    while (backDateObj <= startDateObj) {
                        var monthYear = backDateObj.toLocaleString('fr-FR', { month: 'short', year: 'numeric' });
                        var formattedMonth = backDateObj.toISOString().slice(0, 7); // Format YYYY-MM
                        cpte++;
                        // Créer une nouvelle ligne pour le tableau
                        var newRow = document.createElement('tr');
                        var smmCell = document.createElement('td');
                        var sbCell = document.createElement('td');
                        var nbCell = document.createElement('td');
                        var mlCell = document.createElement('td'); // Cellule pour le mois

                        smmCell.innerHTML = monthYear;

                        var salaireBrutTrouve = false;

                        $.each(netMonth, function(i, month) {
                            if (month.salary_month == formattedMonth) {
                                $.each(netPayables, function(j, brut) {
                                    if (brut.salary_month == formattedMonth) {
                                        var inputMonth = document.createElement('input');
                                        var input = document.createElement('input');
                                        var input2 = document.createElement('input');
                                        inputMonth.type = 'hidden';
                                        inputMonth.name = 'month_leave['+ cpte +']'; // Ajouter un nom pour l'input
                                        inputMonth.className = 'form-control month-leave';
                                        inputMonth.value = brut.salary_month; // Placer le mois dans l'input
                                        input.type = 'number';
                                        input.name = 'salaire_brut['+ cpte +']'; // Ajouter un nom pour l'input
                                        input.className = 'form-control salaire-brut';
                                        input.value = (parseFloat(brut.salary_brut) || 0) - ((parseInt(brut.nbre_jour) || 0) * 1000);// Placer le salaire brut dans l'input
                                        input.dataset.salaireBrut = (parseFloat(brut.salary_brut) || 0) - ((parseInt(brut.nbre_jour) || 0) * 1000);
                                        input.oninput = updateTotalBrut; // Mettre à jour la somme lorsque la valeur change
                                        input2.type = 'number';
                                        input2.name = 'total_jours['+ cpte +']'; // Ajouter un nom pour l'input
                                        input2.className = 'form-control total-jours';
                                        input2.value = (parseInt(brut.nbre_jour) || 0); // Placer le salaire brut dans l'input
                                        input2.dataset.totalJours = (parseInt(brut.nbre_jour) || 0);
                                        input2.oninput = updateTotalBrut; // Mettre à jour la somme lorsque la valeur change
                                        sbCell.appendChild(input);
                                        nbCell.appendChild(input2);
                                        mlCell.appendChild(inputMonth).hidden = true; // Hide the month input
                                        salaireBrutTrouve = true;
                                        return false; // Quitter la boucle
                                    }
                                });
                                if (salaireBrutTrouve) return false; // Quitter la boucle externe
                            }
                        });

                        // Ajouter une cellule vide si aucun salaire brut n'est trouvé
                        if (!salaireBrutTrouve) {
                            var inputMonth = document.createElement('input');
                            inputMonth.type = 'hidden';
                            inputMonth.name = 'month_leave['+ cpte +']'; // Ajouter un nom pour l'input
                            inputMonth.className = 'form-control month-leave';
                            inputMonth.value = 'N/A'; // Placer le mois dans l'input
                            var input = document.createElement('input');
                            input.type = 'number';
                            input.name = 'salaire_brut['+ cpte +']'; // Ajouter un nom pour l'input
                            input.className = 'form-control salaire-brut';
                            input.value = '0'; // Valeur par défaut si aucun salaire brut n'est trouvé
                            input.dataset.salaireBrut = '0';
                            input.oninput = updateTotalBrut; // Appeler la fonction pour mettre à jour la somme lorsque la valeur change
                            sbCell.appendChild(input);
                            var input2 = document.createElement('input');
                            input2.type = 'number';
                            input2.name = 'total_jours['+ cpte +']'; // Ajouter un nom pour l'input
                            input2.className = 'form-control total-jours';
                            input2.value = '0'; // Valeur par défaut
                            input2.dataset.totalJours = 0;
                            input2.oninput = updateTotalBrut; // Mettre à jour la somme lorsque la valeur change
                            sbCell.appendChild(input);
                            nbCell.appendChild(input2);
                            mlCell.appendChild(inputMonth).hidden = true; // Hide the month input


                        }

                        newRow.appendChild(mlCell).hidden = true; // Hide the month input;
                        newRow.appendChild(smmCell);
                        newRow.appendChild(sbCell);
                        newRow.appendChild(nbCell);
                        tableBody.appendChild(newRow);

                        // Passer au mois suivant
                        backDateObj.setMonth(backDateObj.getMonth() + 1);
                    }

                    // Calculer la somme initiale des salaires bruts
                    updateTotalBrut();
                    document.getElementById('cpte_salary_brut').value = cpte;
                }

            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function updateTotalBrut() {
        var totalBrut = 0; var totalJours = 0;
        var periode_reference = parseInt(document.getElementById('periode_reference').value);
        var nombre_jours = parseInt(document.getElementById('nb_jours_conge').value);
        var smm_amount = document.getElementById('allo_smm_amount');
        var nbr_salary = document.getElementById('cpte_salary_brut').value;
        var days_sup = parseInt(document.getElementById('numberInput').value);
        var dayold = 0;

        // Parcourir toutes les cellules contenant un salaire brut
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
        var nombre_jours2 = parseInt(Math.round((totalJours/30) * 2.2) + days_sup);
        var nombre_jours3 = parseInt(Math.round((totalJours/30) * 2.2 * 1.25));
        document.getElementById('total_brut').innerHTML = totalBrut.toLocaleString();
        document.getElementById('total_salary_brut').value = totalBrut;
        document.getElementById('allo_smm').innerHTML = Math.round((totalBrut/totalJours)*30).toLocaleString();
        smm_amount.value = Math.round((totalBrut/totalJours)*30);
        document.getElementById('allo_conge_brut_span').innerHTML = Math.round((smm_amount.value/30)*nombre_jours).toLocaleString();
        document.getElementById('allo_conge_brut').value = Math.round((smm_amount.value/30)*nombre_jours);
        document.getElementById('total_jours_payés').innerHTML = totalJours;
        document.getElementById('days_net_span').innerHTML = Math.round((totalJours/30) * 2.2) + days_sup;
        document.getElementById('dure_days').value = Math.round((totalJours/30) * 2.2) + days_sup;
        //document.getElementById('days2_net_span').innerHTML = parseInt(Math.round((totalJours/30) * 2.2 * 1.25));

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

        //nombre de part en FCFA
        var nbre = document.getElementById('nb_parts').value;
        var resultricf = 0;

        if(nbre==1){
            resultricf = 0;
        }else if(nbre=="1,5"){
            resultricf = 5500;
        }else if(nbre=="2"){
            resultricf = 11000;
        }else if(nbre=="2,5"){
            resultricf = 16500;
        }else if(nbre=="3"){
            resultricf = 22000;
        }else if(nbre=="3,5"){
            resultricf = 27500;
        }else if(nbre=="4"){
            resultricf = 33000;
        }else if(nbre=="4,5"){
            resultricf = 38500;
        }else if(nbre=="5"){
            resultricf = 44000;
        }

        var impots = 0;
        var retenue = (resultimpricf) - (resultricf);

        if(retenue>0){
                impots = retenue + resultcnps;
        }else{
                impots = resultcnps;
                retenue = 0;
        }

        document.getElementById('allo_impot_brut').innerHTML = Math.round(resultimpricf).toLocaleString();
        document.getElementById('allo_ricf_brut').innerHTML = Math.round(resultricf).toLocaleString();
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

    // La date de fin ne pilote que la durée : elle ne change ni la période de référence
    // ni les mois de la période. On rafraîchit l'allocation seulement si les salaires
    // ont déjà été chargés par fillMonths(), sinon updateTotalBrut() n'a rien à calculer.
    document.getElementById('end_date').addEventListener('change', function () {
        updateLeaveDuration();
        var cpte = document.getElementById('cpte_salary_brut');
        if (cpte && cpte.value) {
            updateTotalBrut();
        }
    });

    $(document).ready(function() {
        // Fonction pour obtenir la date de retour du dernier congé de l'employé sélectionné
        function leave_end() {
            var employeeId = $('#employee_id').val();

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

                    $('#nb_parts').val(response.nb_parts);
                    $('#back_date').val(response.leave_date);
                    $('#back_date_span').text(response.leave_date);
                    $('#employee_company_doj').val(response.company_doj);
                    $('#employee_salary').val(response.leave_salary);
                    $('#input_name').val(response.name_emp);

                    //var netPayables = response.netPayables;
                    var dateEmbauche = new Date(response.company_doj);
                    var dateActuelle = new Date();

                    var difference = dateActuelle - dateEmbauche;
                    var annees = difference / (365 * 24 * 60 * 60 * 1000);
                    var mois = difference / (30 * 24 * 60 * 60 * 1000);

                    document.getElementById('date_pa').innerHTML = Math.round(annees);

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

        // Employé pré-sélectionné (création depuis la liste des employés) :
        // charger ses données tout de suite, sans attendre un changement de sélection.
        if ($('#employee_id').val()) {
            leave_end();
        }
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
@endpush