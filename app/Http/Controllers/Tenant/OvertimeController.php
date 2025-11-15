<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\OvertimeRecord;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;

        $query = OvertimeRecord::with(['employee.department', 'approver'])
            ->where('tenant_id', $tenantId);

        // Filters
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('overtime_type')) {
            $query->where('overtime_type', $request->overtime_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->where('overtime_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('overtime_date', '<=', $request->date_to);
        }

        // Default to current month
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereYear('overtime_date', date('Y'))
                ->whereMonth('overtime_date', date('m'));
        }

        $overtimes = $query->orderBy('overtime_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $departments = Department::where('tenant_id', $tenantId)->get();
        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        // Summary statistics
        $summary = [
            'pending_count' => OvertimeRecord::where('tenant_id', $tenantId)
                ->where('status', 'pending')
                ->count(),
            'pending_amount' => OvertimeRecord::where('tenant_id', $tenantId)
                ->where('status', 'pending')
                ->sum('total_amount'),
            'approved_unpaid_count' => OvertimeRecord::where('tenant_id', $tenantId)
                ->where('status', 'approved')
                ->where('is_paid', false)
                ->count(),
            'approved_unpaid_amount' => OvertimeRecord::where('tenant_id', $tenantId)
                ->where('status', 'approved')
                ->where('is_paid', false)
                ->sum('total_amount'),
        ];

        return view('tenant.overtime.index', compact(
            'overtimes',
            'departments',
            'employees',
            'summary'
        ));
    }

    public function create(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;

        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('tenant.overtime.create', compact('employees'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'overtime_date' => 'required|date',
            'calculation_method' => 'required|in:hourly,fixed',
            'start_time' => 'required_if:calculation_method,hourly|nullable|date_format:H:i',
            'end_time' => 'required_if:calculation_method,hourly|nullable|date_format:H:i|after:start_time',
            'overtime_type' => 'required_if:calculation_method,hourly|nullable|in:weekday,weekend,holiday,emergency',
            'hourly_rate' => 'required_if:calculation_method,hourly|nullable|numeric|min:0',
            'fixed_amount' => 'required_if:calculation_method,fixed|nullable|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $employee = Employee::where('id', $request->employee_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $data = [
                'tenant_id' => $tenantId,
                'employee_id' => $request->employee_id,
                'overtime_date' => $request->overtime_date,
                'calculation_method' => $request->calculation_method,
                'reason' => $request->reason,
                'work_description' => $request->work_description,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ];

            if ($request->calculation_method === 'hourly') {
                // Hourly calculation
                $data['start_time'] = $request->start_time;
                $data['end_time'] = $request->end_time;
                $data['overtime_type'] = $request->overtime_type;
                $data['hourly_rate'] = $request->hourly_rate;

                // Calculate hours
                $start = \Carbon\Carbon::parse($request->overtime_date . ' ' . $request->start_time);
                $end = \Carbon\Carbon::parse($request->overtime_date . ' ' . $request->end_time);
                $data['total_hours'] = $end->diffInHours($start, true);

                // Multiplier based on type
                $multipliers = [
                    'weekday' => 1.5,
                    'weekend' => 2.0,
                    'holiday' => 2.5,
                    'emergency' => 2.0,
                ];
                $data['multiplier'] = $multipliers[$request->overtime_type];
            } else {
                // Fixed amount
                $data['total_amount'] = $request->fixed_amount;
            }

            $overtime = OvertimeRecord::create($data);

            DB::commit();

            return redirect()
                ->route('tenant.payroll.overtime.show', ['tenant' => $tenant->slug, 'id' => $overtime->id])
                ->with('success', 'Overtime record created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create overtime: ' . $e->getMessage());
        }
    }

    public function show(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;

        $overtime = OvertimeRecord::with([
            'employee.department',
            'approver',
            'rejector',
            'payrollRun'
        ])
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        return view('tenant.overtime.show', compact('overtime'));
    }

    public function edit(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;

        $overtime = OvertimeRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('tenant.overtime.edit', compact('overtime', 'employees'));
    }

    public function update(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'overtime_date' => 'required|date',
            'calculation_method' => 'required|in:hourly,fixed',
            'start_time' => 'required_if:calculation_method,hourly|nullable|date_format:H:i',
            'end_time' => 'required_if:calculation_method,hourly|nullable|date_format:H:i|after:start_time',
            'overtime_type' => 'required_if:calculation_method,hourly|nullable|in:weekday,weekend,holiday,emergency',
            'hourly_rate' => 'required_if:calculation_method,hourly|nullable|numeric|min:0',
            'fixed_amount' => 'required_if:calculation_method,fixed|nullable|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $overtime = OvertimeRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $overtime->calculation_method = $request->calculation_method;
            $overtime->overtime_date = $request->overtime_date;
            $overtime->reason = $request->reason;
            $overtime->work_description = $request->work_description;

            if ($request->calculation_method === 'hourly') {
                // Hourly calculation
                $overtime->start_time = $request->start_time;
                $overtime->end_time = $request->end_time;
                $overtime->overtime_type = $request->overtime_type;
                $overtime->hourly_rate = $request->hourly_rate;

                // Calculate hours
                $start = \Carbon\Carbon::parse($request->overtime_date . ' ' . $request->start_time);
                $end = \Carbon\Carbon::parse($request->overtime_date . ' ' . $request->end_time);
                $overtime->total_hours = $end->diffInHours($start, true);

                // Update multiplier
                $multipliers = [
                    'weekday' => 1.5,
                    'weekend' => 2.0,
                    'holiday' => 2.5,
                    'emergency' => 2.0,
                ];
                $overtime->multiplier = $multipliers[$request->overtime_type];
            } else {
                // Fixed amount
                $overtime->total_amount = $request->fixed_amount;
                // Clear hourly fields but keep overtime_type with default
                $overtime->start_time = null;
                $overtime->end_time = null;
                $overtime->total_hours = null;
                $overtime->hourly_rate = null;
                $overtime->multiplier = null;
                // Keep weekday as default for fixed amount (column doesn't allow null)
                if (!$overtime->overtime_type) {
                    $overtime->overtime_type = 'weekday';
                }
            }

            $overtime->updated_by = auth()->id();
            $overtime->save();

            DB::commit();

            return redirect()
                ->route('tenant.payroll.overtime.show', ['tenant' => $tenant->slug, 'id' => $overtime->id])
                ->with('success', 'Overtime record updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update overtime: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'approved_hours' => 'nullable|numeric|min:0',
            'approval_remarks' => 'nullable|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $overtime = OvertimeRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $overtime->approve(
                Auth::id(),
                $request->approved_hours,
                $request->approval_remarks
            );

            DB::commit();

            return back()->with('success', 'Overtime approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve overtime: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $overtime = OvertimeRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $overtime->reject(Auth::id(), $request->rejection_reason);
            DB::commit();

            return back()->with('success', 'Overtime rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject overtime: ' . $e->getMessage());
        }
    }

    public function markPaid(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'payroll_run_id' => 'nullable|exists:payroll_runs,id',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|in:cash,bank,voucher',
            'reference_number' => 'nullable|string|max:255',
            'create_voucher' => 'nullable|boolean',
            'cash_bank_account_id' => 'required_if:create_voucher,true|nullable|exists:ledger_accounts,id',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $overtime = OvertimeRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->where('is_paid', false)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $paymentDate = $request->payment_date ? \Carbon\Carbon::parse($request->payment_date) : now();

            // Create accounting voucher if requested
            $voucherNumber = null;
            if ($request->create_voucher && $request->cash_bank_account_id) {
                $voucherNumber = $this->createPaymentVoucher(
                    $tenant,
                    $overtime,
                    $request->cash_bank_account_id,
                    $paymentDate,
                    $request->reference_number,
                    $request->payment_notes
                );
            }

            // Mark overtime as paid
            $overtime->is_paid = true;
            $overtime->paid_date = $paymentDate;
            $overtime->status = 'paid';

            if ($request->payroll_run_id) {
                $overtime->payroll_run_id = $request->payroll_run_id;
            }

            $overtime->save();

            DB::commit();

            $message = 'Overtime marked as paid successfully.';
            if ($voucherNumber) {
                $message .= " Payment voucher {$voucherNumber} created.";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to mark as paid: ' . $e->getMessage());
        }
    }

    /**
     * Create payment voucher for overtime payment
     */
    private function createPaymentVoucher(
        Tenant $tenant,
        OvertimeRecord $overtime,
        int $cashBankAccountId,
        $paymentDate,
        $referenceNumber = null,
        $notes = null
    ): string {
        // Get voucher type for payment
        $voucherType = \App\Models\VoucherType::where('tenant_id', $tenant->id)
            ->where('code', 'PV')
            ->first();

        if (!$voucherType) {
            throw new \Exception('Payment voucher type not found. Please set up voucher types.');
        }

        // Get or create Overtime Expense ledger account
        $overtimeExpenseAccount = \App\Models\LedgerAccount::where('tenant_id', $tenant->id)
            ->where('code', 'EXP-OT')
            ->first();

        if (!$overtimeExpenseAccount) {
            // Get or create Expense account group
            $expenseGroup = \App\Models\AccountGroup::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => 'Expenses',
                ],
                [
                    'code' => 'EXP',
                    'description' => 'Expense accounts',
                    'is_system' => true,
                ]
            );

            // Create the overtime expense account
            $overtimeExpenseAccount = \App\Models\LedgerAccount::create([
                'tenant_id' => $tenant->id,
                'account_group_id' => $expenseGroup->id,
                'code' => 'EXP-OT',
                'name' => 'Overtime Expenses',
                'account_type' => 'expense',
                'is_system_account' => true,
                'is_active' => true,
                'description' => 'Employee overtime payments',
                'created_by' => Auth::id(),
            ]);
        }

        // Get cash/bank account
        $cashBankAccount = \App\Models\LedgerAccount::findOrFail($cashBankAccountId);

        // Create voucher
        $voucher = \App\Models\Voucher::create([
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

        // Create voucher entries (Payment: Credit Cash/Bank, Debit Expense)
        \App\Models\VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $overtimeExpenseAccount->id,
            'debit_amount' => $overtime->total_amount,
            'credit_amount' => 0,
            'particulars' => "Overtime expense - {$overtime->employee->full_name}",
        ]);

        \App\Models\VoucherEntry::create([
            'voucher_id' => $voucher->id,
            'ledger_account_id' => $cashBankAccount->id,
            'debit_amount' => 0,
            'credit_amount' => $overtime->total_amount,
            'particulars' => "Payment for overtime - {$overtime->overtime_number}",
        ]);

        return $voucher->voucher_number;
    }

    public function bulkApprove(Request $request, Tenant $tenant)
    {
        $request->validate([
            'overtime_ids' => 'required|array',
            'overtime_ids.*' => 'exists:overtime_records,id',
        ]);

        $tenantId = $tenant->id;
        $success = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->overtime_ids as $overtimeId) {
                $overtime = OvertimeRecord::where('id', $overtimeId)
                    ->where('tenant_id', $tenantId)
                    ->where('status', 'pending')
                    ->first();

                if ($overtime) {
                    $overtime->approve(Auth::id());
                    $success++;
                } else {
                    $errors[] = "Overtime #$overtimeId not found or not pending";
                }
            }

            DB::commit();

            $message = "$success overtime record(s) approved successfully.";
            if (count($errors) > 0) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve overtime records: ' . $e->getMessage());
        }
    }

    public function destroy(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;

        $overtime = OvertimeRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $overtime->delete();
            DB::commit();

            return redirect()
                ->route('tenant.payroll.overtime.index', ['tenant' => $tenant->slug])
                ->with('success', 'Overtime record deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete overtime: ' . $e->getMessage());
        }
    }

    public function report(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $departmentId = $request->input('department_id');

        $query = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['department', 'overtimeRecords' => function($q) use ($year, $month) {
                $q->whereYear('overtime_date', $year)
                    ->whereMonth('overtime_date', $month)
                    ->where('status', 'approved');
            }]);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->get()->map(function($employee) {
            $overtimes = $employee->overtimeRecords;
            return [
                'employee' => $employee,
                'total_hours' => $overtimes->sum('total_hours'),
                'total_amount' => $overtimes->sum('total_amount'),
                'record_count' => $overtimes->count(),
                'paid_amount' => $overtimes->where('is_paid', true)->sum('total_amount'),
                'unpaid_amount' => $overtimes->where('is_paid', false)->sum('total_amount'),
            ];
        })->filter(fn($data) => $data['record_count'] > 0);

        $departments = Department::where('tenant_id', $tenantId)->get();

        return view('tenant.overtime.report', compact(
            'employees',
            'departments',
            'month',
            'year'
        ));
    }

    /**
     * Download overtime payment slip as PDF
     */
    public function downloadPaymentSlip(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;

        $overtime = OvertimeRecord::with([
            'employee.department',
            'approver',
            'rejector',
            'payrollRun'
        ])
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Generate PDF
        $pdf = \PDF::loadView('tenant.overtime.payment-slip-pdf', compact('tenant', 'overtime'));

        $fileName = 'overtime_payment_slip_' .
                    $overtime->employee->employee_number . '_' .
                    $overtime->overtime_number . '_' .
                    \Carbon\Carbon::parse($overtime->overtime_date)->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }
}
