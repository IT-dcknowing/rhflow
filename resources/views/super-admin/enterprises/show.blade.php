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
        
        <!-- En-tête avec navigation -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-bag me-2"></i>
                        {{ $enterprise->name }}
                    </h5>
                    <small class="text-muted">
                        {{ $company->sector->name }}
                    </small>
                    </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.enterprises.edit', $enterprise) }}" class="btn btn-primary bg-primary text-white">
                        <i class="ti ti-pencil me-2"></i>
                        Modifier
                    </a>
                    <a href="{{ route('super-admin.enterprises.index') }}" class="btn btn-outline-primary bg-label-primary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <!-- Informations générales -->
                            <div class="col-md-8">
                                <div class="card border">
                                    <div class="card-header">
                                        <h6 class="section-title text-primary mb-0">
                                            <i class="ti ti-info-alt me-2"></i>
                                            Informations Générales
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Nom:</strong> {{ $company->name }}</p>
                                                <p><strong>Email:</strong> {{ $company->email }}</p>
                                                <p><strong>Téléphone:</strong> {{ $company->phone ?? 'N/A' }}</p>
                                                <p><strong>Ville:</strong> {{ $company->city ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Plan:</strong>
                                                    <span class="badge bg-primary">{{ $pack->name ?? 'N/A' }}</span>
                                                </p>
                                                <p><strong>Statut:</strong>
                                                    @if($company->is_active == true)
                                                        <span class="badge bg-success">Actif</span>
                                                    @elseif($company->subscription_status === 'trial')
                                                        <span class="badge bg-warning">Essai</span>
                                                    @elseif($company->subscription_status === 'expired')
                                                        <span class="badge bg-danger">Expiré</span>
                                                    @else
                                                        <span class="badge bg-warning">Inactif</span>
                                                    @endif
                                                </p>
                                                <p><strong>Date de création:</strong> {{ $company->created_at->format('d/m/Y H:i') }}</p>
                                                <p><strong>Dernière connexion:</strong> {{ $enterprise->last_login ? $enterprise->last_login->diffForHumans() : 'Jamais' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Utilisation du stockage -->
                                <div class="card border mt-3">
                                    <div class="card-header">
                                        <h6 class="section-title text-primary mb-0">
                                            <i class="ti ti-pie-chart me-2"></i>
                                            Utilisation du Stockage
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Espace utilisé</span>
                                                <span>{{ number_format($company->current_storage_used ?? 0, 1) }} GB / {{ $company->max_storage_gb ?? 5 }} GB</span>
                                            </div>
                                            <div class="progress">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                    style="width: {{ $company->getStorageUsagePercentage() ?? 0 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistiques rapides -->
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header">
                                        <h6 class="section-title text-primary mb-0">
                                            <i class="ti ti-stats-up me-2"></i>
                                            Statistiques
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <div class="app-brand mb-2" style="justify-content:center; align-items:center;">
                                                @if($company->logo)
                                                    <img src="{{ asset('storage/logos/' . $company->logo) }}" alt="Logo" class="logo logo-lg" width="25%">
                                                @else
                                                    <span class="avatar-initial bg-primary rounded-circle">{{ substr($company->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h4 class="mb-1">{{ $company->employees_count ?? 0 }}</h4>
                                                <small class="text-muted">Employés</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="mb-1">{{ $company->max_employees ?? 0 }}</h4>
                                                <small class="text-muted">Limite</small>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="row text-center">
                                            <div class="col-6">
                                                <h4 class="mb-1">{{ $enterprise->storage_limit ?? 5 }} GB</h4>
                                                <small class="text-muted">Stockage</small>
                                            </div>
                                            <div class="col-6">
                                                <h4 class="mb-1">{{ $enterprise->nbre_trait ?? 1000 }}</h4>
                                                <small class="text-muted">Traitements</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions rapides -->
                                <div class="card border mt-3">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-settings me-2"></i>
                                            Actions
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            @if($company->is_active == true)
                                                <a href="{{ route('super-admin.enterprises.suspend', $enterprise) }}" class="btn btn-outline-warning" onclick="return confirm('Êtes-vous sûr de vouloir suspendre cette entreprise ?')">
                                                    <i class="ti ti-control-pause me-2"></i>
                                                    Suspendre
                                                </a>
                                            @else
                                                <a href="{{ route('super-admin.enterprises.activate', $enterprise) }}" class="btn btn-outline-success" onclick="return confirm('Êtes-vous sûr de vouloir activer cette entreprise ?')">
                                                    <i class="ti ti-control-play me-2"></i>
                                                    Activer
                                                </a>
                                            @endif

                                            <a href="{{ route('super-admin.enterprises.users', $enterprise) }}" class="btn btn-outline-info">
                                                <i class="ti ti-user me-2"></i>
                                                Voir les utilisateurs
                                            </a>

                                            <a href="{{ route('super-admin.enterprises.subscription', $enterprise) }}" class="btn btn-outline-secondary">
                                                <i class="ti ti-credit-card me-2"></i>
                                                Gérer l'abonnement
                                            </a>

                                            <form action="{{ route('super-admin.enterprises.delete', $enterprise) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger col-md-12" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')">
                                                    <i class="ti ti-trash me-2"></i>
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

    .avatar-xl {
        width: 4rem;
        height: 4rem;
    }

    .avatar-initial {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        font-weight: 600;
        font-size: 1rem;
        border-radius: 50%;
    }

    .progress {
        height: 8px;
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
    }, 5000); // 5 seconds
});
</script>
@endpush
