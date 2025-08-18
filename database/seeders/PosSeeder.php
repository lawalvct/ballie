<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\CashRegister;
use App\Models\Tenant;

class PosSeeder extends Seeder
{
    public function run()
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Create default payment methods
            $paymentMethods = [
                [
                    'name' => 'Cash',
                    'code' => 'cash',
                    'requires_reference' => false,
                    'is_active' => true,
                    'description' => 'Cash payment',
                    'charge_percentage' => 0,
                    'charge_amount' => 0,
                ],
                [
                    'name' => 'Credit Card',
                    'code' => 'credit_card',
                    'requires_reference' => true,
                    'is_active' => true,
                    'description' => 'Credit card payment',
                    'charge_percentage' => 2.5,
                    'charge_amount' => 0,
                ],
                [
                    'name' => 'Debit Card',
                    'code' => 'debit_card',
                    'requires_reference' => true,
                    'is_active' => true,
                    'description' => 'Debit card payment',
                    'charge_percentage' => 1.5,
                    'charge_amount' => 0,
                ],
                [
                    'name' => 'Mobile Money',
                    'code' => 'mobile_money',
                    'requires_reference' => true,
                    'is_active' => true,
                    'description' => 'Mobile money transfer',
                    'charge_percentage' => 1,
                    'charge_amount' => 0,
                ],
                [
                    'name' => 'Bank Transfer',
                    'code' => 'bank_transfer',
                    'requires_reference' => true,
                    'is_active' => true,
                    'description' => 'Bank transfer payment',
                    'charge_percentage' => 0,
                    'charge_amount' => 50,
                ],
                [
                    'name' => 'Check',
                    'code' => 'check',
                    'requires_reference' => true,
                    'is_active' => true,
                    'description' => 'Check payment',
                    'charge_percentage' => 0,
                    'charge_amount' => 0,
                ],
            ];

            foreach ($paymentMethods as $method) {
                PaymentMethod::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'code' => $method['code']
                    ],
                    array_merge($method, ['tenant_id' => $tenant->id])
                );
            }

            // Create default cash register
            CashRegister::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => 'Main Register'
                ],
                [
                    'tenant_id' => $tenant->id,
                    'name' => 'Main Register',
                    'location' => 'Main Counter',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'is_active' => true,
                ]
            );

            // Create additional registers if needed
            $additionalRegisters = [
                [
                    'name' => 'Express Register',
                    'location' => 'Express Lane',
                ],
                [
                    'name' => 'Mobile Register',
                    'location' => 'Mobile/Tablet',
                ],
            ];

            foreach ($additionalRegisters as $register) {
                CashRegister::firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'name' => $register['name']
                    ],
                    array_merge($register, [
                        'tenant_id' => $tenant->id,
                        'opening_balance' => 0,
                        'current_balance' => 0,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
