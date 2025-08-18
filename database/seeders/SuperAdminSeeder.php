<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::create([
            'name' => 'Super Administrator',
            'email' => 'admin@ballie.ng',
            'password' => Hash::make('password'),
            'role' => SuperAdmin::ROLE_SUPER_ADMIN,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        SuperAdmin::create([
            'name' => 'Support Admin',
            'email' => 'support@ballie.ng',
            'password' => Hash::make('password'),
            'role' => SuperAdmin::ROLE_SUPPORT,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
