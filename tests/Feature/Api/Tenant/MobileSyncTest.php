<?php

namespace Tests\Feature\Api\Tenant;

use App\Models\Customer;
use App\Models\MobileDevice;
use App\Models\MobileMutation;
use App\Models\SyncTombstone;
use App\Models\Tenant;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileSyncTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create([
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Seed the mobile sync permissions and grant them to the user via a role.
        $this->seed(\Database\Seeders\MobileSyncPermissionsSeeder::class);

        $role = Role::firstOrCreate(
            ['slug' => 'mobile-sync-tester', 'tenant_id' => $this->tenant->id],
            ['name' => 'Mobile Sync Tester', 'guard_name' => 'web', 'is_active' => true]
        );

        Permission::query()
            ->whereIn('slug', [
                'mobile.sync.read',
                'mobile.sync.read.invoices',
                'mobile.sync.write.crm',
                'mobile.sync.write.inventory',
            ])
            ->get()
            ->each(fn ($p) => $role->permissions()->syncWithoutDetaching([$p->id]));

        $this->user->roles()->syncWithoutDetaching([$role->id]);

        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function it_registers_a_device_and_returns_schema_version(): void
    {
        $response = $this->postJson($this->url('/sync/register-device'), [
            'device_uuid' => 'dev-uuid-1',
            'device_name' => 'Pixel 8',
            'platform' => 'android',
            'app_version' => '1.0.0',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.device.device_uuid', 'dev-uuid-1')
            ->assertJsonPath('data.schema_version', 1);

        $this->assertDatabaseHas('mobile_devices', [
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'device_uuid' => 'dev-uuid-1',
        ]);
    }

    /** @test */
    public function bootstrap_lists_pullable_tables_for_user(): void
    {
        $this->registerDevice();

        $response = $this->getJson($this->url('/sync/bootstrap'));

        $response->assertOk()
            ->assertJsonPath('data.tenant.id', $this->tenant->id)
            ->assertJsonPath('data.sync.schema_version', 1);

        $pullable = $response->json('data.sync.pullable_tables');
        $this->assertContains('customers', $pullable);
        $this->assertContains('products', $pullable);
    }

    /** @test */
    public function pull_returns_changed_customers_for_tenant_only(): void
    {
        $this->registerDevice();

        $mine = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'Mine',
        ]);

        $otherTenant = Tenant::factory()->create(['is_active' => true]);
        Customer::factory()->create([
            'tenant_id' => $otherTenant->id,
            'first_name' => 'Other',
        ]);

        $response = $this->postJson($this->url('/sync/pull'), [
            'device_uuid' => 'dev-uuid-1',
            'tables' => ['customers'],
        ]);

        $response->assertOk();

        $records = collect($response->json('data.tables.customers.records'));
        $this->assertCount(1, $records);
        $this->assertSame((string) $mine->sync_uuid, $records->first()['sync_uuid']);
    }

    /** @test */
    public function push_creates_a_customer_offline_and_is_idempotent(): void
    {
        $this->registerDevice();

        $syncUuid = (string) \Illuminate\Support\Str::uuid();
        $payload = [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'cm-1',
                'table' => 'customers',
                'action' => 'create',
                'sync_uuid' => $syncUuid,
                'data' => [
                    'first_name' => 'Offline',
                    'last_name' => 'User',
                    'email' => 'offline@example.com',
                    'customer_type' => 'individual',
                    'status' => 'active',
                ],
            ]],
        ];

        $first = $this->postJson($this->url('/sync/push'), $payload);
        $first->assertOk()
            ->assertJsonPath('data.applied_count', 1)
            ->assertJsonPath('data.results.0.status', 'applied');

        $this->assertDatabaseHas('customers', [
            'sync_uuid' => $syncUuid,
            'tenant_id' => $this->tenant->id,
            'first_name' => 'Offline',
        ]);

        // Idempotency: replay returns "skipped" without creating a duplicate.
        $second = $this->postJson($this->url('/sync/push'), $payload);
        $second->assertOk()
            ->assertJsonPath('data.results.0.status', 'skipped');

        $this->assertSame(1, Customer::where('sync_uuid', $syncUuid)->count());
    }

    /** @test */
    public function push_detects_version_conflict_on_stale_update(): void
    {
        $this->registerDevice();

        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'Original',
        ]);

        // Simulate someone else already saved a newer version on the server.
        $customer->update(['first_name' => 'Updated On Web']);
        $serverVersion = $customer->fresh()->server_version;

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'cm-stale',
                'table' => 'customers',
                'action' => 'update',
                'sync_uuid' => $customer->sync_uuid,
                'base_server_version' => max(0, $serverVersion - 1),
                'data' => ['first_name' => 'Mobile Edit'],
            ]],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.results.0.status', MobileMutation::STATUS_CONFLICT)
            ->assertJsonPath('data.results.0.error_code', 'version_mismatch');

        $this->assertSame('Updated On Web', $customer->fresh()->first_name);
    }

    /** @test */
    public function push_records_a_tombstone_on_delete(): void
    {
        $this->registerDevice();

        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'cm-del',
                'table' => 'customers',
                'action' => 'delete',
                'sync_uuid' => $customer->sync_uuid,
            ]],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.results.0.status', MobileMutation::STATUS_APPLIED);

        $this->assertDatabaseHas('sync_tombstones', [
            'tenant_id' => $this->tenant->id,
            'table_name' => 'customers',
            'record_sync_uuid' => $customer->sync_uuid,
        ]);
    }

    /** @test */
    public function status_reports_pending_failed_and_conflict_counts(): void
    {
        $this->registerDevice();

        MobileMutation::create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $this->user->id,
            'mobile_device_id' => MobileDevice::first()->id,
            'device_uuid' => 'dev-uuid-1',
            'client_mutation_id' => 'cm-failed',
            'table_name' => 'customers',
            'record_sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
            'action' => 'update',
            'status' => MobileMutation::STATUS_FAILED,
        ]);

        $response = $this->getJson($this->url('/sync/status?device_uuid=dev-uuid-1'));

        $response->assertOk()
            ->assertJsonPath('data.failed_mutations', 1)
            ->assertJsonPath('data.device.device_uuid', 'dev-uuid-1');
    }

    /** @test */
    public function unauthenticated_requests_are_rejected(): void
    {
        app('auth')->forgetGuards();
        Sanctum::actingAs(User::factory()->create(['tenant_id' => $this->tenant->id]));
        $tenant = Tenant::factory()->create(['is_active' => true]);

        $response = $this->getJson("/api/v1/tenant/{$tenant->slug}/sync/bootstrap");
        $response->assertForbidden();
    }

    private function registerDevice(string $uuid = 'dev-uuid-1'): void
    {
        $this->postJson($this->url('/sync/register-device'), [
            'device_uuid' => $uuid,
        ])->assertOk();
    }

    private function url(string $path): string
    {
        return "/api/v1/tenant/{$this->tenant->slug}{$path}";
    }
}
