<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Customer;
use App\Models\MobileDevice;
use App\Models\MobileDocumentExport;
use App\Models\MobileMutation;
use App\Models\MobileSyncConflict;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Services\MobileSync\CustomerStatementSnapshotService;
use App\Services\MobileSync\DocumentExportService;
use App\Services\MobileSync\PullService;
use App\Services\MobileSync\PushService;
use App\Services\MobileSync\SyncRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * Mobile offline sync entrypoint (Phase 1).
 *
 * All routes are mounted under
 *   /api/v1/tenant/{tenant}/sync
 * inside the `auth:sanctum` group. Tenant slug is verified against the
 * authenticated user's tenant on every request.
 */
class SyncController extends BaseApiController
{
    public function __construct(
        private SyncRegistry $registry,
        private PullService $pullService,
        private PushService $pushService,
        private DocumentExportService $documentExports,
        private CustomerStatementSnapshotService $statementService,
    ) {
    }

    public function registerDevice(Request $request, Tenant $tenant): JsonResponse
    {
        if ($guard = $this->guardTenant($request, $tenant)) {
            return $guard;
        }

        $validator = Validator::make($request->all(), [
            'device_uuid' => 'required|string|max:64',
            'device_name' => 'sometimes|nullable|string|max:255',
            'platform' => 'sometimes|nullable|string|max:32',
            'app_version' => 'sometimes|nullable|string|max:32',
            'os_version' => 'sometimes|nullable|string|max:64',
            'push_token' => 'sometimes|nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        $device = MobileDevice::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'device_uuid' => $request->string('device_uuid')->toString(),
            ],
            [
                'user_id' => $user->id,
                'device_name' => $request->input('device_name'),
                'platform' => $request->input('platform'),
                'app_version' => $request->input('app_version'),
                'os_version' => $request->input('os_version'),
                'push_token' => $request->input('push_token'),
                'last_seen_at' => now(),
                'revoked_at' => null,
                'revoked_reason' => null,
            ]
        );

        return $this->success([
            'device' => [
                'id' => $device->id,
                'device_uuid' => $device->device_uuid,
                'last_synced_at' => optional($device->last_synced_at)->toIso8601String(),
            ],
            'schema_version' => (int) config('mobile_sync.schema_version', 1),
            'server_time' => Carbon::now()->toIso8601String(),
        ], 'Device registered');
    }

