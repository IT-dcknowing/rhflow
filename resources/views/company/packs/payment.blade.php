
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Finaliser votre abonnement
                </h5>
            </div>
            <div class="card-body">
                <!-- Récapitulatif de la commande -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h6>{{ $plan->name }}</h6>
                        <p class="text-muted mb-0">
                            @if($action === 'new')
                                Nouvel abonnement pour {{ $duration }} mois
                            @elseif($action === 'renew')
                                Renouvellement pour {{ $duration }} mois
                            @elseif($action === 'upgrade')
                                Changement de plan
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <h4 class="text-primary">{{ number_format($amount, 0, ',', ' ') }} FCFA</h4>
                    </div>
                </div>

                <!-- Détails du plan -->
                <div class="bg-light p-3 rounded mb-4">
                    <h6 class="mb-3">Détails du plan</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Utilisateurs:</strong> {{ $plan->max_users == 0 ? 'Illimité' : $plan->max_users }}</p>
                            <p class="mb-2"><strong>Employés:</strong> {{ $plan->max_employees == 0 ? 'Illimité' : $plan->max_employees }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Stockage:</strong> {{ $plan->storage_limit == 0 ? 'Illimité' : $plan->storage_limit . ' GB' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de paiement -->
                <form id="paymentForm" action="{{ route('company.packs.subscribe') }}" method="POST">
                    @csrf
                    
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="action" value="{{ $action }}">
                    <input type="hidden" name="duration" value="{{ $duration }}">
                    <input type="hidden" name="amount" value="{{ $amount }}">
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    <div class="mb-4">
                        <h6 class="mb-3">Méthode de paiement</h6>
                        
                        <!-- Mobile Money -->
                        <div class="payment-methods">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                        id="wave" value="wave" checked>
                                <label class="form-check-label" for="wave">
                                    <i class="fas fa-brand-wave me-2"></i>
                                    Wave
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                        id="orange" value="orange">
                                <label class="form-check-label" for="orange">
                                    <i class="fas fa-brand-orange me-2"></i>
                                    Orange Money
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                        id="mtn" value="mtn">
                                <label class="form-check-label" for="mtn">
                                    <i class="fas fa-brand-mtn me-2"></i>
                                    MTN Mobile Money
                                </label>
                            </div>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" 
                                        id="moov" value="moov">
                                <label class="form-check-label" for="moov">
                                    <i class="fas fa-brand-moov me-2"></i>
                                    Moov Money
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Numéro de téléphone -->
                    <div class="mb-4">
                        <label for="phone" class="form-label">Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                    placeholder="+225 07 00 00 00 00" required>
                        </div>
                        <small class="text-muted">Le numéro doit être au format international</small>
                    </div>

                    <!-- Informations additionnelles -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                    placeholder="Informations additionnelles...">{{ $order->notes ?? '' }}</textarea>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('company.packs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-credit-card me-2"></i>
                            Procéder au paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informations de sécurité -->
        <div class="alert alert-info mt-3">
            <h6><i class="fas fa-shield me-2"></i> Sécurité du paiement</h6>
            <p class="mb-2">
                <i class="fas fa-check me-1"></i>
                Paiement sécurisé via les plateformes Mobile Money officielles
            </p>
            <p class="mb-2">
                <i class="fas fa-check me-1"></i>
                Votre abonnement sera activé immédiatement après confirmation du paiement
            </p>
            <p class="mb-0">
                <i class="fas fa-check me-1"></i>
                Vous recevrez une confirmation par SMS et email
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentForm = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitBtn');
        
        paymentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(paymentForm);
            const paymentMethod = formData.get('payment_method');
            const phone = formData.get('phone');
            
            // Validation du numéro de téléphone
            if (!validatePhone(phone)) {
                toastr.error('Veuillez entrer un numéro de téléphone valide');
                return;
            }
            
            // Désactiver le bouton
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-loader me-2"></i>Traitement en cours...';
            
            // Envoyer la requête
            fetch(paymentForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (paymentMethod === 'wave') {
                        // Rediriger vers la page de paiement Wave
                        window.location.href = data.payment_url;
                    } else {
                        // Afficher les instructions pour les autres méthodes
                        showPaymentInstructions(paymentMethod, data);
                    }
                } else {
                    toastr.error(data.message || 'Une erreur est survenue');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Procéder au paiement';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                toastr.error('Une erreur est survenue lors du traitement du paiement');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Procéder au paiement';
            });
        });
        
        function validatePhone(phone) {
            // Validation simple pour les numéros de téléphone africains
            const phoneRegex = /^\+?[0-9]{10,15}$/;
            return phoneRegex.test(phone.replace(/\s/g, ''));
        }
        
        function showPaymentInstructions(method, data) {
            let instructions = '';
            
            switch(method) {
                case 'orange':
                    instructions = `
                        <h6>Instructions Orange Money</h6>
                        <p>1. Allez dans votre application Orange Money</p>
                        <p>2. Choisissez "Payer" puis "Paiement Marchand"</p>
                        <p>3. Entrez le code marchand: <strong>${data.merchant_code}</strong></p>
                        <p>4. Entrez le montant: <strong>${data.amount} FCFA</strong></p>
                        <p>5. Confirmez avec votre code secret</p>
                    `;
                    break;
                case 'mtn':
                    instructions = `
                        <h6>Instructions MTN Mobile Money</h6>
                        <p>1. Composez *133# et validez</p>
                        <p>2. Choisissez "Payer"</p>
                        <p>3. Entrez le numéro marchand: <strong>${data.merchant_number}</strong></p>
                        <p>4. Entrez le montant: <strong>${data.amount} FCFA</strong></p>
                        <p>5. Confirmez avec votre code secret</p>
                    `;
                    break;
                case 'moov':
                    instructions = `
                        <h6>Instructions Moov Money</h6>
                        <p>1. Allez dans votre application Moov Money</p>
                        <p>2. Choisissez "Paiement marchand"</p>
                        <p>3. Entrez le code marchand: <strong>${data.merchant_code}</strong></p>
                        <p>4. Entrez le montant: <strong>${data.amount} FCFA</strong></p>
                        <p>5. Confirmez avec votre code secret</p>
                    `;
                    break;
            }
            
            // Afficher les instructions dans une modal
            const modalHtml = `
                <div class="modal fade" id="paymentInstructionsModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Instructions de paiement</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                ${instructions}
                                <div class="alert alert-warning mt-3">
                                    <strong>Important:</strong> Votre commande expire dans 7 jours si le paiement n'est pas effectué.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                <a href="{{ route('company.packs.index') }}" class="btn btn-primary">
                                    Voir mes commandes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            new bootstrap.Modal(document.getElementById('paymentInstructionsModal')).show();
        }
    });
</script>
@endpush