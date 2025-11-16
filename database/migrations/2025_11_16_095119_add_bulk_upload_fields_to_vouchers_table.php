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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('bulk_upload_reference')->nullable()->after('reference_number');
            $table->string('uploaded_file_name')->nullable()->after('bulk_upload_reference');
            $table->index('bulk_upload_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropIndex(['bulk_upload_reference']);
            $table->dropColumn(['bulk_upload_reference', 'uploaded_file_name']);
        });
    }
};
