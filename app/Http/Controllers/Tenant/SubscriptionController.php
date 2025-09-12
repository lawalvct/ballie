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
    public function upgrade(Plan $plan)
    {
        $tenant = tenant();
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
    public function processUpgrade(Request $request, Plan $plan)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|string',
        ]);

        $tenant = tenant();

        try {
            DB::beginTransaction();

            // Use the tenant method to upgrade
            $subscription = $tenant->upgradeToPaid($plan, $request->billing_cycle);

            // Create payment record
            $payment = $tenant->subscriptionPayments()->create([
                'subscription_id' => $subscription->id,
                'amount' => $subscription->amount,
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
                'plan_id' => $plan->id,
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
    public function processDowngrade(Request $request, Plan $plan)
    {
        $request->validate([
            'billing_cycle' => 'required|in:monthly,yearly',
            'reason' => 'nullable|string|max:500',
        ]);

        $tenant = tenant();
        $currentPlan = $tenant->plan;

        try {
            DB::beginTransaction();

            // Create a subscription record for the downgrade
            $subscription = $tenant->subscriptions()->create([
                'plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'amount' => $request->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->monthly_price,
                'status' => 'scheduled',
                'starts_at' => $tenant->trial_ends_at ?? now()->addMonth(), // Start after current period
                'ends_at' => $request->billing_cycle === 'yearly' ?
                    ($tenant->trial_ends_at ?? now()->addMonth())->addYear() :
                    ($tenant->trial_ends_at ?? now()->addMonth())->addMonth(),
                'metadata' => [
                    'downgrade_reason' => $request->reason,
                    'previous_plan_id' => $currentPlan->id,
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

        try {
            DB::beginTransaction();

            // Create a cancellation record in subscriptions
            $subscription = $tenant->subscriptions()->create([
                'plan_id' => $tenant->plan_id,
                'billing_cycle' => 'cancelled',
                'amount' => 0,
                'status' => 'cancelled',
                'starts_at' => now(),
                'ends_at' => $tenant->trial_ends_at ?? now(),
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
