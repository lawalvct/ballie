<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayout;
use App\Models\AffiliateReferral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    /**
     * Display a listing of affiliates.
     */
    public function index(Request $request)
    {
        $query = Affiliate::with(['user', 'referrals', 'commissions'])
            ->withCount(['referrals', 'commissions']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('affiliate_code', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by commission range
        if ($request->filled('min_commission')) {
            $query->where('total_commissions', '>=', $request->min_commission);
        }

        if ($request->filled('max_commission')) {
            $query->where('total_commissions', '<=', $request->max_commission);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $affiliates = $query->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => Affiliate::count(),
            'active' => Affiliate::where('status', 'active')->count(),
            'pending' => Affiliate::where('status', 'pending')->count(),
            'suspended' => Affiliate::where('status', 'suspended')->count(),
            'total_commissions' => Affiliate::sum('total_commissions'),
            'total_paid' => Affiliate::sum('total_paid'),
            'pending_payouts' => AffiliatePayout::where('status', 'pending')->count(),
        ];

        return view('super-admin.affiliates.index', compact('affiliates', 'stats'));
    }

    /**
     * Display the specified affiliate.
     */
    public function show(Affiliate $affiliate)
    {
        $affiliate->load([
            'user',
            'referrals' => fn($q) => $q->latest()->limit(10),
            'referrals.tenant',
            'commissions' => fn($q) => $q->latest()->limit(10),
            'commissions.tenant',
            'payouts' => fn($q) => $q->latest()->limit(10),
        ]);

        // Monthly performance data
        $monthlyData = AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('status', 'paid')
            ->selectRaw('MONTH(paid_date) as month, YEAR(paid_date) as year, SUM(commission_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Recent activity stats
        $activityStats = [
            'total_referrals' => $affiliate->referrals()->count(),
            'confirmed_referrals' => $affiliate->referrals()->where('status', 'confirmed')->count(),
            'pending_referrals' => $affiliate->referrals()->where('status', 'pending')->count(),
            'total_commissions' => $affiliate->commissions()->sum('commission_amount'),
            'pending_commissions' => $affiliate->commissions()->where('status', 'pending')->sum('commission_amount'),
            'approved_commissions' => $affiliate->commissions()->where('status', 'approved')->sum('commission_amount'),
            'paid_commissions' => $affiliate->commissions()->where('status', 'paid')->sum('commission_amount'),
            'balance' => $affiliate->total_commissions - $affiliate->total_paid,
        ];

        return view('super-admin.affiliates.show', compact('affiliate', 'monthlyData', 'activityStats'));
    }

    /**
     * Show the form for editing the specified affiliate.
     */
    public function edit(Affiliate $affiliate)
    {
        $affiliate->load('user');
        return view('super-admin.affiliates.edit', compact('affiliate'));
    }

    /**
     * Update the specified affiliate.
     */
    public function update(Request $request, Affiliate $affiliate)
    {
        $request->validate([
            'affiliate_code' => [
                'required',
                'string',
                'max:20',
                'alpha_num',
                'unique:affiliates,affiliate_code,' . $affiliate->id,
            ],
            'custom_commission_rate' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:pending,active,suspended,rejected',
            'notes' => 'nullable|string',
        ]);

        $affiliate->update([
            'affiliate_code' => strtoupper($request->affiliate_code),
            'custom_commission_rate' => $request->custom_commission_rate,
            'status' => $request->status,
        ]);

        // If approving for the first time
        if ($request->status === 'active' && !$affiliate->approved_at) {
            $affiliate->update(['approved_at' => now()]);
        }

        return redirect()
            ->route('super-admin.affiliates.show', $affiliate)
            ->with('success', 'Affiliate updated successfully.');
    }

    /**
     * Approve an affiliate.
     */
    public function approve(Affiliate $affiliate)
    {
        if ($affiliate->status !== 'pending') {
            return back()->with('error', 'Only pending affiliates can be approved.');
        }

        $affiliate->update([
            'status' => 'active',
            'approved_at' => now(),
        ]);

        // TODO: Send approval email to affiliate

        return back()->with('success', 'Affiliate approved successfully.');
    }

    /**
     * Reject an affiliate.
     */
    public function reject(Request $request, Affiliate $affiliate)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $affiliate->update([
            'status' => 'rejected',
        ]);

        // TODO: Send rejection email with reason

        return back()->with('success', 'Affiliate rejected.');
    }

    /**
     * Suspend an affiliate.
     */
    public function suspend(Request $request, Affiliate $affiliate)
    {
        $request->validate([
            'suspension_reason' => 'required|string|max:500',
        ]);

        $affiliate->update([
            'status' => 'suspended',
        ]);

        // TODO: Send suspension email

        return back()->with('success', 'Affiliate suspended.');
    }

    /**
     * Reactivate a suspended affiliate.
     */
    public function reactivate(Affiliate $affiliate)
    {
        if ($affiliate->status !== 'suspended') {
            return back()->with('error', 'Only suspended affiliates can be reactivated.');
        }

        $affiliate->update([
            'status' => 'active',
        ]);

        // TODO: Send reactivation email

        return back()->with('success', 'Affiliate reactivated successfully.');
    }

    /**
     * Display affiliate commissions.
     */
    public function commissions(Request $request)
    {
        $query = AffiliateCommission::with(['affiliate', 'affiliate.user', 'tenant'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by affiliate
        if ($request->filled('affiliate_id')) {
            $query->where('affiliate_id', $request->affiliate_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $commissions = $query->paginate(20)->withQueryString();

        $stats = [
            'pending' => AffiliateCommission::where('status', 'pending')->sum('commission_amount'),
            'approved' => AffiliateCommission::where('status', 'approved')->sum('commission_amount'),
            'paid' => AffiliateCommission::where('status', 'paid')->sum('commission_amount'),
        ];

        return view('super-admin.affiliates.commissions', compact('commissions', 'stats'));
    }

    /**
     * Approve a commission.
     */
    public function approveCommission(AffiliateCommission $commission)
    {
        if ($commission->status !== 'pending') {
            return back()->with('error', 'Only pending commissions can be approved.');
        }

        $commission->update(['status' => 'approved']);

        return back()->with('success', 'Commission approved successfully.');
    }

    /**
     * Display affiliate payouts.
     */
    public function payouts(Request $request)
    {
        $query = AffiliatePayout::with(['affiliate', 'affiliate.user'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payouts = $query->paginate(20)->withQueryString();

        $stats = [
            'pending' => AffiliatePayout::where('status', 'pending')->sum('net_amount'),
            'processing' => AffiliatePayout::where('status', 'processing')->sum('net_amount'),
            'completed' => AffiliatePayout::where('status', 'completed')->sum('net_amount'),
        ];

        return view('super-admin.affiliates.payouts', compact('payouts', 'stats'));
    }

    /**
     * Process a payout.
     */
    public function processPayout(Request $request, AffiliatePayout $payout)
    {
        $request->validate([
            'transaction_reference' => 'required|string|max:255',
        ]);

        if ($payout->status !== 'pending') {
            return back()->with('error', 'Only pending payouts can be processed.');
        }

        DB::transaction(function () use ($payout, $request) {
            $payout->update([
                'status' => 'completed',
                'processed_at' => now(),
                'completed_at' => now(),
                'notes' => 'Transaction Reference: ' . $request->transaction_reference,
            ]);

            // Update affiliate's total_paid
            $affiliate = $payout->affiliate;
            $affiliate->increment('total_paid', $payout->net_amount);
            $affiliate->update(['last_payout_at' => now()]);
        });

        // TODO: Send payout confirmation email

        return back()->with('success', 'Payout processed successfully.');
    }

    /**
     * Bulk approve affiliates.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'affiliate_ids' => 'required|array',
            'affiliate_ids.*' => 'exists:affiliates,id',
        ]);

        Affiliate::whereIn('id', $request->affiliate_ids)
            ->where('status', 'pending')
            ->update([
                'status' => 'active',
                'approved_at' => now(),
            ]);

        return back()->with('success', 'Affiliates approved successfully.');
    }

    /**
     * Export affiliates data.
     */
    public function export(Request $request)
    {
        $affiliates = Affiliate::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->get();

        $filename = 'affiliates_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($affiliates) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID',
                'Affiliate Code',
                'Name',
                'Email',
                'Company',
                'Phone',
                'Status',
                'Total Referrals',
                'Total Commissions',
                'Total Paid',
                'Balance',
                'Commission Rate',
                'Joined Date',
                'Approved Date',
            ]);

            // Data
            foreach ($affiliates as $affiliate) {
                fputcsv($file, [
                    $affiliate->id,
                    $affiliate->affiliate_code,
                    $affiliate->user->name,
                    $affiliate->user->email,
                    $affiliate->company_name,
                    $affiliate->phone,
                    ucfirst($affiliate->status),
                    $affiliate->total_referrals,
                    number_format($affiliate->total_commissions, 2),
                    number_format($affiliate->total_paid, 2),
                    number_format($affiliate->total_commissions - $affiliate->total_paid, 2),
                    $affiliate->getCommissionRate() . '%',
                    $affiliate->created_at->format('Y-m-d'),
                    $affiliate->approved_at ? $affiliate->approved_at->format('Y-m-d') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
