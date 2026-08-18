<form id="addRetenueToEmployeesForm" action="{{ route('company.paiesalaries.retenues.store-employees', $retenue->id) }}" method="POST">
    @csrf
    <input type="hidden" name="retenue_id" value="{{ $retenue->id }}">
    <input type="text" id="periode_id" name="periode_id" value="{{$retenue->periode_id}}" hidden="">
               
    <div class="mb-3">
        <h5 class="text-end text-danger">-{{ number_format($retenue->amount, 0, ',', ' ') }} FCFA</h5>
        <p class="text-muted text-center">Cette retenue est appliquée à <span class="badge bg-label-danger">{{ $retenueCount }}</span> employés</p>
        <label class="form-label">Sélectionnez les employés</label>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAllEmployees">
                            </div>
                        </th>
                        <th>N°</th>
                        <th>Nom</th>
                        <th>Succursale</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input employee-checkbox" type="checkbox" 
                                           name="employees[]" 
                                           value="{{ $employee->id }}"
                                           id="employee_{{ $employee->id }}"
                                           {{ $employee->retenues()->where('libelle', $retenue->libelle)->exists() ? 'checked disabled' : '' }}
                                           data-has-retenue="{{ $employee->retenues()->where('libelle', $retenue->libelle)->exists() ? 'true' : 'false' }}">
                                    @if($employee->retenues()->where('libelle', $retenue->libelle)->exists())
                                        <input type="hidden" name="employees[]" value="{{ $employee->id }}">
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-dark">
                                    {{ \Auth::user()->employeeIdFormat($employee->employee_id) }}
                                </span>
                            </td>                            
                            <td>
                                <label class="form-check-label" for="employee_{{ $employee->id }}">
                                    {{ $employee->name }}
                                </label><br>
                                <label class="text-muted">{{ $employee->designation->name ?? '-' }}</label><br>
                                <label class="text-muted">{{ $employee->department->name ?? '-' }}</label>
                            </td>
                            <td>{{ $employee->branch->name ?? '-' }}</td>
                            <td>{{ number_format($employee->get_Autre_retenue(), 0, ',', ' ') . ' FCFA' ?? '-' }}</td>
                            <td>
                                @php
                                    $employeeRetenue = $employee->retenues()->where('libelle', $retenue->libelle)->first();
                                @endphp
                                @if($employeeRetenue && $employeeRetenue->is_active)
                                    <span class="badge bg-label-success">Actif</span>
                                @elseif($employeeRetenue && !$employeeRetenue->is_active)
                                    <span class="badge bg-label-danger">Inactif</span>
                                @else
                                    <span class="badge bg-label-secondary">Non affecté</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    @php
                                        $employeeRetenue = $employee->retenues()->where('libelle', $retenue->libelle)->first();
                                    @endphp
                                    @if($employeeRetenue)
                                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        @if($employeeRetenue->is_active)
                                        <a class="dropdown-item" onclick="return confirm('Êtes-vous sûr de vouloir désactiver cette retenue ?')"
                                            href="{{ route('company.paiesalaries.retenues.deactivate.add', $employeeRetenue->id) }}">
                                            <i class="fas fa-times me-1"></i>Désactiver la retenue
                                        </a>
                                        @else
                                        <a class="dropdown-item" onclick="return confirm('Êtes-vous sûr de vouloir activer cette retenue ?')"
                                            href="{{ route('company.paiesalaries.retenues.activate.add', $employeeRetenue->id) }}">
                                            <i class="fas fa-check me-1"></i>Activer la retenue
                                        </a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette retenue ?')"
                                            href="{{ route('company.paiesalaries.retenues.destroy.add', $employeeRetenue->id) }}">
                                            <i class="fas fa-trash-alt me-2"></i>Supprimer
                                        </a>
                                    </div>
                                    @else
                                        <button type="button" class="btn btn-light btn-sm dropdown-toggle disabled" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Aucun employé trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Enregistrer
        </button>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        // Sélectionner/désélectionner tous les employés
        $('#selectAllEmployees').on('change', function() {
            $('.employee-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Si un employé est décoché, décocher la case "Tout sélectionner"
        $('.employee-checkbox').on('change', function() {
            if (!$(this).prop('checked')) {
                $('#selectAllEmployees').prop('checked', false);
            } else {
                // Vérifier si tous les employés sont sélectionnés
                var allChecked = $('.employee-checkbox:checked').length === $('.employee-checkbox').length;
                $('#selectAllEmployees').prop('checked', allChecked);
            }
        });

        // Soumission du formulaire en AJAX
        $('#addRetenueToEmployeesForm').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = form.serialize();
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Succès',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#addEmployeeRetenueModal').modal('hide');
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON?.message || 'Une erreur est survenue lors de l\'ajout de la retenue aux employés';
                    Swal.fire({
                        title: 'Erreur',
                        text: errorMessage,
                        icon: 'error'
                    });
                }
            });
        });
    });
</script>
@endpush