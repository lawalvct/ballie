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
        $totalProducts = Product::where('tenant_id', $tenant->id)->count();

        $totalStockValue = Product::where('tenant_id', $tenant->id)
            ->sum(DB::raw('COALESCE(current_stock, 0) * COALESCE(purchase_rate, 0)'));

        $lowStockItems = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->count();

        $outOfStockItems = Product::where('tenant_id', $tenant->id)
            ->where('maintain_stock', true)
            ->where('current_stock', '<=', 0)
            ->count();

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

        // Get low stock products
        $lowStockProducts = Product::where('tenant_id', $tenant->id)
            ->with(['category', 'primaryUnit'])
            ->where('maintain_stock', true)
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->orderBy('current_stock', 'asc')
            ->limit(5)
            ->get()
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
            'recentActivities'
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
}
