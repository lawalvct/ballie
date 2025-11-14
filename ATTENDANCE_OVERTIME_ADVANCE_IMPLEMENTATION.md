# Attendance, Overtime & Advance Salary System Implementation

**Date:** November 9, 2025
**Status:** Phase 1 Complete - Attendance System Backend Ready

---

## ✅ COMPLETED: Attendance System Backend

### 1. Database Schema (Migration Created)

**File:** `database/migrations/2025_12_11_000018_create_attendance_records_table.php`

**Table:** `attendance_records`

**Key Features:**

-   Clock in/out tracking with timestamps
-   IP address and location logging
-   mparisoScheduled vs actual time con
-   Automatic calculation of:
    -   Late minutes
    -   Early out minutes
    -   Work hours (in minutes)
    -   Break time
    -   Overtime minutes
-   Status tracking: present, absent, late, half_day, on_leave, weekend, holiday
-   Approval workflow
-   Shift assignment reference
-   Soft deletes for data preservation

**Indexes:**

-   Unique constraint on `employee_id` + `attendance_date` (one record per employee per day)
-   Performance indexes on tenant_id, attendance_date, status, shift_id

---

### 2. Controller Implemented

**File:** `app/Http/Controllers/Tenant/Payroll/AttendanceController.php`

**Methods Available:**

#### **Dashboard & Viewing**

| Method                 | Route                               | Purpose                                      |
| ---------------------- | ----------------------------------- | -------------------------------------------- |
| `index()`              | GET /attendance                     | Daily attendance dashboard with filters      |
| `monthlyReport()`      | GET /attendance/monthly-report      | Monthly attendance summary for all employees |
| `employeeAttendance()` | GET /attendance/employee/{employee} | Individual employee attendance history       |

#### **Clock In/Out** (API)

| Method       | Route                      | Purpose                                         |
| ------------ | -------------------------- | ----------------------------------------------- |
| `clockIn()`  | POST /attendance/clock-in  | Employee clock in with automatic late detection |
| `clockOut()` | POST /attendance/clock-out | Employee clock out with work hours calculation  |

#### **Manual Management**

| Method          | Route                                       | Purpose                          |
| --------------- | ------------------------------------------- | -------------------------------- |
| `markAbsent()`  | POST /attendance/mark-absent                | Mark employee absent with reason |
| `markHalfDay()` | POST /attendance/{attendance}/mark-half-day | Mark attendance as half day      |
| `update()`      | PUT /attendance/{attendance}                | Manual edit of attendance record |

#### **Approval**

| Method          | Route                                 | Purpose                          |
| --------------- | ------------------------------------- | -------------------------------- |
| `approve()`     | POST /attendance/{attendance}/approve | Approve single attendance record |
| `bulkApprove()` | POST /attendance/bulk-approve         | Approve multiple records at once |

---

### 3. Routes Configured

**File:** `routes/tenant.php` (lines 670-682)

**Route Pattern:** `/tenant/{tenant}/payroll/attendance/*`

All routes are protected by:

-   Authentication middleware
-   Tenant context
-   Subscription check

---

### 4. Model Already Exists

**File:** `app/Models/AttendanceRecord.php`

**Key Features:**

-   Automatic status calculation
-   Built-in `clockIn()` and `clockOut()` methods
-   Grace period handling for late arrivals
-   Overtime calculation
-   Relationship with Employee, Shift, Tenant

**Useful Methods:**

```php
$attendance->clockIn($location, $ip, $notes);
$attendance->clockOut($location, $ip, $notes);
$attendance->markAbsent($reason);
$attendance->markHalfDay();
$attendance->approve($userId);
$attendance->calculateWorkHours(); // Returns hours as float
$attendance->calculateOvertimeHours(); // Returns overtime hours
$attendance->isLate(); // Boolean
$attendance->hasOvertime(); // Boolean
```

---

## 📋 HOW THE ATTENDANCE SYSTEM WORKS

### Shift & Working Hours Management:

**The system uses SHIFT SCHEDULES to determine working hours:**

1. **Shift Schedules** (`shift_schedules` table)

    - Define working hours (e.g., 8:00 AM - 5:00 PM = 8 hours)
    - Set break time (e.g., 60 minutes lunch)
    - Configure grace periods:
        - Late grace: 15 minutes (arrive 8:15 AM = not late)
        - Early out grace: 15 minutes (leave 4:45 PM = not early)
    - Optional shift allowance (extra pay for evening/night shifts)
    - Working days configuration (Monday-Friday, etc.)
    - Default shift for new employees

2. **Default Shifts Created Automatically:**

    - **Morning Shift**: 8:00 AM - 5:00 PM (8 hours + 1 hour break)
    - **Evening Shift**: 2:00 PM - 10:00 PM (8 hours + 1 hour break, ₦5,000 allowance)
    - **Night Shift**: 10:00 PM - 6:00 AM (8 hours + 1 hour break, ₦10,000 allowance)

3. **Assigning Shifts to Employees:**

    - Admin assigns shift to employee via `employee_shift_assignments`
    - Can have different shifts for different periods
    - System uses employee's active shift for attendance calculation

4. **Tenant-Wide Default Settings** (stored in `tenants.settings` JSON):
    ```json
    {
        "attendance": {
            "default_shift_id": 1,
            "default_work_hours": 8,
            "default_break_minutes": 60,
            "default_late_grace_minutes": 15,
            "auto_clock_out_enabled": true,
            "auto_clock_out_time": "18:00",
            "require_location": false,
            "allow_early_clock_in_minutes": 30
        }
    }
    ```

### Daily Flow:

1. **Morning - Auto-Create Records**

    - When admin opens attendance page
    - System creates blank attendance records for all active employees
    - Uses employee's assigned shift OR tenant's default shift
    - Sets `scheduled_in` and `scheduled_out` from shift times
    - Default status: "absent"

2. **Employee Arrives**

    - Employee/Admin clocks in via interface
    - System records:
        - Exact clock-in time
        - IP address
        - Location (if provided)
    - **Compares with shift's scheduled start time:**
        - Calculates difference in minutes
        - Applies shift's `late_grace_minutes` (default 15 min)
        - If within grace period: Status = "present"
        - If beyond grace period: Status = "late" + records late_minutes
    - Example:
        - Shift start: 8:00 AM, Grace: 15 min
        - Clock in at 8:10 AM → Present (within grace)
        - Clock in at 8:20 AM → Late (20 minutes late)

3. **Employee Leaves**

    - Employee/Admin clocks out
    - System calculates:
        - **Total minutes** = clock_out - clock_in
        - **Work hours** = (total_minutes - break_minutes) / 60
        - **Early departure**: If clock_out < scheduled_out (minus grace)
        - **Overtime**: If clock_out > scheduled_out
    - Example:
        - Shift: 8:00 AM - 5:00 PM (8 hours + 1 hour break)
        - Clock in: 8:00 AM, Clock out: 6:00 PM
        - Work time: 10 hours - 1 hour break = 9 hours
        - Overtime: 1 hour

4. **Manual Adjustments**

    - Admin can mark absent with reason
    - Admin can mark half-day (work_hours = shift_hours / 2)
    - Admin can manually edit clock times
    - Admin can adjust break minutes

5. **Approval**
    - Manager/Admin approves attendance
    - Bulk approval available for multiple employees

---

## 🎯 NEXT STEPS

### Phase 2: Attendance Views (TODO)

Create the following views:

1. **`resources/views/tenant/payroll/attendance/index.blade.php`**

    - Daily attendance dashboard
    - Filter by department, status, employee
    - Quick actions: clock in/out, mark absent
    - Statistics cards (present, late, absent counts)

2. **`resources/views/tenant/payroll/attendance/monthly-report.blade.php`**

    - Calendar view of month
    - Employee-wise summary table
    - Export to CSV/PDF
    - Attendance percentage

