@extends('layouts.super-admin')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Messages de succès/erreur -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Les actions d'abonnement renvoient leurs échecs via withErrors() et non
             session('error') : sans ce bloc, la page revenait muette et l'admin
             ne savait pas pourquoi l'abonnement n'avait pas été activé. --}}
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>
            @if($errors->count() === 1)
                {{ $errors->first() }}
            @else
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- En-tête avec navigation -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-credit-card me-2"></i>
                        Gestion de l'abonnement - {{ $enterprise->name }}
                    </h5>
                    <small class="text-muted">Gérer la souscription et les paramètres d'abonnement</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.enterprises.show', $enterprise) }}" class="btn btn-outline-info">
                        <i class="ti ti-eye me-2"></i>
                        Voir l'entreprise
                    </a>
                    <a href="{{ route('super-admin.enterprises.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <!-- Informations d'abonnement actuel -->
            <div class="col-xl-8 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            Abonnement Actuel
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Plan actuel</label>
                                    @php
                                        $planName = $currentPlan->name;
                                        $planColor = $planName == 'Gratuit' ? 'primary' : ($planName == 'Basic' ? 'info' : ($planName == 'Pro' ? 'success' : 'danger'));
                                    @endphp
                                    <div class="d-flex align-items-center">
                                        @if($currentPlan)
                                            <span class="badge bg-{{$planColor}} me-2">{{ $planName }}</span>
                                            <span class="text-muted">{{ formatPrice($currentPlan->price) }} </span>
                                        @else
                                            <span class="text-muted">Aucun plan</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Statut de l'abonnement</label>
                                    @if($subscriptionStats['is_active'])
                                        <span class="badge bg-success">Actif</span>
                                    @elseif($subscriptionStats['is_expired'])
                                        <span class="badge bg-danger">Expiré</span>
                                    @else
                                        <span class="badge bg-warning">Inactif</span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Période de facturation</label>
                                    <p class="mb-0">{{ $subscriptionStats['current_period'] }}</p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date de début</label>
                                    <p class="mb-0">
                                        @if($company->subscription_start_date)
                                            {{ $company->subscription_start_date->format('d/m/Y H:i') }}
                                        @else
                                            Non définie
                                        @endif
                                    </p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Prochaine expiration</label>
                                    <p class="mb-0">
                                        @if($subscriptionStats['next_renewal'])
                                            {{ $subscriptionStats['next_renewal'] }}
                                        @else
                                            Aucune date définie
                                        @endif
                                    </p>
                                </div>

                                @if($subscriptionStats['days_remaining'] !== null)
                                <div class="mb-3">
                                    <label class="form-label">Jours restants</label>
                                    @if($subscriptionStats['days_remaining'] >= 0)
                                        <span class="badge {{ $subscriptionStats['days_remaining'] <= 7 ? 'bg-danger' : 'bg-success' }}">
                                            {{ round($subscriptionStats['days_remaining']) }} jours
                                        </span>
                                    @else
                                        <span class="badge bg-danger">Expiré</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions sur l'abonnement -->
                        <div class="border-top pt-3 mt-3">
                            <h6 class="mb-3">Actions disponibles</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($subscriptionStats['is_active'])
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#suspendModal">
                                        <i class="ti ti-pause me-2"></i>
                                        Suspendre l'abonnement
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#activateModal">
                                        <i class="ti ti-play me-2"></i>
                                        Activer l'abonnement
                                    </button>
                                @endif

                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#changePlanModal">
                                    <i class="ti ti-exchange me-2"></i>
                                    Changer de plan
                                </button>

                                @if($subscriptionStats['is_expired'] || $subscriptionStats['days_remaining'] < 0)
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#renewModal">
                                        <i class="ti ti-refresh me-2"></i>
                                        Renouveler l'abonnement
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique des commandes -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-history me-2"></i>
                            Historique des commandes
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($orders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Plan</th>
                                            <th>Montant</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $order->plan->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($order->amount, 0, ',', ' ') }} XOF</td>
                                            <td>
                                                @switch($order->status)
                                                    @case('paid')
                                                        <span class="badge bg-success">Payée</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning">En attente</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-danger">Annulée</span>
                                                        @break
                                                    @case('refunded')
                                                        <span class="badge bg-info">Remboursée</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="{{ route('super-admin.commandes.show', $order) }}" class="btn btn-icon btn-sm btn-label-info">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ti ti-receipt-off text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Aucune commande trouvée</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panneau latéral avec statistiques -->
            <div class="col-xl-4 col-lg-5">
                <!-- Statistiques d'abonnement -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-chart-bar me-2"></i>
                            Statistiques d'abonnement
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="mb-1 text-primary">{{ $enterprise->storage_limit ?? 0 }}</h4>
                                    <small class="text-muted">Go de stockage</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="mb-1 text-info">{{ $enterprise->nbre_trait ?? 0 }}</h4>
                                    <small class="text-muted">Traitements/mois</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Stockage utilisé</span>
                                <span>{{ $company->current_storage_used ?? 0 }} / {{ $company->max_storage_gb ?? 5 }} GB</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" role="progressbar"
                                     style="width: {{ $company->current_storage_used && $company->max_storage_gb ? ($company->current_storage_used / $company->max_storage_gb) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Employés actifs</span>
                                <span>{{ $company->employees_count ?? 0 }} / {{ $pack->max_employees ?? 0 }}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $pack->max_employees && $company->employees_count ? ($company->employees_count / $pack->max_employees) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations sur le plan actuel -->
                @if($currentPlan)
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-package me-2"></i>
                            Détails du plan actuel
                        </h6>
                    </div>
                    <div class="card-body">
                        <h5 class="text-primary">{{ $currentPlan->name }}</h5>
                        <p class="text-muted">{{ $currentPlan->description }}</p>

                        <div class="row text-sm">
                            <div class="col-6">
                                <strong>Prix:</strong><br>
                                {{ formatPrice($currentPlan->price) }} 
                            </div>
                            <div class="col-6">
                                <strong>Durée:</strong><br>
                                {{ $currentPlan->duration ?? 1 }} 
                            </div>
                        </div>

                        @if($currentPlan->features)
                        <div class="mt-3">
                            <strong>Fonctionnalités:</strong>
                            <ul class="list-unstyled mt-2">
                                @foreach($currentPlan->features as $feature)
                                    <li><i class="ti ti-check text-success me-1"></i> {{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de suspension d'abonnement -->
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suspendre l'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir suspendre l'abonnement de cette entreprise ?</p>
                <p class="text-muted">L'entreprise ne pourra plus accéder aux fonctionnalités payantes jusqu'à reactivation.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('super-admin.enterprises.suspend', $enterprise) }}" method="GET" class="d-inline">
                    <button type="submit" class="btn btn-warning">Suspendre</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'activation d'abonnement -->
