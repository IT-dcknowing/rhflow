 @extends('landingpage::master.app')

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Paiement sécurisé
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Vous allez être redirigé vers la page de paiement sécurisée de Wave.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <div class="text-center mb-6">
                    <p class="text-lg font-medium text-gray-900">Commande #{{ $order->order_number }}</p>
                    <p class="text-2xl font-bold text-blue-600 mt-2">{{ number_format($order->total_amount ?? $order->amount, 0, ',', ' ') }} FCFA</p>
                </div>

                <div id="wave-button" class="mt-6">
                    <button onclick="initiateWavePayment()" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Payer avec Wave
                    </button>
                </div>

                <div id="wave-container" class="mt-6 hidden">
                    <div id="waveform"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/waves-client@1.0.0/dist/waves.umd.min.js"></script>
    <script>
        function initiateWavePayment() {
            document.getElementById('wave-button').classList.add('hidden');
            document.getElementById('wave-container').classList.remove('hidden');
            
            // Initialiser le widget de paiement Wave
            const wave = new WaveButton({
                container: '#waveform',
                client: {
                    key: '{{ $public_key }}',
                    env: '{{ $wave_environment }}',
                },
                payment: {
                    amount: {{ $order->amount ?? 0 }},
                    currency: 'XOF',
                    order_id: '{{ $order->order_number ?? $order->id }}',
                    phone: '{{ $order->user->phone ?? "" }}',
                    callback: function(response) {
                        if (response.status === 'success') {
                            window.location.href = '{{ route("order.success", $order) }}';
                        } else {
                            window.location.href = '{{ route("order.payment", $order) }}?error=payment_failed';
                        }
                    }
                }
            });
            
            // Lancer le processus de paiement
            wave.pay();
        }
        
        // Démarrer automatiquement le processus de paiement
        document.addEventListener('DOMContentLoaded', function() {
            initiateWavePayment();
        });
    </script>
@endsection