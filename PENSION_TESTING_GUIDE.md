# Pension System Testing Guide

## Overview
This guide explains how to test the pension contribution system in Ballie. The system automatically calculates:
- **Employee Contribution**: 8% of basic salary (deducted from employee)
- **Employer Contribution**: 10% of basic salary (paid by employer)

## Prerequisites
- Access to Ballie application
- At least one employee created
- Payroll period created

---

## Test Scenario 1: Employee with Pension

### Step 1: Create/Update Employee with Pension Details
1. Navigate to **Payroll → Employees**
2. Click **Create Employee** or edit existing employee
3. Fill in employee details:
   - First Name: `John`
   - Last Name: `Doe`
   - Basic Salary: `₦500,000`
   - **PFA Provider**: `Stanbic IBTC Pension`
   - **RSA PIN**: `PEN123456789`
   - **Pension Exempt**: Leave **unchecked** (or set to `false`)
4. Click **Save**

### Step 2: Generate Payroll
1. Navigate to **Payroll → Processing**
2. Click **Create Payroll Period**
3. Fill in:
   - Period Name: `November 2025`
   - Start Date: `2025-11-01`
   - End Date: `2025-11-30`
   - Apply PAYE Tax: ✓ (checked)
   - Apply NSITF: ✓ (checked)
4. Click **Save**
5. Click **Generate Payroll** button

### Step 3: Verify Pension Calculation
1. View the generated payroll
2. Check the payslip for John Doe
3. **Expected Results**:
   ```
   Basic Salary:           ₦500,000.00
   Employee Pension (8%):  ₦40,000.00  (deduction)
   Employer Pension (10%): ₦50,000.00  (employer pays)
   Total Pension:          ₦90,000.00
   ```

### Step 4: Check Statutory Report
1. Navigate to **Statutory → Pension Report**
2. Select date range (current month)
3. Click **Filter**
4. **Expected Results**:
   - Employee listed under "Stanbic IBTC Pension"
   - Employee Contribution: ₦40,000.00
   - Employer Contribution: ₦50,000.00
   - Total: ₦90,000.00

---

## Test Scenario 2: Employee Exempt from Pension

### Step 1: Create Employee Exempt from Pension
1. Navigate to **Payroll → Employees**
2. Click **Create Employee**
3. Fill in employee details:
   - First Name: `Jane`
   - Last Name: `Smith`
   - Basic Salary: `₦300,000`
   - **Pension Exempt**: ✓ **Check this box**
4. Click **Save**

### Step 2: Generate Payroll
1. Use the same payroll period from Test Scenario 1
2. Click **Generate Payroll** (or regenerate if already generated)

### Step 3: Verify No Pension Deduction
1. View the payslip for Jane Smith
2. **Expected Results**:
   ```
   Basic Salary:           ₦300,000.00
   Employee Pension (8%):  ₦0.00       (no deduction)
   Employer Pension (10%): ₦0.00       (no contribution)
   Total Pension:          ₦0.00
   ```

### Step 4: Check Statutory Report
1. Navigate to **Statutory → Pension Report**
2. Jane Smith should either:
   - Not appear in the report, OR
   - Appear with ₦0.00 contributions

---

## Test Scenario 3: Multiple Employees with Different PFAs

### Step 1: Create Employees with Different PFAs
Create 3 employees:

**Employee 1:**
- Name: `Alice Johnson`
- Basic Salary: `₦400,000`
- PFA Provider: `ARM Pension`
- RSA PIN: `PEN111111111`
- Pension Exempt: ✗ (unchecked)

**Employee 2:**
- Name: `Bob Williams`
- Basic Salary: `₦600,000`
- PFA Provider: `ARM Pension`
- RSA PIN: `PEN222222222`
- Pension Exempt: ✗ (unchecked)

**Employee 3:**
- Name: `Carol Brown`
- Basic Salary: `₦350,000`
- PFA Provider: `Leadway Pensure`
- RSA PIN: `PEN333333333`
- Pension Exempt: ✗ (unchecked)

### Step 2: Generate Payroll
1. Generate payroll for current period
2. All 3 employees should be included

### Step 3: Verify Pension Report Grouping
1. Navigate to **Statutory → Pension Report**
2. **Expected Results**:

**ARM Pension Group:**
- Alice Johnson: ₦32,000 (employee) + ₦40,000 (employer) = ₦72,000
- Bob Williams: ₦48,000 (employee) + ₦60,000 (employer) = ₦108,000
- **Subtotal**: ₦80,000 (employee) + ₦100,000 (employer) = ₦180,000

**Leadway Pensure Group:**
- Carol Brown: ₦28,000 (employee) + ₦35,000 (employer) = ₦63,000
- **Subtotal**: ₦28,000 (employee) + ₦35,000 (employer) = ₦63,000

