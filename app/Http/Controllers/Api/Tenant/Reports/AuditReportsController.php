<?php

namespace App\Http\Controllers\Api\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditReportsController extends Controller
{
    /**
     * Audit dashboard data.
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

        $perPage = (int) $request->get('per_page', 50);
        $currentPage = (int) $request->get('page', 1);
        $items = $activities->forPage($currentPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items,
            $activities->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

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
                'activities' => $paginated,
            ],
        ]);
    }

    /**
     * Audit trail for a specific model.
     */
    public function show(Request $request, Tenant $tenant, string $model, int $id)
    {
        $record = null;
        $activities = collect();

        switch (strtolower($model)) {
            case 'customer':
                $record = Customer::where('tenant_id', $tenant->id)->findOrFail($id);
                $activities = $this->getCustomerAuditTrail($record);
                break;
            case 'vendor':
                $record = Vendor::where('tenant_id', $tenant->id)->findOrFail($id);
                $activities = $this->getVendorAuditTrail($record);
                break;
            case 'voucher':
                $record = Voucher::where('tenant_id', $tenant->id)->findOrFail($id);
                $activities = $this->getVoucherAuditTrail($record);
                break;
            case 'product':
                $record = Product::where('tenant_id', $tenant->id)->findOrFail($id);
                $activities = $this->getProductAuditTrail($record);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid audit model',
                    'data' => null,
                ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Audit trail retrieved successfully',
            'data' => [
                'model' => $model,
                'record' => [
                    'id' => $record->id,
                    'name' => $record->name ?? $record->getFullNameAttribute() ?? $record->voucher_number ?? $record->id,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ],
                'activities' => $activities->values(),
            ],
        ]);
    }

    private function getAuditStatistics(int $tenantId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $stats = [
            'total_records' => 0,
            'created_today' => 0,
            'updated_today' => 0,
            'posted_today' => 0,
            'active_users' => 0,
        ];

        $stats['total_records'] = collect([
            Customer::where('tenant_id', $tenantId)->whereNotNull('created_by')->count(),
            Vendor::where('tenant_id', $tenantId)->whereNotNull('created_by')->count(),
            Product::where('tenant_id', $tenantId)->whereNotNull('created_by')->count(),
            Voucher::where('tenant_id', $tenantId)->whereNotNull('created_by')->count(),
        ])->sum();

        $stats['created_today'] = collect([
            Customer::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            Vendor::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            Product::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            Voucher::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
        ])->sum();

        $stats['updated_today'] = collect([
            Customer::where('tenant_id', $tenantId)->whereDate('updated_at', today())->whereNotNull('updated_by')->count(),
            Vendor::where('tenant_id', $tenantId)->whereDate('updated_at', today())->whereNotNull('updated_by')->count(),
            Product::where('tenant_id', $tenantId)->whereDate('updated_at', today())->whereNotNull('updated_by')->count(),
            Voucher::where('tenant_id', $tenantId)->whereDate('updated_at', today())->whereNotNull('updated_by')->count(),
        ])->sum();

        $stats['posted_today'] = Voucher::where('tenant_id', $tenantId)
            ->whereDate('posted_at', today())
            ->whereNotNull('posted_by')
            ->count();

        $stats['active_users'] = User::where('tenant_id', $tenantId)
            ->where(function ($query) use ($tenantId) {
                $query->whereHas('createdCustomers', function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->whereDate('created_at', today());
                })
                    ->orWhereHas('createdVendors', function ($q) use ($tenantId) {
                        $q->where('tenant_id', $tenantId)->whereDate('created_at', today());
                    })
                    ->orWhereHas('createdProducts', function ($q) use ($tenantId) {
                        $q->where('tenant_id', $tenantId)->whereDate('created_at', today());
                    })
                    ->orWhereHas('createdVouchers', function ($q) use ($tenantId) {
                        $q->where('tenant_id', $tenantId)->whereDate('created_at', today());
                    });
            })
            ->count();

        if ($dateFrom || $dateTo) {
            $stats['total_records'] = collect([
                Customer::where('tenant_id', $tenantId)->whereNotNull('created_by')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->count(),
                Vendor::where('tenant_id', $tenantId)->whereNotNull('created_by')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->count(),
                Product::where('tenant_id', $tenantId)->whereNotNull('created_by')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->count(),
                Voucher::where('tenant_id', $tenantId)->whereNotNull('created_by')
                    ->when($dateFrom, fn($q) => $q->whereDate('created_at', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->whereDate('created_at', '<=', $dateTo))
                    ->count(),
            ])->sum();
        }

        return $stats;
    }

    private function getRecentActivities(int $tenantId, array $filters)
    {
        $activities = collect();

        $customers = Customer::where('tenant_id', $tenantId)
            ->with('creator', 'updater')
            ->when($filters['user_id'], function ($q, $userId) {
                $q->where(function ($query) use ($userId) {
                    $query->where('created_by', $userId)
                        ->orWhere('updated_by', $userId);
                });
            })
            ->when($filters['date_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'model' => 'Customer',
                    'model_key' => 'customer',
                    'model_name' => $item->getFullNameAttribute(),
                    'action' => 'created',
                    'user' => $item->creator,
                    'timestamp' => $item->created_at,
                    'details' => "Created customer: {$item->getFullNameAttribute()}",
                ];
            });

        $activities = $activities->merge($customers);

        $vendors = Vendor::where('tenant_id', $tenantId)
            ->with('creator', 'updater')
            ->when($filters['user_id'], function ($q, $userId) {
                $q->where(function ($query) use ($userId) {
                    $query->where('created_by', $userId)
                        ->orWhere('updated_by', $userId);
                });
            })
            ->when($filters['date_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'model' => 'Vendor',
                    'model_key' => 'vendor',
                    'model_name' => $item->getFullNameAttribute(),
                    'action' => 'created',
                    'user' => $item->creator,
                    'timestamp' => $item->created_at,
                    'details' => "Created vendor: {$item->getFullNameAttribute()}",
                ];
            });

        $activities = $activities->merge($vendors);

        $vouchers = Voucher::where('tenant_id', $tenantId)
            ->with('creator', 'updater', 'poster', 'voucherType')
            ->when($filters['user_id'], function ($q, $userId) {
                $q->where(function ($query) use ($userId) {
                    $query->where('created_by', $userId)
                        ->orWhere('updated_by', $userId)
                        ->orWhere('posted_by', $userId);
                });
            })
            ->when($filters['date_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()
            ->limit(20)
            ->get()
            ->flatMap(function ($item) {
                $items = [];

                $items[] = [
                    'id' => $item->id,
                    'model' => 'Voucher',
                    'model_key' => 'voucher',
                    'model_name' => $item->voucherType?->name . ' #' . $item->voucher_number,
                    'action' => 'created',
                    'user' => $item->creator,
                    'timestamp' => $item->created_at,
                    'details' => "Created {$item->voucherType?->name} #{$item->voucher_number}",
                ];

                if ($item->posted_at && $item->poster) {
                    $items[] = [
                        'id' => $item->id,
                        'model' => 'Voucher',
                        'model_key' => 'voucher',
                        'model_name' => $item->voucherType?->name . ' #' . $item->voucher_number,
                        'action' => 'posted',
                        'user' => $item->poster,
                        'timestamp' => $item->posted_at,
                        'details' => "Posted {$item->voucherType?->name} #{$item->voucher_number}",
                    ];
                }

                return $items;
            });

        $activities = $activities->merge($vouchers);

        $products = Product::where('tenant_id', $tenantId)
            ->with('creator', 'updater')
            ->when($filters['user_id'], function ($q, $userId) {
                $q->where(function ($query) use ($userId) {
                    $query->where('created_by', $userId)
                        ->orWhere('updated_by', $userId);
                });
            })
            ->when($filters['date_from'], fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'model' => 'Product',
                    'model_key' => 'product',
                    'model_name' => $item->name,
                    'action' => 'created',
                    'user' => $item->creator,
                    'timestamp' => $item->created_at,
                    'details' => "Created product: {$item->name}",
                ];
            });

        $activities = $activities->merge($products);

        if (!empty($filters['action'])) {
            $activities = $activities->where('action', $filters['action']);
        }

        if (!empty($filters['model'])) {
            $activities = $activities->where('model_key', strtolower($filters['model']));
        }

        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $activities = $activities->filter(function ($activity) use ($search) {
                return str_contains(strtolower($activity['details'] ?? ''), $search)
                    || str_contains(strtolower($activity['model_name'] ?? ''), $search);
            });
        }

        return $activities->sortByDesc('timestamp')->take(50)->values();
    }

    private function getCustomerAuditTrail(Customer $customer)
    {
        $activities = collect();

        if ($customer->creator) {
            $activities->push([
                'action' => 'created',
                'user' => $customer->creator,
                'timestamp' => $customer->created_at,
                'details' => 'Customer created',
            ]);
        }

        if ($customer->updater && $customer->updated_at > $customer->created_at) {
            $activities->push([
                'action' => 'updated',
                'user' => $customer->updater,
                'timestamp' => $customer->updated_at,
                'details' => 'Customer information updated',
            ]);
        }

        if ($customer->deleted_at && $customer->deleter) {
            $activities->push([
                'action' => 'deleted',
                'user' => $customer->deleter,
                'timestamp' => $customer->deleted_at,
                'details' => 'Customer deleted',
            ]);
        }

        return $activities->sortByDesc('timestamp');
    }

    private function getVendorAuditTrail(Vendor $vendor)
    {
        $activities = collect();

        if ($vendor->creator) {
            $activities->push([
                'action' => 'created',
                'user' => $vendor->creator,
                'timestamp' => $vendor->created_at,
                'details' => 'Vendor created',
            ]);
        }

        if ($vendor->updater && $vendor->updated_at > $vendor->created_at) {
            $activities->push([
                'action' => 'updated',
                'user' => $vendor->updater,
                'timestamp' => $vendor->updated_at,
                'details' => 'Vendor information updated',
            ]);
        }

        if ($vendor->deleted_at && $vendor->deleter) {
            $activities->push([
                'action' => 'deleted',
                'user' => $vendor->deleter,
                'timestamp' => $vendor->deleted_at,
                'details' => 'Vendor deleted',
            ]);
        }

        return $activities->sortByDesc('timestamp');
    }

    private function getVoucherAuditTrail(Voucher $voucher)
    {
        $activities = collect();

        if ($voucher->creator) {
            $activities->push([
                'action' => 'created',
                'user' => $voucher->creator,
                'timestamp' => $voucher->created_at,
                'details' => 'Voucher created as draft',
            ]);
        }

        if ($voucher->updater && $voucher->updated_at > $voucher->created_at) {
            $activities->push([
                'action' => 'updated',
                'user' => $voucher->updater,
                'timestamp' => $voucher->updated_at,
                'details' => 'Voucher updated',
            ]);
        }

        if ($voucher->posted_at && $voucher->poster) {
            $activities->push([
                'action' => 'posted',
                'user' => $voucher->poster,
                'timestamp' => $voucher->posted_at,
                'details' => 'Voucher posted to ledger',
            ]);
        }

        return $activities->sortByDesc('timestamp');
    }

    private function getProductAuditTrail(Product $product)
    {
        $activities = collect();

        if ($product->creator) {
            $activities->push([
                'action' => 'created',
                'user' => $product->creator,
                'timestamp' => $product->created_at,
                'details' => 'Product created',
            ]);
        }

        if ($product->updater && $product->updated_at > $product->created_at) {
            $activities->push([
                'action' => 'updated',
                'user' => $product->updater,
                'timestamp' => $product->updated_at,
                'details' => 'Product information updated',
            ]);
        }

        return $activities->sortByDesc('timestamp');
    }
}
