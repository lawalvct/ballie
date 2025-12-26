<?php

namespace App\Http\Controllers\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Tenant $tenant)
    {
        // Get inventory statistics
        $totalProducts = Product::where('tenant_id', $tenant->id)
         ->where('type', 'item') // Exclude services
         ->count();

        // Calculate total stock value from all products with calculated stock
        $products = Product::where('tenant_id', $tenant->id)
         ->where('type', 'item') // Exclude services
            ->where('maintain_stock', true)
            ->where('is_active', true)
            ->get();

        $totalStockValue = 0;
        $lowStockItems = 0;
        $outOfStockItems = 0;

        foreach ($products as $product) {
            $currentStock = $product->current_stock; // Uses calculated stock from movements
            $stockValue = $currentStock * ($product->purchase_rate ?? 0); // Calculate using purchase rate

            $totalStockValue += $stockValue;

            // Count low stock items
            if ($product->reorder_level && $currentStock > 0 && $currentStock <= $product->reorder_level) {
                $lowStockItems++;
            }

            // Count out of stock items
            if ($currentStock <= 0) {
                $outOfStockItems++;
            }
        }

        $totalCategories = ProductCategory::where('tenant_id', $tenant->id)->count();

        $totalUnits = Unit::where('tenant_id', $tenant->id)->count();

        // Get recent products
        $recentProducts = Product::where('tenant_id', $tenant->id)
            ->with(['category', 'primaryUnit'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                // Add compatibility attributes
                $product->quantity = $product->current_stock;
                $product->selling_price = $product->sales_rate;
                $product->unit = $product->primaryUnit;
                return $product;
            });

        // Get low stock products (calculated from movements)
        $allProducts = Product::where('tenant_id', $tenant->id)
            ->with(['category', 'primaryUnit'])
            ->where('maintain_stock', true)
            ->get();

        $lowStockProducts = $allProducts->filter(function ($product) {
            $currentStock = $product->current_stock; // Calculated from movements
            return $product->reorder_level && $currentStock > 0 && $currentStock <= $product->reorder_level;
        })
        ->sortBy('current_stock')
        ->take(5)
        ->map(function ($product) {
                // Add compatibility attributes
                $product->quantity = $product->current_stock;
                $product->minimum_stock_level = $product->reorder_level;
                $product->unit = $product->primaryUnit;
                return $product;
            });

        // Get real recent activities from various inventory operations
        $recentActivities = collect();

        // Recent stock movements
        $stockMovements = \App\Models\StockMovement::where('tenant_id', $tenant->id)
            ->with(['product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($stockMovements as $movement) {
            $recentActivities->push((object) [
                'description' => $this->formatStockMovementActivity($movement),
                'type' => $this->getStockMovementType($movement),
                'icon' => $this->getStockMovementIcon($movement),
                'date' => $movement->created_at,
                'priority' => $this->getActivityPriority($movement->transaction_type)
            ]);
        }

        // Recent product additions
        $recentProductAdditions = Product::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentProductAdditions as $product) {
            $recentActivities->push((object) [
                'description' => "New product \"{$product->name}\" was added to inventory",
                'type' => 'product_added',
                'icon' => 'cube',
                'date' => $product->created_at,
                'priority' => 2
            ]);
        }

        // Recent category additions
        $recentCategories = ProductCategory::where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentCategories as $category) {
            $recentActivities->push((object) [
                'description' => "New category \"{$category->name}\" was created",
                'type' => 'category_added',
                'icon' => 'folder-plus',
                'date' => $category->created_at,
                'priority' => 3
            ]);
        }

        // Add low stock alerts for products that are currently low
        $currentLowStockProducts = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
             ->where('type', 'item') // Exclude services
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->limit(3)
            ->get();

        foreach ($currentLowStockProducts as $product) {
            $recentActivities->push((object) [
                'description' => "Low stock alert for \"{$product->name}\" - only {$product->current_stock} units remaining",
                'type' => 'low_stock_alert',
                'icon' => 'exclamation-triangle',
                'date' => $product->updated_at,
                'priority' => 1 // High priority for low stock
            ]);
        }

        // Sort by priority first, then by date, and take only the most recent 8
        $recentActivities = $recentActivities
            ->sortBy([
                ['priority', 'asc'],
                ['date', 'desc']
            ])
            ->take(8)
            ->values();

        // Get chart data
        $categoryDistribution = $this->getCategoryDistribution($tenant);
        $stockLevelDistribution = $this->getStockLevelDistribution($tenant);
        $monthlyStockMovements = $this->getMonthlyStockMovements($tenant);

        return view('tenant.inventory.index', compact(
            'tenant',
            'totalProducts',
            'totalStockValue',
            'lowStockItems',
            'outOfStockItems',
            'totalCategories',
            'totalUnits',
            'recentProducts',
            'lowStockProducts',
            'recentActivities',
            'categoryDistribution',
            'stockLevelDistribution',
            'monthlyStockMovements'
        ));
    }

    /**
     * Format stock movement activity description
     */
    private function formatStockMovementActivity($movement)
    {
        $productName = $movement->product->name ?? 'Unknown Product';
        $quantity = abs($movement->quantity);
        $direction = $movement->quantity > 0 ? 'increased' : 'decreased';
        $unit = $movement->product->primaryUnit->name ?? 'units';

        switch ($movement->transaction_type) {
            case 'purchase':
                return "Stock {$direction} for \"{$productName}\" by {$quantity} {$unit} via purchase";
            case 'sales':
            case 'sale':
                return "Stock {$direction} for \"{$productName}\" by {$quantity} {$unit} via sales";
            case 'stock_journal':
                return "Stock {$direction} for \"{$productName}\" by {$quantity} {$unit} via stock journal";
            case 'physical_adjustment':
                return "Stock adjusted for \"{$productName}\" by {$quantity} {$unit} via physical count";
            case 'opening_stock':
                return "Opening stock set for \"{$productName}\" to {$quantity} {$unit}";
            default:
                return "Stock {$direction} for \"{$productName}\" by {$quantity} {$unit}";
        }
    }

    /**
     * Get stock movement activity type
     */
    private function getStockMovementType($movement)
    {
        if ($movement->quantity > 0) {
            return 'stock_increased';
        } else {
            return 'stock_decreased';
        }
    }

    /**
     * Get stock movement activity icon
     */
    private function getStockMovementIcon($movement)
    {
        switch ($movement->transaction_type) {
            case 'purchase':
                return 'shopping-cart';
            case 'sales':
            case 'sale':
                return 'currency-dollar';
            case 'stock_journal':
                return 'clipboard-document-list';
            case 'physical_adjustment':
                return 'adjustments';
            case 'opening_stock':
                return 'archive-box';
            default:
                return $movement->quantity > 0 ? 'arrow-trending-up' : 'arrow-trending-down';
        }
    }

    /**
     * Get activity priority (lower number = higher priority)
     */
    private function getActivityPriority($transactionType)
    {
        switch ($transactionType) {
            case 'physical_adjustment':
                return 1;
            case 'sales':
            case 'sale':
                return 2;
            case 'purchase':
                return 3;
            case 'stock_journal':
                return 4;
            default:
                return 5;
        }
    }

    /**
     * Get category distribution for pie chart
     */
    private function getCategoryDistribution($tenant)
    {
        $distribution = ProductCategory::where('tenant_id', $tenant->id)
            ->withCount('products')
            ->having('products_count', '>', 0)  // Only categories with products
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->products_count,
                    'color' => $this->generateCategoryColor($category->id)
                ];
            });

        // Add uncategorized products
        $uncategorizedCount = Product::where('tenant_id', $tenant->id)
            ->whereNull('category_id')
            ->count();

        if ($uncategorizedCount > 0) {
            $distribution->push([
                'name' => 'Uncategorized',
                'count' => $uncategorizedCount,
                'color' => '#6B7280'
            ]);
        }

        // If no data, return a default empty state
        if ($distribution->isEmpty()) {
            $distribution->push([
                'name' => 'No Products',
                'count' => 1,
                'color' => '#E5E7EB'
            ]);
        }

        return $distribution;
    }

    /**
     * Get stock level distribution for doughnut chart
     */
    private function getStockLevelDistribution($tenant)
    {
        // Get all products and calculate stock from movements
        $productsWithStock = Product::where('tenant_id', $tenant->id)
        ->where('type', 'item') // Exclude services
            ->where('maintain_stock', true)
            ->get();

        $inStock = 0;
        $lowStock = 0;
        $outOfStock = 0;

        foreach ($productsWithStock as $product) {
            $currentStock = $product->current_stock; // Calculated from movements

            if ($currentStock <= 0) {
                $outOfStock++;
            } elseif ($product->reorder_level && $currentStock <= $product->reorder_level) {
                $lowStock++;
            } else {
                $inStock++;
            }
        }

        $noStockTracking = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', false)
            ->count();

        $total = $inStock + $lowStock + $outOfStock + $noStockTracking;

        // If no products, show empty state
        if ($total === 0) {
            return [
                [
                    'label' => 'No Products',
                    'count' => 1,
                    'color' => '#E5E7EB',
                    'percentage' => 100
                ]
            ];
        }

        return [
            [
                'label' => 'In Stock',
                'count' => $inStock,
                'color' => '#10B981',
                'percentage' => $this->calculatePercentage($inStock, $total)
            ],
            [
                'label' => 'Low Stock',
                'count' => $lowStock,
                'color' => '#F59E0B',
                'percentage' => $this->calculatePercentage($lowStock, $total)
            ],
            [
                'label' => 'Out of Stock',
                'count' => $outOfStock,
                'color' => '#EF4444',
                'percentage' => $this->calculatePercentage($outOfStock, $total)
            ],
            [
                'label' => 'No Stock Tracking',
                'count' => $noStockTracking,
                'color' => '#6B7280',
                'percentage' => $this->calculatePercentage($noStockTracking, $total)
            ]
        ];
    }

    /**
     * Get monthly stock movements for line chart
     */
    private function getMonthlyStockMovements($tenant)
    {
        $movements = \App\Models\StockMovement::where('tenant_id', $tenant->id)
         ->where('type', 'item') // Exclude services
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, transaction_type, SUM(ABS(quantity)) as total_quantity')
            ->groupBy('month', 'transaction_type')
            ->orderBy('month')
            ->get();

        $months = collect(range(1, 12))->map(function ($month) {
            return now()->month($month)->format('M');
        });

        $purchaseData = $months->map(function ($monthName, $index) use ($movements) {
            $month = $index + 1;
            return $movements->where('month', $month)
                ->where('transaction_type', 'purchase')
                ->sum('total_quantity') ?? 0;
        });

        $salesData = $months->map(function ($monthName, $index) use ($movements) {
            $month = $index + 1;
            return $movements->where('month', $month)
                ->whereIn('transaction_type', ['sales', 'sale'])
                ->sum('total_quantity') ?? 0;
        });

        return [
            'months' => $months->values(),
            'datasets' => [
                [
                    'label' => 'Purchases',
                    'data' => $purchaseData->values(),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Sales',
                    'data' => $salesData->values(),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4
                ]
            ]
        ];
    }

    /**
     * Generate consistent color for category
     */
    private function generateCategoryColor($categoryId)
    {
        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
        ];

        return $colors[$categoryId % count($colors)];
    }

    /**
     * Calculate percentage
     */
    private function calculatePercentage($value, $total)
    {
        return $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }
}
