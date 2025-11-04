<?php

namespace App\Http\Controllers\Tenant\Audit;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\LedgerAccount;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    /**
     * Display the audit trail dashboard.
     */
    public function index(Request $request)
    {
        $tenant = tenant();

        // Get filter parameters
        $userFilter = $request->input('user_id');
        $actionFilter = $request->input('action');
        $modelFilter = $request->input('model');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Get all users for the tenant
        $users = User::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Get summary statistics
        $stats = $this->getAuditStatistics($tenant->id, $dateFrom, $dateTo);

        // Get recent activities
        $activities = $this->getRecentActivities($tenant->id, [
            'user_id' => $userFilter,
            'action' => $actionFilter,
            'model' => $modelFilter,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        return view('tenant.audit.index', compact(
            'tenant',
            'users',
            'stats',
            'activities',
            'userFilter',
            'actionFilter',
            'modelFilter',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Get audit statistics.
     */
    private function getAuditStatistics($tenantId, $dateFrom = null, $dateTo = null)
    {
        $stats = [
            'total_records' => 0,
            'created_today' => 0,
            'updated_today' => 0,
            'posted_today' => 0,
            'active_users' => 0,
        ];

        $dateFilter = function($query) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
        };

        // Count total records with audit columns
        $stats['total_records'] = collect([
            Customer::where('tenant_id', $tenantId)->whereNotNull('created_by')->count(),
            Vendor::where('tenant_id', $tenantId)->whereNotNull('created_by')->count(),
            Product::where('tenant_id', $tenantId)->whereNotNull('created_by')->count(),
            Voucher::where('tenant_id', $tenantId)->whereNotNull('created_by')->count(),
        ])->sum();

        // Count created today
        $stats['created_today'] = collect([
            Customer::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            Vendor::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            Product::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            Voucher::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
        ])->sum();

        // Count updated today
        $stats['updated_today'] = collect([
            Customer::where('tenant_id', $tenantId)->whereDate('updated_at', today())->whereNotNull('updated_by')->count(),
            Vendor::where('tenant_id', $tenantId)->whereDate('updated_at', today())->whereNotNull('updated_by')->count(),
            Product::where('tenant_id', $tenantId)->whereDate('updated_at', today())->whereNotNull('updated_by')->count(),
            Voucher::where('tenant_id', $tenantId)->whereDate('updated_at', today())->whereNotNull('updated_by')->count(),
        ])->sum();

        // Count posted today
        $stats['posted_today'] = Voucher::where('tenant_id', $tenantId)
            ->whereDate('posted_at', today())
            ->whereNotNull('posted_by')
            ->count();

        // Count active users (users who performed actions today)
        $stats['active_users'] = User::where('tenant_id', $tenantId)
            ->where(function($query) use ($tenantId) {
                $query->whereHas('createdCustomers', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->whereDate('created_at', today());
                })
                ->orWhereHas('createdVendors', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->whereDate('created_at', today());
                })
                ->orWhereHas('createdProducts', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->whereDate('created_at', today());
                })
                ->orWhereHas('createdVouchers', function($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)->whereDate('created_at', today());
                });
            })
            ->count();

        return $stats;
    }

    /**
     * Get recent activities with filters.
     */
    private function getRecentActivities($tenantId, $filters = [])
    {
        $activities = collect();

        // Get recent customers
        $customers = Customer::where('tenant_id', $tenantId)
            ->with('creator', 'updater')
            ->when($filters['user_id'], function($q, $userId) {
                $q->where(function($query) use ($userId) {
                    $query->where('created_by', $userId)
                          ->orWhere('updated_by', $userId);
                });
            })
            ->when($filters['date_from'], function($q, $date) {
                $q->whereDate('created_at', '>=', $date);
            })
            ->when($filters['date_to'], function($q, $date) {
                $q->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'model' => 'Customer',
                    'model_name' => $item->getFullNameAttribute(),
                    'action' => 'created',
                    'user' => $item->creator,
                    'timestamp' => $item->created_at,
                    'details' => "Created customer: {$item->getFullNameAttribute()}",
                ];
            });

        $activities = $activities->merge($customers);

        // Get recent vendors
        $vendors = Vendor::where('tenant_id', $tenantId)
            ->with('creator', 'updater')
            ->when($filters['user_id'], function($q, $userId) {
                $q->where(function($query) use ($userId) {
                    $query->where('created_by', $userId)
                          ->orWhere('updated_by', $userId);
                });
            })
            ->when($filters['date_from'], function($q, $date) {
                $q->whereDate('created_at', '>=', $date);
            })
            ->when($filters['date_to'], function($q, $date) {
                $q->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'model' => 'Vendor',
                    'model_name' => $item->getFullNameAttribute(),
                    'action' => 'created',
                    'user' => $item->creator,
                    'timestamp' => $item->created_at,
                    'details' => "Created vendor: {$item->getFullNameAttribute()}",
                ];
            });

        $activities = $activities->merge($vendors);

        // Get recent vouchers
        $vouchers = Voucher::where('tenant_id', $tenantId)
            ->with('creator', 'updater', 'poster', 'voucherType')
            ->when($filters['user_id'], function($q, $userId) {
                $q->where(function($query) use ($userId) {
                    $query->where('created_by', $userId)
                          ->orWhere('updated_by', $userId)
                          ->orWhere('posted_by', $userId);
                });
            })
            ->when($filters['date_from'], function($q, $date) {
                $q->whereDate('created_at', '>=', $date);
            })
            ->when($filters['date_to'], function($q, $date) {
                $q->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->limit(20)
            ->get()
            ->flatMap(function($item) {
                $activities = [];

                // Created activity
                $activities[] = [
                    'id' => $item->id,
                    'model' => 'Voucher',
                    'model_name' => $item->voucherType->name . ' #' . $item->voucher_number,
                    'action' => 'created',
                    'user' => $item->creator,
                    'timestamp' => $item->created_at,
                    'details' => "Created {$item->voucherType->name} #{$item->voucher_number}",
                ];

                // Posted activity
                if ($item->posted_at && $item->poster) {
                    $activities[] = [
                        'id' => $item->id,
                        'model' => 'Voucher',
                        'model_name' => $item->voucherType->name . ' #' . $item->voucher_number,
                        'action' => 'posted',
                        'user' => $item->poster,
                        'timestamp' => $item->posted_at,
                        'details' => "Posted {$item->voucherType->name} #{$item->voucher_number}",
                    ];
                }

                return $activities;
            });

        $activities = $activities->merge($vouchers);

        // Sort by timestamp and paginate
        return $activities->sortByDesc('timestamp')->take(50);
    }

    /**
     * Show detailed audit trail for a specific model.
     */
    public function show(Request $request, $model, $id)
    {
        $tenant = tenant();

        $record = null;
        $activities = collect();

        switch ($model) {
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
                abort(404);
        }

        return view('tenant.audit.show', compact('tenant', 'record', 'activities', 'model'));
    }

    /**
     * Get customer audit trail.
     */
    private function getCustomerAuditTrail($customer)
    {
        $activities = collect();

        // Created
        if ($customer->creator) {
            $activities->push([
                'action' => 'created',
                'user' => $customer->creator,
                'timestamp' => $customer->created_at,
                'details' => "Customer created",
            ]);
        }

        // Updated
        if ($customer->updater && $customer->updated_at > $customer->created_at) {
            $activities->push([
                'action' => 'updated',
                'user' => $customer->updater,
                'timestamp' => $customer->updated_at,
                'details' => "Customer information updated",
            ]);
        }

        // Deleted
        if ($customer->deleted_at && $customer->deleter) {
            $activities->push([
                'action' => 'deleted',
                'user' => $customer->deleter,
                'timestamp' => $customer->deleted_at,
                'details' => "Customer deleted",
            ]);
        }

        return $activities->sortByDesc('timestamp');
    }

    /**
     * Get vendor audit trail.
     */
    private function getVendorAuditTrail($vendor)
    {
        $activities = collect();

        if ($vendor->creator) {
            $activities->push([
                'action' => 'created',
                'user' => $vendor->creator,
                'timestamp' => $vendor->created_at,
                'details' => "Vendor created",
            ]);
        }

        if ($vendor->updater && $vendor->updated_at > $vendor->created_at) {
            $activities->push([
                'action' => 'updated',
                'user' => $vendor->updater,
                'timestamp' => $vendor->updated_at,
                'details' => "Vendor information updated",
            ]);
        }

        if ($vendor->deleted_at && $vendor->deleter) {
            $activities->push([
                'action' => 'deleted',
                'user' => $vendor->deleter,
                'timestamp' => $vendor->deleted_at,
                'details' => "Vendor deleted",
            ]);
        }

        return $activities->sortByDesc('timestamp');
    }

    /**
     * Get voucher audit trail.
     */
    private function getVoucherAuditTrail($voucher)
    {
        $activities = collect();

        if ($voucher->creator) {
            $activities->push([
                'action' => 'created',
                'user' => $voucher->creator,
                'timestamp' => $voucher->created_at,
                'details' => "Voucher created as draft",
            ]);
        }

        if ($voucher->updater && $voucher->updated_at > $voucher->created_at) {
            $activities->push([
                'action' => 'updated',
                'user' => $voucher->updater,
                'timestamp' => $voucher->updated_at,
                'details' => "Voucher updated",
            ]);
        }

        if ($voucher->posted_at && $voucher->poster) {
            $activities->push([
                'action' => 'posted',
                'user' => $voucher->poster,
                'timestamp' => $voucher->posted_at,
                'details' => "Voucher posted to ledger",
            ]);
        }

        return $activities->sortByDesc('timestamp');
    }

    /**
     * Get product audit trail.
     */
    private function getProductAuditTrail($product)
    {
        $activities = collect();

        if ($product->creator) {
            $activities->push([
                'action' => 'created',
                'user' => $product->creator,
                'timestamp' => $product->created_at,
                'details' => "Product created",
            ]);
        }

        if ($product->updater && $product->updated_at > $product->created_at) {
            $activities->push([
                'action' => 'updated',
                'user' => $product->updater,
                'timestamp' => $product->updated_at,
                'details' => "Product information updated",
            ]);
        }

        return $activities->sortByDesc('timestamp');
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
