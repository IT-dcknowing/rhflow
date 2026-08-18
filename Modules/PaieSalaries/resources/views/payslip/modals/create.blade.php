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
    $date_embauche = new DateTime($employee->company_doj ?? $employee->start_date);
    $date_actuelle = new DateTime($periode->date_fin);
    $difference = $date_embauche->diff($date_actuelle);
    $date_pa = intval($difference->format('%y'));
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
<input type="text" id="salarybrut" name="salarybrut" value="{{ $employee->get_brut_salary($periode->id) }}" hidden="">
<input type="text" id="salarybase" name="salarybase" value="{{ $employee->salary }}" hidden="">
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
    <div class="col-12">
        <div class="card-body table-border-style" style=" overflow:auto">
            <div class="table-responsive">
                @php
                    $montantConv = $allowancesDefault->filter(function($allowance) {
                        return strpos($allowance->type_montant, '1') !== false;
                    });

                    $montantLibre = $allowancesDefault->filter(function($allowance) {
                        return strpos($allowance->type_montant, '0') !== false;
                    });
                    $cpteallow = 0;
                    $cpteallow = $allowancesCreated->count();
                @endphp
                <div class="tab" style="display:block;">
                    <table class="table-sm" width="100%">
                        <thead>
                            <tr bgcolor="#ddcd08">
                                <td align="center" class ="border border-dark" width="2%"><strong>#</strong></td>
                                <td align="center" class ="border border-dark" width="4%"><strong>Codes</strong></td>
                                <td align="center" class ="border border-dark" width="3%"><strong>Compta</strong></td>
                                <td align="center" class ="border border-dark" width="25%"><strong>Eléments du brut</strong></td>
                                <td align="center" class ="border border-dark" width="15%"><strong>Montants conventionnels</strong></td>
                                <td align="center" class ="border border-dark" width="15%"><strong>Montants libres</strong></td>
                                <td colspan="2" align="center" class ="border border-dark" width="36%"><strong>Montants au prorata temporis</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($montantConv as $allowance)
                                @if($allowance->code != 110)
                                <tr>
                                    <td align="center" class ="border border-dark" width="2%">
                                        <input type="checkbox" id="option{{ $allowance->code}}"  value="{{ $allowance->code}}" onchange="afficherChamp{{ $allowance->code}}()" @if(in_array($allowance->id, $allowances)) disabled @endif>
                                    </td>
                                    <td align="right" class ="border border-dark" width="4%">  
                                        <strong>{{ $allowance->code }}</strong>
                                        <input type="text" id="code{{ $allowance->code}}" name="code{{ $allowance->code}}" value="{{ $allowance->code }}" hidden="">
                                    </td>
                                    <td align="right" class ="border border-dark" width="3%">
                                        <strong>{{ $allowance->code_compta }}</strong>
                                        <input type="text" id="code_compta{{ $allowance->code}}" name="code_compta{{ $allowance->code}}" value="{{ $allowance->code_compta }}" hidden="">
                                    </td>
                                    <td class ="border border-dark" width="25%">
                                        <input type="text" id="item_brut{{ $allowance->code}}" value="{{ $allowance->id}}" hidden="">
                                        <label for="option{{ $allowance->code}}"> {{ $allowance->name }}</label>
                                        <input type="text" id="allowance_name{{ $allowance->code}}" name="allowance_name{{ $allowance->code}}" value="{{ $allowance->name }}" hidden="">
                                        <input type="text" name="trait_fisc{{ $allowance->code}}" id="trait_fisc{{ $allowance->code}}" value="{{ $allowance->param_fiscal }}" hidden="">
                                        <input type="text{{ $allowance->code}}" name="trait_cnps{{ $allowance->code}}" id="trait_cnps{{ $allowance->code}}" value="{{ $allowance->param_social }}" hidden="">
                                        <input type="text" name="details{{ $allowance->code}}" id="details{{ $allowance->code}}" value="Montant Conventionel, {{ $allowance->param_fiscal }} - {{ $allowance->param_social }}" hidden="">
                                        <input type="number" class="form-control" id="amount_imp{{ $allowance->code}}" name="amount_imp{{ $allowance->code}}" placeholder="Entrer le montant Soumis" hidden>
                                        <table id="info{{ $allowance->code}}" style="display: none;" class="table table-sm">
                                            <tr><td class="border border-dark" align="center">ITS</td><td class="border border-dark" align="center">CNPS</td></tr>
                                            <tr><td class="border border-dark"  align="center"><strong style="color:brown;">Exonéré<br/>{{ $allowance->param_fiscal }}</strong></td><td class="border border-dark" align="center"><strong style="color:brown;">{{ $allowance->param_social }}</strong></td></tr>
                                        </table>
                                    </td>
                                    <td class ="border border-dark"  align="right"><span id="value{{ $allowance->code}}"></span></td>
                                    <td class ="border border-dark"><input type="number" class="form-control" id="montant{{ $allowance->code}}" name="montant{{ $allowance->code}}" value="" oninput="workDay({{ $allowance->code}})" style="text-align: right;" hidden></td>
                                    <td class ="border border-dark"><input type="number" class="form-control" id="amount{{ $allowance->code}}" name="amount{{ $allowance->code}}" value="" style="text-align: right;" hidden readonly></td>
                                    <td class ="border border-dark">
                                        <div style="display: none;" id="choix_prorata{{ $allowance->code}}">
                                            <select type="text" name="jours_work{{ $allowance->code}}" class="form-control" id="jours_work{{ $allowance->code}}" onchange="workJours({{ $allowance->code}})">
                                                <option value="1">Oui</option>
                                                <option value="0">Non</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                @else
                                <tr>
                                    <td class ="border border-dark">
                                        <input type="checkbox" id="option{{ $allowance->code}}"  value="{{$allowance->name}}" onchange="afficherChamp{{ $allowance->code}}()"  @if(in_array($allowance->id, $allowances)) disabled @endif>
                                    </td>
                                    <td align="right" class ="border border-dark" >
                                        <strong>{{$allowance->code}}</strong>
                                        <input type="text" id="code{{ $allowance->code}}" name="code{{ $allowance->code}}" value="{{$allowance->code}}" hidden="">
                                    </td>
                                    <td align="right" class ="border border-dark">
                                        <strong>{{$allowance->code_compta}}</strong>
                                        <input type="text" id="code_compta{{ $allowance->code}}" name="code_compta{{ $allowance->code}}" value="{{$allowance->code_compta}}" hidden="">
                                    </td>
                                    <td class ="border border-dark" >
                                        <input type="text" id="item_brut{{ $allowance->code}}"  value="{{ $allowance->id}}" hidden="">
                                        <label for="option{{ $allowance->code}}">{{$allowance->name}}</label>
                                        <input type="text" id="allowance_name{{ $allowance->code}}" name="allowance_name{{ $allowance->code}}" value="{{$allowance->name}}" hidden="">
                                        <input type="text" name="trait_fisc{{ $allowance->code}}" id="trait_fisc{{ $allowance->code}}" value="{{$allowance->param_fiscal}}" hidden=""><input type="text" name="trait_cnps{{ $allowance->code}}" id="trait_cnps{{ $allowance->code}}" value="{{$allowance->param_social}}" hidden="">
                                        <input type="text" name="details{{ $allowance->code}}" id="details{{ $allowance->code}}" value="Montant conventionnel" hidden="">
                                        <input type="number" class="form-control" id="amount_imp{{ $allowance->code}}" name="amount_imp{{ $allowance->code}}" placeholder="Entrer le montant Soumis" hidden>
                                        <table id="info{{ $allowance->code}}" style="display: none;">
                                            <tr><td class="border border-dark" align="center">ITS</td><td class="border border-dark" align="center">CNPS</td></tr>
                                            <tr><td class="border border-dark"  align="center"><strong style="color:brown;">Exonéré<br/>{{$allowance->param_fiscal}}</strong></td><td class="border border-dark" align="center"><strong style="color:brown;">{{$allowance->param_social}}</strong></td></tr>
                                        </table>
                                    </td>
                                    <td class ="border border-dark">
                                        <span id="value{{ $allowance->code}}"></span>
                                        <input type="number" id="nbrjours" name="nbrjours" class="form-control" min="1" value="" style="text-align: right;" oninput="panierjours()" hidden>
                                    </td>
                                    <td class ="border border-dark">
                                        <span id="total{{ $allowance->code}}"></span>
                                        <input type="number" id="montant{{ $allowance->code}}" name="montant{{ $allowance->code}}" class="form-control" value="" style="text-align: right;" hidden>
                                    </td>
                                    <td class ="border border-dark">
                                        <span id="total_prorata{{ $allowance->code}}"></span>
                                        <input type="number" class="form-control" id="amount{{ $allowance->code}}" name="amount{{ $allowance->code}}" value="" style="text-align: right;" hidden readonly>
                                    </td>
                                    <td class ="border border-dark">
                                        <div style="display: none;" id="choix_prorata{{ $allowance->code}}">
                                            -
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab" style="display:none;">
                    <table class="table-sm" width="100%">
                        <thead>
                            <tr bgcolor="#ddcd08">
                                <td align="center" class ="border border-dark" width="2%"><strong>#</strong></td>
                                <td align="center" class ="border border-dark" width="4%"><strong>Codes</strong></td>
                                <td align="center" class ="border border-dark" width="3%"><strong>Compta</strong></td>
                                <td align="center" class ="border border-dark" width="35%"><strong>Eléments du brut</strong></td>
                                <td align="center" class ="border border-dark" width="15%"><strong>Montants libres</strong></td>
                                <td colspan="2" align="center" class ="border border-dark" width="31%"><strong>Montants au prorata temporis</strong></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($montantLibre as $allowance)
                                <tr>
                                    <td align="center" class ="border border-dark" width="2%">
                                        <input type="checkbox" id="option{{ $allowance->code}}"  value="{{ $allowance->code}}" onchange="afficherChamp{{ $allowance->code}}()" @if(in_array($allowance->id, $allowances)) disabled @endif>
                                    </td>
                                    <td align="right" class ="border border-dark" width="4%">
                                        <strong>{{ $allowance->code }}</strong>
                                        <input type="text" id="code{{ $allowance->code}}" name="code{{ $allowance->code}}" value="{{ $allowance->code }}" hidden="">
                                    </td>
                                    <td align="right" class ="border border-dark" width="3%">
                                        <strong>{{ $allowance->code_compta }}</strong>
                                        <input type="text" id="code_compta{{ $allowance->code}}" name="code_compta{{ $allowance->code}}" value="{{ $allowance->code_compta }}" hidden="">
                                    </td>
                                    <td class ="border border-dark" >
                                        <input type="text" id="item_brut{{ $allowance->code}}" value="{{ $allowance->id}}" hidden="">
                                        <label for="option{{ $allowance->code}}">{{ $allowance->name }}</label>
                                        <input type="text" id="allowance_name{{ $allowance->code}}" name="allowance_name{{ $allowance->code}}" value="{{ $allowance->name }}" hidden="">
                                        <input type="text" name="trait_fisc{{ $allowance->code}}" id="trait_fisc{{ $allowance->code}}" value="{{ $allowance->param_fiscal }}" hidden="">
                                        <input type="text" name="trait_cnps{{ $allowance->code}}" id="trait_cnps{{ $allowance->code}}" value="{{ $allowance->param_social }}" hidden="">
                                        <input type="text" name="details{{ $allowance->code}}" id="details{{ $allowance->code}}" value="Montant Libre, {{ $allowance->param_fiscal }} - {{ $allowance->param_social }}" hidden="">
                                        <table id="info{{ $allowance->code}}" style="display: none;" class="table table-sm">
                                            <tr>
                                                <td class="border border-dark" align="center">ITS</td><td class="border border-dark" align="center">CNPS</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-dark"  align="center"><strong style="color:brown;">Exonéré<br/>{{ $allowance->param_fiscal }}</strong></td>
                                                <td class="border border-dark" align="center"><strong style="color:brown;">{{ $allowance->param_social }}</strong></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class ="border border-dark" ><input type="number" class="form-control" id="montant{{ $allowance->code}}" name="montant{{ $allowance->code}}" oninput="workDay({{ $allowance->code}})" style="text-align: right;" hidden=""></td>
                                    <td class ="border border-dark" ><input type="number" class="form-control" id="amount{{ $allowance->code}}" name="amount{{ $allowance->code}}" value="" style="text-align: right;" hidden readonly></td>
                                    <td class ="border border-dark">
                                        <div style="display: none;" id="choix_prorata{{ $allowance->code}}">
                                            <select type="text" name="jours_work{{ $allowance->code}}" class="form-control" id="jours_work{{ $allowance->code}}" onchange="workJours({{ $allowance->code}})">
                                                <option value="1">Oui</option>
                                                <option value="0">Non</option>
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($allowancesCreated->count() > 0)
                    <input type="hidden" id="allowcreate" name="allowcreate" value="{{$cpteallow}}">
                    <div class="tab" style="display:none;">
                        <h6 align="center"><span class="text-info"><strong>Éléments du brut créés</strong></span></h6>
                        <table class="table-sm" width="100%">
                            <thead>
                                <tr bgcolor="#ddcd08">
                                    <td align="center" class="border border-dark"><strong>#</strong></td>
                                    <td align="center" class="border border-dark"><strong>Codes</strong></td>
                                    <td align="center" class ="border border-dark" width="3%"><strong>Compta</strong></td>
                                    <td align="center" class="border border-dark"><strong>Éléments du brut</strong></td>
                                    <td align="center" class="border border-dark"><strong>Montants libres</strong></td>
                                    <td colspan="2" align="center" class="border border-dark"><strong>Montants au prorata temporis</strong></td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allowancesCreated as $allowance)
                                    <tr>
                                        <td class ="border border-dark" >
                                            <input type="checkbox" id="option{{ $allowance->code}}" name="checkbox" value="{{$allowance->title}}" onchange="afficherNew({{ $allowance->code}})"  @if(in_array($allowance->id, $allowances)) disabled @endif>
                                        </td>
                                        <td align="right" class ="border border-dark">
                                            <strong>{{ $allowance->id + 100 }}</strong>
                                            <input type="text" id="code{{ $allowance->code}}" name="code{{ $allowance->code}}" value="{{ $allowance->id + 100 }}" hidden="">
                                        </td>
                                        <td align="right" class ="border border-dark" width="3%">
                                            <strong>{{ $allowance->code_compta }}</strong>
                                            <input type="text" id="code_compta{{ $allowance->code}}" name="code_compta{{ $allowance->code}}" value="{{ $allowance->code_compta }}" hidden="">
                                        </td>
                                        <td class ="border border-dark" >
                                            <input type="text" id="item_brut{{ $allowance->code}}" value="{{ $allowance->id}}" hidden="">
                                            <label for="option{{ $allowance->code}}">{{$allowance->name}}</label>
                                            <input type="text" id="allowance_name{{ $allowance->code}}" name="allowance_name{{ $allowance->code}}" value="{{$allowance->name}}" hidden="">
                                            <input type="text" name="trait_fisc{{ $allowance->code}}" id="trait_fisc{{ $allowance->code}}" value="{{$allowance->param_fiscal}}" hidden="">
                                            <input type="text" name="trait_cnps{{ $allowance->code}}" id="trait_cnps{{ $allowance->code}}" value="{{$allowance->param_social}}" hidden="">
                                            <input type="text" name="details{{ $allowance->code}}" id="details{{ $allowance->code}}" value="Montant Libre, {{ $allowance->param_fiscal }} - {{ $allowance->param_social }}" hidden="">
                                            <table id="info{{ $allowance->code}}" style="display: none;">
                                                <tr><td class="border border-dark" align="center">ITS</td><td class="border border-dark" align="center">CNPS</td></tr>
                                                <tr><td class="border border-dark" align="center"><strong style="color:brown;">Exonéré<br/>{{$allowance->param_fiscal}}</strong></td><td class="border border-dark" align="center"><strong style="color:brown;">{{$allowance->param_social}}</strong></td></tr>
                                            </table>
                                        </td>
                                        <td class ="border border-dark" ><input type="number" class="form-control" id="montant{{ $allowance->code}}" name="montant{{ $allowance->code}}" oninput="workDay({{ $allowance->code}})" style="text-align: right;" hidden=""></td>
                                        <td class ="border border-dark" ><input type="number" class="form-control" id="amount{{ $allowance->code}}" name="amount{{ $allowance->code}}" value="" style="text-align: right;" hidden readonly></td>
                                        <td class ="border border-dark">
                                            <div style="display: none;" id="choix_prorata{{ $allowance->code}}">
                                                <select type="text" name="jours_work{{ $allowance->code}}" class="form-control" id="jours_work{{ $allowance->code}}" onchange="workJours({{ $allowance->code}})">
                                                    <option value="1">Oui</option>
                                                    <option value="0">Non</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Circles which indicates the steps of the form: -->
    @if($allowancesCreated->count() > 0)
        <div style="text-align:center;margin-top:10px;">
            <span class="step"></span>
            <span class="step"></span>
            <span class="step"></span>
        </div>
    @else
        <div style="text-align:center;margin-top:10px;">
            <span class="step"></span>
            <span class="step"></span>
        </div>
    @endif
    <div style="overflow:auto;">
        <div align="center">
            <button type="button" id="prevBtn" class="btn  btn-secondary" onclick="nextPrev(-1)" style="display: none;">Précédent</button>
            <button type="button" id="nextBtn" class="btn  btn-primary" onclick="nextPrev(1)">Suivant</button>
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
            document.getElementById("cpte1").innerHTML = '';
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
    var total_brut = document.getElementById('salarybrut').value;
    var brut_span = document.getElementById("brut_update");
    brut_span.innerHTML = total_brut.toLocaleString() + ' FCFA';
    function exofisc(){
        var trait_fisc = document.getElementById("trait_fisc130").value;;
        var span = document.getElementById("art_1");
        var div = document.getElementById("art_2");
        if(trait_fisc == '10%-Art 116-1'){
            span.innerHTML ='<strong>Classification de l\'exonération <br/>  Article 116 alinéas 1.</strong>';
            div.style.display = 'none';
            //span2.innerHTML ='';
        }else if(trait_fisc == '0%'){
            span.innerHTML ='';
            div.style.display = 'none';
            //span2.innerHTML ='';
        }else if(trait_fisc == '100% (Parfois limite d\'exo)'){
            //span2.innerHTML ='<strong>Classification de l\'exonération<br/><span>. <br/> Article 116 alinéas 10.<br/>Article 116 alinéas 12.</strong>';
            div.style.display = 'inline';
            span.innerHTML ='';
        }else{
            span.innerHTML ='';
            div.style.display = 'none';
        }
    }

    function montimpo(){
        var trait_cnps = document.getElementById("trait_cnps130").value;
        var input = document.getElementById("amount_imp130");
        if (trait_cnps == 'Soumis au-delà du mode de calcul'){
            input.hidden = false;
        }else{
            input.hidden = true;
        }
    }

    $(document).ready(function() {
        afficherChamp4();
    });

    function updateDetails(value, detailsId) {
        document.getElementById(detailsId).value = value;
    }

    function Prorata(){
        var worsk =document.getElementById('jours_work130').value;
        var montant = parseInt(document.getElementById('montant130').value);
        var amount = document.getElementById('amount130');
        var input = document.getElementById("amount_imp130");
        var nbrjours = parseInt(document.getElementById('nbre_jour').value);

        // Vérifier si 'montant' et 'nbrjours' sont des nombres valides
        if (isNaN(montant) || isNaN(nbrjours)) {
            console.error('Montant ou nombre de jours non valide');
            return;
        }

        if(worsk == 0){
            amount.value = montant;
            input.value = montant;
        }else{
            var calculatedValue = Math.round((montant / 30) * nbrjours);
            amount.value = calculatedValue;
            input.value = calculatedValue;
        }
    }

    function workDay(i) {
        var montant = parseInt(document.getElementById('montant' + i).value);
        var worsk = document.getElementById('jours_work' + i).value;
        var amount = document.getElementById('amount' + i);
        var input = document.getElementById("amount_imp" + i);
        var nbrjours = parseInt(document.getElementById('nbre_jour').value);

        // Vérifier si 'montant' et 'nbrjours' sont des nombres valides
        if (isNaN(montant) || isNaN(nbrjours)) {
            console.error('Montant ou nombre de jours non valide');
            return;
        }

        if (nbrjours == 30) {
            amount.value = montant;
            input.value = montant;
        } else {
            if(worsk == 0){
                amount.value = montant;
                input.value = montant;
            }else{
                var calculatedValue = Math.round((montant / 30) * nbrjours);
                amount.value = calculatedValue;
                input.value = calculatedValue;
            }
        }
    }

    function workJours(i) {
        var montant = parseInt(document.getElementById('montant' + i).value);
        var worsk = document.getElementById('jours_work' + i).value;
        var amount = document.getElementById('amount' + i);
        var input = document.getElementById("amount_imp" + i);
        var nbrjours = parseInt(document.getElementById('nbre_jour').value);

        // Vérifier si 'montant' et 'nbrjours' sont des nombres valides
        if (isNaN(montant) || isNaN(nbrjours)) {
            console.error('Montant ou nombre de jours non valide');
            return;
        }

        if (nbrjours == 30) {
            amount.value = montant;
            input.value = montant;
        } else {
            if(worsk == 0){
                amount.value = montant;
                input.value = montant;
            }else{
                var calculatedValue = Math.round((montant / 30) * nbrjours);
                amount.value = calculatedValue;
                input.value = calculatedValue;
            }
        }
    }

    var salary =  parseInt(document.getElementById("salarybrut").value);
    var countAllowances = parseInt(document.getElementById("countAllowances").value);
    var dateancien = parseInt(document.getElementById("dateancien").value);
    var salarybase = parseInt(document.getElementById("salarybase").value);
    var city = document.getElementById("city").value;
    var enfts = document.getElementById("enfts").value;
    var nbre_jour = parseInt(document.getElementById("nbre_jour").value);
    var salary_day = parseInt(document.getElementById("salary_day").value);
    var cpte = countAllowances;

    function afficherChamp102() {
        var checkbox = document.getElementById('option102');
        var span = document.getElementById('value102');
        var item_brut = document.getElementById("item_brut102");
        var worsk = document.getElementById('choix_prorata102');
        var spaninfo = document.getElementById('info102');
        var amount = document.getElementById('amount102');
        var montant = document.getElementById('montant102');
        var input = document.getElementById("amount_imp102");
        if (checkbox.checked) {
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut102');
            if(nbre_jour == 30){
                amount.value = Math.round((salary*50)/100);
                input.value =  Math.round((salary*50)/100);
                montant.value = Math.round((salary*50)/100);
                span.innerHTML = '<strong>' + Math.round((salary*50)/100).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value =  Math.round((((salary*50)/100)/30)*nbre_jour);
                montant.value = Math.round((salary*50)/100);
                input.value =  Math.round((((salary*50)/100)/30)*nbre_jour);
                span.innerHTML = '<strong>' + Math.round((salary*50)/100).toLocaleString() + ' FCFA</strong>';
            }
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'none';
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp104() {
        var checkbox = document.getElementById('option104');
        var span = document.getElementById('value104');
        var item_brut = document.getElementById("item_brut104");
        var spaninfo = document.getElementById('info104');
        var amount = document.getElementById('amount104');
        var montant = document.getElementById('montant104');
        var worsk = document.getElementById('choix_prorata104');
        var input = document.getElementById("amount_imp104");
        if(checkbox.disabled == true){
            document.getElementById("cpte1").innerHTML = '1';
        }
        if (checkbox.checked) {
            amount.hidden = false; cpte++;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut104');
            spaninfo.style.display = 'block';
            if(dateancien < 2){
                amount.value = 0; montant.value = 0; span.innerHTML = '<strong>0 FCFA / ' + dateancien + ' ans</strong>';
            }else if(dateancien == 2){
                if(nbre_jour == 30){
                    amount.value =  Math.round((salarybase*2)/100);
                    montant.value = Math.round((salarybase*2)/100);
                    input.value = Math.round((salarybase*2)/100);
                    span.innerHTML = '<strong> ' + Math.round((salarybase*2)/100).toLocaleString() + ' FCFA / ' + dateancien + ' ans </strong>';
                }else{
                    amount.value =  Math.round((((salarybase*2)/100)/30)*nbre_jour);
                    montant.value = Math.round((salarybase*2)/100);
                    input.value = Math.round((((salarybase*2)/100)/30)*nbre_jour);
                    span.innerHTML = '<strong> ' + Math.round((salarybase*2)/100).toLocaleString() + ' FCFA / ' + dateancien + ' ans </strong>';

                }
            }else if(dateancien > 2 && dateancien < 26){
                if(nbre_jour == 30){
                    amount.value =  Math.round((salarybase*dateancien)/100);
                    montant.value = Math.round((salarybase*dateancien)/100);
                    input.value = Math.round((salarybase*dateancien)/100);
                    span.innerHTML = '<strong>' + Math.round((salarybase*dateancien)/100).toLocaleString() + ' FCFA / ' + dateancien + ' ans </strong>';
                }else{
                    amount.value =  Math.round((((salarybase*dateancien)/100)/30)*nbre_jour);
                    montant.value = Math.round((salarybase*dateancien)/100);
                    input.value = Math.round((((salarybase*dateancien)/100)/30)*nbre_jour);
                    span.innerHTML = '<strong>' + Math.round((salarybase*dateancien)/100).toLocaleString() + ' FCFA / ' + dateancien + ' ans </strong>';
                }
            }else{
                if(nbre_jour == 30){
                    amount.value =  Math.round((salarybase*25)/100);
                    montant.value = Math.round((salarybase*25)/100);
                    input.value = Math.round((salarybase*25)/100);
                    span.innerHTML = '<strong>' + Math.round((salarybase*25)/100).toLocaleString() + ' FCFA / ' + dateancien + ' ans </strong>';
                }else{
                    amount.value =  Math.round((((salarybase*25)/100)/30)*nbre_jour);
                    montant.value = Math.round((salarybase*25)/100);
                    input.value = Math.round((((salarybase*25)/100)/30)*nbre_jour);
                    span.innerHTML = '<strong>' + Math.round((salarybase*25)/100).toLocaleString() + ' FCFA / ' + dateancien + ' ans </strong>';
                }
            }
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            montant.hidden = true;
            worsk.style.display = 'none';
            spaninfo.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp105() {
        var checkbox = document.getElementById('option105');
        var span = document.getElementById('value105');
        var spaninfo = document.getElementById('info105');
        var item_brut = document.getElementById("item_brut105");
        var amount = document.getElementById('amount105');
        var montant = document.getElementById('montant105');
        var worsk = document.getElementById('choix_prorata105');
        var input = document.getElementById("amount_imp105");
        if (checkbox.checked) {
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut105');
            if(nbre_jour == 30){
                amount.value = Math.round((salarybase*75)/100);
                input.value = Math.round((salarybase*75)/100);
                montant.value = Math.round((salarybase*75)/100);
                span.innerHTML = '<strong>' + Math.round((salarybase*75)/100).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value = Math.round((((salarybase*75)/100)/30)*nbre_jour);
                input.value = Math.round((((salarybase*75)/100)/30)*nbre_jour);
                montant.value = Math.round((salarybase*75)/100);
                span.innerHTML = '<strong>' + Math.round((salarybase*75)/100).toLocaleString() + ' FCFA</strong>';
            }
            spaninfo.style.display = 'block';
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'none';
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp107() {
        var checkbox = document.getElementById('option107');
        var span = document.getElementById('value107');
        var spaninfo = document.getElementById('info107');
        var item_brut = document.getElementById("item_brut107");
        var amount = document.getElementById('amount107');
        var montant = document.getElementById('montant107');
        var input = document.getElementById("amount_imp107");
        var worsk = document.getElementById('choix_prorata107');
        if (checkbox.checked) {
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut107');
            if(nbre_jour == 30){
                amount.value = Math.round(13*(75000/173.33));
                input.value = Math.round(13*(75000/173.33));
                montant.value = Math.round(13*(75000/173.33));
                span.innerHTML = '<strong>' + Math.round(13*(75000/173.33)).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value = Math.round(((13*(75000/173.33))/30)*nbre_jour);
                input.value = Math.round(((13*(75000/173.33))/30)*nbre_jour);
                montant.value = Math.round(13*(75000/173.33));
                span.innerHTML = '<strong>' + Math.round(13*(75000/173.33)).toLocaleString() + ' FCFA</strong>';
            }
            spaninfo.style.display = 'block';
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'none';
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function panierjours() {
        //var checkbox = document.getElementById('option110');
        var span = document.getElementById('value110');
        var amount = document.getElementById('amount110');
        var montant = document.getElementById('montant110');
        var input = document.getElementById("amount_imp110");
        var nbrjours = document.getElementById('nbrjours').value;
        if (nbrjours > 0) {
            amount.value = Math.round(3*(75000/173.33))*nbrjours;
            montant.value = Math.round(3*(75000/173.33))*nbrjours;
            input.value = Math.round(3*(75000/173.33))*nbrjours;
        } else {
            amount.value = Math.round(3*(75000/173.33))
            montant.value = Math.round(3*(75000/173.33))
            input.value = Math.round(3*(75000/173.33))
        }
    }

    function afficherChamp110() {
        var checkbox = document.getElementById('option110');
        var span = document.getElementById('value110');
        var spaninfo = document.getElementById('info110');
        var item_brut = document.getElementById("item_brut110");
        var span2 = document.getElementById('total');
        var span3 = document.getElementById('total_prorata');
        var amount = document.getElementById('amount110');
        var montant = document.getElementById('montant110');
        var worsk = document.getElementById('choix_prorata110');
        var nbrjours = document.getElementById('nbrjours');
        if (checkbox.checked) {
            amount.hidden = false;
            montant.hidden = false;
            nbrjours.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut110');
            span.innerHTML = '<strong>Nb de jrs(' + Math.round(3*(75000/173.33)).toLocaleString() + ' FCFA / jrs).</strong>';
            span2.innerHTML = '<strong>Total</strong>';
            span3.innerHTML = '<strong>Total</strong>';
            spaninfo.style.display = 'block';
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            span2.innerHTML = '';
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            spaninfo.style.display = 'none';
            nbrjours.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp111() {
        var checkbox = document.getElementById('option111');
        var span = document.getElementById('value111');
        var spaninfo = document.getElementById('info111');
        var item_brut = document.getElementById("item_brut111");
        var amount = document.getElementById('amount111');
        var montant = document.getElementById('montant111');
        var worsk = document.getElementById('choix_prorata111');
        var salarybrut = document.getElementById('salarybrut').value;
        var input = document.getElementById("amount_imp111");
        var brut_span = document.getElementById("brut_update");
        if (checkbox.checked) {
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut111');
            spaninfo.style.display = 'block';
            if(city == 'ABIDJAN' || city == 'Abidjan' || $city == 'abidjan'){
                if(nbre_jour == 30){
                    amount.value = 30000;
                    input.value = 30000;
                    montant.value = 30000;
                    span.innerHTML = '<strong>30 000 FCFA - ' + city +' | Nombre de jours travaillés : '+nbre_jour+' </strong>';
                    //total_brut = (parseInt(salarybrut) + parseInt(input.value));
                    //brut_span.innerHTML = total_brut.toLocaleString() + ' FCFA';
                }else{
                    amount.value = ((30000/30)*nbre_jour);
                    input.value = ((30000/30)*nbre_jour);
                    montant.value = 30000;
                    span.innerHTML = '<strong>30 000 FCFA - ' + city +' | Nombre de jours travaillés : '+nbre_jour+'</strong>';
                    //total_brut = (parseInt(salarybrut) + parseInt(input.value));
                    //brut_span.innerHTML = total_brut.toLocaleString() + ' FCFA';
                }
            }else if(city == 'Bouaké' || city == 'bouaké' || $city == 'BOUAKE'){
                if(nbre_jour == 30){
                    amount.value =24000;
                    input.value = 24000;
                    montant.value = 24000;
                    span.innerHTML = '<strong>24 000 FCFA - ' + city + ' | Nombre de jours travaillés : '+nbre_jour+'</strong>';
                    //total_brut = (parseInt(salarybrut) + parseInt(input.value));
                    //brut_span.innerHTML = total_brut.toLocaleString() + ' FCFA';
                }else{
                    amount.value = ((24000/30)*nbre_jour);
                    input.value = ((24000/30)*nbre_jour);
                    montant.value = 24000;
                    span.innerHTML = '<strong>24 000 FCFA - ' + city + ' | Nombre de jours travaillés : '+nbre_jour+'</strong>';
                    //total_brut = (parseInt(salarybrut) + parseInt(input.value));
                    //brut_span.innerHTML = total_brut.toLocaleString() + ' FCFA';
                }
            }else{
                if(nbre_jour == 30){
                    amount.value = 22000;
                    input.value = 22000;
                    montant.value = 22000;
                    span.innerHTML = '<strong>22 000 FCFA - ' + city +' | Nombre de jours travaillés : '+nbre_jour+'</strong>'
                    //total_brut = (parseInt(salarybrut) + parseInt(input.value));
                    //brut_span.innerHTML = total_brut.toLocaleString() + ' FCFA';
                }else{
                    amount.value = ((22000/30)*nbre_jour);
                    input.value = ((22000/30)*nbre_jour);
                    montant.value = 22000;
                    span.innerHTML = '<strong>22 000 FCFA - ' + city +' | Nombre de jours travaillés : '+nbre_jour+'</strong>'
                    //total_brut = (parseInt(salarybrut) + parseInt(input.value));
                    //brut_span.innerHTML = total_brut.toLocaleString() + ' FCFA';
                }
            }
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'none';
            item_brut.setAttribute('name', '');
            worsk.style.display = 'none';
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp112() {
        var checkbox = document.getElementById('option112');
        var span = document.getElementById('value112');
        var spaninfo = document.getElementById('info112');
        var item_brut = document.getElementById("item_brut112");
        var amount = document.getElementById('amount112');
        var montant = document.getElementById('montant112');
        var worsk = document.getElementById('choix_prorata112');
        var input = document.getElementById("amount_imp112");
        if (checkbox.checked) {
            spaninfo.style.display = 'block';
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut112');
            if(nbre_jour == 30){
                amount.value = Math.round(10*(75000/173.33));
                input.value = Math.round(10*(75000/173.33));
                montant.value = Math.round(10*(75000/173.33));
                span.innerHTML = '<strong>' + Math.round(10*(75000/173.33)).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value = Math.round(((10*(75000/173.33))/30)*nbre_jour);
                input.value = Math.round(((10*(75000/173.33))/30)*nbre_jour);
                montant.value = Math.round(10*(75000/173.33));
                span.innerHTML = '<strong>' + Math.round(10*(75000/173.33)).toLocaleString() + ' FCFA</strong>';
            }
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'none';
            item_brut.setAttribute('name', '');
            worsk.style.display = 'none';
            amount.hidden = true;montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp113() {
        var checkbox = document.getElementById('option113');
        var span = document.getElementById('value113');
        var spaninfo = document.getElementById('info113');
        var item_brut = document.getElementById("item_brut113");
        var amount = document.getElementById('amount113');
        var montant = document.getElementById('montant113');
        var worsk = document.getElementById('choix_prorata113');
        var input = document.getElementById("amount_imp113");
        if (checkbox.checked) {
            spaninfo.style.display = 'block';
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut113');
            if(nbre_jour == 30){
                amount.value = Math.round(7*(75000/173.33));
                input.value = Math.round(7*(75000/173.33));
                montant.value = Math.round(7*(75000/173.33));
                span.innerHTML = '<strong>' + Math.round(7*(75000/173.33)).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value = Math.round(((7*(75000/173.33))/30)*nbre_jour);
                input.value = Math.round(((7*(75000/173.33))/30)*nbre_jour);
                montant.value = Math.round(7*(75000/173.33));
                span.innerHTML = '<strong>' + Math.round(7*(75000/173.33)).toLocaleString() + ' FCFA</strong>';
            }
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            worsk.style.display = 'none';
            spaninfo.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp114() {
        var checkbox = document.getElementById('option114');
        var span = document.getElementById('value114');
        var spaninfo = document.getElementById('info114');
        var item_brut = document.getElementById("item_brut114");
        var amount = document.getElementById('amount114');
        var montant = document.getElementById('montant114');
        var input = document.getElementById("amount_imp114");
        var worsk = document.getElementById('choix_prorata114');
        if (checkbox.checked) {
            spaninfo.style.display = 'block';
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut114');
            if(nbre_jour == 30){
                amount.value =  Math.round(salary/173,33);
                input.value = Math.round(salary/173,33);
                montant.value = Math.round(salary/173,33);
                span.innerHTML = '<strong>' + Math.round(salary/173,33).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value =  Math.round(((salary/173,33)/30)*nbre_jour);
                input.value = Math.round(((salary/173,33)/30)*nbre_jour);
                montant.value = Math.round(salary/173,33);
                span.innerHTML = '<strong>' + Math.round(salary/173,33).toLocaleString() + ' FCFA</strong>';
            }
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            worsk.style.display = 'none';
            spaninfo.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp115() {
        var checkbox = document.getElementById('option115');
        var span = document.getElementById('value115');
        var spaninfo = document.getElementById('info115');
        var worsk = document.getElementById('choix_prorata115');
        var item_brut = document.getElementById("item_brut115");
        var amount = document.getElementById('amount115');
        var montant = document.getElementById('montant115');
        var input = document.getElementById("amount_imp115");
        if (checkbox.checked) {
            spaninfo.style.display = 'block';
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut115');
            if(nbre_jour == 30){
                amount.value = Math.round((salary*75)/100);
                input.value = Math.round((salary*75)/100);
                montant.value = Math.round((salary*75)/100);
                span.innerHTML = '<strong>' + Math.round((salary*75)/100).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value = Math.round((((salary*75)/100)/30)*nbre_jour);
                input.value = Math.round((((salary*75)/100)/30)*nbre_jour);
                montant.value = Math.round((salary*75)/100);
                span.innerHTML = '<strong>' + Math.round((salary*75)/100).toLocaleString() + ' FCFA</strong>';
            }
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'block';
            worsk.style.display = 'none';
            amount.hidden = true;
            montant.hidden = true;
            item_brut.setAttribute('name', '');
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp116() {
        var checkbox = document.getElementById('option116');
        var span = document.getElementById('value116');
        var spaninfo = document.getElementById('info116');
        var item_brut = document.getElementById("item_brut116");
        var worsk = document.getElementById('choix_prorata116');
        var amount = document.getElementById('amount116');
        var montant = document.getElementById('montant116');
        var input = document.getElementById("amount_imp116");
        if (checkbox.checked) {
            spaninfo.style.display = 'block';
            worsk.style.display = 'block';
            amount.hidden = false;
            montant.hidden = false;
            item_brut.setAttribute('name', 'item_brut116');
            if(nbre_jour == 30){
                amount.value = Math.round((salarybase*40)/100);
                input.value = Math.round((salarybase*40)/100);
                montant.value = Math.round((salarybase*40)/100);
                span.innerHTML = '<strong>' + Math.round((salarybase*40)/100).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value = Math.round((((salarybase*40)/100)/30)*nbre_jour);
                input.value = Math.round((((salarybase*40)/100)/30)*nbre_jour);
                montant.value = Math.round((salarybase*40)/100);
                span.innerHTML = '<strong>' + Math.round((salarybase*40)/100).toLocaleString() + ' FCFA</strong>';
            }
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'none';
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp117() {
        var checkbox = document.getElementById('option117');
        var span = document.getElementById('value117');
        var spaninfo = document.getElementById('info117');
        var item_brut = document.getElementById("item_brut117");
        var worsk = document.getElementById('choix_prorata117');
        var amount = document.getElementById('amount117');
        var montant = document.getElementById('montant117');
        var input = document.getElementById("amount_imp117");
        if (checkbox.checked) {
            spaninfo.style.display = 'block';
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut117');
            if(nbre_jour == 30){
                amount.value = Math.round(((((salarybase*75)/100)*3)/100) + (salarybase/173.33));
                input.value =  Math.round(((((salarybase*75)/100)*3)/100) + (salarybase/173.33));
                montant.value = Math.round(((((salarybase*75)/100)*3)/100) + (salarybase/173.33));
                span.innerHTML = '<strong>' + Math.round(((((salarybase*75)/100)*3)/100) + (salarybase/173.33)).toLocaleString() + ' FCFA</strong>';
            }else{
                amount.value = Math.round(((((((salarybase*75)/100)*3)/100) + (salarybase/173.33))/30)*nbre_jour);
                input.value =  Math.round(((((((salarybase*75)/100)*3)/100) + (salarybase/173.33))/30)*nbre_jour);
                montant.value = Math.round(((((salarybase*75)/100)*3)/100) + (salarybase/173.33));
                span.innerHTML = '<strong>' + Math.round(((((salarybase*75)/100)*3)/100) + (salarybase/173.33)).toLocaleString() + ' FCFA</strong>';
            }
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'none';
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp126() {
        var checkbox = document.getElementById('option126');
        var span = document.getElementById('value126');
        var spaninfo = document.getElementById('info126');
        var item_brut = document.getElementById("item_brut126");
        var amount = document.getElementById('amount126');
        var worsk = document.getElementById('choix_prorata126');
        var montant = document.getElementById('montant126');
        var input = document.getElementById("amount_imp126");
        if (checkbox.checked) {
            spaninfo.style.display = 'block';
            amount.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut126');
            amount.value = Math.round(enfts*1500);
            montant.value = '';
            input.value = Math.round(enfts*1500);
            span.innerHTML = '<strong>' + Math.round(enfts*1500).toLocaleString() + ' FCFA</strong>';
            cpte++
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            span.innerHTML = '';
            spaninfo.style.display = 'block';
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            amount.hidden = true;
            montant.hidden = true;
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }

    }

    function afficherChamp101() {
        var checkbox = document.getElementById('option101');
        var input = document.getElementById('amount101');
        var montant = document.getElementById('montant101');
        var item_brut = document.getElementById("item_brut101");
        var worsk = document.getElementById('choix_prorata101');
        var spaninfo = document.getElementById('info101');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut101');
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
            //total_brut += parseInt(input.value);
            //brut_span.innerHTML = total_brut.toLocaleString() + ' FCFA';
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp106() {
        var checkbox = document.getElementById('option106');
        var input = document.getElementById('amount106');
        var montant = document.getElementById('montant106');
        var item_brut = document.getElementById("item_brut106");
        var worsk = document.getElementById('choix_prorata106');
        var spaninfo = document.getElementById('info106');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            spaninfo.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut106');
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp108() {
        var checkbox = document.getElementById('option108');
        var input = document.getElementById('amount108');
        var montant = document.getElementById('montant108');
        var item_brut = document.getElementById("item_brut108");
        var spaninfo = document.getElementById('info108');
        var worsk = document.getElementById('choix_prorata108');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut108');
            cpte++;
            spaninfo.style.display = 'block';
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';'';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp109() {
        var checkbox = document.getElementById('option109');
        var input = document.getElementById('amount109');
        var montant = document.getElementById('montant109');
        var item_brut = document.getElementById("item_brut109");
        var spaninfo = document.getElementById('info109');
        var worsk = document.getElementById('choix_prorata109');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            spaninfo.style.display = 'block';
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut109');
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            item_brut.setAttribute('name', '');
            worsk.style.display = 'none';
            cpte--;
            spaninfo.style.display = 'none';
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp118() {
        var checkbox = document.getElementById('option118');
        var input = document.getElementById('amount118');
        var montant = document.getElementById('montant118');
        var item_brut = document.getElementById("item_brut118");
        var spaninfo = document.getElementById('info118');
        var worsk = document.getElementById('choix_prorata118');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            spaninfo.style.display = 'block';
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut118');
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp119() {
        var checkbox = document.getElementById('option119');
        var input = document.getElementById('amount119');
        var montant = document.getElementById('montant119');
        var item_brut = document.getElementById("item_brut119");
        var worsk = document.getElementById('choix_prorata119');
        var spaninfo = document.getElementById('info119');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            spaninfo.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut119');
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp120() {
        var checkbox = document.getElementById('option120');
        var input = document.getElementById('amount120');
        var montant = document.getElementById('montant120');
        var worsk = document.getElementById('choix_prorata120');
        var item_brut = document.getElementById("item_brut120");
        var spaninfo = document.getElementById('info120');

        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            spaninfo.style.display = 'block';
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut120');
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp121() {
        var checkbox = document.getElementById('option121');
        var input = document.getElementById('amount121');
        var montant = document.getElementById('montant121');
        var worsk = document.getElementById('choix_prorata121');
        var item_brut = document.getElementById("item_brut121");
        var spaninfo = document.getElementById('info121');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            spaninfo.style.display = 'block';
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut121');
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp122() {
        var checkbox = document.getElementById('option122');
        var input = document.getElementById('amount122');
        var montant = document.getElementById('montant122');
        var worsk = document.getElementById('choix_prorata122');
        var item_brut = document.getElementById("item_brut122");
        var spaninfo = document.getElementById('info122');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            spaninfo.style.display = 'block';
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut122');
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            worsk.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp123() {
        var checkbox = document.getElementById('option123');
        var input = document.getElementById('amount123');
        var montant = document.getElementById('montant123');
        var worsk = document.getElementById('choix_prorata123');
        var item_brut = document.getElementById("item_brut123");
        var spaninfo = document.getElementById('info123');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            spaninfo.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut123');
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp124() {
        var checkbox = document.getElementById('option124');
        var input = document.getElementById('amount124');
        var montant = document.getElementById('montant124');
        var worsk = document.getElementById('choix_prorata124');
        var item_brut = document.getElementById("item_brut124");
        var spaninfo = document.getElementById('info124');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut124');
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'block';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp125() {
        var checkbox = document.getElementById('option125');
        var input = document.getElementById('amount125');
        var montant = document.getElementById('montant125');
        var worsk = document.getElementById('choix_prorata125');
        var item_brut = document.getElementById("item_brut125");
        var spaninfo = document.getElementById('info125');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut125');
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp127() {
        var checkbox = document.getElementById('option127');
        var input = document.getElementById('amount127');
        var montant = document.getElementById('montant127');
        var worsk = document.getElementById('choix_prorata127');
        var item_brut = document.getElementById("item_brut127");
        var spaninfo = document.getElementById('info127');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut127');
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';'';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp128() {
        var checkbox = document.getElementById('option128');
        var input = document.getElementById('amount128');
        var montant = document.getElementById('montant128');
        var worsk = document.getElementById('choix_prorata128');
        var item_brut = document.getElementById("item_brut128");
        var spaninfo = document.getElementById('info28');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut128');
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp129() {
        var checkbox = document.getElementById('option129');
        var input = document.getElementById('amount129');
        var worsk = document.getElementById('choix_prorata129');
        var montant = document.getElementById('montant129');
        var item_brut = document.getElementById("item_brut129");
        var spaninfo = document.getElementById('info129');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut129');
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp131() {
        var checkbox = document.getElementById('option131');
        var input = document.getElementById('amount131');
        var worsk = document.getElementById('choix_prorata131');
        var montant = document.getElementById('montant131');
        var item_brut = document.getElementById("item_brut131");
        var spaninfo = document.getElementById('info131');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut131');
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }

    function afficherChamp134() {
        var checkbox = document.getElementById('option134');
        var input = document.getElementById('amount134');
        var worsk = document.getElementById('choix_prorata134');
        var montant = document.getElementById('montant134');
        var item_brut = document.getElementById("item_brut134");
        var spaninfo = document.getElementById('info134');
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut134');
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
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

    function afficherNew(i) {
        var checkbox = document.getElementById('option'+i);
        var input = document.getElementById('amount' + i);
        var worsk = document.getElementById('choix_prorata' + i);
        var montant = document.getElementById('montant' + i);
        var item_brut = document.getElementById('item_brut' + i);
        var spaninfo = document.getElementById('info' + i);
        if (checkbox.checked) {
            input.hidden = false;
            montant.hidden = false;
            worsk.style.display = 'block';
            item_brut.setAttribute('name', 'item_brut' + i);
            spaninfo.style.display = 'block';
            cpte++;
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }else{
            input.hidden = true;
            montant.hidden = true;
            worsk.style.display = 'none';
            item_brut.setAttribute('name', '');
            spaninfo.style.display = 'none';
            cpte--;
            if(cpte<0){cpte=0;}
            document.getElementById("cpte1").innerHTML = '<strong style="color: red;">Vous avez sélectionné ' + cpte +'  éléments</strong>';
            document.getElementById("cpte").value = cpte ;
        }
    }
</script>
                