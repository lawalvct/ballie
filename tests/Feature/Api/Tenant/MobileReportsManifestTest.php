<?php

namespace Tests\Feature\Api\Tenant;

use App\Models\Tenant;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Phase 6: cacheable reports manifest.
 *
 * Verifies that GET /sync/reports/manifest:
 *   - returns 401 without a token / 403 without `mobile.sync.read`;
 *   - returns the catalog filtered by the calling user's existing
 *     tenant permissions (e.g. a user lacking `dashboard.view` does
 *     not receive any dashboard.* entries);
 *   - includes ttl_minutes + max_age_minutes per entry so the mobile
 *     React Query persistence layer can demote stale snapshots.
 */
class MobileReportsManifestTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create(['is_active' => true]);
        $this->user = User::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->seed(\Database\Seeders\MobileSyncPermissionsSeeder::class);

        // Make sure the per-report permission slugs exist as Permissions
        // so hasPermissionTo() resolves them. Real production seeders
        // already provision these — here we only need the row to exist.
        foreach (['dashboard.view', 'reports.view', 'inventory.view'] as $slug) {
            Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => $slug, 'guard_name' => 'web', 'module' => explode('.', $slug)[0]],
            );
        }
    }

    /** @test */
    public function unauthenticated_request_is_rejected(): void
    {
        $this->getJson($this->url('/sync/reports/manifest'))->assertStatus(401);
    }

    /** @test */
    public function user_without_mobile_sync_read_is_forbidden(): void
    {
        // A role with NO permissions at all.
        $role = Role::firstOrCreate(
            ['slug' => 'mobile-sync-bare', 'tenant_id' => $this->tenant->id],
            ['name' => 'Bare', 'guard_name' => 'web', 'is_active' => true],
        );
        $this->user->roles()->syncWithoutDetaching([$role->id]);

        Sanctum::actingAs($this->user);

        $this->getJson($this->url('/sync/reports/manifest'))->assertStatus(403);
    }

    /** @test */
    public function manifest_filters_entries_by_user_permissions(): void
    {
        $role = $this->makeRole('mobile-sync-reader', [
            'mobile.sync.read',
            // Note: NO dashboard.view, NO reports.view, NO inventory.view
        ]);
        $this->user->roles()->syncWithoutDetaching([$role->id]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->url('/sync/reports/manifest'));
        $response->assertOk();

        $reports = $response->json('data.reports');
        $this->assertIsArray($reports);
        // Every cacheable report in config gates on a permission the
        // user does not have, so the list must be empty.
        $this->assertSame([], $reports);
    }

    /** @test */
    public function manifest_includes_dashboard_entries_when_user_can_view_dashboard(): void
    {
        $role = $this->makeRole('mobile-sync-dashboard', [
            'mobile.sync.read',
            'dashboard.view',
        ]);
        $this->user->roles()->syncWithoutDetaching([$role->id]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->url('/sync/reports/manifest'));
        $response->assertOk();

        $keys = collect($response->json('data.reports'))->pluck('key')->all();
        $this->assertContains('dashboard.summary', $keys);
        $this->assertContains('dashboard.balances', $keys);
        // Reports module gated on reports.view — must NOT be in payload.
        $this->assertNotContains('reports.financial.profit_loss', $keys);
    }

    /** @test */
    public function manifest_entries_carry_ttl_and_max_age(): void
    {
        $role = $this->makeRole('mobile-sync-full', [
            'mobile.sync.read',
            'dashboard.view',
            'reports.view',
            'inventory.view',
        ]);
        $this->user->roles()->syncWithoutDetaching([$role->id]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->url('/sync/reports/manifest'));
        $response->assertOk();

        $reports = $response->json('data.reports');
        $this->assertNotEmpty($reports);

        foreach ($reports as $entry) {
            $this->assertArrayHasKey('key', $entry);
            $this->assertArrayHasKey('method', $entry);
            $this->assertArrayHasKey('absolute_path', $entry);
            $this->assertArrayHasKey('ttl_minutes', $entry);
            $this->assertArrayHasKey('max_age_minutes', $entry);
            $this->assertGreaterThan(0, (int) $entry['ttl_minutes']);
            $this->assertGreaterThanOrEqual((int) $entry['ttl_minutes'], (int) $entry['max_age_minutes']);
            $this->assertStringStartsWith("/api/v1/tenant/{$this->tenant->slug}/", $entry['absolute_path']);
        }
    }

    // ── helpers ──────────────────────────────────────────────────────────

    /**
     * @param  array<int, string>  $permissionSlugs
     */
    private function makeRole(string $slug, array $permissionSlugs): Role
    {
        $role = Role::firstOrCreate(
            ['slug' => $slug, 'tenant_id' => $this->tenant->id],
            ['name' => ucfirst($slug), 'guard_name' => 'web', 'is_active' => true],
        );

        Permission::query()
            ->whereIn('slug', $permissionSlugs)
            ->get()
            ->each(fn ($p) => $role->permissions()->syncWithoutDetaching([$p->id]));

        return $role;
    }

    private function url(string $path): string
    {
        return "/api/v1/tenant/{$this->tenant->slug}{$path}";
    }
}
