<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\MobileDevice;
use App\Models\MobileMutation;
use App\Models\Tenant;
use App\Services\MobileSync\PullService;
use App\Services\MobileSync\PushService;
use App\Services\MobileSync\SyncRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
