@extends('layouts.app')

@section('title', 'Vérification des configurations')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- En-tête -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                            Configuration requise avant la création d'employé
                        </h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Pour pouvoir créer un employé, vous devez d'abord configurer les éléments suivants pour votre entreprise <strong>{{ $company->name }}</strong>.
                        </p>
                        
                        @if(count($missingConfigs) > 1)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Plusieurs configurations sont requises. Vous pouvez les configurer dans n'importe quel ordre.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Configurations manquantes -->
        <div class="row">
            @foreach($missingConfigs as $config)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-{{ $config['type'] === 'succursale' ? 'primary' : ($config['type'] === 'service' ? 'info' : 'warning') }}">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($config['type'] === 'succursale')
                                    <i class="fas fa-building fa-3x text-primary"></i>
                                @elseif($config['type'] === 'service')
                                    <i class="fas fa-sitemap fa-3x text-info"></i>
                                @else
                                    <i class="fas fa-briefcase fa-3x text-warning"></i>
                                @endif
                            </div>
                            
                            <h5 class="card-title mb-3">
                                {{ ucfirst($config['type']) }}
                            </h5>
                            
                            <p class="card-text text-muted">
                                {{ $config['message'] }}
                            </p>
                            
                            <div class="mt-auto">
                                <a href="{{ route($config['route']) }}" class="btn btn-{{ $config['type'] === 'succursale' ? 'primary' : ($config['type'] === 'service' ? 'info' : 'warning') }}">
                                    <i class="fas fa-plus me-2"></i>
                                    {{ $config['button_text'] }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Une fois les configurations terminées</h6>
                                <p class="text-muted mb-0">Vous pourrez revenir à cette page pour créer votre employé</p>
                            </div>
                            <div>
                                <a href="{{ route('company.employees.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Retour à la liste
                                </a>
                                <button onclick="location.reload()" class="btn btn-success" id="refreshBtn">
                                    <i class="fas fa-sync me-2"></i>
                                    Vérifier à nouveau
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter une animation de rotation au bouton d'actualisation
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.classList.add('fa-spin');
            
            // Désactiver le bouton pendant l'actualisation
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-sync fa-spin me-2"></i>Vérification...';
            
            // La page va se recharger, donc pas besoin de réactiver le bouton
        });
    }
});
</script>
@endpush
