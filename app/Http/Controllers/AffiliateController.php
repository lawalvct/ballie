<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateReferral;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    /**
     * Show affiliate program landing page
     */
    public function index()
    {
        return view('affiliate.index');
    }

    /**
     * Show affiliate registration form
     */
    public function register()
    {
        // Check if user is already an affiliate
        if (Auth::check()) {
            $existingAffiliate = Affiliate::where('user_id', Auth::id())->first();
            if ($existingAffiliate) {
                return redirect()->route('affiliate.dashboard')
                    ->with('info', 'You are already registered as an affiliate.');
            }
        }

        return view('affiliate.register');
    }

    /**
     * Store new affiliate registration
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:bank_transfer,paypal,stripe,paystack',
            'payment_details' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // Create user account
            $user = \App\Models\User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);

            // Create affiliate profile
            $status = config('affiliate.auto_approval') ? 'active' : 'pending';

            $affiliate = Affiliate::create([
                'user_id' => $user->id,
                'company_name' => $validated['company_name'],
                'phone' => $validated['phone'],
                'bio' => $validated['bio'],
                'payment_details' => [
                    'method' => $validated['payment_method'],
                    'details' => $validated['payment_details'],
                ],
                'status' => $status,
                'approved_at' => config('affiliate.auto_approval') ? now() : null,
            ]);

            DB::commit();

            // Log the user in
            Auth::login($user);

            $message = $status === 'active'
                ? 'Your affiliate account has been created successfully!'
                : 'Your affiliate application has been submitted for review.';

            return redirect()->route('affiliate.dashboard')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Show affiliate dashboard
     */
    public function dashboard()
    {
        $affiliate = Affiliate::where('user_id', Auth::id())->firstOrFail();

        // Get statistics
        $stats = [
            'total_referrals' => $affiliate->referrals()->count(),
            'confirmed_referrals' => $affiliate->referrals()->confirmed()->count(),
            'pending_referrals' => $affiliate->referrals()->pending()->count(),
            'total_earned' => $affiliate->total_commissions,
            'total_paid' => $affiliate->total_paid,
            'pending_commissions' => $affiliate->getPendingCommissions(),
            'this_month_earnings' => $affiliate->getMonthlyEarnings(now()->month, now()->year),
        ];

        // Recent referrals
        $recentReferrals = $affiliate->referrals()
            ->with('tenant')
            ->latest()
            ->limit(10)
            ->get();

        // Recent commissions
        $recentCommissions = $affiliate->commissions()
            ->with('tenant')
            ->latest()
            ->limit(10)
            ->get();

        // Monthly earnings chart data (last 6 months)
        $monthlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyEarnings[] = [
                'month' => $date->format('M Y'),
                'amount' => $affiliate->getMonthlyEarnings($date->month, $date->year),
            ];
        }

        return view('affiliate.dashboard', compact('affiliate', 'stats', 'recentReferrals', 'recentCommissions', 'monthlyEarnings'));
    }

    /**
     * Show referrals list
     */
    public function referrals()
    {
        $affiliate = Affiliate::where('user_id', Auth::id())->firstOrFail();

        $referrals = $affiliate->referrals()
            ->with('tenant')
            ->latest()
            ->paginate(20);

        return view('affiliate.referrals', compact('affiliate', 'referrals'));
    }

    /**
     * Show commissions list
     */
    public function commissions()
    {
        $affiliate = Affiliate::where('user_id', Auth::id())->firstOrFail();

        $commissions = $affiliate->commissions()
            ->with('tenant', 'referral')
            ->latest()
            ->paginate(20);

        return view('affiliate.commissions', compact('affiliate', 'commissions'));
    }

    /**
     * Show payouts list
     */
    public function payouts()
    {
        $affiliate = Affiliate::where('user_id', Auth::id())->firstOrFail();

        $payouts = $affiliate->payouts()
            ->latest()
            ->paginate(20);

        $availableBalance = $affiliate->getPendingCommissions();
        $minimumPayout = config('affiliate.minimum_payout');

        return view('affiliate.payouts', compact('affiliate', 'payouts', 'availableBalance', 'minimumPayout'));
    }

    /**
     * Request a payout
     */
    public function requestPayout(Request $request)
    {
        $affiliate = Affiliate::where('user_id', Auth::id())->firstOrFail();

        $availableBalance = $affiliate->getPendingCommissions();
        $minimumPayout = config('affiliate.minimum_payout');

        if ($availableBalance < $minimumPayout) {
            return back()->with('error', "Minimum payout amount is ₦{$minimumPayout}");
        }

        $validated = $request->validate([
            'amount' => "required|numeric|min:{$minimumPayout}|max:{$availableBalance}",
            'payout_method' => 'required|in:' . implode(',', array_keys(config('affiliate.payout_methods'))),
            'notes' => 'nullable|string|max:500',
        ]);

        $feePercentage = config('affiliate.platform_fee_percentage', 0);
        $feeAmount = ($validated['amount'] * $feePercentage) / 100;
        $netAmount = $validated['amount'] - $feeAmount;

        AffiliatePayout::create([
            'affiliate_id' => $affiliate->id,
            'total_amount' => $validated['amount'],
            'fee_amount' => $feeAmount,
            'net_amount' => $netAmount,
            'payout_method' => $validated['payout_method'],
            'payout_details' => $affiliate->payment_details,
            'status' => 'pending',
            'notes' => $validated['notes'],
            'requested_at' => now(),
        ]);

        return back()->with('success', 'Payout request submitted successfully!');
    }

    /**
     * Show affiliate settings
     */
    public function settings()
    {
        $affiliate = Affiliate::where('user_id', Auth::id())->firstOrFail();
        return view('affiliate.settings', compact('affiliate'));
    }

    /**
     * Update affiliate settings
     */
    public function updateSettings(Request $request)
    {
        $affiliate = Affiliate::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'payment_method' => 'required|in:' . implode(',', array_keys(config('affiliate.payout_methods'))),
            'payment_details' => 'required|array',
        ]);

        $affiliate->update([
            'company_name' => $validated['company_name'],
            'phone' => $validated['phone'],
            'bio' => $validated['bio'],
            'payment_details' => [
                'method' => $validated['payment_method'],
                'details' => $validated['payment_details'],
            ],
        ]);

        return back()->with('success', 'Settings updated successfully!');
    }
}
