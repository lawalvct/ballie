<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\PayrollRunDetail;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PayrollProcessingController extends Controller
{
    /**
     * List payroll periods.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = PayrollPeriod::where('tenant_id', $tenant->id)
            ->withCount('payrollRuns')
            ->orderBy('start_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('date_range')) {
            $range = $request->get('date_range');
            $now = now();

            if ($range === 'current_month') {
                $query->whereBetween('pay_date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
            } elseif ($range === 'last_month') {
                $lastMonth = $now->copy()->subMonth();
                $query->whereBetween('pay_date', [$lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth()]);
            } elseif ($range === 'current_quarter') {
                $query->whereBetween('pay_date', [$now->copy()->firstOfQuarter(), $now->copy()->endOfQuarter()]);
            } elseif ($range === 'current_year') {
                $query->whereYear('pay_date', $now->year);
            }
        }

        $perPage = (int) $request->get('per_page', 20);
        $periods = $query->paginate($perPage);

        $periods->getCollection()->transform(function (PayrollPeriod $period) {
            return $this->formatPeriod($period);
        });

        return response()->json([
            'success' => true,
            'message' => 'Payroll periods retrieved successfully',
            'data' => $periods,
        ]);
    }

    /**
     * Create payroll period.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->periodRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $period = PayrollPeriod::create(array_merge($validator->validated(), [
            'tenant_id' => $tenant->id,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]));

        $period->loadCount('payrollRuns');

        return response()->json([
            'success' => true,
            'message' => 'Payroll period created successfully',
            'data' => [
                'period' => $this->formatPeriod($period),
            ],
        ], 201);
    }

    /**
     * Show payroll period details.
     */
    public function show(Request $request, Tenant $tenant, PayrollPeriod $period)
    {
        if ($period->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period not found',
            ], 404);
        }

        $period->load(['createdBy', 'approvedBy']);
        $period->loadCount('payrollRuns');

        $runsQuery = PayrollRun::with(['employee.department'])
            ->where('payroll_period_id', $period->id)
            ->orderBy('created_at');

        $perPage = (int) $request->get('per_page', 50);
        $runs = $runsQuery->paginate($perPage);

        $runs->getCollection()->transform(function (PayrollRun $run) {
            return $this->formatRun($run);
        });

        return response()->json([
            'success' => true,
            'message' => 'Payroll period retrieved successfully',
            'data' => [
                'period' => $this->formatPeriod($period, true),
                'runs' => $runs,
            ],
        ]);
    }

    /**
     * Update payroll period.
     */
    public function update(Request $request, Tenant $tenant, PayrollPeriod $period)
    {
        if ($period->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period not found',
            ], 404);
        }

        if ($period->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft periods can be updated.',
            ], 409);
        }

        $validator = Validator::make($request->all(), $this->periodRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $period->update($validator->validated());
        $period->loadCount('payrollRuns');

        return response()->json([
            'success' => true,
            'message' => 'Payroll period updated successfully',
            'data' => [
                'period' => $this->formatPeriod($period),
            ],
        ]);
    }

    /**
     * Delete payroll period.
     */
    public function destroy(Tenant $tenant, PayrollPeriod $period)
    {
        if ($period->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period not found',
            ], 404);
        }

        if (!in_array($period->status, ['draft', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period cannot be deleted in current status.',
            ], 409);
        }

        DB::transaction(function () use ($period) {
            PayrollRunDetail::whereIn('payroll_run_id', $period->payrollRuns()->pluck('id'))->delete();
            $period->payrollRuns()->delete();
            $period->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Payroll period deleted successfully',
        ]);
    }

    /**
     * Generate payroll for a period.
     */
    public function generate(Request $request, Tenant $tenant, PayrollPeriod $period)
    {
        if ($period->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period not found',
            ], 404);
        }

        if (!$period->canBeProcessed()) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll cannot be processed in current status.',
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'apply_paye_tax' => 'required|boolean',
            'apply_nsitf' => 'required|boolean',
            'paye_tax_rate' => 'nullable|numeric|min:0|max:100',
            'nsitf_rate' => 'nullable|numeric|min:0|max:100',
            'tax_exemption_reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($period, $data) {
                $period->update([
                    'apply_paye_tax' => $data['apply_paye_tax'],
                    'apply_nsitf' => $data['apply_nsitf'],
                    'paye_tax_rate' => $data['paye_tax_rate'] ?? null,
                    'nsitf_rate' => $data['nsitf_rate'] ?? null,
                    'tax_exemption_reason' => $data['tax_exemption_reason'] ?? null,
                ]);

                $period->generatePayrollForAllEmployees();
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating payroll: ' . $e->getMessage(),
            ], 500);
        }

        $period->refresh();
        $period->loadCount('payrollRuns');

        return response()->json([
            'success' => true,
            'message' => 'Payroll generated successfully',
            'data' => [
                'period' => $this->formatPeriod($period, true),
            ],
        ]);
    }

    /**
     * Approve payroll period.
     */
    public function approve(Tenant $tenant, PayrollPeriod $period)
    {
        if ($period->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period not found',
            ], 404);
        }

        if (!$period->canBeApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll cannot be approved in current status.',
            ], 409);
        }

        try {
            DB::transaction(function () use ($period) {
                $period->createAccountingEntries();
                $period->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving payroll: ' . $e->getMessage(),
            ], 500);
        }

        $period->refresh();
        $period->loadCount('payrollRuns');

        return response()->json([
            'success' => true,
            'message' => 'Payroll approved successfully',
            'data' => [
                'period' => $this->formatPeriod($period, true),
            ],
        ]);
    }

    /**
     * Reset payroll generation for a period.
     */
    public function reset(Tenant $tenant, PayrollPeriod $period)
    {
        if ($period->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period not found',
            ], 404);
        }

        if (in_array($period->status, ['approved', 'paid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Approved or paid payroll cannot be reset.',
            ], 409);
        }

        DB::transaction(function () use ($period) {
            PayrollRunDetail::whereIn('payroll_run_id', $period->payrollRuns()->pluck('id'))->delete();
            $period->payrollRuns()->delete();
            $period->update([
                'status' => 'draft',
                'total_gross' => 0,
                'total_deductions' => 0,
                'total_net' => 0,
                'total_tax' => 0,
                'total_nsitf' => 0,
            ]);
        });

        $period->refresh();
        $period->loadCount('payrollRuns');

        return response()->json([
            'success' => true,
            'message' => 'Payroll reset successfully',
            'data' => [
                'period' => $this->formatPeriod($period),
            ],
        ]);
    }

    /**
     * Export bank file (CSV).
     */
    public function exportBankFile(Tenant $tenant, PayrollPeriod $period)
    {
        if ($period->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period not found',
            ], 404);
        }

        if ($period->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Payroll must be approved before exporting bank file.',
            ], 409);
        }

        $payrollRuns = $period->payrollRuns()->with('employee')->get();
        $filename = "payroll_bank_file_{$period->name}_" . now()->format('Y_m_d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payrollRuns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Employee Name', 'Account Number', 'Bank Name', 'Amount']);

            foreach ($payrollRuns as $run) {
                fputcsv($file, [
                    $run->employee->full_name,
                    $run->employee->account_number,
                    $run->employee->bank_name,
                    $run->net_salary,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function periodRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'pay_date' => 'required|date|after_or_equal:end_date',
            'type' => 'required|in:monthly,weekly,bi_weekly,contract',
        ];
    }

    private function formatPeriod(PayrollPeriod $period, bool $withMeta = false): array
    {
        $data = [
            'id' => $period->id,
            'name' => $period->name,
            'type' => $period->type,
            'status' => $period->status,
            'start_date' => $period->start_date?->toDateString(),
            'end_date' => $period->end_date?->toDateString(),
            'pay_date' => $period->pay_date?->toDateString(),
            'total_gross' => $period->total_gross,
            'total_deductions' => $period->total_deductions,
            'total_net' => $period->total_net,
            'total_tax' => $period->total_tax,
            'total_nsitf' => $period->total_nsitf,
            'payroll_runs_count' => $period->payroll_runs_count ?? 0,
            'created_at' => $period->created_at?->toDateTimeString(),
            'updated_at' => $period->updated_at?->toDateTimeString(),
        ];

        if ($withMeta) {
            $data['apply_paye_tax'] = (bool) $period->apply_paye_tax;
            $data['apply_nsitf'] = (bool) $period->apply_nsitf;
            $data['paye_tax_rate'] = $period->paye_tax_rate;
            $data['nsitf_rate'] = $period->nsitf_rate;
            $data['tax_exemption_reason'] = $period->tax_exemption_reason;
            $data['created_by'] = $period->createdBy?->name;
            $data['approved_by'] = $period->approvedBy?->name;
            $data['approved_at'] = $period->approved_at?->toDateTimeString();
        }

        return $data;
    }

    private function formatRun(PayrollRun $run): array
    {
        return [
            'id' => $run->id,
            'employee_id' => $run->employee_id,
            'employee_name' => $run->employee?->full_name,
            'employee_number' => $run->employee?->employee_number,
            'department_name' => $run->employee?->department?->name,
            'basic_salary' => $run->basic_salary,
            'total_allowances' => $run->total_allowances,
            'total_deductions' => $run->total_deductions,
            'monthly_tax' => $run->monthly_tax,
            'net_salary' => $run->net_salary,
            'payment_status' => $run->payment_status,
            'created_at' => $run->created_at?->toDateTimeString(),
        ];
    }
}
