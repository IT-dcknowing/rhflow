@extends('layouts.super-admin')

@section('title', 'Détails du Pack - RH Flow')

@section('content')
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
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-package me-2"></i>
                        Pack : {{ $pack->name }}
                    </h5>
                    <small class="text-muted">
                        {{ $pack->description ?? 'Aucune description disponible' }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.packs.modules', $pack) }}" class="btn btn-info bg-info text-white">
                        <i class="ti ti-settings me-2"></i>
                        Gérer les Modules
                    </a>
                    <a href="{{ route('super-admin.packs.edit', $pack) }}" class="btn btn-primary bg-primary text-white">
                        <i class="ti ti-pencil me-2"></i>
                        Modifier
                    </a>
                    <a href="{{ route('super-admin.packs.index') }}" class="btn btn-outline-primary bg-label-primary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-12 col-lg-12">
            <div class="card"> 
                <div class="card-body">
                    <div class="row">
                        <!-- Informations générales -->
                        <div class="col-lg-8">
                            <h6 class="section-title mb-0">
                                <i class="ti ti-info-alt me-2"></i>
                                Informations générales
                            </h6>
                                    
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Nom du pack</label>
                                        <p class="mb-0">{{ $pack->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Statut</label>
                                        <p class="mb-0">
                                            @if($pack->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @else
                                                <span class="badge bg-danger">Inactif</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Prix de base</label>
                                        <p class="mb-0 fs-5 fw-bold text-primary">
                                            {{ formatPrice($pack->price, $currencyDetails) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Durée</label>
                                        <p class="mb-0">{{ $pack->duration ?? 1 }} </p>
                                    </div>
                                </div>
                            </div>

                            @if($pack->popular || $pack->enterprise)
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Étiquettes</label>
                                        <div class="d-flex gap-2">
                                            @if($pack->popular)
                                                <span class="badge bg-primary">Populaire</span>
                                            @endif
                                            @if($pack->enterprise)
                                                <span class="badge bg-warning">Entreprise</span>
                                            @endif
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            @endif

                            @if($pack->description)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <p class="mb-0">{{ $pack->description }}</p>
                            </div>
                            @endif

                            @if($pack->features_list && count($pack->features_list) > 0)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Fonctionnalités</label>
                                        <ul class="list-unstyled mb-0">
                                            @foreach($pack->features_list as $feature)
                                            <li class="mb-2">
                                                <i class="ti ti-check text-success me-2"></i>
                                                {{ $feature }}
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div> 
                                </div> 
                            </div>
                            @endif
                            <hr>                     
                            <div class="mb-3">
                                <label class="form-label fw-bold">Modules : </label>
                                <div class="gap-2">
                                    @foreach($pack->modules as $module)
                                        <span class="badge bg-success">{{ $module->name }}</span>
                                    @endforeach 
                                </div>
                            </div> 
                        </div>

                        <!-- Statistiques -->
                        <div class="col-lg-4">
                            <h6 class="section-title mb-4">
                                <i class="ti ti-bar-chart me-2"></i>
                                Statistiques
                            </h6>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial bg-label-primary rounded">
                                        <i class="ti ti-building ti-sm"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $pack->company_count }}</h6>
                                    <small class="text-muted">Abonnements totaux</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial bg-label-success rounded">
                                        <i class="ti ti-user-check ti-sm"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $pack->active_company_count }}</h6>
                                    <small class="text-muted">Abonnements actifs</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial bg-label-info rounded">
                                        <i class="ti ti-users ti-sm"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $pack->max_users === 0 ? 'Illimité' : $pack->max_users }}</h6>
                                    <small class="text-muted">Utilisateurs max</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial bg-label-warning rounded">
                                        <i class="ti ti-database ti-sm"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $pack->storage_limit }} GB</h6>
                                    <small class="text-muted">Stockage</small>
                                </div>
                            </div>

                            <!-- Revenus générés -->
                            <div class="card mt-4 border-info">
                                <div class="card-body">
                                    <h6 class="section-title mb-0">
                                        <i class="ti ti-money me-2"></i>
                                        Revenus générés
                                    </h6>
                                    
                                    <div class="text-center">
                                        <h4 class="text-primary mb-1">
                                            {{ formatPrice($pack->active_company_count * $pack->price, $currencyDetails) }}
                                        </h4>
                                        <small class="text-muted">Revenus mensuels</small>
                                    </div>

                                    @if($pack->price_yearly)
                                    <div class="text-center mt-3">
                                        <h5 class="text-success mb-1">
                                            {{ formatPrice($pack->active_company_count * $pack->price_yearly, $currencyDetails) }}
                                        </h5>
                                        <small class="text-muted">Revenus annuels</small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<!-- Entreprises utilisant ce pack -->
@if($pack->company_count > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="section-title text-primary mb-0">
                    <i class="ti ti-bag me-2"></i>
                    Entreprises utilisant ce pack
                </h5>
                <a href="{{ route('super-admin.enterprises.index') }}" class="btn btn-sm btn-outline-info">
                    Voir toutes les entreprises
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($pack->companies->take(6) as $company)
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial bg-label-info rounded">
                                    {{ substr($company->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $company->name }}</h6>
                                <small class="text-muted">
                                    {{ $company->isActive() ? 'Actif' : 'Inactif' }}
                                    • {{ $company->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($pack->company_count > 6)
                <div class="text-center mt-3">
                    <small class="text-muted">
                        Et {{ $pack->company_count - 6 }} autres entreprises...
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .section-title {
        color: #263d88;
        font-weight: 600;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(38, 61, 136, 0.1);
        border: 1px solid #e9ecef;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .form-control:focus, .form-select:focus {
        border-color: #263d88;
        box-shadow: 0 0 0 0.2rem rgba(38, 61, 136, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #263d88 0%, #3d5aa6 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1e2a5e 0%, #263d88 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(38, 61, 136, 0.3);
    }

    .btn-outline-secondary:hover {
        background-color: #263d88;
        border-color: #263d88;
        color: white;
    }

    .avatar-initial {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 50%;
    }
    .text-danger {
        color: #ff3e1d !important;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }
</style>
@endpush

@section('scripts')
<script>
    
	// Auto-dismiss notifications after 5 seconds
	const notifications = document.querySelectorAll('.alert-dismissible');
	notifications.forEach(function(notification) {
		setTimeout(function() {
			const bsAlert = new bootstrap.Alert(notification);
			bsAlert.close();
		}, 5000); // 5 seconds
	});
</script>
@endsection