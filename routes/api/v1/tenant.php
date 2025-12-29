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

    });

    // Future API routes will be added here:
    // Dashboard
    // Support Tickets
    // Invoices
    // Products
    // Customers
    // POS
    // etc.

});
