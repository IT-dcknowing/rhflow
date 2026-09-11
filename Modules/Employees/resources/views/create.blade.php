@extends('layouts.app')

@section('title', 'Créer un Employé Mensuel')

@push('styles')
<style>
    /* Styles pour les onglets */
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 1rem 1.5rem;
        position: relative;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active {
        color: #1e3a8a;
        background: rgba(var(--primary-rgb), 0.1);
        border: none;
        border-bottom: 3px solid #1e3a8a;
    }

    .nav-tabs .nav-link:not(.active):hover {
        color: #1e3a8a;
        background: rgba(var(--primary-rgb), 0.05);
    }

    .tab-content {
        padding: 1.5rem;
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    /* Style pour les sections du formulaire */
    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        color: #1e3a8a;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
    }

    /* Styles pour la barre de progression */
    .progress-container {
        padding: 0 20px;
        margin: 20px 0;
    }

    .progress-steps {
        position: relative;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        counter-reset: step;
        padding: 0;
        list-style: none;
    }

    .progress-step {
        position: relative;
        text-align: center;
        z-index: 1;
        flex: 1;
        padding: 0 10px;
    }

    .progress-step:first-child {
        padding-left: 0;
    }

    .progress-step:last-child {
        padding-right: 0;
    }

    .step-number {
        width: 40px;
        height: 40px;
        line-height: 36px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #dee2e6;
        color: #6c757d;
        font-weight: 600;
        margin: 0 auto 10px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .progress-step.active .step-number {
        border-color: #0d6efd;
        background-color: #0d6efd;
        color: white;
        transform: scale(1.1);
    }

    .progress-step.completed .step-number {
        border-color: #198754;
        background-color: #198754;
        color: white;
    }

    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 5px;
        transition: all 0.3s ease;
    }

    .progress-step.active .step-label {
        color: #0d6efd;
        font-weight: 600;
    }

    .progress-bar {
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 0;
        margin: 0 5%;
        width: 90%;
    }

    .progress-bar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0;
        background: #0d6efd;
        transition: width 0.5s ease;
    }

    .progress-step.active ~ .progress-step .step-number {
        border-color: #e9ecef;
        background: #fff;
        color: #6c757d;
    }

    .progress-step.completed .step-number::before {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.2em;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .invalid-feedback {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .alert {
        animation: fadeIn 0.3s ease-out;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        z-index: 1090;
    }

    @media (max-width: 768px) {
        .step-label {
            font-size: 0.75rem;
        }

        .step-number {
            width: 32px;
            height: 32px;
            line-height: 28px;
            font-size: 0.875rem;
        }
    }

    .section-title i {
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }

    /* Style pour les champs du formulaire */
    .form-label {
        font-weight: 500;
        color: #495057;
    }

    /* Style pour les boutons de navigation */
    .form-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }

    /* Style pour les messages d'erreur */
    .invalid-feedback {
        font-size: 0.8rem;
    }

    /* Style pour les champs obligatoires */
    .required:after {
        content: '*';
        color: #dc3545;
        margin-left: 4px;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 text-primary">
                    <i class="fas fa-user-plus  me-2"></i>
                    Création d'un Nouvel Employé
                </h4>
                <a href="{{ route('company.employees.index') }}" class="btn btn-outline-info">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>

            <!-- Barre de progression -->
            {{-- store() renvoie view(...)->with('error', ...) : variable de vue, pas flash --}}
            @if(($error ?? session('error')))
                <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Erreur !</strong> {{ $error ?? session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-warning alert-dismissible mb-4" role="alert">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Veuillez corriger les erreurs suivantes :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-1"></i>
                Tous les champs marqués d'un <span class="required"></span> sont obligatoires
            </div>

            <form method="POST" action="{{ route('company.employees.store') }}" id="employeeForm" enctype="multipart/form-data">
                @csrf

                <!-- Navigation par onglets -->
                <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                            <i class="fas fa-user me-2"></i>Informations Personnelles
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="professional-tab" data-bs-toggle="tab" data-bs-target="#professional" type="button" role="tab" aria-controls="professional" aria-selected="false">
                            <i class="fas fa-briefcase me-2"></i>Données du poste
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab" aria-controls="documents" aria-selected="false">
                            <i class="fas fa-file-alt me-2"></i>Documents
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial" type="button" role="tab" aria-controls="financial" aria-selected="false">
                            <i class="fas fa-money-bill-wave me-2"></i>Informations Financières
                        </button>
                    </li>
                </ul>

                <!-- Contenu des onglets -->
                <div class="tab-content mb-3" id="employeeTabsContent">
                    <!-- Onglet 1: Informations Personnelles -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                        <div class="card-body">
                            <!-- Section Identité -->
                            <div class="form-section">
                                <h5 class="section-title">
                                    <i class="fas fa-id-card"></i>
                                    Identité
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label required">Nom complet</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name') }}" required
                                                    oninput="generateUsername()">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="dob" class="form-label required">Date de naissance</label>
                                        <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                                id="dob" name="dob" value="{{ old('dob') }}" required>
                                        @error('dob')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label required mb-2">Genre</label>
                                        <div class="radio-modern d-flex justify-content-start">
                                            <div class="radio-item me-3">
                                                <input class="form-check-input" type="radio" name="gender" id="g_male"
                                                        value="Male" {{ old('gender') == 'Male' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="g_male">
                                                    <i class="fas fa-mars me-1"></i> Homme
                                                </label>
                                            </div>
                                            <div class="radio-item">
                                                <input class="form-check-input" type="radio" name="gender" id="g_female"
                                                        value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="g_female">
                                                    <i class="fas fa-venus me-1"></i> Femme
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label required mb-2">Type d'employé</label>
                                        <div class="radio-modern d-flex justify-content-start ">
                                            <div class="radio-item me-3">
                                                <input class="form-check-input" type="radio" name="charge_expat" id="local"
                                                        value="local" {{ old('charge_expat') == 'local' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="local">
                                                    <i class="fas fa-home me-1"></i> Local
                                                </label>
                                            </div>
                                            <div class="radio-item">
                                                <input class="form-check-input" type="radio" name="charge_expat" id="expat"
                                                        value="expat" {{ old('charge_expat') == 'expat' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="expat">
                                                    <i class="fas fa-plane me-1"></i> Expatrié
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="form-floating-modern">
                                            <label for="nationality" class="form-label required">Pays</label>
                                            <select class="form-select select2 @error('nationality') is-invalid @enderror"
                                                    name="nationality" id="nationality" required>
                                                <option value="">Sélectionner un pays</option>
                                               @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}" {{ old('nationality') == $country->id ? 'selected' : '' }}>{{ $country->flag }} {{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('nationality')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="cmu" class="form-label required">Bénéficiaire CMU</label>
                                        <select type="text" name="cmu" class="form-select" id="cmu">
                                            @for ($i = 0; $i <= 10; $i++)
                                                <option value="{{ $i }}" @if(old('cmu') == $i) selected="selected" @endif>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="martalstatu_id" class="form-label required">Situation matrimoniale</label>
                                        <select type="text" name="martalstatu_id" class="form-select" id="martalstatu_id" onchange="status()">
                                            @foreach ($maritalstatus as $status)
                                                <option value="{{$status->id}}" @if(old('situation_mat') == $status->id) selected="selected" @endif>{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="enfant" class="form-label required">Enfants à charge</label>
                                        <select type="text" name="enfant" class="form-select" id="enfant" onchange="status()">
                                            @for ($i = 0; $i <= 10; $i++)
                                                <option value="{{ $i }}" @if(old('enfant') == $i) selected="selected" @endif>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="personneinf" class="form-label required">Personnes infirmes à charge</label>
                                        <select type="text" name="personneinf" class="form-select" id="personneinf" onchange="status()">
                                            @for ($i = 0; $i <= 5; $i++)
                                                <option value="{{ $i }}" @if(old('personneinf') == $i) selected="selected" @endif>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="parts" class="form-label required">Nombre de parts</label>
                                        <input type="text" name="parts" class="form-control" id="parts" value="{{ old('parts', 1) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Section Coordonnées -->
                            <div class="form-section mt-4">
                                <h5 class="section-title">
                                    <i class="fas fa-address-book"></i>
                                    Coordonnées
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email professionnel</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                    id="email" name="email" value="{{ old('email') }}">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label required">Téléphone</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                    id="phone" name="phone" value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Adresse (Localisation)</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror"
                                                    id="address" name="address" rows="2">{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Section Compte Utilisateur -->
                            <div class="form-section mt-4">
                                <h5 class="section-title">
                                    <i class="fas fa-user-shield"></i>
                                    Compte Utilisateur
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="username" class="form-label">Nom d'utilisateur</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                                    id="username" name="username" value="{{ old('username') }}" readonly>
                                            <button class="btn btn-outline-secondary" type="button" id="regenerateUsername"
                                                title="Générer un autre identifiant">
                                                <i class="fas fa-rotate"></i>
                                            </button>
                                        </div>
                                        @error('username')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Généré automatiquement, puis figé : il sert à la connexion de l'employé.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="password" class="form-label">Mot de passe</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password">
                                            <button class="btn btn-outline-secondary" type="button" id="generatePassword">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Laissez vide pour générer un mot de passe aléatoire</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                             <button class="btn btn-outline-secondary" type="button" id="showPassword">
                                                <span id='iconPassword'><i class="fas fa-eye" id="showIcon"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 2: Données du poste -->
                    <div class="tab-pane fade" id="professional" role="tabpanel" aria-labelledby="professional-tab">
                        <div class="modern-card">
                            <div class="modern-header">
                                <h5 class="section-title">
                                    <i class="fas fa-briefcase"></i>
                                     Secteur de l'entreprise : {{ $company->sector->name ?? 'Non spécifié' }}
                                </h5>
                            </div>
                            <div class="modern-body">
                                <!-- Informations sur l'employé -->
                                <div class="info-section mb-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="employee_id" class="form-label required">ID Employé</label>
                                            <input type="text" class="form-control disabled bg-label-info" id="employee_id" name="employee_id" value="{{ $employeesId ?? 'EMP0001' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating-modern">
                                            <label for="branch_id" class="form-label required">Succursale</label>
                                            <select class="form-select @error('branch_id') is-invalid @enderror"
                                                    name="branch_id" id="branch_id" required>
                                                <option value="">Sélectionner une succursale</option>
                                                @if(!empty($branches))
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->id }}">
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('branch_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating-modern">
                                            <label for="department_id" class="form-label required">Service</label>
                                            <select class="form-select @error('department_id') is-invalid @enderror"
                                                    name="department_id" id="department_id" required>
                                                <option value="">Sélectionner un service</option>
                                            </select>
                                            @error('department_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-floating-modern">
                                            <label for="designation_id" class="form-label required">Poste</label>
                                            <select class="form-select @error('designation_id') is-invalid @enderror"
                                                    name="designation_id" id="designation_id" required>
                                                <option value="">Sélectionner un poste</option>
                                            </select>
                                            @error('designation_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @php
                                        $jobCategories = '';
                                        $jobCategories = $company->sector->getJobCategorieAttribute();
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <label for="salary_type" class="form-label required">Type d'employé</label>
                                        <select name="salary_type" class="form-select @error('salary_type') is-invalid @enderror" id="salary_type" required>
                                            <option value="">-- Sélectionner le type --</option>
                                            <option value="1" {{ old('salary_type') == 1 ? 'selected' : '' }}>Mensuel</option>
                                            <option value="2" {{ old('salary_type') == 2 ? 'selected' : '' }}>Journalier</option>
                                        </select>
                                        <small class="text-muted">Mensuel : rémunéré au salaire mensuel. Journalier : rémunéré au taux horaire.</small>
                                        @error('salary_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="category_job_id" class="form-label required">Sélectionner le type de catégorie</label>
                                        <select type="text" name="category_job_id" class="form-select" id="category_job_id">
                                            <option value="">-- Sélectionner un type de catégorie --</option>
                                            @foreach($jobCategories as $jobCategorie)
                                                <option value="{{ $jobCategorie->id }}">{{ $jobCategorie->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3" id="cat_id">
                                        <label for="category_id" class="form-label">Sélectionner la catégorie</label>
                                        <select type="text" name="category_id" class="form-select" id="category_id">

                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3" id="hor_sal_id">
                                        <label for="salaire_minima_horaire" class="form-label">Salaire Catégoriel Horaire</label>
                                        <input type="number" name="salaire_minima_horaire" class="form-control" id="salaire_minima_horaire" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3" id="mens_sal_id">
                                        <label for="salaire_minima_mensuel" class="form-label">Salaire Catégoriel mensuel</label>
                                        <input type="number" name="salaire_minima_mensuel" class="form-control" id="salaire_minima_mensuel" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="end_leave" class="form-label">Retour du dernier congé</label>
                                        <input type="date" name="end_leave" class="form-control" id="end_leave" value="{{old('end_leave')}}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="charge_cmu"class="form-label">Prise en charge CMU</label>
                                        <select type="text" name="charge_cmu" class="form-select" id="charge_cmu">
                                            <option value="1">Oui</option>
                                            <option value="0">Non</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="num_secu_soc" class="form-label">Numéro CMU</label>
                                        <input type="text" name="num_secu_soc" class="form-control" id="num_secu_soc" value="{{ old('num_secu_soc') }}" maxlength="13">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="charge_cnps" class="form-label">Prise en charge CNPS</label>
                                        <select type="text" name="charge_cnps" class="form-select" id="charge_cnps">
                                            <option value="1">Oui</option>
                                            <option value="0">Non</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="num_cnps" class="form-label">Numéro CNPS</label>
                                        <input type="text" name="num_cnps" class="form-control" id="num_cnps" value="{{ old('num_cnps') }}" maxlength="12">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="charge_its" class="form-label">Prise en charge ITS</label>
                                        <select type="text" name="charge_its" class="form-select" id="charge_its">
                                            <option value="1">Oui</option>
                                            <option value="0">Non</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 3: Pièces jointes  Extrait de naissance Photo d'identité Contrat de travail (signé)-->
                    <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                        <div class="modern-card">
                            <div class="modern-header">
                                <h5 class="section-title">
                                    <i class="fas fa-paperclip"></i>
                                    Documents Requis
                                </h5>
                            </div>
                            <div class="modern-body">
                                @if(!empty($documents))
                                    <div class="row">
                                        @foreach ($documents as $key => $document)
                                            <div class="col-md-6 mb-4">
                                                <div class="document-upload-card">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="document-icon me-3">
                                                                <i class="fas fa-file-alt text-primary"></i>
                                                            </div>
                                                            <div class="d-flex">
                                                                <h6 class="mb-1 me-2">{{ $document['name'] }}</h6>
                                                                @if ($document['is_required'] == 1)
                                                                    <span class="badge bg-danger">Requis</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Optionnel</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="upload-area">
                                                        <input type="hidden" name="emp_doc_id[{{ $document['id'] }}]" value="{{ $document['id'] }}">
                                                        <input type="file" class="form-control file @error('document') is-invalid @enderror"
                                                            @if ($document['is_required'] == 1) required @endif
                                                            name="document[{{ $document['id'] }}]"
                                                            id="document[{{ $document['id'] }}]"
                                                            data-filename="{{ $document['id'] . '_filename' }}"
                                                            onchange="document.getElementById('{{ 'blah' . $key }}').src = window.URL.createObjectURL(this.files[0])">
                                                        <label for="document[{{ $document['id'] }}]" class="upload-label">
                                                            <div class="upload-content">
                                                                <i class="fas fa-cloud-upload-alt"></i>
                                                                <span>Choisir un fichier</span>
                                                            </div>
                                                        </label>
                                                        <span class="file-name" id="fileName{{ $document['id'] }}">{{ $document['id'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">Aucun document requis configuré</h6>
                                        <p class="text-muted">Les documents requis seront configurés par l'administrateur</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Onglet 4: Données financières -->
                    <div class="tab-pane fade" id="financial" role="tabpanel" aria-labelledby="financial-tab">
                        <div class="modern-card">
                            <div class="modern-header">
                                <h5 class="section-title">
                                    <i class="fas fa-credit-card"></i>
                                    Informations Financières
                                </h5>
                            </div>
                            <div class="modern-body">
                                <!-- Informations bancaires -->
                                <div class="info-section mb-4">
                                    <div class="info-title">
                                        <i class="fas fa-university me-2"></i>Informations Bancaires
                                         <hr class="bg-label-primary">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating-modern">
                                            <label for="account_holder_name_input" class="form-label">Nom du titulaire du compte</label>
                                            <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror"
                                                name="account_holder_name" id="account_holder_name_input"
                                                value="{{ old('account_holder_name') }}" placeholder="Nom du titulaire">
                                            @error('account_holder_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating-modern">
                                            <label for="account_number_input" class="form-label">Numéro de compte</label>
                                            <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                                name="account_number" id="account_number_input"
                                                value="{{ old('account_number') }}" placeholder="CXXXX 01XXX 00XXXXX 00059 XX"
                                                maxlength="25">
                                            @error('account_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating-modern">
                                            <label for="bank_name" class="form-label">Nom de la banque</label>
                                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                                name="bank_name" value="{{ old('bank_name') }}" placeholder="Nom de la banque">
                                            @error('bank_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating-modern">
                                            <label for="bank_identifier_code" class="form-label">Adresse de domiciliation</label>
                                            <input type="text" class="form-control @error('bank_identifier_code') is-invalid @enderror"
                                                name="bank_identifier_code" value="{{ old('bank_identifier_code') }}"
                                                placeholder="Adresse de domiciliation">
                                            @error('bank_identifier_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Mobile Money -->
                                <div class="info-section mb-4">
                                    <div class="info-title">
                                        <i class="fas fa-mobile-alt me-2"></i>Mobile Money
                                        <hr class="bg-label-primary">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="orange_money" class="form-label">Orange Money</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <img src="{{ asset('images/OM.png') }}" alt="Orange Money" style="height: 20px;">
                                                </span>
                                            </div>
                                            <input type="number" class="form-control @error('orange_money') is-invalid @enderror"
                                                name="orange_money" value="{{ old('orange_money') }}"
                                            placeholder="Numéro Orange Money">
                                            @error('orange_money')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="mtn_money" class="form-label">MTN Money</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <img src="{{ asset('images/MTN.png') }}" alt="MTN Money" style="height: 20px;">
                                                </span>
                                            </div>
                                            <input type="number" class="form-control @error('mtn_money') is-invalid @enderror"
                                                name="mtn_money" value="{{ old('mtn_money') }}"
                                                placeholder="Numéro MTN Money">
                                            @error('mtn_money')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="moov_money" class="form-label">Moov Money</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <img src="{{ asset('images/MOOV.png') }}" alt="Moov Money" style="height: 20px;">
                                                </span>
                                            </div>
                                            <input type="number" class="form-control @error('moov_money') is-invalid @enderror"
                                                name="moov_money" value="{{ old('moov_money') }}"
                                                placeholder="Numéro Moov Money">
                                            @error('moov_money')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="wave_money" class="form-label">Wave Money</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <img src="{{ asset('images/WAVE.png') }}" alt="Wave Money" style="height: 20px;">
                                                </span>
                                            </div>
                                            <input type="number" class="form-control @error('wave_money') is-invalid @enderror"
                                                    name="wave_money" value="{{ old('wave_money') }}"
                                                placeholder="Numéro Wave">
                                            @error('wave_money')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="modern-card">
                    <div class="modern-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-warning prev-tab">
                                <i class="fas fa-arrow-left me-2"></i>Précédent
                            </button>
                            <div class="progress-container mb-4">
                                <div class="progress-steps d-flex justify-content-between position-relative">
                                    <div class="progress-step active" data-step="1">
                                        <div class="step-number">1</div>
                                    </div>
                                    <div class="progress-step" data-step="2">
                                        <div class="step-number">2</div>
                                    </div>
                                    <div class="progress-step" data-step="3">
                                        <div class="step-number">3</div>
                                    </div>
                                    <div class="progress-step" data-step="4">
                                        <div class="step-number">4</div>
                                    </div>
                                    <div class="progress-bar" role="progressbar"></div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary next-tab">
                                Suivant <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // ==============================================
        // GESTION DES APPELS AJAX
        // ==============================================

        // Gestion du changement de branche
        $(document).on('change', 'select[name=branch_id]', function() {
            var branch_id = $(this).val();
            var token = $('meta[name="csrf-token"]').attr('content');

            if(branch_id) {
                $.ajax({
                    url: "{{ route('company.employees.getdepartment') }}",
                    type: "POST",
                    data: {
                        _token: token,
                        branch_id: branch_id
                    },
                    dataType: "json",
                    success: function(data) {
                        var $departmentSelect = $('select[name="department_id"]');
                        $departmentSelect.empty();
                        $departmentSelect.append('<option value="">Sélectionner un service</option>');

                        $.each(data, function(key, value) {
                            $departmentSelect.append('<option value="'+ key +'">'+ value +'</option>');
                        });

                        $('select[name="designation_id"]').empty().append('<option value="">Sélectionner un poste</option>');
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors du chargement des services:", xhr.responseText);
                        alert("Une erreur est survenue lors du chargement des services.");
                    }
                });
            } else {
                $('select[name="department_id"]').empty().append('<option value="">Sélectionner un service</option>');
                $('select[name="designation_id"]').empty().append('<option value="">Sélectionner un poste</option>');
            }
        });

        // Gestion du changement de département
        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            var token = $('meta[name="csrf-token"]').attr('content');

            if(department_id) {
                $.ajax({
                    url: "{{ route('company.employees.employee.json') }}",
                    type: "POST",
                    data: {
                        _token: token,
                        department_id: department_id
                    },
                    dataType: "json",
                    success: function(data) {
                        var $designationSelect = $('select[name="designation_id"]');
                        $designationSelect.empty();
                        $designationSelect.append('<option value="">Sélectionner un poste</option>');

                        if (data && Object.keys(data).length > 0) {
                            $.each(data, function(key, value) {
                                $designationSelect.append('<option value="'+ key +'">'+ value +'</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors du chargement des postes:", xhr.responseText);
                        alert("Une erreur est survenue lors du chargement des postes.");
                    }
                });
            } else {
                $('select[name="designation_id"]').empty().append('<option value="">Sélectionner un poste</option>');
            }
        });

        // Fonction pour gérer la visibilité des champs de catégorie et salaire
        var categorieVisible = true;

        function toggleCategoryFields(show) {
            categorieVisible = show;
            if (show) {
                $('#cat_id').show();
                appliquerTypeSalaire();
            } else {
                $('#cat_id, #hor_sal_id, #mens_sal_id').hide();
                $('#category_id, #salaire_minima_horaire, #salaire_minima_mensuel').val('');
            }
        }

        // Mensuel : on met en avant le salaire mensuel. Journalier : le taux horaire.
        // Les deux champs restent renseignes (meme ligne de grille categorielle) et
        // sont soumis meme masques, le controleur en a besoin pour calculer la paie.
        function appliquerTypeSalaire() {
            if (!categorieVisible) {
                return;
            }

            var type = $('#salary_type').val();

            if (type === '1') {
                $('#mens_sal_id').show();
                $('#hor_sal_id').hide();
            } else if (type === '2') {
                $('#hor_sal_id').show();
                $('#mens_sal_id').hide();
            } else {
                $('#hor_sal_id, #mens_sal_id').show();
            }
        }

        $('#salary_type').on('change', appliquerTypeSalaire);
        appliquerTypeSalaire();

        // Gestion du changement de type de catégorie
        $('#category_job_id').on('change', function() {
            var selectedText = $(this).find('option:selected').text().trim();

            if (selectedText === 'Stagiaire') {
                toggleCategoryFields(false);
                return;
            } else {
                toggleCategoryFields(true);
            }

            var typeId = $(this).val();
            var $categorySelect = $('#category_id');

            if(typeId) {
                $.ajax({
                    url: "{{ route('company.employees.get-categories-by-type', ':id') }}".replace(':id', typeId),
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $categorySelect.empty().append('<option value="">Sélectionner une catégorie</option>');

                        if (data && data.length > 0) {
                            $.each(data, function(index, item) {
                                $categorySelect.append('<option value="' + item.id + '" data-salaireminie="' + item.salaire_minima_horaire + '" data-salairemensuel="' + item.salaire_minima_mensuel + '">Catégorie : ' + item.categorie +' / S. Horaire : '+ item.salaire_minima_horaire +' / S. Mensuel : '+ item.salaire_minima_mensuel +'</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Erreur lors du chargement des catégories:", xhr.responseText);
                        alert("Une erreur est survenue lors du chargement des catégories.");
                    }
                });
            } else {
                $categorySelect.empty().append('<option value="">Sélectionner une catégorie</option>');
            }
        });

        // Gestion du changement de catégorie
        $('#category_id').on('change', function() {
            var salaireHoraire = $('#category_id option:selected').data('salaireminie');
            var salaireMensuel = $('#category_id option:selected').data('salairemensuel');

            $('#salaire_minima_horaire').val(salaireHoraire);
            $('#salaire_minima_mensuel').val(salaireMensuel);
        });

        // Vérifier la valeur initiale
        var initialText = $('#category_job_id option:selected').text().trim();
        if (initialText === 'Stagiaire') {
            toggleCategoryFields(false);
        }

        // ==============================================
        // VALIDATION DU FORMULAIRE
        // ==============================================

        function validateCurrentTab(tabId) {
            let isValid = true;
            const tabElement = document.getElementById(tabId);

            if (!tabElement) return true;

            // Réinitialiser les états d'erreur
            $(tabElement).find('.is-invalid').removeClass('is-invalid');
            $(tabElement).find('.invalid-feedback').remove();

            // Récupérer tous les champs requis visibles
            const requiredFields = $(tabElement).find('[required]').filter(':visible');

            requiredFields.each(function() {
                const $field = $(this);
                const value = $field.val();

                // Vérifier si le champ est vide
                if (!value || (typeof value === 'string' && !value.trim())) {
                    isValid = false;
                    $field.addClass('is-invalid');

                    // Ajouter un message d'erreur
                    const label = $field.closest('.form-group, .col-md-3, .col-md-4, .col-md-6, .col-12').find('label').first().text().replace('*', '').trim();
                    const errorMsg = label ? `Le champ "${label}" est obligatoire` : 'Ce champ est obligatoire';

                    const $errorDiv = $('<div class="invalid-feedback d-block">' + errorMsg + '</div>');

                    // Insérer le message d'erreur
                    if ($field.parent().hasClass('input-group')) {
                        $field.parent().after($errorDiv);
                    } else {
                        $field.after($errorDiv);
                    }
                }

                // Validation spécifique pour les emails
                if ($field.attr('type') === 'email' && value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        $field.addClass('is-invalid');
                        const $errorDiv = $('<div class="invalid-feedback d-block">Veuillez entrer une adresse email valide</div>');
                        if ($field.parent().hasClass('input-group')) {
                            $field.parent().after($errorDiv);
                        } else {
                            $field.after($errorDiv);
                        }
                    }
                }
            });

            // Si des erreurs, afficher une notification et scroller
            if (!isValid) {
                const firstError = $(tabElement).find('.is-invalid').first();
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 150
                    }, 300);
                }

                // Afficher une alerte
                showAlert('Veuillez remplir tous les champs obligatoires avant de continuer', 'danger');
            }

            return isValid;
        }

        // Fonction pour afficher les alertes
        function showAlert(message, type = 'danger') {
            // Supprimer les alertes existantes
            $('.alert-notification').remove();

            const alert = $(`
                <div class="alert alert-${type} alert-notification alert-dismissible fade show position-fixed top-0 end-0 m-3" role="alert" style="z-index: 9999; min-width: 300px;">
                    <i class="fas fa-${type === 'danger' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);

            $('body').append(alert);

            setTimeout(() => {
                alert.fadeOut(300, function() { $(this).remove(); });
            }, 5000);
        }

        // ==============================================
        // NAVIGATION ENTRE LES ONGLETS
        // ==============================================

        function updateNavButtons() {
            const activeTab = $('.nav-tabs .nav-link.active');
            const $prevButton = $('.prev-tab');
            const $nextButton = $('.next-tab');
            const $submitButton = $('#submitButton');

            // Gérer le bouton précédent
            if (activeTab.parent().is(':first-child')) {
                $prevButton.attr('style', 'display: none !important');
            } else {
                $prevButton.attr('style', 'display: block !important');
            }

            // Gérer le bouton suivant et le bouton de soumission
            if (activeTab.parent().is(':last-child')) {
                // Afficher le bouton de soumission sur le dernier onglet
                if ($submitButton.length === 0) {
                    const submitBtn = `
                        <button type="submit" class="btn btn-success" id="submitButton">
                            <i class="fas fa-check me-2"></i>Enregistrer l'employé
                        </button>
                    `;
                    $nextButton.parent().append(submitBtn); // Ajoute le bouton de soumission
                }
                $nextButton.attr('style', 'display: none !important'); // Force la disparition du bouton suivant
            } else {
                $nextButton.attr('style', 'display: block !important'); // Montre le bouton suivant
                $('#submitButton').remove(); // Supprime le bouton de soumission s'il existe
            }
        }

        function updateProgressBar(activeTab) {
            if (!activeTab) return;

            const steps = document.querySelectorAll('.progress-step');
            const $tabItem = $(activeTab).closest('li');

            if ($tabItem.length) {
                const tabIndex = $tabItem.index();

                // Marquer les étapes complétées
                steps.forEach((step, index) => {
                    step.classList.remove('active', 'completed');
                    if (index < tabIndex) {
                        step.classList.add('completed');
                    } else if (index === tabIndex) {
                        step.classList.add('active');
                    }
                });
            }
        }

        // Bouton Suivant
        $('.next-tab').on('click', function(e) {
            e.preventDefault();

            const currentTab = $('.nav-tabs .nav-link.active');
            const currentHref = currentTab.attr('data-bs-target');

            if (currentHref) {
                const currentTabId = currentHref.substring(1);

                // Valider avant de passer au suivant
                if (validateCurrentTab(currentTabId)) {
                    const nextTab = currentTab.parent().next().find('.nav-link');
                    if (nextTab.length) {
                        nextTab.tab('show');
                    }
                }
            }
        });

        // Bouton Précédent
        $('.prev-tab').on('click', function(e) {
            e.preventDefault();

            const currentTab = $('.nav-tabs .nav-link.active');
            const prevTab = currentTab.parent().prev().find('.nav-link');

            if (prevTab.length) {
                prevTab.tab('show');
            }
        });

        // Validation lors du clic sur les onglets
        $('.nav-tabs .nav-link').on('click', function(e) {
            const targetTab = $(this);
            const currentTab = $('.nav-tabs .nav-link.active');

            // Si on essaie d'aller à un onglet suivant
            if (targetTab.parent().index() > currentTab.parent().index()) {
                const currentHref = currentTab.attr('data-bs-target');
                if (currentHref) {
                    const currentTabId = currentHref.substring(1);

                    // Valider l'onglet actuel
                    if (!validateCurrentTab(currentTabId)) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }
            }
        });

        // Événement après changement d'onglet
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            updateNavButtons();
            updateProgressBar(e.target);

            // Scroll vers le haut
            $('html, body').animate({ scrollTop: 0 }, 300);

            // --- Guide IA Proactif ---
            const tabId = $(e.target).attr('id');
            if (tabId === 'professional-tab') {
                showAiTip("Ici, vous configurez le poste. <b>Rappel :</b> La catégorie détermine le salaire minima légal.");
            } else if (tabId === 'documents-tab') {
                showAiTip("Veuillez joindre les documents requis. Les fichiers PDF sont recommandés pour une meilleure lisibilité.");
            } else if (tabId === 'financial-tab') {
                showAiTip("Dernière étape ! Vérifiez bien les coordonnées bancaires pour éviter tout rejet de virement.");
            }
        });

        // Initialisation au chargement
        updateNavButtons();
        updateProgressBar($('.nav-tabs .nav-link.active')[0]);
    });

    // ==============================================
    // FONCTIONS GLOBALES (hors jQuery ready)
    // ==============================================

    // Calcul automatique du nombre de parts
    function status() {
        var x = document.getElementById("martalstatu_id").value;
        var y = parseInt(document.getElementById("enfant").value);
        var z = parseInt(document.getElementById("personneinf").value);
        let nombreDeParts = 1;

        if (x == '2' && y == 0) {
            nombreDeParts = 2;
        } else {
            if (y > 0) {
                if (x == '1' || x == '3') {
                    nombreDeParts = 2 + Math.max(0, y - 1) * 0.5;
                } else if (x == '2' || x == '4') {
                    nombreDeParts = 2.5 + Math.max(0, y - 1) * 0.5;
                }
            }
        }
        nombreDeParts = nombreDeParts + parseFloat(z);
        document.getElementById("parts").value = Math.min(nombreDeParts, 5);
    }

    // Génération automatique du nom d'utilisateur.
    // Une fois pose, l'identifiant ne bouge plus : c'est celui que l'employe
    // utilisera pour se connecter (application mobile comprise). Le bouton
    // "regenerer" reste le seul moyen d'en obtenir un autre.
    function construireUsername() {
        const name = document.getElementById('name').value.replace(/\s+/g, '').toUpperCase();
        if (name.length < 4) {
            return null;
        }
        return name.substring(0, 4) + Math.floor(10 + Math.random() * 90);
    }

    function generateUsername(forcer = false) {
        const champUsername = document.getElementById('username');
        if (!forcer && champUsername.value.trim() !== '') {
            return;
        }

        const username = construireUsername();
        if (username) {
            champUsername.value = username;
        }
    }

    // Génération de mot de passe sécurisé
    document.addEventListener('DOMContentLoaded', function() {
        const generatePasswordBtn = document.getElementById('generatePassword');
        if (generatePasswordBtn) {
            generatePasswordBtn.addEventListener('click', function() {
                const length = 12;
                const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+~`|}{[]\\:;?><,./-=';
                let password = '';
                password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)];
                password += '0123456789'[Math.floor(Math.random() * 10)];
                password += '!@#$%^&*'[Math.floor(Math.random() * 8)];
                for (let i = 3; i < length; i++) {
                    password += charset[Math.floor(Math.random() * charset.length)];
                }
                password = password.split('').sort(() => 0.5 - Math.random()).join('');
                document.getElementById('password').value = password;
                document.getElementById('password_confirmation').value = password;
            });
        }

        // Afficher/masquer le mot de passe
        const showPasswordBtn = document.getElementById('showPassword');
        if (showPasswordBtn) {
            showPasswordBtn.addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const passwordConfirmationInput = document.getElementById('password_confirmation');
                const iconPassword = document.getElementById('iconPassword');

                if (passwordInput.type === 'password') {
                    iconPassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
                    passwordInput.type = 'text';
                    passwordConfirmationInput.type = 'text';
                } else {
                    iconPassword.innerHTML = '<i class="fas fa-eye"></i>';
                    passwordInput.type = 'password';
                    passwordConfirmationInput.type = 'password';
                }
            });
        }

        // Générer le nom d'utilisateur si le nom est saisi et qu'aucun
        // identifiant n'a encore été attribué
        if (document.getElementById('name').value) {
            generateUsername();
        }

        const boutonRegenerer = document.getElementById('regenerateUsername');
        if (boutonRegenerer) {
            boutonRegenerer.addEventListener('click', function () {
                if (!construireUsername()) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Nom complet requis',
                        text: "Saisissez d'abord le nom complet (4 caractères minimum) pour générer un identifiant.",
                        confirmButtonText: 'Compris',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    return;
                }
                generateUsername(true);
            });
        }

        var nameInput = document.getElementById('name');

        // Ajouter un écouteur d'événements pour détecter les modifications dans le champ "Nom & Prénoms"
        nameInput.addEventListener('input', function() {
            // Récupérer la valeur saisie dans le champ "Nom & Prénoms"
            var nameValue = this.value;

            // Mettre à jour la valeur du champ "Account Holder Name" avec la valeur saisie dans le champ "Nom & Prénoms"
            document.getElementById('account_holder_name_input').value = nameValue;
        });

        // Récupérer l'élément du champ "Account Number" par son identifiant
        var accountNumberInput = document.getElementById('account_number_input');

        // Ajouter un écouteur d'événements pour détecter les modifications dans le champ "Account Number"
        accountNumberInput.addEventListener('input', function() {
            // Vérifier si la longueur de la valeur saisie dépasse 25 caractères
            if (this.value.length > 25) {
                // Si oui, raccourcir la valeur saisie à 25 caractères
                this.value = this.value.slice(0, 25);
            }
        });

        // Gestion des erreurs de validation
        const form = document.getElementById('employeeForm');
        const errorAlert = document.querySelector('.alert-danger, .alert-warning');

        // S'il y a des erreurs, scroller vers la première erreur
        if (errorAlert) {
            errorAlert.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Mettre en évidence temporairement l'alerte
            errorAlert.style.animation = 'pulse 1s ease-in-out 3';

            // Trouver et mettre en évidence les champs avec des erreurs
            const errorFields = form.querySelectorAll('.is-invalid, .border-danger');
            errorFields.forEach(field => {
                field.classList.add('error-highlight');
                field.focus();
            });

            // Activer l'onglet contenant la première erreur
            const firstErrorField = errorFields[0];
            if (firstErrorField) {
                const tabPane = firstErrorField.closest('.tab-pane');
                if (tabPane) {
                    const tabId = tabPane.id;
                    const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
                    if (tabButton) {
                        // Activer l'onglet
                        const tab = new bootstrap.Tab(tabButton);
                        tab.show();
                    }
                }
            }
        }

        // Animation CSS pour la mise en évidence
        const style = document.createElement('style');
        style.textContent = `
            @keyframes pulse {
                0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
                70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
                100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
            }

            .error-highlight {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
                transition: all 0.3s ease;
            }

            .error-highlight:focus {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
            }
        `;
        document.head.appendChild(style);

        // Enlever la mise en évidence après correction
        form.addEventListener('input', function(e) {
            if (e.target.classList.contains('error-highlight')) {
                e.target.classList.remove('error-highlight');
            }
        });

        // Confirmation avant de quitter la page si des données sont modifiées
        let formChanged = false;
        form.addEventListener('change', function() {
            formChanged = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (formChanged) {
                e.preventDefault();
                e.returnValue = 'Les modifications non enregistrées seront perdues. Voulez-vous vraiment quitter ?';
            }
        });

        // Réinitialiser le flag après soumission réussie
        form.addEventListener('submit', function() {
            formChanged = false;
        });

        // --- Déclencheurs spécifiques Guide IA ---
        document.getElementById('name').addEventListener('focus', function() {
            showAiTip("Saisissez le nom exactement comme il apparaît sur la pièce d'identité de l'employé.");
        });

        document.getElementById('category_job_id').addEventListener('change', function() {
            if(this.value) {
                showAiTip("Excellent ! J'ai mis à jour les catégories disponibles pour ce type de poste.");
            }
        });
    });
</script>
@endpush

@push('scripts')
    @php
        // Les deux chemins d'erreur de EmployeesController::store() renvoient
        // view(...)->with('error', ...) : c'est une variable de vue, pas un flash.
        $messageTechnique = $error ?? session('error');
    @endphp
    @if($errors->any() || $messageTechnique)
        <script>
            $(function () {
                'use strict';

                // Onglet dans lequel se trouve chaque champ du formulaire
                const ongletsParChamp = {
                    personal: ['name', 'dob', 'gender', 'charge_expat', 'nationality', 'cmu',
                        'martalstatu_id', 'enfant', 'personneinf', 'parts', 'email', 'phone',
                        'address', 'username', 'password', 'password_confirmation'],
                    professional: ['employee_id', 'branch_id', 'department_id', 'designation_id',
                        'salary_type', 'category_job_id', 'category_id', 'salaire_minima_horaire',
                        'salaire_minima_mensuel', 'end_leave', 'charge_cmu', 'num_secu_soc',
                        'charge_cnps', 'num_cnps', 'charge_its'],
                    documents: ['document', 'emp_doc_id'],
                    financial: ['account_holder_name', 'account_number', 'bank_name',
                        'bank_identifier_code', 'orange_money', 'mtn_money', 'moov_money', 'wave_money']
                };

                const libellesOnglets = {
                    personal: 'Informations Personnelles',
                    professional: 'Données du poste',
                    documents: 'Documents',
                    financial: 'Informations Financières',
                    autre: 'Autre'
                };

                const erreurs = @json($errors->toArray());
                const messageTechnique = @json($messageTechnique);

                function ongletDuChamp(champ) {
                    const racine = champ.split('.')[0];
                    for (const onglet in ongletsParChamp) {
                        if (ongletsParChamp[onglet].indexOf(racine) !== -1) {
                            return onglet;
                        }
                    }
                    return 'autre';
                }

                // Regrouper les messages par onglet pour dire où se situe le problème
                const parOnglet = {};
                let premierChamp = null;

                Object.keys(erreurs).forEach(function (champ) {
                    const onglet = ongletDuChamp(champ);
                    if (!parOnglet[onglet]) {
                        parOnglet[onglet] = [];
                    }
                    erreurs[champ].forEach(function (message) {
                        parOnglet[onglet].push(message);
                    });
                    if (!premierChamp) {
                        premierChamp = champ.split('.')[0];
                    }
                });

                let html = '';

                Object.keys(parOnglet).forEach(function (onglet) {
                    html += '<div class="text-start mb-3">';
                    html += '<div class="fw-bold mb-1">Onglet « ' + libellesOnglets[onglet] + ' »</div>';
                    html += '<ul class="mb-0 ps-3">';
                    parOnglet[onglet].forEach(function (message) {
                        html += '<li>' + message + '</li>';
                    });
                    html += '</ul></div>';
                });

                if (messageTechnique) {
                    html += '<div class="text-start"><div class="fw-bold mb-1">Détail technique</div>' +
                        '<div class="small text-muted">' + messageTechnique + '</div></div>';
                }

                const nombre = Object.keys(erreurs).length;

                Swal.fire({
                    icon: 'error',
                    title: nombre > 0
                        ? nombre + (nombre > 1 ? ' champs à corriger' : ' champ à corriger')
                        : "L'employé n'a pas pu être créé",
                    html: html,
                    width: 600,
                    confirmButtonText: premierChamp ? 'Aller au premier problème' : 'Fermer',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                }).then(function () {
                    if (!premierChamp) {
                        return;
                    }

                    // Ouvrir l'onglet concerné puis mettre le focus sur le champ fautif
                    const onglet = ongletDuChamp(premierChamp);
                    const bouton = document.getElementById(onglet + '-tab');
                    if (bouton) {
                        bootstrap.Tab.getOrCreateInstance(bouton).show();
                    }

                    const champ = document.querySelector('[name="' + premierChamp + '"]');
                    if (champ) {
                        champ.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        champ.focus({ preventScroll: true });
                    }
                });
            });
        </script>
    @endif
@endpush
