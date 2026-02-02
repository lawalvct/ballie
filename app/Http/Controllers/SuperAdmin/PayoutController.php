<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use App\Models\PayoutSetting;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    /**
     * Display list of all payout requests.
     */
    public function index(Request $request)
    {
        $query = PayoutRequest::with(['tenant', 'requester', 'processor']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by tenant
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payouts = $query->latest()->paginate(20);

        // Get statistics
        $stats = [
            'pending' => PayoutRequest::where('status', PayoutRequest::STATUS_PENDING)->count(),
            'approved' => PayoutRequest::where('status', PayoutRequest::STATUS_APPROVED)->count(),
            'processing' => PayoutRequest::where('status', PayoutRequest::STATUS_PROCESSING)->count(),
            'completed' => PayoutRequest::where('status', PayoutRequest::STATUS_COMPLETED)->count(),
            'total_pending_amount' => PayoutRequest::whereIn('status', [
                PayoutRequest::STATUS_PENDING,
                PayoutRequest::STATUS_APPROVED,
                PayoutRequest::STATUS_PROCESSING,
            ])->sum('net_amount'),
            'total_completed_amount' => PayoutRequest::where('status', PayoutRequest::STATUS_COMPLETED)->sum('net_amount'),
        ];

        // Get tenants for filter
        $tenants = Tenant::orderBy('name')->get(['id', 'name', 'slug']);

        return view('super-admin.payouts.index', compact('payouts', 'stats', 'tenants'));
    }

    /**
     * Show payout request details.
     */
    public function show(PayoutRequest $payout)
    {
        $payout->load(['tenant', 'requester', 'processor']);

        // Get tenant's payout history
        $tenantPayoutHistory = PayoutRequest::where('tenant_id', $payout->tenant_id)
            ->where('id', '!=', $payout->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('super-admin.payouts.show', compact('payout', 'tenantPayoutHistory'));
    }

    /**
     * Approve a payout request.
     */
    public function approve(Request $request, PayoutRequest $payout)
    {
        if ($payout->status !== PayoutRequest::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending payouts can be approved.');
        }

        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $payout->update([
            'status' => PayoutRequest::STATUS_APPROVED,
            'processed_by' => auth('super_admin')->id(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        Log::info('Payout approved', [
            'payout_id' => $payout->id,
            'admin_id' => auth('super_admin')->id(),
        ]);

        return redirect()->back()->with('success', 'Payout request approved successfully.');
    }

    /**
     * Mark payout as processing.
     */
    public function processing(Request $request, PayoutRequest $payout)
    {
        if (!in_array($payout->status, [PayoutRequest::STATUS_PENDING, PayoutRequest::STATUS_APPROVED])) {
            return redirect()->back()->with('error', 'This payout cannot be marked as processing.');
        }

        $payout->update([
            'status' => PayoutRequest::STATUS_PROCESSING,
            'processed_by' => auth('super_admin')->id(),
        ]);

        return redirect()->back()->with('success', 'Payout marked as processing.');
    }

    /**
     * Complete a payout request.
     */
    public function complete(Request $request, PayoutRequest $payout)
    {
        if (!in_array($payout->status, [PayoutRequest::STATUS_APPROVED, PayoutRequest::STATUS_PROCESSING])) {
            return redirect()->back()->with('error', 'Only approved or processing payouts can be completed.');
        }

        $validated = $request->validate([
            'payment_reference' => 'required|string|max:255',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $payout->update([
            'status' => PayoutRequest::STATUS_COMPLETED,
            'processed_by' => auth('super_admin')->id(),
            'processed_at' => now(),
            'payment_reference' => $validated['payment_reference'],
            'admin_notes' => $validated['admin_notes'] ?? $payout->admin_notes,
        ]);

        Log::info('Payout completed', [
            'payout_id' => $payout->id,
            'admin_id' => auth('super_admin')->id(),
            'payment_reference' => $validated['payment_reference'],
        ]);

        // TODO: Send notification to tenant about completed payout

        return redirect()->back()->with('success', 'Payout marked as completed successfully.');
    }

    /**
     * Reject a payout request.
     */
    public function reject(Request $request, PayoutRequest $payout)
    {
        if (!in_array($payout->status, [PayoutRequest::STATUS_PENDING, PayoutRequest::STATUS_APPROVED])) {
            return redirect()->back()->with('error', 'This payout cannot be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $payout->update([
            'status' => PayoutRequest::STATUS_REJECTED,
            'processed_by' => auth('super_admin')->id(),
            'processed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        Log::info('Payout rejected', [
            'payout_id' => $payout->id,
            'admin_id' => auth('super_admin')->id(),
            'reason' => $validated['rejection_reason'],
        ]);

        // TODO: Send notification to tenant about rejected payout

        return redirect()->back()->with('success', 'Payout request rejected.');
    }

    /**
     * Show payout settings.
     */
    public function settings()
    {
        $settings = PayoutSetting::getSettings();

        return view('super-admin.payouts.settings', compact('settings'));
    }

    /**
     * Update payout settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'deduction_type' => 'required|in:percentage,fixed',
            'deduction_value' => 'required|numeric|min:0',
            'deduction_name' => 'required|string|max:100',
            'minimum_payout' => 'required|numeric|min:0',
            'maximum_payout' => 'nullable|numeric|min:0',
            'processing_time' => 'required|string|max:100',
            'payouts_enabled' => 'nullable|boolean',
            'payout_terms' => 'nullable|string',
        ]);

        $validated['payouts_enabled'] = $request->has('payouts_enabled');

        $settings = PayoutSetting::getSettings();
        $settings->update($validated);

        Log::info('Payout settings updated', [
            'admin_id' => auth('super_admin')->id(),
            'settings' => $validated,
        ]);

        return redirect()->back()->with('success', 'Payout settings updated successfully.');
    }

    /**
     * Export payout requests.
     */
    public function export(Request $request)
    {
        $query = PayoutRequest::with(['tenant', 'requester']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payouts = $query->latest()->get();

        $filename = 'payouts-export-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payouts) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Request #',
                'Tenant',
                'Requested By',
                'Requested Amount',
                'Deduction',
                'Net Amount',
                'Bank Name',
                'Account Name',
                'Account Number',
                'Status',
                'Payment Reference',
                'Requested Date',
                'Processed Date',
            ]);

            foreach ($payouts as $payout) {
                fputcsv($file, [
                    $payout->request_number,
                    $payout->tenant->name ?? 'N/A',
                    $payout->requester->name ?? 'N/A',
                    $payout->requested_amount,
                    $payout->deduction_amount,
                    $payout->net_amount,
                    $payout->bank_name,
                    $payout->account_name,
                    $payout->account_number,
                    $payout->status_label,
                    $payout->payment_reference ?? 'N/A',
                    $payout->created_at->format('Y-m-d H:i'),
                    $payout->processed_at ? $payout->processed_at->format('Y-m-d H:i') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
