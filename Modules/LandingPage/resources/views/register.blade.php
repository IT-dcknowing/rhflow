@extends('landingpage::master.app')

@section('title', 'Inscription - RHFlow')

@section('content')
<section class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="{{ route('landingpage') }}" class="inline-flex items-center mb-6">
                   <img src="{{ asset('img/logos/logo-dark.png') }}" width="100px" alt="logo">
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Créer votre compte RHFlow</h1>
                <p class="text-gray-600">Rejoignez des centaines d'entreprises qui font confiance à RHFlow pour la gestion de leur paie</p>
            </div>

            <!-- Formulaire d'inscription -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
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
                <form action="{{ route('register.store') }}" method="POST" class="space-y-6" id="registrationForm">
                    @csrf

                    <!-- Alertes de validation globales -->
                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6" id="error-summary">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="ri-error-warning-fill text-red-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700 font-bold">
                                        Il y a des erreurs dans le formulaire. Veuillez vérifier les champs en rouge.
                                    </p>
                                    <ul class="mt-1 text-xs text-red-600 list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Informations du plan sélectionné -->
                    @if(request('plan_id'))
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-blue-900">Plan sélectionné</h3>
                                    <p class="text-blue-700">{{ App\Models\Plan::find(request('plan_id'))->name ?? 'Plan inconnu' }}</p>
                                </div>
                                <div class="text-right">
                                    @if($plan = App\Models\Plan::find(request('plan_id')))
                                        <p class="text-2xl font-bold text-blue-900">{{ number_format($plan->price, 0, ',', ' ') }} FCFA</p>
                                        <p class="text-sm text-blue-600">par mois</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Messages d'erreur globaux -->
                    @if ($errors->has('plan_id') || $errors->has('terms'))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="ri-error-warning-line text-red-400 text-xl"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Erreur de validation</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @if ($errors->has('plan_id'))
                                                <li>{{ $errors->first('plan_id') }}</li>
                                            @endif
                                            @if ($errors->has('terms'))
                                                <li>{{ $errors->first('terms') }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Informations personnelles -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Informations personnelles</h3>
                            
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-4 py-2 border {{ $errors->has('name') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    value="{{ old('name') }}" placeholder="Jean Dupont" oninput="generateProposal()">
                                @if ($errors->has('name'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('name') }}</p>
                                @endif
                            </div>

                            <input type="text"
                                id="username"
                                name="username"
                                value="{{ old('username') }}"
                                style="display: none;">
                                
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Adresse email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-2 border {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    value="{{ old('email') }}" placeholder="jean@exemple.com">
                                @if ($errors->has('email'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('email') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Téléphone <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" required
                                    class="w-full px-4 py-2 border {{ $errors->has('phone') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    value="{{ old('phone') }}" placeholder="+225 07 67 13 19 93">
                                @if ($errors->has('phone'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('phone') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-2 border {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    placeholder="••••••••">
                                <p class="text-xs text-gray-500 mt-1">Minimum 8 caractères</p>
                                @if ($errors->has('password'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('password') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmer le mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full px-4 py-2 border {{ $errors->has('password_confirmation') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    placeholder="••••••••">
                                @if ($errors->has('password_confirmation'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('password_confirmation') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Informations de l'entreprise -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Informations de l'entreprise</h3>
                            
                            <div>
                                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nom de l'entreprise <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="company_name" name="company_name" required
                                    class="w-full px-4 py-2 border {{ $errors->has('company_name') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    value="{{ old('company_name') }}" placeholder="SARL Exemple">
                                @if ($errors->has('company_name'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('company_name') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">
                                    Adresse
                                </label>
                                <textarea id="company_address" name="company_address" rows="3"
                                    class="w-full px-4 py-2 border {{ $errors->has('company_address') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    placeholder="Abidjan, Cocody, Rue des Princes">{{ old('company_address') }}</textarea>
                                @if ($errors->has('company_address'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('company_address') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="company_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Téléphone de l'entreprise
                                </label>
                                <input type="tel" id="company_phone" name="company_phone"
                                    class="w-full px-4 py-2 border {{ $errors->has('company_phone') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    value="{{ old('company_phone') }}" placeholder="+225 27 20 00 00 00">
                                @if ($errors->has('company_phone'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('company_phone') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="company_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email de l'entreprise
                                </label>
                                <input type="email" id="company_email" name="company_email"
                                    class="w-full px-4 py-2 border {{ $errors->has('company_email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent"
                                    value="{{ old('company_email') }}" placeholder="contact@entreprise.com">
                                @if ($errors->has('company_email'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('company_email') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="company_size" class="block text-sm font-medium text-gray-700 mb-1">
                                    Taille de l'entreprise <span class="text-red-500">*</span>
                                </label>
                                <select id="company_size" name="company_size" required
                                    class="w-full px-4 py-2 border {{ $errors->has('company_size') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500' }} rounded-lg focus:ring-2 focus:border-transparent">
                                    <option value="">Sélectionner...</option>
                                    <option value="startup" {{ old('company_size') == 'startup' ? 'selected' : '' }}>Startup</option>
                                    <option value="small" {{ old('company_size') == 'small' ? 'selected' : '' }}>Petite entreprise (1-50 employés)</option>
                                    <option value="medium" {{ old('company_size') == 'medium' ? 'selected' : '' }}>Moyenne entreprise (51-200 employés)</option>
                                    <option value="large" {{ old('company_size') == 'large' ? 'selected' : '' }}>Grande entreprise (201-1000 employés)</option>
                                    <option value="enterprise" {{ old('company_size') == 'enterprise' ? 'selected' : '' }}>Grande entreprise (1000+ employés)</option>
                                </select>
                                @if ($errors->has('company_size'))
                                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('company_size') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Plan sélectionné (champ caché) -->
                    <input type="hidden" name="plan_id" value="{{ request('plan_id') }}">

                    <!-- Conditions générales -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="terms" value="1" required
                                class="mt-1 mr-3 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 {{ $errors->has('terms') ? 'border-red-500 focus:ring-red-500' : '' }}"
                                {{ old('terms') ? 'checked' : '' }}>
                            <span class="text-sm text-gray-600">
                                J'accepte les <a href="#" class="text-blue-600 hover:underline">conditions générales d'utilisation</a> 
                                et la <a href="#" class="text-blue-600 hover:underline">politique de confidentialité</a> de RHFlow.
                                Je comprends que mes données seront traitées conformément à la réglementation en vigueur.
                            </span>
                        </label>
                        @if ($errors->has('terms'))
                            <p class="mt-2 text-sm text-red-600 ml-7">{{ $errors->first('terms') }}</p>
                        @endif
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <a href="{{ route('landingpage') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                            <i class="ri-arrow-left-line mr-2"></i>Retour à l'accueil
                        </a>
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="ri-user-add-line mr-2"></i>Créer mon compte
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informations de paiement -->
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">
                    <i class="ri-bank-card-line mr-2"></i>Informations de paiement
                </h3>
                <div class="space-y-3 text-sm text-yellow-800">
                    <p><strong>Paiement sécurisé par Mobile Money</strong></p>
                    <p>Après validation de votre inscription, vous recevrez les instructions pour finaliser votre paiement :</p>
                    <div class="bg-white rounded-lg p-4 space-y-2">
                        <p><strong>Référence de paiement :</strong> Vous recevrez une référence unique à utiliser lors du dépôt</p>
                        <p class="text-xs text-gray-600">Veuillez conserver votre reçu de paiement pour toute vérification</p>
                    </div>
                    <p class="text-xs">Une fois le paiement confirmé, votre compte sera activé immédiatement.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Définir la fonction en premier pour qu'elle soit disponible globalement
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

document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');

    function validatePasswords() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }
    
    password.addEventListener('change', validatePasswords);
    passwordConfirmation.addEventListener('keyup', validatePasswords);
    
    // Mettre le focus sur le premier champ en erreur
    const firstErrorField = document.querySelector('input.border-red-500, textarea.border-red-500, select.border-red-500');
    if (firstErrorField) {
        firstErrorField.focus();
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endpush