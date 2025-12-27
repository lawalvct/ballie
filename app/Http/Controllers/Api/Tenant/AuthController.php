<?php

namespace App\Http\Controllers\Api\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends BaseApiController
{
    /**
     * Register a new user for a tenant.
     */
    public function register(Request $request, string $tenantSlug): JsonResponse
    {
        // Find tenant
        $tenant = Tenant::where('slug', $tenantSlug)->first();
        if (!$tenant) {
            return $this->notFound('Tenant not found');
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'phone' => ['nullable', 'string', 'max:20'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
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

        // Create token
        $deviceName = $request->device_name ?? 'mobile_app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->created([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
            ],
        ], 'Registration successful');
    }

    /**
     * Login a user.
     */
    public function login(Request $request, string $tenantSlug): JsonResponse
    {
        // Find tenant
        $tenant = Tenant::where('slug', $tenantSlug)->first();
        if (!$tenant) {
            return $this->notFound('Tenant not found');
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        // Find user in this tenant
        $user = User::where('tenant_id', $tenant->id)
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->unauthorized('Invalid credentials');
        }

        // Check if user is active
        if (!$user->is_active) {
            return $this->forbidden('Your account has been deactivated. Please contact support.');
        }

        // Set tenant context
        $tenant->makeCurrent();

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Create token
        $deviceName = $request->device_name ?? 'mobile_app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'logo' => $tenant->logo,
            ],
        ], 'Login successful');
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Logout from all devices.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return $this->success(null, 'Logged out from all devices');
    }

    /**
     * Get the authenticated user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('tenant');

        return $this->success([
            'user' => new UserResource($user),
            'tenant' => [
                'id' => $user->tenant->id,
                'name' => $user->tenant->name,
                'slug' => $user->tenant->slug,
                'logo' => $user->tenant->logo,
                'subscription_status' => $user->tenant->subscription_status,
            ],
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return $this->success([
            'user' => new UserResource($user),
        ], 'Profile updated successfully');
    }

    /**
     * Change the authenticated user's password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error('Current password is incorrect', 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success(null, 'Password changed successfully');
    }

    /**
     * Request password reset (send email).
     */
    public function forgotPassword(Request $request, string $tenantSlug): JsonResponse
    {
        $tenant = Tenant::where('slug', $tenantSlug)->first();
        if (!$tenant) {
            return $this->notFound('Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = User::where('tenant_id', $tenant->id)
            ->where('email', $request->email)
            ->first();

        if ($user) {
            // Send password reset notification
            $token = app('auth.password.broker')->createToken($user);
            $user->sendPasswordResetNotification($token);
        }

        // Always return success to prevent email enumeration
        return $this->success(null, 'If the email exists, a password reset link has been sent.');
    }

    /**
     * Refresh the current token.
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $deviceName = $request->device_name ?? 'mobile_app';

        // Delete current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Token refreshed successfully');
    }

    /**
     * Get list of active sessions/tokens.
     */
    public function sessions(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->get(['id', 'name', 'last_used_at', 'created_at']);

        return $this->success([
            'sessions' => $tokens->map(function ($token) use ($request) {
                return [
                    'id' => $token->id,
                    'device_name' => $token->name,
                    'last_used_at' => $token->last_used_at?->toIso8601String(),
                    'created_at' => $token->created_at->toIso8601String(),
                    'is_current' => $token->id === $request->user()->currentAccessToken()->id,
                ];
            }),
        ]);
    }

    /**
     * Revoke a specific session/token.
     */
    public function revokeSession(Request $request, int $tokenId): JsonResponse
    {
        $token = $request->user()->tokens()->find($tokenId);

        if (!$token) {
            return $this->notFound('Session not found');
        }

        $token->delete();

        return $this->success(null, 'Session revoked successfully');
    }
}