<div class="modal fade" id="activateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activer l'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir activer l'abonnement de cette entreprise ?</p>
                <p class="text-muted">L'entreprise pourra accéder à toutes les fonctionnalités de son plan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('super-admin.enterprises.activate', $enterprise) }}" method="GET" class="d-inline">
                    <button type="submit" class="btn btn-success">Activer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de changement de plan -->
<div class="modal fade" id="changePlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer de plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super-admin.enterprises.subscription.update', $enterprise) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sélectionner un nouveau plan</label>
                        <select name="plan_id" class="form-select" required>
                            <option value="">Choisir un plan...</option>
                            @foreach($allPlans as $plan)
                                <option value="{{ $plan->id }}" {{ $currentPlan && $currentPlan->id == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} - {{ number_format($plan->price, 0, ',', ' ') }} XOF
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date d'activation</label>
                        <input type="datetime-local" name="activation_date" class="form-control"
                               value="{{ now()->format('Y-m-d\TH:i') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Période de facturation</label>
                        <select name="billing_period" class="form-select">
                            <option value="monthly">Mensuel</option>
                            <option value="yearly">Annuel</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Changer de plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de renouvellement d'abonnement -->
<div class="modal fade" id="renewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Renouveler l'abonnement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>L'abonnement de cette entreprise a expiré ou va bientôt expirer.</p>
                <p>Voulez-vous renouveler l'abonnement avec le même plan ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('super-admin.enterprises.subscription.renew', $enterprise) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Renouveler</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.75rem;
    }

    .progress {
        height: 8px;
    }

    .table th {
        font-size: 0.875rem;
        font-weight: 600;
        border-top: none;
    }

    .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
// Auto-dismiss notifications after 5 seconds
const notifications = document.querySelectorAll('.alert-dismissible');
notifications.forEach(function(notification) {
    setTimeout(function() {
        const bsAlert = new bootstrap.Alert(notification);
        bsAlert.close();
    }, 5000);
});
</script>
@endpush