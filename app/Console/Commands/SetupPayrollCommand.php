<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\Department;
use App\Models\SalaryComponent;
use App\Models\TaxBracket;

class SetupPayrollCommand extends Command
{
    protected $signature = 'payroll:setup {tenant_id?}';
    protected $description = 'Set up payroll system with default data for a tenant';

    public function handle()
    {
        $tenantId = $this->argument('tenant_id');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
        } else {
            $tenant = Tenant::first();
        }

        if (!$tenant) {
            $this->error('No tenant found. Please create a tenant first or specify a valid tenant ID.');
            return 1;
        }

        $this->info("Setting up payroll for tenant: {$tenant->name}");

        // Switch to tenant context
        $tenant->makeCurrent();

        // Create departments
        $this->createDepartments();

        // Create salary components
        $this->createSalaryComponents();

        // Create tax brackets
        $this->createTaxBrackets();

        $this->info('Payroll system setup completed successfully!');
        return 0;
    }

    private function createDepartments()
    {
        $departments = [
            ['name' => 'Administration', 'code' => 'ADM', 'description' => 'Administrative and executive staff'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Human resources and personnel management'],
            ['name' => 'Finance & Accounting', 'code' => 'FIN', 'description' => 'Financial and accounting operations'],
            ['name' => 'Sales & Marketing', 'code' => 'SAL', 'description' => 'Sales and marketing activities'],
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Day-to-day operations and logistics'],
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'Technology and systems management'],
            ['name' => 'Customer Service', 'code' => 'CS', 'description' => 'Customer support and relations'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['code' => $department['code']],
                $department
            );
        }

        $this->info('✓ Created ' . count($departments) . ' departments');
    }

    private function createSalaryComponents()
    {
        $salaryComponents = [
            // Earnings
            [
                'name' => 'Basic Salary',
                'code' => 'BASIC',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'is_mandatory' => true,
                'is_taxable' => true,
                'is_pensionable' => true,
                'description' => 'Base monthly salary',
                'sort_order' => 1
            ],
            [
                'name' => 'Housing Allowance',
                'code' => 'HOUSE',
                'type' => 'earning',
                'calculation_type' => 'percentage',
                'default_value' => 25.00,
                'is_mandatory' => false,
                'is_taxable' => true,
                'is_pensionable' => false,
                'description' => 'Housing allowance (25% of basic salary)',
                'sort_order' => 2
            ],
            [
                'name' => 'Transport Allowance',
                'code' => 'TRANS',
                'type' => 'earning',
                'calculation_type' => 'percentage',
                'default_value' => 10.00,
                'is_mandatory' => false,
                'is_taxable' => true,
                'is_pensionable' => false,
                'description' => 'Transport allowance (10% of basic salary)',
                'sort_order' => 3
            ],
            [
                'name' => 'Meal Allowance',
                'code' => 'MEAL',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'default_value' => 15000.00,
                'is_mandatory' => false,
                'is_taxable' => false,
                'is_pensionable' => false,
                'description' => 'Monthly meal allowance',
                'sort_order' => 4
            ],
            [
                'name' => 'Overtime Pay',
                'code' => 'OT',
                'type' => 'earning',
                'calculation_type' => 'variable',
                'is_mandatory' => false,
                'is_taxable' => true,
                'is_pensionable' => false,
                'description' => 'Overtime compensation',
                'sort_order' => 6
            ],

            // Deductions
            [
                'name' => 'Pay As You Earn (PAYE)',
                'code' => 'PAYE',
                'type' => 'deduction',
                'calculation_type' => 'computed',
                'is_mandatory' => true,
                'is_statutory' => true,
                'description' => 'Personal income tax deduction',
                'sort_order' => 10
            ],
            [
                'name' => 'Pension Contribution (Employee)',
                'code' => 'PENSION_EE',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'default_value' => 8.00,
                'is_mandatory' => true,
                'is_statutory' => true,
                'description' => 'Employee pension contribution (8%)',
                'sort_order' => 11
            ],
            [
                'name' => 'Health Insurance',
                'code' => 'HMO',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'default_value' => 5000.00,
                'is_mandatory' => false,
                'is_statutory' => false,
                'description' => 'Monthly health insurance premium',
                'sort_order' => 13
            ],

            // Employer Contributions
            [
                'name' => 'Pension Contribution (Employer)',
                'code' => 'PENSION_ER',
                'type' => 'employer_contribution',
                'calculation_type' => 'percentage',
                'default_value' => 10.00,
                'is_mandatory' => true,
                'is_statutory' => true,
                'description' => 'Employer pension contribution (10%)',
                'sort_order' => 20
            ],
            [
                'name' => 'Nigeria Social Insurance Trust Fund (NSITF)',
                'code' => 'NSITF',
                'type' => 'employer_contribution',
                'calculation_type' => 'percentage',
                'default_value' => 1.00,
                'is_mandatory' => true,
                'is_statutory' => true,
                'description' => 'NSITF contribution (1% of total emolument)',
                'sort_order' => 21
            ],
        ];

        foreach ($salaryComponents as $component) {
            SalaryComponent::firstOrCreate(
                ['code' => $component['code']],
                $component
            );
        }

        $this->info('✓ Created ' . count($salaryComponents) . ' salary components');
    }

    private function createTaxBrackets()
    {
        $taxBrackets = [
            [
                'name' => 'Tax Free',
                'min_amount' => 0.00,
                'max_amount' => 300000.00,
                'rate' => 0.00,
                'year' => 2024,
                'annual_relief' => 200000.00,
                'is_active' => true,
                'effective_from' => '2024-01-01',
                'description' => 'Tax-free income bracket'
            ],
            [
                'name' => 'First Bracket',
                'min_amount' => 300000.01,
                'max_amount' => 600000.00,
                'rate' => 7.00,
                'year' => 2024,
                'annual_relief' => 0.00,
                'is_active' => true,
                'effective_from' => '2024-01-01',
                'description' => '7% tax rate on income between ₦300,001 - ₦600,000'
            ],
            [
                'name' => 'Second Bracket',
                'min_amount' => 600000.01,
                'max_amount' => 1100000.00,
                'rate' => 11.00,
                'year' => 2024,
                'annual_relief' => 0.00,
                'is_active' => true,
                'effective_from' => '2024-01-01',
                'description' => '11% tax rate on income between ₦600,001 - ₦1,100,000'
            ],
            [
                'name' => 'Third Bracket',
                'min_amount' => 1100000.01,
                'max_amount' => 1600000.00,
                'rate' => 15.00,
                'year' => 2024,
                'annual_relief' => 0.00,
                'is_active' => true,
                'effective_from' => '2024-01-01',
                'description' => '15% tax rate on income between ₦1,100,001 - ₦1,600,000'
            ],
            [
                'name' => 'Fourth Bracket',
                'min_amount' => 1600000.01,
                'max_amount' => 3200000.00,
                'rate' => 19.00,
                'year' => 2024,
                'annual_relief' => 0.00,
                'is_active' => true,
                'effective_from' => '2024-01-01',
                'description' => '19% tax rate on income between ₦1,600,001 - ₦3,200,000'
            ],
            [
                'name' => 'Fifth Bracket',
                'min_amount' => 3200000.01,
                'max_amount' => null,
                'rate' => 21.00,
                'year' => 2024,
                'annual_relief' => 0.00,
                'is_active' => true,
                'effective_from' => '2024-01-01',
                'description' => '21% tax rate on income above ₦3,200,000'
            ],
        ];

        foreach ($taxBrackets as $bracket) {
            TaxBracket::firstOrCreate(
                [
                    'min_amount' => $bracket['min_amount'],
                    'max_amount' => $bracket['max_amount'],
                    'year' => $bracket['year']
                ],
                $bracket
            );
        }

        $this->info('✓ Created ' . count($taxBrackets) . ' tax brackets');
    }
}
