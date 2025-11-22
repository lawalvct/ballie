<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SharedResourcesSeeder::class,
            SuperAdminSeeder::class,
            PlanSeeder::class,
            PermissionsSeeder::class,
            UnitsSeeder::class,
            ProductCategoriesSeeder::class,
        ]);
    }
}
