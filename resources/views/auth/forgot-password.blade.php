@extends('layouts.auth')

@section('title', 'Mot de passe oublié - RH Flow')
@section('subtitle', 'Réinitialisez votre mot de passe')

@section('content')
<form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
    @csrf

    <!-- Email -->
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
                   value="{{ old('email') }}"
                   required
                   autocomplete="email"
                   placeholder="votre@email.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-text">
            Entrez l'adresse email de votre compte : vous recevrez un lien pour choisir un nouveau mot de passe.
        </div>
    </div>

    <!-- Bouton d'envoi -->
    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary btn-lg text-white">
            <i class="ti ti-send me-2"></i>
            Envoyer le lien de réinitialisation
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

.text-decoration-none:hover {
    color: #263d88 !important;
}
</style>
@endpush
