<?php

namespace App\Services\MobileSync;

use App\Models\Customer;
use App\Models\LedgerAccount;
use App\Models\MobileDevice;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Voucher;
use App\Models\VoucherType;
use App\Models\StockMovement;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Phase 2: applies a `vouchers` push mutation from a mobile device.
 *
 * Reuses the canonical posting flow used by the web/API
 * `Accounting\InvoiceController::store`:
 *   1. Resolve party (customer/vendor) by sync_uuid -> ledger_account_id
 *   2. Resolve each line item product by product_sync_uuid (or product_id)
 *   3. Generate official voucher_number via VoucherType::getNextVoucherNumber()
 *   4. Create Voucher (default `draft` for offline-created — server-side
 *      posting still requires an explicit online action)
 *   5. Create invoice_items
 *   6. Create voucher_entries via the same double-entry rules as the
 *      web invoice controller
 *   7. If client requested status=posted AND device user has post
 *      permission, post and update stock
 *
 * Idempotency is handled by the caller (PushService) via the
 * `mobile_mutations` table.
 */
class InvoiceSyncService
{
    /**
     * Apply a single voucher mutation. Returns an envelope compatible
     * with PushService::persistAndReturn.
     *
     * @param  array<string, mixed>  $payload  Decoded `data` from the
     *                                         mutation envelope.
     * @return array{ok: bool, server_response?: array, error_code?: string,
     *               error_message?: string, errors?: array}
     */
    public function applyCreate(
        Tenant $tenant,
        User $user,
        MobileDevice $device,
        string $syncUuid,
        array $payload,
    ): array {
        // ── 1. Validate envelope ─────────────────────────────────────────
        $validator = Validator::make($payload, [
            'voucher_type_id'         => 'sometimes|integer|exists:voucher_types,id',
            'voucher_type_code'       => 'sometimes|string|max:32',
            'voucher_date'            => 'required|date',
            'reference_number'        => 'nullable|string|max:255',
            'narration'               => 'nullable|string',
            'customer_sync_uuid'      => 'nullable|uuid',
            'vendor_sync_uuid'        => 'nullable|uuid',
            'party_id'                => 'nullable|integer',
            'items'                   => 'required|array|min:1',
            'items.*.product_sync_uuid' => 'sometimes|nullable|uuid',
            'items.*.product_id'      => 'sometimes|nullable|integer|exists:products,id',
            'items.*.quantity'        => 'required|numeric|min:0.0001',
            'items.*.rate'            => 'required|numeric|min:0',
            'items.*.discount'        => 'nullable|numeric|min:0',
            'items.*.vat_rate'        => 'nullable|numeric|min:0',
            'items.*.description'     => 'nullable|string',
            'additional_ledger_accounts'                    => 'nullable|array',
            'additional_ledger_accounts.*.ledger_account_id' => 'required_with:additional_ledger_accounts|integer|exists:ledger_accounts,id',
            'additional_ledger_accounts.*.amount'           => 'required_with:additional_ledger_accounts|numeric|min:0',
            'additional_ledger_accounts.*.narration'        => 'nullable|string',
            'vat_enabled'             => 'nullable|boolean',
            'vat_amount'              => 'nullable|numeric|min:0',
            'vat_applies_to'          => 'nullable|in:items_only,items_and_charges',
            'status'                  => 'nullable|in:draft,posted',
            'client_voucher_number'   => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return [
                'ok' => false,
                'error_code' => 'validation_failed',
                'error_message' => 'Invoice payload failed validation',
                'errors' => $validator->errors()->toArray(),
            ];
        }

        // ── 2. Resolve VoucherType (by id or code) ───────────────────────
        $voucherType = $this->resolveVoucherType($tenant, $payload);
        if (!$voucherType) {
            return [
                'ok' => false,
                'error_code' => 'voucher_type_not_found',
                'error_message' => 'voucher_type_id or voucher_type_code is required and must belong to the tenant',
            ];
        }

        $isSales = $voucherType->inventory_effect === 'decrease';

        // ── 3. Resolve party ledger ──────────────────────────────────────
        $partyResolution = $this->resolveParty($tenant, $isSales, $payload);
        if (!$partyResolution['ok']) {
            return $partyResolution;
        }
        $partyLedgerAccountId = $partyResolution['ledger_account_id'];

        // ── 4. Resolve item products ─────────────────────────────────────
        $resolvedItems = [];
        foreach (Arr::get($payload, 'items', []) as $idx => $item) {
            $product = $this->resolveProduct($tenant, $item);
            if (!$product) {
                return [
                    'ok' => false,
                    'error_code' => 'product_not_found',
                    'error_message' => "Item {$idx}: product could not be resolved by product_sync_uuid or product_id",
                    'errors' => ['items.' . $idx . '.product_sync_uuid' => ['Unknown product']],
                ];
            }

            $quantity = (float) $item['quantity'];
            $rate     = (float) $item['rate'];
            $discount = (float) ($item['discount'] ?? 0);
            $amount   = ($quantity * $rate) - $discount;

            $resolvedItems[] = [
                'product_id'        => $product->id,
                'product_name'      => $product->name,
                'description'       => $item['description'] ?? $product->name,
                'quantity'          => $quantity,
                'unit_id'           => $item['unit_id'] ?? $product->primary_unit_id,
                'rate'              => $rate,
                'amount'            => $amount,
                'discount_percentage' => 0,
                'discount_amount'   => $discount,
                'tax_percentage'    => (float) ($item['vat_rate'] ?? 0),
                'tax_amount'        => 0,
                'total'             => $amount,
                'purchase_rate'     => (float) ($product->purchase_rate ?? 0),
            ];
        }

        // ── 5. Build additional ledger accounts (incl. VAT auto-lookup) ──
        $additionalLedgerAccounts = Arr::get($payload, 'additional_ledger_accounts', []) ?? [];

        if (!empty($payload['vat_enabled']) && (float) ($payload['vat_amount'] ?? 0) > 0) {
            $vatAccountCode = $isSales ? 'VAT-OUT-001' : 'VAT-IN-001';
            $vatAccount = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('code', $vatAccountCode)
                ->first();

            if ($vatAccount) {
                $vatAppliesTo = $payload['vat_applies_to'] ?? 'items_only';
                $additionalLedgerAccounts[] = [
                    'ledger_account_id' => $vatAccount->id,
                    'amount' => (float) $payload['vat_amount'],
                    'narration' => $vatAppliesTo === 'items_only'
                        ? 'VAT @ 7.5% (on items)'
                        : 'VAT @ 7.5% (on items + charges)',
                ];
            } else {
                Log::warning('Mobile sync: VAT account missing', [
                    'tenant_id' => $tenant->id,
                    'vat_account_code' => $vatAccountCode,
                ]);
            }
        }

        // ── 6. Decide post vs draft ──────────────────────────────────────
        $requestedStatus = $payload['status'] ?? 'draft';
        $canPost = $user->can('accounting.invoices.post')
            || $user->can('mobile.sync.write.invoices');
        $shouldPost = $requestedStatus === 'posted' && $canPost;

        $totalAmount = collect($resolvedItems)->sum('amount')
            + collect($additionalLedgerAccounts)->sum('amount');

        // ── 7. Persist ───────────────────────────────────────────────────
        try {
            $voucher = DB::transaction(function () use (
                $tenant, $user, $device, $syncUuid, $voucherType, $payload,
                $resolvedItems, $additionalLedgerAccounts, $partyLedgerAccountId,
                $totalAmount, $shouldPost, $isSales,
            ) {
                $voucherNumber = $voucherType->getNextVoucherNumber();

                $voucher = Voucher::create([
                    'tenant_id'        => $tenant->id,
                    'voucher_type_id'  => $voucherType->id,
                    'voucher_number'   => $voucherNumber,
                    'voucher_date'     => $payload['voucher_date'],
                    'reference_number' => $payload['reference_number'] ?? null,
                    'narration'        => $payload['narration'] ?? null,
                    'total_amount'     => $totalAmount,
                    'status'           => $shouldPost ? Voucher::STATUS_POSTED : Voucher::STATUS_DRAFT,
                    'created_by'       => $user->id,
                    'posted_at'        => $shouldPost ? now() : null,
                    'posted_by'        => $shouldPost ? $user->id : null,
                    'sync_uuid'        => $syncUuid,
                    'last_modified_by_device_id' => $device->device_uuid,
                ]);

                foreach ($resolvedItems as $item) {
                    $voucher->items()->create([
                        'product_id'          => $item['product_id'],
                        'product_name'        => $item['product_name'],
                        'description'         => $item['description'],
                        'quantity'            => $item['quantity'],
                        'rate'                => $item['rate'],
                        'amount'              => $item['amount'],
                        'discount'            => $item['discount_amount'] ?? 0,
                        'tax'                 => $item['tax_amount'] ?? 0,
                        'unit'                => null,
                        'total'               => $item['total'] ?? $item['amount'],
                        'purchase_rate'       => $item['purchase_rate'] ?? 0,
                    ]);
                }

                $this->createAccountingEntries(
                    $voucher,
                    $resolvedItems,
                    $partyLedgerAccountId,
                    $additionalLedgerAccounts,
                    $isSales,
                );

                if ($shouldPost) {
                    foreach ($resolvedItems as $item) {
                        $product = Product::find($item['product_id']);
                        if (!$product) {
                            continue;
                        }
                        $movementType = $isSales ? 'out' : 'in';
                        StockMovement::createFromVoucher($voucher, $item, $movementType);
                    }
                }

                return $voucher;
            });
        } catch (\Throwable $e) {
            report($e);
            return [
                'ok' => false,
                'error_code' => 'invoice_apply_failed',
                'error_message' => $e->getMessage(),
            ];
        }

        $voucher->refresh();

        return [
            'ok' => true,
            'server_response' => [
                'server_id'              => $voucher->id,
                'server_version'         => (int) ($voucher->server_version ?? 0),
                'sync_uuid'              => $voucher->sync_uuid,
                'official_voucher_number' => $voucher->voucher_number,
                'voucher_type_code'      => $voucherType->code,
                'voucher_type_prefix'    => $voucherType->prefix,
                'status'                 => $voucher->status,
                'total_amount'           => (float) $voucher->total_amount,
                'updated_at'             => optional($voucher->updated_at)->toIso8601String(),
                'posted_at'              => optional($voucher->posted_at)->toIso8601String(),
            ],
        ];
    }

    private function resolveVoucherType(Tenant $tenant, array $payload): ?VoucherType
    {
        if (!empty($payload['voucher_type_id'])) {
            return VoucherType::where('tenant_id', $tenant->id)
                ->where('id', $payload['voucher_type_id'])
                ->first();
        }
        if (!empty($payload['voucher_type_code'])) {
            return VoucherType::where('tenant_id', $tenant->id)
                ->where('code', $payload['voucher_type_code'])
                ->first();
        }
        return null;
    }

    /**
     * @return array{ok: bool, ledger_account_id?: int, error_code?: string, error_message?: string, errors?: array}
     */
    private function resolveParty(Tenant $tenant, bool $isSales, array $payload): array
    {
        if ($isSales) {
            $customer = null;
            if (!empty($payload['customer_sync_uuid'])) {
                $customer = Customer::where('tenant_id', $tenant->id)
                    ->where('sync_uuid', $payload['customer_sync_uuid'])
                    ->first();
            }
            if (!$customer && !empty($payload['party_id'])) {
                $customer = Customer::where('tenant_id', $tenant->id)
                    ->where('id', $payload['party_id'])
                    ->first();
            }
            if (!$customer) {
                return [
                    'ok' => false,
                    'error_code' => 'customer_not_found',
                    'error_message' => 'customer_sync_uuid or party_id required and must belong to tenant',
                ];
            }
            if (!$customer->ledger_account_id) {
                return [
                    'ok' => false,
                    'error_code' => 'customer_ledger_missing',
                    'error_message' => 'Customer has no ledger account',
                ];
            }
            return ['ok' => true, 'ledger_account_id' => (int) $customer->ledger_account_id];
        }

        $vendor = null;
        if (!empty($payload['vendor_sync_uuid'])) {
            $vendor = Vendor::where('tenant_id', $tenant->id)
                ->where('sync_uuid', $payload['vendor_sync_uuid'])
                ->first();
        }
        if (!$vendor && !empty($payload['party_id'])) {
            $vendor = Vendor::where('tenant_id', $tenant->id)
                ->where('id', $payload['party_id'])
                ->first();
        }
        if (!$vendor) {
            return [
                'ok' => false,
                'error_code' => 'vendor_not_found',
                'error_message' => 'vendor_sync_uuid or party_id required and must belong to tenant',
            ];
        }
        if (!$vendor->ledger_account_id) {
            return [
                'ok' => false,
                'error_code' => 'vendor_ledger_missing',
                'error_message' => 'Vendor has no ledger account',
            ];
        }
        return ['ok' => true, 'ledger_account_id' => (int) $vendor->ledger_account_id];
    }

    private function resolveProduct(Tenant $tenant, array $item): ?Product
    {
        if (!empty($item['product_sync_uuid'])) {
            $p = Product::where('tenant_id', $tenant->id)
                ->where('sync_uuid', $item['product_sync_uuid'])
                ->first();
            if ($p) {
                return $p;
            }
        }
        if (!empty($item['product_id'])) {
            return Product::where('tenant_id', $tenant->id)
                ->where('id', $item['product_id'])
                ->first();
        }
        return null;
    }

    /**
     * Mirrors `Api\Tenant\Accounting\InvoiceController::createAccountingEntries`
     * exactly. Kept here (rather than calling the controller) to avoid
     * coupling an HTTP controller to a service.
     */
    private function createAccountingEntries(
        Voucher $voucher,
        array $inventoryItems,
        int $partyLedgerAccountId,
        array $additionalLedgerAccounts,
        bool $isSales,
    ): void {
        $partyAccount = LedgerAccount::find($partyLedgerAccountId);
        if (!$partyAccount) {
            throw new \RuntimeException('Party ledger account not found');
        }

        $totalAmount = collect($inventoryItems)->sum('amount')
            + collect($additionalLedgerAccounts)->sum('amount');

        $groupedItems = [];
        foreach ($inventoryItems as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                continue;
            }
            $accountId = $isSales ? $product->sales_account_id : $product->purchase_account_id;
            if (!$accountId) {
                continue;
            }
            $groupedItems[$accountId] = ($groupedItems[$accountId] ?? 0) + $item['amount'];
        }

        if ($isSales) {
            $voucher->entries()->create([
                'ledger_account_id' => $partyAccount->id,
                'debit_amount' => $totalAmount,
                'credit_amount' => 0,
                'particulars' => 'Sales to ' . $partyAccount->name,
            ]);
            foreach ($groupedItems as $accountId => $amount) {
                $voucher->entries()->create([
                    'ledger_account_id' => $accountId,
                    'debit_amount' => 0,
                    'credit_amount' => $amount,
                    'particulars' => 'Sales',
                ]);
            }
        } else {
            $voucher->entries()->create([
                'ledger_account_id' => $partyAccount->id,
                'debit_amount' => 0,
                'credit_amount' => $totalAmount,
                'particulars' => 'Purchase from ' . $partyAccount->name,
            ]);
            foreach ($groupedItems as $accountId => $amount) {
                $voucher->entries()->create([
                    'ledger_account_id' => $accountId,
                    'debit_amount' => $amount,
                    'credit_amount' => 0,
                    'particulars' => 'Purchase',
                ]);
            }
        }

        foreach ($additionalLedgerAccounts as $ledger) {
            if ($isSales) {
                $voucher->entries()->create([
                    'ledger_account_id' => $ledger['ledger_account_id'],
                    'debit_amount' => 0,
                    'credit_amount' => $ledger['amount'],
                    'particulars' => $ledger['narration'] ?? 'Additional charge',
                ]);
            } else {
                $voucher->entries()->create([
                    'ledger_account_id' => $ledger['ledger_account_id'],
                    'debit_amount' => $ledger['amount'],
                    'credit_amount' => 0,
                    'particulars' => $ledger['narration'] ?? 'Additional charge',
                ]);
            }
        }
    }
}
