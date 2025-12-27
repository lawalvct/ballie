<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Tenant\AuthController;

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

    // Future API routes will be added here:
    // Dashboard
    // Support Tickets
    // Invoices
    // Products
    // Customers
    // POS
    // etc.

});
