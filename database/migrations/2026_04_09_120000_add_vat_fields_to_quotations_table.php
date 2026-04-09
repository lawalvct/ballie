<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->boolean('vat_enabled')->default(false)->after('total_amount');
            $table->decimal('vat_amount', 15, 2)->default(0)->after('vat_enabled');
            $table->string('vat_applies_to', 50)->default('items_only')->after('vat_amount');
            $table->json('additional_charges')->nullable()->after('vat_applies_to');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->string('item_type', 20)->default('product')->after('quotation_id');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['vat_enabled', 'vat_amount', 'vat_applies_to', 'additional_charges']);
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn('item_type');
        });
    }
};
