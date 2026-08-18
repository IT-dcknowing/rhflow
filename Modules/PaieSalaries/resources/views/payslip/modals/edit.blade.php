<style>
    input.invalid {
        background-color: #ffdddd;
    }
    .tab {
        display: none;
    }

    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #253e87;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
    }

    .step.active {
        opacity: 1;
    }

    /* Mark the steps that are finished and valid: */
    .step.finish {
        background-color: #253e87;
    }
</style>
@php

    $date_embauche = new DateTime($employee->company_doj);
    $date_actuelle = new DateTime(date('Y-m-d'));
    $difference = $date_embauche->diff($date_actuelle);
    $date_pa = $difference->format('%y');
    $city = $company->city;
@endphp

<input type="text" id="enfts" name="enfts" value="{{$employee->enfant}}" hidden="">
<input type="text" id="city" name="city" value="{{ $city }}" hidden="">
<input type="text" id="dateancien" name="dateancien" value="{{ $date_pa }}" hidden="">
<input type="text" id="cpte" name="cpte" value="" hidden="">
<input type="text" id="employee_id" name="employee_id" value="{{$employee->id}}" hidden="">
<input type="text" id="nbre_jour" name="nbre_jour" value="{{$employee->tax_payer_id}}" hidden="">
<input type="text" id="cmu_brut" name="cmu_brut" value="{{$employee->cmu}}" hidden="">
<input type="text" id="part_igr" name="part_igr" value="{{$employee->parts}}" hidden="">
@if(strpos($city, 'Abidjan') !== false || strpos($city, 'abidjan') !== false ||  strpos($city, 'ABIDJAN') !== false)
        <input type="text" id="tp_brut" name="tp_brut" value="30000" hidden="">
    @elseif(strpos($city, 'Bouaké') !== false || strpos($city, 'bouaké') !== false ||  strpos($city, 'BOUAKE') !== false)
        <input type="text" id="tp_brut" name="tp_brut" value="24000" hidden="">
    @else
        <input type="text" id="tp_brut" name="tp_brut" value="22000" hidden="">
