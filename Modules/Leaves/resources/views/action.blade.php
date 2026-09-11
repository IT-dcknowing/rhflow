<form action="{{ route('company.leaves.changeaction', $leave->id) }}" method="POST">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-12">
                <table class="table modal-table mb-4" id="pc-dt-simple">
                    <tr role="row">
                        <th>{{ __('Employee') }}</th>
                        <td>{{ !empty($leave->employee->name) ? $leave->employee->name : '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Type de congé') }}</th>
                        <td>{{ !empty($leave->leavetype->title) ? $leave->leavetype->title : '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Envoyez le ') }}</th>
                        <td>{{ Carbon\Carbon::parse($leave->applied_on)->locale('fr')->isoFormat('DD MMMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Date de début') }}</th>
                        <td>{{ Carbon\Carbon::parse($leave->start_date)->locale('fr')->isoFormat('DD MMMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Date de fin') }}</th>
                        <td>{{ Carbon\Carbon::parse($leave->end_date)->locale('fr')->isoFormat('DD MMMM YYYY') }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Motif du départ') }}</th>
                        <td>{{ !empty($leave->leave_reason) ? $leave->leave_reason : '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Status') }}</th>
                        <td>
                            @if($leave->status == 'Pending')
                                <div class="mb-3">
                                    <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="Approuvé" {{ old('status', $leave->status) == 'Approuvé' ? 'selected' : '' }}>Approuvé</option>
                                        <option value="Rejeté" {{ old('status', $leave->status) == 'Rejeté' ? 'selected' : '' }}>Rejeté</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </td>
                    </tr>
                    <input type="hidden" value="{{ $leave->id }}" name="leave_id">
                </table>

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

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary rounded" data-bs-dismiss="modal">Fermer</button>
        @if($leave->status == 'Approuvé' || $leave->status == 'Rejeté' || $leave->status == 'Terminé')
            <button type="submit" class="btn btn-primary rounded" disabled>Valider</button>
        @else
            <button type="submit" class="btn btn-primary rounded">Valider</button>
        @endif
    </div>
</form>
