# Attendance System - Complete Guide

## Overview

The attendance system allows administrators to track employee work hours, manage leaves, and calculate overtime automatically.

---

## How Work Hours Are Determined

### Automatic Calculation

Work hours are calculated using the following formula:

```
Work Hours = (Clock Out Time - Clock In Time) - Break Minutes
```

**Example:**

-   Clock In: 9:00 AM
-   Clock Out: 6:00 PM
-   Break: 60 minutes (1 hour)
-   **Work Hours = 9 hours - 1 hour = 8 hours**

### Components:

1. **Total Time**: Difference between clock out and clock in
2. **Break Minutes**: Deducted from total time (default: 60 minutes)
3. **Net Work Hours**: Final billable hours

### Late Arrival Detection

-   Compares clock-in time with scheduled shift start time
-   Applies grace period (if shift has one)
-   Marks as "Late" if beyond grace period
-   Calculates late minutes for reporting

### Overtime Calculation

-   Automatically detected when clock-out exceeds scheduled end time
-   Calculated at 1.5x hourly rate
-   Overtime Minutes = Clock Out Time - Scheduled End Time

---

## Admin Features

### 1. Manual Attendance Entry

**Use Case**: Record attendance for employees who forgot to clock in/out or were working remotely.

**Features:**

-   ✅ Select any employee
-   ✅ Choose past or current date
-   ✅ Set custom clock-in time (HH:MM format)
-   ✅ Set custom clock-out time (optional)
-   ✅ Define break duration (in minutes)
-   ✅ Add notes/reason for manual entry
-   ✅ Real-time work hours preview

**Access**: Click **"Manual Entry"** button in attendance dashboard

**How It Works:**

1. Select employee and date
2. Enter clock-in time (e.g., 09:00)
3. Enter clock-out time (e.g., 18:00)
4. Set break minutes (default: 60)
5. Add notes (e.g., "Remote work - forgot to clock in")
6. System calculates:
    - Total work hours
    - Late minutes (if any)
    - Overtime hours (if any)
    - Early departure (if any)

---

### 2. Mark Employee on Leave

**Use Case**: Record approved leaves for employees.

**Leave Types Available:**

1. **Sick Leave** - Medical reasons
2. **Annual Leave** - Vacation/holiday
3. **Unpaid Leave** - Leave without pay
4. **Maternity Leave** - For expecting mothers
5. **Paternity Leave** - For new fathers
6. **Compassionate Leave** - Family emergencies/bereavement

**Access**: Click **"Mark Leave"** button in attendance dashboard

**Process:**

1. Select employee
2. Choose date
3. Select leave type
4. Add reason/notes (optional)
5. Submit

**Effect:**

-   Status set to "On Leave"
-   No work hours recorded
-   Not counted as absence
-   Leave type stored for reporting

---

### 3. Quick Actions in Table

**Clock In Button:**

-   Manually clock in employee
-   Records current timestamp
-   Prompts for optional notes

**Clock Out Button:**

-   Manually clock out employee
-   Calculates work hours automatically
-   Displays total work hours

**Mark Absent:**

-   Marks employee as absent
-   Requires reason
-   No work hours recorded

**Approve:**

-   Approve individual attendance record
-   Marks as verified by admin
-   Records approver and timestamp

**Bulk Approve:**

-   Select multiple records (checkboxes)
-   Approve all selected at once
-   Efficient for large teams

---

## Work Hours Breakdown

### Standard Day Example:

```
Scheduled: 9:00 AM - 5:00 PM (8 hours)
Break: 1 hour (60 minutes)

Scenario 1: On Time
Clock In: 9:00 AM
Clock Out: 5:00 PM
Work Hours: 7 hours (8 - 1 hour break)
Status: Present ✓

Scenario 2: Late Arrival
Clock In: 9:30 AM
Clock Out: 5:00 PM
Work Hours: 6.5 hours
Late Minutes: 30
Status: Late ⚠️

Scenario 3: With Overtime
Clock In: 9:00 AM
Clock Out: 7:00 PM
Work Hours: 9 hours (10 - 1 hour break)
Overtime: 2 hours
Status: Present ✓

Scenario 4: Half Day
Clock In: 9:00 AM
Clock Out: 1:00 PM
Work Hours: 3 hours (4 - 1 hour break)
Status: Half Day 🕐
```

---

## Attendance Statuses

