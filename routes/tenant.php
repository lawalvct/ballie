<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\Accounting\VoucherController;
use App\Http\Controllers\Tenant\Accounting\VoucherTypeController;
use App\Http\Controllers\Tenant\Accounting\AccountGroupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\OnboardingController;
use App\Http\Controllers\Tenant\TourController;
use App\Http\Controllers\Tenant\Inventory\ProductController;
use App\Http\Controllers\Tenant\Crm\CustomerController;
use App\Http\Controllers\Tenant\Accounting\InvoiceController;
use App\Http\Controllers\Tenant\HelpController;
use App\Http\Controllers\Tenant\SupportController;
use App\Http\Controllers\Tenant\CommunityController;
use App\Http\Controllers\Tenant\Accounting\AccountingController;
use App\Http\Controllers\Tenant\Inventory\InventoryController;
use App\Http\Controllers\Tenant\Crm\CrmController;
use App\Http\Controllers\Tenant\Pos\PosController;
use App\Http\Controllers\Tenant\Payroll\PayrollController;
use App\Http\Controllers\Tenant\PositionController;
use App\Http\Controllers\Tenant\Reports\ReportsController;
use App\Http\Controllers\Tenant\Documents\DocumentsController;
use App\Http\Controllers\Tenant\Activity\ActivityController;
use App\Http\Controllers\Tenant\Inventory\ProductCategoryController;
use App\Http\Controllers\Tenant\Settings\SettingsController;
use App\Http\Controllers\Tenant\Admin\AdminController;
use App\Http\Controllers\Tenant\Crm\VendorController;
use App\Http\Controllers\Tenant\Inventory\UnitController;
use App\Http\Controllers\Tenant\Inventory\StockJournalController;
use App\Http\Controllers\Tenant\Inventory\PhysicalStockController;
use App\Models\Tenant;
use App\Models\Tenant\Role;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Team;
use App\Models\SubscriptionPayment;
use App\Http\Controllers\Tenant\Accounting\LedgerAccountController;
use App\Http\Controllers\Auth\SocialAuthController;

// Additional organized controller imports
// use App\Http\Controllers\Tenant\Inventory\StockAdjustmentController;
use App\Http\Controllers\Tenant\Crm\LeadController;
use App\Http\Controllers\Tenant\Crm\OpportunityController;
use App\Http\Controllers\Tenant\Accounting\ExpenseController;
use App\Http\Controllers\Tenant\Accounting\PaymentController;
use App\Http\Controllers\Tenant\Accounting\ChartOfAccountsController;
use App\Http\Controllers\Tenant\Api\SearchController;
use App\Http\Controllers\Tenant\Api\NotificationController;
use App\Http\Controllers\Tenant\Api\UploadController;
use App\Http\Controllers\Tenant\Api\ExportController;
use App\Http\Controllers\Tenant\Api\GlobalSearchController;
use App\Http\Controllers\Tenant\Reports\SalesReportsController;
use App\Http\Controllers\Tenant\Reports\PurchaseReportsController;
use App\Http\Controllers\Tenant\SubscriptionController;
use App\Http\Controllers\Tenant\Audit\AuditController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the RouteServiceProvider and are
| prefixed with the tenant slug from the main web.php routes.
|
*/

// Route model binding for tenant
Route::bind('tenant', function ($value) {
    return Tenant::where('slug', $value)->firstOrFail();
});

// Route model binding for plan
Route::bind('plan', function ($value) {
    return \App\Models\Plan::findOrFail($value);
});

// Route model binding for subscription payment
Route::bind('payment', function ($value) {
    return SubscriptionPayment::findOrFail($value);
});

// Route model bindings for admin management
Route::bind('role', function ($value) {
    return \App\Models\Tenant\Role::where('tenant_id', tenant('id'))->findOrFail($value);
});

Route::bind('permission', function ($value) {
    return \App\Models\Tenant\Permission::findOrFail($value);
});

Route::bind('team', function ($value) {
    return \App\Models\Tenant\Team::where('tenant_id', tenant('id'))->findOrFail($value);
});

// Public routes (accessible without authentication)
Route::prefix('public')->name('tenant.public.')->group(function () {
    // Public invoice PDF download (accessible from email links)
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
});

// Guest routes (login, register, etc.)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('tenant.register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('tenant.password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('tenant.password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('tenant.password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('tenant.password.update');

    // Social Authentication Routes
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('tenant.auth.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('tenant.auth.callback');
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('tenant.auth.google');
    Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('tenant.auth.facebook');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

    // Onboarding routes
    Route::prefix('onboarding')->name('tenant.onboarding.')->group(function () {
        Route::get('/', [OnboardingController::class, 'index'])->name('index');
        Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
        Route::get('/{step}', [OnboardingController::class, 'showStep'])->name('step');
        Route::post('/{step}', [OnboardingController::class, 'saveStep'])->name('save-step');
        Route::get('/show-step', [OnboardingController::class, 'showStep'])->name('show-step');

        // Utility routes for debugging/reseeding
        Route::post('/reseed-ledgers', [OnboardingController::class, 'reseedLedgerAccounts'])->name('reseed-ledgers');
        Route::get('/check-status', [OnboardingController::class, 'checkOnboardingStatus'])->name('check-status');
    });

    // Routes that require completed onboarding and active subscription
    // Note: email.verified middleware removed - users can access dashboard without verifying email
    // They will receive notifications to verify within 7 days
    Route::middleware(['onboarding.completed', 'subscription.check'])->group(function () {
        // Global Search API
        Route::prefix('api')->name('tenant.api.')->group(function () {
            Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global-search');
            Route::get('/quick-actions', [GlobalSearchController::class, 'quickActions'])->name('quick-actions');
            Route::get('/customers/search', [InvoiceController::class, 'searchCustomers'])->name('customers.search');
            Route::get('/vendors/search', [VendorController::class, 'search'])->name('vendors.search');
            Route::get('/products/search', [InvoiceController::class, 'searchProducts'])->name('products.search');
            Route::get('/ledger-accounts/search', [InvoiceController::class, 'searchLedgerAccounts'])->name('ledger-accounts.search');
        });

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

        // Audit Trail Routes
        Route::prefix('audit')->name('tenant.audit.')->group(function () {
            Route::get('/', [AuditController::class, 'index'])->name('index');
            Route::get('/{model}/{id}', [AuditController::class, 'show'])->name('show');
            Route::get('/export', [AuditController::class, 'export'])->name('export');
        });

        // User Profile Routes
        Route::prefix('profile')->name('tenant.profile.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\ProfileController::class, 'index'])->name('index');
            Route::put('/update', [\App\Http\Controllers\Tenant\ProfileController::class, 'update'])->name('update');
            Route::put('/password', [\App\Http\Controllers\Tenant\ProfileController::class, 'updatePassword'])->name('password.update');
            Route::delete('/avatar', [\App\Http\Controllers\Tenant\ProfileController::class, 'removeAvatar'])->name('avatar.remove');
        });

        // Tour Routes - Guide for new users
        Route::prefix('tour')->name('tenant.tour.')->group(function () {
            Route::get('/start', [TourController::class, 'start'])->name('start');
            Route::get('/dashboard', [TourController::class, 'dashboard'])->name('dashboard');
            Route::get('/customers', [TourController::class, 'customers'])->name('customers');
            Route::get('/products', [TourController::class, 'products'])->name('products');
            Route::get('/sales', [TourController::class, 'sales'])->name('sales');
            Route::get('/inventory', [TourController::class, 'inventory'])->name('inventory');
            Route::get('/accounting', [TourController::class, 'accounting'])->name('accounting');
            Route::get('/reports', [TourController::class, 'reports'])->name('reports');
            Route::get('/settings', [TourController::class, 'settings'])->name('settings');
            Route::post('/complete', [TourController::class, 'complete'])->name('complete');
            Route::post('/skip', [TourController::class, 'skip'])->name('skip');
        });

        // Accounting Module
        Route::prefix('accounting')->name('tenant.accounting.')->group(function () {
            Route::get('/', [AccountingController::class, 'index'])->name('index');

            // Chart Data API
            Route::get('/chart-data', [AccountingController::class, 'getChartDataApi'])->name('chart-data');

            // Invoices (moved from root level)
         // Sales Invoices
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('index');
    Route::get('/create', [InvoiceController::class, 'create'])->name('create');
    Route::post('/', [InvoiceController::class, 'store'])->name('store');
    Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
    Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
    Route::put('/{invoice}', [InvoiceController::class, 'update'])->name('update');
    Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    Route::post('/{invoice}/post', [InvoiceController::class, 'post'])->name('post');
    Route::post('/{invoice}/unpost', [InvoiceController::class, 'unpost'])->name('unpost');
    Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
    Route::get('/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('pdf');
    Route::post('/{invoice}/email', [InvoiceController::class, 'email'])->name('email');
    Route::post('/{invoice}/record-payment', [InvoiceController::class, 'recordPayment'])->name('record-payment');
});

            // Invoice API routes
            Route::get('/api/customers/search', [InvoiceController::class, 'searchCustomers'])->name('api.customers.search');

            // Quotations (Proforma Invoices)
            Route::prefix('quotations')->name('quotations.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'store'])->name('store');
                Route::get('/{quotation}', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'show'])->name('show');
                Route::get('/{quotation}/edit', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'edit'])->name('edit');
                Route::put('/{quotation}', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'update'])->name('update');
                Route::delete('/{quotation}', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'destroy'])->name('destroy');

                // Quotation Actions
                Route::post('/{quotation}/convert', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'convertToInvoice'])->name('convert');
                Route::post('/{quotation}/send', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'markAsSent'])->name('send');
                Route::post('/{quotation}/accept', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'markAsAccepted'])->name('accept');
                Route::post('/{quotation}/reject', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'markAsRejected'])->name('reject');
                Route::post('/{quotation}/duplicate', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'duplicate'])->name('duplicate');
                Route::get('/{quotation}/print', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'print'])->name('print');
                Route::get('/{quotation}/pdf', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'pdf'])->name('pdf');
                Route::post('/{quotation}/email', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'email'])->name('email');

                // AJAX Search Endpoints
                Route::get('/search/customers', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'searchCustomers'])->name('search.customers');
                Route::get('/search/products', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'searchProducts'])->name('search.products');
            });

            // Short alias routes for quotations (supports /quotes/*)
            Route::prefix('quotes')->name('quotes.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Accounting\QuotationController::class, 'create'])->name('create');
            });

            // Account Groups
           Route::prefix('account-groups')->name('account-groups.')->group(function () {
    Route::get('/', [AccountGroupController::class, 'index'])->name('index');
    Route::get('/create', [AccountGroupController::class, 'create'])->name('create');
    Route::post('/', [AccountGroupController::class, 'store'])->name('store');
    Route::get('/{account_group}', [AccountGroupController::class, 'show'])->name('show');
    Route::get('/{account_group}/edit', [AccountGroupController::class, 'edit'])->name('edit');
    Route::put('/{account_group}', [AccountGroupController::class, 'update'])->name('update');
    Route::delete('/{account_group}', [AccountGroupController::class, 'destroy'])->name('destroy');

    // Account Group specific actions
    Route::post('/{account_group}/toggle', [AccountGroupController::class, 'toggle'])->name('toggle');
    Route::post('/bulk-action', [AccountGroupController::class, 'bulkAction'])->name('bulk-action');

    // Export/Import routes
    Route::get('/export/template', [AccountGroupController::class, 'downloadTemplate'])->name('export.template');
    Route::post('/import', [AccountGroupController::class, 'import'])->name('import');
    Route::get('/export/all', [AccountGroupController::class, 'export'])->name('export');

    // Hierarchy management
    Route::post('/{account_group}/move', [AccountGroupController::class, 'move'])->name('move');
    Route::get('/hierarchy', [AccountGroupController::class, 'hierarchy'])->name('hierarchy');
    Route::post('/reorder', [AccountGroupController::class, 'reorder'])->name('reorder');

    //Route [tenant.accounting.account-groups.bulk] not defined.
    Route::post('/bulk', [AccountGroupController::class, 'bulk'])->name('bulk');
});

            // Voucher Types
            Route::prefix('voucher-types')->name('voucher-types.')->group(function () {
                Route::get('/', [VoucherTypeController::class, 'index'])->name('index');
                Route::get('/create', [VoucherTypeController::class, 'create'])->name('create');
                Route::post('/', [VoucherTypeController::class, 'store'])->name('store');
                Route::get('/{voucherType}', [VoucherTypeController::class, 'show'])->name('show');
                Route::get('/{voucherType}/edit', [VoucherTypeController::class, 'edit'])->name('edit');
                Route::put('/{voucherType}', [VoucherTypeController::class, 'update'])->name('update');
                Route::delete('/{voucherType}', [VoucherTypeController::class, 'destroy'])->name('destroy');
                Route::post('/{voucherType}/reset-numbering', [VoucherTypeController::class, 'resetNumbering'])->name('reset-numbering');

                Route::post('/bulk-action', [VoucherTypeController::class, 'bulkAction'])->name('bulk-action');
                Route::post('/toggle/{voucherType}', [VoucherTypeController::class, 'toggle'])->name('toggle');

            });

          // Vouchers
