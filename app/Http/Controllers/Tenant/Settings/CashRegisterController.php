<?php

namespace App\Http\Controllers\Tenant\Settings;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashRegisterController extends Controller
{
    public function index(Tenant $tenant)
    {
        $cashRegisters = CashRegister::where('tenant_id', $tenant->id)
            ->withCount(['sessions', 'sales'])
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return view('tenant.settings.cash-registers.index', compact('tenant', 'cashRegisters'));
    }

    public function create(Tenant $tenant)
    {
        return view('tenant.settings.cash-registers.create', compact('tenant'));
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:cash_registers,name,NULL,id,tenant_id,' . $tenant->id
            ],
            'location' => 'nullable|string|max:500',
            'opening_balance' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['current_balance'] = $validated['opening_balance'];
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Track who created this
        $validated['created_by'] = Auth::id();

        CashRegister::create($validated);

        return redirect()
            ->route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug])
            ->with('success', 'Cash register created successfully.');
    }

    public function edit(Tenant $tenant, CashRegister $cashRegister)
    {
        // Ensure the cash register belongs to this tenant
        if ($cashRegister->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Check if there are any active sessions
        $hasActiveSessions = $cashRegister->sessions()->whereNull('closed_at')->exists();

        return view('tenant.settings.cash-registers.edit', compact('tenant', 'cashRegister', 'hasActiveSessions'));
    }

    public function update(Request $request, Tenant $tenant, CashRegister $cashRegister)
    {
        // Ensure the cash register belongs to this tenant
        if ($cashRegister->tenant_id !== $tenant->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:cash_registers,name,' . $cashRegister->id . ',id,tenant_id,' . $tenant->id
            ],
            'location' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Track who updated this
        $validated['updated_by'] = Auth::id();

        $cashRegister->update($validated);

        return redirect()
            ->route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug])
            ->with('success', 'Cash register updated successfully.');
    }

    public function destroy(Tenant $tenant, CashRegister $cashRegister)
    {
        // Ensure the cash register belongs to this tenant
        if ($cashRegister->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Check if there are any sessions
        if ($cashRegister->sessions()->exists()) {
            return redirect()
                ->route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug])
                ->with('error', 'Cannot delete cash register with existing sessions. Deactivate it instead.');
        }

        // Check if there are any sales
        if ($cashRegister->sales()->exists()) {
            return redirect()
                ->route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug])
                ->with('error', 'Cannot delete cash register with existing sales. Deactivate it instead.');
        }

        $cashRegister->delete();

        return redirect()
            ->route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug])
            ->with('success', 'Cash register deleted successfully.');
    }

    public function toggleStatus(Tenant $tenant, CashRegister $cashRegister)
    {
        // Ensure the cash register belongs to this tenant
        if ($cashRegister->tenant_id !== $tenant->id) {
            abort(404);
        }

        // Check if trying to deactivate a register with active sessions
        if ($cashRegister->is_active) {
            $hasActiveSessions = $cashRegister->sessions()->whereNull('closed_at')->exists();

            if ($hasActiveSessions) {
                return redirect()
                    ->route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug])
                    ->with('error', 'Cannot deactivate cash register with active sessions. Close all sessions first.');
            }
        }

        $cashRegister->update([
            'is_active' => !$cashRegister->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $cashRegister->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->route('tenant.settings.cash-registers.index', ['tenant' => $tenant->slug])
            ->with('success', "Cash register {$status} successfully.");
    }
}
