<?php

namespace App\Http\Controllers\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\ProductCategoryRequest;
use App\Models\ProductCategory;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = ProductCategory::where('tenant_id', $tenant->id)
            ->with(['parent', 'children'])
            ->withCount(['products', 'children']);

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by parent category
        if ($request->filled('parent')) {
            $parentId = $request->get('parent');
            if ($parentId === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');

        if ($sortBy === 'name') {
            $query->orderBy('name', $sortDirection);
        } elseif ($sortBy === 'products_count') {
            $query->orderBy('products_count', $sortDirection);
        } elseif ($sortBy === 'created_at') {
            $query->orderBy('created_at', $sortDirection);
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
        }

        $categories = $query->paginate(15)->withQueryString();

        // Get parent categories for filter dropdown
        $parentCategories = ProductCategory::where('tenant_id', $tenant->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('tenant.inventory.categories.index', compact(
            'categories',
            'parentCategories',
            'tenant'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Tenant $tenant)
    {
        // Get parent categories for dropdown
        $parentCategories = ProductCategory::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Build hierarchical structure
        $hierarchicalCategories = $this->buildHierarchy($parentCategories);

        $selectedParentId = $request->get('parent_id');

        return view('tenant.inventory.categories.create', compact(
            'hierarchicalCategories',
            'selectedParentId',
            'tenant'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryRequest $request, Tenant $tenant)
    {
        $data = $request->validated();
        $data['tenant_id'] = $tenant->id;

        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique for this tenant
        $originalSlug = $data['slug'];
        $counter = 1;
        while (ProductCategory::where('tenant_id', $tenant->id)
                              ->where('slug', $data['slug'])
                              ->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Set sort order if not provided
        if (empty($data['sort_order'])) {
            $maxSortOrder = ProductCategory::where('tenant_id', $tenant->id)
                ->where('parent_id', $data['parent_id'] ?? null)
                ->max('sort_order') ?? 0;
            $data['sort_order'] = $maxSortOrder + 1;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = ProductCategory::create($data);

        return redirect()
            ->route('tenant.inventory.categories.show', ['tenant' => $tenant->slug, 'category' => $category->id])
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, ProductCategory $category)
    {
        $category->load(['parent', 'children.children', 'products' => function ($query) {
            $query->take(10);
        }]);

        $category->loadCount(['products', 'children']);

        // Get breadcrumb path
        $breadcrumbs = $this->getBreadcrumbs($category);

        return view('tenant.inventory.categories.show', compact(
            'category',
            'breadcrumbs',
            'tenant'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant, ProductCategory $category)
    {
        // Get parent categories for dropdown (excluding current category and its descendants)
        $excludeIds = $this->getDescendantIds($category);
        $excludeIds[] = $category->id;

        $parentCategories = ProductCategory::where('tenant_id', $tenant->id)
            ->where('is_active', true)
            ->whereNotIn('id', $excludeIds)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Build hierarchical structure
        $hierarchicalCategories = $this->buildHierarchy($parentCategories);

        return view('tenant.inventory.categories.edit', compact(
            'category',
            'hierarchicalCategories',
            'tenant'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCategoryRequest $request, Tenant $tenant, ProductCategory $category)
    {
        $data = $request->validated();

        // Generate slug if not provided or if name changed
        if (empty($data['slug']) || $data['name'] !== $category->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure slug is unique for this tenant (excluding current category)
        $originalSlug = $data['slug'];
        $counter = 1;
        while (ProductCategory::where('tenant_id', $tenant->id)
                              ->where('slug', $data['slug'])
                              ->where('id', '!=', $category->id)
                              ->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                \Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()
            ->route('tenant.inventory.categories.show', ['tenant' => $tenant->slug, 'category' => $category->id])
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant, ProductCategory $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category that has products assigned to it.');
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Cannot delete category that has subcategories.');
        }

        // Delete image if exists
        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('tenant.inventory.categories.index', ['tenant' => $tenant->slug])
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Tenant $tenant, ProductCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Category {$status} successfully.");
    }

    /**
     * Quick store a category via AJAX
     */
    public function quickStore(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'description', 'parent_id']);
        $data['tenant_id'] = $tenant->id;
        $data['is_active'] = $request->boolean('is_active', true);

        // Generate slug
        $data['slug'] = Str::slug($data['name']);

        // Ensure slug is unique for this tenant
        $originalSlug = $data['slug'];
        $counter = 1;
        while (ProductCategory::where('tenant_id', $tenant->id)
                              ->where('slug', $data['slug'])
                              ->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Set sort order
        $maxSortOrder = ProductCategory::where('tenant_id', $tenant->id)
            ->where('parent_id', $data['parent_id'] ?? null)
            ->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;

        $category = ProductCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ]
        ]);
    }

    /**
     * Reorder categories
     */
    public function reorder(Request $request, Tenant $tenant)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:product_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->categories as $categoryData) {
            ProductCategory::where('id', $categoryData['id'])
                ->where('tenant_id', $tenant->id)
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Build hierarchical category structure
     */
    private function buildHierarchy($categories, $parentId = null, $level = 0)
    {
        $result = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $category->level = $level;
                $result[] = $category;

                $children = $this->buildHierarchy($categories, $category->id, $level + 1);
                $result = array_merge($result, $children);
            }
        }

        return $result;
    }

    /**
     * Get all descendant IDs of a category
     */
    private function getDescendantIds(ProductCategory $category)
    {
        $ids = [];
        $children = $category->children;

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getDescendantIds($child));
        }

        return $ids;
    }

    /**
     * Get breadcrumb path for a category
     */
    private function getBreadcrumbs(ProductCategory $category)
    {
        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }
}
