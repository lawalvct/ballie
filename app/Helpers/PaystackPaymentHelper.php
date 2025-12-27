<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use Illuminate\Support\Str;

class PaystackPaymentHelper
{
    private $baseUrl = 'https://api.paystack.co';
    private $secretKey;
    private $publicKey;

    public function __construct()
    {
        $this->loadCredentials();
    }

    /**
     * Load Paystack credentials from settings
     */
    private function loadCredentials()
    {
        try {
            $publicKeySetting = Setting::where('slug', 'paystack_public_key')->first();
            $secretKeySetting = Setting::where('slug', 'paystack_secret_key')->first();

            $this->publicKey = $publicKeySetting ? $publicKeySetting->value : null;
            $this->secretKey = $secretKeySetting ? $secretKeySetting->value : null;
        } catch (\Exception $e) {
            Log::error('Failed to load Paystack credentials', ['error' => $e->getMessage()]);
            $this->publicKey = null;
            $this->secretKey = null;
        }
    }

    /**
     * Check if Paystack is configured
     */
    public function isConfigured()
    {
        return !empty($this->secretKey) && !empty($this->publicKey);
    }

    /**
     * Get public key for frontend
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Initialize a transaction
     *
     * @param float $amount Amount in Naira (will be converted to kobo)
     * @param string $email Customer email
     * @param string $callbackUrl URL to redirect after payment
     * @param string|null $reference Custom reference (optional)
     * @param array $metadata Additional metadata (optional)
     * @return array
     */
    public function initializeTransaction($amount, $email, $callbackUrl, $reference = null, $metadata = [])
    {
        if (!$this->isConfigured()) {
            return [
                'status' => false,
                'message' => 'Paystack is not configured. Please add API keys in settings.'
            ];
        }

        // Generate reference if not provided
        if (!$reference) {
            $reference = 'PS_' . strtoupper(Str::random(10)) . '_' . time();
        }

        // Convert amount to kobo (Paystack uses smallest currency unit)
        $amountInKobo = (int) round($amount * 100);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $email,
                'amount' => $amountInKobo,
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => $metadata,
            ]);

            $result = $response->json();

            Log::info('Paystack initialize transaction response', [
                'reference' => $reference,
                'status' => $response->status(),
                'response' => $result
            ]);

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                return [
                    'status' => true,
                    'authorization_url' => $result['data']['authorization_url'],
                    'access_code' => $result['data']['access_code'],
                    'reference' => $result['data']['reference'],
                ];
            }

            return [
                'status' => false,
                'message' => $result['message'] ?? 'Failed to initialize transaction',
                'response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('Paystack initialize transaction exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => false,
                'message' => 'Payment service unavailable. Please try again.'
            ];
        }
    }

    /**
     * Verify a transaction
     *
     * @param string $reference Transaction reference
     * @return array
     */
    public function verifyTransaction($reference)
    {
        if (!$this->isConfigured()) {
            return [
                'status' => false,
                'message' => 'Paystack is not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/transaction/verify/' . rawurlencode($reference));

            $result = $response->json();

            Log::info('Paystack verify transaction response', [
                'reference' => $reference,
                'status' => $response->status(),
                'response' => $result
            ]);

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                $data = $result['data'];
                $paymentStatus = strtolower($data['status'] ?? 'unknown');
                $isSuccessful = $paymentStatus === 'success';

                return [
                    'status' => $isSuccessful,
                    'payment_status' => $isSuccessful ? 'successful' : $paymentStatus,
                    'data' => $data,
                    'amount' => ($data['amount'] ?? 0) / 100, // Convert from kobo to Naira
                    'currency' => $data['currency'] ?? 'NGN',
                    'channel' => $data['channel'] ?? null,
                    'paid_at' => $data['paid_at'] ?? null,
                    'response' => $result
                ];
            }

            return [
                'status' => false,
                'payment_status' => 'failed',
                'message' => $result['message'] ?? 'Failed to verify transaction',
                'response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('Paystack verify transaction exception', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => false,
                'payment_status' => 'error',
                'message' => 'Failed to verify payment. Please contact support.'
            ];
        }
    }

    /**
     * List banks for bank transfer
     *
     * @param string $country Country code (default: nigeria)
     * @return array
     */
    public function listBanks($country = 'nigeria')
    {
        if (!$this->isConfigured()) {
            return [
                'status' => false,
                'message' => 'Paystack is not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/bank', [
                'country' => $country
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                return [
                    'status' => true,
                    'banks' => $result['data']
                ];
            }

            return [
                'status' => false,
                'message' => $result['message'] ?? 'Failed to fetch banks'
            ];

        } catch (\Exception $e) {
            Log::error('Paystack list banks exception', ['error' => $e->getMessage()]);
            return [
                'status' => false,
                'message' => 'Failed to fetch banks'
            ];
        }
    }

    /**
     * Create a refund
     *
     * @param string $reference Transaction reference
     * @param float|null $amount Amount to refund (null for full refund)
     * @param string|null $reason Reason for refund
     * @return array
     */
    public function refund($reference, $amount = null, $reason = null)
    {
        if (!$this->isConfigured()) {
            return [
                'status' => false,
                'message' => 'Paystack is not configured'
            ];
        }

        try {
            $payload = [
                'transaction' => $reference,
            ];

            if ($amount !== null) {
                $payload['amount'] = (int) round($amount * 100); // Convert to kobo
            }

            if ($reason) {
                $payload['customer_note'] = $reason;
                $payload['merchant_note'] = $reason;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/refund', $payload);

            $result = $response->json();

            Log::info('Paystack refund response', [
                'reference' => $reference,
                'status' => $response->status(),
                'response' => $result
            ]);

            if ($response->successful() && isset($result['status']) && $result['status'] === true) {
                return [
                    'status' => true,
                    'data' => $result['data'],
                    'message' => 'Refund initiated successfully'
                ];
            }

            return [
                'status' => false,
                'message' => $result['message'] ?? 'Failed to process refund'
            ];

        } catch (\Exception $e) {
            Log::error('Paystack refund exception', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => false,
                'message' => 'Refund service unavailable'
            ];
        }
    }

    /**
     * Verify webhook signature
     *
     * @param string $payload Raw request body
     * @param string $signature X-Paystack-Signature header value
     * @return bool
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        if (!$this->secretKey) {
            return false;
        }

        $computedSignature = hash_hmac('sha512', $payload, $this->secretKey);
        return hash_equals($computedSignature, $signature);
    }

    /**
     * Process webhook event
     *
     * @param array $event Webhook event data
     * @return array
     */
    public function processWebhook($event)
    {
        $eventType = $event['event'] ?? null;
        $data = $event['data'] ?? [];

        Log::info('Paystack webhook received', [
            'event' => $eventType,
            'reference' => $data['reference'] ?? null
        ]);

        switch ($eventType) {
            case 'charge.success':
                return [
                    'type' => 'payment_success',
                    'reference' => $data['reference'] ?? null,
                    'amount' => ($data['amount'] ?? 0) / 100,
                    'currency' => $data['currency'] ?? 'NGN',
                    'channel' => $data['channel'] ?? null,
                    'data' => $data
                ];

            case 'charge.failed':
                return [
                    'type' => 'payment_failed',
                    'reference' => $data['reference'] ?? null,
                    'data' => $data
                ];

            case 'refund.processed':
                return [
                    'type' => 'refund_processed',
                    'reference' => $data['transaction_reference'] ?? null,
                    'amount' => ($data['amount'] ?? 0) / 100,
                    'data' => $data
                ];

            default:
                return [
                    'type' => 'unknown',
                    'event' => $eventType,
                    'data' => $data
                ];
        }
    }
}
