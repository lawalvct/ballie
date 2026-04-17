<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\SubscriptionPayment;
use App\Helpers\PaymentHelper;
use App\Helpers\PaystackPaymentHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReconfirmPendingPayments extends Command
{
    protected $signature = 'subscriptions:reconfirm-pending';

    protected $description = 'Re-verify pending/failed subscription payments with payment gateways';

    public function handle()
    {
        $this->info('Checking for pending/failed subscription payments...');

        // Get pending or failed payments from the last 48 hours
        $payments = SubscriptionPayment::whereIn('status', ['pending', 'failed'])
            ->where('created_at', '>=', now()->subHours(48))
            ->with(['subscription', 'tenant'])
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No pending payments found.');
            return 0;
        }

        $this->info("Found {$payments->count()} payment(s) to verify.");

        $confirmed = 0;
        $stillPending = 0;
        $errors = 0;

        foreach ($payments as $payment) {
            try {
                $this->line("  Verifying payment #{$payment->id} ({$payment->payment_method}) - Ref: {$payment->payment_reference}");

                $verificationResult = $this->verifyPayment($payment);

                if ($verificationResult['status'] && $verificationResult['payment_status'] === 'successful') {
                    DB::beginTransaction();

                    $payment->update([
                        'status' => 'successful',
                        'paid_at' => now(),
                        'gateway_response' => $verificationResult,
                    ]);

                    $subscription = $payment->subscription;
                    $subscription->update(['status' => 'active']);

                    $plan = Plan::findOrFail($subscription->plan_id);
                    $tenant = $payment->tenant;

                    $isRenewal = (
                        isset($subscription->metadata['renewal']) && $subscription->metadata['renewal']
                    ) || str_starts_with($payment->payment_reference, 'REN_');

                    if ($isRenewal) {
                        $tenant->update([
                            'subscription_status' => 'active',
                            'subscription_starts_at' => now(),
                            'subscription_ends_at' => $subscription->ends_at->format('Y-m-d H:i:s'),
                            'billing_cycle' => $subscription->billing_cycle,
                        ]);
                    } else {
                        $tenant->upgradeToPaid($plan, $subscription->billing_cycle);
                    }

                    DB::commit();

                    $confirmed++;
                    $this->info("    ✅ Payment confirmed - {$tenant->name} activated on {$plan->name} plan");

                    Log::info('Cron: Payment auto-confirmed', [
                        'payment_id' => $payment->id,
                        'tenant_id' => $tenant->id,
                        'plan' => $plan->name,
                    ]);
                } else {
                    $stillPending++;
                    $this->warn("    ⏳ Still not confirmed (gateway status: " . ($verificationResult['payment_status'] ?? 'unknown') . ")");
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $errors++;
                $this->error("    ❌ Error: {$e->getMessage()}");

                Log::error('Cron: Payment reconfirm failed', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info("Results: {$confirmed} confirmed, {$stillPending} still pending, {$errors} errors");

        return 0;
    }

    private function verifyPayment(SubscriptionPayment $payment): array
    {
        $reference = $payment->gateway_reference ?? $payment->payment_reference;

        if ($payment->payment_method === 'nomba') {
            return (new PaymentHelper())->verifyPayment($reference);
        } elseif ($payment->payment_method === 'paystack') {
            return (new PaystackPaymentHelper())->verifyTransaction($reference);
        }

        return ['status' => false, 'payment_status' => 'unknown', 'message' => 'Unsupported payment method'];
    }
}
