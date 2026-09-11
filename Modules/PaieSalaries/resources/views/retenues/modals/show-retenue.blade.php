<div class="modal-body">
    <div class="row mb-4">
        <div class="col-md-6">
            <h6>Informations de la retenue</h6>
            <table class="table table-bordered">
                <tr>
                    <th width="40%">Libellé</th>
                    <td>{{ $retenue->libelle }}</td>
                </tr>
                <tr>
                    <th>Code</th>
                    <td>{{ $retenue->code ?? '-' }}</td>
                </tr>
                <tr>
                    @if($retenue->base)
                    <th>Base/Taux</th>
                    <td><span class="badge bg-label-success">{{ $retenue->base }} - {{ $retenue->taux }} %</span></td>
                    @endif
                    @if($retenue->amount)
                    <th>Montant</th>
                    <td><span class="badge bg-label-success">{{ number_format($retenue->amount, 0, ',', ' ') }} FCFA</span></td>
                    @endif
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{ $retenue->typeRetenue->libelle ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Statut</th>
                    <td>
                        @if($retenue->is_active)
                            <span class="badge bg-success">Activée</span>
                        @else
                            <span class="badge bg-danger">Désactivée</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <h6>Période</h6>
            <table class="table table-bordered">
                <tr>
                    <th width="40%">Période</th>
                    <td>{{ $retenue->periode->nom ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Date d'application</th>
                    <td>{{ $retenue->date_application ? \Carbon\Carbon::parse($retenue->date_application)->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <th>Mois de paie</th>
                    <td>{{ $retenue->month_paie ? \Carbon\Carbon::createFromFormat('Y-m', $retenue->month_paie)->format('m/Y') : '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <h6>Employés concernés ({{ $retenueEmployees->count() }})</h6>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom complet</th>
                        <th>Montant</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($retenueEmployees as $employee)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $employee->name }}</td>
                            <td><span class="badge bg-label-danger">{{ number_format($retenue->amount, 0, ',', ' ') }} FCFA</span></td>
                            <td>
                                @if($retenue->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-danger">Inactif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun employé associé à cette retenue</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
</div>

@push('scripts')
<script>
    // Initialisation des tooltips Bootstrap
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush