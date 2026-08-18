{{ Form::model($retenue, ['route' => ['setsalary.updateAutreRetenue', $retenue->id], 'method' => 'PUT']) }}
<div class="modal-body">
    @csrf
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label">Nom de la retenue</label>
                <input type="text" name="lib_retenue" value="{{ $retenue->lib_retenue }}" class="form-control" required>
                <input type="hidden" name="monthpaie" class="form-control" value="{{ $retenue->monthpaie }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label">Type de retenue</label>
                <select name="type_retenue" id="type_retenue" class="form-control " required>
                        @if($retenue->type_retenue)
                            <option value="{{ $retenue->type_retenue }}">{{ $retenue->type_retenue }}</opttion>
                        @endif
                        <option value="Oppositions">Oppositions</opttion>
                        <option value="Saisies-Arrêts">Saisies-Arrêt</opttion>
                        <option value="Avis à tiers détenteur">Avis à tiers détenteur</opttion>
                        <option value="Prélèvement pour assurance">Prélèvement pour assurance</opttion>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {{ Form::label('employee', __('Sélectionné un employé'), ['class' => 'form-label']) }}
                <div class="employee_div">
                    <select name="employee_id" id="employee_id" class="form-control " required>
                        @foreach($employee as $emp)
                            @if($emp->id == $retenue->employee_id)
                                <option value="{{ $retenue->employee_id }}">{{ $emp->name }}</option>
                            @endif
                            @if($emp->id != $retenue->employee_id) 
                                <option value="{{$emp->id}}">{{$emp->name}}</opttion>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
     <div class="form-group col-md-12">
        <label class="form-label">Montant</label>
        <input type="number" name="montant" value="{{ $retenue->montant }}"  class="form-control" step="0.01" required>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Retour" class="btn btn-light" data-bs-dismiss="modal">
    <button type="submit" class="btn  btn-primary">{{ __('Save') }}</button>
</div>