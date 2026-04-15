<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\LedgerAccount;
use App\Models\Vendor;

class GlobalSearchController extends Controller
{
    /**
     * Search through routes and database records
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');

        if (strlen($query) < 2) {
            return response()->json([
                'routes' => [],
                'records' => [],
            ]);
        }

        $tenantId = tenant('id');
        $results = [
            'routes' => $this->searchRoutes($query),
            'records' => $this->searchRecords($query, $tenantId),
        ];

        return response()->json($results);
    }

    /**
     * Search through tenant routes
     */
    private function searchRoutes($query)
    {
        $searchableRoutes = [
            // Accounting - Invoices
            ['name' => 'tenant.accounting.invoices.index', 'title' => 'Sales Invoices', 'description' => 'View all sales invoices', 'icon' => 'fas fa-file-invoice-dollar', 'category' => 'Accounting', 'keywords' => 'invoice sales bill'],
            ['name' => 'tenant.accounting.invoices.create', 'title' => 'Create Sales Invoice', 'description' => 'Create a new sales invoice', 'icon' => 'fas fa-plus-circle', 'category' => 'Accounting', 'queryParams' => ['type' => 'sv'], 'keywords' => 'new invoice sales'],
            ['name' => 'tenant.accounting.invoices.create', 'title' => 'Create Purchase Invoice', 'description' => 'Create a new purchase invoice', 'icon' => 'fas fa-file-invoice', 'category' => 'Accounting', 'queryParams' => ['type' => 'pur'], 'keywords' => 'purchase buy supplier vendor bill'],

            // Accounting - Quotations
            ['name' => 'tenant.accounting.quotations.index', 'title' => 'Quotations', 'description' => 'View all quotations and estimates', 'icon' => 'fas fa-file-alt', 'category' => 'Accounting', 'keywords' => 'quote estimate proforma'],
            ['name' => 'tenant.accounting.quotations.create', 'title' => 'Create Quotation', 'description' => 'Create a new quotation or estimate', 'icon' => 'fas fa-plus-circle', 'category' => 'Accounting', 'keywords' => 'new quote estimate proforma'],

            // Accounting - Vouchers
            ['name' => 'tenant.accounting.vouchers.index', 'title' => 'Vouchers', 'description' => 'View all journal vouchers', 'icon' => 'fas fa-receipt', 'category' => 'Accounting', 'keywords' => 'journal entry voucher'],
            ['name' => 'tenant.accounting.vouchers.create', 'title' => 'Create Voucher', 'description' => 'Create a new journal voucher', 'icon' => 'fas fa-plus', 'category' => 'Accounting', 'keywords' => 'new voucher journal entry'],
            ['name' => 'tenant.accounting.voucher-types.index', 'title' => 'Voucher Types', 'description' => 'Manage voucher types and numbering', 'icon' => 'fas fa-list-ol', 'category' => 'Accounting', 'keywords' => 'voucher type numbering'],

            // Accounting - Expenses & Payments
            ['name' => 'tenant.accounting.expenses.index', 'title' => 'Expenses', 'description' => 'View and manage expenses', 'icon' => 'fas fa-money-bill-alt', 'category' => 'Accounting', 'keywords' => 'expense cost spending'],
            ['name' => 'tenant.accounting.expenses.create', 'title' => 'Create Expense', 'description' => 'Record a new expense', 'icon' => 'fas fa-plus', 'category' => 'Accounting', 'keywords' => 'new expense cost'],
            ['name' => 'tenant.accounting.payments.index', 'title' => 'Payments', 'description' => 'View all payment records', 'icon' => 'fas fa-money-check', 'category' => 'Accounting', 'keywords' => 'payment receipt money'],
            ['name' => 'tenant.accounting.payments.create', 'title' => 'Record Payment', 'description' => 'Record a new payment', 'icon' => 'fas fa-plus', 'category' => 'Accounting', 'keywords' => 'new payment receive'],
            ['name' => 'tenant.accounting.prepaid-expenses.index', 'title' => 'Prepaid Expenses', 'description' => 'Manage prepaid expenses and amortization', 'icon' => 'fas fa-clock', 'category' => 'Accounting', 'keywords' => 'prepaid amortization deferred'],
            ['name' => 'tenant.accounting.payouts.index', 'title' => 'Payouts', 'description' => 'Manage vendor payouts', 'icon' => 'fas fa-hand-holding-usd', 'category' => 'Accounting', 'keywords' => 'payout disbursement'],

            // Accounting - Ledger & Chart of Accounts
            ['name' => 'tenant.accounting.ledger-accounts.index', 'title' => 'Ledger Accounts', 'description' => 'View chart of accounts and ledgers', 'icon' => 'fas fa-book', 'category' => 'Accounting', 'keywords' => 'ledger chart accounts coa'],
            ['name' => 'tenant.accounting.ledger-accounts.create', 'title' => 'Create Ledger Account', 'description' => 'Add new ledger account', 'icon' => 'fas fa-plus', 'category' => 'Accounting', 'keywords' => 'new account ledger'],
            ['name' => 'tenant.accounting.account-groups.index', 'title' => 'Account Groups', 'description' => 'Manage account groups hierarchy', 'icon' => 'fas fa-layer-group', 'category' => 'Accounting', 'keywords' => 'group hierarchy account'],

            // Accounting - Financial Reports
            ['name' => 'tenant.accounting.trial-balance', 'title' => 'Trial Balance', 'description' => 'View trial balance report', 'icon' => 'fas fa-balance-scale', 'category' => 'Reports', 'keywords' => 'trial balance debit credit'],
            ['name' => 'tenant.accounting.balance-sheet', 'title' => 'Balance Sheet', 'description' => 'View balance sheet statement', 'icon' => 'fas fa-chart-bar', 'category' => 'Reports', 'keywords' => 'balance sheet assets liabilities equity'],
            ['name' => 'tenant.accounting.profit-loss', 'title' => 'Profit & Loss', 'description' => 'View income statement / P&L', 'icon' => 'fas fa-chart-line', 'category' => 'Reports', 'keywords' => 'profit loss income statement pnl'],
            ['name' => 'tenant.accounting.cash-flow', 'title' => 'Cash Flow Statement', 'description' => 'Cash flows from operating, investing, and financing', 'icon' => 'fas fa-money-bill-wave', 'category' => 'Reports', 'keywords' => 'cash flow statement'],

            // CRM - Customers
            ['name' => 'tenant.crm.customers.index', 'title' => 'Customers', 'description' => 'View all customers', 'icon' => 'fas fa-users', 'category' => 'CRM', 'keywords' => 'customer client buyer'],
            ['name' => 'tenant.crm.customers.create', 'title' => 'Add Customer', 'description' => 'Create a new customer', 'icon' => 'fas fa-user-plus', 'category' => 'CRM', 'keywords' => 'new customer client'],
            ['name' => 'tenant.crm.customers.statements', 'title' => 'Customer Statements', 'description' => 'View customer account statements', 'icon' => 'fas fa-file-alt', 'category' => 'CRM', 'keywords' => 'statement balance outstanding'],

            // CRM - Vendors
            ['name' => 'tenant.crm.vendors.index', 'title' => 'Vendors', 'description' => 'View all vendors and suppliers', 'icon' => 'fas fa-truck', 'category' => 'CRM', 'keywords' => 'vendor supplier'],
            ['name' => 'tenant.crm.vendors.create', 'title' => 'Add Vendor', 'description' => 'Create a new vendor', 'icon' => 'fas fa-plus', 'category' => 'CRM', 'keywords' => 'new vendor supplier'],

            // CRM - Activities & Reports
            ['name' => 'tenant.crm.activities.index', 'title' => 'CRM Activities', 'description' => 'View customer and vendor activities', 'icon' => 'fas fa-tasks', 'category' => 'CRM', 'keywords' => 'activity follow up task'],
            ['name' => 'tenant.crm.activities.create', 'title' => 'Create Activity', 'description' => 'Schedule a new CRM activity', 'icon' => 'fas fa-plus', 'category' => 'CRM', 'keywords' => 'new activity task follow up'],
            ['name' => 'tenant.crm.reports', 'title' => 'CRM Reports', 'description' => 'Customer and vendor analytics', 'icon' => 'fas fa-chart-pie', 'category' => 'CRM', 'keywords' => 'crm report analytics'],
            ['name' => 'tenant.crm.payment-reminders', 'title' => 'Payment Reminders', 'description' => 'Send payment reminders to customers', 'icon' => 'fas fa-bell', 'category' => 'CRM', 'keywords' => 'reminder overdue payment'],
            ['name' => 'tenant.crm.record-payment', 'title' => 'Record Customer Payment', 'description' => 'Record payment from customer', 'icon' => 'fas fa-money-check', 'category' => 'CRM', 'keywords' => 'receive payment customer'],

            // Inventory
            ['name' => 'tenant.inventory.products.index', 'title' => 'Products', 'description' => 'View all products and items', 'icon' => 'fas fa-boxes', 'category' => 'Inventory', 'keywords' => 'product item goods'],
            ['name' => 'tenant.inventory.products.create', 'title' => 'Add Product', 'description' => 'Create a new product', 'icon' => 'fas fa-box', 'category' => 'Inventory', 'keywords' => 'new product item'],
            ['name' => 'tenant.inventory.categories.index', 'title' => 'Product Categories', 'description' => 'Manage product categories', 'icon' => 'fas fa-tags', 'category' => 'Inventory', 'keywords' => 'category group classification'],
            ['name' => 'tenant.inventory.categories.create', 'title' => 'Add Category', 'description' => 'Create a new product category', 'icon' => 'fas fa-plus', 'category' => 'Inventory', 'keywords' => 'new category'],
            ['name' => 'tenant.inventory.stock-journal.index', 'title' => 'Stock Journal', 'description' => 'Manage stock journal entries and adjustments', 'icon' => 'fas fa-exchange-alt', 'category' => 'Inventory', 'keywords' => 'stock journal adjustment transfer'],
            ['name' => 'tenant.inventory.stock-journal.create', 'title' => 'Create Stock Journal', 'description' => 'New stock adjustment or transfer', 'icon' => 'fas fa-plus', 'category' => 'Inventory', 'keywords' => 'new stock adjustment'],
            ['name' => 'tenant.inventory.physical-stock.index', 'title' => 'Physical Stock', 'description' => 'Physical stock verification and counting', 'icon' => 'fas fa-clipboard-check', 'category' => 'Inventory', 'keywords' => 'physical stock count verification'],
            ['name' => 'tenant.inventory.physical-stock.create', 'title' => 'Create Stock Count', 'description' => 'Start physical stock count', 'icon' => 'fas fa-plus', 'category' => 'Inventory', 'keywords' => 'new stock count'],
            ['name' => 'tenant.inventory.units.index', 'title' => 'Units', 'description' => 'Manage product units of measure', 'icon' => 'fas fa-ruler', 'category' => 'Inventory', 'keywords' => 'unit measure uom'],
            ['name' => 'tenant.inventory.low-stock', 'title' => 'Low Stock Items', 'description' => 'View items below reorder level', 'icon' => 'fas fa-exclamation-triangle', 'category' => 'Inventory', 'keywords' => 'low stock reorder alert'],
            ['name' => 'tenant.inventory.movements', 'title' => 'Stock Movements', 'description' => 'View all stock movements and transactions', 'icon' => 'fas fa-arrows-alt', 'category' => 'Inventory', 'keywords' => 'movement transaction history'],
            ['name' => 'tenant.inventory.reports', 'title' => 'Inventory Reports', 'description' => 'Inventory analytics and reports', 'icon' => 'fas fa-chart-bar', 'category' => 'Inventory', 'keywords' => 'inventory report analytics'],

            // Procurement
            ['name' => 'tenant.procurement.purchase-orders.index', 'title' => 'Purchase Orders', 'description' => 'View all purchase orders', 'icon' => 'fas fa-shopping-basket', 'category' => 'Procurement', 'keywords' => 'purchase order po buy'],
            ['name' => 'tenant.procurement.purchase-orders.create', 'title' => 'Create Purchase Order', 'description' => 'Create a new purchase order', 'icon' => 'fas fa-plus-circle', 'category' => 'Procurement', 'keywords' => 'new purchase order po'],

            // Banking
            ['name' => 'tenant.banking.banks.index', 'title' => 'Bank Accounts', 'description' => 'Manage bank accounts', 'icon' => 'fas fa-university', 'category' => 'Banking', 'keywords' => 'bank account'],
            ['name' => 'tenant.banking.banks.create', 'title' => 'Add Bank Account', 'description' => 'Create a new bank account', 'icon' => 'fas fa-plus', 'category' => 'Banking', 'keywords' => 'new bank'],
            ['name' => 'tenant.banking.reconciliations.index', 'title' => 'Bank Reconciliation', 'description' => 'Reconcile bank statements', 'icon' => 'fas fa-balance-scale', 'category' => 'Banking', 'keywords' => 'reconciliation match statement'],
            ['name' => 'tenant.banking.reconciliations.create', 'title' => 'New Reconciliation', 'description' => 'Create bank reconciliation', 'icon' => 'fas fa-check-double', 'category' => 'Banking', 'keywords' => 'new reconciliation'],

            // POS
            ['name' => 'tenant.pos.index', 'title' => 'Point of Sale', 'description' => 'Open POS terminal', 'icon' => 'fas fa-cash-register', 'category' => 'POS', 'keywords' => 'pos cashier register sell'],
            ['name' => 'tenant.pos.transactions', 'title' => 'POS Transactions', 'description' => 'View POS transaction history', 'icon' => 'fas fa-shopping-cart', 'category' => 'POS', 'keywords' => 'pos sales history transaction'],
            ['name' => 'tenant.pos.reports', 'title' => 'POS Reports', 'description' => 'POS sales and performance reports', 'icon' => 'fas fa-chart-bar', 'category' => 'POS', 'keywords' => 'pos report daily sales'],

            // Projects
            ['name' => 'tenant.projects.index', 'title' => 'Projects', 'description' => 'View and manage all projects', 'icon' => 'fas fa-project-diagram', 'category' => 'Projects', 'keywords' => 'project task milestone'],
            ['name' => 'tenant.projects.create', 'title' => 'Create Project', 'description' => 'Start a new project', 'icon' => 'fas fa-plus-circle', 'category' => 'Projects', 'keywords' => 'new project'],
            ['name' => 'tenant.projects.reports', 'title' => 'Project Reports', 'description' => 'Project analytics and profitability', 'icon' => 'fas fa-chart-line', 'category' => 'Projects', 'keywords' => 'project report profitability'],

            // E-Commerce
            ['name' => 'tenant.ecommerce.orders.index', 'title' => 'E-Commerce Orders', 'description' => 'View online store orders', 'icon' => 'fas fa-shopping-bag', 'category' => 'E-Commerce', 'keywords' => 'ecommerce order online shop'],
            ['name' => 'tenant.ecommerce.coupons.index', 'title' => 'Coupons', 'description' => 'Manage discount coupons', 'icon' => 'fas fa-ticket-alt', 'category' => 'E-Commerce', 'keywords' => 'coupon discount promo'],
            ['name' => 'tenant.ecommerce.coupons.create', 'title' => 'Create Coupon', 'description' => 'Create a new discount coupon', 'icon' => 'fas fa-plus', 'category' => 'E-Commerce', 'keywords' => 'new coupon discount'],
            ['name' => 'tenant.ecommerce.shipping-methods.index', 'title' => 'Shipping Methods', 'description' => 'Manage shipping and delivery methods', 'icon' => 'fas fa-shipping-fast', 'category' => 'E-Commerce', 'keywords' => 'shipping delivery method'],
            ['name' => 'tenant.ecommerce.payouts.index', 'title' => 'E-Commerce Payouts', 'description' => 'Manage e-commerce payouts', 'icon' => 'fas fa-hand-holding-usd', 'category' => 'E-Commerce', 'keywords' => 'payout commission'],
            ['name' => 'tenant.ecommerce.settings.index', 'title' => 'E-Commerce Settings', 'description' => 'Configure online store settings', 'icon' => 'fas fa-cog', 'category' => 'E-Commerce', 'keywords' => 'ecommerce store settings'],

            // Settings
            ['name' => 'tenant.settings.index', 'title' => 'Settings', 'description' => 'Application settings overview', 'icon' => 'fas fa-cog', 'category' => 'Settings', 'keywords' => 'settings configuration'],
            ['name' => 'tenant.settings.general', 'title' => 'General Settings', 'description' => 'Configure general settings', 'icon' => 'fas fa-cog', 'category' => 'Settings', 'keywords' => 'general settings preference'],
            ['name' => 'tenant.settings.company', 'title' => 'Company Settings', 'description' => 'Update company information and logo', 'icon' => 'fas fa-building', 'category' => 'Settings', 'keywords' => 'company profile logo'],
            ['name' => 'tenant.settings.financial', 'title' => 'Financial Settings', 'description' => 'Configure financial year and defaults', 'icon' => 'fas fa-coins', 'category' => 'Settings', 'keywords' => 'financial year currency defaults'],
            ['name' => 'tenant.settings.tax', 'title' => 'Tax Settings', 'description' => 'Configure tax rates and VAT', 'icon' => 'fas fa-percentage', 'category' => 'Settings', 'keywords' => 'tax vat rate'],
            ['name' => 'tenant.settings.email', 'title' => 'Email Settings', 'description' => 'Configure email and SMTP settings', 'icon' => 'fas fa-envelope', 'category' => 'Settings', 'keywords' => 'email smtp mail'],
            ['name' => 'tenant.settings.notifications', 'title' => 'Notification Settings', 'description' => 'Configure notifications and alerts', 'icon' => 'fas fa-bell', 'category' => 'Settings', 'keywords' => 'notification alert'],
            ['name' => 'tenant.settings.integrations', 'title' => 'Integrations', 'description' => 'Manage third-party integrations', 'icon' => 'fas fa-plug', 'category' => 'Settings', 'keywords' => 'integration api connect'],
            ['name' => 'tenant.settings.backup', 'title' => 'Backup & Restore', 'description' => 'Manage data backups', 'icon' => 'fas fa-database', 'category' => 'Settings', 'keywords' => 'backup restore data'],
            ['name' => 'tenant.settings.cash-registers.index', 'title' => 'Cash Registers', 'description' => 'Manage POS cash registers', 'icon' => 'fas fa-cash-register', 'category' => 'Settings', 'keywords' => 'cash register pos terminal'],

            // Reports
            ['name' => 'tenant.reports.index', 'title' => 'Reports Dashboard', 'description' => 'View all available reports', 'icon' => 'fas fa-chart-bar', 'category' => 'Reports', 'keywords' => 'report dashboard analytics'],
            ['name' => 'tenant.reports.sales-by-period', 'title' => 'Sales By Period', 'description' => 'Sales analysis by date period', 'icon' => 'fas fa-chart-line', 'category' => 'Reports', 'keywords' => 'sales period monthly weekly'],
            ['name' => 'tenant.reports.purchases-by-period', 'title' => 'Purchases By Period', 'description' => 'Purchase analysis by date period', 'icon' => 'fas fa-shopping-cart', 'category' => 'Reports', 'keywords' => 'purchase period monthly'],
            ['name' => 'tenant.reports.sales-summary', 'title' => 'Sales Summary', 'description' => 'Summary of all sales', 'icon' => 'fas fa-chart-pie', 'category' => 'Reports', 'keywords' => 'sales summary total'],
            ['name' => 'tenant.reports.purchase-summary', 'title' => 'Purchase Summary', 'description' => 'Summary of all purchases', 'icon' => 'fas fa-chart-pie', 'category' => 'Reports', 'keywords' => 'purchase summary total'],
            ['name' => 'tenant.reports.customer-sales', 'title' => 'Customer Sales', 'description' => 'Sales report by customer', 'icon' => 'fas fa-users', 'category' => 'Reports', 'keywords' => 'customer sales report'],
            ['name' => 'tenant.reports.vendor-purchases', 'title' => 'Vendor Purchases', 'description' => 'Purchase report by vendor', 'icon' => 'fas fa-truck', 'category' => 'Reports', 'keywords' => 'vendor purchase report'],
            ['name' => 'tenant.reports.product-sales', 'title' => 'Product Sales', 'description' => 'Sales report by product', 'icon' => 'fas fa-box', 'category' => 'Reports', 'keywords' => 'product sales report'],
            ['name' => 'tenant.reports.product-purchases', 'title' => 'Product Purchases', 'description' => 'Purchase report by product', 'icon' => 'fas fa-box', 'category' => 'Reports', 'keywords' => 'product purchase report'],
            ['name' => 'tenant.reports.stock-summary', 'title' => 'Stock Summary', 'description' => 'Inventory stock summary report', 'icon' => 'fas fa-boxes', 'category' => 'Reports', 'keywords' => 'stock inventory summary'],
            ['name' => 'tenant.reports.stock-movement', 'title' => 'Stock Movement', 'description' => 'Stock movement history report', 'icon' => 'fas fa-arrows-alt', 'category' => 'Reports', 'keywords' => 'stock movement history'],
            ['name' => 'tenant.reports.stock-valuation', 'title' => 'Stock Valuation', 'description' => 'Inventory valuation report', 'icon' => 'fas fa-dollar-sign', 'category' => 'Reports', 'keywords' => 'stock valuation worth'],
            ['name' => 'tenant.reports.inventory-valuation', 'title' => 'Inventory Valuation', 'description' => 'Detailed inventory valuation', 'icon' => 'fas fa-calculator', 'category' => 'Reports', 'keywords' => 'inventory valuation cost'],
            ['name' => 'tenant.reports.low-stock', 'title' => 'Low Stock Report', 'description' => 'Items below reorder level', 'icon' => 'fas fa-exclamation-triangle', 'category' => 'Reports', 'keywords' => 'low stock reorder alert'],
            ['name' => 'tenant.reports.bin-card', 'title' => 'Bin Card', 'description' => 'Product bin card / stock card', 'icon' => 'fas fa-id-card', 'category' => 'Reports', 'keywords' => 'bin card stock card'],
            ['name' => 'tenant.reports.customer-analysis', 'title' => 'Customer Analysis', 'description' => 'Customer analytics and insights', 'icon' => 'fas fa-user-chart', 'category' => 'Reports', 'keywords' => 'customer analysis analytics'],
            ['name' => 'tenant.reports.product-performance', 'title' => 'Product Performance', 'description' => 'Product performance analytics', 'icon' => 'fas fa-chart-line', 'category' => 'Reports', 'keywords' => 'product performance best selling'],
            ['name' => 'tenant.reports.trial-balance', 'title' => 'Trial Balance Report', 'description' => 'View trial balance', 'icon' => 'fas fa-balance-scale', 'category' => 'Reports', 'keywords' => 'trial balance'],
            ['name' => 'tenant.reports.profit-loss', 'title' => 'Profit & Loss Report', 'description' => 'Profit and loss statement', 'icon' => 'fas fa-chart-pie', 'category' => 'Reports', 'keywords' => 'profit loss income'],
            ['name' => 'tenant.reports.balance-sheet', 'title' => 'Balance Sheet Report', 'description' => 'View balance sheet', 'icon' => 'fas fa-file-invoice-dollar', 'category' => 'Reports', 'keywords' => 'balance sheet assets liabilities'],
            ['name' => 'tenant.reports.cash-flow', 'title' => 'Cash Flow Report', 'description' => 'Cash flow statement', 'icon' => 'fas fa-money-bill-wave', 'category' => 'Reports', 'keywords' => 'cash flow'],
            ['name' => 'tenant.reports.financial', 'title' => 'Financial Reports', 'description' => 'All financial reports', 'icon' => 'fas fa-chart-bar', 'category' => 'Reports', 'keywords' => 'financial report'],
            ['name' => 'tenant.reports.sales', 'title' => 'Sales Reports', 'description' => 'All sales reports', 'icon' => 'fas fa-chart-line', 'category' => 'Reports', 'keywords' => 'sales report'],
            ['name' => 'tenant.reports.inventory', 'title' => 'Inventory Reports', 'description' => 'All inventory reports', 'icon' => 'fas fa-boxes', 'category' => 'Reports', 'keywords' => 'inventory report stock'],

            // Admin
            ['name' => 'tenant.admin.index', 'title' => 'Admin Panel', 'description' => 'Administration overview', 'icon' => 'fas fa-shield-alt', 'category' => 'Admin', 'keywords' => 'admin panel'],
            ['name' => 'tenant.admin.users.index', 'title' => 'User Management', 'description' => 'Manage users and access', 'icon' => 'fas fa-users-cog', 'category' => 'Admin', 'keywords' => 'user manage staff'],
            ['name' => 'tenant.admin.users.create', 'title' => 'Add User', 'description' => 'Create a new user account', 'icon' => 'fas fa-user-plus', 'category' => 'Admin', 'keywords' => 'new user add staff'],
            ['name' => 'tenant.admin.roles.index', 'title' => 'Roles & Permissions', 'description' => 'Manage roles and permissions', 'icon' => 'fas fa-user-shield', 'category' => 'Admin', 'keywords' => 'role permission access control'],
            ['name' => 'tenant.admin.roles.create', 'title' => 'Create Role', 'description' => 'Create a new user role', 'icon' => 'fas fa-plus', 'category' => 'Admin', 'keywords' => 'new role'],
            ['name' => 'tenant.admin.permissions.index', 'title' => 'Permissions', 'description' => 'View all system permissions', 'icon' => 'fas fa-key', 'category' => 'Admin', 'keywords' => 'permission access right'],
            ['name' => 'tenant.admin.teams.index', 'title' => 'Teams', 'description' => 'Manage teams and groups', 'icon' => 'fas fa-users', 'category' => 'Admin', 'keywords' => 'team group'],
            ['name' => 'tenant.admin.activity.index', 'title' => 'Activity Log', 'description' => 'View system activity and audit log', 'icon' => 'fas fa-history', 'category' => 'Admin', 'keywords' => 'activity log audit trail history'],
            ['name' => 'tenant.admin.security.index', 'title' => 'Security', 'description' => 'Security settings and logs', 'icon' => 'fas fa-lock', 'category' => 'Admin', 'keywords' => 'security login session'],
            ['name' => 'tenant.admin.reports.index', 'title' => 'Admin Reports', 'description' => 'System usage and analytics', 'icon' => 'fas fa-chart-area', 'category' => 'Admin', 'keywords' => 'admin report usage analytics'],
            ['name' => 'tenant.admin.system.health', 'title' => 'System Health', 'description' => 'Check system health and status', 'icon' => 'fas fa-heartbeat', 'category' => 'Admin', 'keywords' => 'health status system check'],

            // Payroll
            ['name' => 'tenant.payroll.index', 'title' => 'Payroll Dashboard', 'description' => 'Payroll overview and statistics', 'icon' => 'fas fa-money-check-alt', 'category' => 'Payroll', 'keywords' => 'payroll dashboard'],
            ['name' => 'tenant.payroll.employees.index', 'title' => 'Payroll Employees', 'description' => 'Manage payroll employees', 'icon' => 'fas fa-users', 'category' => 'Payroll', 'keywords' => 'employee staff worker'],
            ['name' => 'tenant.payroll.employees.create', 'title' => 'Add Employee', 'description' => 'Add new payroll employee', 'icon' => 'fas fa-user-plus', 'category' => 'Payroll', 'keywords' => 'new employee hire'],
            ['name' => 'tenant.payroll.processing.index', 'title' => 'Payroll Processing', 'description' => 'Process and manage payroll periods', 'icon' => 'fas fa-calculator', 'category' => 'Payroll', 'keywords' => 'process run payroll'],
            ['name' => 'tenant.payroll.processing.create', 'title' => 'Create Payroll Period', 'description' => 'Create new payroll period', 'icon' => 'fas fa-plus-circle', 'category' => 'Payroll', 'keywords' => 'new payroll period'],
            ['name' => 'tenant.payroll.departments.index', 'title' => 'Departments', 'description' => 'Manage company departments', 'icon' => 'fas fa-building', 'category' => 'Payroll', 'keywords' => 'department division'],
            ['name' => 'tenant.payroll.departments.create', 'title' => 'Create Department', 'description' => 'Add a new department', 'icon' => 'fas fa-plus', 'category' => 'Payroll', 'keywords' => 'new department'],
            ['name' => 'tenant.payroll.positions.index', 'title' => 'Positions', 'description' => 'Manage job positions and designations', 'icon' => 'fas fa-briefcase', 'category' => 'Payroll', 'keywords' => 'position designation job title'],
            ['name' => 'tenant.payroll.components.index', 'title' => 'Salary Components', 'description' => 'Manage salary components and allowances', 'icon' => 'fas fa-list-ul', 'category' => 'Payroll', 'keywords' => 'salary component allowance deduction'],
            ['name' => 'tenant.payroll.attendance.index', 'title' => 'Attendance Management', 'description' => 'Track employee attendance', 'icon' => 'fas fa-calendar-check', 'category' => 'Payroll', 'keywords' => 'attendance clock in out'],
            ['name' => 'tenant.payroll.leaves.index', 'title' => 'Leave Management', 'description' => 'Manage employee leave requests', 'icon' => 'fas fa-calendar-minus', 'category' => 'Payroll', 'keywords' => 'leave vacation absence annual sick'],
            ['name' => 'tenant.payroll.leaves.create', 'title' => 'Request Leave', 'description' => 'Submit a new leave request', 'icon' => 'fas fa-plus', 'category' => 'Payroll', 'keywords' => 'new leave request'],
            ['name' => 'tenant.payroll.overtime.index', 'title' => 'Overtime Management', 'description' => 'Manage employee overtime', 'icon' => 'fas fa-clock', 'category' => 'Payroll', 'keywords' => 'overtime extra hours'],
            ['name' => 'tenant.payroll.overtime.create', 'title' => 'Record Overtime', 'description' => 'Record employee overtime hours', 'icon' => 'fas fa-plus', 'category' => 'Payroll', 'keywords' => 'new overtime record'],
            ['name' => 'tenant.payroll.shifts.index', 'title' => 'Shift Management', 'description' => 'Manage work shifts and schedules', 'icon' => 'fas fa-business-time', 'category' => 'Payroll', 'keywords' => 'shift schedule roster'],
            ['name' => 'tenant.payroll.shifts.create', 'title' => 'Create Shift', 'description' => 'Create a new work shift', 'icon' => 'fas fa-plus', 'category' => 'Payroll', 'keywords' => 'new shift'],
            ['name' => 'tenant.payroll.loans.index', 'title' => 'Employee Loans', 'description' => 'Manage employee loans and advances', 'icon' => 'fas fa-hand-holding-usd', 'category' => 'Payroll', 'keywords' => 'loan advance borrow'],
            ['name' => 'tenant.payroll.loans.create', 'title' => 'Create Loan', 'description' => 'Create a new employee loan', 'icon' => 'fas fa-plus', 'category' => 'Payroll', 'keywords' => 'new loan'],
            ['name' => 'tenant.payroll.salary-advance.create', 'title' => 'Salary Advance', 'description' => 'Create salary advance voucher', 'icon' => 'fas fa-money-bill-wave', 'category' => 'Payroll', 'keywords' => 'salary advance'],
            ['name' => 'tenant.payroll.pfas.index', 'title' => 'PFAs', 'description' => 'Manage Pension Fund Administrators', 'icon' => 'fas fa-piggy-bank', 'category' => 'Payroll', 'keywords' => 'pfa pension fund administrator'],
            ['name' => 'tenant.payroll.settings', 'title' => 'Payroll Settings', 'description' => 'Configure payroll settings', 'icon' => 'fas fa-cog', 'category' => 'Payroll', 'keywords' => 'payroll settings configuration'],
            ['name' => 'tenant.payroll.reports.summary', 'title' => 'Payroll Summary', 'description' => 'Payroll summary report', 'icon' => 'fas fa-chart-bar', 'category' => 'Payroll', 'keywords' => 'payroll summary report'],
            ['name' => 'tenant.payroll.reports.tax-summary', 'title' => 'Tax Summary', 'description' => 'Employee tax summary report', 'icon' => 'fas fa-file-invoice-dollar', 'category' => 'Payroll', 'keywords' => 'tax paye summary'],
            ['name' => 'tenant.payroll.reports.employee-summary', 'title' => 'Employee Summary', 'description' => 'Employee payroll statistics', 'icon' => 'fas fa-user-chart', 'category' => 'Payroll', 'keywords' => 'employee summary report'],
            ['name' => 'tenant.payroll.reports.bank-schedule', 'title' => 'Bank Payment Schedule', 'description' => 'Bank payment schedule for payroll', 'icon' => 'fas fa-university', 'category' => 'Payroll', 'keywords' => 'bank payment schedule'],
            ['name' => 'tenant.payroll.reports.detailed', 'title' => 'Detailed Payroll Report', 'description' => 'Detailed payroll report with breakdown', 'icon' => 'fas fa-file-alt', 'category' => 'Payroll', 'keywords' => 'detailed payroll breakdown'],
            ['name' => 'tenant.payroll.announcements.index', 'title' => 'Employee Announcements', 'description' => 'Send announcements to employees', 'icon' => 'fas fa-bullhorn', 'category' => 'Payroll', 'keywords' => 'announcement notify broadcast'],
            ['name' => 'tenant.payroll.announcements.create', 'title' => 'Send Announcement', 'description' => 'Create and send announcement', 'icon' => 'fas fa-paper-plane', 'category' => 'Payroll', 'keywords' => 'new announcement send'],

            // Statutory & Tax
            ['name' => 'tenant.statutory.index', 'title' => 'Statutory Compliance', 'description' => 'Tax and statutory compliance dashboard', 'icon' => 'fas fa-gavel', 'category' => 'Statutory', 'keywords' => 'statutory compliance tax regulation'],
            ['name' => 'tenant.statutory.vat.dashboard', 'title' => 'VAT Dashboard', 'description' => 'VAT overview and filing status', 'icon' => 'fas fa-percentage', 'category' => 'Statutory', 'keywords' => 'vat tax value added'],
            ['name' => 'tenant.statutory.vat.report', 'title' => 'VAT Report', 'description' => 'Generate VAT report for filing', 'icon' => 'fas fa-file-alt', 'category' => 'Statutory', 'keywords' => 'vat report filing return'],
            ['name' => 'tenant.statutory.paye.report', 'title' => 'PAYE Report', 'description' => 'Pay As You Earn tax report', 'icon' => 'fas fa-file-invoice-dollar', 'category' => 'Statutory', 'keywords' => 'paye tax income employee'],
            ['name' => 'tenant.statutory.pension.report', 'title' => 'Pension Report', 'description' => 'Employee pension contribution report', 'icon' => 'fas fa-piggy-bank', 'category' => 'Statutory', 'keywords' => 'pension contribution retirement'],
            ['name' => 'tenant.statutory.nsitf.report', 'title' => 'NSITF Report', 'description' => 'NSITF compliance report', 'icon' => 'fas fa-shield-alt', 'category' => 'Statutory', 'keywords' => 'nsitf social insurance'],
            ['name' => 'tenant.statutory.filings.index', 'title' => 'Tax Filings', 'description' => 'View and manage tax filing history', 'icon' => 'fas fa-folder-open', 'category' => 'Statutory', 'keywords' => 'filing return submission'],
            ['name' => 'tenant.statutory.settings', 'title' => 'Statutory Settings', 'description' => 'Configure statutory and tax settings', 'icon' => 'fas fa-cog', 'category' => 'Statutory', 'keywords' => 'statutory tax settings'],

            // Audit
            ['name' => 'tenant.audit.index', 'title' => 'Audit Trail', 'description' => 'View all system changes and audit log', 'icon' => 'fas fa-search', 'category' => 'Admin', 'keywords' => 'audit trail log change history who modified'],

            // Support
            ['name' => 'tenant.support.index', 'title' => 'Support Center', 'description' => 'Get help and submit support tickets', 'icon' => 'fas fa-life-ring', 'category' => 'Support', 'keywords' => 'support help ticket'],
            ['name' => 'tenant.support.create', 'title' => 'Create Ticket', 'description' => 'Submit a new support ticket', 'icon' => 'fas fa-plus', 'category' => 'Support', 'keywords' => 'new ticket support issue'],
            ['name' => 'tenant.support.knowledge-base', 'title' => 'Knowledge Base', 'description' => 'Browse help articles and guides', 'icon' => 'fas fa-book-open', 'category' => 'Support', 'keywords' => 'knowledge base help article guide faq'],

            // Subscription
            ['name' => 'tenant.subscription.index', 'title' => 'Subscription', 'description' => 'Manage your subscription plan', 'icon' => 'fas fa-crown', 'category' => 'Settings', 'keywords' => 'subscription plan billing payment'],
            ['name' => 'tenant.subscription.plans', 'title' => 'Available Plans', 'description' => 'View and compare subscription plans', 'icon' => 'fas fa-th-list', 'category' => 'Settings', 'keywords' => 'plan pricing upgrade'],
            ['name' => 'tenant.subscription.invoices', 'title' => 'Billing History', 'description' => 'View subscription invoices and payments', 'icon' => 'fas fa-file-invoice-dollar', 'category' => 'Settings', 'keywords' => 'billing invoice payment history'],

            // Profile & Notifications
            ['name' => 'tenant.profile.index', 'title' => 'My Profile', 'description' => 'View and edit your profile', 'icon' => 'fas fa-user-circle', 'category' => 'Settings', 'keywords' => 'profile account my settings'],
            ['name' => 'tenant.notifications.index', 'title' => 'Notifications', 'description' => 'View all notifications', 'icon' => 'fas fa-bell', 'category' => 'Settings', 'keywords' => 'notification alert message'],

            // Dashboard
            ['name' => 'tenant.dashboard', 'title' => 'Dashboard', 'description' => 'Main dashboard overview', 'icon' => 'fas fa-tachometer-alt', 'category' => 'Dashboard', 'keywords' => 'dashboard home overview summary'],
            ['name' => 'tenant.help', 'title' => 'Help', 'description' => 'Getting started and help center', 'icon' => 'fas fa-question-circle', 'category' => 'Support', 'keywords' => 'help getting started tutorial'],
        ];

        $matched = collect($searchableRoutes)->filter(function ($route) use ($query) {
            $searchString = strtolower($query);
            $keywords = strtolower($route['keywords'] ?? '');
            return str_contains(strtolower($route['title']), $searchString) ||
                   str_contains(strtolower($route['description']), $searchString) ||
                   str_contains(strtolower($route['category']), $searchString) ||
                   str_contains($keywords, $searchString);
        })->take(10)->map(function ($route) {
            if (Route::has($route['name'])) {
                $url = route($route['name'], ['tenant' => tenant()->slug]);
                if (!empty($route['queryParams'])) {
                    $url .= '?' . http_build_query($route['queryParams']);
                }
                $route['url'] = $url;
                unset($route['queryParams'], $route['keywords']);
                return $route;
            }
            return null;
        })->filter()->values();

        return $matched;
    }

