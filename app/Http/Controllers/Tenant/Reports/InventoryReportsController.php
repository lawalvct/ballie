<?php

namespace App\Http\Controllers\Tenant\Reports;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryReportsController extends Controller
{
    /**
     * Stock Summary Report
     * Overview of all products with current stock levels and values
     */
    public function stockSummary(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $categoryId = $request->get('category_id');
        $stockStatus = $request->get('stock_status'); // all, in_stock, low_stock, out_of_stock
        $sortBy = $request->get('sort_by', 'name'); // name, stock_value, current_stock
        $sortOrder = $request->get('sort_order', 'asc');

        // Build query for products
        $query = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', '!=', 'service') // Exclude services
            ->with(['category', 'primaryUnit']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Get products and calculate stock as of date
        $products = $query->get()->map(function ($product) use ($asOfDate) {
            $stockData = $product->getStockValueAsOfDate($asOfDate, 'weighted_average');

            $product->calculated_stock = $stockData['quantity'];
            $product->calculated_value = $stockData['value'];
            $product->average_rate = $stockData['average_rate'];

            // Determine stock status
            if ($product->calculated_stock <= 0) {
                $product->status_flag = 'out_of_stock';
            } elseif ($product->reorder_level && $product->calculated_stock <= $product->reorder_level) {
                $product->status_flag = 'low_stock';
            } else {
                $product->status_flag = 'in_stock';
            }

            return $product;
        });

        // Filter by stock status if specified
        if ($stockStatus && $stockStatus !== 'all') {
            $products = $products->filter(function ($product) use ($stockStatus) {
                return $product->status_flag === $stockStatus;
            });
        }

        // Sort products
        $products = $products->sortBy(function ($product) use ($sortBy) {
            return match($sortBy) {
                'stock_value' => $product->calculated_value,
                'current_stock' => $product->calculated_stock,
                default => $product->name,
            };
        });

        if ($sortOrder === 'desc') {
            $products = $products->reverse();
        }

        // Calculate summary statistics
        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function($product) {
            return $product->calculated_stock * ($product->purchase_rate ?? 0);
        });
        $totalStockQuantity = $products->sum('calculated_stock');
        $outOfStockCount = $products->where('status_flag', 'out_of_stock')->count();
        $lowStockCount = $products->where('status_flag', 'low_stock')->count();

        // Get categories for filter
        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $paginatedProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('tenant.reports.inventory.stock-summary', compact(
            'tenant',
            'asOfDate',
            'categoryId',
            'stockStatus',
            'sortBy',
            'sortOrder',
            'paginatedProducts',
            'categories',
            'totalProducts',
            'totalStockValue',
            'totalStockQuantity',
            'outOfStockCount',
            'lowStockCount'
        ));
    }

