<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmailVerificationController extends Controller
{
    /**
     * Display the email verification notice.
     */
    public function notice(): View
    {
        // If already verified, redirect to dashboard
        if (Auth::user()->hasVerifiedEmail()) {
            $tenant = Auth::user()->tenant;
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug]);
        }

        return view('auth.verify-email');
    }

    /**
     * Handle the verification code submission.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        $user = Auth::user();

        // Find the verification code
        $verification = DB::table('email_verification_codes')
            ->where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return back()->withErrors([
                'code' => 'Invalid or expired verification code. Please try again or request a new code.',
            ])->withInput();
        }

        // Mark email as verified
        $user->email_verified_at = now();
        $user->save();

        // Delete the used verification code
        DB::table('email_verification_codes')
            ->where('id', $verification->id)
            ->delete();

        // Redirect to tenant dashboard with success message
        $tenant = $user->tenant;
        return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug])
            ->with('success', 'Email verified successfully! Welcome to Ballie. Your 30-day trial for the ' . $tenant->plan->name . ' plan has started.');
    }

    /**
     * Resend the verification code.
     */
    public function resend(): RedirectResponse
    {
        $user = Auth::user();

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            $tenant = $user->tenant;
            return redirect()->route('tenant.dashboard', ['tenant' => $tenant->slug]);
        }

        // Delete any existing codes for this user
        DB::table('email_verification_codes')
            ->where('user_id', $user->id)
            ->delete();

        // Generate new 4-digit verification code
        $code = sprintf('%04d', random_int(0, 9999));

        // Store new verification code
        DB::table('email_verification_codes')->insert([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(60),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send welcome email with new verification code
        $user->notify(new WelcomeNotification($code));

        return back()->with('success', 'A new verification code has been sent to your email address.');
    }
}