@endif
<input type="text" id="salary_day" name="salary_day" value="{{$employee->branch_location}}" hidden="">
<input type="text" id="salarybrut" name="salarybrut" value="{{$employee->get_brut_salary($periode)}}" hidden="">
<input type="text" id="salarybase" name="salarybase" value="{{$employee->salary}}" hidden="">
<input type="text" id="countAllowances" name="countAllowances" value="{{$countAllowances}}" hidden="">
<div class="row">  
    <div class="col-12 text-center mb-4">
        <h6 align="center" class="text-center mb-4">
            <span class="text-center text-primary">
                <strong>Nombre de jours travaillés : {{$employee->tax_payer_id}} jours | Salaire Brut : </strong>
            </span>
            <span id="brut_update" class="badge rounded bg-label-black p-1" style="background-color:#000; color:yellow;"></span>
        </h6>
        <span id="nombreDesactive" style="margin-bottom: 10px;"></span> 
        <span id="cpte1" align="center" class="text-center mb-4">
            <strong></strong>
        </span>
        <span id="cpte2" align="center" class="text-center mb-4">
            <strong></strong>
        </span>
    </div>
    
    <div class="col-12 mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-sm border border-dark" width="100%">
                    <thead>
                        <tr bgcolor="#ddcd08">
                            <td align="center" class ="border border-dark" width="2%"><strong>#</strong></td>
                            <td align="center" class ="border border-dark" width="4%"><strong>Codes</strong></td>
                            <td align="center" class ="border border-dark" width="3%"><strong>Compta</strong></td>
                            <td align="center" class ="border border-dark" width="25%"><strong>Eléments du brut</strong></td>
                            <td align="center" class ="border border-dark" width="30%"><strong>Montants</strong></td>
                            <td colspan="2" align="center" class ="border border-dark" width="36%"><strong>Montants au prorata temporis</strong></td>
                            <td align="center" class ="border border-dark" width="10%"><strong>Actions</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allowanceEmployee->where('periode_id', $periode) as $allowance)
                            <tr class="allowance-row" id="allowance-row-{{ $allowance->id }}">
                                <td align="center" class="border border-dark" width="2%">
                                    <input type="checkbox" id="option{{ $allowance->id }}"  value="{{ $allowance->allowance_option_id }}" checked @if(in_array($allowance->allowance_option_id, $allowances)) disabled @endif>
                                </td>
                                <td align="center" class="border border-dark">
                                    <strong>{{ $allowance->code }}</strong>
                                </td>
                                <td align="center" class="border border-dark"> 
                                    <strong>{{ $allowance->code_compta }}</strong>
                                </td>
                                <td class="border border-dark">
                                    {{ $allowance->title }}
                                    <input type="text" id="allowance_name{{ $allowance->id }}" name="allowance_name{{ $allowance->id }}" value="{{ $allowance->title }}" hidden="">
                                </td>
                                <td class ="border border-dark">
                                    <input type="number" class="form-control" id="montant{{ $allowance->id }}" name="montant{{ $allowance->id }}" value="{{ $allowance->montant }}" oninput="workDay({{ $allowance->id }})" oninput="verifierLimite({{ $allowance->id }})"style="text-align: right;">
                                </td>
                                <td class ="border border-dark">
                                    <input type="number" class="form-control" id="amount{{ $allowance->id }}" name="amount{{ $allowance->id }}" value="{{ $allowance->amount }}" style="text-align: right;" readonly>
                                </td>
                                <td class="border border-dark">
                                    <div id="choix_prorata{{ $allowance->id }}">
                                        <select type="text" name="jours_work{{ $allowance->id }}" class="form-control" id="jours_work{{ $allowance->id }}" onchange="workJours({{ $allowance->id }})">
                                            <option value="1">Oui</option>
                                            <option value="0">Non</option>
                                        </select>
                                    </div>
                                </td>
                                <td class="border border-dark">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" class="btn btn-sm btn-success me-4" onclick="updateSingleAllowance({{ $allowance->id }})" title="Modifier">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteSingleAllowance({{ $allowance->id }})" title="Supprimer">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Aucun élément du brut trouvé</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    var currentTab = 0; // Onglet actuel (0 = premier onglet)
    var selectedItems = 0; // Compteur d'éléments sélectionnés
    
    // Affiche l'onglet spécifié
    function showTab(n) {
        var tabs = document.getElementsByClassName("tab");
        var hasCreatedAllowances = document.getElementById("allowcreate")?.value > 0;
        var totalTabs = hasCreatedAllowances ? 3 : 2;
        
        // Masquer tous les onglets
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].style.display = "none";
        }
        
        // Afficher l'onglet actuel
        tabs[n].style.display = "block";
        
        // Gestion des boutons Précédent/Suivant
        if (n === 0) {
            // Premier onglet
            document.getElementById("prevBtn").style.display = "none";
            document.getElementById("nextBtn").style.display = "inline";
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + selectedItems + ' éléments</strong>';
        } else if (n === totalTabs - 1) {
            // Dernier onglet
            document.getElementById("prevBtn").style.display = "inline";
            document.getElementById("nextBtn").style.display = "none";
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + selectedItems + ' éléments</strong>';
        } else {
            // Onglets intermédiaires
            document.getElementById("prevBtn").style.display = "inline";
            document.getElementById("nextBtn").style.display = "inline";
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + selectedItems + ' éléments</strong>';
        }
        
        // Mettre à jour l'indicateur d'étape
        fixStepIndicator(n);
    }
    
    // Gère la navigation entre les onglets
    function nextPrev(n) {
        var tabs = document.getElementsByClassName("tab");
        var hasCreatedAllowances = document.getElementById("allowcreate")?.value > 0;
        var totalTabs = hasCreatedAllowances ? 3 : 2;
        
        // Masquer l'onglet actuel
        tabs[currentTab].style.display = "none";
        
        // Déterminer le nouvel onglet
        if (n === 1 && !validateForm()) {
            return false;
        }
        
        // Mettre à jour l'onglet actuel
        currentTab = currentTab + n;
        
        // Si on dépasse le nombre d'onglets, soumettre le formulaire
        if (currentTab >= totalTabs) {
            document.getElementById("payslipForm").submit();
            return false;
        }
        
        // Afficher le nouvel onglet
        showTab(currentTab);
    }
    
    // Valide le formulaire de l'onglet actuel
    function validateForm() {
        // Implémentez ici la validation du formulaire si nécessaire
        return true;
    }
    
    // Initialisation
    showTab(currentTab);
    
    // Fonction pour mettre à jour le compteur d'éléments sélectionnés
    function updateSelectedItems(count) {
        selectedItems = count;
        document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + selectedItems + ' éléments</strong>';
    }
    
    // Fonction pour mettre à jour l'indicateur d'étape
    function fixStepIndicator(n) {
        var steps = document.getElementsByClassName("step");
        for (var i = 0; i < steps.length; i++) {
            steps[i].className = steps[i].className.replace(" active", "");
        }
        if (steps[n]) {
            steps[n].className += " active";
        }
    }

    // Fonction pour compter les cases à cocher désactivées
    function compterCasesDesactivees() {
        var nombreDesactivees = 0;
        var cases = document.querySelectorAll('input[type="checkbox"]');
        cases.forEach(function(caseCheckbox) {
            if (caseCheckbox.disabled) {
                nombreDesactivees++;
            }
        });
        document.getElementById('nombreDesactive').textContent = "Nombre de cases désactivées : " + nombreDesactivees;
    }

    // Appel de la fonction au chargement de la page
    window.onload = function() {
        compterCasesDesactivees();
    };
