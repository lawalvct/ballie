# 📊 Complete Guide: Using Salary Components in Payroll

## Overview

Salary components (earnings & deductions) are automatically calculated and displayed on employee payslips after you generate payroll. This guide shows you exactly where to see them.

---

## ✅ Step-by-Step Process

### **STEP 1: Create Salary Components**

1. Navigate to: **Payroll → Salary Components**
2. Click the **"Earnings"** tab
3. Click **"Add Earning"** button
4. Create components like:

    - **Housing Allowance** - Fixed Amount: ₦50,000
    - **Transport Allowance** - Fixed Amount: ₦30,000
    - **Meal Allowance** - Percentage: 10% (of basic salary)

5. Click the **"Deductions"** tab
6. Click **"Add Deduction"** button
7. Create components like:
    - **Pension (Employee)** - Percentage: 8%
    - **Loan Repayment** - Fixed Amount: ₦20,000
    - **Union Dues** - Fixed Amount: ₦5,000

---

### **STEP 2: Assign Components to Employees**

1. Navigate to: **Payroll → Employees**
2. Click **"Edit"** on any employee
3. Scroll down to: **"Salary Components (Allowances & Deductions)"** section
4. Select which **Earnings** the employee receives:

    - ☑ Housing Allowance
    - ☑ Transport Allowance
    - ☑ Meal Allowance

5. Select which **Deductions** apply to the employee:

    - ☑ Pension (Employee)
    - ☑ Loan Repayment

6. Click **"Save"**
7. Repeat for all employees who should receive these components

---

### **STEP 3: Create Payroll Period**

1. Navigate to: **Payroll → Payroll Processing**
2. Click **"New Payroll Period"**
3. Fill in:
    - Name: "November 2025 Payroll"
    - Type: Monthly
    - Start Date: 2025-11-01
    - End Date: 2025-11-30
    - Pay Date: 2025-11-30
4. Click **"Create Period"**

---

### **STEP 4: Generate Payroll**

1. On the newly created period, click **"Generate Payroll"** button
2. System will:

    - Calculate each employee's basic salary
    - ✅ **Add ALL assigned earning components** (Housing, Transport, Meal)
    - Calculate gross salary (Basic + Earnings)
    - Calculate PAYE tax
    - ✅ **Subtract ALL assigned deduction components** (Pension, Loan)
    - Calculate net salary

3. Wait for processing to complete
4. You'll see: **"Payroll generated successfully"**

---

### **STEP 5: View Salary Components on Payslips** ⭐

This is where you'll see your salary components in action!

#### **Option A: View in Period Details**

1. Click **"View Details"** on the payroll period
2. You'll see a table with all employees showing:
    - Basic Salary
    - **Allowances** (sum of all earnings)
    - **Deductions** (sum of all deductions)
    - Net Pay

#### **Option B: View Individual Payslip** (BEST VIEW!)

1. In the employee list, click the **👁️ (eye icon)** next to any employee
2. The payslip will open showing:

**EARNINGS SECTION:**

```
✅ Basic Salary         ₦200,000.00
✅ Housing Allowance    ₦50,000.00  ← Your component!
✅ Transport Allowance  ₦30,000.00  ← Your component!
✅ Meal Allowance       ₦20,000.00  ← Your component! (10% of basic)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   Gross Salary        ₦300,000.00
```

**DEDUCTIONS SECTION:**

```
✅ PAYE Tax             ₦15,000.00
✅ NSITF                ₦1,000.00
✅ Pension (Employee)   ₦16,000.00  ← Your component! (8% of basic)
✅ Loan Repayment       ₦20,000.00  ← Your component!
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   Total Deductions    ₦52,000.00
```

**NET PAY:**

```
💰 Net Pay             ₦248,000.00
```

3. Each component shows:
    - Component name (exactly as you created it)
    - Calculated amount
    - Properly formatted currency

#### **Option C: Download PDF Payslip**

1. Click the **⬇️ (download icon)** next to any employee
2. PDF will include the same detailed breakdown
3. Professional format ready for printing/emailing

---

## 📋 What's Stored in the Database

When payroll is generated, the system creates records in:

### **`payroll_run_details` table:**

Each earning and deduction component creates ONE row:

| id  | payroll_run_id | salary_component_id | component_name      | component_type | amount | is_taxable |
| --- | -------------- | ------------------- | ------------------- | -------------- | ------ | ---------- |
| 1   | 101            | 5                   | Housing Allowance   | earning        | 50000  | 1          |
| 2   | 101            | 6                   | Transport Allowance | earning        | 30000  | 1          |
| 3   | 101            | 7                   | Meal Allowance      | earning        | 20000  | 1          |
| 4   | 101            | 10                  | Pension (Employee)  | deduction      | 16000  | 0          |
| 5   | 101            | 11                  | Loan Repayment      | deduction      | 20000  | 0          |

This is a **snapshot** of components at the time payroll was generated, so even if you change component amounts later, historical payslips remain accurate.

---

## 🎨 Visual Flow Diagram

