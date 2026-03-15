<?php

namespace App\Http\Controllers\Api\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Order;
use App\Models\PayoutRequest;
use App\Models\PayoutSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PayoutController extends Controller
{
    /**
     * List payouts with stats
     */
    public function index(Request $request, Tenant $tenant)
    {
        try {
            $settings = PayoutSetting::getSettings();
            $availableBalance = PayoutRequest::calculateAvailableBalance($tenant->id);

            // Revenue stats
            $totalRevenue = (float) Order::where('tenant_id', $tenant->id)
                ->where('payment_status', 'paid')->sum('total_amount');

            $totalWithdrawn = (float) PayoutRequest::where('tenant_id', $tenant->id)
                ->where('status', PayoutRequest::STATUS_COMPLETED)->sum('requested_amount');

            $pendingWithdrawals = (float) PayoutRequest::where('tenant_id', $tenant->id)
                ->whereIn('status', [PayoutRequest::STATUS_PENDING, PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PROCESSING])
                ->sum('requested_amount');

            $thisMonthRevenue = (float) Order::where('tenant_id', $tenant->id)
                ->where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount');

            // Payout history
            $perPage = min($request->integer('per_page', 15), 50);
            $payouts = PayoutRequest::where('tenant_id', $tenant->id)
                ->with('requester:id,name')
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => [
                        'total_revenue' => $totalRevenue,
                        'available_balance' => (float) $availableBalance,
                        'total_withdrawn' => $totalWithdrawn,
                        'pending_withdrawals' => $pendingWithdrawals,
                        'this_month_revenue' => $thisMonthRevenue,
                    ],
                    'settings' => [
                        'payouts_enabled' => (bool) $settings->payouts_enabled,
                        'minimum_payout' => (float) $settings->minimum_payout,
                        'maximum_payout' => (float) $settings->maximum_payout,
                        'deduction_type' => $settings->deduction_type,
                        'deduction_value' => (float) $settings->deduction_value,
                        'deduction_name' => $settings->deduction_name,
                        'processing_time' => $settings->processing_time,
                        'payout_terms' => $settings->payout_terms,
                    ],
                    'payouts' => $payouts->map(fn($p) => [
                        'id' => $p->id,
                        'request_number' => $p->request_number,
                        'requested_amount' => (float) $p->requested_amount,
                        'deduction_amount' => (float) $p->deduction_amount,
                        'net_amount' => (float) $p->net_amount,
                        'bank_name' => $p->bank_name,
                        'account_name' => $p->account_name,
                        'account_number' => $p->account_number,
                        'status' => $p->status,
                        'status_color' => $p->status_color,
                        'status_label' => $p->status_label,
                        'can_be_cancelled' => $p->canBeCancelled(),
                        'payment_reference' => $p->payment_reference,
                        'requester' => $p->requester ? [
                            'id' => $p->requester->id,
                            'name' => $p->requester->name,
                        ] : null,
                        'processed_at' => $p->processed_at?->toIso8601String(),
                        'created_at' => $p->created_at->toIso8601String(),
                    ]),
                ],
                'pagination' => [
                    'current_page' => $payouts->currentPage(),
                    'last_page' => $payouts->lastPage(),
                    'per_page' => $payouts->perPage(),
                    'total' => $payouts->total(),
                    'from' => $payouts->firstItem(),
                    'to' => $payouts->lastItem(),
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Payouts list API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load payouts.',
            ], 500);
        }
    }

    /**
     * Get form data for creating a payout (banks list, balance, settings)
     */
    public function create(Request $request, Tenant $tenant)
    {
        try {
            $settings = PayoutSetting::getSettings();
            $availableBalance = PayoutRequest::calculateAvailableBalance($tenant->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'available_balance' => (float) $availableBalance,
                    'payouts_enabled' => (bool) $settings->payouts_enabled,
                    'minimum_payout' => (float) $settings->minimum_payout,
                    'maximum_payout' => (float) $settings->maximum_payout,
                    'deduction_description' => $settings->deduction_description,
                    'banks' => $this->getNigerianBanks(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load form data.',
            ], 500);
        }
    }

    /**
     * Store a new payout request
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'requested_amount' => 'required|numeric|min:1',
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20',
            'bank_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $settings = PayoutSetting::getSettings();
            $availableBalance = PayoutRequest::calculateAvailableBalance($tenant->id);

            // Validate amount against settings
            $errors = $settings->validateAmount($validated['requested_amount'], $availableBalance);
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => $errors[0],
                    'errors' => ['requested_amount' => $errors],
                ], 422);
            }

            DB::beginTransaction();

            $deduction = PayoutRequest::calculateDeduction($validated['requested_amount'], $settings);

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

            return response()->json([
                'success' => true,
                'message' => 'Payout request submitted successfully.',
                'data' => [
                    'id' => $payout->id,
                    'request_number' => $payout->request_number,
                    'requested_amount' => (float) $payout->requested_amount,
                    'deduction_amount' => (float) $payout->deduction_amount,
                    'net_amount' => (float) $payout->net_amount,
                    'status' => $payout->status,
                    'created_at' => $payout->created_at->toIso8601String(),
                ],
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payout store API error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit payout request.',
            ], 500);
        }
    }

    /**
     * Show payout details
     */
    public function show(Request $request, Tenant $tenant, $payoutId)
    {
        try {
            $payout = PayoutRequest::with(['requester:id,name,email', 'processor:id,name'])
                ->where('tenant_id', $tenant->id)
                ->findOrFail($payoutId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $payout->id,
                    'request_number' => $payout->request_number,
                    'requested_amount' => (float) $payout->requested_amount,
                    'deduction_amount' => (float) $payout->deduction_amount,
                    'net_amount' => (float) $payout->net_amount,
                    'deduction_type' => $payout->deduction_type,
                    'deduction_rate' => (float) $payout->deduction_rate,
                    'deduction_description' => $payout->deduction_description,
                    'available_balance' => (float) $payout->available_balance,
                    'bank_name' => $payout->bank_name,
                    'account_name' => $payout->account_name,
                    'account_number' => $payout->account_number,
                    'bank_code' => $payout->bank_code,
                    'status' => $payout->status,
                    'status_color' => $payout->status_color,
                    'status_label' => $payout->status_label,
                    'progress_percentage' => $payout->progress_percentage,
                    'can_be_cancelled' => $payout->canBeCancelled(),
                    'notes' => $payout->notes,
                    'admin_notes' => $payout->admin_notes,
                    'rejection_reason' => $payout->rejection_reason,
                    'payment_reference' => $payout->payment_reference,
                    'requester' => $payout->requester ? [
                        'id' => $payout->requester->id,
                        'name' => $payout->requester->name,
                        'email' => $payout->requester->email,
                    ] : null,
                    'processor' => $payout->processor ? [
                        'id' => $payout->processor->id,
                        'name' => $payout->processor->name,
                    ] : null,
                    'processed_at' => $payout->processed_at?->toIso8601String(),
                    'created_at' => $payout->created_at->toIso8601String(),
                    'updated_at' => $payout->updated_at->toIso8601String(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payout request not found.',
            ], 404);
        }
    }

    /**
     * Cancel a pending payout request
     */
    public function cancel(Request $request, Tenant $tenant, $payoutId)
    {
        $payout = PayoutRequest::where('tenant_id', $tenant->id)->findOrFail($payoutId);

        if (!$payout->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'This payout request cannot be cancelled.',
            ], 422);
        }

        try {
            $payout->update(['status' => PayoutRequest::STATUS_CANCELLED]);

            return response()->json([
                'success' => true,
                'message' => 'Payout request cancelled successfully.',
                'data' => [
                    'status' => $payout->status,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel payout request.',
            ], 500);
        }
    }

    /**
     * Calculate deduction preview
     */
    public function calculateDeduction(Request $request, Tenant $tenant)
    {
        $amount = floatval($request->input('amount', 0));

        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid amount.',
            ]);
        }

        $settings = PayoutSetting::getSettings();
        $deduction = PayoutRequest::calculateDeduction($amount, $settings);

        return response()->json([
            'success' => true,
            'data' => [
                'requested_amount' => (float) $amount,
                'deduction_amount' => (float) $deduction['deduction_amount'],
                'net_amount' => (float) $deduction['net_amount'],
                'deduction_description' => $settings->deduction_description,
            ],
        ]);
    }

    /**
     * Get Nigerian banks list
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
