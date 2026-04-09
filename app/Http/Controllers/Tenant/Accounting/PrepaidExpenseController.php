<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\PrepaidExpense;
use Illuminate\Http\Request;

class PrepaidExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
    }

    public function index(Request $request, Tenant $tenant)
    {
        $query = PrepaidExpense::with(['voucher.voucherType', 'prepaidAccount', 'expenseAccount', 'createdBy'])
            ->where('tenant_id', $tenant->id)
            ->latest('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('expenseAccount', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $prepaidExpenses = $query->paginate(20)->appends($request->query());

        $stats = [
            'active' => PrepaidExpense::where('tenant_id', $tenant->id)->where('status', 'active')->count(),
            'completed' => PrepaidExpense::where('tenant_id', $tenant->id)->where('status', 'completed')->count(),
            'total_remaining' => PrepaidExpense::where('tenant_id', $tenant->id)->where('status', 'active')->get()->sum(fn($p) => $p->getRemainingAmount()),
        ];

        return view('tenant.accounting.prepaid-expenses.index', compact('tenant', 'prepaidExpenses', 'stats'));
    }

    public function show(Tenant $tenant, PrepaidExpense $prepaidExpense)
    {
        if ($prepaidExpense->tenant_id !== $tenant->id) {
            abort(403);
        }

        $prepaidExpense->load([
            'voucher.voucherType',
            'voucherEntry',
            'prepaidAccount',
            'expenseAccount',
            'createdBy',
            'postings.voucher',
        ]);

        $schedule = $prepaidExpense->getSchedule();

        return view('tenant.accounting.prepaid-expenses.show', compact('tenant', 'prepaidExpense', 'schedule'));
    }

    public function pause(Tenant $tenant, PrepaidExpense $prepaidExpense)
    {
        if ($prepaidExpense->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($prepaidExpense->status !== 'active') {
            return back()->with('error', 'Only active prepaid expenses can be paused.');
        }

        $prepaidExpense->update(['status' => 'paused']);

        return back()->with('success', 'Prepaid expense schedule paused.');
    }

    public function resume(Tenant $tenant, PrepaidExpense $prepaidExpense)
    {
        if ($prepaidExpense->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($prepaidExpense->status !== 'paused') {
            return back()->with('error', 'Only paused prepaid expenses can be resumed.');
        }

        $prepaidExpense->update(['status' => 'active']);

        return back()->with('success', 'Prepaid expense schedule resumed.');
    }

    public function cancel(Tenant $tenant, PrepaidExpense $prepaidExpense)
    {
        if ($prepaidExpense->tenant_id !== $tenant->id) {
            abort(403);
        }

        if (!in_array($prepaidExpense->status, ['active', 'paused'])) {
            return back()->with('error', 'This prepaid expense cannot be cancelled.');
        }

        $prepaidExpense->update([
            'status' => 'cancelled',
            'next_posting_date' => null,
        ]);

        return back()->with('success', 'Prepaid expense schedule cancelled. No further installments will be posted.');
    }
}
