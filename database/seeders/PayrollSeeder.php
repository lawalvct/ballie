<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\SalaryComponent;
use App\Models\TaxBracket;
use App\Models\Tenant;

class PayrollSeeder extends Seeder
{
    public function run()
    {
        // Get the first tenant or create one for testing
        $tenant = Tenant::first();
        if (!$tenant) {
            $this->command->error('No tenant found. Please create a tenant first.');
            return;
        }

        // Switch to tenant context
        $tenant->makeCurrent();

        // Create default departments
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
            Department::create($department);
        }

        // Create default salary components
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
                'name' => 'Medical Allowance',
                'code' => 'MED',
                'type' => 'earning',
                'calculation_type' => 'fixed',
                'default_value' => 20000.00,
                'is_mandatory' => false,
                'is_taxable' => false,
                'is_pensionable' => false,
                'description' => 'Medical allowance',
                'sort_order' => 5
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
            [
                'name' => 'Bonus',
                'code' => 'BONUS',
                'type' => 'earning',
                'calculation_type' => 'variable',
                'is_mandatory' => false,
                'is_taxable' => true,
                'is_pensionable' => false,
                'description' => 'Performance or special bonus',
                'sort_order' => 7
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
                'name' => 'National Housing Fund (NHF)',
                'code' => 'NHF',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'default_value' => 2.50,
                'is_mandatory' => false,
                'is_statutory' => true,
                'description' => 'National Housing Fund contribution (2.5%)',
                'sort_order' => 12
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
            [
                'name' => 'Life Insurance',
                'code' => 'LIFE',
                'type' => 'deduction',
                'calculation_type' => 'percentage',
                'default_value' => 1.00,
                'is_mandatory' => false,
                'is_statutory' => false,
                'description' => 'Life insurance premium (1% of basic)',
                'sort_order' => 14
            ],
            [
                'name' => 'Union Dues',
                'code' => 'UNION',
                'type' => 'deduction',
                'calculation_type' => 'fixed',
                'default_value' => 2000.00,
                'is_mandatory' => false,
                'is_statutory' => false,
                'description' => 'Monthly union membership dues',
                'sort_order' => 15
            ],
            [
                'name' => 'Loan Repayment',
                'code' => 'LOAN',
                'type' => 'deduction',
                'calculation_type' => 'variable',
                'is_mandatory' => false,
                'is_statutory' => false,
                'description' => 'Employee loan repayment',
                'sort_order' => 16
            ],
            [
                'name' => 'Advance Salary Deduction',
                'code' => 'ADVANCE',
                'type' => 'deduction',
                'calculation_type' => 'variable',
                'is_mandatory' => false,
                'is_statutory' => false,
                'description' => 'Salary advance recovery',
                'sort_order' => 17
            ],

            // Employer Contributions (for accounting purposes)
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
            [
                'name' => 'Industrial Training Fund (ITF)',
                'code' => 'ITF',
                'type' => 'employer_contribution',
                'calculation_type' => 'percentage',
                'default_value' => 1.00,
                'is_mandatory' => true,
                'is_statutory' => true,
                'description' => 'ITF contribution (1% of total emolument)',
                'sort_order' => 22
            ],
        ];

        foreach ($salaryComponents as $component) {
            SalaryComponent::create($component);
        }

        // Create Nigerian tax brackets for 2024
        $taxBrackets = [
            [
                'name' => 'Tax Free',
                'min_amount' => 0.00,
                'max_amount' => 300000.00,
                'rate' => 0.00,
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
                'annual_relief' => 0.00,
                'is_active' => true,
                'effective_from' => '2024-01-01',
                'description' => '21% tax rate on income above ₦3,200,000'
            ],
        ];

        foreach ($taxBrackets as $bracket) {
            TaxBracket::create($bracket);
        }

        $this->command->info('Payroll default data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . count($departments) . ' departments');
        $this->command->info('- ' . count($salaryComponents) . ' salary components');
        $this->command->info('- ' . count($taxBrackets) . ' tax brackets');
    }
}
