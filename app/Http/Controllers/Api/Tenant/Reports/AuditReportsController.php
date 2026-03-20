<?php

namespace App\Http\Controllers\Api\Tenant\Reports;

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
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditReportsController extends Controller
{
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
     * Audit dashboard data with activities, stats, and filters.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $userFilter = $request->get('user_id');
        $actionFilter = $request->get('action');
        $modelFilter = $request->get('model');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $search = $request->get('search');

        $users = User::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $stats = $this->getAuditStatistics($tenant->id, $dateFrom, $dateTo);

        $activities = $this->getRecentActivities($tenant->id, [
            'user_id' => $userFilter,
            'action' => $actionFilter,
            'model' => $modelFilter,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'search' => $search,
        ]);

        $perPage = min((int) $request->get('per_page', 50), 100);
        $currentPage = (int) $request->get('page', 1);
        $items = $activities->forPage($currentPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items,
            $activities->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Build model options grouped by category
        $modelOptions = collect($this->getAuditableModels())
            ->map(fn($config, $key) => [
                'key' => $key,
                'label' => $config['label'],
                'category' => $config['category'],
            ])
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'message' => 'Audit dashboard retrieved successfully',
            'data' => [
                'filters' => [
                    'user_id' => $userFilter,
                    'action' => $actionFilter,
                    'model' => $modelFilter,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'search' => $search,
                ],
                'stats' => $stats,
                'users' => $users,
                'model_options' => $modelOptions,
                'activities' => $paginated,
            ],
        ]);
    }

    /**
     * Audit trail for a specific model record.
     */
    public function show(Request $request, Tenant $tenant, string $model, int $id)
    {
        $models = $this->getAuditableModels();

        if (!isset($models[$model])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid audit model. Valid models: ' . implode(', ', array_keys($models)),
                'data' => null,
            ], 422);
        }

        $config = $models[$model];
        $class = $config['class'];

        $record = $class::where('tenant_id', $tenant->id)->findOrFail($id);
        $nameField = $config['name_field'];
        $activities = $this->getModelAuditTrail($record, $config);

        return response()->json([
            'success' => true,
            'message' => 'Audit trail retrieved successfully',
            'data' => [
                'model' => $model,
                'model_label' => $config['label'],
                'category' => $config['category'],
                'record' => [
                    'id' => $record->id,
                    'name' => $nameField($record),
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ],
                'activities' => $activities->values(),
            ],
        ]);
    }

    /**
     * Get audit statistics summary.
     */
    public function statistics(Request $request, Tenant $tenant)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $stats = $this->getAuditStatistics($tenant->id, $dateFrom, $dateTo);

        return response()->json([
            'success' => true,
            'message' => 'Audit statistics retrieved successfully',
            'data' => $stats,
        ]);
    }

    /**
     * List available auditable model types.
     */
    public function models(Request $request, Tenant $tenant)
    {
        $modelOptions = collect($this->getAuditableModels())
            ->map(fn($config, $key) => [
                'key' => $key,
                'label' => $config['label'],
                'category' => $config['category'],
            ])
            ->groupBy('category');

        return response()->json([
            'success' => true,
            'message' => 'Auditable models retrieved successfully',
            'data' => [
                'models' => $modelOptions,
                'actions' => ['created', 'updated', 'posted', 'deleted'],
            ],
        ]);
    }

    private function getAuditStatistics(int $tenantId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $models = $this->getAuditableModels();
        $totalRecords = 0;
        $createdToday = 0;
        $updatedToday = 0;

        foreach ($models as $key => $config) {
            $class = $config['class'];
            $hasCreatedBy = $config['has_created_by'] ?? true;

            $base = $class::where('tenant_id', $tenantId);

            if ($dateFrom || $dateTo) {
                $base = $base->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo));
            }

            if ($hasCreatedBy) {
                $totalRecords += (clone $base)->whereNotNull('created_by')->count();
            } else {
                $totalRecords += (clone $base)->count();
            }

            $createdToday += (clone $class::where('tenant_id', $tenantId))->whereDate('created_at', today())->count();

            if ($config['has_updated_by'] ?? false) {
                $updatedToday += (clone $class::where('tenant_id', $tenantId))
                    ->whereDate('updated_at', today())
                    ->whereNotNull('updated_by')->count();
            }
        }

        $postedToday = Voucher::where('tenant_id', $tenantId)
            ->whereDate('posted_at', today())
            ->whereNotNull('posted_by')
            ->count();

        $postedToday += PhysicalStockVoucher::where('tenant_id', $tenantId)
            ->whereDate('approved_at', today())
            ->whereNotNull('approved_by')
            ->count();

        $auditableTables = ['customers', 'vendors', 'products', 'vouchers', 'sales',
            'projects', 'purchase_orders', 'quotations', 'ledger_accounts', 'physical_stock_vouchers'];

        $activeUsers = DB::table('users')
            ->where('tenant_id', $tenantId)
            ->where(function ($query) use ($tenantId, $auditableTables) {
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

    private function getRecentActivities(int $tenantId, array $filters)
    {
        $activities = collect();
        $models = $this->getAuditableModels();
        $modelFilter = $filters['model'] ?? null;

        foreach ($models as $key => $config) {
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

                // Format user for API response
                $formatUser = fn($user) => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ] : null;

                // Created activity
                if ($hasCreatedBy && $item->{$creatorRelation}) {
                    if ($this->matchesActionFilter($filters, 'created')) {
                        $activities->push([
                            'id' => $item->id,
                            'model' => $config['label'],
                            'model_key' => $key,
                            'category' => $config['category'],
                            'model_name' => $itemName,
                            'action' => 'created',
                            'user' => $formatUser($item->{$creatorRelation}),
                            'timestamp' => $item->created_at,
                            'details' => "Created {$config['label']}: {$itemName}",
                        ]);
                    }
                } elseif (!$hasCreatedBy) {
                    if ($this->matchesActionFilter($filters, 'created')) {
                        $activities->push([
                            'id' => $item->id,
                            'model' => $config['label'],
                            'model_key' => $key,
                            'category' => $config['category'],
                            'model_name' => $itemName,
                            'action' => 'created',
                            'user' => null,
                            'timestamp' => $item->created_at,
                            'details' => "Created {$config['label']}: {$itemName}",
                        ]);
                    }
                }

                // Updated activity
                if (($config['has_updated_by'] ?? false) && $item->{$updaterRelation}
                    && $item->updated_at && $item->updated_at->gt($item->created_at)) {
                    if ($this->matchesActionFilter($filters, 'updated')) {
                        $activities->push([
                            'id' => $item->id,
                            'model' => $config['label'],
                            'model_key' => $key,
                            'category' => $config['category'],
                            'model_name' => $itemName,
                            'action' => 'updated',
                            'user' => $formatUser($item->{$updaterRelation}),
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
                            'category' => $config['category'],
                            'model_name' => $itemName,
                            'action' => 'posted',
                            'user' => $formatUser($item->poster),
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
                            'category' => $config['category'],
                            'model_name' => $itemName,
                            'action' => 'approved',
                            'user' => $formatUser($item->approver),
                            'timestamp' => $item->approved_at,
                            'details' => "Approved {$config['label']}: {$itemName}",
                        ]);
                    }
                }

                // Deleted activity
                if (($config['has_deleted_by'] ?? false) && $item->deleted_at
                    && method_exists($item, 'deleter') && $item->deleter) {
                    if ($this->matchesActionFilter($filters, 'deleted')) {
                        $activities->push([
                            'id' => $item->id,
                            'model' => $config['label'],
                            'model_key' => $key,
                            'category' => $config['category'],
                            'model_name' => $itemName,
                            'action' => 'deleted',
                            'user' => $formatUser($item->deleter),
                            'timestamp' => $item->deleted_at,
                            'details' => "Deleted {$config['label']}: {$itemName}",
                        ]);
                    }
                }
            }
        }

        // Search filter
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $activities = $activities->filter(function ($activity) use ($search) {
                return str_contains(strtolower($activity['details'] ?? ''), $search)
                    || str_contains(strtolower($activity['model_name'] ?? ''), $search);
            });
        }

        return $activities->sortByDesc('timestamp')->take(100)->values();
    }

    private function matchesActionFilter(array $filters, string $action): bool
    {
        return empty($filters['action']) || $filters['action'] === $action;
    }

    private function getModelAuditTrail($record, array $config): \Illuminate\Support\Collection
    {
        $activities = collect();
        $label = $config['label'];
        $hasCreatedBy = $config['has_created_by'] ?? true;
        $creatorRelation = $config['creator_relation'] ?? 'creator';
        $updaterRelation = $config['updater_relation'] ?? 'updater';

        $formatUser = fn($user) => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ] : null;

        // Created
        if ($hasCreatedBy && $record->{$creatorRelation}) {
            $activities->push([
                'action' => 'created',
                'user' => $formatUser($record->{$creatorRelation}),
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
                'user' => $formatUser($record->{$updaterRelation}),
                'timestamp' => $record->updated_at,
                'details' => "{$label} information updated",
            ]);
        }

        // Posted (Vouchers)
        if (($config['has_posted_by'] ?? false) && $record->posted_at && $record->poster) {
            $activities->push([
                'action' => 'posted',
                'user' => $formatUser($record->poster),
                'timestamp' => $record->posted_at,
                'details' => "{$label} posted to ledger",
            ]);
        }

        // Approved (Physical Stock Vouchers)
        if (($config['has_approved_by'] ?? false) && $record->approved_at && $record->approver) {
            $activities->push([
                'action' => 'approved',
                'user' => $formatUser($record->approver),
                'timestamp' => $record->approved_at,
                'details' => "{$label} approved",
            ]);
        }

        // Deleted
        if (($config['has_deleted_by'] ?? false) && $record->deleted_at
            && method_exists($record, 'deleter') && $record->deleter) {
            $activities->push([
                'action' => 'deleted',
                'user' => $formatUser($record->deleter),
                'timestamp' => $record->deleted_at,
                'details' => "{$label} deleted",
            ]);
        }

        // Quotation-specific lifecycle events
        if ($record instanceof Quotation) {
            if ($record->sent_at) {
                $activities->push([
                    'action' => 'updated',
                    'user' => $formatUser($record->createdBy),
                    'timestamp' => $record->sent_at,
                    'details' => "Quotation sent to customer",
                ]);
            }
            if ($record->accepted_at) {
                $activities->push([
                    'action' => 'posted',
                    'user' => $formatUser($record->createdBy),
                    'timestamp' => $record->accepted_at,
                    'details' => "Quotation accepted",
                ]);
            }
            if ($record->rejected_at) {
                $activities->push([
                    'action' => 'deleted',
                    'user' => $formatUser($record->createdBy),
                    'timestamp' => $record->rejected_at,
                    'details' => "Quotation rejected" . ($record->rejection_reason ? ": {$record->rejection_reason}" : ''),
                ]);
            }
            if ($record->converted_at) {
                $activities->push([
                    'action' => 'posted',
                    'user' => $formatUser($record->createdBy),
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
}