| Status       | Icon      | Description                     | Work Hours                 |
| ------------ | --------- | ------------------------------- | -------------------------- |
| **Present**  | ✅ Green  | Clocked in on time              | Full hours recorded        |
| **Late**     | ⚠️ Yellow | Clocked in after scheduled time | Full hours but marked late |
| **Absent**   | ❌ Red    | No attendance record            | Zero hours                 |
| **On Leave** | 🏖️ Purple | Approved leave                  | Zero hours (excused)       |
| **Half Day** | 🕐 Orange | Partial day work                | Half of standard hours     |
| **Weekend**  | 📅 Gray   | Non-working day                 | N/A                        |
| **Holiday**  | 🎉 Blue   | Public holiday                  | N/A                        |

---

## Monthly Report Features

### Statistics Tracked:

-   Total Days
-   Present Days
-   Late Days
-   Absent Days
-   Leave Days
-   Half Days
-   Total Work Hours
-   Total Overtime Hours
-   Attendance Percentage

### Export Options:

-   CSV Download
-   PDF Report (upcoming)
-   Excel Export (upcoming)

---

## Payroll Integration

Attendance data automatically feeds into payroll:

### Deductions:

-   **Absent Days**: Deducted from salary (daily rate × absent days)
-   **Late Arrivals**: Can be configured for deduction
-   **Half Days**: 50% deduction

### Additions:

-   **Overtime Hours**: Added at 1.5x hourly rate
-   **Work on Holidays**: Bonus rate (if configured)

### Formula:

```
Gross Salary = Basic Salary + Allowances
Deductions = (Absent Days × Daily Rate) + Other Deductions
Overtime Pay = Overtime Hours × Hourly Rate × 1.5
Net Salary = Gross Salary - Deductions + Overtime Pay
```

---

## Best Practices

### For Administrators:

1. **Daily Review**: Check attendance dashboard daily
2. **Manual Entry**: Use only when necessary (forgot to clock in, remote work)
3. **Leave Management**: Mark leaves promptly to avoid confusion
4. **Bulk Approve**: Review and approve attendance weekly
5. **Notes**: Always add notes for manual entries for audit trail

### For Accurate Reporting:

1. Set correct shift schedules for employees
2. Define standard break times
3. Configure grace periods for late arrivals
4. Review and approve attendance before payroll processing
5. Export monthly reports for records

---

## Technical Details

### Database Fields:

-   `attendance_date`: Date of attendance
-   `clock_in`: Timestamp when employee clocked in
-   `clock_out`: Timestamp when employee clocked out
-   `scheduled_in`: Expected clock-in time (from shift)
-   `scheduled_out`: Expected clock-out time (from shift)
-   `work_hours_minutes`: Total work minutes (excluding breaks)
-   `break_minutes`: Break duration in minutes
-   `late_minutes`: Minutes late (if any)
-   `early_out_minutes`: Minutes left early (if any)
-   `overtime_minutes`: Overtime minutes worked
-   `status`: Current status (present, absent, late, etc.)
-   `is_approved`: Whether admin has approved
-   `approved_by`: User who approved
-   `approved_at`: Timestamp of approval

### API Endpoints:

-   `POST /attendance/clock-in`: Clock in employee
-   `POST /attendance/clock-out`: Clock out employee
-   `POST /attendance/manual-entry`: Manual attendance entry
-   `POST /attendance/mark-absent`: Mark as absent
-   `POST /attendance/mark-leave`: Mark as on leave
-   `POST /attendance/bulk-approve`: Approve multiple records
-   `PUT /attendance/{id}`: Update attendance record

---

## Frequently Asked Questions

**Q: Can I edit attendance after it's recorded?**
A: Yes, use the update endpoint or mark it and create a new manual entry.

**Q: How do I handle employees working from home?**
A: Use Manual Entry feature to record their work hours.

**Q: What if an employee forgets to clock out?**
A: Admin can manually set clock-out time using Manual Entry.

**Q: Can I mark leave for multiple days at once?**
A: Currently one day at a time. Bulk leave feature coming soon.

**Q: How are public holidays handled?**
A: Mark the day as "Holiday" status, no attendance required.

**Q: What about weekend work?**
A: Record as manual entry, can be configured for premium pay.

---

## Future Enhancements

-   [ ] Bulk leave marking (date range)
-   [ ] Shift pattern management
-   [ ] Biometric integration
-   [ ] Mobile app for employee self-service
-   [ ] GPS-based clock in/out
-   [ ] Facial recognition
-   [ ] Leave balance tracking
-   [ ] Automated reminders for missing clock-outs

---

## Support

For issues or questions:

1. Check this documentation
2. Review monthly reports for discrepancies
3. Contact system administrator
4. Check audit logs for attendance history

---

**Last Updated**: {{ now()->format('F d, Y') }}
**Version**: 1.0.0
