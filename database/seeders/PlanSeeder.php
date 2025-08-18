<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small businesses and startups',
                'monthly_price' => 750000, // ₦7,500 in kobo
                'yearly_price' => 7650000, // ₦76,500 in kobo (15% discount)
                'max_users' => 5,
                'max_customers' => 100,
                'has_pos' => false,
                'has_payroll' => false,
                'has_api_access' => false,
                'has_advanced_reports' => false,
                'support_level' => 'email',
                'is_popular' => false,
                'sort_order' => 1,
                'features' => [
                    'Up to 5 users',
                    'Basic accounting features',
                    'Inventory management',
                    'Basic CRM',
                    'Standard reports',
                    'Email support',
                    'Mobile app access'
                ]
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'Ideal for growing businesses',
                'monthly_price' => 1000000, // ₦10,000 in kobo
                'yearly_price' => 10200000, // ₦102,000 in kobo (15% discount)
                'max_users' => 15,
                'max_customers' => null,
                'has_pos' => true,
                'has_payroll' => true,
                'has_api_access' => true,
                'has_advanced_reports' => true,
                'support_level' => 'priority',
                'is_popular' => true,
                'sort_order' => 2,
                'features' => [
                    'Up to 15 users',
                    'Advanced accounting features',
                    'Full inventory management',
                    'Advanced CRM & sales pipeline',
                    'POS system',
                    'Basic payroll management',
                    'Advanced reports & analytics',
                    'Priority support',
                    'API access'
                ]
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large businesses and corporations',
                'monthly_price' => 1500000, // ₦15,000 in kobo
                'yearly_price' => 15300000, // ₦153,000 in kobo (15% discount)
                'max_users' => null,
                'max_customers' => null,
                'has_pos' => true,
                'has_payroll' => true,
                'has_api_access' => true,
                'has_advanced_reports' => true,
                'support_level' => '24/7',
                'is_popular' => false,
                'sort_order' => 3,
                'features' => [
                    'Unlimited users',
                    'Full accounting suite',
                    'Multi-location inventory',
                    'Enterprise CRM & automation',
                    'Multi-location POS',
                    'Full payroll & HR management',
                    'Custom reports & dashboards',
                    '24/7 dedicated support',
                    'Advanced API & integrations',
                    'Custom training & onboarding'
                ]
            ]
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
