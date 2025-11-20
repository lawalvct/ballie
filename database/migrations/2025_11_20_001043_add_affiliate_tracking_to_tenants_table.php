<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('referred_by_affiliate_id')->nullable()->after('created_by')->constrained('affiliates')->onDelete('set null');
            $table->string('referral_code')->nullable()->after('referred_by_affiliate_id'); // Store the code used
            $table->json('referral_data')->nullable()->after('referral_code'); // Store UTM params, source, etc.
            $table->timestamp('referral_registered_at')->nullable()->after('referral_data');

            $table->index('referred_by_affiliate_id');
            $table->index('referral_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['referred_by_affiliate_id']);
            $table->dropIndex(['referred_by_affiliate_id']);
            $table->dropIndex(['referral_code']);
            $table->dropColumn(['referred_by_affiliate_id', 'referral_code', 'referral_data', 'referral_registered_at']);
        });
    }
};
