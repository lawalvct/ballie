<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\VoucherEntry;
use App\Models\Voucher;
use App\Models\PayrollRun;
use App\Models\TaxRate;
use App\Models\TaxFiling;
use App\Models\Department;
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
        $payrollQuery = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $startOfMonth, $endOfMonth) {
                $q->where('tenant_id', $tenant->id)
                  ->whereBetween('start_date', [$startOfMonth, $endOfMonth]);
            })
            ->where('payment_status', '!=', 'cancelled');

        $pensionTotal = (clone $payrollQuery)->sum(DB::raw('pension_employee + pension_employer'));

        // PAYE tax total for current month
        $payeTaxTotal = (clone $payrollQuery)->sum('monthly_tax');

        // NSITF total for current month
        $nsitfTotal = (clone $payrollQuery)->sum('nsitf_contribution');

        // Overdue filings count
        $overdueFilingsCount = TaxFiling::where('tenant_id', $tenant->id)
            ->where(function($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function($q2) {
                      $q2->whereIn('status', ['draft', 'filed'])
                         ->where('due_date', '<', now());
                  });
            })
            ->count();

        return view('tenant.statutory.index', compact(
            'tenant',
            'vatOutput',
            'vatInput',
            'netVatPayable',
            'pensionTotal',
            'payeTaxTotal',
            'nsitfTotal',
            'overdueFilingsCount',
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
        $taxRates = TaxRate::where('is_active', true)
            ->get();

        return view('tenant.statutory.settings', compact('tenant', 'taxRates'));
    }

    public function updateSettings(Request $request, Tenant $tenant)
    {
        $request->validate([
            'vat_rate' => 'required|numeric|min:0|max:100',
            'vat_registration_number' => 'nullable|string|max:255',
            'tax_identification_number' => 'nullable|string|max:255',
        ]);

        $tenant->update([
            'vat_rate' => $request->vat_rate,
            'vat_registration_number' => $request->vat_registration_number,
            'tax_identification_number' => $request->tax_identification_number,
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

    public function payeReport(Request $request, Tenant $tenant)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $departmentId = $request->input('department_id');

        $query = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $startDate, $endDate) {
                $q->where('tenant_id', $tenant->id)
                  ->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->with(['employee.department', 'payrollPeriod'])
            ->where('payment_status', '!=', 'cancelled');

        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $payrollRuns = $query->get();

        $groupedByEmployee = $payrollRuns->groupBy('employee_id');

        $summary = [
            'total_gross' => $payrollRuns->sum('gross_salary'),
            'total_relief' => $payrollRuns->sum('consolidated_relief'),
            'total_taxable' => $payrollRuns->sum('taxable_income'),
            'total_tax' => $payrollRuns->sum('monthly_tax'),
        ];

        $departments = Department::where('tenant_id', $tenant->id)->orderBy('name')->get();

        return view('tenant.statutory.paye-report', compact(
            'tenant',
            'payrollRuns',
            'groupedByEmployee',
            'summary',
            'departments',
            'startDate',
            'endDate'
        ));
    }

    public function nsitfReport(Request $request, Tenant $tenant)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $payrollRuns = PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant, $startDate, $endDate) {
                $q->where('tenant_id', $tenant->id)
                  ->whereBetween('start_date', [$startDate, $endDate]);
            })
            ->with(['employee.department', 'payrollPeriod'])
            ->where('payment_status', '!=', 'cancelled')
            ->get();

        $groupedByEmployee = $payrollRuns->groupBy('employee_id');

        $summary = [
            'total_nsitf' => $payrollRuns->sum('nsitf_contribution'),
            'employee_count' => $payrollRuns->unique('employee_id')->count(),
            'rate' => 1,
        ];

        return view('tenant.statutory.nsitf-report', compact(
            'tenant',
            'payrollRuns',
            'groupedByEmployee',
            'summary',
            'startDate',
            'endDate'
        ));
    }

    public function filings(Request $request, Tenant $tenant)
    {
        $query = TaxFiling::where('tenant_id', $tenant->id);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('year')) {
            $query->whereYear('period_start', $request->year);
        }

        $filings = $query->orderByDesc('period_start')->paginate(20);

        $overdueFilings = TaxFiling::where('tenant_id', $tenant->id)
            ->where(function($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function($q2) {
                      $q2->whereIn('status', ['draft', 'filed'])
                         ->where('due_date', '<', now());
                  });
            })
            ->get();

        $filingSummary = [
            'paid' => TaxFiling::where('tenant_id', $tenant->id)->where('status', 'paid')->count(),
            'filed' => TaxFiling::where('tenant_id', $tenant->id)->where('status', 'filed')->count(),
            'draft' => TaxFiling::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
        ];

        return view('tenant.statutory.filings', compact(
            'tenant',
            'filings',
            'overdueFilings',
            'filingSummary'
        ));
    }

    public function storeFiling(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'type' => 'required|in:vat,paye,pension,nsitf,wht,cit',
            'period_label' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,filed,paid',
            'due_date' => 'nullable|date',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['filed_by'] = auth()->id();

        if ($validated['status'] === 'filed') {
            $validated['filed_date'] = now();
        } elseif ($validated['status'] === 'paid') {
            $validated['filed_date'] = now();
            $validated['paid_date'] = now();
        }

        TaxFiling::create($validated);

        return redirect()->back()->with('success', 'Tax filing recorded successfully.');
    }

    public function updateFilingStatus(Request $request, Tenant $tenant, TaxFiling $filing)
    {
        if ($filing->tenant_id !== $tenant->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:draft,filed,paid,overdue',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'filed' && !$filing->filed_date) {
            $data['filed_date'] = now();
            $data['filed_by'] = auth()->id();
        } elseif ($request->status === 'paid') {
            $data['paid_date'] = now();
            if (!$filing->filed_date) {
                $data['filed_date'] = now();
                $data['filed_by'] = auth()->id();
            }
        }

        $filing->update($data);

        return redirect()->back()->with('success', 'Filing status updated successfully.');
    }

    public function destroyFiling(Tenant $tenant, TaxFiling $filing)
    {
        if ($filing->tenant_id !== $tenant->id) {
            abort(403);
        }

        $filing->delete();

        return redirect()->back()->with('success', 'Filing record deleted.');
    }
}
