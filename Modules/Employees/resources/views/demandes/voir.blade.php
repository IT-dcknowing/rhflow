


<!-- Modal de détails de la demande -->
<div class="modal-lg">
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Type de demande</label>
                    <div>{{ getDemandeTypeLabel($demande->demande_type_id) }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Catégorie</label>
                    <div>{{ getCategorieLabel($demande->categorie_id) }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Période</label>
                    <div>Du {{ \Carbon\Carbon::parse($demande->start_date)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($demande->end_date)->format('d/m/Y') }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Statut actuel</label>
                    <div><span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Créée par</label>
                    <div>{{ $employee->employeeName() }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Date de création</label>
                    <div>{{ \Carbon\Carbon::parse($demande->created_at)->format('d/m/Y H:i') }}</div>
                </div>
                @if($demande->montant)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant</label>
                        <div>{{ number_format($demande->montant, 0, '.', ' ') }} XOF</div>
                    </div>
                @endif
            </div>
        </div>

        @if($demande->demande_reason)
            <div class="mb-3">
                <label class="form-label fw-bold">Motif de la demande</label>
                <div class="p-3 bg-light rounded">
                    {!! nl2br(e($demande->demande_reason)) !!}
                </div>
            </div>
        @endif

        @if($demande->file_path)
            <div class="mb-3">
                <label class="form-label fw-bold">Document joint</label>
                <div>
                    <a href="{{ asset('storage/app/public/' . $demande->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-download me-1"></i> Télécharger
                    </a>
                </div>
            </div>
        @endif
    </div>
    <div class="modal-footer justify-content-between">
        <div>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        </div>
        @if ($demande->status == 'Pending')
            <div>
                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Approved">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check-circle me-1"></i> Valider
                    </button>
                </form>
                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Rejected">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times-circle me-1"></i> Refuser
                    </button>
                </form>
            </div>
            @elseif ($demande->status == 'Approved')
            <div>
                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Pending">
                    <button type="submit" class="btn btn-info disabled">
                        <i class="fa fa-clock me-1"></i> En attente
                    </button>
                </form>
            </div>
        @elseif ($demande->status == 'Rejected')
            <div>
                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Pending">
                    <button type="submit" class="btn btn-info disabled">
                        <i class="fa fa-clock me-1"></i> En attente
                    </button>
                </form>
                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Approved">
                    <button type="submit" class="btn btn-success disabled">
                        <i class="fa fa-check-circle me-1"></i> Valider
                    </button>
                </form>
            </div>
        @else
            <div>
                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Pending">
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-clock me-1"></i> En attente
                    </button>
                </form>
                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Rejected">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times-circle me-1"></i> Refuser
                    </button>
                </form>
                <form action="{{ route('demandes.updateStatus', $demande->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="Approved">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check-circle me-1"></i> Valider
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