    /**
     * Search through database records
     */
    private function searchRecords($query, $tenantId)
    {
        $results = [];
        $searchString = strtolower($query);

        // Search Customers
        $customers = Customer::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'type' => 'customer',
                    'title' => $customer->getFullNameAttribute(),
                    'description' => $customer->email,
                    'url' => route('tenant.crm.customers.show', ['tenant' => tenant()->slug, 'customer' => $customer->id]),
                    'icon' => 'fas fa-user',
                    'category' => 'Customer',
                ];
            });

        // Search Products
        $products = Product::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'product',
                    'title' => $product->name,
                    'description' => "SKU: {$product->sku}",
                    'url' => route('tenant.inventory.products.show', ['tenant' => tenant()->slug, 'product' => $product->id]),
                    'icon' => 'fas fa-box',
                    'category' => 'Product',
                ];
            });

        // Search Vouchers
        $vouchers = Voucher::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('voucher_number', 'like', "%{$query}%")
                  ->orWhere('narration', 'like', "%{$query}%");
            })
            ->with('voucherType')
            ->limit(5)
            ->get()
            ->map(function ($voucher) {
                return [
                    'type' => 'voucher',
                    'title' => $voucher->voucher_number,
                    'description' => ($voucher->voucherType->name ?? 'Voucher') . ' - ' . $voucher->narration,
                    'url' => route('tenant.accounting.vouchers.show', ['tenant' => tenant()->slug, 'voucher' => $voucher->id]),
                    'icon' => 'fas fa-receipt',
                    'category' => 'Voucher',
                ];
            });

        // Search Ledger Accounts
        $ledgerAccounts = LedgerAccount::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($account) {
                return [
                    'type' => 'ledger_account',
                    'title' => $account->name,
                    'description' => "Code: {$account->code}",
                    'url' => route('tenant.accounting.ledger-accounts.show', ['tenant' => tenant()->slug, 'ledgerAccount' => $account->id]),
                    'icon' => 'fas fa-book',
                    'category' => 'Ledger Account',
                ];
            });

        // Search Employees
        $employees = \App\Models\Employee::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('employee_number', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($employee) {
                return [
                    'type' => 'employee',
                    'title' => $employee->first_name . ' ' . $employee->last_name,
                    'description' => "Employee #: {$employee->employee_number}",
                    'url' => route('tenant.payroll.employees.show', ['tenant' => tenant()->slug, 'employee' => $employee->id]),
                    'icon' => 'fas fa-user-tie',
                    'category' => 'Employee',
                ];
            });

        // Search Payroll Periods
        $payrollPeriods = \App\Models\PayrollPeriod::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('status', 'like', "%{$query}%");
            })
            ->limit(3)
            ->get()
            ->map(function ($period) {
                $statusColors = [
                    'draft' => 'gray',
                    'processing' => 'yellow',
                    'approved' => 'green',
                    'paid' => 'blue',
                ];
                $statusColor = $statusColors[$period->status] ?? 'gray';

                return [
                    'type' => 'payroll_period',
                    'title' => $period->name,
                    'description' => ucfirst($period->status) . " - " . $period->start_date->format('M d, Y') . " to " . $period->end_date->format('M d, Y'),
                    'url' => route('tenant.payroll.processing.show', ['tenant' => tenant()->slug, 'period' => $period->id]),
                    'icon' => 'fas fa-calendar-alt',
                    'category' => 'Payroll Period',
                ];
            });

        // Search Vendors
        $vendors = Vendor::where('tenant_id', $tenantId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($vendor) {
                $name = trim(($vendor->first_name ?? '') . ' ' . ($vendor->last_name ?? ''));
                if (empty($name) && $vendor->company_name) {
                    $name = $vendor->company_name;
                }
                return [
                    'type' => 'vendor',
                    'title' => $name ?: 'Vendor',
                    'description' => $vendor->email ?? $vendor->company_name ?? '',
                    'url' => route('tenant.crm.vendors.show', ['tenant' => tenant()->slug, 'vendor' => $vendor->id]),
                    'icon' => 'fas fa-truck',
                    'category' => 'Vendor',
                ];
            });

        // Merge all results
        $results = $customers
            ->concat($vendors)
            ->concat($products)
            ->concat($vouchers)
            ->concat($ledgerAccounts)
            ->concat($employees)
            ->concat($payrollPeriods)
            ->take(12);

        return $results;
    }

    /**
     * Get quick actions based on search context
     */
    public function quickActions(Request $request)
    {
        $query = strtolower($request->input('query', ''));
        $actions = [];

        if (str_contains($query, 'sales') || str_contains($query, 'invoice')) {
            $actions[] = [
                'title' => 'Create Sales Invoice',
                'url' => route('tenant.accounting.invoices.create', ['tenant' => tenant()->slug]) . '?type=sv',
                'icon' => 'fas fa-plus-circle',
                'color' => 'blue',
            ];
        }

        if (str_contains($query, 'purchase') || str_contains($query, 'buy') || str_contains($query, 'supplier')) {
            $actions[] = [
                'title' => 'Create Purchase Invoice',
                'url' => route('tenant.accounting.invoices.create', ['tenant' => tenant()->slug]) . '?type=pur',
                'icon' => 'fas fa-file-invoice',
                'color' => 'orange',
            ];
        }

        if (str_contains($query, 'quote') || str_contains($query, 'quotation') || str_contains($query, 'estimate') || str_contains($query, 'proforma')) {
            $actions[] = [
                'title' => 'Create Quotation',
                'url' => route('tenant.accounting.quotations.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-file-alt',
                'color' => 'purple',
            ];
        }

        if (str_contains($query, 'customer') || str_contains($query, 'client')) {
            $actions[] = [
                'title' => 'Add New Customer',
                'url' => route('tenant.crm.customers.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-user-plus',
                'color' => 'green',
            ];
        }

        if (str_contains($query, 'vendor') || str_contains($query, 'supplier')) {
            $actions[] = [
                'title' => 'Add New Vendor',
                'url' => route('tenant.crm.vendors.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-truck',
                'color' => 'green',
            ];
        }

        if (str_contains($query, 'product') || str_contains($query, 'item')) {
            $actions[] = [
                'title' => 'Add New Product',
                'url' => route('tenant.inventory.products.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-box',
                'color' => 'purple',
            ];
        }

        if (str_contains($query, 'voucher') || str_contains($query, 'journal')) {
            $actions[] = [
                'title' => 'Create Voucher',
                'url' => route('tenant.accounting.vouchers.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-receipt',
                'color' => 'orange',
            ];
        }

        if (str_contains($query, 'expense')) {
            $actions[] = [
                'title' => 'Record Expense',
                'url' => route('tenant.accounting.expenses.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-money-bill-alt',
                'color' => 'orange',
            ];
        }

        if (str_contains($query, 'purchase order') || str_contains($query, 'po')) {
            $actions[] = [
                'title' => 'Create Purchase Order',
                'url' => route('tenant.procurement.purchase-orders.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-shopping-basket',
                'color' => 'blue',
            ];
        }

        if (str_contains($query, 'project')) {
            $actions[] = [
                'title' => 'Create Project',
                'url' => route('tenant.projects.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-project-diagram',
                'color' => 'purple',
            ];
        }

        if (str_contains($query, 'payroll') || str_contains($query, 'salary') || str_contains($query, 'employee')) {
            $actions[] = [
                'title' => 'Process Payroll',
                'url' => route('tenant.payroll.processing.index', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-calculator',
                'color' => 'blue',
            ];
        }

        if (str_contains($query, 'attendance') || str_contains($query, 'clock')) {
            $actions[] = [
                'title' => 'Mark Attendance',
                'url' => route('tenant.payroll.attendance.index', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-calendar-check',
                'color' => 'green',
            ];
        }

        if (str_contains($query, 'leave') || str_contains($query, 'vacation') || str_contains($query, 'absence')) {
            $actions[] = [
                'title' => 'Request Leave',
                'url' => route('tenant.payroll.leaves.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-calendar-minus',
                'color' => 'green',
            ];
        }

        if (str_contains($query, 'overtime')) {
            $actions[] = [
                'title' => 'Record Overtime',
                'url' => route('tenant.payroll.overtime.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-clock',
                'color' => 'blue',
            ];
        }

        if (str_contains($query, 'loan') || str_contains($query, 'advance')) {
            $actions[] = [
                'title' => 'Employee Loans',
                'url' => route('tenant.payroll.loans.index', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-hand-holding-usd',
                'color' => 'purple',
            ];
        }

        if (str_contains($query, 'department')) {
            $actions[] = [
                'title' => 'Manage Departments',
                'url' => route('tenant.payroll.departments.index', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-building',
                'color' => 'blue',
            ];
        }

        if (str_contains($query, 'announcement') || str_contains($query, 'notify') || str_contains($query, 'communication')) {
            $actions[] = [
                'title' => 'Send Announcement',
                'url' => route('tenant.payroll.announcements.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-bullhorn',
                'color' => 'orange',
            ];
        }

        if (str_contains($query, 'support') || str_contains($query, 'ticket') || str_contains($query, 'help')) {
            $actions[] = [
                'title' => 'Create Support Ticket',
                'url' => route('tenant.support.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-life-ring',
                'color' => 'blue',
            ];
        }

        // Limit to 4 quick actions max
        return response()->json(array_slice($actions, 0, 4));
    }
}
