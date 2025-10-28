<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LedgerAccount;
use App\Models\AccountGroup;

class DefaultLedgerAccountsSeeder extends Seeder
{
    public static function seedForTenant($tenantId)
    {
        // Check if ledger accounts already exist for this tenant
        $existingAccounts = LedgerAccount::where('tenant_id', $tenantId)->count();
        if ($existingAccounts > 0) {
            return; // Skip seeding if accounts already exist
        }

        // Get account groups for this tenant
        $accountGroups = AccountGroup::where('tenant_id', $tenantId)->get()->keyBy('name');

        $defaultAccounts = [
            // CURRENT ASSETS
            [
                'name' => 'Cash in Hand',
                'code' => 'CASH-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Physical cash available in the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            // Note: Bank accounts are now created via the Bank model
            // which automatically creates linked ledger accounts
            [
                'name' => 'Petty Cash',
                'code' => 'PETTY-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Small cash fund for minor expenses',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
              [
                'name' => 'Short-term Investments',
                'code' => 'ST-INV-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Short-term investments',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

             [
                'name' => 'Prepaid Expenses',
                'code' => 'PREPAID-EXP-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Expenses paid in advance  (Rent, Insurance)',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
              [
                'name' => 'Accrued Income',
                'code' => 'ACCRUED-INC-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Income earned but not yet received',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Fixed Deposits',
                'code' => 'FIXED-DEP-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Funds held in fixed deposits',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Inventory',
                'code' => 'INV-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Goods available for sale (Raw Materials, Work-in-Progress, Finished Goods)',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Accounts Receivable',
                'code' => 'AR-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Money owed by customers - Debtors',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sales Ledger',
                'code' => 'SALES-LED-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Customer receivables ledger',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Stock in Hand',
                'code' => 'STOCK-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Inventory/Stock on hand',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'VAT Input',
                'code' => 'VAT-IN-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'VAT paid to suppliers',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'PAYE Tax Receivable',
                'code' => 'PAYE-REC-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Pay As You Earn tax receivable from employees',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Withholding Tax Receivable',
                'code' => 'WHT-REC-001',
                'account_group_id' => $accountGroups->get('Current Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Withholding tax receivable from FIRS',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Fixed Assets',
                'code' => 'FIXED-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Tangible fixed assets (Property, Plant, Equipment)',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Accumulated Depreciation',
                'code' => 'ACCUM-DEPR-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Accumulated depreciation on fixed assets',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Intangible Assets',
                'code' => 'INTANG-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Intangible assets (Patents, Trademarks)',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Long-term Investments',
                'code' => 'LT-INV-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Long-term investments in other companies',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Goodwill',
                'code' => 'GOODWILL-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Goodwill from business acquisitions',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Advance Payments to Suppliers',
                'code' => 'ADVANCE-SUP-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Advance payments made to suppliers',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Land',
                'code' => 'LAND-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Land owned by the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Buildings',
                'code' => 'BUILD-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Buildings owned by the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Machinery',
                'code' => 'MACH-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Machinery owned by the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Vehicles',
                'code' => 'VEH-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Vehicles owned by the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Furniture & Fixtures',
                'code' => 'FURN-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Office furniture and fixtures',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Computer Equipment',
                'code' => 'COMP-EQ-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Computers and IT equipment',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Office Equipment',
                'code' => 'OFF-EQ-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Office furniture and fixtures',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Leasehold Improvements',
                'code' => 'LEASEHOLD-IMP-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Improvements made to leased property',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            // Intangible Assets
            [
                'name' => 'Patents',
                'code' => 'PATENT-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Patents owned by the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Trademarks',
                'code' => 'TRADEMARK-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Trademarks owned by the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Copyrights',
                'code' => 'COPYRIGHT-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Copyrights owned by the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Software Licenses',
                'code' => 'SOFT-LIC-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Software licenses owned by the business',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Accumulated Depreciation - Fixed Assets',
                'code' => 'ACCUM-DEPR-FIXED-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Accumulated depreciation on fixed assets',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Investments (Long-term)
            [
                'name' => 'Long-term Investments',
                'code' => 'LT-INVEST-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Long-term investments in other companies',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            // Real Estate Investments
            [
                'name' => 'Real Estate Investments',
                'code' => 'REAL-EST-INV-001',
                'account_group_id' => $accountGroups->get('Fixed Assets')?->id,
                'account_type' => 'asset',
                'description' => 'Investments in real estate properties',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            // CURRENT LIABILITIES
            [
                'name' => 'Accounts Payable',
                'code' => 'AP-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Money owed to suppliers - Creditors',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Purchase Ledger',
                'code' => 'PURCH-LED-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Supplier payables ledger',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'VAT Output',
                'code' => 'VAT-OUT-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'VAT collected from customers',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'PAYE Tax Payable',
                'code' => 'PAYE-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Pay As You Earn tax payable',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Withholding Tax Payable',
                'code' => 'WHT-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Withholding tax payable to FIRS',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Unearned Revenue',
                'code' => 'UN-REV-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Revenue received in advance',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Dividends Payable',
                'code' => 'DIV-PAY-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Dividends declared but not paid',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Accrued Expenses',
                'code' => 'ACCRUED-EXP-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Expenses incurred but not yet paid',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Bank Loans - Short-term',
                'code' => 'BANK-LOAN-ST-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Short-term bank loans payable',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Bank Loans - Current',
                'code' => 'BANK-LOAN-CUR-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Current portion of bank loans payable',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Short-term Loans',
                'code' => 'SHORT-TERM-LOAN-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Short-term loans payable',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Mortgage Payable',
                'code' => 'MORTGAGE-PAYABLE-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Mortgage payable',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Deferred Tax Liabilities
            [
                'name' => 'Deferred Tax Liabilities',
                'code' => 'DEFERRED-TAX-LIAB-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Deferred tax liabilities',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Lease Obligations
            [
                'name' => 'Lease Obligations',
                'code' => 'LEASE-OBLIG-001',
                'account_group_id' => $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'liability',
                'description' => 'Lease obligations payable',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],


            // DIRECT INCOME
            [
                'name' => 'Sales Revenue',
                'code' => 'SALES-001',
                'account_group_id' => $accountGroups->get('Direct Income')?->id,
                'account_type' => 'income',
                'description' => 'Revenue from sales of goods',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Service Income',
                'code' => 'SERV-001',
                'account_group_id' => $accountGroups->get('Direct Income')?->id,
                'account_type' => 'income',
                'description' => 'Income from services provided',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sales Returns',
                'code' => 'SALES-RET-001',
                'account_group_id' => $accountGroups->get('Direct Income')?->id,
                'account_type' => 'income',
                'description' => 'Returns and allowances on sales',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Commission Income
            [
                'name' => 'Commission Income',
                'code' => 'COMM-INC-001',
                'account_group_id' => $accountGroups->get('Direct Income')?->id,
                'account_type' => 'income',
                'description' => 'Commission earned from sales or services',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            //Rental Income
            [
                'name' => 'Rental Income',
                'code' => 'RENT-INC-001',
                'account_group_id' => $accountGroups->get('Direct Income')?->id,
                'account_type' => 'income',
                'description' => 'Income from renting out property or equipment',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Professional Fees income
            [
                'name' => 'Professional Fees Income',
                'code' => 'PROF-FEES-INC-001',
                'account_group_id' => $accountGroups->get('Direct Income')?->id,
                'account_type' => 'income',
                'description' => 'Income from professional services',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            // INDIRECT INCOME
            [
                'name' => 'Interest Income',
                'code' => 'INT-INC-001',
                'account_group_id' => $accountGroups->get('Indirect Income')?->id,
                'account_type' => 'income',
                'description' => 'Interest earned on bank deposits',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Discount Received',
                'code' => 'DISC-REC-001',
                'account_group_id' => $accountGroups->get('Indirect Income')?->id,
                'account_type' => 'income',
                'description' => 'Discounts received from suppliers',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Other Income',
                'code' => 'OTHER-INC-001',
                'account_group_id' => $accountGroups->get('Indirect Income')?->id,
                'account_type' => 'income',
                'description' => 'Miscellaneous income',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Gain on Sale of Assets
            [
                'name' => 'Gain on Sale of Assets',
                'code' => 'GAIN-ON-SALE-001',
                'account_group_id' => $accountGroups->get('Direct Income')?->id,
                'account_type' => 'income',
                'description' => 'Profit from selling assets',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Foreign Exchange Gain
            [
                'name' => 'Foreign Exchange Gain',
                'code' => 'FOREX-GAIN-001',
                'account_group_id' => $accountGroups->get('Indirect Income')?->id,
                'account_type' => 'income',
                'description' => 'Gain from foreign currency transactions',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Royalty Income
            [
                'name' => 'Royalty Income',
                'code' => 'ROYALTY-INC-001',
                'account_group_id' => $accountGroups->get('Indirect Income')?->id,
                'account_type' => 'income',
                'description' => 'Income from royalties or intellectual property rights',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Miscellaneous Income
            [
                'name' => 'Miscellaneous Income',
                'code' => 'MISC-INC-001',
                'account_group_id' => $accountGroups->get('Indirect Income')?->id,
                'account_type' => 'income',
                'description' => 'Other income not categorized elsewhere',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],


            // DIRECT EXPENSES
            [
                'name' => 'Purchases',
                'code' => 'PURCH-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Purchases of goods for resale',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Cost of Goods Sold',
                'code' => 'COGS-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Direct cost of goods sold',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Purchase Returns',
                'code' => 'PURCH-RET-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Returns to suppliers',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Raw Material Purchases
            [
                'name' => 'Raw Material Purchases',
                'code' => 'RAW-MAT-PURCH-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Purchases of raw materials for production',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Factory Overhead
            [
                'name' => 'Factory Overhead',
                'code' => 'FACTORY-OVERHEAD-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Indirect costs related to manufacturing',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
         //Packaging Costs
            [
                'name' => 'Packaging Costs',
                'code' => 'PACKAGING-COSTS-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Costs related to packaging products',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Shipping Costs
            [
                'name' => 'Shipping Costs',
                'code' => 'SHIPPING-COSTS-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Costs related to shipping products',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            //Manufacturing Expenses
            [
                'name' => 'Manufacturing Expenses',
                'code' => 'MANUFACTURING-EXP-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Expenses directly related to manufacturing',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Labor Costs
            [
                'name' => 'Labor Costs',
                'code' => 'LABOR-COSTS-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Costs associated with labor',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Freight Costs',
                'code' => 'FREIGHT-COSTS-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Costs related to freight and transportation',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Customs Duties',
                'code' => 'CUSTOMS-DUTIES-001',
                'account_group_id' => $accountGroups->get('Direct Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Customs duties and tariffs on imported goods',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],


            // INDIRECT EXPENSES
            [
                'name' => 'Office Rent',
                'code' => 'RENT-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Monthly office rent expense',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Salaries & Wages',
                'code' => 'SAL-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Employee salaries and wages',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Electricity Expense',
                'code' => 'ELEC-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Electricity and power expenses',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Telephone & Internet',
                'code' => 'TEL-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Communication expenses',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Bank Charges',
                'code' => 'BANK-CHG-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Bank fees and charges',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Office Supplies',
                'code' => 'SUPP-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Office supplies and stationery',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Transportation',
                'code' => 'TRANS-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Transportation and travel expenses',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Fees',
                'code' => 'PROF-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Legal, accounting, and professional fees',
                'opening_balance' => 0,
                'current_balance' => 0,
                             'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Marketing & Advertising',
                'code' => 'MARK-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Marketing and advertising expenses',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Insurance',
                'code' => 'INS-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Insurance premiums',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Depreciation Expense',
                'code' => 'DEPR-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Depreciation on fixed assets',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Interest Expense',
                'code' => 'INT-EXP-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Interest paid on loans',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Miscellaneous Expense',
                'code' => 'MISC-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Other miscellaneous expenses',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Repairs & Maintenance
            [
                'name' => 'Repairs & Maintenance',
                'code' => 'REPAIRS-MAINT-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Costs related to repairs and maintenance',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Bad Debts
            [
                'name' => 'Bad Debts',
                'code' => 'BAD-DEBTS-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Uncollectible accounts receivable',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Travel & Entertainment
            [
                'name' => 'Travel & Entertainment',
                'code' => 'TRAVEL-ENT-001',
                'account_group_id' => $accountGroups->get('Indirect Expenses')?->id,
                'account_type' => 'expense',
                'description' => 'Travel and entertainment expenses',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],

            // CAPITAL ACCOUNTS (if you have Capital Account group)
            [
                'name' => 'Owner\'s Capital',
                'code' => 'CAPITAL-001',
                'account_group_id' => $accountGroups->get('Capital Account')?->id ?? $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'equity',
                'description' => 'Owner\'s capital investment',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Owner\'s Drawings',
                'code' => 'DRAW-001',
                'account_group_id' => $accountGroups->get('Capital Account')?->id ?? $accountGroups->get('Current Assets')?->id,
                'account_type' => 'equity',
                'description' => 'Owner\'s withdrawals',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Retained Earnings',
                'code' => 'RETAIN-001',
                'account_group_id' => $accountGroups->get('Capital Account')?->id ?? $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'equity',
                'description' => 'Accumulated retained earnings',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Partner’s Capital
            [
                'name' => 'Partner\'s Capital',
                'code' => 'PARTNER-CAP-001',
                'account_group_id' => $accountGroups->get('Capital Account')?->id ?? $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'equity',
                'description' => 'Capital contributed by partners',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Shareholder’s Equity
            [
                'name' => 'Shareholder\'s Equity',
                'code' => 'SHAREHOLDER-EQ-001',
                'account_group_id' => $accountGroups->get('Capital Account')?->id ?? $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'equity',
                'description' => 'Equity held by shareholders',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
            //Dividend Reserve
            [
                'name' => 'Dividend Reserve',
                'code' => 'DIVIDEND-RESERVE-001',
                'account_group_id' => $accountGroups->get('Capital Account')?->id ?? $accountGroups->get('Current Liabilities')?->id,
                'account_type' => 'equity',
                'description' => 'Reserve for future dividend payments',
                'opening_balance' => 0,
                'current_balance' => 0,
                'is_system_account' => true,
                'is_active' => true,
            ],
        ];

        // Filter out accounts with missing account groups
        $validAccounts = array_filter($defaultAccounts, function($accountData) {
            return !empty($accountData['account_group_id']);
        });

        \Log::info("Starting ledger account seeding", [
            'tenant_id' => $tenantId,
            'total_accounts' => count($defaultAccounts),
            'valid_accounts' => count($validAccounts),
            'skipped' => count($defaultAccounts) - count($validAccounts)
        ]);

        // Prepare all accounts with tenant_id and timestamps
        $preparedAccounts = array_map(function($accountData) use ($tenantId) {
            $accountData['tenant_id'] = $tenantId;
            $accountData['created_at'] = now();
            $accountData['updated_at'] = now();
            return $accountData;
        }, $validAccounts);

        // Seed accounts in chunks to avoid timeout and prepared statement issues
        $chunkSize = 20; // Process 20 accounts at a time
        $chunks = array_chunk($preparedAccounts, $chunkSize);
        $successCount = 0;
        $failedCount = 0;
        $failedAccounts = [];

        foreach ($chunks as $chunkIndex => $chunk) {
            try {
                // Refresh database connection before each chunk
                \DB::disconnect();
                \DB::reconnect();

                // Small delay between chunks to prevent overwhelming the server
                if ($chunkIndex > 0) {
                    usleep(200000); // 200ms delay
                }

                // Insert chunk using batch insert for better performance
                \DB::transaction(function() use ($chunk, &$successCount, &$failedAccounts) {
                    foreach ($chunk as $accountData) {
                        try {
                            LedgerAccount::create($accountData);
                            $successCount++;
                        } catch (\Exception $e) {
                            $failedCount++;
                            $failedAccounts[] = [
                                'name' => $accountData['name'],
                                'code' => $accountData['code'],
                                'error' => $e->getMessage()
                            ];
                            \Log::error("Failed to create ledger account", [
                                'account_name' => $accountData['name'],
                                'account_code' => $accountData['code'],
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }, 3); // Retry transaction up to 3 times

                \Log::info("Chunk processed", [
                    'chunk' => $chunkIndex + 1,
                    'total_chunks' => count($chunks),
                    'chunk_size' => count($chunk),
                    'success_count' => $successCount
                ]);

            } catch (\Exception $e) {
                $failedCount += count($chunk);
                \Log::error("Chunk processing failed", [
                    'chunk' => $chunkIndex + 1,
                    'chunk_size' => count($chunk),
                    'error' => $e->getMessage()
                ]);

                // Continue with next chunk even if this one fails
                continue;
            }
        }

        \Log::info("Ledger account seeding completed", [
            'tenant_id' => $tenantId,
            'total_attempted' => count($validAccounts),
            'successful' => $successCount,
            'failed' => $failedCount,
            'failed_accounts' => $failedAccounts
        ]);

        // Throw exception if critical accounts failed
        if ($successCount === 0) {
            throw new \Exception("Failed to seed any ledger accounts for tenant {$tenantId}");
        }

        // Log warning if some accounts failed but continue
        if ($failedCount > 0) {
            \Log::warning("Some ledger accounts failed to seed", [
                'tenant_id' => $tenantId,
                'failed_count' => $failedCount,
                'success_rate' => round(($successCount / count($validAccounts)) * 100, 2) . '%'
            ]);
        }
    }

    public function run()
    {
        // This method can be used for standalone seeding if needed
        $tenantId = $this->command->option('tenant-id');

        if ($tenantId) {
            self::seedForTenant($tenantId);
            $this->command->info("Default ledger accounts seeded for tenant ID: {$tenantId}");
        } else {
            $this->command->error('Please provide --tenant-id option');
        }
    }
}
