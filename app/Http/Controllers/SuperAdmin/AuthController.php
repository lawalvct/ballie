<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Debug: Check if user is already authenticated
        if (Auth::guard('super_admin')->check()) {
            return redirect()->route('super-admin.dashboard');
        }

        // Clear any non-super-admin intended URL from session
        $intendedUrl = session('url.intended');
        if ($intendedUrl && !str_contains($intendedUrl, '/super-admin/')) {
            session()->forget('url.intended');
        }

        return view('super-admin.auth.login');
    }

    public function login(Request $request)
    {
        Log::info('Super Admin login attempt', ['email' => $request->email]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Check if super admin exists and is active
        $superAdmin = SuperAdmin::where('email', $credentials['email'])->first();

        if (!$superAdmin) {
            Log::warning('Super Admin not found', ['email' => $credentials['email']]);
            return back()->withErrors([
                'email' => 'No super admin account found with this email address.',
            ])->withInput($request->except('password'));
        }

        if (!$superAdmin->is_active) {
            Log::warning('Super Admin account inactive', ['email' => $credentials['email']]);
            return back()->withErrors([
                'email' => 'This super admin account has been deactivated.',
            ])->withInput($request->except('password'));
        }

        if (Auth::guard('super_admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Log::info('Super Admin login successful', ['email' => $credentials['email']]);

            // Update last login time
            $superAdmin->update(['last_login_at' => now()]);

            // Get the intended URL and validate it's a super admin route
            $intendedUrl = $request->session()->get('url.intended');

            // Only redirect to intended URL if it's a super admin route
            if ($intendedUrl && str_contains($intendedUrl, '/super-admin/')) {
                return redirect()->intended(route('super-admin.dashboard'));
            }

            // Otherwise, always redirect to super admin dashboard
            return redirect()->route('super-admin.dashboard');
        }

        // Login failed - redirect back to super admin login with error
        Log::warning('Super Admin login failed', ['email' => $credentials['email']]);
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    public function showRegistrationForm()
    {
        // Only allow registration if no super admins exist
        if (SuperAdmin::count() > 0) {
            abort(403, 'Super Admin registration is not allowed.');
        }

        return view('super-admin.auth.register');
    }

    public function register(Request $request)
    {
        // Only allow registration if no super admins exist
        if (SuperAdmin::count() > 0) {
            abort(403, 'Super Admin registration is not allowed.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:super_admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $superAdmin = SuperAdmin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        Auth::guard('super_admin')->login($superAdmin);

        return redirect()->route('super-admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('super_admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super-admin.login');
    }
}
