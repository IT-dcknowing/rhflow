@extends('layouts.super-admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title m-0 text-primary">
                        <i class="ti ti-plus me-2"></i>
                        Créer un nouvel utilisateur
                    </h5>
                    <small class="text-muted">
                        Remplissez les informations de l'utilisateur
                    </small>
                </div>
                <a href="{{ route('super-admin.enterprises.users', $entreprise->id) }}" class="btn btn-outline-primary bg-label-primary">
                    <i class="ti ti-arrow-left me-2"></i>
                    Retour à la liste
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mt-3">
            <div class="col-xl-12 col-lg-12">
                <div class="card">  
                    <div class="card-body">
                        <form method="POST" action="{{ route('super-admin.users.store') }}">
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
                                            <label for="name" class="form-label">Nom et Prénoms <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="name"
                                                name="name"
                                                value="{{ old('name') }}"
                                                oninput="generateProposal()"
                                                required
                                                placeholder="Ex: Jean Dupont">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email de connexion <span class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                id="email"
                                                name="email"
                                                value="{{ old('email') }}"
                                                required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('username') is-invalid @enderror"
                                                id="username"
                                                name="username"
                                                value="{{ old('username') }}"
                                                readonly
                                                required>
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password"
                                                    name="password"
                                                    value="{{ old('password') }}"
                                                    required>
                                                <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()" title="Générer un mot de passe sécurisé">
                                                    <i class="ti ti-key"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password"
                                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                                    id="password_confirmation"
                                                    name="password_confirmation"
                                                    value="{{ old('password_confirmation') }}"
                                                    required>
                                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('password_confirmation')" title="Générer un mot de passe sécurisé">
                                                    <i class="ti ti-eye"></i>
                                                </button>
                                            </div>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>                                    
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="phone">Téléphone</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations entreprise -->
                                <div class="col-lg-4">
                                    <h6 class="section-title mb-3">
                                        <i class="ti ti-pin-alt me-2"></i>
                                        Informations entreprise
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label" for="enterprise_id">Entreprise</label>
                                            <input type="hidden" name="enterprise_id" value="{{ $entreprise->id }}">
                                            <input type="text" class="form-control" value="{{ $entreprise->name }}" readonly>
                                            <small class="form-text text-muted">Utilisateur créé pour l'entreprise: {{ $entreprise->name }}</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label" for="type">Type <span class="text-danger">*</span></label>
                                            <select class="form-select" id="type" name="type" required>
                                                <option value="hr" {{ old('type') == 'hr' ? 'selected' : '' }}>HR</option>
                                                <option value="paie" {{ old('type') == 'paie' ? 'selected' : '' }}>Paie</option>
                                                <option value="employee" {{ old('type') == 'employee' ? 'selected' : '' }}>Employé</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="is_active" value="0">
                                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">Actif</label>
                                            </div>
                                            @error('is_active')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <!-- Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
                                        <button type="submit" class="btn btn-primary">Créer l'Utilisateur</button>
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

function generatePassword() {
    var passwordField = document.getElementById('password');
    var passwordConfirmationField = document.getElementById('password_confirmation');

    // Caractères possibles pour le mot de passe
    var lowercase = 'abcdefghijklmnopqrstuvwxyz';
    var uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var numbers = '0123456789';
    var symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    // Assurer au moins 8 caractères avec différents types
    var password = '';

    // Ajouter au moins une minuscule
    password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));

    // Ajouter au moins une majuscule
    password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));

    // Ajouter au moins un chiffre
    password += numbers.charAt(Math.floor(Math.random() * numbers.length));

    // Ajouter au moins un symbole
    password += symbols.charAt(Math.floor(Math.random() * symbols.length));

    // Ajouter des caractères aléatoires jusqu'à atteindre au moins 8 caractères
    var allChars = lowercase + uppercase + numbers + symbols;
    while (password.length < 8) {
        password += allChars.charAt(Math.floor(Math.random() * allChars.length));
    }

    // Mélanger le mot de passe pour plus de sécurité
    password = password.split('').sort(function() {
        return 0.5 - Math.random();
    }).join('');

    // Remplir les champs
    passwordField.value = password;
    passwordConfirmationField.value = password;

    // Afficher le mot de passe généré (optionnel, pour que l'utilisateur puisse le voir)
    // Vous pouvez commenter cette ligne si vous ne voulez pas afficher le mot de passe
    // alert('Mot de passe généré: ' + password);
}

function updatePasswordStrength() {
    var passwordField = document.getElementById('password');
    var strengthIndicator = document.getElementById('password-strength-indicator');

    if (!strengthIndicator) {
        strengthIndicator = document.createElement('div');
        strengthIndicator.id = 'password-strength-indicator';
        strengthIndicator.style.cssText = 'margin-top: 5px; font-size: 12px;';
        passwordField.parentElement.appendChild(strengthIndicator);
    }

    var password = passwordField.value;
    if (password.length === 0) {
        strengthIndicator.innerHTML = '';
        return;
    }

    var result = checkPasswordStrength(password);

    var strengthText = '';
    var strengthClass = '';

    if (result.strength <= 2) {
        strengthText = '🔴 Faible';
        strengthClass = 'text-danger';
    } else if (result.strength <= 3) {
        strengthText = '🟡 Moyen';
        strengthClass = 'text-warning';
    } else {
        strengthText = '🟢 Fort';
        strengthClass = 'text-success';
    }

    strengthIndicator.innerHTML = `
        <span class="${strengthClass}">${strengthText}</span>
        ${result.feedback.length > 0 ? '<br><small class="text-muted">Conseils: ' + result.feedback.join(', ') + '</small>' : ''}
    `;
}

function togglePasswordVisibility(inputId) {
    const passwordField = document.getElementById(inputId);
    const button = document.querySelector(`button[onclick="togglePasswordVisibility('${inputId}')"]`);
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        button.innerHTML = '<i class="ti ti-power-off"></i>';
    } else {
        passwordField.type = 'password';
        button.innerHTML = '<i class="ti ti-eye"></i>';
    }
}
</script>
@endpush