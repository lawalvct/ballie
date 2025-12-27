<?php

use App\Http\Controllers\Api\AccountingAssistantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
    use App\Http\Controllers\Storefront\StorefrontController;
    use App\Http\Controllers\Storefront\CartController;
    use App\Http\Controllers\Storefront\CheckoutController;
    use App\Http\Controllers\Storefront\CustomerAuthController;

// Firebase Service Worker - Return 404 to prevent auth redirection
Route::get('/firebase-messaging-sw.js', function () {
    abort(404);
});

// Debugbar Test Route (Development Only)
Route::get('/debugbar-test', function () {
    // Send test messages to debugbar
    \Debugbar::info('Debugbar test page loaded successfully! 🎉');
    \Debugbar::warning('Testing VSCode integration - click file paths to open in VSCode');
    \Debugbar::error('This is a test error message (not a real error!)');

    // Add some context data
    \Debugbar::info('Current Environment', [
        'app_env' => config('app.env'),
        'debug_enabled' => config('app.debug'),
        'debugbar_enabled' => config('debugbar.enabled'),
        'editor' => config('debugbar.editor'),
    ]);

    // Measure some fake operation
    \Debugbar::startMeasure('test_operation', 'Testing Performance Measurement');
    usleep(100000); // Sleep 100ms
    \Debugbar::stopMeasure('test_operation');

    // Execute a simple database query to show in queries tab
    \DB::table('users')->count();

    return view('debugbar-test');
})->name('debugbar.test');

// Include authentication routes
require __DIR__.'/auth.php';

// Public routes (landing page, pricing, etc.)
Route::get('/', [HomeController::class, 'welcome'])->name('home');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/demo', [HomeController::class, 'demo'])->name('demo');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/cookies', [HomeController::class, 'cookies'])->name('cookies');

Route::get('/demo2', [HomeController::class, 'demo'])->name('profile.edit');

// Affiliate Program Routes
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\Affiliate\AffiliateVerificationController;

Route::prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/', [AffiliateController::class, 'index'])->name('index');
    Route::get('/register', [AffiliateController::class, 'register'])->name('register');
    Route::post('/register', [AffiliateController::class, 'store'])->name('store');

    // Email verification routes (require auth but not email verification)
    Route::middleware('auth')->group(function () {
        Route::get('/verify-email', [AffiliateVerificationController::class, 'notice'])->name('verification.notice');
        Route::post('/verify-email', [AffiliateVerificationController::class, 'verify'])->name('verification.verify')->middleware('throttle:6,1');
        Route::post('/verification-code/resend', [AffiliateVerificationController::class, 'resend'])->name('verification.resend')->middleware('throttle:6,1');
    });

    // Protected affiliate routes (require email verification)
    Route::middleware(['auth', 'affiliate.verified'])->group(function () {
        Route::get('/dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
        Route::get('/referrals', [AffiliateController::class, 'referrals'])->name('referrals');
        Route::get('/commissions', [AffiliateController::class, 'commissions'])->name('commissions');
        Route::get('/payouts', [AffiliateController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/request', [AffiliateController::class, 'requestPayout'])->name('payouts.request');
        Route::get('/settings', [AffiliateController::class, 'settings'])->name('settings');
        Route::post('/settings', [AffiliateController::class, 'updateSettings'])->name('settings.update');
    });
});

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

// Super Admin Routes - Include separate route file
require __DIR__.'/super-admin.php';

// Public payment callback for invoices - NO AUTH, NO TENANT PARAMETER
// Customer payment callback - creates receipt and shows success page
// Accept both GET and POST (Paystack redirects GET, some gateways webhook POST)
// Only uses voucher/invoice ID - tenant is retrieved from voucher relationship
Route::match(['get', 'post'], '/invoice/payment-callback/{invoice}', [App\Http\Controllers\PublicPaymentCallbackController::class, 'handleCallback'])
    ->middleware(['web'])
    ->name('invoice.payment.callback');


// Tenant Routes (path-based: /tenant1/dashboard, /tenant2/invoices, etc.)
Route::prefix('{tenant}')->middleware(['tenant'])->group(function () {
    require __DIR__.'/tenant.php';
});

// Storefront Routes (Public E-commerce - path-based: /tenant1/store/*, /tenant2/store/*)
Route::prefix('{tenant}/store')->middleware(['tenant'])->group(function () {


    // Public storefront pages
    Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');
    Route::get('/products', [StorefrontController::class, 'products'])->name('storefront.products');
    Route::get('/products/{slug}', [StorefrontController::class, 'show'])->name('storefront.product.show');

    // Cart management
    Route::get('/cart', [CartController::class, 'index'])->name('storefront.cart');
    Route::get('/cart/count', [CartController::class, 'count'])->name('storefront.cart.count');
    Route::post('/cart/add', [CartController::class, 'add'])->name('storefront.cart.add');
    Route::patch('/cart/update/{item}', [CartController::class, 'update'])->name('storefront.cart.update');
    Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('storefront.cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('storefront.cart.clear');

    // Customer authentication
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('storefront.login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])->name('storefront.register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('storefront.logout');

    // Google OAuth
    Route::get('/auth/google', [CustomerAuthController::class, 'redirectToGoogle'])->name('storefront.auth.google');
    Route::get('/auth/google/callback', [CustomerAuthController::class, 'handleGoogleCallback'])->name('storefront.auth.google.callback');

    // Order success (public - no auth required)
    Route::get('/order/success/{order}', [CheckoutController::class, 'success'])->name('storefront.order.success');

    // Payment callback (public - Nomba redirects here after payment)
    Route::get('/payment/callback/{order}', [CheckoutController::class, 'paymentCallback'])->name('storefront.payment.callback');

    // Checkout (requires authentication)
    Route::middleware('auth:customer')->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('storefront.checkout');
        Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('storefront.checkout.apply-coupon');
        Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('storefront.checkout.process');

        // Payment retry for failed orders
        Route::post('/orders/{order}/retry-payment', [CheckoutController::class, 'retryPayment'])->name('storefront.order.retry-payment');

        // Customer Account Management
        Route::get('/account', [CustomerAuthController::class, 'account'])->name('storefront.account');
        Route::get('/account/edit', [CustomerAuthController::class, 'editAccount'])->name('storefront.account.edit');
        Route::put('/account/update', [CustomerAuthController::class, 'updateAccount'])->name('storefront.account.update');
        Route::put('/account/password', [CustomerAuthController::class, 'updatePassword'])->name('storefront.account.password');

        // Order Management
        Route::get('/orders', [CheckoutController::class, 'orders'])->name('storefront.orders');
        Route::get('/orders/{order}', [CheckoutController::class, 'orderDetail'])->name('storefront.order.detail');
        Route::get('/orders/{order}/invoice', [CheckoutController::class, 'downloadInvoice'])->name('storefront.order.invoice');
        Route::post('/orders/{order}/dispute', [CheckoutController::class, 'submitDispute'])->name('storefront.order.dispute');
    });
});


// Tenant Invitation Routes (public)
Route::get('/accept-invitation/{token}', [App\Http\Controllers\InvitationController::class, 'show'])
    ->name('invitation.show');
Route::post('/accept-invitation/{token}', [App\Http\Controllers\InvitationController::class, 'accept'])
    ->name('invitation.accept');

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
