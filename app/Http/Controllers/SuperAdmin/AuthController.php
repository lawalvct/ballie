<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('super-admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('super_admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Update last login time
            $superAdmin = Auth::guard('super_admin')->user();
            $superAdmin->update(['last_login_at' => now()]);

            return redirect()->intended(route('super-admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
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
