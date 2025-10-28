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
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            // Only include tenant routes with names
            return $route->getName() &&
                   str_starts_with($route->getName(), 'tenant.') &&
                   in_array('GET', $route->methods());
        });

        $searchableRoutes = [
            // Accounting
            ['name' => 'tenant.accounting.invoices.index', 'title' => 'Sales Invoices', 'description' => 'View all sales invoices', 'icon' => 'fas fa-file-invoice-dollar', 'category' => 'Accounting'],
            ['name' => 'tenant.accounting.invoices.create', 'title' => 'Create Sales Invoice', 'description' => 'Create a new sales invoice', 'icon' => 'fas fa-plus-circle', 'category' => 'Accounting'],
            ['name' => 'tenant.accounting.vouchers.index', 'title' => 'Vouchers', 'description' => 'View all vouchers', 'icon' => 'fas fa-receipt', 'category' => 'Accounting'],
            ['name' => 'tenant.accounting.vouchers.create', 'title' => 'Create Voucher', 'description' => 'Create a new voucher', 'icon' => 'fas fa-plus', 'category' => 'Accounting'],
            ['name' => 'tenant.accounting.ledger-accounts.index', 'title' => 'Ledger Accounts', 'description' => 'View chart of accounts', 'icon' => 'fas fa-book', 'category' => 'Accounting'],
            ['name' => 'tenant.accounting.ledger-accounts.create', 'title' => 'Create Ledger Account', 'description' => 'Add new ledger account', 'icon' => 'fas fa-plus', 'category' => 'Accounting'],
            ['name' => 'tenant.accounting.account-groups.index', 'title' => 'Account Groups', 'description' => 'Manage account groups', 'icon' => 'fas fa-layer-group', 'category' => 'Accounting'],
            ['name' => 'tenant.accounting.trial-balance', 'title' => 'Trial Balance', 'description' => 'View trial balance report', 'icon' => 'fas fa-balance-scale', 'category' => 'Reports'],
            ['name' => 'tenant.accounting.balance-sheet', 'title' => 'Balance Sheet', 'description' => 'View balance sheet', 'icon' => 'fas fa-chart-bar', 'category' => 'Reports'],
            ['name' => 'tenant.accounting.profit-loss', 'title' => 'Profit & Loss', 'description' => 'View P&L statement', 'icon' => 'fas fa-chart-line', 'category' => 'Reports'],

            // CRM
            ['name' => 'tenant.crm.customers.index', 'title' => 'Customers', 'description' => 'View all customers', 'icon' => 'fas fa-users', 'category' => 'CRM'],
            ['name' => 'tenant.crm.customers.create', 'title' => 'Add Customer', 'description' => 'Create a new customer', 'icon' => 'fas fa-user-plus', 'category' => 'CRM'],
            ['name' => 'tenant.crm.customers.statements', 'title' => 'Customer Statements', 'description' => 'View customer account statements', 'icon' => 'fas fa-file-alt', 'category' => 'CRM'],
            ['name' => 'tenant.crm.vendors.index', 'title' => 'Vendors', 'description' => 'View all vendors', 'icon' => 'fas fa-truck', 'category' => 'CRM'],
            ['name' => 'tenant.crm.vendors.create', 'title' => 'Add Vendor', 'description' => 'Create a new vendor', 'icon' => 'fas fa-plus', 'category' => 'CRM'],

            // Inventory
            ['name' => 'tenant.inventory.products.index', 'title' => 'Products', 'description' => 'View all products', 'icon' => 'fas fa-boxes', 'category' => 'Inventory'],
            ['name' => 'tenant.inventory.products.create', 'title' => 'Add Product', 'description' => 'Create a new product', 'icon' => 'fas fa-box', 'category' => 'Inventory'],
            ['name' => 'tenant.inventory.categories.index', 'title' => 'Product Categories', 'description' => 'Manage product categories', 'icon' => 'fas fa-tags', 'category' => 'Inventory'],
            ['name' => 'tenant.inventory.stock-journal.index', 'title' => 'Stock Journal', 'description' => 'Manage stock journal entries', 'icon' => 'fas fa-exchange-alt', 'category' => 'Inventory'],
            ['name' => 'tenant.inventory.physical-stock.index', 'title' => 'Physical Stock', 'description' => 'Physical stock verification', 'icon' => 'fas fa-clipboard-check', 'category' => 'Inventory'],
            ['name' => 'tenant.inventory.units.index', 'title' => 'Units', 'description' => 'Manage product units', 'icon' => 'fas fa-ruler', 'category' => 'Inventory'],

            // Banking
            ['name' => 'tenant.banking.banks.index', 'title' => 'Bank Accounts', 'description' => 'Manage bank accounts', 'icon' => 'fas fa-university', 'category' => 'Banking'],
            ['name' => 'tenant.banking.banks.create', 'title' => 'Add Bank Account', 'description' => 'Create a new bank account', 'icon' => 'fas fa-plus', 'category' => 'Banking'],
            ['name' => 'tenant.banking.reconciliations.index', 'title' => 'Bank Reconciliation', 'description' => 'Reconcile bank statements', 'icon' => 'fas fa-balance-scale', 'category' => 'Banking'],
            ['name' => 'tenant.banking.reconciliations.create', 'title' => 'New Reconciliation', 'description' => 'Create bank reconciliation', 'icon' => 'fas fa-check-double', 'category' => 'Banking'],

            // POS
            ['name' => 'tenant.pos.index', 'title' => 'Point of Sale', 'description' => 'Open POS terminal', 'icon' => 'fas fa-cash-register', 'category' => 'POS'],
            ['name' => 'tenant.pos.sales.index', 'title' => 'POS Sales', 'description' => 'View POS sales history', 'icon' => 'fas fa-shopping-cart', 'category' => 'POS'],

            // Settings
            ['name' => 'tenant.settings.general', 'title' => 'General Settings', 'description' => 'Configure general settings', 'icon' => 'fas fa-cog', 'category' => 'Settings'],
            ['name' => 'tenant.settings.profile', 'title' => 'Company Profile', 'description' => 'Update company information', 'icon' => 'fas fa-building', 'category' => 'Settings'],
            ['name' => 'tenant.settings.users', 'title' => 'User Management', 'description' => 'Manage users and permissions', 'icon' => 'fas fa-users-cog', 'category' => 'Settings'],

            // Reports
            ['name' => 'tenant.reports.index', 'title' => 'Reports', 'description' => 'View all reports', 'icon' => 'fas fa-chart-bar', 'category' => 'Reports'],
            ['name' => 'tenant.reports.sales-by-period', 'title' => 'Sales By Period', 'description' => 'Sales analysis by period', 'icon' => 'fas fa-chart-line', 'category' => 'Reports'],
            ['name' => 'tenant.reports.purchases-by-period', 'title' => 'Purchases By Period', 'description' => 'Purchase analysis by period', 'icon' => 'fas fa-shopping-cart', 'category' => 'Reports'],
            ['name' => 'tenant.reports.stock-summary', 'title' => 'Stock Summary', 'description' => 'Inventory stock summary', 'icon' => 'fas fa-boxes', 'category' => 'Reports'],
            ['name' => 'tenant.reports.trial-balance', 'title' => 'Trial Balance', 'description' => 'View trial balance', 'icon' => 'fas fa-balance-scale', 'category' => 'Reports'],
            ['name' => 'tenant.reports.profit-loss', 'title' => 'Profit & Loss', 'description' => 'Profit and loss statement', 'icon' => 'fas fa-chart-pie', 'category' => 'Reports'],
            ['name' => 'tenant.reports.balance-sheet', 'title' => 'Balance Sheet', 'description' => 'View balance sheet', 'icon' => 'fas fa-file-invoice-dollar', 'category' => 'Reports'],

            // Admin
            ['name' => 'tenant.admin.users.index', 'title' => 'User Management', 'description' => 'Manage users and permissions', 'icon' => 'fas fa-users-cog', 'category' => 'Admin'],
            ['name' => 'tenant.admin.roles.index', 'title' => 'Roles & Permissions', 'description' => 'Manage roles and permissions', 'icon' => 'fas fa-user-shield', 'category' => 'Admin'],

            // Dashboard
            ['name' => 'tenant.dashboard', 'title' => 'Dashboard', 'description' => 'Main dashboard overview', 'icon' => 'fas fa-tachometer-alt', 'category' => 'Dashboard'],
        ];

        $matched = collect($searchableRoutes)->filter(function ($route) use ($query) {
            $searchString = strtolower($query);
            return str_contains(strtolower($route['title']), $searchString) ||
                   str_contains(strtolower($route['description']), $searchString) ||
                   str_contains(strtolower($route['category']), $searchString);
        })->take(8)->map(function ($route) {
            // Check if route exists
            if (Route::has($route['name'])) {
                $route['url'] = route($route['name'], ['tenant' => tenant()->slug]);
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

        // Merge all results
        $results = $customers
            ->concat($products)
            ->concat($vouchers)
            ->concat($ledgerAccounts)
            ->take(10);

        return $results;
    }

    /**
     * Get quick actions based on search context
     */
    public function quickActions(Request $request)
    {
        $query = strtolower($request->input('query', ''));
        $actions = [];

        // Context-aware quick actions
        if (str_contains($query, 'invoice') || str_contains($query, 'sales')) {
            $actions[] = [
                'title' => 'Create Sales Invoice',
                'url' => route('tenant.accounting.invoices.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-plus-circle',
                'color' => 'blue',
            ];
        }

        if (str_contains($query, 'customer')) {
            $actions[] = [
                'title' => 'Add New Customer',
                'url' => route('tenant.crm.customers.create', ['tenant' => tenant()->slug]),
                'icon' => 'fas fa-user-plus',
                'color' => 'green',
            ];
        }

        if (str_contains($query, 'product')) {
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

        return response()->json($actions);
    }
}
