<?php

/**
 * Delete All Tenants Script
 *
 * This script deletes all tenants and their related records from the database.
 * It handles foreign key constraints by deleting in the correct order.
 *
 * WARNING: This is a destructive operation and cannot be undone!
 *
 * Usage: php delete_all_tenants.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant;

// Color output helpers
function colorOutput($text, $color = 'white') {
    $colors = [
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'reset' => "\033[0m",
    ];

    return $colors[$color] . $text . $colors['reset'];
}

function outputLine($text, $color = 'white') {
    echo colorOutput($text, $color) . PHP_EOL;
}

function outputSuccess($text) {
    outputLine("✓ " . $text, 'green');
}

function outputError($text) {
    outputLine("✗ " . $text, 'red');
}

function outputWarning($text) {
    outputLine("⚠ " . $text, 'yellow');
}

function outputInfo($text) {
    outputLine("ℹ " . $text, 'cyan');
}

// Start the deletion process
outputLine("\n" . str_repeat("=", 80), 'magenta');
outputLine("DELETE ALL TENANTS SCRIPT", 'magenta');
outputLine(str_repeat("=", 80) . "\n", 'magenta');

outputWarning("WARNING: This will delete ALL tenants and their related data!");
outputWarning("This operation CANNOT be undone!");
echo "\n";

// Count tenants
$tenantCount = Tenant::count();

if ($tenantCount === 0) {
    outputInfo("No tenants found in the database.");
    exit(0);
}

outputInfo("Found {$tenantCount} tenant(s) in the database.");
echo "\n";

// Ask for confirmation
outputLine("Are you sure you want to continue? (yes/no): ", 'yellow');
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$confirmation = trim(strtolower($line));
fclose($handle);

if ($confirmation !== 'yes') {
    outputInfo("Operation cancelled.");
    exit(0);
}

outputLine("\n" . str_repeat("-", 80), 'blue');
outputInfo("Starting deletion process...");
outputLine(str_repeat("-", 80) . "\n", 'blue');

try {
    // Disable foreign key checks temporarily
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    outputInfo("Foreign key checks disabled.");

    $deletedCounts = [];

    // List of tables with tenant_id in deletion order
    // Tables are ordered to handle dependencies first
    $tenantTables = [
        // Pivot and relationship tables first
        'tenant_users' => 'Tenant Users (Pivot)',
        'role_user' => 'User Roles (Pivot)',
        'permission_role' => 'Role Permissions (Pivot)',
        'team_user' => 'Team Users (Pivot)',

        // Child records that depend on other tenant tables
        'quotation_items' => 'Quotation Items',
        'sale_items' => 'Sale Items',
        'sale_payments' => 'Sale Payments',
        'order_items' => 'Order Items',
        'purchase_order_items' => 'Purchase Order Items',
        'cart_items' => 'Cart Items',
        'wishlist_items' => 'Wishlist Items',
        'voucher_entries' => 'Voucher Entries',
        'stock_journal_entry_items' => 'Stock Journal Entry Items',
        'physical_stock_entries' => 'Physical Stock Entries',
        'bank_reconciliation_items' => 'Bank Reconciliation Items',
        'invoice_items' => 'Invoice Items',
        'coupon_usages' => 'Coupon Usages',

        // Employee-related child records
        'attendance_records' => 'Attendance Records',
        'overtime_records' => 'Overtime Records',
        'employee_leaves' => 'Employee Leaves',
        'employee_leave_balances' => 'Employee Leave Balances',
        'employee_shift_assignments' => 'Employee Shift Assignments',
        'employee_loans' => 'Employee Loans',
        'employee_salaries' => 'Employee Salaries',
        'employee_salary_components' => 'Employee Salary Components',
        'employee_documents' => 'Employee Documents',
        'payroll_run_details' => 'Payroll Run Details',
        'payroll_runs' => 'Payroll Runs',
        'payroll_periods' => 'Payroll Periods',
        'employee_announcements' => 'Employee Announcements',
        'announcement_recipients' => 'Announcement Recipients',

        // Main entity tables
        'receipts' => 'Receipts',
        'sales' => 'Sales',
        'quotations' => 'Quotations',
        'orders' => 'Orders',
        'purchase_orders' => 'Purchase Orders',
        'invoices' => 'Invoices',
        'carts' => 'Carts',
        'wishlists' => 'Wishlists',
        'vouchers' => 'Vouchers',
        'stock_journal_entries' => 'Stock Journal Entries',
        'stock_movements' => 'Stock Movements',
        'physical_stock_vouchers' => 'Physical Stock Vouchers',
        'bank_reconciliations' => 'Bank Reconciliations',
        'banks' => 'Banks',
        'coupons' => 'Coupons',
        'shipping_addresses' => 'Shipping Addresses',

        // Affiliate-related tables
        'affiliate_commissions' => 'Affiliate Commissions',
        'affiliate_referrals' => 'Affiliate Referrals',
        'affiliate_payouts' => 'Affiliate Payouts',

        // Support system
        'support_ticket_replies' => 'Support Ticket Replies',
        'support_ticket_attachments' => 'Support Ticket Attachments',
        'support_ticket_status_histories' => 'Support Ticket Status Histories',
        'support_tickets' => 'Support Tickets',

        // HR & Organizational
        'employees' => 'Employees',
        'departments' => 'Departments',
        'positions' => 'Positions',
        'shift_schedules' => 'Shift Schedules',
        'leave_types' => 'Leave Types',
        'salary_components' => 'Salary Components',
        'public_holidays' => 'Public Holidays',
        'pfas' => 'PFAs (Pension Fund Administrators)',

        // Product-related
        'products' => 'Products',
        'product_images' => 'Product Images',
        'product_categories' => 'Product Categories',
        'units' => 'Units',

        // Customer & Vendor management
        'customers' => 'Customers',
        'customer_authentications' => 'Customer Authentications',
        'customer_activities' => 'Customer Activities',
        'vendors' => 'Vendors',

        // Accounting & Finance
        'voucher_types' => 'Voucher Types',
        'ledger_accounts' => 'Ledger Accounts',
        'account_groups' => 'Account Groups',
        'journal_entries' => 'Journal Entries',
        'journal_entry_details' => 'Journal Entry Details',

        // Settings & Configuration
        'payment_methods' => 'Payment Methods',
        'tax_brackets' => 'Tax Brackets',
        'tax_rates' => 'Tax Rates',
        'invoice_templates' => 'Invoice Templates',
        'cash_register_sessions' => 'Cash Register Sessions',
        'cash_registers' => 'Cash Registers',
        'shipping_methods' => 'Shipping Methods',
        'ecommerce_settings' => 'E-commerce Settings',
        'settings' => 'Settings',
        'knowledge_base_articles' => 'Knowledge Base Articles',

        // Users & Permissions (near the end)
        'users' => 'Users',
        'teams' => 'Teams',
        'roles' => 'Roles',
        'permissions' => 'Permissions',

        // Subscription & Payment
        'subscription_payments' => 'Subscription Payments',
        'subscriptions' => 'Subscriptions',

        // Domain management
        'domains' => 'Domains',

        // Invitations
        'tenant_invitations' => 'Tenant Invitations',

        // Backup records
        'backups' => 'Backups',
    ];

    outputInfo("Processing " . count($tenantTables) . " related tables...\n");

    foreach ($tenantTables as $table => $description) {
        if (!Schema::hasTable($table)) {
            outputWarning("Table '{$table}' does not exist. Skipping...");
            continue;
        }

        try {
            $count = DB::table($table)->count();

            if ($count > 0) {
                DB::table($table)->delete();
                $deletedCounts[$description] = $count;
                outputSuccess("Deleted {$count} record(s) from {$description} ({$table})");
            } else {
                outputInfo("No records in {$description} ({$table})");
            }
        } catch (\Exception $e) {
            outputError("Error deleting from {$table}: " . $e->getMessage());
        }
    }

    // Finally, delete the tenants themselves
    outputLine("\n" . str_repeat("-", 80), 'blue');
    outputInfo("Deleting tenants...");

    $deletedTenants = DB::table('tenants')->delete();
    outputSuccess("Deleted {$deletedTenants} tenant(s)");

    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    outputInfo("Foreign key checks re-enabled.");

    // Summary
    outputLine("\n" . str_repeat("=", 80), 'green');
    outputLine("DELETION SUMMARY", 'green');
    outputLine(str_repeat("=", 80), 'green');

    $totalDeleted = array_sum($deletedCounts) + $deletedTenants;

    outputSuccess("Total records deleted: " . number_format($totalDeleted));
    outputSuccess("Tenants deleted: {$deletedTenants}");

    if (!empty($deletedCounts)) {
        outputLine("\nDetailed breakdown:", 'cyan');
        foreach ($deletedCounts as $description => $count) {
            outputLine("  • {$description}: " . number_format($count), 'white');
        }
    }

    outputLine("\n" . str_repeat("=", 80), 'green');
    outputSuccess("All tenants and related records have been successfully deleted!");
    outputLine(str_repeat("=", 80) . "\n", 'green');

} catch (\Exception $e) {
    outputError("\nAn error occurred during deletion:");
    outputError($e->getMessage());
    outputError($e->getTraceAsString());

    // Try to re-enable foreign key checks even on error
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        outputInfo("Foreign key checks re-enabled.");
    } catch (\Exception $fkError) {
        outputError("Could not re-enable foreign key checks: " . $fkError->getMessage());
    }

    exit(1);
}
