<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Models\CashRegister;

class PosInitializerService
{
    public static function initializeForTenant()
    {
        $tenant = tenant();

        if (!$tenant) {
            throw new \Exception('No tenant context available');
        }

        self::createDefaultPaymentMethods($tenant->id);
        self::createDefaultCashRegister($tenant->id);
    }

    private static function createDefaultPaymentMethods($tenantId)
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
            $existingMethod = PaymentMethod::where('tenant_id', $tenantId)
                ->where('code', $method['code'])
                ->first();

            if (!$existingMethod) {
                PaymentMethod::create([
                    'tenant_id' => $tenantId,
                    'name' => $method['name'],
                    'code' => $method['code'],
                    'description' => $method['description'],
                    'is_active' => true,
                    'requires_reference' => $method['requires_reference'],
                    'charge_percentage' => 0.00,
                    'charge_amount' => 0.00
                ]);
            }
        }
    }

    private static function createDefaultCashRegister($tenantId)
    {
        $existingRegister = CashRegister::where('tenant_id', $tenantId)->first();

        if (!$existingRegister) {
            CashRegister::create([
                'tenant_id' => $tenantId,
                'name' => 'Main Register',
                'location' => 'Front Counter',
                'is_active' => true
            ]);
        }
    }
}
