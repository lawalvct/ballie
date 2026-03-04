<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\ModuleRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Assigns default enabled_modules to existing tenants based on their business category.
 *
 * Safe for production:
 *  - By default, skips tenants that already have enabled_modules configured.
 *  - Use --force to override ALL tenants (resets to category defaults).
 *  - Tenants without a business_type_id default to 'hybrid' (all modules enabled — no disruption).
 *
 * Usage:
 *   php artisan tenants:assign-modules           # Skip tenants already configured
 *   php artisan tenants:assign-modules --force   # Reset ALL tenants to category defaults
 *   php artisan tenants:assign-modules --dry-run # Preview without saving
 */
class AssignTenantModules extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenants:assign-modules
                            {--force : Override tenants that already have enabled_modules set}
                            {--dry-run : Preview changes without saving to the database}';

    /**
     * The console command description.
     */
    protected $description = 'Assign default module sets to existing tenants based on their business category';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force  = $this->option('force');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — no changes will be saved.');
        }

        if ($force) {
            $this->warn('--force flag set: ALL tenants will have their modules reset to category defaults.');
        }

        $tenants = Tenant::with('businessType')->get();
        $total   = $tenants->count();

        if ($total === 0) {
            $this->info('No tenants found. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info("Processing {$total} tenant(s)...");
        $this->newLine();

        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        foreach ($tenants as $tenant) {
            // Skip tenants that already have modules configured (unless --force)
            if (!$force && $tenant->enabled_modules !== null) {
                $bar->setMessage("Skipped: {$tenant->name}");
                $bar->advance();
                $skipped++;
                continue;
            }

            $category       = $tenant->getBusinessCategory(); // defaults to 'hybrid' if no business_type
            $defaultModules = ModuleRegistry::getDefaultModules($category);

            $bar->setMessage("{$tenant->name} [{$category}]");

            if (!$dryRun) {
                $tenant->update(['enabled_modules' => $defaultModules]);

                Log::info('tenants:assign-modules — assigned modules', [
                    'tenant_id' => $tenant->id,
                    'tenant'    => $tenant->name,
                    'category'  => $category,
                    'modules'   => $defaultModules,
                ]);
            }

            $bar->advance();
            $updated++;
        }

        $bar->setMessage('Done!');
        $bar->finish();

        $this->newLine(2);

        // Summary table
        $this->table(
            ['Stat', 'Count'],
            [
                ['Total tenants',  $total],
                ['Updated',        $updated],
                ['Skipped (already configured)', $skipped],
            ]
        );

        if ($dryRun) {
            $this->warn('Dry run complete — no data was modified.');
        } else {
            $this->info('Module assignment complete.');

            if ($updated > 0) {
                $this->line("  {$updated} tenant(s) assigned category-default modules.");
            }
            if ($skipped > 0) {
                $this->line("  {$skipped} tenant(s) skipped (already have custom modules). Use --force to override.");
            }
        }

        return self::SUCCESS;
    }
}