    public function bootstrap(Request $request, Tenant $tenant): JsonResponse
    {
        if ($guard = $this->guardTenant($request, $tenant)) {
            return $guard;
        }

        $user = $request->user();

        $pullable = $this->registry->pullableTablesFor($user);
        $pushable = $this->registry->pushableTablesFor($user);

        $tablesMeta = [];
        foreach ($pullable as $table) {
            $def = $this->registry->get($table) ?? [];
            $tablesMeta[$table] = [
                'pullable' => true,
                'pushable' => in_array($table, $pushable, true),
                'allowed_actions' => $def['allowed_actions'] ?? [],
                'dependencies' => $def['dependencies'] ?? [],
            ];
        }

        return $this->success([
            'tenant' => [
                'id' => $tenant->id,
                'slug' => $tenant->slug,
                'name' => $tenant->name ?? null,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'permissions' => method_exists($user, 'getAllPermissions')
                ? $user->getAllPermissions()->pluck('slug')->all()
                : [],
            'sync' => [
                'schema_version' => (int) config('mobile_sync.schema_version', 1),
                'server_time' => Carbon::now()->toIso8601String(),
                'pullable_tables' => $pullable,
                'pushable_tables' => $pushable,
                'tables' => $tablesMeta,
                'pull_limits' => [
                    'default_limit' => (int) config('mobile_sync.pull.default_limit', 200),
                    'max_limit' => (int) config('mobile_sync.pull.max_limit', 1000),
                ],
                'push_limits' => [
                    'max_mutations_per_request' => (int) config('mobile_sync.push.max_mutations_per_request', 200),
                ],
            ],
        ], 'Bootstrap');
    }

    public function pull(Request $request, Tenant $tenant): JsonResponse
    {
        if ($guard = $this->guardTenant($request, $tenant)) {
            return $guard;
        }

        $validator = Validator::make($request->all(), [
            'last_pulled_at' => 'sometimes|nullable|date',
            'tables' => 'sometimes|nullable|array',
            'tables.*' => 'string|max:64',
            'limit' => 'sometimes|nullable|integer|min:1',
            'device_uuid' => 'required|string|max:64',
        ]);
        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();
        $device = $this->resolveDevice($tenant, $user, $request->string('device_uuid')->toString());
        if (!$device) {
            return $this->forbidden('Unknown or revoked device. Re-register before syncing.');
        }

        $result = $this->pullService->pull(
            tenant: $tenant,
            user: $user,
            device: $device,
            lastPulledAt: $request->input('last_pulled_at'),
            requestedTables: $request->input('tables'),
            limit: $request->input('limit'),
        );

        return $this->success($result, 'Pull complete');
    }

    public function push(Request $request, Tenant $tenant): JsonResponse
    {
        if ($guard = $this->guardTenant($request, $tenant)) {
            return $guard;
        }

        $validator = Validator::make($request->all(), [
            'device_uuid' => 'required|string|max:64',
            'mutations' => 'required|array|min:1',
        ]);
        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();
        $device = $this->resolveDevice($tenant, $user, $request->string('device_uuid')->toString());
        if (!$device) {
            return $this->forbidden('Unknown or revoked device. Re-register before syncing.');
        }

        $result = $this->pushService->push(
            tenant: $tenant,
            user: $user,
            device: $device,
            mutations: $request->input('mutations', []),
        );

        return $this->success($result, 'Push complete');
    }

    public function status(Request $request, Tenant $tenant): JsonResponse
    {
        if ($guard = $this->guardTenant($request, $tenant)) {
            return $guard;
        }

        $user = $request->user();
        $deviceUuid = $request->query('device_uuid');
        $device = $deviceUuid
            ? $this->resolveDevice($tenant, $user, (string) $deviceUuid)
            : null;

        $pendingConflicts = \App\Models\MobileSyncConflict::query()
            ->where('tenant_id', $tenant->id)
            ->whereNull('resolved_at')
            ->when($device, fn ($q) => $q->where('mobile_device_id', $device->id))
            ->count();

        $failedMutations = MobileMutation::query()
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [MobileMutation::STATUS_FAILED, MobileMutation::STATUS_CONFLICT])
            ->when($device, fn ($q) => $q->where('mobile_device_id', $device->id))
            ->count();

        return $this->success([
            'server_time' => Carbon::now()->toIso8601String(),
            'schema_version' => (int) config('mobile_sync.schema_version', 1),
            'device' => $device ? [
                'id' => $device->id,
                'device_uuid' => $device->device_uuid,
                'last_synced_at' => optional($device->last_synced_at)->toIso8601String(),
                'last_seen_at' => optional($device->last_seen_at)->toIso8601String(),
                'revoked' => $device->revoked_at !== null,
            ] : null,
            'pending_conflicts' => $pendingConflicts,
            'failed_mutations' => $failedMutations,
        ], 'Sync status');
    }

    // ── Phase 2 endpoints ────────────────────────────────────────────────

