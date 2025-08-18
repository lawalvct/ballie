<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function redirect($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('login')->with('error', 'Invalid social provider');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            if (!in_array($provider, ['google', 'facebook'])) {
                return redirect()->route('login')->with('error', 'Invalid social provider');
            }

            $socialUser = Socialite::driver($provider)->user();

            // Check if user already exists
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // User exists, log them in
                Auth::login($user);

                // Redirect based on onboarding status
                if (!$user->onboarding_completed) {
                    // For tenant-specific applications
                    if (request()->is('*/auth/*')) {
                        $tenant = request()->route('tenant');
                        if ($tenant) {
                            return redirect()->route('tenant.onboarding.index', ['tenant' => $tenant]);
                        }
                    }
                    // For tenant-specific applications, we need to redirect to the tenant onboarding
                    if (request()->is('*/auth/*')) {
                        // If the request is coming from a tenant subdomain
                        $tenant = request()->route('tenant');
                        if ($tenant) {
                            return redirect()->route('tenant.onboarding.index', ['tenant' => $tenant]);
                        }
                    }

                    return redirect()->route('onboarding.index');
                }

                // For tenant-specific applications
                if (request()->is('*/auth/*')) {
                    $tenant = request()->route('tenant');
                    if ($tenant) {
                        return redirect()->route('tenant.dashboard', ['tenant' => $tenant]);
                    }
                }

                return redirect()->route('dashboard');
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), // Random password since they'll use social login
                    'email_verified_at' => now(), // Social accounts are pre-verified
                    'onboarding_completed' => false,
                    'social_provider' => $provider,
                    'social_provider_id' => $socialUser->getId(),
                    'social_avatar' => $socialUser->getAvatar(),
                ]);

                Auth::login($user);

                return redirect()->route('onboarding.index');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong with social authentication. Please try again.');
        }
    }
}
