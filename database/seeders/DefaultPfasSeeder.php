<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pfa;
use Illuminate\Support\Facades\Log;

class DefaultPfasSeeder extends Seeder
{
    public static function seedForTenant($tenantId)
    {
        // Check if PFAs already exist for this tenant
        $existingPfas = Pfa::where('tenant_id', $tenantId)->count();
        if ($existingPfas > 0) {
            Log::info("PFAs already exist for tenant, skipping seeding", [
                'tenant_id' => $tenantId,
                'existing_count' => $existingPfas
            ]);
            return; // Skip seeding if PFAs already exist
        }

        $pfas = [
            ['name' => 'Stanbic IBTC Pension', 'code' => 'SIBTC', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'ARM Pension', 'code' => 'ARM', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Leadway Pensure', 'code' => 'LEADWAY', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'AIICO Pension', 'code' => 'AIICO', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'PAL Pension', 'code' => 'PAL', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Premium Pension', 'code' => 'PREMIUM', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'AXA Mansard Pension', 'code' => 'AXA', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Crusader Sterling Pensions', 'code' => 'CRUSADER', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'FCMB Pensions', 'code' => 'FCMB', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Fidelity Pension', 'code' => 'FIDELITY', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'IEI-Anchor Pension', 'code' => 'IEI', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Investment One Pension', 'code' => 'INVONE', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'NPF Pensions', 'code' => 'NPF', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'OAK Pensions', 'code' => 'OAK', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Pensions Alliance', 'code' => 'PALLIANCE', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Radix Pension', 'code' => 'RADIX', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Sigma Pensions', 'code' => 'SIGMA', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Trustfund Pensions', 'code' => 'TRUSTFUND', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
            ['name' => 'Veritas Glanvills Pensions', 'code' => 'VERITAS', 'contact_person' => null, 'email' => null, 'phone' => null, 'address' => null],
        ];

        $created = 0;
        foreach ($pfas as $pfaData) {
            $exists = Pfa::where('tenant_id', $tenantId)
                ->where('code', $pfaData['code'])
                ->exists();

            if (!$exists) {
                Pfa::create(array_merge($pfaData, [
                    'tenant_id' => $tenantId,
                    'is_active' => true,
                ]));
                $created++;
            }
        }

        Log::info("Default PFAs seeded for tenant", [
            'tenant_id' => $tenantId,
            'created' => $created,
            'total' => count($pfas)
        ]);
    }

    public function run()
    {
        // This method is not used for tenant-specific seeding
    }
}