**Grand Total:**
- Employee Contribution: ₦108,000
- Employer Contribution: ₦135,000
- Total: ₦243,000

---

## Test Scenario 4: Statutory Dashboard

### Step 1: View Statutory Dashboard
1. Navigate to **Statutory & Tax Management**
2. Check the **Pension Contributions** card

### Step 2: Verify Summary
**Expected Results**:
- Shows total pension contributions for current month
- Amount should match sum of all employee + employer contributions
- Click "View Details →" should navigate to pension report

---

## Database Verification (Optional)

### Check Employee Table
```sql
SELECT 
    first_name, 
    last_name, 
    pfa_provider, 
    rsa_pin, 
    pension_exempt 
FROM employees 
WHERE tenant_id = YOUR_TENANT_ID;
```

### Check Payroll Runs Table
```sql
SELECT 
    e.first_name,
    e.last_name,
    pr.basic_salary,
    pr.pension_employee,
    pr.pension_employer,
    (pr.pension_employee + pr.pension_employer) as total_pension
FROM payroll_runs pr
JOIN employees e ON pr.employee_id = e.id
WHERE pr.payroll_period_id = YOUR_PERIOD_ID;
```

---

## Common Issues & Solutions

### Issue 1: Pension Not Calculated
**Symptoms**: Pension shows ₦0.00 for all employees

**Solutions**:
1. Check if `pension_exempt` is set to `true` (should be `false`)
2. Verify basic salary is set for employee
3. Regenerate payroll after fixing employee data

### Issue 2: Wrong Pension Amount
**Symptoms**: Pension amount doesn't match 8% + 10%

**Solutions**:
1. Verify basic salary amount
2. Check calculation:
   - Employee: Basic Salary × 0.08
   - Employer: Basic Salary × 0.10
3. Clear cache: `php artisan optimize:clear`

### Issue 3: Employee Not Showing in Report
**Symptoms**: Employee missing from pension report

**Solutions**:
1. Check if employee has `pension_exempt = true`
2. Verify payroll was generated for that period
3. Check date range filter in report

### Issue 4: PFA Provider Not Grouping
**Symptoms**: Employees not grouped by PFA in report

**Solutions**:
1. Ensure `pfa_provider` field is filled for employees
2. Check spelling consistency (e.g., "ARM Pension" vs "ARM pension")
3. Empty PFA providers will be grouped under "Not Assigned"

---

## Calculation Examples

### Example 1: Basic Calculation
```
Basic Salary: ₦500,000

Employee Contribution (8%):
₦500,000 × 0.08 = ₦40,000

Employer Contribution (10%):
₦500,000 × 0.10 = ₦50,000

Total Pension:
₦40,000 + ₦50,000 = ₦90,000
```

### Example 2: Multiple Employees
```
Employee A: ₦300,000 basic
- Employee: ₦24,000
- Employer: ₦30,000
- Total: ₦54,000

Employee B: ₦450,000 basic
- Employee: ₦36,000
- Employer: ₦45,000
- Total: ₦81,000

Grand Total:
- Employee: ₦60,000
- Employer: ₦75,000
- Total: ₦135,000
```

---

## Supported PFA Providers

The system supports any PFA provider name. Common ones include:
- Stanbic IBTC Pension
- ARM Pension
- Leadway Pensure
- AIICO Pension
- PAL Pension
- Premium Pension
- Sigma Pensions
- AXA Mansard Pension
- Crusader Sterling Pensions
- NLPC Pension
- OAK Pensions
- Radix Pension
- Trustfund Pensions
- Veritas Glanvills Pensions
- IEI-Anchor Pension

---

## Testing Checklist

- [ ] Create employee with pension details
- [ ] Generate payroll
- [ ] Verify pension deduction in payslip
- [ ] Check pension report shows correct amounts
- [ ] Test pension-exempt employee (no deduction)
- [ ] Verify multiple PFAs group correctly
- [ ] Check statutory dashboard shows total
- [ ] Test date range filter in report
- [ ] Verify RSA PIN displays correctly
- [ ] Test with different salary amounts

---

## Support

If you encounter issues not covered in this guide:
1. Check application logs: `storage/logs/laravel.log`
2. Verify database migrations ran successfully
3. Clear cache: `php artisan optimize:clear`
4. Contact system administrator

---

## Notes

- Pension is calculated on **basic salary only**, not gross salary
- Employee contribution is **deducted** from net salary
- Employer contribution is **not deducted** from employee salary
- Pension-exempt employees have ₦0.00 for both contributions
- Report groups employees by PFA provider for easy remittance
