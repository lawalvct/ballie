<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\CashRegister;

class PosMinimalSeeder extends Seeder
{
    public function run()
    {
        // Only add payment methods if they don't exist
        $this->seedPaymentMethods();

        // Only add cash register if none exist
        $this->seedCashRegister();
    }

    private function seedPaymentMethods()
    {
        $methods = [
            [
                'code' => 'cash',
                'name' => 'Cash Payment',
                'description' => 'Physical cash payment',
                'requires_reference' => false
            ],
            [
                'code' => 'card',
                'name' => 'Credit/Debit Card',
                'description' => 'Card payment via terminal',
                'requires_reference' => true
            ],
            [
                'code' => 'transfer',
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer',
                'requires_reference' => true
            ],
            [
                'code' => 'pos_terminal',
                'name' => 'POS Terminal',
                'description' => 'Electronic POS payment',
                'requires_reference' => true
            ],
            [
                'code' => 'mobile_money',
                'name' => 'Mobile Money',
                'description' => 'Mobile wallet payment',
                'requires_reference' => true
            ]
        ];

        foreach ($methods as $method) {
            $existingMethod = PaymentMethod::where('code', $method['code'])->first();

            if (!$existingMethod) {
                PaymentMethod::create([
                    'name' => $method['name'],
                    'code' => $method['code'],
                    'description' => $method['description'],
                    'is_active' => true,
                    'requires_reference' => $method['requires_reference'],
                    'charge_percentage' => 0.00,
                    'charge_amount' => 0.00
                ]);

                $this->command->info("Created payment method: {$method['name']}");
            } else {
                $this->command->info("Payment method already exists: {$method['name']}");
            }
        }

        $this->command->info('Payment methods processing completed.');
    }

    private function seedCashRegister()
    {
        if (CashRegister::count() == 0) {
            CashRegister::create([
                'name' => 'Main Register',
                'location' => 'Front Counter',
                'is_active' => true
            ]);

            $this->command->info('Cash register seeded successfully.');
        } else {
            $this->command->info('Cash registers already exist, skipping.');
        }
    }
}
