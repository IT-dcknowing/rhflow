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
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-pencil me-2"></i>
                        Modifier l'entreprise: {{ $enterprises->name }}
                    </h5>
                    <small class="text-muted">
                        Modifiez les informations de l'entreprise
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('super-admin.enterprises.show', $enterprises->user_id) }}" class="btn btn-outline-info">
                        <i class="ti ti-eye me-2"></i>
                        Voir détails
                    </a>
                    <a href="{{ route('super-admin.enterprises.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">  
                    <div class="card-body">
                        <form action="{{ route('super-admin.enterprises.update', $enterprise) }}" method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Informations générales -->
                                <div class="col-lg-7">
                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-info-alt me-2"></i>Informations Générales
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $enterprises->name) }}"
                                                oninput="generateProposal()"
                                                required
                                                placeholder="Ex: TechCorp SARL">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email de l'entreprise(ou du DG) <span class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                id="email"
                                                name="email"
                                                value="{{ old('email', $enterprises->email) }}"
                                                oninput="repeatEmail()"
                                                required
                                                placeholder="contact@entreprise.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Téléphone</label>
                                            <input type="tel"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                id="phone"
                                                name="phone"
                                                value="{{ old('phone', $enterprises->phone) }}"
                                                placeholder="+225 01 02 234 567"
                                                data-intl-tel-input>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="website" class="form-label">Site web</label>
                                            <input type="url"
                                                class="form-control @error('website') is-invalid @enderror"
                                                id="website"
                                                name="website"
                                                value="{{ old('website', $enterprises->website) }}"
                                                placeholder="https://www.entreprise.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="industry" class="form-label">Secteur d'activité <span class="text-danger">*</span></label>
                                        <select class="form-select select2 @error('industry') is-invalid @enderror"
                                                id="industry"
                                                name="industry"
                                                required>
                                            <option value="">Choisir un secteur...</option>
                                            @foreach($sectors as $sector)
                                                <option value="{{ $sector->slug }}" {{ old('industry', $enterprises->industry) == $sector->slug ? 'selected' : '' }}>{{ $sector->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('industry')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="size" class="form-label">Taille de l'entreprise *</label>
                                            <select class="form-select @error('size') is-invalid @enderror"
                                                    id="size"
                                                    name="size"
                                                    required>
                                                <option value="">Choisir la taille...</option>
                                                <option value="startup" {{ old('size', $enterprises->size) == 'startup' ? 'selected' : '' }}>Startup</option>
                                                <option value="small" {{ old('size', $enterprises->size) == 'small' ? 'selected' : '' }}>Petite entreprise (1-50 employés)</option>
                                                <option value="medium" {{ old('size', $enterprises->size) == 'medium' ? 'selected' : '' }}>Moyenne entreprise (51-200 employés)</option>
                                                <option value="large" {{ old('size', $enterprises->size) == 'large' ? 'selected' : '' }}>Grande entreprise (201-1000 employés)</option>
                                                <option value="enterprise" {{ old('size', $enterprises->size) == 'enterprise' ? 'selected' : '' }}>Grande entreprise (1000+ employés)</option>
                                            </select>
                                            @error('size')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="timezone" class="form-label">Fuseau horaire</label>
                                            <select class="form-select @error('timezone') is-invalid @enderror"
                                                    id="timezone"
                                                    name="timezone"
                                                    required>
                                                <option value="">Choisir un fuseau horaire...</option>
                                                <option value="Africa/Abidjan" {{ old('timezone', $enterprises->timezone) == 'Africa/Abidjan' ? 'selected' : '' }}>Africa/Abidjan (GMT+0)</option>
                                                <option value="Europe/Paris" {{ old('timezone', $enterprises->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (GMT+1)</option>
                                                <option value="Europe/London" {{ old('timezone', $enterprises->timezone) == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT+0)</option>
                                                <option value="America/New_York" {{ old('timezone', $enterprises->timezone) == 'America/New_York' ? 'selected' : '' }}>America/New_York (GMT-5)</option>
                                                <option value="America/Montreal" {{ old('timezone', $enterprises->timezone) == 'America/Montreal' ? 'selected' : '' }}>America/Montreal (GMT-5)</option>  
                                                <option value="Asia/Tokyo" {{ old('timezone', $enterprises->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (GMT+9)</option>
                                            </select>
                                            @error('timezone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="logo" class="form-label">Logo</label>
                                        <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
                                        @error('logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                id="description"
                                                name="description"
                                                rows="3"
                                                placeholder="Brève description de l'entreprise...">{{ old('description', $enterprises->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-user me-2"></i>Informations du compte
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email_connexion" class="form-label">Email de connexion <span class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('email_connexion') is-invalid @enderror"
                                                id="email_connexion"
                                                name="email_connexion"
                                                value="{{ old('email_connexion', $enterprise->email) }}"
                                                required>
                                            @error('email_connexion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('username') is-invalid @enderror"
                                                id="username"
                                                name="username"
                                                value="{{ old('username', $enterprise->username) }}"
                                                readonly
                                                required>
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Configuration de l'abonnement -->
                                <div class="col-lg-5">
                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-settings me-2"></i>Configuration de l'Abonnement
                                    </h6>

                                    <div class="mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input"
                                                    type="checkbox"
                                                    id="is_active"
                                                    name="is_active"
                                                    value="1"
                                                    {{ old('is_active', $enterprise->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Entreprise active
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="plan" class="form-label">Plan d'abonnement <span class="text-danger">*</span></label>
                                        <select class="form-select @error('plan') is-invalid @enderror"
                                                id="plan"
                                                name="plan"
                                                required>
                                            <option value="">Choisir un plan...</option>
                                            @foreach($packs as $packs)
                                                <option value="{{ $packs->id }}" {{ old('plan', $enterprises->plan_id) == $packs->id ? 'selected' : '' }}>{{ $packs->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('plan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div id="subscriptionConfig" class="mb-3" style="display:none;">
                                        <div class="border rounded p-3 bg-light">
                                            <h6 class="mb-3">
                                            <i class="ti ti-bolt me-2"></i>Activation du Pack
                                            </h6>

                                            <div class="row g-3 align-items-center mb-2">
                                            <div class="col-12">
                                                <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="activate_now" value="now">
                                                <label class="form-check-label" for="activate_now">Activer maintenant</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="activate_later" value="later">
                                                <label class="form-check-label" for="activate_later">Activer ultérieurement</label>
                                                </div>
                                                <input type="hidden" name="activation_timing" id="activation_timing" value="{{ old('activation_timing', $enterprise->is_active ? 'now' : 'later') }}">
                                            </div>
                                            </div>

                                            <div class="row g-3 mb-2" id="startDateRow" style="display:none;">
                                            <div class="col-md-6">
                                                <label for="subscription_start_date" class="form-label">Date de début</label>
                                                <input type="date" class="form-control" id="subscription_start_date" name="subscription_start_date" value="{{ old('subscription_start_date', optional($enterprises->subscription_start_date)->format('Y-m-d')) }}">
                                            </div>
                                            </div>

                                            <hr class="my-3">

                                            <h6 class="mb-2">
                                            <i class="ti ti-calendar-stats me-2"></i>Période de facturation
                                            </h6>
                                            <div class="row g-3 align-items-center mb-2">
                                            <div class="col-12">
                                                <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="billing_period" id="billing_monthly" value="monthly" {{ old('billing_period', 'monthly') == 'monthly' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="billing_monthly">Mensuel (par défaut)</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="billing_period" id="billing_yearly" value="yearly" {{ old('billing_period') == 'yearly' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="billing_yearly">Annuel</label>
                                                </div>
                                            </div>
                                            </div>

                                            <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="calculated_start_date" class="form-label">Début effectif</label>
                                                <input type="date" class="form-control" id="calculated_start_date" value="{{ old('subscription_start_date', optional($enterprises->subscription_start_date)->format('Y-m-d')) }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="subscription_end_date" class="form-label">Date de fin</label>
                                                <input type="date" class="form-control" id="subscription_end_date" name="subscription_end_date" value="{{ old('subscription_end_date', optional($enterprises->subscription_end_date)->format('Y-m-d')) }}" readonly>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="storage_limit" class="form-label">Limite de stockage (GB) <span class="text-danger">*</span></label>
                                        <input type="number"
                                            class="form-control @error('storage_limit') is-invalid @enderror"
                                            id="storage_limit"
                                            name="storage_limit"
                                            value="{{ old('storage_limit', $enterprise->storage_limit) }}"
                                            min="1"
                                            max="2000"
                                            step="0.5"
                                            required>
                                        @error('storage_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Stockage alloué à l'entreprise en GB</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="working_hours_per_week" class="form-label">Heures de travail par semaine</label>
                                        <input type="number"
                                            class="form-control @error('working_hours_per_week') is-invalid @enderror"
                                            id="working_hours_per_week"
                                            name="working_hours_per_week"
                                            value="{{ old('working_hours_per_week', $enterprises->working_hours_per_week) }}"
                                            min="1"
                                            max="168">
                                        @error('working_hours_per_week')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="working_days_per_week" class="form-label">Jours travaillés par semaine</label>
                                        <input type="number"
                                            class="form-control @error('working_days_per_week') is-invalid @enderror"
                                            id="working_days_per_week"
                                            name="working_days_per_week"
                                            value="{{ old('working_days_per_week', $enterprises->working_days_per_week) }}"
                                            min="1"
                                            max="7">
                                        @error('working_days_per_week')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Adresse -->
                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-map-alt me-2"></i>Adresse
                                    </h6>

                                    <div class="row">
                                        <!-- Champ de localisation principale avec autocomplétion -->
                                        <div class="col-md-12 mb-3">
                                            <label for="location" class="form-label">
                                                Localisation
                                                <span class="text-muted small">(Ville, Région ou Pays)</span>
                                            </label>
                                            <div class="position-relative">
                                                <input type="text" 
                                                    class="form-control @error('location') is-invalid @enderror" 
                                                    id="location"
                                                    name="location"
                                                    value="{{ old('location', $enterprises->address) }}"
                                                    placeholder="Rechercher une ville, région ou pays..."
                                                    autocomplete="off">
                                                <input type="hidden" id="location_data" name="location_data" value="{{ old('location_data', $enterprises->location_data) }}">
                                                @error('location')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Commencez à taper pour voir les suggestions (minimum 3 caractères)
                                            </small>
                                        </div>

                                        <!-- Informations additionnelles (auto-remplies) -->
                                        <div class="col-md-6 mb-3">
                                            <label for="city" class="form-label">Ville</label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="city"
                                                name="city"
                                                value="{{ old('city', $enterprises->city) }}"
                                                readonly
                                                placeholder="Sera rempli automatiquement">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="postal_code" class="form-label">Code postal</label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="postal_code"
                                                name="postal_code"
                                                value="{{ old('postal_code', $enterprises->postal_code) }}"
                                                placeholder="Code postal">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="state" class="form-label">Région / État</label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="state"
                                                name="state"
                                                value="{{ old('state', $enterprises->state) }}"
                                                readonly
                                                placeholder="Sera rempli automatiquement">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="country" class="form-label">Pays</label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="country"
                                                name="country"
                                                value="{{ old('country', $enterprises->country) }}"
                                                readonly
                                                placeholder="Sera rempli automatiquement">
                                        </div>                                

                                        <!-- Coordonnées géographiques (cachées mais utiles) -->
                                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $enterprises->latitude) }}">
                                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $enterprises->longitude) }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Informations administratives -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-info-alt me-2"></i>Informations Administratives
                                    </h6>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tax_id" class="form-label">N° Compte Contribuable</label>
                                    <input type="text"
                                        class="form-control @error('tax_id') is-invalid @enderror"
                                        id="tax_id"
                                        name="tax_id"
                                        value="{{ old('tax_id', $enterprises->tax_id) }}"
                                        maxlength="8"
                                        placeholder="9727498F">
                                    @error('tax_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="registration_number" class="form-label">N° RCCM</label>
                                    <input type="text"
                                        class="form-control @error('registration_number') is-invalid @enderror"
                                        id="registration_number"
                                        name="registration_number"
                                        value="{{ old('registration_number', $enterprises->registration_number) }}"
                                        placeholder="CI-ABJ-03-2023-B13-00452">
                                    @error('registration_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('super-admin.enterprises.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-arrow-left me-2"></i>
                                            Annuler
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-lg text-white" id="submitBtn">
                                            <i class="ti ti-check me-2"></i>
                                            Mettre à jour l'Entreprise
                                        </button>
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
</style>
@endpush

@push('scripts')
<script>
// ------- Subscription dynamic UI -------
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan');
    const configBox = document.getElementById('subscriptionConfig');
    const activateNow = document.getElementById('activate_now');
    const activateLater = document.getElementById('activate_later');
    const activationTiming = document.getElementById('activation_timing');
    const startDateRow = document.getElementById('startDateRow');
    const startDateInput = document.getElementById('subscription_start_date');
    const calcStartDate = document.getElementById('calculated_start_date');
    const endDateInput = document.getElementById('subscription_end_date');
    const billingMonthly = document.getElementById('billing_monthly');
    const billingYearly = document.getElementById('billing_yearly');

    function formatDateToInput(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function addMonths(date, months) {
        const d = new Date(date.getTime());
        const day = d.getDate();
        d.setMonth(d.getMonth() + months);
        if (d.getDate() < day) { d.setDate(0); } // dernier jour du mois si overflow
        return d;
    }

    function addYears(date, years) {
        const d = new Date(date.getTime());
        d.setFullYear(d.getFullYear() + years);
        return d;
    }

    function recalcDates() {
        let start;
        if (activationTiming.value === 'now') {
            start = new Date();
        } else {
            start = startDateInput.value ? new Date(startDateInput.value) : new Date();
        }
        calcStartDate.value = formatDateToInput(start);

        let end;
        if (billingYearly && billingYearly.checked) {
            end = addYears(start, 1);
        } else {
            end = addMonths(start, 1);
        }
        endDateInput.value = formatDateToInput(end);
    }

    function toggleConfigVisibility() {
        configBox.style.display = planSelect && planSelect.value ? 'block' : 'none';
    }

    function enforceMutualExclusion(changed) {
        if (changed === 'now' && activateNow.checked) {
            activateLater.checked = false;
            activationTiming.value = 'now';
            startDateRow.style.display = 'none';
        } else if (changed === 'later' && activateLater.checked) {
            activateNow.checked = false;
            activationTiming.value = 'later';
            startDateRow.style.display = 'flex';
        } else if (!activateNow.checked && !activateLater.checked) {
            // par défaut: now
            activateNow.checked = true;
            activationTiming.value = 'now';
            startDateRow.style.display = 'none';
        }
        recalcDates();
    }

    function initFromOld() {
        toggleConfigVisibility();

        const oldTiming = activationTiming.value || 'now';
        if (oldTiming === 'later') {
            activateLater.checked = true;
            activateNow.checked = false;
            startDateRow.style.display = 'flex';
        } else {
            activateNow.checked = true;
            activateLater.checked = false;
            startDateRow.style.display = 'none';
        }

        recalcDates();
    }

    if (planSelect) {
        planSelect.addEventListener('change', function() {
            toggleConfigVisibility();
            recalcDates();
        });
    }
    if (activateNow) activateNow.addEventListener('change', () => enforceMutualExclusion('now'));
    if (activateLater) activateLater.addEventListener('change', () => enforceMutualExclusion('later'));
    if (billingMonthly) billingMonthly.addEventListener('change', recalcDates);
    if (billingYearly) billingYearly.addEventListener('change', recalcDates);
    if (startDateInput) startDateInput.addEventListener('change', recalcDates);

    initFromOld();
});

function validateTaxId() {
    const taxIdInput = document.getElementById('tax_id');
    const taxIdPattern = /^[0-9]{7}[A-Z]$/;

    if (!taxIdPattern.test(taxIdInput.value)) {
        taxIdInput.classList.add('is-invalid');
        taxIdInput.classList.remove('is-valid');
    } else {
        taxIdInput.classList.remove('is-invalid');
        taxIdInput.classList.add('is-valid');
    }
}

function validateRegistrationNumber() {
    const registrationNumberInput = document.getElementById('registration_number');
    const registrationNumberPattern = /CI/; // Vérifie simplement si 'CI' est présent

    if (!registrationNumberPattern.test(registrationNumberInput.value)) {
        registrationNumberInput.classList.add('is-invalid');
        registrationNumberInput.classList.remove('is-valid');
    } else {
        registrationNumberInput.classList.remove('is-invalid');
        registrationNumberInput.classList.add('is-valid');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const taxIdInput = document.getElementById('tax_id');
    const registrationNumberInput = document.getElementById('registration_number');

    taxIdInput.addEventListener('input', validateTaxId);
    registrationNumberInput.addEventListener('input', validateRegistrationNumber);
});

function generateProposal() {
    var nameField = document.getElementById('name');
    var usernameField = document.getElementById('username');
    var username = nameField.value.replace(/\s+/g, '').toUpperCase(); // Supprime tous les espaces et convertit en majuscules

    if (username.length >= 4) {
        var firstFourLetters = username.substring(0, 4); // Prend les quatre premières lettres
        var randomDigits = Math.floor(10 + Math.random() * 90); // Génère deux chiffres aléatoires
        var proposal = firstFourLetters + randomDigits; // Combine les lettres et les chiffres

        usernameField.value = proposal; // Met à jour la valeur du champ avec la proposition générée
    }
}

function repeatEmail() {
    var emailField = document.getElementById('email');
    var email = emailField.value;
    var usernameField = document.getElementById('email_connexion');
    usernameField.value = email;
}

document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.querySelector('.needs-validation');

    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        // Désactiver le bouton
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="ti ti-loader me-2"></i>Création...';

        form.classList.add('was-validated');
    });

    // Gestion globale des erreurs de réseau
    window.addEventListener('offline', function() {
        console.log('Mode hors ligne détecté');
        showNetworkStatus('Hors ligne - Certaines fonctionnalités peuvent être limitées', 'warning');
    });

    window.addEventListener('online', function() {
        console.log('Connexion rétablie');
        showNetworkStatus('Connexion rétablie', 'success');
    });

    // Fonction pour afficher le statut réseau
    function showNetworkStatus(message, type) {
        // Supprimer les anciennes notifications de statut
        const existingStatus = document.querySelector('.network-status');
        if (existingStatus) {
            existingStatus.remove();
        }

        const statusDiv = document.createElement('div');
        statusDiv.className = `network-status alert alert-${type} alert-dismissible fade show`;
        statusDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        statusDiv.innerHTML = `
            <i class="fas fa-wifi me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(statusDiv);

        // Auto-fermeture après 5 secondes
        setTimeout(() => {
            if (statusDiv && statusDiv.parentElement) {
                const bsAlert = new bootstrap.Alert(statusDiv);
                bsAlert.close();
            }
        }, 5000);
    }

    // Aperçu du logo (si upload de fichier)
    const logoInput = document.getElementById('logo');
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logoPreview');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Calcul automatique des heures par jour
    const hoursInput = document.getElementById('working_hours_per_week');
    const daysInput = document.getElementById('working_days_per_week');

    function updateDailyHours() {
        const hours = parseInt(hoursInput.value) || 0;
        const days = parseInt(daysInput.value) || 1;
        const dailyHours = hours / days;

        const infoElement = document.getElementById('dailyHoursInfo');
        if (infoElement) {
            infoElement.textContent = `≈ ${dailyHours.toFixed(1)} heures par jour`;
        }
    }

    if (hoursInput && daysInput) {
        hoursInput.addEventListener('input', updateDailyHours);
        daysInput.addEventListener('input', updateDailyHours);
        updateDailyHours(); // Calcul initial
    }

    // ========================================
    // AUTOCOMPLETION FUSEAUX HORAIRES
    // ========================================

    const timezoneSelect = document.getElementById('timezone');

    // Fonction pour récupérer tous les fuseaux horaires
    async function loadTimezones() {
        try {
            // Utiliser l'API Intl pour obtenir les fuseaux horaires
            const timezones = Intl.supportedValuesOf('timeZone');

            // Obtenir l'offset actuel pour chaque fuseau
            const now = new Date();
            const timezoneData = timezones.map(tz => {
                const formatter = new Intl.DateTimeFormat('en', {
                    timeZone: tz,
                    timeZoneName: 'longOffset'
                });

                const parts = formatter.formatToParts(now);
                const offsetPart = parts.find(part => part.type === 'timeZoneName');
                const offset = offsetPart ? offsetPart.value : '';

                // Obtenir le nom de la région/ville
                const cityName = tz.split('/').pop().replace(/_/g, ' ');
                const regionName = tz.split('/')[0].replace(/_/g, ' ');

                return {
                    value: tz,
                    label: `${tz} (${offset})`,
                    city: cityName,
                    region: regionName,
                    offset: offset,
                    search: `${tz} ${cityName} ${regionName} ${offset}`.toLowerCase()
                };
            });

            // Trier par région puis par ville
            timezoneData.sort((a, b) => {
                if (a.region !== b.region) {
                    return a.region.localeCompare(b.region);
                }
                return a.city.localeCompare(b.city);
            });

            return timezoneData;

        } catch (error) {
            console.error('Erreur lors du chargement des fuseaux horaires:', error);
            // Fallback avec des fuseaux horaires communs
            return [
                { value: 'Europe/Paris', label: 'Europe/Paris (GMT+1)', search: 'europe paris gmt+1' },
                { value: 'Europe/London', label: 'Europe/London (GMT+0)', search: 'europe london gmt+0' },
                { value: 'America/New_York', label: 'America/New_York (GMT-5)', search: 'america new york gmt-5' },
                { value: 'America/Los_Angeles', label: 'America/Los_Angeles (GMT-8)', search: 'america los angeles gmt-8' },
                { value: 'Asia/Tokyo', label: 'Asia/Tokyo (GMT+9)', search: 'asia tokyo gmt+9' },
                { value: 'Africa/Abidjan', label: 'Africa/Abidjan (GMT+0)', search: 'africa abidjan gmt+0' },
                { value: 'Africa/Cairo', label: 'Africa/Cairo (GMT+2)', search: 'africa cairo gmt+2' },
                { value: 'Asia/Dubai', label: 'Asia/Dubai (GMT+4)', search: 'asia dubai gmt+4' },
                { value: 'Australia/Sydney', label: 'Australia/Sydney (GMT+10)', search: 'australia sydney gmt+10' }
            ];
        }
    }

    // Créer l'autocomplétion pour les fuseaux horaires
    async function createTimezoneAutocomplete() {
        const timezones = await loadTimezones();

        // Remplacer le select par un input avec datalist
        const timezoneContainer = timezoneSelect.parentElement;

        // Créer le nouveau input
        const timezoneInput = document.createElement('input');
        timezoneInput.type = 'text';
        timezoneInput.id = 'timezone';
        timezoneInput.name = 'timezone';
        timezoneInput.className = timezoneSelect.className;
        timezoneInput.required = timezoneSelect.required;
        timezoneInput.placeholder = 'Rechercher un fuseau horaire...';
        timezoneInput.value = timezoneSelect.value || 'Europe/Paris';
        timezoneInput.setAttribute('list', 'timezone-list');
        timezoneInput.setAttribute('autocomplete', 'off');

        // Créer la datalist
        const datalist = document.createElement('datalist');
        datalist.id = 'timezone-list';

        // Remplir la datalist
        timezones.forEach(tz => {
            const option = document.createElement('option');
            option.value = tz.value;
            option.textContent = tz.label;
            datalist.appendChild(option);
        });

        // Remplacer l'ancien select
        timezoneContainer.replaceChild(timezoneInput, timezoneSelect);
        timezoneContainer.appendChild(datalist);

        // Ajouter la fonctionnalité de recherche avancée
        let searchTimeout;
        let suggestionBox;

        timezoneInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                showTimezoneSuggestions(query, timezones, this);
            }, 300);
        });

        timezoneInput.addEventListener('focus', function() {
            if (this.value === '') {
                showTimezoneSuggestions('', timezones.slice(0, 10), this);
            }
        });

        timezoneInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (suggestionBox) {
                    suggestionBox.remove();
                    suggestionBox = null;
                }
            }, 200);
        });

        function showTimezoneSuggestions(query, timezones, input) {
            // Supprimer l'ancienne suggestion box
            if (suggestionBox) {
                suggestionBox.remove();
            }

            // Filtrer les fuseaux horaires
            let filtered = timezones;
            if (query) {
                filtered = timezones.filter(tz =>
                    tz.search.includes(query) ||
                    tz.value.toLowerCase().includes(query)
                ).slice(0, 10);
            } else {
                filtered = timezones.slice(0, 10);
            }

            if (filtered.length === 0) return;

            // Créer la suggestion box
            suggestionBox = document.createElement('div');
            suggestionBox.className = 'timezone-suggestions';
            suggestionBox.style.cssText = `
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 1px solid #ddd;
                border-top: none;
                max-height: 200px;
                overflow-y: auto;
                z-index: 1000;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            `;

            filtered.forEach(tz => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                item.style.cssText = `
                    padding: 8px 12px;
                    cursor: pointer;
                    border-bottom: 1px solid #f0f0f0;
                    font-size: 14px;
                `;
                item.innerHTML = `
                    <div style="font-weight: 500;">${tz.city}, ${tz.region}</div>
                    <small style="color: #666;">${tz.value} - ${tz.offset}</small>
                `;

                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'white';
                });

                item.addEventListener('click', function() {
                    input.value = tz.value;
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    suggestionBox.remove();
                    suggestionBox = null;
                });

                suggestionBox.appendChild(item);
            });

            // Positionner la suggestion box
            const inputRect = input.getBoundingClientRect();
            input.parentElement.style.position = 'relative';
            input.parentElement.appendChild(suggestionBox);
        }
    }


    // ========================================
    // AUTOCOMPLETION LOCALISATION GÉOGRAPHIQUE
    // ========================================

    function initializeLocationAutocomplete() {
        const locationInput = document.getElementById('location');
        if (!locationInput) return;

        let searchTimeout;
        let suggestionBox;
        let selectedLocation = null;

        locationInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(searchTimeout);
            
            if (query.length >= 3) {
                searchTimeout = setTimeout(() => {
                    searchLocation(query, this);
                }, 500);
            } else {
                hideSuggestions();
            }
        });

        locationInput.addEventListener('blur', function() {
            setTimeout(() => hideSuggestions(), 200);
        });

        async function searchLocation(query, input) {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 8000);

                const response = await fetch(
                    `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=8&addressdetails=1&accept-language=fr,en`,
                    {
                        signal: controller.signal,
                        headers: { 'User-Agent': 'RHFLOW-Enterprise/1.0' }
                    }
                );

                clearTimeout(timeoutId);

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const data = await response.json();
                if (data && data.length > 0) {
                    showLocationSuggestions(data, input);
                } else {
                    fallbackLocationSearch(query, input);
                }
            } catch (error) {
                console.error('Erreur recherche:', error);
                fallbackLocationSearch(query, input);
            }
        }

        function fallbackLocationSearch(query, input) {
            const fallbackLocations = [
                { display_name: 'Abidjan, Côte d\'Ivoire', lat: '5.35995', lon: '-4.008256', address: { city: 'Abidjan', country: 'Côte d\'Ivoire', country_code: 'ci' } },
                { display_name: 'Dakar, Sénégal', lat: '14.7645', lon: '-17.366028', address: { city: 'Dakar', country: 'Sénégal', country_code: 'sn' } },
                { display_name: 'Paris, France', lat: '48.8566', lon: '2.3522', address: { city: 'Paris', country: 'France', country_code: 'fr' } },
                { display_name: 'Bamako, Mali', lat: '12.6392', lon: '-8.002889', address: { city: 'Bamako', country: 'Mali', country_code: 'ml' } }
            ];

            const filtered = fallbackLocations.filter(loc =>
                loc.display_name.toLowerCase().includes(query.toLowerCase())
            );

            if (filtered.length > 0) {
                showLocationSuggestions(filtered, input, true);
            }
        }

        function showLocationSuggestions(locations, input, isFallback = false) {
            hideSuggestions();

            suggestionBox = document.createElement('div');
            suggestionBox.className = 'location-suggestions';
            suggestionBox.style.cssText = `
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 1px solid #ddd;
                border-top: none;
                max-height: 300px;
                overflow-y: auto;
                z-index: 1000;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            `;

            locations.forEach(location => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                item.style.cssText = `
                    padding: 12px;
                    cursor: pointer;
                    border-bottom: 1px solid #f0f0f0;
                    font-size: 14px;
                `;

                const addr = location.address || {};
                const city = addr.city || addr.town || addr.village || '';
                const state = addr.state || addr.region || '';
                const country = addr.country || '';

                item.innerHTML = `
                    <div style="font-weight: 500; color: #333;">
                        📍 ${city}${state ? ', ' + state : ''} ${country}
                    </div>
                    <small style="color: #666;">${location.display_name}</small>
                `;

                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'white';
                });

                item.addEventListener('click', function() {
                    const shortDisplay = `${city}${state ? ', ' + state : ''}, ${country}`;
                    input.value = shortDisplay;
                    input.classList.add('is-valid');

                    // Remplir les champs cachés et visibles
                    document.getElementById('city').value = city;
                    document.getElementById('state').value = state;
                    document.getElementById('country').value = country;
                    document.getElementById('postal_code').value = addr.postcode || '';
                    document.getElementById('latitude').value = location.lat;
                    document.getElementById('longitude').value = location.lon;
                    document.getElementById('location_data').value = JSON.stringify({
                        lat: location.lat,
                        lon: location.lon,
                        city, state, country,
                        postcode: addr.postcode
                    });

                    hideSuggestions();
                });

                suggestionBox.appendChild(item);
            });

            input.parentElement.appendChild(suggestionBox);
        }

        function hideSuggestions() {
            if (suggestionBox) {
                suggestionBox.remove();
                suggestionBox = null;
            }
        }
    }
    
    // ========================================
    // FONCTIONS UTILITAIRES
    // ========================================

    // Obtenir le drapeau d'un pays depuis son code
    function getCountryFlag(countryCode) {
        if (!countryCode) return '🌍';

        const flags = {
            'fr': '🇫🇷', 'us': '🇺🇸', 'gb': '🇬🇧', 'de': '🇩🇪', 'es': '🇪🇸', 'it': '🇮🇹',
            'pt': '🇵🇹', 'br': '🇧🇷', 'ru': '🇷🇺', 'cn': '🇨🇳', 'jp': '🇯🇵', 'kr': '🇰🇷',
            'in': '🇮🇳', 'ca': '🇨🇦', 'au': '🇦🇺', 'mx': '🇲🇽', 'ar': '🇦🇷', 'ch': '🇨🇭',
            'nl': '🇳🇱', 'be': '🇧🇪', 'se': '🇸🇪', 'no': '🇳🇴', 'dk': '🇩🇰', 'fi': '🇫🇮',
            'pl': '🇵🇱', 'cz': '🇨🇿', 'at': '🇦🇹', 'ie': '🇮🇪', 'za': '🇿🇦', 'ng': '🇳🇬',
            'eg': '🇪🇬', 'ma': '🇲🇦', 'ci': '🇨🇮', 'sn': '🇸🇳', 'gh': '🇬🇭', 'ke': '🇰🇪'
        };

        return flags[countryCode.toLowerCase()] || '🌍';
    }

    // Estimer le fuseau horaire depuis les coordonnées (approximation simple)
    function getTimezoneFromCoordinates(lat, lon) {
        // Mapping approximatif basé sur la longitude (très simplifié)
        const longitude = parseFloat(lon);

        // Cas spéciaux pour certaines régions
        if (lat >= 45 && lat <= 55 && lon >= -5 && lon <= 10) {
            return 'Europe/Paris'; // France, Allemagne, etc.
        }
        if (lat >= 49 && lat <= 61 && lon >= -8 && lon <= 2) {
            return 'Europe/London'; // UK
        }
        if (lat >= 35 && lat <= 42 && lon >= -9 && lon <= 4) {
            return 'Europe/Madrid'; // Espagne
        }
        if (lat >= 35 && lat <= 47 && lon >= 6 && lon <= 19) {
            return 'Europe/Rome'; // Italie
        }

        // Mapping général basé sur la longitude
        if (longitude >= -30 && longitude < -15) return 'Atlantic/Azores';
        else if (longitude >= -15 && longitude < 15) return 'Europe/London';
        else if (longitude >= 15 && longitude < 45) return 'Europe/Berlin';
        else if (longitude >= 45 && longitude < 75) return 'Asia/Dubai';
        else if (longitude >= 75 && longitude < 105) return 'Asia/Kolkata';
        else if (longitude >= 105 && longitude < 135) return 'Asia/Shanghai';
        else if (longitude >= 135 && longitude < 165) return 'Asia/Tokyo';
        else if (longitude >= 165 || longitude < -165) return 'Pacific/Auckland';
        else if (longitude >= -165 && longitude < -135) return 'Pacific/Honolulu';
        else if (longitude >= -135 && longitude < -105) return 'America/Los_Angeles';
        else if (longitude >= -105 && longitude < -75) return 'America/Denver';
        else if (longitude >= -75 && longitude < -45) return 'America/New_York';
        else return 'UTC';
    }

    // Obtenir la langue recommandée depuis le code pays
    function getLocaleFromCountryCode(countryCode) {
        if (!countryCode) return 'en';

        const countryToLocale = {
            'fr': 'fr', 'be': 'fr', 'ch': 'fr', 'ca': 'fr', 'ci': 'fr', 'sn': 'fr',
            'us': 'en', 'gb': 'en', 'au': 'en', 'ie': 'en', 'za': 'en', 'ng': 'en',
            'de': 'de', 'at': 'de',
            'es': 'es', 'mx': 'es', 'ar': 'es',
            'it': 'it',
            'pt': 'pt', 'br': 'pt',
            'ru': 'ru',
            'cn': 'zh',
            'jp': 'ja',
            'kr': 'ko',
            'in': 'hi'
        };

        return countryToLocale[countryCode.toLowerCase()] || 'en';
    }

    // ========================================
    // INITIALISATION
    // ========================================

    // ========================================
    // AUTOCOMPLETION TÉLÉPHONIQUE INTERNATIONALE
    // ========================================

    // Fonction pour créer l'autocomplétion téléphonique
    function createPhoneAutocomplete() {
        const phoneInput = document.querySelector('[data-intl-tel-input]');
        if (!phoneInput) return;

        // Liste des indicatifs téléphoniques par pays
        const countryCodes = {
            'fr': { code: '+33', name: 'France', flag: '🇫🇷' },
            'be': { code: '+32', name: 'Belgique', flag: '🇧🇪' },
            'ch': { code: '+41', name: 'Suisse', flag: '🇨🇭' },
            'ca': { code: '+1', name: 'Canada', flag: '🇨🇦' },
            'us': { code: '+1', name: 'États-Unis', flag: '🇺🇸' },
            'gb': { code: '+44', name: 'Royaume-Uni', flag: '🇬🇧' },
            'de': { code: '+49', name: 'Allemagne', flag: '🇩🇪' },
            'es': { code: '+34', name: 'Espagne', flag: '🇪🇸' },
            'it': { code: '+39', name: 'Italie', flag: '🇮🇹' },
            'pt': { code: '+351', name: 'Portugal', flag: '🇵🇹' },
            'br': { code: '+55', name: 'Brésil', flag: '🇧🇷' },
            'ci': { code: '+225', name: 'Côte d\'Ivoire', flag: '🇨🇮' },
            'sn': { code: '+221', name: 'Sénégal', flag: '🇸🇳' },
            'ml': { code: '+223', name: 'Mali', flag: '🇲🇱' },
            'bf': { code: '+226', name: 'Burkina Faso', flag: '🇧🇫' },
            'gn': { code: '+224', name: 'Guinée', flag: '🇬🇳' },
            'tg': { code: '+228', name: 'Togo', flag: '🇹🇬' },
            'bj': { code: '+229', name: 'Bénin', flag: '🇧🇯' },
            'ne': { code: '+227', name: 'Niger', flag: '🇳🇪' },
            'cm': { code: '+237', name: 'Cameroun', flag: '🇨🇲' },
            'td': { code: '+235', name: 'Tchad', flag: '🇹🇩' },
            'cf': { code: '+236', name: 'République Centrafricaine', flag: '🇨🇫' },
            'cg': { code: '+242', name: 'Congo', flag: '🇨🇬' },
            'cd': { code: '+243', name: 'RD Congo', flag: '🇨🇩' },
            'ga': { code: '+241', name: 'Gabon', flag: '🇬🇦' },
            'ao': { code: '+244', name: 'Angola', flag: '🇦🇴' },
            'mz': { code: '+258', name: 'Mozambique', flag: '🇲🇿' },
            'mg': { code: '+261', name: 'Madagascar', flag: '🇲🇬' },
            'mu': { code: '+230', name: 'Maurice', flag: '🇲🇺' },
            'sc': { code: '+248', name: 'Seychelles', flag: '🇸🇨' },
            'km': { code: '+269', name: 'Comores', flag: '🇰🇲' },
            'dj': { code: '+253', name: 'Djibouti', flag: '🇩🇯' },
            'er': { code: '+291', name: 'Érythrée', flag: '🇪🇷' },
            'et': { code: '+251', name: 'Éthiopie', flag: '🇪🇹' },
            'so': { code: '+252', name: 'Somalie', flag: '🇸🇴' },
            'ke': { code: '+254', name: 'Kenya', flag: '🇰🇪' },
            'tz': { code: '+255', name: 'Tanzanie', flag: '🇹🇿' },
            'ug': { code: '+256', name: 'Ouganda', flag: '🇺🇬' },
            'rw': { code: '+250', name: 'Rwanda', flag: '🇷🇼' },
            'bi': { code: '+257', name: 'Burundi', flag: '🇧🇮' },
            'mw': { code: '+265', name: 'Malawi', flag: '🇲🇼' },
            'zm': { code: '+260', name: 'Zambie', flag: '🇿🇲' },
            'zw': { code: '+263', name: 'Zimbabwe', flag: '🇿🇼' },
            'na': { code: '+264', name: 'Namibie', flag: '🇳🇦' },
            'bw': { code: '+267', name: 'Botswana', flag: '🇧🇼' },
            'za': { code: '+27', name: 'Afrique du Sud', flag: '🇿🇦' },
            'sz': { code: '+268', name: 'Eswatini', flag: '🇸🇿' },
            'ls': { code: '+266', name: 'Lesotho', flag: '🇱🇸' },
            'ma': { code: '+212', name: 'Maroc', flag: '🇲🇦' },
            'dz': { code: '+213', name: 'Algérie', flag: '🇩🇿' },
            'tn': { code: '+216', name: 'Tunisie', flag: '🇹🇳' },
            'ly': { code: '+218', name: 'Libye', flag: '🇱🇾' },
            'eg': { code: '+20', name: 'Égypte', flag: '🇪🇬' },
            'sd': { code: '+249', name: 'Soudan', flag: '🇸🇩' },
            'ss': { code: '+211', name: 'Soudan du Sud', flag: '🇸🇸' },
            'sa': { code: '+966', name: 'Arabie Saoudite', flag: '🇸🇦' },
            'ae': { code: '+971', name: 'Émirats Arabes Unis', flag: '🇦🇪' },
            'qa': { code: '+974', name: 'Qatar', flag: '🇶🇦' },
            'bh': { code: '+973', name: 'Bahreïn', flag: '🇧🇭' },
            'kw': { code: '+965', name: 'Koweït', flag: '🇰🇼' },
            'om': { code: '+968', name: 'Oman', flag: '🇴🇲' },
            'ye': { code: '+967', name: 'Yémen', flag: '🇾🇪' },
            'jo': { code: '+962', name: 'Jordanie', flag: '🇯🇴' },
            'ps': { code: '+970', name: 'Palestine', flag: '🇵🇸' },
            'lb': { code: '+961', name: 'Liban', flag: '🇱🇧' },
            'sy': { code: '+963', name: 'Syrie', flag: '🇸🇾' },
            'iq': { code: '+964', name: 'Irak', flag: '🇮🇶' },
            'ir': { code: '+98', name: 'Iran', flag: '🇮🇷' },
            'tr': { code: '+90', name: 'Turquie', flag: '🇹🇷' },
            'cy': { code: '+357', name: 'Chypre', flag: '🇨🇾' },
            'gr': { code: '+30', name: 'Grèce', flag: '🇬🇷' },
            'bg': { code: '+359', name: 'Bulgarie', flag: '🇧🇬' },
            'ro': { code: '+40', name: 'Roumanie', flag: '🇷🇴' },
            'hu': { code: '+36', name: 'Hongrie', flag: '🇭🇺' },
            'sk': { code: '+421', name: 'Slovaquie', flag: '🇸🇰' },
            'cz': { code: '+420', name: 'République Tchèque', flag: '🇨🇿' },
            'pl': { code: '+48', name: 'Pologne', flag: '🇵🇱' },
            'ua': { code: '+380', name: 'Ukraine', flag: '🇺🇦' },
            'ru': { code: '+7', name: 'Russie', flag: '🇷🇺' },
            'by': { code: '+375', name: 'Biélorussie', flag: '🇧🇾' },
            'md': { code: '+373', name: 'Moldavie', flag: '🇲🇩' },
            'lt': { code: '+370', name: 'Lituanie', flag: '🇱🇹' },
            'lv': { code: '+371', name: 'Lettonie', flag: '🇱🇻' },
            'ee': { code: '+372', name: 'Estonie', flag: '🇪🇪' },
            'fi': { code: '+358', name: 'Finlande', flag: '🇫🇮' },
            'se': { code: '+46', name: 'Suède', flag: '🇸🇪' },
            'no': { code: '+47', name: 'Norvège', flag: '🇳🇴' },
            'dk': { code: '+45', name: 'Danemark', flag: '🇩🇰' },
            'is': { code: '+354', name: 'Islande', flag: '🇮🇸' },
            'ie': { code: '+353', name: 'Irlande', flag: '🇮🇪' },
            'nl': { code: '+31', name: 'Pays-Bas', flag: '🇳🇱' },
            'lu': { code: '+352', name: 'Luxembourg', flag: '🇱🇺' },
            'at': { code: '+43', name: 'Autriche', flag: '🇦🇹' },
            'si': { code: '+386', name: 'Slovénie', flag: '🇸🇮' },
            'hr': { code: '+385', name: 'Croatie', flag: '🇭🇷' },
            'ba': { code: '+387', name: 'Bosnie-Herzégovine', flag: '🇧🇦' },
            'me': { code: '+382', name: 'Monténégro', flag: '🇲🇪' },
            'rs': { code: '+381', name: 'Serbie', flag: '🇷🇸' },
            'mk': { code: '+389', name: 'Macédoine du Nord', flag: '🇲🇰' },
            'al': { code: '+355', name: 'Albanie', flag: '🇦🇱' },
            'mt': { code: '+356', name: 'Malte', flag: '🇲🇹' },
            'mc': { code: '+377', name: 'Monaco', flag: '🇲🇨' },
            'ad': { code: '+376', name: 'Andorre', flag: '🇦🇩' },
            'li': { code: '+423', name: 'Liechtenstein', flag: '🇱🇮' },
            'sm': { code: '+378', name: 'Saint-Marin', flag: '🇸🇲' },
            'va': { code: '+39', name: 'Vatican', flag: '🇻🇦' },
            'au': { code: '+61', name: 'Australie', flag: '🇦🇺' },
            'nz': { code: '+64', name: 'Nouvelle-Zélande', flag: '🇳🇿' },
            'fj': { code: '+679', name: 'Fidji', flag: '🇫🇯' },
            'pg': { code: '+675', name: 'Papouasie-Nouvelle-Guinée', flag: '🇵🇬' },
            'sb': { code: '+677', name: 'Îles Salomon', flag: '🇸🇧' },
            'vu': { code: '+678', name: 'Vanuatu', flag: '🇻🇺' },
            'nc': { code: '+687', name: 'Nouvelle-Calédonie', flag: '🇳🇨' },
            'pf': { code: '+689', name: 'Polynésie française', flag: '🇵🇫' },
            'as': { code: '+1', name: 'Samoa américaines', flag: '🇦🇸' },
            'gu': { code: '+1', name: 'Guam', flag: '🇬🇺' },
            'mp': { code: '+1', name: 'Îles Mariannes du Nord', flag: '🇲🇵' },
            'pr': { code: '+1', name: 'Porto Rico', flag: '🇵🇷' },
            'vi': { code: '+1', name: 'Îles Vierges américaines', flag: '🇻🇮' },
            'vg': { code: '+1', name: 'Îles Vierges britanniques', flag: '🇻🇬' },
            'ai': { code: '+1', name: 'Anguilla', flag: '🇦🇮' },
            'ag': { code: '+1', name: 'Antigua-et-Barbuda', flag: '🇦🇬' },
            'bs': { code: '+1', name: 'Bahamas', flag: '🇧🇸' },
            'bb': { code: '+1', name: 'Barbade', flag: '🇧🇧' },
            'bz': { code: '+501', name: 'Belize', flag: '🇧🇿' },
            'cr': { code: '+506', name: 'Costa Rica', flag: '🇨🇷' },
            'sv': { code: '+503', name: 'Salvador', flag: '🇸🇻' },
            'gt': { code: '+502', name: 'Guatemala', flag: '🇬🇹' },
            'hn': { code: '+504', name: 'Honduras', flag: '🇭🇳' },
            'ni': { code: '+505', name: 'Nicaragua', flag: '🇳🇮' },
            'pa': { code: '+507', name: 'Panama', flag: '🇵🇦' },
            'cu': { code: '+53', name: 'Cuba', flag: '🇨🇺' },
            'do': { code: '+1', name: 'République Dominicaine', flag: '🇩🇴' },
            'ht': { code: '+509', name: 'Haïti', flag: '🇭🇹' },
            'jm': { code: '+1', name: 'Jamaïque', flag: '🇯🇲' },
            'tt': { code: '+1', name: 'Trinité-et-Tobago', flag: '🇹🇹' },
            'lc': { code: '+1', name: 'Sainte-Lucie', flag: '🇱🇨' },
            'vc': { code: '+1', name: 'Saint-Vincent-et-les-Grenadines', flag: '🇻🇨' },
            'gd': { code: '+1', name: 'Grenade', flag: '🇬🇩' },
            'kn': { code: '+1', name: 'Saint-Christophe-et-Niévès', flag: '🇰🇳' },
            'dm': { code: '+1', name: 'Dominique', flag: '🇩🇲' },
            'gy': { code: '+592', name: 'Guyana', flag: '🇬🇾' },
            'sr': { code: '+597', name: 'Suriname', flag: '🇸🇷' },
            'py': { code: '+595', name: 'Paraguay', flag: '🇵🇾' },
            'uy': { code: '+598', name: 'Uruguay', flag: '🇺🇾' },
            've': { code: '+58', name: 'Venezuela', flag: '🇻🇪' },
            'co': { code: '+57', name: 'Colombie', flag: '🇨🇴' },
            'ec': { code: '+593', name: 'Équateur', flag: '🇪🇨' },
            'pe': { code: '+51', name: 'Pérou', flag: '🇵🇪' },
            'bo': { code: '+591', name: 'Bolivie', flag: '🇧🇴' },
            'cl': { code: '+56', name: 'Chili', flag: '🇨🇱' },
            'ar': { code: '+54', name: 'Argentine', flag: '🇦🇷' },
            'mx': { code: '+52', name: 'Mexique', flag: '🇲🇽' },
            'hn': { code: '+504', name: 'Honduras', flag: '🇭🇳' },
            'ni': { code: '+505', name: 'Nicaragua', flag: '🇳🇮' },
            'pa': { code: '+507', name: 'Panama', flag: '🇵🇦' }
        };

        // Créer le conteneur pour l'indicatif
        const container = document.createElement('div');
        container.className = 'phone-input-container';
        container.style.cssText = 'position: relative; display: flex;';

        // Créer le bouton pour l'indicatif
        const countryButton = document.createElement('button');
        countryButton.type = 'button';
        countryButton.className = 'btn btn-outline-secondary country-button';
        countryButton.style.cssText = `
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: none;
            min-width: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        `;
        countryButton.innerHTML = `
            <span class="country-flag">🇨🇮</span>
            <span class="country-code">+225</span>
            <i class="fas fa-chevron-down" style="font-size: 12px;"></i>
        `;

        // Créer le dropdown pour les pays
        const dropdown = document.createElement('div');
        dropdown.className = 'country-dropdown';
        dropdown.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        `;

        // Remplir le dropdown
        Object.entries(countryCodes).forEach(([code, country]) => {
            const item = document.createElement('div');
            item.className = 'country-item';
            item.style.cssText = `
                padding: 8px 12px;
                cursor: pointer;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                align-items: center;
                gap: 8px;
            `;
            item.innerHTML = `
                <span>${country.flag}</span>
                <span>${country.name}</span>
                <span style="margin-left: auto; color: #666;">${country.code}</span>
            `;

            item.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
            });

            item.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'white';
            });

            item.addEventListener('click', function() {
                // Mettre à jour l'indicatif
                const flag = country.flag;
                const code = country.code;
                document.querySelector('.country-flag').textContent = flag;
                document.querySelector('.country-code').textContent = code;

                // Stocker l'indicatif dans un champ caché
                let hiddenInput = document.getElementById('phone_country_code');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.id = 'phone_country_code';
                    hiddenInput.name = 'phone_country_code';
                    phoneInput.parentElement.appendChild(hiddenInput);
                }
                hiddenInput.value = code;

                // Fermer le dropdown
                dropdown.style.display = 'none';
            });

            dropdown.appendChild(item);
        });

        // Modifier le style du champ téléphone
        phoneInput.style.borderTopLeftRadius = '0';
        phoneInput.style.borderBottomLeftRadius = '0';

        // Insérer les éléments
        phoneInput.parentElement.insertBefore(container, phoneInput);
        container.appendChild(countryButton);
        container.appendChild(phoneInput);
        container.appendChild(dropdown);

        // Gestionnaire pour ouvrir/fermer le dropdown
        countryButton.addEventListener('click', function() {
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });

        // Fermer le dropdown quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!container.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Auto-détection du pays depuis l'IP (optionnel)
        detectCountryFromIP().then(country => {
            if (country && countryCodes[country]) {
                const countryInfo = countryCodes[country];
                document.querySelector('.country-flag').textContent = countryInfo.flag;
                document.querySelector('.country-code').textContent = countryInfo.code;
                let hiddenInput = document.getElementById('phone_country_code');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.id = 'phone_country_code';
                    hiddenInput.name = 'phone_country_code';
                    phoneInput.parentElement.appendChild(hiddenInput);
                }
                hiddenInput.value = countryInfo.code;
            }
        });
    }

    // Fonction pour détecter le pays depuis l'IP (approximation simple)
    async function detectCountryFromIP() {
        try {
            // Utiliser un service de géolocalisation gratuit avec timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 5000);

            const response = await fetch('https://ipapi.co/json/', {
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json'
                }
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();
            return data.country_code2?.toLowerCase();
        } catch (error) {
            console.log('Impossible de détecter le pays depuis l\'IP:', error.message);
            return 'ci'; // Fallback sur la Côte d'Ivoire
        }
    }

    // ========================================
    // INITIALISATION
    // ========================================

    // Initialiser les autocompletions si les éléments existent
    if (timezoneSelect) {
        createTimezoneAutocomplete();
    }

    // Initialiser l'autocomplétion au chargement de la page
    initializeLocationAutocomplete();

    // Initialiser l'autocomplétion téléphonique
    createPhoneAutocomplete();
    generateProposal();

    console.log('Autocomplétion fuseaux horaires, langues et localisation chargée avec succès');
});
// Auto-dismiss notifications after 5 seconds
const notifications = document.querySelectorAll('.alert-dismissible');
notifications.forEach(function(notification) {
    setTimeout(function() {
        const bsAlert = new bootstrap.Alert(notification);
        bsAlert.close();
    }, 5000); // 5 seconds
});
</script>