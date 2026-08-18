<?php
	$nbre_jour = 0;
    $heures = 0;
    foreach ($timesheets as $timeSheetnbre){
            $heures = $heures + $timeSheetnbre->hours;
    }
    $nbre_jour = $heures;
?>
{{ Form::model($employee, ['route' => ['employee.salary.update', $employee->id], 'method' => 'POST']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-4">
            {!! Form::label('nbrejour', __('Nombre de jours d\'absences'), ['class' => 'form-label']) !!}    
            <input type="text" id="nbre_jour" class="form-control" name="nbre_jour" value="{{$nbre_jour}}" step="0.01" readonly>
            <input type="text" id="countAllowances" name="countAllowances" value="{{$employee->salary}}" hidden="">
            <input type="text" id="secteur_id" name="secteur_id" value="{{\Utility::getValByName('secteur_activite')}}" hidden="">
            <input type="text" id="id_secteur" name="id_secteur" value="{{\Utility::getValByName('secteur_activite')}}" hidden="">
        </div>
  
        <div class="form-group col-md-4">
            {!! Form::label('nbrjrs', __('Nombre de jours travaillés (30 par defaut)'), ['class' => 'form-label']) !!}
            <input type="number" id="tax_payer_id" class="form-control" name="tax_payer_id" oninput="salaryDay()" step="0.01">
        </div>

        <div class="form-group col-md-4">
            {{ Form::label('salary_type', __('Payslip Type'), ['class' => 'form-label']) }}*
            {{ Form::select('salary_type', $payslip_type, null, ['class' => 'form-control select2', 'required' => 'required', 'onchange'=>'affichesalaire()']) }}
        </div>
        
        <div id="shoraire" class="form-group col-md-4" style="display : none;">
            {{ Form::label('salary_horaire', __('Salaire catégoriel horaire'), ['class' => 'form-label']) }} 
            {{ Form::number('salary_horaire', null, ['class' => 'form-control']) }}
        </div> 
        <div id="smensuel" class="form-group col-md-4" style="display : none";>
            {{ Form::label('label_salary', __('Salaire catégoriel mensuel'), ['class' => 'form-label']) }} 
            <input type="number" id="salary_mensuel" name="salary" class="form-control" value="{{$employee->salary}}" required readonly>
        </div>
        <div class="form-group col-md-4">
            {!! Form::label('branch_location_for', __('S. catégoriel y compris les jours travaillés'), ['class' => 'form-label']) !!}
            {!! Form::text('branch_location', old('branch_location'), ['class' => 'form-control', 'id'=>'branch_location']) !!}
        </div>
		<div class="form-group col-md-4">
			 {{ Form::label('paytype', __('Sélectionner le mode de paiement'), ['class' => 'form-label']) }}
			<select type="text" name="paytype" class="form-control" id="paytype"> 
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
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Retour" class="btn btn-light" data-bs-dismiss="modal">
    <button type="submit" class="btn  btn-primary">{{ __('Save') }}</button>
</div>

<script>
    var nbre_jour = parseFloat(document.getElementById("nbre_jour").value);
    var salary_base = parseInt(document.getElementById("countAllowances").value);
    var salary = document.getElementById("salary_mensuel"); 
    var salary_day = document.getElementById("branch_location");
    var jours = document.getElementById("tax_payer_id");
    var shoraire = document.getElementById("shoraire");
    var smensuel = document.getElementById("smensuel");
    var salary_type = document.getElementById("salary_type");
    var test = 30 - nbre_jour;
    jours.value = test;
    salary_day.value = salary_base;
    
    if(salary_type.value == "1"){
        shoraire.style.display = "none";
        smensuel.style.display = "block";
    }else{
        shoraire.style.display = "block";
        smensuel.style.display = "none";
    }

    function salaryDay(){
        var jours = parseInt(document.getElementById("tax_payer_id").value);
        var nbre_jour = parseFloat(document.getElementById("nbre_jour").value);
        var salary_base = parseInt(document.getElementById("salary_mensuel").value); 
        var test = 30 - nbre_jour; 
        
        if(jours > 30){
            alert('Le nombre de jours de travail par est de 30 jours.')
            document.getElementById("tax_payer_id").value = '30';
        }
        
        salary_day.value = salary_base;
    }

    function affichesalaire(){
        if(salary_type.value == "1"){
            shoraire.style.display = "none";
            smensuel.style.display = "block";
        }else{
            shoraire.style.display = "block";
            smensuel.style.display = "none";
        }
    }
</script>
<script>
    var header = document.getElementById("myDIV");
    var btns = header.getElementsByClassName("btn");
    for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click", function() {
    var current = document.getElementsByClassName("active");
    current[0].className = current[0].className.replace(" active", "");
    this.className += " active";
    });
    }
</script>
{{ Form::close() }}
