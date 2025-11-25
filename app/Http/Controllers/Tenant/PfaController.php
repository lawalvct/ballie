<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Pfa;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PfaController extends Controller
{
    public function index(Tenant $tenant)
    {
        $pfas = Pfa::where('tenant_id', $tenant->id)
            ->withCount('employees')
            ->orderBy('name')
            ->paginate(20);

        return view('tenant.payroll.pfas.index', compact('tenant', 'pfas'));
    }

    public function create(Tenant $tenant)
    {
        return view('tenant.payroll.pfas.create', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:pfas,code',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $tenant->id;

        Pfa::create($validated);

        return redirect()->route('tenant.payroll.pfas.index', $tenant)
            ->with('success', 'PFA created successfully.');
    }

    public function edit(Tenant $tenant, Pfa $pfa)
    {
        return view('tenant.payroll.pfas.edit', compact('tenant', 'pfa'));
    }

    public function update(Request $request, Tenant $tenant, Pfa $pfa)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:pfas,code,' . $pfa->id,
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $pfa->update($validated);

        return redirect()->route('tenant.payroll.pfas.index', $tenant)
            ->with('success', 'PFA updated successfully.');
    }

    public function destroy(Tenant $tenant, Pfa $pfa)
    {
        if ($pfa->employees()->count() > 0) {
            return back()->with('error', 'Cannot delete PFA with assigned employees.');
        }

        $pfa->delete();

        return redirect()->route('tenant.payroll.pfas.index', $tenant)
            ->with('success', 'PFA deleted successfully.');
    }
}
