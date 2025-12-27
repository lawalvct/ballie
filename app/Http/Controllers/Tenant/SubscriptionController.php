<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\Affiliate;
use App\Models\AffiliateReferral;
use App\Models\AffiliateCommission;
use App\Helpers\PaymentHelper;
use App\Helpers\PaystackPaymentHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class SubscriptionController extends Controller
{
    /**
     * Display current subscription status and overview
     */
    public function index()
    {
        $tenant = tenant();
        $currentPlan = $tenant->plan; // Get current plan from relationship
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        // Get recent payment history
        $recentPayments = $tenant->subscriptionPayments()
            ->latest()
            ->limit(5)
            ->get();

        return view('tenant.subscription.index', compact(
            'tenant',
            'currentPlan',
            'plans',
            'recentPayments'
        ));
    }    /**
     * Display available plans
     */
    public function plans()
    {
        $tenant = tenant();
        $currentPlan = $tenant->plan;
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('tenant.subscription.plans', compact(
            'tenant',
            'currentPlan',
            'plans'
        ));
    }

    /**
     * Show upgrade form for a specific plan
     */
    public function upgrade($tenant, Plan $plan)
    {
        $tenant = tenant(); // Use the tenant() helper instead of the route parameter
        $currentPlan = $tenant->plan;

        // Check if upgrade is valid
        if ($currentPlan && $currentPlan->id === $plan->id) {
            return redirect()->route('tenant.subscription.index', tenant()->slug)
                ->with('error', 'You are already on this plan.');
        }

        return view('tenant.subscription.upgrade', compact(
            'tenant',
            'plan',
            'currentPlan'
        ));
    }

    /**
     * Process upgrade to a new plan
     */
    public function processUpgrade(Request $request, $tenant, Plan $plan)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|in:nomba,paystack',
        ]);

        $tenant = tenant(); // Use the tenant() helper instead of the route parameter
        $currentPlan = $tenant->plan;

        // Calculate amount based on billing cycle
        $amount = $request->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

        // Debug logging
        Log::info('ProcessUpgrade started', [
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'billing_cycle' => $request->billing_cycle,
            'amount' => $amount
        ]);

        try {
            DB::beginTransaction();

            // Create a pending subscription record
            $subscription = $tenant->subscriptions()->create([
                'plan_id' => $plan->id,
                'plan' => $plan->slug, // Add this for backward compatibility
                'billing_cycle' => $request->billing_cycle,
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending',
                'starts_at' => now(),
                'ends_at' => $request->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
                'metadata' => [
                    'upgrade_from' => $currentPlan?->id,
                    'initiated_at' => now(),
                ]
            ]);

            // Generate unique payment reference
            $paymentReference = 'SUB_' . strtoupper(Str::random(8)) . '_' . $tenant->id;

            // Create pending payment record
            $payment = $tenant->subscriptionPayments()->create([
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_reference' => $paymentReference,
                'gateway_reference' => null, // Will be updated after gateway response
            ]);

            Log::info('Payment record created', [
                'payment_id' => $payment->id,
                'payment_reference' => $paymentReference
            ]);

            // Route to appropriate payment gateway based on payment method
            $paymentMethod = $request->payment_method;

            if ($paymentMethod === 'nomba') {
                return $this->processNombaPaymentForSubscription($request, $subscription, $payment, $tenant, $plan, $amount);
            } elseif ($paymentMethod === 'paystack') {
                return $this->processPaystackPaymentForSubscription($request, $subscription, $payment, $tenant, $plan, $amount);
            }

            DB::rollBack();
            return back()->with('error', 'Invalid payment method selected.');

            // This code has been moved to processNombaPaymentForSubscription and processPaystackPaymentForSubscription methods

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription upgrade failed', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to process upgrade. Please try again.');
        }
    }

    /**
     * Process Nomba payment for subscription
     */
    private function processNombaPaymentForSubscription($request, $subscription, $payment, $tenant, $plan, $amount)
    {
        // Initialize payment helper
        $paymentHelper = new PaymentHelper();

        // Check if Nomba credentials are configured
        $tokenData = $paymentHelper->nombaAccessToken();
        if (!$tokenData) {
            DB::rollBack();
            Log::error('Nomba credentials not configured', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id
            ]);
            return back()->with('error', 'Payment gateway not configured. Please contact administrator.');
        }

        // Prepare callback URL
        $callbackUrl = route('tenant.subscription.payment.callback', [
            'tenant' => $tenant->slug,
            'payment' => $payment->id
        ]);

        // Get user email
        $userEmail = $tenant->users()->first()?->email ?? auth()->user()?->email;

        Log::info('Initiating Nomba payment for subscription', [
            'amount' => $amount / 100,
            'userEmail' => $userEmail,
            'callbackUrl' => $callbackUrl,
            'paymentReference' => $payment->payment_reference
        ]);

        // Process payment with Nomba
        $paymentResult = $paymentHelper->processPayment(
            $amount / 100, // Convert to naira (amount is in kobo)
            'NGN',
            $userEmail,
            $callbackUrl,
            $payment->payment_reference
        );

        Log::info('Nomba payment result', $paymentResult);

        if ($paymentResult['status']) {
            // Update payment record with gateway reference
            $payment->update([
                'gateway_reference' => $paymentResult['orderReference'],
                'gateway_response' => $paymentResult,
            ]);

            DB::commit();

            Log::info('Redirecting to Nomba checkout', [
                'checkoutLink' => $paymentResult['checkoutLink']
            ]);

            // Redirect to Nomba checkout
            return redirect($paymentResult['checkoutLink']);

        } else {
            // Payment initiation failed
            DB::rollBack();

            Log::error('Nomba payment initiation failed', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'amount' => $amount,
                'error' => $paymentResult['message'] ?? 'Unknown error',
                'full_result' => $paymentResult
            ]);

            return back()->with('error', 'Failed to initiate payment: ' . ($paymentResult['message'] ?? 'Payment service unavailable'));
        }
    }

    /**
     * Process Paystack payment for subscription
     */
    private function processPaystackPaymentForSubscription($request, $subscription, $payment, $tenant, $plan, $amount)
    {
        // Initialize Paystack helper
        $paystackHelper = new PaystackPaymentHelper();

        // Check if Paystack is configured
        if (!$paystackHelper->isConfigured()) {
            DB::rollBack();
            Log::error('Paystack credentials not configured', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id
            ]);
            return back()->with('error', 'Payment gateway not configured. Please contact administrator.');
        }

        // Prepare callback URL
        $callbackUrl = route('tenant.subscription.payment.callback', [
            'tenant' => $tenant->slug,
            'payment' => $payment->id
        ]);

        // Get user email
        $userEmail = $tenant->users()->first()?->email ?? auth()->user()?->email ?? 'noreply@' . $tenant->slug . '.com';

        Log::info('Initiating Paystack payment for subscription', [
            'amount' => $amount / 100,
            'userEmail' => $userEmail,
            'callbackUrl' => $callbackUrl,
            'paymentReference' => $payment->payment_reference
        ]);

        // Initialize transaction with Paystack
        $paymentResult = $paystackHelper->initializeTransaction(
            $amount / 100, // Convert to naira (amount is in kobo)
            $userEmail,
            $callbackUrl,
            $payment->payment_reference,
            [
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id,
                'tenant_id' => $tenant->id,
                'billing_cycle' => $subscription->billing_cycle
            ]
        );

        Log::info('Paystack payment result for subscription', $paymentResult);

        if ($paymentResult['status']) {
            // Update payment record with gateway reference
            $payment->update([
                'gateway_reference' => $paymentResult['reference'],
                'gateway_response' => $paymentResult,
            ]);

            DB::commit();

            Log::info('Redirecting to Paystack checkout', [
                'authorization_url' => $paymentResult['authorization_url']
            ]);

            // Redirect to Paystack checkout
            return redirect($paymentResult['authorization_url']);

        } else {
            // Payment initiation failed
            DB::rollBack();

            Log::error('Paystack payment initiation failed', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'amount' => $amount,
                'error' => $paymentResult['message'] ?? 'Unknown error',
                'full_result' => $paymentResult
            ]);

            return back()->with('error', 'Failed to initiate payment: ' . ($paymentResult['message'] ?? 'Payment service unavailable'));
        }
    }

    /**
     * Verify payment based on payment method
     */
    private function verifyPaymentByMethod(SubscriptionPayment $payment)
    {
        $paymentMethod = $payment->payment_method;
        $reference = $payment->gateway_reference ?? $payment->payment_reference;

        Log::info('Verifying payment by method', [
            'payment_id' => $payment->id,
            'payment_method' => $paymentMethod,
            'reference' => $reference
        ]);

        if ($paymentMethod === 'nomba') {
            $paymentHelper = new PaymentHelper();
            return $paymentHelper->verifyPayment($reference);
        } elseif ($paymentMethod === 'paystack') {
            $paystackHelper = new PaystackPaymentHelper();
            return $paystackHelper->verifyTransaction($reference);
        }

        return [
            'status' => false,
            'payment_status' => 'unknown',
            'message' => 'Unsupported payment method'
        ];
    }

    /**
     * Show downgrade form
     */
    public function downgrade($tenant, Plan $plan)
    {
        $tenant = tenant(); // Use the tenant() helper instead of the route parameter
        $currentPlan = $tenant->plan;

        return view('tenant.subscription.downgrade', compact(
            'tenant',
            'plan',
            'currentPlan'
        ));
    }

    /**
     * Process downgrade to a lower plan
     */
    public function processDowngrade(Request $request, $tenant, Plan $plan)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
            'reason' => 'nullable|string|max:500',
        ]);

        $tenant = tenant(); // Use the tenant() helper instead of the route parameter
        $currentPlan = $tenant->plan;

        try {
            DB::beginTransaction();

            // Create a subscription record for the downgrade
            $subscription = $tenant->subscriptions()->create([
                'plan_id' => $plan->id,
                'plan' => $plan->slug, // Add this for backward compatibility
                'billing_cycle' => $request->billing_cycle,
                'amount' => $request->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
                'currency' => 'NGN',
                'status' => 'scheduled',
                'starts_at' => now(),
                'ends_at' => $request->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
                'metadata' => [
                    'downgrade_from' => $currentPlan?->id,
                    'reason' => $request->reason,
                    'scheduled_at' => now(),
                ]
            ]);

            DB::commit();

            return redirect()->route('tenant.subscription.index', tenant()->slug)
                ->with('success', 'Downgrade scheduled for ' . $subscription->starts_at->format('M j, Y'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription downgrade failed', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to schedule downgrade. Please try again.');
        }
    }

    /**
     * Show cancellation form
     */
    public function cancel()
    {
        $tenant = tenant();
        $currentPlan = $tenant->plan;

        return view('tenant.subscription.cancel', compact(
            'tenant',
            'currentPlan'
        ));
    }

    /**
     * Process subscription cancellation
     */
    public function processCancel(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $tenant = tenant();
        $currentPlan = $tenant->plan;

        try {
            DB::beginTransaction();

            // Create a cancellation record in subscriptions
            $subscription = $tenant->subscriptions()->create([
                'plan_id' => $currentPlan?->id,
                'plan' => $currentPlan?->slug, // Add this for backward compatibility
                'billing_cycle' => 'monthly', // Default for cancellation record
                'amount' => 0,
                'currency' => 'NGN',
                'status' => 'cancelled',
                'starts_at' => now(),
                'ends_at' => now(),
                'cancelled_at' => now(),
                'metadata' => [
                    'cancellation_reason' => $request->reason,
                    'feedback' => $request->feedback,
                    'cancelled_at' => now(),
                ]
            ]);

            // Update tenant to free plan if exists, or remove plan
            $freePlan = Plan::where('slug', 'free')->first();
            if ($freePlan) {
                $tenant->update(['plan_id' => $freePlan->id]);
            } else {
                $tenant->update(['plan_id' => null]);
            }

            DB::commit();

            $accessUntil = $tenant->trial_ends_at ?? now();
            return redirect()->route('tenant.subscription.index', tenant()->slug)
                ->with('success', 'Your subscription has been cancelled. Access will continue until ' .
                       $accessUntil->format('M j, Y'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription cancellation failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to cancel subscription. Please try again.');
        }
    }

    /**
     * Display subscription history
     */
    public function history()
    {
        $tenant = tenant();
        $subscriptions = $tenant->subscriptions()->latest()->paginate(10);
        $payments = $tenant->subscriptionPayments()->latest()->paginate(15);

        return view('tenant.subscription.history', compact(
            'tenant',
            'subscriptions',
            'payments'
        ));
    }

    /**
     * Display invoice for a payment
     */
    public function invoice($tenant, SubscriptionPayment $payment)
    {
        $tenant = tenant(); // Use the tenant helper instead of the route parameter

        if ($payment->tenant_id !== $tenant->id) {
            abort(403);
        }

        // Load the subscription and plan relationships
        $payment->load(['subscription.plan']);

        return view('tenant.subscription.invoice', compact(
            'tenant',
            'payment'
        ));
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice($tenant, SubscriptionPayment $payment)
    {
        $tenant = tenant(); // Use the tenant helper instead of the route parameter

        if ($payment->tenant_id !== $tenant->id) {
            abort(403);
        }

        // Load the subscription and plan relationships
        $payment->load(['subscription.plan']);

        // Generate PDF using dompdf
        $pdf = Pdf::loadView('tenant.subscription.invoice-pdf', compact('tenant', 'payment'));

        // Set PDF options for better formatting
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Arial'
        ]);

        // Generate filename
        $filename = 'Invoice-' . $payment->payment_reference . '.pdf';

        // Return PDF for download
        return $pdf->download($filename);
    }

    /**
     * Handle successful payment callback
     */
    public function paymentSuccess(Request $request)
    {
        $tenant = tenant();
        $paymentId = $request->query('payment');

        if ($paymentId) {
            $payment = SubscriptionPayment::find($paymentId);
            if ($payment && $payment->tenant_id === $tenant->id) {
                return view('tenant.subscription.payment-success', compact('tenant', 'payment'));
            }
        }

        return redirect()->route('tenant.subscription.index', tenant()->slug);
    }

    /**
     * Handle cancelled payment callback
     */
    public function paymentCancel(Request $request)
    {
        return view('tenant.subscription.payment-cancel', [
            'tenant' => tenant()
        ]);
    }

    /**
     * Handle payment gateway webhooks
     */
    public function webhook(Request $request)
    {
        // Handle payment gateway webhooks
        // Log for debugging
        Log::info('Subscription webhook received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        return response()->json(['status' => 'received']);
    }

    /**
     * Handle payment callback from payment gateways (Nomba & Paystack)
     */
    public function paymentCallback(Request $request, $tenant, SubscriptionPayment $payment)
    {
        // Extract tenant slug and payment ID for logging
        $tenantSlug = is_object($tenant) ? $tenant->slug : $tenant;
        $paymentId = is_object($payment) ? $payment->id : $payment;

        Log::info('Payment callback started', [
            'tenant_slug' => $tenantSlug,
            'payment_id' => $paymentId,
            'request_data' => $request->all()
        ]);

        try {
            // If payment is not a model instance, fetch it
            if (!($payment instanceof SubscriptionPayment)) {
                $payment = SubscriptionPayment::findOrFail($payment);
            }

            $tenant = tenant();

            Log::info('Payment callback - loaded data', [
                'payment_id' => $payment->id,
                'payment_tenant_id' => $payment->tenant_id,
                'current_tenant_id' => $tenant ? $tenant->id : null,
                'current_tenant_slug' => $tenant ? $tenant->slug : null
            ]);

            // Verify payment belongs to current tenant
            if (!$tenant || $payment->tenant_id !== $tenant->id) {
                Log::error('Payment callback - tenant mismatch', [
                    'payment_tenant_id' => $payment->tenant_id,
                    'current_tenant_id' => $tenant ? $tenant->id : null
                ]);
                abort(403, 'Unauthorized access to payment record');
            }

            // Verify payment based on payment method
            $verificationResult = $this->verifyPaymentByMethod($payment);

            Log::info('Payment verification result', [
                'payment_id' => $payment->id,
                'payment_method' => $payment->payment_method,
                'verification_result' => $verificationResult
            ]);

            if ($verificationResult['status'] && $verificationResult['payment_status'] === 'successful') {
                DB::beginTransaction();

                // Update payment record - Laravel will auto-cast gateway_response to JSON
                $payment->update([
                    'status' => 'successful',
                    'paid_at' => now(),
                    'gateway_response' => $verificationResult,
                ]);

                // Update subscription status
                $subscription = $payment->subscription;

                Log::info('Subscription details before update', [
                    'subscription_id' => $subscription->id,
                    'plan_id' => $subscription->plan_id,
                    'plan_slug' => $subscription->plan,
                    'billing_cycle' => $subscription->billing_cycle
                ]);

                $subscription->update(['status' => 'active']);

                // Load the plan relationship to ensure we have the Plan model
                $plan = Plan::findOrFail($subscription->plan_id);

                Log::info('About to process subscription update', [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'billing_cycle' => $subscription->billing_cycle,
                    'tenant_id' => $tenant->id,
                    'subscription_metadata' => $subscription->metadata
                ]);

                // Check if this is a renewal (check metadata or payment reference)
                $isRenewal = (
                    isset($subscription->metadata['renewal']) && $subscription->metadata['renewal']
                ) || str_starts_with($payment->payment_reference, 'REN_');

                if ($isRenewal) {
                    // For renewals, just update the tenant's subscription dates
                    $tenant->update([
                        'subscription_status' => 'active',
                        'subscription_starts_at' => now(),
                        'subscription_ends_at' => $subscription->ends_at->format('Y-m-d H:i:s'),
                        'billing_cycle' => $subscription->billing_cycle,
                    ]);

                    Log::info('Subscription renewed successfully', [
                        'tenant_id' => $tenant->id,
                        'plan_name' => $plan->name,
                        'billing_cycle' => $subscription->billing_cycle,
                        'new_end_date' => $subscription->ends_at
                    ]);

                    $successMessage = 'Payment successful! Your ' . $plan->name . ' subscription has been renewed.';
                } else {
                    // For upgrades/new subscriptions, use the upgradeToPaid method
                    $tenant->upgradeToPaid($plan, $subscription->billing_cycle);

                    Log::info('Subscription upgraded successfully', [
                        'tenant_id' => $tenant->id,
                        'plan_name' => $plan->name,
                        'billing_cycle' => $subscription->billing_cycle
                    ]);

                    $successMessage = 'Payment successful! You have been upgraded to ' . $plan->name . ' plan.';
                }

                // Process affiliate commission
                $this->processAffiliateCommission($tenant, $payment, $isRenewal);

                DB::commit();

                // Redirect to success page
                return redirect()->route('tenant.subscription.payment.success', [
                    'tenant' => $tenant->slug,
                    'payment' => $payment->id
                ])->with('success', $successMessage);

            } else {
                // Payment failed or pending
                DB::rollBack();

                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $verificationResult,
                ]);

                $payment->subscription->update(['status' => 'failed']);

                return redirect()->route('tenant.subscription.plans', $tenant->slug)
                    ->with('error', 'Payment was not successful. Please try again.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment callback processing failed', [
                'payment_id' => is_object($payment) ? $payment->id : $payment,
                'tenant_slug' => is_object($tenant) ? $tenant->slug : $tenant,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->route('tenant.subscription.plans', tenant()->slug)
                ->with('error', 'An error occurred while processing your payment. Please contact support.');
        }
    }

    /**
     * Show renewal form for current plan
     */
    public function renew()
    {
        $tenant = tenant();
        $currentPlan = $tenant->plan;

        if (!$currentPlan) {
            return redirect()->route('tenant.subscription.plans', tenant()->slug)
                ->with('error', 'No current plan to renew. Please choose a plan.');
        }

        return view('tenant.subscription.renew', compact(
            'tenant',
            'currentPlan'
        ));
    }

    /**
     * Process renewal for current plan
     */
    public function processRenewal(Request $request)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|in:nomba,paystack',
        ]);

        $tenant = tenant();
        $currentPlan = $tenant->plan;

        if (!$currentPlan) {
            return redirect()->route('tenant.subscription.plans', tenant()->slug)
                ->with('error', 'No current plan to renew. Please choose a plan.');
        }

        // Calculate amount based on billing cycle
        $amount = $request->billing_cycle === 'yearly' ? $currentPlan->yearly_price : $currentPlan->monthly_price;

        // Debug logging
        Log::info('ProcessRenewal started', [
            'tenant_id' => $tenant->id,
            'plan_id' => $currentPlan->id,
            'billing_cycle' => $request->billing_cycle,
            'amount' => $amount
        ]);

        try {
            DB::beginTransaction();

            // Create a pending subscription record for renewal
            $subscription = $tenant->subscriptions()->create([
                'plan_id' => $currentPlan->id,
                'plan' => $currentPlan->slug,
                'billing_cycle' => $request->billing_cycle,
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending',
                'starts_at' => now(),
                'ends_at' => $request->billing_cycle === 'yearly' ? now()->addYear() : now()->addMonth(),
                'metadata' => [
                    'renewal' => true,
                    'initiated_at' => now(),
                ]
            ]);

            // Generate unique payment reference
            $paymentReference = 'REN_' . strtoupper(Str::random(8)) . '_' . $tenant->id;

            // Create pending payment record
            $payment = $tenant->subscriptionPayments()->create([
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_reference' => $paymentReference,
                'gateway_reference' => null,
            ]);

            Log::info('Renewal payment record created', [
                'payment_id' => $payment->id,
                'payment_reference' => $paymentReference,
                'payment_method' => $request->payment_method
            ]);

            // Route to appropriate payment gateway
            $paymentMethod = $request->payment_method;

            if ($paymentMethod === 'nomba') {
                return $this->processNombaPaymentForSubscription($request, $subscription, $payment, $tenant, $currentPlan, $amount);
            } elseif ($paymentMethod === 'paystack') {
                return $this->processPaystackPaymentForSubscription($request, $subscription, $payment, $tenant, $currentPlan, $amount);
            }

            DB::rollBack();
            return back()->with('error', 'Invalid payment method selected.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Renewal process failed', [
                'tenant_id' => $tenant->id,
                'plan_id' => $currentPlan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'An error occurred while processing your renewal. Please try again.');
        }
    }

    /**
     * Process affiliate commission for successful payments
     */
    protected function processAffiliateCommission($tenant, SubscriptionPayment $payment, $isRenewal = false)
    {
        try {
            // Check if tenant was referred by an affiliate
            $referral = AffiliateReferral::where('referred_tenant_id', $tenant->id)
                ->where('status', '!=', 'cancelled')
                ->first();

            if (!$referral) {
                Log::info('No affiliate referral found for tenant', [
                    'tenant_id' => $tenant->id
                ]);
                return;
            }

            $affiliate = $referral->affiliate;

            if (!$affiliate || $affiliate->status !== 'active') {
                Log::info('Affiliate not active or not found', [
                    'tenant_id' => $tenant->id,
                    'affiliate_id' => $referral->affiliate_id
                ]);
                return;
            }

            // Get commission settings
            $recurringEnabled = config('affiliate.recurring_commission_enabled', true);
            $commissionRate = $affiliate->getCommissionRate();
            $firstPaymentBonus = config('affiliate.first_payment_bonus', 0);

            // Determine if this is the first payment
            $isFirstPayment = $referral->status === 'pending';

            // Check if we should create commission
            $shouldCreateCommission = false;
            $commissionType = 'recurring';
            $bonusRate = 0;

            if ($isFirstPayment) {
                // Always create commission for first payment
                $shouldCreateCommission = true;
                $commissionType = 'first_payment';
                $bonusRate = $firstPaymentBonus;

                // Update referral to confirmed
                $referral->update([
                    'status' => 'confirmed',
                    'conversion_type' => 'first_payment',
                    'conversion_value' => $payment->amount,
                    'conversion_date' => now(),
                ]);

                Log::info('First payment commission triggered', [
                    'tenant_id' => $tenant->id,
                    'affiliate_id' => $affiliate->id,
                    'payment_amount' => $payment->amount
                ]);
            } elseif ($recurringEnabled && $isRenewal) {
                // Create commission for recurring payments if enabled
                $shouldCreateCommission = true;
                $commissionType = 'recurring';

                Log::info('Recurring payment commission triggered', [
                    'tenant_id' => $tenant->id,
                    'affiliate_id' => $affiliate->id,
                    'payment_amount' => $payment->amount
                ]);
            }

            if ($shouldCreateCommission) {
                // Calculate commission amount
                $totalRate = $commissionRate + $bonusRate;
                $commissionAmount = ($payment->amount / 100) * $totalRate; // Convert from kobo to naira and apply rate

                // Calculate due date (hold period)
                $holdDays = config('affiliate.commission_hold_days', 30);
                $dueDate = now()->addDays($holdDays);

                // Create commission record
                $commission = AffiliateCommission::create([
                    'affiliate_id' => $affiliate->id,
                    'referred_tenant_id' => $tenant->id,
                    'affiliate_referral_id' => $referral->id,
                    'payment_reference' => $payment->payment_reference,
                    'payment_amount' => $payment->amount / 100, // Convert to naira
                    'commission_rate' => $totalRate,
                    'commission_amount' => $commissionAmount,
                    'commission_type' => $commissionType,
                    'status' => 'pending', // Requires approval
                    'description' => $isFirstPayment
                        ? "First payment commission from {$tenant->name}"
                        : "Recurring payment commission from {$tenant->name}",
                    'payment_date' => now(),
                    'due_date' => $dueDate,
                ]);

                // Update affiliate total commissions
                $affiliate->increment('total_commissions', $commissionAmount);

                Log::info('Affiliate commission created', [
                    'commission_id' => $commission->id,
                    'affiliate_id' => $affiliate->id,
                    'tenant_id' => $tenant->id,
                    'commission_amount' => $commissionAmount,
                    'commission_type' => $commissionType,
                    'payment_reference' => $payment->payment_reference,
                    'due_date' => $dueDate
                ]);

                // TODO: Send notification to affiliate about new commission
            } else {
                Log::info('Commission not created - conditions not met', [
                    'tenant_id' => $tenant->id,
                    'affiliate_id' => $affiliate->id,
                    'is_first_payment' => $isFirstPayment,
                    'is_renewal' => $isRenewal,
                    'recurring_enabled' => $recurringEnabled
                ]);
            }

        } catch (\Exception $e) {
            // Don't fail the payment if commission processing fails
            Log::error('Affiliate commission processing failed', [
                'tenant_id' => $tenant->id,
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
