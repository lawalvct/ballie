<?php

namespace Tests\Feature\Api\Tenant;

use App\Models\Product;
use App\Models\StockJournalEntry;
use App\Models\StockLocation;
use App\Models\Tenant;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Phase 4 mobile-sync coverage:
 *   - Pushing offline-created stock journal entries goes through the
 *     custom StockJournalSyncService handler and creates parent + items
 *     as `draft` (no stock_movements rows are written).
 *   - Server assigns the official journal_number.
 *   - The same mutation pushed twice is idempotent (one row).
 *   - Validation rejects empty items or missing entry_type.
 *   - Transfer entries require both from + to stock locations.
 *   - Locations are resolved by sync_uuid.
 */
class MobileStockJournalSyncTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;
    private Product $product;
    private StockLocation $fromLocation;
    private StockLocation $toLocation;

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
                'mobile.sync.write.inventory',
            ])->get()
            ->each(fn ($p) => $role->permissions()->syncWithoutDetaching([$p->id]));
        $this->user->roles()->syncWithoutDetaching([$role->id]);

        $this->bootstrap();

        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function pushing_an_offline_stock_journal_creates_draft_with_items(): void
    {
        $this->registerDevice();
        $journalSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sj-cm-1',
                'table' => 'stock_journal_entries',
                'action' => 'create',
                'sync_uuid' => $journalSyncUuid,
                'data' => [
                    'journal_date' => now()->toDateString(),
                    'entry_type' => 'consumption',
                    'narration' => 'Offline consumption draft',
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'movement_type' => 'out',
                        'quantity' => 5,
                        'rate' => 100,
                    ]],
                ],
            ]],
        ]);

        $response->assertOk();
        $result = $response->json('data.results.0');
        $this->assertSame('applied', $result['status']);
        $this->assertNotEmpty($result['server_response']['official_journal_number']);
        $this->assertSame('draft', $result['server_response']['status']);
        $this->assertSame($journalSyncUuid, $result['server_response']['sync_uuid']);

        $entry = StockJournalEntry::where('sync_uuid', $journalSyncUuid)->firstOrFail();
        $this->assertSame('draft', $entry->status);
        $this->assertSame('consumption', $entry->entry_type);
        $this->assertCount(1, $entry->items);
        $this->assertNull($entry->posted_at);

        // Posting is online-only — no stock_movements should exist yet.
        $this->assertSame(
            0,
            DB::table('stock_movements')
                ->where('source_type', StockJournalEntry::class)
                ->where('source_id', $entry->id)
                ->count(),
        );
    }

    /** @test */
    public function pushing_a_transfer_journal_resolves_locations_by_sync_uuid(): void
    {
        $this->registerDevice();
        $journalSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sj-cm-transfer',
                'table' => 'stock_journal_entries',
                'action' => 'create',
                'sync_uuid' => $journalSyncUuid,
                'data' => [
                    'journal_date' => now()->toDateString(),
                    'entry_type' => 'transfer',
                    'from_stock_location_sync_uuid' => $this->fromLocation->sync_uuid,
                    'to_stock_location_sync_uuid' => $this->toLocation->sync_uuid,
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'movement_type' => 'out',
                        'quantity' => 2,
                        'rate' => 50,
                    ]],
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('applied', $response->json('data.results.0.status'));

        $entry = StockJournalEntry::where('sync_uuid', $journalSyncUuid)->firstOrFail();
        $this->assertSame($this->fromLocation->id, $entry->from_stock_location_id);
        $this->assertSame($this->toLocation->id, $entry->to_stock_location_id);
    }

    /** @test */
    public function pushing_same_stock_journal_mutation_twice_is_idempotent(): void
    {
        $this->registerDevice();
        $journalSyncUuid = (string) \Illuminate\Support\Str::uuid();

        $payload = [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sj-cm-idem',
                'table' => 'stock_journal_entries',
                'action' => 'create',
                'sync_uuid' => $journalSyncUuid,
                'data' => [
                    'journal_date' => now()->toDateString(),
                    'entry_type' => 'adjustment',
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'movement_type' => 'in',
                        'quantity' => 1,
                        'rate' => 10,
                    ]],
                ],
            ]],
        ];

        $this->postJson($this->url('/sync/push'), $payload)->assertOk();
        $this->postJson($this->url('/sync/push'), $payload)->assertOk();

        $this->assertSame(1, StockJournalEntry::where('sync_uuid', $journalSyncUuid)->count());
    }

    /** @test */
    public function pushing_stock_journal_without_items_fails(): void
    {
        $this->registerDevice();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sj-cm-bad-items',
                'table' => 'stock_journal_entries',
                'action' => 'create',
                'sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'data' => [
                    'journal_date' => now()->toDateString(),
                    'entry_type' => 'adjustment',
                    'items' => [],
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('failed', $response->json('data.results.0.status'));
    }

    /** @test */
    public function transfer_without_both_locations_is_rejected(): void
    {
        $this->registerDevice();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sj-cm-bad-transfer',
                'table' => 'stock_journal_entries',
                'action' => 'create',
                'sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'data' => [
                    'journal_date' => now()->toDateString(),
                    'entry_type' => 'transfer',
                    'from_stock_location_sync_uuid' => $this->fromLocation->sync_uuid,
                    // no to_stock_location_sync_uuid
                    'items' => [[
                        'product_sync_uuid' => $this->product->sync_uuid,
                        'movement_type' => 'out',
                        'quantity' => 1,
                        'rate' => 10,
                    ]],
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('failed', $response->json('data.results.0.status'));
        $this->assertSame(
            'transfer_requires_locations',
            $response->json('data.results.0.error_code'),
        );
    }

    /** @test */
    public function pushing_with_unknown_product_sync_uuid_fails(): void
    {
        $this->registerDevice();

        $response = $this->postJson($this->url('/sync/push'), [
            'device_uuid' => 'dev-uuid-1',
            'mutations' => [[
                'client_mutation_id' => 'sj-cm-bad-prod',
                'table' => 'stock_journal_entries',
                'action' => 'create',
                'sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
                'data' => [
                    'journal_date' => now()->toDateString(),
                    'entry_type' => 'consumption',
                    'items' => [[
                        'product_sync_uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'movement_type' => 'out',
                        'quantity' => 1,
                        'rate' => 10,
                    ]],
                ],
            ]],
        ]);

        $response->assertOk();
        $this->assertSame('failed', $response->json('data.results.0.status'));
        $this->assertSame(
            'product_not_found',
            $response->json('data.results.0.error_code'),
        );
    }

    // ── helpers ──────────────────────────────────────────────────────────

    private function bootstrap(): void
    {
        $this->product = Product::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->fromLocation = StockLocation::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Main Warehouse',
            'code' => 'WH-MAIN',
            'is_active' => true,
        ]);

        $this->toLocation = StockLocation::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Secondary Warehouse',
            'code' => 'WH-SEC',
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
