<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeleteTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:delete
                            {tenantId : Tenant ID to delete}
                            {--force : Force deletion without confirmation}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a single tenant and its related records from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = (int) $this->argument('tenantId');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found.");
            return self::FAILURE;
        }

        $this->info('╔═══════════════════════════════════════════════════════════════════════════╗');
        $this->info('║                         DELETE SINGLE TENANT COMMAND                       ║');
        $this->info('╚═══════════════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No data will be deleted');
            $this->newLine();
        }

        $this->info('Tenant to be deleted:');
        $this->table(
            ['ID', 'Name', 'Slug', 'Email', 'Status', 'Created'],
            [[
                $tenant->id,
                $tenant->name,
                $tenant->slug,
                $tenant->email,
                $tenant->subscription_status,
                optional($tenant->created_at)->format('Y-m-d H:i'),
            ]]
        );

        if (!$force && !$dryRun) {
            $this->error('⚠️  WARNING: This will delete the tenant and ALL related data!');
            $this->error('⚠️  This operation CANNOT be undone!');
            $this->newLine();

            if (!$this->confirm('Are you absolutely sure you want to continue?', false)) {
                $this->info('Operation cancelled.');
                return self::SUCCESS;
            }

            if (!$this->confirm("Please confirm again. Delete tenant #{$tenantId}?", false)) {
                $this->info('Operation cancelled.');
                return self::SUCCESS;
            }
        }

        $this->newLine();
        $this->line('─────────────────────────────────────────────────────────────────────────────');
        $this->info('Starting deletion process...');
        $this->line('─────────────────────────────────────────────────────────────────────────────');
        $this->newLine();

        try {
            $deletedCounts = [];

            if (!$dryRun) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $this->comment('Foreign key checks disabled.');
            }

            $tenantTables = $this->getTenantTables();

            $progressBar = $this->output->createProgressBar(count($tenantTables));
            $progressBar->setFormat('verbose');
            $this->info('Processing related tables...');
            $this->newLine();

            foreach ($tenantTables as $table => $description) {
                if (!Schema::hasTable($table)) {
                    $this->warn("Table '{$table}' does not exist. Skipping...");
                    $progressBar->advance();
                    continue;
                }

                if (!Schema::hasColumn($table, 'tenant_id')) {
                    $this->warn("Table '{$table}' does not have tenant_id. Skipping...");
                    $progressBar->advance();
                    continue;
                }

                try {
                    $count = DB::table($table)->where('tenant_id', $tenantId)->count();

                    if ($count > 0) {
                        if ($dryRun) {
                            $this->comment("[DRY RUN] Would delete {$count} record(s) from {$description} ({$table})");
                        } else {
                            DB::table($table)->where('tenant_id', $tenantId)->delete();
                            $this->line("<fg=green>✓</> Deleted {$count} record(s) from {$description} ({$table})");
                        }
                        $deletedCounts[$description] = $count;
                    } else {
                        $this->comment("No records in {$description} ({$table})");
                    }
                } catch (\Exception $e) {
                    $this->error("✗ Error processing {$table}: " . $e->getMessage());
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->line('─────────────────────────────────────────────────────────────────────────────');
            $this->info('Processing tenants table...');

            if ($dryRun) {
                $this->comment("[DRY RUN] Would delete tenant #{$tenantId}");
                $deletedTenants = 1;
            } else {
                $deletedTenants = DB::table('tenants')->where('id', $tenantId)->delete();
                $this->line("<fg=green>✓</> Deleted tenant #{$tenantId}");

                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                $this->comment('Foreign key checks re-enabled.');
            }

            $this->newLine();
            $this->line('═════════════════════════════════════════════════════════════════════════════');
            $this->info('DELETION SUMMARY');
            $this->line('═════════════════════════════════════════════════════════════════════════════');

            $totalDeleted = array_sum($deletedCounts) + $deletedTenants;

            $this->info("Total records " . ($dryRun ? 'to be deleted' : 'deleted') . ": " . number_format($totalDeleted));
            $this->info("Tenants " . ($dryRun ? 'to be deleted' : 'deleted') . ": {$deletedTenants}");

            if (!empty($deletedCounts)) {
                $this->newLine();
                $this->comment('Detailed breakdown:');

                $categorized = $this->categorizeDeletions($deletedCounts);

                foreach ($categorized as $category => $items) {
                    $this->newLine();
                    $this->line("<fg=cyan>{$category}:</>");
                    foreach ($items as $description => $count) {
                        $this->line("  • {$description}: <fg=yellow>" . number_format($count) . "</>");
                    }
                }
            }

            $this->newLine();
            $this->line('═════════════════════════════════════════════════════════════════════════════');

            if ($dryRun) {
                $this->warn('DRY RUN COMPLETED - No data was actually deleted');
                $this->info('Run without --dry-run flag to perform actual deletion');
            } else {
                $this->info('✓ Tenant and related records have been successfully deleted!');
            }

            $this->line('═════════════════════════════════════════════════════════════════════════════');
            $this->newLine();

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("\n✗ An error occurred during deletion:");
            $this->error($e->getMessage());
            $this->newLine();
            $this->error($e->getTraceAsString());

            if (!$dryRun) {
                try {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    $this->comment('Foreign key checks re-enabled.');
                } catch (\Exception $fkError) {
                    $this->error('Could not re-enable foreign key checks: ' . $fkError->getMessage());
                }
            }

            return self::FAILURE;
        }
    }

    /**
     * Get list of tenant tables in deletion order.
     */
    private function getTenantTables(): array
    {
        return [
            'tenant_users' => 'Tenant Users (Pivot)',
            'role_user' => 'User Roles (Pivot)',
            'permission_role' => 'Role Permissions (Pivot)',
            'permission_user' => 'User Permissions (Pivot)',
            'team_user' => 'Team Users (Pivot)',

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

            'affiliate_commissions' => 'Affiliate Commissions',
            'affiliate_referrals' => 'Affiliate Referrals',
            'affiliate_payouts' => 'Affiliate Payouts',

            'support_ticket_replies' => 'Support Ticket Replies',
            'support_ticket_attachments' => 'Support Ticket Attachments',
            'support_ticket_status_histories' => 'Support Ticket Status Histories',
            'support_tickets' => 'Support Tickets',

            'employees' => 'Employees',
            'departments' => 'Departments',
            'positions' => 'Positions',
            'shift_schedules' => 'Shift Schedules',
            'leave_types' => 'Leave Types',
            'salary_components' => 'Salary Components',
            'public_holidays' => 'Public Holidays',
            'pfas' => 'PFAs (Pension Fund Administrators)',

            'products' => 'Products',
            'product_images' => 'Product Images',
            'product_categories' => 'Product Categories',
            'units' => 'Units',

            'customers' => 'Customers',
            'customer_authentications' => 'Customer Authentications',
            'customer_activities' => 'Customer Activities',
            'vendors' => 'Vendors',

            'voucher_types' => 'Voucher Types',
            'ledger_accounts' => 'Ledger Accounts',
            'account_groups' => 'Account Groups',
            'journal_entries' => 'Journal Entries',
            'journal_entry_details' => 'Journal Entry Details',

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

            'users' => 'Users',
            'teams' => 'Teams',
            'roles' => 'Roles',
            'permissions' => 'Permissions',

            'subscription_payments' => 'Subscription Payments',
            'subscriptions' => 'Subscriptions',

            'domains' => 'Domains',

            'tenant_invitations' => 'Tenant Invitations',

            'backups' => 'Backups',
        ];
    }

    /**
     * Categorize deletions for better summary.
     */
    private function categorizeDeletions(array $deletedCounts): array
    {
        $categorized = [
            'Sales & Orders' => [],
            'Inventory & Products' => [],
            'Accounting & Finance' => [],
            'HR & Payroll' => [],
            'Customers & Vendors' => [],
            'Users & Permissions' => [],
            'Configuration' => [],
            'Other' => [],
        ];

        $categoryMap = [
            'Sales' => 'Sales & Orders',
            'Sale Items' => 'Sales & Orders',
            'Sale Payments' => 'Sales & Orders',
            'Orders' => 'Sales & Orders',
            'Order Items' => 'Sales & Orders',
            'Quotations' => 'Sales & Orders',
            'Quotation Items' => 'Sales & Orders',
            'Invoices' => 'Sales & Orders',
            'Invoice Items' => 'Sales & Orders',
            'Receipts' => 'Sales & Orders',
            'Carts' => 'Sales & Orders',
            'Cart Items' => 'Sales & Orders',
            'Wishlists' => 'Sales & Orders',
            'Wishlist Items' => 'Sales & Orders',

            'Products' => 'Inventory & Products',
            'Product Images' => 'Inventory & Products',
            'Product Categories' => 'Inventory & Products',
            'Stock Movements' => 'Inventory & Products',
            'Stock Journal Entries' => 'Inventory & Products',
            'Stock Journal Entry Items' => 'Inventory & Products',
            'Physical Stock Vouchers' => 'Inventory & Products',
            'Physical Stock Entries' => 'Inventory & Products',
            'Units' => 'Inventory & Products',
            'Purchase Orders' => 'Inventory & Products',
            'Purchase Order Items' => 'Inventory & Products',

            'Vouchers' => 'Accounting & Finance',
            'Voucher Entries' => 'Accounting & Finance',
            'Voucher Types' => 'Accounting & Finance',
            'Ledger Accounts' => 'Accounting & Finance',
            'Account Groups' => 'Accounting & Finance',
            'Journal Entries' => 'Accounting & Finance',
            'Journal Entry Details' => 'Accounting & Finance',
            'Banks' => 'Accounting & Finance',
            'Bank Reconciliations' => 'Accounting & Finance',
            'Bank Reconciliation Items' => 'Accounting & Finance',
            'Payment Methods' => 'Accounting & Finance',
            'Tax Brackets' => 'Accounting & Finance',
            'Tax Rates' => 'Accounting & Finance',

            'Employees' => 'HR & Payroll',
            'Departments' => 'HR & Payroll',
            'Positions' => 'HR & Payroll',
            'Attendance Records' => 'HR & Payroll',
            'Overtime Records' => 'HR & Payroll',
            'Employee Leaves' => 'HR & Payroll',
            'Employee Leave Balances' => 'HR & Payroll',
            'Shift Schedules' => 'HR & Payroll',
            'Employee Shift Assignments' => 'HR & Payroll',
            'Leave Types' => 'HR & Payroll',
            'Payroll Periods' => 'HR & Payroll',
            'Payroll Runs' => 'HR & Payroll',
            'Payroll Run Details' => 'HR & Payroll',
            'Employee Salaries' => 'HR & Payroll',
            'Employee Salary Components' => 'HR & Payroll',
            'Salary Components' => 'HR & Payroll',
            'Employee Loans' => 'HR & Payroll',
            'Employee Documents' => 'HR & Payroll',
            'Employee Announcements' => 'HR & Payroll',
            'Announcement Recipients' => 'HR & Payroll',
            'Public Holidays' => 'HR & Payroll',
            'PFAs (Pension Fund Administrators)' => 'HR & Payroll',

            'Customers' => 'Customers & Vendors',
            'Customer Authentications' => 'Customers & Vendors',
            'Customer Activities' => 'Customers & Vendors',
            'Vendors' => 'Customers & Vendors',

            'Users' => 'Users & Permissions',
            'Tenant Users (Pivot)' => 'Users & Permissions',
            'Roles' => 'Users & Permissions',
            'Permissions' => 'Users & Permissions',
            'Teams' => 'Users & Permissions',
            'User Roles (Pivot)' => 'Users & Permissions',
            'Role Permissions (Pivot)' => 'Users & Permissions',
            'User Permissions (Pivot)' => 'Users & Permissions',
            'Team Users (Pivot)' => 'Users & Permissions',
        ];

        foreach ($deletedCounts as $description => $count) {
            $category = $categoryMap[$description] ?? 'Other';
            $categorized[$category][$description] = $count;
        }

        return array_filter($categorized, fn($items) => !empty($items));
    }
}
