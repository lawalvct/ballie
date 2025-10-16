<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Notifications\AffiliateWelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AffiliateVerificationController extends Controller
{
    /**
     * Show affiliate verification form
     */
    public function notice()
    {
        // Redirect if already verified
        if (Auth::check()) {
            $affiliate = Affiliate::where('user_id', Auth::id())->first();
            if ($affiliate && Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('affiliate.dashboard')->with('success', 'Email already verified!');
            }
        }

        return view('affiliate.verify-email');
    }

    /**
     * Verify affiliate email with code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        $code = $request->input('code');
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Find the affiliate record
        $affiliate = Affiliate::where('user_id', $user->id)->first();
        if (!$affiliate) {
            return redirect()->route('affiliate.register')->with('error', 'Affiliate account not found.');
        }

        // Check verification code
        $verificationCode = DB::table('email_verification_codes')
            ->where('user_id', $user->id)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationCode) {
            throw ValidationException::withMessages([
                'code' => ['Invalid or expired verification code.'],
            ]);
        }

        // Mark user as verified
        $user->markEmailAsVerified();

        // Delete the verification code
        DB::table('email_verification_codes')
            ->where('user_id', $user->id)
            ->delete();

        // Log the verification
        Log::info('Affiliate email verified successfully', [
            'user_id' => $user->id,
            'affiliate_id' => $affiliate->id,
            'email' => $user->email,
        ]);

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Email verified successfully! Welcome to the Ballie Affiliate Program!');
    }

    /**
     * Resend verification code
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('affiliate.dashboard')->with('info', 'Email already verified!');
        }

        // Find the affiliate record
        $affiliate = Affiliate::where('user_id', $user->id)->first();
        if (!$affiliate) {
            return redirect()->route('affiliate.register')->with('error', 'Affiliate account not found.');
        }

        // Delete existing codes for this user
        DB::table('email_verification_codes')
            ->where('user_id', $user->id)
            ->delete();

        // Generate new 4-digit code
        $code = sprintf('%04d', random_int(0, 9999));

        // Store the new code
        DB::table('email_verification_codes')->insert([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(60),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send notification
        try {
            $user->notify(new AffiliateWelcomeNotification($code));

            Log::info('Affiliate verification code resent', [
                'user_id' => $user->id,
                'affiliate_id' => $affiliate->id,
                'email' => $user->email,
            ]);

            return back()->with('success', 'Verification code sent! Please check your email.');
        } catch (\Exception $e) {
            Log::error('Failed to resend affiliate verification code', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send verification code. Please try again.');
        }
    }
}