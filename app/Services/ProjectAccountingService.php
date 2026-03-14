<?php

namespace App\Services;

use App\Models\AccountGroup;
use App\Models\LedgerAccount;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectMilestone;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Support\Facades\DB;

class ProjectAccountingService
{
    private int $tenantId;
    private array $ledgerAccounts = [];

    public function __construct(int $tenantId)
    {
        $this->tenantId = $tenantId;
        $this->initializeLedgerAccounts();
    }

    // ─── Ledger Account Setup ─────────────────────────────

    private function initializeLedgerAccounts(): void
    {
        $this->ensureAccountGroupsExist();

        $this->ledgerAccounts = [
            // Revenue accounts
            'service_revenue'      => $this->getOrCreateAccount('Service Revenue', 'SVC-REV', 'income'),
            // Receivable
            'accounts_receivable'  => $this->getOrCreateAccount('Accounts Receivable', 'AR', 'asset'),
            // Expense accounts
            'project_expense'      => $this->getOrCreateAccount('Project Expenses', 'PROJ-EXP', 'expense'),
            // Liability (deferred / unearned revenue)
            'deferred_revenue'     => $this->getOrCreateAccount('Deferred Revenue', 'DEF-REV', 'liability'),
        ];
    }

    private function ensureAccountGroupsExist(): void
    {
        $groups = [
            ['name' => 'Current Assets', 'nature' => 'assets', 'code' => 'CA'],
            ['name' => 'Current Liabilities', 'nature' => 'liabilities', 'code' => 'CL'],
            ['name' => 'Operating Expenses', 'nature' => 'expenses', 'code' => 'OPEX'],
            ['name' => 'Revenue', 'nature' => 'income', 'code' => 'REV'],
        ];

        foreach ($groups as $g) {
            AccountGroup::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'code' => $g['code']],
                ['name' => $g['name'], 'nature' => $g['nature'], 'is_active' => true]
            );
        }
    }

    private function getOrCreateAccount(string $name, string $code, string $type): LedgerAccount
    {
        $groupCode = match ($type) {
            'asset'     => 'CA',
            'liability' => 'CL',
            'expense'   => 'OPEX',
            'income'    => 'REV',
            default     => 'CA',
        };

        $group = AccountGroup::where('tenant_id', $this->tenantId)
            ->where('code', $groupCode)
            ->first();

        return LedgerAccount::firstOrCreate(
            ['tenant_id' => $this->tenantId, 'code' => $code],
            [
                'name'             => $name,
                'account_group_id' => $group->id,
                'account_type'     => $type,
                'opening_balance'  => 0,
                'current_balance'  => 0,
                'is_active'        => true,
                'is_system_account' => true,
                'description'      => "System generated for projects — {$name}",
                'created_by'       => auth()->id(),
            ]
        );
    }

    private function getOrCreateVoucherType(string $name, string $code): VoucherType
    {
        return VoucherType::firstOrCreate(
            ['tenant_id' => $this->tenantId, 'code' => $code],
            [
                'name'              => $name,
                'abbreviation'      => strtoupper(substr($code, 0, 3)),
                'description'       => "System generated for {$name}",
                'numbering_method'  => 'auto',
                'prefix'            => $code . '-',
                'starting_number'   => 1,
                'current_number'    => 0,
                'has_reference'     => false,
                'affects_inventory' => false,
                'affects_cashbank'  => false,
                'is_system_defined' => true,
                'is_active'         => true,
            ]
        );
    }

    // ═══════════════════════════════════════════════════════
    //  1. MILESTONE INVOICING — Revenue Recognition
    // ═══════════════════════════════════════════════════════
    //
    //  When a completed, billable milestone is invoiced:
    //
    //    DR  Accounts Receivable     (asset ↑)   ₦ amount
    //    CR  Service Revenue         (income ↑)  ₦ amount
    //
    //  This is the standard revenue recognition entry for a
    //  service business billing against project deliverables.
    // ═══════════════════════════════════════════════════════

    public function invoiceMilestone(ProjectMilestone $milestone): Voucher
    {
        if ($milestone->invoice_id) {
            throw new \Exception('Milestone is already invoiced.');
        }

        if (!$milestone->completed_at) {
            throw new \Exception('Milestone must be completed before invoicing.');
        }

        if (!$milestone->is_billable || !$milestone->amount || $milestone->amount <= 0) {
            throw new \Exception('Milestone is not billable or has no amount.');
        }

        $project = $milestone->project;

        return DB::transaction(function () use ($milestone, $project) {
            $voucherType = $this->getOrCreateVoucherType('Sales', 'SV');

            // Use customer ledger if project has a client linked
            $receivableAccount = $this->resolveReceivableAccount($project);

            $voucher = Voucher::create([
                'tenant_id'       => $this->tenantId,
                'voucher_type_id' => $voucherType->id,
                'voucher_number'  => $voucherType->getNextVoucherNumber(),
                'voucher_date'    => now(),
                'reference_number' => $project->project_number,
                'narration'       => "Project milestone invoice — {$project->name}: {$milestone->title}",
                'total_amount'    => $milestone->amount,
                'status'          => 'posted',
                'created_by'      => auth()->id(),
                'posted_at'       => now(),
                'posted_by'       => auth()->id(),
            ]);

            // DR Accounts Receivable / Client Account
            VoucherEntry::create([
                'voucher_id'        => $voucher->id,
                'ledger_account_id' => $receivableAccount->id,
                'debit_amount'      => $milestone->amount,
                'credit_amount'     => 0,
                'particulars'       => "Milestone billing — {$milestone->title} ({$project->project_number})",
            ]);

            // CR Service Revenue
            VoucherEntry::create([
                'voucher_id'        => $voucher->id,
                'ledger_account_id' => $this->ledgerAccounts['service_revenue']->id,
                'debit_amount'      => 0,
                'credit_amount'     => $milestone->amount,
                'particulars'       => "Service revenue — {$milestone->title} ({$project->project_number})",
            ]);

            // Link voucher to milestone
            $milestone->update(['invoice_id' => $voucher->id]);

            // Update ledger balances
            $receivableAccount->updateCurrentBalance();
            $this->ledgerAccounts['service_revenue']->updateCurrentBalance();

            return $voucher;
        });
    }

    // ═══════════════════════════════════════════════════════
    //  2. EXPENSE RECORDING — Cost Tracking
    // ═══════════════════════════════════════════════════════
    //
    //  When an expense is recorded against a project:
    //
    //    DR  Project Expenses        (expense ↑) ₦ amount
    //    CR  Accounts Payable / Bank (liability ↑ or asset ↓)
    //
    //  Also updates project.actual_cost for budget tracking.
    // ═══════════════════════════════════════════════════════

    public function recordExpense(Project $project, array $data): ProjectExpense
    {
        return DB::transaction(function () use ($project, $data) {
            $voucherType = $this->getOrCreateVoucherType('Journal Entry', 'JE');

            $amount = (float) $data['amount'];

            $voucher = Voucher::create([
                'tenant_id'       => $this->tenantId,
                'voucher_type_id' => $voucherType->id,
                'voucher_number'  => $voucherType->getNextVoucherNumber(),
                'voucher_date'    => $data['expense_date'],
                'reference_number' => $project->project_number,
                'narration'       => "Project expense — {$project->name}: {$data['title']}",
                'total_amount'    => $amount,
                'status'          => 'posted',
                'created_by'      => auth()->id(),
                'posted_at'       => now(),
                'posted_by'       => auth()->id(),
            ]);

            // DR Project Expenses
            VoucherEntry::create([
                'voucher_id'        => $voucher->id,
                'ledger_account_id' => $this->ledgerAccounts['project_expense']->id,
                'debit_amount'      => $amount,
                'credit_amount'     => 0,
                'particulars'       => "Project expense — {$data['title']} ({$project->project_number})",
            ]);

            // CR Deferred Revenue (general liability — settled when payment is made)
            VoucherEntry::create([
                'voucher_id'        => $voucher->id,
                'ledger_account_id' => $this->ledgerAccounts['deferred_revenue']->id,
                'debit_amount'      => 0,
                'credit_amount'     => $amount,
                'particulars'       => "Project cost accrual — {$data['title']} ({$project->project_number})",
            ]);

            // Create tracked expense record
            $expense = ProjectExpense::create([
                'project_id'   => $project->id,
                'tenant_id'    => $this->tenantId,
                'title'        => $data['title'],
                'description'  => $data['description'] ?? null,
                'amount'       => $amount,
                'expense_date' => $data['expense_date'],
                'category'     => $data['category'] ?? 'general',
                'voucher_id'   => $voucher->id,
                'created_by'   => auth()->id(),
            ]);

            // Update project actual_cost
            $project->increment('actual_cost', $amount);

            // Update ledger balances
            $this->ledgerAccounts['project_expense']->updateCurrentBalance();
            $this->ledgerAccounts['deferred_revenue']->updateCurrentBalance();

            return $expense;
        });
    }

    // ═══════════════════════════════════════════════════════
    //  3. HELPER — Resolve client receivable account
    // ═══════════════════════════════════════════════════════

    private function resolveReceivableAccount(Project $project): LedgerAccount
    {
        // If the project has a customer with a dedicated ledger account, use it
        if ($project->customer && $project->customer->ledger_account_id) {
            return LedgerAccount::find($project->customer->ledger_account_id);
        }

        // Fall back to generic Accounts Receivable
        return $this->ledgerAccounts['accounts_receivable'];
    }
}
