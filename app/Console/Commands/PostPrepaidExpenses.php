<?php

namespace App\Console\Commands;

use App\Models\PrepaidExpense;
use App\Models\PrepaidExpensePosting;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostPrepaidExpenses extends Command
{
    protected $signature = 'prepaid:post-installments {--date= : Process as of this date (default: today)}';

    protected $description = 'Post due prepaid expense amortization installments as journal vouchers';

    public function handle(): int
    {
        $asOfDate = $this->option('date') ?: now()->toDateString();
        $this->info("Processing prepaid expense installments due on or before {$asOfDate}...");

        $dueItems = PrepaidExpense::with(['tenant', 'prepaidAccount', 'expenseAccount'])
            ->due($asOfDate)
            ->get();

        if ($dueItems->isEmpty()) {
            $this->info('No prepaid expense installments due.');
            return self::SUCCESS;
        }

        $posted = 0;
        $failed = 0;

        foreach ($dueItems as $prepaid) {
            try {
                $this->processInstallment($prepaid);
                $posted++;
                $this->line("  ✓ Posted installment {$prepaid->installments_posted}/{$prepaid->installments_count} for: {$prepaid->description}");
            } catch (\Exception $e) {
                $failed++;
                Log::error("Prepaid expense posting failed [ID:{$prepaid->id}]: " . $e->getMessage());
                $this->error("  ✗ Failed for ID {$prepaid->id}: {$e->getMessage()}");
            }
        }

        $this->info("Done. Posted: {$posted}, Failed: {$failed}");
        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function processInstallment(PrepaidExpense $prepaid): void
    {
        DB::transaction(function () use ($prepaid) {
            $tenant = $prepaid->tenant;

            // Find the JV voucher type for this tenant
            $jvType = VoucherType::where('tenant_id', $tenant->id)
                ->where('code', 'JV')
                ->where('is_active', true)
                ->first();

            if (!$jvType) {
                throw new \RuntimeException("No active JV voucher type found for tenant {$tenant->id}");
            }

            $installmentNumber = $prepaid->installments_posted + 1;
            $amount = $prepaid->getNextInstallmentAmount();

            $narration = "Prepaid amortization: {$prepaid->description} - Installment {$installmentNumber} of {$prepaid->installments_count}";

            // Create the Journal Voucher
            $voucher = Voucher::create([
                'tenant_id' => $tenant->id,
                'voucher_type_id' => $jvType->id,
                'voucher_number' => $jvType->getNextVoucherNumber(),
                'voucher_date' => $prepaid->next_posting_date,
                'narration' => $narration,
                'total_amount' => $amount,
                'status' => 'posted',
                'created_by' => $prepaid->created_by,
                'posted_at' => now(),
                'posted_by' => $prepaid->created_by,
                'meta_data' => [
                    'prepaid_expense_id' => $prepaid->id,
                    'installment_number' => $installmentNumber,
                    'auto_posted' => true,
                ],
            ]);

            // Debit the expense account (e.g., Rent Expense)
            $debitEntry = VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $prepaid->expense_account_id,
                'particulars' => $narration,
                'debit_amount' => $amount,
                'credit_amount' => 0,
            ]);

            // Credit the prepaid asset account (e.g., Prepaid Expenses)
            $creditEntry = VoucherEntry::create([
                'voucher_id' => $voucher->id,
                'ledger_account_id' => $prepaid->prepaid_account_id,
                'particulars' => $narration,
                'debit_amount' => 0,
                'credit_amount' => $amount,
            ]);

            // Update ledger balances (since we posted directly)
            $debitEntry->updateLedgerAccountBalance();
            $creditEntry->updateLedgerAccountBalance();

            // Record the posting
            PrepaidExpensePosting::create([
                'prepaid_expense_id' => $prepaid->id,
                'voucher_id' => $voucher->id,
                'installment_number' => $installmentNumber,
                'amount' => $amount,
                'posting_date' => $prepaid->next_posting_date,
                'status' => 'posted',
            ]);

            // Advance the schedule
            $prepaid->markInstallmentPosted();
        });
    }
}
