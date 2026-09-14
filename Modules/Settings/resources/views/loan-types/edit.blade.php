@extends('layouts.app')

@section('title', 'Modifier un Type de Prêt - RH Flow')

@push('styles')
<style>
    .card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: none;
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .form-label {
        font-weight: 600;
        color: #566a7f;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #696cff;
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.25);
    }

    .invalid-feedback {
        font-size: 0.875rem;
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        color: #dc3545;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .required:after {
        content: ' *';
        color: #dc3545;
    }

    .form-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .section-title {
        color: #566a7f;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e0e6ed;
    }

    .help-text {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #696cff;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
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
                        Modifier le Type de Prêt
                    </h4>
                    <p class="text-muted mb-0">Mettez à jour les informations du type de prêt</p>
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
                            <li class="breadcrumb-item active">Modifier</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('company.settings.loan-types.show', $loanType->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Voir
                    </a>
                    <a href="{{ route('company.settings.loan-types.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Veuillez corriger les erreurs suivantes :</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('company.settings.loan-types.update', $loanType->id) }}" id="loanTypeForm">
                        @csrf
                        @method('PUT')

                        <!-- Informations générales -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>Informations Générales
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label required">Nom du Type de Prêt</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $loanType->name) }}" 
                                           placeholder="Ex: Prêt personnel, Prêt logement..."
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="max_amount" class="form-label required">Montant Maximum (FCFA)</label>
                                    <input type="number" 
                                           class="form-control @error('max_amount') is-invalid @enderror" 
                                           id="max_amount" 
                                           name="max_amount" 
                                           value="{{ old('max_amount', $loanType->max_amount) }}" 
                                           placeholder="5000000"
                                           min="0"
                                           step="1000"
                                           required>
                                    @error('max_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="interest_rate" class="form-label required">Taux d'Intérêt (%)</label>
                                    <input type="number" 
                                           class="form-control @error('interest_rate') is-invalid @enderror" 
                                           id="interest_rate" 
                                           name="interest_rate" 
                                           value="{{ old('interest_rate', $loanType->interest_rate) }}" 
                                           placeholder="5.5"
                                           min="0"
                                           max="100"
                                           step="0.1"
                                           required>
                                    @error('interest_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Statut</label>
                                    <div class="d-flex align-items-center">
                                        <label class="switch">
                                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $loanType->is_active) ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <span class="ms-2">Actif</span>
                                    </div>
                                    <div class="help-text">Un type de prêt inactif ne pourra pas être utilisé</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3"
                                          placeholder="Description détaillée du type de prêt...">{{ old('description', $loanType->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Période de remboursement -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-clock me-2"></i>Période de Remboursement
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="repayment_period_min" class="form-label required">Période Minimale (mois)</label>
                                    <input type="number" 
                                           class="form-control @error('repayment_period_min') is-invalid @enderror" 
                                           id="repayment_period_min" 
                                           name="repayment_period_min" 
                                           value="{{ old('repayment_period_min', $loanType->repayment_period_min) }}" 
                                           placeholder="1"
                                           min="1"
                                           required>
                                    @error('repayment_period_min')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="repayment_period_max" class="form-label required">Période Maximale (mois)</label>
                                    <input type="number" 
                                           class="form-control @error('repayment_period_max') is-invalid @enderror" 
                                           id="repayment_period_max" 
                                           name="repayment_period_max" 
                                           value="{{ old('repayment_period_max', $loanType->repayment_period_max) }}" 
                                           placeholder="12"
                                           min="1"
                                           required>
                                    @error('repayment_period_max')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Conditions de garantie -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-shield-alt me-2"></i>Conditions de Garantie
                            </h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Garantie Requise</label>
                                <div class="d-flex align-items-center">
                                    <label class="switch">
                                        <input type="checkbox" name="requires_guarantor" value="1" id="requires_guarantor" {{ old('requires_guarantor', $loanType->requires_guarantor) ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    <span class="ms-2">Ce type de prêt nécessite une garantie</span>
                                </div>
                            </div>

                            <div class="mb-3" id="guarantor_conditions_section" style="display: {{ old('requires_guarantor', $loanType->requires_guarantor) ? 'block' : 'none' }};">
                                <label for="guarantor_conditions" class="form-label">Conditions de Garantie</label>
                                <textarea class="form-control @error('guarantor_conditions') is-invalid @enderror" 
                                          id="guarantor_conditions" 
                                          name="guarantor_conditions" 
                                          rows="3"
                                          placeholder="Décrivez les conditions de garantie requises...">{{ old('guarantor_conditions', $loanType->guarantor_conditions) }}</textarea>
                                @error('guarantor_conditions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('company.settings.loan-types.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informations actuelles -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info me-2"></i>Informations Actuelles
                    </h5>
                    <div class="mb-3">
                        <small class="text-muted">Nom</small>
                        <div class="fw-semibold">{{ $loanType->name }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Montant Max</small>
                        <div class="fw-semibold text-primary">{{ $loanType->formatted_max_amount }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Taux d'intérêt</small>
                        <div class="fw-semibold">{{ $loanType->formatted_interest_rate }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Période</small>
                        <div class="fw-semibold">{{ $loanType->formatted_repayment_period }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Statut actuel</small>
                        <div>
                            @if($loanType->is_active)
                                <span class="badge bg-label-success">Actif</span>
                            @else
                                <span class="badge bg-label-danger">Inactif</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Dernière modification</small>
                        <div>{{ $loanType->updated_at->format('d/m/Y H:i') }}</div>
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
    // Gestion de l'affichage des conditions de garantie
    const requiresGuarantorCheckbox = document.getElementById('requires_guarantor');
    const guarantorConditionsSection = document.getElementById('guarantor_conditions_section');

    if (requiresGuarantorCheckbox && guarantorConditionsSection) {
        function toggleGuarantorConditions() {
            guarantorConditionsSection.style.display = requiresGuarantorCheckbox.checked ? 'block' : 'none';
        }

        requiresGuarantorCheckbox.addEventListener('change', toggleGuarantorConditions);
        toggleGuarantorConditions(); // Initial state
    }

    // Validation du formulaire
    const form = document.getElementById('loanTypeForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const repaymentMin = parseInt(document.getElementById('repayment_period_min').value);
            const repaymentMax = parseInt(document.getElementById('repayment_period_max').value);
            
            if (repaymentMax < repaymentMin) {
                e.preventDefault();
                alert('La période maximale doit être supérieure ou égale à la période minimale');
                return false;
            }
        });
    }

    // Formatage automatique du montant
    const maxAmountInput = document.getElementById('max_amount');
    if (maxAmountInput) {
        maxAmountInput.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (!isNaN(value)) {
                this.value = Math.round(value / 1000) * 1000;
            }
        });
    }
});
</script>
@endpush
