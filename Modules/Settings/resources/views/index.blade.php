@extends('layouts.app')

@section('title', 'Paramètres Entreprise - RH Flow')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête des Paramètres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"> Paramètres Entreprise</h4>
                        <p class="text-muted mb-0">Configurez et gérez tous les paramètres de votre entreprise</p>
                        <small class="text-primary">
                            <i class="fas fa-calendar me-1"></i>
                            {{ now()->translatedFormat('l d F Y') }} •
                            <i class="fas fa-clock me-1"></i>
                            {{ now()->format('H:i') }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('company.settings.config.end') }}" class="btn btn-outline-success">
                            <i class="fas fa-check me-1"></i>Terminer la configuration
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques des Paramètres -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="fas fa-building fa-28px"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-primary">{{ $stats['total_branches'] ?? 0 }}</h3>
                        <p class="text-muted mb-2">Siège & Succursales</p>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary"
                                style="width: {{ min(100, ($stats['total_branches'] ?? 0) * 20) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                            <div class="avatar-initial bg-label-info rounded">
                                <i class="fas fa-sitemap fa-28px"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-info">{{ $stats['total_departments'] ?? 0 }}</h3>
                        <p class="text-muted mb-2">Services</p>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-info"
                                style="width: {{ min(100, ($stats['total_departments'] ?? 0) * 15) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="fas fa-user-tie fa-28px"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-warning">{{ $stats['total_designations'] ?? 0 }}</h3>
                        <p class="text-muted mb-2">Postes</p>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-warning"
                                style="width: {{ min(100, ($stats['total_designations'] ?? 0) * 10) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="avatar mx-auto mb-3" style="width: 60px; height: 60px;">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="fas fa-calendar-alt fa-28px"></i>
                            </div>
                        </div>
                        <h3 class="mb-1 text-success">{{ $stats['total_leave_types'] ?? 0 }}</h3>
                        <p class="text-muted mb-2">Types de Congés</p>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ min(100, ($stats['total_leave_types'] ?? 0) * 25) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sections de Configuration -->
        <div class="row mb-4">
            <!-- Configuration Entreprise -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Configuration Entreprise</h5>
                        <a href="{{ route('company.settings.settings') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i>Configurer
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3" style="width: 50px; height: 50px;">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="fas fa-building fa-24px"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $company->name ?? 'Non configuré' }}</h6>
                                <small class="text-muted">{{ $company->email ?? 'Email non configuré' }}</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-primary me-2"
                                        style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                    <small><strong>{{ $stats['total_branches'] ?? 0 }}</strong> Sites</small>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-info me-2"
                                        style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                    <small><strong>{{ $stats['total_departments'] ?? 0 }}</strong> Services</small>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-warning me-2"
                                        style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                    <small><strong>{{ $stats['total_designations'] ?? 0 }}</strong> Postes</small>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-label-success me-2"
                                        style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                    <small><strong>{{ $stats['total_work_locations'] ?? 0 }}</strong> Pointeuses</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Statut:</h6>
                                <small class="text-muted">{{ $company->is_active ? 'Active' : 'Inactive' }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge {{ $company->is_active ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $company->is_active ? '✅ Active' : '❌ Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents et Images -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Documents & Images</h5>
                        <div class="dropdown">

                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showLogoModal()">
                                        <i class="fas fa-image me-1"></i>Logo
                                    </a></li>
                                <li><a class="dropdown-item" href="#" onclick="showSignatureModal()">
                                        <i class="fas fa-signature me-1"></i>Signature
                                    </a></li>
                                <li><a class="dropdown-item" href="#" onclick="showStampModal()">
                                        <i class="fas fa-stamp me-1"></i>Cachet
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3" style="width: 50px; height: 50px;">
                                @if($company->logo)
                                    <img src="{{ url($company->logo_url) }}" alt="Logo" class="rounded">
                                @else
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="fas fa-image fa-24px"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Logo de l'entreprise</h6>
                                <small class="text-muted">
                                    @if($company->logo)
                                        Configuré
                                    @else
                                        Non configuré
                                    @endif
                                </small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="showLogoModal()">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>

                        <!-- Signature -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-md me-3" style="width: 50px; height: 50px;">
                                @if($company->electronic_signature)
                                    <img src="{{ url($company->electronic_signature_url) }}" alt="Signature" class="rounded">
                                @else
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="fas fa-signature fa-24px"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Signature électronique</h6>
                                <small class="text-muted">
                                    @if($company->electronic_signature)
                                        Configurée
                                    @else
                                        Non configurée
                                    @endif
                                </small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="showSignatureModal()">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>

                        <!-- Cachet -->
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3" style="width: 50px; height: 50px;">
                                @if($company->electronic_stamp)
                                    <img src="{{ url($company->electronic_stamp_url) }}" alt="Cachet" class="rounded">
                                @else
                                    <div class="avatar-initial bg-label-secondary rounded">
                                        <i class="fas fa-stamp fa-24px"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">Cachet électronique</h6>
                                <small class="text-muted">
                                    @if($company->electronic_stamp)
                                        Configuré
                                    @else
                                        Non configuré
                                    @endif
                                </small>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="showStampModal()">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paramètres RH 
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">👥 Paramètres Ressources Humaines</h5>
                                        <span class="badge bg-label-primary">Configuration RH</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">

                                            <div class="col-xl-3 col-md-6 mb-4">
                                                <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                            <div class="avatar-initial bg-label-success rounded">
                                                                <i class="fas fa-calendar-alt fa-20px"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Types de Congés</h6>
                                                            <small class="text-muted">{{ $stats['total_leave_types'] ?? 0 }} configurés</small>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('company.settings.leave-types.index') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="col-xl-3 col-md-6 mb-4">
                                                <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                            <div class="avatar-initial bg-label-info rounded">
                                                                <i class="fas fa-map-marker-alt fa-20px"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Emplacements</h6>
                                                            <small class="text-muted">{{ $stats['total_work_locations'] ?? 0 }} sites</small>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('company.settings.work-locations.index') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="col-xl-3 col-md-6 mb-4">
                                                <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                            <div class="avatar-initial bg-label-primary rounded">
                                                                <i class="fas fa-building fa-20px"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Sites & Succursales</h6>
                                                            <small class="text-muted">{{ $stats['total_branches'] ?? 0 }} sites</small>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('company.settings.branches.index') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="col-xl-3 col-md-6 mb-4">
                                                <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                            <div class="avatar-initial bg-label-info rounded">
                                                                <i class="fas fa-sitemap fa-20px"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Services</h6>
                                                            <small class="text-muted">{{ $stats['total_departments'] ?? 0 }} services</small>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('company.settings.departments.index') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>


                                            <div class="col-xl-3 col-md-6 mb-4">
                                                <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                            <div class="avatar-initial bg-label-warning rounded">
                                                                <i class="fas fa-user-tie fa-20px"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Postes</h6>
                                                            <small class="text-muted">{{ $stats['total_designations'] ?? 0 }} postes</small>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('company.settings.designations.index') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-xl-3 col-md-6 mb-4">
                                                <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                            <div class="avatar-initial bg-label-secondary rounded">
                                                                <i class="fas fa-clock fa-20px"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Système de Présence</h6>
                                                            <small class="text-muted">Configuration</small>
                                                        </div>
                                                    </div>
                                                    <a href="{{ route('company.settings.attendance-system.index') }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="col-xl-3 col-md-6 mb-4">
                                                <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3" style="width: 40px; height: 40px;">
                                                            <div class="avatar-initial bg-label-secondary rounded">
                                                                <i class="fas fa-palette fa-20px"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">Thème & Couleurs</h6>
                                                            <small class="text-muted">Personnalisé</small>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="configureColors()">
                                                        <i class="fas fa-palette"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        -->

        <!-- Actions Rapides -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"> Actions Rapides</h5>

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('company.settings.settings') }}" class="btn btn-outline-primary">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-cog me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Paramètres</div>
                                                <small class="text-muted">Entreprise</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('company.settings.leave-types.index') }}"
                                        class="btn btn-outline-success">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Types de Congés</div>
                                                <small class="text-muted">Configurer</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('company.settings.loan-types.index') }}"
                                        class="btn btn-outline-danger">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-dollar-sign me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Types de Prêts</div>
                                                <small class="text-muted">Configurer</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('company.settings.work-locations.index') }}"
                                        class="btn btn-outline-info">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Pointeuses</div>
                                                <small class="text-muted">Emplacements</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('company.settings.branches.index') }}"
                                        class="btn btn-outline-primary">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-building me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Sites</div>
                                                <small class="text-muted">Succursales</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('company.settings.departments.index') }}"
                                        class="btn btn-outline-info">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-sitemap me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Services</div>
                                                <small class="text-muted">Départements</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('company.settings.designations.index') }}"
                                        class="btn btn-outline-warning">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user-tie me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Postes</div>
                                                <small class="text-muted">Designations</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 mb-3">
                                <div class="d-grid">
                                    <a href="{{ route('company.settings.attendance-system.index') }}"
                                        class="btn btn-outline-secondary">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-clock me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-semibold">Présence</div>
                                                <small class="text-muted">Configuration</small>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour le Logo -->
    <div class="modal fade" id="logoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📷 Mettre à jour le Logo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('company.settings.logo.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nouveau Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*" required>
                            <small class="text-muted">Formats acceptés: JPEG, PNG, GIF, SVG (max 2MB)</small>
                        </div>
                        <div class="text-center">
                            @if($company->logo)
                                <img src="{{ $company->logo_url }}" alt="Logo actuel" class="img-fluid mb-3"
                                    style="max-height: 100px;">
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour la Signature -->
    <div class="modal fade" id="signatureModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✍️ Signature Électronique</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('company.settings.signature.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Signature Électronique</label>
                            <input type="file" class="form-control" name="signature" accept="image/*" required>
                            <small class="text-muted">Formats acceptés: JPEG, PNG, GIF (max 2MB)</small>
                        </div>
                        <div class="text-center">
                            @if($company->electronic_signature)
                                <img src="{{ $company->electronic_signature_url }}" alt="Signature actuelle"
                                    class="img-fluid mb-3" style="max-height: 100px;">
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal pour le Cachet -->
    <div class="modal fade" id="stampModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">🛡️ Cachet Électronique</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('company.settings.stamp.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Cachet Électronique</label>
                            <input type="file" class="form-control" name="stamp" accept="image/*" required>
                            <small class="text-muted">Formats acceptés: JPEG, PNG, GIF (max 2MB)</small>
                        </div>
                        <div class="text-center">
                            @if($company->electronic_stamp)
                                <img src="{{ $company->electronic_stamp_url }}" alt="Cachet actuel" class="img-fluid mb-3"
                                    style="max-height: 100px;">
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fonctions utilitaires
        function refreshSettings() {
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualisation...';
            btn.disabled = true;

            setTimeout(() => {
                location.reload();
            }, 1000);
        }

        function showLogoModal() {
            const modal = new bootstrap.Modal(document.getElementById('logoModal'));
            modal.show();
        }

        function showSignatureModal() {
            const modal = new bootstrap.Modal(document.getElementById('signatureModal'));
            modal.show();
        }

        function showStampModal() {
            const modal = new bootstrap.Modal(document.getElementById('stampModal'));
            modal.show();
        }

        function configurePayroll() {
            // Rediriger vers les paramètres de paie
            alert('Configuration de paie en cours de développement');
        }

        function configureColors() {
            // Rediriger vers les paramètres de couleur
            alert('Configuration des couleurs en cours de développement');
        }

        function configureTheme() {
            // Rediriger vers les paramètres de thème
            alert('Configuration du thème en cours de développement');
        }
    </script>

    <style>
        /* Card hover effects */
        .card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
            transition: all 0.3s ease;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(105, 110, 255, 0.05) 0%, rgba(3, 195, 236, 0.05) 100%);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 12px 12px 0 0 !important;
        }

        .card-header h5 {
            color: #566a7f;
            font-weight: 600;
        }

        /* Avatar improvements */
        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Progress bars */
        .progress {
            background-color: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Button improvements */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #696cff 0%, #03c3ec 100%);
            border-color: transparent;
        }

        /* Badge improvements */
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

        .bg-label-info {
            background-color: rgba(3, 195, 236, 0.1) !important;
            color: #03c3ec !important;
        }

        .bg-label-danger {
            background-color: rgba(255, 77, 77, 0.1) !important;
            color: #ff4d4f !important;
        }

        .bg-label-secondary {
            background-color: rgba(133, 146, 163, 0.1) !important;
            color: #8592a3 !important;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }

            h3 {
                font-size: 1.5rem;
            }

            h5 {
                font-size: 1.125rem;
            }
        }

        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease-out;
        }

        .card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .card:nth-child(3) {
            animation-delay: 0.3s;
        }

        .card:nth-child(4) {
            animation-delay: 0.4s;
        }
    </style>
@endsection