```
┌─────────────────────────┐
│ 1. CREATE COMPONENTS    │
│   - Housing: ₦50,000    │
│   - Transport: ₦30,000  │
│   - Loan: ₦20,000       │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ 2. ASSIGN TO EMPLOYEES  │
│   Employee: John Doe    │
│   ☑ Housing             │
│   ☑ Transport           │
│   ☑ Loan                │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ 3. CREATE PAYROLL       │
│   Period: Nov 2025      │
│   Status: Draft         │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ 4. GENERATE PAYROLL     │
│   PayrollCalculator     │
│   - Fetch components    │
│   - Calculate amounts   │
│   - Store in details    │
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ 5. VIEW PAYSLIP         │
│   ✅ Basic: ₦200,000    │
│   ✅ Housing: ₦50,000   │ ← Shows here!
│   ✅ Transport: ₦30,000 │ ← Shows here!
│   ━━━━━━━━━━━━━━━━━━━  │
│   ✅ Loan: -₦20,000     │ ← Shows here!
│   ━━━━━━━━━━━━━━━━━━━  │
│   💰 Net: ₦260,000      │
└─────────────────────────┘
```

---

## 🔍 How to Verify Components Are Working

### **Test 1: Check Calculation**

1. Create employee with Basic Salary: ₦100,000
2. Assign Housing Allowance (Fixed ₦50,000)
3. Generate payroll
4. Open payslip
5. **Expected:** Gross Salary = ₦150,000 (₦100,000 + ₦50,000)

### **Test 2: Check Percentage Calculation**

1. Create employee with Basic Salary: ₦200,000
2. Assign Meal Allowance (10% percentage)
3. Generate payroll
4. Open payslip
5. **Expected:** Meal Allowance shows ₦20,000 (10% of ₦200,000)

### **Test 3: Check Multiple Components**

1. Create employee with Basic Salary: ₦300,000
2. Assign:
    - Housing: ₦50,000 (fixed)
    - Transport: ₦30,000 (fixed)
    - Bonus: 5% (percentage = ₦15,000)
    - Pension: 8% (deduction = ₦24,000)
3. Generate payroll
4. Open payslip
5. **Expected:**
    - Gross: ₦395,000 (₦300k + ₦50k + ₦30k + ₦15k)
    - After Pension: ₦371,000 (₦395k - ₦24k)

---

## 🚨 Common Issues

### **Issue 1: Components Not Showing on Payslip**

**Possible Causes:**

-   Components were created but not assigned to employee
-   Payroll was generated before components were assigned
-   Component status is inactive

**Solution:**

1. Go to Employees → Edit → Check "Salary Components" section
2. Ensure components are checked/selected
3. Save employee
4. Re-generate payroll period (if already generated)

---

### **Issue 2: Wrong Amounts Calculated**

**Possible Causes:**

-   Calculation type mismatch (fixed vs percentage)
-   Basic salary not set correctly
-   Component value entered wrong

**Solution:**

1. Go to Salary Components → Find the component
2. Verify:
    - Calculation Type: Fixed or Percentage?
    - Value: Is it correct?
    - Is Taxable: Set correctly?
3. Edit if needed
4. Re-generate payroll

---

### **Issue 3: Old Components Still Showing**

**Explanation:** This is by design! Once payroll is generated, it creates a snapshot in `payroll_run_details` table. This preserves historical accuracy.

**To Update:**

1. Edit components or assignments
2. Create NEW payroll period
3. Generate fresh payroll
4. New period will use updated values

---

## 🎯 Quick Reference

| Action             | Location                      | Button/Link                     |
| ------------------ | ----------------------------- | ------------------------------- |
| Create Components  | Payroll → Salary Components   | "Add Earning" / "Add Deduction" |
| Assign to Employee | Payroll → Employees → Edit    | Scroll to "Salary Components"   |
| Create Period      | Payroll → Payroll Processing  | "New Payroll Period"            |
| Generate Payroll   | Period Details Page           | "Generate Payroll"              |
| View Components    | Period Details → Employee Row | 👁️ Eye Icon                     |
| Download PDF       | Period Details → Employee Row | ⬇️ Download Icon                |

---

## 📧 Additional Features

### **Email Payslip** (Coming Soon)

Click the ✉️ envelope icon to send payslip with component breakdown to employee's email.

### **Export Bank File** (Available)

After approving payroll, export bank upload file with net pay (after all components).

### **Payroll Reports** (Available)

Generate reports showing:

-   Total earnings by component type
-   Total deductions by component type
-   Component-wise analysis

---

## 🎓 Summary

**Salary components ARE working and ARE being used!**

They appear on:

1. ✅ Individual payslips (web view)
2. ✅ PDF payslips (download)
3. ✅ Period summary (as totals)
4. ✅ Database records (payroll_run_details table)

**The key is:**

1. Create components → Assign to employees → Generate payroll → View payslip
2. Components show ONLY AFTER payroll is generated
3. Each employee's payslip shows ONLY components assigned to them

---

## 🆘 Need Help?

If components still don't show:

1. Check browser console for JavaScript errors
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify database: `SELECT * FROM payroll_run_details WHERE payroll_run_id = [your_run_id]`
4. Clear cache: `php artisan optimize:clear`

---

**Document Created:** November 9, 2025
**System:** Ballie Payroll Management
**Version:** 1.0
