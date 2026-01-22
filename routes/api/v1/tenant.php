<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Tenant\AuthController;
use App\Http\Controllers\Api\Tenant\OnboardingController;

/*
|--------------------------------------------------------------------------
| Tenant API v1 Routes
|--------------------------------------------------------------------------
|
| Mobile API routes for tenant users.
| All routes are prefixed with: /api/v1/tenant/{tenant}
|
*/

// Public authentication routes (no auth required)
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
});

// Protected routes (requires auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // Auth management
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
        Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('refresh-token');
        Route::get('/sessions', [AuthController::class, 'sessions'])->name('sessions');
        Route::delete('/sessions/{tokenId}', [AuthController::class, 'revokeSession'])->name('sessions.revoke');
    });

    // User profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AuthController::class, 'profile'])->name('show');
        Route::put('/', [AuthController::class, 'updateProfile'])->name('update');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    });

    // Onboarding
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/status', [OnboardingController::class, 'status'])->name('status');
        Route::post('/company', [OnboardingController::class, 'saveCompany'])->name('save-company');
        Route::post('/preferences', [OnboardingController::class, 'savePreferences'])->name('save-preferences');
        Route::post('/skip', [OnboardingController::class, 'skip'])->name('skip');
        Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
    });

    // Accounting Module
    Route::prefix('accounting')->name('accounting.')->group(function () {

        // Voucher Types
        Route::prefix('voucher-types')->name('voucher-types.')->group(function () {
            Route::get('/search', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'search'])->name('search');
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'store'])->name('store');
            Route::post('/bulk-action', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'index'])->name('index');
            Route::get('/{voucherType}', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'show'])->name('show');
            Route::put('/{voucherType}', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'update'])->name('update');
            Route::delete('/{voucherType}', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'destroy'])->name('destroy');
            Route::post('/{voucherType}/toggle', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'toggle'])->name('toggle');
            Route::post('/{voucherType}/reset-numbering', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherTypeController::class, 'resetNumbering'])->name('reset-numbering');
        });

        // Account Groups
        Route::prefix('account-groups')->name('account-groups.')->group(function () {
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Accounting\AccountGroupController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Accounting\AccountGroupController::class, 'store'])->name('store');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Accounting\AccountGroupController::class, 'index'])->name('index');
            Route::get('/{accountGroup}', [\App\Http\Controllers\Api\Tenant\Accounting\AccountGroupController::class, 'show'])->name('show');
            Route::put('/{accountGroup}', [\App\Http\Controllers\Api\Tenant\Accounting\AccountGroupController::class, 'update'])->name('update');
            Route::delete('/{accountGroup}', [\App\Http\Controllers\Api\Tenant\Accounting\AccountGroupController::class, 'destroy'])->name('destroy');
            Route::post('/{accountGroup}/toggle', [\App\Http\Controllers\Api\Tenant\Accounting\AccountGroupController::class, 'toggle'])->name('toggle');
        });

        // Ledger Accounts
        Route::prefix('ledger-accounts')->name('ledger-accounts.')->group(function () {
            Route::get('/search', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'search'])->name('search');
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'store'])->name('store');
            Route::post('/bulk-action', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'index'])->name('index');
            Route::get('/{ledgerAccount}', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'show'])->name('show');
            Route::put('/{ledgerAccount}', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'update'])->name('update');
            Route::delete('/{ledgerAccount}', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'destroy'])->name('destroy');
            Route::post('/{ledgerAccount}/toggle', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'toggle'])->name('toggle');
            Route::get('/{ledgerAccount}/balance', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'balance'])->name('balance');
            Route::get('/{ledgerAccount}/children', [\App\Http\Controllers\Api\Tenant\Accounting\LedgerAccountController::class, 'children'])->name('children');
        });

        // Invoices (Sales & Purchase)
        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('/search-customers', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'searchCustomers'])->name('search-customers');
            Route::get('/search-products', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'searchProducts'])->name('search-products');
            Route::get('/search-ledger-accounts', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'searchLedgerAccounts'])->name('search-ledger-accounts');
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'store'])->name('store');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'index'])->name('index');
            Route::get('/{invoice}', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'show'])->name('show');
            Route::put('/{invoice}', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'update'])->name('update');
            Route::delete('/{invoice}', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'destroy'])->name('destroy');
            Route::post('/{invoice}/post', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'post'])->name('post');
            Route::post('/{invoice}/unpost', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'unpost'])->name('unpost');
            Route::get('/{invoice}/pdf', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'pdf'])->name('pdf');
            Route::post('/{invoice}/email', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'email'])->name('email');
            Route::post('/{invoice}/record-payment', [\App\Http\Controllers\Api\Tenant\Accounting\InvoiceController::class, 'recordPayment'])->name('record-payment');
        });

        // Vouchers
        Route::prefix('vouchers')->name('vouchers.')->group(function () {
            Route::get('/search', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'search'])->name('search');
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'store'])->name('store');
            Route::post('/bulk-action', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'index'])->name('index');
            Route::get('/{voucher}', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'show'])->name('show');
            Route::put('/{voucher}', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'update'])->name('update');
            Route::delete('/{voucher}', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'destroy'])->name('destroy');
            Route::post('/{voucher}/post', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'post'])->name('post');
            Route::post('/{voucher}/unpost', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'unpost'])->name('unpost');
            Route::get('/{voucher}/duplicate', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'duplicate'])->name('duplicate');
        });

    });

    // Inventory Module
    Route::prefix('inventory')->name('inventory.')->group(function () {

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/search', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'search'])->name('search');
            Route::get('/statistics', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'statistics'])->name('statistics');
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'store'])->name('store');
            Route::post('/bulk-action', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'bulkAction'])->name('bulk-action');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'index'])->name('index');
            Route::get('/{product}', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'show'])->name('show');
            Route::put('/{product}', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'destroy'])->name('destroy');
            Route::post('/{product}/toggle-status', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{product}/stock-movements', [\App\Http\Controllers\Api\Tenant\Inventory\ProductController::class, 'stockMovements'])->name('stock-movements');
        });

    });

    // CRM Module
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'store'])->name('store');
            Route::get('/statements', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'statements'])->name('statements');
            Route::get('/{customer}', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'show'])->name('show');
            Route::put('/{customer}', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'destroy'])->name('destroy');
            Route::post('/{customer}/toggle-status', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{customer}/statement', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'statement'])->name('statement');
            Route::get('/{customer}/statement/pdf', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'statementPdf'])->name('statement-pdf');
            Route::get('/{customer}/statement/excel', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'statementExcel'])->name('statement-excel');
        });

        Route::prefix('vendors')->name('vendors.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'store'])->name('store');
            Route::get('/statements', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'statements'])->name('statements');
            Route::get('/{vendor}', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'show'])->name('show');
            Route::put('/{vendor}', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'update'])->name('update');
            Route::delete('/{vendor}', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'destroy'])->name('destroy');
            Route::post('/{vendor}/toggle-status', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{vendor}/statement', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'statement'])->name('statement');
            Route::get('/{vendor}/statement/pdf', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'statementPdf'])->name('statement-pdf');
            Route::get('/{vendor}/statement/excel', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'statementExcel'])->name('statement-excel');
        });
    });

    // Future API routes will be added here:
    // Dashboard
    // Support Tickets
    // Invoices
    // Customers
    // POS
    // etc.

});

// Public download routes (token in query)
Route::prefix('crm')->name('crm.')->group(function () {
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/{customer}/statement/pdf', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'statementPdf'])->name('statement-pdf-public');
        Route::get('/{customer}/statement/excel', [\App\Http\Controllers\Api\Tenant\Crm\CustomerController::class, 'statementExcel'])->name('statement-excel-public');
    });
    Route::prefix('vendors')->name('vendors.')->group(function () {
        Route::get('/{vendor}/statement/pdf', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'statementPdf'])->name('statement-pdf-public');
        Route::get('/{vendor}/statement/excel', [\App\Http\Controllers\Api\Tenant\Crm\VendorController::class, 'statementExcel'])->name('statement-excel-public');
    });
});
