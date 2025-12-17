<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\EmailVerificationReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendEmailVerificationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-verification-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email verification reminders to unverified users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for users with unverified emails...');

        // Find users who haven't verified their email
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('is_active', true)
            ->get();

        if ($unverifiedUsers->isEmpty()) {
            $this->info('No unverified users found.');
            return 0;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($unverifiedUsers as $user) {
            try {
                $accountAge = $user->created_at->diffInDays(now());
                $daysRemaining = max(0, 7 - $accountAge);

                // Send reminders on day 3 and day 6
                if ($accountAge == 3 || $accountAge == 6) {
                    $user->notify(new EmailVerificationReminder($daysRemaining));
                    $sentCount++;
                    $this->line("Reminder sent to: {$user->email} (Days remaining: {$daysRemaining})");

                    Log::info('Email verification reminder sent', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'days_remaining' => $daysRemaining,
                        'account_age' => $accountAge
                    ]);
                }
            } catch (\Exception $e) {
                $failedCount++;
                $this->error("Failed to send reminder to {$user->email}: {$e->getMessage()}");

                Log::error('Failed to send email verification reminder', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\nReminders sent: {$sentCount}");
        if ($failedCount > 0) {
            $this->warn("Failed: {$failedCount}");
        }

        return 0;
    }
}
