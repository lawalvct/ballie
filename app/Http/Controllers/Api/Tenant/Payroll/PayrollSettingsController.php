<?php

namespace App\Http\Controllers\Api\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayrollSettingsController extends Controller
{
    /**
     * Get payroll settings.
     */
    public function show(Tenant $tenant)
    {
        return response()->json([
            'success' => true,
            'message' => 'Payroll settings retrieved successfully',
            'data' => [
                'employee_number_format' => $tenant->employee_number_format ?? 'EMP-{YYYY}-{####}',
            ],
        ]);
    }

    /**
     * Update payroll settings.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'employee_number_format' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tenant->update([
            'employee_number_format' => $request->get('employee_number_format'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payroll settings updated successfully',
            'data' => [
                'employee_number_format' => $tenant->employee_number_format,
            ],
        ]);
    }
}
