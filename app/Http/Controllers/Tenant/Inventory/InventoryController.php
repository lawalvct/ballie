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

        // Mock recent activities (you can replace with actual activity log)
        $recentActivities = collect([
            (object) [
                'description' => 'New product "Sample Product" was added to inventory',
                'type' => 'product_added',
                'icon' => 'cube',
                'date' => now()->subHours(2)
            ],
            (object) [
                'description' => 'Stock updated for "Another Product" - quantity increased by 50',
                'type' => 'stock_updated',
                'icon' => 'arrow-trending-up',
                'date' => now()->subHours(4)
            ],
            (object) [
                'description' => 'Low stock alert for "Critical Item" - only 5 units remaining',
                'type' => 'low_stock_alert',
                'icon' => 'exclamation-triangle',
                'date' => now()->subHours(6)
            ],
            (object) [
                'description' => 'New category "Electronics" was created',
                'type' => 'category_added',
                'icon' => 'folder-plus',
                'date' => now()->subDay()
            ],
            (object) [
                'description' => 'Product "Old Item" was removed from inventory',
                'type' => 'product_removed',
                'icon' => 'trash',
                'date' => now()->subDays(2)
            ]
        ]);

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
}
