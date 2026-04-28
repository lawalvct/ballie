<?php

namespace App\Services\MobileSync;

use App\Models\Customer;
use App\Models\LedgerAccount;
use App\Models\MobileDevice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Phase 3: applies a `quotations` push mutation from a mobile device.
 *
 * Mirrors the canonical posting flow used by
 * `Api\Tenant\Accounting\QuotationController::store`:
 *   1. Resolve customer/vendor by sync_uuid -> ledger_account_id
 *   2. Generate next quotation_number (server-authoritative)
 *   3. Create Quotation row + line items
 *   4. Recalculate subtotal/VAT/total via Quotation::calculateTotals()
 *
 * Stays as a `draft` quotation. State transitions (sent, accepted,
 * rejected, converted) remain online-only because they trigger
 * notifications, voucher creation, and ledger effects.
 *
 * Idempotency is handled by the caller (PushService) via the
 * `mobile_mutations` table — this service is only invoked when no
 * Quotation with the given sync_uuid exists yet.
 */
class QuotationSyncService
{
    /**
     * Apply a single quotation create mutation.
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
            'quotation_date'         => 'required|date',
            'expiry_date'            => 'nullable|date|after_or_equal:quotation_date',
            'customer_sync_uuid'     => 'nullable|uuid',
            'vendor_sync_uuid'       => 'nullable|uuid',
            'customer_ledger_id'     => 'nullable|integer',
            'reference_number'       => 'nullable|string|max:255',
            'subject'                => 'nullable|string|max:500',
            'document_title'         => 'nullable|string|max:255',
            'terms_and_conditions'   => 'nullable|string',
            'notes'                  => 'nullable|string',
            'vat_enabled'            => 'nullable|boolean',
            'vat_applies_to'         => 'nullable|string|in:items,items_and_charges',
            'additional_charges'     => 'nullable|array',
            'additional_charges.*.label'  => 'required_with:additional_charges|string|max:255',
            'additional_charges.*.amount' => 'required_with:additional_charges|numeric|min:0',
            'items'                       => 'required|array|min:1',
            'items.*.product_sync_uuid'   => 'sometimes|nullable|uuid',
            'items.*.product_id'          => 'sometimes|nullable|integer',
            'items.*.description'         => 'nullable|string',
            'items.*.quantity'            => 'required|numeric|min:0.0001',
            'items.*.rate'                => 'required|numeric|min:0',
            'items.*.discount'            => 'nullable|numeric|min:0',
            'items.*.tax'                 => 'nullable|numeric|min:0',
            'items.*.is_tax_inclusive'    => 'nullable|boolean',
            'client_quotation_number'     => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return [
                'ok'            => false,
                'error_code'    => 'validation_failed',
                'error_message' => 'Validation failed',
                'errors'        => $validator->errors()->toArray(),
            ];
        }

        // ── Resolve party (customer/vendor) ──────────────────────────────
        [$customer, $vendor, $customerLedgerId, $partyError] = $this->resolveParty($tenant, $payload);
        if ($partyError) {
            return [
                'ok'            => false,
                'error_code'    => 'party_not_found',
                'error_message' => $partyError,
            ];
        }

        // ── Resolve products by sync_uuid (preferred) or product_id ──────
        $resolvedItems = [];
        foreach ($payload['items'] as $idx => $item) {
            $product = $this->resolveProduct($tenant, $item);
            if (!$product) {
                return [
                    'ok'            => false,
                    'error_code'    => 'product_not_found',
                    'error_message' => "Item #{$idx}: product not found or not saleable",
                ];
            }
            $resolvedItems[] = ['raw' => $item, 'product' => $product];
        }

        try {
            return DB::transaction(function () use (
                $tenant, $user, $device, $syncUuid, $payload,
                $customer, $vendor, $customerLedgerId, $resolvedItems,
            ) {
                // ── Generate next quotation_number atomically ────────
                $last = Quotation::where('tenant_id', $tenant->id)
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();
                $nextNumber = $last ? ((int) $last->quotation_number) + 1 : 1;

                $quotation = Quotation::create([
                    'sync_uuid'              => $syncUuid,
                    'tenant_id'              => $tenant->id,
                    'quotation_number'       => $nextNumber,
                    'quotation_date'         => $payload['quotation_date'],
                    'expiry_date'            => $payload['expiry_date'] ?? null,
                    'customer_id'            => $customer?->id,
                    'vendor_id'              => $vendor?->id,
                    'customer_ledger_id'     => $customerLedgerId,
                    'reference_number'       => $payload['reference_number'] ?? null,
                    'subject'                => $payload['subject'] ?? null,
                    'document_title'         => $payload['document_title'] ?? null,
                    'terms_and_conditions'   => $payload['terms_and_conditions'] ?? null,
                    'notes'                  => $payload['notes'] ?? null,
                    'vat_enabled'            => (bool) ($payload['vat_enabled'] ?? false),
                    'vat_applies_to'         => $payload['vat_applies_to'] ?? 'items',
                    'additional_charges'     => $payload['additional_charges'] ?? null,
                    'status'                 => 'draft',
                    'created_by'             => $user->id,
                    'last_modified_by_device_id' => $device->device_uuid,
                ]);

                foreach ($resolvedItems as $idx => $resolved) {
                    $item = $resolved['raw'];
                    $product = $resolved['product'];
                    $quotation->items()->create([
                        'product_id'        => $product->id,
                        'item_type'         => 'product',
                        'product_name'      => $product->name,
                        'description'       => $item['description'] ?? $product->description,
                        'quantity'          => $item['quantity'],
                        'unit'              => $product->primaryUnit->symbol ?? 'Pcs',
                        'rate'              => $item['rate'],
                        'discount'          => $item['discount'] ?? 0,
                        'tax'               => $item['tax'] ?? 0,
                        'is_tax_inclusive'  => $item['is_tax_inclusive'] ?? false,
                        'sort_order'        => $idx,
                        'last_modified_by_device_id' => $device->device_uuid,
                    ]);
                }

                $quotation->load('items');
                $quotation->calculateTotals();
                $quotation->save();

                return [
                    'ok'              => true,
                    'server_response' => [
                        'server_id'                  => $quotation->id,
                        'server_version'             => $quotation->server_version,
                        'sync_uuid'                  => $quotation->sync_uuid,
                        'official_quotation_number'  => $quotation->quotation_number,
                        'status'                     => $quotation->status,
                        'subtotal'                   => (float) $quotation->subtotal,
                        'vat_amount'                 => (float) $quotation->vat_amount,
                        'total_amount'               => (float) $quotation->total_amount,
                        'updated_at'                 => optional($quotation->updated_at)->toIso8601String(),
                    ],
                ];
            });
        } catch (\Throwable $e) {
            report($e);
            return [
                'ok'            => false,
                'error_code'    => 'server_error',
                'error_message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @return array{0: ?Customer, 1: ?Vendor, 2: ?int, 3: ?string}
     */
    private function resolveParty(Tenant $tenant, array $payload): array
    {
        $customer = null;
        $vendor = null;
        $customerLedgerId = null;

        if (!empty($payload['customer_sync_uuid'])) {
            $customer = Customer::where('tenant_id', $tenant->id)
                ->where('sync_uuid', $payload['customer_sync_uuid'])
                ->first();
            if (!$customer) {
                return [null, null, null, 'Customer not found for sync_uuid'];
            }
            $customerLedgerId = $customer->ledger_account_id;
        } elseif (!empty($payload['vendor_sync_uuid'])) {
            $vendor = Vendor::where('tenant_id', $tenant->id)
                ->where('sync_uuid', $payload['vendor_sync_uuid'])
                ->first();
            if (!$vendor) {
                return [null, null, null, 'Vendor not found for sync_uuid'];
            }
            $customerLedgerId = $vendor->ledger_account_id;
        } elseif (!empty($payload['customer_ledger_id'])) {
            $ledger = LedgerAccount::where('tenant_id', $tenant->id)
                ->where('id', $payload['customer_ledger_id'])
                ->first();
            if (!$ledger) {
                return [null, null, null, 'customer_ledger_id is invalid'];
            }
            $customerLedgerId = $ledger->id;
            $customer = Customer::where('ledger_account_id', $ledger->id)->first();
            $vendor = $customer ? null : Vendor::where('ledger_account_id', $ledger->id)->first();
        } else {
            return [null, null, null, 'A customer_sync_uuid, vendor_sync_uuid, or customer_ledger_id is required'];
        }

        return [$customer, $vendor, $customerLedgerId, null];
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
}
