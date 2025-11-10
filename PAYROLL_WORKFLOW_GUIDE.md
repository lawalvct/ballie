# Payroll Processing Workflow Guide

## Complete Payroll Cycle - Step by Step

### Overview

The payroll system follows a **3-stage workflow**: Draft → Processing → Approved

```
┌─────────┐    Generate     ┌────────────┐    Approve      ┌──────────┐
│  DRAFT  │  ──────────────> │ PROCESSING │ ──────────────> │ APPROVED │
└─────────┘                  └────────────┘                 └──────────┘
    │                             │                              │
    └─ Create period              └─ Calculate salaries          └─ Final & locked
       Set dates                     Calculate taxes                Create vouchers
       Add employees                 Calculate deductions           Export bank file
                                                                    Email payslips
```

---

## Stage 1: CREATE PAYROLL PERIOD (Draft Status)

### What Happens:

-   Payroll period is created with date ranges
-   Status: **DRAFT**
-   No calculations yet
-   Can be edited or deleted

### How To Do It:

1. **Go to:** Payroll → Processing → Create New
2. **Fill in:**

    - Name: e.g., "November 2025 Payroll"
    - Start Date: 2025-11-01
    - End Date: 2025-11-30
    - Pay Date: 2025-12-05 (when employees will receive payment)
    - Type: Monthly/Weekly/Contract

3. **Click:** "Create Payroll Period"

**Result:** Payroll period created with status = **DRAFT**

---

## Stage 2: GENERATE PAYROLL (Processing Status)

### What Happens:

-   System calculates salaries for ALL active employees
-   Calculates: Basic + Allowances = Gross Salary
-   Calculates: PAYE Tax, Pension, Other Deductions
-   Calculates: Attendance deductions (absent days)
-   Calculates: Overtime pay
-   Net Salary = Gross - Deductions
-   Status changes to: **PROCESSING**

### How To Do It:

1. **Go to:** Payroll → Processing → Click on payroll period
2. **You'll see:** Summary showing 0 employees, ₦0.00 totals
3. **Click:** "Generate Payroll" button (green button)
4. **Confirm:** Click "Yes" on confirmation dialog

**Result:**

-   All employee payroll runs created
-   Status = **PROCESSING**
-   Payslips show as "Pending"
-   Summary updates with totals

### What Gets Calculated:

```
Employee: John Doe
├─ Basic Salary: ₦500,000
├─ Housing Allowance (20%): ₦100,000
├─ Transport Allowance: ₦50,000
├─ Overtime Pay: ₦15,000
├─ GROSS SALARY: ₦665,000
│
├─ PAYE Tax: -₦98,500
├─ Pension (8%): -₦40,000
├─ Loan Deduction: -₦20,000
├─ Absent Days (2 days): -₦45,454
├─ TOTAL DEDUCTIONS: -₦203,954
│
└─ NET SALARY: ₦461,046
```

---

## Stage 3: APPROVE PAYROLL (Approved Status)

### What Happens:

-   Payroll is finalized and locked
-   Accounting vouchers are created automatically
-   Status changes to: **APPROVED**
-   Payslips can now be downloaded/emailed
-   Bank file can be exported

### How To Do It:

1. **Review:** Check the employee list and amounts
2. **Verify:** Ensure all calculations are correct
3. **Click:** "Approve Payroll" button (blue button)
4. **Confirm:** Click "Yes" on confirmation dialog

**Result:**

-   Status = **APPROVED**
-   Payslips change from "Pending" to "Approved"
-   Accounting entries created:
    -   **Debit:** Salary Expense
    -   **Credit:** Bank Payable / Employee Payable
    -   **Credit:** Tax Payable (PAYE)
    -   **Credit:** Pension Payable

### What Gets Created:

```
Journal Entry (Voucher):
Date: 2025-12-05
Type: Payment Voucher (PV)

Debit  | Salary Expense          | ₦665,000  | (Gross)
Credit | Employee Payable        | ₦461,046  | (Net to pay)
Credit | PAYE Tax Payable        | ₦98,500   | (To remit)
Credit | Pension Payable         | ₦40,000   | (To remit)
Credit | Loan Payable            | ₦20,000   | (Loan recovery)
```

---

## Stage 4: PAYMENT & FINALIZATION

### After Approval, You Can:

### 1. **Export Bank File (Bank Schedule)**

**Purpose:** Generate CSV file for uploading to bank for bulk payments

**How To:**

1. **Click:** "Export Bank File" button (purple button)
2. **File Downloads:** `payroll_bank_file_November_2025_2025_11_10.csv`

**File Format:**

```csv
Employee Number,Employee Name,Account Number,Bank Name,Amount,Narration
EMP001,John Doe,1234567890,GTBank,461046.00,Salary for November 2025
EMP002,Jane Smith,0987654321,Access Bank,523400.00,Salary for November 2025
EMP003,Mike Johnson,5555555555,Zenith Bank,389500.00,Salary for November 2025
```

**How to Use Bank File:**

1. Download the CSV file
2. Login to your company's bank portal
3. Go to "Bulk Payments" or "Salary Upload"
4. Upload the CSV file
5. Review and authorize payment
6. Bank will process all payments

**Alternative:** Manually pay each employee using the account numbers shown

---

### 2. **Download Payslips**

**Individual Payslip:**

-   Click on employee row
-   Click "Download" icon
-   PDF payslip downloads

**Email Payslips:**

-   Click "Email" icon on employee row
-   System sends payslip to employee's email

