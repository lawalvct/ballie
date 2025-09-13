<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class HandleExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:handle-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle expired tenant subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting expired subscription handling...');

        // Handle expired subscriptions
        $expiredTenants = Tenant::where('subscription_status', Tenant::STATUS_ACTIVE)
            ->where('subscription_ends_at', '<', now())
            ->get();

        $this->info("Found {$expiredTenants->count()} expired subscriptions");

        foreach ($expiredTenants as $tenant) {
            $tenant->handleExpiredSubscription();

            Log::info("Handled expired subscription for tenant: {$tenant->name}");
            $this->line("Handled: {$tenant->name}");
        }

        // Handle expired trials
        $expiredTrials = Tenant::where('subscription_status', Tenant::STATUS_TRIAL)
            ->where('trial_ends_at', '<', now())
            ->get();

        $this->info("Found {$expiredTrials->count()} expired trials");

        foreach ($expiredTrials as $tenant) {
            $tenant->update(['subscription_status' => Tenant::STATUS_EXPIRED]);

            Log::info("Handled expired trial for tenant: {$tenant->name}");
            $this->line("Handled trial: {$tenant->name}");
        }

        $this->info('Expired subscriptions handled successfully');
        return 0;
    }
}
