<?php

namespace App\Services\MobileSync;

use App\Models\MobileDevice;
use App\Models\Product;
use App\Models\StockJournalEntry;
use App\Models\StockJournalEntryItem;
use App\Models\StockLocation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Phase 4: applies a `stock_journal_entries` push mutation from a
 * mobile device.
 *
 * Mirrors the canonical posting flow used by
 * `Api\Tenant\Inventory\StockJournalController::store`:
 *   1. Resolve products by sync_uuid (or product_id) per item
 *   2. Resolve optional stock locations by sync_uuid (transfer)
 *   3. Generate next journal_number atomically (server-authoritative)
 *   4. Create StockJournalEntry row with status = 'draft'
 *   5. Create StockJournalEntryItem rows
 *
 * Stays as `draft`. Posting (status -> 'posted') remains online-only
 * because it triggers stock_movements writes, ledger effects, and
 * stock validation that needs strict server-side conflict rules.
 *
 * Idempotency is handled by the caller (PushService) via the
 * `mobile_mutations` table — this service is only invoked when no
 * StockJournalEntry with the given sync_uuid exists yet.
 */
class StockJournalSyncService
{
    /**
     * Apply a single stock journal create mutation.
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
            'journal_date'                       => 'required|date',
            'entry_type'                         => 'required|in:consumption,production,adjustment,transfer',
            'reference_number'                   => 'nullable|string|max:100',
            'narration'                          => 'nullable|string|max:500',
            'from_stock_location_sync_uuid'      => 'nullable|uuid',
            'to_stock_location_sync_uuid'        => 'nullable|uuid',
            'production_batch_number'            => 'nullable|string|max:100',
            'work_order_number'                  => 'nullable|string|max:100',
            'production_shift'                   => 'nullable|string|max:50',
            'machine_name'                       => 'nullable|string|max:100',
            'production_started_at'              => 'nullable|date',
            'production_ended_at'                => 'nullable|date',
            'production_notes'                   => 'nullable|string',
            'items'                              => 'required|array|min:1',
            'items.*.product_sync_uuid'          => 'sometimes|nullable|uuid',
            'items.*.product_id'                 => 'sometimes|nullable|integer',
            'items.*.movement_type'              => 'required|in:in,out',
            'items.*.quantity'                   => 'required|numeric|min:0.0001',
            'items.*.rate'                       => 'required|numeric|min:0',
            'items.*.batch_number'               => 'nullable|string|max:50',
            'items.*.expiry_date'                => 'nullable|date',
            'items.*.remarks'                    => 'nullable|string|max:200',
            'items.*.stock_location_sync_uuid'   => 'sometimes|nullable|uuid',
        ]);

        if ($validator->fails()) {
            return [
                'ok'            => false,
                'error_code'    => 'validation_failed',
                'error_message' => 'Validation failed',
                'errors'        => $validator->errors()->toArray(),
            ];
        }

        // ── Resolve optional from/to stock locations ────────────────────
        $fromLocation = null;
        $toLocation = null;
        if (!empty($payload['from_stock_location_sync_uuid'])) {
            $fromLocation = StockLocation::where('tenant_id', $tenant->id)
                ->where('sync_uuid', $payload['from_stock_location_sync_uuid'])
                ->first();
            if (!$fromLocation) {
                return [
                    'ok'            => false,
                    'error_code'    => 'stock_location_not_found',
                    'error_message' => 'from_stock_location_sync_uuid is invalid',
                ];
            }
        }
        if (!empty($payload['to_stock_location_sync_uuid'])) {
            $toLocation = StockLocation::where('tenant_id', $tenant->id)
                ->where('sync_uuid', $payload['to_stock_location_sync_uuid'])
                ->first();
            if (!$toLocation) {
                return [
                    'ok'            => false,
                    'error_code'    => 'stock_location_not_found',
                    'error_message' => 'to_stock_location_sync_uuid is invalid',
                ];
            }
        }

        // Transfer entries require both endpoints and they must differ.
        if ($payload['entry_type'] === 'transfer') {
            if (!$fromLocation || !$toLocation) {
                return [
                    'ok'            => false,
                    'error_code'    => 'transfer_requires_locations',
                    'error_message' => 'Transfer entries require both from_stock_location_sync_uuid and to_stock_location_sync_uuid',
                ];
            }
            if ($fromLocation->id === $toLocation->id) {
                return [
                    'ok'            => false,
                    'error_code'    => 'transfer_same_location',
                    'error_message' => 'Transfer source and destination locations must differ',
                ];
            }
        }

        // ── Resolve products + per-item stock locations ─────────────────
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

            $itemLocation = null;
            if (!empty($item['stock_location_sync_uuid'])) {
                $itemLocation = StockLocation::where('tenant_id', $tenant->id)
                    ->where('sync_uuid', $item['stock_location_sync_uuid'])
                    ->first();
                if (!$itemLocation) {
                    return [
                        'ok'            => false,
                        'error_code'    => 'stock_location_not_found',
                        'error_message' => "Item #{$idx}: stock_location_sync_uuid is invalid",
                    ];
                }
            }

            $resolvedItems[] = [
                'raw'      => $item,
                'product'  => $product,
                'location' => $itemLocation,
            ];
        }

        try {
            return DB::transaction(function () use (
                $tenant, $user, $device, $syncUuid, $payload,
                $fromLocation, $toLocation, $resolvedItems,
            ) {
                // Generate next journal_number atomically. We lock the
                // most recent journal row for this tenant so concurrent
                // pushes serialise on number assignment, matching the
                // pattern used by other sync services.
                StockJournalEntry::where('tenant_id', $tenant->id)
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();

                $entry = StockJournalEntry::create([
                    'sync_uuid'                  => $syncUuid,
                    'tenant_id'                  => $tenant->id,
                    'journal_date'               => $payload['journal_date'],
                    'reference_number'           => $payload['reference_number'] ?? null,
                    'narration'                  => $payload['narration'] ?? null,
                    'entry_type'                 => $payload['entry_type'],
                    'from_stock_location_id'     => $fromLocation?->id,
                    'to_stock_location_id'       => $toLocation?->id,
                    'production_batch_number'    => $payload['production_batch_number'] ?? null,
                    'work_order_number'          => $payload['work_order_number'] ?? null,
                    'production_shift'           => $payload['production_shift'] ?? null,
                    'machine_name'               => $payload['machine_name'] ?? null,
                    'production_started_at'      => $payload['production_started_at'] ?? null,
                    'production_ended_at'        => $payload['production_ended_at'] ?? null,
                    'production_notes'           => $payload['production_notes'] ?? null,
                    'status'                     => 'draft',
                    'created_by'                 => $user->id,
                    'last_modified_by_device_id' => $device->device_uuid,
                ]);

                foreach ($resolvedItems as $resolved) {
                    $item = $resolved['raw'];
                    $product = $resolved['product'];
                    $location = $resolved['location'];

                    StockJournalEntryItem::create([
                        'stock_journal_entry_id'     => $entry->id,
                        'product_id'                 => $product->id,
                        'stock_location_id'          => $location?->id,
                        'movement_type'              => $item['movement_type'],
                        'quantity'                   => $item['quantity'],
                        'rate'                       => $item['rate'],
                        'batch_number'               => $item['batch_number'] ?? null,
                        'expiry_date'                => $item['expiry_date'] ?? null,
                        'remarks'                    => $item['remarks'] ?? null,
                        'last_modified_by_device_id' => $device->device_uuid,
                    ]);
                }

                $entry->refresh();

                return [
                    'ok'              => true,
                    'server_response' => [
                        'server_id'              => $entry->id,
                        'server_version'         => (int) ($entry->server_version ?? 1),
                        'sync_uuid'              => $entry->sync_uuid,
                        'official_journal_number' => $entry->journal_number,
                        'status'                 => $entry->status,
                        'entry_type'             => $entry->entry_type,
                        'journal_date'           => optional($entry->journal_date)->toDateString(),
                        'updated_at'             => optional($entry->updated_at)->toIso8601String(),
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
