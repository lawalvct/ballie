<?php

namespace App\Services;

use App\Models\PayrollPeriod;
use App\Models\LedgerAccount;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Illuminate\Support\Facades\DB;

class PayrollAccountingService
{
    private PayrollPeriod $payrollPeriod;
    private array $ledgerAccounts = [];

    public function __construct(PayrollPeriod $payrollPeriod)
    {
        $this->payrollPeriod = $payrollPeriod;
        $this->initializeLedgerAccounts();
    }

    /**
     * Initialize required ledger accounts for payroll
     */
    private function initializeLedgerAccounts(): void
    {
        $tenantId = $this->payrollPeriod->tenant_id;

        // Ensure default account groups exist
        $this->ensureAccountGroupsExist($tenantId);

        // Get or create essential payroll accounts
        $this->ledgerAccounts = [
            'salary_expense' => $this->getOrCreateAccount($tenantId, 'Salary Expense', 'SAL-EXP', 'expense'),
            'allowance_expense' => $this->getOrCreateAccount($tenantId, 'Allowance Expense', 'ALL-EXP', 'expense'),
            'tax_payable' => $this->getOrCreateAccount($tenantId, 'PAYE Tax Payable', 'TAX-PAY', 'liability'),
            'nsitf_payable' => $this->getOrCreateAccount($tenantId, 'NSITF Payable', 'NSITF-PAY', 'liability'),
            'salary_payable' => $this->getOrCreateAccount($tenantId, 'Salary Payable', 'SAL-PAY', 'liability'),
            'loan_deduction' => $this->getOrCreateAccount($tenantId, 'Employee Loan Deductions', 'LOAN-DED', 'liability'),
            'bank_account' => $this->getOrCreateAccount($tenantId, 'Bank Account', 'BANK', 'asset'),
        ];
    }

        /**
     * Ensure required account groups exist for payroll accounts
     */
    private function ensureAccountGroupsExist(int $tenantId): void
    {
        $defaultGroups = [
            ['name' => 'Current Assets', 'nature' => 'assets', 'code' => 'CA'],
            ['name' => 'Current Liabilities', 'nature' => 'liabilities', 'code' => 'CL'],
            ['name' => 'Operating Expenses', 'nature' => 'expenses', 'code' => 'OPEX'],
        ];

        foreach ($defaultGroups as $group) {
            \App\Models\AccountGroup::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'code' => $group['code']
                ],
                [
                    'name' => $group['name'],
                    'nature' => $group['nature'],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Get or create a ledger account
     */
    private function getOrCreateAccount(int $tenantId, string $name, string $code, string $type): LedgerAccount
    {
        // Determine the account group based on type
        $accountGroupCode = match($type) {
            'asset' => 'CA',          // Current Assets
            'liability' => 'CL',      // Current Liabilities
            'expense' => 'OPEX',      // Operating Expenses
            'income' => 'REV',        // Revenue
            default => 'CA'
        };

        $accountGroup = \App\Models\AccountGroup::where('tenant_id', $tenantId)
            ->where('code', $accountGroupCode)
            ->first();

        // Fallback: create account group if it doesn't exist
        if (!$accountGroup) {
            $groupData = match($accountGroupCode) {
                'CA' => ['name' => 'Current Assets', 'nature' => 'assets'],
                'CL' => ['name' => 'Current Liabilities', 'nature' => 'liabilities'],
                'OPEX' => ['name' => 'Operating Expenses', 'nature' => 'expenses'],
                'REV' => ['name' => 'Revenue', 'nature' => 'income'],
                default => ['name' => 'Current Assets', 'nature' => 'assets']
            };

            $accountGroup = \App\Models\AccountGroup::create([
                'tenant_id' => $tenantId,
                'code' => $accountGroupCode,
                'name' => $groupData['name'],
                'nature' => $groupData['nature'],
                'is_active' => true,
            ]);
        }

        return LedgerAccount::firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'code' => $code,
            ],
            [
                'name' => $name,
                'account_group_id' => $accountGroup->id,
                'account_type' => $type,
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_active' => true,
                'is_system_account' => true,
                'description' => "System generated account for payroll - {$name}",
                'created_by' => $this->payrollPeriod->created_by ?? auth()->id(),
            ]
        );
    }