---

### 3. **Print Reports**

**Payroll Summary:**

-   Click "Print Summary"
-   Shows all employees with amounts
-   Good for records/audit

**Export Report:**

-   Click "Export Report"
-   Downloads Excel/CSV with full details

---

## Important Notes & Tips

### ✅ Best Practices

1. **Always Review Before Approving:**

    - Check employee list is complete
    - Verify salary amounts
    - Check deductions are correct
    - Look for any errors or anomalies

2. **Attendance Must Be Completed First:**

    - Mark all attendance before generating payroll
    - Ensure absent days are recorded
    - Approve overtime hours
    - Otherwise, deductions won't be accurate

3. **Cannot Undo Approval:**

    - Once approved, payroll is locked
    - Accounting entries are created
    - If errors found, must create adjustment voucher manually

4. **Bank File Security:**
    - Store bank file securely
    - Delete after upload
    - Contains sensitive employee data

---

## Payroll Status Meanings

| Status         | Meaning                              | Actions Available                       |
| -------------- | ------------------------------------ | --------------------------------------- |
| **DRAFT**      | Period created, no calculations      | Edit, Delete, Generate                  |
| **PROCESSING** | Salaries calculated, pending review  | View, Edit amounts, Approve, Regenerate |
| **APPROVED**   | Finalized & locked, vouchers created | View, Export, Email, Print              |
| **PAID**       | Payments made to employees           | View, Reports only                      |

---

## Common Issues & Solutions

### Issue 1: "Generate Payroll" Button Missing

**Solution:** Status must be DRAFT. If not, delete and recreate period.

### Issue 2: "Approve Payroll" Button Missing

**Solution:** Status must be PROCESSING. Click "Generate Payroll" first.

### Issue 3: Payslips Show "Pending"

**Solution:** Payslips are pending until payroll is APPROVED. Click "Approve Payroll".

### Issue 4: Bank File Not Downloading

**Solution:** Payroll must be APPROVED first. Check status.

### Issue 5: Wrong Calculations

**Solution:**

-   Still in PROCESSING? Edit individual amounts, then approve
-   Already APPROVED? Create manual adjustment voucher

### Issue 6: Employee Missing from Payroll

**Solution:**

-   Check employee status is "Active"
-   Employee must have salary structure
-   Regenerate payroll if in PROCESSING status

---

## Quick Reference Commands

```bash
# View all payroll periods
/tenant/{tenant}/payroll/processing

# Create new payroll
/tenant/{tenant}/payroll/processing/create

# Generate payroll (calculate salaries)
POST /tenant/{tenant}/payroll/processing/{period}/generate

# Approve payroll (finalize & create vouchers)
POST /tenant/{tenant}/payroll/processing/{period}/approve

# Export bank file (for bulk payment)
GET /tenant/{tenant}/payroll/processing/{period}/export-bank-file

# View individual payslip
GET /tenant/{tenant}/payroll/payslips/{payrollRun}

# Download payslip PDF
GET /tenant/{tenant}/payroll/payslips/{payrollRun}/download

# Email payslip to employee
POST /tenant/{tenant}/payroll/payslips/{payrollRun}/email
```

---

## Complete Example Workflow

### Real-World Scenario:

**Monday, November 1, 2025 - Month Starts**

-   Employees clock in/out daily
-   Attendance tracked automatically
-   Leave requests submitted

**Wednesday, November 27, 2025 - Last Week of Month**

1. HR reviews and approves all attendance
2. Marks any absent days
3. Approves overtime hours

**Friday, November 29, 2025 - Create Payroll**

1. Go to: Payroll → Processing → Create
2. Create "November 2025 Payroll"
    - Start: Nov 1
    - End: Nov 30
    - Pay Date: Dec 5
3. Status: DRAFT

**Monday, December 2, 2025 - Generate Payroll**

1. Click on "November 2025 Payroll"
2. Click "Generate Payroll"
3. System calculates all salaries (takes 10-30 seconds)
4. Review employee list and amounts
5. Status: PROCESSING

**Tuesday, December 3, 2025 - Review & Approve**

1. HR Manager reviews calculations
2. Finance checks totals against budget
3. CEO approves
4. Click "Approve Payroll"
5. Status: APPROVED
6. Vouchers created automatically

**Wednesday, December 4, 2025 - Export & Upload to Bank**

1. Click "Export Bank File"
2. Login to bank portal
3. Upload CSV file
4. Schedule payment for Dec 5
5. Email payslips to employees

**Thursday, December 5, 2025 - Payment Day**

-   Bank processes payments
-   Employees receive salaries
-   HR sends payment confirmation

**Complete! ✅**

---

## Support & Troubleshooting

**For help:**

1. Check this guide first
2. Review error messages carefully
3. Check employee records (status, salary, bank details)
4. Verify attendance is complete
5. Contact system administrator

**Emergency Contact:**

-   Email: support@yourcompany.com
-   Phone: +234-XXX-XXXX-XXX

---

## Related Documentation

-   [Employee Management Guide](./EMPLOYEE_MANAGEMENT.md)
-   [Attendance System Guide](./ATTENDANCE_GUIDE.md)
-   [Salary Components Setup](./SALARY_COMPONENTS.md)
-   [Tax Configuration](./TAX_SETUP.md)
-   [Accounting Integration](./ACCOUNTING_INTEGRATION.md)

---

_Last Updated: November 10, 2025_
_Version: 2.0_
