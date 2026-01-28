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

        // Quotations
        Route::prefix('quotations')->name('quotations.')->group(function () {
            Route::get('/search-customers', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'searchCustomers'])->name('search-customers');
            Route::get('/search-products', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'searchProducts'])->name('search-products');
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'store'])->name('store');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'index'])->name('index');
            Route::get('/{quotation}', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'show'])->name('show');
            Route::put('/{quotation}', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'update'])->name('update');
            Route::delete('/{quotation}', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'destroy'])->name('destroy');
            Route::post('/{quotation}/send', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'send'])->name('send');
            Route::post('/{quotation}/accept', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'accept'])->name('accept');
            Route::post('/{quotation}/reject', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'reject'])->name('reject');
            Route::post('/{quotation}/convert', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'convert'])->name('convert');
            Route::post('/{quotation}/duplicate', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'duplicate'])->name('duplicate');
            Route::get('/{quotation}/pdf', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'pdf'])->name('pdf');
            Route::post('/{quotation}/email', [\App\Http\Controllers\Api\Tenant\Accounting\QuotationController::class, 'email'])->name('email');
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

    // Banking Module
    Route::prefix('banking')->name('banking.')->group(function () {
        Route::prefix('banks')->name('banks.')->group(function () {
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Banking\BankController::class, 'create'])->name('create');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Banking\BankController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Banking\BankController::class, 'store'])->name('store');
            Route::get('/{bank}', [\App\Http\Controllers\Api\Tenant\Banking\BankController::class, 'show'])->name('show');
            Route::put('/{bank}', [\App\Http\Controllers\Api\Tenant\Banking\BankController::class, 'update'])->name('update');
            Route::delete('/{bank}', [\App\Http\Controllers\Api\Tenant\Banking\BankController::class, 'destroy'])->name('destroy');
            Route::get('/{bank}/statement', [\App\Http\Controllers\Api\Tenant\Banking\BankController::class, 'statement'])->name('statement');
        });
        Route::prefix('reconciliations')->name('reconciliations.')->group(function () {
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Banking\BankReconciliationController::class, 'create'])->name('create');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Banking\BankReconciliationController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Banking\BankReconciliationController::class, 'store'])->name('store');
            Route::get('/{reconciliation}', [\App\Http\Controllers\Api\Tenant\Banking\BankReconciliationController::class, 'show'])->name('show');
            Route::post('/{reconciliation}/items/status', [\App\Http\Controllers\Api\Tenant\Banking\BankReconciliationController::class, 'updateItemStatus'])->name('items.status');
            Route::post('/{reconciliation}/complete', [\App\Http\Controllers\Api\Tenant\Banking\BankReconciliationController::class, 'complete'])->name('complete');
            Route::post('/{reconciliation}/cancel', [\App\Http\Controllers\Api\Tenant\Banking\BankReconciliationController::class, 'cancel'])->name('cancel');
            Route::delete('/{reconciliation}', [\App\Http\Controllers\Api\Tenant\Banking\BankReconciliationController::class, 'destroy'])->name('destroy');
        });
    });

    // Inventory Module
    Route::prefix('inventory')->name('inventory.')->group(function () {

        // Stock Journal
        Route::prefix('stock-journal')->name('stock-journal.')->group(function () {
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'create'])->name('create');
            Route::get('/product-stock/{product}', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'productStock'])->name('product-stock');
            Route::post('/calculate-stock', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'calculateStock'])->name('calculate-stock');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'store'])->name('store');
            Route::get('/{stockJournal}', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'show'])->name('show');
            Route::put('/{stockJournal}', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'update'])->name('update');
            Route::delete('/{stockJournal}', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'destroy'])->name('destroy');
            Route::post('/{stockJournal}/post', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'post'])->name('post');
            Route::post('/{stockJournal}/cancel', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'cancel'])->name('cancel');
            Route::get('/{stockJournal}/duplicate', [\App\Http\Controllers\Api\Tenant\Inventory\StockJournalController::class, 'duplicate'])->name('duplicate');
        });

        // Categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'create'])->name('create');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'store'])->name('store');
            Route::post('/quick-store', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'quickStore'])->name('quick-store');
            Route::post('/reorder', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'reorder'])->name('reorder');
            Route::get('/{category}', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'show'])->name('show');
            Route::put('/{category}', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'update'])->name('update');
            Route::patch('/{category}/toggle-status', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{category}', [\App\Http\Controllers\Api\Tenant\Inventory\ProductCategoryController::class, 'destroy'])->name('destroy');
        });

        // Units
        Route::prefix('units')->name('units.')->group(function () {
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Inventory\UnitController::class, 'create'])->name('create');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Inventory\UnitController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Inventory\UnitController::class, 'store'])->name('store');
            Route::get('/{unit}', [\App\Http\Controllers\Api\Tenant\Inventory\UnitController::class, 'show'])->name('show');
            Route::put('/{unit}', [\App\Http\Controllers\Api\Tenant\Inventory\UnitController::class, 'update'])->name('update');
            Route::patch('/{unit}/toggle-status', [\App\Http\Controllers\Api\Tenant\Inventory\UnitController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{unit}', [\App\Http\Controllers\Api\Tenant\Inventory\UnitController::class, 'destroy'])->name('destroy');
        });

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

    // Procurement Module
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
            Route::get('/search-vendors', [\App\Http\Controllers\Api\Tenant\Procurement\PurchaseOrderController::class, 'searchVendors'])->name('search-vendors');
            Route::get('/search-products', [\App\Http\Controllers\Api\Tenant\Procurement\PurchaseOrderController::class, 'searchProducts'])->name('search-products');
            Route::get('/create', [\App\Http\Controllers\Api\Tenant\Procurement\PurchaseOrderController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Procurement\PurchaseOrderController::class, 'store'])->name('store');
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Procurement\PurchaseOrderController::class, 'index'])->name('index');
            Route::get('/{purchaseOrder}', [\App\Http\Controllers\Api\Tenant\Procurement\PurchaseOrderController::class, 'show'])->name('show');
            Route::get('/{purchaseOrder}/pdf', [\App\Http\Controllers\Api\Tenant\Procurement\PurchaseOrderController::class, 'pdf'])->name('pdf');
            Route::post('/{purchaseOrder}/email', [\App\Http\Controllers\Api\Tenant\Procurement\PurchaseOrderController::class, 'email'])->name('email');
        });
    });

    // Payroll Module
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\DepartmentController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Payroll\DepartmentController::class, 'store'])->name('store');
            Route::get('/{department}', [\App\Http\Controllers\Api\Tenant\Payroll\DepartmentController::class, 'show'])->name('show');
            Route::put('/{department}', [\App\Http\Controllers\Api\Tenant\Payroll\DepartmentController::class, 'update'])->name('update');
            Route::delete('/{department}', [\App\Http\Controllers\Api\Tenant\Payroll\DepartmentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('salary-components')->name('salary-components.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\SalaryComponentController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Payroll\SalaryComponentController::class, 'store'])->name('store');
            Route::get('/{component}', [\App\Http\Controllers\Api\Tenant\Payroll\SalaryComponentController::class, 'show'])->name('show');
            Route::put('/{component}', [\App\Http\Controllers\Api\Tenant\Payroll\SalaryComponentController::class, 'update'])->name('update');
            Route::delete('/{component}', [\App\Http\Controllers\Api\Tenant\Payroll\SalaryComponentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('positions')->name('positions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\PositionController::class, 'index'])->name('index');
            Route::get('/by-department', [\App\Http\Controllers\Api\Tenant\Payroll\PositionController::class, 'byDepartment'])->name('by-department');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Payroll\PositionController::class, 'store'])->name('store');
            Route::get('/{position}', [\App\Http\Controllers\Api\Tenant\Payroll\PositionController::class, 'show'])->name('show');
            Route::put('/{position}', [\App\Http\Controllers\Api\Tenant\Payroll\PositionController::class, 'update'])->name('update');
            Route::delete('/{position}', [\App\Http\Controllers\Api\Tenant\Payroll\PositionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\EmployeeController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Payroll\EmployeeController::class, 'store'])->name('store');
            Route::get('/{employee}', [\App\Http\Controllers\Api\Tenant\Payroll\EmployeeController::class, 'show'])->name('show');
            Route::put('/{employee}', [\App\Http\Controllers\Api\Tenant\Payroll\EmployeeController::class, 'update'])->name('update');
            Route::delete('/{employee}', [\App\Http\Controllers\Api\Tenant\Payroll\EmployeeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('shifts')->name('shifts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'store'])->name('store');
            Route::get('/{shift}', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'show'])->name('show');
            Route::put('/{shift}', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'update'])->name('update');
            Route::delete('/{shift}', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'destroy'])->name('destroy');

            Route::get('/assignments', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'assignments'])->name('assignments');
            Route::post('/assignments', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'storeAssignment'])->name('assignments.store');
            Route::post('/assignments/bulk', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'bulkAssign'])->name('assignments.bulk');
            Route::patch('/assignments/{assignment}/end', [\App\Http\Controllers\Api\Tenant\Payroll\ShiftController::class, 'endAssignment'])->name('assignments.end');
        });

        Route::prefix('processing')->name('processing.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'store'])->name('store');
            Route::get('/{period}', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'show'])->name('show');
            Route::put('/{period}', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'update'])->name('update');
            Route::delete('/{period}', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'destroy'])->name('destroy');

            Route::post('/{period}/generate', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'generate'])->name('generate');
            Route::post('/{period}/approve', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'approve'])->name('approve');
            Route::delete('/{period}/reset', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'reset'])->name('reset');
            Route::get('/{period}/export-bank-file', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollProcessingController::class, 'exportBankFile'])->name('export-bank-file');
        });

        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'index'])->name('index');
            Route::post('/clock-in', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'clockIn'])->name('clock-in');
            Route::post('/clock-out', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'clockOut'])->name('clock-out');
            Route::post('/scan-qr', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'scanQr'])->name('scan-qr');
            Route::post('/mark-absent', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'markAbsent'])->name('mark-absent');
            Route::post('/mark-leave', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'markLeave'])->name('mark-leave');
            Route::post('/manual-entry', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'manualEntry'])->name('manual-entry');

            Route::put('/{attendance}', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'update'])->name('update');
            Route::post('/{attendance}/half-day', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'markHalfDay'])->name('half-day');
            Route::post('/{attendance}/approve', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'approve'])->name('approve');
            Route::post('/bulk-approve', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'bulkApprove'])->name('bulk-approve');

            Route::get('/monthly-report', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'monthlyReport'])->name('monthly-report');
            Route::get('/employee/{employee}', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'employeeAttendance'])->name('employee');
            Route::get('/qr-code', [\App\Http\Controllers\Api\Tenant\Payroll\AttendanceController::class, 'generateQr'])->name('qr-code');
        });

        Route::prefix('overtime')->name('overtime.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'store'])->name('store');
            Route::get('/report/monthly', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'report'])->name('report');
            Route::get('/{overtime}', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'show'])->name('show');
            Route::get('/{overtime}/payment-slip', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'downloadPaymentSlip'])->name('payment-slip');
            Route::put('/{overtime}', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'update'])->name('update');
            Route::delete('/{overtime}', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'destroy'])->name('destroy');

            Route::post('/{overtime}/approve', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'approve'])->name('approve');
            Route::post('/{overtime}/reject', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'reject'])->name('reject');
            Route::post('/{overtime}/mark-paid', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'markPaid'])->name('mark-paid');
            Route::post('/bulk-approve', [\App\Http\Controllers\Api\Tenant\Payroll\OvertimeController::class, 'bulkApprove'])->name('bulk-approve');
        });

        Route::prefix('loans')->name('loans.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\LoanController::class, 'index'])->name('index');
            Route::get('/{loan}', [\App\Http\Controllers\Api\Tenant\Payroll\LoanController::class, 'show'])->name('show');
        });

        Route::post('/salary-advance', [\App\Http\Controllers\Api\Tenant\Payroll\LoanController::class, 'storeSalaryAdvance'])->name('salary-advance.store');

        Route::prefix('announcements')->name('announcements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\AnnouncementController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Api\Tenant\Payroll\AnnouncementController::class, 'store'])->name('store');
            Route::get('/{announcement}', [\App\Http\Controllers\Api\Tenant\Payroll\AnnouncementController::class, 'show'])->name('show');
            Route::put('/{announcement}', [\App\Http\Controllers\Api\Tenant\Payroll\AnnouncementController::class, 'update'])->name('update');
            Route::delete('/{announcement}', [\App\Http\Controllers\Api\Tenant\Payroll\AnnouncementController::class, 'destroy'])->name('destroy');
            Route::post('/{announcement}/send', [\App\Http\Controllers\Api\Tenant\Payroll\AnnouncementController::class, 'send'])->name('send');
            Route::post('/preview-recipients', [\App\Http\Controllers\Api\Tenant\Payroll\AnnouncementController::class, 'previewRecipients'])->name('preview-recipients');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollSettingsController::class, 'show'])->name('show');
            Route::put('/', [\App\Http\Controllers\Api\Tenant\Payroll\PayrollSettingsController::class, 'update'])->name('update');
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

Route::prefix('accounting')->name('accounting.')->group(function () {
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        Route::get('/{voucher}/pdf', [\App\Http\Controllers\Api\Tenant\Accounting\VoucherController::class, 'pdf'])->name('pdf-public');
    });
});
