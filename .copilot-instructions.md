# Project Instructions for AI Assistants

## ⚠️ CRITICAL: Database Commands - NEVER USE DESTRUCTIVE COMMANDS

**NEVER suggest or run these commands:**

-   ❌ `php artisan migrate:fresh`
-   ❌ `php artisan migrate:fresh --seed`
-   ❌ `php artisan migrate:reset`
-   ❌ `php artisan migrate:rollback --step=all`
-   ❌ `php artisan db:wipe`

**Reason:** This project has live data managed in phpMyAdmin. These commands will destroy all existing data.

---

## ✅ SAFE Database Commands

**ALWAYS use these safe commands instead:**

### For New Migrations:

```bash
# Check what migrations need to run
php artisan migrate:status

# Run only new/pending migrations (safe)
php artisan migrate

# Run specific migration file
php artisan migrate --path=database/migrations/2025_12_10_000008_create_payroll_run_details_table.php
```

### For Modifying Existing Tables:

```bash
# Create a new migration to ALTER existing table
php artisan make:migration add_column_to_table_name --table=table_name
php artisan make:migration update_column_in_table_name --table=table_name

# Then run the new migration
php artisan migrate
```

### For Seeders:

```bash
# Run specific seeder only (not all seeders)
php artisan db:seed --class=LeaveTypeSeeder
php artisan db:seed --class=SpecificSeeder
```

### After Changes:

```bash
# Clear caches
php artisan optimize:clear

# Or individual caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📋 Payroll System Architecture

### Salary Component Types:

-   **`earning`**: Income components (Housing, Transport, Bonus) - replaces old 'allowance'
-   **`deduction`**: Deductions (Pension, Tax, Union Dues)
-   **`employer_contribution`**: Employer-paid items (NSITF, Pension Employer Share)

### Calculation Types:

-   **`fixed`**: Fixed amount per period (₦50,000)
-   **`percentage`**: Percentage of basic salary (20% = 20% of basic)
-   **`variable`**: Amount changes each period (manually entered)
-   **`computed`**: Calculated by system logic (overtime, bonuses)

### Payroll Flow:

```
1. Basic Salary + Earnings = Gross Salary
2. Calculate PAYE Tax on taxable income
3. Subtract Deductions
4. Gross Salary - Total Deductions = Net Salary
```

### Database Tables:

-   `salary_components`: Master list of component types
-   `employee_salary_components`: Employee assignments
-   `payroll_run_details`: Snapshot per payroll period
-   `payroll_runs`: Main payroll records (NO tenant_id column)
-   `payroll_periods`: Payroll period definitions

### Important Query Pattern:

```php
// WRONG - payroll_runs has no tenant_id column
PayrollRun::where('tenant_id', $tenant->id)->get();

// CORRECT - filter through relationship
PayrollRun::whereHas('payrollPeriod', function($q) use ($tenant) {
    $q->where('tenant_id', $tenant->id);
})->get();
```

---

## 🔄 Recent Changes Applied (Nov 9, 2025)

### Files Modified:

1. ✅ **PayrollCalculator.php** - Uses 'earning' instead of 'allowance', stores component details
2. ✅ **PayrollPeriod.php** - Saves detailed component breakdown to PayrollRunDetail
3. ✅ **EmployeeSalary.php** - Updated to use 'earning' type
4. ✅ **SalaryComponent.php** - Added scopes for earnings/employer contributions
5. ✅ **PayrollController.php** - Fixed validation, queries, added documentation
6. ✅ **Payslip Views** - Display individual component breakdown
7. ✅ **Components Index View** - Changed "Allowances" to "Earnings"
8. ✅ **Processing Show View** - Fixed column names (total_gross, total_net)

### Migrations Applied:

-   ✅ `2025_11_09_174626_update_payroll_run_details_component_type_enum.php`
    -   Updated `component_type` enum from `('allowance', 'deduction')`
    -   To `('earning', 'deduction', 'employer_contribution')`

---

## 🛠️ Development Workflow

### When Adding New Features:

1. Create migrations for new tables/columns
2. Run `php artisan migrate` (never migrate:fresh)
3. Update models and relationships
4. Clear caches: `php artisan optimize:clear`
5. Test thoroughly

### When Modifying Existing Tables:

1. Create ALTER migration: `php artisan make:migration name --table=existing_table`
2. Write up() and down() methods
3. Run: `php artisan migrate --path=database/migrations/filename.php`
4. Verify in phpMyAdmin
5. Clear caches

### Before Committing:

1. Verify no destructive commands in code
2. Check migrations are incremental (not fresh)
3. Test on development data first
4. Backup database if unsure

---

## 📝 Notes for AI Assistants

-   This is a **multi-tenant Laravel application**
-   Existing data managed via **phpMyAdmin**
-   **Never** suggest dropping or truncating tables
-   Always check migration status before running migrations
-   Prefer ALTER migrations over recreation
-   Test queries with `whereHas()` for tenant isolation
-   Document any schema changes

---

## 🆘 Emergency Recovery

If destructive command was accidentally run:

```bash
# Restore from latest backup in phpMyAdmin
# Import .sql file: ballie_backup_YYYY_MM_DD.sql

# Or restore specific tables if backed up separately
```

**Always backup before major changes!**
