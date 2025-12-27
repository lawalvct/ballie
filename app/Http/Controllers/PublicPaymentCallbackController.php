<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\VoucherEntry;
use App\Models\LedgerAccount;
use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\PaymentHelper;
use App\Helpers\PaystackPaymentHelper;

class PublicPaymentCallbackController extends Controller
{
    /**
     * Public payment callback for handling payment gateway redirects
     * This endpoint is NOT protected by auth - customers use payment links
     *
     * When customer pays:
     * - Nomba redirects with: ?orderReference=XXX
     * - Paystack redirects with: ?reference=XXX&trxref=XXX
     */
    public function handleCallback(Request $request, $invoice)
    {
        Log::info('=== PUBLIC PAYMENT CALLBACK RECEIVED ===', [
            'invoice_id' => $invoice,
            'query_params' => $request->all(),
            'full_url' => $request->fullUrl()
        ]);

        try {
            // Get invoice object - this will get tenant automatically
            $invoice = Voucher::findOrFail($invoice);

            // Load tenant relationship
            $tenant = $invoice->tenant;

            Log::info('Resolved invoice and tenant', [
                'invoice_id' => $invoice->id,
                'tenant_id' => $tenant->id,
                'tenant_slug' => $tenant->slug,
                'invoice_status' => $invoice->status,
                'payment_links' => $invoice->meta_data['payment_links'] ?? null
            ]);

            // Check if invoice is posted
            if ($invoice->status !== 'posted') {
                Log::warning('Invoice is not posted', [
                    'invoice_id' => $invoice->id,
                    'status' => $invoice->status
                ]);

                // Return simple HTML success page for customer
                return $this->showCustomerSuccessPage(
                    $tenant,
                    $invoice,
                    'This invoice is not available for payment.',
                    'error'
                );
            }

            // Get payment links from meta_data
            $paymentLinks = $invoice->meta_data['payment_links'] ?? [];

            if (empty($paymentLinks)) {
                Log::warning('No payment links found', [
                    'invoice_id' => $invoice->id,
                    'meta_data' => $invoice->meta_data
                ]);

                return $this->showCustomerSuccessPage(
                    $tenant,
                    $invoice,
                    'No payment information found for this invoice.',
                    'error'
                );
            }

            // Determine which gateway was used and verify payment
            $paymentVerified = false;
            $paymentAmount = 0;
            $paymentReference = null;
            $gatewayName = null;

            // Try Nomba first (check if orderReference in query params)
            if (isset($paymentLinks['nomba']) && $request->has('orderReference')) {
                try {
                    $nombaHelper = new PaymentHelper();
                    $nombaReference = $paymentLinks['nomba']['reference'];

                    Log::info('Attempting Nomba verification', [
                        'stored_reference' => $nombaReference,
                        'query_reference' => $request->get('orderReference')
                    ]);

                    $verificationResult = $nombaHelper->verifyPayment($nombaReference);

                    Log::info('Nomba payment verification result', [
                        'reference' => $nombaReference,
                        'result' => $verificationResult
                    ]);

                    if ($verificationResult['status'] && $verificationResult['payment_status'] === 'successful') {
                        $paymentVerified = true;
                        $paymentAmount = $invoice->total_amount;
                        $paymentReference = $nombaReference;
                        $gatewayName = 'Nomba';
                    }
                } catch (\Exception $e) {
                    Log::warning('Nomba verification failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // Try Paystack if Nomba didn't verify (check if reference in query params)
            if (!$paymentVerified && isset($paymentLinks['paystack']) && ($request->has('reference') || $request->has('trxref'))) {
                try {
                    $paystackHelper = new PaystackPaymentHelper();
                    $paystackReference = $paymentLinks['paystack']['reference'];

                    Log::info('Attempting Paystack verification', [
                        'stored_reference' => $paystackReference,
                        'query_reference' => $request->get('reference'),
                        'query_trxref' => $request->get('trxref')
                    ]);

                    $verificationResult = $paystackHelper->verifyTransaction($paystackReference);

                    Log::info('Paystack payment verification result', [
                        'reference' => $paystackReference,
                        'result' => $verificationResult
                    ]);

                    if ($verificationResult['status'] && $verificationResult['payment_status'] === 'successful') {
                        $paymentVerified = true;
                        $paymentAmount = $verificationResult['amount'];
                        $paymentReference = $paystackReference;
                        $gatewayName = 'Paystack';
                    }
                } catch (\Exception $e) {
                    Log::warning('Paystack verification failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            if (!$paymentVerified) {
                return $this->showCustomerSuccessPage(
                    $tenant,
                    $invoice,
                    'Payment verification failed. Please contact support if payment was deducted.',
                    'error'
                );
            }

            // Check if this payment has already been recorded
            $existingPayment = Voucher::where('tenant_id', $tenant->id)
                ->where('reference_number', 'LIKE', "%{$paymentReference}%")
                ->first();

            if ($existingPayment) {
                Log::info('Payment already recorded', [
                    'existing_voucher_id' => $existingPayment->id
                ]);

                return $this->showCustomerSuccessPage(
                    $tenant,
                    $invoice,
                    'Your payment has already been recorded. Thank you!',
                    'info'
                );
            }

            // Record the payment
            DB::beginTransaction();

            // Get receipt voucher type
            $receiptVoucherType = VoucherType::where('tenant_id', $tenant->id)
                ->where('code', 'RV')
                ->first();

            if (!$receiptVoucherType) {
                throw new \Exception('Receipt voucher type not found');
            }

            // Get customer account from the original invoice
            $customerAccount = $invoice->entries->where('debit_amount', '>', 0)->first()?->ledgerAccount;

            if (!$customerAccount) {
                throw new \Exception('Customer account not found in invoice');
            }

            // Get default bank account
            $bankAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->whereHas('accountGroup', function($q) {
                    $q->where('code', 'BA');
                })
                ->first();

            if (!$bankAccount) {
                // Fallback: get any cash/bank account
                $bankAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where(function($q) {
                        $q->where('name', 'LIKE', '%Cash%')
                          ->orWhere('name', 'LIKE', '%Bank%');
                    })
                    ->first();
            }

            if (!$bankAccount) {
                throw new \Exception('No bank account found. Please contact support.');
            }

            // Generate voucher number for receipt
            $lastReceipt = Voucher::where('tenant_id', $tenant->id)
                ->where('voucher_type_id', $receiptVoucherType->id)
                ->orderBy('voucher_number', 'desc')
                ->first();

            $nextNumber = $lastReceipt ? $lastReceipt->voucher_number + 1 : 1;

            // Create receipt voucher
            $receiptVoucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $receiptVoucherType->id,
                'voucher_number' => $nextNumber,
                'voucher_date' => now()->format('Y-m-d'),
                'reference_number' => "Online Payment - {$gatewayName} - {$paymentReference}",
                'narration' => "Payment received via {$gatewayName} for invoice " . $invoice->voucherType->prefix . $invoice->voucher_number,
                'total_amount' => $paymentAmount,
                'status' => 'posted',
                'created_by' => 1, // System user
                'posted_at' => now(),
                'posted_by' => 1,
            ]);

            // Create accounting entries for receipt
            // Debit: Bank/Cash Account
            VoucherEntry::create([
                'voucher_id' => $receiptVoucher->id,
                'ledger_account_id' => $bankAccount->id,
                'debit_amount' => $paymentAmount,
                'credit_amount' => 0,
                'particulars' => "Payment received via {$gatewayName} - Ref: {$paymentReference}",
            ]);

            // Credit: Customer Account (reducing their outstanding balance)
            VoucherEntry::create([
                'voucher_id' => $receiptVoucher->id,
                'ledger_account_id' => $customerAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $paymentAmount,
                'particulars' => "Payment for invoice " . $invoice->voucherType->prefix . $invoice->voucher_number,
            ]);

            // Update ledger account balances
            $bankAccount->fresh()->updateCurrentBalance();
            $customerAccount->fresh()->updateCurrentBalance();

            // Update customer outstanding balance if linked
            $customer = Customer::where('ledger_account_id', $customerAccount->id)->first();
            if ($customer) {
                $customerBalance = $customerAccount->fresh()->current_balance;
                $customer->outstanding_balance = max(0, $customerBalance);
                $customer->save();
            }

            DB::commit();

            Log::info('=== PAYMENT RECORDED SUCCESSFULLY ===', [
                'invoice_id' => $invoice->id,
                'receipt_voucher_id' => $receiptVoucher->id,
                'amount' => $paymentAmount,
                'gateway' => $gatewayName,
                'reference' => $paymentReference
            ]);

            // Show customer success page
            return $this->showCustomerSuccessPage(
                $tenant,
                $invoice,
                "Payment of ₦" . number_format($paymentAmount, 2) . " received successfully via {$gatewayName}!",
                'success',
                $paymentAmount,
                $gatewayName,
                $paymentReference
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== PUBLIC PAYMENT CALLBACK FAILED ===', [
                'invoice_id' => $invoice ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->showCustomerSuccessPage(
                $tenant ?? null,
                $invoice ?? null,
                'Failed to process payment: ' . $e->getMessage(),
                'error'
            );
        }
    }

    /**
     * Show a simple HTML success/error page for customers
     */
    private function showCustomerSuccessPage($tenant, $invoice, $message, $type = 'success', $amount = null, $gateway = null, $reference = null)
    {
        $statusColor = match($type) {
            'success' => '#10b981',
            'error' => '#ef4444',
            'info' => '#3b82f6',
            default => '#6b7280'
        };

        $statusIcon = match($type) {
            'success' => '✓',
            'error' => '✗',
            'info' => 'ℹ',
            default => '•'
        };

        $tenantName = $tenant ? $tenant->name : 'Business';
        $invoiceNumber = $invoice ? ($invoice->voucherType->prefix . $invoice->voucher_number) : 'N/A';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment {$type}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 500px; width: 100%; padding: 40px; text-align: center; }
        .icon { width: 80px; height: 80px; margin: 0 auto 20px; background: {$statusColor}; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px; color: white; font-weight: bold; }
        h1 { color: #1f2937; font-size: 28px; margin-bottom: 16px; }
        .message { color: #6b7280; font-size: 16px; line-height: 1.6; margin-bottom: 24px; }
        .details { background: #f9fafb; border-radius: 8px; padding: 20px; margin-bottom: 24px; text-align: left; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-row:last-child { border-bottom: none; }
        .label { color: #6b7280; font-size: 14px; }
        .value { color: #1f2937; font-size: 14px; font-weight: 600; }
        .button { display: inline-block; background: {$statusColor}; color: white; padding: 12px 32px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 16px; transition: transform 0.2s; }
        .button:hover { transform: translateY(-2px); }
        .footer { margin-top: 24px; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">{$statusIcon}</div>
        <h1>Payment {$type}</h1>
        <p class="message">{$message}</p>

        <div class="details">
            <div class="detail-row">
                <span class="label">Business:</span>
                <span class="value">{$tenantName}</span>
            </div>
            <div class="detail-row">
                <span class="label">Invoice:</span>
                <span class="value">{$invoiceNumber}</span>
            </div>
HTML;

        if ($amount) {
            $html .= <<<HTML
            <div class="detail-row">
                <span class="label">Amount:</span>
                <span class="value">₦{$amount}</span>
            </div>
HTML;
        }

        if ($gateway) {
            $html .= <<<HTML
            <div class="detail-row">
                <span class="label">Gateway:</span>
                <span class="value">{$gateway}</span>
            </div>
HTML;
        }

        if ($reference) {
            $html .= <<<HTML
            <div class="detail-row">
                <span class="label">Reference:</span>
                <span class="value">{$reference}</span>
            </div>
HTML;
        }

        $html .= <<<HTML
            <div class="detail-row">
                <span class="label">Date:</span>
                <span class="value">{$this->formatDate(now())}</span>
            </div>
        </div>

        <p class="footer">Thank you for your payment! You may close this window.</p>
    </div>
</body>
</html>
HTML;

        return response($html)->header('Content-Type', 'text/html');
    }

    private function formatDate($date)
    {
        return $date->format('F j, Y g:i A');
    }
}
