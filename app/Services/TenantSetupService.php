<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\AccountGroup;
use Illuminate\Support\Facades\DB;

class TenantSetupService
{
    public function createDefaultLedgerAccounts(Tenant $tenant)
    {
        DB::transaction(function () use ($tenant) {
            $this->createDefaultAccounts($tenant->id);
        });
    }

    private function createDefaultAccounts($tenantId)
    {
        // Get account groups
        $accountGroups = $this->getAccountGroups($tenantId);

        $defaultAccounts = [
            // Cash & Bank Accounts
            [
                'name' => 'Cash in Hand',
                'code' => 'CASH-001',
                'account_group_id' => $accountGroups['current_assets']?->id,
                'account_type' => 'asset',
                'description' => 'Physical cash available in the business',
                'is_system_account' => true,
            ],
            [
                'name' => 'Bank Account - Current',
                'code' => 'BANK-001',
                'account_group_id' => $accountGroups['current_assets']?->id,
                'account_type' => 'asset',
                'description' => 'Primary bank current account',
                'is_system_account' => true,
            ],
            [
                'name' => 'Petty Cash',
                'code' => 'PETTY-001',
                'account_group_id' => $accountGroups['current_assets']?->id,
                'account_type' => 'asset',
                'description' => 'Small cash fund for minor expenses',
                'is_system_account' => true,
            ],

            // Receivables
            [
                'name' => 'Accounts Receivable',
                'code' => 'AR-001',
                'account_group_id' => $accountGroups['current_assets']?->id,
                'account_type' => 'asset',
                'description' => 'Money owed by customers',
                'is_system_account' => true,
            ],
            [
                'name' => 'Sales Ledger',
                'code' => 'SALES-LED-001',
                'account_group_id' => $accountGroups['current_assets']?->id,
                'account_type' => 'asset',
                'description' => 'Customer receivables ledger',
                'is_system_account' => true,
            ],

            // Inventory
            [
                'name' => 'Stock in Hand',
                'code' => 'STOCK-001',
                'account_group_id' => $accountGroups['current_assets']?->id,
                'account_type' => 'asset',
                'description' => 'Inventory/Stock on hand',
                'is_system_account' => true,
            ],

            // Payables
            [
                'name' => 'Accounts Payable',
                'code' => 'AP-001',
                'account_group_id' => $accountGroups['current_liabilities']?->id,
                'account_type' => 'liability',
                'description' => 'Money owed to suppliers',
                'is_system_account' => true,
            ],
            [
                'name' => 'Purchase Ledger',
                'code' => 'PURCH-LED-001',
                'account_group_id' => $accountGroups['current_liabilities']?->id,
                'account_type' => 'liability',
                'description' => 'Supplier payables ledger',
                'is_system_account' => true,
            ],

            // Tax Accounts
            [
                'name' => 'VAT Output',
                'code' => 'VAT-OUT-001',
                'account_group_id' => $accountGroups['current_liabilities']?->id,
                'account_type' => 'liability',
                'description' => 'VAT collected from customers',
                'is_system_account' => true,
            ],
            [
                'name' => 'VAT Input',
                'code' => 'VAT-IN-001',
                'account_group_id' => $accountGroups['current_assets']?->id,
                'account_type' => 'asset',
                'description' => 'VAT paid to suppliers',
                'is_system_account' => true,
            ],
            [
                'name' => 'PAYE Tax Payable',
                'code' => 'PAYE-001',
                'account_group_id' => $accountGroups['current_liabilities']?->id,
                'account_type' => 'liability',
                'description' => 'Pay As You Earn tax payable',
                'is_system_account' => true,
            ],
            [
                'name' => 'Withholding Tax Payable',
                'code' => 'WHT-001',
                'account_group_id' => $accountGroups['current_liabilities']?->id,
                'account_type' => 'liability',
                'description' => 'Withholding tax payable to FIRS',
                'is_system_account' => true,
            ],

            // Sales/Revenue Accounts
            [
                'name' => 'Sales Revenue',
                'code' => 'SALES-001',
                'account_group_id' => $accountGroups['direct_income']?->id,
                'account_type' => 'income',
                'description' => 'Revenue from sales of goods',
                'is_system_account' => true,
            ],
            [
                'name' => 'Service Income',
                'code' => 'SERV-001',
                'account_group_id' => $accountGroups['direct_income']?->id,
                'account_type' => 'income',
                'description' => 'Income from services provided',
                'is_system_account' => true,
            ],
            [
                'name' => 'Sales Returns',
                'code' => 'SALES-RET-001',
                'account_group_id' => $accountGroups['direct_income']?->id,
                'account_type' => 'income',
                'description' => 'Returns and allowances on sales',
                'is_system_account' => true,
                'normal_balance' => 'debit', // Contra-revenue account
            ],
            [
                'name' => 'Discount Allowed',
                'code' => 'DISC-ALL-001',
                'account_group_id' => $accountGroups['direct_income']?->id,
                'account_type' => 'income',
                'description' => 'Discounts given to customers',
                'is_system_account' => true,
                'normal_balance' => 'debit', // Contra-revenue account
            ],

            // Purchase/COGS Accounts
            [
                'name' => 'Purchases',
                'code' => 'PURCH-001',
                'account_group_id' => $accountGroups['direct_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Purchases of goods for resale',
                'is_system_account' => true,
            ],
            [
                'name' => 'Cost of Goods Sold',
                'code' => 'COGS-001',
                'account_group_id' => $accountGroups['direct_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Direct cost of goods sold',
                'is_system_account' => true,
            ],
            [
                'name' => 'Purchase Returns',
                'code' => 'PURCH-RET-001',
                'account_group_id' => $accountGroups['direct_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Returns to suppliers',
                'is_system_account' => true,
                'normal_balance' => 'credit', // Contra-expense account
            ],
            [
                'name' => 'Discount Received',
                'code' => 'DISC-REC-001',
                'account_group_id' => $accountGroups['indirect_income']?->id,
                'account_type' => 'income',
                'description' => 'Discounts received from suppliers',
                'is_system_account' => true,
            ],

            // Operating Expenses
            [
                'name' => 'Office Rent',
                'code' => 'RENT-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Monthly office rent expense',
                'is_system_account' => true,
            ],
            [
                'name' => 'Salaries & Wages',
                'code' => 'SAL-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Employee salaries and wages',
                'is_system_account' => true,
            ],
            [
                'name' => 'Electricity Expense',
                'code' => 'ELEC-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Electricity and power expenses',
                'is_system_account' => true,
            ],
            [
                'name' => 'Telephone & Internet',
                'code' => 'TEL-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Communication expenses',
                'is_system_account' => true,
            ],
            [
                'name' => 'Bank Charges',
                'code' => 'BANK-CHG-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Bank fees and charges',
                'is_system_account' => true,
            ],
            [
                'name' => 'Office Supplies',
                'code' => 'SUPP-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Office supplies and stationery',
                'is_system_account' => true,
            ],
            [
                'name' => 'Transportation',
                'code' => 'TRANS-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Transportation and travel expenses',
                'is_system_account' => true,
            ],
            [
                'name' => 'Professional Fees',
                'code' => 'PROF-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Legal, accounting, and professional fees',
                'is_system_account' => true,
            ],
            [
                'name' => 'Marketing & Advertising',
                'code' => 'MARK-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Marketing and advertising expenses',
                'is_system_account' => true,
            ],
            [
                'name' => 'Insurance',
                'code' => 'INS-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Insurance premiums',
                'is_system_account' => true,
            ],
            [
                'name' => 'Depreciation Expense',
                'code' => 'DEPR-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Depreciation on fixed assets',
                'is_system_account' => true,
            ],

            // Other Income
            [
                'name' => 'Interest Income',
                'code' => 'INT-INC-001',
                'account_group_id' => $accountGroups['indirect_income']?->id,
                'account_type' => 'income',
                'description' => 'Interest earned on bank deposits',
                'is_system_account' => true,
            ],
            [
                'name' => 'Other Income',
                'code' => 'OTHER-INC-001',
                'account_group_id' => $accountGroups['indirect_income']?->id,
                'account_type' => 'income',
                'description' => 'Miscellaneous income',
                'is_system_account' => true,
            ],

            // Other Expenses
            [
                'name' => 'Interest Expense',
                'code' => 'INT-EXP-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Interest paid on loans',
                'is_system_account' => true,
            ],
            [
                'name' => 'Miscellaneous Expense',
                'code' => 'MISC-001',
                'account_group_id' => $accountGroups['indirect_expenses']?->id,
                'account_type' => 'expense',
                'description' => 'Other miscellaneous expenses',
                'is_system_account' => true,
            ],

            // Equity Accounts
            [
                'name' => 'Owner\'s Capital',
                'code' => 'CAPITAL-001',
                'account_group_id' => $accountGroups['capital']?->id,
                'account_type' => 'equity',
                'description' => 'Owner\'s capital investment',
                'is_system_account' => true,
            ],
            [
                'name' => 'Owner\'s Drawings',
                'code' => 'DRAW-001',
                'account_group_id' => $accountGroups['capital']?->id,
                'account_type' => 'equity',
                'description' => 'Owner\'s withdrawals',
                'is_system_account' => true,
                'normal_balance' => 'debit', // Contra-equity account
            ],
            [
                'name' => 'Retained Earnings',
                'code' => 'RETAIN-001',
                'account_group_id' => $accountGroups['capital']?->id,
                'account_type' => 'equity',
                'description' => 'Accumulated retained earnings',
                'is_system_account' => true,
            ],
        ];

        foreach ($defaultAccounts as $accountData) {
            $accountData['tenant_id'] = $tenantId;
            $accountData['opening_balance'] = 0;
            $accountData['current_balance'] = 0;
            $accountData['is_active'] = true;
            $accountData['normal_balance'] = $accountData['normal_balance'] ?? $this->getNormalBalance($accountData['account_type']);
            $accountData['created_at'] = now();
            $accountData['updated_at'] = now();

            LedgerAccount::create($accountData);
        }
    }

    private function getAccountGroups($tenantId)
    {
        $groups = AccountGroup::where('tenant_id', $tenantId)->get()->keyBy('name');

        return [
            'current_assets' => $groups->get('Current Assets'),
            'fixed_assets' => $groups->get('Fixed Assets'),
            'current_liabilities' => $groups->get('Current Liabilities'),
            'long_term_liabilities' => $groups->get('Long Term Liabilities'),
            'direct_income' => $groups->get('Direct Income'),
            'indirect_income' => $groups->get('Indirect Income'),
            'direct_expenses' => $groups->get('Direct Expenses'),
            'indirect_expenses' => $groups->get('Indirect Expenses'),
            'capital' => $groups->get('Capital Account'),
        ];
    }

    private function getNormalBalance($accountType)
    {
        return match($accountType) {
            'asset', 'expense' => 'debit',
            'liability', 'income', 'equity' => 'credit',
            default => 'debit'
        };
    }
}