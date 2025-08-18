<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('account_groups', function (Blueprint $table) {
            // Add fields for enhanced functionality
            $table->text('description')->nullable()->after('code');
            $table->enum('balance_type', ['dr', 'cr'])->nullable()->after('nature');
            $table->boolean('is_system_defined')->default(false)->after('is_active');
            $table->integer('sort_order')->default(0)->after('is_system_defined');
            $table->string('icon')->nullable()->after('sort_order');
            $table->string('color', 7)->nullable()->after('icon'); // For hex color codes

            // Add indexes
            $table->index(['tenant_id', 'nature']);
            $table->index(['tenant_id', 'parent_id', 'is_active']);
            $table->index(['sort_order']);
        });
    }

    public function down()
    {
        Schema::table('account_groups', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'nature']);
            $table->dropIndex(['tenant_id', 'parent_id', 'is_active']);
            $table->dropIndex(['sort_order']);

            $table->dropColumn([
                'description',
                'balance_type',
                'is_system_defined',
                'sort_order',
                'icon',
                'color'
            ]);
        });
    }
};
