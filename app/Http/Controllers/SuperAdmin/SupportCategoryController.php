<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = SupportCategory::withCount('tickets')->ordered()->get();

        return view('super-admin.support.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('super-admin.support.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:support_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        // Set the highest sort order
        $maxOrder = SupportCategory::max('sort_order');
        $validated['sort_order'] = ($maxOrder ?? 0) + 1;

        SupportCategory::create($validated);

        return redirect()
            ->route('super-admin.support.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(SupportCategory $category)
    {
        return view('super-admin.support.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, SupportCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:support_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()
            ->route('super-admin.support.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(SupportCategory $category)
    {
        // Check if category has tickets
        if ($category->tickets()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing tickets.');
        }

        $category->delete();

        return redirect()
            ->route('super-admin.support.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:support_categories,id',
        ]);

        foreach ($validated['order'] as $index => $categoryId) {
            SupportCategory::where('id', $categoryId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Categories reordered successfully!']);
    }
}
