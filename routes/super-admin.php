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
        Route::get('/tenants/export', [TenantController::class, 'export'])->name('tenants.export');
        Route::resource('tenants', TenantController::class);
        Route::post('/tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('/tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');

        // Tenant Invitations
        Route::get('/tenants/invite/create', [TenantController::class, 'invite'])->name('tenants.invite');
        Route::post('/tenants/invite/send', [TenantController::class, 'sendInvitation'])->name('tenants.send-invitation');

        // Tenant Impersonation
        Route::post('/impersonate/{tenant}/{user}', [TenantController::class, 'impersonate'])->name('impersonate');
        Route::post('/stop-impersonation', [TenantController::class, 'stopImpersonation'])->name('stop-impersonation');

        // Affiliate Management
        Route::prefix('affiliates')->name('affiliates.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'index'])->name('index');
            Route::get('/export', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'export'])->name('export');
            Route::get('/{affiliate}', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'show'])->name('show');
            Route::get('/{affiliate}/edit', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'edit'])->name('edit');
            Route::put('/{affiliate}', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'update'])->name('update');

            // Approval actions
            Route::post('/{affiliate}/approve', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'approve'])->name('approve');
            Route::post('/{affiliate}/reject', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'reject'])->name('reject');
            Route::post('/{affiliate}/suspend', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'suspend'])->name('suspend');
            Route::post('/{affiliate}/reactivate', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'reactivate'])->name('reactivate');

            // Bulk actions
            Route::post('/bulk/approve', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'bulkApprove'])->name('bulk.approve');
        });

        // Affiliate Commissions
        Route::prefix('affiliate-commissions')->name('affiliate-commissions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'commissions'])->name('index');
            Route::post('/{commission}/approve', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'approveCommission'])->name('approve');
        });

        // Affiliate Payouts
        Route::prefix('affiliate-payouts')->name('affiliate-payouts.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'payouts'])->name('index');
            Route::post('/{payout}/process', [\App\Http\Controllers\SuperAdmin\AffiliateController::class, 'processPayout'])->name('process');
        });

        // Email Management (CyberPanel Integration)
        Route::prefix('emails')->name('emails.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\EmailController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\SuperAdmin\EmailController::class, 'create'])->name('create');
            Route::post('/store', [\App\Http\Controllers\SuperAdmin\EmailController::class, 'store'])->name('store');
            Route::delete('/destroy', [\App\Http\Controllers\SuperAdmin\EmailController::class, 'destroy'])->name('destroy');
            Route::get('/change-password', [\App\Http\Controllers\SuperAdmin\EmailController::class, 'editPassword'])->name('edit-password');
            Route::post('/update-password', [\App\Http\Controllers\SuperAdmin\EmailController::class, 'updatePassword'])->name('update-password');
            Route::get('/generate-password', [\App\Http\Controllers\SuperAdmin\EmailController::class, 'generatePassword'])->name('generate-password');

            // Test route to verify token and API connection
            Route::get('/test-connection', [\App\Http\Controllers\SuperAdmin\EmailController::class, 'testConnection'])->name('test-connection');
        });

        // Backup Management
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'index'])->name('index');
            Route::post('/server', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'createServerBackup'])->name('create-server');
            Route::post('/local', [\App\Http\Controllers\SuperAdmin\BackupController::class, 'createLocalBackup'])->name('create-local');
        });

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
