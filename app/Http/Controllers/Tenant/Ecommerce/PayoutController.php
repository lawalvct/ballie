<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PayoutRequest;
use App\Models\PayoutSetting;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    /**
     * Display payout dashboard with history.
     */
    public function index(Request $request)
    {
        $tenant = tenant();
        $settings = PayoutSetting::getSettings();

        // Calculate available balance
        $availableBalance = PayoutRequest::calculateAvailableBalance($tenant->id);

        // Get revenue statistics
        $stats = $this->getRevenueStats($tenant);

        // Get payout history
        $payouts = PayoutRequest::where('tenant_id', $tenant->id)
            ->with('requester')
            ->latest()
            ->paginate(15);

        return view('tenant.ecommerce.payouts.index', compact(
            'tenant',
            'settings',
            'availableBalance',
            'stats',
            'payouts'
        ));
    }

    /**
     * Show the payout request form.
     */
    public function create(Request $request)
    {
        $tenant = tenant();
        $settings = PayoutSetting::getSettings();

        if (!$settings->payouts_enabled) {
            return redirect()->route('tenant.ecommerce.payouts.index', $tenant->slug)
                ->with('error', 'Payouts are currently disabled.');
        }

        // Calculate available balance
        $availableBalance = PayoutRequest::calculateAvailableBalance($tenant->id);

        // Check minimum balance
        if ($availableBalance < $settings->minimum_payout) {
            return redirect()->route('tenant.ecommerce.payouts.index', $tenant->slug)
                ->with('error', 'You need at least ₦' . number_format($settings->minimum_payout, 2) . ' to request a payout.');
        }

        // Get Nigerian banks list
        $banks = $this->getNigerianBanks();

        return view('tenant.ecommerce.payouts.create', compact(
            'tenant',
            'settings',
            'availableBalance',
            'banks'
        ));
    }

    /**
     * Store a new payout request.
     */
    public function store(Request $request)
    {
        $tenant = tenant();
        $settings = PayoutSetting::getSettings();

        $validated = $request->validate([
            'requested_amount' => 'required|numeric|min:1',
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20',
            'bank_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calculate available balance
        $availableBalance = PayoutRequest::calculateAvailableBalance($tenant->id);

        // Validate amount against settings
        $errors = $settings->validateAmount($validated['requested_amount'], $availableBalance);
        if (!empty($errors)) {
            return redirect()->back()
            ->withErrors(['requested_amount' => $errors[0]])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Calculate deductions
            $deduction = PayoutRequest::calculateDeduction($validated['requested_amount'], $settings);

            // Create payout request
            $payout = PayoutRequest::create([
                'tenant_id' => $tenant->id,
                'requested_by' => auth()->id(),
                'requested_amount' => $validated['requested_amount'],
                'deduction_amount' => $deduction['deduction_amount'],
                'net_amount' => $deduction['net_amount'],
                'deduction_type' => $deduction['deduction_type'],
                'deduction_rate' => $deduction['deduction_rate'],
                'available_balance' => $availableBalance,
                'bank_name' => $validated['bank_name'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
                'bank_code' => $validated['bank_code'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => PayoutRequest::STATUS_PENDING,
            ]);

            DB::commit();

            Log::info('Payout request created', [
                'payout_id' => $payout->id,
                'tenant_id' => $tenant->id,
                'amount' => $payout->requested_amount,
                'net_amount' => $payout->net_amount,
            ]);

            return redirect()->route('tenant.ecommerce.payouts.show', [
                'tenant' => $tenant->slug,
                'payout' => $payout->id,
            ])->with('success', 'Payout request submitted successfully! You will receive ₦' . number_format($payout->net_amount, 2) . ' after processing.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payout request failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to submit payout request. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show payout request details.
     */
    public function show(Tenant $tenant, PayoutRequest $payout)
    {
        // Ensure payout belongs to tenant
        if ($payout->tenant_id !== $tenant->id) {
            abort(404);
        }

        $payout->load(['requester', 'processor']);

        return view('tenant.ecommerce.payouts.show', compact('tenant', 'payout'));
    }

    /**
     * Cancel a pending payout request.
     */
    public function cancel(Request $request, Tenant $tenant, PayoutRequest $payout)
    {
        // Ensure payout belongs to tenant
        if ($payout->tenant_id !== $tenant->id) {
            abort(404);
        }

        if (!$payout->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This payout request cannot be cancelled.');
        }

        $payout->update([
            'status' => PayoutRequest::STATUS_CANCELLED,
        ]);

        return redirect()->route('tenant.ecommerce.payouts.index', $tenant->slug)
            ->with('success', 'Payout request cancelled successfully.');
    }

    /**
     * Calculate deduction preview (AJAX).
     */
    public function calculateDeduction(Request $request)
    {
        $settings = PayoutSetting::getSettings();
        $amount = floatval($request->input('amount', 0));

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid amount',
            ]);
        }

        $deduction = PayoutRequest::calculateDeduction($amount, $settings);

        return response()->json([
            'success' => true,
            'requested_amount' => number_format($amount, 2),
            'deduction_amount' => number_format($deduction['deduction_amount'], 2),
            'net_amount' => number_format($deduction['net_amount'], 2),
            'deduction_description' => $settings->deduction_description,
        ]);
    }

    /**
     * Get revenue statistics for the tenant.
     */
    private function getRevenueStats(Tenant $tenant): array
    {
        $totalRevenue = Order::where('tenant_id', $tenant->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalWithdrawn = PayoutRequest::where('tenant_id', $tenant->id)
            ->where('status', PayoutRequest::STATUS_COMPLETED)
            ->sum('requested_amount');

        $pendingWithdrawals = PayoutRequest::where('tenant_id', $tenant->id)
            ->whereIn('status', [
                PayoutRequest::STATUS_PENDING,
                PayoutRequest::STATUS_APPROVED,
                PayoutRequest::STATUS_PROCESSING,
            ])
            ->sum('requested_amount');

        $thisMonthRevenue = Order::where('tenant_id', $tenant->id)
            ->where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        return [
            'total_revenue' => $totalRevenue,
            'total_withdrawn' => $totalWithdrawn,
            'pending_withdrawals' => $pendingWithdrawals,
            'available_balance' => max(0, $totalRevenue - $totalWithdrawn - $pendingWithdrawals),
            'this_month_revenue' => $thisMonthRevenue,
            'total_payouts' => PayoutRequest::where('tenant_id', $tenant->id)->count(),
            'completed_payouts' => PayoutRequest::where('tenant_id', $tenant->id)
                ->where('status', PayoutRequest::STATUS_COMPLETED)
                ->count(),
        ];
    }

    /**
     * Get list of Nigerian banks.
     */
    private function getNigerianBanks(): array
    {
        return [
            ['code' => '044', 'name' => 'Access Bank'],
            ['code' => '023', 'name' => 'Citibank Nigeria'],
            ['code' => '050', 'name' => 'Ecobank Nigeria'],
            ['code' => '070', 'name' => 'Fidelity Bank'],
            ['code' => '011', 'name' => 'First Bank of Nigeria'],
            ['code' => '214', 'name' => 'First City Monument Bank (FCMB)'],
            ['code' => '058', 'name' => 'Guaranty Trust Bank (GTBank)'],
            ['code' => '030', 'name' => 'Heritage Bank'],
            ['code' => '301', 'name' => 'Jaiz Bank'],
            ['code' => '082', 'name' => 'Keystone Bank'],
            ['code' => '526', 'name' => 'Parallex Bank'],
            ['code' => '101', 'name' => 'Providus Bank'],
            ['code' => '076', 'name' => 'Polaris Bank'],
            ['code' => '221', 'name' => 'Stanbic IBTC Bank'],
            ['code' => '068', 'name' => 'Standard Chartered Bank'],
            ['code' => '232', 'name' => 'Sterling Bank'],
            ['code' => '100', 'name' => 'Suntrust Bank'],
            ['code' => '032', 'name' => 'Union Bank of Nigeria'],
            ['code' => '033', 'name' => 'United Bank for Africa (UBA)'],
            ['code' => '215', 'name' => 'Unity Bank'],
            ['code' => '035', 'name' => 'Wema Bank'],
            ['code' => '057', 'name' => 'Zenith Bank'],
            ['code' => '999', 'name' => 'Other'],
        ];
    }
}
