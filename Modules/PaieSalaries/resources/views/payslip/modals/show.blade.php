<div class="row">
    <div class="col-md-12">
        <div class="card-body">
            @if($employee->allowances->count() > 0)
                <div class="timeline">
                    @foreach($employee->allowances->where('periode_id', $periode) as $allowance)
                    <div class="timeline-item">
                        <div class="timeline-badge">
                            <i class="fas fa-indent text-success mb-2"></i>
                        </div>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1 align-items-center">
                                            {{ $allowance->allowanceOption->name ?? 'Élément sans nom' }}
                                            <span class="badge bg-label-info ms-2">
                                                {{ $allowance->type === 'fixed' ? 'Fixe' : 'Pourcentage' }}
                                            </span>
                                        </h5>
                                        <p class="mb-1">
                                            <span class="text-muted">Code :</span> 
                                            {{ $allowance->code ?? 'N/A' }} 
                                            <span class="mx-2">|</span>
                                            <span class="text-muted">Jours travaillés :</span> 
                                            {{ $allowance->jours_work ?? 'N/A' }}
                                            <span class="mx-2">|</span>
                                            <span class="text-muted">Montant :</span>
                                            <strong>{{ number_format($allowance->amount, 0, ',', ' ') }} FCFA</strong>
                                        </p>
                                        <p class="mb-0">
                                            <span class="text-muted">Détails :</span>
                                            <span class="fw-medium">{{ $allowance->details ?? 'N/A' }}</span>
                                            <span class="mx-2">|</span>
                                            <span class="text-muted">Ajouté le :</span>
                                            {{ $allowance->created_at->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <div class="dropdown">
                                        <span class="text-muted">Période :</span>
                                        <span class="fw-medium">{{ $allowance->periode->nom ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox fa-4x text-muted"></i>
                    </div>
                    <h5 class="mb-2">Aucun élément de paie enregistré</h5>
                    <p class="text-muted">Commencez par ajouter des éléments de paie pour cet employé en cliquant sur le bouton "Ajouter un élément"</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 2rem;
        margin: 2rem 0;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 2rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-badge {
        position: absolute;
        left: -2.5rem;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        z-index: 2;
    }
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -1.25rem;
        top: 2.5rem;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }
    .card {
        border-left: 3px solid;
        border-color: inherit;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Gérer le clic sur le bouton d'ajout
        $('.add-allowance-btn').on('click', function() {
            var employeeId = $(this).data('employee-id');
            var employeeName = $(this).data('employee-name');
            
            // Mettre à jour le modal d'ajout
            $('#addElementsModal .modal-title').text('Ajouter des éléments pour ' + employeeName);
            $('#employee_id').val(employeeId);
            
            // Afficher le modal d'ajout
            var addModal = new bootstrap.Modal(document.getElementById('addElementsModal'));
            addModal.show();
        });

        // Gérer la suppression d'un élément
        $('.delete-allowance-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette action est irréversible !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.ajaxSubmit({
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Supprimé !',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Erreur !',
                                'Une erreur est survenue lors de la suppression.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>
@endpush