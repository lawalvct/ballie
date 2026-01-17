<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\ShiftSchedule;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    /**
     * Display attendance dashboard
     */
    public function index(Request $request, Tenant $tenant)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $query = AttendanceRecord::where('tenant_id', $tenant->id)
            ->with(['employee.department', 'employee.currentShiftAssignment.shift', 'shift', 'approver'])
            ->whereDate('attendance_date', $selectedDate);

        // Filters
        if ($request->filled('department')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('department_id', $request->department);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee')) {
            $query->where('employee_id', $request->employee);
        }

        // Shift filter
        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        $attendanceRecords = $query->orderBy('clock_in')->get();

        // Get all active employees for the dropdown filter
        $employees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('department')
            ->orderBy('first_name')
            ->get();

        // Only create missing attendance records when NO filters are applied (showing all employees for the day)
        $hasFilters = $request->filled('department') || $request->filled('status') ||
                      $request->filled('employee') || $request->filled('shift_id');

        if (!$hasFilters) {
            // Create attendance records for employees without records
            foreach ($employees as $employee) {
                if (!$attendanceRecords->where('employee_id', $employee->id)->count()) {
                    // Check if record already exists in database (to prevent duplicates)
                    $existing = AttendanceRecord::where('tenant_id', $tenant->id)
                        ->where('employee_id', $employee->id)
                        ->where('attendance_date', $selectedDate)
                        ->first();

                    if (!$existing) {
                        // Get employee's current shift assignment
                        $employeeWithShift = Employee::where('id', $employee->id)
                            ->with('currentShiftAssignment.shift')
                            ->first();

                        $shiftId = $employeeWithShift->currentShiftAssignment?->shift_id;

                        $record = AttendanceRecord::create([
                            'tenant_id' => $tenant->id,
                            'employee_id' => $employee->id,
                            'attendance_date' => $selectedDate,
                            'shift_id' => $shiftId,
                            'status' => 'absent',
                            'created_by' => Auth::id(),
                        ]);

                        $attendanceRecords->push($record->load(['employee.department', 'shift']));
                    }
                }
            }
        }

        // Statistics
        $stats = [
            'total' => $attendanceRecords->count(),
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'on_leave' => $attendanceRecords->where('status', 'on_leave')->count(),
            'half_day' => $attendanceRecords->where('status', 'half_day')->count(),
        ];

        $departments = Department::where('tenant_id', $tenant->id)->active()->get();
        $shifts = ShiftSchedule::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.payroll.attendance.index', compact(
            'tenant', 'attendanceRecords', 'selectedDate', 'stats', 'departments', 'employees', 'shifts'
        ));
    }

    /**
     * Employee clock in
     */
    public function clockIn(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        // Check if employee belongs to tenant
        if ($employee->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Invalid employee'], 403);
        }

        $today = now()->format('Y-m-d');

        // Check if already clocked in today
        $existingRecord = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existingRecord && $existingRecord->clock_in) {
            return response()->json([
                'error' => 'Already clocked in today',
                'clock_in_time' => $existingRecord->clock_in->format('h:i A')
            ], 400);
        }

        // Get employee's shift schedule from current assignment
        $employeeWithShift = Employee::where('id', $employee->id)
            ->with('currentShiftAssignment.shift')
            ->first();

        $shift = $employeeWithShift->currentShiftAssignment?->shift;

        $attendance = $existingRecord ?? new AttendanceRecord();
        $attendance->tenant_id = $tenant->id;
        $attendance->employee_id = $employee->id;
        $attendance->attendance_date = $today;
        $attendance->shift_id = $shift?->id;
        $attendance->scheduled_in = $shift ? Carbon::parse($today . ' ' . $shift->start_time->format('H:i:s')) : null;
        $attendance->scheduled_out = $shift ? Carbon::parse($today . ' ' . $shift->end_time->format('H:i:s')) : null;

        $attendance->clockIn(
            $request->header('X-Forwarded-For') ?? $request->ip(),
            $request->header('User-Agent'),
            $validated['notes'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Clocked in successfully',
            'clock_in_time' => $attendance->clock_in->format('h:i A'),
            'status' => $attendance->status,
            'late_minutes' => $attendance->late_minutes,
        ]);
    }

    /**
     * Employee clock out
     */
    public function clockOut(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        if ($employee->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Invalid employee'], 403);
        }

        $today = now()->format('Y-m-d');

        $attendance = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return response()->json([
                'error' => 'Must clock in before clocking out'
            ], 400);
        }

        if ($attendance->clock_out) {
            return response()->json([
                'error' => 'Already clocked out',
                'clock_out_time' => $attendance->clock_out->format('h:i A')
            ], 400);
        }

        $attendance->clockOut(
            $request->header('X-Forwarded-For') ?? $request->ip(),
            $request->header('User-Agent'),
            $validated['notes'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Clocked out successfully',
            'clock_out_time' => $attendance->clock_out->format('h:i A'),
            'work_hours' => $attendance->calculateWorkHours(),
            'overtime_hours' => $attendance->calculateOvertimeHours(),
        ]);
    }

    /**
     * Mark employee as absent
     */
    public function markAbsent(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'reason' => 'required|string|max:500',
        ]);

        $attendance = AttendanceRecord::where('tenant_id', $tenant->id)
            ->where('employee_id', $validated['employee_id'])
            ->whereDate('attendance_date', $validated['date'])
            ->first();

        if (!$attendance) {
            $attendance = AttendanceRecord::create([
                'tenant_id' => $tenant->id,
                'employee_id' => $validated['employee_id'],
                'attendance_date' => $validated['date'],
                'created_by' => Auth::id(),
            ]);
        }

        $attendance->markAbsent($validated['reason']);

        return response()->json([
            'success' => true,
            'message' => 'Employee marked as absent',
        ]);
    }

    /**
     * Mark employee as on leave
     */
    public function markLeave(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'leave_type' => 'required|string|in:sick_leave,annual_leave,unpaid_leave,maternity_leave,paternity_leave,compassionate_leave',
            'reason' => 'nullable|string|max:500',
        ]);

        $attendance = AttendanceRecord::where('tenant_id', $tenant->id)
            ->where('employee_id', $validated['employee_id'])
            ->whereDate('attendance_date', $validated['date'])
            ->first();

        if (!$attendance) {
            $attendance = AttendanceRecord::create([
                'tenant_id' => $tenant->id,
                'employee_id' => $validated['employee_id'],
                'attendance_date' => $validated['date'],
                'created_by' => Auth::id(),
            ]);
        }

        $attendance->update([
            'status' => 'on_leave',
            'absence_reason' => $validated['leave_type'] . ($validated['reason'] ? ': ' . $validated['reason'] : ''),
            'admin_notes' => 'Leave type: ' . str_replace('_', ' ', ucwords($validated['leave_type'])),
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee marked as on leave',
        ]);
    }

    /**
     * Manual attendance entry with custom times
     */
    public function manualEntry(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'nullable|date_format:H:i|after:clock_in_time',
            'break_minutes' => 'nullable|integer|min:0|max:480',
            'notes' => 'nullable|string|max:500',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        if ($employee->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Invalid employee'], 403);
        }

        // Check if attendance exists for this date
        $attendance = AttendanceRecord::where('tenant_id', $tenant->id)
            ->where('employee_id', $validated['employee_id'])
            ->whereDate('attendance_date', $validated['date'])
            ->first();

        if ($attendance) {
            return response()->json([
                'error' => 'Attendance record already exists for this date. Please edit instead.',
            ], 400);
        }

        // Get employee's shift schedule
        $shift = $employee->currentShift;

        // Create datetime objects
        $clockIn = Carbon::parse($validated['date'] . ' ' . $validated['clock_in_time']);
        $clockOut = $validated['clock_out_time']
            ? Carbon::parse($validated['date'] . ' ' . $validated['clock_out_time'])
            : null;

        // Calculate scheduled times from shift
        $scheduledIn = $shift ? Carbon::parse($validated['date'] . ' ' . $shift->start_time) : null;
        $scheduledOut = $shift ? Carbon::parse($validated['date'] . ' ' . $shift->end_time) : null;

        // Calculate late minutes
        $lateMinutes = 0;
        $status = 'present';
        if ($scheduledIn && $clockIn->gt($scheduledIn)) {
            $lateMinutes = $scheduledIn->diffInMinutes($clockIn);
            $status = 'late';
        }

        // Calculate work hours and overtime
        $workHoursMinutes = 0;
        $overtimeMinutes = 0;
        $earlyOutMinutes = 0;

        if ($clockOut) {
            $totalMinutes = $clockIn->diffInMinutes($clockOut);
            $breakMinutes = $validated['break_minutes'] ?? 0;
            $workHoursMinutes = $totalMinutes - $breakMinutes;

            // Calculate early out
            if ($scheduledOut && $clockOut->lt($scheduledOut)) {
                $earlyOutMinutes = $clockOut->diffInMinutes($scheduledOut);
            }

            // Calculate overtime
            if ($scheduledOut && $clockOut->gt($scheduledOut)) {
                $overtimeMinutes = $scheduledOut->diffInMinutes($clockOut);
            }
        }

        $attendance = AttendanceRecord::create([
            'tenant_id' => $tenant->id,
            'employee_id' => $validated['employee_id'],
            'attendance_date' => $validated['date'],
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'scheduled_in' => $scheduledIn,
            'scheduled_out' => $scheduledOut,
            'late_minutes' => $lateMinutes,
            'early_out_minutes' => $earlyOutMinutes,
            'work_hours_minutes' => $workHoursMinutes,
            'break_minutes' => $validated['break_minutes'] ?? 0,
            'overtime_minutes' => $overtimeMinutes,
            'status' => $status,
            'admin_notes' => 'Manual entry: ' . ($validated['notes'] ?? 'No notes'),
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully',
            'data' => [
                'clock_in' => $clockIn->format('h:i A'),
                'clock_out' => $clockOut ? $clockOut->format('h:i A') : null,
                'work_hours' => round($workHoursMinutes / 60, 2),
                'overtime_hours' => round($overtimeMinutes / 60, 2),
                'status' => $status,
            ],
        ]);
    }

    /**
     * Mark employee as half day
     */
    public function markHalfDay(Request $request, Tenant $tenant, AttendanceRecord $attendance)
    {
        if ($attendance->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $attendance->markHalfDay();

        return response()->json([
            'success' => true,
            'message' => 'Marked as half day',
        ]);
    }

    /**
     * Approve attendance record
     */
    public function approve(Request $request, Tenant $tenant, AttendanceRecord $attendance)
    {
        if ($attendance->tenant_id !== $tenant->id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $attendance->approve(Auth::id());

        return redirect()->back()->with('success', 'Attendance approved');
    }

    /**
     * Bulk approve attendance
     */
    public function bulkApprove(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:attendance_records,id',
        ]);

        $count = AttendanceRecord::where('tenant_id', $tenant->id)
            ->whereIn('id', $validated['attendance_ids'])
            ->update([
                'is_approved' => true,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => "{$count} attendance records approved",
        ]);
    }

    /**
     * View monthly attendance report
     */
    public function monthlyReport(Request $request, Tenant $tenant)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $employees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with(['department', 'attendanceRecords' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('attendance_date', [$startDate, $endDate]);
            }])
            ->get();

        // Calculate summary for each employee
        $employees->each(function($employee) {
            $records = $employee->attendanceRecords;
            $employee->attendance_summary = [
                'total_days' => $records->count(),
                'present' => $records->where('status', 'present')->count(),
                'late' => $records->where('status', 'late')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'on_leave' => $records->where('status', 'on_leave')->count(),
                'half_day' => $records->where('status', 'half_day')->count(),
                'total_hours' => $records->sum('work_hours_minutes') / 60,
                'total_overtime' => $records->sum('overtime_minutes') / 60,
            ];
        });

        return view('tenant.payroll.attendance.monthly-report', compact(
            'tenant', 'employees', 'year', 'month', 'startDate', 'endDate'
        ));
    }

    /**
     * Employee attendance history
     */
    public function employeeAttendance(Request $request, Tenant $tenant, Employee $employee)
    {
        if ($employee->tenant_id !== $tenant->id) {
            abort(403);
        }

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $attendanceRecords = AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date')
            ->get();

        $summary = [
            'total_days' => $attendanceRecords->count(),
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'on_leave' => $attendanceRecords->where('status', 'on_leave')->count(),
            'half_day' => $attendanceRecords->where('status', 'half_day')->count(),
            'total_hours' => round($attendanceRecords->sum('work_hours_minutes') / 60, 2),
            'total_overtime' => round($attendanceRecords->sum('overtime_minutes') / 60, 2),
        ];

        return view('tenant.payroll.attendance.employee', compact(
            'tenant', 'employee', 'attendanceRecords', 'summary', 'year', 'month', 'startDate', 'endDate'
        ));
    }

    /**
     * Update attendance record
     */
    public function update(Request $request, Tenant $tenant, AttendanceRecord $attendance)
    {
        if ($attendance->tenant_id !== $tenant->id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'clock_in' => 'nullable|date_format:Y-m-d H:i',
            'clock_out' => 'nullable|date_format:Y-m-d H:i',
            'status' => 'required|in:present,absent,late,half_day,on_leave,weekend,holiday',
            'absence_reason' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        $attendance->update(array_merge($validated, [
            'updated_by' => Auth::id(),
        ]));

        return redirect()->back()->with('success', 'Attendance updated successfully');
    }

    /**
     * Generate daily attendance QR codes (Clock In and Clock Out)
     */
    public function generateAttendanceQR(Request $request, Tenant $tenant)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));
            $type = $request->get('type', 'clock_in'); // clock_in or clock_out

            // Create encrypted payload
            $payload = encrypt([
                'tenant_id' => $tenant->id,
                'date' => $date,
                'type' => $type,
                'expires_at' => now()->endOfDay()->toDateTimeString(),
                'generated_at' => now()->toDateTimeString(),
            ]);

            // Generate QR code as SVG
            $qrCode = QrCode::size(300)
                ->margin(2)
                ->generate($payload);

            return response()->json([
                'success' => true,
                'qr_code' => (string) $qrCode,
                'type' => $type,
                'date' => $date,
                'expires_at' => now()->endOfDay()->format('Y-m-d H:i:s'),
            ]);
        } catch (\Error $e) {
            return response()->json([
                'success' => false,
                'error' => 'QR Code package not available. Please install: composer require simplesoftwareio/simple-qrcode',
                'qr_code' => '<div class="text-center p-8"><p class="text-red-600">QR Code package not installed</p><p class="text-sm text-gray-600 mt-2">Run: composer require simplesoftwareio/simple-qrcode</p></div>',
            ]);
        }
    }

    /**
     * Display QR code view for admin
     */
    public function showAttendanceQR(Tenant $tenant)
    {
        return view('tenant.payroll.attendance.qr-codes', compact('tenant'));
    }
}

