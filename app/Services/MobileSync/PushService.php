<?php

namespace App\Services\MobileSync;

use App\Models\MobileDevice;
use App\Models\MobileMutation;
use App\Models\MobileSyncConflict;
use App\Models\SyncTombstone;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Applies a batch of offline mutations from a mobile device.
 *
 * Phase 1 scope: master-data tables only (customers, vendors, units,
 * product_categories, products — descriptive fields only). Documents
 * (vouchers, invoices) are deferred to Phase 2 where they will reuse
 * the existing invoice/voucher posting services.
 */
class PushService
{
    public function __construct(private SyncRegistry $registry)
    {
    }

    /**
     * @param  array<int, array<string, mixed>>  $mutations
     * @return array{results: array<int, array<string, mixed>>, applied_count: int, conflict_count: int, failed_count: int}
     */
    public function push(Tenant $tenant, User $user, MobileDevice $device, array $mutations): array
    {
        $maxBatch = (int) config('mobile_sync.push.max_mutations_per_request', 200);
        if (count($mutations) > $maxBatch) {
            $mutations = array_slice($mutations, 0, $maxBatch);
        }

        $results = [];
        $applied = $conflict = $failed = 0;

        foreach ($mutations as $raw) {
            $result = $this->applyOne($tenant, $user, $device, $raw);
            $results[] = $result;
            match ($result['status']) {
                MobileMutation::STATUS_APPLIED => $applied++,
                MobileMutation::STATUS_CONFLICT => $conflict++,
                MobileMutation::STATUS_SKIPPED => $applied++, // dedup hit on idempotency, treated as success
                default => $failed++,
            };
        }

        $device->forceFill(['last_seen_at' => now()])->save();

        return [
            'results' => $results,
            'applied_count' => $applied,
            'conflict_count' => $conflict,
            'failed_count' => $failed,
        ];
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function applyOne(Tenant $tenant, User $user, MobileDevice $device, array $raw): array
    {
        $envelope = $this->validateEnvelope($raw);
        if ($envelope['errors']) {
            return $this->failResult(
                clientMutationId: $raw['client_mutation_id'] ?? null,
                table: $raw['table'] ?? null,
                syncUuid: $raw['sync_uuid'] ?? null,
                code: 'invalid_envelope',
                message: 'Invalid mutation envelope',
                errors: $envelope['errors'],
            );
        }

        $clientMutationId = $envelope['client_mutation_id'];
        $table = $envelope['table'];
        $action = $envelope['action'];
        $syncUuid = $envelope['sync_uuid'];
        $payload = $envelope['data'];
        $baseVersion = $envelope['base_server_version'];

        // Idempotency: short-circuit if we have already processed this mutation id.
        $existing = MobileMutation::query()
            ->where('tenant_id', $tenant->id)
            ->where('device_uuid', $device->device_uuid)
            ->where('client_mutation_id', $clientMutationId)
            ->first();

        if ($existing && $existing->status === MobileMutation::STATUS_APPLIED) {
            return [
                'client_mutation_id' => $clientMutationId,
                'table' => $table,
                'sync_uuid' => $existing->record_sync_uuid,
                'status' => MobileMutation::STATUS_SKIPPED,
                'server_response' => $existing->server_response,
            ];
        }

        if (!$this->registry->canPush($user, $table, $action)) {
            return $this->persistAndReturn(
                tenant: $tenant, user: $user, device: $device,
                clientMutationId: $clientMutationId, table: $table,
                action: $action, syncUuid: $syncUuid, baseVersion: $baseVersion,
                payload: $payload, status: MobileMutation::STATUS_FAILED,
                code: 'forbidden', message: 'Permission or table not pushable',
            );
        }

        $def = $this->registry->get($table);
        $modelClass = Arr::get($def, 'model');

        try {
            return DB::transaction(function () use (
                $tenant, $user, $device, $clientMutationId, $table, $action,
                $syncUuid, $baseVersion, $payload, $modelClass, $def
            ) {
                /** @var class-string<Model> $modelClass */
                $row = $modelClass::query()->where('sync_uuid', $syncUuid)->lockForUpdate()->first();

                if ($action === MobileMutation::ACTION_CREATE) {
                    if ($row) {
                        // Already exists — treat as idempotent update if same tenant.
                        if ($this->mismatchesTenant($row, $def, $tenant)) {
                            return $this->persistAndReturn(
                                tenant: $tenant, user: $user, device: $device,
                                clientMutationId: $clientMutationId, table: $table,
                                action: $action, syncUuid: $syncUuid, baseVersion: $baseVersion,
                                payload: $payload, status: MobileMutation::STATUS_FAILED,
                                code: 'tenant_mismatch', message: 'Record belongs to another tenant',
                            );
                        }
                        return $this->doUpdate(
                            tenant: $tenant, user: $user, device: $device, row: $row,
                            clientMutationId: $clientMutationId, table: $table,
                            syncUuid: $syncUuid, baseVersion: $baseVersion,
                            payload: $payload, def: $def,
                        );
                    }

                    $validation = $this->validatePayload($table, $action, $payload);
                    if ($validation) {
                        return $this->persistAndReturn(
                            tenant: $tenant, user: $user, device: $device,
                            clientMutationId: $clientMutationId, table: $table,
                            action: $action, syncUuid: $syncUuid, baseVersion: $baseVersion,
                            payload: $payload, status: MobileMutation::STATUS_FAILED,
                            code: 'validation_failed', message: 'Validation failed',
                            errors: $validation,
                        );
                    }

                    $clean = $this->registry->stripProtectedAttributes($table, $payload);
                    $clean['sync_uuid'] = $syncUuid;
                    if (Arr::get($def, 'tenant_scoped')) {
                        $clean['tenant_id'] = $tenant->id;
                    }
                    if (in_array('created_by', (new $modelClass)->getFillable(), true)) {
                        $clean['created_by'] = $user->id;
                    }
                    $clean['last_modified_by_device_id'] = $device->device_uuid;

                    /** @var Model $created */
                    $created = $modelClass::query()->create($clean);

                    return $this->persistAndReturn(
                        tenant: $tenant, user: $user, device: $device,
                        clientMutationId: $clientMutationId, table: $table,
                        action: $action, syncUuid: $syncUuid, baseVersion: $baseVersion,
                        payload: $payload, status: MobileMutation::STATUS_APPLIED,
                        serverResponse: [
                            'server_id' => $created->getKey(),
                            'server_version' => $created->getAttribute('server_version'),
                            'updated_at' => optional($created->updated_at)->toIso8601String(),
                        ],
                    );
                }

                if (!$row) {
                    return $this->persistAndReturn(
                        tenant: $tenant, user: $user, device: $device,
                        clientMutationId: $clientMutationId, table: $table,
                        action: $action, syncUuid: $syncUuid, baseVersion: $baseVersion,
                        payload: $payload, status: MobileMutation::STATUS_FAILED,
                        code: 'record_not_found', message: 'Record not found on server',
                    );
                }

                if ($this->mismatchesTenant($row, $def, $tenant)) {
                    return $this->persistAndReturn(
                        tenant: $tenant, user: $user, device: $device,
                        clientMutationId: $clientMutationId, table: $table,
                        action: $action, syncUuid: $syncUuid, baseVersion: $baseVersion,
                        payload: $payload, status: MobileMutation::STATUS_FAILED,
                        code: 'tenant_mismatch', message: 'Record belongs to another tenant',
                    );
                }

                if ($action === MobileMutation::ACTION_DELETE) {
                    return $this->doDelete(
                        tenant: $tenant, user: $user, device: $device, row: $row,
                        clientMutationId: $clientMutationId, table: $table,
                        syncUuid: $syncUuid, baseVersion: $baseVersion,
                    );
                }

                return $this->doUpdate(
                    tenant: $tenant, user: $user, device: $device, row: $row,
                    clientMutationId: $clientMutationId, table: $table,
                    syncUuid: $syncUuid, baseVersion: $baseVersion,
                    payload: $payload, def: $def,
                );
            });
        } catch (\Throwable $e) {
            report($e);
            return $this->persistAndReturn(
                tenant: $tenant, user: $user, device: $device,
                clientMutationId: $clientMutationId, table: $table,
                action: $action, syncUuid: $syncUuid, baseVersion: $baseVersion,
                payload: $payload, status: MobileMutation::STATUS_FAILED,
                code: 'server_error', message: $e->getMessage(),
            );
        }
    }

    private function doUpdate(
        Tenant $tenant, User $user, MobileDevice $device, Model $row,
        string $clientMutationId, string $table, string $syncUuid,
        ?int $baseVersion, array $payload, array $def,
    ): array {
        $serverVersion = (int) ($row->getAttribute('server_version') ?? 0);
        if ($baseVersion !== null && $serverVersion > $baseVersion) {
            $conflict = MobileSyncConflict::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'mobile_device_id' => $device->id,
                'client_mutation_id' => $clientMutationId,
                'table_name' => $table,
                'record_sync_uuid' => $syncUuid,
                'conflict_type' => 'version_mismatch',
                'client_payload' => $payload,
                'server_payload' => $row->toArray(),
            ]);

            return $this->persistAndReturn(
                tenant: $tenant, user: $user, device: $device,
                clientMutationId: $clientMutationId, table: $table,
                action: MobileMutation::ACTION_UPDATE, syncUuid: $syncUuid,
                baseVersion: $baseVersion, payload: $payload,
                status: MobileMutation::STATUS_CONFLICT,
                code: 'version_mismatch',
                message: 'Server has a newer version of this record',
                serverResponse: [
                    'conflict_id' => $conflict->id,
                    'server' => $row->toArray(),
                ],
            );
        }

