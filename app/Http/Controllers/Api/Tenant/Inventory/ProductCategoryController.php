<?php

namespace App\Http\Controllers\Api\Tenant\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    /**
     * List categories with filters and pagination.
     */
    public function index(Request $request, Tenant $tenant)
    {
        $query = ProductCategory::forTenant($tenant->id)
            ->with(['parent'])
            ->withCount(['products', 'children']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->get('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('parent')) {
            $parentId = $request->get('parent');
            if ($parentId === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }
        }

        $sortBy = $request->get('sort', 'sort_order');
        $sortDirection = $request->get('direction', 'asc');
        $allowedSorts = ['sort_order', 'name', 'products_count', 'created_at'];

        if (in_array($sortBy, $allowedSorts, true)) {
            if ($sortBy === 'products_count') {
                $query->orderBy('products_count', $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
        }

        $perPage = (int) $request->get('per_page', 15);
        $categories = $query->paginate($perPage);

        $categories->getCollection()->transform(function (ProductCategory $category) {
            return $this->formatCategory($category);
        });

        $statistics = [
            'total_categories' => ProductCategory::forTenant($tenant->id)->count(),
            'active_categories' => ProductCategory::forTenant($tenant->id)->where('is_active', true)->count(),
            'root_categories' => ProductCategory::forTenant($tenant->id)->whereNull('parent_id')->count(),
            'with_products' => ProductCategory::forTenant($tenant->id)->whereHas('products')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Get create form data.
     */
    public function create(Request $request, Tenant $tenant)
    {
        $categories = ProductCategory::forTenant($tenant->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $hierarchy = $this->buildHierarchy($categories);

        return response()->json([
            'success' => true,
            'message' => 'Category form data retrieved successfully',
            'data' => [
                'parent_categories' => $hierarchy,
            ],
        ]);
    }

    /**
     * Store a new category.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), $this->rules($tenant));

        $validator->after(function ($validator) use ($request, $tenant) {
            if ($request->filled('parent_id')) {
                $parent = ProductCategory::find($request->get('parent_id'));
                if (!$parent || $parent->tenant_id !== $tenant->id) {
                    $validator->errors()->add('parent_id', 'The selected parent category is invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['tenant_id'] = $tenant->id;
        $data['is_active'] = $request->boolean('is_active', true);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['slug'] = $this->uniqueSlug($tenant->id, $data['slug']);

        if (empty($data['sort_order'])) {
            $maxSortOrder = ProductCategory::forTenant($tenant->id)
                ->where('parent_id', $data['parent_id'] ?? null)
                ->max('sort_order') ?? 0;
            $data['sort_order'] = $maxSortOrder + 1;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = ProductCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => [
                'category' => $this->formatCategory($category->fresh(['parent'])->loadCount(['products', 'children'])),
            ],
        ], 201);
    }

    /**
     * Show a category.
     */
    public function show(Request $request, Tenant $tenant, ProductCategory $category)
    {
        if ($category->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $category->load(['parent', 'children', 'children.children', 'products']);
        $category->loadCount(['products', 'children']);

        $children = $category->children->map(function (ProductCategory $child) {
            return $this->formatCategory($child);
        })->values();

        $products = $category->products->take(10)->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'sales_rate' => (float) ($product->sales_rate ?? 0),
                'selling_price' => (float) ($product->selling_price ?? 0),
                'stock_quantity' => (float) ($product->stock_quantity ?? 0),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully',
            'data' => [
                'category' => $this->formatCategory($category),
                'children' => $children,
                'products' => $products,
                'products_count' => $category->products_count,
                'children_count' => $category->children_count,
                'descendants_count' => $category->descendants()->count(),
            ],
        ]);
    }

    /**
     * Update a category.
     */
    public function update(Request $request, Tenant $tenant, ProductCategory $category)
    {
        if ($category->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), $this->rules($tenant, $category->id));

        $validator->after(function ($validator) use ($request, $tenant, $category) {
            if ($request->filled('parent_id')) {
                $parent = ProductCategory::find($request->get('parent_id'));
                if (!$parent || $parent->tenant_id !== $tenant->id) {
                    $validator->errors()->add('parent_id', 'The selected parent category is invalid.');
                }

                if ((int) $request->get('parent_id') === (int) $category->id) {
                    $validator->errors()->add('parent_id', 'A category cannot be its own parent.');
                }

                if ($this->wouldCreateCircularReference($category->id, $request->get('parent_id'))) {
                    $validator->errors()->add('parent_id', 'Cannot set this category as parent as it would create a circular reference.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if (empty($data['slug']) || $data['name'] !== $category->name) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['slug'] = $this->uniqueSlug($tenant->id, $data['slug'], $category->id);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => [
                'category' => $this->formatCategory($category->fresh(['parent'])->loadCount(['products', 'children'])),
            ],
        ]);
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus(Request $request, Tenant $tenant, ProductCategory $category)
    {
        if ($category->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        $category->update(['is_active' => !$category->is_active]);

        return response()->json([
            'success' => true,
            'message' => $category->is_active ? 'Category activated successfully' : 'Category deactivated successfully',
            'data' => [
                'category' => $this->formatCategory($category->fresh(['parent'])->loadCount(['products', 'children'])),
            ],
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroy(Request $request, Tenant $tenant, ProductCategory $category)
    {
        if ($category->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has products assigned to it.',
            ], 422);
        }

        if ($category->children()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has subcategories.',
            ], 422);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Quick store a category via AJAX.
     */
    public function quickStore(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:product_categories,id',
            'is_active' => 'boolean',
        ]);

        $validator->after(function ($validator) use ($request, $tenant) {
            if ($request->filled('parent_id')) {
                $parent = ProductCategory::find($request->get('parent_id'));
                if (!$parent || $parent->tenant_id !== $tenant->id) {
                    $validator->errors()->add('parent_id', 'The selected parent category is invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['tenant_id'] = $tenant->id;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['slug'] = $this->uniqueSlug($tenant->id, Str::slug($data['name']));

        $maxSortOrder = ProductCategory::forTenant($tenant->id)
            ->where('parent_id', $data['parent_id'] ?? null)
            ->max('sort_order') ?? 0;
        $data['sort_order'] = $maxSortOrder + 1;

        $category = ProductCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => [
                'category' => $this->formatCategory($category),
            ],
        ], 201);
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:product_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        $validator->after(function ($validator) use ($request, $tenant) {
            if ($request->filled('categories')) {
                $ids = collect($request->get('categories'))->pluck('id')->all();
                $invalid = ProductCategory::whereIn('id', $ids)
                    ->where('tenant_id', '!=', $tenant->id)
                    ->exists();

                if ($invalid) {
                    $validator->errors()->add('categories', 'One or more categories are invalid.');
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->get('categories', []) as $categoryData) {
            ProductCategory::where('id', $categoryData['id'])
                ->where('tenant_id', $tenant->id)
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categories reordered successfully',
        ]);
    }

    private function formatCategory(ProductCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'parent_id' => $category->parent_id,
            'parent' => $category->parent ? [
                'id' => $category->parent->id,
                'name' => $category->parent->name,
            ] : null,
            'image' => $category->image,
            'image_url' => $category->image ? Storage::url($category->image) : null,
            'sort_order' => $category->sort_order,
            'is_active' => (bool) $category->is_active,
            'status' => $category->is_active ? 'Active' : 'Inactive',
            'products_count' => (int) ($category->products_count ?? $category->products()->count()),
            'children_count' => (int) ($category->children_count ?? $category->children()->count()),
            'meta_title' => $category->meta_title,
            'meta_description' => $category->meta_description,
            'full_path' => $category->full_path,
            'depth' => $category->depth,
            'created_at' => $category->created_at?->toDateTimeString(),
            'updated_at' => $category->updated_at?->toDateTimeString(),
            'can_delete' => (($category->products_count ?? $category->products()->count()) == 0)
                && (($category->children_count ?? $category->children()->count()) == 0),
        ];
    }

    private function buildHierarchy($categories, $parentId = null, $level = 0): array
    {
        $result = [];

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $result[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'level' => $level,
                ];

                $children = $this->buildHierarchy($categories, $category->id, $level + 1);
                $result = array_merge($result, $children);
            }
        }

        return $result;
    }

    private function uniqueSlug(int $tenantId, string $slug, ?int $ignoreId = null): string
    {
        $baseSlug = $slug;
        $counter = 1;

        while (ProductCategory::where('tenant_id', $tenantId)
            ->where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                return $query->where('id', '!=', $ignoreId);
            })
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function wouldCreateCircularReference(int $categoryId, int $parentId): bool
    {
        $current = ProductCategory::find($parentId);

        while ($current) {
            if ((int) $current->id === (int) $categoryId) {
                return true;
            }
            $current = $current->parent;
        }

        return false;
    }

    private function rules(Tenant $tenant, ?int $categoryId = null): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($categoryId),
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('product_categories')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($categoryId),
            ],
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:product_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer|min:0|max:999999',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }
}
