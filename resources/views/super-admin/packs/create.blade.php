@extends('layouts.super-admin')

@section('title', 'Créer un Pack - RH Flow')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-package me-2"></i>
                        Créer un nouveau pack
                    </h5>
                    <small class="text-muted">
                        Configurez les paramètres et fonctionnalités du nouveau pack d'abonnement
                    </small>
                </div>
                <div>
                    <a href="{{ route('super-admin.packs.index') }}" class="btn btn-outline-primary bg-label-primary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour aux packs
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">   
                    <div class="card-body">
                        <form method="POST" action="{{ route('super-admin.packs.store') }}" id="createPackForm">
                            @csrf

                            <div class="row">
                                <!-- Informations de base -->
                                <div class="col-lg-8">
                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-info-alt me-2"></i>
                                        Informations de base
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="name">Nom du pack <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="price">Prix de base ({{ $currencyDetails['symbol'] }}) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                id="price" name="price" value="{{ old('price') }}" step="0.01" required>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="duration">Durée (mois)</label>
                                            <input type="number" class="form-control @error('duration') is-invalid @enderror"
                                                id="duration" name="duration" value="{{ old('duration', 1) }}" min="1" max="12">
                                            @error('duration')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="price_yearly">Prix annuel ({{ $currencyDetails['symbol'] }})</label>
                                            <input type="number" class="form-control @error('price_yearly') is-invalid @enderror"
                                                id="price_yearly" name="price_yearly" value="{{ old('price_yearly') }}" step="0.01">
                                            @error('price_yearly')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="features">Fonctionnalités (une par ligne)</label>
                                        <textarea class="form-control @error('features') is-invalid @enderror"
                                                id="features" name="features" rows="6"
                                                placeholder="Gestion des employés&#10;Pointage manuel&#10;Rapports de base&#10;Support par email&#10;Stockage 5 GB">{{ old('features') }}</textarea>
                                        @error('features')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Limites et capacités -->
                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-settings me-2"></i>
                                        Limites et capacités
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="max_users">Nombre max d'utilisateurs</label>
                                            <input type="number" class="form-control @error('max_users') is-invalid @enderror"
                                                id="max_users" name="max_users" value="{{ old('max_users', 0) }}" min="0">
                                            <small class="text-muted">0 = illimité</small>
                                            @error('max_users')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="max_employees">Nombre max d'employés</label>
                                            <input type="number" class="form-control @error('max_employees') is-invalid @enderror"
                                                id="max_employees" name="max_employees" value="{{ old('max_employees', 0) }}" min="0">
                                            <small class="text-muted">0 = illimité</small>
                                            @error('max_employees')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="storage_limit">Limite de stockage (GB)</label>
                                            <input type="number" class="form-control @error('storage_limit') is-invalid @enderror"
                                                id="storage_limit" name="storage_limit" value="{{ old('storage_limit', 5) }}" step="0.5" min="0">
                                            @error('storage_limit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="nbre_trait">Nombre de traitements</label>
                                            <input type="number" class="form-control @error('nbre_trait') is-invalid @enderror"
                                                id="nbre_trait" name="nbre_trait" value="{{ old('nbre_trait', 1000) }}" min="0">
                                            @error('nbre_trait')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Options et paramètres -->
                                <div class="col-lg-4">
                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-pin-alt me-2"></i>
                                        Options
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('popular') is-invalid @enderror"
                                                type="checkbox" id="popular" name="popular" value="1"
                                                {{ old('popular') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="popular">
                                                Pack populaire
                                            </label>
                                        </div>
                                        @error('popular')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('enterprise') is-invalid @enderror"
                                                type="checkbox" id="enterprise" name="enterprise" value="1"
                                                {{ old('enterprise') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enterprise">
                                                Pack entreprise
                                            </label>
                                        </div>
                                        @error('enterprise')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!--<div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('enable_chatgpt') is-invalid @enderror"
                                                type="checkbox" id="enable_chatgpt" name="enable_chatgpt" value="1"
                                                {{ old('enable_chatgpt') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_chatgpt">
                                                ChatGPT activé
                                            </label>
                                        </div>
                                        @error('enable_chatgpt')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>-->

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('is_active') is-invalid @enderror"
                                                type="checkbox" id="is_active" name="is_active" value="1"
                                                {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Actif
                                            </label>
                                        </div>
                                        @error('is_active')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Actions -->
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-check me-2"></i>
                                            Créer le pack
                                        </button>
                                        <a href="{{ route('super-admin.packs.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x me-2"></i>
                                            Annuler
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcul automatique du prix annuel si durée et prix mensuel sont définis
    const priceInput = document.getElementById('price');
    const durationInput = document.getElementById('duration');
    const priceYearlyInput = document.getElementById('price_yearly');

    function calculateYearlyPrice() {
        const price = parseFloat(priceInput.value) || 0;
        const duration = parseInt(durationInput.value) || 1;

        if (price > 0 && duration > 0) {
            const yearlyPrice = price * 12;
            priceYearlyInput.value = yearlyPrice.toFixed(2);
        }
    }

    priceInput.addEventListener('input', calculateYearlyPrice);
    durationInput.addEventListener('input', calculateYearlyPrice);

    // Validation du formulaire
    const form = document.getElementById('createPackForm');
    form.addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const price = document.getElementById('price').value;

        if (!name) {
            e.preventDefault();
            alert('Le nom du pack est obligatoire');
            return false;
        }

        if (!price || price <= 0) {
            e.preventDefault();
            alert('Le prix doit être supérieur à 0');
            return false;
        }

        // Confirmation pour les packs entreprise
        const isEnterprise = document.getElementById('enterprise').checked;
        if (isEnterprise) {
            if (!confirm('Êtes-vous sûr de vouloir créer un pack entreprise ? Ce type de pack nécessite généralement une configuration personnalisée.')) {
                e.preventDefault();
                return false;
            }
        }

        return true;
    });

    // Prévisualisation des fonctionnalités
    const featuresTextarea = document.getElementById('features');
    featuresTextarea.addEventListener('input', function() {
        const features = this.value.split('\n').filter(f => f.trim());
        console.log('Fonctionnalités:', features);
    });
});
</script>
@endpush