3. **`resources/views/tenant/payroll/attendance/employee.blade.php`**
    - Single employee attendance history
    - Monthly calendar
    - Statistics (total hours, overtime, absences)

### Phase 3: Payroll Integration (TODO)

Update `app/Services/PayrollCalculator.php`:

```php
// Calculate deductions for absent days
$absentDays = $attendance->where('status', 'absent')->count();
$deductionPerDay = $basicSalary / $workingDaysInMonth;
$absentDeduction = $absentDays * $deductionPerDay;

// Add overtime pay
$overtimeHours = $attendance->sum('overtime_minutes') / 60;
$overtimeRate = $hourlyRate * 1.5; // Or get from overtime_records
$overtimePay = $overtimeHours * $overtimeRate;
```

---

## 🔄 OVERTIME SYSTEM (Already Exists!)

### Database Table: `overtime_records`

**Migration:** `database/migrations/2025_12_11_000017_create_overtime_records_table.php`

**Model:** `app/Models/OvertimeRecord.php`

**Features:**

-   Overtime request with approval workflow
-   Multiplier support (1.5x weekday, 2x weekend, 2.5x holiday)
-   Automatic amount calculation
-   Payment tracking (links to payroll_run)
-   Status: pending → approved → paid

**Routes Exist:** `/tenant/{tenant}/payroll/overtime/*`

**Controllers Needed:**

