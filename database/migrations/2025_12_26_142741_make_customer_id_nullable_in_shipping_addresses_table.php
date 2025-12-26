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
        Schema::table('shipping_addresses', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['customer_id']);

            // Make customer_id nullable
            $table->foreignId('customer_id')->nullable()->change()->constrained('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_addresses', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['customer_id']);

            // Make customer_id non-nullable again
            $table->foreignId('customer_id')->nullable(false)->change()->constrained('customers')->onDelete('cascade');
        });
    }
};