Route::prefix('vouchers')->name('vouchers.')->group(function () {
    Route::get('/', [VoucherController::class, 'index'])->name('index');
    Route::get('/create', [VoucherController::class, 'create'])->name('create');
    Route::get('/create/{type}', [VoucherController::class, 'create'])->name('create.type');

    // Bulk payment upload (MUST be before parameterized routes)
    Route::get('/bulk-payment-template', [VoucherController::class, 'downloadBulkPaymentTemplate'])->name('bulk-payment-template');
    Route::post('/upload-bulk-payments', [VoucherController::class, 'uploadBulkPayments'])->name('upload-bulk-payments');

    // Bulk actions
    Route::post('/bulk/post', [VoucherController::class, 'bulkPost'])->name('bulk.post');
    Route::delete('/bulk/delete', [VoucherController::class, 'bulkDelete'])->name('bulk.delete');
    Route::get('/export', [VoucherController::class, 'export'])->name('export');
    Route::post('/bulk-action', [VoucherController::class, 'bulkAction'])->name('bulk.action');

    // Standard CRUD routes
    Route::post('/', [VoucherController::class, 'store'])->name('store');
    Route::get('/{voucher}', [VoucherController::class, 'show'])->name('show');
    Route::get('/{voucher}/edit', [VoucherController::class, 'edit'])->name('edit');
    Route::put('/{voucher}', [VoucherController::class, 'update'])->name('update');
    Route::delete('/{voucher}', [VoucherController::class, 'destroy'])->name('destroy');

    // Voucher actions
    Route::post('/{voucher}/post', [VoucherController::class, 'post'])->name('post');
    Route::post('/{voucher}/unpost', [VoucherController::class, 'unpost'])->name('unpost');
    Route::get('/{voucher}/duplicate', [VoucherController::class, 'duplicate'])->name('duplicate');
    Route::get('/{voucher}/pdf', [VoucherController::class, 'pdf'])->name('pdf');
    Route::get('/{voucher}/print', [VoucherController::class, 'print'])->name('print');
});
// Ledger Accounts
Route::prefix('ledger-accounts')->name('ledger-accounts.')->group(function () {
     Route::get('/', [LedgerAccountController::class, 'index'])->name('index');
    Route::get('/create', [LedgerAccountController::class, 'create'])->name('create');
       Route::get('/template', [LedgerAccountController::class, 'downloadTemplate'])->name('template');

    // Search API - MUST be before parameterized routes
    Route::get('/search', [LedgerAccountController::class, 'search'])->name('search');
    Route::get('/download-pdf', [LedgerAccountController::class, 'downloadPdf'])->name('download-pdf');

    // Opening balance reclassification
    Route::post('/reclassify-opening-balance', [LedgerAccountController::class, 'reclassifyOpeningBalance'])->name('reclassify-opening-balance');

    Route::post('/', [LedgerAccountController::class, 'store'])->name('store');
    Route::get('/{ledgerAccount}', [LedgerAccountController::class, 'show'])->name('show');
    Route::get('/{ledgerAccount}/edit', [LedgerAccountController::class, 'edit'])->name('edit');
    Route::put('/{ledgerAccount}', [LedgerAccountController::class, 'update'])->name('update');
    Route::delete('/{ledgerAccount}', [LedgerAccountController::class, 'destroy'])->name('destroy');

    // Export/Import routes
    Route::get('/export/template', [LedgerAccountController::class, 'downloadTemplate'])->name('export.template');
    Route::get('/export/account-groups', [LedgerAccountController::class, 'downloadAccountGroupsReference'])->name('export.account-groups');
    Route::post('/import', [LedgerAccountController::class, 'import'])->name('import');
    Route::get('/export/all', [LedgerAccountController::class, 'export'])->name('export');

    // Individual account actions
    Route::get('/{ledgerAccount}/statement', [LedgerAccountController::class, 'statement'])->name('statement');
    Route::get('/{ledgerAccount}/export-ledger', [LedgerAccountController::class, 'exportLedger'])->name('export-ledger');
    Route::get('/{ledgerAccount}/print-ledger', [LedgerAccountController::class, 'printLedger'])->name('print-ledger');
    Route::get('/{ledgerAccount}/balance', [LedgerAccountController::class, 'getBalance'])->name('balance');

    // Bulk actions
    Route::post('/bulk-delete', [LedgerAccountController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/bulk-activate', [LedgerAccountController::class, 'bulkActivate'])->name('bulk-activate');
    Route::post('/bulk-deactivate', [LedgerAccountController::class, 'bulkDeactivate'])->name('bulk-deactivate');

    Route::patch('/{ledgerAccount}/toggle-status', [LedgerAccountController::class, 'toggleStatus'])->name('toggle-status');


});




            // Expenses (add if not exists)
            Route::prefix('expenses')->name('expenses.')->group(function () {
                Route::get('/', [ExpenseController::class, 'index'])->name('index');
                Route::get('/create', [ExpenseController::class, 'create'])->name('create');
                Route::post('/', [ExpenseController::class, 'store'])->name('store');
                Route::get('/{expense}', [ExpenseController::class, 'show'])->name('show');
                Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('edit');
                Route::put('/{expense}', [ExpenseController::class, 'update'])->name('update');
                Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
            });

            // Payments (add if not exists)
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::get('/', [PaymentController::class, 'index'])->name('index');
                Route::get('/create', [PaymentController::class, 'create'])->name('create');
                Route::post('/', [PaymentController::class, 'store'])->name('store');
                Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
                Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->name('edit');
                Route::put('/{payment}', [PaymentController::class, 'update'])->name('update');
                Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
            });

            // Chart of Accounts (add if not exists)
            Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
                Route::get('/', [ChartOfAccountsController::class, 'index'])->name('index');
                Route::get('/create', [ChartOfAccountsController::class, 'create'])->name('create');
                Route::post('/', [ChartOfAccountsController::class, 'store'])->name('store');
                Route::get('/{account}', [ChartOfAccountsController::class, 'show'])->name('show');
                Route::get('/{account}/edit', [ChartOfAccountsController::class, 'edit'])->name('edit');
                Route::put('/{account}', [ChartOfAccountsController::class, 'update'])->name('update');
                Route::delete('/{account}', [ChartOfAccountsController::class, 'destroy'])->name('destroy');
            });


            // Trial Balance
            Route::get('/trial-balance', [ReportsController::class, 'trialBalance'])->name('trial-balance');

            // Balance Sheet
            Route::get('/balance-sheet', [ReportsController::class, 'balanceSheet'])->name('balance-sheet');
            Route::get('/balance-sheet-pdf', [ReportsController::class, 'balanceSheetPdf'])->name('balance-sheet-pdf');
            Route::get('/balance-sheet-excel', [ReportsController::class, 'balanceSheetExcel'])->name('balance-sheet-excel');
            // Standard Table Balance Sheet
            Route::get('/balance-sheet-table', [ReportsController::class, 'balanceSheetTable'])->name('balance-sheet-table');
            // DR/CR Balance Sheet
            Route::get('/balance-sheet-dr-cr', [ReportsController::class, 'balanceSheetDrCr'])->name('balance-sheet-dr-cr');

            // Profit & Loss
            Route::get('/profit-loss', [ReportsController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/profit-loss-table', [ReportsController::class, 'profitLossTable'])->name('profit-loss-table');
            Route::get('/profit-loss-pdf', [ReportsController::class, 'profitLossPdf'])->name('profit-loss-pdf');
            Route::get('/profit-loss-excel', [ReportsController::class, 'profitLossExcel'])->name('profit-loss-excel');

            // Cash Flow
            Route::get('/cash-flow', [ReportsController::class, 'cashFlow'])->name('cash-flow');
        });

        // Inventory Management
        Route::prefix('inventory')->name('tenant.inventory.')->group(function () {
            // Products
            Route::get('products/{product}/stock-movements', [ProductController::class, 'stockMovements'])->name('products.stock-movements');
            Route::get('products/export/template', [ProductController::class, 'downloadTemplate'])->name('products.export.template');
            Route::get('products/export/categories-reference', [ProductController::class, 'downloadCategoriesReference'])->name('products.export.categories-reference');
            Route::post('products/import', [ProductController::class, 'importProducts'])->name('products.import');
            Route::delete('products/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');
            Route::resource('products', ProductController::class);
            Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
            Route::get('products/export/all', [ProductController::class, 'export'])->name('products.export');
            Route::post('products/bulk', [ProductController::class, 'bulk'])->name('products.bulk');
            Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');

            // Product Categories
            Route::resource('categories', ProductCategoryController::class);
            Route::post('categories/quick-store', [ProductCategoryController::class, 'quickStore'])->name('categories.quick-store');
            Route::patch('categories/{category}/toggle-status', [ProductCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
            Route::get('categories/export/all', [ProductCategoryController::class, 'export'])->name('categories.export');
            Route::get('categories/export/template', [ProductCategoryController::class, 'exportTemplate'])->name('categories.export.template');
            Route::post('categories/import', [ProductCategoryController::class, 'import'])->name('categories.import');
            Route::post('categories/bulk', [ProductCategoryController::class, 'bulk'])->name('categories.bulk');
            Route::post('categories/bulk-action', [ProductCategoryController::class, 'bulkAction'])->name('categories.bulk-action');

            // Units
            Route::resource('units', UnitController::class);
            Route::patch('units/{unit}/toggle-status', [UnitController::class, 'toggleStatus'])->name('units.toggle-status');
            Route::get('units/export/all', [UnitController::class, 'export'])->name('units.export');
            Route::get('units/export/template', [UnitController::class, 'exportTemplate'])->name('units.export.template');
            Route::post('units/import', [UnitController::class, 'import'])->name('units.import');
            Route::post('units/bulk', [UnitController::class, 'bulk'])->name('units.bulk');
            Route::post('units/bulk-action', [UnitController::class, 'bulkAction'])->name('units.bulk-action');

            // Stock Journal Entries
            Route::prefix('stock-journal')->name('stock-journal.')->group(function () {
                Route::get('/', [StockJournalController::class, 'index'])->name('index');
                Route::get('/create', [StockJournalController::class, 'create'])->name('create');
                Route::get('/create/{type}', [StockJournalController::class, 'create'])->name('create.type');
                Route::post('/', [StockJournalController::class, 'store'])->name('store');
                Route::get('/{stockJournal}', [StockJournalController::class, 'show'])->name('show');
                Route::get('/{stockJournal}/edit', [StockJournalController::class, 'edit'])->name('edit');
                Route::put('/{stockJournal}', [StockJournalController::class, 'update'])->name('update');
                Route::delete('/{stockJournal}', [StockJournalController::class, 'destroy'])->name('destroy');

                // Stock Journal Actions
                Route::post('/{stockJournal}/post', [StockJournalController::class, 'post'])->name('post');
                Route::post('/{stockJournal}/cancel', [StockJournalController::class, 'cancel'])->name('cancel');
                Route::get('/{stockJournal}/duplicate', [StockJournalController::class, 'duplicate'])->name('duplicate');
                Route::get('/{stockJournal}/print', [StockJournalController::class, 'print'])->name('print');

                // Bulk actions
                Route::post('/bulk-post', [StockJournalController::class, 'bulkPost'])->name('bulk-post');
                Route::post('/bulk-cancel', [StockJournalController::class, 'bulkCancel'])->name('bulk-cancel');
                Route::delete('/bulk-delete', [StockJournalController::class, 'bulkDelete'])->name('bulk-delete');

                // Export/Import
                Route::get('/export/all', [StockJournalController::class, 'export'])->name('export');
                Route::get('/export/template', [StockJournalController::class, 'exportTemplate'])->name('export.template');
                Route::post('/import', [StockJournalController::class, 'import'])->name('import');

                // AJAX routes for dynamic features
                Route::get('/ajax/product-stock/{product}', [StockJournalController::class, 'getProductStock'])->name('ajax.product-stock');
                Route::post('/ajax/calculate-stock', [StockJournalController::class, 'calculateStock'])->name('ajax.calculate-stock');
            });

            // Physical Stock Vouchers
            Route::prefix('physical-stock')->name('physical-stock.')->group(function () {
                Route::get('/', [PhysicalStockController::class, 'index'])->name('index');
                Route::get('/create', [PhysicalStockController::class, 'create'])->name('create');
                Route::post('/', [PhysicalStockController::class, 'store'])->name('store');
                Route::get('/{voucher}', [PhysicalStockController::class, 'show'])->name('show');
                Route::get('/{voucher}/edit', [PhysicalStockController::class, 'edit'])->name('edit');
                Route::put('/{voucher}', [PhysicalStockController::class, 'update'])->name('update');
                Route::delete('/{voucher}', [PhysicalStockController::class, 'destroy'])->name('destroy');

                // Voucher Actions
                Route::post('/{voucher}/submit', [PhysicalStockController::class, 'submit'])->name('submit');
                Route::post('/{voucher}/approve', [PhysicalStockController::class, 'approve'])->name('approve');
                Route::post('/{voucher}/cancel', [PhysicalStockController::class, 'cancel'])->name('cancel');

                // AJAX routes
                Route::get('/ajax/product-stock', [PhysicalStockController::class, 'getProductStock'])->name('product-stock');
                Route::get('/ajax/products-search', [PhysicalStockController::class, 'getProductsWithStock'])->name('products-search');
            });

            // Main Inventory
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('reports', [InventoryController::class, 'reports'])->name('reports');
            Route::get('movements', [InventoryController::class, 'movements'])->name('movements');
            Route::post('adjust', [InventoryController::class, 'adjust'])->name('adjust');
            Route::get('low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');
        });

        // E-commerce Store Management
        Route::prefix('ecommerce')->name('tenant.ecommerce.')->group(function () {
            // Store Settings
            Route::get('/settings', [\App\Http\Controllers\Tenant\Ecommerce\EcommerceSettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [\App\Http\Controllers\Tenant\Ecommerce\EcommerceSettingsController::class, 'update'])->name('settings.update');
            Route::get('/settings/generate-qr', [\App\Http\Controllers\Tenant\Ecommerce\EcommerceSettingsController::class, 'generateQrCode'])->name('settings.generate-qr');

            // Order Management
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Ecommerce\OrderManagementController::class, 'index'])->name('index');
                Route::get('/{order}', [\App\Http\Controllers\Tenant\Ecommerce\OrderManagementController::class, 'show'])->name('show');
                Route::put('/{order}/status', [\App\Http\Controllers\Tenant\Ecommerce\OrderManagementController::class, 'updateStatus'])->name('update-status');
                Route::put('/{order}/payment-status', [\App\Http\Controllers\Tenant\Ecommerce\OrderManagementController::class, 'updatePaymentStatus'])->name('update-payment');
                Route::post('/{order}/create-invoice', [\App\Http\Controllers\Tenant\Ecommerce\OrderManagementController::class, 'createInvoice'])->name('create-invoice');
            });

            // E-commerce Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/orders', [\App\Http\Controllers\Tenant\Ecommerce\EcommerceReportsController::class, 'orders'])->name('orders');
                Route::get('/revenue', [\App\Http\Controllers\Tenant\Ecommerce\EcommerceReportsController::class, 'revenue'])->name('revenue');
                Route::get('/products', [\App\Http\Controllers\Tenant\Ecommerce\EcommerceReportsController::class, 'products'])->name('products');
                Route::get('/customers', [\App\Http\Controllers\Tenant\Ecommerce\EcommerceReportsController::class, 'customers'])->name('customers');
                Route::get('/abandoned-carts', [\App\Http\Controllers\Tenant\Ecommerce\EcommerceReportsController::class, 'abandonedCarts'])->name('abandoned-carts');
            });

            // Shipping Methods
            Route::resource('shipping-methods', \App\Http\Controllers\Tenant\Ecommerce\ShippingMethodController::class)->except(['show']);
            Route::post('/shipping-methods/{shipping_method}/toggle', [\App\Http\Controllers\Tenant\Ecommerce\ShippingMethodController::class, 'toggle'])->name('shipping-methods.toggle');

            // Coupons
            Route::resource('coupons', \App\Http\Controllers\Tenant\Ecommerce\CouponController::class)->except(['show']);
            Route::post('/coupons/{coupon}/toggle', [\App\Http\Controllers\Tenant\Ecommerce\CouponController::class, 'toggle'])->name('coupons.toggle');
        });

        // CRM - Customer & Vendor Management
        Route::prefix('crm')->name('tenant.crm.')->group(function () {
            // Customer Activities
            Route::get('activities', [\App\Http\Controllers\Tenant\CustomerActivityController::class, 'index'])->name('activities.index');
            Route::get('activities/create', [\App\Http\Controllers\Tenant\CustomerActivityController::class, 'create'])->name('activities.create');
            Route::post('activities', [\App\Http\Controllers\Tenant\CustomerActivityController::class, 'store'])->name('activities.store');
            Route::get('activities/{activity}/edit', [\App\Http\Controllers\Tenant\CustomerActivityController::class, 'edit'])->name('activities.edit');
            Route::put('activities/{activity}', [\App\Http\Controllers\Tenant\CustomerActivityController::class, 'update'])->name('activities.update');
            Route::delete('activities/{activity}', [\App\Http\Controllers\Tenant\CustomerActivityController::class, 'destroy'])->name('activities.destroy');

            // Record Payment
            Route::get('record-payment', [VoucherController::class, 'recordPayment'])->name('record-payment');
            Route::post('record-payment', [VoucherController::class, 'storePayment'])->name('store-payment');

            // Payment Reminders
            Route::get('payment-reminders', [CustomerController::class, 'paymentReminders'])->name('payment-reminders');
            Route::post('payment-reminders/send', [CustomerController::class, 'sendPaymentReminders'])->name('payment-reminders.send');

            // Payment Reports
            Route::get('payment-reports', [CustomerController::class, 'paymentReports'])->name('payment-reports');

            // Customers
            Route::get('customers/statements', [CustomerController::class, 'statements'])->name('customers.statements');
            Route::get('customers/{customer}/statement', [CustomerController::class, 'showStatement'])->name('customers.statement');
            Route::get('customers/export/all', [CustomerController::class, 'export'])->name('customers.export');
            Route::get('customers/export/template', [CustomerController::class, 'exportTemplate'])->name('customers.export.template');
            Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import');
            Route::post('customers/bulk', [CustomerController::class, 'bulk'])->name('customers.bulk');
            Route::post('customers/bulk-action', [CustomerController::class, 'bulkAction'])->name('customers.bulk-action');
            Route::resource('customers', CustomerController::class);

            // Vendors
            Route::resource('vendors', VendorController::class);
            Route::get('vendors/export/all', [VendorController::class, 'export'])->name('vendors.export');
            Route::get('vendors/export/template', [VendorController::class, 'exportTemplate'])->name('vendors.export.template');
            Route::post('vendors/import', [VendorController::class, 'import'])->name('vendors.import');
            Route::post('vendors/bulk', [VendorController::class, 'bulk'])->name('vendors.bulk');
            Route::post('vendors/bulk-action', [VendorController::class, 'bulkAction'])->name('vendors.bulk-action');

            // CRM Dashboard
            Route::get('/', [CrmController::class, 'index'])->name('index');
            Route::get('reports', [CrmController::class, 'reports'])->name('reports');
        });

        // Procurement - Purchase Orders (LPO)
        Route::prefix('procurement')->name('tenant.procurement.')->group(function () {
            Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Procurement\PurchaseOrderController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Procurement\PurchaseOrderController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Procurement\PurchaseOrderController::class, 'store'])->name('store');
                Route::get('/{purchaseOrder}', [\App\Http\Controllers\Tenant\Procurement\PurchaseOrderController::class, 'show'])->name('show');
                Route::get('/{purchaseOrder}/pdf', [\App\Http\Controllers\Tenant\Procurement\PurchaseOrderController::class, 'pdf'])->name('pdf');
                Route::post('/{purchaseOrder}/email', [\App\Http\Controllers\Tenant\Procurement\PurchaseOrderController::class, 'email'])->name('email');
                Route::get('/search/vendors', [\App\Http\Controllers\Tenant\Procurement\PurchaseOrderController::class, 'searchVendors'])->name('search.vendors');
                Route::get('/search/products', [\App\Http\Controllers\Tenant\Procurement\PurchaseOrderController::class, 'searchProducts'])->name('search.products');
            });
        });

        // Banking - Bank Account Management
        Route::prefix('banking')->name('tenant.banking.')->group(function () {
            // Bank Accounts
            Route::get('banks/{bank}/statement', [\App\Http\Controllers\Tenant\Banking\BankController::class, 'statement'])->name('banks.statement');
            Route::get('banks/{bank}/statement/print', [\App\Http\Controllers\Tenant\Banking\BankController::class, 'statementPrint'])->name('banks.statement.print');
            Route::resource('banks', \App\Http\Controllers\Tenant\Banking\BankController::class);

            // Bank Reconciliation
            Route::prefix('reconciliations')->name('reconciliations.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Banking\BankReconciliationController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Banking\BankReconciliationController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Banking\BankReconciliationController::class, 'store'])->name('store');
                Route::get('/{reconciliation}', [\App\Http\Controllers\Tenant\Banking\BankReconciliationController::class, 'show'])->name('show');
                Route::post('/{reconciliation}/update-item', [\App\Http\Controllers\Tenant\Banking\BankReconciliationController::class, 'updateItemStatus'])->name('update-item');
                Route::post('/{reconciliation}/complete', [\App\Http\Controllers\Tenant\Banking\BankReconciliationController::class, 'complete'])->name('complete');
                Route::post('/{reconciliation}/cancel', [\App\Http\Controllers\Tenant\Banking\BankReconciliationController::class, 'cancel'])->name('cancel');
                Route::delete('/{reconciliation}', [\App\Http\Controllers\Tenant\Banking\BankReconciliationController::class, 'destroy'])->name('destroy');
            });
        });

        // POS - Point of Sale System
        Route::prefix('pos')->name('tenant.pos.')->group(function () {
            Route::get('/', [PosController::class, 'index'])->name('index');
            Route::post('/', [PosController::class, 'store'])->name('store');

            // Receipt routes
            Route::get('/receipt/{sale}', [PosController::class, 'receipt'])->name('receipt');
            Route::get('/receipt/{sale}/print', [PosController::class, 'printReceipt'])->name('receipt.print');
            Route::post('/receipt/{sale}/email', [PosController::class, 'emailReceipt'])->name('email-receipt');

            // Sale actions (void, refund)
            Route::post('/{sale}/void', [PosController::class, 'voidTransaction'])->name('void');
            Route::post('/{sale}/refund', [PosController::class, 'refundTransaction'])->name('refund');

            Route::get('/register-session', [PosController::class, 'registerSession'])->name('register-session');
            Route::post('/open-session', [PosController::class, 'openSession'])->name('open-session');
            Route::get('/close-session', [PosController::class, 'closeSession'])->name('close-session');
            Route::post('/close-session', [PosController::class, 'storeCloseSession'])->name('store-close-session');

            // Customer Display
            Route::get('/customer-display', [PosController::class, 'customerDisplay'])->name('customer-display');

            Route::get('/sale', [PosController::class, 'sale'])->name('sale');
            Route::post('/sale', [PosController::class, 'processSale'])->name('sale.process');
            Route::get('/transactions', [PosController::class, 'transactions'])->name('transactions');
            Route::get('/transaction/{transaction}', [PosController::class, 'showTransaction'])->name('transaction.show');
            Route::get('/transaction/{transaction}/receipt', [PosController::class, 'receipt'])->name('transaction.receipt');
            Route::get('/transaction/{transaction}/print', [PosController::class, 'printReceipt'])->name('transaction.print');
            Route::post('/transaction/{transaction}/void', [PosController::class, 'voidTransaction'])->name('transaction.void');
            Route::post('/transaction/{transaction}/refund', [PosController::class, 'refundTransaction'])->name('transaction.refund');

            // POS Reports
            Route::get('/reports', [PosController::class, 'reports'])->name('reports');
            Route::get('/reports/daily-sales', [PosController::class, 'dailySalesReport'])->name('reports.daily-sales');
            Route::get('/reports/monthly-sales', [PosController::class, 'monthlySalesReport'])->name('reports.monthly-sales');
            Route::get('/reports/top-products', [PosController::class, 'topProductsReport'])->name('reports.top-products');
        });

        // Payroll Management Routes
        Route::prefix('payroll')->name('tenant.payroll.')->group(function () {
            Route::get('/', [PayrollController::class, 'index'])->name('index');

            // Employees route (accessible directly)
            Route::get('/employees', [PayrollController::class, 'employees'])->name('employees');

            // Employees Management
            Route::prefix('employees')->name('employees.')->group(function () {
                Route::get('/', [PayrollController::class, 'employees'])->name('index');
                Route::get('/create', [PayrollController::class, 'createEmployee'])->name('create');

                // Non-parameterized routes MUST come before /{employee} routes
                Route::get('/export', [PayrollController::class, 'exportEmployees'])->name('export-all');
                Route::get('/template', [PayrollController::class, 'downloadEmployeeTemplate'])->name('template');
                Route::post('/import', [PayrollController::class, 'importEmployees'])->name('import');
                Route::post('/bulk-action', [PayrollController::class, 'bulkEmployeeAction'])->name('bulk-action');

                // Parameterized routes come last
                Route::post('/', [PayrollController::class, 'storeEmployee'])->name('store');
                Route::get('/{employee}', [PayrollController::class, 'showEmployee'])->name('show');
                Route::get('/{employee}/edit', [PayrollController::class, 'editEmployee'])->name('edit');
                Route::put('/{employee}', [PayrollController::class, 'updateEmployee'])->name('update');
                Route::delete('/{employee}', [PayrollController::class, 'destroyEmployee'])->name('destroy');
                Route::patch('/{employee}/toggle-status', [PayrollController::class, 'toggleStatus'])->name('toggle-status');
                Route::post('/{employee}/reset-portal-token', [PayrollController::class, 'resetPortalToken'])->name('reset-portal-token');
                Route::post('/{employee}/reset-portal-link', [PayrollController::class, 'resetPortalToken'])->name('reset-portal-link');
                Route::get('/{employee}/profile', [PayrollController::class, 'employeeProfile'])->name('profile');
                Route::get('/{employee}/salary-history', [PayrollController::class, 'salaryHistory'])->name('salary-history');
                Route::get('/{employee}/edit-salary', [PayrollController::class, 'editSalary'])->name('edit-salary');
                Route::post('/{employee}/update-salary', [PayrollController::class, 'updateSalary'])->name('update-salary');
                Route::get('/{employee}/export', [PayrollController::class, 'exportEmployee'])->name('export');
                Route::get('/{employee}/payslip', [PayrollController::class, 'generatePayslip'])->name('payslip');

                // Document management
                Route::post('/{employee}/documents', [PayrollController::class, 'uploadDocument'])->name('documents.upload');
                Route::get('/{employee}/documents/{document}/download', [PayrollController::class, 'downloadDocument'])->name('documents.download');
                Route::delete('/{employee}/documents/{document}', [PayrollController::class, 'deleteDocument'])->name('documents.delete');
            });

            // Departments
            Route::prefix('departments')->name('departments.')->group(function () {
                Route::get('/', [PayrollController::class, 'departments'])->name('index');
                Route::get('/create', [PayrollController::class, 'createDepartment'])->name('create');
                Route::post('/', [PayrollController::class, 'storeDepartment'])->name('store');
                Route::get('/{department}', [PayrollController::class, 'showDepartment'])->name('show');
                Route::get('/{department}/edit', [PayrollController::class, 'editDepartment'])->name('edit');
                Route::put('/{department}', [PayrollController::class, 'updateDepartment'])->name('update');
                Route::delete('/{department}', [PayrollController::class, 'destroyDepartment'])->name('destroy');
            });

            // Positions
            Route::prefix('positions')->name('positions.')->group(function () {
                Route::get('/', [PositionController::class, 'index'])->name('index');
                Route::get('/create', [PositionController::class, 'create'])->name('create');
                Route::post('/', [PositionController::class, 'store'])->name('store');
                Route::get('/{position}', [PositionController::class, 'show'])->name('show');
                Route::get('/{position}/edit', [PositionController::class, 'edit'])->name('edit');
                Route::put('/{position}', [PositionController::class, 'update'])->name('update');
                Route::delete('/{position}', [PositionController::class, 'destroy'])->name('destroy');
                Route::post('/{position}/toggle-status', [PositionController::class, 'toggleStatus'])->name('toggle-status');
                Route::get('/by-department', [PositionController::class, 'byDepartment'])->name('by-department');
            });

            // Salary Components
            Route::prefix('components')->name('components.')->group(function () {
                Route::get('/', [PayrollController::class, 'components'])->name('index');
                Route::get('/create', [PayrollController::class, 'createComponent'])->name('create');
                Route::post('/', [PayrollController::class, 'storeComponent'])->name('store');
                Route::get('/{component}', [PayrollController::class, 'showComponent'])->name('show');
                Route::get('/{component}/edit', [PayrollController::class, 'editComponent'])->name('edit');
                Route::put('/{component}', [PayrollController::class, 'updateComponent'])->name('update');
                Route::delete('/{component}', [PayrollController::class, 'destroyComponent'])->name('destroy');
            });

            // Payroll Processing
            Route::prefix('processing')->name('processing.')->group(function () {
                Route::get('/', [PayrollController::class, 'processing'])->name('index');
                Route::get('/create', [PayrollController::class, 'createPayroll'])->name('create');
                Route::get('/export-summary', [PayrollController::class, 'exportProcessingSummary'])->name('export-summary');
                Route::post('/', [PayrollController::class, 'processPayroll'])->name('store');
                Route::get('/{period}', [PayrollController::class, 'showPayrollPeriod'])->name('show');
                Route::get('/{period}/edit', [PayrollController::class, 'editPayrollPeriod'])->name('edit');
                Route::put('/{period}', [PayrollController::class, 'updatePayrollPeriod'])->name('update');
                Route::delete('/{period}', [PayrollController::class, 'deletePayrollPeriod'])->name('destroy');
                Route::post('/{period}/generate', [PayrollController::class, 'generatePayroll'])->name('generate');
                Route::post('/{period}/approve', [PayrollController::class, 'approvePayroll'])->name('approve');
                Route::post('/{period}/finalize', [PayrollController::class, 'finalizePayroll'])->name('finalize');
                Route::delete('/{period}/reset', [PayrollController::class, 'resetPayrollGeneration'])->name('reset');
                Route::get('/{period}/export-tax-file', [PayrollController::class, 'exportTaxFile'])->name('export-tax-file');
                Route::get('/{period}/export-bank-file', [PayrollController::class, 'exportBankFile'])->name('export-bank-file');
            });

            // Loans Management
            Route::prefix('loans')->name('loans.')->group(function () {
                Route::get('/', [PayrollController::class, 'loans'])->name('index');
                Route::get('/create', [PayrollController::class, 'createLoan'])->name('create');
                Route::post('/', [PayrollController::class, 'storeLoan'])->name('store');
                Route::get('/{loan}', [PayrollController::class, 'showLoan'])->name('show');
                Route::get('/{loan}/edit', [PayrollController::class, 'editLoan'])->name('edit');
                Route::put('/{loan}', [PayrollController::class, 'updateLoan'])->name('update');
                Route::delete('/{loan}', [PayrollController::class, 'destroyLoan'])->name('destroy');
                Route::post('/{loan}/approve', [PayrollController::class, 'approveLoan'])->name('approve');
            });

            // Salary Advance / IOU Management
            Route::get('/salary-advance/create', [PayrollController::class, 'createSalaryAdvance'])->name('salary-advance.create');
            Route::post('/salary-advance/store', [PayrollController::class, 'storeSalaryAdvance'])->name('salary-advance.store');

            // Payroll Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/summary', [PayrollController::class, 'payrollSummary'])->name('summary');
                Route::get('/detailed', [PayrollController::class, 'detailedReport'])->name('detailed');
                Route::get('/tax-report', [PayrollController::class, 'taxReport'])->name('tax-report');
                Route::get('/tax-summary', [PayrollController::class, 'taxSummary'])->name('tax-summary');
                Route::get('/employee-summary', [PayrollController::class, 'employeeSummary'])->name('employee-summary');
                Route::get('/bank-schedule', [PayrollController::class, 'bankSchedule'])->name('bank-schedule');
            });

            // Payslips Management
            Route::prefix('payslips')->name('payslips.')->group(function () {
                Route::get('/{payrollRun}', [PayrollController::class, 'viewPayslip'])->name('view');
                Route::get('/{payrollRun}/download', [PayrollController::class, 'downloadPayslip'])->name('download');
                Route::post('/{payrollRun}/email', [PayrollController::class, 'emailPayslip'])->name('email');
                Route::post('/{payrollRun}/mark-paid', [PayrollController::class, 'markPayslipAsPaid'])->name('mark-paid');
            });

            // Payment Management
            Route::post('/processing/{period}/mark-paid', [PayrollController::class, 'markPayrollAsPaid'])->name('processing.mark-paid');

            // Attendance & Leave Management
            Route::prefix('attendance')->name('attendance.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'index'])->name('index');
                Route::get('/monthly-report', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'monthlyReport'])->name('monthly-report');
                Route::get('/employee/{employee}', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'employeeAttendance'])->name('employee');
                Route::get('/qr-codes', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'showAttendanceQR'])->name('qr-codes');
                Route::get('/generate-qr', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'generateAttendanceQR'])->name('generate-qr');

                Route::post('/clock-in', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'clockIn'])->name('clock-in');
                Route::post('/clock-out', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'clockOut'])->name('clock-out');
                Route::post('/manual-entry', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'manualEntry'])->name('manual-entry');
                Route::post('/mark-absent', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'markAbsent'])->name('mark-absent');
                Route::post('/mark-leave', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'markLeave'])->name('mark-leave');
                Route::post('/{attendance}/mark-half-day', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'markHalfDay'])->name('mark-half-day');
                Route::post('/{attendance}/approve', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'approve'])->name('approve');
                Route::post('/bulk-approve', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'bulkApprove'])->name('bulk-approve');
                Route::put('/{attendance}', [\App\Http\Controllers\Tenant\Payroll\AttendanceController::class, 'update'])->name('update');
            });

            // Leave Management
            Route::prefix('leaves')->name('leaves.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\LeaveController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\LeaveController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\LeaveController::class, 'store'])->name('store');
                Route::get('/{id}', [\App\Http\Controllers\Tenant\LeaveController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [\App\Http\Controllers\Tenant\LeaveController::class, 'edit'])->name('edit');
                Route::put('/{id}', [\App\Http\Controllers\Tenant\LeaveController::class, 'update'])->name('update');
                Route::post('/{id}/approve', [\App\Http\Controllers\Tenant\LeaveController::class, 'approve'])->name('approve');
                Route::post('/{id}/reject', [\App\Http\Controllers\Tenant\LeaveController::class, 'reject'])->name('reject');
                Route::post('/{id}/cancel', [\App\Http\Controllers\Tenant\LeaveController::class, 'cancel'])->name('cancel');
                Route::get('/balances/view', [\App\Http\Controllers\Tenant\LeaveController::class, 'balances'])->name('balances');
                Route::get('/balance-history/{employeeId}', [\App\Http\Controllers\Tenant\LeaveController::class, 'balanceHistory'])->name('balance-history');
                Route::get('/my-leaves', [\App\Http\Controllers\Tenant\LeaveController::class, 'myLeaves'])->name('my-leaves');
            });

            // Overtime Management
            Route::prefix('overtime')->name('overtime.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\OvertimeController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\OvertimeController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\OvertimeController::class, 'store'])->name('store');
                Route::get('/{id}', [\App\Http\Controllers\Tenant\OvertimeController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [\App\Http\Controllers\Tenant\OvertimeController::class, 'edit'])->name('edit');
                Route::put('/{id}', [\App\Http\Controllers\Tenant\OvertimeController::class, 'update'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\Tenant\OvertimeController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/approve', [\App\Http\Controllers\Tenant\OvertimeController::class, 'approve'])->name('approve');
                Route::post('/{id}/reject', [\App\Http\Controllers\Tenant\OvertimeController::class, 'reject'])->name('reject');
                Route::post('/{id}/mark-paid', [\App\Http\Controllers\Tenant\OvertimeController::class, 'markPaid'])->name('mark-paid');
                Route::get('/{id}/download-payment-slip', [\App\Http\Controllers\Tenant\OvertimeController::class, 'downloadPaymentSlip'])->name('download-payment-slip');
                Route::post('/bulk-approve', [\App\Http\Controllers\Tenant\OvertimeController::class, 'bulkApprove'])->name('bulk-approve');
                Route::get('/report/monthly', [\App\Http\Controllers\Tenant\OvertimeController::class, 'report'])->name('report');
            });

            // Shift Management
            Route::prefix('shifts')->name('shifts.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\ShiftController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\ShiftController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\ShiftController::class, 'store'])->name('store');
                Route::get('/{id}', [\App\Http\Controllers\Tenant\ShiftController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [\App\Http\Controllers\Tenant\ShiftController::class, 'edit'])->name('edit');
                Route::put('/{id}', [\App\Http\Controllers\Tenant\ShiftController::class, 'update'])->name('update');
                Route::delete('/{id}', [\App\Http\Controllers\Tenant\ShiftController::class, 'destroy'])->name('destroy');

                // Shift Assignments
                Route::get('/assignments/list', [\App\Http\Controllers\Tenant\ShiftController::class, 'assignments'])->name('assignments');
                Route::get('/assignments/create', [\App\Http\Controllers\Tenant\ShiftController::class, 'assignEmployees'])->name('assign-employees');
                Route::post('/assignments', [\App\Http\Controllers\Tenant\ShiftController::class, 'storeAssignment'])->name('store-assignment');
                Route::post('/assignments/{id}/end', [\App\Http\Controllers\Tenant\ShiftController::class, 'endAssignment'])->name('end-assignment');
                Route::post('/assignments/bulk', [\App\Http\Controllers\Tenant\ShiftController::class, 'bulkAssign'])->name('bulk-assign');
            });

            // Announcements & Notifications
            Route::prefix('announcements')->name('announcements.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'store'])->name('store');
                Route::get('/{announcement}', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'show'])->name('show');
                Route::get('/{announcement}/edit', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'edit'])->name('edit');
                Route::put('/{announcement}', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'update'])->name('update');
                Route::delete('/{announcement}', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'destroy'])->name('destroy');
                Route::post('/{announcement}/send', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'send'])->name('send');
                Route::post('/preview-recipients', [\App\Http\Controllers\Tenant\Payroll\AnnouncementController::class, 'previewRecipients'])->name('preview-recipients');
            });

            // PFA Management
            Route::resource('pfas', \App\Http\Controllers\Tenant\PfaController::class)->except(['show']);

            // Payroll Settings
            Route::get('/settings', [PayrollController::class, 'settings'])->name('settings');
            Route::put('/settings', [PayrollController::class, 'updateSettings'])->name('settings.update');
        });

        // Admin Management Module
        Route::prefix('admin')->name('tenant.admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');

            // Diagnostic routes (temporary - remove in production)
            Route::prefix('diagnostic')->name('diagnostic.')->group(function () {
                Route::get('/routes', [\App\Http\Controllers\Tenant\Admin\DiagnosticController::class, 'checkRoutes'])->name('routes');
                Route::get('/user/{userId}', [\App\Http\Controllers\Tenant\Admin\DiagnosticController::class, 'testUserAccess'])->name('user');
            });

            // Users & Admins Management
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [AdminController::class, 'users'])->name('index');
                Route::get('/create', [AdminController::class, 'createUser'])->name('create');
                Route::post('/', [AdminController::class, 'storeUser'])->name('store');
                Route::get('/export', [AdminController::class, 'exportUsers'])->name('export');
                Route::post('/import', [AdminController::class, 'importUsers'])->name('import');
                Route::post('/bulk-action', [AdminController::class, 'bulkUserAction'])->name('bulk-action');
                Route::get('/{userId}/edit', [AdminController::class, 'editUser'])->name('edit');
                Route::get('/{userId}/login-as', [AdminController::class, 'loginAsUser'])->name('login-as');
                Route::get('/{userId}', [AdminController::class, 'showUser'])->name('show');
                Route::put('/{userId}', [AdminController::class, 'updateUser'])->name('update');
                Route::delete('/{userId}', [AdminController::class, 'destroyUser'])->name('destroy');
                Route::post('/{userId}/activate', [AdminController::class, 'activateUser'])->name('activate');
                Route::post('/{userId}/deactivate', [AdminController::class, 'deactivateUser'])->name('deactivate');
                Route::post('/{userId}/reset-password', [AdminController::class, 'resetUserPassword'])->name('reset-password');
            });

            // Roles & Permissions
            Route::prefix('roles')->name('roles.')->group(function () {
                Route::get('/', [AdminController::class, 'roles'])->name('index');
                Route::get('/create', [AdminController::class, 'createRole'])->name('create');
                Route::post('/', [AdminController::class, 'storeRole'])->name('store');
                Route::get('/matrix', [AdminController::class, 'permissionMatrix'])->name('matrix');
                Route::get('/{role}/permissions', [AdminController::class, 'getRolePermissions']);
                Route::post('/{role}/clone', [AdminController::class, 'cloneRole'])->name('clone');
                Route::get('/{role}', [AdminController::class, 'showRole'])->name('show');
                Route::get('/{role}/edit', [AdminController::class, 'editRole'])->name('edit');
                Route::put('/{role}', [AdminController::class, 'updateRole'])->name('update');
                Route::delete('/{role}', [AdminController::class, 'destroyRole'])->name('destroy');
                Route::post('/{role}/assign-permission', [AdminController::class, 'assignPermission'])->name('assign-permission');
                Route::delete('/{role}/revoke-permission', [AdminController::class, 'revokePermission'])->name('revoke-permission');
            });

            // Permissions Management
            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::get('/', [AdminController::class, 'permissions'])->name('index');
                Route::get('/create', [AdminController::class, 'createPermission'])->name('create');
                Route::post('/', [AdminController::class, 'storePermission'])->name('store');
                Route::get('/{permission}', [AdminController::class, 'showPermission'])->name('show');
                Route::get('/{permission}/edit', [AdminController::class, 'editPermission'])->name('edit');
                Route::put('/{permission}', [AdminController::class, 'updatePermission'])->name('update');
                Route::delete('/{permission}', [AdminController::class, 'destroyPermission'])->name('destroy');
                Route::post('/sync', [AdminController::class, 'syncPermissions'])->name('sync');
                Route::get('/by-module', [AdminController::class, 'permissionsByModule'])->name('by-module');
            });

            // Security & Access Management
            Route::prefix('security')->name('security.')->group(function () {
                Route::get('/', [AdminController::class, 'security'])->name('index');
                Route::get('/login-attempts', [AdminController::class, 'loginAttempts'])->name('login-attempts');
                Route::get('/active-sessions', [AdminController::class, 'activeSessions'])->name('active-sessions');
                Route::post('/terminate-session', [AdminController::class, 'terminateSession'])->name('terminate-session');
                Route::get('/security-logs', [AdminController::class, 'securityLogs'])->name('logs');
                Route::get('/security-settings', [AdminController::class, 'securitySettings'])->name('settings');
                Route::put('/security-settings', [AdminController::class, 'updateSecuritySettings'])->name('settings.update');
            });

            // Team Management
            Route::prefix('teams')->name('teams.')->group(function () {
                Route::get('/', [AdminController::class, 'teams'])->name('index');
                Route::get('/create', [AdminController::class, 'createTeam'])->name('create');
                Route::post('/', [AdminController::class, 'storeTeam'])->name('store');
                Route::get('/{team}', [AdminController::class, 'showTeam'])->name('show');
                Route::get('/{team}/edit', [AdminController::class, 'editTeam'])->name('edit');
                Route::put('/{team}', [AdminController::class, 'updateTeam'])->name('update');
                Route::delete('/{team}', [AdminController::class, 'destroyTeam'])->name('destroy');
                Route::post('/{team}/add-member', [AdminController::class, 'addTeamMember'])->name('add-member');
                Route::delete('/{team}/remove-member/{user}', [AdminController::class, 'removeTeamMember'])->name('remove-member');
            });

            // Activity & Audit Logs
            Route::prefix('activity')->name('activity.')->group(function () {
                Route::get('/', [AdminController::class, 'activityLogs'])->name('index');
                Route::get('/{log}', [AdminController::class, 'showActivityLog'])->name('show');
                Route::delete('/{log}', [AdminController::class, 'destroyActivityLog'])->name('destroy');
                Route::post('/clear-old', [AdminController::class, 'clearOldLogs'])->name('clear-old');
                Route::get('/export', [AdminController::class, 'exportActivity'])->name('export');
            });

            // System Information
            Route::prefix('system')->name('system.')->group(function () {
                Route::get('/info', [AdminController::class, 'systemInfo'])->name('info');
                Route::get('/health', [AdminController::class, 'systemHealth'])->name('health');
                Route::get('/logs', [AdminController::class, 'systemLogs'])->name('logs');
                Route::post('/optimize', [AdminController::class, 'optimizeSystem'])->name('optimize');
            });

            // Reports & Analytics
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', [AdminController::class, 'adminReports'])->name('index');
                Route::get('/user-activity', [AdminController::class, 'userActivity'])->name('user-activity');
                Route::get('/system-usage', [AdminController::class, 'systemUsage'])->name('system-usage');
                Route::get('/login-analytics', [AdminController::class, 'loginAnalytics'])->name('login-analytics');
            });
        });

        // Reports & Analytics Module
        Route::prefix('reports')->name('tenant.reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');

            // Financial Reports
            Route::get('/financial', [ReportsController::class, 'financial'])->name('financial');
            Route::get('/profit-loss', [ReportsController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/balance-sheet', [ReportsController::class, 'balanceSheet'])->name('balance-sheet');
            Route::get('/trial-balance', [ReportsController::class, 'trialBalance'])->name('trial-balance');
            Route::get('/cash-flow', [ReportsController::class, 'cashFlow'])->name('cash-flow');

            // Inventory Reports
            Route::get('/inventory', [ReportsController::class, 'inventory'])->name('inventory');
            Route::get('/stock-movement', [ReportsController::class, 'stockMovement'])->name('stock-movement');
            Route::get('/low-stock', [ReportsController::class, 'lowStock'])->name('low-stock');
            Route::get('/inventory-valuation', [ReportsController::class, 'inventoryValuation'])->name('inventory-valuation');

            // Sales Reports
            Route::get('/sales', [ReportsController::class, 'sales'])->name('sales');
            Route::get('/sales-summary', [SalesReportsController::class, 'salesSummary'])->name('sales-summary');
            Route::get('/customer-sales', [SalesReportsController::class, 'customerSales'])->name('customer-sales');
            Route::get('/product-sales', [SalesReportsController::class, 'productSales'])->name('product-sales');
            Route::get('/sales-by-period', [SalesReportsController::class, 'salesByPeriod'])->name('sales-by-period');
            Route::get('/customer-analysis', [ReportsController::class, 'customerAnalysis'])->name('customer-analysis');
            Route::get('/product-performance', [ReportsController::class, 'productPerformance'])->name('product-performance');

            // Purchase Reports
            Route::get('/purchase-summary', [PurchaseReportsController::class, 'purchaseSummary'])->name('purchase-summary');
            Route::get('/vendor-purchases', [PurchaseReportsController::class, 'vendorPurchases'])->name('vendor-purchases');
            Route::get('/product-purchases', [PurchaseReportsController::class, 'productPurchases'])->name('product-purchases');
            Route::get('/purchases-by-period', [PurchaseReportsController::class, 'purchasesByPeriod'])->name('purchases-by-period');

            // Inventory Reports
            Route::get('/stock-summary', [\App\Http\Controllers\Tenant\Reports\InventoryReportsController::class, 'stockSummary'])->name('stock-summary');
            Route::get('/low-stock-alert', [\App\Http\Controllers\Tenant\Reports\InventoryReportsController::class, 'lowStockAlert'])->name('low-stock-alert');
            Route::get('/stock-valuation', [\App\Http\Controllers\Tenant\Reports\InventoryReportsController::class, 'stockValuation'])->name('stock-valuation');
            Route::get('/stock-movement', [\App\Http\Controllers\Tenant\Reports\InventoryReportsController::class, 'stockMovement'])->name('stock-movement');
            // Bin Card (Inventory Ledger) - per product running balance
            Route::get('/bin-card', [\App\Http\Controllers\Tenant\Reports\InventoryReportsController::class, 'binCard'])->name('bin-card');
        });

        // Statutory & Tax Management Module
        Route::prefix('statutory')->name('tenant.statutory.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\StatutoryController::class, 'index'])->name('index');

            // VAT Management
            Route::get('/vat/dashboard', [\App\Http\Controllers\Tenant\StatutoryController::class, 'vatDashboard'])->name('vat.dashboard');
            Route::get('/vat/output', [\App\Http\Controllers\Tenant\StatutoryController::class, 'vatOutput'])->name('vat.output');
            Route::get('/vat/input', [\App\Http\Controllers\Tenant\StatutoryController::class, 'vatInput'])->name('vat.input');
            Route::get('/vat/report', [\App\Http\Controllers\Tenant\StatutoryController::class, 'vatReport'])->name('vat.report');

            // Pension Management
            Route::get('/pension/report', [\App\Http\Controllers\Tenant\StatutoryController::class, 'pensionReport'])->name('pension.report');

            // Tax Settings
            Route::get('/settings', [\App\Http\Controllers\Tenant\StatutoryController::class, 'settings'])->name('settings');
            Route::put('/settings', [\App\Http\Controllers\Tenant\StatutoryController::class, 'updateSettings'])->name('settings.update');
        });

        // Help & Documentation
        Route::get('/help', [HelpController::class, 'index'])->name('tenant.help');

        // Support Center
        Route::prefix('support')->name('tenant.support.')->group(function () {
            // Ticket Management
            Route::get('/', [SupportController::class, 'index'])->name('index');
            Route::get('/create', [SupportController::class, 'create'])->name('create');
            Route::post('/tickets', [SupportController::class, 'store'])->name('tickets.store');
            Route::get('/tickets/{supportTicket}', [SupportController::class, 'show'])->where('supportTicket', '[0-9]+')->name('tickets.show');
            Route::post('/tickets/{supportTicket}/reply', [SupportController::class, 'reply'])->where('supportTicket', '[0-9]+')->name('tickets.reply');
            Route::post('/tickets/{supportTicket}/close', [SupportController::class, 'close'])->where('supportTicket', '[0-9]+')->name('tickets.close');
            Route::post('/tickets/{supportTicket}/reopen', [SupportController::class, 'reopen'])->where('supportTicket', '[0-9]+')->name('tickets.reopen');
            Route::post('/tickets/{supportTicket}/rate', [SupportController::class, 'rate'])->where('supportTicket', '[0-9]+')->name('tickets.rate');

            // Attachments
            Route::post('/attachments/upload', [SupportController::class, 'uploadAttachment'])->name('attachments.upload');
            Route::get('/attachments/{attachment}/download', [SupportController::class, 'downloadAttachment'])->name('attachments.download');

            // Knowledge Base
            Route::get('/knowledge-base', [SupportController::class, 'knowledgeBase'])->name('knowledge-base.index');
            Route::get('/knowledge-base/{category}', [SupportController::class, 'knowledgeBaseCategory'])->name('knowledge-base.category');
            Route::get('/knowledge-base/{category}/{article}', [SupportController::class, 'knowledgeBaseArticle'])->name('knowledge-base.article');
            Route::post('/knowledge-base/{article}/helpful', [SupportController::class, 'markHelpful'])->name('knowledge-base.helpful');

            // Search
            Route::get('/search', [SupportController::class, 'search'])->name('search');
        });

        // Notifications
        Route::prefix('notifications')->name('tenant.notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Tenant\NotificationController::class, 'index'])->name('index');
            Route::get('/unread-count', [\App\Http\Controllers\Tenant\NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::post('/{id}/mark-read', [\App\Http\Controllers\Tenant\NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [\App\Http\Controllers\Tenant\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [\App\Http\Controllers\Tenant\NotificationController::class, 'destroy'])->name('destroy');
        });

        // Settings & Configuration Module
        Route::prefix('settings')->name('tenant.settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');

            // General Settings
            Route::get('/general', [SettingsController::class, 'general'])->name('general');
            Route::put('/general', [SettingsController::class, 'updateGeneral'])->name('general.update');

            // Company Settings (Owner Only)
            Route::get('/company', [\App\Http\Controllers\Tenant\CompanySettingsController::class, 'index'])->name('company');
            Route::put('/company/info', [\App\Http\Controllers\Tenant\CompanySettingsController::class, 'updateCompanyInfo'])->name('company.update-info');
            Route::put('/company/business', [\App\Http\Controllers\Tenant\CompanySettingsController::class, 'updateBusinessDetails'])->name('company.update-business');
            Route::put('/company/logo', [\App\Http\Controllers\Tenant\CompanySettingsController::class, 'updateLogo'])->name('company.update-logo');
            Route::delete('/company/logo', [\App\Http\Controllers\Tenant\CompanySettingsController::class, 'removeLogo'])->name('company.remove-logo');
            Route::put('/company/preferences', [\App\Http\Controllers\Tenant\CompanySettingsController::class, 'updatePreferences'])->name('company.update-preferences');

            // Financial Settings
            Route::get('/financial', [SettingsController::class, 'financial'])->name('financial');
            Route::put('/financial', [SettingsController::class, 'updateFinancial'])->name('financial.update');

            // Tax Settings
            Route::get('/tax', [SettingsController::class, 'tax'])->name('tax');
            Route::put('/tax', [SettingsController::class, 'updateTax'])->name('tax.update');

            // Email Settings
            Route::get('/email', [SettingsController::class, 'email'])->name('email');
            Route::put('/email', [SettingsController::class, 'updateEmail'])->name('email.update');
            Route::post('/email/test', [SettingsController::class, 'testEmail'])->name('email.test');

            // Notification Settings
            Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications');
            Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications.update');

            // Integration Settings
            Route::get('/integrations', [SettingsController::class, 'integrations'])->name('integrations');
            Route::put('/integrations', [SettingsController::class, 'updateIntegrations'])->name('integrations.update');

            // Backup Settings
            Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
            Route::post('/backup/create', [SettingsController::class, 'createBackup'])->name('backup.create');
            Route::get('/backup/download/{backup}', [SettingsController::class, 'downloadBackup'])->name('backup.download');
            Route::delete('/backup/{backup}', [SettingsController::class, 'deleteBackup'])->name('backup.delete');

            // Cash Register Management
            Route::prefix('cash-registers')->name('cash-registers.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Tenant\Settings\CashRegisterController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Tenant\Settings\CashRegisterController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\Tenant\Settings\CashRegisterController::class, 'store'])->name('store');
                Route::get('/{cashRegister}/edit', [\App\Http\Controllers\Tenant\Settings\CashRegisterController::class, 'edit'])->name('edit');
                Route::put('/{cashRegister}', [\App\Http\Controllers\Tenant\Settings\CashRegisterController::class, 'update'])->name('update');
                Route::delete('/{cashRegister}', [\App\Http\Controllers\Tenant\Settings\CashRegisterController::class, 'destroy'])->name('destroy');
                Route::patch('/{cashRegister}/toggle-status', [\App\Http\Controllers\Tenant\Settings\CashRegisterController::class, 'toggleStatus'])->name('toggle-status');
            });
        });

    }); // Close subscription.check middleware group

    // Subscription & Plan Management (accessible even with expired subscription)
    Route::middleware(['onboarding.completed'])->group(function () {
        Route::prefix('subscription')->name('tenant.subscription.')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('index');
            Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');
            Route::get('/renew', [SubscriptionController::class, 'renew'])->name('renew');
            Route::post('/renew', [SubscriptionController::class, 'processRenewal'])->name('renew.process');
            Route::get('/upgrade/{plan}', [SubscriptionController::class, 'upgrade'])->name('upgrade');
            Route::post('/upgrade/{plan}', [SubscriptionController::class, 'processUpgrade'])->name('upgrade.process');
            Route::get('/downgrade/{plan}', [SubscriptionController::class, 'downgrade'])->name('downgrade');
            Route::post('/downgrade/{plan}', [SubscriptionController::class, 'processDowngrade'])->name('downgrade.process');
            Route::get('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
            Route::post('/cancel', [SubscriptionController::class, 'processCancel'])->name('cancel.process');
            Route::get('/history', [SubscriptionController::class, 'history'])->name('history');
            Route::get('/invoice/{payment}', [SubscriptionController::class, 'invoice'])->name('invoice');
            Route::get('/invoice/{payment}/download', [SubscriptionController::class, 'downloadInvoice'])->name('invoice.download');

            // Payment callbacks
            Route::get('/payment/success', [SubscriptionController::class, 'paymentSuccess'])->name('payment.success');
            Route::get('/payment/cancel', [SubscriptionController::class, 'paymentCancel'])->name('payment.cancel');
            Route::get('/payment/callback/{payment}', [SubscriptionController::class, 'paymentCallback'])->name('payment.callback');
            Route::post('/webhook', [SubscriptionController::class, 'webhook'])->name('webhook');
        });
    });
}); // Close main authenticated routes group

