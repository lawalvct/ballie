<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Pricing Discount Structure:
         * Monthly   - No discount (base price)
         * Quarterly - Half-month discount (3 months minus ½ month)
         * Bi-Annual - 1 month discount  (6 months minus 1 month)
         * Yearly    - 2 months discount  (12 months minus 2 months)
         */
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small businesses and startups getting started with digital tools.',
                'monthly_price'   => 750000,   // ₦7,500
                'quarterly_price' => 1875000,   // ₦18,750 (₦7,500×3 - ₦3,750)
                'biannual_price'  => 3750000,   // ₦37,500 (₦7,500×6 - ₦7,500)
                'yearly_price'    => 7500000,   // ₦75,000 (₦7,500×12 - ₦15,000)
                'max_users' => 5,
                'max_customers' => 100,
                'has_pos' => false,
                'has_payroll' => false,
                'has_api_access' => false,
                'has_advanced_reports' => false,
                'has_ecommerce' => false,
                'has_audit_log' => false,
                'has_multi_location' => false,
                'has_multi_currency' => false,
                'support_level' => 'email',
                'is_popular' => false,
                'sort_order' => 1,
                'features' => [
                    'Up to 5 users',
                    'Up to 100 customers',
                    // Accounting (Basic)
                    'Chart of Accounts (Standard Nigerian COA)',
                    'Vouchers & Journal Entries',
                    'Financial Statements (P&L, Balance Sheet)',
                    'Ledger & Daybook',
                    // Inventory (Basic)
                    'Real-time Stock Tracking',
                    'Product & Category Management',
                    // CRM (Basic)
                    'Customer & Vendor Profiles',
                    'Basic Balance Tracking',
                    // Reports (Standard)
                    'Real-time Dashboard',
                    'Standard Financial Reports',
                    // AI (Basic)
                    'BallieAI Basic Q&A Assistant',
                    // Other
                    'Mobile App Access',
                    'Smart Search',
                    'Email Support',
                ],
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing businesses that need advanced tools and automation.',
                'monthly_price'   => 1000000,    // ₦10,000
                'quarterly_price' => 2500000,    // ₦25,000 (₦10,000×3 - ₦5,000)
                'biannual_price'  => 5000000,    // ₦50,000 (₦10,000×6 - ₦10,000)
                'yearly_price'    => 10000000,   // ₦100,000 (₦10,000×12 - ₦20,000)
                'max_users' => 15,
                'max_customers' => null,
                'has_pos' => true,
                'has_payroll' => true,
                'has_api_access' => true,
                'has_advanced_reports' => true,
                'has_ecommerce' => false,
                'has_audit_log' => true,
                'has_multi_location' => false,
                'has_multi_currency' => true,
                'support_level' => 'priority',
                'is_popular' => true,
                'sort_order' => 2,
                'features' => [
                    'Up to 15 users',
                    'Unlimited customers',
                    // Accounting (Advanced)
                    'Full Chart of Accounts with Custom Groups',
                    'Vouchers with Approval Workflows',
                    'Full Financial Statements & Cash Flow',
                    'Bank Reconciliation (Auto-matching)',
                    'Multi-Currency Support',
                    'Complete Ledger & Daybook',
                    // Inventory (Advanced)
                    'Full Real-time Stock Tracking with Barcode',
                    'Product Variants & CSV Import',
                    'Supplier Management & Purchase Orders',
                    'COGS & Costing (FIFO, LIFO, Weighted Avg)',
                    'Inventory Valuation Reports',
                    // CRM (Advanced)
                    'Advanced Customer & Vendor Profiles',
                    'Sales Pipeline & Lead Management',
                    'Communication & Follow-up Tracking',
                    'CRM Reports & Conversion Analytics',
                    // POS
                    'POS System (Fast Checkout, Barcode)',
                    'Cash Register Management',
                    'Professional Receipts & Invoices',
                    // Payroll (Basic)
                    'PAYE & Tax Calculation',
                    'Pension & NHF Deductions',
                    'Payslip Generation',
                    // Tax
                    'VAT Management (Auto 7.5%)',
                    'WHT Tracking',
                    'Filing Reminders & Calendar',
                    // Reports (Advanced)
                    'Advanced Reports & Analytics',
                    'Financial Report Exports (PDF & Excel)',
                    'Sales & Inventory Reports',
                    // AI (Full)
                    'Full BallieAI Suite (Invoice by Prompt, Report Interpretation)',
                    'AI Voucher Assistance',
                    // Other
                    'Audit Log & Change History',
                    'API Access',
                    'Smart Notifications',
                    'Mobile App Access',
                    'Priority Support',
                ],
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large businesses needing unlimited power, multi-location, and e-commerce.',
                'monthly_price'   => 1500000,    // ₦15,000
                'quarterly_price' => 3750000,    // ₦37,500 (₦15,000×3 - ₦7,500)
                'biannual_price'  => 7500000,    // ₦75,000 (₦15,000×6 - ₦15,000)
                'yearly_price'    => 15000000,   // ₦150,000 (₦15,000×12 - ₦30,000)
                'max_users' => null,
                'max_customers' => null,
                'has_pos' => true,
                'has_payroll' => true,
                'has_api_access' => true,
                'has_advanced_reports' => true,
                'has_ecommerce' => true,
                'has_audit_log' => true,
                'has_multi_location' => true,
                'has_multi_currency' => true,
                'support_level' => '24/7',
                'is_popular' => false,
                'sort_order' => 3,
                'features' => [
                    'Unlimited users',
                    'Unlimited customers',
                    // Accounting (Full)
                    'Full Accounting Suite with Custom COA',
                    'Vouchers with Approval Workflows & Audit Trail',
                    'Complete Financial Statements',
                    'Bank Reconciliation (Auto-matching & Discrepancy Alerts)',
                    'Multi-Currency with Real-time Exchange Rates',
                    'Full General & Sub-Ledger, Daybook',
                    // Inventory (Full)
                    'Multi-Location Inventory Tracking',
                    'Warehouse & Inter-branch Transfers',
                    'Full Product Management with Variants & Barcode',
                    'Advanced Supplier Management & Performance Tracking',
                    'COGS & Margin Analysis',
                    'Inventory Valuation, Aging & Reorder Reports',
                    // CRM (Enterprise)
                    'Enterprise CRM with Automation',
                    'Sales Pipeline, Lead Scoring & Conversion Analytics',
                    'Full Communication Logging & Smart Reminders',
                    'Customer Import (CSV Bulk)',
                    // POS (Multi-location)
                    'Multi-Location POS',
                    'Advanced Cash Register Management',
                    'Branded Receipts & Digital Delivery',
                    // E-Commerce
                    'Online Storefront (Mobile-Responsive)',
                    'Order Management & Fulfillment',
                    'Online Payments (Paystack, Flutterwave)',
                    'Inventory Auto-Sync with Store',
                    // Payroll (Full)
                    'Full Payroll & HR Management',
                    'PAYE, Pension, NHF, NSITF, ITF',
                    'Payslips & Bulk Payment Upload',
                    'Overtime, Advances & Bonuses',
                    'Employee Self-Service Portal',
                    // Tax (Full)
                    'Full VAT, WHT & CIT Management',
                    'FIRS-Ready Reports & Filing',
                    'Compliance Dashboard & Deadline Alerts',
                    // Reports (Custom)
                    'Custom Reports & Dashboards',
                    'Period Comparison & Trend Analysis',
                    'PDF, Excel & Scheduled Exports',
                    // AI (Enterprise)
                    'Enterprise BallieAI with Predictive Insights',
                    'AI-Powered Invoice, Voucher & Report Interpretation',
                    'Custom AI Workflows & Automation',
                    // Other
                    'Full Audit Log with Filterable Reports',
                    'Admin Management (Roles, Permissions, Teams)',
                    'Multi-Business Support (Isolated Data)',
                    'Advanced API & Integrations',
                    'Enterprise Data Security & Backups',
                    'Smart Notifications & Custom Triggers',
                    'Custom Training & Onboarding',
                    '24/7 Dedicated Support',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
