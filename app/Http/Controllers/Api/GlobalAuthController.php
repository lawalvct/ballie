<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class GlobalAuthController extends BaseApiController
{
    /**
     * Login - Auto-detect tenant from email
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Find user by email (across all tenants)
        $users = User::where('email', $request->email)->get();

        if ($users->isEmpty()) {
            return $this->unauthorized('Invalid credentials');
        }

        // If user belongs to multiple tenants, return tenant list for selection
        if ($users->count() > 1) {
            $tenants = $users->map(function ($user) {
                return [
                    'tenant_id' => $user->tenant_id,
                    'tenant_slug' => $user->tenant->slug,
                    'tenant_name' => $user->tenant->name,
                    'user_role' => $user->role,
                ];
            });

            return $this->success([
                'multiple_tenants' => true,
                'email' => $request->email,
                'tenants' => $tenants,
                'message' => 'Please select your workspace',
            ], 'Multiple workspaces found');
        }

        // Single tenant - proceed with login
        $user = $users->first();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return $this->unauthorized('Invalid credentials');
        }

        // Check if user is active
        if (!$user->is_active) {
            return $this->forbidden('Your account has been deactivated. Please contact support.');
        }

        // Set tenant context
        $user->tenant->makeCurrent();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Generate token
        $deviceName = $request->device_name ?? 'Mobile App';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'tenant' => [
                'id' => $user->tenant->id,
                'slug' => $user->tenant->slug,
                'name' => $user->tenant->name,
            ],
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Select tenant when user belongs to multiple tenants
     */
    public function selectTenant(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
            'tenant_id' => 'required|integer',
            'device_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Find user for specific tenant
        $user = User::where('email', $request->email)
            ->where('tenant_id', $request->tenant_id)
            ->first();

        if (!$user) {
            return $this->unauthorized('Invalid credentials or tenant');
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return $this->unauthorized('Invalid credentials');
        }

        // Check if user is active
        if (!$user->is_active) {
            return $this->forbidden('Your account has been deactivated. Please contact support.');
        }

        // Set tenant context
        $user->tenant->makeCurrent();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Generate token
        $deviceName = $request->device_name ?? 'Mobile App';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'tenant' => [
                'id' => $user->tenant->id,
                'slug' => $user->tenant->slug,
                'name' => $user->tenant->name,
            ],
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    /**
     * Check which tenant(s) an email belongs to
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $users = User::where('email', $request->email)->get();

        if ($users->isEmpty()) {
            return $this->notFound('No account found with this email');
        }

        $tenants = $users->map(function ($user) {
            return [
                'tenant_id' => $user->tenant_id,
                'tenant_slug' => $user->tenant->slug,
                'tenant_name' => $user->tenant->name,
                'user_role' => $user->role,
            ];
        });

        return $this->success([
            'email' => $request->email,
            'tenants' => $tenants,
            'multiple_tenants' => $users->count() > 1,
        ], 'Email found');
    }

    /**
     * Register new tenant and owner user
     *
     * MOBILE APP REGISTRATION FLOW:
     * Step 1: GET /api/v1/auth/business-types - Show business types for user to select
     * Step 2: Show form for personal info (name, email, password) and business info (business_name, phone)
     * Step 3: GET /api/v1/auth/plans - Show plans, let user select and accept terms
     * Step 4: POST /api/v1/auth/register - Submit all data to create tenant + owner user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // Step 1: Business Type Selection
            'business_type_id' => 'required|integer|exists:business_types,id',
            'business_structure' => 'nullable|string',

            // Step 2: Personal & Business Information
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'business_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',

            // Step 3: Plan Selection & Terms
            'plan_id' => 'required|integer|exists:plans,id',
            'terms' => 'required|accepted',

            // Optional
            'device_name' => 'nullable|string|max:255',
            'affiliate_code' => 'nullable|string', // For referral tracking
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $tenant = null;
            $user = null;

            \DB::transaction(function () use ($request, &$tenant, &$user) {
                // Get the selected plan
                $selectedPlan = \App\Models\Plan::findOrFail($request->plan_id);

                // Create tenant (the business/workspace)
                $tenant = Tenant::create([
                    'name' => $request->business_name,
                    'slug' => \App\Helpers\TenantHelper::generateUniqueSlug($request->business_name),
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'business_structure' => $request->business_structure,
                    'business_type_id' => $request->business_type_id,
                    'plan_id' => $selectedPlan->id,
                    'trial_ends_at' => now()->addDays(30), // 30-day trial
                    'is_active' => true,
                    'onboarding_completed' => false,
                ]);

                // Handle affiliate referral if provided
                if ($request->affiliate_code) {
                    $affiliate = \App\Models\Affiliate::where('affiliate_code', $request->affiliate_code)
                        ->where('status', 'active')
                        ->first();

                    if ($affiliate) {
                        \App\Models\AffiliateReferral::create([
                            'affiliate_id' => $affiliate->id,
                            'tenant_id' => $tenant->id,
                            'status' => 'registered',
                            'tracking_data' => [
                                'registered_at' => now(),
                                'plan_selected' => $selectedPlan->name,
                                'source' => 'mobile_app',
                            ],
                        ]);
                    }
                }

                // Start trial for the selected plan
                $tenant->startTrial($selectedPlan);

                // Create owner user
                $user = User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'role' => User::ROLE_OWNER, // Owner role
                    'is_active' => true,
                ]);

                // Generate and store email verification code
                $verificationCode = sprintf('%04d', random_int(0, 9999));
                \DB::table('email_verification_codes')->insert([
                    'user_id' => $user->id,
                    'code' => $verificationCode,
                    'expires_at' => now()->addMinutes(60),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // TODO: Send verification email (implement queue job)
                // dispatch(new SendVerificationEmail($user, $verificationCode));
            });

            // Set tenant context
            $tenant->makeCurrent();

            // Generate auth token
            $deviceName = $request->device_name ?? 'Mobile App';
            $token = $user->createToken($deviceName)->plainTextToken;

            return $this->created([
                'user' => new UserResource($user),
                'token' => $token,
                'tenant' => [
                    'id' => $tenant->id,
                    'slug' => $tenant->slug,
                    'name' => $tenant->name,
                    'plan' => [
                        'id' => $tenant->plan_id,
                        'name' => $tenant->plan->name,
                    ],
                    'trial_ends_at' => $tenant->trial_ends_at,
                ],
                'token_type' => 'Bearer',
                'message' => 'Registration successful! You have 30 days free trial.',
            ], 'Registration successful');

        } catch (\Exception $e) {
            \Log::error('Mobile registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->error('Registration failed. Please try again.', 500);
        }
    }

    /**
     * Get available business types grouped by category
     * For Step 1 of registration
     */
    public function getBusinessTypes(): JsonResponse
    {
        $businessTypes = \App\Models\BusinessType::getGroupedByCategory();

        // Format for mobile consumption
        $formatted = [];
        foreach ($businessTypes as $category => $types) {
            $formatted[] = [
                'category' => $category,
                'types' => $types->map(function ($type) {
                    return [
                        'id' => $type->id,
                        'name' => $type->name,
                        'slug' => $type->slug,
                        'icon' => $type->icon,
                        'description' => $type->description,
                    ];
                })->values(),
            ];
        }

        return $this->success($formatted, 'Business types retrieved successfully');
    }

    /**
     * Get available subscription plans
     * For Step 3 of registration
     */
    public function getPlans(): JsonResponse
    {
        $plans = \App\Models\Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('monthly_price')
            ->get([
                'id',
                'name',
                'slug',
                'description',
                'features',
                'monthly_price',
                'yearly_price',
                'max_users',
                'max_customers',
                'has_pos',
                'has_payroll',
                'has_api_access',
                'has_advanced_reports',
                'support_level',
                'is_popular',
            ])
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'features' => $plan->features,
                    'monthly_price' => $plan->monthly_price,
                    'yearly_price' => $plan->yearly_price,
                    'formatted_monthly_price' => '₦' . number_format($plan->monthly_price / 100, 0),
                    'formatted_yearly_price' => '₦' . number_format($plan->yearly_price / 100, 0),
                    'yearly_savings_percent' => round((1 - ($plan->yearly_price / 12) / $plan->monthly_price) * 100),
                    'limits' => [
                        'max_users' => $plan->max_users,
                        'max_customers' => $plan->max_customers,
                    ],
                    'capabilities' => [
                        'pos' => $plan->has_pos,
                        'payroll' => $plan->has_payroll,
                        'api_access' => $plan->has_api_access,
                        'advanced_reports' => $plan->has_advanced_reports,
                    ],
                    'support_level' => $plan->support_level,
                    'is_popular' => $plan->is_popular,
                ];
            });

        return $this->success($plans, 'Plans retrieved successfully');
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'tenant_slug' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // If tenant slug provided, find user in that tenant
        if ($request->tenant_slug) {
            $tenant = Tenant::where('slug', $request->tenant_slug)->first();
            if (!$tenant) {
                return $this->notFound('Workspace not found');
            }

            $user = User::where('email', $request->email)
                ->where('tenant_id', $tenant->id)
                ->first();
        } else {
            // Find first user with this email
            $user = User::where('email', $request->email)->first();
        }

        if (!$user) {
            // Don't reveal if email exists for security
            return $this->success(null, 'If your email is registered, you will receive a password reset link');
        }

        // Set tenant context
        $user->tenant->makeCurrent();

        // Send reset link
        $status = Password::sendResetLink(['email' => $request->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return $this->success(null, 'Password reset link sent to your email');
        }

        return $this->error('Failed to send password reset link', 500);
    }
}
