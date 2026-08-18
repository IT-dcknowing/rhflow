
<div class="modal-xl">
    {{ Form::model($employee, ['route' => ['setsalary.allowanceStore'], 'method' => 'POST']) }}
    <div class="modal-body">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Nom de la retenue</label>
                    <input type="text" name="lib_retenue" class="form-control" required>
                    <input type="hidden" name="monthpaie" class="form-control" value="{{ $monthpaie }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Type de retenue</label>
                    <select name="type_retenue" id="type_retenue" class="form-select " required>
                            <option value="">--</option>
                            <option value="Oppositions">Oppositions</option>
                            <option value="Saisies-Arrêts">Saisies-Arrêt</option>
                            <option value="Avis à tiers détenteur">Avis à tiers détenteur</option>
                            <option value="Prélèvement pour assurance">Prélèvement pour assurance</option>
                            <option value="Rétrocession de retenue">Rétrocession de retenue</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('employee', __('Sélectionné un employé'), ['class' => 'form-label']) }}
                    @foreach($employee as $emp)
                        @if ($emp->id == $id)
                            <input type="text" name="employee_name"  class="form-control" value="{{$emp->name}}" readonly required>
                            <input type="hidden" name="employee_id"  class="form-control" value="{{$emp->id}}">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="form-group col-md-12">
            <label class="form-label">Montant</label>
            <input type="number" name="montant"  class="form-control" step="0.01" required>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="Retour" class="btn btn-light" data-bs-dismiss="modal">
        <button type="submit" class="btn  btn-primary">{{ __('Save') }}</button>
    </div>
</div>
