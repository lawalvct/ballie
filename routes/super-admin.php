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

        // Support Center
        Route::prefix('support')->name('support.')->group(function () {
            // Dashboard & Tickets
            Route::get('/', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'index'])->name('index');
            Route::get('/tickets/{ticket}', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'show'])->name('tickets.show');
            Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'reply'])->name('tickets.reply');
            Route::post('/tickets/{ticket}/internal-note', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'internalNote'])->name('tickets.internal-note');
            Route::patch('/tickets/{ticket}/status', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'updateStatus'])->name('tickets.update-status');
            Route::patch('/tickets/{ticket}/priority', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'updatePriority'])->name('tickets.update-priority');
            Route::patch('/tickets/{ticket}/assign', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'assign'])->name('tickets.assign');
            Route::delete('/tickets/{ticket}', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'destroy'])->name('tickets.destroy');

            // Bulk Actions
            Route::post('/tickets/bulk/status', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'bulkUpdateStatus'])->name('tickets.bulk-status');
            Route::post('/tickets/bulk/assign', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'bulkAssign'])->name('tickets.bulk-assign');
            Route::delete('/tickets/bulk/delete', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'bulkDelete'])->name('tickets.bulk-delete');

            // Response Templates
            Route::resource('templates', \App\Http\Controllers\SuperAdmin\ResponseTemplateController::class);

            // Categories
            Route::resource('categories', \App\Http\Controllers\SuperAdmin\SupportCategoryController::class);
            Route::post('/categories/reorder', [\App\Http\Controllers\SuperAdmin\SupportCategoryController::class, 'reorder'])->name('categories.reorder');

            // Knowledge Base Management
            Route::prefix('kb')->name('kb.')->group(function () {
                Route::get('/', [\App\Http\Controllers\SuperAdmin\KnowledgeBaseController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\SuperAdmin\KnowledgeBaseController::class, 'create'])->name('create');
                Route::post('/', [\App\Http\Controllers\SuperAdmin\KnowledgeBaseController::class, 'store'])->name('store');
                Route::get('/{article}/edit', [\App\Http\Controllers\SuperAdmin\KnowledgeBaseController::class, 'edit'])->name('edit');
                Route::put('/{article}', [\App\Http\Controllers\SuperAdmin\KnowledgeBaseController::class, 'update'])->name('update');
                Route::delete('/{article}', [\App\Http\Controllers\SuperAdmin\KnowledgeBaseController::class, 'destroy'])->name('destroy');
                Route::post('/{article}/publish', [\App\Http\Controllers\SuperAdmin\KnowledgeBaseController::class, 'publish'])->name('publish');
                Route::post('/{article}/unpublish', [\App\Http\Controllers\SuperAdmin\KnowledgeBaseController::class, 'unpublish'])->name('unpublish');
            });

            // Reports & Analytics
            Route::get('/reports', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'reports'])->name('reports');
            Route::get('/reports/export', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'exportReport'])->name('reports.export');

            // Settings
            Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'settings'])->name('settings');
            Route::put('/settings', [\App\Http\Controllers\SuperAdmin\SupportController::class, 'updateSettings'])->name('settings.update');
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

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\NotificationController::class, 'index'])->name('index');
            Route::get('/unread-count', [\App\Http\Controllers\SuperAdmin\NotificationController::class, 'getUnreadCount'])->name('unread-count');
            Route::post('/{id}/mark-read', [\App\Http\Controllers\SuperAdmin\NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::post('/mark-all-read', [\App\Http\Controllers\SuperAdmin\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [\App\Http\Controllers\SuperAdmin\NotificationController::class, 'destroy'])->name('destroy');
        });
    });
});
