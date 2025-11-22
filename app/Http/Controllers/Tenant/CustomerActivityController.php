<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerActivity;
use App\Models\Tenant;
use Illuminate\Http\Request;

class CustomerActivityController extends Controller
{
    public function index(Tenant $tenant)
    {
        $query = CustomerActivity::where('tenant_id', $tenant->id)
            ->with(['customer', 'user']);

        // Filter by customer
        if (request('customer_id')) {
            $query->where('customer_id', request('customer_id'));
        }

        // Filter by activity type
        if (request('activity_type')) {
            $query->where('activity_type', request('activity_type'));
        }

        // Filter by status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Filter by date range
        if (request('date_from')) {
            $query->whereDate('activity_date', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $query->whereDate('activity_date', '<=', request('date_to'));
        }

        // Search
        if (request('search')) {
            $query->where('subject', 'like', '%' . request('search') . '%');
        }

        $activities = $query->orderBy('activity_date', 'desc')->paginate(20);
        $customers = Customer::where('tenant_id', $tenant->id)->get();

        return view('tenant.crm.activities.index', compact('tenant', 'activities', 'customers'));
    }

    public function create(Tenant $tenant)
    {
        $customers = Customer::where('tenant_id', $tenant->id)->get();
        return view('tenant.crm.activities.create', compact('tenant', 'customers'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'activity_type' => 'required|in:call,email,meeting,note,task,follow_up',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        CustomerActivity::create([
            'tenant_id' => $tenant->id,
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return redirect()->route('tenant.crm.activities.index', $tenant->slug)
            ->with('success', 'Activity logged successfully!');
    }

    public function edit(Tenant $tenant, CustomerActivity $activity)
    {
        $customers = Customer::where('tenant_id', $tenant->id)->get();
        return view('tenant.crm.activities.edit', compact('tenant', 'activity', 'customers'));
    }

    public function update(Request $request, Tenant $tenant, CustomerActivity $activity)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'activity_type' => 'required|in:call,email,meeting,note,task,follow_up',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $activity->update($validated);

        return redirect()->route('tenant.crm.activities.index', $tenant->slug)
            ->with('success', 'Activity updated successfully!');
    }

    public function destroy(Tenant $tenant, CustomerActivity $activity)
    {
        $activity->delete();
        return redirect()->route('tenant.crm.activities.index', $tenant->slug)
            ->with('success', 'Activity deleted successfully!');
    }
}
