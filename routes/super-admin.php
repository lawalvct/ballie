<?php

use App\Http\Controllers\SuperAdmin\AuthController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\TenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| Here are the routes for the Super Admin panel. These routes are
| completely separate from tenant routes and handle super admin
| authentication and management features.
|
*/

Route::prefix('super-admin')->name('super-admin.')->group(function () {

    // Guest Super Admin Routes (login, register)
    Route::middleware(['guest:super_admin'])->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
    });

    // Authenticated Super Admin Routes
    Route::middleware(['auth:super_admin'])->group(function () {

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Tenant Management
        Route::resource('tenants', TenantController::class);
        Route::post('/tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('/tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');

        // Tenant Invitations
        Route::get('/tenants/invite/create', [TenantController::class, 'invite'])->name('tenants.invite');
        Route::post('/tenants/invite/send', [TenantController::class, 'sendInvitation'])->name('tenants.send-invitation');

        // Tenant Impersonation
        Route::post('/impersonate/{tenant}/{user}', [TenantController::class, 'impersonate'])->name('impersonate');
        Route::post('/stop-impersonation', [TenantController::class, 'stopImpersonation'])->name('stop-impersonation');

        // Support Center (Future implementation)
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/tickets', function () {
                return view('super-admin.support.tickets');
            })->name('tickets');
            Route::get('/chat', function () {
                return view('super-admin.support.chat');
            })->name('chat');
        });

        // System Management (Future implementation)
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/settings', function () {
                return view('super-admin.system.settings');
            })->name('settings');
            Route::get('/logs', function () {
                return view('super-admin.system.logs');
            })->name('logs');
            Route::get('/maintenance', function () {
                return view('super-admin.system.maintenance');
            })->name('maintenance');
        });

        // Analytics & Reports (Future implementation)
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/overview', function () {
                return view('super-admin.analytics.overview');
            })->name('overview');
            Route::get('/revenue', function () {
                return view('super-admin.analytics.revenue');
            })->name('revenue');
            Route::get('/usage', function () {
                return view('super-admin.analytics.usage');
            })->name('usage');
        });

        // API Routes for AJAX calls
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/dashboard-stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
            Route::get('/tenants/search', [TenantController::class, 'search'])->name('tenants.search');
            Route::get('/system-health', [DashboardController::class, 'systemHealth'])->name('system.health');
        });
    });
});
