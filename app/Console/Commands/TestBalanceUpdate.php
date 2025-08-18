<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LedgerAccount;
use App\Models\Customer;
use App\Models\VoucherEntry;

class TestBalanceUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:balance-update {ledger_account_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test balance update for a specific ledger account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ledgerAccountId = $this->argument('ledger_account_id');

        $ledgerAccount = LedgerAccount::find($ledgerAccountId);

        if (!$ledgerAccount) {
            $this->error("Ledger account with ID {$ledgerAccountId} not found.");
            return 1;
        }

        $this->info("Testing balance update for: {$ledgerAccount->name}");
        $this->info("Current balance (cached): {$ledgerAccount->current_balance}");

        // Get fresh calculation
        $freshBalance = $ledgerAccount->getCurrentBalance(null, false);
        $this->info("Fresh calculated balance: {$freshBalance}");

        // Get totals
        $totalDebits = $ledgerAccount->voucherEntries()
            ->whereHas('voucher', function($q) {
                $q->where('status', 'posted');
            })->sum('debit_amount');

        $totalCredits = $ledgerAccount->voucherEntries()
            ->whereHas('voucher', function($q) {
                $q->where('status', 'posted');
            })->sum('credit_amount');

        $this->info("Opening Balance: {$ledgerAccount->opening_balance}");
        $this->info("Total Debits: {$totalDebits}");
        $this->info("Total Credits: {$totalCredits}");
        $this->info("Account Type: {$ledgerAccount->account_type}");

        // Update balance
        $ledgerAccount->updateCurrentBalance();
        $updatedAccount = $ledgerAccount->fresh();
        $this->info("Updated balance in DB: {$updatedAccount->current_balance}");

        // Check if linked to customer
        $customer = Customer::where('ledger_account_id', $ledgerAccountId)->first();
        if ($customer) {
            $this->info("Linked Customer: {$customer->getFullNameAttribute()}");
            $this->info("Customer Outstanding Balance: {$customer->outstanding_balance}");

            // Update customer balance
            $customer->updateOutstandingBalance();
            $updatedCustomer = $customer->fresh();
            $this->info("Updated Customer Outstanding Balance: {$updatedCustomer->outstanding_balance}");
        } else {
            $this->info("No customer linked to this ledger account.");
        }

        return 0;
    }
}
