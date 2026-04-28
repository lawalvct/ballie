<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use App\Models\SystemSetting;
use App\Support\RegistrationInputGuard;
use Illuminate\Auth\Events\Registered;
use App\Notifications\WelcomeNotification;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = 'tenant-login:' . strtolower($request->input('email')) . '|' . $request->ip();
        $maxAttempts = SystemSetting::getValue('max_login_attempts', 5);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            $tenant = $request->route('tenant');
            $tenantId = is_object($tenant) ? $tenant->id : optional(\App\Models\Tenant::where('slug', $tenant)->first())->id;

            // Validate user belongs to this tenant
            if ($tenantId && (int) $user->tenant_id !== (int) $tenantId) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'This account does not belong to this company. Please use the correct login page.',
                ])->onlyInput('email');
            }

            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Always redirect to the user's own tenant dashboard
            $userTenantSlug = $user->tenant ? $user->tenant->slug : null;
            if ($userTenantSlug) {
                return redirect()->intended(route('tenant.dashboard', ['tenant' => $userTenantSlug]));
            }

            return redirect()->intended(route('tenant.dashboard'));
        }

        $lockoutSeconds = SystemSetting::getValue('lockout_duration_minutes', 15) * 60;
        RateLimiter::hit($throttleKey, $lockoutSeconds);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => RegistrationInputGuard::humanNameRules(),
            'email' => RegistrationInputGuard::emailRules('unique:users,email'),
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Send welcome notification with verification code
        // Note: Email verification is optional - users can access dashboard without verifying
        // They will receive reminders to verify within 7 days
        $verificationCode = rand(100000, 999999);
        $user->notify(new WelcomeNotification($verificationCode));

        Auth::login($user);

        return redirect()->route('tenant.dashboard');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('tenant.login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant.login');
    }
}