</script>
<script>
    function workDay(i) {
        var montant = parseInt(document.getElementById('montant' + i).value);
        var worsk = document.getElementById('jours_work' + i).value;
        var amount = document.getElementById('amount' + i);
        var nbrjours = parseInt(document.getElementById('nbre_jour').value);

        // Vérifier si 'montant' et 'nbrjours' sont des nombres valides
        if (isNaN(montant) || isNaN(nbrjours)) {
            console.error('Montant ou nombre de jours non valide');
            return;
        }

        if (nbrjours == 30) {
            amount.value = montant;
        } else {
            if(worsk == 0){
                amount.value = montant;
            }else{
                var calculatedValue = Math.round((montant / 30) * nbrjours);
                amount.value = calculatedValue;
            }
        }
    }
    
    function workJours(i) {
        var montant = parseInt(document.getElementById('montant' + i).value);
        var worsk = document.getElementById('jours_work' + i).value;
        var amount = document.getElementById('amount' + i);
        var nbrjours = parseInt(document.getElementById('nbre_jour').value);

        // Vérifier si 'montant' et 'nbrjours' sont des nombres valides
        if (isNaN(montant) || isNaN(nbrjours)) {
            console.error('Montant ou nombre de jours non valide');
            return;
        }

        if (nbrjours == 30) {
            amount.value = montant;
        } else {
            if(worsk == 0){
                amount.value = montant;
            }else{
                var calculatedValue = Math.round((montant / 30) * nbrjours);
                amount.value = calculatedValue;
            }
        }
    }

    function verifierLimite(i) {
        var nameAllowance = document.getElementById('allowance_name' + i).value;
        var limite = document.getElementById('amount' + i).value;
        var messageSpan = document.getElementById("value" + i);
        var details = document.getElementById("details" + i);
        var limite2 = parseInt(limite);
        if(nameAllowance == 'Prime de stage'){
            if (limite2 > 150000) {
                messageSpan.innerHTML = '<strong style="color:red;">Le montant imposable est égale à '+ (parseInt(limite)-150000).toLocaleString() + ' FCFA.</strong>';
                details.value = 'Le montant imposable est égale à '+ (parseInt(limite)-150000).toLocaleString() + ' FCFA.';
                //input.value = limite; // bloquer le champ à la valeur maximale
            } else {
                messageSpan.innerHTML = '';
            }
        }else if(nameAllowance == 'Indemnité d\'apprentissage'){
            if (limite2 > 100000) {
                messageSpan.innerHTML = '<strong style="color:red;">Le montant imposable est  égale à '+ (parseInt(limite)-100000).toLocaleString() + ' FCFA.</strong>';
                details.value = 'Le montant imposable est égale à '+ (parseInt(limite)-100000).toLocaleString() + ' FCFA.';
                //input.value = limite; // bloquer le champ à la valeur maximale
            } else {
                messageSpan.innerHTML = '';
            }
        }
    }

    function updateSingleAllowance(allowanceId) {
        var montant = document.getElementById('montant' + allowanceId).value;
        var amount = document.getElementById('amount' + allowanceId).value;
        var employeeId = document.getElementById('employee_id').value;
        var nbreJour = document.getElementById('nbre_jour').value;
        
        if (montant <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Le montant doit être supérieur à 0'
            });
            return;
        }
        
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Voulez-vous modifier cet élément?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, modifier!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/company/paiesalaries/allowance/' + encodeURIComponent(allowanceId),
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        allowance_id: allowanceId,
                        employee_id: employeeId,
                        montant: montant,
                        amount: amount,
                        jours_work: nbreJour
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la modification'
                        });
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    }

    function deleteSingleAllowance(allowanceId) {
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Cette action est irréversible!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/company/paiesalaries/allowance/' + encodeURIComponent(allowanceId),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        allowance_id: allowanceId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Supprimé!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                $('#allowance-row-' + allowanceId).fadeOut(300, function() {
                                    $(this).remove();
                                });
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la suppression'
                        });
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    }
</script>
