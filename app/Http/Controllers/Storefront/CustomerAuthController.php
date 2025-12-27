<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Customer;
use App\Models\CustomerAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class CustomerAuthController extends Controller
{
    /**
     * Show customer login page
     */
    public function showLogin(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        if (!$storeSettings || !$storeSettings->is_store_enabled) {
            abort(404, 'Store not available');
        }

        return view('storefront.auth.login', compact('tenant', 'storeSettings'));
    }

    /**
     * Show customer registration page
     */
    public function showRegister(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        if (!$storeSettings || !$storeSettings->is_store_enabled || !$storeSettings->allow_email_registration) {
            abort(404, 'Registration not available');
        }

        return view('storefront.auth.register', compact('tenant', 'storeSettings'));
    }

    /**
     * Handle customer login
     */
    public function login(Request $request)
    {
        $tenant = $request->current_tenant;

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find customer authentication
        $customerAuth = CustomerAuthentication::where('email', $credentials['email'])
            ->whereHas('customer', function ($query) use ($tenant) {
                $query->where('tenant_id', $tenant->id);
            })
            ->first();

        if (!$customerAuth || !Hash::check($credentials['password'], $customerAuth->password)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        Auth::guard('customer')->login($customerAuth, $request->filled('remember'));

        return redirect()->intended(route('storefront.index', ['tenant' => $tenant->slug]))
            ->with('success', 'Welcome back!');
    }

    /**
     * Handle customer registration
     */
    public function register(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        if (!$storeSettings->allow_email_registration) {
            return back()->with('error', 'Registration is currently disabled');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer_authentications,email',
            'phone' => $storeSettings->require_phone_number ? 'required|string|max:20' : 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            \DB::beginTransaction();

            // Create customer in CRM
            $customer = Customer::create([
                'tenant_id' => $tenant->id,
                'customer_type' => 'individual',
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'has_online_account' => true,
                'registration_source' => 'storefront',
                'status' => 'active',
            ]);

            // Ensure ledger account is created
            $customer->refresh();
            if (!$customer->ledgerAccount) {
                $customer->createLedgerAccount();
                $customer->refresh();
            }

            // Create authentication record
            $customerAuth = CustomerAuthentication::create([
                'customer_id' => $customer->id,
                'email' => $validated['email'],
                'password' => $validated['password'], // Model's setPasswordAttribute will hash it
                'is_social_login' => false,
            ]);

            \DB::commit();

            Auth::guard('customer')->login($customerAuth);

            return redirect()->route('storefront.index', ['tenant' => $tenant->slug])
                ->with('success', 'Account created successfully! Welcome to our store.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Customer registration failed: ' . $e->getMessage());
            return back()->with('error', 'Registration failed. Please try again.')->withInput();
        }
    }

    /**
     * Handle customer logout
     */
    public function logout(Request $request)
    {
        $tenant = $request->current_tenant;

        Auth::guard('customer')->logout();

        return redirect()->route('storefront.index', ['tenant' => $tenant->slug])
            ->with('success', 'Logged out successfully');
    }

    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        if (!$storeSettings->allow_google_login) {
            return back()->with('error', 'Google login is not enabled');
        }

        // Store tenant slug in session for callback
        session(['oauth_tenant_slug' => $tenant->slug]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google authentication callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $tenantSlug = session('oauth_tenant_slug');
            if (!$tenantSlug) {
                return redirect()->route('home')->with('error', 'Session expired. Please try again.');
            }

            $tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();
            $googleUser = Socialite::driver('google')->user();

            \DB::beginTransaction();

            // Find or create customer authentication
            $customerAuth = CustomerAuthentication::where('email', $googleUser->email)
                ->whereHas('customer', function ($query) use ($tenant) {
                    $query->where('tenant_id', $tenant->id);
                })
                ->first();

            if ($customerAuth) {
                // Update Google info
                $customerAuth->update([
                    'google_id' => $googleUser->id,
                    'is_social_login' => true,
                ]);
            } else {
                // Create new customer
                $customer = Customer::create([
                    'tenant_id' => $tenant->id,
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'has_online_account' => true,
                    'registration_source' => 'google',
                ]);

                $customerAuth = CustomerAuthentication::create([
                    'customer_id' => $customer->id,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'is_social_login' => true,
                    'email_verified_at' => now(),
                ]);
            }

            \DB::commit();

            Auth::guard('customer')->login($customerAuth);

            session()->forget('oauth_tenant_slug');

            return redirect()->route('storefront.index', ['tenant' => $tenant->slug])
                ->with('success', 'Successfully logged in with Google!');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Google OAuth failed: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Login failed. Please try again.');
        }
    }

    /**
     * Show customer account page
     */
    public function account(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;
        $customer = Auth::guard('customer')->user()->customer;

        return view('storefront.account.index', compact('tenant', 'storeSettings', 'customer'));
    }

    /**
     * Show edit account page
     */
    public function editAccount(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;
        $customer = Auth::guard('customer')->user()->customer;

        return view('storefront.account.edit', compact('tenant', 'storeSettings', 'customer'));
    }

    /**
     * Update customer account information
     */
    public function updateAccount(Request $request)
    {
        $tenant = $request->current_tenant;
        $customer = Auth::guard('customer')->user()->customer;

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            'email' => 'required|email|unique:customer_authentications,email,' . Auth::guard('customer')->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        // Update customer
        $customer->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Update email in authentication table
        Auth::guard('customer')->user()->update([
            'email' => $validated['email'],
        ]);

        return redirect()->route('storefront.account', ['tenant' => $tenant->slug])
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update customer password
     */
    public function updatePassword(Request $request)
    {
        $tenant = $request->current_tenant;

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customerAuth = Auth::guard('customer')->user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $customerAuth->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password (model mutator will hash it)
        $customerAuth->update([
            'password' => $validated['password'],
        ]);

        return redirect()->route('storefront.account', ['tenant' => $tenant->slug])
            ->with('success', 'Password updated successfully!');
    }
}
