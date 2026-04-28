<?php

namespace App\Services\MobileSync;

use App\Models\CashRegisterSession;
use App\Models\Customer;
use App\Models\LedgerAccount;
use App\Models\MobileDevice;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Phase 5: applies a `sales` push mutation from a mobile POS device.
 *
 * Mirrors the canonical POS posting flow used by
 * `Api\Tenant\Pos\PosApiController::store`:
 *   1. Resolve customer (optional) + products + payment methods + cash
 *      register session by sync_uuid.
 *   2. Validate the cash register session is OPEN and belongs to the
 *      pushing user (per architecture plan: sessions are opened online
 *      only; offline sales attach to an already-open session).
 *   3. Server-authoritative stock validation per line (fresh stock).
 *   4. Generate official sale_number, create Sale + SaleItem + SalePayment.
 *   5. Write StockMovement rows for products that maintain_stock.
 *   6. Generate Receipt and the RV accounting voucher (debit Cash,
 *      credit Sales/Tax) — same logic as the web/mobile controller.
 *
 * Idempotency is handled by PushService via the `mobile_mutations`
 * table — this service is only invoked when no Sale with the given
 * sync_uuid exists yet.
 */
class PosSaleSyncService
{
    /**
     * Apply a single POS sale create mutation.
     *
     * @param  array<string, mixed>  $payload  Decoded `data` from the
     *                                         mutation envelope.
     * @return array{ok: bool, server_response?: array<string, mixed>,
     *               error_code?: string, error_message?: string,
     *               errors?: array<string, mixed>}
     */
    public function applyCreate(
        Tenant $tenant,
        User $user,
        MobileDevice $device,
        string $syncUuid,
        array $payload,
    ): array {
        $validator = Validator::make($payload, [
            'sale_date'                          => 'nullable|date',
            'customer_sync_uuid'                 => 'nullable|uuid',
            'cash_register_session_sync_uuid'    => 'required|uuid',
            'notes'                              => 'nullable|string|max:1000',
            'items'                              => 'required|array|min:1',
            'items.*.product_sync_uuid'          => 'sometimes|nullable|uuid',
            'items.*.product_id'                 => 'sometimes|nullable|integer',
            'items.*.quantity'                   => 'required|numeric|min:0.01',
            'items.*.unit_price'                 => 'required|numeric|min:0',
            'items.*.discount_amount'            => 'nullable|numeric|min:0',
            'payments'                           => 'required|array|min:1',
            'payments.*.payment_method_sync_uuid' => 'sometimes|nullable|uuid',
            'payments.*.payment_method_id'       => 'sometimes|nullable|integer',
            'payments.*.amount'                  => 'required|numeric|min:0.01',
            'payments.*.reference'               => 'nullable|string|max:255',
            'client_sale_number'                 => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return [
                'ok'            => false,
                'error_code'    => 'validation_failed',
                'error_message' => 'Validation failed',
                'errors'        => $validator->errors()->toArray(),
            ];
        }

        // ── Resolve cash register session ───────────────────────────────
        $session = CashRegisterSession::where('tenant_id', $tenant->id)
            ->where('sync_uuid', $payload['cash_register_session_sync_uuid'])
            ->first();
        if (!$session) {
            return [
                'ok'            => false,
                'error_code'    => 'session_not_found',
                'error_message' => 'Cash register session not found for sync_uuid',
            ];
        }
        if ($session->user_id !== $user->id) {
            return [
                'ok'            => false,
                'error_code'    => 'session_user_mismatch',
                'error_message' => 'Cash register session belongs to another user',
            ];
        }
        if ($session->closed_at !== null) {
            return [
                'ok'            => false,
                'error_code'    => 'session_closed',
                'error_message' => 'Cash register session is already closed; reopen a session online before pushing offline sales',
            ];
        }

        // ── Resolve optional customer ───────────────────────────────────
        $customer = null;
        if (!empty($payload['customer_sync_uuid'])) {
            $customer = Customer::where('tenant_id', $tenant->id)
                ->where('sync_uuid', $payload['customer_sync_uuid'])
                ->first();
            if (!$customer) {
                return [
                    'ok'            => false,
                    'error_code'    => 'customer_not_found',
                    'error_message' => 'Customer not found for sync_uuid',
                ];
            }
        }

        // ── Resolve products + payment methods ─────────────────────────
        $resolvedItems = [];
        foreach ($payload['items'] as $idx => $item) {
            $product = $this->resolveProduct($tenant, $item);
            if (!$product) {
                return [
                    'ok'            => false,
                    'error_code'    => 'product_not_found',
                    'error_message' => "Item #{$idx}: product not found",
                ];
            }
            $resolvedItems[] = ['raw' => $item, 'product' => $product];
        }

        $resolvedPayments = [];
        foreach ($payload['payments'] as $idx => $payment) {
            $method = $this->resolvePaymentMethod($tenant, $payment);
            if (!$method) {
                return [
                    'ok'            => false,
                    'error_code'    => 'payment_method_not_found',
                    'error_message' => "Payment #{$idx}: payment_method not found",
                ];
            }
            $resolvedPayments[] = ['raw' => $payment, 'method' => $method];
        }

        try {
            return DB::transaction(function () use (
                $tenant, $user, $device, $syncUuid, $payload,
                $session, $customer, $resolvedItems, $resolvedPayments,
            ) {
                // ── Server-side stock validation (fresh, per item) ──
                foreach ($resolvedItems as $idx => $resolved) {
                    /** @var Product $product */
                    $product = Product::where('id', $resolved['product']->id)
                        ->where('tenant_id', $tenant->id)
                        ->lockForUpdate()
                        ->firstOrFail();
                    $resolvedItems[$idx]['product'] = $product;

                    if ($product->maintain_stock) {
                        $freshStock = $product->getStockAsOfDate(now(), true);
                        if ($freshStock < $resolved['raw']['quantity']) {
                            // Bubble out as a domain error so PushService
                            // can persist a `failed` mutation that the
                            // mobile inbox can surface for manager review.
                            throw new \RuntimeException(
                                "insufficient_stock:{$product->id}:" . number_format($freshStock, 2),
                            );
                        }
                    }
                }

                $paidAmount = collect($resolvedPayments)->sum(fn ($p) => (float) $p['raw']['amount']);

                $sale = Sale::create([
                    'sync_uuid'                  => $syncUuid,
                    'tenant_id'                  => $tenant->id,
                    'sale_number'                => Sale::generateSaleNumber($tenant),
                    'customer_id'                => $customer?->id,
                    'user_id'                    => $user->id,
                    'cash_register_id'           => $session->cash_register_id,
                    'cash_register_session_id'   => $session->id,
                    'subtotal'                   => 0,
                    'tax_amount'                 => 0,
                    'discount_amount'            => 0,
                    'total_amount'               => 0,
                    'paid_amount'                => $paidAmount,
                    'change_amount'              => 0,
                    'status'                     => 'completed',
                    'sale_date'                  => $payload['sale_date'] ?? now(),
                    'notes'                      => $payload['notes'] ?? null,
                    'created_by'                 => $user->id,
                    'last_modified_by_device_id' => $device->device_uuid,
                ]);

                $subtotal = 0;
                $taxAmount = 0;
                $discountAmount = 0;

                foreach ($resolvedItems as $resolved) {
                    /** @var Product $product */
                    $product = $resolved['product'];
                    $item = $resolved['raw'];

                    $itemSubtotal = $item['quantity'] * $item['unit_price'];
                    $itemDiscount = (float) ($item['discount_amount'] ?? 0);
                    $itemTax = ($itemSubtotal - $itemDiscount) * ($product->tax_rate ?? 0) / 100;
                    $lineTotal = $itemSubtotal - $itemDiscount + $itemTax;

                    SaleItem::create([
                        'tenant_id'                  => $tenant->id,
                        'sale_id'                    => $sale->id,
                        'product_id'                 => $product->id,
                        'product_name'               => $product->name,
                        'product_sku'                => $product->sku,
                        'quantity'                   => $item['quantity'],
                        'unit_price'                 => $item['unit_price'],
                        'discount_amount'            => $itemDiscount,
                        'tax_amount'                 => $itemTax,
                        'line_total'                 => $lineTotal,
                        'last_modified_by_device_id' => $device->device_uuid,
                    ]);

                    if ($product->maintain_stock) {
                        $this->createStockMovement($product, $item['quantity'], $sale, $user);
                    }

                    $subtotal += $itemSubtotal;
                    $taxAmount += $itemTax;
                    $discountAmount += $itemDiscount;
                }

                foreach ($resolvedPayments as $resolved) {
                    SalePayment::create([
                        'tenant_id'                  => $tenant->id,
                        'sale_id'                    => $sale->id,
                        'payment_method_id'          => $resolved['method']->id,
                        'amount'                     => $resolved['raw']['amount'],
                        'reference_number'           => $resolved['raw']['reference'] ?? null,
                        'last_modified_by_device_id' => $device->device_uuid,
                    ]);
                }

                $totalAmount = $subtotal - $discountAmount + $taxAmount;
                $changeAmount = max(0, $paidAmount - $totalAmount);

                $sale->update([
                    'subtotal'        => $subtotal,
                    'tax_amount'      => $taxAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount'    => $totalAmount,
                    'change_amount'   => $changeAmount,
                ]);

                $this->generateReceipt($sale);
                $this->createAccountingEntries($tenant, $sale, $user);

                $sale->refresh();

                return [
                    'ok'              => true,
                    'server_response' => [
                        'server_id'            => $sale->id,
                        'server_version'       => (int) ($sale->server_version ?? 1),
                        'sync_uuid'            => $sale->sync_uuid,
                        'official_sale_number' => $sale->sale_number,
                        'status'               => $sale->status,
                        'subtotal'             => (float) $sale->subtotal,
                        'tax_amount'           => (float) $sale->tax_amount,
                        'discount_amount'      => (float) $sale->discount_amount,
                        'total_amount'         => (float) $sale->total_amount,
                        'paid_amount'          => (float) $sale->paid_amount,
                        'change_amount'        => (float) $sale->change_amount,
                        'updated_at'           => optional($sale->updated_at)->toIso8601String(),
                    ],
                ];
            });
        } catch (\RuntimeException $e) {
            // Stock-validation failure surfaces as a structured error so
            // the mobile sync inbox can show a `manager review` prompt.
            if (str_starts_with($e->getMessage(), 'insufficient_stock:')) {
                [, $productId, $available] = explode(':', $e->getMessage(), 3);
                return [
                    'ok'            => false,
                    'error_code'    => 'insufficient_stock',
                    'error_message' => "Insufficient stock for product #{$productId}. Available: {$available}",
                    'errors'        => [
                        'product_id' => (int) $productId,
                        'available'  => (float) $available,
                    ],
                ];
            }
            report($e);
            return [
                'ok'            => false,
                'error_code'    => 'server_error',
                'error_message' => $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            report($e);
            return [
                'ok'            => false,
                'error_code'    => 'server_error',
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function resolveProduct(Tenant $tenant, array $item): ?Product
    {
        $query = Product::where('tenant_id', $tenant->id);

        if (!empty($item['product_sync_uuid'])) {
            return $query->where('sync_uuid', $item['product_sync_uuid'])->first();
        }
        if (!empty($item['product_id'])) {
            return $query->where('id', $item['product_id'])->first();
        }
        return null;
    }

    private function resolvePaymentMethod(Tenant $tenant, array $payment): ?PaymentMethod
    {
        $query = PaymentMethod::where('tenant_id', $tenant->id);

        if (!empty($payment['payment_method_sync_uuid'])) {
            return $query->where('sync_uuid', $payment['payment_method_sync_uuid'])->first();
        }
        if (!empty($payment['payment_method_id'])) {
            return $query->where('id', $payment['payment_method_id'])->first();
        }
        return null;
    }

    /**
     * Mirror PosApiController::createStockMovement.
     */
    private function createStockMovement(Product $product, $quantity, Sale $sale, User $user): void
    {
        if (!class_exists(StockMovement::class)) {
            return;
        }

        $oldStock = $product->getStockAsOfDate(now(), true);
        $movementQuantity = -abs($quantity);
        $newStock = $oldStock + $movementQuantity;

        StockMovement::create([
            'tenant_id'                => $product->tenant_id,
            'product_id'               => $product->id,
            'type'                     => 'out',
            'quantity'                 => $movementQuantity,
            'old_stock'                => $oldStock,
            'new_stock'                => $newStock,
            'rate'                     => $product->purchase_rate ?? $product->sales_rate ?? 0,
            'reference'                => 'POS Sale - ' . $sale->sale_number,
            'remarks'                  => 'POS sale stock deduction (offline-synced)',
            'created_by'               => $user->id,
            'transaction_type'         => 'sales',
            'transaction_date'         => optional($sale->sale_date)->toDateString() ?? now()->toDateString(),
            'transaction_reference'    => $sale->sale_number,
            'source_transaction_type'  => Sale::class,
            'source_transaction_id'    => $sale->id,
        ]);

        $today = now()->toDateString();
        $saleDate = optional($sale->sale_date)->toDateString() ?? $today;
        Cache::forget("product_stock_{$product->id}_{$today}");
        Cache::forget("product_stock_{$product->id}_{$saleDate}");
        Cache::forget("product_stock_value_{$product->id}_{$today}_weighted_average");
        Cache::forget("product_stock_value_{$product->id}_{$saleDate}_weighted_average");
    }

    /**
     * Mirror PosApiController::generateReceipt.
     */
    private function generateReceipt(Sale $sale): Receipt
    {
        $sale->loadMissing(['customer', 'items.product', 'payments.paymentMethod', 'user']);

        $receiptData = [
            'company' => [
                'name' => $sale->tenant->name ?? null,
                'email' => $sale->tenant->email ?? null,
                'phone' => $sale->tenant->phone ?? null,
                'address' => $sale->tenant->address ?? null,
            ],
            'sale' => [
                'number' => $sale->sale_number,
                'date' => optional($sale->sale_date)->format('Y-m-d H:i:s'),
                'cashier' => $sale->user->name ?? 'N/A',
                'customer' => $sale->customer ? [
                    'name' => $sale->customer->full_name ?? null,
                    'email' => $sale->customer->email ?? null,
                    'phone' => $sale->customer->phone ?? null,
                ] : null,
            ],
            'items' => $sale->items->map(fn ($item) => [
                'name' => $item->product_name,
                'sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount_amount,
                'tax' => $item->tax_amount,
                'total' => $item->line_total,
            ]),
            'payments' => $sale->payments->map(fn ($p) => [
                'method' => $p->paymentMethod->name ?? 'Unknown',
                'amount' => $p->amount,
                'reference' => $p->reference_number,
            ]),
            'totals' => [
                'subtotal' => $sale->subtotal,
                'discount' => $sale->discount_amount,
                'tax' => $sale->tax_amount,
                'total' => $sale->total_amount,
                'paid' => $sale->paid_amount,
                'change' => $sale->change_amount,
            ],
        ];

        return Receipt::create([
            'tenant_id' => $sale->tenant_id,
            'sale_id' => $sale->id,
            'receipt_number' => Receipt::generateReceiptNumber($sale),
            'type' => 'original',
            'receipt_data' => $receiptData,
        ]);
    }

    /**
     * Mirror PosApiController::createAccountingEntries.
     * Best-effort: logs and returns silently if accounts are missing.
     */
    private function createAccountingEntries(Tenant $tenant, Sale $sale, User $user): void
    {
        try {
            $cashAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('account_type', 'asset')
                ->where(function ($q) {
                    $q->where('code', 'CASH-001')
                      ->orWhere('name', 'LIKE', '%Cash%');
                })
                ->where('is_active', true)
                ->first();

            $salesAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('account_type', 'income')
                ->where(function ($q) {
                    $q->where('code', 'SALES-001')
                      ->orWhere('name', 'LIKE', '%Sales%');
                })
                ->where('is_active', true)
                ->first();

            if (!$cashAccount || !$salesAccount) {
                Log::warning('PosSaleSync: Missing ledger accounts for accounting entries', [
                    'sale_id' => $sale->id,
                    'tenant_id' => $tenant->id,
                ]);
                return;
            }

            $voucherType = VoucherType::where('tenant_id', $tenant->id)
                ->where('code', 'RV')
                ->first();

            if (!$voucherType) {
                Log::warning('PosSaleSync: No RV voucher type found', ['tenant_id' => $tenant->id]);
                return;
            }

            $netSalesRevenue = $sale->subtotal - $sale->discount_amount;

            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $voucherType->id,
                'voucher_number' => Voucher::generateVoucherNumber($voucherType),
                'date' => optional($sale->sale_date)->toDateString() ?? now()->toDateString(),
                'narration' => 'POS Sale - ' . $sale->sale_number,
                'status' => 'posted',
                'total_amount' => $sale->total_amount,
                'created_by' => $user->id,
            ]);

            VoucherEntry::create([
                'tenant_id' => $tenant->id,
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $cashAccount->id,
                'debit' => $sale->total_amount,
                'credit' => 0,
                'narration' => 'Cash from POS Sale ' . $sale->sale_number,
            ]);

            VoucherEntry::create([
                'tenant_id' => $tenant->id,
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $salesAccount->id,
                'debit' => 0,
                'credit' => $netSalesRevenue,
                'narration' => 'Sales revenue from POS Sale ' . $sale->sale_number,
            ]);

            if ($sale->tax_amount > 0) {
                $taxAccount = LedgerAccount::where('tenant_id', $tenant->id)
                    ->where('account_type', 'liability')
                    ->where(function ($q) {
                        $q->where('code', 'TAX-001')
                          ->orWhere('name', 'LIKE', '%Tax Payable%');
                    })
                    ->where('is_active', true)
                    ->first();

                if ($taxAccount) {
                    VoucherEntry::create([
                        'tenant_id' => $tenant->id,
                        'voucher_id' => $voucher->id,
                        'ledger_account_id' => $taxAccount->id,
                        'debit' => 0,
                        'credit' => $sale->tax_amount,
                        'narration' => 'Tax payable from POS Sale ' . $sale->sale_number,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('PosSaleSync: Failed to create accounting entries', [
                'sale_id' => $sale->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
