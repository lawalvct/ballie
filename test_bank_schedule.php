<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PayrollPeriod;
use App\Models\Tenant;

echo "=== CHECKING BANK SCHEDULE DATA ===\n\n";

// Get ALL tenants
$tenants = Tenant::all();

if ($tenants->count() === 0) {
    echo "❌ No tenants found in database\n";
    exit(1);
}

echo "📋 Found " . $tenants->count() . " tenant(s)\n\n";

// Check each tenant for payroll periods
foreach ($tenants as $tenant) {
    echo "=== TENANT: {$tenant->name} (ID: {$tenant->id}) ===\n";

    // Get ALL payroll periods for this tenant
    $allPeriods = PayrollPeriod::where('tenant_id', $tenant->id)
        ->with(['payrollRuns'])
        ->orderBy('created_at', 'desc')
        ->get();

echo "📊 Total Payroll Periods: " . $allPeriods->count() . "\n\n";

if ($allPeriods->count() > 0) {
    echo "Status Breakdown:\n";
    foreach ($allPeriods->groupBy('status') as $status => $periods) {
        echo "  - {$status}: " . $periods->count() . " period(s)\n";
    }
    echo "\n";

    echo "=== ALL PAYROLL PERIODS ===\n";
    foreach ($allPeriods as $period) {
        $employeeCount = $period->payrollRuns->count();
        echo "\n";
        echo "Period: {$period->name}\n";
        echo "  ID: {$period->id}\n";
        echo "  Status: {$period->status}\n";
        echo "  Period: {$period->start_date->format('Y-m-d')} to {$period->end_date->format('Y-m-d')}\n";
        echo "  Pay Date: {$period->pay_date->format('Y-m-d')}\n";
        echo "  Employees: {$employeeCount}\n";
        echo "  Gross: ₦" . number_format($period->total_gross ?? 0, 2) . "\n";
        echo "  Net: ₦" . number_format($period->total_net ?? 0, 2) . "\n";
    }
    echo "\n";

    // Check specifically for approved periods
    $approvedPeriods = $allPeriods->where('status', 'approved');
    echo "=== APPROVED PERIODS (Should show in Bank Schedule) ===\n";
    if ($approvedPeriods->count() > 0) {
        foreach ($approvedPeriods as $period) {
            echo "\n✅ APPROVED: {$period->name}\n";
            echo "   Pay Date: {$period->pay_date->format('Y-m-d')} (Year: {$period->pay_date->year}, Month: {$period->pay_date->month})\n";
            echo "   Net Amount: ₦" . number_format($period->total_net ?? 0, 2) . "\n";
        }
    } else {
        echo "❌ No approved payroll periods found!\n";
        echo "   This is why the bank schedule shows 'No bank payments scheduled'\n";
    }
} else {
    echo "❌ No payroll periods found for this tenant\n";
}

echo "\n" . str_repeat("=", 60) . "\n\n";
}

echo "=== TEST COMPLETE ===\n";
