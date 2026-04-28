<?php

namespace Tests\Feature\Api\Tenant;

use App\Models\AccountGroup;
use App\Models\Customer;
use App\Models\LedgerAccount;
use App\Models\MobileDevice;
use App\Models\MobileDocumentExport;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Phase 2 mobile-sync coverage:
 *   - Pushing an offline-created invoice (vouchers) creates Voucher +
 *     items + balanced voucher_entries, assigns an official voucher
 *     number and returns it in server_response.
 *   - Invoice PDF endpoint returns a signed download URL after sync.
 *   - Customer statement endpoint respects tenant scope and date range.
 */
class MobileInvoiceSyncTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;
    private VoucherType $salesVoucherType;
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

        $this->bootstrapAccounts();

        Sanctum::actingAs($this->user);
        Storage::fake('local');
    }

    /** @test */
    public function pushing_an_offline_invoice_creates_voucher_and_balanced_entries(): void
    {
        $this->registerDevice();

        $invoiceSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'inv-cm-1',
                'table' => 'vouchers',
                'action' => 'create',
                'sync_uuid' => $invoiceSyncUuid,
                'data' => [
                    'voucher_type_id' => $this->salesVoucherType->id,
                    'voucher_date' => now()->toDateString(),
                    'customer_sync_uuid' => $this->customer->sync_uuid,
                    'narration' => 'Offline mobile sale',
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 2,
                        'rate' => 1500,
                        'discount' => 0,
                    ]],
                    'status' => 'draft',
                    'client_voucher_number' => 'LOCAL-1',
                ],
            ]],
        ]);

        $response->assertOk();
        $result = $response->json('data.results.0');
        $this->assertSame('applied', $result['status']);
        $this->assertNotEmpty($result['server_response']['official_voucher_number']);
        $this->assertSame($invoiceSyncUuid, $result['server_response']['sync_uuid']);

        $voucher = Voucher::where('sync_uuid', $invoiceSyncUuid)->firstOrFail();
        $this->assertSame((float) $voucher->total_amount, 3000.0);
        $this->assertSame('draft', $voucher->status);

        $entries = $voucher->entries;
        $this->assertGreaterThanOrEqual(2, $entries->count());
        $this->assertEquals(
            (float) $entries->sum('debit_amount'),
            (float) $entries->sum('credit_amount'),
            'Voucher entries must be balanced',
        );
    }

    /** @test */
    public function pushing_same_mutation_twice_is_idempotent(): void
    {
        $this->registerDevice();

        $invoiceSyncUuid = (string) \Illuminate\Support\Str::uuid();
        $payload = [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'inv-cm-2',
                'table' => 'vouchers',
                'action' => 'create',
                'sync_uuid' => $invoiceSyncUuid,
                'data' => [
                    'voucher_type_id' => $this->salesVoucherType->id,
                    'voucher_date' => now()->toDateString(),
                    'customer_sync_uuid' => $this->customer->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 1,
                        'rate' => 100,
                    ]],
                    'status' => 'draft',
                ],
            ]],
        ];

        $this->postJson($this->url('/sync/push'), $payload)->assertOk();
        $this->postJson($this->url('/sync/push'), $payload)->assertOk();

        $this->assertSame(1, Voucher::where('sync_uuid', $invoiceSyncUuid)->count());
    }

    /** @test */
    public function invoice_pdf_endpoint_returns_signed_download_url(): void
    {
        $this->registerDevice();
        $invoiceSyncUuid = $this->createSyncedInvoice();

        $response = $this->getJson(
            $this->url('/sync/documents/invoices/' . $invoiceSyncUuid . '/pdf?device_uuid=dev-uuid-1')
        );

        $response->assertOk()
            ->assertJsonPath('data.document_type', MobileDocumentExport::TYPE_INVOICE_PDF)
            ->assertJsonStructure(['data' => ['export_uuid', 'download_url', 'voucher_number']]);

        $this->assertDatabaseHas('mobile_document_exports', [
            'tenant_id' => $this->tenant->id,
            'document_type' => MobileDocumentExport::TYPE_INVOICE_PDF,
            'source_sync_uuid' => $invoiceSyncUuid,
            'status' => MobileDocumentExport::STATUS_READY,
        ]);
    }

    /** @test */
    public function invoice_pdf_returns_404_for_unknown_voucher(): void
    {
        $this->registerDevice();

        $response = $this->getJson(
            $this->url('/sync/documents/invoices/' . \Illuminate\Support\Str::uuid() . '/pdf?device_uuid=dev-uuid-1')
        );

        $response->assertNotFound();
    }

    /** @test */
    public function customer_statement_endpoint_returns_signed_url_and_summary(): void
    {
        $this->registerDevice();

        $response = $this->postJson(
            $this->url('/sync/documents/customers/' . $this->customer->sync_uuid . '/statement'),
            [
                'device_uuid' => 'dev-uuid-1',
                'from_date'   => now()->startOfMonth()->toDateString(),
                'to_date'     => now()->endOfMonth()->toDateString(),
            ],
        );

        $response->assertOk()
            ->assertJsonPath('data.document_type', MobileDocumentExport::TYPE_CUSTOMER_STATEMENT)
            ->assertJsonStructure(['data' => ['summary' => ['opening_balance', 'closing_balance', 'transaction_count']]]);
    }

    /** @test */
    public function customer_statement_rejects_other_tenants_customer(): void
    {
        $this->registerDevice();
        $otherTenant = Tenant::factory()->create(['is_active' => true]);
        $otherCustomer = Customer::factory()->create([
            'tenant_id' => $otherTenant->id,
            'ledger_account_id' => null,
        ]);

        $response = $this->postJson(
            $this->url('/sync/documents/customers/' . $otherCustomer->sync_uuid . '/statement'),
            [
                'device_uuid' => 'dev-uuid-1',
                'from_date'   => now()->startOfMonth()->toDateString(),
                'to_date'     => now()->endOfMonth()->toDateString(),
            ],
        );

        $response->assertNotFound();
    }

    // ── helpers ──────────────────────────────────────────────────────────

    private function bootstrapAccounts(): void
    {
        // Minimal chart of accounts needed for sales posting.
        $assets = AccountGroup::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Sundry Debtors',
            'code' => 'AR',
            'nature' => 'asset',
        ]);
        $income = AccountGroup::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Direct Income',
            'code' => 'INC',
            'nature' => 'income',
        ]);

        $customerLedger = LedgerAccount::create([
            'tenant_id' => $this->tenant->id,
            'account_group_id' => $assets->id,
            'name' => 'Test Customer',
            'code' => 'CUST-001',
            'opening_balance' => 0,
            'opening_balance_type' => 'debit',
        ]);

        $salesLedger = LedgerAccount::create([
            'tenant_id' => $this->tenant->id,
            'account_group_id' => $income->id,
            'name' => 'Sales Account',
            'code' => 'SAL-001',
            'opening_balance' => 0,
            'opening_balance_type' => 'credit',
        ]);

        $this->customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'ledger_account_id' => $customerLedger->id,
        ]);

        $this->product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'sales_account_id' => $salesLedger->id,
            'purchase_account_id' => $salesLedger->id,
        ]);

        $this->salesVoucherType = VoucherType::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Sales Invoice',
            'code' => 'SV',
            'prefix' => 'SI-',
            'abbreviation' => 'SV',
            'inventory_effect' => 'decrease',
            'is_active' => true,
        ]);
    }

    private function createSyncedInvoice(): string
    {
        $invoiceSyncUuid = (string) \Illuminate\Support\Str::uuid();
        $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'inv-pdf-' . uniqid(),
                'table' => 'vouchers',
                'action' => 'create',
                'sync_uuid' => $invoiceSyncUuid,
                'data' => [
                    'voucher_type_id' => $this->salesVoucherType->id,
                    'voucher_date' => now()->toDateString(),
                    'customer_sync_uuid' => $this->customer->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 1,
                        'rate' => 500,
                    ]],
                    'status' => 'draft',
                ],
            ]],
        ])->assertOk();

        return $invoiceSyncUuid;
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