    /**
     * Resolve a single sync conflict using server_wins | client_wins | merged.
     *
     * POST /sync/resolve-conflict
     *  body: { device_uuid, conflict_id, strategy, merged_payload? }
     */
    public function resolveConflict(Request $request, Tenant $tenant): JsonResponse
    {
        if ($guard = $this->guardTenant($request, $tenant)) {
            return $guard;
        }

        $validator = Validator::make($request->all(), [
            'device_uuid'     => 'required|string|max:64',
            'conflict_id'     => 'required|integer|min:1',
            'strategy'        => 'required|in:server_wins,client_wins,merged',
            'merged_payload'  => 'required_if:strategy,merged|array',
        ]);
        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();
        $device = $this->resolveDevice($tenant, $user, $request->string('device_uuid')->toString());
        if (!$device) {
            return $this->forbidden('Unknown or revoked device. Re-register before syncing.');
        }

        $conflict = MobileSyncConflict::query()
            ->where('tenant_id', $tenant->id)
            ->where('id', $request->integer('conflict_id'))
            ->whereNull('resolved_at')
            ->first();
        if (!$conflict) {
            return $this->notFound('Conflict not found or already resolved');
        }

        $strategy = $request->input('strategy');
        $def = $this->registry->get($conflict->table_name);
        $modelClass = $def['model'] ?? null;
        if (!$modelClass) {
            return $this->validationError(['table' => ['Unknown table']]);
        }

        $row = $modelClass::query()->where('sync_uuid', $conflict->record_sync_uuid)->first();
        if (!$row && $strategy !== 'server_wins') {
            return $this->notFound('Underlying record no longer exists');
        }

        $resolvedPayload = match ($strategy) {
            'server_wins' => $row?->toArray() ?? [],
            'client_wins' => $conflict->client_payload ?? [],
            'merged'      => $request->input('merged_payload', []),
        };

        if ($strategy !== 'server_wins' && $row) {
            $clean = $this->registry->stripProtectedAttributes($conflict->table_name, $resolvedPayload);
            $clean['last_modified_by_device_id'] = $device->device_uuid;
            $row->fill($clean)->save();
        }

        $conflict->forceFill([
            'resolved_at'         => now(),
            'resolved_by'         => $user->id,
            'resolution_strategy' => $strategy,
            'resolved_payload'    => $resolvedPayload,
        ])->save();

        return $this->success([
            'conflict_id' => $conflict->id,
            'strategy'    => $strategy,
            'record'      => $row?->fresh()?->toArray(),
        ], 'Conflict resolved');
    }

