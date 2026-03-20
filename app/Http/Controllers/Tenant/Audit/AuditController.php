<?php

namespace App\Http\Controllers\Tenant\Audit;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LedgerAccount;
use App\Models\Order;
use App\Models\PhysicalStockVoucher;
use App\Models\Product;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditController extends Controller
{
    /**
     * Models included in the audit trail with their configuration.
     */
    private function getAuditableModels(): array
    {
        return [
            'customer' => [
                'class' => Customer::class,
                'label' => 'Customer',
                'category' => 'CRM',
                'relations' => ['creator', 'updater'],
                'has_updated_by' => true,
                'has_deleted_by' => true,
                'name_field' => fn($item) => $item->getFullNameAttribute(),
            ],
            'vendor' => [
                'class' => Vendor::class,
                'label' => 'Vendor',
                'category' => 'CRM',
                'relations' => ['creator', 'updater'],
                'has_updated_by' => true,
                'has_deleted_by' => true,
                'name_field' => fn($item) => $item->getFullNameAttribute(),
            ],
            'product' => [
                'class' => Product::class,
                'label' => 'Product',
                'category' => 'Inventory',
                'relations' => ['creator', 'updater'],
                'has_updated_by' => true,
                'has_deleted_by' => false,
                'name_field' => fn($item) => $item->name,
            ],
            'voucher' => [
                'class' => Voucher::class,
                'label' => 'Voucher',
                'category' => 'Accounting',
                'relations' => ['creator', 'updater', 'poster', 'voucherType'],
                'has_updated_by' => true,
                'has_deleted_by' => false,
                'has_posted_by' => true,
                'name_field' => fn($item) => ($item->voucherType->name ?? 'Voucher') . ' #' . $item->voucher_number,
            ],
            'sale' => [
                'class' => Sale::class,
                'label' => 'Sale',
                'category' => 'POS',
                'relations' => ['creator', 'updater'],
                'has_updated_by' => true,
                'has_deleted_by' => false,
                'name_field' => fn($item) => 'Sale #' . $item->sale_number,
            ],
            'project' => [
                'class' => Project::class,
                'label' => 'Project',
                'category' => 'Projects',
                'relations' => ['creator', 'updater'],
                'has_updated_by' => true,
                'has_deleted_by' => true,
                'name_field' => fn($item) => $item->name . ' (' . $item->project_number . ')',
            ],
            'purchase_order' => [
                'class' => PurchaseOrder::class,
                'label' => 'Purchase Order',
                'category' => 'Procurement',
                'relations' => ['creator', 'updater'],
                'has_updated_by' => true,
                'has_deleted_by' => false,
                'name_field' => fn($item) => 'LPO #' . $item->lpo_number,
            ],
            'quotation' => [
                'class' => Quotation::class,
                'label' => 'Quotation',
                'category' => 'Accounting',
                'relations' => ['createdBy', 'updatedBy'],
                'creator_relation' => 'createdBy',
                'updater_relation' => 'updatedBy',
                'has_updated_by' => true,
                'has_deleted_by' => false,
                'name_field' => fn($item) => 'Quotation #' . $item->quotation_number,
            ],
            'ledger_account' => [
                'class' => LedgerAccount::class,
                'label' => 'Ledger Account',
                'category' => 'Accounting',
                'relations' => ['creator', 'updater'],
                'has_updated_by' => true,
                'has_deleted_by' => false,
                'name_field' => fn($item) => $item->name . ' (' . $item->code . ')',
            ],
            'order' => [
                'class' => Order::class,
                'label' => 'Order',
                'category' => 'E-commerce',
                'relations' => ['customer'],
                'has_created_by' => false,
                'has_updated_by' => false,
                'has_deleted_by' => false,
                'name_field' => fn($item) => 'Order #' . $item->order_number,
            ],
            'physical_stock_voucher' => [
                'class' => PhysicalStockVoucher::class,
                'label' => 'Physical Stock Voucher',
                'category' => 'Inventory',
                'relations' => ['creator', 'updater', 'approver'],
                'has_updated_by' => true,
                'has_deleted_by' => false,
                'has_approved_by' => true,
                'name_field' => fn($item) => 'PSV #' . $item->voucher_number,
            ],
        ];
    }

    /**
     * Display the audit trail dashboard.
     */
    public function index(Request $request)
    {
        $tenant = tenant();

        $userFilter = $request->input('user_id');
        $actionFilter = $request->input('action');
        $modelFilter = $request->input('model');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $users = User::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $stats = $this->getAuditStatistics($tenant->id);

        $activities = $this->getRecentActivities($tenant->id, [
            'user_id' => $userFilter,
            'action' => $actionFilter,
            'model' => $modelFilter,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        // Build model options for filter dropdown, grouped by category
        $modelOptions = collect($this->getAuditableModels())
            ->map(fn($config, $key) => [
                'key' => $key,
                'label' => $config['label'],
                'category' => $config['category'],
            ])
            ->groupBy('category');

        return view('tenant.audit.index', compact(
            'tenant',
            'users',
            'stats',
            'activities',
            'userFilter',
            'actionFilter',
            'modelFilter',
            'dateFrom',
            'dateTo',
            'modelOptions'
        ));
    }

    /**
     * Get audit statistics.
     */
    private function getAuditStatistics($tenantId)
    {
        $models = $this->getAuditableModels();
        $totalRecords = 0;
        $createdToday = 0;
        $updatedToday = 0;

        foreach ($models as $key => $config) {
            $class = $config['class'];
            $hasCreatedBy = $config['has_created_by'] ?? true;

            $base = $class::where('tenant_id', $tenantId);

            if ($hasCreatedBy) {
                $totalRecords += (clone $base)->whereNotNull('created_by')->count();
            } else {
                $totalRecords += (clone $base)->count();
            }

            $createdToday += (clone $base)->whereDate('created_at', today())->count();

            if ($config['has_updated_by'] ?? false) {
                $updatedToday += (clone $base)->whereDate('updated_at', today())
                    ->whereNotNull('updated_by')->count();
            }
        }

        $postedToday = Voucher::where('tenant_id', $tenantId)
            ->whereDate('posted_at', today())
            ->whereNotNull('posted_by')
            ->count();

        // Approved physical stock vouchers today
        $postedToday += PhysicalStockVoucher::where('tenant_id', $tenantId)
            ->whereDate('approved_at', today())
            ->whereNotNull('approved_by')
            ->count();

        $activeUsers = DB::table('users')
            ->where('tenant_id', $tenantId)
            ->where(function ($query) use ($tenantId) {
                $auditableTables = ['customers', 'vendors', 'products', 'vouchers', 'sales',
                    'projects', 'purchase_orders', 'quotations', 'ledger_accounts', 'physical_stock_vouchers'];

                foreach ($auditableTables as $i => $table) {
                    if (!Schema::hasColumn($table, 'created_by')) continue;

                    $method = $i === 0 ? 'whereExists' : 'orWhereExists';
                    $query->$method(function ($sub) use ($table, $tenantId) {
                        $sub->select(DB::raw(1))
                            ->from($table)
                            ->where($table . '.tenant_id', $tenantId)
                            ->whereColumn($table . '.created_by', 'users.id')
                            ->whereDate($table . '.created_at', today());
                    });
                }
            })
            ->count();

        return [
            'total_records' => $totalRecords,
            'created_today' => $createdToday,
            'updated_today' => $updatedToday,
            'posted_today' => $postedToday,
            'active_users' => $activeUsers,
        ];
    }

    /**
     * Get recent activities with filters.
     */
    private function getRecentActivities($tenantId, $filters = [])
    {
        $activities = collect();
        $models = $this->getAuditableModels();
        $modelFilter = $filters['model'] ?? null;

        foreach ($models as $key => $config) {
            // Skip if a specific model filter is set and doesn't match
            if ($modelFilter && $modelFilter !== $key) {
                continue;
            }

            $class = $config['class'];
            $hasCreatedBy = $config['has_created_by'] ?? true;
            $creatorRelation = $config['creator_relation'] ?? 'creator';
            $updaterRelation = $config['updater_relation'] ?? 'updater';

            $query = $class::where('tenant_id', $tenantId)
                ->with($config['relations']);

            // User filter
            if (!empty($filters['user_id']) && $hasCreatedBy) {
                $userId = $filters['user_id'];
                $query->where(function ($q) use ($userId, $config) {
                    $q->where('created_by', $userId);
                    if ($config['has_updated_by'] ?? false) {
                        $q->orWhere('updated_by', $userId);
                    }
                    if ($config['has_posted_by'] ?? false) {
                        $q->orWhere('posted_by', $userId);
                    }
                    if ($config['has_approved_by'] ?? false) {
                        $q->orWhere('approved_by', $userId);
                    }
                });
            }

            // Date filters
            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            $items = $query->latest()->limit(15)->get();

            foreach ($items as $item) {
                $nameField = $config['name_field'];
                $itemName = $nameField($item);

                // Created activity
                if ($hasCreatedBy && $item->{$creatorRelation}) {
                    if (!$this->matchesActionFilter($filters, 'created')) continue;

                    $activities->push([
                        'id' => $item->id,
                        'model' => $config['label'],
                        'model_key' => $key,
                        'model_name' => $itemName,
                        'action' => 'created',
                        'user' => $item->{$creatorRelation},
                        'timestamp' => $item->created_at,
                        'details' => "Created {$config['label']}: {$itemName}",
                    ]);
                } elseif (!$hasCreatedBy) {
                    // For models without created_by (e.g. Order), use timestamp only
                    if (!$this->matchesActionFilter($filters, 'created')) continue;

                    $activities->push([
                        'id' => $item->id,
                        'model' => $config['label'],
                        'model_key' => $key,
                        'model_name' => $itemName,
                        'action' => 'created',
                        'user' => null,
                        'timestamp' => $item->created_at,
                        'details' => "Created {$config['label']}: {$itemName}",
                    ]);
                }

                // Updated activity
                if (($config['has_updated_by'] ?? false) && $item->{$updaterRelation}
                    && $item->updated_at && $item->updated_at->gt($item->created_at)) {
                    if ($this->matchesActionFilter($filters, 'updated')) {
                        $activities->push([
                            'id' => $item->id,
                            'model' => $config['label'],
                            'model_key' => $key,
                            'model_name' => $itemName,
                            'action' => 'updated',
                            'user' => $item->{$updaterRelation},
                            'timestamp' => $item->updated_at,
                            'details' => "Updated {$config['label']}: {$itemName}",
                        ]);
                    }
                }

                // Posted activity (Vouchers)
                if (($config['has_posted_by'] ?? false) && $item->posted_at && $item->poster) {
                    if ($this->matchesActionFilter($filters, 'posted')) {
                        $activities->push([
                            'id' => $item->id,
                            'model' => $config['label'],
                            'model_key' => $key,
                            'model_name' => $itemName,
                            'action' => 'posted',
                            'user' => $item->poster,
                            'timestamp' => $item->posted_at,
                            'details' => "Posted {$config['label']}: {$itemName}",
                        ]);
                    }
                }

                // Approved activity (Physical Stock Vouchers)
                if (($config['has_approved_by'] ?? false) && $item->approved_at && $item->approver) {
                    if ($this->matchesActionFilter($filters, 'posted')) {
                        $activities->push([
                            'id' => $item->id,
                            'model' => $config['label'],
                            'model_key' => $key,
                            'model_name' => $itemName,
                            'action' => 'posted',
                            'user' => $item->approver,
                            'timestamp' => $item->approved_at,
                            'details' => "Approved {$config['label']}: {$itemName}",
                        ]);
                    }
                }

                // Deleted activity
                if (($config['has_deleted_by'] ?? false) && $item->deleted_at && method_exists($item, 'deleter') && $item->deleter) {
                    if ($this->matchesActionFilter($filters, 'deleted')) {
                        $activities->push([
                            'id' => $item->id,
                            'model' => $config['label'],
                            'model_key' => $key,
                            'model_name' => $itemName,
                            'action' => 'deleted',
                            'user' => $item->deleter,
                            'timestamp' => $item->deleted_at,
                            'details' => "Deleted {$config['label']}: {$itemName}",
                        ]);
                    }
                }
            }
        }

        return $activities->sortByDesc('timestamp')->take(50)->values();
    }

    /**
     * Check if the activity action matches the filter.
     */
    private function matchesActionFilter(array $filters, string $action): bool
    {
        return empty($filters['action']) || $filters['action'] === $action;
    }

    /**
     * Show detailed audit trail for a specific model.
     */
    public function show(Request $request, $model, $id)
    {
        $tenant = tenant();
        $models = $this->getAuditableModels();

        if (!isset($models[$model])) {
            abort(404);
        }

        $config = $models[$model];
        $class = $config['class'];

        $record = $class::where('tenant_id', $tenant->id)->findOrFail($id);
        $auditTrail = $this->getModelAuditTrail($record, $config);
        $modelType = $model;
        $recordId = $id;

        return view('tenant.audit.show', compact('tenant', 'record', 'auditTrail', 'modelType', 'recordId'));
    }

    /**
     * Build audit trail for any auditable model.
     */
    private function getModelAuditTrail($record, array $config): \Illuminate\Support\Collection
    {
        $activities = collect();
        $label = $config['label'];
        $hasCreatedBy = $config['has_created_by'] ?? true;
        $creatorRelation = $config['creator_relation'] ?? 'creator';
        $updaterRelation = $config['updater_relation'] ?? 'updater';

        // Created
        if ($hasCreatedBy && $record->{$creatorRelation}) {
            $activities->push([
                'action' => 'created',
                'user' => $record->{$creatorRelation},
                'timestamp' => $record->created_at,
                'details' => "{$label} created",
            ]);
        } elseif (!$hasCreatedBy) {
            $activities->push([
                'action' => 'created',
                'user' => null,
                'timestamp' => $record->created_at,
                'details' => "{$label} created",
            ]);
        }

        // Updated
        if (($config['has_updated_by'] ?? false) && $record->{$updaterRelation}
            && $record->updated_at && $record->updated_at->gt($record->created_at)) {
            $activities->push([
                'action' => 'updated',
                'user' => $record->{$updaterRelation},
                'timestamp' => $record->updated_at,
                'details' => "{$label} information updated",
            ]);
        }

        // Posted (Vouchers)
        if (($config['has_posted_by'] ?? false) && $record->posted_at && $record->poster) {
            $activities->push([
                'action' => 'posted',
                'user' => $record->poster,
                'timestamp' => $record->posted_at,
                'details' => "{$label} posted to ledger",
            ]);
        }

        // Approved (Physical Stock Vouchers)
        if (($config['has_approved_by'] ?? false) && $record->approved_at && $record->approver) {
            $activities->push([
                'action' => 'posted',
                'user' => $record->approver,
                'timestamp' => $record->approved_at,
                'details' => "{$label} approved",
            ]);
        }

        // Deleted
        if (($config['has_deleted_by'] ?? false) && $record->deleted_at
            && method_exists($record, 'deleter') && $record->deleter) {
            $activities->push([
                'action' => 'deleted',
                'user' => $record->deleter,
                'timestamp' => $record->deleted_at,
                'details' => "{$label} deleted",
            ]);
        }

        // Quotation-specific lifecycle events
        if ($record instanceof Quotation) {
            if ($record->sent_at) {
                $activities->push([
                    'action' => 'updated',
                    'user' => $record->createdBy,
                    'timestamp' => $record->sent_at,
                    'details' => "Quotation sent to customer",
                ]);
            }
            if ($record->accepted_at) {
                $activities->push([
                    'action' => 'posted',
                    'user' => $record->createdBy,
                    'timestamp' => $record->accepted_at,
                    'details' => "Quotation accepted",
                ]);
            }
            if ($record->rejected_at) {
                $activities->push([
                    'action' => 'deleted',
                    'user' => $record->createdBy,
                    'timestamp' => $record->rejected_at,
                    'details' => "Quotation rejected" . ($record->rejection_reason ? ": {$record->rejection_reason}" : ''),
                ]);
            }
            if ($record->converted_at) {
                $activities->push([
                    'action' => 'posted',
                    'user' => $record->createdBy,
                    'timestamp' => $record->converted_at,
                    'details' => "Quotation converted to invoice",
                ]);
            }
        }

        // Order-specific lifecycle events
        if ($record instanceof Order) {
            if ($record->fulfilled_at) {
                $activities->push([
                    'action' => 'posted',
                    'user' => null,
                    'timestamp' => $record->fulfilled_at,
                    'details' => "Order fulfilled",
                ]);
            }
            if ($record->cancelled_at) {
                $activities->push([
                    'action' => 'deleted',
                    'user' => null,
                    'timestamp' => $record->cancelled_at,
                    'details' => "Order cancelled" . ($record->cancellation_reason ? ": {$record->cancellation_reason}" : ''),
                ]);
            }
        }

        return $activities->sortByDesc('timestamp')->values();
    }

    /**
     * Export audit trail report.
     */
    public function export(Request $request)
    {
        // TODO: Implement CSV/PDF export
        return back()->with('info', 'Export functionality coming soon!');
    }
}
