<!-- Modal Valider le paiement -->
<div class="modal fade" id="validerPaiementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Valider le paiement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('company.paiesalaries.periodes.valider-paiement', $periode->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Voulez-vous marquer cette période comme payée ?</p>
                    
                    <div class="mb-3"> 
                        <label for="date_paiement_effectif" class="form-label">Date de paiement effectif</label>
                        <input type="date" class="form-control" id="date_paiement_effectif" name="date_paiement_effectif" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="envoyerNotifications" name="envoyer_notifications" checked>
                        <label class="form-check-label" for="envoyerNotifications">
                            Envoyer les notifications aux employés
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i>Valider le paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>