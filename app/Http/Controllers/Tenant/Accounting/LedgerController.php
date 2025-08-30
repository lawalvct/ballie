<?php

namespace App\Http\Controllers\Tenant\Accounting;

use App\Http\Controllers\Controller;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LedgerController extends Controller
{
    public function index()
    {
        $ledgerAccounts = LedgerAccount::where('tenant_id', tenant()->id)
            ->with('accountGroup')
            ->active()
            ->orderBy('name')
            ->paginate(20);

        return view('tenant.ledger.index', compact('ledgerAccounts'));
    }

    public function show($id, Request $request)
    {
        $ledgerAccount = LedgerAccount::where('tenant_id', tenant()->id)
            ->with('accountGroup')
            ->findOrFail($id);

        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : Carbon::now()->startOfYear();
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : Carbon::now();

        // Get opening balance as of from_date
        $openingBalance = $ledgerAccount->getCurrentBalance($fromDate->copy()->subDay());

        // Get entries for the period
        $entries = $ledgerAccount->getLedgerEntries($fromDate, $toDate);

        // Calculate running balance
        $runningBalance = $openingBalance;
        $processedEntries = [];

        foreach ($entries as $entry) {
            $runningBalance += $entry->debit_amount - $entry->credit_amount;
            $processedEntries[] = [
                'entry' => $entry,
                'running_balance' => $runningBalance,
            ];
        }

        $closingBalance = $runningBalance;

        return view('tenant.ledger.show', compact(
            'ledgerAccount',
            'processedEntries',
            'openingBalance',
            'closingBalance',
            'fromDate',
            'toDate'
        ));
    }
}