-   Need to create `OvertimeController` (referenced in routes but doesn't exist)

---

## 💰 LOAN SYSTEM (Already Exists!)

### Database Table: `employee_loans`

**Migration:** `database/migrations/2025_12_10_000009_create_employee_loans_table.php`

**Model:** `app/Models/EmployeeLoan.php`

**Features:**

-   Loan tracking with monthly deduction
-   Auto-deduction from payroll
-   Balance tracking
-   Status: active, completed, suspended

**Integration with Payroll:**

```php
$loans = EmployeeLoan::where('employee_id', $employee->id)
    ->where('status', 'active')
    ->get();

foreach ($loans as $loan) {
    $monthlyDeduction = $loan->monthly_deduction;
    $loan->makePayment($monthlyDeduction); // Updates balance
}
```

---

## 🆕 ADVANCE SALARY SYSTEM (TO BE CREATED)

### Required Migration:

```php
Schema::create('advance_salary_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tenant_id')->constrained();
    $table->foreignId('employee_id')->constrained();
    $table->string('advance_number')->unique();
    $table->decimal('amount', 15, 2);
    $table->date('request_date');
    $table->date('payment_date')->nullable();
    $table->text('reason');
    $table->enum('status', ['pending', 'approved', 'rejected', 'paid', 'deducted']);
    $table->integer('deduction_months')->default(1); // Spread over months
    $table->decimal('monthly_deduction', 15, 2);
    $table->decimal('total_deducted', 15, 2)->default(0);
    $table->decimal('balance', 15, 2);
    $table->foreignId('approved_by')->nullable()->constrained('users');
    $table->timestamp('approved_at')->nullable();
    $table->foreignId('payroll_run_id')->nullable(); // When fully deducted
    $table->timestamps();
});
```

---

## 📊 COMPLETE SYSTEM ARCHITECTURE

```
┌─────────────────────────────────────────────────┐
│          ATTENDANCE SYSTEM                      │
│  ✅ Database: attendance_records                │
│  ✅ Model: AttendanceRecord                     │
│  ✅ Controller: AttendanceController            │
│  ✅ Routes: /payroll/attendance/*               │
│  ⏳ Views: Dashboard, Reports (TODO)            │
└─────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────┐
│          OVERTIME SYSTEM                        │
│  ✅ Database: overtime_records                  │
│  ✅ Model: OvertimeRecord                       │
│  ✅ Routes: /payroll/overtime/*                 │
│  ⏳ Controller: OvertimeController (TODO)       │
│  ⏳ Views: Request, Approval, Report (TODO)     │
└─────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────┐
│          LOAN SYSTEM                            │
│  ✅ Database: employee_loans                    │
│  ✅ Model: EmployeeLoan                         │
│  ✅ Routes: /payroll/loans/*                    │
│  ✅ Basic Integration Exists                    │
│  ⏳ Enhanced UI (TODO)                          │
└─────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────┐
│          ADVANCE SALARY SYSTEM                  │
│  ⏳ Database: advance_salary_payments (TODO)    │
│  ⏳ Model: AdvanceSalaryPayment (TODO)          │
│  ⏳ Controller: AdvanceSalaryController (TODO)  │
│  ⏳ Routes: /payroll/advances/* (TODO)          │
│  ⏳ Views: Request, Approval (TODO)             │
└─────────────────────────────────────────────────┘
                      ↓
┌─────────────────────────────────────────────────┐
│          PAYROLL CALCULATOR                     │
│  ✅ Basic Salary Calculation                    │
│  ✅ Salary Components (Earnings/Deductions)     │
│  ✅ Tax Calculation                             │
│  ⏳ Attendance Deductions (TODO)                │
│  ⏳ Overtime Addition (TODO)                    │
│  ⏳ Loan Deductions (PARTIAL)                   │
│  ⏳ Advance Salary Deductions (TODO)            │
└─────────────────────────────────────────────────┘
```

---

## 🚀 IMPLEMENTATION PRIORITY

### Priority 1: Attendance (Current Phase)

-   ✅ Backend complete
-   ⏳ Create views
-   ⏳ Test clock in/out
-   ⏳ Integrate with payroll calculation

### Priority 2: Overtime

-   ✅ Database exists
-   ✅ Model exists
-   ⏳ Create controller
-   ⏳ Create views
-   ⏳ Integrate with payroll

### Priority 3: Advance Salary

-   ⏳ Create migration
-   ⏳ Create model
-   ⏳ Create controller
-   ⏳ Create views
-   ⏳ Integrate with payroll

### Priority 4: Enhanced Loan Management

-   ✅ Basic system exists
-   ⏳ Improve UI
-   ⏳ Add reporting

---

## 📝 TESTING CHECKLIST

### Attendance Testing:

-   [ ] Create attendance record via API
-   [ ] Clock in employee (on time)
-   [ ] Clock in employee (late)
-   [ ] Clock out and verify hours calculated
-   [ ] Mark employee absent
-   [ ] Mark half day
-   [ ] Approve attendance
-   [ ] View monthly report
-   [ ] Export attendance data

### Overtime Testing:

-   [ ] Create overtime request
-   [ ] Approve overtime
-   [ ] Reject overtime
-   [ ] Link to payroll run
-   [ ] Mark as paid
-   [ ] Generate overtime report

### Advance Salary Testing:

-   [ ] Request advance
-   [ ] Approve advance
-   [ ] Auto-deduct from payroll
-   [ ] Track balance
-   [ ] Complete payment

---

## 🎓 USAGE EXAMPLES

### Clock In Employee:

```javascript
fetch("/tenant/yourcompany/payroll/attendance/clock-in", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
    },
    body: JSON.stringify({
        employee_id: 123,
        notes: "Started morning shift",
    }),
});
```

### Get Attendance for Payroll:

```php
$attendance = AttendanceRecord::where('employee_id', $employee->id)
    ->whereYear('attendance_date', $year)
    ->whereMonth('attendance_date', $month)
    ->get();

$absentDays = $attendance->where('status', 'absent')->count();
$overtimeHours = $attendance->sum('overtime_minutes') / 60;
```

---

## 📖 DOCUMENTATION FOR FUTURE REFERENCE

All documentation files created:

-   `SALARY_COMPONENTS_GUIDE.md` - Salary component system
-   `salary_components_visual_guide.html` - Visual guide
-   `.copilot-instructions.md` - AI assistant instructions
-   `ATTENDANCE_OVERTIME_ADVANCE_IMPLEMENTATION.md` - This file

---

**Next Action:** Create attendance dashboard views to complete Phase 1
