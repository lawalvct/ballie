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

    // Future API routes will be added here:
    // Dashboard
    // Support Tickets
    // Invoices
    // Customers
    // POS
    // etc.

});
