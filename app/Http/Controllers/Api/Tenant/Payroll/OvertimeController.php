<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\AccountGroup;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LedgerAccount;
use App\Models\OvertimeRecord;
use App\Models\Tenant;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OvertimeController extends Controller
{
    /**
     * List overtime records with summary and filters.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = OvertimeRecord::with(['employee.department', 'approver', 'rejector', 'payrollRun'])
            ->where('tenant_id', $tenant->id);

        if ($request->filled('department_id')) {
            $departmentId = $request->get('department_id');
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->get('employee_id'));
        }

        if ($request->filled('overtime_type')) {
            $query->where('overtime_type', $request->get('overtime_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('payment_status')) {
            $paymentStatus = $request->get('payment_status');
            if ($paymentStatus === 'paid') {
                $query->where('is_paid', true);
            } elseif ($paymentStatus === 'unpaid') {
                $query->where('is_paid', false)->where('status', 'approved');
            }
        }

        if ($request->filled('date_from')) {
            $query->where('overtime_date', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->where('overtime_date', '<=', $request->get('date_to'));
        }

        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereYear('overtime_date', date('Y'))
                ->whereMonth('overtime_date', date('m'));
        }

        $perPage = (int) $request->get('per_page', 20);
        $overtimes = $query->orderBy('overtime_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $overtimes->getCollection()->transform(function (OvertimeRecord $record) {
            return $this->formatOvertime($record);
        });

        $summary = [
            'pending_count' => OvertimeRecord::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->count(),
            'pending_amount' => OvertimeRecord::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->sum('total_amount'),
            'approved_unpaid_count' => OvertimeRecord::where('tenant_id', $tenant->id)
                ->where('status', 'approved')
                ->where('is_paid', false)
                ->count(),
            'approved_unpaid_amount' => OvertimeRecord::where('tenant_id', $tenant->id)
                ->where('status', 'approved')
                ->where('is_paid', false)
                ->sum('total_amount'),
            'total_records' => OvertimeRecord::where('tenant_id', $tenant->id)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Overtime records retrieved successfully',
            'data' => [
                'summary' => $summary,
                'records' => $overtimes,
            ],
        ]);
    }

    /**
     * Create overtime record.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = Employee::where('id', $request->get('employee_id'))
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $data = [
                'tenant_id' => $tenant->id,
                'employee_id' => $request->get('employee_id'),
                'overtime_date' => $request->get('overtime_date'),
                'calculation_method' => $request->get('calculation_method'),
                'reason' => $request->get('reason'),
                'work_description' => $request->get('work_description'),
                'status' => 'pending',
                'created_by' => Auth::id(),
            ];

            if ($request->get('calculation_method') === 'hourly') {
                $start = Carbon::parse($request->get('overtime_date') . ' ' . $request->get('start_time'));
                $end = Carbon::parse($request->get('overtime_date') . ' ' . $request->get('end_time'));
                if ($end->lessThan($start)) {
                    $end->addDay();
                }

                $multipliers = [
                    'weekday' => 1.5,
                    'weekend' => 2.0,
                    'holiday' => 2.5,
                    'emergency' => 2.0,
                ];

                $data['start_time'] = $start;
                $data['end_time'] = $end;
                $data['overtime_type'] = $request->get('overtime_type');
                $data['hourly_rate'] = $request->get('hourly_rate');
                $data['total_hours'] = $end->diffInHours($start, true);
                $data['multiplier'] = $multipliers[$request->get('overtime_type')] ?? 1;
            } else {
                $data['total_amount'] = $request->get('fixed_amount');
                $data['overtime_type'] = $request->get('overtime_type', 'weekday');
            }

            $overtime = OvertimeRecord::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Overtime record created successfully',
                'data' => [
                    'overtime' => $this->formatOvertime($overtime->fresh(['employee.department', 'approver', 'rejector', 'payrollRun'])),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create overtime record',
            ], 500);
        }
    }

    /**
     * Show overtime record.
     */
    public function show(Tenant $tenant, OvertimeRecord $overtime)
    {
        if ($overtime->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Overtime record not found',
            ], 404);
        }

        $overtime->load(['employee.department', 'approver', 'rejector', 'payrollRun']);

        return response()->json([
            'success' => true,
            'message' => 'Overtime record retrieved successfully',
            'data' => [
                'overtime' => $this->formatOvertime($overtime),
            ],
        ]);
    }

    /**
     * Update overtime record.
     */
    public function update(Request $request, Tenant $tenant, OvertimeRecord $overtime)
    {
        if ($overtime->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Overtime record not found',
            ], 404);
        }

        if ($overtime->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending overtime records can be updated',
            ], 409);
        }

        $validator = Validator::make($request->all(), $this->rules(false));

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $overtime->calculation_method = $request->get('calculation_method');
            $overtime->overtime_date = $request->get('overtime_date');
            $overtime->reason = $request->get('reason');
            $overtime->work_description = $request->get('work_description');

            if ($request->get('calculation_method') === 'hourly') {
                $start = Carbon::parse($request->get('overtime_date') . ' ' . $request->get('start_time'));
                $end = Carbon::parse($request->get('overtime_date') . ' ' . $request->get('end_time'));
                if ($end->lessThan($start)) {
                    $end->addDay();
                }

                $multipliers = [
                    'weekday' => 1.5,
                    'weekend' => 2.0,
                    'holiday' => 2.5,
                    'emergency' => 2.0,
                ];

                $overtime->start_time = $start;
                $overtime->end_time = $end;
                $overtime->overtime_type = $request->get('overtime_type');
                $overtime->hourly_rate = $request->get('hourly_rate');
                $overtime->total_hours = $end->diffInHours($start, true);
                $overtime->multiplier = $multipliers[$request->get('overtime_type')] ?? 1;
            } else {
                $overtime->total_amount = $request->get('fixed_amount');
                $overtime->start_time = null;
                $overtime->end_time = null;
                $overtime->total_hours = null;
                $overtime->hourly_rate = null;
                $overtime->multiplier = null;
                if (!$overtime->overtime_type) {
                    $overtime->overtime_type = 'weekday';
                }
            }

            $overtime->updated_by = Auth::id();
            $overtime->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Overtime record updated successfully',
                'data' => [
                    'overtime' => $this->formatOvertime($overtime->fresh(['employee.department', 'approver', 'rejector', 'payrollRun'])),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update overtime record',
            ], 500);
        }
    }

    /**
     * Delete overtime record.
     */
    public function destroy(Tenant $tenant, OvertimeRecord $overtime)
    {
        if ($overtime->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Overtime record not found',
            ], 404);
        }

        $overtime->delete();

        return response()->json([
            'success' => true,
            'message' => 'Overtime record deleted successfully',
        ]);
    }

    /**
     * Approve overtime record.
     */
    public function approve(Request $request, Tenant $tenant, OvertimeRecord $overtime)
    {
        if ($overtime->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Overtime record not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'approved_hours' => 'nullable|numeric|min:0',
            'approval_remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$overtime->approve(Auth::id(), $request->get('approved_hours'), $request->get('approval_remarks'))) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending overtime records can be approved',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Overtime approved successfully',
            'data' => [
                'overtime' => $this->formatOvertime($overtime->fresh(['employee.department', 'approver', 'rejector', 'payrollRun'])),
            ],
        ]);
    }

    /**
     * Reject overtime record.
     */
    public function reject(Request $request, Tenant $tenant, OvertimeRecord $overtime)
    {
        if ($overtime->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Overtime record not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$overtime->reject(Auth::id(), $request->get('rejection_reason'))) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending overtime records can be rejected',
            ], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'Overtime rejected successfully',
            'data' => [
                'overtime' => $this->formatOvertime($overtime->fresh(['employee.department', 'approver', 'rejector', 'payrollRun'])),
            ],
        ]);
    }

    /**
     * Mark overtime as paid.
     */
    public function markPaid(Request $request, Tenant $tenant, OvertimeRecord $overtime)
    {
        if ($overtime->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Overtime record not found',
            ], 404);
        }

        if ($overtime->status !== 'approved' || $overtime->is_paid) {
            return response()->json([
                'success' => false,
                'message' => 'Only approved and unpaid overtime records can be marked as paid',
            ], 409);
        }

        $validator = Validator::make($request->all(), [
            'payroll_run_id' => 'nullable|exists:payroll_runs,id',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|in:cash,bank,voucher',
            'reference_number' => 'nullable|string|max:255',
            'create_voucher' => 'nullable|boolean',
            'cash_bank_account_id' => 'required_if:create_voucher,true|nullable|exists:ledger_accounts,id',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $paymentDate = $request->get('payment_date')
                ? Carbon::parse($request->get('payment_date'))
                : now();

            $voucherNumber = null;
            if ($request->boolean('create_voucher') && $request->filled('cash_bank_account_id')) {
                $voucherNumber = $this->createPaymentVoucher(
                    $tenant,
                    $overtime,
                    (int) $request->get('cash_bank_account_id'),
                    $paymentDate,
                    $request->get('reference_number'),
                    $request->get('payment_notes')
                );
            }

            $overtime->is_paid = true;
            $overtime->paid_date = $paymentDate;
            $overtime->status = 'paid';

            if ($request->filled('payroll_run_id')) {
                $overtime->payroll_run_id = $request->get('payroll_run_id');
            }

            $overtime->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Overtime marked as paid successfully',
                'data' => [
                    'overtime' => $this->formatOvertime($overtime->fresh(['employee.department', 'approver', 'rejector', 'payrollRun'])),
                    'voucher_number' => $voucherNumber,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark overtime as paid',
            ], 500);
        }
    }

    /**
     * Bulk approve overtime records.
     */
    public function bulkApprove(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'overtime_ids' => 'required|array',
            'overtime_ids.*' => 'exists:overtime_records,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $success = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->get('overtime_ids', []) as $overtimeId) {
                $overtime = OvertimeRecord::where('tenant_id', $tenant->id)
                    ->where('id', $overtimeId)
                    ->where('status', 'pending')
                    ->first();

                if (!$overtime) {
                    $errors[] = "Overtime {$overtimeId} not found or not pending";
                    continue;
                }

                $overtime->approve(Auth::id());
                $success++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$success} overtime record(s) approved successfully",
                'data' => [
                    'approved_count' => $success,
                    'errors' => $errors,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve overtime records',
            ], 500);
        }
    }

    /**
     * Monthly overtime report.
     */
    public function report(Request $request, Tenant $tenant)
    {
        $month = (int) $request->get('month', date('m'));
        $year = (int) $request->get('year', date('Y'));
        $departmentId = $request->get('department_id');

        $query = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with(['department', 'overtimeRecords' => function ($q) use ($year, $month) {
                $q->whereYear('overtime_date', $year)
                    ->whereMonth('overtime_date', $month);
            }]);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->get()->map(function (Employee $employee) {
            $records = $employee->overtimeRecords;
            $totalAmount = $records->sum('total_amount');
            $paidAmount = $records->where('is_paid', true)->sum('total_amount');
            $unpaidAmount = $records->where('is_paid', false)->sum('total_amount');

            return [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'employee_number' => $employee->employee_number,
                    'department_name' => $employee->department?->name,
                ],
                'record_count' => $records->count(),
                'total_hours' => (float) $records->sum('total_hours'),
                'total_amount' => (float) $totalAmount,
                'paid_amount' => (float) $paidAmount,
                'unpaid_amount' => (float) $unpaidAmount,
            ];
        })->filter(fn ($data) => $data['record_count'] > 0)->values();

        $summary = [
            'total_employees' => $employees->count(),
            'total_records' => $employees->sum('record_count'),
            'total_hours' => (float) $employees->sum('total_hours'),
            'total_amount' => (float) $employees->sum('total_amount'),
            'paid_amount' => (float) $employees->sum('paid_amount'),
            'unpaid_amount' => (float) $employees->sum('unpaid_amount'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Overtime report retrieved successfully',
            'data' => [
                'year' => $year,
                'month' => $month,
                'summary' => $summary,
                'employees' => $employees,
            ],
        ]);
    }

    /**
     * Download overtime payment slip PDF.
     */
    public function downloadPaymentSlip(Tenant $tenant, OvertimeRecord $overtime)
    {
        if ($overtime->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Overtime record not found',
            ], 404);
        }

        $overtime->load(['employee.department', 'approver', 'rejector', 'payrollRun']);

        $pdf = \PDF::loadView('tenant.overtime.payment-slip-pdf', [
            'tenant' => $tenant,
            'overtime' => $overtime,
        ]);

        $fileName = 'overtime_payment_slip_' .
            $overtime->employee->employee_number . '_' .
            $overtime->overtime_number . '_' .
            Carbon::parse($overtime->overtime_date)->format('Y-m-d') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $fileName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function rules(bool $requireEmployee = true): array
    {
        return [
            'employee_id' => ($requireEmployee ? 'required|' : 'nullable|') . 'exists:employees,id',
            'overtime_date' => 'required|date',
            'calculation_method' => 'required|in:hourly,fixed',
            'start_time' => 'required_if:calculation_method,hourly|nullable|date_format:H:i',
            'end_time' => 'required_if:calculation_method,hourly|nullable|date_format:H:i|after:start_time',
            'overtime_type' => 'required_if:calculation_method,hourly|nullable|in:weekday,weekend,holiday,emergency',
            'hourly_rate' => 'required_if:calculation_method,hourly|nullable|numeric|min:0',
            'fixed_amount' => 'required_if:calculation_method,fixed|nullable|numeric|min:0',
            'reason' => 'required|string|max:500',
            'work_description' => 'nullable|string|max:500',
        ];
    }

    private function formatOvertime(OvertimeRecord $overtime): array
    {
        return [
            'id' => $overtime->id,
            'overtime_number' => $overtime->overtime_number,
            'employee_id' => $overtime->employee_id,
            'employee_name' => $overtime->employee?->full_name,
            'employee_number' => $overtime->employee?->employee_number,
            'department_name' => $overtime->employee?->department?->name,
            'overtime_date' => $overtime->overtime_date?->toDateString(),
            'calculation_method' => $overtime->calculation_method,
            'start_time' => $overtime->start_time?->format('H:i'),
            'end_time' => $overtime->end_time?->format('H:i'),
            'total_hours' => $overtime->total_hours,
            'hourly_rate' => $overtime->hourly_rate,
            'multiplier' => $overtime->multiplier,
            'total_amount' => $overtime->total_amount,
            'overtime_type' => $overtime->overtime_type,
            'reason' => $overtime->reason,
            'work_description' => $overtime->work_description,
            'status' => $overtime->status,
            'is_paid' => (bool) $overtime->is_paid,
            'paid_date' => $overtime->paid_date?->toDateString(),
            'approved_by' => $overtime->approver?->name,
            'approved_at' => $overtime->approved_at?->toDateTimeString(),
            'approval_remarks' => $overtime->approval_remarks,
            'rejected_by' => $overtime->rejector?->name,
            'rejected_at' => $overtime->rejected_at?->toDateTimeString(),
            'rejection_reason' => $overtime->rejection_reason,
            'created_at' => $overtime->created_at?->toDateTimeString(),
            'updated_at' => $overtime->updated_at?->toDateTimeString(),
        ];
    }

    private function createPaymentVoucher(
        Tenant $tenant,
        OvertimeRecord $overtime,
        int $cashBankAccountId,
        $paymentDate,
        $referenceNumber = null,
        $notes = null
    ): string {
        $voucherType = VoucherType::where('tenant_id', $tenant->id)
            ->where('code', 'PV')
            ->first();

        if (!$voucherType) {
            throw new \Exception('Payment voucher type not found. Please set up voucher types.');
        }

        $overtimeExpenseAccount = LedgerAccount::where('tenant_id', $tenant->id)
            ->where('code', 'EXP-OT')
            ->first();

        if (!$overtimeExpenseAccount) {
            $expenseGroup = AccountGroup::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'code' => 'EXP',
                ],
                [
                    'name' => 'Expenses',
                    'type' => 'expense',
                    'parent_id' => null,
                ]
            );

            $overtimeExpenseAccount = LedgerAccount::create([
                'tenant_id' => $tenant->id,
                'account_group_id' => $expenseGroup->id,
                'code' => 'EXP-OT',
                'name' => 'Overtime Expense',
                'type' => 'expense',
                'balance' => 0,
                'description' => 'Overtime payments',
                'created_by' => Auth::id(),
            ]);
        }

        $cashBankAccount = LedgerAccount::findOrFail($cashBankAccountId);

        $voucher = Voucher::create([
            'tenant_id' => $tenant->id,
            'voucher_type_id' => $voucherType->id,
            'voucher_number' => $voucherType->getNextVoucherNumber(),
            'voucher_date' => $paymentDate,
            'reference_number' => $referenceNumber ?? $overtime->overtime_number,
            'narration' => $notes ?? "Overtime payment for {$overtime->employee->full_name} - {$overtime->overtime_number}",
            'total_amount' => $overtime->total_amount,
            'status' => 'posted',
            'created_by' => Auth::id(),
            'posted_by' => Auth::id(),
            'posted_at' => now(),
        ]);

        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $overtimeExpenseAccount->id,
            'debit_amount' => $overtime->total_amount,
            'credit_amount' => 0,
            'particulars' => "Overtime expense - {$overtime->employee->full_name}",
        ]);

        VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $cashBankAccount->id,
            'debit_amount' => 0,
            'credit_amount' => $overtime->total_amount,
            'particulars' => "Payment for overtime - {$overtime->overtime_number}",
        ]);

        return $voucher->voucher_number;
    }
}
