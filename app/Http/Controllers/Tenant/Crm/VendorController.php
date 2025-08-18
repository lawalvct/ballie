<?php

namespace App\Http\Controllers\Tenant\Crm;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{
    public function index(Tenant $tenant)
    {
        $vendors = Vendor::where('tenant_id', $tenant->id)
            ->with('ledgerAccount')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalVendors = Vendor::where('tenant_id', tenant()->id)->count();
        $totalPurchases = Vendor::where('tenant_id', tenant()->id)->sum('total_purchases');
        $totalOutstanding = Vendor::where('tenant_id', tenant()->id)->sum('outstanding_balance');
        $avgPaymentDays = 0; // Calculate based on your payment data

        return view('tenant.crm.vendors.index', compact(
            'vendors',
            'totalVendors',
            'totalPurchases',
            'totalOutstanding',
            'avgPaymentDays'
        ));
    }

    public function create(Tenant $tenant)
    {
        return view('tenant.crm.vendors.create', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'vendor_type' => 'required|in:individual,business',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:3',
            'payment_terms' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vendor = new Vendor($request->all());
        $vendor->tenant_id = tenant()->id;
        $vendor->status = 'active';
        $vendor->save();

        return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
            ->with('success', 'Vendor created successfully with ledger account.');
    }

    public function show(Tenant $tenant, $id)
    {
        $vendor = Vendor::where('tenant_id', $tenant->id)
            ->with(['ledgerAccount.accountGroup'])
            ->findOrFail($id);

        // Update outstanding balance from ledger
        $vendor->updateOutstandingBalance();

        return view('tenant.crm.vendors.show', compact('vendor'));
    }

    public function edit(Tenant $tenant, $id)
    {
        $vendor = Vendor::where('tenant_id', $tenant->id)
            ->findOrFail($id);

        return view('tenant.crm.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::where('tenant_id', tenant()->id)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'vendor_type' => 'required|in:individual,business',
            'first_name' => 'required_if:vendor_type,individual|string|max:255',
            'last_name' => 'required_if:vendor_type,individual|string|max:255',
            'company_name' => 'required_if:vendor_type,business|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'tax_id' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:3',
            'payment_terms' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $vendor->update($request->all());

        return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
            ->with('success', 'Vendor updated successfully.');
    }

    public function destroy($id)
    {
        $vendor = Vendor::where('tenant_id', tenant()->id)
            ->findOrFail($id);

        // Check if vendor has outstanding balance
        if ($vendor->outstanding_balance > 0) {
            return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
                ->with('error', 'Cannot delete vendor with outstanding balance.');
        }

        $vendor->delete();

        return redirect()->route('tenant.crm.vendors.index', ['tenant' => tenant()->slug])
            ->with('success', 'Vendor deleted successfully.');
    }
}