    /**
     * Create journal entries for payroll
     */
    public function createJournalEntries(): void
    {
        DB::transaction(function () {
            // Create salary expense voucher
            $this->createSalaryExpenseVoucher();

            // Create tax liability voucher
            $this->createTaxLiabilityVoucher();

            // Create NSITF voucher
            $this->createNSITFVoucher();

            // Update payroll period with voucher references
            $this->updatePayrollVoucherReferences();
        });
    }

    /**
     * Create salary expense voucher
     */
    private function createSalaryExpenseVoucher(): void
    {
        $voucherType = $this->getOrCreateVoucherType('Journal Entry', 'JE');

        $voucher = Voucher::create([
            'tenant_id' => $this->payrollPeriod->tenant_id,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $this->generateVoucherNumber('SAL'),
            'voucher_date' => $this->payrollPeriod->pay_date,
            'narration' => "Salary expense for {$this->payrollPeriod->name}",
            'total_amount' => $this->payrollPeriod->total_gross,
            'status' => 'posted',
            'created_by' => $this->payrollPeriod->created_by,
        ]);

        // Debit: Salary Expense
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $this->ledgerAccounts['salary_expense']->id,
            'particulars' => 'Being salary expense for the period',
            'debit_amount' => $this->payrollPeriod->total_gross,
            'credit_amount' => 0,
        ]);

        // Credit: Salary Payable
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $this->ledgerAccounts['salary_payable']->id,
            'particulars' => 'Being salary payable for the period',
            'debit_amount' => 0,
            'credit_amount' => $this->payrollPeriod->total_gross,
        ]);

        // Update account balances
        $this->updateAccountBalance($this->ledgerAccounts['salary_expense']);
        $this->updateAccountBalance($this->ledgerAccounts['salary_payable']);

        // Store voucher reference in payroll runs
        foreach ($this->payrollPeriod->payrollRuns as $run) {
            $run->update(['salary_expense_voucher_id' => $voucher->id]);
        }
    }

    /**
     * Create tax liability voucher
     */
    private function createTaxLiabilityVoucher(): void
    {
        if ($this->payrollPeriod->total_tax <= 0) return;

        $voucherType = $this->getOrCreateVoucherType('Journal Entry', 'JE');

        $voucher = Voucher::create([
            'tenant_id' => $this->payrollPeriod->tenant_id,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $this->generateVoucherNumber('TAX'),
            'voucher_date' => $this->payrollPeriod->pay_date,
            'narration' => "PAYE tax liability for {$this->payrollPeriod->name}",
            'total_amount' => $this->payrollPeriod->total_tax,
            'status' => 'posted',
            'created_by' => $this->payrollPeriod->created_by,
        ]);

        // Debit: Salary Payable (reducing the liability)
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $this->ledgerAccounts['salary_payable']->id,
            'particulars' => 'Being PAYE tax deduction from salary',
            'debit_amount' => $this->payrollPeriod->total_tax,
            'credit_amount' => 0,
        ]);

        // Credit: Tax Payable
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $this->ledgerAccounts['tax_payable']->id,
            'particulars' => 'Being PAYE tax payable to government',
            'debit_amount' => 0,
            'credit_amount' => $this->payrollPeriod->total_tax,
        ]);

        // Update account balances
        $this->updateAccountBalance($this->ledgerAccounts['salary_payable']);
        $this->updateAccountBalance($this->ledgerAccounts['tax_payable']);

        // Store voucher reference in payroll runs
        foreach ($this->payrollPeriod->payrollRuns as $run) {
            $run->update(['tax_payable_voucher_id' => $voucher->id]);
        }
    }

    /**
     * Create NSITF voucher (employer contribution)
     */
    private function createNSITFVoucher(): void
    {
        if ($this->payrollPeriod->total_nsitf <= 0) return;

        $voucherType = $this->getOrCreateVoucherType('Journal Entry', 'JE');

        $voucher = Voucher::create([
            'tenant_id' => $this->payrollPeriod->tenant_id,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $this->generateVoucherNumber('NSITF'),
            'voucher_date' => $this->payrollPeriod->pay_date,
            'narration' => "NSITF contribution for {$this->payrollPeriod->name}",
            'total_amount' => $this->payrollPeriod->total_nsitf,
            'status' => 'posted',
            'created_by' => $this->payrollPeriod->created_by,
        ]);

        // Debit: Salary Expense (additional employer cost)
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $this->ledgerAccounts['salary_expense']->id,
            'particulars' => 'Being NSITF contribution (employer)',
            'debit_amount' => $this->payrollPeriod->total_nsitf,
            'credit_amount' => 0,
        ]);

        // Credit: NSITF Payable
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $this->ledgerAccounts['nsitf_payable']->id,
            'particulars' => 'Being NSITF payable to government',
            'debit_amount' => 0,
            'credit_amount' => $this->payrollPeriod->total_nsitf,
        ]);

        // Update account balances
        $this->updateAccountBalance($this->ledgerAccounts['salary_expense']);
        $this->updateAccountBalance($this->ledgerAccounts['nsitf_payable']);
    }

    /**
     * Create payment voucher when salaries are paid
     */
    public function createPaymentVoucher(): void
    {
        $voucherType = $this->getOrCreateVoucherType('Payment Voucher', 'PV');

        $voucher = Voucher::create([
            'tenant_id' => $this->payrollPeriod->tenant_id,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $this->generateVoucherNumber('PAY'),
            'voucher_date' => now(),
            'narration' => "Salary payment for {$this->payrollPeriod->name}",
            'total_amount' => $this->payrollPeriod->total_net,
            'status' => 'posted',
            'created_by' => auth()->id(),
        ]);

        // Debit: Salary Payable
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $this->ledgerAccounts['salary_payable']->id,
            'particulars' => 'Being salary payment to employees',
            'debit_amount' => $this->payrollPeriod->total_net,
            'credit_amount' => 0,
        ]);

        // Credit: Bank Account
        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $this->ledgerAccounts['bank_account']->id,
            'particulars' => 'Being bank payment for salaries',
            'debit_amount' => 0,
            'credit_amount' => $this->payrollPeriod->total_net,
        ]);

        // Update account balances
        $this->updateAccountBalance($this->ledgerAccounts['salary_payable']);
        $this->updateAccountBalance($this->ledgerAccounts['bank_account']);
    }

    /**
     * Get or create voucher type
     */
    private function getOrCreateVoucherType(string $name, string $code): VoucherType
    {
        // Generate abbreviation from code (first 2-3 letters)
        $abbreviation = strtoupper(substr($code, 0, min(3, strlen($code))));

        return VoucherType::firstOrCreate(
            [
                'tenant_id' => $this->payrollPeriod->tenant_id,
                'code' => $code
            ],
            [
                'name' => $name,
                'abbreviation' => $abbreviation,
                'description' => "System generated voucher type for {$name}",
                'numbering_method' => 'auto',
                'prefix' => $code . '-',
                'starting_number' => 1,
                'current_number' => 0,
                'has_reference' => false,
                'affects_inventory' => false,
                'affects_cashbank' => false,
                'is_system_defined' => true,
                'is_active' => true,
            ]
        );
    }

    /**
     * Generate voucher number
     */
    private function generateVoucherNumber(string $prefix): string
    {
        $date = $this->payrollPeriod->pay_date->format('Ym');
        $count = Voucher::where('tenant_id', $this->payrollPeriod->tenant_id)
            ->where('voucher_number', 'like', "{$prefix}-{$date}%")
            ->count() + 1;

        return "{$prefix}-{$date}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update account balance
     */
    private function updateAccountBalance(LedgerAccount $account): void
    {
        $account->updateCurrentBalance();
    }

    /**
     * Update payroll period with voucher references
     */
    private function updatePayrollVoucherReferences(): void
    {
        // This can be extended to store additional voucher references if needed
    }
}