        $validation = $this->validatePayload($table, MobileMutation::ACTION_UPDATE, $payload);
        if ($validation) {
            return $this->persistAndReturn(
                tenant: $tenant, user: $user, device: $device,
                clientMutationId: $clientMutationId, table: $table,
                action: MobileMutation::ACTION_UPDATE, syncUuid: $syncUuid,
                baseVersion: $baseVersion, payload: $payload,
                status: MobileMutation::STATUS_FAILED,
                code: 'validation_failed', message: 'Validation failed',
                errors: $validation,
            );
        }

        $clean = $this->registry->stripProtectedAttributes($table, $payload);
        $clean['last_modified_by_device_id'] = $device->device_uuid;
        if (in_array('updated_by', $row->getFillable(), true)) {
            $clean['updated_by'] = $user->id;
        }

        $row->fill($clean)->save();

        return $this->persistAndReturn(
            tenant: $tenant, user: $user, device: $device,
            clientMutationId: $clientMutationId, table: $table,
            action: MobileMutation::ACTION_UPDATE, syncUuid: $syncUuid,
            baseVersion: $baseVersion, payload: $payload,
            status: MobileMutation::STATUS_APPLIED,
            serverResponse: [
                'server_id' => $row->getKey(),
                'server_version' => $row->getAttribute('server_version'),
                'updated_at' => optional($row->updated_at)->toIso8601String(),
            ],
        );
    }

    private function doDelete(
        Tenant $tenant, User $user, MobileDevice $device, Model $row,
        string $clientMutationId, string $table, string $syncUuid, ?int $baseVersion,
    ): array {
        $serverId = $row->getKey();
        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($row), true);

        if ($usesSoftDeletes && in_array('deleted_by', $row->getFillable(), true)) {
            $row->forceFill(['deleted_by' => $user->id])->save();
        }

        $row->delete();

        SyncTombstone::record(
            tenantId: $tenant->id,
            tableName: $table,
            syncUuid: $syncUuid,
            serverId: $serverId,
            deletedBy: $user->id,
            reason: 'mobile_push',
        );

        return $this->persistAndReturn(
            tenant: $tenant, user: $user, device: $device,
            clientMutationId: $clientMutationId, table: $table,
            action: MobileMutation::ACTION_DELETE, syncUuid: $syncUuid,
            baseVersion: $baseVersion, payload: [],
            status: MobileMutation::STATUS_APPLIED,
            serverResponse: ['server_id' => $serverId, 'deleted' => true],
        );
    }

    private function mismatchesTenant(Model $row, array $def, Tenant $tenant): bool
    {
        if (Arr::get($def, 'tenant_scoped')) {
            return (int) $row->getAttribute('tenant_id') !== (int) $tenant->id;
        }
        if ($parentVia = Arr::get($def, 'parent_via')) {
            $relation = $parentVia['relation'] ?? null;
            if ($relation && $row->{$relation}) {
                return (int) $row->{$relation}->getAttribute('tenant_id') !== (int) $tenant->id;
            }
        }
        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function validateEnvelope(array $raw): array
    {
        $rules = [
            'client_mutation_id' => 'required|string|max:64',
            'table' => 'required|string|max:64',
            'action' => 'required|in:create,update,delete',
            'sync_uuid' => 'required|uuid',
            'data' => 'sometimes|array',
            'base_server_version' => 'sometimes|nullable|integer|min:0',
        ];

        $validator = Validator::make($raw, $rules);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()->toArray()];
        }

        return [
            'errors' => null,
            'client_mutation_id' => $raw['client_mutation_id'],
            'table' => $raw['table'],
            'action' => $raw['action'],
            'sync_uuid' => $raw['sync_uuid'],
            'data' => $raw['data'] ?? [],
            'base_server_version' => $raw['base_server_version'] ?? null,
        ];
    }

    /**
     * @return array<string, array>|null
     */
    private function validatePayload(string $table, string $action, array $payload): ?array
    {
        // Phase 1: lightweight validation. Heavy domain rules (invoice
        // totals, stock effects) belong to Phase 2 services.
        $rules = match ($table) {
            'customers' => [
                'first_name' => $action === 'create' ? 'sometimes|string|max:255' : 'sometimes|string|max:255',
                'email' => 'sometimes|nullable|email|max:255',
                'phone' => 'sometimes|nullable|string|max:50',
            ],
            'vendors' => [
                'name' => $action === 'create' ? 'sometimes|string|max:255' : 'sometimes|string|max:255',
                'email' => 'sometimes|nullable|email|max:255',
            ],
            'products' => [
                'name' => 'sometimes|string|max:255',
                'sku' => 'sometimes|nullable|string|max:100',
            ],
            'units' => [
                'name' => 'sometimes|string|max:100',
            ],
            'product_categories' => [
                'name' => 'sometimes|string|max:255',
            ],
            default => [],
        };

        if (empty($rules)) {
            return null;
        }

        $validator = Validator::make($payload, $rules);
        return $validator->fails() ? $validator->errors()->toArray() : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function persistAndReturn(
        Tenant $tenant, User $user, MobileDevice $device,
        string $clientMutationId, string $table, string $action,
        string $syncUuid, ?int $baseVersion, array $payload,
        string $status,
        ?string $code = null, ?string $message = null,
        ?array $errors = null, ?array $serverResponse = null,
    ): array {
        $payloadHash = hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR));
        $response = $serverResponse ?? [];
        if ($errors) {
            $response['errors'] = $errors;
        }

        MobileMutation::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'device_uuid' => $device->device_uuid,
                'client_mutation_id' => $clientMutationId,
            ],
            [
                'user_id' => $user->id,
                'mobile_device_id' => $device->id,
                'table_name' => $table,
                'record_sync_uuid' => $syncUuid,
                'action' => $action,
                'base_server_version' => $baseVersion,
                'payload_hash' => $payloadHash,
                'payload' => $payload,
                'server_response' => $response,
                'status' => $status,
                'error_code' => $code,
                'error_message' => $message,
                'processed_at' => now(),
            ]
        );

        return [
            'client_mutation_id' => $clientMutationId,
            'table' => $table,
            'sync_uuid' => $syncUuid,
            'status' => $status,
            'error_code' => $code,
            'error_message' => $message,
            'errors' => $errors,
            'server_response' => $response,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function failResult(
        ?string $clientMutationId, ?string $table, ?string $syncUuid,
        string $code, string $message, ?array $errors = null,
    ): array {
        return [
            'client_mutation_id' => $clientMutationId,
            'table' => $table,
            'sync_uuid' => $syncUuid,
            'status' => MobileMutation::STATUS_FAILED,
            'error_code' => $code,
            'error_message' => $message,
            'errors' => $errors,
            'server_response' => null,
        ];
    }
}
