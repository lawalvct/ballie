<?php

namespace Tests\Feature\Api\Tenant;

use App\Models\MobileDevice;
use App\Models\MobileDocumentExport;
use App\Models\Tenant;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Phase 2 — exercises the signed-URL download flow used by the mobile
 * app to fetch the cached invoice/statement PDFs produced by
 * DocumentExportService and CustomerStatementSnapshotService.
 *
 * Focus is the download endpoint itself (signature verification, expiry,
 * cross-tenant isolation, downloaded_at bookkeeping) rather than the PDF
 * generators (covered separately by MobileInvoiceSyncTest).
 */
class MobileDocumentExportTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

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
            ])->get()
            ->each(fn ($p) => $role->permissions()->syncWithoutDetaching([$p->id]));
        $this->user->roles()->syncWithoutDetaching([$role->id]);

        Sanctum::actingAs($this->user);
    }

    /** @test */
    public function signed_url_downloads_the_underlying_file_and_marks_downloaded_at(): void
    {
        $export = $this->createExport();

        $url = URL::temporarySignedRoute(
            'api.v1.tenant.sync.documents.download',
            now()->addMinutes(5),
            [
                'tenant'      => $this->tenant->slug,
                'export_uuid' => $export->export_uuid,
            ],
        );

        $response = $this->get($url);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertNotNull($export->fresh()->downloaded_at);
    }

    /** @test */
    public function download_rejects_invalid_signature_with_403(): void
    {
        $export = $this->createExport();

        // Build a route URL without signing — signature middleware should reject.
        $unsigned = route('api.v1.tenant.sync.documents.download', [
            'tenant'      => $this->tenant->slug,
            'export_uuid' => $export->export_uuid,
        ]);

        $this->get($unsigned)->assertStatus(403);
    }

    /** @test */
    public function download_rejects_expired_signature_with_403(): void
    {
        $export = $this->createExport();

        $expiredUrl = URL::temporarySignedRoute(
            'api.v1.tenant.sync.documents.download',
            now()->subMinute(),
            [
                'tenant'      => $this->tenant->slug,
                'export_uuid' => $export->export_uuid,
            ],
        );

        $this->get($expiredUrl)->assertStatus(403);
    }

    /** @test */
    public function download_returns_410_when_export_row_is_expired(): void
    {
        $export = $this->createExport();
        $export->forceFill(['expires_at' => now()->subDay()])->save();

        $url = URL::temporarySignedRoute(
            'api.v1.tenant.sync.documents.download',
            now()->addMinutes(5),
            [
                'tenant'      => $this->tenant->slug,
                'export_uuid' => $export->export_uuid,
            ],
        );

        $this->get($url)->assertStatus(410);
    }

    /** @test */
    public function download_isolates_exports_across_tenants(): void
    {
        $otherTenant = Tenant::factory()->create(['is_active' => true]);
        $export = $this->createExport($otherTenant);

        // Sign URL using *this* tenant's slug — should 404 because the
        // export belongs to another tenant.
        $url = URL::temporarySignedRoute(
            'api.v1.tenant.sync.documents.download',
            now()->addMinutes(5),
            [
                'tenant'      => $this->tenant->slug,
                'export_uuid' => $export->export_uuid,
            ],
        );

        $this->get($url)->assertNotFound();
    }

    /** @test */
    public function download_returns_404_when_underlying_file_is_missing(): void
    {
        $export = $this->createExport();
        Storage::disk($export->disk)->delete($export->storage_path);

        $url = URL::temporarySignedRoute(
            'api.v1.tenant.sync.documents.download',
            now()->addMinutes(5),
            [
                'tenant'      => $this->tenant->slug,
                'export_uuid' => $export->export_uuid,
            ],
        );

        $this->get($url)->assertNotFound();
    }

    private function createExport(?Tenant $tenant = null): MobileDocumentExport
    {
        $tenant ??= $this->tenant;
        $device = MobileDevice::create([
            'device_uuid'      => (string) Str::uuid(),
            'tenant_id'        => $tenant->id,
            'user_id'          => $this->user->id,
            'platform'         => 'android',
            'app_version'      => '1.0.0',
            'last_seen_at'     => now(),
        ]);

        $exportUuid = (string) Str::uuid();
        $storagePath = 'mobile-exports/invoices/' . $exportUuid . '.pdf';
        Storage::disk('local')->put($storagePath, '%PDF-1.4 fake pdf body');

        return MobileDocumentExport::create([
            'export_uuid'         => $exportUuid,
            'tenant_id'           => $tenant->id,
            'user_id'             => $this->user->id,
            'mobile_device_id'    => $device->id,
            'document_type'       => MobileDocumentExport::TYPE_INVOICE_PDF,
            'source_table'        => 'vouchers',
            'source_server_id'    => 0,
            'source_sync_uuid'    => (string) Str::uuid(),
            'disk'                => 'local',
            'storage_path'        => $storagePath,
            'file_name'           => 'invoice.pdf',
            'mime_type'           => 'application/pdf',
            'file_size'           => 22,
            'status'              => MobileDocumentExport::STATUS_READY,
            'generated_at'        => now(),
            'expires_at'          => Carbon::now()->addDays(7),
            'meta'                => ['source_server_version' => 1],
        ]);
    }
}
