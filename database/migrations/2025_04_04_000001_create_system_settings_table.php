<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->timestamps();
        });

        // Seed default system settings
        $defaults = [
            // General
            ['group' => 'general', 'key' => 'app_name', 'value' => 'Ballie', 'type' => 'string'],
            ['group' => 'general', 'key' => 'app_tagline', 'value' => 'Business Management Made Simple', 'type' => 'string'],
            ['group' => 'general', 'key' => 'support_email', 'value' => 'support@ballie.app', 'type' => 'string'],
            ['group' => 'general', 'key' => 'support_phone', 'value' => '', 'type' => 'string'],
            ['group' => 'general', 'key' => 'default_currency', 'value' => 'NGN', 'type' => 'string'],
            ['group' => 'general', 'key' => 'default_timezone', 'value' => 'Africa/Lagos', 'type' => 'string'],

            // Registration
            ['group' => 'registration', 'key' => 'registration_enabled', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'registration', 'key' => 'affiliate_registration_enabled', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'registration', 'key' => 'default_trial_days', 'value' => '14', 'type' => 'integer'],
            ['group' => 'registration', 'key' => 'max_companies_per_user', 'value' => '3', 'type' => 'integer'],
            ['group' => 'registration', 'key' => 'require_email_verification', 'value' => '1', 'type' => 'boolean'],

            // Payment Gateways
            ['group' => 'payment', 'key' => 'paystack_enabled', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'nomba_enabled', 'value' => '0', 'type' => 'boolean'],

            // Maintenance
            ['group' => 'maintenance', 'key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean'],
            ['group' => 'maintenance', 'key' => 'maintenance_message', 'value' => 'We are currently performing scheduled maintenance. Please check back soon.', 'type' => 'string'],
            ['group' => 'maintenance', 'key' => 'maintenance_allowed_ips', 'value' => '', 'type' => 'string'],

            // Security
            ['group' => 'security', 'key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer'],
            ['group' => 'security', 'key' => 'lockout_duration_minutes', 'value' => '15', 'type' => 'integer'],
            ['group' => 'security', 'key' => 'force_password_change_days', 'value' => '0', 'type' => 'integer'],
            ['group' => 'security', 'key' => 'two_factor_enforcement', 'value' => '0', 'type' => 'boolean'],
        ];

        $now = now();
        foreach ($defaults as &$setting) {
            $setting['created_at'] = $now;
            $setting['updated_at'] = $now;
        }

        \Illuminate\Support\Facades\DB::table('system_settings')->insert($defaults);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
