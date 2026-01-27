<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\ShiftSchedule;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    /**
     * Daily attendance list with stats.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        $query = AttendanceRecord::where('tenant_id', $tenant->id)
            ->with(['employee.department', 'shift', 'approver'])
            ->whereDate('attendance_date', $selectedDate);

        $departmentId = $request->get('department_id', $request->get('department'));
        if (!empty($departmentId)) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $employeeId = $request->get('employee_id', $request->get('employee'));
        if (!empty($employeeId)) {
            $query->where('employee_id', $employeeId);
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->get('shift_id'));
        }

        $attendanceRecords = $query->orderBy('clock_in')->get();

        $employees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with('department')
            ->orderBy('first_name')
            ->get();

        $hasFilters = !empty($departmentId) || $request->filled('status') || !empty($employeeId) || $request->filled('shift_id');

        if (!$hasFilters) {
            foreach ($employees as $employee) {
                if (!$attendanceRecords->where('employee_id', $employee->id)->count()) {
                    $existing = AttendanceRecord::where('tenant_id', $tenant->id)
                        ->where('employee_id', $employee->id)
                        ->where('attendance_date', $selectedDate)
                        ->first();

                    if (!$existing) {
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

        $stats = [
            'total' => $attendanceRecords->count(),
            'present' => $attendanceRecords->where('status', 'present')->count(),
            'late' => $attendanceRecords->where('status', 'late')->count(),
            'absent' => $attendanceRecords->where('status', 'absent')->count(),
            'on_leave' => $attendanceRecords->where('status', 'on_leave')->count(),
            'half_day' => $attendanceRecords->where('status', 'half_day')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Attendance records retrieved successfully',
            'data' => [
                'date' => $selectedDate->toDateString(),
                'stats' => $stats,
                'records' => $attendanceRecords->map(fn (AttendanceRecord $record) => $this->formatAttendance($record)),
            ],
        ]);
    }

    /**
     * Employee clock in.
     */
    public function clockIn(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = Employee::findOrFail($request->get('employee_id'));

        if ($employee->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Invalid employee'], 403);
        }

        $today = now()->format('Y-m-d');

        $existingRecord = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existingRecord && $existingRecord->clock_in) {
            return response()->json([
                'success' => false,
                'message' => 'Employee has already clocked in today',
                'clock_in_time' => $existingRecord->clock_in->format('h:i A'),
            ], 400);
        }

        $employeeWithShift = Employee::where('id', $employee->id)
            ->with('currentShiftAssignment.shift')
            ->first();

        $shift = $employeeWithShift->currentShiftAssignment?->shift;

        $attendance = $existingRecord ?? new AttendanceRecord();
        $attendance->tenant_id = $tenant->id;
        $attendance->employee_id = $employee->id;
        $attendance->attendance_date = $today;
        $attendance->shift_id = $shift?->id;
        $attendance->scheduled_in = $shift ? Carbon::parse($today . ' ' . $shift->start_time) : null;
        $attendance->scheduled_out = $shift ? Carbon::parse($today . ' ' . $shift->end_time) : null;

        $attendance->clockIn(
            $request->header('X-Forwarded-For') ?? $request->ip(),
            $request->header('User-Agent'),
            $request->get('notes')
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
     * Employee clock out.
     */
    public function clockOut(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = Employee::findOrFail($request->get('employee_id'));

        if ($employee->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Invalid employee'], 403);
        }

        $today = now()->format('Y-m-d');

        $attendance = AttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->clock_in) {
            return response()->json([
                'success' => false,
                'message' => 'Must clock in before clocking out',
            ], 400);
        }

        if ($attendance->clock_out) {
            return response()->json([
                'success' => false,
                'message' => 'Employee has already clocked out today',
                'clock_out_time' => $attendance->clock_out->format('h:i A'),
            ], 400);
        }

        $attendance->clockOut(
            $request->header('X-Forwarded-For') ?? $request->ip(),
            $request->header('User-Agent'),
            $request->get('notes')
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
     * Mark employee as absent.
     */
    public function markAbsent(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attendance = AttendanceRecord::where('tenant_id', $tenant->id)
            ->where('employee_id', $request->get('employee_id'))
            ->whereDate('attendance_date', $request->get('date'))
            ->first();

        if (!$attendance) {
            $attendance = AttendanceRecord::create([
                'tenant_id' => $tenant->id,
                'employee_id' => $request->get('employee_id'),
                'attendance_date' => $request->get('date'),
                'status' => 'absent',
                'created_by' => Auth::id(),
            ]);
        }

        $attendance->markAbsent($request->get('reason'));

        return response()->json([
            'success' => true,
            'message' => 'Employee marked as absent',
            'data' => [
                'attendance' => $this->formatAttendance($attendance),
            ],
        ]);
    }

    /**
     * Mark employee as on leave.
     */
    public function markLeave(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'leave_type' => 'required|string|in:sick_leave,annual_leave,unpaid_leave,maternity_leave,paternity_leave,compassionate_leave',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attendance = AttendanceRecord::where('tenant_id', $tenant->id)
            ->where('employee_id', $request->get('employee_id'))
            ->whereDate('attendance_date', $request->get('date'))
            ->first();

        if (!$attendance) {
            $attendance = AttendanceRecord::create([
                'tenant_id' => $tenant->id,
                'employee_id' => $request->get('employee_id'),
                'attendance_date' => $request->get('date'),
                'status' => 'on_leave',
                'created_by' => Auth::id(),
            ]);
        }

        $attendance->update([
            'status' => 'on_leave',
            'absence_reason' => $request->get('leave_type') . ($request->get('reason') ? ': ' . $request->get('reason') : ''),
            'admin_notes' => 'Leave type: ' . str_replace('_', ' ', ucwords($request->get('leave_type'))),
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Employee marked as on leave',
            'data' => [
                'attendance' => $this->formatAttendance($attendance),
            ],
        ]);
    }

    /**
     * Manual attendance entry.
     */
    public function manualEntry(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'nullable|date_format:H:i|after:clock_in_time',
            'break_minutes' => 'nullable|integer|min:0|max:480',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = Employee::findOrFail($request->get('employee_id'));

        if ($employee->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Invalid employee'], 403);
        }

        $attendance = AttendanceRecord::where('tenant_id', $tenant->id)
            ->where('employee_id', $request->get('employee_id'))
            ->whereDate('attendance_date', $request->get('date'))
            ->first();

        if ($attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record already exists for this date. Please edit instead.',
            ], 400);
        }

        $shift = $employee->currentShift;

        $clockIn = Carbon::parse($request->get('date') . ' ' . $request->get('clock_in_time'));
        $clockOut = $request->get('clock_out_time')
            ? Carbon::parse($request->get('date') . ' ' . $request->get('clock_out_time'))
            : null;

        $scheduledIn = $shift ? Carbon::parse($request->get('date') . ' ' . $shift->start_time) : null;
        $scheduledOut = $shift ? Carbon::parse($request->get('date') . ' ' . $shift->end_time) : null;

        $lateMinutes = 0;
        $status = 'present';
        if ($scheduledIn && $clockIn->gt($scheduledIn)) {
            $lateMinutes = $scheduledIn->diffInMinutes($clockIn);
            $status = 'late';
        }

        $workHoursMinutes = 0;
        $overtimeMinutes = 0;
        $earlyOutMinutes = 0;

        if ($clockOut) {
            $totalMinutes = $clockIn->diffInMinutes($clockOut);
            $breakMinutes = (int) $request->get('break_minutes', 0);
            $workHoursMinutes = $totalMinutes - $breakMinutes;

            if ($scheduledOut && $clockOut->lt($scheduledOut)) {
                $earlyOutMinutes = $clockOut->diffInMinutes($scheduledOut);
            }

            if ($scheduledOut && $clockOut->gt($scheduledOut)) {
                $overtimeMinutes = $scheduledOut->diffInMinutes($clockOut);
            }
        }

        $attendance = AttendanceRecord::create([
            'tenant_id' => $tenant->id,
            'employee_id' => $request->get('employee_id'),
            'attendance_date' => $request->get('date'),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'scheduled_in' => $scheduledIn,
            'scheduled_out' => $scheduledOut,
            'late_minutes' => $lateMinutes,
            'early_out_minutes' => $earlyOutMinutes,
            'work_hours_minutes' => $workHoursMinutes,
            'break_minutes' => (int) $request->get('break_minutes', 0),
            'overtime_minutes' => $overtimeMinutes,
            'status' => $status,
            'admin_notes' => 'Manual entry: ' . ($request->get('notes') ?? 'No notes'),
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded successfully',
            'data' => [
                'attendance' => $this->formatAttendance($attendance),
            ],
        ]);
    }

    /**
     * Mark attendance as half day.
     */
    public function markHalfDay(Tenant $tenant, AttendanceRecord $attendance)
    {
        if ($attendance->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $attendance->markHalfDay();

        return response()->json([
            'success' => true,
            'message' => 'Marked as half day',
            'data' => [
                'attendance' => $this->formatAttendance($attendance),
            ],
        ]);
    }

    /**
     * Approve attendance record.
     */
    public function approve(Tenant $tenant, AttendanceRecord $attendance)
    {
        if ($attendance->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $attendance->approve(Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Attendance approved',
            'data' => [
                'attendance' => $this->formatAttendance($attendance),
            ],
        ]);
    }

    /**
     * Bulk approve attendance.
     */
    public function bulkApprove(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:attendance_records,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $count = AttendanceRecord::where('tenant_id', $tenant->id)
            ->whereIn('id', $request->get('attendance_ids'))
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
     * Update attendance record.
     */
    public function update(Request $request, Tenant $tenant, AttendanceRecord $attendance)
    {
        if ($attendance->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'clock_in' => 'nullable|date_format:Y-m-d H:i',
            'clock_out' => 'nullable|date_format:Y-m-d H:i',
            'status' => 'required|in:present,absent,late,half_day,on_leave,weekend,holiday',
            'absence_reason' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attendance->update(array_merge($validator->validated(), [
            'updated_by' => Auth::id(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'data' => [
                'attendance' => $this->formatAttendance($attendance),
            ],
        ]);
    }

    /**
     * Monthly attendance report (summary per employee).
     */
    public function monthlyReport(Request $request, Tenant $tenant)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $employees = Employee::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->with(['department', 'attendanceRecords' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('attendance_date', [$startDate, $endDate]);
            }])
            ->get();

        $data = $employees->map(function (Employee $employee) {
            $records = $employee->attendanceRecords;

            return [
                'employee_id' => $employee->id,
                'employee_name' => $employee->full_name,
                'employee_number' => $employee->employee_number,
                'department_name' => $employee->department?->name,
                'summary' => [
                    'total_days' => $records->count(),
                    'present' => $records->where('status', 'present')->count(),
                    'late' => $records->where('status', 'late')->count(),
                    'absent' => $records->where('status', 'absent')->count(),
                    'on_leave' => $records->where('status', 'on_leave')->count(),
                    'half_day' => $records->where('status', 'half_day')->count(),
                    'total_hours' => round($records->sum('work_hours_minutes') / 60, 2),
                    'total_overtime' => round($records->sum('overtime_minutes') / 60, 2),
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Monthly attendance report retrieved successfully',
            'data' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'employees' => $data,
            ],
        ]);
    }

    /**
     * Employee attendance history for a month.
     */
    public function employeeAttendance(Request $request, Tenant $tenant, Employee $employee)
    {
        if ($employee->tenant_id !== $tenant->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

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

        return response()->json([
            'success' => true,
            'message' => 'Employee attendance retrieved successfully',
            'data' => [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'employee_number' => $employee->employee_number,
                    'department_name' => $employee->department?->name,
                ],
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'summary' => $summary,
                'records' => $attendanceRecords->map(fn (AttendanceRecord $record) => $this->formatAttendance($record)),
            ],
        ]);
    }

    /**
     * Generate daily attendance QR codes.
     */
    public function generateQr(Request $request, Tenant $tenant)
    {
        try {
            $date = $request->get('date', now()->format('Y-m-d'));
            $type = $request->get('type', 'clock_in');

            $payload = encrypt([
                'tenant_id' => $tenant->id,
                'date' => $date,
                'type' => $type,
                'expires_at' => now()->endOfDay()->toDateTimeString(),
                'generated_at' => now()->toDateTimeString(),
            ]);

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
                'message' => 'QR Code package not available. Please install simplesoftwareio/simple-qrcode',
                'qr_code' => '<div class="text-center p-8"><p class="text-red-600">QR Code package not installed</p><p class="text-sm text-gray-600 mt-2">Run: composer require simplesoftwareio/simple-qrcode</p></div>',
            ]);
        }
    }

    private function formatAttendance(AttendanceRecord $record): array
    {
        return [
            'id' => $record->id,
            'employee_id' => $record->employee_id,
            'employee_name' => $record->employee?->full_name,
            'employee_number' => $record->employee?->employee_number,
            'department_name' => $record->employee?->department?->name,
            'shift_id' => $record->shift_id,
            'shift_name' => $record->shift?->name,
            'attendance_date' => $record->attendance_date?->toDateString(),
            'clock_in' => $record->clock_in?->toDateTimeString(),
            'clock_out' => $record->clock_out?->toDateTimeString(),
            'scheduled_in' => $record->scheduled_in?->toDateTimeString(),
            'scheduled_out' => $record->scheduled_out?->toDateTimeString(),
            'late_minutes' => $record->late_minutes,
            'early_out_minutes' => $record->early_out_minutes,
            'work_hours_minutes' => $record->work_hours_minutes,
            'break_minutes' => $record->break_minutes,
            'overtime_minutes' => $record->overtime_minutes,
            'status' => $record->status,
            'absence_reason' => $record->absence_reason,
            'remarks' => $record->remarks,
            'admin_notes' => $record->admin_notes,
            'is_approved' => (bool) $record->is_approved,
            'approved_by' => $record->approver?->name,
            'approved_at' => $record->approved_at?->toDateTimeString(),
            'created_at' => $record->created_at?->toDateTimeString(),
            'updated_at' => $record->updated_at?->toDateTimeString(),
        ];
    }
}
