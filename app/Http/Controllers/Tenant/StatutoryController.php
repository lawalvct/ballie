<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\VoucherEntry;
use App\Models\Voucher;
use App\Models\PayrollRun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatutoryController extends Controller
{
    public function index(Tenant $tenant)
    {
        // Get VAT accounts
        $vatOutputAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('code', 'VAT-OUT-001')
            ->first();

        $vatInputAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('code', 'VAT-IN-001')
            ->first();

        // Calculate VAT summary for current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $vatOutput = 0;
        $vatInput = 0;

        if ($vatOutputAccount) {
            $vatOutput = VoucherEntry::where('ledger_account_id', $vatOutputAccount->id)
                ->whereHas('voucher', function($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('status', 'posted')
                      ->whereBetween('voucher_date', [$startOfMonth, $endOfMonth]);
                })
                ->sum('credit_amount');
        }

        if ($vatInputAccount) {
            $vatInput = VoucherEntry::where('ledger_account_id', $vatInputAccount->id)
                ->whereHas('voucher', function($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('status', 'posted')
                      ->whereBetween('voucher_date', [$startOfMonth, $endOfMonth]);
                })
                ->sum('debit_amount');
        }

        $netVatPayable = $vatOutput - $vatInput;

        // Calculate pension contributions for current month
        $pensionTotal = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $startOfMonth, $endOfMonth) {
                $q->where('tenant_id', $tenant->id)
                  ->whereBetween('start_date', [$startOfMonth, $endOfMonth]);
            })
            ->where('payment_status', '!=', 'cancelled')
            ->sum(DB::raw('pension_employee + pension_employer'));

        return view('tenant.statutory.index', compact(
            'tenant',
            'vatOutput',
            'vatInput',
            'netVatPayable',
            'pensionTotal',
            'vatOutputAccount',
            'vatInputAccount'
        ));
    }

    public function vatDashboard(Tenant $tenant)
    {
        return $this->index($tenant);
    }

    public function vatOutput(Request $request, Tenant $tenant)
    {
        $vatOutputAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('code', 'VAT-OUT-001')
            ->first();

        if (!$vatOutputAccount) {
            return redirect()->back()->with('error', 'VAT Output account not found.');
        }

        // Date filter
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get VAT Output transactions
        $transactions = VoucherEntry::where('ledger_account_id', $vatOutputAccount->id)
            ->whereHas('voucher', function($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                  ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->with(['voucher.voucherType'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $totalVatOutput = $transactions->sum('credit_amount');

        return view('tenant.statutory.vat-output', compact(
            'tenant',
            'transactions',
            'totalVatOutput',
            'startDate',
            'endDate'
        ));
    }

    public function vatInput(Request $request, Tenant $tenant)
    {
        $vatInputAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('code', 'VAT-IN-001')
            ->first();

        if (!$vatInputAccount) {
            return redirect()->back()->with('error', 'VAT Input account not found.');
        }

        // Date filter
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get VAT Input transactions
        $transactions = VoucherEntry::where('ledger_account_id', $vatInputAccount->id)
            ->whereHas('voucher', function($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                  ->whereBetween('voucher_date', [$startDate, $endDate]);
            })
            ->with(['voucher.voucherType'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $totalVatInput = $transactions->sum('debit_amount');

        return view('tenant.statutory.vat-input', compact(
            'tenant',
            'transactions',
            'totalVatInput',
            'startDate',
            'endDate'
        ));
    }

    public function vatReport(Request $request, Tenant $tenant)
    {
        // Date filter
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get VAT accounts
        $vatOutputAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('code', 'VAT-OUT-001')
            ->first();

        $vatInputAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('code', 'VAT-IN-001')
            ->first();

        $vatOutput = 0;
        $vatInput = 0;

        if ($vatOutputAccount) {
            $vatOutput = VoucherEntry::where('ledger_account_id', $vatOutputAccount->id)
                ->whereHas('voucher', function($q) use ($startDate, $endDate) {
                    $q->where('status', 'posted')
                      ->whereBetween('voucher_date', [$startDate, $endDate]);
                })
                ->sum('credit_amount');
        }

        if ($vatInputAccount) {
            $vatInput = VoucherEntry::where('ledger_account_id', $vatInputAccount->id)
                ->whereHas('voucher', function($q) use ($startDate, $endDate) {
                    $q->where('status', 'posted')
                      ->whereBetween('voucher_date', [$startDate, $endDate]);
                })
                ->sum('debit_amount');
        }

        $netVatPayable = $vatOutput - $vatInput;

        return view('tenant.statutory.vat-report', compact(
            'tenant',
            'vatOutput',
            'vatInput',
            'netVatPayable',
            'startDate',
            'endDate'
        ));
    }

    public function settings(Tenant $tenant)
    {
        return view('tenant.statutory.settings', compact('tenant'));
    }

    public function updateSettings(Request $request, Tenant $tenant)
    {
        // Validate and update tax settings
        $request->validate([
            'vat_rate' => 'required|numeric|min:0|max:100',
            'vat_registration_number' => 'nullable|string|max:255',
        ]);

        // Update tenant settings (you may need to add these fields to tenants table)
        $tenant->update([
            'vat_rate' => $request->vat_rate,
            'vat_registration_number' => $request->vat_registration_number,
        ]);

        return redirect()->back()->with('success', 'Tax settings updated successfully.');
    }

    public function pensionReport(Request $request, Tenant $tenant)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $payrollRuns = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $startDate, $endDate) {
                $q->where('tenant_id', $tenant->id)
                  ->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->with(['employee', 'payrollPeriod'])
            ->where('payment_status', '!=', 'cancelled')
            ->get();

        $groupedByPFA = $payrollRuns->groupBy(function($run) {
            return $run->employee->pfa_provider ?? 'Not Assigned';
        });

        $summary = [
            'total_employee_contribution' => $payrollRuns->sum('pension_employee'),
            'total_employer_contribution' => $payrollRuns->sum('pension_employer'),
            'total_contribution' => $payrollRuns->sum(function($run) {
                return $run->pension_employee + $run->pension_employer;
            }),
            'employee_count' => $payrollRuns->unique('employee_id')->count(),
        ];

        return view('tenant.statutory.pension-report', compact(
            'tenant',
            'payrollRuns',
            'groupedByPFA',
            'summary',
            'startDate',
            'endDate'
        ));
    }
}
