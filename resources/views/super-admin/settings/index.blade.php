@extends('layouts.super-admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0 text-primary">
                    <i class="ti ti-settings me-2"></i>
                    Paramètres Système
                </h5>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="settingsForm" method="POST" action="{{ route('super-admin.settings.update') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Configuration générale -->
                    <div class="row">
                        <div class="col-lg-8">
                            <h6 class="section-title mb-3">Configuration Générale</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="app_name" class="form-label">Nom de l'application</label>
                                    <input type="text"
                                           class="form-control @error('app_name') is-invalid @enderror"
                                           id="app_name"
                                           name="app_name"
                                           value="{{ old('app_name', $settings['general']['app_name'] ?? 'RH Flow') }}">
                                    @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="app_url" class="form-label">URL de l'application</label>
                                    <input type="url"
                                           class="form-control @error('app_url') is-invalid @enderror"
                                           id="app_url"
                                           name="app_url"
                                           value="{{ old('app_url', $settings['general']['app_url'] ?? request()->root()) }}">
                                    @error('app_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Logo de l'application -->
                            <div class="mb-3">
                                <label for="app_logo" class="form-label">Logo de l'application</label>
                                <div class="row">
                                    <div class="col-md-8">
                                        <input type="file"
                                               class="form-control @error('app_logo') is-invalid @enderror"
                                               id="app_logo"
                                               name="app_logo"
                                               accept="image/*">
                                        @error('app_logo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Formats acceptés: JPG, PNG, GIF, WebP. Taille maximale: 2MB
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light text-center">
                                            @php
                                                $currentLogo = $settings['general']['app_logo'] ?? '';
                                                $logoUrl = $currentLogo && file_exists(public_path('storage/logos/' . $currentLogo))
                                                         ? asset('storage/logos/' . $currentLogo)
                                                         : asset('img/logos/logo.png');
                                            @endphp
                                            <img src="{{ $logoUrl }}"
                                                 alt="Logo actuel"
                                                 class="img-fluid mb-2"
                                                 style="max-height: 80px;">
                                            <small class="text-muted d-block">Logo actuel</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label">Fuseau horaire</label>
                                    <select class="form-select @error('timezone') is-invalid @enderror"
                                            id="timezone"
                                            name="timezone">
                                        <option value="Europe/Paris" {{ (old('timezone', $settings['general']['timezone'] ?? 'Europe/Paris') == 'Europe/Paris') ? 'selected' : '' }}>Europe/Paris</option>
                                        <option value="Europe/London" {{ (old('timezone', $settings['general']['timezone'] ?? '') == 'Europe/London') ? 'selected' : '' }}>Europe/London</option>
                                        <option value="America/New_York" {{ (old('timezone', $settings['general']['timezone'] ?? '') == 'America/New_York') ? 'selected' : '' }}>America/New_York</option>
                                        <option value="Asia/Tokyo" {{ (old('timezone', $settings['general']['timezone'] ?? '') == 'Asia/Tokyo') ? 'selected' : '' }}>Asia/Tokyo</option>
                                        <option value="Pacific/Auckland" {{ (old('timezone', $settings['general']['timezone'] ?? '') == 'Pacific/Auckland') ? 'selected' : '' }}>Pacific/Auckland</option>
                                    </select>
                                    @error('timezone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="default_language" class="form-label">Langue par défaut</label>
                                    <select class="form-select @error('default_language') is-invalid @enderror"
                                            id="default_language"
                                            name="default_language">
                                        <option value="fr" {{ (old('default_language', $settings['general']['default_language'] ?? 'fr') == 'fr') ? 'selected' : '' }}>Français</option>
                                        <option value="en" {{ (old('default_language', $settings['general']['default_language'] ?? '') == 'en') ? 'selected' : '' }}>English</option>
                                        <option value="es" {{ (old('default_language', $settings['general']['default_language'] ?? '') == 'es') ? 'selected' : '' }}>Español</option>
                                        <option value="de" {{ (old('default_language', $settings['general']['default_language'] ?? '') == 'de') ? 'selected' : '' }}>Deutsch</option>
                                    </select>
                                    @error('default_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="maintenance_mode" class="form-label">Mode maintenance</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('maintenance_mode') is-invalid @enderror"
                                           type="checkbox"
                                           id="maintenance_mode"
                                           name="maintenance_mode"
                                           value="1"
                                           {{ old('maintenance_mode', $settings['general']['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_mode">
                                        Activer le mode maintenance
                                    </label>
                                </div>
                                <div class="form-text">
                                    Le mode maintenance empêchera l'accès à l'application pour les utilisateurs normaux.
                                </div>
                                @error('maintenance_mode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="maintenance_message" class="form-label">Message de maintenance</label>
                                <textarea class="form-control @error('maintenance_message') is-invalid @enderror"
                                          id="maintenance_message"
                                          name="maintenance_message"
                                          rows="3"
                                          placeholder="Message affiché aux utilisateurs pendant la maintenance">{{ old('maintenance_message', $settings['general']['maintenance_message'] ?? 'Le site est en maintenance. Nous serons bientôt de retour.') }}</textarea>
                                @error('maintenance_message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Ce message sera affiché aux utilisateurs lorsqu'ils tenteront d'accéder à l'application en mode maintenance.
                                </div>
                            </div>
                        </div>

                        <!-- Configuration email -->
                        <div class="col-lg-4">
                            <h6 class="section-title mb-3">Configuration Email</h6>

                            <div class="mb-3">
                                <label for="mail_driver" class="form-label">Driver email</label>
                                <select class="form-select @error('mail_driver') is-invalid @enderror"
                                        id="mail_driver"
                                        name="mail_driver">
                                    <option value="smtp" {{ (old('mail_driver', $settings['email']['mail_driver'] ?? 'smtp') == 'smtp') ? 'selected' : '' }}>SMTP</option>
                                    <option value="mailgun" {{ (old('mail_driver', $settings['email']['mail_driver'] ?? '') == 'mailgun') ? 'selected' : '' }}>Mailgun</option>
                                    <option value="sendgrid" {{ (old('mail_driver', $settings['email']['mail_driver'] ?? '') == 'sendgrid') ? 'selected' : '' }}>SendGrid</option>
                                    <option value="log" {{ (old('mail_driver', $settings['email']['mail_driver'] ?? '') == 'log') ? 'selected' : '' }}>Log (Développement)</option>
                                </select>
                                @error('mail_driver')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="mail_host" class="form-label">Serveur SMTP</label>
                                <input type="text"
                                       class="form-control @error('mail_host') is-invalid @enderror"
                                       id="mail_host"
                                       name="mail_host"
                                       value="{{ old('mail_host', $settings['email']['mail_host'] ?? 'smtp.gmail.com') }}"
                                       placeholder="smtp.gmail.com">
                                @error('mail_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mail_port" class="form-label">Port</label>
                                    <input type="number"
                                           class="form-control @error('mail_port') is-invalid @enderror"
                                           id="mail_port"
                                           name="mail_port"
                                           value="{{ old('mail_port', $settings['email']['mail_port'] ?? 587) }}">
                                    @error('mail_port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="mail_encryption" class="form-label">Chiffrement</label>
                                    <select class="form-select @error('mail_encryption') is-invalid @enderror"
                                            id="mail_encryption"
                                            name="mail_encryption">
                                        <option value="tls" {{ (old('mail_encryption', $settings['email']['mail_encryption'] ?? 'tls') == 'tls') ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ (old('mail_encryption', $settings['email']['mail_encryption'] ?? '') == 'ssl') ? 'selected' : '' }}>SSL</option>
                                        <option value="" {{ (old('mail_encryption', $settings['email']['mail_encryption'] ?? '') === '') ? 'selected' : '' }}>Aucun</option>
                                    </select>
                                    @error('mail_encryption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mail_username" class="form-label">Nom d'utilisateur</label>
                                    <input type="text"
                                           class="form-control @error('mail_username') is-invalid @enderror"
                                           id="mail_username"
                                           name="mail_username"
                                           value="{{ old('mail_username', $settings['email']['mail_username'] ?? '') }}">
                                    @error('mail_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="mail_password" class="form-label">Mot de passe</label>
                                    <input type="password"
                                           class="form-control @error('mail_password') is-invalid @enderror"
                                           id="mail_password"
                                           name="mail_password"
                                           value="{{ old('mail_password', $settings['email']['mail_password'] ?? '') }}">
                                    @error('mail_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="mail_from_address" class="form-label">Adresse expéditeur</label>
                                <input type="email"
                                       class="form-control @error('mail_from_address') is-invalid @enderror"
                                       id="mail_from_address"
                                       name="mail_from_address"
                                       value="{{ old('mail_from_address', $settings['email']['mail_from_address'] ?? 'noreply@rhflow.com') }}"
                                       placeholder="noreply@rhflow.com">
                                @error('mail_from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="mail_from_name" class="form-label">Nom expéditeur</label>
                                <input type="text"
                                       class="form-control @error('mail_from_name') is-invalid @enderror"
                                       id="mail_from_name"
                                       name="mail_from_name"
                                       value="{{ old('mail_from_name', $settings['email']['mail_from_name'] ?? 'RH Flow') }}">
                                @error('mail_from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Sécurité -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="section-title mb-3">Sécurité</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="session_lifetime" class="form-label">Durée de session (minutes)</label>
                            <input type="number"
                                   class="form-control @error('session_lifetime') is-invalid @enderror"
                                   id="session_lifetime"
                                   name="session_lifetime"
                                   value="{{ old('session_lifetime', $settings['security']['session_lifetime'] ?? 120) }}"
                                   min="5"
                                   max="1440">
                            @error('session_lifetime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_expiry_days" class="form-label">Expiration mot de passe (jours)</label>
                            <input type="number"
                                   class="form-control @error('password_expiry_days') is-invalid @enderror"
                                   id="password_expiry_days"
                                   name="password_expiry_days"
                                   value="{{ old('password_expiry_days', $settings['security']['password_expiry_days'] ?? 90) }}"
                                   min="0"
                                   max="365">
                            @error('password_expiry_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">0 = jamais</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_login_attempts" class="form-label">Tentatives de connexion max</label>
                            <input type="number"
                                   class="form-control @error('max_login_attempts') is-invalid @enderror"
                                   id="max_login_attempts"
                                   name="max_login_attempts"
                                   value="{{ old('max_login_attempts', $settings['security']['max_login_attempts'] ?? 5) }}"
                                   min="1"
                                   max="10">
                            @error('max_login_attempts')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lockout_duration" class="form-label">Durée de blocage (minutes)</label>
                            <input type="number"
                                   class="form-control @error('lockout_duration') is-invalid @enderror"
                                   id="lockout_duration"
                                   name="lockout_duration"
                                   value="{{ old('lockout_duration', $settings['security']['lockout_duration'] ?? 15) }}"
                                   min="1"
                                   max="60">
                            @error('lockout_duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input @error('require_email_verification') is-invalid @enderror"
                                   type="checkbox"
                                   id="require_email_verification"
                                   name="require_email_verification"
                                   value="1"
                                   {{ old('require_email_verification', $settings['security']['require_email_verification'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="require_email_verification">
                                Exiger la vérification des emails
                            </label>
                        </div>
                        @error('require_email_verification')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Configuration de la devise -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="section-title mb-3">Configuration de la Devise</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="currency" class="form-label">Devise par défaut</label>
                            <select class="form-select @error('currency') is-invalid @enderror"
                                    id="currency"
                                    name="currency">
                                @foreach($currencies as $code => $currency)
                                    <option value="{{ $code }}"
                                            {{ (old('currency', auth()->user()->currency ?? 'XOF') == $code) ? 'selected' : '' }}>
                                        {{ $currency['symbol'] }} - {{ $currency['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Cette devise sera utilisée pour afficher tous les prix et montants dans l'application.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Aperçu du formatage</label>
                            <div class="border rounded p-3 bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Prix exemple :</strong><br>
                                        <span class="text-primary fs-5">{{ auth()->user()->formatPrice(150000) }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Montant avec décimales :</strong><br>
                                        <span class="text-success fs-5">{{ auth()->user()->formatPrice(1250.75) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stockage et sauvegarde -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="section-title mb-3">Stockage et Sauvegarde</h6>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="backup_frequency" class="form-label">Fréquence de sauvegarde</label>
                            <select class="form-select @error('backup_frequency') is-invalid @enderror"
                                    id="backup_frequency"
                                    name="backup_frequency">
                                <option value="daily" {{ (old('backup_frequency', $settings['backup']['backup_frequency'] ?? 'daily') == 'daily') ? 'selected' : '' }}>Quotidienne</option>
                                <option value="weekly" {{ (old('backup_frequency', $settings['backup']['backup_frequency'] ?? '') == 'weekly') ? 'selected' : '' }}>Hebdomadaire</option>
                                <option value="monthly" {{ (old('backup_frequency', $settings['backup']['backup_frequency'] ?? '') == 'monthly') ? 'selected' : '' }}>Mensuelle</option>
                            </select>
                            @error('backup_frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="retention_days" class="form-label">Rétention des sauvegardes (jours)</label>
                            <input type="number"
                                   class="form-control @error('retention_days') is-invalid @enderror"
                                   id="retention_days"
                                   name="retention_days"
                                   value="{{ old('retention_days', $settings['backup']['retention_days'] ?? 30) }}"
                                   min="1"
                                   max="365">
                            @error('retention_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-danger" onclick="resetSettings()">
                                    <i class="ti ti-refresh me-2"></i>
                                    Réinitialiser
                                </button>
                                <div>
                                    <button type="button" class="btn btn-outline-warning me-2" onclick="testEmail()">
                                        <i class="ti ti-mail me-2"></i>
                                        Tester Email
                                    </button>
                                    <button type="submit" class="btn btn-primary text-white">
                                        <i class="ti ti-check me-2"></i>
                                        Sauvegarder les Paramètres
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de test d'email -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test de Configuration Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="emailTestResult">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Test en cours...</span>
                        </div>
                        <p class="mt-2">Test de configuration email en cours...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
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

    .form-check-input:checked {
        background-color: #263d88;
        border-color: #263d88;
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

    .btn-outline-warning:hover {
        background-color: #263d88;
        border-color: #263d88;
        color: white;
    }

    .btn-outline-secondary:hover {
        background-color: #263d88;
        border-color: #263d88;
        color: white;
    }

    .form-switch .form-check-input {
        width: 3em;
        margin-left: -2.5em;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255, 255, 255, 1.0%29'/%3e%3c/svg%3e");
        background-position: left center;
        border-radius: 2em;
        transition: background-position .15s ease-in-out;
    }

    .form-switch .form-check-input:focus {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%28255, 255, 255, 1.0%29'/%3e%3c/svg%3e");
        box-shadow: 0 0 0 0.2rem rgba(38, 61, 136, 0.25);
    }

    .form-switch .form-check-input:checked {
        background-position: right center;
        background-color: #263d88;
        border-color: #263d88;
    }

    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Mise à jour de l'aperçu de devise en temps réel
    const currencySelect = document.getElementById('currency');
    if (currencySelect) {
        currencySelect.addEventListener('change', function() {
            updateCurrencyPreview();
        });
    }

    // Fonction pour mettre à jour l'aperçu de devise
    function updateCurrencyPreview() {
        const selectedCurrency = currencySelect.value;
        const currencies = @json($currencies);

        if (currencies[selectedCurrency]) {
            const currency = currencies[selectedCurrency];
            // Ici vous pourriez faire un appel AJAX pour récupérer un nouvel aperçu
            // Pour l'instant, on garde l'aperçu statique
        }
    }

    // Test de configuration email
    window.testEmail = function() {
        const modal = new bootstrap.Modal(document.getElementById('testEmailModal'));
        modal.show();

        const resultDiv = document.getElementById('emailTestResult');

        // Test réel de la configuration email via AJAX
        fetch('{{ route("super-admin.settings.test-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="ti ti-check-circle me-2"></i>
                        <strong>Test réussi !</strong><br>
                        ${data.message}
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Test échoué !</strong><br>
                        ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <strong>Erreur de connexion !</strong><br>
                    Impossible de tester la configuration email.
                </div>
            `;
        });
    };

    // Réinitialisation des paramètres
    window.resetSettings = function() {
        if (confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres aux valeurs par défaut ?')) {
            fetch('{{ route("super-admin.settings.reset") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || response.ok) {
                    showAlert('Paramètres réinitialisés avec succès !', 'success');
                    // Recharger la page après 2 secondes
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert('Erreur lors de la réinitialisation', 'danger');
                }
            })
            .catch(error => {
                showAlert('Erreur de connexion lors de la réinitialisation', 'danger');
            });
        }
    };
});
</script>
@endpush
