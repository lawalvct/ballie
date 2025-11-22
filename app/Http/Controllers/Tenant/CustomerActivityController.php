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
        $activities = CustomerActivity::where('tenant_id', $tenant->id)
            ->with(['customer', 'user'])
            ->orderBy('activity_date', 'desc')
            ->paginate(20);

        return view('tenant.crm.activities.index', compact('tenant', 'activities'));
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
}
