<?php

namespace App\Http\Controllers\Api\Tenant\Statutory;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\LedgerAccount;
use App\Models\VoucherEntry;
use App\Models\PayrollRun;
use App\Models\TaxRate;
use App\Models\TaxFiling;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatutoryController extends Controller
{
    /**
     * Dashboard - overview of all statutory obligations for current month
     */
    public function dashboard(Request $request, Tenant $tenant)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // VAT
        $vatOutputAccount = LedgerAccount::where('tenant_id', $tenant->id)->where('code', 'VAT-OUT-001')->first();
        $vatInputAccount = LedgerAccount::where('tenant_id', $tenant->id)->where('code', 'VAT-IN-001')->first();

        $vatOutput = 0;
        $vatInput = 0;

        if ($vatOutputAccount) {
            $vatOutput = VoucherEntry::where('ledger_account_id', $vatOutputAccount->id)
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted')->whereBetween('voucher_date', [$startOfMonth, $endOfMonth]))
                ->sum('credit_amount');
        }
        if ($vatInputAccount) {
            $vatInput = VoucherEntry::where('ledger_account_id', $vatInputAccount->id)
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted')->whereBetween('voucher_date', [$startOfMonth, $endOfMonth]))
                ->sum('debit_amount');
        }

        // Payroll-based taxes
        $payrollQuery = PayrollRun::whereHas('payrollPeriod', fn($q) => $q->where('tenant_id', $tenant->id)->whereBetween('start_date', [$startOfMonth, $endOfMonth]))
            ->where('payment_status', '!=', 'cancelled');

        $pensionTotal = (clone $payrollQuery)->sum(DB::raw('pension_employee + pension_employer'));
        $payeTaxTotal = (clone $payrollQuery)->sum('monthly_tax');
        $nsitfTotal = (clone $payrollQuery)->sum('nsitf_contribution');

        // Filing compliance
        $overdueFilings = TaxFiling::where('tenant_id', $tenant->id)
            ->where(function($q) {
                $q->where('status', 'overdue')
                  ->orWhere(fn($q2) => $q2->whereIn('status', ['draft', 'filed'])->where('due_date', '<', now()));
            })
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'month' => $startOfMonth->format('F Y'),
                    'start' => $startOfMonth->toDateString(),
                    'end' => $endOfMonth->toDateString(),
                ],
                'vat' => [
                    'output' => round($vatOutput, 2),
                    'input' => round($vatInput, 2),
                    'net_payable' => round($vatOutput - $vatInput, 2),
                    'rate' => $tenant->vat_rate ?? 7.50,
                ],
                'paye' => [
                    'total_tax' => round($payeTaxTotal, 2),
                ],
                'pension' => [
                    'total' => round($pensionTotal, 2),
                    'employee_rate' => 8,
                    'employer_rate' => 10,
                ],
                'nsitf' => [
                    'total' => round($nsitfTotal, 2),
                    'rate' => 1,
                ],
                'compliance' => [
                    'overdue_filings' => $overdueFilings,
                ],
            ],
        ]);
    }

    /**
     * VAT Report
     */
    public function vatReport(Request $request, Tenant $tenant)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $vatOutputAccount = LedgerAccount::where('tenant_id', $tenant->id)->where('code', 'VAT-OUT-001')->first();
        $vatInputAccount = LedgerAccount::where('tenant_id', $tenant->id)->where('code', 'VAT-IN-001')->first();

        $vatOutput = 0;
        $vatInput = 0;
        $outputTransactions = collect();
        $inputTransactions = collect();

        if ($vatOutputAccount) {
            $query = VoucherEntry::where('ledger_account_id', $vatOutputAccount->id)
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted')->whereBetween('voucher_date', [$startDate, $endDate]))
                ->with(['voucher.voucherType']);
            $vatOutput = (clone $query)->sum('credit_amount');
            $outputTransactions = $query->orderBy('created_at', 'desc')->get()->map(fn($t) => [
                'id' => $t->id,
                'date' => $t->voucher->voucher_date,
                'voucher_number' => $t->voucher->voucher_number,
                'type' => $t->voucher->voucherType->name ?? null,
                'description' => $t->narration ?? $t->voucher->narration,
                'amount' => round($t->credit_amount, 2),
            ]);
        }

        if ($vatInputAccount) {
            $query = VoucherEntry::where('ledger_account_id', $vatInputAccount->id)
                ->whereHas('voucher', fn($q) => $q->where('status', 'posted')->whereBetween('voucher_date', [$startDate, $endDate]))
                ->with(['voucher.voucherType']);
            $vatInput = (clone $query)->sum('debit_amount');
            $inputTransactions = $query->orderBy('created_at', 'desc')->get()->map(fn($t) => [
                'id' => $t->id,
                'date' => $t->voucher->voucher_date,
                'voucher_number' => $t->voucher->voucher_number,
                'type' => $t->voucher->voucherType->name ?? null,
                'description' => $t->narration ?? $t->voucher->narration,
                'amount' => round($t->debit_amount, 2),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'period' => ['start_date' => $startDate, 'end_date' => $endDate],
                'summary' => [
                    'vat_output' => round($vatOutput, 2),
                    'vat_input' => round($vatInput, 2),
                    'net_payable' => round($vatOutput - $vatInput, 2),
                    'rate' => $tenant->vat_rate ?? 7.50,
                ],
                'output_transactions' => $outputTransactions,
                'input_transactions' => $inputTransactions,
            ],
        ]);
    }

    /**
     * PAYE Tax Report
     */
    public function payeReport(Request $request, Tenant $tenant)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $departmentId = $request->input('department_id');

        $query = PayrollRun::whereHas('payrollPeriod', fn($q) => $q->where('tenant_id', $tenant->id)->whereBetween('start_date', [$startDate, $endDate]))
            ->with(['employee.department', 'payrollPeriod'])
            ->where('payment_status', '!=', 'cancelled');

        if ($departmentId) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $departmentId));
        }

        $payrollRuns = $query->get();
        $grouped = $payrollRuns->groupBy('employee_id');

        $employees = $grouped->map(function($runs, $employeeId) {
            $employee = $runs->first()->employee;
            return [
                'employee_id' => $employeeId,
                'name' => $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name),
                'tin' => $employee->tin,
                'department' => $employee->department->name ?? null,
                'gross_salary' => round($runs->sum('gross_salary'), 2),
                'consolidated_relief' => round($runs->sum('consolidated_relief'), 2),
                'taxable_income' => round($runs->sum('taxable_income'), 2),
                'monthly_tax' => round($runs->sum('monthly_tax'), 2),
            ];
        })->values();

        $departments = Department::where('tenant_id', $tenant->id)->orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => [
                'period' => ['start_date' => $startDate, 'end_date' => $endDate],
                'summary' => [
                    'total_gross' => round($payrollRuns->sum('gross_salary'), 2),
                    'total_relief' => round($payrollRuns->sum('consolidated_relief'), 2),
                    'total_taxable' => round($payrollRuns->sum('taxable_income'), 2),
                    'total_tax' => round($payrollRuns->sum('monthly_tax'), 2),
                    'employee_count' => $grouped->count(),
                ],
                'employees' => $employees,
                'departments' => $departments,
            ],
        ]);
    }

    /**
     * Pension Report
     */
    public function pensionReport(Request $request, Tenant $tenant)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $payrollRuns = PayrollRun::whereHas('payrollPeriod', fn($q) => $q->where('tenant_id', $tenant->id)->whereBetween('start_date', [$startDate, $endDate]))
            ->with(['employee.department', 'payrollPeriod'])
            ->where('payment_status', '!=', 'cancelled')
            ->get();

        $groupedByPFA = $payrollRuns->groupBy(fn($run) => $run->employee->pfa_provider ?? 'Not Assigned');

        $pfaSummary = $groupedByPFA->map(function($runs, $pfa) {
            return [
                'pfa_provider' => $pfa,
                'employee_count' => $runs->unique('employee_id')->count(),
                'employee_contribution' => round($runs->sum('pension_employee'), 2),
                'employer_contribution' => round($runs->sum('pension_employer'), 2),
                'total_contribution' => round($runs->sum('pension_employee') + $runs->sum('pension_employer'), 2),
            ];
        })->values();

        $employees = $payrollRuns->groupBy('employee_id')->map(function($runs, $employeeId) {
            $employee = $runs->first()->employee;
            return [
                'employee_id' => $employeeId,
                'name' => $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name),
                'pfa_provider' => $employee->pfa_provider,
                'pfa_name' => $employee->pfa_name,
                'rsa_pin' => $employee->rsa_pin,
                'pension_pin' => $employee->pension_pin,
                'employee_contribution' => round($runs->sum('pension_employee'), 2),
                'employer_contribution' => round($runs->sum('pension_employer'), 2),
                'total' => round($runs->sum('pension_employee') + $runs->sum('pension_employer'), 2),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => ['start_date' => $startDate, 'end_date' => $endDate],
                'summary' => [
                    'total_employee_contribution' => round($payrollRuns->sum('pension_employee'), 2),
                    'total_employer_contribution' => round($payrollRuns->sum('pension_employer'), 2),
                    'total_contribution' => round($payrollRuns->sum('pension_employee') + $payrollRuns->sum('pension_employer'), 2),
                    'employee_count' => $payrollRuns->unique('employee_id')->count(),
                    'employee_rate' => 8,
                    'employer_rate' => 10,
                ],
                'by_pfa' => $pfaSummary,
                'employees' => $employees,
            ],
        ]);
    }

    /**
     * NSITF Report
     */
    public function nsitfReport(Request $request, Tenant $tenant)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $payrollRuns = PayrollRun::whereHas('payrollPeriod', fn($q) => $q->where('tenant_id', $tenant->id)->whereBetween('start_date', [$startDate, $endDate]))
            ->with(['employee.department', 'payrollPeriod'])
            ->where('payment_status', '!=', 'cancelled')
            ->get();

        $employees = $payrollRuns->groupBy('employee_id')->map(function($runs, $employeeId) {
            $employee = $runs->first()->employee;
            return [
                'employee_id' => $employeeId,
                'name' => $employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name),
                'department' => $employee->department->name ?? null,
                'gross_salary' => round($runs->sum('gross_salary'), 2),
                'nsitf_contribution' => round($runs->sum('nsitf_contribution'), 2),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => ['start_date' => $startDate, 'end_date' => $endDate],
                'summary' => [
                    'total_nsitf' => round($payrollRuns->sum('nsitf_contribution'), 2),
                    'employee_count' => $payrollRuns->unique('employee_id')->count(),
                    'rate' => 1,
                ],
                'employees' => $employees,
            ],
        ]);
    }

    /**
     * Settings - get current tax configuration
     */
    public function settings(Tenant $tenant)
    {
        $taxRates = TaxRate::where('is_active', true)->get(['id', 'name', 'rate', 'type', 'is_default']);

        return response()->json([
            'success' => true,
            'data' => [
                'vat_rate' => $tenant->vat_rate ?? 7.50,
                'vat_registration_number' => $tenant->vat_registration_number,
                'tax_identification_number' => $tenant->tax_identification_number,
                'tax_rates' => $taxRates,
                'statutory_rates' => [
                    'pension_employee' => 8,
                    'pension_employer' => 10,
                    'nsitf' => 1,
                ],
            ],
        ]);
    }

    /**
     * Update tax settings
     */
    public function updateSettings(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'vat_rate' => 'required|numeric|min:0|max:100',
            'vat_registration_number' => 'nullable|string|max:255',
            'tax_identification_number' => 'nullable|string|max:255',
        ]);

        $tenant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tax settings updated successfully.',
            'data' => [
                'vat_rate' => $tenant->vat_rate,
                'vat_registration_number' => $tenant->vat_registration_number,
                'tax_identification_number' => $tenant->tax_identification_number,
            ],
        ]);
    }

    /**
     * List tax filings
     */
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

        $filings = $query->orderByDesc('period_start')->paginate($request->input('per_page', 20));

        $overdueCount = TaxFiling::where('tenant_id', $tenant->id)
            ->where(function($q) {
                $q->where('status', 'overdue')
                  ->orWhere(fn($q2) => $q2->whereIn('status', ['draft', 'filed'])->where('due_date', '<', now()));
            })
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'filings' => $filings->items(),
                'pagination' => [
                    'current_page' => $filings->currentPage(),
                    'last_page' => $filings->lastPage(),
                    'per_page' => $filings->perPage(),
                    'total' => $filings->total(),
                ],
                'summary' => [
                    'paid' => TaxFiling::where('tenant_id', $tenant->id)->where('status', 'paid')->count(),
                    'filed' => TaxFiling::where('tenant_id', $tenant->id)->where('status', 'filed')->count(),
                    'draft' => TaxFiling::where('tenant_id', $tenant->id)->where('status', 'draft')->count(),
                    'overdue' => $overdueCount,
                ],
            ],
        ]);
    }

    /**
     * Create a tax filing record
     */
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

        $filing = TaxFiling::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tax filing recorded successfully.',
            'data' => $filing,
        ], 201);
    }

    /**
     * Update filing status
     */
    public function updateFilingStatus(Request $request, Tenant $tenant, TaxFiling $filing)
    {
        if ($filing->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Filing not found.'], 404);
        }

        $request->validate([
            'status' => 'required|in:draft,filed,paid,overdue',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'filed' && !$filing->filed_date) {
            $data['filed_date'] = now();
            $data['filed_by'] = auth()->id();
        } elseif ($request->status === 'paid') {
            $data['paid_date'] = now();
            if ($request->filled('payment_reference')) {
                $data['payment_reference'] = $request->payment_reference;
            }
            if (!$filing->filed_date) {
                $data['filed_date'] = now();
                $data['filed_by'] = auth()->id();
            }
        }

        $filing->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Filing status updated.',
            'data' => $filing->fresh(),
        ]);
    }

    /**
     * Delete a filing record
     */
    public function destroyFiling(Tenant $tenant, TaxFiling $filing)
    {
        if ($filing->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Filing not found.'], 404);
        }

        $filing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Filing record deleted.',
        ]);
    }
}
