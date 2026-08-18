<?php

namespace Modules\LandingPage\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Order;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Modules\LandingPage\Services\GeniusPayService;

class PaymentController extends Controller
{
    protected $geniusPay;

    public function __construct(GeniusPayService $geniusPay)
    {
        $this->geniusPay = $geniusPay;
    }

    /**
     * Initier le paiement via Genius Pay
     */
    public function initiate(Order $order)
    {
        try {
            // Vérifier si la commande est déjà payée
            if ($order->status === 'paid') {
                return redirect()->route('order.success.view', $order)
                    ->with('success', 'Cette commande a déjà été payée.');
            }

            $successUrl = route('order.payment.callback', ['order' => $order->id, 'status' => 'success']);
            $cancelUrl = route('order.payment.callback', ['order' => $order->id, 'status' => 'cancel']);

            $response = $this->geniusPay->createPayment($order, $successUrl, $cancelUrl);

            if (isset($response['data']['checkout_url'])) {
                // Optionnellement enregistrer une trace de la transaction
                $order->update([
                    'notes' => ($order->notes ? $order->notes . "\n" : "") . "Lien de paiement Genius Pay généré."
                ]);

                return redirect()->away($response['data']['checkout_url']);
            }

            return redirect()->back()->with('error', 'Impossible de générer le lien de paiement Genius Pay.');

        } catch (\Exception $e) {
            Log::error('Erreur initiation Genius Pay : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la connexion à la passerelle de paiement.');
        }
    }

    /**
     * Gère le retour de l'utilisateur après le paiement (Redirection)
     */
    public function callback(Request $request, Order $order)
    {
        Log::info('Callback Genius Pay hit', $request->all());
        $status = $request->get('status');

        // Accepter 'success' (notre paramètre) ou 'completed'/'COMPLETED' (paramètres Genius Pay)
        if (in_array(strtolower($status), ['success', 'completed'])) {
            return redirect()->route('order.success.view', $order)
                ->with('success', 'Votre paiement a été initié avec succès. Votre compte sera activé dès confirmation.');
        }

        return redirect()->route('order.payment', $order)
            ->with('error', 'Le paiement a été annulé ou a échoué. Veuillez réessayer.');
    }

    /**
     * Webhook Genius Pay pour les notifications en arrière-plan
     */
    public function webhook(Request $request)
    {
        // Gérer les pings ou visites GET dans le navigateur de manière conviviale
        if ($request->isMethod('get')) {
            return response()->json([
                'status' => 'active',
                'message' => 'GeniusPay Webhook Endpoint is active and ready to receive POST requests.',
                'timestamp' => now()->toIso8601String()
            ]);
        }

        $payload = $request->getContent();
        $signature = $request->header('X-Webhook-Signature');
        $timestamp = $request->header('X-Webhook-Timestamp');
        $headerEvent = $request->header('X-Webhook-Event');

        Log::info('Webhook Genius Pay reçu', [
            'timestamp' => $timestamp,
            'signature' => $signature,
            'event' => $headerEvent
        ]);

        if (!$this->geniusPay->verifyWebhookSignature($payload, $timestamp, $signature)) {
            Log::warning('Signature Webhook Genius Pay invalide');
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);
        if (!$data) {
            Log::warning('Webhook Genius Pay: Payload JSON invalide ou vide');
            return response()->json(['message' => 'Invalid JSON'], 400);
        }

        // Récupérer l'identifiant de la commande depuis les différents emplacements possibles du payload
        $orderNumber = $data['order_id'] ?? 
                       ($data['data']['metadata']['order_id'] ?? 
                       ($data['data']['order_id'] ?? 
                       ($data['data']['reference'] ?? 
                       ($data['reference'] ?? null))));

        // Récupérer le statut
        $status = $data['status'] ?? 
                  ($data['data']['status'] ?? null);

        // Déterminer si le paiement est réussi (completed, success, ou event est payment.success)
        $isSuccessful = false;
        if ($headerEvent && in_array(strtolower($headerEvent), ['payment.success', 'payment.completed'])) {
            $isSuccessful = true;
        } elseif ($status && in_array(strtolower($status), ['completed', 'success', 'paid'])) {
            $isSuccessful = true;
        }

        Log::info('Webhook Genius Pay - Parsing du payload', [
            'extracted_order_number' => $orderNumber,
            'extracted_status' => $status,
            'is_successful' => $isSuccessful
        ]);

        if ($orderNumber && $isSuccessful) {
            // Rechercher la commande par order_number ou par ID
            $order = Order::where('order_number', $orderNumber)
                ->orWhere('id', $orderNumber)
                ->first();

            if ($order && $order->status !== 'paid') {
                $paymentMethod = $data['payment_method'] ?? 
                                 ($data['data']['payment_method'] ?? 
                                 ($data['data']['provider'] ?? 'genius_pay'));
                                 
                $paymentReference = $data['transaction_id'] ?? 
                                    ($data['data']['id'] ?? 
                                    ($data['data']['reference'] ?? null));

                $order->update([
                    'status' => 'paid',
                    'payment_method' => $paymentMethod,
                    'payment_reference' => $paymentReference,
                    'paid_at' => now(),
                ]);

                // Activer le compte de l'utilisateur et de l'entreprise
                $user = $order->user;
                if ($user) {
                    $user->update(['is_active' => true]);

                    $company = Company::where('user_id', $user->id)->first();
                    if ($company) {
                        $company->update(['is_active' => true]);
                    }
                }

                Log::info('Commande payée avec succès via Webhook Genius Pay', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_method' => $paymentMethod,
                    'payment_reference' => $paymentReference
                ]);
            } else {
                Log::info('Commande déjà payée ou introuvable', [
                    'order_number' => $orderNumber,
                    'exists' => (bool)$order,
                    'status' => $order ? $order->status : null
                ]);
            }
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
