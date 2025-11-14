# Shift Management & Working Hours Configuration Guide

**Date:** November 14, 2025
**Status:** Complete Implementation Guide

---

## 📚 TABLE OF CONTENTS

1. [Overview](#overview)
2. [How It Works](#how-it-works)
3. [Shift Schedule Configuration](#shift-schedule-configuration)
4. [Setting Up Default Shifts](#setting-up-default-shifts)
5. [Assigning Shifts to Employees](#assigning-shifts-to-employees)
6. [Late Determination Logic](#late-determination-logic)
7. [Tenant-Wide Settings](#tenant-wide-settings)
8. [Admin Interface Implementation](#admin-interface-implementation)

---

## 🎯 OVERVIEW

The attendance system determines if an employee is late, on time, or working overtime based on **SHIFT SCHEDULES**. Each shift defines:

-   **Working Hours** (e.g., 8 hours, 12 hours, 5 hours)
-   **Start and End Times** (e.g., 8:00 AM - 5:00 PM)
-   **Break Duration** (e.g., 60 minutes for lunch)
-   **Late Grace Period** (e.g., 15 minutes tolerance)
-   **Early Out Grace Period** (e.g., 15 minutes tolerance)
-   **Shift Allowance** (optional extra pay)
-   **Working Days** (which days this shift applies)

---

## 🔧 HOW IT WORKS

### Step-by-Step Flow

```
1. SHIFT CREATED
   ├─> Define: Morning Shift (8:00 AM - 5:00 PM, 8 hours)
   ├─> Set grace period: 15 minutes
   └─> Set break: 60 minutes

2. SHIFT ASSIGNED TO EMPLOYEE
   ├─> John Doe → Morning Shift
   ├─> Jane Smith → Evening Shift
   └─> Bob Wilson → Night Shift

3. ATTENDANCE RECORD CREATED
   ├─> scheduled_in = 8:00 AM (from shift)
   ├─> scheduled_out = 5:00 PM (from shift)
   └─> shift_id = Morning Shift ID

4. EMPLOYEE CLOCKS IN
   ├─> Clock in time: 8:10 AM
   ├─> Compare with scheduled: 8:00 AM
   ├─> Difference: 10 minutes
   ├─> Check grace period: 15 minutes
   └─> Result: ON TIME (within grace)

5. EMPLOYEE CLOCKS IN LATE
   ├─> Clock in time: 8:25 AM
   ├─> Compare with scheduled: 8:00 AM
   ├─> Difference: 25 minutes
   ├─> Check grace period: 15 minutes
   └─> Result: LATE (25 minutes late)

6. EMPLOYEE CLOCKS OUT
   ├─> Clock out time: 6:30 PM
   ├─> Scheduled out: 5:00 PM
   ├─> Work duration: 10.5 hours - 1 hour break = 9.5 hours
   ├─> Expected hours: 8 hours
   └─> Result: 1.5 HOURS OVERTIME
```

---

## 📋 SHIFT SCHEDULE CONFIGURATION

### Database Table: `shift_schedules`

| Field                     | Type    | Description               | Example                  |
| ------------------------- | ------- | ------------------------- | ------------------------ |
| `name`                    | string  | Shift name                | "Morning Shift"          |
| `code`                    | string  | Short code                | "MS"                     |
| `start_time`              | time    | Shift start               | "08:00:00"               |
| `end_time`                | time    | Shift end                 | "17:00:00"               |
| `work_hours`              | integer | Expected hours            | 8                        |
| `break_minutes`           | integer | Break duration            | 60                       |
| `late_grace_minutes`      | integer | Late tolerance            | 15                       |
| `early_out_grace_minutes` | integer | Early leave tolerance     | 15                       |
| `shift_allowance`         | decimal | Extra pay                 | 5000.00                  |
| `is_night_shift`          | boolean | Night shift flag          | false                    |
| `working_days`            | JSON    | Days applicable           | ["monday","tuesday",...] |
| `is_active`               | boolean | Active status             | true                     |
| `is_default`              | boolean | Default for new employees | true                     |

### Example Shift Configurations

#### 1. Standard Office Hours (8 hours)

```json
{
    "name": "Morning Shift",
    "code": "MS",
    "start_time": "08:00:00",
    "end_time": "17:00:00",
    "work_hours": 8,
    "break_minutes": 60,
    "late_grace_minutes": 15,
    "early_out_grace_minutes": 15,
    "shift_allowance": 0,
    "is_default": true,
    "working_days": ["monday", "tuesday", "wednesday", "thursday", "friday"]
}
```

#### 2. Extended Shift (12 hours)

```json
{
    "name": "Extended Shift",
    "code": "ES",
    "start_time": "06:00:00",
    "end_time": "18:00:00",
    "work_hours": 12,
    "break_minutes": 90,
    "late_grace_minutes": 20,
    "early_out_grace_minutes": 20,
    "shift_allowance": 8000,
    "working_days": ["monday", "tuesday", "wednesday", "thursday", "friday"]
}
```

#### 3. Half-Day Shift (5 hours)

```json
{
    "name": "Part-Time Shift",
    "code": "PT",
    "start_time": "09:00:00",
    "end_time": "14:00:00",
    "work_hours": 5,
    "break_minutes": 0,
    "late_grace_minutes": 10,
    "early_out_grace_minutes": 10,
    "shift_allowance": 0,
    "working_days": [
        "monday",
        "tuesday",
        "wednesday",
        "thursday",
        "friday",
        "saturday"
    ]
}
```

#### 4. Night Shift (8 hours)

```json
{
    "name": "Night Shift",
    "code": "NS",
    "start_time": "22:00:00",
    "end_time": "06:00:00",
    "work_hours": 8,
    "break_minutes": 60,
    "late_grace_minutes": 15,
    "early_out_grace_minutes": 15,
    "shift_allowance": 10000,
    "is_night_shift": true,
    "working_days": ["monday", "tuesday", "wednesday", "thursday", "friday"]
}
```

---

## 🚀 SETTING UP DEFAULT SHIFTS

### Automatic Creation on Tenant Registration

The system automatically creates 3 default shifts when a tenant registers:

```php
// app/Models/ShiftSchedule.php
ShiftSchedule::createDefaultShifts($tenantId);
```

This creates:

1. **Morning Shift** (8:00 AM - 5:00 PM) - Default
2. **Evening Shift** (2:00 PM - 10:00 PM) - ₦5,000 allowance
3. **Night Shift** (10:00 PM - 6:00 AM) - ₦10,000 allowance

### Admin Can Create Custom Shifts

**Controller:** `ShiftController@store`
**Route:** `POST /tenant/{tenant}/shifts`

**Example Request:**

```json
{
    "name": "Weekend Shift",
    "code": "WS",
    "start_time": "10:00",
    "end_time": "18:00",
    "work_hours": 8,
    "break_minutes": 60,
    "late_grace_minutes": 20,
    "early_out_grace_minutes": 20,
    "shift_allowance": 7000,
    "working_days": ["saturday", "sunday"],
    "color": "#10b981"
}
```

---

## 👥 ASSIGNING SHIFTS TO EMPLOYEES

### Individual Assignment

**Table:** `employee_shift_assignments`

| Field               | Type    | Description               |
| ------------------- | ------- | ------------------------- |
| `employee_id`       | integer | Employee ID               |
| `shift_schedule_id` | integer | Shift ID                  |
| `effective_from`    | date    | Start date                |
| `effective_to`      | date    | End date (null = ongoing) |

**Controller:** `ShiftController@assignShift`
**Route:** `POST /tenant/{tenant}/shifts/assign`

**Example:**

```json
{
    "employee_id": 123,
    "shift_schedule_id": 2,
    "effective_from": "2025-11-14"
}
```

### Bulk Assignment

**Controller:** `ShiftController@bulkAssign`
**Route:** `POST /tenant/{tenant}/shifts/bulk-assign`

**Example:**

```json
{
    "employee_ids": [123, 124, 125, 126],
    "shift_schedule_id": 1,
    "effective_from": "2025-11-14"
}
```

### Department-Wide Assignment

**Route:** `POST /tenant/{tenant}/shifts/assign-department`

**Example:**

```json
{
    "department_id": 5,
    "shift_schedule_id": 1,
    "effective_from": "2025-11-14"
}
```

---

## ⏰ LATE DETERMINATION LOGIC

### The Algorithm

```php
// When employee clocks in
public function clockIn(?string $location = null, ?string $ip = null, ?string $notes = null): void
{
    $this->clock_in = now();
    $this->status = 'present';

    // Calculate if late
    if ($this->scheduled_in && $this->clock_in > $this->scheduled_in) {
        // Calculate difference in minutes
        $this->late_minutes = $this->scheduled_in->diffInMinutes($this->clock_in);

        // Apply grace period from shift
        if ($this->shift && $this->late_minutes <= $this->shift->late_grace_minutes) {
            $this->late_minutes = 0; // Within grace period
        } else {
            $this->status = 'late'; // Beyond grace period
        }
    }

    $this->save();
}
```

### Examples

#### Scenario 1: On Time

-   **Shift Start:** 8:00 AM
-   **Grace Period:** 15 minutes
-   **Clock In:** 8:00 AM
-   **Result:** Status = "present", late_minutes = 0

#### Scenario 2: Within Grace Period

-   **Shift Start:** 8:00 AM
-   **Grace Period:** 15 minutes
-   **Clock In:** 8:12 AM
-   **Result:** Status = "present", late_minutes = 0 (grace applied)

#### Scenario 3: Late (Beyond Grace)

-   **Shift Start:** 8:00 AM
-   **Grace Period:** 15 minutes
-   **Clock In:** 8:20 AM
-   **Result:** Status = "late", late_minutes = 20

#### Scenario 4: Very Late

-   **Shift Start:** 8:00 AM
-   **Grace Period:** 15 minutes
-   **Clock In:** 9:30 AM
-   **Result:** Status = "late", late_minutes = 90

### Overtime Calculation

```php
// When employee clocks out
public function clockOut(?string $location = null, ?string $ip = null, ?string $notes = null): void
{
    $this->clock_out = now();

    // Calculate work hours
    $totalMinutes = $this->clock_in->diffInMinutes($this->clock_out);
    $this->work_hours_minutes = $totalMinutes - $this->break_minutes;

    // Calculate overtime
    if ($this->scheduled_out && $this->clock_out > $this->scheduled_out) {
        $overtimeStart = $this->scheduled_out;

        // Apply early out grace (if left early, no overtime)
        if ($this->shift && $this->early_out_minutes > 0) {
            $overtimeStart = $overtimeStart->addMinutes($this->shift->early_out_grace_minutes);
        }

        if ($this->clock_out > $overtimeStart) {
            $this->overtime_minutes = $overtimeStart->diffInMinutes($this->clock_out);
        }
    }

    $this->save();
}
```

---

## ⚙️ TENANT-WIDE SETTINGS

### Default Attendance Configuration

Store in `tenants.settings` JSON field:

```json
{
    "attendance": {
        "default_shift_id": 1,
        "default_work_hours": 8,
        "default_break_minutes": 60,
        "default_late_grace_minutes": 15,
        "default_early_out_grace_minutes": 15,
        "auto_clock_out_enabled": false,
        "auto_clock_out_time": "18:00",
        "require_location": false,
        "require_notes_for_late": true,
        "allow_early_clock_in_minutes": 30,
        "allow_manual_clock_in_edit": true,
        "attendance_approval_required": true,
        "weekend_days": ["saturday", "sunday"],
        "public_holidays": []
    }
}
```

### Usage in Code

```php
// Get tenant's default shift
$defaultShiftId = $tenant->settings['attendance']['default_shift_id'] ?? null;
$defaultShift = ShiftSchedule::find($defaultShiftId);

// Or get first default shift
$defaultShift = ShiftSchedule::where('tenant_id', $tenant->id)
    ->where('is_default', true)
    ->first();
```

---

## 🎨 ADMIN INTERFACE IMPLEMENTATION

### Required Views

#### 1. Shift Management Page

**Route:** `/tenant/{tenant}/shifts`
**View:** `resources/views/tenant/shifts/index.blade.php`

**Features:**

-   List all shifts with details
-   Create new shift button
-   Edit/Delete shift actions
-   Set default shift toggle
-   Color coding for visual identification

#### 2. Shift Create/Edit Form

**Routes:**

-   Create: `GET /tenant/{tenant}/shifts/create`
-   Edit: `GET /tenant/{tenant}/shifts/{shift}/edit`

**Form Fields:**

-   Shift Name
-   Shift Code
-   Start Time (time picker)
-   End Time (time picker)
-   Work Hours (auto-calculate or manual)
-   Break Minutes
-   Late Grace Minutes
-   Early Out Grace Minutes
-   Shift Allowance
-   Is Night Shift (checkbox)
-   Working Days (multi-select)
-   Is Active (checkbox)
-   Is Default (checkbox)
-   Color Picker

#### 3. Employee Shift Assignment Page

**Route:** `/tenant/{tenant}/shifts/assignments`
**View:** `resources/views/tenant/shifts/assignments.blade.php`

**Features:**

-   Employee list with current shift
-   Quick assign dropdown
-   Bulk assign functionality
-   Assignment history
-   Filter by department/shift

#### 4. Attendance Settings Page

**Route:** `/tenant/{tenant}/settings/attendance`
**View:** `resources/views/tenant/settings/attendance.blade.php`

**Settings to Configure:**

-   Default shift selection
-   Auto clock-out settings
-   Location requirement
-   Grace period defaults
-   Approval workflow
-   Weekend days
-   Public holidays

### Example Blade Components

#### Shift Badge Component

```blade
{{-- resources/views/components/shift-badge.blade.php --}}
@props(['shift'])

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
      style="background-color: {{ $shift->color }}20; color: {{ $shift->color }}">
    <i class="fas fa-clock mr-1"></i>
    {{ $shift->name }} ({{ $shift->getFormattedTimeRange() }})
</span>
```

#### Shift Form Component

```blade
{{-- Shift working hours inputs --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="start_time" class="block text-sm font-medium text-gray-700">
            Start Time
        </label>
        <input type="time" name="start_time" id="start_time"
               value="{{ old('start_time', $shift->start_time ?? '08:00') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>
    <div>
        <label for="end_time" class="block text-sm font-medium text-gray-700">
            End Time
        </label>
        <input type="time" name="end_time" id="end_time"
               value="{{ old('end_time', $shift->end_time ?? '17:00') }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div>
        <label for="work_hours" class="block text-sm font-medium text-gray-700">
            Work Hours
        </label>
        <input type="number" name="work_hours" id="work_hours"
               value="{{ old('work_hours', $shift->work_hours ?? 8) }}"
               min="1" max="24"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>
    <div>
        <label for="break_minutes" class="block text-sm font-medium text-gray-700">
            Break (minutes)
        </label>
        <input type="number" name="break_minutes" id="break_minutes"
               value="{{ old('break_minutes', $shift->break_minutes ?? 60) }}"
               min="0" max="240" step="15"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>
    <div>
        <label for="late_grace_minutes" class="block text-sm font-medium text-gray-700">
            Late Grace (minutes)
        </label>
        <input type="number" name="late_grace_minutes" id="late_grace_minutes"
               value="{{ old('late_grace_minutes', $shift->late_grace_minutes ?? 15) }}"
               min="0" max="60" step="5"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
    </div>
</div>
```

---

## 📊 USAGE EXAMPLES

### Creating a Custom 12-Hour Shift

```php
// Controller
public function store(Request $request, Tenant $tenant)
{
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'code' => 'required|string|max:10|unique:shift_schedules,code',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i',
        'work_hours' => 'required|integer|min:1|max:24',
        'break_minutes' => 'required|integer|min:0|max:240',
        'late_grace_minutes' => 'required|integer|min:0|max:60',
        'shift_allowance' => 'nullable|numeric|min:0',
    ]);

    $shift = ShiftSchedule::create([
        'tenant_id' => $tenant->id,
        'name' => $validated['name'],
        'code' => $validated['code'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'work_hours' => $validated['work_hours'],
        'break_minutes' => $validated['break_minutes'],
        'late_grace_minutes' => $validated['late_grace_minutes'],
        'shift_allowance' => $validated['shift_allowance'] ?? 0,
        'is_active' => true,
    ]);

    return redirect()->route('tenant.shifts.index', $tenant)
        ->with('success', 'Shift created successfully!');
}
```

### Assigning Shift to Employee

```php
// When hiring or updating employee
$employee = Employee::find($employeeId);

EmployeeShiftAssignment::create([
    'tenant_id' => $tenant->id,
    'employee_id' => $employee->id,
    'shift_schedule_id' => $shiftId,
    'effective_from' => now(),
    'effective_to' => null, // Ongoing
]);
```

### Checking if Employee is Late

```php
// In attendance creation
$attendance = AttendanceRecord::create([
    'tenant_id' => $tenant->id,
    'employee_id' => $employee->id,
    'attendance_date' => today(),
    'shift_id' => $employee->getCurrentShift()->id,
    'scheduled_in' => $employee->getCurrentShift()->start_time,
    'scheduled_out' => $employee->getCurrentShift()->end_time,
]);

// When clocking in
$attendance->clockIn();
// Automatically calculates late status
```

---

## ✅ IMPLEMENTATION CHECKLIST

### Database Setup

-   [x] `shift_schedules` table exists
-   [x] `employee_shift_assignments` table exists
-   [x] Default shifts auto-created on tenant registration

### Models

-   [x] `ShiftSchedule` model complete
-   [x] `EmployeeShiftAssignment` model complete
-   [x] Relationships defined

### Controllers

-   [x] `ShiftController` exists with CRUD operations
-   [x] `AttendanceController` uses shift data
-   [ ] Settings controller for tenant-wide defaults (TODO)

### Views (TODO)

-   [ ] Shift management index page
-   [ ] Shift create/edit form
-   [ ] Employee shift assignment page
-   [ ] Attendance settings page
-   [ ] Shift selection dropdown in attendance
-   [ ] Shift badge component

### Integration

-   [x] Attendance uses shift for late calculation
-   [x] Overtime calculation based on shift
-   [ ] Payroll includes shift allowance
-   [ ] Reports show shift-wise attendance

---

## 🎓 BEST PRACTICES

1. **Always assign a shift to employees** - Use default shift if no specific shift assigned
2. **Review grace periods regularly** - Adjust based on company culture
3. **Use shift allowances** - Incentivize night/weekend shifts
4. **Set realistic work hours** - Don't set 12-hour shifts without proper break times
5. **Monitor overtime** - High overtime might indicate need for more staff
6. **Audit shift changes** - Keep history of who changed what

---

## 📚 RELATED DOCUMENTATION

-   `ATTENDANCE_OVERTIME_ADVANCE_IMPLEMENTATION.md` - Complete attendance system
-   `ATTENDANCE_SYSTEM_COMPLETE.md` - Detailed implementation
-   `AttendanceRecord.php` - Model with late calculation logic
-   `ShiftSchedule.php` - Shift model implementation

---

**Last Updated:** November 14, 2025
**Version:** 1.0
**Status:** Ready for Implementation
