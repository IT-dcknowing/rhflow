<!-- Modal Générer les bulletins -->
<div class="modal fade" id="genererBulletinsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Générer les bulletins de paie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('company.paiesalaries.periodes.generer-bulletins', $periode->id) }}" method="POST">
                @csrf
                <input type="hidden" name="periode_id" id="periode_id" value="{{ $periode->id }}">
                <div class="modal-body">
                    <p>Voulez-vous générer les bulletins de paie pour cette période ?</p>
                    <p class="text-muted small">Cette action va créer un bulletin pour chaque employé actif avec son contrat en cours.</p>
                    
                    <!--<div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="forcerRegeneration" name="forcer">
                        <label class="form-check-label" for="forcerRegeneration">
                            Forcer la régénération des bulletins existants
                        </label> 
                        <small class="d-block text-muted">Cocher cette case pour régénérer les bulletins même s'ils existent déjà.</small>
                    </div>-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cogs me-1"></i>Générer les bulletins
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>