// Employee Self-Service Portal (outside tenant middleware)
Route::prefix('employee-portal')->name('payroll.portal.')->group(function () {
    Route::match(['get', 'post'], '/{token}/login', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'login'])->name('login');
    Route::get('/{token}/dashboard', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/{token}/payslips', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'payslips'])->name('payslips');
    Route::get('/{token}/payslip/{payslip}', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'payslip'])->name('payslip');
    Route::get('/{token}/payslip/{payslip}/download', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'downloadPayslip'])->name('payslip.download');
    Route::get('/{token}/profile', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'profile'])->name('profile');
    Route::post('/{token}/profile', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'updateProfile'])->name('profile.update');
    Route::get('/{token}/loans', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'loans'])->name('loans');
    Route::get('/{token}/tax-certificate/{year?}', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'taxCertificate'])->name('tax-certificate');
    Route::get('/{token}/tax-certificate/{year}/download', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'downloadTaxCertificate'])->name('tax-certificate.download');
    Route::get('/{token}/attendance', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'attendance'])->name('attendance');
    Route::post('/{token}/scan-attendance', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'scanAttendanceQR'])->name('scan-attendance');
    Route::post('/{token}/logout', [App\Http\Controllers\Payroll\EmployeePortalController::class, 'logout'])->name('logout');
});
