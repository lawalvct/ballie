<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Display current subscription status and overview
     */
    public function index()
    {
        $tenant = tenant();
        $currentSubscription = $tenant->subscription()->latest()->first();
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        // Get recent payment history
        $recentPayments = SubscriptionPayment::where('tenant_id', $tenant->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('tenant.subscription.index', compact(
            'tenant',
            'currentSubscription',
            'plans',
            'recentPayments'
        ));
    }

    /**
     * Display available plans
     */
    public function plans()
    {
        $tenant = tenant();
        $currentSubscription = $tenant->subscription()->latest()->first();
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('tenant.subscription.plans', compact(
            'tenant',
            'currentSubscription',
            'plans'
        ));
    }

    /**
     * Show upgrade form for a specific plan
     */
    public function upgrade(Plan $plan)
    {
        $tenant = tenant();
        $currentSubscription = $tenant->subscription()->latest()->first();

        // Check if upgrade is valid
        if ($currentSubscription && $currentSubscription->plan === $plan->slug) {
            return redirect()->route('tenant.subscription.index', tenant()->slug)
                ->with('error', 'You are already on this plan.');
        }

        return view('tenant.subscription.upgrade', compact(
            'tenant',
            'plan',
            'currentSubscription'
        ));
    }

    /**
     * Process upgrade to a new plan
     */
    public function processUpgrade(Request $request, Plan $plan)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|string',
        ]);

        $tenant = tenant();
        $billingCycle = $request->billing_cycle;

        // Calculate amount based on billing cycle
        $amount = $billingCycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price;

        try {
            DB::beginTransaction();

            // Create new subscription
            $subscription = $tenant->subscriptions()->create([
                'plan' => $plan->slug,
                'billing_cycle' => $billingCycle,
                'amount' => $amount,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth(),
                'metadata' => [
                    'plan_name' => $plan->name,
                    'features' => $plan->features,
                    'upgraded_at' => now(),
                    'previous_plan' => $tenant->subscription()->latest()->first()?->plan,
                ]
            ]);

            // Create payment record
            $payment = $subscription->payments()->create([
                'tenant_id' => $tenant->id,
                'amount' => $amount,
                'status' => 'successful', // In real implementation, integrate with payment gateway
                'payment_method' => $request->payment_method,
                'payment_reference' => 'PAY_' . strtoupper(uniqid()),
                'paid_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('tenant.subscription.payment.success', [
                'tenant' => tenant()->slug,
                'payment' => $payment->id
            ])->with('success', 'Successfully upgraded to ' . $plan->name . ' plan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription upgrade failed', [
                'tenant_id' => $tenant->id,
                'plan' => $plan->slug,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to process upgrade. Please try again.');
        }
    }

    /**
     * Show downgrade form
     */
    public function downgrade(Plan $plan)
    {
        $tenant = tenant();
        $currentSubscription = $tenant->subscription()->latest()->first();

        return view('tenant.subscription.downgrade', compact(
            'tenant',
            'plan',
            'currentSubscription'
        ));
    }

    /**
     * Process downgrade to a lower plan
     */
    public function processDowngrade(Request $request, Plan $plan)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
            'reason' => 'nullable|string|max:500',
        ]);

        $tenant = tenant();
        $currentSubscription = $tenant->subscription()->latest()->first();

        // Schedule downgrade for next billing cycle
        $currentSubscription->update([
            'metadata' => array_merge($currentSubscription->metadata ?? [], [
                'scheduled_downgrade' => [
                    'plan' => $plan->slug,
                    'billing_cycle' => $request->billing_cycle,
                    'effective_date' => $currentSubscription->ends_at,
                    'reason' => $request->reason,
                    'scheduled_at' => now(),
                ]
            ])
        ]);

        return redirect()->route('tenant.subscription.index', tenant()->slug)
            ->with('success', 'Downgrade scheduled for ' . $currentSubscription->ends_at->format('M j, Y'));
    }

    /**
     * Show cancellation form
     */
    public function cancel()
    {
        $tenant = tenant();
        $currentSubscription = $tenant->subscription()->latest()->first();

        return view('tenant.subscription.cancel', compact(
            'tenant',
            'currentSubscription'
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
        $currentSubscription = $tenant->subscription()->latest()->first();

        if ($currentSubscription) {
            $currentSubscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'metadata' => array_merge($currentSubscription->metadata ?? [], [
                    'cancellation' => [
                        'reason' => $request->reason,
                        'feedback' => $request->feedback,
                        'cancelled_at' => now(),
                    ]
                ])
            ]);
        }

        return redirect()->route('tenant.subscription.index', tenant()->slug)
            ->with('success', 'Your subscription has been cancelled. Access will continue until ' .
                   $currentSubscription->ends_at->format('M j, Y'));
    }

    /**
     * Display subscription history
     */
    public function history()
    {
        $tenant = tenant();
        $subscriptions = $tenant->subscriptions()->latest()->paginate(10);
        $payments = SubscriptionPayment::where('tenant_id', $tenant->id)
            ->latest()
            ->paginate(15);

        return view('tenant.subscription.history', compact(
            'tenant',
            'subscriptions',
            'payments'
        ));
    }

    /**
     * Display invoice for a payment
     */
    public function invoice(SubscriptionPayment $payment)
    {
        $tenant = tenant();

        if ($payment->tenant_id !== $tenant->id) {
            abort(403);
        }

        return view('tenant.subscription.invoice', compact(
            'tenant',
            'payment'
        ));
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(SubscriptionPayment $payment)
    {
        $tenant = tenant();

        if ($payment->tenant_id !== $tenant->id) {
            abort(403);
        }

        // In real implementation, generate PDF
        // For now, redirect to invoice view
        return redirect()->route('tenant.subscription.invoice', [
            'tenant' => tenant()->slug,
            'payment' => $payment->id
        ]);
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
}
