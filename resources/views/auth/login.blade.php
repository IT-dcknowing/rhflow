@extends('layouts.auth')

@section('title', 'Connexion - RH Flow')
@section('subtitle', 'Connectez-vous à votre compte')

@section('content')
<form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
    @csrf

    <!-- Email ou Username -->
    <div class="mb-3">
        <label for="email" class="form-label">Adresse email ou nom d'utilisateur</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="ti-email"></i>
            </span>
            <input type="text"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   required
                   autocomplete="email"
                   placeholder="votre@email.com ou username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text">Vous pouvez vous connecter avec votre adresse email ou votre nom d'utilisateur (6 caractères, ex: PHEN54)</div>
    </div>

    <!-- Mot de passe -->
    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <div class="input-group">
            <span class="input-group-text">
                <i class="ti-lock"></i>
            </span>
            <input type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   id="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <button class="btn btn-outline-dark" type="button" id="togglePassword">
                <i class="ti-eye"></i>
            </button>
        </div>
    </div>

    <!-- Remember me -->
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label text-dark" for="remember">
            Se souvenir de moi
        </label>
    </div>

    <!-- Bouton de connexion -->
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg text-white">
            <i class="ti ti-login me-2"></i>
            Se connecter
        </button>
    </div>

    <!-- Liens utiles -->
    <div class="text-center">
        <a href="{{ route('password.request') }}" class="text-decoration-none text-primary">
            Mot de passe oublié ?
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

/* Animation pour afficher/masquer le mot de passe */
#togglePassword {
    border-left: none;
    background: transparent;
}

#togglePassword:hover {
    background-color: #f8f9fa;
    color: #263d88;
}

.avatar-initial {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 4rem;
    height: 4rem;
    font-weight: 600;
    font-size: 1.5rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle visibility du mot de passe
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        const icon = this.querySelector('i');
        icon.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
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