    /**
     * Generate (or fetch from cache) the official invoice PDF.
     *
     * GET /sync/documents/invoices/{invoiceSyncUuid}/pdf?device_uuid=...
     */
    public function invoicePdf(Request $request, Tenant $tenant, string $invoiceSyncUuid): JsonResponse
    {
        if ($guard = $this->guardTenant($request, $tenant)) {
            return $guard;
        }

        $user = $request->user();
        if (!$user->can('mobile.sync.read.invoices') && !$user->can('accounting.invoices.manage')) {
            return $this->forbidden('Missing permission to read invoices');
        }

        $deviceUuid = $request->query('device_uuid');
        if (!$deviceUuid) {
            return $this->validationError(['device_uuid' => ['device_uuid is required']]);
        }
        $device = $this->resolveDevice($tenant, $user, (string) $deviceUuid);
        if (!$device) {
            return $this->forbidden('Unknown or revoked device.');
        }

        $invoice = Voucher::query()
            ->where('tenant_id', $tenant->id)
            ->where('sync_uuid', $invoiceSyncUuid)
            ->first();
        if (!$invoice) {
            return $this->notFound('Invoice not found for this tenant');
        }

        try {
            $result = $this->documentExports->generateInvoicePdf($tenant, $invoice, $device);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice PDF: ' . $e->getMessage(),
            ], 500);
        }

        $export = $result['export'];
        return $this->success([
            'export_uuid'    => $export->export_uuid,
            'document_type'  => $export->document_type,
            'file_name'      => $export->file_name,
            'mime_type'      => $export->mime_type,
            'file_size'      => $export->file_size,
            'generated_at'   => optional($export->generated_at)->toIso8601String(),
            'expires_at'     => optional($export->expires_at)->toIso8601String(),
            'download_url'   => $result['download_url'],
            'voucher_number' => $invoice->voucher_number,
        ], 'Invoice PDF ready');
    }

    /**
     * Generate a customer ledger statement PDF snapshot.
     *
     * POST /sync/documents/customers/{customerSyncUuid}/statement
     *  body: { device_uuid, from_date, to_date }
     */
    public function customerStatement(Request $request, Tenant $tenant, string $customerSyncUuid): JsonResponse
    {
        if ($guard = $this->guardTenant($request, $tenant)) {
            return $guard;
        }

        $user = $request->user();
        if (!$user->can('crm.customers.statements') && !$user->can('mobile.sync.read')) {
            return $this->forbidden('Missing permission to read customer statements');
        }

        $validator = Validator::make($request->all(), [
            'device_uuid' => 'required|string|max:64',
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
        ]);
        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $device = $this->resolveDevice($tenant, $user, $request->string('device_uuid')->toString());
        if (!$device) {
            return $this->forbidden('Unknown or revoked device.');
        }

        $customer = Customer::query()
            ->where('tenant_id', $tenant->id)
            ->where('sync_uuid', $customerSyncUuid)
            ->first();
        if (!$customer) {
            return $this->notFound('Customer not found for this tenant');
        }

        try {
            $result = $this->statementService->generateStatement(
                $tenant, $customer,
                $request->input('from_date'),
                $request->input('to_date'),
                $device,
            );
        } catch (\Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate statement: ' . $e->getMessage(),
            ], 500);
        }

        $export = $result['export'];
        return $this->success([
            'export_uuid'   => $export->export_uuid,
            'document_type' => $export->document_type,
            'file_name'     => $export->file_name,
            'mime_type'     => $export->mime_type,
            'file_size'     => $export->file_size,
            'period_from'   => optional($export->period_from)->format('Y-m-d'),
            'period_to'     => optional($export->period_to)->format('Y-m-d'),
            'generated_at'  => optional($export->generated_at)->toIso8601String(),
            'expires_at'    => optional($export->expires_at)->toIso8601String(),
            'download_url'  => $result['download_url'],
            'summary'       => $result['statement_summary'],
        ], 'Customer statement ready');
    }

    /**
     * Signed download endpoint for previously-generated PDFs.
     * Public via signed URL — no auth middleware (verified by signature).
     *
     * GET /sync/documents/{export_uuid}/download
     */
    public function downloadDocument(Request $request, Tenant $tenant, string $exportUuid)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired download link'], 403);
        }

        $export = MobileDocumentExport::query()
            ->where('tenant_id', $tenant->id)
            ->where('export_uuid', $exportUuid)
            ->where('status', MobileDocumentExport::STATUS_READY)
            ->first();

        if (!$export) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        if ($export->expires_at && $export->expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'Document has expired'], 410);
        }

        $disk = Storage::disk($export->disk ?: 'local');
        if (!$disk->exists($export->storage_path)) {
            return response()->json(['success' => false, 'message' => 'Document file missing'], 404);
        }

        $export->forceFill(['downloaded_at' => now()])->save();

        $contents = $disk->get($export->storage_path);

        return response($contents, 200, [
            'Content-Type'        => $export->mime_type ?: 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $export->file_name . '"',
            'Content-Length'      => strlen($contents),
        ]);
    }

    private function resolveDevice(Tenant $tenant, $user, string $deviceUuid): ?MobileDevice
    {
        $device = MobileDevice::query()
            ->where('tenant_id', $tenant->id)
            ->where('user_id', $user->id)
            ->where('device_uuid', $deviceUuid)
            ->first();

        if (!$device || !$device->isActive()) {
            return null;
        }

        return $device;
    }

    /**
     * Defense-in-depth: ensure the {tenant} URL slug actually belongs to
     * the authenticated user.
     */
    private function guardTenant(Request $request, Tenant $tenant): ?JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->unauthorized();
        }

        if (property_exists($user, 'tenant_id') || isset($user->tenant_id)) {
            $userTenantId = (int) $user->tenant_id;
            if ($userTenantId !== 0 && $userTenantId !== (int) $tenant->id) {
                return $this->forbidden('Tenant does not match authenticated user');
            }
        }

        return null;
    }
}
