<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use Illuminate\Support\Str;

class GenerateEmployeePortalTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:generate-portal-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate portal tokens for existing employees who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating portal tokens for employees...');

        $employees = Employee::whereNull('portal_token')
            ->orWhere('portal_token_expires_at', '<', now())
            ->get();

        if ($employees->isEmpty()) {
            $this->info('All employees already have valid portal tokens.');
            return 0;
        }

        $count = 0;
        foreach ($employees as $employee) {
            $employee->portal_token = Str::random(64);
            $employee->portal_token_expires_at = now()->addDays(90);
            $employee->save();
            $count++;

            $this->line("✓ Generated token for: {$employee->full_name} ({$employee->email})");
        }

        $this->info("Successfully generated portal tokens for {$count} employee(s).");
        return 0;
    }
}

