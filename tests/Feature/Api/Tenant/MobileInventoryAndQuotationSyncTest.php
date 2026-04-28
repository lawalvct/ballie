<?php

namespace Tests\Feature\Api\Tenant;

use App\Models\AccountGroup;
use App\Models\Customer;
use App\Models\LedgerAccount;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Quotation;
use App\Models\Tenant;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Phase 3 mobile-sync coverage:
 *   - Pushing offline-created units / product_categories / products goes
 *     through the generic master-data path and creates rows linked by
 *     sync_uuid.
 *   - Stock-derived fields (current_stock, opening_stock, etc.) on
 *     products are stripped from the push payload by SyncRegistry.
 *   - Pushing an offline quotation creates the Quotation + items, with
 *     server-assigned quotation_number and recalculated totals via the
 *     custom QuotationSyncService handler.
 *   - The same quotation mutation pushed twice is idempotent.
 */
class MobileInventoryAndQuotationSyncTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;
    private Customer $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create(['is_active' => true]);
        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->seed(\Database\Seeders\MobileSyncPermissionsSeeder::class);
        $role = Role::firstOrCreate(
            ['slug' => 'mobile-sync-tester', 'tenant_id' => $this->tenant->id],
            ['name' => 'Mobile Sync Tester', 'guard_name' => 'web', 'is_active' => true],
        );
        Permission::query()
            ->whereIn('slug', [
                'mobile.sync.read',
                'mobile.sync.read.invoices',
                'mobile.sync.write.crm',
                'mobile.sync.write.inventory',
                'mobile.sync.write.invoices',
            ])->get()
            ->each(fn ($p) => $role->permissions()->syncWithoutDetaching([$p->id]));
        $this->user->roles()->syncWithoutDetaching([$role->id]);

        $this->bootstrap();

        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function pushing_a_new_unit_creates_unit_row_with_sync_uuid(): void
    {
        $this->registerDevice();
        $unitSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'unit-cm-1',
                'table' => 'units',
                'action' => 'create',
                'sync_uuid' => $unitSyncUuid,
                'data' => [
                    'name' => 'Box of 12',
                    'symbol' => 'box12',
                    'is_active' => true,
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('applied', $response->json('data.results.0.status'));
        $this->assertDatabaseHas('units', [
            'tenant_id' => $this->tenant->id,
            'sync_uuid' => $unitSyncUuid,
            'name' => 'Box of 12',
            'symbol' => 'box12',
        ]);
    }

    /** @test */
    public function pushing_a_new_product_category_creates_row_with_sync_uuid(): void
    {
        $this->registerDevice();
        $catSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'cat-cm-1',
                'table' => 'product_categories',
                'action' => 'create',
                'sync_uuid' => $catSyncUuid,
                'data' => [
                    'name' => 'Beverages',
                    'description' => 'Drinks and water',
                    'is_active' => true,
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('applied', $response->json('data.results.0.status'));
        $this->assertDatabaseHas('product_categories', [
            'tenant_id' => $this->tenant->id,
            'sync_uuid' => $catSyncUuid,
            'name' => 'Beverages',
        ]);
    }

    /** @test */
    public function pushing_a_product_strips_protected_stock_fields(): void
    {
        $this->registerDevice();
        $productSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'prod-cm-1',
                'table' => 'products',
                'action' => 'create',
                'sync_uuid' => $productSyncUuid,
                'data' => [
                    'name' => 'Mobile-created widget',
                    'sku' => 'MOB-001',
                    'sales_price' => 250.00,
                    'is_active' => true,
                    'is_saleable' => true,
                    // These must be ignored by the server.
                    'current_stock' => 9999,
                    'opening_stock' => 9999,
                    'last_sale_price' => 9999,
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('applied', $response->json('data.results.0.status'));

        $row = Product::where('sync_uuid', $productSyncUuid)->firstOrFail();
        $this->assertSame('Mobile-created widget', $row->name);
        // Server-derived fields must NOT have been overwritten with 9999.
        $this->assertNotSame(9999, (int) ($row->current_stock ?? 0));
        $this->assertNotSame(9999, (int) ($row->opening_stock ?? 0));
    }

    /** @test */
    public function pushing_an_offline_quotation_creates_parent_and_items(): void
    {
        $this->registerDevice();
        $quotationSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'quot-cm-1',
                'table' => 'quotations',
                'action' => 'create',
                'sync_uuid' => $quotationSyncUuid,
                'data' => [
                    'quotation_date' => now()->toDateString(),
                    'expiry_date' => now()->addDays(7)->toDateString(),
                    'customer_sync_uuid' => $this->customer->sync_uuid,
                    'subject' => 'Offline mobile quote',
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 3,
                        'rate' => 1000,
                        'discount' => 0,
                    ]],
                    'client_quotation_number' => 'LOCAL-Q-1',
                ],
            ]],
        ]);

        $response->assertOk();
        $result = $response->json('data.results.0');
        $this->assertSame('applied', $result['status']);
        $this->assertNotEmpty($result['server_response']['official_quotation_number']);
        $this->assertSame($quotationSyncUuid, $result['server_response']['sync_uuid']);

        $quotation = Quotation::where('sync_uuid', $quotationSyncUuid)->firstOrFail();
        $this->assertSame((float) $quotation->total_amount, 3000.0);
        $this->assertSame('draft', $quotation->status);
        $this->assertCount(1, $quotation->items);
    }

    /** @test */
    public function pushing_same_quotation_mutation_twice_is_idempotent(): void
    {
        $this->registerDevice();
        $quotationSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $payload = [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'quot-cm-2',
                'table' => 'quotations',
                'action' => 'create',
                'sync_uuid' => $quotationSyncUuid,
                'data' => [
                    'quotation_date' => now()->toDateString(),
                    'customer_sync_uuid' => $this->customer->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 1,
                        'rate' => 100,
                    ]],
                ],
            ]],
        ];

        $this->postJson($this->url('/sync/push'), $payload)->assertOk();
        $this->postJson($this->url('/sync/push'), $payload)->assertOk();

        $this->assertSame(1, Quotation::where('sync_uuid', $quotationSyncUuid)->count());
    }

    /** @test */
    public function pushing_quotation_without_party_or_items_fails(): void
    {
        $this->registerDevice();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'quot-cm-bad',
                'table' => 'quotations',
                'action' => 'create',
                'sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'data' => [
                    'quotation_date' => now()->toDateString(),
                    // no customer_sync_uuid / vendor_sync_uuid / customer_ledger_id
                    'items' => [],
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('failed', $response->json('data.results.0.status'));
    }

    // ── helpers ──────────────────────────────────────────────────────────

    private function bootstrap(): void
    {
        $assets = AccountGroup::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Sundry Debtors',
            'code' => 'AR',
            'nature' => 'asset',
        ]);

        $customerLedger = LedgerAccount::create([
            'tenant_id' => $this->tenant->id,
            'account_group_id' => $assets->id,
            'name' => 'Test Customer',
            'code' => 'CUST-001',
            'opening_balance' => 0,
            'opening_balance_type' => 'debit',
        ]);

        $this->customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'ledger_account_id' => $customerLedger->id,
        ]);

        $this->product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
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
