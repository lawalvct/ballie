<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    /**
     * Display the store homepage
     */
    public function index(Request $request)
    {
        $tenant = $request->current_tenant;

        // Check if store is enabled
        $storeSettings = $tenant->ecommerceSettings;
        if (!$storeSettings || !$storeSettings->is_store_enabled) {
            abort(404, 'Store not available');
        }

        // Get featured products
        $featuredProducts = Product::where('tenant_id', $tenant->id)
            ->where('is_visible_online', true)
            ->where('is_featured', true)
            ->where('is_active', true)
            ->with('primaryImage', 'category')
            ->take(8)
            ->get();

        // Get new arrivals
        $newProducts = Product::where('tenant_id', $tenant->id)
            ->where('is_visible_online', true)
            ->where('is_active', true)
            ->with('primaryImage', 'category')
            ->latest()
            ->take(8)
            ->get();

        // Get categories with product count
        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->withCount(['products' => function ($query) {
                $query->where('is_visible_online', true)
                    ->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->get();

        return view('storefront.index', compact('tenant', 'storeSettings', 'featuredProducts', 'newProducts', 'categories'));
    }

    /**
     * Display product listing page
     */
    public function products(Request $request)
    {
        $tenant = $request->current_tenant;
        $storeSettings = $tenant->ecommerceSettings;

        if (!$storeSettings || !$storeSettings->is_store_enabled) {
            abort(404, 'Store not available');
        }

        $query = Product::where('tenant_id', $tenant->id)
            ->where('is_visible_online', true)
            ->where('is_active', true)
            ->with('primaryImage', 'category');

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('short_description', 'like', '%' . $search . '%')
                  ->orWhere('long_description', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%');
            });
        }

        // Price range filter
        if ($request->filled('price_min')) {
            $query->where('sales_rate', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('sales_rate', '<=', $request->price_max);
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('sales_rate', 'asc');
                break;
            case 'price_high':
                $query->orderBy('sales_rate', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default: // newest
                $query->latest();
        }

        $products = $query->paginate(12);

        // Get categories for filter sidebar
        $categories = ProductCategory::where('tenant_id', $tenant->id)
            ->withCount(['products' => function ($query) {
                $query->where('is_visible_online', true)
                    ->where('is_active', true);
            }])
            ->having('products_count', '>', 0)
            ->get();

        return view('storefront.products.index', compact('tenant', 'storeSettings', 'products', 'categories'));
    }

    /**
     * Display single product detail page
     */
    public function show($tenant, $slug)
    {
        // Resolve tenant if it's a string (slug)
        if (is_string($tenant)) {
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
        }

        $storeSettings = $tenant->ecommerceSettings;

        if (!$storeSettings || !$storeSettings->is_store_enabled) {
            abort(404, 'Store not available');
        }

        $product = Product::where('tenant_id', $tenant->id)
            ->where('slug', $slug)
            ->where('is_visible_online', true)
            ->where('is_active', true)
            ->with('images', 'category', 'unit')
            ->firstOrFail();

        // Increment view count
        $product->increment('view_count');

        // Get related products (same category)
        $relatedProducts = Product::where('tenant_id', $tenant->id)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_visible_online', true)
            ->where('is_active', true)
            ->with('primaryImage', 'category')
            ->take(4)
            ->get();

        return view('storefront.products.show', compact('tenant', 'storeSettings', 'product', 'relatedProducts'));
    }
}
