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
        Schema::create('payout_settings', function (Blueprint $table) {
            $table->id();

            // Deduction settings
            $table->enum('deduction_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('deduction_value', 8, 2)->default(5.00); // 5% or fixed amount
            $table->string('deduction_name')->default('Processing Fee');

            // Minimum payout amount
            $table->decimal('minimum_payout', 15, 2)->default(5000.00);

            // Maximum payout per request
            $table->decimal('maximum_payout', 15, 2)->nullable();

            // Processing time notice
            $table->string('processing_time')->default('3-5 business days');

            // Payout enabled
            $table->boolean('payouts_enabled')->default(true);

            // Terms and conditions for payouts
            $table->text('payout_terms')->nullable();

            $table->timestamps();
        });

        // Insert default settings
        DB::table('payout_settings')->insert([
            'deduction_type' => 'percentage',
            'deduction_value' => 5.00,
            'deduction_name' => 'Processing Fee',
            'minimum_payout' => 5000.00,
            'maximum_payout' => null,
            'processing_time' => '3-5 business days',
            'payouts_enabled' => true,
            'payout_terms' => 'Payout requests are processed within 3-5 business days. A processing fee will be deducted from each payout. Ensure your bank details are correct before submitting a request.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_settings');
    }
};
