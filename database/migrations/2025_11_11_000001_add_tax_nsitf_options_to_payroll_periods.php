<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->boolean('apply_paye_tax')->default(true)->after('total_nsitf');
            $table->boolean('apply_nsitf')->default(true)->after('apply_paye_tax');
            $table->decimal('paye_tax_rate', 5, 2)->nullable()->after('apply_nsitf')->comment('Override PAYE rate if needed (0-100)');
            $table->decimal('nsitf_rate', 5, 2)->nullable()->after('paye_tax_rate')->comment('Override NSITF rate if needed (0-100)');
            $table->text('tax_exemption_reason')->nullable()->after('nsitf_rate');
        });
    }

    public function down()
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            $table->dropColumn([
                'apply_paye_tax',
                'apply_nsitf',
                'paye_tax_rate',
                'nsitf_rate',
                'tax_exemption_reason'
            ]);
        });
    }
};