    /**
     * Low Stock Alert Report
     * Products that are below reorder level or out of stock
     */
    public function lowStockAlert(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $categoryId = $request->get('category_id');
        $alertType = $request->get('alert_type', 'all'); // all, critical, low, out_of_stock

        // Build query for products with stock tracking
        $query = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', '!=', 'service') // Exclude services
            ->where('is_active', true)
            ->with(['category', 'primaryUnit']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Get products and calculate stock
        $products = $query->get()->map(function ($product) use ($asOfDate) {
            $stockData = $product->getStockValueAsOfDate($asOfDate, 'weighted_average');

            $product->calculated_stock = $stockData['quantity'];
            $product->calculated_value = $stockData['value'];
            $product->average_rate = $stockData['average_rate'];

            // Calculate stock shortage
            if ($product->reorder_level) {
                $product->shortage_quantity = max(0, $product->reorder_level - $product->calculated_stock);
                $product->shortage_percentage = $product->reorder_level > 0
                    ? (($product->reorder_level - $product->calculated_stock) / $product->reorder_level) * 100
                    : 0;
            } else {
                $product->shortage_quantity = 0;
                $product->shortage_percentage = 0;
            }

            // Determine alert level
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

        // Filter products that need attention
        $products = $products->filter(function ($product) use ($alertType) {
            if ($alertType === 'all') {
                return $product->alert_level !== 'normal';
            } elseif ($alertType === 'critical') {
                return $product->alert_level === 'critical';
            } elseif ($alertType === 'low') {
                return $product->alert_level === 'warning';
            } elseif ($alertType === 'out_of_stock') {
                return $product->calculated_stock <= 0;
            }
            return true;
        });

        // Sort by urgency (critical first, then by shortage percentage)
        $products = $products->sortByDesc(function ($product) {
            return ($product->alert_level === 'critical' ? 1000 : 0) + $product->shortage_percentage;
        })->values();

        // Summary statistics
        $totalAlerts = $products->count();
        $criticalAlerts = $products->where('alert_level', 'critical')->count();
        $warningAlerts = $products->where('alert_level', 'warning')->count();
        $outOfStockCount = $products->where('calculated_stock', '<=', 0)->count();
        $estimatedReorderValue = $products->sum(function ($product) {
            return $product->shortage_quantity * ($product->purchase_rate ?? 0);
        });

        // Get categories for filter
        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        // Paginate
        $page = $request->get('page', 1);
        $perPage = 20;
        $paginatedProducts = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('tenant.reports.inventory.low-stock-alert', compact(
            'tenant',
            'asOfDate',
            'categoryId',
            'alertType',
            'paginatedProducts',
            'categories',
            'totalAlerts',
            'criticalAlerts',
            'warningAlerts',
            'outOfStockCount',
            'estimatedReorderValue'
        ));
    }

    /**
     * Stock Valuation Report
     * Detailed valuation of inventory using different methods
     */
    public function stockValuation(Request $request, Tenant $tenant)
    {
        $asOfDate = $request->get('as_of_date', now()->toDateString());
        $categoryId = $request->get('category_id');
        $valuationMethod = $request->get('valuation_method', 'weighted_average'); // weighted_average, fifo
        $groupBy = $request->get('group_by', 'product'); // product, category

        // Build query
        $query = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', '!=', 'service') // Exclude services
            ->with(['category', 'primaryUnit']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        // Calculate valuation for each product
        $valuationData = $products->map(function ($product) use ($asOfDate, $valuationMethod) {
            $stockData = $product->getStockValueAsOfDate($asOfDate, $valuationMethod);

            return [
                'product' => $product,
                'quantity' => $stockData['quantity'],
                'value' => $stockData['value'],
                'average_rate' => $stockData['average_rate'],
                'category_id' => $product->category_id,
                'category_name' => $product->category->name ?? 'Uncategorized',
            ];
        })->filter(function ($item) {
            return $item['quantity'] > 0; // Only show products with stock
        });

        if ($groupBy === 'category') {
            // Group by category
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
                    'products' => $items,
                ];
            })->sortByDesc('total_value')->values();

            $displayData = $groupedData;
        } else {
            // Sort by value descending
            $displayData = $valuationData->sortByDesc('value')->values();
        }

        // Summary statistics
        $totalProducts = $valuationData->count();
        $totalStockValue = $valuationData->sum('value');
        $totalQuantity = $valuationData->sum('quantity');
        $averageValue = $totalProducts > 0 ? $totalStockValue / $totalProducts : 0;

        // Top 10 most valuable items
        $topValueProducts = $valuationData->sortByDesc('value')->take(10);

        // Get categories for filter
        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        // Paginate if needed
        $page = $request->get('page', 1);
        $perPage = 20;
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $displayData->forPage($page, $perPage),
            $displayData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('tenant.reports.inventory.stock-valuation', compact(
            'tenant',
            'asOfDate',
            'categoryId',
            'valuationMethod',
            'groupBy',
            'paginatedData',
            'categories',
            'totalProducts',
            'totalStockValue',
            'totalQuantity',
            'averageValue',
            'topValueProducts'
        ));
    }

    /**
     * Stock Movement Report
     * Detailed movement history with in/out transactions
     */
    public function stockMovement(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->get('product_id');
        $categoryId = $request->get('category_id');
        $movementType = $request->get('movement_type'); // in, out, all

        // Build query for stock movements
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

        // Get movements with running balance
        $movements = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Calculate summary statistics
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

        $totalIn = $summaryQuery->where('quantity', '>', 0)->sum('quantity');
        $totalOut = abs($summaryQuery->where('quantity', '<', 0)->sum('quantity'));
        $totalInValue = $summaryQuery->where('quantity', '>', 0)
            ->sum(DB::raw('quantity * rate'));
        $totalOutValue = abs($summaryQuery->where('quantity', '<', 0)
            ->sum(DB::raw('quantity * rate')));
        $netMovement = $totalIn - $totalOut;
        $transactionCount = $movements->total();

        // Get products for filter
        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->orderBy('name')
            ->get();

        // Get categories for filter
        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        return view('tenant.reports.inventory.stock-movement', compact(
            'tenant',
            'fromDate',
            'toDate',
            'productId',
            'categoryId',
            'movementType',
            'movements',
            'products',
            'categories',
            'totalIn',
            'totalOut',
            'totalInValue',
            'totalOutValue',
            'netMovement',
            'transactionCount'
        ));
    }

    /**
     * Bin Card (Inventory Ledger) per product
     * Shows opening balance, movements (in/out) and running closing balance
     */
    public function binCard(Request $request, Tenant $tenant)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to_date', now()->toDateString());
        $productId = $request->get('product_id');

        // Products for filter
        $products = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('type', '!=', 'service') // Exclude services
            ->orderBy('name')
            ->get();

        $product = null;
        if ($productId) {
            $product = $products->firstWhere('id', $productId);
        }

        // If no product selected, pick the first one if available
        if (!$product && $products->count() > 0) {
            $product = $products->first();
            $productId = $product->id;
        }

        $rows = collect();
        $openingQty = 0;
        $openingValue = 0;

        if ($product) {
            // Opening balance as of the day before fromDate
            $openingDate = Carbon::parse($fromDate)->subDay()->toDateString();
            try {
                $openingData = $product->getStockValueAsOfDate($openingDate, 'weighted_average');
                $openingQty = $openingData['quantity'] ?? 0;
                $openingValue = $openingData['value'] ?? 0;
            } catch (\Throwable $e) {
                // Fallback to zero if product method not available
                $openingQty = 0;
                $openingValue = 0;
            }

            // Movements between fromDate and toDate inclusive
            $movements = StockMovement::where('tenant_id', $tenant->id)
                ->where('product_id', $product->id)
                ->whereBetween('transaction_date', [$fromDate, $toDate])
                ->with(['product', 'creator'])
                ->orderBy('transaction_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            $runningQty = $openingQty;
            $runningValue = $openingValue;

            foreach ($movements as $m) {
                $inQty = $m->quantity > 0 ? $m->quantity : 0;
                $outQty = $m->quantity < 0 ? abs($m->quantity) : 0;
                $inValue = $inQty * ($m->rate ?? 0);
                $outValue = $outQty * ($m->rate ?? 0);

                $runningQty += $m->quantity;
                $runningValue += ($m->quantity * ($m->rate ?? 0));

                $rows->push((object)[
                    'date' => $m->transaction_date,
                    'particulars' => $m->reference ?? ($m->particulars ?? '-'),
                    'vch_type' => $m->vch_type ?? '-',
                    'vch_no' => $m->vch_no ?? '-',
                    'in_qty' => $inQty,
                    'in_value' => $inValue,
                    'out_qty' => $outQty,
                    'out_value' => $outValue,
                    'closing_qty' => $runningQty,
                    'closing_value' => $runningValue,
                    'created_by' => $m->creator->name ?? null,
                ]);
            }
        }

        // Totals
        $totalInQty = $rows->sum('in_qty');
        $totalOutQty = $rows->sum('out_qty');
        $totalInValue = $rows->sum('in_value');
        $totalOutValue = $rows->sum('out_value');

        // Build a pagination for the ledger rows
        $page = $request->get('page', 1);
        $perPage = 50;
        $paginatedRows = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows->forPage($page, $perPage),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('tenant.reports.inventory.bin-card', compact(
            'tenant',
            'fromDate',
            'toDate',
            'productId',
            'products',
            'rows',
            'paginatedRows',
            'openingQty',
            'openingValue',
            'totalInQty',
            'totalOutQty',
            'totalInValue',
            'totalOutValue'
        ));
    }
}
