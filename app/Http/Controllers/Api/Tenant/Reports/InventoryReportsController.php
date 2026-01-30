<?php

namespace App\Http\Controllers\Api\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockMovement;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InventoryReportsController extends Controller
{
    public function stockSummary(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $categoryId = $request->get('category_id');
        $stockStatus = $request->get('stock_status');
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 20);

        $query = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', '!=', 'service')
            ->with(['category', 'primaryUnit']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->get()->map(function ($product) use ($asOfDate) {
            $stockData = $product->getStockValueAsOfDate($asOfDate, 'weighted_average');

            $product->calculated_stock = $stockData['quantity'];
            $product->calculated_value = $stockData['value'];
            $product->average_rate = $stockData['average_rate'];

            if ($product->calculated_stock <= 0) {
                $product->status_flag = 'out_of_stock';
            } elseif ($product->reorder_level && $product->calculated_stock <= $product->reorder_level) {
                $product->status_flag = 'low_stock';
            } else {
                $product->status_flag = 'in_stock';
            }

            return $product;
        });

        if ($stockStatus && $stockStatus !== 'all') {
            $products = $products->filter(function ($product) use ($stockStatus) {
                return $product->status_flag === $stockStatus;
            });
        }

        $products = $products->sortBy(function ($product) use ($sortBy) {
            return match ($sortBy) {
                'stock_value' => $product->calculated_value,
                'current_stock' => $product->calculated_stock,
                default => $product->name,
            };
        });

        if ($sortOrder === 'desc') {
            $products = $products->reverse();
        }

        $products = $products->values();

        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function ($product) {
            return $product->calculated_stock * ($product->purchase_rate ?? 0);
        });
        $totalStockQuantity = $products->sum('calculated_stock');
        $outOfStockCount = $products->where('status_flag', 'out_of_stock')->count();
        $lowStockCount = $products->where('status_flag', 'low_stock')->count();

        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        $page = (int) $request->get('page', 1);
        $paginatedProducts = new LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Stock summary retrieved successfully',
            'data' => [
                'filters' => [
                    'as_of_date' => $asOfDate,
                    'category_id' => $categoryId,
                    'stock_status' => $stockStatus,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                    'search' => $search,
                ],
                'summary' => [
                    'total_products' => $totalProducts,
                    'total_stock_value' => (float) $totalStockValue,
                    'total_stock_quantity' => (float) $totalStockQuantity,
                    'out_of_stock_count' => $outOfStockCount,
                    'low_stock_count' => $lowStockCount,
                ],
                'categories' => $categories,
                'records' => $paginatedProducts,
            ],
        ]);
    }

    public function lowStockAlert(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $categoryId = $request->get('category_id');
        $alertType = $request->get('alert_type', 'all');
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 20);

        $query = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', '!=', 'service')
            ->where('is_active', true)
            ->with(['category', 'primaryUnit']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->get()->map(function ($product) use ($asOfDate) {
            $stockData = $product->getStockValueAsOfDate($asOfDate, 'weighted_average');

            $product->calculated_stock = $stockData['quantity'];
            $product->calculated_value = $stockData['value'];
            $product->average_rate = $stockData['average_rate'];

            if ($product->reorder_level) {
                $product->shortage_quantity = max(0, $product->reorder_level - $product->calculated_stock);
                $product->shortage_percentage = $product->reorder_level > 0
                    ? (($product->reorder_level - $product->calculated_stock) / $product->reorder_level) * 100
                    : 0;
            } else {
                $product->shortage_quantity = 0;
                $product->shortage_percentage = 0;
            }

            if ($product->calculated_stock <= 0) {
                $product->alert_level = 'critical';
                $product->alert_status = 'out_of_stock';
            } elseif ($product->reorder_level && $product->calculated_stock <= ($product->reorder_level * 0.5)) {
                $product->alert_level = 'critical';
                $product->alert_status = 'critically_low';
            } elseif ($product->reorder_level && $product->calculated_stock <= $product->reorder_level) {
                $product->alert_level = 'warning';
                $product->alert_status = 'low_stock';
            } else {
                $product->alert_level = 'normal';
                $product->alert_status = 'sufficient';
            }

            return $product;
        });

        $products = $products->filter(function ($product) use ($alertType) {
            if ($alertType === 'all') {
                return $product->alert_level !== 'normal';
            }
            if ($alertType === 'critical') {
                return $product->alert_level === 'critical';
            }
            if ($alertType === 'low') {
                return $product->alert_level === 'warning';
            }
            if ($alertType === 'out_of_stock') {
                return $product->calculated_stock <= 0;
            }

            return true;
        });

        $products = $products->sortByDesc(function ($product) {
            return ($product->alert_level === 'critical' ? 1000 : 0) + $product->shortage_percentage;
        })->values();

        $totalAlerts = $products->count();
        $criticalAlerts = $products->where('alert_level', 'critical')->count();
        $warningAlerts = $products->where('alert_level', 'warning')->count();
        $outOfStockCount = $products->where('calculated_stock', '<=', 0)->count();
        $estimatedReorderValue = $products->sum(function ($product) {
            return $product->shortage_quantity * ($product->purchase_rate ?? 0);
        });

        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        $page = (int) $request->get('page', 1);
        $paginatedProducts = new LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Low stock alert retrieved successfully',
            'data' => [
                'filters' => [
                    'as_of_date' => $asOfDate,
                    'category_id' => $categoryId,
                    'alert_type' => $alertType,
                    'search' => $search,
                ],
                'summary' => [
                    'total_alerts' => $totalAlerts,
                    'critical_alerts' => $criticalAlerts,
                    'warning_alerts' => $warningAlerts,
                    'out_of_stock_count' => $outOfStockCount,
                    'estimated_reorder_value' => (float) $estimatedReorderValue,
                ],
                'categories' => $categories,
                'records' => $paginatedProducts,
            ],
        ]);
    }

    public function stockValuation(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $categoryId = $request->get('category_id');
        $valuationMethod = $request->get('valuation_method', 'weighted_average');
        $groupBy = $request->get('group_by', 'product');
        $search = $request->get('search');
        $perPage = (int) $request->get('per_page', 20);

        $query = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', '!=', 'service')
            ->with(['category', 'primaryUnit']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->get();

        $valuationData = $products->map(function ($product) use ($asOfDate, $valuationMethod) {
            $stockData = $product->getStockValueAsOfDate($asOfDate, $valuationMethod);

            return [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'unit' => $product->primaryUnit?->abbreviation,
                ],
                'quantity' => $stockData['quantity'],
                'value' => $stockData['value'],
                'average_rate' => $stockData['average_rate'],
                'category_id' => $product->category_id,
                'category_name' => $product->category->name ?? 'Uncategorized',
            ];
        })->filter(function ($item) {
            return $item['quantity'] > 0;
        });

        if ($groupBy === 'category') {
            $groupedData = $valuationData->groupBy('category_id')->map(function ($items, $categoryId) {
                $categoryName = $items->first()['category_name'];
                $totalQuantity = $items->sum('quantity');
                $totalValue = $items->sum('value');
                $productCount = $items->count();

                return [
                    'category_id' => $categoryId,
                    'category_name' => $categoryName,
                    'product_count' => $productCount,
                    'total_quantity' => $totalQuantity,
                    'total_value' => $totalValue,
                    'products' => $items->values(),
                ];
            })->sortByDesc('total_value')->values();

            $displayData = $groupedData;
        } else {
            $displayData = $valuationData->sortByDesc('value')->values();
        }

        $totalProducts = $valuationData->count();
        $totalStockValue = $valuationData->sum('value');
        $totalQuantity = $valuationData->sum('quantity');
        $averageValue = $totalProducts > 0 ? $totalStockValue / $totalProducts : 0;

        $topValueProducts = $valuationData->sortByDesc('value')->take(10)->values();

        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        $page = (int) $request->get('page', 1);
        $paginatedData = new LengthAwarePaginator(
            $displayData->forPage($page, $perPage),
            $displayData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Stock valuation retrieved successfully',
            'data' => [
                'filters' => [
                    'as_of_date' => $asOfDate,
                    'category_id' => $categoryId,
                    'valuation_method' => $valuationMethod,
                    'group_by' => $groupBy,
                    'search' => $search,
                ],
                'summary' => [
                    'total_products' => $totalProducts,
                    'total_stock_value' => (float) $totalStockValue,
                    'total_quantity' => (float) $totalQuantity,
                    'average_value' => (float) $averageValue,
                ],
                'top_value_products' => $topValueProducts,
                'categories' => $categories,
                'records' => $paginatedData,
            ],
        ]);
    }

    public function stockMovement(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->get('product_id');
        $categoryId = $request->get('category_id');
        $movementType = $request->get('movement_type');
        $perPage = (int) $request->get('per_page', 50);

        $query = StockMovement::where('tenant_id', $tenant->id)
            ->whereBetween('transaction_date', [$fromDate, $toDate])
            ->with(['product.category', 'product.primaryUnit', 'creator']);

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($categoryId) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($movementType === 'in') {
            $query->where('quantity', '>', 0);
        } elseif ($movementType === 'out') {
            $query->where('quantity', '<', 0);
        }

        $movements = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $summaryQuery = StockMovement::where('tenant_id', $tenant->id)
            ->whereBetween('transaction_date', [$fromDate, $toDate]);

        if ($productId) {
            $summaryQuery->where('product_id', $productId);
        }

        if ($categoryId) {
            $summaryQuery->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($movementType === 'in') {
            $summaryQuery->where('quantity', '>', 0);
        } elseif ($movementType === 'out') {
            $summaryQuery->where('quantity', '<', 0);
        }

        $totalIn = (clone $summaryQuery)->where('quantity', '>', 0)->sum('quantity');
        $totalOut = abs((clone $summaryQuery)->where('quantity', '<', 0)->sum('quantity'));
        $totalInValue = (clone $summaryQuery)->where('quantity', '>', 0)
            ->sum(DB::raw('quantity * rate'));
        $totalOutValue = abs((clone $summaryQuery)->where('quantity', '<', 0)
            ->sum(DB::raw('quantity * rate')));
        $netMovement = $totalIn - $totalOut;
        $transactionCount = $movements->total();

        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Stock movement retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'product_id' => $productId,
                    'category_id' => $categoryId,
                    'movement_type' => $movementType,
                ],
                'summary' => [
                    'total_in' => (float) $totalIn,
                    'total_out' => (float) $totalOut,
                    'net_movement' => (float) $netMovement,
                    'total_in_value' => (float) $totalInValue,
                    'total_out_value' => (float) $totalOutValue,
                    'transaction_count' => $transactionCount,
                ],
                'products' => $products,
                'categories' => $categories,
                'records' => $movements,
            ],
        ]);
    }

    public function binCard(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->get('product_id');
        $perPage = (int) $request->get('per_page', 50);

        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', '!=', 'service')
            ->orderBy('name')
            ->get();

        $product = null;
        if ($productId) {
            $product = $products->firstWhere('id', $productId);
        }

        if (!$product && $products->count() > 0) {
            $product = $products->first();
            $productId = $product->id;
        }

        $rows = collect();
        $openingQty = 0;
        $openingValue = 0;

        if ($product) {
            $openingDate = Carbon::parse($fromDate)->subDay()->toDateString();
            try {
                $openingData = $product->getStockValueAsOfDate($openingDate, 'weighted_average');
                $openingQty = $openingData['quantity'] ?? 0;
                $openingValue = $openingData['value'] ?? 0;
            } catch (\Throwable $e) {
                $openingQty = 0;
                $openingValue = 0;
            }

            $movements = StockMovement::where('tenant_id', $tenant->id)
                ->where('product_id', $product->id)
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->with(['product', 'creator'])
                ->orderBy('transaction_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            $runningQty = $openingQty;
            $runningValue = $openingValue;

            foreach ($movements as $movement) {
                $inQty = $movement->quantity > 0 ? $movement->quantity : 0;
                $outQty = $movement->quantity < 0 ? abs($movement->quantity) : 0;
                $inValue = $inQty * ($movement->rate ?? 0);
                $outValue = $outQty * ($movement->rate ?? 0);

                $runningQty += $movement->quantity;
                $runningValue += ($movement->quantity * ($movement->rate ?? 0));

                $rows->push([
                    'date' => $movement->transaction_date,
                    'particulars' => $movement->reference ?? ($movement->particulars ?? '-'),
                    'vch_type' => $movement->vch_type ?? '-',
                    'vch_no' => $movement->vch_no ?? '-',
                    'in_qty' => (float) $inQty,
                    'in_value' => (float) $inValue,
                    'out_qty' => (float) $outQty,
                    'out_value' => (float) $outValue,
                    'closing_qty' => (float) $runningQty,
                    'closing_value' => (float) $runningValue,
                    'created_by' => $movement->creator?->name,
                ]);
            }
        }

        $totalInQty = $rows->sum('in_qty');
        $totalOutQty = $rows->sum('out_qty');
        $totalInValue = $rows->sum('in_value');
        $totalOutValue = $rows->sum('out_value');

        $page = (int) $request->get('page', 1);
        $paginatedRows = new LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'success' => true,
            'message' => 'Bin card retrieved successfully',
            'data' => [
                'filters' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'product_id' => $productId,
                ],
                'summary' => [
                    'opening_qty' => (float) $openingQty,
                    'opening_value' => (float) $openingValue,
                    'total_in_qty' => (float) $totalInQty,
                    'total_out_qty' => (float) $totalOutQty,
                    'total_in_value' => (float) $totalInValue,
                    'total_out_value' => (float) $totalOutValue,
                ],
                'products' => $products,
                'records' => $paginatedRows,
            ],
        ]);
    }
}
