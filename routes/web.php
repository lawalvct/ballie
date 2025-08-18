<?php

use App\Http\Controllers\Api\AccountingAssistantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;

// Include authentication routes
require __DIR__.'/auth.php';

// Public routes (landing page, pricing, etc.)
Route::get('/', [HomeController::class, 'welcome'])->name('home');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/demo', [HomeController::class, 'demo'])->name('demo');

Route::get('/demo2', [HomeController::class, 'demo'])->name('profile.edit');

// Social Authentication Routes
use App\Http\Controllers\Auth\SocialAuthController;

Route::middleware('guest')->group(function () {
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('auth.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.callback');

    // Named routes for specific providers
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
});




// General dashboard route that redirects to tenant dashboard
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->tenant) {
        return redirect()->route('tenant.dashboard', ['tenant' => $user->tenant->slug]);
    }
    return redirect()->route('home');
})->name('dashboard');

// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->group(function () {
    // Guest routes
    Route::middleware(['guest:super_admin'])->group(function () {
        Route::get('/login', [SuperAdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [SuperAdminAuthController::class, 'login']);
        Route::get('/register', [SuperAdminAuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [SuperAdminAuthController::class, 'register']);
    });

    // Protected Super Admin Routes
    Route::middleware(['auth:super_admin'])->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [SuperAdminAuthController::class, 'logout'])->name('logout');

        // Tenant Management
        Route::resource('tenants', TenantController::class);
        Route::post('/tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('/tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');

        // Impersonation
        Route::post('/impersonate/{tenant}/{user}', [TenantController::class, 'impersonate'])->name('impersonate');
        Route::post('/stop-impersonation', [TenantController::class, 'stopImpersonation'])->name('stop-impersonation');
    });
});

// Tenant Routes (path-based: /tenant1/dashboard, /tenant2/invoices, etc.)
Route::prefix('{tenant}')->middleware(['tenant'])->group(function () {
    require __DIR__.'/tenant.php';
});


// AI Accounting Assistant API Routes (using web middleware for CSRF protection)
Route::prefix('api')->middleware(['web', 'auth'])->group(function () {

    Route::prefix('ai')->group(function () {

        Route::post('/accounting-suggestions', [AccountingAssistantController::class, 'getSuggestions']);
        Route::post('/real-time-insights', [AccountingAssistantController::class, 'getRealTimeInsights']);
        Route::post('/validate-transaction', [AccountingAssistantController::class, 'validateTransaction']);
        Route::post('/smart-templates', [AccountingAssistantController::class, 'getSmartTemplates']);
        Route::post('/explain-entry', [AccountingAssistantController::class, 'explainEntry']);
        Route::post('/generate-particulars', [AccountingAssistantController::class, 'generateParticulars']);
        Route::post('/suggest-accounts', [AccountingAssistantController::class, 'suggestAccounts']);
        Route::post('/ask-question', [AccountingAssistantController::class, 'askQuestion']);

    });
});
