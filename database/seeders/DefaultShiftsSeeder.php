<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\ShiftSchedule;

class DefaultShiftsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->seedForTenant(null);
    }

    /**
     * Seed default shifts for a specific tenant
     */
    public static function seedForTenant($tenantId)
    {
        // Check if shifts already exist for this tenant
        $existingShifts = ShiftSchedule::where('tenant_id', $tenantId)->count();
        if ($existingShifts > 0) {
            Log::info("Shifts already exist for tenant, skipping seeding", [
                'tenant_id' => $tenantId,
                'existing_count' => $existingShifts
            ]);
            return; // Skip seeding if shifts already exist
        }

        $shifts = [
            [
                'name' => 'Morning Shift',
                'code' => 'MS',
                'description' => 'Standard morning working hours',
                'start_time' => '08:00:00',
                'end_time' => '18:00:00',
                'work_hours' => 9,
                'break_minutes' => 60,
                'late_grace_minutes' => 15,
                'early_out_grace_minutes' => 15,
                'shift_allowance' => 0,
                'is_night_shift' => false,
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'is_active' => true,
                'is_default' => true,
                'color' => '#3b82f6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Evening Shift',
                'code' => 'ES',
                'description' => 'Evening working hours',
                'start_time' => '14:00:00',
                'end_time' => '22:00:00',
                'work_hours' => 8,
                'break_minutes' => 60,
                'late_grace_minutes' => 15,
                'early_out_grace_minutes' => 15,
                'shift_allowance' => 5000,
                'is_night_shift' => false,
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'is_active' => true,
                'is_default' => false,
                'color' => '#f59e0b',
                'sort_order' => 2,
            ],
            [
                'name' => 'Night Shift',
                'code' => 'NS',
                'description' => 'Night working hours',
                'start_time' => '22:00:00',
                'end_time' => '06:00:00',
                'work_hours' => 8,
                'break_minutes' => 60,
                'late_grace_minutes' => 15,
                'early_out_grace_minutes' => 15,
                'shift_allowance' => 10000,
                'is_night_shift' => true,
                'working_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'is_active' => true,
                'is_default' => false,
                'color' => '#6366f1',
                'sort_order' => 3,
            ],
        ];

        foreach ($shifts as $shiftData) {
            // Check if shift already exists for this tenant
            $existingShift = ShiftSchedule::where('tenant_id', $tenantId)
                ->where('code', $shiftData['code'])
                ->first();

            if (!$existingShift) {
                ShiftSchedule::create(array_merge([
                    'tenant_id' => $tenantId
                ], $shiftData));
            }
        }
    }
}
