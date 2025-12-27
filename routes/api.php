<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountingAssistantController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Default Laravel API route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Mobile API Routes - Version 1
|--------------------------------------------------------------------------
|
| Load v1 API routes from separate files for better organization.
| When you need v2, just create routes/api/v2/ directory
|
*/

// Global authentication (no tenant required - auto-detects tenant from email)
Route::prefix('v1/auth')
    ->name('api.v1.auth.')
    ->group(base_path('routes/api/v1/auth.php'));

// Tenant-scoped routes (for backwards compatibility or direct tenant access)
Route::prefix('v1/tenant/{tenant}')
    ->name('api.v1.tenant.')
    ->group(base_path('routes/api/v1/tenant.php'));

// AI Accounting Assistant Routes
Route::prefix('ai')->middleware(['web', 'auth'])->group(function () {

    // Tenant-specific AI routes
    Route::middleware(['tenant'])->group(function () {

        // Main AI suggestions endpoint
        Route::post('/accounting-suggestions', [AccountingAssistantController::class, 'getSuggestions'])
             ->name('api.ai.accounting.suggestions');

        // Real-time insights for live feedback
        Route::post('/real-time-insights', [AccountingAssistantController::class, 'getRealTimeInsights'])
             ->name('api.ai.accounting.insights');

        // Transaction validation
        Route::post('/validate-transaction', [AccountingAssistantController::class, 'validateTransaction'])
             ->name('api.ai.accounting.validate');

        // Smart templates based on context
        Route::post('/smart-templates', [AccountingAssistantController::class, 'getSmartTemplates'])
             ->name('api.ai.accounting.templates');

        // Explain entry functionality
        Route::post('/explain-entry', [AccountingAssistantController::class, 'explainEntry'])
             ->name('api.ai.accounting.explain');

        // Generate particulars suggestions
        Route::post('/generate-particulars', [AccountingAssistantController::class, 'generateParticulars'])
             ->name('api.ai.accounting.particulars');

        // Account matching suggestions
        Route::post('/suggest-accounts', [AccountingAssistantController::class, 'suggestAccounts'])
             ->name('api.ai.accounting.accounts');

    });
});
