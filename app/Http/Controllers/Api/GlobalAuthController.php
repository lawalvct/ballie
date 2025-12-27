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
     * Register new user (requires tenant identification)
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tenant_slug' => 'required|string|exists:tenants,slug',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'device_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Get tenant
        $tenant = Tenant::where('slug', $request->tenant_slug)->firstOrFail();

        // Check if email already exists for this tenant
        $existingUser = User::where('tenant_id', $tenant->id)
            ->where('email', $request->email)
            ->first();

        if ($existingUser) {
            return $this->error('Email already registered for this workspace', 422);
        }

        // Create user
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'user',
            'is_active' => true,
        ]);

        // Set tenant context
        $tenant->makeCurrent();

        // Generate token
        $deviceName = $request->device_name ?? 'Mobile App';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->created([
            'user' => new UserResource($user),
            'token' => $token,
            'tenant' => [
                'id' => $tenant->id,
                'slug' => $tenant->slug,
                'name' => $tenant->name,
            ],
            'token_type' => 'Bearer',
        ], 'Registration successful');
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
