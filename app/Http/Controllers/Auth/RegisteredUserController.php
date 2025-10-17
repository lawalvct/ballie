<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Helpers\TenantHelper;
use App\Models\Plan;
use App\Notifications\WelcomeNotification;
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

        $businessTypes = \App\Models\BusinessType::getGroupedByCategory();

        return view('auth.register', compact('plans', 'businessTypes'));
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
            'business_structure' => ['nullable', 'string'],
            'business_type_id' => ['required', 'integer', 'exists:business_types,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
            'terms' => ['required', 'accepted'],
        ]);

        Log::info('Validation passed', ['business_name' => $request->business_name]);

        try {
            $tenant = null;
            $user = null;

            DB::transaction(function () use ($request, &$tenant, &$user) {
                Log::info('Starting database transaction');

                // Get the selected plan
                $selectedPlan = Plan::findOrFail($request->plan_id);
                Log::info('Selected plan', ['plan_id' => $selectedPlan->id, 'plan_name' => $selectedPlan->name]);

                // Create tenant with the selected plan
                $tenantData = [
                    'name' => $request->business_name,
                    'slug' => TenantHelper::generateUniqueSlug($request->business_name),
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'business_structure' => $request->business_structure,
                    'business_type_id' => $request->business_type_id,
                    'plan_id' => $selectedPlan->id,
                    'trial_ends_at' => now()->addDays(30), // 30-day trial
                    'is_active' => true,
                    'onboarding_completed' => false,
                ];

                Log::info('Creating tenant with data', $tenantData);

                $tenant = Tenant::create($tenantData);

                Log::info('Tenant created successfully', ['tenant_id' => $tenant->id, 'slug' => $tenant->slug]);

                // Start trial for the selected plan using the tenant method
                $tenant->startTrial($selectedPlan);

                Log::info('Trial started successfully', ['plan' => $selectedPlan->name, 'trial_ends_at' => $tenant->trial_ends_at]);

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

                // Generate 4-digit verification code
                $code = sprintf('%04d', random_int(0, 9999));

                // Store verification code
                DB::table('email_verification_codes')->insert([
                    'user_id' => $user->id,
                    'code' => $code,
                    'expires_at' => now()->addMinutes(60),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('Verification code generated', ['user_id' => $user->id]);

                // Send welcome email with verification code (inside transaction)
                // If this fails, the entire transaction will be rolled back
                try {
                    $user->notify(new WelcomeNotification($code));
                    Log::info('Welcome email sent successfully', ['user_id' => $user->id]);
                } catch (\Exception $emailError) {
                    Log::error('Email sending failed', [
                        'user_id' => $user->id,
                        'error' => $emailError->getMessage()
                    ]);

                    // Throw the exception to trigger transaction rollback
                    throw new \Exception('Failed to send verification email. Please check your internet connection and try again.');
                }

                // Log user in (but they'll need to verify email)
                Auth::login($user);

                Log::info('User logged in successfully');
            });

            Log::info('Registration completed successfully');

            // Redirect to verification notice page
            return redirect()->route('verification.notice')
                ->with('success', 'Registration successful! Please check your email for a verification code.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email,
                'business_name' => $request->business_name
            ]);

            // Check if it's an email-related error
            $errorMessage = 'Registration failed. Please try again.';

            if (str_contains($e->getMessage(), 'verification email') ||
                str_contains($e->getMessage(), 'Connection could not be established') ||
                str_contains($e->getMessage(), 'smtp') ||
                str_contains($e->getMessage(), 'mailtrap')) {
                $errorMessage = 'Unable to send verification email. Please check your internet connection and try again. If the problem persists, contact support.';
            }

            return back()->withInput($request->except('password', 'password_confirmation'))->withErrors([
                'email' => $errorMessage
            ]);
        }
    }
}
