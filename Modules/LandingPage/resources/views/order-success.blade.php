@extends('landingpage::master.app')

@section('title', 'Commande confirmée - RHFlow')

@section('content')
<section class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="{{ route('landingpage') }}" class="inline-flex items-center mb-6">
                    <img src="{{ asset('img/logos/logo-dark.png') }}" width="100px" alt="logo">
                </a>
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                        <i class="ri-check-line text-green-600 text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Paiement confirmé!</h1>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Validation en cours...</h1>
                    <p class="text-gray-600">Votre compte RHFlow est maintenant activé</p>
                </div>
            </div>

            <!-- Carte de confirmation -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center bg-green-100 text-green-800 px-4 py-2 rounded-full mb-4">
                        <i class="ri-checkbox-circle-line mr-2"></i>
                        <span class="font-medium">Commande payée avec succès. Commande en cours de validation...</span>
                    </div>
                </div>

                <!-- Détails de la commande -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Détails de la commande</h3>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Numéro de commande:</span>
                            <span class="font-medium">{{ $order->order_number }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Plan choisi:</span>
                            <span class="font-medium">{{ $order->plan->name }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Date de paiement:</span>
                            <span class="font-medium">{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : 'En cours de validation' }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Méthode de paiement:</span>
                            <span class="font-medium">Mobile Money</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Montant payé</h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Sous-total:</span>
                                <span class="font-medium">{{ number_format($order->amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            
                            @if($order->discount_amount > 0)
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Réduction:</span>
                                <span class="font-medium text-green-600">-{{ number_format($order->discount_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @endif
                            
                            @if($order->tax_amount > 0)
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-600">Taxes:</span>
                                <span class="font-medium">{{ number_format($order->tax_amount, 0, ',', ' ') }} FCFA</span>
                            </div>
                            @endif
                            
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-gray-900">Total payé:</span>
                                    <span class="text-2xl font-bold text-green-600">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations du compte -->
                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-blue-900 mb-4">
                        <i class="ri-user-line mr-2"></i>Votre compte est maintenant actif
                    </h3>
                    <div class="grid md:grid-cols-2 gap-4 text-blue-800">
                        <div>
                            <p class="font-medium mb-1">Email de connexion:</p>
                            <p class="text-sm">{{ $order->user->email }}</p>
                        </div>
                        <div>
                            <p class="font-medium mb-1">Plan actuel:</p>
                            <p class="text-sm">{{ $order->plan->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Prochaines étapes -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="ri-compass-3-line mr-2"></i>Prochaines étapes
                    </h3>
                    @if($order->status == 'validee')
                    <div class="space-y-3 text-gray-700">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">1</div>
                            <div>
                                <p class="font-medium">Connectez-vous à votre compte</p>
                                <p class="text-sm text-gray-600">Utilisez votre email et mot de passe pour accéder à votre espace RHFlow</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">2</div>
                            <div>
                                <p class="font-medium">Configurez votre entreprise</p>
                                <p class="text-sm text-gray-600">Ajoutez vos employés et configurez les paramètres de paie</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">3</div>
                            <div>
                                <p class="font-medium">Commencez à utiliser RHFlow</p>
                                <p class="text-sm text-gray-600">Générez vos bulletins de paie et simplifiez votre gestion RH</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="space-y-3 text-gray-700">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3 mt-0.5">1</div>
                            <div>
                                <p class="font-medium">Votre commande est cours de validation.</p>
                                <p class="text-sm text-gray-600">Un mail vous sera transmis avec vos paramètres de connexion.</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if($order->status == 'validee')
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium text-center">
                        <i class="ri-login-box-line mr-2"></i>Me connecter
                    </a>
                    @endif
                    <a href="{{ route('landingpage') }}" class="px-8 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-center">
                        <i class="ri-home-line mr-2"></i>Retour à l'accueil
                    </a>
                </div>
            </div>

            <!-- Support -->
            <div class="text-center text-gray-600">
                <p class="mb-2">Besoin d'aide ? Contactez notre support:</p>
                <div class="flex items-center justify-center space-x-4">
                    <a href="mailto:infos@dc-knowing.com" class="text-blue-600 hover:underline">
                        <i class="ri-mail-line mr-1"></i>infos@dc-knowing.com
                    </a>
                    <span>•</span>
                    <a href="tel:+2250767131993" class="text-blue-600 hover:underline">
                        <i class="ri-phone-line mr-1"></i>+225 07 67 13 19 93
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
