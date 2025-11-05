<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeave;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;

        $query = EmployeeLeave::with(['employee.department', 'leaveType', 'approvedBy', 'rejectedBy'])
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

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(20);

        $departments = Department::where('tenant_id', $tenantId)->get();
        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();
        $leaveTypes = LeaveType::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('tenant.leaves.index', compact(
            'leaves',
            'departments',
            'employees',
            'leaveTypes'
        ));
    }

    public function create(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;

        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $leaveTypes = LeaveType::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('tenant.leaves.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'reliever_id' => 'nullable|exists:employees,id',
            'contact_during_leave' => 'nullable|string|max:200',
            'document_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $tenantId = $tenant->id;
        $employee = Employee::where('id', $request->employee_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $leaveType = LeaveType::find($request->leave_type_id);

        // Calculate working days
        $workingDays = EmployeeLeave::calculateWorkingDays(
            $request->start_date,
            $request->end_date,
            $leaveType->exclude_weekends,
            $leaveType->exclude_holidays,
            $tenantId
        );

        // Check if employee has sufficient leave balance
        $balance = $employee->getLeaveBalance($request->leave_type_id);
        if (!$balance || !$balance->hasAvailableDays($workingDays)) {
            return back()->with('error', 'Insufficient leave balance. Available: ' .
                ($balance ? $balance->available_days : 0) . ' days, Required: ' . $workingDays . ' days');
        }

        // Check if document is required
        if ($leaveType->requires_document && !$request->hasFile('document_path')) {
            return back()->with('error', 'This leave type requires supporting document.');
        }

        DB::beginTransaction();
        try {
            $data = $request->only([
                'employee_id',
                'leave_type_id',
                'start_date',
                'end_date',
                'reason',
                'reliever_id',
                'contact_during_leave',
            ]);

            $data['tenant_id'] = $tenantId;
            $data['working_days'] = $workingDays;
            $data['status'] = 'pending';
            $data['applied_by'] = Auth::id();

            // Handle document upload
            if ($request->hasFile('document_path')) {
                $path = $request->file('document_path')->store('leave-documents', 'public');
                $data['document_path'] = $path;
            }

            $leave = EmployeeLeave::create($data);

            // Update leave balance pending days
            $balance->pending_days += $workingDays;
            $balance->save();

            DB::commit();

            return redirect()
                ->route('tenant.leaves.show', ['tenant' => $tenant->id, 'leave' => $leave->id])
                ->with('success', 'Leave application submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit leave: ' . $e->getMessage());
        }
    }

    public function show(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;

        $leave = EmployeeLeave::with([
            'employee.department',
            'leaveType',
            'reliever',
            'appliedByUser',
            'approvedBy',
            'rejectedBy',
            'cancelledBy'
        ])
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        return view('tenant.leaves.show', compact('leave'));
    }

    public function edit(Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;

        $leave = EmployeeLeave::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $leaveTypes = LeaveType::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('tenant.leaves.edit', compact('leave', 'employees', 'leaveTypes'));
    }

    public function update(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'reliever_id' => 'nullable|exists:employees,id',
            'contact_during_leave' => 'nullable|string|max:200',
            'document_path' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $tenantId = $tenant->id;
        $leave = EmployeeLeave::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        $leaveType = LeaveType::find($request->leave_type_id);

        // Calculate new working days
        $workingDays = EmployeeLeave::calculateWorkingDays(
            $request->start_date,
            $request->end_date,
            $leaveType->exclude_weekends,
            $leaveType->exclude_holidays,
            $tenantId
        );

        // Get balance and check
        $balance = $leave->employee->getLeaveBalance($request->leave_type_id);
        $availableAfterRevert = $balance->available_days + $leave->working_days;

        if ($workingDays > $availableAfterRevert) {
            return back()->with('error', 'Insufficient leave balance.');
        }

        DB::beginTransaction();
        try {
            // Revert old pending days
            $oldBalance = $leave->employee->getLeaveBalance($leave->leave_type_id);
            $oldBalance->pending_days -= $leave->working_days;
            $oldBalance->save();

            // Update leave
            $leave->fill($request->only([
                'leave_type_id',
                'start_date',
                'end_date',
                'reason',
                'reliever_id',
                'contact_during_leave',
            ]));
            $leave->working_days = $workingDays;

            // Handle document upload
            if ($request->hasFile('document_path')) {
                $path = $request->file('document_path')->store('leave-documents', 'public');
                $leave->document_path = $path;
            }

            $leave->save();

            // Update new pending days
            $newBalance = $leave->employee->getLeaveBalance($request->leave_type_id);
            $newBalance->pending_days += $workingDays;
            $newBalance->save();

            DB::commit();

            return redirect()
                ->route('tenant.leaves.show', ['tenant' => $tenant->id, 'leave' => $leave->id])
                ->with('success', 'Leave application updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update leave: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'approval_remarks' => 'nullable|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $leave = EmployeeLeave::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $leave->approve(Auth::id(), $request->approval_remarks);
            DB::commit();

            return back()->with('success', 'Leave approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve leave: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $leave = EmployeeLeave::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $leave->reject(Auth::id(), $request->rejection_reason);
            DB::commit();

            return back()->with('success', 'Leave rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject leave: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, Tenant $tenant, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $leave = EmployeeLeave::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['pending', 'approved'])
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $leave->cancel(Auth::id(), $request->cancellation_reason);
            DB::commit();

            return back()->with('success', 'Leave cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel leave: ' . $e->getMessage());
        }
    }

    public function balances(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;
        $year = $request->input('year', date('Y'));

        $query = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['department', 'leaveBalances' => function($q) use ($year) {
                $q->where('year', $year)->with('leaveType');
            }]);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->orderBy('first_name')->get();
        $departments = Department::where('tenant_id', $tenantId)->get();
        $leaveTypes = LeaveType::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('tenant.leaves.balances', compact(
            'employees',
            'departments',
            'leaveTypes',
            'year'
        ));
    }

    public function balanceHistory(Request $request, Tenant $tenant, $employeeId)
    {
        $tenantId = $tenant->id;

        $employee = Employee::where('id', $employeeId)
            ->where('tenant_id', $tenantId)
            ->with('department')
            ->firstOrFail();

        $balances = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('year', 'desc')
            ->orderBy('leave_type_id')
            ->get()
            ->groupBy('year');

        $leaves = EmployeeLeave::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('start_date', 'desc')
            ->get();

        return view('tenant.leaves.balance-history', compact(
            'employee',
            'balances',
            'leaves'
        ));
    }

    public function myLeaves(Request $request, Tenant $tenant)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$employee) {
            return redirect()->back()->with('error', 'No employee record found.');
        }

        $year = $request->input('year', date('Y'));

        $leaves = EmployeeLeave::where('employee_id', $employee->id)
            ->with('leaveType')
            ->whereYear('start_date', $year)
            ->orderBy('start_date', 'desc')
            ->get();

        $balances = EmployeeLeaveBalance::where('employee_id', $employee->id)
            ->where('year', $year)
            ->with('leaveType')
            ->get();

        return view('tenant.leaves.my-leaves', compact('employee', 'leaves', 'balances', 'year'));
    }
}
