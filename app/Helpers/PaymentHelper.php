<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Setting;

class PaymentHelper
{
    /**
     * Get Nomba access token
     */
    public function nombaAccessToken()
    {
        try {
            $AccountId = Setting::where(['slug' => 'nombaAccountID'])->first()?->value;
            $client_id = Setting::where(['slug' => 'nombaClientID'])->first()?->value;
            $client_secret = Setting::where(['slug' => 'nombaPrivatekey'])->first()?->value;

            if (!$AccountId || !$client_id || !$client_secret) {
                Log::error('Nomba credentials not found in settings', [
                    'accountId' => $AccountId ? 'configured' : 'missing',
                    'clientId' => $client_id ? 'configured' : 'missing',
                    'privateKey' => $client_secret ? 'configured' : 'missing'
                ]);
                return null;
            }

            $response = Http::withHeaders([
                'AccountId' => $AccountId,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.nomba.com/v1/auth/token/issue', [
                'grant_type' => 'client_credentials',
                'client_id' => $client_id,
                'client_secret' => $client_secret,
            ]);

            $result = $response->json();

            if (isset($result['data']['access_token'])) {
                $accessToken = $result['data']['access_token'];
                return ["accessToken" => $accessToken, "accountId" => $AccountId];
            }

            Log::error('Failed to get Nomba access token', ['response' => $result]);
            return null;

        } catch (\Exception $e) {
            Log::error('Nomba access token exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Process payment through Nomba
     */
    public function processPayment($amount, $currency = 'NGN', $email = null, $callbackUrl = null, $customReference = null)
    {
        try {
            $tokenData = $this->nombaAccessToken();

            if (!$tokenData) {
                return [
                    'status' => false,
                    'message' => 'Failed to get access token'
                ];
            }

            $AccountId = $tokenData['accountId'];
            $accessToken = $tokenData['accessToken'];

            // Use authenticated user's email if not provided
            if (!$email && Auth::check()) {
                $email = Auth::user()->email;
            }

            // Generate a unique reference if not provided
            if (!$customReference) {
                $customReference = 'SUB_' . Str::uuid();
            }

            // Validate currency
            $supportedCurrencies = ['NGN', 'USD'];
            if (!in_array(strtoupper($currency), $supportedCurrencies)) {
                return [
                    'status' => false,
                    'message' => 'Unsupported currency. Supported currencies: ' . implode(', ', $supportedCurrencies)
                ];
            }

            $response = Http::withHeaders([
                'accountId' => $AccountId,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post('https://api.nomba.com/v1/checkout/order', [
                'order' => [
                    'orderReference' => $customReference,
                    'callbackUrl' => $callbackUrl,
                    'customerEmail' => $email,
                    'amount' => $amount,
                    'currency' => strtoupper($currency),
                ],
                'tokenizeCard' => 'true',
            ]);

            $result = $response->json();

            if (isset($result['data']['checkoutLink']) && isset($result['data']['orderReference'])) {
                $checkoutLink = $result['data']['checkoutLink'];
                $orderReference = $result['data']['orderReference'];

                return [
                    'status' => true,
                    'checkoutLink' => $checkoutLink,
                    'orderReference' => $orderReference,
                    'currency' => strtoupper($currency),
                    'amount' => $amount
                ];
            }

            Log::error('Nomba payment processing failed', ['response' => $result]);
            return [
                'status' => false,
                'message' => 'Failed to process payment',
                'response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('Nomba payment processing exception', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'currency' => $currency
            ]);

            return [
                'status' => false,
                'message' => 'Payment service unavailable'
            ];
        }
    }

    /**
     * Verify payment with Nomba
     */
    public function verifyPayment($orderReference)
    {
        try {
            $tokenData = $this->nombaAccessToken();

            if (!$tokenData) {
                return [
                    'status' => false,
                    'message' => 'Failed to get access token'
                ];
            }

            $AccountId = $tokenData['accountId'];
            $accessToken = $tokenData['accessToken'];

            $response = Http::withHeaders([
                'accountId' => $AccountId,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://api.nomba.com/v1/checkout/transaction?idType=ORDER_ID&id=$orderReference");

            $result = $response->json();

            // Check for success based on the provided response format
            if (isset($result['data'])) {
                // Check if success field exists directly
                if (isset($result['data']['success'])) {
                    $isSuccessful = $result['data']['success'] === true;
                    $paymentStatus = $isSuccessful ? 'successful' : 'failed';
                }
                // Check if message field contains "PAYMENT SUCCESSFUL"
                elseif (isset($result['data']['message']) && $result['data']['message'] === 'PAYMENT SUCCESSFUL') {
                    $isSuccessful = true;
                    $paymentStatus = 'successful';
                }
                // Fallback to the original status check if available
                elseif (isset($result['data']['status'])) {
                    $paymentStatus = $result['data']['status'];
                    $isSuccessful = strtolower($paymentStatus) === 'successful';
                }
                // If none of the above conditions are met
                else {
                    $isSuccessful = false;
                    $paymentStatus = 'unknown';
                }

                return [
                    'status' => $isSuccessful,
                    'payment_status' => $paymentStatus,
                    'data' => $result['data'],
                    'response' => $result
                ];
            }

            return [
                'status' => false,
                'message' => 'Failed to verify payment',
                'response' => $result
            ];

        } catch (\Exception $e) {
            Log::error('Nomba payment verification exception', [
                'error' => $e->getMessage(),
                'orderReference' => $orderReference
            ]);

            return [
                'status' => false,
                'message' => 'Payment verification service unavailable'
            ];
        }
    }
}
