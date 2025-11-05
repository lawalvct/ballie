<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;

        $query = AttendanceRecord::with(['employee.department', 'approvedBy'])
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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('attendance_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('attendance_date', '<=', $request->date_to);
        }

        // Default to current month if no date filter
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $query->whereYear('attendance_date', date('Y'))
                ->whereMonth('attendance_date', date('m'));
        }

        $attendances = $query->orderBy('attendance_date', 'desc')
            ->orderBy('clock_in_time', 'desc')
            ->paginate(50);

        $departments = Department::where('tenant_id', $tenantId)->get();
        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('tenant.attendance.index', compact('attendances', 'departments', 'employees'));
    }

    public function dashboard(Tenant $tenant)
    {
        $tenantId = $tenant->id;
        $today = now()->toDateString();

        // Today's statistics
        $todayStats = [
            'total_employees' => Employee::where('tenant_id', $tenantId)->where('status', 'active')->count(),
            'present_today' => AttendanceRecord::where('tenant_id', $tenantId)
                ->where('attendance_date', $today)
                ->where('status', 'present')
                ->count(),
            'absent_today' => AttendanceRecord::where('tenant_id', $tenantId)
                ->where('attendance_date', $today)
                ->where('status', 'absent')
                ->count(),
            'late_today' => AttendanceRecord::where('tenant_id', $tenantId)
                ->where('attendance_date', $today)
                ->where('is_late', true)
                ->count(),
            'on_leave_today' => AttendanceRecord::where('tenant_id', $tenantId)
                ->where('attendance_date', $today)
                ->where('status', 'on_leave')
                ->count(),
        ];

        // Recent attendance records
        $recentAttendance = AttendanceRecord::with(['employee', 'shiftSchedule'])
            ->where('tenant_id', $tenantId)
            ->where('attendance_date', $today)
            ->orderBy('clock_in_time', 'desc')
            ->limit(10)
            ->get();

        // Department-wise attendance
        $departmentStats = Department::where('tenant_id', $tenantId)
            ->withCount(['employees' => function($q) {
                $q->where('status', 'active');
            }])
            ->with(['employees' => function($q) use ($today) {
                $q->whereHas('attendanceRecords', function($q2) use ($today) {
                    $q2->where('attendance_date', $today)
                        ->where('status', 'present');
                });
            }])
            ->get()
            ->map(function($dept) {
                return [
                    'name' => $dept->name,
                    'total' => $dept->employees_count,
                    'present' => $dept->employees->count(),
                    'percentage' => $dept->employees_count > 0
                        ? round(($dept->employees->count() / $dept->employees_count) * 100, 1)
                        : 0,
                ];
            });

        // Weekly trend
        $weekStart = now()->startOfWeek();
        $weeklyTrend = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $weekStart->copy()->addDays($i);
            $weeklyTrend[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'present' => AttendanceRecord::where('tenant_id', $tenantId)
                    ->where('attendance_date', $date->toDateString())
                    ->where('status', 'present')
                    ->count(),
            ];
        }

        return view('tenant.attendance.dashboard', compact(
            'todayStats',
            'recentAttendance',
            'departmentStats',
            'weeklyTrend'
        ));
    }

    public function clockIn(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $employee = Employee::where('id', $request->employee_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Check if already clocked in today
        $today = now()->toDateString();
        $existing = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existing && $existing->clock_in_time) {
            return back()->with('error', 'Employee has already clocked in today.');
        }

        try {
            $attendance = AttendanceRecord::clockIn(
                $employee->id,
                $tenantId,
                $request->ip(),
                $request->notes
            );

            return back()->with('success', sprintf(
                '%s clocked in successfully at %s',
                $employee->full_name,
                $attendance->clock_in_time
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clock in: ' . $e->getMessage());
        }
    }

    public function clockOut(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $employee = Employee::where('id', $request->employee_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Check if clocked in today
        $today = now()->toDateString();
        $attendance = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in_time) {
            return back()->with('error', 'Employee has not clocked in today.');
        }

        if ($attendance->clock_out_time) {
            return back()->with('error', 'Employee has already clocked out today.');
        }

        try {
            $attendance->clockOut($request->ip(), $request->notes);

            return back()->with('success', sprintf(
                '%s clocked out successfully at %s. Total work hours: %.2f',
                $employee->full_name,
                $attendance->clock_out_time,
                $attendance->work_hours
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to clock out: ' . $e->getMessage());
        }
    }

    public function markAbsent(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'reason' => 'nullable|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $employee = Employee::where('id', $request->employee_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Check if attendance already exists
        $existing = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $request->attendance_date)
            ->first();

        if ($existing) {
            return back()->with('error', 'Attendance record already exists for this date.');
        }

        try {
            AttendanceRecord::markAbsent(
                $employee->id,
                $tenantId,
                $request->attendance_date,
                $request->reason
            );

            return back()->with('success', sprintf(
                '%s marked as absent for %s',
                $employee->full_name,
                $request->attendance_date
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark absent: ' . $e->getMessage());
        }
    }

    public function markHalfDay(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'reason' => 'nullable|string|max:500',
        ]);

        $tenantId = $tenant->id;
        $employee = Employee::where('id', $request->employee_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        // Get attendance record
        $attendance = AttendanceRecord::where('employee_id', $employee->id)
            ->where('attendance_date', $request->attendance_date)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Attendance record not found for this date.');
        }

        try {
            $attendance->markHalfDay($request->reason);

            return back()->with('success', sprintf(
                '%s marked as half day for %s',
                $employee->full_name,
                $request->attendance_date
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark half day: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, Tenant $tenant, $id)
    {
        $tenantId = $tenant->id;
        $attendance = AttendanceRecord::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        try {
            $attendance->approve(Auth::id());
            return back()->with('success', 'Attendance approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    public function employeeAttendance(Request $request, Tenant $tenant, $employeeId)
    {
        $tenantId = $tenant->id;
        $employee = Employee::where('id', $employeeId)
            ->where('tenant_id', $tenantId)
            ->with('department', 'currentShiftAssignment.shiftSchedule')
            ->firstOrFail();

        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $attendances = AttendanceRecord::where('employee_id', $employee->id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->orderBy('attendance_date', 'asc')
            ->get();

        $summary = $employee->getMonthlyAttendanceSummary($month, $year);
        $leaveSummary = $employee->getMonthlyLeaveSummary($month, $year);

        return view('tenant.attendance.employee', compact(
            'employee',
            'attendances',
            'summary',
            'leaveSummary',
            'month',
            'year'
        ));
    }

    public function monthlyReport(Request $request, Tenant $tenant)
    {
        $tenantId = $tenant->id;
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));
        $departmentId = $request->input('department_id');

        $query = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->with(['department', 'attendanceRecords' => function($q) use ($year, $month) {
                $q->whereYear('attendance_date', $year)
                    ->whereMonth('attendance_date', $month);
            }]);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->get()->map(function($employee) use ($month, $year) {
            $summary = $employee->getMonthlyAttendanceSummary($month, $year);
            return [
                'employee' => $employee,
                'summary' => $summary,
            ];
        });

        $departments = Department::where('tenant_id', $tenantId)->get();

        return view('tenant.attendance.monthly-report', compact(
            'employees',
            'departments',
            'month',
            'year'
        ));
    }

    public function bulkClockIn(Request $request, Tenant $tenant)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $tenantId = $tenant->id;
        $today = now()->toDateString();
        $success = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->employee_ids as $employeeId) {
                $existing = AttendanceRecord::where('employee_id', $employeeId)
                    ->where('attendance_date', $today)
                    ->exists();

                if (!$existing) {
                    AttendanceRecord::clockIn($employeeId, $tenantId, $request->ip());
                    $success++;
                } else {
                    $employee = Employee::find($employeeId);
                    $errors[] = $employee->full_name . ' already clocked in';
                }
            }

            DB::commit();

            $message = "$success employee(s) clocked in successfully.";
            if (count($errors) > 0) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to clock in employees: ' . $e->getMessage());
        }
    }
}
