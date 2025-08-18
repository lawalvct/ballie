<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->seedUnitsForTenant($tenant);
        }
    }

    /**
     * Seed units for a specific tenant.
     */
    private function seedUnitsForTenant(Tenant $tenant): void
    {
        // Length Units
        $meter = Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Meter',
            'symbol' => 'm',
            'description' => 'Base unit of length in the metric system',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Centimeter',
            'symbol' => 'cm',
            'description' => 'One hundredth of a meter',
            'is_base_unit' => false,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 0.01,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Millimeter',
            'symbol' => 'mm',
            'description' => 'One thousandth of a meter',
            'is_base_unit' => false,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 0.001,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kilometer',
            'symbol' => 'km',
            'description' => 'One thousand meters',
            'is_base_unit' => false,
            'base_unit_id' => $meter->id,
            'conversion_factor' => 1000,
            'is_active' => true,
        ]);

        // Weight Units
        $kilogram = Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Kilogram',
            'symbol' => 'kg',
            'description' => 'Base unit of mass in the metric system',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Gram',
            'symbol' => 'g',
            'description' => 'One thousandth of a kilogram',
            'is_base_unit' => false,
            'base_unit_id' => $kilogram->id,
            'conversion_factor' => 0.001,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Pound',
            'symbol' => 'lb',
            'description' => 'Imperial unit of weight',
            'is_base_unit' => false,
            'base_unit_id' => $kilogram->id,
            'conversion_factor' => 0.453592,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Ounce',
            'symbol' => 'oz',
            'description' => 'Imperial unit of weight, 1/16 of a pound',
            'is_base_unit' => false,
            'base_unit_id' => $kilogram->id,
            'conversion_factor' => 0.0283495,
            'is_active' => true,
        ]);

        // Volume Units
        $liter = Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Liter',
            'symbol' => 'L',
            'description' => 'Base unit of volume in the metric system',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Milliliter',
            'symbol' => 'mL',
            'description' => 'One thousandth of a liter',
            'is_base_unit' => false,
            'base_unit_id' => $liter->id,
            'conversion_factor' => 0.001,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Gallon',
            'symbol' => 'gal',
            'description' => 'Imperial unit of volume',
            'is_base_unit' => false,
            'base_unit_id' => $liter->id,
            'conversion_factor' => 3.78541,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Quart',
            'symbol' => 'qt',
            'description' => 'Imperial unit of volume, 1/4 of a gallon',
            'is_base_unit' => false,
            'base_unit_id' => $liter->id,
            'conversion_factor' => 0.946353,
            'is_active' => true,
        ]);

        // Count/Quantity Units
        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Piece',
            'symbol' => 'pcs',
            'description' => 'Individual items or pieces',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Dozen',
            'symbol' => 'dz',
            'description' => 'Twelve pieces',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Gross',
            'symbol' => 'gr',
            'description' => 'Twelve dozen (144 pieces)',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Box',
            'symbol' => 'box',
            'description' => 'Box or carton',
            'is_base_unit' => true,
            'conversion_factor' => 1.0,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Pack',
            'symbol' => 'pack',
            'description' => 'Pack or package',
            'is_base_unit' => true,
            'conversion_factor' => 1.0,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Set',
            'symbol' => 'set',
            'description' => 'Set of items',
            'is_base_unit' => true,
            'conversion_factor' => 1.0,
        ]);

        // Area Units
        $squareMeter = Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Square Meter',
            'symbol' => 'm²',
            'description' => 'Base unit of area in the metric system',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Square Foot',
            'symbol' => 'ft²',
            'description' => 'Imperial unit of area',
            'is_base_unit' => false,
            'base_unit_id' => $squareMeter->id,
            'conversion_factor' => 0.092903,
            'is_active' => true,
        ]);

        // Time Units
        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Hour',
            'symbol' => 'hr',
            'description' => 'Unit of time',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Day',
            'symbol' => 'day',
            'description' => 'Unit of time',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Week',
            'symbol' => 'wk',
            'description' => 'Unit of time',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Month',
            'symbol' => 'mo',
            'description' => 'Unit of time',
            'is_base_unit' => true,
            'is_active' => true,
        ]);

        Unit::create([
            'tenant_id' => $tenant->id,
            'name' => 'Year',
            'symbol' => 'yr',
            'description' => 'Unit of time',
            'is_base_unit' => true,
            'is_active' => true,
        ]);
    }
}
