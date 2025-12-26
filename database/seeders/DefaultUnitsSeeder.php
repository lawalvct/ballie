<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\Unit;

class DefaultUnitsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->seedForTenant(null);
    }

    /**
     * Seed default units for a specific tenant
     */
    public static function seedForTenant($tenantId)
    {
        // Check if units already exist for this tenant
        $existingUnits = Unit::where('tenant_id', $tenantId)->count();
        if ($existingUnits > 0) {
            Log::info("Units already exist for tenant, skipping seeding", [
                'tenant_id' => $tenantId,
                'existing_count' => $existingUnits
            ]);
            return; // Skip seeding if units already exist
        }

        $units = [
            // Basic Units
            [
                'name' => 'Piece',
                'symbol' => 'pcs',
                'description' => 'Individual items or units',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],

            // Weight Units
            [
                'name' => 'Kilogram',
                'symbol' => 'kg',
                'description' => 'Weight measurement in kilograms',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Gram',
                'symbol' => 'g',
                'description' => 'Weight measurement in grams',
                'is_base_unit' => false,
                'base_unit_id' => null, // Will be set after kg is created
                'conversion_factor' => 0.001,
            ],

            // Volume Units
            [
                'name' => 'Litre',
                'symbol' => 'l',
                'description' => 'Volume measurement in litres',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Millilitre',
                'symbol' => 'ml',
                'description' => 'Volume measurement in millilitres',
                'is_base_unit' => false,
                'base_unit_id' => null, // Will be set after litre is created
                'conversion_factor' => 0.001,
            ],

            // Packaging Units
            [
                'name' => 'Pack',
                'symbol' => 'pack',
                'description' => 'Packaged items',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Carton',
                'symbol' => 'ctn',
                'description' => 'Items packed in cartons',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Dozen',
                'symbol' => 'doz',
                'description' => 'Set of 12 items',
                'is_base_unit' => false,
                'base_unit_id' => null, // Will be set to piece
                'conversion_factor' => 12.0,
            ],
            [
                'name' => 'Box',
                'symbol' => 'box',
                'description' => 'Items packed in boxes',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Bag',
                'symbol' => 'bag',
                'description' => 'Items packed in bags',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],

            // Length Units
            [
                'name' => 'Meter',
                'symbol' => 'm',
                'description' => 'Length measurement in meters',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Centimeter',
                'symbol' => 'cm',
                'description' => 'Length measurement in centimeters',
                'is_base_unit' => false,
                'base_unit_id' => null, // Will be set after meter is created
                'conversion_factor' => 0.01,
            ],

            // Other Units
            [
                'name' => 'Roll',
                'symbol' => 'roll',
                'description' => 'Items in roll form',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Bundle',
                'symbol' => 'bdl',
                'description' => 'Items bundled together',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Set',
                'symbol' => 'set',
                'description' => 'Complete set of items',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],

            // Additional Units requested
            [
                'name' => 'Each',
                'symbol' => 'ea',
                'description' => 'Individual each unit',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Hour',
                'symbol' => 'h',
                'description' => 'Time in hours',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Day',
                'symbol' => 'd',
                'description' => 'Time in days',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Month',
                'symbol' => 'mo',
                'description' => 'Time in months',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Session',
                'symbol' => 'session',
                'description' => 'Single session unit',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
            [
                'name' => 'Project',
                'symbol' => 'proj',
                'description' => 'Project-based unit',
                'is_base_unit' => true,
                'base_unit_id' => null,
                'conversion_factor' => 1.0,
            ],
        ];

        $createdUnits = [];

        // First pass: Create all base units and standalone units
        foreach ($units as $unitData) {
            $existingUnit = Unit::where('tenant_id', $tenantId)
                ->where('name', $unitData['name'])
                ->first();

            if (!$existingUnit) {
                $unit = Unit::create([
                    'tenant_id' => $tenantId,
                    'name' => $unitData['name'],
                    'symbol' => $unitData['symbol'],
                    'description' => $unitData['description'],
                    'is_base_unit' => $unitData['is_base_unit'],
                    'base_unit_id' => $unitData['base_unit_id'],
                    'conversion_factor' => $unitData['conversion_factor'],
                    'is_active' => true,
                ]);

                $createdUnits[$unitData['name']] = $unit;
            } else {
                $createdUnits[$unitData['name']] = $existingUnit;
            }
        }

        // Second pass: Update derived units with correct base_unit_id
        $derivedUnits = [
            'Gram' => 'Kilogram',
            'Millilitre' => 'Litre',
            'Dozen' => 'Piece',
            'Centimeter' => 'Meter',
        ];

        foreach ($derivedUnits as $derivedUnit => $baseUnit) {
            if (isset($createdUnits[$derivedUnit]) && isset($createdUnits[$baseUnit])) {
                $createdUnits[$derivedUnit]->update([
                    'base_unit_id' => $createdUnits[$baseUnit]->id
                ]);
            }
        }
    }
}
