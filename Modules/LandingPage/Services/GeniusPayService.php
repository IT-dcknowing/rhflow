<?php

namespace Modules\LandingPage\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeniusPayService
{
    protected $baseUrl;
    protected $publicKey;
    protected $secretKey;
    protected $webhookSecret;
    protected $environment;

    public function __construct()
    {
        $this->publicKey = config('services.genius_pay.public_key');
        $this->secretKey = config('services.genius_pay.secret_key');
        $this->webhookSecret = config('services.genius_pay.webhook_secret');
        $this->environment = config('services.genius_pay.environment');
        $this->baseUrl = config('services.genius_pay.base_url', 'https://pay.genius.ci/api/v1/merchant');
    }

    /**
     * Initier un paiement sur Genius Pay
     */
    public function createPayment(Order $order, $successUrl, $cancelUrl)
    {
        try {
            $payload = [
                'amount' => $order->total_amount,
                'currency' => $order->currency ?: 'XOF',
                'description' => "Paiement de l'abonnement : " . $order->plan->name,
                'order_id' => $order->order_number,
                'customer' => [
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                    'phone' => $order->phone ?: ($order->user->company->phone ?? ''),
                ],
                'success_url' => $successUrl,
                'error_url' => $cancelUrl,
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ]
            ];

            $response = Http::withHeaders([
                'X-API-Key' => $this->publicKey,
                'X-API-Secret' => $this->secretKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payments', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erreur de réponse Genius Pay', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);

            throw new Exception('Erreur lors de la création du paiement : ' . ($response->json()['message'] ?? 'Erreur inconnue'));

        } catch (Exception $e) {
            Log::error('Exception Genius Pay : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Vérifier la signature du webhook
     */
    public function verifyWebhookSignature($payload, $timestamp, $signature)
    {
        if (empty($signature) || empty($timestamp)) {
            return false;
        }

        $secret = $this->webhookSecret ?: $this->secretKey;

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
