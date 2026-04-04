<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') === 'active');
        }

        $plans = $query->orderBy('sort_order')->orderBy('monthly_price')->get();

        $stats = [
            'total' => Plan::count(),
            'active' => Plan::where('is_active', true)->count(),
            'inactive' => Plan::where('is_active', false)->count(),
            'popular' => Plan::where('is_popular', true)->count(),
        ];

        return view('super-admin.plans.index', compact('plans', 'stats'));
    }

    public function create()
    {
        return view('super-admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plans,name',
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable|string',
            'monthly_price' => 'required|integer|min:0',
            'quarterly_price' => 'required|integer|min:0',
            'biannual_price' => 'required|integer|min:0',
            'yearly_price' => 'required|integer|min:0',
            'max_users' => 'required|integer|min:1',
            'max_customers' => 'required|integer|min:0',
            'support_level' => 'required|in:basic,standard,priority,dedicated',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Parse features from textarea (one per line)
        $validated['features'] = $request->filled('features')
            ? array_values(array_filter(array_map('trim', explode("\n", $request->features))))
            : [];

        // Boolean feature flags
        $validated['has_pos'] = $request->boolean('has_pos');
        $validated['has_payroll'] = $request->boolean('has_payroll');
        $validated['has_api_access'] = $request->boolean('has_api_access');
        $validated['has_advanced_reports'] = $request->boolean('has_advanced_reports');
        $validated['has_ecommerce'] = $request->boolean('has_ecommerce');
        $validated['has_audit_log'] = $request->boolean('has_audit_log');
        $validated['has_multi_location'] = $request->boolean('has_multi_location');
        $validated['has_multi_currency'] = $request->boolean('has_multi_currency');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Plan::create($validated);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan "' . $validated['name'] . '" created successfully.');
    }

    public function edit(Plan $plan)
    {
        return view('super-admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('plans', 'name')->ignore($plan->id)],
            'description' => 'nullable|string|max:1000',
            'features' => 'nullable|string',
            'monthly_price' => 'required|integer|min:0',
            'quarterly_price' => 'required|integer|min:0',
            'biannual_price' => 'required|integer|min:0',
            'yearly_price' => 'required|integer|min:0',
            'max_users' => 'required|integer|min:1',
            'max_customers' => 'required|integer|min:0',
            'support_level' => 'required|in:basic,standard,priority,dedicated',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $validated['features'] = $request->filled('features')
            ? array_values(array_filter(array_map('trim', explode("\n", $request->features))))
            : [];

        $validated['has_pos'] = $request->boolean('has_pos');
        $validated['has_payroll'] = $request->boolean('has_payroll');
        $validated['has_api_access'] = $request->boolean('has_api_access');
        $validated['has_advanced_reports'] = $request->boolean('has_advanced_reports');
        $validated['has_ecommerce'] = $request->boolean('has_ecommerce');
        $validated['has_audit_log'] = $request->boolean('has_audit_log');
        $validated['has_multi_location'] = $request->boolean('has_multi_location');
        $validated['has_multi_currency'] = $request->boolean('has_multi_currency');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $plan->update($validated);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan "' . $plan->name . '" updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        $name = $plan->name;
        $plan->delete();

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan "' . $name . '" deleted successfully.');
    }

    /**
     * Toggle plan active status via AJAX.
     */
    public function toggleStatus(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $plan->is_active,
            'message' => 'Plan ' . ($plan->is_active ? 'activated' : 'deactivated') . ' successfully.',
        ]);
    }
}
