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
        Schema::table('customers', function (Blueprint $table) {
            // E-commerce specific fields
            $table->boolean('has_online_account')->default(false)->after('status');
            $table->enum('registration_source', ['admin', 'online_store', 'import'])->default('admin')->after('has_online_account');

            // Index
            $table->index('has_online_account');
            $table->index('registration_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['has_online_account']);
            $table->dropIndex(['registration_source']);

            $table->dropColumn([
                'has_online_account',
                'registration_source'
            ]);
        });
    }
};
