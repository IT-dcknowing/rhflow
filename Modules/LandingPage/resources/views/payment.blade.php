@extends('landingpage::master.app')

@section('title', 'Paiement - RHFlow')

@section('content')
<section class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="{{ route('landingpage') }}" class="inline-flex items-center mb-6">
                    <img src="{{ asset('img/logos/logo-dark.png') }}" width="100px" alt="logo">
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Finaliser votre paiement</h1>
                <p class="text-gray-600">Suivez les instructions ci-dessous pour activer votre compte RHFlow</p>
            </div>

            <!-- Message de succès -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="ri-check-line text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Message d'erreur -->
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="ri-error-warning-line text-red-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Détails de la commande -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Récapitulatif de la commande</h2>
                   
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Informations de la commande -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Détails de la commande</h3>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Numéro de commande:</span>
                            <span class="font-medium">{{ $order->order_number }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Plan sélectionné:</span>
                            <span class="font-medium">{{ $order->plan->name }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Montant:</span>
                            <span class="font-bold text-green-600">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Date d'expiration:</span>
                            <span class="font-medium">{{ $order->expires_at->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Informations du client -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Informations du client</h3>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Nom:</span>
                            <span class="font-medium">{{ $order->user->name }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ $order->user->email }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Téléphone:</span>
                            <span class="font-medium">{{ $order->user->company->phone }}</span>
                        </div>
                        
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Entreprise:</span>
                            <span class="font-medium">{{ $order->user->company->name }}</span>
                        </div>
                    </div>
                </div>
            </div>


            <!-- <div class="bg-yellow-50 rounded-2xl shadow-xl p-8 mb-6">
                <div class="flex items-center mb-6">
                    <i class="ri-bank-card-line text-yellow-600 text-2xl mr-3"></i>
                    <h2 class="text-2xl font-bold text-yellow-900">Instructions de paiement</h2>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <strong class="text-blue-800">Information importante :</strong> 
                    <span class="text-blue-700">Choisissez votre mode de paiement ci-dessous. Après paiement, vous devrez confirmer la transaction pour recevoir votre ticket.</span>
                </div>

               
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Choisissez votre mode de paiement :</h3>

                   
                    <div id="mobile-option" class="payment-option bg-white border-2 border-gray-200 rounded-xl p-6 cursor-pointer transition-all duration-300 hover:border-blue-500 hover:shadow-lg hover:-translate-y-1" onclick="selectPayment('mobile')">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">💱 Mobile Money</h3>
                                <p class="text-gray-600">Orange Money, MTN Money, Moov Money, Wave</p>
                            </div>
                            <div class="text-2xl text-blue-500">
                                <i class="ri-smartphone-line"></i>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div id="mobile-qr" class="qr-section hidden">
                        <div class="bg-white border border-gray-200 rounded-xl p-6 mt-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">🔄 Paiement Mobile Money</h3> 
                            
                            <div class="space-y-4"> 
                                <div class="text-center">
                                    <a class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-center"  href="https://ebank.gtbankci.com/MerchandQRCode/Home/PayMe?pay2=2cc60a88-68d4-48d7-abcc-f82ed5922083" target="_blank">Cliquez ici pour payer</a> 
                                </div> 
                                <div class="text-center text-gray-500 font-medium">------------- Ou -------------</div>
                                
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="space-y-3">
                                        <div class="flex items-start">
                                            <span class="text-blue-500 mr-3">1.</span>
                                            <p class="text-gray-700">Scannez le QR Code ci-dessous (Utilisez votre caméra ou votre application dédiée)</p>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-blue-500 mr-3">2.</span>
                                            <p class="text-gray-700">Validez le paiement de <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></p>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="text-blue-500 mr-3">3.</span>
                                            <p class="text-gray-700">Confirmez ci-dessous après paiement</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <img src="{{ asset('img/QrCodeGTBank.jpeg') }}" alt="QR Code GTBank" class="mx-auto rounded-lg shadow-md" style="max-width: 200px; max-height: 200px;" /> <br>
                                 
                                </div>
                            </div>
                        </div>
                    </div>

                   
                    <div id="confirmation-form" class="confirmation-form hidden">
                        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 mt-4">
                            <h3 class="text-lg font-semibold text-green-900 mb-4">✅ Confirmer votre paiement</h3>
                            <p class="text-green-700 mb-6">Une fois votre paiement effectué, veuillez remplir les informations ci-dessous :</p>
                            
                            <form id="payment-confirmation-form" action="{{ route('order.success', $order) }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                
                                <div>
                                    <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-2">Référence de la transaction *</label>
                                    <input type="text" id="payment_reference" name="payment_reference" required 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Ex: MP250814.1633.... ou numéro de confirmation">
                                </div>
                                
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Mode de paiement utilisé *</label>
                                    <select id="payment_method" name="payment_method" required 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Choisir...</option>
                                        <option value="orange_money">Orange Money</option>
                                        <option value="mtn_money">MTN Money</option>
                                        <option value="moov_money">Moov Money</option>
                                        <option value="wave_money">Wave</option>
                                        <option value="espece">Espèce</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-2">Montant payé (FCFA) *</label>
                                    <input type="number" id="payment_amount" name="payment_amount" required readonly
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed"
                                        value="{{ $order->total_amount }}">
                                </div>
                                
                                <div>
                                    <label for="payment_comment" class="block text-sm font-medium text-gray-700 mb-2">Commentaire (optionnel)</label>
                                    <textarea id="payment_comment" name="payment_comment" rows="3" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Ajoutez des informations complémentaires si nécessaire"></textarea>
                                </div> -->
                                
            @if($order->total_amount > 0)
            <!-- Activation  de compte -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="ri-bank-card-line text-blue-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Activation de compte</h2>
                    <p class="text-gray-600">Veuillez effectuer le paiement pour activer votre compte</p>
                </div>
                
                <!-- Montant à payer -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-6 text-center">
                    <p class="text-sm text-gray-600 mb-2">Montant à payer</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
                </div>

                <!-- Instructions de paiement -->
                <div class="space-y-6">
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Payer par Mobile Money ou Carte</h3>
                        
                        <div class="max-w-md mx-auto">
                            <!-- Bouton Genius Pay -->
                            <a href="{{ route('order.pay.genius', $order) }}" class="flex items-center justify-center bg-blue-600 text-white px-8 py-4 rounded-xl hover:bg-blue-700 transition-all transform hover:scale-105 shadow-lg group">
                                <span class="text-2xl mr-3 group-hover:rotate-12 transition-transform">💳</span>
                                <div class="text-left">
                                    <span class="block font-bold text-lg">Payer maintenant</span>
                                    <span class="block text-xs text-blue-100 italic">Orange, MTN, Moov, Wave, Visa/Mastercard</span>
                                </div>
                                <i class="ri-arrow-right-line ml-4 text-xl"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-white text-gray-500 font-medium italic">Ou paiement manuel par Wave</span>
                        </div>
                    </div>

                    <!-- Étapes de paiement Manuel -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center font-bold mr-3 italic">💱</div>
                            <h4 class="font-bold text-gray-900">Transfert Direct Wave</h4>
                        </div>
                        <div class="space-y-3 mb-6">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">1</span>
                                <p class="text-gray-700">Effectuez le transfert de <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong> au numéro <span class="font-bold text-blue-600">+225 07 67 13 19 93</span></p>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold mr-3">2</span>
                                <p class="text-gray-700">Indiquez votre nom en référence.</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <button onclick="window.location.href='{{ route('order.success', $order) }}'" class="text-blue-600 font-semibold hover:underline decoration-2 underline-offset-4">
                                <i class="ri-check-line mr-1"></i>J'ai déjà effectué le transfert Wave
                            </button>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @if(isset(Auth::user()->company_id))
                        <a href="{{ route('company.dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-center">
                            <i class="ri-arrow-left-line mr-2"></i>Retour à l'accueil
                        </a>
                        @else
                        <a href="{{ route('landingpage') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium text-center">
                            <i class="ri-arrow-left-line mr-2"></i>Retour à l'accueil
                        </a>
                        @endif
                        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="ri-printer-line mr-2"></i>Imprimer cette page
                        </button>
                    </div>
                </div>
            </div>
            @else
            <!-- Section pour abonnement gratuit -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6 text-center">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="ri-check-double-line text-green-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Compte déjà activé</h2>
                    <p class="text-gray-600">Votre abonnement gratuit est déjà actif</p>
                </div>
                
                <div class="text-center">
                    <a href="{{ route('login') }}" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium text-lg inline-block">
                        <i class="ri-arrow-right-line mr-2"></i>Connecter 
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>   
    // Fonction pour revenir à la page précédente
    function goBack() {
        window.history.back();
    }
</script>
@endpush