<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-4">
            <label for="nbre_jour" class="form-label">Nombre de jours d\'absences</label>    
            <input type="text" id="nbre_jour" class="form-control" name="nbre_jour" value="{{ $nbre_jour }}" step="0.01" readonly>
            <input type="hidden" id="countAllowances" name="countAllowances" value="{{ $employee->salary }}">
            <input type="hidden" id="employee_id" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" id="periode_id" name="periode_id" value="{{ $periodePaie->id }}">
        </div> 

        <div class="form-group col-md-4">
            <label for="tax_payer_id" class="form-label">Nombre de jours travaillés (30 par defaut)</label>
            <input type="number" id="tax_payer_id" class="form-control" name="tax_payer_id" oninput="salaryDay()" step="0.01">
        </div>
        
        <div class="form-group col-md-4">
            <label for="paytype" class="form-label">Sélectionner le mode de paiement</label>
            <select name="paytype" class="form-select" id="paytype"> 
                <option value="">-- Sélectionner un moyen de paiement --</option>
                @if (!empty($paytype))
                    @foreach ($paytype as $type)
                        <option value="{{ $type->id }}" @if ($employee->paytype == $type->id) selected @endif>
                            {{ $type->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        
        <div id="shoraire" class="form-group col-md-4">
            <label for="salary_horaire" class="form-label">Salaire catégoriel horaire</label> 
            <input type="number" id="salary_horaire" class="form-control" name="salary_horaire" value="{{ $employee->salary_horaire }}" readonly>
        </div> 
        
        <div id="smensuel" class="form-group col-md-4">
            <label for="salary_mensuel" class="form-label">Salaire catégoriel mensuel</label> 
            <input type="number" id="salary_mensuel" name="salary" class="form-control" value="{{ $employee->salary }}" required readonly>
        </div>

        <div class="form-group col-md-4">
            <label for="branch_location" class="form-label">S. catégoriel y compris les jours travaillés</label>
            <input type="text" name="branch_location" class="form-control" id="branch_location" value="{{ $employee->branch_location }}" readonly>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
    <button type="button" class="btn btn-primary" onclick="updateDaysWork({{ $employee->id }})">Enregistrer</button>
</div>
<script>
    var nbre_jour = parseFloat(document.getElementById("nbre_jour").value) || 0;
    var salary_base = parseInt(document.getElementById("countAllowances").value) || 0;
    var salary = document.getElementById("salary_mensuel"); 
    var salary_day = document.getElementById("branch_location");
    var jours = document.getElementById("tax_payer_id");
    var shoraire = parseFloat(document.getElementById("salary_horaire").value) || 0;
    var smensuel = parseFloat(document.getElementById("salary_mensuel").value) || 0;
    var test = 30 - nbre_jour;
    jours.value = test;

    // Proratisation sur une base de 30 jours : (salaire mensuel / 30) * jours travaillés
    function calculSalaireProratise(salaireMensuel, joursTravailles){
        if(joursTravailles >= 30 || joursTravailles <= 0){
            return salaireMensuel;
        }
        return Math.round((salaireMensuel / 30) * joursTravailles);
    }

    salary_day.value = calculSalaireProratise(salary_base, test);

    if(shoraire <= 0 && smensuel > 0){
        shoraire.value = (smensuel/173.33).toFixed(2);
    }

    function salaryDay(){
        var jours = parseInt(document.getElementById("tax_payer_id").value) || 0;
        var salary_base = parseInt(document.getElementById("salary_mensuel").value) || 0;

        if(jours > 30){
            alert('Le nombre de jours de travail par défaut est de 30 jours.')
            document.getElementById("tax_payer_id").value = '30';
            jours = 30;
        }

        // Le salaire catégoriel affiché tient compte des jours travaillés (cf. libellé du champ)
        salary_day.value = calculSalaireProratise(salary_base, jours);
    }
</script>
<script>
    function updateDaysWork(employeeId) {
        var employeeId = document.getElementById('employee_id').value;
        var tax_payer_id = document.getElementById('tax_payer_id').value;
        var paytype = document.getElementById('paytype').value;
        var branch_location = document.getElementById('branch_location').value;
        var periode_id = document.getElementById('periode_id').value;

        if (!tax_payer_id || tax_payer_id < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Le nombre de jours doit être supérieur ou égal à 0'
            });
            return;
        }
        
        Swal.fire({
            title: 'Êtes-vous sûr?',
            text: "Voulez-vous modifier le nombre de jours travaillés ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, modifier!',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("company.paiesalaries.update",":id") }}'.replace(':id', employeeId),
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id: employeeId,
                        tax_payer_id: tax_payer_id,
                        paytype: paytype,
                        branch_location: branch_location,
                        periode_id: periode_id
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
</script>