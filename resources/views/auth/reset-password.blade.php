@extends('layouts.auth')

@section('title', 'Réinitialiser le mot de passe - RH Flow')
@section('subtitle', 'Nouveau mot de passe')

@section('content')
<form method="POST" action="{{ route('password.reset', $code) }}" class="needs-validation" novalidate>
    @csrf

    <!-- Email du compte (prérempli depuis le lien reçu) -->
    <div class="mb-3">
        <label for="email" class="form-label">Adresse email</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="ti ti-mail"></i>
            </span>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   value="{{ $email }}"
                   required
                   autocomplete="email"
                   placeholder="votre@email.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Nouveau mot de passe -->
    <div class="mb-3">
        <label for="password" class="form-label">Nouveau mot de passe</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="ti ti-lock"></i>
            </span>
            <input type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   id="password"
                   name="password"
                   required
                   autocomplete="new-password"
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="ti ti-eye"></i>
            </button>
        </div>
        <div class="form-text">
            Le mot de passe doit contenir au moins 8 caractères.
        </div>
    </div>

    <!-- Confirmation du mot de passe -->
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="ti ti-lock-check"></i>
            </span>
            <input type="password"
                   class="form-control"
                   id="password_confirmation"
                   name="password_confirmation"
                   required
                   autocomplete="new-password"
                   placeholder="••••••••">
            <button class="btn btn-outline-success" type="button" id="togglePasswordConfirm">
                <i class="ti ti-eye"></i>
            </button>
        </div>
    </div>

    <!-- Indicateur de force du mot de passe -->
    <div class="mb-3">
        <div class="password-strength-meter">
            <div class="strength-bar">
                <div class="strength-fill" id="strengthFill"></div>
            </div>
            <small class="strength-text" id="strengthText">Force du mot de passe</small>
        </div>
    </div>

    <!-- Bouton de réinitialisation -->
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg text-white">
            <i class="ti ti-refresh me-2"></i>
            Réinitialiser le mot de passe
        </button>
    </div>

    <!-- Lien de retour -->
    <div class="text-center">
        <a href="{{ route('login') }}" class="text-decoration-none text-primary">
            <i class="ti ti-arrow-left me-2"></i>
            Retour à la connexion
        </a>
    </div>
</form>
@endsection

@push('styles')
<style>
.authentication-bg {
    background: linear-gradient(135deg, #263d88 0%, #3d5aa6 50%, #263d88 100%);
    position: relative;
}

.authentication-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1.5" fill="rgba(255,255,255,0.1)"/></svg>');
    opacity: 0.3;
}

.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

.form-control:focus {
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

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.password-strength-meter {
    margin-top: 0.5rem;
}

.strength-bar {
    width: 100%;
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.25rem;
}

.strength-fill {
    height: 100%;
    transition: all 0.3s ease;
    width: 0%;
}

.strength-fill.weak {
    background-color: #dc3545;
    width: 33%;
}

.strength-fill.medium {
    background-color: #ffc107;
    width: 66%;
}

.strength-fill.strong {
    background-color: #28a745;
    width: 100%;
}

.strength-text {
    color: #6c757d;
    font-size: 0.75rem;
}

.text-decoration-none:hover {
    color: #263d88 !important;
}

/* Animation pour afficher/masquer le mot de passe */
#togglePassword, #togglePasswordConfirm {
    border-left: none;
    background: transparent;
}

#togglePassword:hover, #togglePasswordConfirm:hover {
    background-color: #f8f9fa;
    color: #263d88;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle visibility du mot de passe
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        const icon = this.querySelector('i');
        icon.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
    });

    togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmInput.setAttribute('type', type);
        const icon = this.querySelector('i');
        icon.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
    });

    // Indicateur de force du mot de passe
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;

        if (password.length >= 8) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;

        strengthFill.className = 'strength-fill';

        if (strength <= 2) {
            strengthFill.classList.add('weak');
            strengthText.textContent = 'Mot de passe faible';
        } else if (strength <= 3) {
            strengthFill.classList.add('medium');
            strengthText.textContent = 'Mot de passe moyen';
        } else {
            strengthFill.classList.add('strong');
            strengthText.textContent = 'Mot de passe fort';
        }
    });

    // Animation du bouton
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            if (!this.form.checkValidity()) {
                e.preventDefault();
                this.form.classList.add('was-validated');
            }
        });
    }
});
</script>
@endpush
