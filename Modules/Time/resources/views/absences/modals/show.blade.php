<!-- En-tête avec photo et informations principales -->
<div class="mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <div class="avatar avatar-xl me-3">
                <span class="avatar-initial rounded-circle bg-label-primary">
                    <i class="fas fa-user fs-4"></i>
                </span>
            </div>
            <div>
                <h4 class="mb-0">{{ $absence->name }}</h4>
                <span class="text-muted">Demande d'absence</span>
            </div>
            <div class="ms-auto">
                @php
                    $badgeClass = [
                        'pending' => 'bg-label-warning',
                        'approved' => 'bg-label-success',
                        'rejected' => 'bg-label-danger'
                    ][$absence->statut] ?? 'bg-label-secondary';
                    
                    $statusText = [
                        'pending' => 'En attente',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté'
                    ][$absence->statut] ?? 'Inconnu';
                @endphp
                <span class="badge {{ $badgeClass }} fs-6 fw-normal">{{ $statusText }}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="badge bg-label-primary rounded p-2 me-3">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Date de départ</p>
                        <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="badge bg-label-info rounded p-2 me-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Date de reprise</p>
                        <p class="mb-0 fw-semibold">{{ \Carbon\Carbon::parse($absence->arrival_date)->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="badge bg-label-success rounded p-2 me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Durée</p>
                        <p class="mb-0 fw-semibold">{{ $absence->hours }} heures ({{ $absence->retenue }} jour(s))</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="badge bg-label-secondary rounded p-2 me-3">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Type d'absence</p>
                        <p class="mb-0 fw-semibold">{{ $absence->type_permis ?: 'Non spécifié' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Détails supplémentaires -->
<div class="row">
    <!-- Motif -->
    <div class="col-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">
                    <i class="fas fa-sticky-note me-2"></i>Motif de l'absence
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $absence->remark ?: 'Aucun motif renseigné' }}</p>
            </div>
        </div>
    </div>
    
    <!-- Document justificatif -->
    <div class="col-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">
                    <i class="fas fa-file-text me-2"></i>Document justificatif
                </h5>
            </div>
            <div class="card-body">
                @if ($absence->document)
                <div class="d-flex align-items-center">
                    <div class="badge bg-label-primary rounded p-2 me-3">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0">Document joint</p>
                        <small class="text-muted">{{ basename($absence->document) }}</small>
                    </div>
                    <a href="{{ asset('storage/' . $absence->document) }}" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-download me-1"></i> Télécharger
                    </a>
                </div>
                @else
                <div class="text-center py-4">
                    <div class="avatar avatar-lg mb-2">
                        <span class="avatar-initial rounded-circle bg-label-secondary">
                            <i class="fas fa-file-off"></i>
                        </span>
                    </div>
                    <p class="text-muted mb-0">Aucun document joint</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Métadonnées -->
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="badge bg-label-secondary rounded p-2 me-3">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Créé le</p>
                        <p class="mb-0 fw-semibold">{{ $absence->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="badge bg-label-secondary rounded p-2 me-3">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-muted">Dernière mise à jour</p>
                        <p class="mb-0 fw-semibold">{{ $absence->updated_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Actions -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-edit me-2"></i>Actions
        </h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between">
            @if($absence->statut != 'approved')
            <form action="{{ route('company.times.absences.update-status', $absence->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="btn btn-success align-items-center">
                    <i class="fas fa-check me-2"></i> Valider l'absence
                </button>
            </form>
            @endif

            @if($absence->statut != 'rejected')
            <form action="{{ route('company.times.absences.update-status', $absence->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="btn btn-danger align-items-center">
                    <i class="fas fa-x me-2"></i> Rejeter l'absence
                </button>
            </form>
            @endif

            @if($absence->statut == 'pending')
            <button type="button" class="btn btn-outline-warning align-items-center" data-bs-dismiss="modal">
                <i class="fas fa-x me-2"></i> Reporter la décision
            </button>
            @else
            <form action="{{ route('company.times.absences.update-status', $absence->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="pending">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-rotate-clockwise me-1"></i> Remettre en attente
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
<!-- Champs cachés -->
<input type="hidden" id="status" name="statut" value="{{ $absence->statut }}">
<input type="hidden" id="dateString" name="date" value="{{ $absence->date }}">
<input type="hidden" id="dateTimeString" name="arrival_date" value="{{ $absence->arrival_date }}">
<input type="hidden" id="created_at" name="created_at" value="{{ $absence->created_at }}">
<input type="hidden" id="updated_at" name="updated_at" value="{{ $absence->updated_at }}">
            
@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Animation des cartes au chargement
        $('.card').each(function(index) {
            $(this).delay(100 * index).fadeIn(300);
        });
    });
</script>
@endpush