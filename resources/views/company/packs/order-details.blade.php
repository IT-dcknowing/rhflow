@extends('layouts.app')

@section('title', 'Détails de la commande - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-file-invoice me-2"></i>
                        Détails de la commande
                    </h4>
                    <p class="text-muted mb-0">Consultez les informations détaillées de votre commande</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.packs.history') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour à l'historique
                    </a>
                    @if($order->isPaid())
                    <button class="btn btn-success" onclick="downloadInvoice({{ $order->id }})">
                        <i class="fas fa-download me-2"></i>
                        Télécharger la facture
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes de statut -->
    @if($order->isPending())
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-clock me-2"></i>
        <strong>En attente de paiement:</strong> Cette commande expirera le {{ $order->expires_at->format('d/m/Y à H:i') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @elseif($order->isPaid())
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <strong>Paiement confirmé:</strong> Votre abonnement est actif.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @elseif($order->isCancelled())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-times-circle me-2"></i>
        <strong>Commande annulée:</strong> {{ $order->notes }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @elseif($order->isExpired())
    <div class="alert alert-secondary alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Commande expirée:</strong> Cette commande a expiré le {{ $order->expires_at->format('d/m/Y à H:i') }}.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Informations principales -->
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations de la commande
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Numéro de commande:</strong></td>
                                    <td><code>{{ $order->order_number }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Date de création:</strong></td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Méthode de paiement:</strong></td>
                                    <td>
                                        @switch($order->payment_method)
                                            @case('wave')
                                                <i class="fas fa-wave text-primary"></i> Wave
                                                @break
                                            @case('orange')
                                                <i class="fas fa-orange text-warning"></i> Orange Money
                                                @break
                                            @case('mtn')
                                                <i class="fas fa-mobile-alt text-success"></i> MTN Mobile Money
                                                @break
                                            @case('moov')
                                                <i class="fas fa-mobile-alt text-info"></i> Moov Money
                                                @break
                                            @default
                                                <span class="text-muted">{{ $order->payment_method }}</span>
                                        @endswitch
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                @if($order->payment_reference)
                                <tr>
                                    <td><strong>Référence de paiement:</strong></td>
                                    <td><code>{{ $order->payment_reference }}</code></td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Numéro de téléphone:</strong></td>
                                    <td>{{ $order->phone ?? 'Non spécifié' }}</td>
                                </tr>
                                @if($order->paid_at)
                                <tr>
                                    <td><strong>Date de paiement:</strong></td>
                                    <td>{{ $order->paid_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($order->expires_at)
                                <tr>
                                    <td><strong>Date d'expiration:</strong></td>
                                    <td>
                                        @if($order->expires_at->isPast())
                                            <span class="text-danger">Expirée le {{ $order->expires_at->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="text-warning">Expire le {{ $order->expires_at->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    @if($order->notes)
                    <div class="mt-3">
                        <h6>Notes</h6>
                        <div class="alert alert-light">
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Détails du plan -->
            @if($order->plan)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box me-2"></i>
                        Plan souscrit
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-primary">{{ $order->plan->name }}</h6>
                            <p class="mb-2">{{ $order->plan->description ?? 'Plan d\'abonnement RH Flow' }}</p>
                            @if($order->plan->popular)
                            <span class="badge bg-warning">
                                <i class="fas fa-star me-1"></i>
                                Le plus populaire
                            </span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h6>Caractéristiques</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-users text-success me-2"></i> {{ $order->plan->max_users == 0 ? 'Utilisateurs illimités' : $order->plan->max_users . ' utilisateurs' }}</li>
                                <li><i class="fas fa-user-check text-success me-2"></i> {{ $order->plan->max_employees == 0 ? 'Employés illimités' : $order->plan->max_employees . ' employés' }}</li>
                                <li><i class="fas fa-cloud text-success me-2"></i> {{ $order->plan->storage_limit == 0 ? 'Stockage illimité' : $order->plan->storage_limit . ' GB stockage' }}</li>
                                @if($order->plan->enable_chatgpt)
                                <li><i class="fas fa-robot text-success me-2"></i> Accès ChatGPT intégré</li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6>Fonctionnalités</h6>
                            @if($order->plan->features)
                                @php
                                    $features = is_array($order->plan->features) ? $order->plan->features : explode("\n", $order->plan->features);
                                @endphp
                                <ul class="list-unstyled">
                                    @foreach($features as $feature)
                                    <li><i class="fas fa-check text-success me-2"></i> {{ $feature }}</li>
                                    @endforeach
                                </ul>
                            @else
                            <p class="text-muted">Aucune fonctionnalité spécifique</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Résumé financier -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>
                        Résumé financier
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Prix unitaire:</strong></td>
                            <td class="text-end">{{ number_format($order->amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td><strong>Réduction:</strong></td>
                            <td class="text-end text-success">-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endif
                        @if($order->tax_amount > 0)
                        <tr>
                            <td><strong>Taxes:</strong></td>
                            <td class="text-end">{{ number_format($order->tax_amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td><strong>Total:</strong></td>
                            <td class="text-end"><h4 class="text-primary mb-0">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</h4></td>
                        </tr>
                    </table>

                    <!-- Actions -->
                    <div class="d-grid gap-2 mt-3">
                        @if($order->isPending())
                        <a href="{{ route('order.payment', $order->id) }}" class="btn btn-primary">
                            <i class="fas fa-credit-card me-2"></i>
                            Payer maintenant
                        </a>
                        @endif
                        
                        @if($order->isPending() || $order->isExpired())
                        <button class="btn btn-outline-danger" onclick="cancelOrder({{ $order->id }})">
                            <i class="fas fa-times me-2"></i>
                            Annuler la commande
                        </button>
                        @endif
                        
                        @if($order->isPaid())
                        <button class="btn btn-outline-success" onclick="downloadInvoice({{ $order->id }})">
                            <i class="fas fa-download me-2"></i>
                            Télécharger la facture
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations de contact -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-headset me-2"></i>
                        Besoin d'aide ?
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">Si vous avez des questions concernant cette commande:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i> +225 27 20 00 00 00</li>
                        <li><i class="fas fa-envelope me-2"></i> support@rhflow.ci</li>
                        <li><i class="fas fa-clock me-2"></i> Lun-Ven: 8h-18h</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelOrder(orderId) {
    if (confirm('Êtes-vous sûr de vouloir annuler cette commande ? Cette action est irréversible.')) {
        fetch('/company/packs/orders/' + orderId + '/cancel', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Commande annulée avec succès');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                toastr.error(data.message || 'Erreur lors de l\'annulation');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            toastr.error('Erreur lors de l\'annulation');
        });
    }
}

function downloadInvoice(orderId) {
    window.open('/company/packs/orders/' + orderId + '/invoice', '_blank');
}
</script>
@endpush

@endsection
