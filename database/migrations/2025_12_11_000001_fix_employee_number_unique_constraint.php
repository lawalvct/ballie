<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['employee_number']);
            
            // Add composite unique constraint for tenant_id and employee_number
            $table->unique(['tenant_id', 'employee_number'], 'employees_tenant_employee_number_unique');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('employees_tenant_employee_number_unique');
            
            // Restore the old unique constraint
            $table->unique('employee_number');
        });
    }
};
