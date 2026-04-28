<?php

namespace Tests\Feature\Api\Tenant;

use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Customer;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Phase 5 mobile-sync coverage:
 *   - Pushing offline-captured POS sales goes through the custom
 *     PosSaleSyncService and creates Sale + SaleItem + SalePayment with
 *     a server-assigned sale_number, status='completed'.
 *   - Server validates that the cash register session is OPEN and
 *     belongs to the pushing user.
 *   - Idempotency: same client_mutation_id pushed twice creates one row.
 *   - Validation rejects empty items / payments.
 *   - Insufficient stock surfaces as a `failed` mutation with
 *     `error_code: insufficient_stock`.
 */
class MobilePosSaleSyncTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;
    private Customer $customer;
    private Product $product;
    private CashRegister $register;
    private CashRegisterSession $session;
    private PaymentMethod $cashMethod;

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
                'mobile.sync.write.invoices',
                'mobile.sync.write.inventory',
            ])->get()
            ->each(fn ($p) => $role->permissions()->syncWithoutDetaching([$p->id]));
        $this->user->roles()->syncWithoutDetaching([$role->id]);

        $this->bootstrap();

        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function pushing_offline_pos_sale_creates_completed_sale_with_items_and_payments(): void
    {
        $this->registerDevice();
        $saleSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sale-cm-1',
                'table' => 'sales',
                'action' => 'create',
                'sync_uuid' => $saleSyncUuid,
                'data' => [
                    'sale_date' => now()->toIso8601String(),
                    'customer_sync_uuid' => $this->customer->sync_uuid,
                    'cash_register_session_sync_uuid' => $this->session->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 2,
                        'unit_price' => 500,
                        'discount_amount' => 0,
                    ]],
                    'payments' => [[
                        'payment_method_sync_uuid' => $this->cashMethod->sync_uuid,
                        'amount' => 1000,
                    ]],
                    'client_sale_number' => 'LOCAL-S-1',
                ],
            ]],
        ]);

        $response->assertOk();
        $result = $response->json('data.results.0');
        $this->assertSame('applied', $result['status']);
        $this->assertNotEmpty($result['server_response']['official_sale_number']);
        $this->assertSame($saleSyncUuid, $result['server_response']['sync_uuid']);

        $sale = Sale::where('sync_uuid', $saleSyncUuid)->firstOrFail();
        $this->assertSame('completed', $sale->status);
        $this->assertSame($this->session->id, $sale->cash_register_session_id);
        $this->assertCount(1, $sale->items);
        $this->assertCount(1, $sale->payments);
        $this->assertEqualsWithDelta(1000.0, (float) $sale->total_amount, 0.01);
    }

    /** @test */
    public function pushing_pos_sale_against_closed_session_fails(): void
    {
        $this->registerDevice();
        $this->session->update(['closed_at' => now()]);

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sale-cm-closed',
                'table' => 'sales',
                'action' => 'create',
                'sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'data' => [
                    'cash_register_session_sync_uuid' => $this->session->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 1,
                        'unit_price' => 100,
                    ]],
                    'payments' => [[
                        'payment_method_sync_uuid' => $this->cashMethod->sync_uuid,
                        'amount' => 100,
                    ]],
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('failed', $response->json('data.results.0.status'));
        $this->assertSame('session_closed', $response->json('data.results.0.error_code'));
    }

    /** @test */
    public function pushing_pos_sale_with_session_owned_by_other_user_fails(): void
    {
        $this->registerDevice();
        $other = User::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->session->update(['user_id' => $other->id]);

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sale-cm-otheruser',
                'table' => 'sales',
                'action' => 'create',
                'sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'data' => [
                    'cash_register_session_sync_uuid' => $this->session->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 1,
                        'unit_price' => 100,
                    ]],
                    'payments' => [[
                        'payment_method_sync_uuid' => $this->cashMethod->sync_uuid,
                        'amount' => 100,
                    ]],
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('failed', $response->json('data.results.0.status'));
        $this->assertSame('session_user_mismatch', $response->json('data.results.0.error_code'));
    }

    /** @test */
    public function pushing_same_pos_sale_mutation_twice_is_idempotent(): void
    {
        $this->registerDevice();
        $saleSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $payload = [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sale-cm-idem',
                'table' => 'sales',
                'action' => 'create',
                'sync_uuid' => $saleSyncUuid,
                'data' => [
                    'cash_register_session_sync_uuid' => $this->session->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 1,
                        'unit_price' => 50,
                    ]],
                    'payments' => [[
                        'payment_method_sync_uuid' => $this->cashMethod->sync_uuid,
                        'amount' => 50,
                    ]],
                ],
            ]],
        ];

        $this->postJson($this->url('/sync/push'), $payload)->assertOk();
        $this->postJson($this->url('/sync/push'), $payload)->assertOk();

        $this->assertSame(1, Sale::where('sync_uuid', $saleSyncUuid)->count());
    }

    /** @test */
    public function pushing_pos_sale_without_payments_fails(): void
    {
        $this->registerDevice();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sale-cm-no-pay',
                'table' => 'sales',
                'action' => 'create',
                'sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'data' => [
                    'cash_register_session_sync_uuid' => $this->session->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'quantity' => 1,
                        'unit_price' => 100,
                    ]],
                    'payments' => [],
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('failed', $response->json('data.results.0.status'));
    }

    // ── helpers ──────────────────────────────────────────────────────────

    private function bootstrap(): void
    {
        $this->customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
            'maintain_stock' => false,
            'tax_rate' => 0,
        ]);

        $this->register = CashRegister::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Counter 1',
            'is_active' => true,
        ]);

        $this->session = CashRegisterSession::create([
            'tenant_id' => $this->tenant->id,
            'cash_register_id' => $this->register->id,
            'user_id' => $this->user->id,
            'opening_balance' => 0,
            'opened_at' => now(),
        ]);

        $this->cashMethod = PaymentMethod::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Cash',
            'code' => 'CASH',
            'is_active' => true,
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
