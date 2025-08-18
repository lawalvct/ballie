<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Helpers\TenantHelper;
use App\Models\Plan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $plans = Plan::where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('monthly_price')
                    ->get();

        return view('auth.register', compact('plans'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('Registration attempt started', ['email' => $request->email]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'business_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'in:retail,service,restaurant,manufacturing,wholesale,other'],
            'phone' => ['nullable', 'string', 'max:20'],
            'plan' => ['nullable', 'string', 'in:starter,professional,enterprise'],
            'terms' => ['required', 'accepted'],
        ]);

        Log::info('Validation passed', ['business_name' => $request->business_name]);

        try {
            $tenant = null;
            $user = null;

            DB::transaction(function () use ($request, &$tenant, &$user) {
                Log::info('Starting database transaction');

                // Create tenant first
                $tenantData = [
                    'name' => $request->business_name,
                    'slug' => TenantHelper::generateUniqueSlug($request->business_name),
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'business_type' => $request->business_type,
                    'subscription_plan' => $request->plan ?? Tenant::PLAN_STARTER,
                    'subscription_status' => Tenant::STATUS_TRIAL,
                    'billing_cycle' => Tenant::BILLING_MONTHLY,
                    'trial_ends_at' => now()->addDays(30), // 30-day trial
                    'is_active' => true,
                    'onboarding_completed' => false,
                ];

                Log::info('Creating tenant with data', $tenantData);

                $tenant = Tenant::create($tenantData);

                Log::info('Tenant created successfully', ['tenant_id' => $tenant->id, 'slug' => $tenant->slug]);

                // Create user associated with the tenant
                $userData = [
                    'tenant_id' => $tenant->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'role' => User::ROLE_OWNER, // First user is the owner
                    'is_active' => true,
                ];

                Log::info('Creating user with data', array_merge($userData, ['password' => '[HIDDEN]']));

                $user = User::create($userData);

                Log::info('User created successfully', ['user_id' => $user->id]);

                event(new Registered($user));

                Auth::login($user);

                Log::info('User logged in successfully');
            });

            Log::info('Registration completed successfully');

            // Redirect to tenant-specific dashboard
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug])
                ->with('success', 'Registration successful! Welcome to Ballie.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email,
                'business_name' => $request->business_name
            ]);

            return back()->withInput()->withErrors([
                'registration' => 'Registration failed. Please try again. Error: ' . $e->getMessage()
            ]);
        }
    }
}
