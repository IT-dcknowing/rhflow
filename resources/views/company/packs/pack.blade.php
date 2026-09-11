@extends('layouts.app')

@section('title', 'Gestion des Abonnements - RH Flow')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-package me-2"></i>
                        Gestion des Abonnements
                    </h4>
                    <p class="text-muted mb-0">Gérez votre abonnement et explorez nos plans tarifaires</p>
                    <small class="text-primary">
                        <i class="fas fa-calendar me-1"></i>
                        {{ ucfirst(\Carbon\Carbon::now()->translatedFormat('l d F Y')) }} •
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.packs.history') }}" class="btn btn-outline-info">
                        <i class="fas fa-list me-2"></i>
                        Historique
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statut d'abonnement actuel -->
    @if(auth()->user()->company && auth()->user()->company->companyPlan)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title text-primary mb-3">
                                <i class="fas fa-crown me-2"></i>
                                Votre Abonnement Actuel
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Plan:</strong> {{ auth()->user()->company->companyPlan->name }}</p>
                                    <p class="mb-2"><strong>Statut:</strong> 
                                        @if(auth()->user()->company->subscription_expires_at && auth()->user()->company->subscription_expires_at->isPast())
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>
                                            Expiré
                                        </span>
                                        @else
                                        <span class="badge bg-success">
                                            <i class="fas fa-circle-filled me-1"></i>
                                            Actif
                                        </span>
                                        @endif
                                    </p>
                                    <p class="mb-2"><strong>Utilisateurs:</strong> {{ auth()->user()->company->users_count ?? 0 }} / {{ auth()->user()->company->companyPlan->max_users == 0 ? 'Illimité' : auth()->user()->company->companyPlan->max_users }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Employés:</strong> {{ auth()->user()->company->employees_count ?? 0 }} / {{ auth()->user()->company->companyPlan->max_employees == 0 ? 'Illimité' : auth()->user()->company->companyPlan->max_employees }}</p>
                                    <p class="mb-2"><strong>Stockage:</strong> {{ number_format(auth()->user()->company->storage_used ?? 0, 2) }} GB / {{ auth()->user()->company->companyPlan->storage_limit == 0 ? 'Illimité' : auth()->user()->company->companyPlan->storage_limit }} GB</p>
                                    @if(auth()->user()->company->subscription_expires_at)
                                    <p class="mb-2"><strong>Expire le:</strong> {{ auth()->user()->company->subscription_expires_at->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-grid gap-2">
                                @if(auth()->user()->company->subscription_expires_at && auth()->user()->company->subscription_expires_at->isPast())
                                <button class="btn btn-primary" onclick="showRenewModal()">
                                    <i class="fas fa-refresh me-2"></i>
                                    Renouveler
                                </button>
                                @else
                                <button class="btn btn-primary" disabled>
                                    <i class="fas fa-refresh me-2"></i>
                                    Renouveler
                                </button>
                                @endif
                                <button class="btn btn-outline-secondary" onclick="showUpgradeModal()">
                                    <i class="fas fa-arrow-up me-2"></i>
                                    Changer de plan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Plans disponibles -->
    <div class="row">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-star me-2"></i>
                Plans Disponibles
            </h5>
        </div>
    </div>

    <div class="row" id="plansContainer">
        @php
            $plans = \App\Models\Plan::active()->orderBy('price')->get();
        @endphp
        
        @foreach($plans as $plan)
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card h-100 @if($plan->popular) border-primary shadow-lg @endif">
                @if($plan->popular)
                <div class="card-header bg-primary text-white text-center py-2">
                    <small><i class="fas fa-star me-1"></i> Le plus populaire</small>
                </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <div class="text-center mb-3">
                        <h5 class="card-title">{{ $plan->name }}</h5>
                        <div class="pricing-price mb-2">
                            <span class="price">{{ $plan->formatted_price }}</span>
                            <span class="period">/{{ $plan->duration > 1 ? $plan->duration . ' mois' : 'mois' }}</span>
                        </div>
                        @if($plan->price_yearly)
                        <small class="text-muted">ou {{ number_format($plan->price_yearly, 0,'.',' ') }}/an</small>
                        @endif
                    </div>

                    <div class="features-list mb-4">
                        @if($plan->features)
                            @php
                                $features = is_array($plan->features) ? $plan->features : explode("\n", $plan->features);
                            @endphp
                            @foreach($features as $feature)
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>{{ $feature }}</small>
                            </div>
                            @endforeach
                        @endif
                        
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-users text-success me-2"></i>
                            <small>{{ $plan->max_users == 0 ? 'Utilisateurs illimités' : $plan->max_users . ' utilisateurs' }}</small>
                        </div>
                        
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user-check text-success me-2"></i>
                            <small>{{ $plan->max_employees == 0 ? 'Employés illimités' : $plan->max_employees . ' employés' }}</small>
                        </div>
                        
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-cloud text-success me-2"></i>
                            <small>{{ $plan->storage_limit == 0 ? 'Stockage illimité' : $plan->storage_limit . ' GB stockage' }}</small>
                        </div>
                    </div>

                    <div class="mt-auto">
                        @if(auth()->user()->company && auth()->user()->company->plan_id == $plan->id)
                            @if(auth()->user()->company->subscription_expires_at && auth()->user()->company->subscription_expires_at->isPast())
                            <button class="btn btn-warning w-100" onclick="showRenewModal()">
                                <i class="fas fa-refresh me-2"></i>
                                Renouveler (Expiré)
                            </button>
                            @else
                            <button class="btn btn-success w-100" disabled>
                                <i class="fas fa-check me-2"></i>
                                Plan actuel
                            </button>
                            @endif
                        @else
                        <button class="btn @if($plan->popular) btn-primary @else btn-outline-primary @endif w-100" 
                                onclick="subscribeToPlan({{ $plan->id }})">
                            <i class="fas fa-shopping-cart me-2"></i>
                            @if(auth()->user()->company && auth()->user()->company->companyPlan)
                                Changer vers ce plan
                            @else
                                S'abonner
                            @endif
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Historique des commandes -->
    @if(auth()->user()->orders && auth()->user()->orders->count() > 0)
    <div class="row mt-5">
        <div class="col-12">
            <h5 class="mb-3">
                <i class="fas fa-history me-2"></i>
                Historique des Commandes
            </h5>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Plan</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(auth()->user()->orders()->latest()->get() as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->plan->name ?? 'N/A' }}</td>
                                    <td>{{ $order->plan->formatted_price ?? number_format($order->total_amount, 0, ',', ' ') . ' FCFA' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($order->isPending())
                                        <button class="btn btn-sm btn-primary" onclick="payOrder({{ $order->id }})">
                                            <i class="fas fa-credit-card me-1"></i>
                                            Payer
                                        </button>
                                        @endif
                                        <button class="btn btn-sm btn-outline-secondary" onclick="viewOrderDetails({{ $order->id }})">
                                            <i class="fas fa-eye me-1"></i>
                                            Détails
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal de paiement -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Finaliser l'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="paymentContent">
                    <!-- Contenu dynamique -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de renouvellement -->
<div class="modal fade" id="renewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Renouveler l'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Choisissez la durée pour renouveler votre abonnement actuel <strong>{{ auth()->user()->company->companyPlan->name ?? '' }}</strong></p>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="renewSubscription(1)">
                        1 mois - {{ auth()->user()->company->companyPlan->formatted_price ?? '' }}
                    </button>
                    <button class="btn btn-outline-primary" onclick="renewSubscription(3)">
                        3 mois - {{ auth()->user()->company->companyPlan ? auth()->user()->company->companyPlan->getFormattedPriceForDuration(3) : '' }}
                    </button>
                    <button class="btn btn-outline-primary" onclick="renewSubscription(12)">
                        12 mois - {{ auth()->user()->company->companyPlan ? auth()->user()->company->companyPlan->getFormattedPriceForDuration(12) : '' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de changement de plan -->
<div class="modal fade" id="upgradeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer de plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="upgradePlansContainer">
                    <!-- Plans chargés dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function subscribeToPlan(planId) {
        showPaymentModal(planId, 'new');
    }

    function renewSubscription(duration) {
        const planId = '{{ auth()->user()->company->plan_id ?? "" }}';
        showPaymentModal(planId, 'renew', duration);
        bootstrap.Modal.getInstance(document.getElementById('renewModal')).hide();
    }

    function showRenewModal() {
        new bootstrap.Modal(document.getElementById('renewModal')).show();
    }

    function showUpgradeModal() {
        const modal = new bootstrap.Modal(document.getElementById('upgradeModal'));
        loadUpgradePlans();
        modal.show();
    }

    function showPaymentModal(planId, action = 'new', duration = 1) {
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        
        // Charger le contenu de paiement
        fetch(`/company/packs/payment/${planId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: action,
                duration: duration
            })
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('paymentContent').innerHTML = html;
            modal.show();
        })
        .catch(error => {
            console.error('Erreur:', error);
            toastr.error('Erreur lors du chargement de la page de paiement');
        });
    }

    function loadUpgradePlans() {
        const currentPlanId = '{{ auth()->user()->company->plan_id ?? 0 }}';
        
        fetch(`/company/packs/plans`)
            .then(response => response.json())
            .then(plans => {
                const container = document.getElementById('upgradePlansContainer');
                container.innerHTML = '';
                
                plans.filter(plan => plan.id != currentPlanId).forEach(plan => {
                    const planCard = `
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6>${plan.name}</h6>
                                    <p class="mb-2">${plan.formatted_price}/${plan.duration > 1 ? plan.duration + ' mois' : 'mois'}</p>
                                    <button class="btn btn-sm btn-primary w-100" onclick="upgradeToPlan(${plan.id})">
                                        Changer vers ce plan
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += planCard;
                });
            })
            .catch(error => {
                console.error('Erreur:', error);
                toastr.error('Erreur lors du chargement des plans');
            });
    }

    function upgradeToPlan(planId) {
        bootstrap.Modal.getInstance(document.getElementById('upgradeModal')).hide();
        showPaymentModal(planId, 'upgrade');
    }

    function payOrder(orderId) {
        window.location.href = `/order/payment/${orderId}`;
    }

    function viewOrderDetails(orderId) {
        window.location.href = `/company/packs/orders/${orderId}`;
    }

    function refreshSubscriptionStatus() {
        location.reload();
    }

    // Auto-rafraîchissement toutes les 5 minutes
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            refreshSubscriptionStatus();
        }
    }, 300000);
</script>
@endpush

