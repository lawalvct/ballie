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
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'overtime_type' => 'required|in:weekday,weekend,holiday,emergency',
            'hourly_rate' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $employee = Employee::where('id', $request->employee_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $data = $request->only([
                'employee_id',
                'overtime_date',
                'start_time',
                'end_time',
                'overtime_type',
                'hourly_rate',
                'reason',
            ]);

            $data['tenant_id'] = $tenantId;
            $data['status'] = 'pending';

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

            $overtime = OvertimeRecord::create($data);

            DB::commit();

            return redirect()
                ->route('tenant.payroll.overtime.show', ['tenant' => $tenant->id, 'id' => $overtime->id])
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
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'overtime_type' => 'required|in:weekday,weekend,holiday,emergency',
            'hourly_rate' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $overtime = OvertimeRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $overtime->fill($request->only([
                'overtime_date',
                'start_time',
                'end_time',
                'overtime_type',
                'hourly_rate',
                'reason',
            ]));

            // Recalculate hours
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

            $overtime->save();

            DB::commit();

            return redirect()
                ->route('tenant.payroll.overtime.show', ['tenant' => $tenant->id, 'id' => $overtime->id])
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
            'payroll_run_id' => 'required|exists:payroll_runs,id',
        ]);

        $tenantId = $tenant->id;
        $overtime = OvertimeRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->where('payment_status', 'pending')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $overtime->markAsPaid($request->payroll_run_id);
            DB::commit();

            return back()->with('success', 'Overtime marked as paid successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to mark as paid: ' . $e->getMessage());
        }
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
                ->route('tenant.payroll.overtime.index', ['tenant' => $tenant->id])
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
}
