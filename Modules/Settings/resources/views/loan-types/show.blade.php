@extends('layouts.app')

@section('title', 'Détails du Type de Prêt - RH Flow')

@push('styles')
<style>
    .card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: none;
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .detail-item {
        padding: 1rem 0;
        border-bottom: 1px solid #e0e6ed;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #566a7f;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        color: #2c3e50;
    }

    .badge {
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 6px;
        padding: 0.375rem 0.75rem;
    }

    .bg-label-primary {
        background-color: rgba(105, 110, 255, 0.1) !important;
        color: #253e87 !important;
    }

    .bg-label-success {
        background-color: rgba(40, 200, 72, 0.1) !important;
        color: #28c848 !important;
    }

    .bg-label-warning {
        background-color: rgba(255, 205, 7, 0.1) !important;
        color: #ffcd07 !important;
    }

    .bg-label-danger {
        background-color: rgba(255, 73, 97, 0.1) !important;
        color: #ff4961 !important;
    }

    .shadow-xs {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y ds">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-hand-holding-usd me-2"></i>
                        Détails du Type de Prêt
                    </h4>
                    <p class="text-muted mb-0">Informations complètes sur le type de prêt</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.dashboard') }}">Tableau de bord</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.settings.config') }}">Paramètres</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('company.settings.loan-types.index') }}">Types de Prêts</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $loanType->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.settings.loan-types.edit', $loanType->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    <a href="{{ route('company.settings.loan-types.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-4 shadow-xs d-flex align-items-center justify-content-between h-100" style="border-color: #E8E8E6 !important;">
                <div>
                    <p class="text-xs fw-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Montant Maximum
                    </p>
                    <p class="fw-bold mb-1" style="font-size: 1.65rem; color: #1e3a8a; line-height: 1.2;">
                        {{ $loanType->formatted_max_amount }}
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Plafond accordé</p>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #EFF6FF; color: #1e3a8a;">
                    <i class="fas fa-hand-holding-usd fa-lg"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-4 shadow-xs d-flex align-items-center justify-content-between h-100" style="border-color: #E8E8E6 !important;">
                <div>
                    <p class="text-xs fw-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Taux d'Intérêt
                    </p>
                    <p class="fw-bold mb-1" style="font-size: 1.65rem; color: #1e3a8a; line-height: 1.2;">
                        {{ $loanType->formatted_interest_rate }}
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Taux annuel appliqué</p>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #EFF6FF; color: #1e3a8a;">
                    <i class="fas fa-percentage fa-lg"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-4 shadow-xs d-flex align-items-center justify-content-between h-100" style="border-color: #E8E8E6 !important;">
                <div>
                    <p class="text-xs fw-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Période Max
                    </p>
                    <p class="fw-bold mb-1" style="font-size: 1.65rem; color: #1e3a8a; line-height: 1.2;">
                        {{ $loanType->repayment_period_max }} mois
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Échéance maximale</p>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: #EFF6FF; color: #1e3a8a;">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="bg-white border rounded-3 p-4 shadow-xs d-flex align-items-center justify-content-between h-100" style="border-color: #E8E8E6 !important;">
                <div>
                    <p class="text-xs fw-bold text-muted text-uppercase tracking-wider mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Statut
                    </p>
                    <p class="fw-bold mb-1" style="font-size: 1.65rem; color: {{ $loanType->is_active ? '#28c848' : '#8592a3' }}; line-height: 1.2;">
                        {{ $loanType->is_active ? 'Actif' : 'Inactif' }}
                    </p>
                    <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ $loanType->is_active ? 'Disponible aux salariés' : 'Désactivé' }}</p>
                </div>
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background-color: {{ $loanType->is_active ? '#E8FADF' : '#EFF6FF' }}; color: {{ $loanType->is_active ? '#28c848' : '#1e3a8a' }};">
                    <i class="fas fa-{{ $loanType->is_active ? 'check-circle' : 'ban' }} fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations détaillées -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-info-circle me-2"></i>Informations Générales
                    </h5>
                    
                    <div class="detail-item">
                        <div class="detail-label">Nom du Type de Prêt</div>
                        <div class="detail-value fw-semibold">{{ $loanType->name }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">
                            @if($loanType->description)
                                {{ $loanType->description }}
                            @else
                                <span class="text-muted">Aucune description</span>
                            @endif
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Montant Maximum Autorisé</div>
                        <div class="detail-value fw-semibold text-primary">{{ $loanType->formatted_max_amount }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Taux d'Intérêt Annuel</div>
                        <div class="detail-value">
                            <span class="badge bg-label-primary">{{ $loanType->formatted_interest_rate }}</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Période de Remboursement</div>
                        <div class="detail-value">{{ $loanType->formatted_repayment_period }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Garantie Requise</div>
                        <div class="detail-value">
                            @if($loanType->requires_guarantor)
                                <span class="badge bg-label-warning">
                                    <i class="fas fa-shield-alt me-1"></i>Oui
                                </span>
                            @else
                                <span class="badge bg-label-success">
                                    <i class="fas fa-times me-1"></i>Non
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($loanType->requires_guarantor && $loanType->guarantor_conditions)
                        <div class="detail-item">
                            <div class="detail-label">Conditions de Garantie</div>
                            <div class="detail-value">{{ $loanType->guarantor_conditions }}</div>
                        </div>
                    @endif

                    <div class="detail-item">
                        <div class="detail-label">Statut</div>
                        <div class="detail-value">
                            @if($loanType->is_active)
                                <span class="badge bg-label-success">
                                    <i class="fas fa-check me-1"></i>Actif
                                </span>
                            @else
                                <span class="badge bg-label-danger">
                                    <i class="fas fa-times me-1"></i>Inactif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations système -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-cog me-2"></i>Informations Système
                    </h5>
                    
                    <div class="detail-item">
                        <div class="detail-label">ID</div>
                        <div class="detail-value">#{{ $loanType->id }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Créé le</div>
                        <div class="detail-value">{{ $loanType->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Modifié le</div>
                        <div class="detail-value">{{ $loanType->updated_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if($loanType->creator)
                        <div class="detail-item">
                            <div class="detail-label">Créé par</div>
                            <div class="detail-value">{{ $loanType->creator->name }}</div>
                        </div>
                    @endif

                    @if($loanType->updater && $loanType->updater->id !== $loanType->creator?->id)
                        <div class="detail-item">
                            <div class="detail-label">Modifié par</div>
                            <div class="detail-value">{{ $loanType->updater->name }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <x-quick-actions class="mt-4" stacked>
                <x-quick-action icon="fas fa-edit" label="Modifier"
                    :href="route('company.settings.loan-types.edit', $loanType->id)"
                    variant="outline" color="warning" />

                <form action="{{ route('company.settings.loan-types.toggle-status', $loanType->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <x-quick-action type="submit"
                        icon="fas fa-{{ $loanType->is_active ? 'ban' : 'check' }}"
                        label="{{ $loanType->is_active ? 'Désactiver' : 'Activer' }}"
                        variant="outline" color="{{ $loanType->is_active ? 'secondary' : 'success' }}" />
                </form>

                <form action="{{ route('company.settings.loan-types.destroy', $loanType->id) }}"
                      method="POST"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type de prêt ? Cette action est irréversible.');">
                    @csrf
                    @method('DELETE')
                    <x-quick-action type="submit" icon="fas fa-trash" label="Supprimer"
                        variant="outline" color="danger" />
                </form>
            </x-quick-actions>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes au survol
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
