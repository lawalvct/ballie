<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SetupSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the first super admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Super Admin...');

        if (SuperAdmin::count() > 0) {
            $this->error('Super Admin already exists!');
            return 1;
        }

        $name = $this->ask('Enter super admin name');
        $email = $this->ask('Enter super admin email');
        $password = $this->secret('Enter password');
        $passwordConfirmation = $this->secret('Confirm password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:super_admins,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        SuperAdmin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);

        $this->info('Super Admin created successfully!');
        $this->info("Email: {$email}");

        return 0;
    }
}
